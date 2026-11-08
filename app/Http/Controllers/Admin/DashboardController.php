<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Annonces;
use App\Models\Brand;
use App\Models\Client;
use App\Models\Discussion;
use App\Models\Estimation;
use App\Models\NadoAutoEncheresBid;
use Illuminate\Support\Facades\Cache;

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
        ]);
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
