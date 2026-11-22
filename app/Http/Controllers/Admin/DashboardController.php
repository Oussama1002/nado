<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Demande;
use App\Models\Discussion;
use App\Models\Estimation;
use App\Models\LegalCheck;
use App\Models\NadoAutoEncheresBid;
use App\Models\WhatsAppConversation;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

/**
 * Tableau de bord admin — board opérationnel live.
 *
 * Chaque KPI est une seule requête d'agrégat sur des tables déjà en place.
 * Les compteurs « à traiter » (approbations en attente, discussions ouvertes)
 * restent LIVE pour ne jamais être périmés ; les graphes / tops, plus lourds et
 * moins sensibles, sont mis en cache quelques minutes.
 */
class DashboardController extends Controller
{
    /** TTL court pour les agrégats non urgents (secondes). */
    private const CACHE_TTL = 300; // 5 min

    public function index()
    {
        // --- Action items : TOUJOURS live (jamais de cache) ---
        $listingsPending = Annonces::where('active', false)->count();
        $clientsPending  = Client::where('is_approved', false)->count();
        $openDiscussions = Discussion::where('status', 'open')->count();

        // --- Agrégats « lecture » : cache court ---
        [$byStatus, $activeCount, $soldCount] = Cache::remember(
            'admin.dash.status', self::CACHE_TTL, fn () => $this->listingsByStatus()
        );

        $bidsThisWeek = Cache::remember(
            'admin.dash.bids_week', self::CACHE_TTL,
            fn () => NadoAutoEncheresBid::where('created_at', '>=', now()->subWeek())->count()
        );

        [$estimationsTotal, $estimations30d] = Cache::remember(
            'admin.dash.estimations', self::CACHE_TTL, fn () => [
                Estimation::count(),
                Estimation::where('created_at', '>=', now()->subDays(30))->count(),
            ]
        );

        $topBrands = Cache::remember('admin.dash.top_brands', self::CACHE_TTL, fn () => $this->topBrands());
        $topCities = Cache::remember('admin.dash.top_cities', self::CACHE_TTL, fn () => $this->topCities());

        // Série « annonces créées / jour » sur 30 jours (graphe ligne).
        [$trendLabels, $trendValues] = Cache::remember(
            'admin.dash.trend', self::CACHE_TTL, fn () => $this->listingsTrend(30)
        );

        // --- Notifications live : action items + événements récents ---
        $notifications = $this->recentNotifications();

        return view('admin.dashboard', [
            'listingsPending'  => $listingsPending,
            'clientsPending'   => $clientsPending,
            'openDiscussions'  => $openDiscussions,
            'byStatus'         => $byStatus,
            'activeCount'      => $activeCount,
            'soldCount'        => $soldCount,
            'bidsThisWeek'     => $bidsThisWeek,
            'estimationsTotal' => $estimationsTotal,
            'estimations30d'   => $estimations30d,
            'topBrands'        => $topBrands,
            'topCities'        => $topCities,
            'trendLabels'      => $trendLabels,
            'trendValues'      => $trendValues,
            'notifications'    => $notifications,
        ]);
    }

    /**
     * Fil des notifications : agrège les événements récents nécessitant
     * une action de l'admin ou qui viennent de se produire.
     *
     * Chaque entrée : { icon, tone, title, body, at (Carbon), url }.
     * Jamais caché — toujours live.
     */
    private function recentNotifications(): array
    {
        $items = [];

        // 1) Annonces en attente d'approbation.
        Annonces::where('active', false)
            ->latest('created_at')->limit(10)->get(['id', 'mark', 'modele', 'city', 'created_at'])
            ->each(function ($a) use (&$items) {
                $label = trim(($a->mark ?? '') . ' ' . ($a->modele ?? ''));
                if ($label === '') { $label = 'Annonce #'.$a->id; }
                $items[] = [
                    'icon'  => 'bi-car-front-fill',
                    'tone'  => 'warning',
                    'title' => 'Nouvelle annonce en attente',
                    'body'  => $a->city ? $label . ' — ' . $a->city : $label,
                    'at'    => $a->created_at,
                    'url'   => route('admin.annonces.showAnnonce', $a->id),
                ];
            });

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

        // 3) Demandes (acheteurs) en attente de modération.
        if (Schema::hasTable('demandes')) {
            Demande::where('status', 'pending')
                ->with(['brand:id,name', 'modele:id,name', 'ville:id,name'])
                ->latest('created_at')->limit(10)->get(['id', 'brand_id', 'modele_id', 'ville_id', 'raw_query', 'created_at'])
                ->each(function ($d) use (&$items) {
                    $parts = array_filter([
                        $d->brand?->name,
                        $d->modele?->name,
                        $d->ville?->name,
                    ]);
                    $label = $parts ? implode(' · ', $parts) : ($d->raw_query ?: 'Demande #'.$d->id);
                    $items[] = [
                        'icon'  => 'bi-megaphone-fill',
                        'tone'  => 'primary',
                        'title' => 'Nouvelle demande acheteur',
                        'body'  => \Illuminate\Support\Str::limit($label, 90),
                        'at'    => $d->created_at,
                        'url'   => route('admin.demandes.pending'),
                    ];
                });
        }

        // 4) Estimations (demande expert) — récentes.
        Estimation::with('client:id,name')
            ->latest('created_at')->limit(10)->get(['id', 'client_id', 'brand', 'model', 'year', 'created_at'])
            ->each(function ($e) use (&$items) {
                $items[] = [
                    'icon'  => 'bi-rulers',
                    'tone'  => 'success',
                    'title' => 'Nouvelle demande expert',
                    'body'  => trim(($e->brand ?? '') . ' ' . ($e->model ?? '') . ($e->year ? ' (' . $e->year . ')' : '')
                        . ($e->client ? ' · ' . $e->client->name : '')),
                    'at'    => $e->created_at,
                    'url'   => route('admin.estimations.show', $e->id),
                ];
            });

        // 5) Situation légale (NARSA) en attente.
        if (feature_on('legalcheck.enabled') && Schema::hasTable('legal_checks')) {
            LegalCheck::where('admin_status', 'pending')
                ->latest('created_at')->limit(10)->get(['id', 'matricule_full', 'created_at'])
                ->each(function ($lc) use (&$items) {
                    $items[] = [
                        'icon'  => 'bi-shield-check',
                        'tone'  => 'danger',
                        'title' => 'Vérification légale à traiter',
                        'body'  => 'Matricule : ' . ($lc->matricule_full ?? '#'.$lc->id),
                        'at'    => $lc->created_at,
                        'url'   => route('admin.legalchecks.show', $lc->id),
                    ];
                });
        }

        // 6) WhatsApp — conversations non lues.
        if (feature_on('whatsapp.enabled') && Schema::hasTable('whatsapp_conversations')) {
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

        // 7) Discussions ouvertes — plus récentes (lie à l'annonce concernée).
        if (Schema::hasTable('discussions')) {
            Discussion::where('status', 'open')
                ->with('annonce:id,mark,modele')
                ->latest('last_message_at')->limit(10)
                ->get(['id', 'annonce_id', 'last_message_at', 'updated_at'])
                ->each(function ($d) use (&$items) {
                    $label = $d->annonce
                        ? trim(($d->annonce->mark ?? '') . ' ' . ($d->annonce->modele ?? ''))
                        : 'Discussion #' . $d->id;
                    $items[] = [
                        'icon'  => 'bi-chat-left-dots-fill',
                        'tone'  => 'secondary',
                        'title' => 'Discussion ouverte',
                        'body'  => $label !== '' ? $label : 'Discussion #'.$d->id,
                        'at'    => $d->last_message_at ?: $d->updated_at,
                        'url'   => route('admin.discussions.index'),
                    ];
                });
        }

        // Tri global par date desc, cap à 30.
        usort($items, function ($a, $b) {
            $ta = $a['at']?->timestamp ?? 0;
            $tb = $b['at']?->timestamp ?? 0;
            return $tb <=> $ta;
        });

        return array_slice($items, 0, 30);
    }

    /**
     * Répartition des annonces par statut + raccourcis actif/vendu.
     *
     * @return array{0: array<string,int>, 1: int, 2: int}
     */
    private function listingsByStatus(): array
    {
        $rows = Annonces::selectRaw('LOWER(COALESCE(status, "")) as st, COUNT(*) as total')
            ->groupBy('st')
            ->pluck('total', 'st');

        $byStatus = [];
        foreach ($rows as $st => $total) {
            $byStatus[$st === '' ? 'inconnu' : $st] = (int) $total;
        }

        // "disponible" = en vente ; "vendu" couvre les variantes (vendu/vendue).
        $active = (int) ($byStatus['disponible'] ?? 0);
        $sold   = 0;
        foreach ($byStatus as $st => $total) {
            if (str_starts_with($st, 'vendu')) {
                $sold += $total;
            }
        }

        return [$byStatus, $active, $sold];
    }

    /**
     * Top 5 marques. `annonces.mark` contient un ID de marque (lignes récentes)
     * ou un nom (lignes migrées) : on résout les IDs numériques en noms.
     *
     * @return array<int, array{label: string, count: int}>
     */
    private function topBrands(): array
    {
        $rows = Annonces::selectRaw('mark, COUNT(*) as total')
            ->whereNotNull('mark')
            ->where('mark', '!=', '')
            ->groupBy('mark')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        // Résolution en un seul SELECT des IDs numériques.
        $ids = $rows->pluck('mark')->filter(fn ($m) => is_numeric($m))->all();
        $names = $ids
            ? Brand::whereIn('id', $ids)->pluck('name', 'id')
            : collect();

        return $rows->map(fn ($r) => [
            'label' => is_numeric($r->mark) ? ($names[$r->mark] ?? ('#' . $r->mark)) : $r->mark,
            'count' => (int) $r->total,
        ])->all();
    }

    /**
     * Top 5 villes. `annonces.city` est un NOM (pas un ID).
     *
     * @return array<int, array{label: string, count: int}>
     */
    private function topCities(): array
    {
        return Annonces::selectRaw('city, COUNT(*) as total')
            ->whereNotNull('city')
            ->where('city', '!=', '')
            ->groupBy('city')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->map(fn ($r) => ['label' => $r->city, 'count' => (int) $r->total])
            ->all();
    }

    /**
     * Annonces créées par jour sur les N derniers jours (série dense : les jours
     * sans annonce valent 0 pour que le graphe reste continu).
     *
     * @return array{0: array<int,string>, 1: array<int,int>}
     */
    private function listingsTrend(int $days): array
    {
        $since = now()->subDays($days - 1)->startOfDay();

        $counts = Annonces::where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as d, COUNT(*) as total')
            ->groupBy('d')
            ->pluck('total', 'd');

        $labels = [];
        $values = [];
        for ($i = 0; $i < $days; $i++) {
            $day = $since->copy()->addDays($i);
            $key = $day->format('Y-m-d');
            $labels[] = $day->format('d/m');
            $values[] = (int) ($counts[$key] ?? 0);
        }

        return [$labels, $values];
    }
}
