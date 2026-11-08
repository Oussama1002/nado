@extends('admin.layouts.app')

@section('title', 'Tableau de bord')

@section('content')
<div class="page-heading">
    <div class="page-title mb-3">
        <h3 class="mb-0">Tableau de bord</h3>
        <p class="text-subtitle text-muted mb-0">Vue d'ensemble de l'activité — mis à jour en direct.</p>
    </div>
</div>

<div class="page-content">

    {{-- ===== Action items : grandes tuiles cliquables (ce qui demande une action aujourd'hui) ===== --}}
    <div class="row mb-1">
        <div class="col-12 col-lg-4 mb-4">
            <a href="{{ route('admin.annonces.approval') }}" class="dash-action dash-action--amber">
                <div class="dash-action__icon"><i class="fa-solid fa-clock-rotate-left"></i></div>
                <div class="dash-action__body">
                    <span class="dash-action__num">{{ $listingsPending }}</span>
                    <span class="dash-action__lbl">Annonces en attente d'approbation</span>
                </div>
                <i class="fa-solid fa-chevron-right dash-action__arrow"></i>
            </a>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <a href="{{ route('admin.clients.approval') }}" class="dash-action dash-action--blue">
                <div class="dash-action__icon"><i class="fa-solid fa-user-check"></i></div>
                <div class="dash-action__body">
                    <span class="dash-action__num">{{ $clientsPending }}</span>
                    <span class="dash-action__lbl">Clients en attente de validation</span>
                </div>
                <i class="fa-solid fa-chevron-right dash-action__arrow"></i>
            </a>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <a href="{{ route('admin.discussions.index') }}" class="dash-action dash-action--green">
                <div class="dash-action__icon"><i class="fa-solid fa-comments"></i></div>
                <div class="dash-action__body">
                    <span class="dash-action__num">{{ $openDiscussions }}</span>
                    <span class="dash-action__lbl">Discussions ouvertes</span>
                </div>
                <i class="fa-solid fa-chevron-right dash-action__arrow"></i>
            </a>
        </div>
    </div>

    {{-- ===== KPI secondaires ===== --}}
    <div class="row">
        <div class="col-6 col-lg-3 mb-4">
            <div class="card dash-kpi">
                <div class="card-body">
                    <span class="dash-kpi__lbl">Annonces en vente</span>
                    <span class="dash-kpi__num text-success">{{ $activeCount }}</span>
                    <span class="dash-kpi__sub">{{ $soldCount }} vendue(s)</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-4">
            <div class="card dash-kpi">
                <div class="card-body">
                    <span class="dash-kpi__lbl">Enchères (7 jours)</span>
                    <span class="dash-kpi__num">{{ $bidsThisWeek }}</span>
                    <span class="dash-kpi__sub">offres déposées</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-4">
            <div class="card dash-kpi">
                <div class="card-body">
                    <span class="dash-kpi__lbl">Estimations</span>
                    <span class="dash-kpi__num">{{ $estimationsTotal }}</span>
                    <span class="dash-kpi__sub">{{ $estimations30d }} ces 30 jours</span>
                </div>
            </div>
        </div>
        <div class="col-6 col-lg-3 mb-4">
            <div class="card dash-kpi">
                <div class="card-body">
                    <span class="dash-kpi__lbl">Total annonces</span>
                    <span class="dash-kpi__num">{{ array_sum($byStatus) }}</span>
                    <span class="dash-kpi__sub">tous statuts</span>
                </div>
            </div>
        </div>
    </div>

    {{-- ===== Graphes ===== --}}
    <div class="row">
        <div class="col-12 col-lg-8 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">Annonces créées — 30 derniers jours</h4></div>
                <div class="card-body">
                    <div style="height: 280px;"><canvas id="dashTrendChart"></canvas></div>
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-4 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">Répartition par statut</h4></div>
                <div class="card-body">
                    @forelse($byStatus as $st => $total)
                        <div class="d-flex justify-content-between align-items-center py-1">
                            <span class="text-capitalize">{{ $st }}</span>
                            <span class="badge bg-light-secondary">{{ $total }}</span>
                        </div>
                    @empty
                        <p class="text-muted mb-0">Aucune annonce.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">Top 5 marques</h4></div>
                <div class="card-body">
                    @if(count($topBrands))
                        <div style="height: 240px;"><canvas id="dashBrandsChart"></canvas></div>
                    @else
                        <p class="text-muted mb-0">Pas encore de données.</p>
                    @endif
                </div>
            </div>
        </div>
        <div class="col-12 col-lg-6 mb-4">
            <div class="card">
                <div class="card-header"><h4 class="mb-0">Top 5 villes</h4></div>
                <div class="card-body">
                    @if(count($topCities))
                        <div style="height: 240px;"><canvas id="dashCitiesChart"></canvas></div>
                    @else
                        <p class="text-muted mb-0">Pas encore de données.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('link')
{{-- Font Awesome : chargé par le site public mais PAS par le thème admin (Mazer). --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<style>
    .dash-action {
        display: flex; align-items: center; gap: 1rem; padding: 1.25rem 1.4rem;
        border-radius: 14px; color: #fff; text-decoration: none; height: 100%;
        box-shadow: 0 8px 22px rgba(15,23,42,.12); transition: transform .12s, box-shadow .12s;
    }
    /* Forcer le blanc sur toute la tuile (le thème colore sinon les spans/liens). */
    .dash-action, .dash-action:hover, .dash-action * { color: #fff !important; }
    .dash-action:hover { transform: translateY(-2px); box-shadow: 0 12px 30px rgba(15,23,42,.18); }
    .dash-action--amber { background: linear-gradient(135deg, #f59e0b, #d97706); }
    .dash-action--blue  { background: linear-gradient(135deg, #3b82f6, #1459DD); }
    .dash-action--green { background: linear-gradient(135deg, #22c55e, #16a34a); }
    .dash-action__icon {
        flex: 0 0 auto; width: 54px; height: 54px; border-radius: 12px;
        background: rgba(255,255,255,.18); display: flex; align-items: center; justify-content: center;
        font-size: 1.5rem;
    }
    .dash-action__body { display: flex; flex-direction: column; line-height: 1.1; flex: 1; }
    .dash-action__num { font-size: 2rem; font-weight: 900; }
    .dash-action__lbl { font-size: .9rem; opacity: .95; font-weight: 600; }
    .dash-action__arrow { opacity: .8; }

    .dash-kpi .card-body { display: flex; flex-direction: column; gap: .15rem; padding: 1.15rem 1.25rem; }
    .dash-kpi__lbl { font-size: .82rem; color: #64748b; font-weight: 700; text-transform: uppercase; letter-spacing: .03em; }
    /* Hérite la couleur de texte de la carte (lisible en thème clair ET sombre). */
    .dash-kpi__num { font-size: 1.9rem; font-weight: 900; color: inherit; line-height: 1.1; }
    .dash-kpi__sub { font-size: .82rem; color: #94a3b8; font-weight: 600; }
</style>
@endsection

@section('script')
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
<script>
(function () {
    if (typeof Chart === 'undefined') return;

    // Couleurs d'axe neutres : lisibles en thème clair comme sombre.
    Chart.defaults.color = '#94a3b8';
    Chart.defaults.borderColor = 'rgba(148,163,184,.18)';

    const BLUE = '#1459DD';
    const trendLabels = @json($trendLabels);
    const trendValues = @json($trendValues);
    const brands = @json($topBrands);
    const cities = @json($topCities);

    const trendEl = document.getElementById('dashTrendChart');
    if (trendEl) {
        new Chart(trendEl, {
            type: 'line',
            data: {
                labels: trendLabels,
                datasets: [{
                    label: 'Annonces créées',
                    data: trendValues,
                    borderColor: BLUE,
                    backgroundColor: 'rgba(20,89,221,.12)',
                    fill: true, tension: .35, pointRadius: 2, borderWidth: 2,
                }],
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    function barChart(id, rows, color) {
        const el = document.getElementById(id);
        if (!el || !rows.length) return;
        new Chart(el, {
            type: 'bar',
            data: {
                labels: rows.map(r => r.label),
                datasets: [{ data: rows.map(r => r.count), backgroundColor: color, borderRadius: 6 }],
            },
            options: {
                indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { x: { beginAtZero: true, ticks: { precision: 0 } } },
            },
        });
    }

    barChart('dashBrandsChart', brands, BLUE);
    barChart('dashCitiesChart', cities, '#16a34a');
})();
</script>
@endsection
