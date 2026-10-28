@php
    $client = Auth::guard('client')->user();
    $isAutoEnchere = request()->routeIs('nadoautoenchere.*');
    $accentColor = $isAutoEnchere ? '#F37021' : '#FDD817';
@endphp

<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://unpkg.com/lucide@latest"></script>

<header class="nado-navbar">

    <!-- Top Bar -->
    <div class="nv-topbar">
        <div class="nv-wrap nv-topbar-inner">
            <div class="nv-contact">
                <div class="nv-info"><i data-lucide="mail"></i><a href="mailto:contact@nado.ma">contact@nado.ma</a></div>
                <div class="nv-info"><i data-lucide="phone-call"></i><a href="tel:+212520070611">+212 520 070 611</a></div>
            </div>
            <div class="nv-social">
                <a href="#"><i data-lucide="facebook"></i></a>
                <a href="#"><i data-lucide="instagram"></i></a>
                <a href="#"><i data-lucide="linkedin"></i></a>
            </div>
        </div>
    </div>

    <!-- Main Navigation -->
    <nav class="nv-main">
        <div class="nv-wrap nv-content">
            <div class="nv-left">
                <a href="{{ $isAutoEnchere ? route('nadoautoenchere.prelogin') : route('index') }}" class="nv-logo">
                    @if ($isAutoEnchere)
                        <span class="nv-logo-text"><span class="nv-dark">AUTO </span><span style="color:#F37021;">ENCHÈRE</span></span>
                    @else
                        <img src="{{ asset('storage/logos/UAfjepY72Xe7grWDva9cqSlRFsRKNZPV3HBDbBP1.png') }}" alt="NADO" style="height:36px;width:auto;object-fit:contain;" />
                    @endif
                </a>

                <div class="nv-menu">
                    @if ($isAutoEnchere)
                        <a href="{{ route('achat.index') }}" class="nv-link">{{ __('messages.nav.buy_ae') }}</a>
                        <div class="nv-dropdown">
                            <button class="nv-link nv-dropdown-trigger">{{ __('messages.nav.sell_ae') }} <i data-lucide="chevron-right" class="nv-icon-sm"></i></button>
                            <div class="nv-dropdown-menu">
                                <a href="{{ route('vente.form') }}">{{ __('messages.nav.individual') }}</a>
                                <a href="{{ route('client.login') }}">{{ __('messages.nav.professional') }}</a>
                            </div>
                        </div>
                    @else
                        <a href="{{ route('achat.index') }}" class="nv-link {{ request()->routeIs('achat.*') ? 'nv-highlight' : '' }}">{{ __('messages.nav.buy') }}</a>
                        <a href="{{ route('vente.form') }}" class="nv-link {{ request()->routeIs('vente.*') ? 'nv-highlight' : '' }}">{{ __('messages.nav.sell') }}</a>
                        <a href="{{ route('nadoautoenchere.prelogin') }}" class="nv-link {{ request()->routeIs('nadoautoenchere.*') ? 'nv-highlight' : '' }}">{{ __('messages.nav.auction') }}</a>
                        <a href="{{ route('reprise.form') }}" class="nv-link {{ request()->routeIs('reprise.*') ? 'nv-highlight' : '' }}">{{ __('messages.nav.tradein') }}</a>
                        <a href="{{ route('financement.index') }}" class="nv-link {{ request()->routeIs('financement.*') ? 'nv-highlight' : '' }}">{{ __('messages.nav.financing') }}</a>
                        <a href="{{ route('about.index') }}" class="nv-link {{ request()->routeIs('about.*') ? 'nv-highlight' : '' }}">{{ __('messages.nav.about') }}</a>
                    @endif
                </div>
            </div>

            <div class="nv-right">
                @if ($client)
                    <div class="nv-user-wrap">
                        <button class="nv-cta nv-outline" type="button" aria-label="Mon compte">
                            <i data-lucide="user"></i>
                            <span class="nv-user-name" title="{{ $client->name }}">{{ $client->name }}</span>
                            <i data-lucide="chevron-down" class="nv-icon-sm"></i>
                        </button>
                        <div class="nv-user-menu">
                            <a href="{{ route('client.dashboard') }}"><i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="nv-logout"><i data-lucide="log-out"></i> {{ __('messages.nav.logout') }}</button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('client.login') }}" class="nv-cta nv-outline nv-cta-publish">
                        {{-- Long label on wide screens, short one on phones. Swapping the
                             text is reliable; shrinking it to fit depends on font metrics. --}}
                        <span class="nv-cta-label-long">@if ($isAutoEnchere) {{ __('messages.nav.create_account') }} @else {{ __('messages.nav.publish_ad') }} @endif</span>
                        <span class="nv-cta-label-short">@if ($isAutoEnchere) {{ __('messages.nav.account_short') }} @else {{ __('messages.nav.publish_short') }} @endif</span>
                    </a>
                @endif
                @include('partials.lang-switcher')
                <button id="mobileMenuBtn" class="nv-hamburger"><i data-lucide="menu"></i></button>
            </div>
        </div>
    </nav>

    <!-- Mobile Drawer -->
    <div id="mobileDrawer" class="nv-drawer">
        <div id="drawerBackdrop" class="nv-backdrop"></div>
        <div id="drawerContent" class="nv-drawer-inner">
            <div class="nv-drawer-head">
                @if ($isAutoEnchere)
                    <span class="nv-logo-text"><span class="nv-dark">AUTO </span><span style="color:#F37021;">ENCHÈRE</span></span>
                @else
                    <img src="{{ asset('storage/logos/UAfjepY72Xe7grWDva9cqSlRFsRKNZPV3HBDbBP1.png') }}" alt="NADO" style="height:30px;width:auto;object-fit:contain;" />
                @endif
                <button id="closeDrawerBtn" class="nv-close"><i data-lucide="x"></i></button>
            </div>
            <div class="nv-drawer-links">
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
            <div class="nv-drawer-footer">
                @if ($client)
                    <a href="{{ route('client.dashboard') }}" class="nv-drawer-cta" style="background-color:{{ $accentColor }};"><i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}</a>
                    <form method="POST" action="{{ route('logout') }}" style="margin:10px 0 0;">
                        @csrf
                        <button type="submit" class="nv-drawer-logout"><i data-lucide="log-out"></i> {{ __('messages.nav.logout') }}</button>
                    </form>
                @else
                    <a href="{{ route('client.login') }}" class="nv-drawer-cta" style="background-color:{{ $accentColor }};"><i data-lucide="user"></i> {{ __('messages.nav.publish_ad') }}</a>
                @endif
                <div style="margin:16px 0 8px;display:flex;justify-content:center;">@include('partials.lang-switcher')</div>
                <div class="nv-socials">
                    <a href="#"><i data-lucide="facebook"></i></a>
                    <a href="#"><i data-lucide="instagram"></i></a>
                    <a href="#"><i data-lucide="linkedin"></i></a>
                </div>
                <div class="nv-divider"></div>
                <div class="nv-support"><p>{{ __('messages.nav.support') }}</p><a href="tel:+212520070611">+212 520 070 611</a></div>
            </div>
        </div>
    </div>

    <style>
        /* ═══ NADO NAVBAR — fully isolated with nv- prefix ═══ */
        .nado-navbar {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
            width: 100% !important;
            position: fixed !important;
            top: 0 !important; left: 0 !important;
            z-index: 1050 !important;
            direction: ltr !important;
        }
        .nado-navbar *, .nado-navbar *::before, .nado-navbar *::after {
            box-sizing: border-box !important;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif !important;
        }

        /* Top bar */
        .nado-navbar .nv-topbar {
            background-color: #FDD817 !important;
            padding: 10px 0 !important;
            border-bottom: 1px solid rgba(0,0,0,.05) !important;
            color: #0f172a !important;
        }
        .nado-navbar .nv-wrap {
            max-width: 1280px !important;
            margin: 0 auto !important;
            padding: 0 24px !important;
            display: flex !important;
            justify-content: space-between !important;
            align-items: center !important;
            width: 100% !important;
        }
        .nado-navbar .nv-contact { display: flex !important; gap: 12px !important; }
        .nado-navbar .nv-info {
            display: flex !important; align-items: center !important; gap: 8px !important;
            font-size: 11px !important; font-weight: 900 !important;
            text-transform: uppercase !important; letter-spacing: .1em !important; opacity: .8 !important;
        }
        .nado-navbar .nv-info i { width: 12px !important; height: 12px !important; color: #1459DD !important; }
        .nado-navbar .nv-info a { color: inherit !important; text-decoration: none !important; }
        .nado-navbar .nv-info a:hover { color: #1459DD !important; opacity: 1 !important; }
        .nado-navbar .nv-social { display: flex !important; gap: 20px !important; }
        .nado-navbar .nv-social a { color: #0f172a !important; opacity: .6 !important; text-decoration: none !important; }
        .nado-navbar .nv-social a:hover { color: #1459DD !important; opacity: 1 !important; }
        .nado-navbar .nv-social i { width: 20px !important; height: 20px !important; }

        /* Main nav */
        .nado-navbar .nv-main {
            background: #ffffff !important;
            border-bottom: 1px solid #e2e8f0 !important;
            height: 96px !important;
            display: flex !important;
            align-items: center !important;
        }
        .nado-navbar .nv-content { height: 100% !important; }
        .nado-navbar .nv-left {
            display: flex !important; align-items: center !important; gap: 64px !important;
        }
        .nado-navbar .nv-right {
            margin-left: auto !important; display: flex !important; align-items: center !important; gap: 12px !important;
        }

        /* Logo */
        .nado-navbar .nv-logo {
            display: inline-flex !important; align-items: center !important;
            text-decoration: none !important; transition: transform .2s !important;
        }
        .nado-navbar .nv-logo:hover { transform: scale(1.05) !important; }
        .nado-navbar .nv-logo-text {
            font-size: 2.25rem !important; font-weight: 900 !important;
            text-transform: uppercase !important; line-height: 1 !important; letter-spacing: -.05em !important;
        }
        .nado-navbar .nv-dark { color: #0f172a !important; }

        /* Desktop menu */
        .nado-navbar .nv-menu { display: none !important; align-items: center !important; gap: 12px !important; }
        @media (min-width: 1024px) { .nado-navbar .nv-menu { display: flex !important; } }

        /* Links */
        .nado-navbar .nv-link {
            font-size: 14px !important; font-weight: 700 !important;
            text-transform: uppercase !important; letter-spacing: .025em !important;
            padding: 8px 12px !important; border-radius: 16px !important;
            color: #334155 !important; text-decoration: none !important;
            transition: all .2s !important; background: transparent !important;
            border: none !important; cursor: pointer !important;
            white-space: nowrap !important; display: inline-flex !important; align-items: center !important; gap: 4px !important;
            line-height: 1.4 !important;
        }
        .nado-navbar .nv-link:hover, .nado-navbar .nv-link:focus {
            background-color: #f1f5f9 !important; color: #1459DD !important; text-decoration: none !important;
        }
        .nado-navbar .nv-highlight { color: #1459DD !important; background-color: #dbeafe !important; }
        .nado-navbar .nv-highlight:hover { background-color: #bfdbfe !important; }

        /* Vendre dropdown */
        .nado-navbar .nv-dropdown { position: relative !important; }
        .nado-navbar .nv-dropdown-menu {
            position: absolute !important; top: 100% !important; left: 0 !important; margin-top: 8px !important;
            background: white !important; border-radius: 12px !important;
            box-shadow: 0 20px 25px -5px rgba(0,0,0,.1) !important;
            border: 1px solid #e2e8f0 !important; min-width: 200px !important;
            overflow: hidden !important; display: none !important; z-index: 9100 !important;
        }
        .nado-navbar .nv-dropdown:hover .nv-dropdown-menu { display: block !important; }
        .nado-navbar .nv-dropdown-menu a {
            display: block !important; padding: 14px 20px !important;
            font-size: 13px !important; font-weight: 700 !important;
            text-transform: uppercase !important; color: #334155 !important; text-decoration: none !important;
        }
        .nado-navbar .nv-dropdown-menu a:hover { background: #fff7ed !important; color: #F37021 !important; }

        /* CTA / outline button */
        .nado-navbar .nv-cta {
            display: flex !important; align-items: center !important; gap: 8px !important;
            padding: 10px 16px !important; border-radius: 12px !important;
            font-size: 13px !important; font-weight: 800 !important;
            text-transform: uppercase !important; letter-spacing: .05em !important;
            text-decoration: none !important; cursor: pointer !important; transition: all .2s !important;
            line-height: 1.4 !important;
        }
        .nado-navbar .nv-cta:active { transform: scale(.95) !important; }
        /* A long client name used to wrap and stretch the navbar. Keep the button on
           one line and truncate the name with an ellipsis (full name in title=""). */
        .nado-navbar .nv-cta { white-space: nowrap !important; }
        .nado-navbar .nv-cta i { flex-shrink: 0 !important; }
        .nado-navbar .nv-user-name {
            display: inline-block !important;
            max-width: 150px !important;
            overflow: hidden !important;
            text-overflow: ellipsis !important;
            white-space: nowrap !important;
            vertical-align: middle !important;
        }
        @media (max-width: 1280px) { .nado-navbar .nv-user-name { max-width: 100px !important; } }
        .nado-navbar .nv-outline {
            background-color: rgba(253,216,23,.2) !important;
            border: 1px solid rgba(253,216,23,.5) !important;
            color: #0f172a !important;
        }
        .nado-navbar .nv-outline:hover { background-color: #FDD817 !important; color: #0f172a !important; text-decoration: none !important; }

        /* Hamburger */
        .nado-navbar .nv-hamburger {
            display: block !important; padding: 10px !important;
            background-color: #f8fafc !important; border: none !important;
            border-radius: 10px !important; cursor: pointer !important; color: #0f172a !important;
        }
        @media (min-width: 1024px) { .nado-navbar .nv-hamburger { display: none !important; } }

        /* User dropdown */
        /* Visible on every screen size (it used to be hidden below 1024px, so a
           logged-in client lost "Mon espace" / "Déconnexion" on mobile) */
        .nado-navbar .nv-user-wrap { position: relative !important; display: block !important; flex-shrink: 0 !important; }
        @media (max-width: 1023px) {
            /* compact: user icon + chevron only, the name doesn't fit next to the logo */
            .nado-navbar .nv-user-wrap .nv-cta { padding: 8px 10px !important; gap: 4px !important; }
            .nado-navbar .nv-user-wrap .nv-user-name { display: none !important; }
        }
        .nado-navbar .nv-user-menu {
            position: absolute !important; right: 0 !important; top: calc(100% + 4px) !important;
            width: 192px !important; background: white !important; border-radius: 12px !important;
            box-shadow: 0 10px 15px -3px rgba(0,0,0,.1) !important;
            border: 1px solid #e2e8f0 !important; display: none !important;
            overflow: hidden !important; z-index: 9200 !important;
        }
        .nado-navbar .nv-user-menu.open { display: block !important; }
        .nado-navbar .nv-user-menu a,
        .nado-navbar .nv-user-menu button {
            display: flex !important; align-items: center !important; width: 100% !important;
            padding: 12px 16px !important; font-size: 13px !important; font-weight: 700 !important;
            text-align: left !important; background: none !important; border: none !important;
            cursor: pointer !important; text-decoration: none !important; color: #334155 !important; gap: 8px !important;
        }
        .nado-navbar .nv-user-menu a:hover { background-color: #f8fafc !important; color: #1459DD !important; }
        .nado-navbar .nv-user-menu i { width: 16px !important; height: 16px !important; }
        .nado-navbar .nv-logout { color: #dc2626 !important; }
        .nado-navbar .nv-logout:hover { background-color: #fef2f2 !important; color: #dc2626 !important; }

        /* Mobile drawer */
        .nado-navbar .nv-drawer {
            position: fixed !important; inset: 0 !important;
            z-index: 9300 !important; visibility: hidden !important; transition: visibility .4s !important;
        }
        .nado-navbar .nv-drawer.visible { visibility: visible !important; }
        .nado-navbar .nv-backdrop {
            position: absolute !important; inset: 0 !important;
            background-color: rgba(15,23,42,.4) !important;
            backdrop-filter: blur(8px) !important;
            opacity: 0 !important; transition: opacity .4s !important;
        }
        .nado-navbar .nv-drawer.visible .nv-backdrop { opacity: 1 !important; }
        .nado-navbar .nv-drawer-inner {
            position: absolute !important; top: 0 !important; right: 0 !important;
            height: 100% !important; width: 85% !important; max-width: 380px !important;
            background: white !important;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,.25) !important;
            transform: translateX(100%) !important; transition: transform .4s !important;
            display: flex !important; flex-direction: column !important;
        }
        .nado-navbar .nv-drawer.visible .nv-drawer-inner { transform: translateX(0) !important; }
        .nado-navbar .nv-drawer-head {
            padding: 28px 28px 20px !important; display: flex !important;
            justify-content: space-between !important; align-items: center !important;
            border-bottom: 1px solid #f1f5f9 !important;
        }
        .nado-navbar .nv-close { background: none !important; border: none !important; color: #94a3b8 !important; cursor: pointer !important; }
        .nado-navbar .nv-close:hover { color: #0f172a !important; }
        .nado-navbar .nv-drawer-links {
            flex: 1 !important; overflow-y: auto !important; padding: 20px 28px !important;
            display: flex !important; flex-direction: column !important; gap: 20px !important;
        }
        .nado-navbar .nv-drawer-links a {
            font-size: 17px !important; font-weight: 900 !important;
            text-transform: uppercase !important; letter-spacing: -.02em !important;
            color: #0f172a !important; text-decoration: none !important;
        }
        .nado-navbar .nv-drawer-links a:hover { color: #1459DD !important; }
        .nado-navbar .nv-drawer-footer { padding: 28px !important; display: flex !important; flex-direction: column !important; align-items: center !important; }
        .nado-navbar .nv-drawer-logout {
            display: flex !important; align-items: center !important; justify-content: center !important; gap: 8px !important;
            width: 100% !important; padding: 12px 16px !important; border-radius: 12px !important;
            background: #fef2f2 !important; border: 1px solid #fecaca !important; color: #dc2626 !important;
            font-size: 13px !important; font-weight: 800 !important; text-transform: uppercase !important;
            letter-spacing: .05em !important; cursor: pointer !important;
        }
        .nado-navbar .nv-drawer-logout i { width: 16px !important; height: 16px !important; }
        .nado-navbar .nv-drawer-cta {
            width: 100% !important; padding: 18px !important; border-radius: 20px !important;
            font-size: 13px !important; font-weight: 900 !important;
            text-transform: uppercase !important; letter-spacing: .15em !important;
            color: #0f172a !important; text-decoration: none !important;
            display: flex !important; align-items: center !important; justify-content: center !important;
            gap: 10px !important; margin-bottom: 32px !important;
        }
        .nado-navbar .nv-socials { display: flex !important; gap: 32px !important; margin-bottom: 32px !important; }
        .nado-navbar .nv-socials a { color: #94a3b8 !important; text-decoration: none !important; }
        .nado-navbar .nv-socials a:hover { color: #0f172a !important; }
        .nado-navbar .nv-divider { width: 100% !important; height: 1px !important; background-color: #f1f5f9 !important; margin-bottom: 28px !important; }
        .nado-navbar .nv-support { text-align: center !important; }
        .nado-navbar .nv-support p { font-size: 12px !important; font-weight: 800 !important; text-transform: uppercase !important; letter-spacing: .05em !important; margin-bottom: 8px !important; color: #334155 !important; }
        .nado-navbar .nv-support a { font-size: 18px !important; font-weight: 900 !important; color: #0f172a !important; text-decoration: none !important; }
        .nado-navbar .nv-support a:hover { color: #1459DD !important; }
        .nado-navbar .nv-icon-sm { width: 15px !important; height: 15px !important; }

        @media (max-width: 1023px) {
            .nado-navbar .nv-main { height: 72px !important; }
            .nado-navbar .nv-wrap { flex-wrap: nowrap !important; }
            .nado-navbar .nv-content { flex-wrap: nowrap !important; }
            .nado-navbar .nv-left { gap: 0 !important; flex-shrink: 1 !important; min-width: 0 !important; overflow: hidden !important; }
            .nado-navbar .nv-logo { flex-shrink: 1 !important; min-width: 0 !important; overflow: hidden !important; }
            .nado-navbar .nv-logo img { height: 24px !important; width: auto !important; max-width: 100% !important; }
            .nado-navbar .nv-logo-text { font-size: 1.4rem !important; }
            .nado-navbar .nv-right { gap: 8px !important; padding-left: 8px !important; flex-shrink: 0 !important; flex-wrap: nowrap !important; }
        }

        /* Force single line on ALL mobile sizes */
        .nado-navbar .nv-cta-publish {
            white-space: nowrap !important;
            flex-shrink: 0 !important;
        }

        @media (max-width: 1023px) {
            .nado-navbar .nv-cta-publish {
                font-size: 11px !important;
                padding: 8px 10px !important;
                letter-spacing: 0 !important;
                text-transform: none !important;
                white-space: nowrap !important;
            }
        }

        @media (max-width: 480px) {
            .nado-navbar .nv-cta-publish {
                font-size: 10px !important;
                padding: 6px 8px !important;
                letter-spacing: 0 !important;
                text-transform: none !important;
            }
        }

        @media (max-width: 360px) {
            .nado-navbar .nv-cta-publish {
                font-size: 9px !important;
                padding: 5px 6px !important;
                letter-spacing: -0.02em !important;
                text-transform: none !important;
            }
        }

        /* ---- Label swap: show the long label by default, the short one on phones ---- */
        .nado-navbar .nv-cta-publish .nv-cta-label-long  { display: inline !important; }
        .nado-navbar .nv-cta-publish .nv-cta-label-short { display: none !important; }
        @media (max-width: 1023px) {
            .nado-navbar .nv-cta-publish .nv-cta-label-long  { display: none !important; }
            .nado-navbar .nv-cta-publish .nv-cta-label-short { display: inline !important; }
        }
        /* The text node itself must not break, whatever the parent is doing. */
        .nado-navbar .nv-cta-publish,
        .nado-navbar .nv-cta-publish span {
            white-space: nowrap !important;
            word-break: keep-all !important;
            overflow-wrap: normal !important;
            hyphens: none !important;
            min-width: 0 !important;
        }

        /* ---- Top bar: keep contact details on one line ---- */
        .nado-navbar .nv-info,
        .nado-navbar .nv-info a {
            white-space: nowrap !important;
            word-break: keep-all !important;
        }
        .nado-navbar .nv-info i { flex-shrink: 0 !important; }
        @media (max-width: 600px) {
            /* Socials are duplicated in the drawer; the phone number matters more. */
            .nado-navbar .nv-social { display: none !important; }
            .nado-navbar .nv-topbar { padding: 8px 0 !important; }
            .nado-navbar .nv-topbar .nv-wrap { padding: 0 10px !important; }
            .nado-navbar .nv-contact { gap: 10px !important; }
            .nado-navbar .nv-info { font-size: 9px !important; letter-spacing: .02em !important; gap: 5px !important; }
        }
        @media (max-width: 380px) {
            /* Last resort: drop the email, keep the phone. */
            .nado-navbar .nv-contact .nv-info:first-child { display: none !important; }
            .nado-navbar .nv-info { font-size: 10px !important; }
        }
    </style>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            if (typeof lucide !== 'undefined') lucide.createIcons();

            var mobileMenuBtn  = document.getElementById('mobileMenuBtn');
            var closeDrawerBtn = document.getElementById('closeDrawerBtn');
            var mobileDrawer   = document.getElementById('mobileDrawer');
            var drawerBackdrop = document.getElementById('drawerBackdrop');

            function openDrawer()  { if (mobileDrawer) { mobileDrawer.classList.add('visible');    document.body.style.overflow = 'hidden'; } }
            function closeDrawer() { if (mobileDrawer) { mobileDrawer.classList.remove('visible'); document.body.style.overflow = ''; } }

            if (mobileMenuBtn)  mobileMenuBtn.addEventListener('click', openDrawer);
            if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
            if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);

            // User dropdown – click-toggle, no hover
            var userWrapper = document.querySelector('.nv-user-wrap');
            if (userWrapper) {
                var toggleBtn = userWrapper.querySelector('.nv-cta');
                var dropMenu  = userWrapper.querySelector('.nv-user-menu');
                if (toggleBtn && dropMenu) {
                    toggleBtn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        dropMenu.classList.toggle('open');
                    });
                    document.addEventListener('click', function (e) {
                        if (!userWrapper.contains(e.target)) dropMenu.classList.remove('open');
                    });
                }
            }
        });
    </script>
</header>

{{-- Spacer to push content below fixed navbar --}}
<div class="nado-navbar-spacer" style="height:118px;display:block;"></div>
<style>@media(max-width:1023px){.nado-navbar-spacer{height:94px !important;}}</style>
