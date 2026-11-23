<?php

namespace App\Services;

use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Demande;
use App\Models\DemandeExpert;
use App\Models\Discussion;
use App\Models\Estimation;
use App\Models\LegalCheck;
use App\Models\Modele;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * Agrégateur des notifications admin.
 *
 * Expose un fil unifié d'événements récents à traiter — pris en direct depuis
 * les tables existantes (aucune nouvelle table). Cache court (30s) pour que
 * la cloche en topbar ne martèle pas la DB à chaque page admin visitée.
 */
class AdminNotifications
{
    private const CACHE_TTL = 30;
    private const MAX_ITEMS = 30;

    /** Entrées normalisées { icon, tone, title, body, at, url }. */
    public static function recent(): array
    {
        return Cache::remember('admin.notifications.recent', self::CACHE_TTL, function () {
            return self::build();
        });
    }

    public static function flushCache(): void
    {
        Cache::forget('admin.notifications.recent');
    }

    /**
     * Résout les colonnes annonces.mark / annonces.modele en noms.
     * Elles contiennent soit un ID numérique (lignes récentes) soit un nom
     * (lignes migrées), d'où cette résolution au coup par coup.
     *
     * @return array{brands: array<int|string,string>, modeles: array<int|string,string>}
     */
    private static function brandModelNames($markValues, $modeleValues): array
    {
        $markIds = collect($markValues)->filter(fn ($v) => is_numeric($v))->unique()->values()->all();
        $modeleIds = collect($modeleValues)->filter(fn ($v) => is_numeric($v))->unique()->values()->all();

        return [
            'brands'  => $markIds ? Brand::whereIn('id', $markIds)->pluck('name', 'id')->all() : [],
            'modeles' => $modeleIds ? Modele::whereIn('id', $modeleIds)->pluck('name', 'id')->all() : [],
        ];
    }

    private static function build(): array
    {
        $items = [];

        // 1) Annonces en attente d'approbation.
        $pendingAnnonces = Annonces::where('active', false)
            ->latest('created_at')->limit(10)->get(['id', 'mark', 'modele', 'city', 'created_at']);
        $brandModel = self::brandModelNames(
            $pendingAnnonces->pluck('mark'),
            $pendingAnnonces->pluck('modele')
        );
        foreach ($pendingAnnonces as $a) {
            $label = trim(
                ($brandModel['brands'][$a->mark] ?? $a->mark ?? '') . ' ' .
                ($brandModel['modeles'][$a->modele] ?? $a->modele ?? '')
            );
            if ($label === '') { $label = 'Annonce #' . $a->id; }
            $items[] = [
                'icon'  => 'bi-car-front-fill',
                'tone'  => 'warning',
                'title' => 'Nouvelle annonce en attente',
                'body'  => $a->city ? $label . ' — ' . $a->city : $label,
                'at'    => $a->created_at,
                'url'   => route('admin.annonces.showAnnonce', $a->id),
            ];
        }

        // 2) Clients en attente d'approbation.
        Client::where('is_approved', false)
            ->latest('created_at')->limit(10)->get(['id', 'name', 'phone', 'type', 'created_at'])
            ->each(function ($c) use (&$items) {
                $items[] = [
                    'icon'  => 'bi-person-plus-fill',
                    'tone'  => 'info',
                    'title' => 'Nouveau client à valider',
                    'body'  => trim(($c->name ?? '—') . ' · ' . ($c->phone ?? '') . ($c->type ? ' (' . $c->type . ')' : '')),
                    'at'    => $c->created_at,
                    'url'   => route('admin.clients.approval'),
                ];
            });

        // 3) Demandes acheteur en attente.
        if (Schema::hasTable('demandes')) {
            Demande::where('status', 'pending')
                ->with(['brand:id,name', 'modele:id,name', 'ville:id,name'])
                ->latest('created_at')->limit(10)
                ->get(['id', 'brand_id', 'modele_id', 'ville_id', 'raw_query', 'created_at'])
                ->each(function ($d) use (&$items) {
                    $parts = array_filter([$d->brand?->name, $d->modele?->name, $d->ville?->name]);
                    $label = $parts ? implode(' · ', $parts) : ($d->raw_query ?: 'Demande #' . $d->id);
                    $items[] = [
                        'icon'  => 'bi-megaphone-fill',
                        'tone'  => 'primary',
                        'title' => 'Nouvelle demande acheteur',
                        'body'  => Str::limit($label, 90),
                        'at'    => $d->created_at,
                        'url'   => route('admin.demandes.pending'),
                    ];
                });
        }

        // 4) Demandes expert (popup "Expert NADO" + formulaire contact).
        if (Schema::hasTable('demande_experts')) {
            DemandeExpert::where('status', 'new')
                ->latest('created_at')->limit(10)
                ->get(['id', 'name', 'phone', 'ville', 'subject', 'created_at'])
                ->each(function ($e) use (&$items) {
                    $parts = array_filter([$e->name, $e->phone, $e->ville]);
                    $body  = $parts ? implode(' · ', $parts) : ($e->subject ?: 'Demande #' . $e->id);
                    $items[] = [
                        'icon'  => 'bi-person-badge',
                        'tone'  => 'success',
                        'title' => 'Nouvelle demande expert',
                        'body'  => Str::limit($body, 90),
                        'at'    => $e->created_at,
                        'url'   => route('admin.demandeexperts.show', $e->id),
                    ];
                });
        }

        // 4b) Estimations clients récentes (différent des demandes expert).
        Estimation::with('client:id,name')
            ->latest('created_at')->limit(5)
            ->get(['id', 'client_id', 'brand', 'model', 'year', 'created_at'])
            ->each(function ($e) use (&$items) {
                $items[] = [
                    'icon'  => 'bi-calculator-fill',
                    'tone'  => 'info',
                    'title' => 'Nouvelle estimation client',
                    'body'  => trim(($e->brand ?? '') . ' ' . ($e->model ?? '') . ($e->year ? ' (' . $e->year . ')' : '')
                        . ($e->client ? ' · ' . $e->client->name : '')),
                    'at'    => $e->created_at,
                    'url'   => route('admin.estimations.show', $e->id),
                ];
            });

        // 5) Vérifications légales (NARSA) en attente.
        if (function_exists('feature_on') && feature_on('legalcheck.enabled') && Schema::hasTable('legal_checks')) {
            LegalCheck::where('admin_status', 'pending')
                ->latest('created_at')->limit(10)->get(['id', 'matricule_full', 'created_at'])
                ->each(function ($lc) use (&$items) {
                    $items[] = [
                        'icon'  => 'bi-shield-check',
                        'tone'  => 'danger',
                        'title' => 'Vérification légale à traiter',
                        'body'  => 'Matricule : ' . ($lc->matricule_full ?? '#' . $lc->id),
                        'at'    => $lc->created_at,
                        'url'   => route('admin.legalchecks.show', $lc->id),
                    ];
                });
        }

        // 6) WhatsApp — conversations non lues.
        if (function_exists('feature_on') && feature_on('whatsapp.enabled') && Schema::hasTable('whatsapp_conversations')) {
            WhatsAppConversation::where('unread_count', '>', 0)
                ->latest('last_message_at')->limit(10)
                ->get(['id', 'display_name', 'wa_phone', 'unread_count', 'last_message_at'])
                ->each(function ($w) use (&$items) {
                    $items[] = [
                        'icon'  => 'bi-whatsapp',
                        'tone'  => 'success',
                        'title' => $w->unread_count . ' nouveau(x) message(s) WhatsApp',
                        'body'  => $w->display_name ?: $w->wa_phone,
                        'at'    => $w->last_message_at,
                        'url'   => route('admin.whatsapp.show', $w->id),
                    ];
                });
        }

        // 7) Discussions ouvertes.
        if (Schema::hasTable('discussions')) {
            $openDiscussions = Discussion::where('status', 'open')
                ->with('annonce:id,mark,modele')
                ->latest('last_message_at')->limit(10)
                ->get(['id', 'annonce_id', 'last_message_at', 'updated_at']);
            $marks   = $openDiscussions->pluck('annonce.mark')->filter();
            $modeles = $openDiscussions->pluck('annonce.modele')->filter();
            $dBrandModel = self::brandModelNames($marks, $modeles);
            foreach ($openDiscussions as $d) {
                $label = $d->annonce
                    ? trim(
                        ($dBrandModel['brands'][$d->annonce->mark] ?? $d->annonce->mark ?? '') . ' ' .
                        ($dBrandModel['modeles'][$d->annonce->modele] ?? $d->annonce->modele ?? '')
                    )
                    : 'Discussion #' . $d->id;
                $items[] = [
                    'icon'  => 'bi-chat-left-dots-fill',
                    'tone'  => 'secondary',
                    'title' => 'Discussion ouverte',
                    'body'  => $label !== '' ? $label : 'Discussion #' . $d->id,
                    'at'    => $d->last_message_at ?: $d->updated_at,
                    'url'   => route('admin.discussions.index'),
                ];
            }
        }

        usort($items, function ($a, $b) {
            return ($b['at']?->timestamp ?? 0) <=> ($a['at']?->timestamp ?? 0);
        });

        return array_slice($items, 0, self::MAX_ITEMS);
    }
}
