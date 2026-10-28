loo<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">



<body id="bg" class="font-poppins home-2" style="background-color: #0f172a;">
@include('partials.locale-head')
    <!-- Ensure body matches Dark Theme -->
    <!-- Google Tag Manager (noscript) -->
    <noscript><iframe src="https://www.googletagmanager.com/ns.html?id=GTM-MZMXWVX4" height="0" width="0"
            style="display:none;visibility:hidden"></iframe></noscript>
    <!-- End Google Tag Manager (noscript) -->

    <div class="page-wraper">
        <!-- Navigation (Embedded) -->
        @php
            $client = Auth::guard('client')->user();
            // Check if on Auto Enchère route
            $isAutoEnchere = request()->routeIs('nadoautoenchere.*');

            // Theme Colors
            $accentColor = $isAutoEnchere ? '#F37021' : '#FDD817';
            $buttonHoverBg = $isAutoEnchere ? '#F37021' : '#FDD817';
            $logoText = $isAutoEnchere ? 'AUTO ENCHÈRE' : 'NADO';
            
        @endphp

        <header class="nado-navbar sticky-top">
            <!-- Font Awesome & Lucide Icons -->
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
                        <!-- Logo -->
                        <a href="{{ $isAutoEnchere ? route('nadoautoenchere.prelogin') : route('index') }}"
                            class="logo-link">
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

                        <!-- Desktop Menu -->
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
                                <a href="{{ route('nadoautoenchere.prelogin') }}"
                                    class="nav-link {{ request()->routeIs('nadoautoenchere.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.auction') }}</a>
                                <a href="{{ route('reprise.form') }}" class="nav-link {{ request()->routeIs('reprise.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.tradein') }}</a>
                                <a href="{{ route('financement.index') }}" class="nav-link {{ request()->routeIs('financement.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.financing') }}</a>
                                <a href="{{ route('about.index') }}" class="nav-link {{ request()->routeIs('about.*') ? 'highlight-link' : '' }}">{{ __('messages.nav.about') }}</a>
                            @endif
                        </div>
                    </div>

                    <!-- Right Actions -->
                    <div class="right-section">
                        @if ($client)
                            <div class="user-dropdown-wrapper">
                                <button class="cta-button outline-btn">
                                    <i data-lucide="user"></i>
                                    <span>{{ $client->name }}</span>
                                    <i data-lucide="chevron-down" class="icon-sm"></i>
                                </button>
                                <div class="user-dropdown-menu">
                                    <a href="{{ route('client.dashboard') }}"><i data-lucide="layout-dashboard"></i>
                                        {{ __('messages.nav.my_space') }}</a>
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="logout-btn"><i data-lucide="log-out"></i>
                                            {{ __('messages.nav.logout') }}</button>
                                    </form>
                                </div>
                            </div>
                        @else
                            <a href="{{ route('client.login') }}" class="cta-button outline-btn">
                                @if ($isAutoEnchere)
                                    {{ __('messages.nav.create_account') }}
                                @else
                                    {{ __('messages.nav.publish_ad') }}
                                @endif
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
                        @if ($isAutoEnchere)
                            <span class="logo-text">
                                <span class="text-dark">AUTO </span>
                                <span style="color: #F37021;">ENCHÈRE</span>
                            </span>
                        @else
                            <img src="{{ asset('storage/logos/UAfjepY72Xe7grWDva9cqSlRFsRKNZPV3HBDbBP1.png') }}"
                                 alt="NADO" style="height: 30px; width: auto; object-fit: contain;" />
                        @endif
                        <button id="closeDrawerBtn" class="close-btn">
                            <i data-lucide="x"></i>
                        </button>
                    </div>

                    <div class="drawer-links">
                        @if ($isAutoEnchere)
                            <a href="{{ route('achat.index') }}">{{ __('messages.nav.buy_ae') }}</a>
                            <a href="{{ route('vente.form') }}">{{ __('messages.nav.sell_individual') }}</a>
                            <a href="{{ route('client.login') }}">{{ __('messages.nav.sell_pro') }}</a>
                        @else
                            <a href="{{ route('achat.index') }}">{{ __('messages.nav.buy') }}</a>
                            <a href="{{ route('vente.form') }}">{{ __('messages.nav.sell') }}</a>
                            <a href="{{ route('nadoautoenchere.prelogin') }}"
                                style="color: #1459DD;">{{ __('messages.nav.auction') }}</a>
                            <a href="{{ route('reprise.form') }}">{{ __('messages.nav.tradein') }}</a>
                            <a href="{{ route('financement.index') }}">{{ __('messages.nav.financing') }}</a>
                            <a href="{{ route('about.index') }}">{{ __('messages.nav.about') }}</a>
                        @endif
                    </div>

                    <div class="drawer-footer">
                        @if ($client)
                            <a href="{{ route('client.dashboard') }}" class="drawer-cta"
                                style="background-color: {{ $accentColor }};">
                                <i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}
                            </a>
                        @else
                            <a href="{{ route('client.login') }}" class="drawer-cta"
                                style="background-color: {{ $accentColor }};">
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
                            <p>Support NADO</p>
                            <a href="tel:+212520070611">+212 520 070 611</a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Styles -->
            <style>
                :root {
                    --primary-blue: #1459DD;
                    --slate-900: #0f172a;
                    --slate-700: #334155;
                    --slate-50: #f8fafc;
                    --border-color: #e2e8f0;
                }

                .nado-navbar {
                    font-family: 'Inter', sans-serif;
                    width: 100%;
                    z-index: 1000;
                    position: relative;
                    direction: ltr;
                }

                .sticky-top {

                    position: fixed;
                    top: 0;
                    left: 0;
                    width: 100%;
                    z-index: 1000;
                }

                /* Top Bar */
                .top-bar {
                    padding: 10px 16px;
                    border-bottom: 1px solid rgba(0, 0, 0, 0.05);
                    color: var(--slate-900);
                }

                .custom-container {
                    max-width: 1280px;
                    margin: 0 auto;
                    padding: 0 16px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    width: 100%;
                }

                .contact-info {
                    display: flex;
                    gap: 12px;
                }

                .info-item {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    font-size: 11px;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    opacity: 0.8;
                }

                .info-item i {
                    width: 12px;
                    height: 12px;
                    color: var(--primary-blue);
                }

                .info-item a {
                    color: inherit;
                    text-decoration: none;
                    transition: color 0.2s;
                }

                .info-item a:hover {
                    color: var(--primary-blue);
                }

                .social-links {
                    display: flex;
                    gap: 20px;
                }

                .social-links a {
                    color: var(--slate-900);
                    opacity: 0.6;
                    transition: opacity 0.2s, color 0.2s;
                }

                .social-links a:hover {
                    color: var(--primary-blue);
                    opacity: 1;
                }

                .social-links i {
                    width: 20px;
                    height: 20px;
                }

                /* Main Nav */
                .main-nav {
                    background: white;
                    border-bottom: 1px solid var(--border-color);
                    height: 96px;
                    display: flex;
                    align-items: center;
                }

                .nav-content {
                    height: 100%;
                    width: 100%;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .left-section {
                    display: flex;
                    align-items: center;
                    gap: 64px;
                }

                .logo-link {
                    text-decoration: none;
                    transition: transform 0.2s;
                }

                .logo-link:hover {
                    transform: scale(1.05);
                }

                .logo-text {
                    font-size: 2.25rem;
                    /* 36px */
                    font-weight: 900;
                    text-transform: uppercase;
                    line-height: 1;
                    letter-spacing: -0.05em;
                }

                .text-dark {
                    color: var(--slate-900);
                }

                /* Desktop Menu */
                .desktop-menu {
                    display: none;
                    align-items: center;
                    gap: 12px;
                }

                @media (min-width: 1024px) {
                    .desktop-menu {
                        display: flex;
                    }
                }

                .nav-link {
                    font-size: 14.4px;
                    font-weight: 700;
                    text-transform: uppercase;
                    letter-spacing: 0.025em;
                    padding: 8px 12px;
                    border-radius: 16px;
                    color: var(--slate-700);
                    text-decoration: none;
                    transition: all 0.2s;
                    background: transparent;
                    border: none;
                    cursor: pointer;
                }

                .nav-link:hover {
                    background-color: #f1f5f9;
                    color: var(--primary-blue);
                }

                .highlight-link {
                    color: var(--primary-blue);
                    background-color: #dbeafe;
                }

                .highlight-link:hover {
                    background-color: #bfdbfe;
                }

                /* Dropdown */
                .nav-dropdown-wrapper {
                    position: relative;
                }

                .dropdown-trigger {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                }

                .nav-dropdown-menu {
                    position: absolute;
                    top: 100%;
                    left: 0;
                    margin-top: 8px;
                    background: white;
                    border-radius: 16px;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                    border: 1px solid var(--border-color);
                    min-width: 200px;
                    overflow: hidden;
                    display: none;
                }

                .nav-dropdown-wrapper:hover .nav-dropdown-menu {
                    display: block;
                }

                .nav-dropdown-menu a {
                    display: block;
                    padding: 16px 20px;
                    font-size: 14px;
                    font-weight: 700;
                    text-transform: uppercase;
                    color: var(--slate-700);
                    text-decoration: none;
                    transition: background 0.2s;
                }

                .nav-dropdown-menu a:hover {
                    background-color: #fff7ed;
                    color: #F37021;
                }

                /* Right Section */
                .right-section {
                    margin-left: auto;
                    display: flex;
                    align-items: center;
                    gap: 12px;
                }

                .cta-button {
                    display: flex;
                    align-items: center;
                    gap: 8px;
                    padding: 10px 16px;
                    border-radius: 16px;
                    font-size: 15px;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    text-decoration: none;
                    cursor: pointer;
                    transition: all 0.2s;
                }

                .cta-button:active {
                    transform: scale(0.95);
                }

                .outline-btn {
                    background-color: rgba(253, 216, 23, 0.2);
                    border: 1px solid rgba(253, 216, 23, 0.5);
                    color: var(--slate-900);
                }

                .outline-btn:hover {
                    background-color: #FDD817;
                }

                /* Adjust hover for enchere mode via JS or specific classes if needed,
                   but inline style logic handles the main color. Here we default to Nado yellow styles. */

                .desktop-only {
                    display: none;
                }

                @media (min-width: 1024px) {
                    .desktop-only {
                        display: flex;
                    }
                }

                .mobile-menu-btn {
                    display: block;
                    padding: 12px;
                    background-color: var(--slate-50);
                    border: none;
                    border-radius: 12px;
                    cursor: pointer;
                    color: var(--slate-900);
                }

                @media (min-width: 1024px) {
                    .mobile-menu-btn {
                        display: none;
                    }
                }

                .user-dropdown-wrapper {
                    position: relative;
                    display: none;
                }

                @media (min-width: 1024px) {
                    .user-dropdown-wrapper {
                        display: block;
                    }
                }

                .user-dropdown-menu {
                    position: absolute;
                    right: 0;
                    top: 100%;
                    margin-top: 8px;
                    width: 192px;
                    background: white;
                    border-radius: 12px;
                    box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
                    border: 1px solid var(--border-color);
                    display: none;
                    overflow: hidden;
                }

                .user-dropdown-wrapper:hover .user-dropdown-menu {
                    display: block;
                }

                .user-dropdown-menu a,
                .user-dropdown-menu button {
                    display: flex;
                    align-items: center;
                    width: 100%;
                    padding: 12px 16px;
                    font-size: 14px;
                    font-weight: 700;
                    text-align: left;
                    background: none;
                    border: none;
                    cursor: pointer;
                    text-decoration: none;
                    color: var(--slate-700);
                }

                .user-dropdown-menu a:hover {
                    background-color: #f8fafc;
                    color: var(--primary-blue);
                }

                .user-dropdown-menu i {
                    width: 16px;
                    height: 16px;
                    margin-right: 8px;
                }

                .logout-btn {
                    color: #dc2626 !important;
                }

                .logout-btn:hover {
                    background-color: #fef2f2 !important;
                }

                /* Mobile Drawer */
                .mobile-drawer {
                    position: fixed;
                    inset: 0;
                    z-index: 1100;
                    visibility: hidden;
                    transition: visibility 0.5s;
                }

                .mobile-drawer.visible {
                    visibility: visible;
                }

                .drawer-backdrop {
                    position: absolute;
                    inset: 0;
                    background-color: rgba(15, 23, 42, 0.4);
                    backdrop-filter: blur(12px);
                    opacity: 0;
                    transition: opacity 0.5s;
                }

                .visible .drawer-backdrop {
                    opacity: 1;
                }

                .drawer-content {
                    position: absolute;
                    top: 0;
                    right: 0;
                    height: 100%;
                    width: 85%;
                    max-width: 420px;
                    background: white;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                    transform: translateX(100%);
                    transition: transform 0.5s;
                    display: flex;
                    flex-direction: column;
                }

                .visible .drawer-content {
                    transform: translateX(0);
                }

                .drawer-header {
                    padding: 32px;
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                }

                .close-btn {
                    background: none;
                    border: none;
                    color: #94a3b8;
                    cursor: pointer;
                }

                .close-btn:hover {
                    color: var(--slate-900);
                }

                .drawer-links {
                    flex: 1;
                    overflow-y: auto;
                    padding: 16px 32px;
                    display: flex;
                    flex-direction: column;
                    gap: 24px;
                }

                .drawer-links a {
                    font-size: 18px;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: -0.025em;
                    color: var(--slate-900);
                    text-decoration: none;
                    transition: color 0.2s;
                }

                .drawer-links a:hover {
                    color: var(--primary-blue);
                }

                .drawer-footer {
                    padding: 40px;
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                }

                .drawer-cta {
                    width: 100%;
                    padding: 24px;
                    border-radius: 32px;
                    font-size: 14px;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: 0.2em;
                    color: var(--slate-900);
                    text-decoration: none;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    gap: 12px;
                    margin-bottom: 48px;
                    transition: transform 0.2s;
                }

                .drawer-cta:active {
                    transform: scale(0.95);
                }

                .drawer-socials {
                    display: flex;
                    gap: 40px;
                    margin-bottom: 48px;
                }

                .drawer-socials a {
                    color: #94a3b8;
                    transition: color 0.2s;
                }

                .drawer-socials a:hover {
                    color: var(--slate-900);
                }

                .drawer-divider {
                    width: 100%;
                    height: 1px;
                    background-color: #f1f5f9;
                    margin-bottom: 40px;
                }

                .drawer-support {
                    text-align: center;
                }

                .drawer-support p {
                    font-size: 14px;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.025em;
                    margin-bottom: 12px;
                }

                .drawer-support a {
                    font-size: 20px;
                    font-weight: 900;
                    color: var(--slate-900);
                    text-decoration: none;
                    transition: color 0.2s;
                }

                .drawer-support a:hover {
                    color: var(--primary-blue);
                }

                .icon-sm {
                    width: 16px;
                    height: 16px;
                }

                @media (max-width: 1023px) {
                    .main-nav {
                        height: 72px;
                    }

                    .logo-link img {
                        height: 24px !important;
                        width: auto !important;
                    }

                    .logo-text {
                        font-size: 1.4rem;
                    }

                    .right-section {
                        gap: 12px;
                        padding-left: 12px;
                    }

                    .right-section .cta-button {
                        padding: 10px 18px;
                        font-size: 13px;
                        letter-spacing: 0.05em;
                        border-radius: 12px;
                    }
                }
            </style>
        </header>

        <!-- Navbar Scripts -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                // Initialize Lucide Icons
                if (typeof lucide !== 'undefined') {
                    lucide.createIcons();
                }

                // Drawer Logic
                const mobileMenuBtn = document.getElementById('mobileMenuBtn');
                const closeDrawerBtn = document.getElementById('closeDrawerBtn');
                const mobileDrawer = document.getElementById('mobileDrawer');
                const drawerBackdrop = document.getElementById('drawerBackdrop');
                const drawerContent = document.getElementById('drawerContent');

                function openDrawer() {
                    if (!mobileDrawer) return;
                    mobileDrawer.classList.add('visible');
                    document.body.style.overflow = 'hidden';
                }

                function closeDrawer() {
                    if (!mobileDrawer) return;
                    mobileDrawer.classList.remove('visible');
                    document.body.style.overflow = 'unset';
                }

                if (mobileMenuBtn) mobileMenuBtn.addEventListener('click', openDrawer);
                if (closeDrawerBtn) closeDrawerBtn.addEventListener('click', closeDrawer);
                if (drawerBackdrop) drawerBackdrop.addEventListener('click', closeDrawer);
            });
        </script>

        <!-- Content -->
        <div class="page-content"
            style="width: 100%; max-width: 100%; padding: 0; margin: 0; overflow-x: hidden;">

            <!-- Custom CSS for Reprise Section -->
            <style>
                :root {
                    --primary: #1459DD;
                    --slate-50: #f8fafc;
                    --slate-100: #f1f5f9;
                    --slate-300: #cbd5e1;
                    --slate-400: #94a3b8;
                    --slate-500: #64748b;
                    --slate-800: #1e293b;
                    --slate-900: #0f172a;
                    --white: #ffffff;
                }

                /* Override any potential global constraints for this specific wrapper */
                .reprise-wrapper {
                    font-family: 'Inter', sans-serif;
                    color: var(--slate-900);
                    width: 100%;
                    overflow-x: hidden;
                    margin: 0;
                    padding: 0;
                }

                .reprise-wrapper button {
                    cursor: pointer;
                    border: none;
                    outline: none;
                    transition: all 0.3s ease;
                }

                .reprise-wrapper .btn-primary {
                    background-color: var(--primary);
                    color: #ffffff !important;
                    padding: 1.5rem 3rem;
                    border-radius: 9999px;
                    font-weight: 900;
                    font-size: 0.75rem;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    box-shadow: 0 25px 50px -12px rgba(20, 89, 221, 0.4);
                    display: inline-flex;
                    align-items: center;
                    gap: 1rem;
                    text-decoration: none;
                }

                .reprise-wrapper .btn-primary:hover {
                    background-color: #1d4ed8;
                    transform: translateY(-2px);
                    color: var(--white);
                }

                .reprise-wrapper .btn-primary:active {
                    transform: scale(0.95);
                }

                /* Hero Section */
                .reprise-wrapper .hero {
                    position: relative;
                    height: 100vh;
                    min-height: 600px;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    background-color: var(--slate-900);
                    overflow: hidden;
                    width: 100%;
                    padding: 0;
                    margin: 0;
                }

                .reprise-wrapper .hero .container-custom { padding-top: 0; }

                .reprise-wrapper .hero-bg {
                    position: absolute;
                    inset: 0;
                    opacity: 0.1;
                    pointer-events: none;
                    background-image: radial-gradient(#1459DD 1px, transparent 1px);
                    background-size: 40px 40px;
                }

                .reprise-wrapper .container-custom {
                    max-width: 80rem;
                    margin: 0 auto;
                    padding: 0 1.5rem;
                    width: 100%;
                }

                /* Override for Hero Title */
                .reprise-wrapper .hero h1 {
                    font-size: 3rem;
                    font-weight: 900;
                    color: #ffffff !important;
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    line-height: 1;
                    margin: 0 0 1.75rem 0;
                }

                @media (min-width: 768px) {
                    .reprise-wrapper .hero h1 {
                        font-size: 4.5rem;
                    }
                }

                @media (max-height: 780px) {
                    .reprise-wrapper .hero h1 { font-size: 2.25rem; margin-bottom: 1rem; }
                    .reprise-wrapper .hero p  { font-size: 1rem !important; margin-bottom: 1.5rem !important; }
                    .reprise-wrapper .hero .badge { margin-bottom: 1rem; }
                }

                .reprise-wrapper .text-highlight {
                    color: var(--primary) !important;
                }

                .reprise-wrapper .text-gradient {
                    background: linear-gradient(135deg, #1459DD, #7ea9ff);
                    -webkit-background-clip: text;
                    -webkit-text-fill-color: transparent;
                    background-clip: text;
                }

                /* Stats strip */
                .reprise-wrapper .stats-strip {
                    background: #fff;
                    border-bottom: 1px solid #e2e8f0;
                    padding: 2rem 0;
                }

                .reprise-wrapper .stats-strip-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 2rem;
                    text-align: center;
                }

                @media (min-width: 768px) {
                    .reprise-wrapper .stats-strip-grid {
                        grid-template-columns: repeat(4, 1fr);
                    }
                }

                .reprise-wrapper .stat-item-num {
                    font-size: 2rem;
                    font-weight: 900;
                    color: var(--primary);
                    letter-spacing: -0.05em;
                    line-height: 1;
                    display: block;
                }

                .reprise-wrapper .stat-item-label {
                    font-size: 0.75rem;
                    font-weight: 700;
                    color: var(--slate-500);
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    margin-top: 0.25rem;
                    display: block;
                }

                /* Floating stat card */
                .reprise-wrapper .hero-float-card {
                    position: absolute;
                    bottom: -1.5rem;
                    left: -1.5rem;
                    background: white;
                    border-radius: 1.5rem;
                    padding: 1.25rem 1.75rem;
                    box-shadow: 0 20px 40px rgba(0,0,0,0.2);
                    display: flex;
                    align-items: center;
                    gap: 1rem;
                    z-index: 10;
                }

                .reprise-wrapper .hero-float-icon {
                    width: 3rem;
                    height: 3rem;
                    background: #eff6ff;
                    border-radius: 0.75rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--primary);
                    flex-shrink: 0;
                }

                .reprise-wrapper .hero-float-num {
                    font-size: 1.5rem;
                    font-weight: 900;
                    color: var(--slate-900);
                    letter-spacing: -0.05em;
                    line-height: 1;
                }

                .reprise-wrapper .hero-float-label {
                    font-size: 0.7rem;
                    font-weight: 700;
                    color: var(--slate-500);
                    text-transform: uppercase;
                    letter-spacing: 0.08em;
                }

                .reprise-wrapper .hero p {
                    font-size: 1.25rem;
                    color: #94a3b8 !important;
                    /* slate-400 */
                    font-weight: 500;
                    line-height: 1.625;
                    max-width: 32rem;
                    margin: 0 0 3rem 0;
                }

                .reprise-wrapper .hero-image-wrapper {
                    display: none;
                    position: relative;
                }

                @media (min-width: 1024px) {
                    .reprise-wrapper .hero-image-wrapper {
                        display: block;
                    }
                }

                .reprise-wrapper .hero-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 4rem;
                    align-items: center;
                    position: relative;
                    z-index: 10;
                }

                @media (min-width: 1024px) {
                    .reprise-wrapper .hero-grid {
                        grid-template-columns: 1fr 1fr;
                    }
                }

                .reprise-wrapper .badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    padding: 0.5rem 1rem;
                    background-color: #FDD817;
                    color: var(--slate-900);
                    border-radius: 9999px;
                    font-weight: 900;
                    font-size: 10px;
                    text-transform: uppercase;
                    letter-spacing: 0.3em;
                    margin-bottom: 2rem;
                    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
                }

                .reprise-wrapper .hero-card {
                    background-color: var(--slate-800);
                    padding: 0.75rem;
                    border-radius: 4rem;
                    border: 1px solid rgba(255, 255, 255, 0.1);
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
                    position: relative;
                    overflow: hidden;
                    transform: rotate(2deg);
                }

                .reprise-wrapper .hero-img {
                    width: 100%;
                    height: auto;
                    border-radius: 3.5rem;
                    filter: grayscale(100%);
                    display: block;
                }

                .reprise-wrapper .hero-card-content {
                    position: absolute;
                    bottom: 2.5rem;
                    left: 2.5rem;
                    right: 2.5rem;
                    display: flex;
                    justify-content: space-between;
                    align-items: flex-end;
                }

                .reprise-wrapper .hero-card-title {
                    color: var(--white);
                    font-weight: 900;
                    font-size: 1.5rem;
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    line-height: 1;
                }

                .reprise-wrapper .icon-box {
                    width: 4rem;
                    height: 4rem;
                    background-color: rgba(255, 255, 255, 0.1);
                    backdrop-filter: blur(24px);
                    border: 1px solid rgba(255, 255, 255, 0.2);
                    border-radius: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    color: var(--white);
                }

                /* Process Section */
                .reprise-wrapper .process-section {
                    padding: 8rem 0;
                    background-color: var(--slate-50);
                }

                .reprise-wrapper .section-header {
                    text-align: left;
                    margin-bottom: 5rem;
                }

                .reprise-wrapper .subtitle {
                    font-size: 10px;
                    font-weight: 900;
                    color: var(--primary);
                    text-transform: uppercase;
                    letter-spacing: 0.5em;
                    margin-bottom: 1rem;
                    display: block;
                }

                .reprise-wrapper .section-title {
                    font-size: 2.25rem;
                    font-weight: 900;
                    color: var(--slate-900);
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    line-height: 1;
                    margin: 0;
                }

                @media (min-width: 768px) {
                    .reprise-wrapper .section-title {
                        font-size: 3.75rem;
                    }
                }

                .reprise-wrapper .steps-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 1.5rem;
                }

                @media (min-width: 768px) {
                    .reprise-wrapper .steps-grid {
                        grid-template-columns: repeat(2, 1fr);
                    }
                }

                @media (min-width: 1024px) {
                    .reprise-wrapper .steps-grid {
                        grid-template-columns: repeat(4, 1fr);
                    }
                }

                .reprise-wrapper .step-card {
                    background-color: var(--white);
                    padding: 2.5rem;
                    border-radius: 2.5rem;
                    border: 1px solid var(--slate-100);
                    transition: all 0.3s ease;
                    position: relative;
                    overflow: hidden;
                }

                .reprise-wrapper .step-card:hover {
                    border-color: var(--primary);
                }

                .reprise-wrapper .step-number {
                    position: absolute;
                    top: 1.5rem;
                    right: 2.5rem;
                    font-size: 3rem;
                    font-weight: 900;
                    color: var(--slate-50);
                    transition: color 0.3s ease;
                }

                .reprise-wrapper .step-card:hover .step-number {
                    color: #eff6ff;
                }

                .reprise-wrapper .step-icon {
                    width: 4rem;
                    height: 4rem;
                    background-color: #eff6ff;
                    color: var(--primary);
                    border-radius: 1rem;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    margin-bottom: 2.5rem;
                    transition: all 0.3s ease;
                    position: relative;
                    z-index: 10;
                }

                .reprise-wrapper .step-card:hover .step-icon {
                    background-color: var(--primary);
                    color: var(--white);
                }

                .reprise-wrapper .step-title {
                    font-size: 1.25rem;
                    font-weight: 900;
                    color: var(--slate-900);
                    text-transform: uppercase;
                    letter-spacing: -0.025em;
                    margin: 0 0 1rem 0;
                    position: relative;
                    z-index: 10;
                }

                .reprise-wrapper .step-desc {
                    color: var(--slate-500);
                    font-size: 0.875rem;
                    font-weight: 500;
                    line-height: 1.625;
                    margin: 0;
                    position: relative;
                    z-index: 10;
                }

                /* Modal */
                .reprise-wrapper .modal-overlay {
                    position: fixed;
                    inset: 0;
                    z-index: 9999;
                    background-color: rgba(15, 23, 42, 0.95);
                    backdrop-filter: blur(8px);
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    padding: 1rem;
                    opacity: 0;
                    visibility: hidden;
                    transition: all 0.3s ease;
                    overflow-y: auto;
                    /* Allow scrolling on small screens */
                }

                .reprise-wrapper .modal-overlay.open {
                    opacity: 1;
                    visibility: visible;
                }

                .reprise-wrapper .modal-content {
                    background-color: var(--white);
                    border-radius: 2rem;
                    /* Reduced for mobile */
                    width: 100%;
                    max-width: 64rem;
                    /* Wider max-width */
                    margin: auto;
                    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
                    position: relative;
                    display: grid;
                    grid-template-columns: 1fr;
                    text-align: left;
                    overflow: hidden;
                }

                @media (min-width: 1024px) {
                    .reprise-wrapper .modal-content {
                        grid-template-columns: 2fr 3fr;
                        /* More space for form */
                        min-height: 600px;
                        border-radius: 3rem;
                    }
                }

                .reprise-wrapper .modal-close {
                    position: absolute;
                    top: 1.5rem;
                    right: 1.5rem;
                    background: rgba(255, 255, 255, 0.8);
                    border-radius: 50%;
                    padding: 0.5rem;
                    color: var(--slate-400);
                    z-index: 20;
                    transition: all 0.2s;
                    cursor: pointer;
                }

                @media (min-width: 768px) {
                    .reprise-wrapper .modal-close {
                        top: 2rem;
                        right: 2rem;
                    }
                }

                .reprise-wrapper .modal-close:hover {
                    color: var(--slate-900);
                    background: white;
                    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
                }

                .reprise-wrapper .modal-left {
                    background-color: #1459DD;
                    padding: 2.5rem;
                    display: none;
                    flex-direction: column;
                    justify-content: space-between;
                    color: var(--white);
                    background-image: linear-gradient(to bottom right, #1459DD, #7ea9ff);
                    position: relative;
                    overflow: hidden;
                }

                .reprise-wrapper .modal-left::before {
                    content: '';
                    position: absolute;
                    inset: 0;
                    background-image: radial-gradient(#FDD817 1.5px, transparent 1.5px);
                    background-size: 28px 28px;
                    opacity: 0.25;
                    pointer-events: none;
                }

                @media (min-width: 1024px) {
                    .reprise-wrapper .modal-left {
                        display: flex;
                        padding: 4rem;
                    }
                }

                .reprise-wrapper .modal-left > * {
                    position: relative;
                    z-index: 1;
                }

                .reprise-wrapper .modal-left h3 {
                    font-size: 2.5rem;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    line-height: 1;
                    margin: 0 0 1.5rem 0;
                    color: white !important;
                }

                .reprise-wrapper .modal-left p {
                    color: rgba(255,255,255,0.75);
                    font-weight: 500;
                    line-height: 1.6;
                    font-size: 1.125rem;
                }

                .reprise-wrapper .modal-left-badge {
                    display: inline-flex;
                    align-items: center;
                    gap: 0.5rem;
                    background: #FDD817;
                    color: #0f172a;
                    border-radius: 9999px;
                    padding: 0.4rem 1rem;
                    font-size: 0.65rem;
                    font-weight: 900;
                    text-transform: uppercase;
                    letter-spacing: 0.15em;
                    margin-bottom: 1.5rem;
                }

                .reprise-wrapper .modal-left-trust {
                    margin-top: auto;
                    padding-top: 2rem;
                    display: flex;
                    flex-direction: column;
                    gap: 0.75rem;
                }

                .reprise-wrapper .modal-left-trust-item {
                    display: flex;
                    align-items: center;
                    gap: 0.75rem;
                    font-size: 0.875rem;
                    font-weight: 600;
                    color: rgba(255,255,255,0.9);
                }

                .reprise-wrapper .modal-left-trust-dot {
                    width: 0.5rem;
                    height: 0.5rem;
                    border-radius: 50%;
                    background: #FDD817;
                    flex-shrink: 0;
                }

                .reprise-wrapper .modal-right {
                    padding: 2rem;
                    /* Reduced mobile padding */
                    background-color: #fff;
                }

                @media (min-width: 768px) {
                    .reprise-wrapper .modal-right {
                        padding: 3rem;
                    }
                }

                @media (min-width: 1024px) {
                    .reprise-wrapper .modal-right {
                        padding: 4rem;
                    }
                }

                .reprise-wrapper .form-grid {
                    display: grid;
                    grid-template-columns: 1fr;
                    gap: 1rem;
                }

                /* Change breakpoint to 768px (MD) for 2 columns */
                @media (min-width: 768px) {
                    .reprise-wrapper .form-grid {
                        grid-template-columns: 1fr 1fr;
                        gap: 1.25rem;
                    }

                    .reprise-wrapper .form-col-full {
                        grid-column: span 2;
                    }
                }

                .reprise-wrapper .form-label {
                    display: block;
                    font-size: 0.7rem;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.05em;
                    color: var(--slate-500);
                    margin-bottom: 0.375rem;
                    margin-left: 0.25rem;
                }

                .reprise-wrapper .form-input {
                    width: 100%;
                    background-color: #f8fafc;
                    /* slate-50 */
                    border: 1px solid #e2e8f0 !important;
                    /* slate-200 */
                    border-radius: 0.75rem;
                    padding: 0.75rem 1rem;
                    font-weight: 600;
                    font-size: 0.95rem;
                    font-family: inherit;
                    box-sizing: border-box;
                    outline: none;
                    transition: all 0.2s ease;
                    height: auto;
                    color: var(--slate-900);
                    box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
                }

                /* ---- Zones de depot photos (gauche / droite / avant / arriere) ---- */
                .reprise-wrapper .rp-dropzones {
                    display: grid;
                    grid-template-columns: 1fr 1fr;
                    gap: 0.75rem;
                }

                .reprise-wrapper .rp-dz {
                    position: relative;
                    display: flex;
                    align-items: center;
                    justify-content: center;
                    aspect-ratio: 4 / 3;
                    background-color: #f8fafc;
                    border: 1.5px dashed #cbd5e1;
                    border-radius: 0.75rem;
                    cursor: pointer;
                    overflow: hidden;
                    text-align: center;
                    transition: border-color 0.2s, background-color 0.2s;
                }

                .reprise-wrapper .rp-dz:hover,
                .reprise-wrapper .rp-dz.is-dragover {
                    border-color: #1459DD;
                    background-color: #f0f6ff;
                }

                .reprise-wrapper .rp-dz-input {
                    position: absolute;
                    inset: 0;
                    opacity: 0;
                    width: 100%;
                    height: 100%;
                    cursor: pointer;
                }

                .reprise-wrapper .rp-dz-body {
                    display: flex;
                    flex-direction: column;
                    align-items: center;
                    justify-content: center;
                    gap: 0.25rem;
                    padding: 0.5rem;
                    pointer-events: none;
                }

                .reprise-wrapper .rp-dz-icon {
                    width: 22px;
                    height: 22px;
                    color: #94a3b8;
                    margin-bottom: 0.125rem;
                }

                .reprise-wrapper .rp-dz:hover .rp-dz-icon,
                .reprise-wrapper .rp-dz.is-dragover .rp-dz-icon {
                    color: #1459DD;
                }

                .reprise-wrapper .rp-dz-title {
                    font-size: 0.75rem;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.04em;
                    color: var(--slate-900);
                    line-height: 1.2;
                }

                .reprise-wrapper .rp-dz-hint {
                    font-size: 0.65rem;
                    font-weight: 600;
                    color: #94a3b8;
                    line-height: 1.2;
                }

                /* Apercu une fois la photo choisie */
                .reprise-wrapper .rp-dz-preview {
                    position: absolute;
                    inset: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                    display: none;
                }

                .reprise-wrapper .rp-dz.has-file {
                    border-style: solid;
                    border-color: #1459DD;
                }

                .reprise-wrapper .rp-dz.has-file .rp-dz-preview { display: block; }
                .reprise-wrapper .rp-dz.has-file .rp-dz-body    { display: none; }

                .reprise-wrapper .rp-dz-remove {
                    position: absolute;
                    top: 6px;
                    right: 6px;
                    z-index: 3;
                    width: 24px;
                    height: 24px;
                    display: none;
                    align-items: center;
                    justify-content: center;
                    border: none;
                    border-radius: 50%;
                    background: rgba(15, 23, 42, 0.75);
                    color: #fff;
                    font-size: 16px;
                    line-height: 1;
                    cursor: pointer;
                    padding: 0;
                }

                .reprise-wrapper .rp-dz-remove:hover { background: #dc2626; }
                .reprise-wrapper .rp-dz.has-file .rp-dz-remove { display: flex; }

                /* Etiquette du cote, visible par-dessus l'apercu */
                .reprise-wrapper .rp-dz.has-file::after {
                    content: attr(data-slot-label);
                    position: absolute;
                    left: 0;
                    right: 0;
                    bottom: 0;
                    z-index: 2;
                    padding: 4px 6px;
                    background: rgba(15, 23, 42, 0.72);
                    color: #fff;
                    font-size: 0.6rem;
                    font-weight: 800;
                    text-transform: uppercase;
                    letter-spacing: 0.06em;
                }

                .reprise-wrapper .rp-dz-note {
                    display: block;
                    margin-top: 0.5rem;
                    font-size: 0.7rem;
                    font-weight: 600;
                    color: #94a3b8;
                    line-height: 1.4;
                }

                .reprise-wrapper .rp-dz-error {
                    border-color: #dc2626 !important;
                    background-color: #fef2f2 !important;
                }

                /* ---- Champ nombre (Kilométrage) : pas de flèches haut/bas ---- */
                .reprise-wrapper input[type="number"]::-webkit-outer-spin-button,
                .reprise-wrapper input[type="number"]::-webkit-inner-spin-button {
                    -webkit-appearance: none;
                    appearance: none;
                    margin: 0;
                }

                .reprise-wrapper input[type="number"] {
                    -moz-appearance: textfield;
                    appearance: textfield;
                }

                .reprise-wrapper .form-input:focus {
                    border-color: var(--primary) !important;
                    background-color: white;
                    box-shadow: 0 0 0 3px rgba(20, 89, 221, 0.15);
                }

                .reprise-wrapper .form-input::placeholder {
                    color: #94a3b8;
                    /* slate-400 */
                    font-weight: 500;
                }

                .reprise-wrapper .btn-submit {
                    width: 100%;
                    padding: 1rem;
                    background-color: var(--primary);
                    background-image: linear-gradient(to bottom right, var(--primary), #1d4ed8);
                    color: var(--white);
                    border-radius: 0.75rem;
                    font-weight: 800;
                    font-size: 0.875rem;
                    text-transform: uppercase;
                    letter-spacing: 0.1em;
                    text-decoration: none;
                    transition: all 0.2s ease;
                    border: none;
                    cursor: pointer;
                    box-shadow: 0 4px 6px -1px rgba(20, 89, 221, 0.3);
                }

                .reprise-wrapper .btn-submit:hover {
                    background-image: linear-gradient(to bottom right, #1d4ed8, #1e40af);
                    transform: translateY(-1px);
                    box-shadow: 0 10px 15px -3px rgba(20, 89, 221, 0.4);
                }

                .reprise-wrapper .btn-submit:active {
                    transform: scale(0.98);
                }

                .reprise-wrapper .success-view {
                    display: none;
                    flex-direction: column;
                    align-items: center;
                    text-align: center;
                    padding: 2.5rem 0;
                }

                .reprise-wrapper .success-view.visible {
                    display: flex;
                }

                .reprise-wrapper .success-title {
                    font-size: 2rem;
                    font-weight: 900;
                    color: var(--slate-900);
                    text-transform: uppercase;
                    letter-spacing: -0.05em;
                    margin: 0 0 1rem 0;
                    line-height: 1;
                }

                .reprise-wrapper .success-desc {
                    color: var(--slate-500);
                    margin: 0 0 2rem 0;
                    font-size: 1.125rem;
                }

                .reprise-wrapper .btn-secondary {
                    padding: 1rem 3rem;
                    background-color: #f1f5f9;
                    /* slate-100 */
                    color: var(--slate-900);
                    border-radius: 0.75rem;
                    font-weight: 800;
                    font-size: 0.75rem;
                    text-transform: uppercase;
                    text-decoration: none;
                    display: inline-block;
                    transition: all 0.2s;
                }

                .reprise-wrapper .btn-secondary:hover {
                    background-color: #e2e8f0;
                    /* slate-200 */
                }

                /* Error message styling */
                .reprise-wrapper .error-msg {
                    color: #ef4444;
                    /* red-500 */
                    font-size: 0.875rem;
                    font-weight: 600;
                    margin-top: 0.5rem;
                    display: none;
                    padding: 0.75rem;
                    background-color: #fef2f2;
                    border-radius: 0.5rem;
                    border: 1px solid #fee2e2;
                }

                .reprise-wrapper .reprise-btn {
                    display: inline-block;
                    color: var(--slate-500);
                    font-weight: 700;
                    text-decoration: none;
                    font-size: 0.75rem;
                    letter-spacing: 0.05em;
                    margin-top: 1.5rem;
                    transition: color 0.2s;
                }

                .reprise-wrapper .reprise-btn:hover {
                    color: var(--primary);
                }
            </style>

            <div class="reprise-wrapper">
                <!-- Hero Section -->
                <section class="hero">
                    <div class="hero-bg"></div>
                    <div class="container-custom relative z-10 w-full">
                        <div class="hero-grid">
                            <div>
                                <div class="badge">
                                    <i data-lucide="hand-coins" width="14" height="14"></i> {{ __('messages.reprise.badge') }}
                                </div>
                                <h1>
                                    {{ __('messages.reprise.h1_1') }}<br />
                                    <span class="text-gradient">{{ __('messages.reprise.h1_grad') }}</span>
                                </h1>
                                <p>
                                    {!! __('messages.reprise.desc') !!}
                                </p>
                                <button class="btn-primary" onclick="openRepriseModal()">
                                    {{ __('messages.reprise.cta') }} <i data-lucide="arrow-right" width="20"
                                        height="20"></i>
                                </button>
                            </div>

                            <div class="hero-image-wrapper" style="position: relative;">
                                <div class="hero-card">
                                    <img src="https://images.unsplash.com/photo-1549317661-bd32c8ce0db2?auto=format&fit=crop&q=80&w=1200"
                                        alt="Premium Car" class="hero-img">
                                    <div class="hero-card-content">
                                        <div>
                                            <p class="hero-card-title">{{ __('messages.reprise.card_title') }}</p>
                                        </div>
                                        <div class="icon-box">
                                            <i data-lucide="zap" width="32" height="32"></i>
                                        </div>
                                    </div>
                                </div>
                                <!-- Floating stat card -->
                                <div class="hero-float-card">
                                    <div class="hero-float-icon">
                                        <i data-lucide="trending-up" width="22" height="22"></i>
                                    </div>
                                    <div>
                                        <div class="hero-float-num">94%</div>
                                        <div class="hero-float-label">{{ __('messages.reprise.float_label') }}</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Stats Strip -->
                <section class="stats-strip">
                    <div class="container-custom">
                        <div class="stats-strip-grid">
                            <div>
                                <span class="stat-item-num">+500</span>
                                <span class="stat-item-label">{{ __('messages.reprise.stat1_label') }}</span>
                            </div>
                            <div>
                                <span class="stat-item-num">48h</span>
                                <span class="stat-item-label">{{ __('messages.reprise.stat2_label') }}</span>
                            </div>
                            <div>
                                <span class="stat-item-num">94%</span>
                                <span class="stat-item-label">{{ __('messages.reprise.stat3_label') }}</span>
                            </div>
                            <div>
                                <span class="stat-item-num">100%</span>
                                <span class="stat-item-label">{{ __('messages.reprise.stat4_label') }}</span>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Process Section -->
                <section class="process-section">
                    <div class="container-custom">
                        <div class="section-header">
                            <span class="subtitle">{{ __('messages.reprise.process_subtitle') }}</span>
                            <h2 class="section-title">
                                {!! __('messages.reprise.process_title') !!}
                            </h2>
                        </div>
                        <div class="steps-grid">
                            <!-- Step 1 -->
                            <div class="step-card">
                                <span class="step-number">01</span>
                                <div class="step-icon">
                                    <i data-lucide="search" width="32" height="32"></i>
                                </div>
                                <h3 class="step-title">{{ __('messages.reprise.step1_t') }}</h3>
                                <p class="step-desc">{{ __('messages.reprise.step1_d') }}</p>
                            </div>
                            <!-- Step 2 -->
                            <div class="step-card">
                                <span class="step-number">02</span>
                                <div class="step-icon">
                                    <i data-lucide="shield-check" width="32" height="32"></i>
                                </div>
                                <h3 class="step-title">{{ __('messages.reprise.step2_t') }}</h3>
                                <p class="step-desc">{{ __('messages.reprise.step2_d') }}</p>
                            </div>
                            <!-- Step 3 -->
                            <div class="step-card">
                                <span class="step-number">03</span>
                                <div class="step-icon">
                                    <i data-lucide="check-square" width="32" height="32"></i>
                                </div>
                                <h3 class="step-title">{{ __('messages.reprise.step3_t') }}</h3>
                                <p class="step-desc">{{ __('messages.reprise.step3_d') }}</p>
                            </div>
                            <!-- Step 4 -->
                            <div class="step-card">
                                <span class="step-number">04</span>
                                <div class="step-icon">
                                    <i data-lucide="truck" width="32" height="32"></i>
                                </div>
                                <h3 class="step-title">{{ __('messages.reprise.step4_t') }}</h3>
                                <p class="step-desc">{{ __('messages.reprise.step4_d') }}</p>
                            </div>
                        </div>
                    </div>
                </section>

                <!-- Modal Form -->
                <div id="repriseModal" class="modal-overlay">
                    <div class="modal-content">
                        <button class="modal-close" onclick="closeRepriseModal()">
                            <i data-lucide="x" width="32" height="32"></i>
                        </button>
                        <div class="modal-left">
                            <div>
                                <div class="modal-left-badge">
                                    <i data-lucide="hand-coins" width="12" height="12"></i>
                                    {{ __('messages.reprise.modal_badge') }}
                                </div>
                                <h3>{!! __('messages.reprise.modal_title') !!}</h3>
                                <p>{{ __('messages.reprise.modal_desc') }}</p>
                            </div>
                            <div class="modal-left-trust">
                                <div class="modal-left-trust-item">
                                    <span class="modal-left-trust-dot"></span>
                                    {{ __('messages.reprise.trust1') }}
                                </div>
                                <div class="modal-left-trust-item">
                                    <span class="modal-left-trust-dot"></span>
                                    {{ __('messages.reprise.trust2') }}
                                </div>
                                <div class="modal-left-trust-item">
                                    <span class="modal-left-trust-dot"></span>
                                    {{ __('messages.reprise.trust3') }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-right">
                            <!-- Form View -->
                            <div id="repriseFormView" class="form-group">
                                <form id="repriseForm" action="{{ route('reprise.store') }}" method="POST" enctype="multipart/form-data"
                                    onsubmit="submitRepriseForm(event)">
                                    @csrf
                                    <div class="form-grid">
                                        <!-- Name + Phone: pris sur le compte client connecté, non modifiables -->
                                        <input type="hidden" name="name" id="name" value="{{ $client?->name }}">
                                        <input type="hidden" name="phone" id="phone" value="{{ $client?->phone }}">

                                        <!-- Brand -->
                                        <div>
                                            <label class="form-label">{{ __('messages.reprise.f_brand') }}</label>
                                            <select class="form-input" name="mark" id="mark" required>
                                                <option value="" disabled selected>{{ __('messages.reprise.f_brand') }}</option>
                                                @foreach ($brands as $brand)
                                                    <option value="{{ $brand->name }}"
                                                        data-id="{{ $brand->id }}">
                                                        {{ $brand->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <!-- Model -->
                                        <div>
                                            <label class="form-label">{{ __('messages.reprise.f_model') }}</label>
                                            <select class="form-input" name="modele" id="modele" required
                                                disabled>
                                                <option value="" disabled selected>{{ __('messages.reprise.f_model') }}</option>
                                            </select>
                                        </div>

                                        <!-- Year -->
                                        <div>
                                            <label class="form-label">{{ __('messages.reprise.f_year') }}</label>
                                            <select class="form-input" name="year" id="year" required>
                                                <option value="" disabled selected>{{ __('messages.reprise.f_year') }}</option>
                                                @for ($i = date('Y'); $i >= 1992; $i--)
                                                    <option title="{{ $i }}" value="{{ $i }}">
                                                        {{ $i }}
                                                    </option>
                                                @endfor
                                            </select>
                                        </div>

                                        <!-- Kilometrage -->
                                        <div>
                                            <label class="form-label">{{ __('messages.reprise.f_km') }}</label>
                                            <input type="number" class="form-input" name="km" id="km"
                                                placeholder="{{ __('messages.reprise.km_ph') }}" min="0" step="1" required>
                                        </div>

                                        <!-- Photos du vehicule : 4 zones de depot -->
                                        <div class="form-col-full">
                                            <label class="form-label">{{ __('messages.reprise.f_photos') }}</label>
                                            <div class="rp-dropzones">
                                                @foreach ([
                                                    'gauche'  => [__('messages.reprise.ph_left'), __('messages.reprise.ph_left_h')],
                                                    'droite'  => [__('messages.reprise.ph_right'), __('messages.reprise.ph_right_h')],
                                                    'avant'   => [__('messages.reprise.ph_front'), __('messages.reprise.ph_front_h')],
                                                    'arriere' => [__('messages.reprise.ph_rear'), __('messages.reprise.ph_rear_h')],
                                                ] as $slot => $meta)
                                                    <label class="rp-dz" for="photo-{{ $slot }}"
                                                        data-slot="{{ $slot }}">
                                                        <input type="file" id="photo-{{ $slot }}"
                                                            name="photos[{{ $slot }}]" accept="image/*"
                                                            class="rp-dz-input">
                                                        <img class="rp-dz-preview" alt="">
                                                        <button type="button" class="rp-dz-remove"
                                                            aria-label="Retirer la photo">&times;</button>
                                                        <span class="rp-dz-body">
                                                            <i data-lucide="camera" class="rp-dz-icon"></i>
                                                            <span class="rp-dz-title">{{ $meta[0] }}</span>
                                                            <span class="rp-dz-hint">{{ $meta[1] }}</span>
                                                        </span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            <small class="rp-dz-note">
                                                {{ __('messages.reprise.dz_note') }}
                                            </small>
                                        </div>

                                        <!-- Description -->
                                        <div class="form-col-full">
                                            <label class="form-label">{{ __('messages.reprise.f_desc') }}</label>
                                            <textarea class="form-input" name="description" id="description" rows="4"
                                                style="resize:vertical;"
                                                placeholder="{{ __('messages.reprise.desc_ph') }}"></textarea>
                                        </div>

                                        <!-- City -->
                                        <div>
                                            <label class="form-label">{{ __('messages.reprise.f_city') }}</label>
                                            <select class="form-input" name="city" id="city" required>
                                                <option value="" disabled selected>{{ __('messages.reprise.f_city') }}</option>
                                                @foreach ($city as $c)
                                                    <option value="{{ $c->name }}">{{ $c->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>


                                        <p id="repriseError" class="error-msg form-col-full"></p>

                                        <div class="form-col-full" style="margin-top: 1rem;">
                                            <button type="submit" class="btn-submit">{{ __('messages.reprise.submit') }}</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <!-- Success View -->
                            <div id="repriseSuccessView" class="success-view">
                                <h3 class="success-title">{{ __('messages.reprise.success_title') }}</h3>
                                <p class="success-desc">{{ __('messages.reprise.success_desc') }}
                                </p>
                                <button onclick="closeRepriseModal()" class="btn-secondary">{{ __('messages.reprise.back') }}</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Content END-->
            @include('layouts.bottom-page')
        </div>
        @include('layouts.footer')
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <!-- Bootstrap JS bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <script>
        // Initialize Icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        const modal = document.getElementById('repriseModal');
        const formView = document.getElementById('repriseFormView');
        const successView = document.getElementById('repriseSuccessView');
        const errorMsg = document.getElementById('repriseError');

        // Connexion exigée au clic sur le bouton de reprise, pas à l'ouverture de la page.
        const REPRISE_LOGGED_IN = @json(Auth::guard('client')->check());
        const REPRISE_LOGIN_URL = @json(route('client.login') . '?redirect=' . urlencode(url()->current()));

        // ---- Zones de depot photos : drag & drop, apercu, suppression ----
        (function initRepriseDropzones() {
            var MAX_BYTES = 5 * 1024 * 1024; // 5 Mo
            var LABELS = {
                gauche: 'Côté gauche',
                droite: 'Côté droit',
                avant: 'Avant',
                arriere: 'Arrière'
            };

            document.querySelectorAll('.rp-dz').forEach(function (zone) {
                var input   = zone.querySelector('.rp-dz-input');
                var preview = zone.querySelector('.rp-dz-preview');
                var remove  = zone.querySelector('.rp-dz-remove');
                if (!input || !preview) return;

                zone.setAttribute('data-slot-label', LABELS[zone.dataset.slot] || '');

                function showFile(file) {
                    zone.classList.remove('rp-dz-error');

                    if (!file.type || file.type.indexOf('image/') !== 0) {
                        zone.classList.add('rp-dz-error');
                        input.value = '';
                        return;
                    }
                    if (file.size > MAX_BYTES) {
                        zone.classList.add('rp-dz-error');
                        input.value = '';
                        alert('« ' + file.name +' » dépasse 5 Mo.');
                        return;
                    }

                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.src = e.target.result;
                        zone.classList.add('has-file');
                    };
                    reader.readAsDataURL(file);
                }

                input.addEventListener('change', function () {
                    if (input.files && input.files[0]) showFile(input.files[0]);
                });

                ['dragenter', 'dragover'].forEach(function (ev) {
                    zone.addEventListener(ev, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.add('is-dragover');
                    });
                });

                ['dragleave', 'drop'].forEach(function (ev) {
                    zone.addEventListener(ev, function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        zone.classList.remove('is-dragover');
                    });
                });

                zone.addEventListener('drop', function (e) {
                    var files = e.dataTransfer && e.dataTransfer.files;
                    if (!files || !files.length) return;
                    // DataTransfer permet d'alimenter l'input : le fichier part avec le FormData.
                    try {
                        var dt = new DataTransfer();
                        dt.items.add(files[0]);
                        input.files = dt.files;
                    } catch (err) {
                        return; // navigateur trop ancien : le clic reste disponible
                    }
                    showFile(files[0]);
                });

                if (remove) {
                    remove.addEventListener('click', function (e) {
                        e.preventDefault();
                        e.stopPropagation();
                        input.value = '';
                        preview.removeAttribute('src');
                        zone.classList.remove('has-file', 'rp-dz-error');
                    });
                }
            });
        })();

        // Le champ Kilométrage ne doit pas changer de valeur à la molette.
        document.querySelectorAll('.reprise-wrapper input[type="number"]').forEach(function (el) {
            el.addEventListener('wheel', function (e) {
                if (document.activeElement === el) e.preventDefault();
            }, { passive: false });
        });

        function openRepriseModal() {
            if (!REPRISE_LOGGED_IN) {
                window.location.href = REPRISE_LOGIN_URL;
                return;
            }
            if (modal) {
                modal.classList.add('open');
                formView.style.display = 'block';
                successView.classList.remove('visible');
                errorMsg.style.display = 'none';
            }
        }

        function closeRepriseModal() {
            if (modal) modal.classList.remove('open');
        }

        function submitRepriseForm(event) {
            event.preventDefault(); // Prevent default form submission

            const form = document.getElementById('repriseForm');
            const formData = new FormData(form);

            // Submit via AJAX
            fetch(form.action, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('input[name="_token"]')?.value || document.querySelector(
                            'meta[name="csrf-token"]')?.content
                    }
                })
                .then(response => {
                    if (response.ok) {
                        // Success
                        formView.style.display = 'none';
                        successView.classList.add('visible');
                        form.reset();
                    } else {
                        // Error
                        return response.json().then(data => {
                            throw new Error(data.message || 'Une erreur est survenue.');
                        });
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    errorMsg.textContent = "Erreur: " + error.message;
                    errorMsg.style.display = 'block';
                });
        }

        // Close on click outside
        window.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeRepriseModal();
            }
        });

        // AJAX for Model Selection (Adapted from user snippet)
        $(document).ready(function() {
            $('#mark').on('change', function() {
                // Determine source: standard view uses data-id, some setups use value
                // In the user's snippet: <option value="{{ $brand->name }}" data-id="{{ $brand->id }}">
                let brandId = $(this).find(':selected').data('id');
                let $modele = $('#modele');

                // Disable and show loading
                $modele.prop('disabled', true).html('<option value="">' + @json(__('messages.reprise.js_loading')) + '</option>');

                if (brandId) {
                    $.ajax({
                        url: "{{ route('get.models') }}",
                        type: "GET",
                        data: {
                            brand_id: brandId
                        },
                        success: function(data) {
                            $modele.empty().append(
                                '<option value="">' + @json(__('messages.reprise.js_select_model')) + '</option>');
                            $.each(data, function(key, value) {
                                $modele.append('<option value="' + value.name + '">' +
                                    value.name + '</option>');
                            });
                            $modele.prop('disabled', false);
                        },
                        error: function() {
                            $modele.empty().append(
                                '<option value="">' + @json(__('messages.reprise.js_load_error')) + '</option>');
                            $modele.prop('disabled', false);
                        }
                    });
                } else {
                    $modele.empty().append('<option value="">' + @json(__('messages.reprise.js_select_brand')) + '</option>');
                    $modele.prop('disabled', true);
                }
            });
        });
    </script>
</body>

</html>
