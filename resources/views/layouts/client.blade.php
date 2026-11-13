@extends('layouts.app')

{{-- Navbar : client enchère → navbar Auto-Enchère (fixe, sans spacer car
     .client-main a déjà padding-top:var(--navH)) ; sinon navbar NADO. --}}
@section('navbar')
    @php $navIsFlotte = Auth::guard('client')->user()?->type === 'flotte&enchere'; @endphp
    @if($navIsFlotte)
        <script src="https://cdn.tailwindcss.com/3.4.16"></script>
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
    $client   = Auth::guard('client')->user();
    $isFlotte = $client?->type === 'flotte&enchere';
@endphp

<style>
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

    .client-shell { display:flex; min-height: 100vh; }

    .client-sidebar {
        width: 260px;
        background: var(--slate-900);
        display: flex;
        flex-direction: column;
        position: fixed;
        top: var(--navH);
        left: 0;
        height: calc(100vh - var(--navH));
        z-index: 300;
        overflow-y: auto;
        transition: transform .3s cubic-bezier(.4,0,.2,1);
    }

    .client-main {
        margin-left: 260px;
        flex: 1;
        min-height: 100vh;
        padding-top: var(--navH);
        background: var(--slate-50);
    }

    .client-body { padding: 28px 20px 60px; }

    .client-overlay {
        display:none;
        position: fixed;
        inset: 0;
        background: rgba(15,23,42,.55);
        z-index: 290;
        backdrop-filter: blur(2px);
    }
    .client-overlay.open { display:block; }

    .client-fab {
        display: none;
        position: fixed;
        left: 16px;
        bottom: 16px;
        z-index: 1200;
        width: 52px;
        height: 52px;
        border-radius: 16px;
        background: #ffffff;
        border: 1px solid var(--slate-200);
        box-shadow: 0 16px 45px rgba(0,0,0,.18);
        color: var(--slate-900);
        cursor: pointer;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        font-weight: 900;
    }

    @media (max-width: 900px) {
        .client-sidebar { transform: translateX(-100%); }
        .client-sidebar.open { transform: translateX(0); }
        .client-main { margin-left: 0; }
        .client-fab { display: inline-flex; }
        .client-body { padding: 18px 16px 72px; }
    }

    /* Bootstrap n'est pas chargé ici : on style localement .table/.btn/.alert
       pour que les pages client (équipe, mes offres, etc.) ne soient pas brutes. */
    .client-body .table {
        width: 100%; border-collapse: collapse; background: #fff;
        border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow);
        font-size: 13.5px; margin-bottom: 20px;
    }
    .client-body .table th, .client-body .table td {
        padding: 12px 16px; text-align: left; vertical-align: middle;
        border-bottom: 1px solid var(--slate-100); color: var(--slate-700);
    }
    .client-body .table thead th {
        background: var(--slate-50); color: var(--slate-500); font-size: 11px;
        font-weight: 800; text-transform: uppercase; letter-spacing: .06em;
        border-bottom: 1px solid var(--slate-200);
    }
    .client-body .table.table-bordered th,
    .client-body .table.table-bordered td { border: 1px solid var(--slate-200); }
    .client-body .table.table-striped tbody tr:nth-child(odd) td { background: #fafbfd; }
    .client-body .table tbody tr:hover td { background: #f1f5f9; }
    .client-body .table td.text-center, .client-body .table th.text-center { text-align: center; }

    .client-body .btn {
        display: inline-flex; align-items: center; gap: 6px; padding: 9px 16px;
        border-radius: 12px; font-size: 13px; font-weight: 700; text-decoration: none;
        border: 1px solid var(--slate-200); background: #fff; color: var(--slate-700);
        cursor: pointer; transition: all .15s; white-space: nowrap;
    }
    .client-body .btn:hover { background: var(--slate-100); }
    .client-body .btn-primary { background: var(--blue); border-color: var(--blue); color: #fff; }
    .client-body .btn-primary:hover { background: #0f4bcc; color: #fff; }
    .client-body .btn-danger { background: #fef2f2; border-color: #fecaca; color: #dc2626; }
    .client-body .btn-sm { padding: 6px 12px; font-size: 12px; }

    .client-body .alert {
        padding: 12px 16px; border-radius: 12px; margin-bottom: 20px;
        font-size: 13.5px; font-weight: 600;
    }
    .client-body .alert-success { background: #f0fdf4; color: #166534; border: 1px solid #bbf7d0; }
    .client-body .alert-danger  { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }
</style>

<div class="client-shell">
    @include('client.partials.sidebar')
    <div class="client-overlay" id="clientOverlay" onclick="closeClientSidebar()"></div>

    <main class="client-main">
        <div class="client-body">
            @yield('client_content')
        </div>
    </main>
</div>

<button class="client-fab" type="button" onclick="toggleClientSidebar()" aria-label="Ouvrir le menu">
    <i class="fa fa-bars"></i>
</button>

<script>
function toggleClientSidebar() {
    var sb = document.getElementById('clientSidebar');
    var ov = document.getElementById('clientOverlay');
    if (!sb || !ov) return;
    sb.classList.toggle('open');
    ov.classList.toggle('open');
    document.body.style.overflow = sb.classList.contains('open') ? 'hidden' : '';
}
function closeClientSidebar() {
    var sb = document.getElementById('clientSidebar');
    var ov = document.getElementById('clientOverlay');
    if (!sb || !ov) return;
    sb.classList.remove('open');
    ov.classList.remove('open');
    document.body.style.overflow = '';
}
</script>
@endsection

