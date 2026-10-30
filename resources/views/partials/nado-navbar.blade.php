@php
    // Self-contained: works on every page even when the parent didn't pass these in.
    $client        = $client        ?? Auth::guard('client')->user();
    $isAutoEnchere = $isAutoEnchere ?? (request()->routeIs('nadoautoenchere.*'));
    $accentColor   = $accentColor   ?? ($isAutoEnchere ? '#F37021' : '#FDD817');
@endphp

<header class="nado-navbar sticky-top">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script src="https://unpkg.com/lucide@latest"></script>

    <!-- Top Bar Info -->
    <div class="top-bar" style="background-color: #FDD817;">
        <div class="container custom-container top-bar-content">
            <div class="contact-info">
                <div class="info-item">
                    <i data-lucide="mail"></i>
                    <a href="mailto:contact@nado.ma">contact@nado.ma</a>
                </div>
                <div class="info-item">
                    <i data-lucide="phone-call"></i>
                    <a href="tel:+212520070611">+212 520 070 611</a>
                </div>
            </div>
            <div class="social-links">
                <a href="#"><i data-lucide="facebook"></i></a>
                <a href="#"><i data-lucide="instagram"></i></a>
                <a href="#"><i data-lucide="linkedin"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="main-nav">
        <div class="container custom-container nav-content">
            <div class="left-section">
                <a href="{{ $isAutoEnchere ? route('nadoautoenchere.prelogin') : route('index') }}" class="logo-link">
                    @if ($isAutoEnchere)
                        <span class="logo-text">
                            <span class="text-dark">AUTO </span>
                            <span style="color: #F37021;">ENCHÈRE</span>
                        </span>
                    @else
                        <img src="{{ asset('storage/logos/UAfjepY72Xe7grWDva9cqSlRFsRKNZPV3HBDbBP1.png') }}"
                             alt="NADO" style="height: 36px; width: auto; object-fit: contain;" />
                    @endif
                </a>

                <div class="desktop-menu">
                    @if ($isAutoEnchere)
                        <a href="{{ route('achat.index') }}" class="nav-link">{{ __('messages.nav.buy_ae') }}</a>
                        <div class="nav-dropdown-wrapper">
                            <button class="nav-link dropdown-trigger">
                                {{ __('messages.nav.sell_ae') }} <i data-lucide="chevron-right" class="icon-sm"></i>
                            </button>
                            <div class="nav-dropdown-menu">
                                <a href="{{ route('vente.form') }}">{{ __('messages.nav.individual') }}</a>
                                <a href="{{ route('client.login') }}">{{ __('messages.nav.professional') }}</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('achat.index') }}" class="nav-link {{ request()->routeIs('achat.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.buy') }}</a>
                        <a href="{{ route('vente.form') }}" class="nav-link {{ request()->routeIs('vente.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.sell') }}</a>
                        <a href="{{ route('nadoautoenchere.prelogin') }}" class="nav-link {{ request()->routeIs('nadoautoenchere.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.auction') }}</a>
                        <a href="{{ route('reprise.form') }}" class="nav-link {{ request()->routeIs('reprise.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.tradein') }}</a>
                        <a href="{{ route('financement.index') }}" class="nav-link {{ request()->routeIs('financement.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.financing') }}</a>
                        <a href="{{ route('about.index') }}" class="nav-link {{ request()->routeIs('about.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.about') }}</a>
                    @endif
                </div>
            </div>

            <div class="right-section">
                @if ($client)
                    <div class="user-dropdown-wrapper" id="userDropdownWrapper">
                        <button class="cta-button outline-btn" type="button" aria-label="Mon compte">
                            <i data-lucide="user"></i>
                            <span class="user-name" title="{{ $client->name }}">{{ $client->name }}</span>
                            <i data-lucide="chevron-down" class="icon-sm"></i>
                        </button>
                        <div class="user-dropdown-menu">
                            <a href="{{ route('client.dashboard') }}"><i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="logout-btn"><i data-lucide="log-out"></i> {{ __('messages.nav.logout') }}</button>
                            </form>
                        </div>
                    </div>
                    <script>
                        (function () {
                            var w = document.getElementById('userDropdownWrapper');
                            if (!w) return;
                            w.querySelector('button').addEventListener('click', function (e) {
                                e.stopPropagation();
                                w.classList.toggle('open');
                            });
                            document.addEventListener('click', function (e) {
                                if (!w.contains(e.target)) w.classList.remove('open');
                            });
                        })();
                    </script>
                @else
                    <a href="{{ route('client.login') }}" class="cta-button outline-btn cta-publish">
                        @if ($isAutoEnchere) {{ __('messages.nav.create_account') }} @else {{ __('messages.nav.publish_ad') }} @endif
                    </a>
                @endif

                @include('partials.lang-switcher')

                <button id="mobileMenuBtn" class="mobile-menu-btn">
                    <i data-lucide="menu"></i>
                </button>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer -->
    <div id="mobileDrawer" class="mobile-drawer">
        <div id="drawerBackdrop" class="drawer-backdrop"></div>
        <div id="drawerContent" class="drawer-content">
            <div class="drawer-header">
                <span class="logo-text">
                    @if ($isAutoEnchere)
                        <span class="text-dark">AUTO </span><span style="color:#F37021;">ENCHÈRE</span>
                    @else
                        <span style="color:#1459DD;">NA</span><span style="color:#FDD817;">DO</span>
                    @endif
                </span>
                <button id="closeDrawerBtn" class="close-btn"><i data-lucide="x"></i></button>
            </div>

            <div class="drawer-links">
                @if ($isAutoEnchere)
                    <a href="{{ route('achat.index') }}">{{ __('messages.nav.buy_ae') }}</a>
                    <a href="{{ route('vente.form') }}">{{ __('messages.nav.sell_individual') }}</a>
                    <a href="{{ route('client.login') }}">{{ __('messages.nav.sell_pro') }}</a>
                @else
                    <a href="{{ route('achat.index') }}">{{ __('messages.nav.buy') }}</a>
                    <a href="{{ route('vente.form') }}">{{ __('messages.nav.sell') }}</a>
                    <a href="{{ route('nadoautoenchere.prelogin') }}" style="color:#1459DD;">{{ __('messages.nav.auction') }}</a>
                    <a href="{{ route('reprise.form') }}">{{ __('messages.nav.tradein') }}</a>
                    <a href="{{ route('financement.index') }}">{{ __('messages.nav.financing') }}</a>
                    <a href="{{ route('about.index') }}">{{ __('messages.nav.about') }}</a>
                @endif
            </div>

            <div class="drawer-footer">
                @if ($client)
                    <a href="{{ route('client.dashboard') }}" class="drawer-cta" style="background-color: {{ $accentColor }};">
                        <i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:10px 0 0;width:100%;">
                        @csrf
                        <button type="submit" class="drawer-logout"
                                style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 16px;border-radius:12px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;cursor:pointer;">
                            <i data-lucide="log-out" style="width:16px;height:16px;"></i> {{ __('messages.nav.logout') }}
                        </button>
                    </form>
                @else
                    <a href="{{ route('client.login') }}" class="drawer-cta" style="background-color: {{ $accentColor }};">
                        <i data-lucide="user"></i> {{ __('messages.nav.publish_ad') }}
                    </a>
                @endif

                <div style="margin:14px 0 6px;display:flex;justify-content:center;">@include('partials.lang-switcher')</div>

                <div class="drawer-socials">
                    <a href="#"><i data-lucide="facebook"></i></a>
                    <a href="#"><i data-lucide="instagram"></i></a>
                    <a href="#"><i data-lucide="linkedin"></i></a>
                </div>
                <div class="drawer-divider"></div>
                <div class="drawer-support">
                    <p>{{ __('messages.nav.support') }}</p>
                    <a href="tel:+212520070611">+212 520 070 611</a>
                </div>
            </div>
        </div>
    </div>

    <style>
        :root {
            --primary-blue: #1459DD;
            --slate-900: #0f172a;
            --slate-700: #334155;
            --slate-50: #f8fafc;
            --border-color: #e2e8f0;
        }
        .nado-navbar { font-family: 'Inter', sans-serif; width: 100%; z-index: 1000; position: relative; direction: ltr; }
        .sticky-top { position: fixed; top: 0; left: 0; width: 100%; z-index: 1000; }
        .top-bar { padding: 10px 16px; border-bottom: 1px solid rgba(0,0,0,.05); color: var(--slate-900); }
        .custom-container { max-width: 1280px; margin: 0 auto; padding: 0 16px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
        .contact-info { display: flex; gap: 12px; }
        .info-item { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; opacity: .8; }
        .info-item i { width: 12px; height: 12px; color: var(--primary-blue); }
        .info-item a { color: inherit; text-decoration: none; transition: color .2s; }
        .info-item a:hover { color: var(--primary-blue); }
        .social-links { display: flex; gap: 20px; }
        .social-links a { color: var(--slate-900); opacity: .6; transition: opacity .2s, color .2s; }
        .social-links a:hover { color: var(--primary-blue); opacity: 1; }
        .social-links i { width: 20px; height: 20px; }
        .main-nav { background: white; border-bottom: 1px solid var(--border-color); height: 96px; display: flex; align-items: center; }
        .nav-content { height: 100%; width: 100%; display: flex; justify-content: space-between; align-items: center; }
        .left-section { display: flex; align-items: center; gap: 64px; }
        .logo-link { text-decoration: none; transition: transform .2s; }
        .logo-link:hover { transform: scale(1.05); }
        .logo-text { font-size: 2.25rem; font-weight: 900; text-transform: uppercase; line-height: 1; letter-spacing: -.05em; }
        .text-dark { color: var(--slate-900); }
        .desktop-menu { display: none; align-items: center; gap: 12px; }
        @media (min-width: 1024px) { .desktop-menu { display: flex; } }
        .nav-link { font-size: 14.4px; font-weight: 700; text-transform: uppercase; letter-spacing: .025em; padding: 8px 12px; border-radius: 16px; color: var(--slate-700); text-decoration: none; transition: all .2s; background: transparent; border: none; cursor: pointer; white-space: nowrap; word-break: keep-all; hyphens: none; }
        .nav-link:hover { background-color: #f1f5f9; color: var(--primary-blue); }
        .highlight-link { color: var(--primary-blue); background-color: #dbeafe; }
        .highlight-link:hover { background-color: #bfdbfe; }
        .nav-dropdown-wrapper { position: relative; }
        .dropdown-trigger { display: flex; align-items: center; gap: 8px; }
        .nav-dropdown-menu { position: absolute; top: 100%; left: 0; margin-top: 8px; background: white; border-radius: 16px; box-shadow: 0 20px 25px -5px rgba(0,0,0,.1); border: 1px solid var(--border-color); min-width: 200px; overflow: hidden; display: none; }
        .nav-dropdown-wrapper:hover .nav-dropdown-menu { display: block; }
        .nav-dropdown-menu a { display: block; padding: 16px 20px; font-size: 14px; font-weight: 700; text-transform: uppercase; color: var(--slate-700); text-decoration: none; transition: background .2s; }
        .nav-dropdown-menu a:hover { background-color: #fff7ed; color: #F37021; }
        .right-section { margin-left: auto; display: flex; align-items: center; gap: 20px; }
        @media (max-width: 1023px) {
            .main-nav { height: 72px; }
            .logo-link img { height: 24px !important; width: auto !important; }
            .logo-text { font-size: 1.4rem; }
            .right-section { gap: 12px; padding-left: 12px; }
            .right-section .cta-button { padding: 10px 18px; font-size: 13px; letter-spacing: .05em; border-radius: 12px; }
        }
        .cta-button { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 16px; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; text-decoration: none; cursor: pointer; transition: all .2s; border: none; font-family: inherit; }
        .cta-button:active { transform: scale(.95); }
        .outline-btn { background-color: rgba(253,216,23,.2); border: 1px solid rgba(253,216,23,.5); color: var(--slate-900); }
        .outline-btn:hover { background-color: #FDD817; }
        .mobile-menu-btn { display: block; padding: 12px; background-color: var(--slate-50); border: none; border-radius: 12px; cursor: pointer; color: var(--slate-900); }
        @media (min-width: 1024px) { .mobile-menu-btn { display: none; } }
        .user-dropdown-wrapper { position: relative; display: block; flex-shrink: 0; }
        @media (max-width: 1023px) {
            .user-dropdown-wrapper .user-name { display: none; }
            .user-dropdown-wrapper .cta-button { padding: 8px 10px; gap: 4px; }
        }
        .user-dropdown-wrapper.open .user-dropdown-menu { opacity: 1; visibility: visible; transform: translateY(0); transition: opacity .15s ease, transform .15s ease, visibility 0s; }
        .user-dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 8px; width: 192px; background: white; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1); border: 1px solid var(--border-color); overflow: hidden; opacity: 0; visibility: hidden; transform: translateY(-4px); transition: opacity .18s ease .4s, transform .18s ease .4s, visibility 0s linear .58s; }
        .user-dropdown-wrapper:hover .user-dropdown-menu, .user-dropdown-menu:hover { opacity: 1; visibility: visible; transform: translateY(0); transition: opacity .15s ease, transform .15s ease, visibility 0s; }
        .user-dropdown-wrapper::after { content: ''; position: absolute; top: 100%; right: 0; width: 100%; height: 14px; }
        .user-name { display: inline-block; max-width: 150px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; vertical-align: middle; }
        @media (max-width: 1280px) { .user-name { max-width: 100px; } }
        .user-dropdown-menu a, .user-dropdown-menu button { display: flex; align-items: center; width: 100%; padding: 12px 16px; font-size: 14px; font-weight: 700; text-align: left; background: none; border: none; cursor: pointer; text-decoration: none; color: var(--slate-700); font-family: inherit; }
        .user-dropdown-menu a:hover { background-color: #f8fafc; color: var(--primary-blue); }
        .user-dropdown-menu i { width: 16px; height: 16px; margin-right: 8px; }
        .logout-btn { color: #dc2626 !important; }
        .logout-btn:hover { background-color: #fef2f2 !important; }
        .mobile-drawer { position: fixed; inset: 0; z-index: 1100; visibility: hidden; transition: visibility .5s; }
        .mobile-drawer.visible { visibility: visible; pointer-events: auto; }
        .mobile-drawer.visible .drawer-backdrop { opacity: 1; }
        .drawer-backdrop { position: absolute; inset: 0; background-color: rgba(15,23,42,.6); backdrop-filter: blur(8px); opacity: 0; transition: opacity .3s ease; }
        .drawer-content { position: fixed; top: 0; right: 0; bottom: 0; height: 100vh; width: 85%; max-width: 420px; background: white; box-shadow: -10px 0 50px rgba(0,0,0,.3); transform: translateX(100%); transition: transform .3s cubic-bezier(.4,0,.2,1); display: flex; flex-direction: column; overflow-y: auto; }
        .mobile-drawer.visible .drawer-content { transform: translateX(0); }
        .drawer-header { padding: 32px; display: flex; justify-content: space-between; align-items: center; }
        .close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; }
        .close-btn:hover { color: var(--slate-900); }
        .drawer-links { flex: 1; overflow-y: auto; padding: 16px 32px; display: flex; flex-direction: column; gap: 24px; }
        .drawer-links a { font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: -.025em; color: var(--slate-900); text-decoration: none; transition: color .2s; }
        .drawer-links a:hover { color: var(--primary-blue); }
        .drawer-footer { padding: 40px; display: flex; flex-direction: column; align-items: center; }
        .drawer-cta { width: 100%; padding: 24px; border-radius: 32px; font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em; color: var(--slate-900); text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 48px; }
        .drawer-cta:active { transform: scale(.95); }
        .drawer-socials { display: flex; gap: 40px; margin-bottom: 48px; }
        .drawer-socials a { color: #94a3b8; transition: color .2s; }
        .drawer-socials a:hover { color: var(--slate-900); }
        .drawer-divider { width: 100%; height: 1px; background-color: #f1f5f9; margin-bottom: 40px; }
        .drawer-support { text-align: center; }
        .drawer-support p { font-size: 14px; font-weight: 800; text-transform: uppercase; letter-spacing: .025em; margin-bottom: 12px; }
        .drawer-support a { font-size: 20px; font-weight: 900; color: var(--slate-900); text-decoration: none; transition: color .2s; }
        .drawer-support a:hover { color: var(--primary-blue); }
        .icon-sm { width: 16px; height: 16px; }

        /* Single-line fixes */
        .cta-button, .cta-button span { white-space: nowrap !important; word-break: keep-all !important; overflow-wrap: normal !important; hyphens: none !important; }
        .cta-button { flex-shrink: 0 !important; text-align: center !important; }
        @media (max-width: 1023px) {
            .right-section .cta-button { font-size: clamp(8.5px, 2.5vw, 13px) !important; padding: 9px 11px !important; letter-spacing: .01em !important; border-radius: 12px !important; gap: 6px !important; }
            .right-section { gap: 8px !important; padding-left: 8px !important; flex-shrink: 0 !important; }
            .left-section { min-width: 0 !important; flex-shrink: 1 !important; overflow: hidden !important; }
        }
        .drawer-cta, .drawer-cta span { white-space: nowrap !important; word-break: keep-all !important; overflow-wrap: normal !important; hyphens: none !important; }
        .drawer-cta { text-align: center !important; }
        .drawer-cta i, .drawer-cta svg { flex-shrink: 0 !important; }
        @media (max-width: 1023px) {
            .drawer-cta { font-size: clamp(9px, 2.9vw, 14px) !important; letter-spacing: .06em !important; padding: 18px 14px !important; gap: 8px !important; }
        }
        @media (max-width: 380px) {
            .drawer-cta { letter-spacing: .03em !important; padding: 16px 10px !important; }
        }
        .info-item, .info-item a { white-space: nowrap !important; word-break: keep-all !important; }
        .info-item i { flex-shrink: 0 !important; }
        @media (max-width: 600px) {
            .top-bar .social-links { display: none !important; }
            .top-bar { padding: 8px 0 !important; }
            .top-bar .custom-container { padding: 0 10px !important; }
            .contact-info { gap: 10px !important; }
            .info-item { font-size: 9px !important; letter-spacing: .02em !important; gap: 5px !important; }
        }
        @media (max-width: 380px) {
            .contact-info .info-item:first-child { display: none !important; }
            .info-item { font-size: 10px !important; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();

            var btn      = document.getElementById('mobileMenuBtn');
            var closeBtn = document.getElementById('closeDrawerBtn');
            var drawer   = document.getElementById('mobileDrawer');
            var backdrop = document.getElementById('drawerBackdrop');

            function openDrawer()  { if (drawer) { drawer.classList.add('visible'); document.body.style.overflow = 'hidden'; } }
            function closeDrawer() { if (drawer) { drawer.classList.remove('visible'); document.body.style.overflow = ''; } }

            if (btn)      btn.addEventListener('click',    function (e) { e.stopPropagation(); openDrawer(); });
            if (closeBtn) closeBtn.addEventListener('click', function (e) { e.stopPropagation(); closeDrawer(); });
            if (backdrop) backdrop.addEventListener('click', closeDrawer);

            document.querySelectorAll('#mobileDrawer .drawer-links a').forEach(function (l) {
                l.addEventListener('click', closeDrawer);
            });
            document.addEventListener('keydown', function (e) { if (e.key === 'Escape') closeDrawer(); });
        });
    </script>
</header>
