<?php

namespace App\Services;

use App\Exceptions\BidException;
use App\Models\Client;
use App\Models\NadoAutoEnchere;
use App\Models\NadoAutoEncheresBid;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Cœur de l'enchère compétitive chronométrée.
 *
 * Toute la logique sensible à la concurrence vit ici : placement d'enchère,
 * enchères automatiques (proxy / max), anti-sniping. Chaque placement s'exécute
 * dans une transaction avec un verrou `lockForUpdate` sur la ligne de l'enchère,
 * ce qui sérialise les enchérisseurs simultanés : jamais deux gagnants, jamais
 * une enchère en dessous de prix courant + incrément.
 *
 * Modèle "proxy" (type eBay) : le montant saisi par le client est son MAXIMUM.
 * Le prix affiché (`current_price`) est dérivé — juste assez pour battre le 2e
 * plafond le plus élevé, plafonné au maximum du leader. Le leader est le client
 * au plafond le plus élevé (égalité départagée par l'antériorité du plafond).
 *
 * Pour passer plus tard au temps réel façon WebSocket (Laravel Echo + Pusher ou
 * Reverb), il suffirait de diffuser un événement après `placeBid()` ; la logique
 * de ce service reste identique. On garde ici le polling JSON (cf. controller
 * `state()`), sans dépendance Node, adapté à l'hébergement mutualisé.
 */
class AuctionService
{
    /**
     * Place une enchère (le montant = plafond proxy du client).
     *
     * @return array{
     *   accepted: bool, is_leader: bool, current_price: float, min_next: float,
     *   previous_leader_id: int|null, extended: bool, bid_id: int
     * }
     *
     * @throws BidException si la règle métier rejette l'enchère.
     */
    public function placeBid(NadoAutoEnchere $enchere, Client $client, float $maxAmount): array
    {
        return DB::transaction(function () use ($enchere, $client, $maxAmount) {
            // Verrou ligne : sérialise les enchérisseurs concurrents sur CETTE enchère.
            $enchere = NadoAutoEnchere::whereKey($enchere->getKey())->lockForUpdate()->firstOrFail();

            $this->assertEligible($enchere, $client);
            $this->assertOpen($enchere);

            $maxAmount = $this->normalizeAmount($maxAmount);
            $minNext   = (float) $enchere->minNextBid();

            if ($maxAmount + 0.001 < $minNext) {
                throw new BidException(
                    __('messages.enchere.err_too_low', ['min' => $this->fmt($minNext)]),
                    'too_low'
                );
            }

            // État AVANT ce placement (pour détecter le leader sortant à notifier).
            $previousLeaderId = $this->leaderClientId($enchere->id);

            // Enregistre l'offre humaine (proposition = le plafond proposé).
            $bid = NadoAutoEncheresBid::create([
                'enchere_id'  => $enchere->id,
                'client_id'   => $client->id,
                'proposition' => $maxAmount,
                'max_amount'  => $maxAmount,
                'is_auto'     => false,
            ]);

            // Recalcule leader + prix visible à partir de tous les plafonds.
            [$leaderId, $visiblePrice] = $this->resolve($enchere);

            // Anti-sniping : une enchère valable en toute fin prolonge la clôture.
            $extended = $this->applyAntiSnipe($enchere);

            $enchere->current_price  = $visiblePrice;
            $enchere->winning_bid_id = $this->leaderBidId($enchere->id, $leaderId);
            $enchere->save();

            return [
                'accepted'           => true,
                'is_leader'          => $leaderId === $client->id,
                'current_price'      => (float) $visiblePrice,
                'min_next'           => (float) $enchere->fresh()->minNextBid(),
                'previous_leader_id' => $previousLeaderId && $previousLeaderId !== $leaderId ? $previousLeaderId : null,
                'extended'           => $extended,
                'bid_id'             => $bid->id,
            ];
        }, 3);
    }

    /**
     * Vérifie l'éligibilité serveur à enchérir (JAMAIS seulement côté UI).
     *
     * @throws BidException
     */
    public function assertEligible(NadoAutoEnchere $enchere, Client $client): void
    {
        if ($client->type !== 'flotte&enchere') {
            throw new BidException(__('messages.enchere.err_not_pro'), 'not_pro');
        }
        if ($client->ae_role === 'vendeur') {
            throw new BidException(__('messages.enchere.err_seller'), 'seller');
        }
        if (! $client->ae_approved) {
            throw new BidException(__('messages.enchere.err_not_approved'), 'not_approved');
        }

        // PHASE 3 (stub) — caution/dépôt : au-delà d'un seuil, exiger un dépôt validé.
        // Aujourd'hui le dépôt n'est pas encore modélisé : hook prêt, décision = autoriser.
        // Quand la caution sera implémentée : $client->hasAuctionDeposit() ou un montant.
        $threshold = (float) settings('enchere.deposit_threshold', 0);
        if ($threshold > 0 && $enchere->currentHighBid() >= $threshold) {
            // if (! $client->hasValidAuctionDeposit()) {
            //     throw new BidException(__('messages.enchere.err_deposit_required'), 'deposit');
            // }
        }
    }

    /** @throws BidException */
    private function assertOpen(NadoAutoEnchere $enchere): void
    {
        if ($enchere->status && ! in_array($enchere->status, ['live', 'scheduled'], true)) {
            throw new BidException(__('messages.enchere.err_closed'), 'closed');
        }
        $now = now();
        if ($enchere->starts_at && $now->lt($enchere->starts_at)) {
            throw new BidException(__('messages.enchere.err_not_started'), 'not_started');
        }
        if ($enchere->ends_at && $now->gt($enchere->ends_at)) {
            throw new BidException(__('messages.enchere.err_ended'), 'ended');
        }
    }

    /**
     * Résout la guerre des proxys : renvoie [leaderClientId, prixVisible].
     *
     * Leader = plafond le plus élevé (égalité → plafond atteint en premier).
     * Prix visible = min(plafondLeader, 2ᵉ plafond + incrément) ; s'il n'y a
     * qu'un enchérisseur, c'est la mise à prix.
     */
    private function resolve(NadoAutoEnchere $enchere): array
    {
        $increment = (float) $enchere->effectiveMinIncrement();
        $start     = (float) $enchere->effectiveStartingPrice();

        // Plafond effectif par client = plus haut max_amount ; on garde la date
        // du premier bid qui a atteint ce plafond pour départager les égalités.
        $rows = NadoAutoEncheresBid::where('enchere_id', $enchere->id)
            ->orderBy('id')
            ->get(['client_id', 'proposition', 'max_amount', 'created_at', 'id']);

        $byClient = []; // client_id => ['max' => float, 'seq' => int]
        foreach ($rows as $r) {
            $cap = (float) ($r->max_amount ?? $r->proposition);
            if (! isset($byClient[$r->client_id])) {
                $byClient[$r->client_id] = ['max' => $cap, 'seq' => $r->id];
            } elseif ($cap > $byClient[$r->client_id]['max']) {
                $byClient[$r->client_id]['max'] = $cap;
                $byClient[$r->client_id]['seq'] = $r->id;
            }
        }

        if (empty($byClient)) {
            return [null, $start];
        }

        // Trie par plafond desc, puis par antériorité (seq asc) pour les égalités.
        uasort($byClient, function ($a, $b) {
            if ($a['max'] === $b['max']) {
                return $a['seq'] <=> $b['seq'];
            }
            return $b['max'] <=> $a['max'];
        });

        $clientIds = array_keys($byClient);
        $leaderId  = $clientIds[0];
        $leaderMax = $byClient[$leaderId]['max'];

        if (count($clientIds) === 1) {
            // Un seul enchérisseur : prix visible = mise à prix (plafond masqué).
            return [$leaderId, min($leaderMax, max($start, $start))];
        }

        $secondMax = $byClient[$clientIds[1]]['max'];
        $visible   = min($leaderMax, $secondMax + $increment);
        $visible   = max($visible, $start);

        return [$leaderId, $visible];
    }

    /** id de la dernière enchère posée par le leader (pour winning_bid_id). */
    private function leaderBidId(int $enchereId, $leaderId): ?int
    {
        if (! $leaderId) {
            return null;
        }
        return NadoAutoEncheresBid::where('enchere_id', $enchereId)
            ->where('client_id', $leaderId)
            ->orderByDesc('max_amount')
            ->orderByDesc('id')
            ->value('id');
    }

    /** Leader courant (client au plafond le plus élevé) sans recalcul de prix. */
    public function leaderClientId(int $enchereId): ?int
    {
        $enchere = NadoAutoEnchere::find($enchereId);
        if (! $enchere) {
            return null;
        }
        [$leaderId] = $this->resolve($enchere);
        return $leaderId;
    }

    /**
     * Prolonge la clôture si une enchère valable arrive dans la fenêtre
     * anti-snipe, dans la limite d'un nombre d'extensions.
     */
    private function applyAntiSnipe(NadoAutoEnchere $enchere): bool
    {
        $window = (int) settings('enchere.antisnipe_seconds', 120);
        $maxExt = (int) settings('enchere.max_extensions', 10);

        if ($window <= 0 || ! $enchere->ends_at) {
            return false;
        }
        if ($enchere->extensions_count >= $maxExt) {
            return false;
        }

        $now       = now();
        $remaining = $now->diffInSeconds(Carbon::parse($enchere->ends_at), false);
        if ($remaining < 0 || $remaining > $window) {
            return false;
        }

        $enchere->ends_at = Carbon::parse($enchere->ends_at)->addSeconds($window);
        $enchere->extensions_count = (int) $enchere->extensions_count + 1;
        // sauvegarde faite par l'appelant (placeBid) ; ici on ne persiste pas seul.
        return true;
    }

    /** Montant propre : entier MAD (pas de centimes), >= 0. */
    private function normalizeAmount(float $amount): float
    {
        return max(0, round($amount));
    }

    private function fmt(float $n): string
    {
        return number_format($n, 0, ',', ' ') . ' MAD';
    }
}
