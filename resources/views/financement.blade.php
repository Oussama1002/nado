<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NADO Connect - Financez votre voiture</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @include('partials.locale-head')

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: #ffffff;
            color: #0f172a;
            min-height: 100vh;
            padding-bottom: 5rem;
            text-align: left;
        }

        /* Header */
        .header {
            background-color: #ffffff;
            border-bottom: 1px solid #f1f5f9;
            padding: 1rem 0;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
        }

        .header-container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .logo-accent {
            color: #1459DD;
        }

        .nav-links {
            display: flex;
            gap: 2rem;
            list-style: none;
            align-items: center;
        }

        .nav-links a {
            color: #64748b;
            text-decoration: none;
            font-weight: 600;
            font-size: 0.875rem;
            transition: color 0.2s;
        }

        .nav-links a:hover {
            color: #1459DD;
        }

        .btn-header {
            padding: 0.75rem 1.5rem;
            background-color: #1459DD;
            color: white;
            border-radius: 0.5rem;
            font-weight: 700;
            font-size: 0.875rem;
            text-decoration: none;
            transition: all 0.2s;
        }

        .btn-header:hover {
            background-color: #1d4ed8;
            transform: translateY(-2px);
        }

        /* Mobile Menu */
        .mobile-menu-btn {
            display: none;
            background: none;
            border: none;
            color: #0f172a;
            font-size: 1.5rem;
            cursor: pointer;
        }

        @media (max-width: 768px) {
            .nav-links {
                display: none;
            }

            .mobile-menu-btn {
                display: block;
            }
        }

        /* Hero Section */
        .hero {
            background-color: #0f172a;
            padding: 8rem 0 8rem;
            /* Adjusted padding-top for fixed header */
            position: relative;
            overflow: hidden;
            margin-top: 4rem;
            /* Added margin for header spacing */
        }

        .hero::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 50%;
            height: 100%;
            background-color: rgba(20, 89, 221, 0.05);
            transform: skewX(-15deg) translateX(8rem);
        }

        .container {
            max-width: 1280px;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }

        .hero-content {
            max-width: 48rem;
        }

        .badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.5rem 1rem;
            background-color: rgba(59, 130, 246, 0.1);
            border: 1px solid rgba(59, 130, 246, 0.2);
            color: #60a5fa;
            border-radius: 9999px;
            font-weight: 900;
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            margin-bottom: 2rem;
        }

        .hero h1 {
            font-size: clamp(3rem, 8vw, 6rem);
            font-weight: 900;
            color: white;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            line-height: 0.9;
            margin-bottom: 2rem;
        }

        .hero-title-blue {
            color: #1459DD;
        }

        .hero-title-yellow {
            color: #FDD817;
        }

        .hero p {
            font-size: 1.25rem;
            color: #94a3b8;
            font-weight: 500;
            line-height: 1.75;
            border-left: 4px solid #1459DD;
            padding-left: 2rem;
        }

        /* Simulateur */
        .simulator {
            max-width: 1280px;
            margin: -4rem auto 6rem;
            padding: 0 1.5rem;
            position: relative;
            z-index: 20;
        }

        .simulator-card {
            background: white;
            border-radius: 3.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #f1f5f9;
            overflow: hidden;
        }

        .simulator-grid {
            display: grid;
            grid-template-columns: 1fr;
        }

        @media (min-width: 1024px) {
            .simulator-grid {
                grid-template-columns: 7fr 5fr;
            }
        }

        .simulator-controls {
            padding: 4rem;
            border-right: 1px solid #f8fafc;
        }

        .section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .icon-box {
            width: 3rem;
            height: 3rem;
            background-color: #eff6ff;
            color: #1459DD;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .section-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.025em;
        }

        .form-group {
            margin-bottom: 3rem;
        }

        .label {
            font-size: 0.75rem;
            font-weight: 900;
            color: #94a3b8;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            display: block;
            margin-bottom: 1.5rem;
        }

        .profile-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .profile-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .profile-btn {
            padding: 1rem 0.75rem;
            border-radius: 0.75rem;
            font-weight: 900;
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            border: 2px solid #f1f5f9;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
        }

        .profile-btn:hover {
            border-color: #e2e8f0;
        }

        .profile-btn.active {
            border-color: #1459DD;
            background-color: #eff6ff;
            color: #1459DD;
        }

        .slider-group {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
        }

        .slider-value {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
        }

        input[type="range"] {
            width: 100%;
            height: 0.375rem;
            background: #f1f5f9;
            border-radius: 0.5rem;
            outline: none;
            -webkit-appearance: none;
            cursor: pointer;
        }

        input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: #1459DD;
            cursor: pointer;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        input[type="range"]::-moz-range-thumb {
            height: 24px;
            width: 24px;
            border-radius: 50%;
            background: #1459DD;
            cursor: pointer;
            border: 4px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .duration-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 0.75rem;
        }

        @media (min-width: 768px) {
            .duration-grid {
                grid-template-columns: repeat(7, 1fr);
            }
        }

        .duration-btn {
            padding: 0.75rem;
            border-radius: 0.75rem;
            font-weight: 900;
            font-size: 0.625rem;
            border: 2px solid #f8fafc;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
        }

        .duration-btn:hover {
            border-color: #e2e8f0;
        }

        .duration-btn.active {
            border-color: #1459DD;
            background-color: #eff6ff;
            color: #1459DD;
        }

        .simulator-result {
            background-color: #f8fafc;
            padding: 4rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        .result-label {
            font-size: 0.625rem;
            font-weight: 900;
            color: #1459DD;
            text-transform: uppercase;
            letter-spacing: 0.3em;
            margin-bottom: 1rem;
        }

        .result-amount {
            font-size: 4.5rem;
            font-weight: 900;
            color: #0f172a;
            letter-spacing: -0.05em;
            margin-bottom: 1rem;
        }

        .result-currency {
            font-size: 1.5rem;
            font-weight: 700;
        }

        .result-info {
            color: #94a3b8;
            font-size: 0.75rem;
            font-weight: 500;
            max-width: 280px;
            line-height: 1.75;
            margin-bottom: 2.5rem;
        }

        .btn-primary {
            width: 100%;
            padding: 1.5rem;
            background-color: #1459DD;
            color: white;
            border-radius: 2rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 1rem;
            transition: all 0.2s;
            box-shadow: 0 25px 50px -12px rgba(20, 89, 221, 0.2);
        }

        .btn-primary:hover {
            background-color: #1d4ed8;
        }

        .btn-primary:active {
            transform: scale(0.95);
        }

        .security-badge {
            margin-top: 2rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: #94a3b8;
            font-size: 0.625rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        /* Documents Section */
        .documents-section {
            padding: 6rem 0;
            background-color: #f8fafc;
            border-top: 1px solid #f1f5f9;
            border-bottom: 1px solid #f1f5f9;
        }

        .documents-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
            align-items: start;
        }

        @media (min-width: 1024px) {
            .documents-grid {
                grid-template-columns: 4fr 8fr;
            }
        }

        .profile-list {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
        }

        .profile-item {
            width: 100%;
            text-align: left;
            padding: 1.5rem;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: space-between;
            border: 2px solid transparent;
            background: transparent;
            color: #94a3b8;
            cursor: pointer;
            transition: all 0.2s;
        }

        .profile-item:hover {
            color: #64748b;
        }

        .profile-item.active {
            border-color: #1459DD;
            background-color: white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            color: #0f172a;
        }

        .profile-name {
            font-size: 0.875rem;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .documents-card {
            background: white;
            border-radius: 2.5rem;
            padding: 4rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid #f1f5f9;
        }

        .docs-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .docs-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .doc-item {
            display: flex;
            align-items: start;
            gap: 1rem;
            padding: 1.25rem;
            background-color: #f8fafc;
            border-radius: 1rem;
            border: 1px solid transparent;
            transition: all 0.2s;
        }

        .doc-item:hover {
            background-color: white;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            border-color: #dbeafe;
        }

        .check-icon {
            width: 1.5rem;
            height: 1.5rem;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .doc-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: #334155;
            line-height: 1.5;
        }

        .info-box {
            margin-top: 4rem;
            padding: 2rem;
            background-color: rgba(253, 216, 23, 0.1);
            border-radius: 1.5rem;
            border: 1px solid rgba(253, 216, 23, 0.2);
            display: flex;
            align-items: center;
            gap: 1.5rem;
        }

        @media (min-width: 768px) {
            .info-box {
                flex-direction: row;
            }
        }

        .info-icon {
            width: 3.5rem;
            height: 3.5rem;
            background-color: #FDD817;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #0f172a;
            flex-shrink: 0;
        }

        .info-text {
            font-size: 0.75rem;
            font-weight: 700;
            color: #64748b;
            line-height: 1.75;
            text-align: left;
        }

        /* Steps Section */
        .steps-section {
            padding: 6rem 0;
            background: white;
            text-align: center;
        }

        .main-title {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            margin-bottom: 5rem;
        }

        .steps-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
        }

        @media (min-width: 768px) {
            .steps-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .step-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .step-icon {
            width: 5rem;
            height: 5rem;
            background-color: #eff6ff;
            color: #1459DD;
            border-radius: 1.5rem;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 2rem;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .step-title {
            font-size: 1.125rem;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.025em;
            margin-bottom: 1rem;
        }

        .step-desc {
            color: #64748b;
            font-size: 0.875rem;
            font-weight: 500;
            line-height: 1.75;
        }

        /* Benefits Section */
        .benefits-section {
            padding: 6rem 0;
            background: white;
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
        }

        @media (min-width: 768px) {
            .benefits-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .benefit-item {
            display: flex;
            gap: 1.5rem;
        }

        .benefit-icon {
            width: 3.5rem;
            height: 3.5rem;
            background-color: #f8fafc;
            color: #1459DD;
            border-radius: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s;
            box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
        }

        .benefit-item:hover .benefit-icon {
            background-color: #1459DD;
            color: white;
        }

        .benefit-content {
            text-align: left;
        }

        .benefit-title {
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.025em;
            font-size: 1rem;
            margin-bottom: 0.5rem;
        }

        .benefit-desc {
            color: #64748b;
            font-size: 0.75rem;
            font-weight: 500;
            line-height: 1.75;
        }

        /* CTA Section */
        .cta-section {
            padding: 6rem 0;
            background-color: #0f172a;
            color: white;
            position: relative;
            overflow: hidden;
            text-align: center;
        }

        .cta-bg {
            position: absolute;
            inset: 0;
            opacity: 0.1;
            background-image: radial-gradient(#1459DD 1px, transparent 1px);
            background-size: 30px 30px;
        }

        .cta-content {
            max-width: 56rem;
            margin: 0 auto;
            padding: 0 1.5rem;
            position: relative;
            z-index: 10;
        }

        .cta-title {
            font-size: clamp(2.5rem, 7vw, 5.5rem);
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            margin-bottom: 2.5rem;
            line-height: 0.9;
        }

        .cta-desc {
            font-size: 1.25rem;
            color: #94a3b8;
            margin-bottom: 3rem;
        }

        .cta-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 1.5rem;
        }

        .btn-phone {
            padding: 1.25rem 3rem;
            background-color: #1459DD;
            color: white;
            border-radius: 1rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            border: none;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 1rem;
            transition: transform 0.2s;
            box-shadow: 0 25px 50px -12px rgba(20, 89, 221, 0.4);
        }

        .btn-phone:hover {
            transform: scale(1.05);
        }

        .btn-back {
            padding: 1.25rem 3rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            color: white;
            background: transparent;
            border-radius: 1rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            cursor: pointer;
            transition: all 0.2s;
        }

        .btn-back:hover {
            background-color: white;
            color: #0f172a;
        }

        /* Footer */
        footer {
            padding: 5rem 0;
            background: white;
            border-top: 1px solid #f1f5f9;
            text-align: center;
        }

        footer p {
            font-size: 0.625rem;
            font-weight: 900;
            color: #cbd5e1;
            text-transform: uppercase;
            letter-spacing: 0.4em;
        }

        .profile-info {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 1rem;
        }

        .info-hint {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.625rem;
            font-weight: 700;
            color: #94a3b8;
        }

        .profile-badge {
            margin-left: 0.5rem;
            display: inline-flex;
            align-items: center;
            padding: 0.25rem 0.75rem;
            border-radius: 9999px;
            background-color: #eff6ff;
            color: #1459DD;
            font-weight: 900;
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .docs-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            margin-bottom: 3rem;
        }

        .docs-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: -0.025em;
        }

        .section-intro {
            margin-bottom: 5rem;
            text-align: left;
        }

        .section-badge {
            font-size: 0.625rem;
            font-weight: 900;
            color: #1459DD;
            text-transform: uppercase;
            letter-spacing: 0.5em;
            margin-bottom: 1rem;
            display: block;
        }

        .section-subtitle {
            margin-top: 1.5rem;
            max-width: 36rem;
            font-size: 0.875rem;
            color: #64748b;
            font-weight: 500;
            line-height: 1.75;
        }

        /* SVG Icons */
        .icon {
            width: 1.75rem;
            height: 1.75rem;
            stroke: currentColor;
            stroke-width: 2;
            stroke-linecap: round;
            stroke-linejoin: round;
            fill: none;
        }

        .icon-sm {
            width: 0.875rem;
            height: 0.875rem;
        }
    </style>
</head>

<body>
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
                        <div class="user-dropdown-wrapper" id="userDropdownWrapper">
                            <button class="cta-button outline-btn" type="button" aria-label="Mon compte">
                                <i data-lucide="user"></i>
                                <span class="user-name" title="{{ $client->name }}">{{ $client->name }}</span>
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
                        <script>
                            // Tap / click toggles the account menu (mobile has no hover)
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
                        <a href="{{ route('nadoautoenchere.prelogin') }}" style="color: #1459DD;">{{ __('messages.nav.auction') }}</a>
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
                        <form method="POST" action="{{ route('logout') }}" style="margin:10px 0 0;">
                            @csrf
                            <button type="submit" class="drawer-logout"
                                style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 16px;border-radius:12px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;cursor:pointer;">
                                <i data-lucide="log-out" style="width:16px;height:16px;"></i> {{ __('messages.nav.logout') }}
                            </button>
                        </form>
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
                        <p>{{ __('messages.nav.support') }}</p>
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
                /* Never let the label wrap onto a second line. */
                white-space: nowrap;
                text-align: center;
                flex-shrink: 0;
            }
            .cta-button i { flex-shrink: 0; }

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

            /* Visible on every screen size (was hidden below 1024px, so a logged-in
               client lost "Mon espace" / "Déconnexion" on mobile) */
            .user-dropdown-wrapper {
                position: relative;
                display: block;
                flex-shrink: 0;
            }

            @media (max-width: 1023px) {
                /* compact: user icon + chevron, the name doesn't fit next to the logo */
                .user-dropdown-wrapper .user-name { display: none; }
                .user-dropdown-wrapper .cta-button { padding: 8px 10px; gap: 4px; }
            }

            /* Tap to open (hover doesn't exist on touch screens) — toggled by JS */
            .user-dropdown-wrapper.open .user-dropdown-menu {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                transition: opacity .15s ease, transform .15s ease, visibility 0s;
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
                overflow: hidden;
                /* Was display:none/block, which vanished the instant hover was lost. */
                opacity: 0;
                visibility: hidden;
                transform: translateY(-4px);
                transition: opacity .18s ease .4s, transform .18s ease .4s, visibility 0s linear .58s;
            }

            .user-dropdown-wrapper:hover .user-dropdown-menu,
            .user-dropdown-menu:hover {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                transition: opacity .15s ease, transform .15s ease, visibility 0s;
            }

            /* Invisible bridge over the 8px gap so moving down does not drop the hover. */
            .user-dropdown-wrapper::after {
                content: '';
                position: absolute;
                top: 100%;
                right: 0;
                width: 100%;
                height: 14px;
            }

            /* Long client names used to wrap and stretch the navbar. */
            .user-name {
                display: inline-block;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                vertical-align: middle;
            }

            @media (max-width: 1280px) {
                .user-name { max-width: 100px; }
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
                pointer-events: auto;
            }

            .mobile-drawer.visible .drawer-backdrop {
                opacity: 1;
            }

            .drawer-backdrop {
                position: absolute;
                inset: 0;
                background-color: rgba(15, 23, 42, 0.6);
                backdrop-filter: blur(8px);
                opacity: 0;
                transition: opacity 0.3s ease;
            }

            .visible .drawer-backdrop {
                opacity: 1;
            }

            .drawer-content {
                position: fixed;
                /* Changed from absolute */
                top: 0;
                right: 0;
                bottom: 0;
                /* Added */
                height: 100vh;
                /* Changed from 100% */
                width: 85%;
                max-width: 420px;
                background: white;
                box-shadow: -10px 0 50px rgba(0, 0, 0, 0.3);
                transform: translateX(100%);
                transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
                display: flex;
                flex-direction: column;
                overflow-y: auto;
            }

            .mobile-drawer.visible .drawer-content {
                transform: translateX(0);
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

            // Drawer Elements
            const mobileMenuBtn = document.getElementById('mobileMenuBtn');
            const closeDrawerBtn = document.getElementById('closeDrawerBtn');
            const mobileDrawer = document.getElementById('mobileDrawer');
            const drawerBackdrop = document.getElementById('drawerBackdrop');

            // Open Drawer
            function openDrawer() {
                if (mobileDrawer) {
                    mobileDrawer.classList.add('visible');
                    document.body.style.overflow = 'hidden';
                    console.log('Drawer opened');
                }
            }

            // Close Drawer
            function closeDrawer() {
                if (mobileDrawer) {
                    mobileDrawer.classList.remove('visible');
                    document.body.style.overflow = '';
                    console.log('Drawer closed');
                }
            }

            // Event Listeners
            if (mobileMenuBtn) {
                mobileMenuBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    openDrawer();
                });
            }

            if (closeDrawerBtn) {
                closeDrawerBtn.addEventListener('click', function(e) {
                    e.stopPropagation();
                    closeDrawer();
                });
            }

            if (drawerBackdrop) {
                drawerBackdrop.addEventListener('click', closeDrawer);
            }

            // Close drawer when clicking on a link (optional)
            const drawerLinks = document.querySelectorAll('.drawer-links a');
            drawerLinks.forEach(link => {
                link.addEventListener('click', closeDrawer);
            });

            // Close on ESC key
            document.addEventListener('keydown', function(e) {
                if (e.key === 'Escape' && mobileDrawer && mobileDrawer.classList.contains('visible')) {
                    closeDrawer();
                }
            });
        });
    </script>

    <!-- HERO SECTION -->
    <section class="hero">
        <div class="container">
            <div class="hero-content">
                <div class="badge">
                    <svg class="icon" style="width: 14px; height: 14px;" viewBox="0 0 24 24">
                        <path d="m3 21 1.9-5.7a8.5 8.5 0 1 1 3.8 3.8z" />
                    </svg>
                    {{ __('messages.financement.badge') }}
                </div>
                <h1>
                    {!! __('messages.financement.h1') !!}
                </h1>
                <p>
                    {{ __('messages.financement.desc') }}
                </p>
            </div>
        </div>
    </section>

    <!-- SIMULATEUR -->
    <section class="simulator" id="financement">
        <div class="simulator-card">
            <div class="simulator-grid">
                <!-- Controls Column -->
                <div class="simulator-controls">
                    <div class="section-header">
                        <div class="icon-box">
                            <svg class="icon" viewBox="0 0 24 24">
                                <path d="M3 3v18h18" />
                                <path d="m19 9-5 5-4-4-3 3" />
                            </svg>
                        </div>
                        <h2 class="section-title">{{ __('messages.financement.config_title') }}</h2>
                    </div>

                    <!-- Profile Selection -->
                    <div class="form-group">
                        <div class="profile-info">
                            <label class="label">{{ __('messages.financement.profile_type') }}</label>
                            <!-- <div class="info-hint">
                                <svg class="icon" style="width: 14px; height: 14px;" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" />
                                </svg>
                                Documents requis selon le profil
                            </div> -->
                        </div>
                        <div class="profile-grid" id="profileButtons">
                            @foreach ($clientTypes ?? [] as $type)
                                <button class="profile-btn"
                                    data-taux="{{ $type->taux_interet }}">{{ $type->name }}</button>
                            @endforeach
                        </div>
                    </div>

                    <!-- Vehicle Price -->
                    <div class="form-group">
                        <div class="slider-group">
                            <label class="label">{{ __('messages.financement.vehicle_value') }}</label>
                            <span class="slider-value" id="vehiclePriceValue">250000</span>
                        </div>
                        <input type="range" id="vehiclePrice" min="50000" max="1500000" step="10000"
                            value="250000">
                    </div>

                    <!-- Apport -->
                    <div class="form-group">
                        <div class="slider-group">
                            <label class="label">{{ __('messages.financement.apport') }}</label>
                            <span class="slider-value" id="apportValue">50000</span>
                        </div>
                        <input type="range" id="apport" min="0" max="1200000" step="5000"
                            value="50000">
                    </div>

                    <!-- Duration -->
                    <div class="form-group">
                        <label class="label">{{ __('messages.financement.duration') }}</label>
                        <div class="duration-grid" id="durationButtons"></div>
                    </div>
                </div>

                <!-- Result Column -->
                <div class="simulator-result">
                    <span class="result-label">{{ __('messages.financement.result_label') }}</span>
                    <div class="result-amount">
                        <span id="mensualiteValue">0</span> <span class="result-currency">MAD</span>
                    </div>
                    <p class="result-info">
                        {{ __('messages.financement.result_info') }}
                    </p>
                    <button class="btn-primary">
                        {{ __('messages.financement.cta_accord') }}
                        <svg class="icon" style="width: 20px; height: 20px;" viewBox="0 0 24 24">
                            <path d="M5 12h14" />
                            <path d="m12 5 7 7-7 7" />
                        </svg>
                    </button>
                    <!-- <div class="security-badge">
                        <svg class="icon" style="width: 16px; height: 16px;" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                        Paiement 100% Sécurisé
                    </div> -->
                </div>
            </div>
        </div>
    </section>

    <!-- DOCUMENTS SECTION -->
    <section class="documents-section">
        <div class="container">
            <div class="section-intro">
                <span class="section-badge">{{ __('messages.financement.doc_badge') }}</span>
                <h2 class="main-title">{!! __('messages.financement.doc_title') !!}</h2>
                <p class="section-subtitle">
                    {!! __('messages.financement.doc_subtitle') !!}
                    <span class="profile-badge" id="activeProfileBadge">Salarié(e) du privé</span>
                </p>
            </div>

            <div class="documents-grid">
                <!-- Profile List -->
                <div class="profile-list" id="profileList"></div>

                <!-- Documents Card -->
                <div>
                    <div class="documents-card">
                        <div class="docs-header">
                            <div class="icon-box">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <rect width="8" height="4" x="8" y="2" rx="1" ry="1" />
                                    <path
                                        d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2" />
                                    <path d="M9 12v-1h6v1" />
                                    <path d="M11 17h2" />
                                    <path d="M12 11v6" />
                                </svg>
                            </div>
                            <h3 class="docs-title">{{ __('messages.financement.profile_label') }} <span id="activeProfileTitle">Salarié(e) du privé</span>
                            </h3>
                        </div>
                        <div class="docs-grid" id="documentsList"></div>

                        <div class="info-box">
                            <div class="info-icon">
                                <svg class="icon" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" />
                                    <path d="M12 16v-4" />
                                    <path d="M12 8h.01" />
                                </svg>
                            </div>
                            <p class="info-text">
                                <span style="color: #0f172a; font-weight: 900;">{{ __('messages.financement.note_label') }}</span> {{ __('messages.financement.note_text') }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- STEPS SECTION -->
    <section class="steps-section" id="process">
        <div class="container">
            <h2 class="main-title">{!! __('messages.financement.journey_title') !!}</h2>
            <div class="steps-grid">
                <div class="step-item">
                    <div class="step-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <circle cx="11" cy="11" r="8" />
                            <path d="m21 21-4.3-4.3" />
                        </svg>
                    </div>
                    <h3 class="step-title">{{ __('messages.financement.step1_t') }}</h3>
                    <p class="step-desc">{{ __('messages.financement.step1_d') }}</p>
                </div>
                <div class="step-item">
                    <div class="step-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <rect width="14" height="20" x="5" y="2" rx="2" ry="2" />
                            <path d="M12 18h.01" />
                        </svg>
                    </div>
                    <h3 class="step-title">{{ __('messages.financement.step2_t') }}</h3>
                    <p class="step-desc">{{ __('messages.financement.step2_d') }}</p>
                </div>
                <div class="step-item">
                    <div class="step-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z" />
                            <polyline points="14 2 14 8 20 8" />
                        </svg>
                    </div>
                    <h3 class="step-title">{{ __('messages.financement.step3_t') }}</h3>
                    <p class="step-desc">{{ __('messages.financement.step3_d') }}</p>
                </div>
                <div class="step-item">
                    <div class="step-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14" />
                            <polyline points="22 4 12 14.01 9 11.01" />
                        </svg>
                    </div>
                    <h3 class="step-title">{{ __('messages.financement.step4_t') }}</h3>
                    <p class="step-desc">{{ __('messages.financement.step4_d') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- BENEFITS SECTION -->
    <section class="benefits-section">
        <div class="container">
            <div class="benefits-grid">
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M11 15h2a2 2 0 1 0 0-4h-3c-.6 0-1.1.2-1.4.6L3 17" />
                            <path
                                d="m7 21 1.6-1.4c.3-.4.8-.6 1.4-.6h4c1.1 0 2.1-.4 2.8-1.2l4.6-4.4a2 2 0 0 0-2.75-2.91l-4.2 3.9" />
                            <path d="m2 16 6 6" />
                            <circle cx="16" cy="9" r="2.9" />
                            <circle cx="6" cy="5" r="3" />
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h4 class="benefit-title">{{ __('messages.financement.b1_t') }}</h4>
                        <p class="benefit-desc">{{ __('messages.financement.b1_d') }}</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10" />
                            <polyline points="12 6 12 12 16 14" />
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h4 class="benefit-title">{{ __('messages.financement.b2_t') }}</h4>
                        <p class="benefit-desc">{{ __('messages.financement.b2_d') }}</p>
                    </div>
                </div>
                <div class="benefit-item">
                    <div class="benefit-icon">
                        <svg class="icon" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10" />
                            <path d="m9 12 2 2 4-4" />
                        </svg>
                    </div>
                    <div class="benefit-content">
                        <h4 class="benefit-title">{{ __('messages.financement.b3_t') }}</h4>
                        <p class="benefit-desc">{{ __('messages.financement.b3_d') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA SECTION -->
    <section class="cta-section" id="contact">
        <div class="cta-bg"></div>
        <div class="cta-content">
            <h2 class="cta-title">
                {!! __('messages.financement.cta_title') !!}
            </h2>
            <p class="cta-desc">{{ __('messages.financement.cta_desc') }}</p>
            <div class="cta-buttons">
                <button class="btn-phone">
                    <svg class="icon" style="width: 18px; height: 18px;" viewBox="0 0 24 24">
                        <path
                            d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                    </svg>
                    +212 520 070 611
                </button>
                <button class="btn-back" onclick="window.history.back()">
                    {{ __('messages.financement.back') }}
                </button>
            </div>
        </div>
    </section>

    <!-- FOOTER -->
    <footer>
        <p>NADO CONNECT • SOLUTIONS DE FINANCEMENT PREMIUM • 2025</p>
    </footer>

    <script>
        // Profiles data
        const profiles = [{
                name: 'Salarié(e) du privé',
                docs: [
                    "Copie de la Carte d'identité nationale",
                    "Spécimen de chèque ou Attestation de RIB",
                    "3 derniers bulletins de salaire",
                    "Attestation de salaire",
                    "Attestation de travail",
                    "Attestation CNSS",
                    "Justificatif d'adresse (moins de 3 mois)",
                    "3 derniers relevés bancaires"
                ]
            },
            {
                name: 'Fonctionnaire',
                docs: [
                    "Copie de la Carte d'identité nationale",
                    "Spécimen de chèque ou Attestation de RIB",
                    "Justificatif d'adresse (moins de 3 mois)",
                    "3 derniers relevés bancaires",
                    "État d'engagement récent ou attestation de salaire"
                ]
            },
            {
                name: 'Retraité(e)',
                docs: [
                    "Copie de la Carte d'identité nationale",
                    "Spécimen de chèque ou Attestation de RIB",
                    "Dernière attestation de pension",
                    "Justificatif d'adresse (moins de 3 mois)",
                    "3 derniers relevés bancaires"
                ]
            },
            {
                name: 'Profession libérale',
                docs: [
                    "Copie de la Carte d'identité nationale",
                    "Spécimen de chèque ou Attestation de RIB",
                    "Copie de la carte professionnelle",
                    "Justificatif d'adresse (moins de 3 mois)",
                    "3 derniers relevés bancaires"
                ]
            },
            {
                name: 'Commerçant',
                docs: [
                    "Copie de la Carte d'identité nationale",
                    "Spécimen de chèque ou Attestation de RIB",
                    "Registre du commerce (Modèle J)",
                    "Justificatif d'adresse (moins de 3 mois)",
                    "3 derniers relevés bancaires"
                ]
            },
            {
                name: 'Entreprise',
                docs: [
                    "Copie de la Carte d'identité nationale du gérant",
                    "Spécimen de chèque ou Attestation de RIB de l'entreprise",
                    "Statuts de l'entreprise",
                    "Justificatif d'adresse du/des gérant(s) (moins de 3 mois)",
                    "3 derniers relevés bancaires de l'entreprise",
                    "Registre du commerce (Modèle J) récent (moins de 3 mois)"
                ]
            },
            {
                name: 'Ancien client',
                docs: [
                    "Copie de la Carte d'identité nationale",
                    "Dernier relevé bancaire",
                    "Dernier bulletin de paie"
                ]
            }
        ];

        // State
        let vehiclePrice = 250000;
        let apport = 50000;
        let duration = 60;
        let activeProfile = 'Salarié(e) du privé';
        let tauxAnnuel = 0.075;

        // Initialize
        function init() {
            setupProfileButtons();
            createDurationButtons();
            createProfileList();
            updateDocuments();
            // set initial rate from active profile if possible, or default
            const initialBtn = document.querySelector('.profile-btn');
            if (initialBtn) {
                const t = parseFloat(initialBtn.dataset.taux);
                if (!isNaN(t)) tauxAnnuel = t / 100;
                activeProfile = initialBtn.textContent.trim();
                updateProfileButtons();
                updateDocuments();
            }
            updateCalculation();
            setupEventListeners();
        }

        function setupProfileButtons() {
            const buttons = document.querySelectorAll('#profileButtons .profile-btn');
            buttons.forEach(btn => {
                btn.onclick = () => {
                    const t = parseFloat(btn.dataset.taux);
                    if (!isNaN(t)) tauxAnnuel = t / 100;
                    setActiveProfile(btn.textContent.trim());
                    updateCalculation();
                };
            });
            updateProfileButtons();
        }

        function createDurationButtons() {
            const container = document.getElementById('durationButtons');
            [12, 24, 36, 48, 60, 72, 84].forEach(months => {
                const button = document.createElement('button');
                button.className = 'duration-btn';
                button.textContent = months + ' m';
                button.onclick = () => setDuration(months);
                container.appendChild(button);
            });
            updateDurationButtons();
        }

        function createProfileList() {
            const container = document.getElementById('profileList');
            profiles.forEach(profile => {
                const button = document.createElement('button');
                button.className = 'profile-item';
                button.innerHTML = `
                    <span class="profile-name">${profile.name}</span>
                    <svg class="icon" style="width: 18px; height: 18px;" viewBox="0 0 24 24">
                        <path d="m9 18 6-6-6-6"/>
                    </svg>
                `;
                button.onclick = () => {
                    // find matching button to get rate
                    const btns = Array.from(document.querySelectorAll('#profileButtons .profile-btn'));
                    const match = btns.find(b => b.textContent.trim() === profile.name);
                    if (match) {
                        const t = parseFloat(match.dataset.taux);
                        if (!isNaN(t)) tauxAnnuel = t / 100;
                    }
                    setActiveProfile(profile.name);
                    updateCalculation();
                };
                container.appendChild(button);
            });
            updateProfileList();
        }

        function setActiveProfile(profileName) {
            activeProfile = profileName;
            updateProfileButtons();
            updateProfileList();
            updateDocuments();
        }

        function setDuration(months) {
            duration = months;
            updateDurationButtons();
            updateCalculation();
        }

        function updateProfileButtons() {
            const buttons = document.querySelectorAll('#profileButtons .profile-btn');
            buttons.forEach(btn => {
                btn.classList.toggle('active', btn.textContent.trim() === activeProfile);
            });
        }

        function updateDurationButtons() {
            const buttons = document.querySelectorAll('.duration-btn');
            buttons.forEach(btn => {
                const months = parseInt(btn.textContent);
                btn.classList.toggle('active', months === duration);
            });
        }

        function updateProfileList() {
            const items = document.querySelectorAll('.profile-item');
            items.forEach(item => {
                const name = item.querySelector('.profile-name').textContent;
                item.classList.toggle('active', name === activeProfile);
            });
        }

        function updateDocuments() {
            const profile = profiles.find(p => p.name === activeProfile);
            if (!profile) return;

            document.getElementById('activeProfileTitle').textContent = activeProfile;
            document.getElementById('activeProfileBadge').textContent = activeProfile;

            const container = document.getElementById('documentsList');
            container.innerHTML = '';
            profile.docs.forEach(doc => {
                const div = document.createElement('div');
                div.className = 'doc-item';
                div.innerHTML = `
                    <div class="check-icon">
                        <svg style="width: 14px; height: 14px; stroke: #22c55e; stroke-width: 2; fill: none;" viewBox="0 0 24 24">
                            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/>
                            <polyline points="22 4 12 14.01 9 11.01"/>
                        </svg>
                    </div>
                    <span class="doc-text">${doc}</span>
                `;
                container.appendChild(div);
            });
        }

        function updateCalculation() {
            const capital = vehiclePrice - apport;
            // tauxAnnuel is global
            const tauxMensuel = tauxAnnuel / 12;
            const mensualite = (capital * tauxMensuel) / (1 - Math.pow(1 + tauxMensuel, -duration));

            document.getElementById('mensualiteValue').textContent = Math.round(mensualite).toLocaleString();
        }

        function setupEventListeners() {
            const vehiclePriceInput = document.getElementById('vehiclePrice');
            const apportInput = document.getElementById('apport');

            vehiclePriceInput.addEventListener('input', (e) => {
                vehiclePrice = Number(e.target.value);
                document.getElementById('vehiclePriceValue').textContent = vehiclePrice.toLocaleString();
                apportInput.max = vehiclePrice * 0.8;
                updateCalculation();
            });

            apportInput.addEventListener('input', (e) => {
                apport = Number(e.target.value);
                document.getElementById('apportValue').textContent = apport.toLocaleString();
                updateCalculation();
            });
        }

        // Start the application
        init();

        // Mobile Menu
        const mobileBtn = document.querySelector('.mobile-menu-btn');
        const navLinks = document.querySelector('.nav-links');

        if (mobileBtn) {
            mobileBtn.addEventListener('click', () => {
                if (navLinks.style.display === 'flex') {
                    navLinks.style.display = 'none';
                } else {
                    navLinks.style.display = 'flex';
                    navLinks.style.flexDirection = 'column';
                    navLinks.style.position = 'absolute';
                    navLinks.style.top = '100%';
                    navLinks.style.left = '0';
                    navLinks.style.right = '0';
                    navLinks.style.background = 'white';
                    navLinks.style.padding = '1rem';
                    navLinks.style.boxShadow = '0 4px 6px -1px rgba(0, 0, 0, 0.1)';
                }
            });
        }
    </script>
</body>

</html>
