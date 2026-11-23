<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title')</title>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <link rel="stylesheet" href="{{ asset('assets/back/assets/extensions/choices.js/public/assets/styles/choices.css') }}">

    <link rel="stylesheet" href="{{ asset('assets/back/assets/css/main/app.css') }}">
    {{-- app-dark.css retiré — admin en clair uniquement (NADO redesign 2026-11-23). --}}
    <link rel="shortcut icon" href="{{ asset('assets/images/favicon.png') }}" type="image/png">

    {{-- Modern NADO admin palette — light theme override, loaded after app.css. --}}
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --nd-bg: #f6f7fb;
            --nd-surface: #ffffff;
            --nd-border: #eceef3;
            --nd-border-strong: #dfe3ec;
            --nd-text: #1f2937;
            --nd-text-muted: #6b7280;
            --nd-text-soft: #9aa3b2;
            --nd-primary: #4f46e5;
            --nd-primary-soft: #eef2ff;
            --nd-primary-hover: #4338ca;
            --nd-success: #10b981;
            --nd-success-soft: #ecfdf5;
            --nd-warning: #f59e0b;
            --nd-warning-soft: #fffbeb;
            --nd-danger:  #ef4444;
            --nd-danger-soft: #fef2f2;
            --nd-info: #0ea5e9;
            --nd-info-soft: #f0f9ff;
            --nd-shadow-sm: 0 1px 2px rgba(16, 24, 40, .04), 0 1px 1px rgba(16, 24, 40, .02);
            --nd-shadow-md: 0 4px 16px rgba(16, 24, 40, .06), 0 2px 4px rgba(16, 24, 40, .03);
            --nd-radius: 12px;
            --nd-radius-sm: 8px;
        }

        /* Force light mode even when the browser/system prefers dark. */
        :root { color-scheme: light !important; }
        html, body, #app, #main { background: var(--nd-bg) !important; color: var(--nd-text) !important; }
        body, body.theme-dark { background: var(--nd-bg) !important; color: var(--nd-text) !important; }
        @media (prefers-color-scheme: dark) {
            html, body, #app, #main, .card, #sidebar, .sidebar-wrapper, header .navbar-top {
                background-color: var(--nd-surface) !important;
                color: var(--nd-text) !important;
            }
            html, body, #app, #main { background: var(--nd-bg) !important; }
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif !important;
            font-size: 14px;
            letter-spacing: -0.005em;
            -webkit-font-smoothing: antialiased;
        }

        h1, h2, h3, h4, h5, h6, .page-title h3, .page-heading h3 {
            color: var(--nd-text) !important;
            font-weight: 600 !important;
            letter-spacing: -0.015em !important;
        }

        a { color: var(--nd-primary); }
        a:hover { color: var(--nd-primary-hover); }

        /* ─── Sidebar : clair, bordure douce ─── */
        #sidebar, .sidebar-wrapper {
            background: var(--nd-surface) !important;
            border-right: 1px solid var(--nd-border) !important;
            box-shadow: var(--nd-shadow-sm) !important;
        }
        .sidebar-header { border-bottom: 1px solid var(--nd-border) !important; background: var(--nd-surface) !important; }
        .sidebar-header .logo img { max-height: 32px; }
        .menu, .sidebar-menu { background: transparent !important; }
        .sidebar-title {
            color: var(--nd-text-soft) !important;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-size: 11px;
            font-weight: 600 !important;
            padding: 16px 20px 6px !important;
        }
        .sidebar-item { margin: 2px 10px; }
        .sidebar-link {
            display: flex !important;
            align-items: center;
            gap: 12px;
            padding: 10px 14px !important;
            border-radius: var(--nd-radius-sm) !important;
            color: var(--nd-text) !important;
            font-weight: 500 !important;
            font-size: 14px !important;
            transition: background .15s, color .15s, transform .1s;
        }
        .sidebar-link i, .sidebar-link .bi { font-size: 18px; color: var(--nd-text-muted); transition: color .15s; }
        .sidebar-link:hover { background: var(--nd-primary-soft) !important; color: var(--nd-primary) !important; text-decoration: none !important; }
        .sidebar-link:hover i, .sidebar-link:hover .bi { color: var(--nd-primary) !important; }
        .sidebar-item.active > .sidebar-link,
        .sidebar-item.active > a.sidebar-link {
            background: var(--nd-primary) !important;
            color: #fff !important;
            box-shadow: 0 4px 10px rgba(79, 70, 229, .25);
        }
        .sidebar-item.active > .sidebar-link i,
        .sidebar-item.active > .sidebar-link .bi { color: #fff !important; }
        .sidebar-item.has-sub .submenu { background: transparent !important; }
        .sidebar-item.has-sub .submenu .submenu-item a {
            color: var(--nd-text-muted) !important;
            padding: 8px 14px 8px 46px !important;
            border-radius: var(--nd-radius-sm);
            font-size: 13.5px;
        }
        .sidebar-item.has-sub .submenu .submenu-item a:hover { color: var(--nd-primary) !important; background: var(--nd-primary-soft) !important; }
        .sidebar-item.has-sub .submenu .submenu-item.active a { color: var(--nd-primary) !important; font-weight: 600; }

        /* ─── Topbar ─── */
        header .navbar-top, header .navbar.navbar-top {
            background: var(--nd-surface) !important;
            border-bottom: 1px solid var(--nd-border);
            box-shadow: var(--nd-shadow-sm) !important;
            border-radius: var(--nd-radius) !important;
            margin-bottom: 20px;
        }
        .burger-btn { color: var(--nd-text-muted) !important; }
        .user-menu .user-name h6 { color: var(--nd-text) !important; font-weight: 600; }
        .user-menu .user-name p { color: var(--nd-text-soft) !important; }

        /* ─── Cards ─── */
        .card {
            background: var(--nd-surface) !important;
            border: 1px solid var(--nd-border) !important;
            border-radius: var(--nd-radius) !important;
            box-shadow: var(--nd-shadow-sm) !important;
            transition: box-shadow .15s;
        }
        .card:hover { box-shadow: var(--nd-shadow-md) !important; }
        .card-header { background: var(--nd-surface) !important; border-bottom: 1px solid var(--nd-border) !important; padding: 16px 20px; border-radius: var(--nd-radius) var(--nd-radius) 0 0 !important; }
        .card-body { padding: 20px; }

        /* ─── Boutons ─── */
        .btn {
            border-radius: var(--nd-radius-sm) !important;
            font-weight: 500 !important;
            padding: 8px 16px;
            transition: background .15s, box-shadow .15s, transform .08s;
        }
        .btn-sm { padding: 6px 12px; font-size: 13px; }
        .btn-lg { padding: 10px 20px; font-size: 15px; }
        .btn:active { transform: translateY(1px); }
        .btn-primary { background: var(--nd-primary) !important; border-color: var(--nd-primary) !important; box-shadow: 0 2px 4px rgba(79, 70, 229, .25); }
        .btn-primary:hover { background: var(--nd-primary-hover) !important; border-color: var(--nd-primary-hover) !important; }
        .btn-success { background: var(--nd-success) !important; border-color: var(--nd-success) !important; }
        .btn-warning { background: var(--nd-warning) !important; border-color: var(--nd-warning) !important; color: #fff !important; }
        .btn-danger { background: var(--nd-danger) !important; border-color: var(--nd-danger) !important; }
        .btn-outline-primary { color: var(--nd-primary) !important; border-color: var(--nd-primary) !important; }
        .btn-outline-primary:hover { background: var(--nd-primary) !important; color: #fff !important; }

        /* ─── Inputs ─── */
        .form-control, .form-select {
            border: 1px solid var(--nd-border-strong) !important;
            border-radius: var(--nd-radius-sm) !important;
            padding: 9px 12px !important;
            font-size: 14px !important;
            transition: border-color .15s, box-shadow .15s;
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--nd-primary) !important;
            box-shadow: 0 0 0 3px rgba(79, 70, 229, .15) !important;
        }
        .form-label { font-weight: 500 !important; color: var(--nd-text) !important; font-size: 13.5px; margin-bottom: 6px; }
        .form-text { color: var(--nd-text-soft) !important; font-size: 12.5px; }

        /* ─── Tables ─── */
        .table { color: var(--nd-text) !important; }
        .table > :not(caption) > * > * { padding: 12px 16px; border-color: var(--nd-border) !important; background: transparent !important; }
        .table thead th, .table thead > tr > th {
            background: #f9fafb !important;
            color: var(--nd-text-muted) !important;
            text-transform: uppercase;
            letter-spacing: .04em;
            font-size: 12px !important;
            font-weight: 600 !important;
            border-bottom: 1px solid var(--nd-border) !important;
        }
        .table tbody tr:hover > * { background: #f9fafb !important; }
        .table-striped tbody tr:nth-of-type(odd) > * { background: #fafbfc !important; }

        /* ─── Badges ─── */
        .badge { font-weight: 500; letter-spacing: .01em; padding: 4px 8px; border-radius: 999px; }
        .badge.bg-primary { background: var(--nd-primary-soft) !important; color: var(--nd-primary) !important; }
        .badge.bg-success { background: var(--nd-success-soft) !important; color: var(--nd-success) !important; }
        .badge.bg-warning { background: var(--nd-warning-soft) !important; color: #b45309 !important; }
        .badge.bg-danger  { background: var(--nd-danger-soft)  !important; color: var(--nd-danger)  !important; }
        .badge.bg-info    { background: var(--nd-info-soft)    !important; color: var(--nd-info)    !important; }
        .badge.bg-secondary { background: #f1f3f7 !important; color: var(--nd-text-muted) !important; }
        /* Rouge plein (notifs) : garder la lisibilité en pastille */
        .position-absolute.badge.bg-danger, .ms-1.badge.bg-danger { background: var(--nd-danger) !important; color: #fff !important; }
        .badge.rounded-pill { padding: 3px 8px; }

        /* ─── Alerts ─── */
        .alert { border: none; border-radius: var(--nd-radius-sm); padding: 12px 16px; }
        .alert-success { background: var(--nd-success-soft); color: #065f46; }
        .alert-danger  { background: var(--nd-danger-soft);  color: #991b1b; }
        .alert-warning { background: var(--nd-warning-soft); color: #92400e; }
        .alert-info    { background: var(--nd-info-soft);    color: #075985; }

        /* ─── Dashboard tiles ─── */
        .dash-action {
            display: flex; align-items: center; gap: 16px;
            background: var(--nd-surface) !important;
            border: 1px solid var(--nd-border);
            border-radius: var(--nd-radius);
            padding: 20px 22px;
            color: var(--nd-text);
            text-decoration: none;
            box-shadow: var(--nd-shadow-sm);
            transition: box-shadow .15s, transform .1s, border-color .15s;
        }
        .dash-action:hover { box-shadow: var(--nd-shadow-md); transform: translateY(-1px); border-color: var(--nd-border-strong); text-decoration: none; color: var(--nd-text); }
        .dash-action__icon { width: 48px; height: 48px; border-radius: 12px; display: inline-flex; align-items: center; justify-content: center; font-size: 22px; color: #fff; flex-shrink: 0; }
        .dash-action--amber .dash-action__icon { background: linear-gradient(135deg, #fbbf24, #f59e0b); }
        .dash-action--blue  .dash-action__icon { background: linear-gradient(135deg, #60a5fa, #3b82f6); }
        .dash-action--green .dash-action__icon { background: linear-gradient(135deg, #34d399, #10b981); }
        .dash-action__num { font-size: 28px; font-weight: 700; display: block; line-height: 1.1; color: var(--nd-text); }
        .dash-action__lbl { color: var(--nd-text-muted); font-size: 13.5px; }
        .dash-action__arrow { color: var(--nd-text-soft); }

        .dash-kpi { border: 1px solid var(--nd-border); background: var(--nd-surface); }
        .dash-kpi__lbl { color: var(--nd-text-muted); font-size: 12.5px; font-weight: 500; text-transform: uppercase; letter-spacing: .05em; display: block; }
        .dash-kpi__num { font-size: 26px; font-weight: 700; display: block; margin: 4px 0 2px; line-height: 1; }
        .dash-kpi__sub { color: var(--nd-text-soft); font-size: 12.5px; }

        /* ─── Pagination ─── */
        .pagination .page-link {
            border: 1px solid var(--nd-border) !important;
            color: var(--nd-text-muted) !important;
            border-radius: var(--nd-radius-sm) !important;
            margin: 0 2px;
            padding: 6px 12px;
            min-width: 36px;
            text-align: center;
        }
        .pagination .page-item.active .page-link {
            background: var(--nd-primary) !important;
            border-color: var(--nd-primary) !important;
            color: #fff !important;
        }
        .pagination .page-link:hover { background: var(--nd-primary-soft) !important; color: var(--nd-primary) !important; }

        /* ─── Page content spacing ─── */
        #main.layout-navbar { padding: 20px 28px; }
        .page-heading h3 { margin: 4px 0 2px; }
        .page-title p, .text-subtitle { color: var(--nd-text-muted); }

        /* ─── Dropdowns / modals ─── */
        .dropdown-menu {
            border: 1px solid var(--nd-border) !important;
            border-radius: var(--nd-radius) !important;
            box-shadow: var(--nd-shadow-md) !important;
            padding: 6px;
        }
        .dropdown-item {
            border-radius: var(--nd-radius-sm);
            padding: 8px 12px;
            font-size: 14px;
            color: var(--nd-text);
        }
        .dropdown-item:hover { background: var(--nd-primary-soft); color: var(--nd-primary); }
        .modal-content {
            border: none !important;
            border-radius: var(--nd-radius) !important;
            box-shadow: var(--nd-shadow-md) !important;
        }
        .modal-header, .modal-footer { border-color: var(--nd-border) !important; }

        /* ─── List group ─── */
        .list-group-item { border-color: var(--nd-border) !important; padding: 12px 16px; }
        .list-group-item.list-group-item-action:hover { background: #f9fafb !important; }

        /* ─── Topbar bell tweaks ─── */
        #adminNotifBell { color: var(--nd-text) !important; }
        #adminNotifBell:hover { color: var(--nd-primary) !important; }

        /* ─── Small screens ─── */
        @media (max-width: 992px) {
            #main.layout-navbar { padding: 14px 16px; }
            .dash-action { padding: 16px; gap: 12px; }
            .dash-action__num { font-size: 22px; }
            .card-body { padding: 16px; }
        }
    </style>

    <link rel="stylesheet" href="{{ asset('assets/back/assets/css/shared/iconly.css') }}">

    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
	<style type="text/css" class="init">

    tfoot input {
        width: 100%;
        padding: 3px;
        box-sizing: border-box;
    }

    </style>
    <!-- Owl Carousel CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

<!-- jQuery (nécessaire pour Owl Carousel) -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<!-- Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>


    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @yield('link')
    <!-- Scripts -->
    <!-- @ vite(['resources/css/app.css', 'resources/js/app.js'])-->

</head>
