<?php

namespace App\Console\Commands;

use App\Models\NadoAutoEnchere;
use App\Services\AuctionService;
use App\Services\EnchereNotifier;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Cycle de vie des enchères compétitives, à exécuter CHAQUE MINUTE.
 *
 *   scheduled → live   quand starts_at est atteint
 *   live      → ended  quand ends_at est atteint (détermine le gagnant)
 *
 * Gagnant = plafond le plus élevé dont le prix courant atteint la réserve
 * (sinon « réserve non atteinte », aucun gagnant). Notifie gagnant + équipe.
 *
 * Cron cPanel (une ligne) :
 *   * * * * * /usr/local/bin/php /home2/autoench/nado/artisan schedule:run >> /dev/null 2>&1
 *
 * Test manuel : php artisan nado:close-auctions
 */
class CloseEndedAuctions extends Command
{
    protected $signature = 'nado:close-auctions {--force : Ignorer le flag live_bidding}';

    protected $description = 'Transitions scheduled→live→ended des enchères + désignation du gagnant';

    public function handle(AuctionService $auctions): int
    {
        if (! $this->option('force') && ! feature_on('enchere.live_bidding')) {
            $this->info('enchere.live_bidding désactivé — rien à faire.');
            return self::SUCCESS;
        }

        $now = now();

        // scheduled → live
        $toLive = NadoAutoEnchere::where('status', 'scheduled')
            ->whereNotNull('starts_at')
            ->where('starts_at', '<=', $now)
            ->where(function ($q) use ($now) {
                $q->whereNull('ends_at')->orWhere('ends_at', '>', $now);
            })
            ->get();
        foreach ($toLive as $enchere) {
            $enchere->forceFill(['status' => 'live'])->save();
            $this->info("Enchère #{$enchere->id} → live.");
        }

        // live → ended (y compris celles qui viennent de passer live mais déjà expirées)
        $toEnd = NadoAutoEnchere::whereIn('status', ['live', 'scheduled'])
            ->whereNotNull('ends_at')
            ->where('ends_at', '<=', $now)
            ->get();
        foreach ($toEnd as $enchere) {
            $this->closeOne($enchere, $auctions);
        }

        $this->info('Terminé : ' . $toLive->count() . ' ouverte(s), ' . $toEnd->count() . ' clôturée(s).');
        return self::SUCCESS;
    }

    private function closeOne(NadoAutoEnchere $enchere, AuctionService $auctions): void
    {
        DB::transaction(function () use ($enchere, $auctions) {
            // Verrou pour ne pas entrer en concurrence avec un dernier placeBid().
            $enchere = NadoAutoEnchere::whereKey($enchere->getKey())->lockForUpdate()->firstOrFail();

            // Déjà clôturée entre-temps ? on sort.
            if ($enchere->status === 'ended' || $enchere->status === 'cancelled') {
                return;
            }

            $leaderId = $auctions->leaderClientId($enchere->id);
            $price    = (float) $enchere->currentHighBid();
            $reserve  = (float) ($enchere->reserve_price ?? 0);

            $reserveMet = $reserve <= 0 || $price >= $reserve;

            if ($leaderId && $reserveMet) {
                $winningBidId = \App\Models\NadoAutoEncheresBid::where('enchere_id', $enchere->id)
                    ->where('client_id', $leaderId)
                    ->orderByDesc('max_amount')->orderByDesc('id')->value('id');

                $enchere->forceFill([
                    'status'           => 'ended',
                    'winner_client_id' => $leaderId,
                    'winning_bid_id'   => $winningBidId,
                ])->save();

                $winner = \App\Models\Client::find($leaderId);
                EnchereNotifier::winnerDeclared($enchere, $winner, $price);
                $this->info("Enchère #{$enchere->id} clôturée → gagnant client #{$leaderId} à {$price} MAD.");
            } else {
                $enchere->forceFill([
                    'status'           => 'ended',
                    'winner_client_id' => null,
                    'winning_bid_id'   => null,
                ])->save();

                EnchereNotifier::winnerDeclared($enchere, null, $price);
                $this->info("Enchère #{$enchere->id} clôturée → réserve non atteinte / aucune enchère.");
            }
        });
    }
}
