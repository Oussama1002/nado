@extends('layouts.app')

@section('title', 'Mon Espace | NADO')

{{-- Navbar : client enchère (flotte&enchere) → navbar Auto-Enchère (fixée en
     haut comme la navbar NADO) ; sinon navbar NADO standard. --}}
@section('navbar')
    @php $navIsFlotte = Auth::guard('client')->user()?->type === 'flotte&enchere'; @endphp
    {{-- Pas de spacer ici : la navbar est fixe (hors flux) et .dash-main a déjà
         padding-top: var(--navH) à tous les breakpoints → un seul espacement,
         aligné avec le haut de la sidebar (évite la bande vide en haut). --}}
    @if($navIsFlotte)
        <script src="https://cdn.tailwindcss.com/3.4.16"></script>
        {{-- preflight OFF : évite que le reset global de Tailwind casse le CSS du dashboard --}}
        <script>tailwind.config = { corePlugins: { preflight: false } };</script>
        <div style="position:fixed;top:0;left:0;width:100%;z-index:1000;">
            @include('nadoautoenchere.partials.navbar')
        </div>
    @else
        @include('partials.nado-navbar')
    @endif
@endsection

@section('content')
@php
    $isFlotte = Auth::guard('client')->user()->type === 'flotte&enchere';
    $client   = Auth::guard('client')->user();
    $logo     = $client->logo ?? null;
    if (is_string($logo) && str_starts_with($logo, '[')) {
        $decoded = json_decode($logo, true);
        $logo    = $decoded[0] ?? null;
    }
@endphp

<style>
/* ── Reset & vars ─────────────────────────────────────────── */
*, *::before, *::after { box-sizing: border-box; }

:root {
    --blue:    #1459DD;
    --yellow:  #FDD817;
    --slate-50:#f8fafc; --slate-100:#f1f5f9; --slate-200:#e2e8f0;
    --slate-400:#94a3b8; --slate-500:#64748b; --slate-600:#475569;
    --slate-700:#334155; --slate-800:#1e293b; --slate-900:#0f172a;
    --white:   #ffffff;
    --radius:  14px;
    --shadow:  0 1px 3px rgba(0,0,0,.06), 0 4px 16px rgba(0,0,0,.06);
    --navH: {{ $isFlotte ? '116px' : '140px' }}; /* hauteur navbar fixe (AE plus courte) */
}

body { background: var(--slate-50); font-family: 'Inter', sans-serif; color: var(--slate-900); }

/* ── Shell ────────────────────────────────────────────────── */
.dash-shell { display: flex; min-height: 100vh; }

/* ── Sidebar ──────────────────────────────────────────────── */
.dash-sidebar {
    width: 260px;
    background: var(--slate-900);
    display: flex;
    flex-direction: column;
    position: fixed;
    top: var(--navH);
    left: 0;
    height: calc(100vh - var(--navH));
    z-index: 200;
    transition: transform .3s cubic-bezier(.4,0,.2,1);
    overflow-y: auto;
}

.dash-brand {
    display: flex;
    align-items: center;
    gap: 10px;
    padding: 24px 24px 20px;
    border-bottom: 1px solid rgba(255,255,255,.07);
    flex-shrink: 0;
}
.dash-brand-logo {
    font-size: 22px;
    font-weight: 900;
    letter-spacing: -.04em;
    line-height: 1;
}
.dash-brand-logo .na { color: #ffffff; }
.dash-brand-logo .do { color: var(--yellow); }
.dash-brand-sub { display:none; }

.dash-profile {
    padding: 20px 24px 16px;
    display: flex;
    align-items: center;
    gap: 14px;
    border-bottom: 1px solid rgba(255,255,255,.07);
}
.dash-avatar {
    width: 44px; height: 44px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid rgba(255,255,255,.2);
    flex-shrink: 0;
}
.dash-profile-name {
    font-size: 14px;
    font-weight: 700;
    color: #fff;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}
.dash-profile-type {
    font-size: 11px;
    font-weight: 600;
    color: rgba(255,255,255,.45);
    text-transform: capitalize;
}

.dash-nav { flex: 1; padding: 12px 12px; }
.dash-nav-section { margin-bottom: 6px; }
.dash-nav-label {
    font-size: 10px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .12em;
    color: rgba(255,255,255,.28);
    padding: 10px 12px 6px;
}
.dash-nav-link {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 10px 12px;
    border-radius: 10px;
    color: rgba(255,255,255,.62);
    text-decoration: none;
    font-size: 13.5px;
    font-weight: 600;
    transition: all .15s;
    margin-bottom: 2px;
}
.dash-nav-link i.fa { width: 18px; text-align: center; font-size: 14px; }
.dash-nav-link:hover, .dash-nav-link.active {
    background: rgba(255,255,255,.09);
    color: #fff;
}
.dash-nav-link.active { background: rgba(20,89,221,.55); color: #fff; }
.dash-nav-link.danger { color: rgba(248,113,113,.8); }
.dash-nav-link.danger:hover { background: rgba(239,68,68,.15); color: #f87171; }

.dash-nav-link form { margin: 0; width: 100%; }
.dash-nav-link form button {
    display: flex;
    align-items: center;
    gap: 12px;
    background: none;
    border: none;
    color: inherit;
    font: inherit;
    width: 100%;
    cursor: pointer;
    padding: 0;
}

/* ── Main content ─────────────────────────────────────────── */
.dash-main {
    margin-left: 260px;
    flex: 1;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
    /* layouts.navigation (fixed) overlaps content otherwise */
    padding-top: var(--navH);
}

/* ── Top bar ──────────────────────────────────────────────── */
.dash-topbar {
    position: sticky;
    /* keep below the fixed global navbar */
    top: var(--navH);
    z-index: 100;
    background: rgba(248,250,252,.9);
    backdrop-filter: blur(12px);
    border-bottom: 1px solid var(--slate-200);
    padding: 14px 32px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.dash-topbar-title { font-size: 18px; font-weight: 800; color: var(--slate-900); }
.dash-topbar-actions { display: flex; align-items: center; gap: 10px; }
.btn-primary-dash {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 18px;
    border-radius: 12px;
    background: var(--blue);
    color: #fff;
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: background .15s, transform .1s;
    white-space: nowrap;
}
.btn-primary-dash:hover { background: #0f4bcc; color: #fff; }
.btn-primary-dash:active { transform: scale(.97); }
.btn-outline-dash {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 9px 16px;
    border-radius: 12px;
    background: transparent;
    color: var(--slate-600);
    font-size: 13px;
    font-weight: 700;
    text-decoration: none;
    border: 1px solid var(--slate-200);
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.btn-outline-dash:hover { background: var(--slate-100); color: var(--slate-900); }

/* ── Page body ────────────────────────────────────────────── */
.dash-body { padding: 28px 32px; flex: 1; }

/* ── Alert ────────────────────────────────────────────────── */
.dash-alert {
    padding: 12px 16px;
    border-radius: 12px;
    margin-bottom: 20px;
    font-size: 13.5px;
    font-weight: 600;
}
.dash-alert.success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
.dash-alert.error   { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

/* ── Stat cards ───────────────────────────────────────────── */
.stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 16px; margin-bottom: 28px; }

.stat-card {
    background: var(--white);
    border-radius: var(--radius);
    padding: 20px;
    box-shadow: var(--shadow);
    border: 1px solid var(--slate-200);
    display: flex;
    align-items: flex-start;
    gap: 14px;
}
.stat-icon {
    width: 42px; height: 42px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 18px;
}
.stat-icon.blue   { background: #dbeafe; color: var(--blue); }
.stat-icon.yellow { background: #fefce8; color: #ca8a04; }
.stat-icon.green  { background: #f0fdf4; color: #16a34a; }
.stat-icon.red    { background: #fef2f2; color: #dc2626; }
.stat-label { font-size: 12px; font-weight: 600; color: var(--slate-500); text-transform: uppercase; letter-spacing: .05em; }
.stat-value { font-size: 28px; font-weight: 900; color: var(--slate-900); line-height: 1.1; margin-top: 2px; }

/* ── Section header ───────────────────────────────────────── */
.section-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 12px;
}
.section-title { font-size: 16px; font-weight: 800; color: var(--slate-900); }

/* ── Table card ───────────────────────────────────────────── */
.table-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--slate-200);
    overflow: hidden;
    margin-bottom: 28px;
}
.table-wrap { overflow-x: auto; }
table.dash-table {
    width: 100%;
    border-collapse: collapse;
    min-width: 560px;
}
table.dash-table th {
    padding: 12px 16px;
    text-align: left;
    font-size: 11px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: .07em;
    color: var(--slate-500);
    background: var(--slate-50);
    border-bottom: 1px solid var(--slate-200);
}
table.dash-table td {
    padding: 14px 16px;
    font-size: 13.5px;
    color: var(--slate-700);
    border-bottom: 1px solid var(--slate-100);
    vertical-align: middle;
}
table.dash-table tr:last-child td { border-bottom: none; }
table.dash-table tr:hover td { background: #fafbfd; }

/* ── Status badges ────────────────────────────────────────── */
.badge {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 4px 10px;
    border-radius: 99px;
    font-size: 11px;
    font-weight: 700;
    white-space: nowrap;
}
.badge-active  { background: #f0fdf4; color: #16a34a; }
.badge-pending { background: #fefce8; color: #a16207; }
.badge-refused { background: #fef2f2; color: #dc2626; }

/* ── Table action buttons ─────────────────────────────────── */
.tbl-btn {
    display: inline-flex;
    align-items: center;
    gap: 5px;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 700;
    text-decoration: none;
    border: none;
    cursor: pointer;
    transition: all .15s;
    white-space: nowrap;
}
.tbl-btn-edit  { background: var(--slate-100); color: var(--slate-700); }
.tbl-btn-edit:hover  { background: #dbeafe; color: var(--blue); }
.tbl-btn-del   { background: #fef2f2; color: #dc2626; }
.tbl-btn-del:hover   { background: #fee2e2; }

/* ── Chart card ───────────────────────────────────────────── */
.chart-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--slate-200);
    padding: 20px;
    margin-bottom: 28px;
}
.chart-title { font-size: 14px; font-weight: 800; color: var(--slate-900); margin-bottom: 16px; }
/* Fixed-height box: with height="80" + keep-aspect-ratio the chart was ~80px tall on phones */
.chart-box { position: relative; height: 260px; }

/* ── Empty state ──────────────────────────────────────────── */
.empty-state { padding: 48px 24px; text-align: center; color: var(--slate-400); }
.empty-state .empty-icon { font-size: 36px; margin-bottom: 10px; }
.empty-state p { font-size: 14px; font-weight: 600; }

/* ── Annonce card grid ────────────────────────────────────── */
.car-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 16px; margin-bottom: 28px; }
.car-card {
    background: var(--white);
    border-radius: var(--radius);
    box-shadow: var(--shadow);
    border: 1px solid var(--slate-200);
    overflow: hidden;
    transition: box-shadow .2s, transform .2s;
}
.car-card:hover { box-shadow: 0 8px 30px rgba(0,0,0,.1); transform: translateY(-2px); }
.car-card-img {
    width: 100%; height: 180px;
    object-fit: cover;
    display: block;
}
.car-card-img-placeholder {
    width: 100%; height: 180px;
    background: var(--slate-100);
    display: flex; align-items: center; justify-content: center;
    font-size: 32px; color: var(--slate-400);
}
.car-card-body { padding: 16px; }
.car-card-ref { font-size: 11px; font-weight: 700; color: var(--slate-400); text-transform: uppercase; margin-bottom: 4px; }
.car-card-title { font-size: 15px; font-weight: 800; color: var(--slate-900); margin-bottom: 8px; }
.car-card-meta { font-size: 12px; color: var(--slate-500); margin-bottom: 12px; }
.car-card-footer {
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 8px;
    padding-top: 12px;
    border-top: 1px solid var(--slate-100);
}

/* ── Mobile overlay sidebar ───────────────────────────────── */
.dash-sidebar-overlay {
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(15,23,42,.5);
    z-index: 190;
    backdrop-filter: blur(2px);
}

/* ── Vente popup (iframe) ───────────────────────────────────── */
.vente-frame-modal {
    position: fixed;
    inset: 0;
    z-index: 5000;
    display: none;
}
.vente-frame-modal.open { display: block; }
/* Same backdrop as the vente modal, shown only while the iframe loads */
.vente-frame-loading {
    position: absolute;
    inset: 0;
    background: rgba(15,23,42,.85);
    backdrop-filter: blur(12px);
    display: flex; align-items: center; justify-content: center;
}
.vente-frame-loading.hidden { display: none; }
.vente-frame-spinner {
    width: 42px; height: 42px;
    border: 4px solid rgba(255,255,255,.25);
    border-top-color: #fff;
    border-radius: 50%;
    animation: venteFrameSpin .8s linear infinite;
}
@keyframes venteFrameSpin { to { transform: rotate(360deg); } }
.vente-frame-iframe {
    position: absolute;
    inset: 0;
    width: 100%; height: 100%;
    border: 0; display: block;
    background: transparent;
    color-scheme: normal;
}
.dash-mobile-toggle {
    display: none;
    align-items: center;
    gap: 8px;
    padding: 8px 12px;
    border-radius: 10px;
    background: var(--white);
    border: 1px solid var(--slate-200);
    cursor: pointer;
    font-size: 14px;
    color: var(--slate-700);
}

@media (max-width: 900px) {
    .dash-sidebar { transform: translateX(-100%); }
    .dash-sidebar.open { transform: translateX(0); }
    .dash-sidebar-overlay.open { display: block; }
    .dash-main { margin-left: 0; padding-top: var(--navH); }
    .dash-mobile-toggle { display: flex; }
    .dash-topbar { padding: 12px 16px; top: var(--navH); }
    .dash-body { padding: 16px; }
    .stat-grid { grid-template-columns: repeat(2, 1fr); }
}
/* ── Mobile / tablet ─────────────────────────────────────────
   The global navbar is shorter below 1024px and its own spacer already pushes the
   page down, so the extra padding-top left a big empty band above the dashboard. */
@media (max-width: 1023px) {
    :root { --navH: {{ $isFlotte ? '116px' : '94px' }}; }
    /* La navbar fixe n'a plus de spacer → on garde l'espacement via dash-main. */
    .dash-main { padding-top: var(--navH); }
}
@media (max-width: 640px) {
    /* Top bar: not sticky on phones — its sticky offset assumed a fixed navbar height,
       so a taller navbar covered it and cut "Publier une annonce" off.
       Menu button + full-width "Publier une annonce"; no title / "Site" (the logo does that). */
    .dash-topbar { position: static; padding: 10px 12px; gap: 8px; }
    .dash-topbar > div:first-child { flex-shrink: 0; }
    .dash-topbar-title { display: none; }
    .dash-topbar-actions { flex: 1; min-width: 0; }
    .dash-topbar-actions .btn-outline-dash { display: none; }
    .dash-topbar-actions .btn-primary-dash { flex: 1; justify-content: center; padding: 11px 14px; font-size: 13px; }
    .dash-body { padding: 14px 12px 24px; }

    /* Stats: 2 compact columns instead of 4 tall rows */
    .stat-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; margin-bottom: 20px; }
    .stat-card { padding: 14px; gap: 10px; flex-direction: column; }
    .stat-icon { width: 34px; height: 34px; font-size: 15px; border-radius: 10px; }
    .stat-label { font-size: 10.5px; }
    .stat-value { font-size: 22px; }

    /* Nothing may make the page wider than the screen (the right side, where the
       buttons are, was cut off) */
    .dash-main { min-width: 0; max-width: 100vw; overflow-x: hidden; }

    .section-header { flex-wrap: wrap; }
    .section-title { font-size: 15px; }
    .section-header .btn-primary-dash { padding: 8px 12px !important; font-size: 12px !important; }

    /* Tables → stacked cards (no sideways scrolling) */
    .table-wrap { overflow-x: visible; }
    table.dash-table { min-width: 0; }
    table.dash-table thead { display: none; }
    table.dash-table, table.dash-table tbody, table.dash-table tr, table.dash-table td { display: block; width: 100%; }
    table.dash-table tr { padding: 12px 14px; border-bottom: 1px solid var(--slate-100); }
    table.dash-table tr:last-child { border-bottom: none; }
    table.dash-table tr:hover td { background: none; }
    table.dash-table td {
        display: flex; align-items: center; justify-content: space-between; gap: 12px;
        padding: 5px 0; border: none; max-width: none !important; text-align: right !important;
        white-space: normal !important;
    }
    table.dash-table td::before {
        content: attr(data-label);
        font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: .05em;
        color: var(--slate-400); text-align: left; flex-shrink: 0;
    }
    /* First cell = title, full width, no label */
    table.dash-table td:first-child { font-size: 14.5px; padding-bottom: 8px; text-align: left !important; }
    table.dash-table td:first-child::before { display: none; }
    /* Actions: buttons side by side, full width */
    table.dash-table td.td-actions { justify-content: flex-end; padding-top: 10px; }
    table.dash-table td.td-actions::before { display: none; }

    .car-grid { grid-template-columns: 1fr; gap: 12px; }
    .car-card-img, .car-card-img-placeholder { height: 170px; }

    .chart-card { padding: 14px; margin-bottom: 20px; }
    .chart-box { height: 220px; }
}
</style>

<div class="dash-shell">

    {{-- ══ SIDEBAR ══════════════════════════════════════════ --}}
    <aside class="dash-sidebar" id="dashSidebar">
        {{-- logo block removed --}}

        <div class="dash-profile">
            @if($logo)
                <img src="{{ asset('storage/nadoautoenchere/logo/' . $logo) }}" alt="Avatar" class="dash-avatar">
            @else
                <img src="{{ asset('assets/images/user.png') }}" alt="Avatar" class="dash-avatar">
            @endif
            <div style="overflow:hidden;">
                <div class="dash-profile-name">{{ $client->name }}</div>
                <div class="dash-profile-type">
                    {{ $client->type ?? 'Particulier' }}
                    @if($isFlotte && $client->ae_role)
                        <span style="margin-left:6px;padding:1px 8px;border-radius:99px;background:rgba(255,255,255,.16);font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;">{{ $client->ae_role === 'vendeur' ? 'Vendeur' : 'Acheteur' }}</span>
                    @endif
                </div>
            </div>
        </div>

        <nav class="dash-nav">
            <div class="dash-nav-section">
                <div class="dash-nav-label">{{ __('messages.dashboard.overview_label') }}</div>
                <a href="{{ route('client.dashboard') }}" class="dash-nav-link active">
                    <i class="fa fa-th-large"></i> {{ __('messages.dashboard.overview') }}
                </a>
            </div>

            @if($isFlotte)
            <div class="dash-nav-section">
                <div class="dash-nav-label">{{ __('messages.dashboard.my_vehicles') }}</div>
                <a href="javascript:void(0)" class="dash-nav-link" onclick="openVenteFrame('{{ route('vente.form', ['open_modal' => 1]) }}')">
                    <i class="fa fa-plus-circle"></i> {{ __('messages.dashboard.publish_ad') }}
                </a>
                <a href="{{ route('client.nadoautoencheres.index') }}" class="dash-nav-link">
                    <i class="fa fa-car"></i> {{ __('messages.dashboard.my_vehicles') }}
                </a>
                <a href="{{ route('client.nadoautoencheresbid.mybids') }}" class="dash-nav-link">
                    <i class="fa fa-gavel"></i> {{ __('messages.dashboard.my_bids') }}
                </a>
                <a href="{{ route('client.nadoautoencheresbid.index') }}" class="dash-nav-link">
                    <i class="fa fa-list"></i> {{ __('messages.dashboard.bids_received') }}
                </a>
                <a href="{{ route('client.equipe.index') }}" class="dash-nav-link">
                    <i class="fa fa-users"></i> {{ __('messages.dashboard.my_team') }}
                </a>
            </div>
            @else
            <div class="dash-nav-section">
                <div class="dash-nav-label">{{ __('messages.dashboard.my_ads') }}</div>
                <a href="javascript:void(0)" class="dash-nav-link" onclick="openVenteFrame('{{ route('vente.form', ['open_modal' => 1]) }}')">
                    <i class="fa fa-plus-circle"></i> {{ __('messages.dashboard.publish_ad') }}
                </a>
                <a href="{{ route('client.annonces.index') }}" class="dash-nav-link">
                    <i class="fa fa-car"></i> {{ __('messages.dashboard.my_ads') }}
                </a>
            </div>
            @endif

            <div class="dash-nav-section">
                <div class="dash-nav-label">{{ __('messages.dashboard.account') }}</div>
                <a href="{{ route('client.profile.edit') }}" class="dash-nav-link">
                    <i class="fa fa-user"></i> {{ __('messages.dashboard.my_profile') }}
                </a>
                <a href="{{ route('client.favorites') }}" class="dash-nav-link">
                    <i class="fa fa-heart"></i> {{ __('messages.dashboard.my_favorites') }}
                </a>
                <a href="{{ route('client.discussions.index') }}" class="dash-nav-link">
                    <i class="fa fa-comments"></i> {{ __('messages.dashboard.discussions') }}
                </a>
            </div>

            <div class="dash-nav-section" style="margin-top:auto;padding-top:8px;border-top:1px solid rgba(255,255,255,.07);">
                <div class="dash-nav-link danger">
                    <form method="POST" action="{{ route('logout') }}" style="width:100%">
                        @csrf
                        <button type="submit"><i class="fa fa-sign-out"></i> {{ __('messages.dashboard.logout') }}</button>
                    </form>
                </div>
            </div>
        </nav>
    </aside>

    {{-- overlay for mobile --}}
    <div class="dash-sidebar-overlay" id="dashOverlay" onclick="closeSidebar()"></div>

    {{-- ══ MAIN ═══════════════════════════════════════════════ --}}
    <div class="dash-main">

        {{-- Barre supérieure retirée. On conserve uniquement le bouton « Menu »
             (mobile) pour ouvrir la sidebar ; .dash-mobile-toggle est masqué sur
             desktop, donc rien ne s'affiche en haut sur grand écran. --}}
        <button class="dash-mobile-toggle" onclick="toggleSidebar()" style="margin:14px 16px 0;">
            <i class="fa fa-bars"></i> {{ __('messages.dashboard.menu') }}
        </button>

        {{-- ── Body ───────────────────────────────────────── --}}
        <div class="dash-body">

            {{-- Flash messages --}}
            @if(session('success'))
                <div class="dash-alert success">
                    <i class="fa fa-check-circle" style="margin-right:6px;"></i>{{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="dash-alert error">
                    <i class="fa fa-exclamation-circle" style="margin-right:6px;"></i>{{ session('error') }}
                </div>
            @endif

            {{-- ── Stat cards ─────────────────────────────── --}}
            <div class="stat-grid">
                <div class="stat-card">
                    <div class="stat-icon blue"><i class="fa fa-car"></i></div>
                    <div>
                        <div class="stat-label">{{ $isFlotte ? __('messages.dashboard.vehicles_published') : __('messages.dashboard.ads_published') }}</div>
                        <div class="stat-value">{{ $publiees }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon yellow"><i class="fa fa-clock-o"></i></div>
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard.pending') }}</div>
                        <div class="stat-value">{{ $enAttente }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon green"><i class="fa fa-heart"></i></div>
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard.favorites') }}</div>
                        <div class="stat-value">{{ count($favoris) }}</div>
                    </div>
                </div>
                <div class="stat-card">
                    <div class="stat-icon red"><i class="fa fa-eye"></i></div>
                    <div>
                        <div class="stat-label">{{ __('messages.dashboard.total_views') }}</div>
                        <div class="stat-value">{{ collect($isFlotte ? $vehicules : $annonces)->sum('views') }}</div>
                    </div>
                </div>
            </div>

            {{-- ── Annonces table ──────────────────────────── --}}
            <div class="section-header">
                <span class="section-title">{{ $isFlotte ? __('messages.dashboard.my_vehicles') : __('messages.dashboard.my_ads') }}</span>
                <a href="javascript:void(0)" class="btn-primary-dash" style="padding:8px 14px;font-size:12px;"
                   onclick="openVenteFrame('{{ route('vente.form', ['open_modal' => 1]) }}')">
                    <i class="fa fa-plus"></i> {{ __('messages.dashboard.publish_ad') }}
                </a>
            </div>

            <div class="table-card">
                @if(($isFlotte ? $vehicules : $annonces)->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">🚗</div>
                        <p>{{ __('messages.dashboard.no_ads') }}</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.dashboard.title') }}</th>
                                    <th>{{ __('messages.dashboard.date') }}</th>
                                    <th>{{ __('messages.dashboard.status') }}</th>
                                    <th>{{ __('messages.dashboard.views') }}</th>
                                    <th style="text-align:right;">{{ __('messages.dashboard.actions') }}</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($isFlotte ? $vehicules : $annonces as $item)
                                    @php
                                        $status = strtolower($item->status ?? 'en attente');
                                        $badgeClass = match(true) {
                                            str_contains($status, 'activ') || str_contains($status, 'publi') => 'badge-active',
                                            str_contains($status, 'refus') || str_contains($status, 'rejet') => 'badge-refused',
                                            default => 'badge-pending',
                                        };
                                        $badgeDot = match($badgeClass) {
                                            'badge-active'  => '●',
                                            'badge-refused' => '●',
                                            default         => '●',
                                        };
                                    @endphp
                                    <tr>
                                        <td style="font-weight:700;color:var(--slate-900);max-width:220px;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;">
                                            {{ $item->titre }}
                                        </td>
                                        <td data-label="{{ __('messages.dashboard.date') }}" style="white-space:nowrap;font-size:12px;color:var(--slate-500);">
                                            {{ $item->created_at->format('d/m/Y') }}
                                        </td>
                                        <td data-label="{{ __('messages.dashboard.status') }}">
                                            <span class="badge {{ $badgeClass }}">
                                                {{ $badgeDot }} {{ $item->status ? ucfirst($item->status) : __('messages.dashboard.pending') }}
                                            </span>
                                        </td>
                                        <td data-label="{{ __('messages.dashboard.views') }}" style="font-weight:700;">{{ $item->views ?? 0 }}</td>
                                        <td class="td-actions" style="text-align:right;white-space:nowrap;">
                                            <a href="{{ $isFlotte ? route('client.nadoautoencheres.edit', $item->id) : 'javascript:void(0)' }}"
                                               class="tbl-btn tbl-btn-edit"
                                               @if(!$isFlotte) onclick="openVenteFrame('{{ route('vente.form', ['open_modal' => 1, 'annonce_id' => $item->id]) }}')" @endif>
                                                <i class="fa fa-pencil"></i> {{ __('messages.dashboard.edit') }}
                                            </a>
                                            <form action="{{ $isFlotte ? route('client.nadoautoencheres.destroy', $item->id) : route('client.annonces.destroy', $item->id) }}"
                                                  method="POST" style="display:inline-block;margin-left:6px;"
                                                  onsubmit="return confirm('{{ __('messages.dashboard.confirm_delete') }}');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="tbl-btn tbl-btn-del">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            {{-- ── Vehicle cards (visual preview) ─────────── --}}
            @if(!($isFlotte ? $vehicules : $annonces)->isEmpty())
            <div class="section-header" style="margin-top:8px;">
                <span class="section-title">{{ __('messages.dashboard.visual_preview') }}</span>
            </div>
            <div class="car-grid">
                @foreach($isFlotte ? $vehicules : $annonces as $item)
                    @php
                        $images     = is_string($item->images) ? json_decode($item->images, true) : $item->images;
                        $images     = is_array($images) ? $images : [];
                        $firstImage = $images[0] ?? null;
                    @endphp
                    <div class="car-card">
                        @if($firstImage)
                            <img class="car-card-img"
                                 src="{{ $isFlotte ? Storage::url('nadoautoenchere/' . $firstImage) : Storage::url('ads/' . $firstImage) }}"
                                 alt="{{ $item->titre }}"
                                 onerror="this.style.display='none';this.nextElementSibling.style.display='flex';">
                            <div class="car-card-img-placeholder" style="display:none;">🚗</div>
                        @else
                            <div class="car-card-img-placeholder">🚗</div>
                        @endif
                        <div class="car-card-body">
                            <div class="car-card-ref">Réf: #VC{{ $item->id }}</div>
                            <div class="car-card-title">
                                {{ strtoupper(optional($item->brand)->name ?? '') }}
                                {{ strtoupper(optional($item->modeleRelation)->name ?? '') }}
                            </div>
                            <div class="car-card-meta">
                                {{ $item->year ?? '—' }} · {{ number_format($item->km ?? 0, 0, ',', ' ') }} km
                                @if($item->carburant) · {{ ucfirst($item->carburant) }} @endif
                            </div>
                            <div class="car-card-footer">
                                <span class="badge {{ $badgeClass ?? 'badge-pending' }}">
                                    {{ $item->status ? ucfirst($item->status) : __('messages.dashboard.pending') }}
                                </span>
                                <a href="{{ $isFlotte ? route('client.nadoautoencheres.edit', $item->id) : 'javascript:void(0)' }}"
                                   class="tbl-btn tbl-btn-edit"
                                   @if(!$isFlotte) onclick="openVenteFrame('{{ route('vente.form', ['open_modal' => 1, 'annonce_id' => $item->id]) }}')" @endif>
                                    <i class="fa fa-pencil"></i> {{ __('messages.dashboard.edit') }}
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            @endif

            {{-- ── Charts ──────────────────────────────────── --}}
            @if($isFlotte)
            <div class="chart-card">
                <div class="chart-title">📈 {{ __('messages.dashboard.chart_offers') }}</div>
                <div class="chart-box"><canvas id="offersChart"></canvas></div>
            </div>
            @endif

            <div class="chart-card">
                <div class="chart-title">📊 {{ __('messages.dashboard.chart_views') }}</div>
                <div class="chart-box"><canvas id="viewsChart"></canvas></div>
            </div>

            {{-- ── Favoris ─────────────────────────────────── --}}
            <div class="section-header" style="margin-top:4px;">
                <span class="section-title">{{ __('messages.dashboard.my_favorites') }}</span>
                <a href="{{ route('client.favorites') }}" class="btn-outline-dash" style="padding:7px 12px;font-size:12px;">{{ __('messages.dashboard.see_all') }}</a>
            </div>

            <div class="table-card">
                @if($favoris->isEmpty())
                    <div class="empty-state">
                        <div class="empty-icon">❤️</div>
                        <p>{{ __('messages.dashboard.no_favorites') }}</p>
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="dash-table">
                            <thead>
                                <tr>
                                    <th>{{ __('messages.dashboard.title') }}</th>
                                    <th>{{ __('messages.dashboard.added_on') }}</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($favoris as $fav)
                                    <tr>
                                        <td style="font-weight:700;">{{ $fav->titre ?? 'N/A' }}</td>
                                        <td data-label="{{ __('messages.dashboard.added_on') }}" style="font-size:12px;color:var(--slate-500);">{{ optional($fav->pivot->created_at)->format('d/m/Y') ?? '—' }}</td>
                                        <td class="td-actions" style="text-align:right;">
                                            <a href="{{ $isFlotte
                                                    ? route('nadoautoenchere.show', $fav->id)
                                                    : route('achat.detail', ['marque' => str_replace(' ', '', $fav->mark), 'modele' => str_replace(' ', '', $fav->modele), 'id' => $fav['id']]) }}"
                                               class="tbl-btn tbl-btn-edit">
                                                <i class="fa fa-eye"></i> {{ __('messages.dashboard.view') }}
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>{{-- /dash-body --}}
    </div>{{-- /dash-main --}}
</div>{{-- /dash-shell --}}

{{-- ── Vente popup (iframe) ─────────────────────────────────── --}}
{{-- The iframe loads /vente/formulaire?embed=1, which renders only the vente
     modal on a transparent page — the modal itself is all the user sees. --}}
<div class="vente-frame-modal" id="venteFrameModal" aria-hidden="true">
    <div class="vente-frame-loading" id="venteFrameLoading" onclick="closeVenteFrame()">
        <span class="vente-frame-spinner"></span>
    </div>
    <iframe class="vente-frame-iframe" id="venteFrame" src="about:blank" title="{{ __('messages.dashboard.publish_ad') }}"></iframe>
</div>

{{-- ── Scripts ──────────────────────────────────────────────── --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// ── Charts ─────────────────────────────────────────────────────
const chartDefaults = {
    responsive: true,
    maintainAspectRatio: false, // fill .chart-box height (was ~80px tall on phones)
    plugins: {
        legend: { display: false },
        tooltip: { callbacks: { label: ctx => ` ${ctx.parsed.y}` } }
    },
    scales: {
        x: { grid: { display: false }, ticks: { color: '#64748b', font: { size: 11, weight: '600' } } },
        y: {
            beginAtZero: true,
            ticks: { color: '#64748b', font: { size: 11 }, stepSize: 1,
                     callback: v => Number.isInteger(v) ? v : null },
            grid: { color: '#f1f5f9' }
        }
    }
};

@if($isFlotte)
const offersCtx = document.getElementById('offersChart')?.getContext('2d');
if (offersCtx) {
    new Chart(offersCtx, {
        type: 'bar',
        data: {
            labels: @json($offersLabels),
            datasets: [{ label: 'Offres', data: @json($offersData),
                         backgroundColor: 'rgba(20,89,221,.18)', borderColor: '#1459DD',
                         borderWidth: 2, borderRadius: 8 }]
        },
        options: chartDefaults
    });
}
@endif

const viewsCtx = document.getElementById('viewsChart')?.getContext('2d');
if (viewsCtx) {
    new Chart(viewsCtx, {
        type: 'bar',
        data: {
            labels: @json($viewsLabels),
            datasets: [{ label: 'Vues', data: @json($viewsData),
                         backgroundColor: 'rgba(20,89,221,.12)', borderColor: '#1459DD',
                         borderWidth: 2, borderRadius: 8 }]
        },
        options: chartDefaults
    });
}

// ── Mobile sidebar ──────────────────────────────────────────────
function toggleSidebar() {
    document.getElementById('dashSidebar').classList.toggle('open');
    document.getElementById('dashOverlay').classList.toggle('open');
}
function closeSidebar() {
    document.getElementById('dashSidebar').classList.remove('open');
    document.getElementById('dashOverlay').classList.remove('open');
}

// ── Vente popup (iframe) ─────────────────────────────────────────
function openVenteFrame(url) {
    var modal = document.getElementById('venteFrameModal');
    var frame = document.getElementById('venteFrame');
    if (!modal || !frame) return;
    var loading = document.getElementById('venteFrameLoading');
    loading.classList.remove('hidden');
    frame.onload = function () {
        if (frame.src !== 'about:blank') loading.classList.add('hidden');
    };
    // embed=1 → the page renders only the vente modal ; lang= pour garder la langue dans l'iframe
    var lang = (document.documentElement.getAttribute('lang') || 'fr').slice(0, 2);
    frame.src = url + (url.indexOf('?') === -1 ? '?' : '&') + 'embed=1&lang=' + lang;
    modal.classList.add('open');
    modal.setAttribute('aria-hidden', 'false');
    document.body.style.overflow = 'hidden';
}
function closeVenteFrame() {
    var modal = document.getElementById('venteFrameModal');
    var frame = document.getElementById('venteFrame');
    if (!modal || !frame) return;
    modal.classList.remove('open');
    modal.setAttribute('aria-hidden', 'true');
    // stop audio/video and free resources
    frame.src = 'about:blank';
    document.body.style.overflow = '';
}
document.addEventListener('keydown', function(e){
    if (e.key === 'Escape') closeVenteFrame();
});
// Messages from the embedded vente modal
window.addEventListener('message', function (e) {
    if (e.origin !== window.location.origin || !e.data) return;
    if (e.data.type === 'vente:close') closeVenteFrame();
    if (e.data.type === 'vente:done') { closeVenteFrame(); window.location.reload(); }
});
</script>
@endsection
