@php
    // Resolve the brand/model names once for the whole page.
    // The relations come back null when the referenced row is missing, so fall back to
    // the raw columns. mark/modele hold ids on newer rows and plain names on migrated
    // ones, so a numeric value counts as "no usable name".
    $brandName = $annonce->brand?->name
        ?? (is_numeric($annonce->mark ?? null) ? null : $annonce->mark);
    $modelName = $annonce->modeleRelation?->name
        ?? (is_numeric($annonce->modele ?? null) ? null : $annonce->modele);

    $vehicleName = trim(($brandName ?? '') . ' ' . ($modelName ?? ''));
    if ($vehicleName === '') {
        $vehicleName = $annonce->titre ?: 'Véhicule';
    }
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>
        {{ $annonce->titre ?: $vehicleName }}
        | Nado.ma
    </title>

    {{-- Open Graph Meta Tags --}}
    <meta property="og:title" content="{{ $annonce->titre ?: $vehicleName }}">
    <meta property="og:description" content="{{ Str::limit(strip_tags($annonce->description), 120) }}">
    <meta property="og:image" content="{{ asset('storage/ads/' . ($annonce->images[0] ?? 'default.jpg')) }}">
    <meta property="og:url" content="{{ request()->fullUrl() }}">
    <meta property="og:type" content="website">

    {{-- Fonts & Icons --}}
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />

    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Arabic font + RTL base styles (adds dir="rtl" support in AR). --}}
    @include('partials.locale-head')
</head>
<style>
    :root {
        --bg-light: #f8fafc;
        --blue-primary: #1459DD;
        --slate-900: #0f172a;
        --active-color: #1459DD;
        --slate-700: #334155;
        --slate-500: #64748b;
        --slate-200: #e2e8f0;
        --slate-100: #f1f5f9;
        --slate-50: #f8fafc;
    }

    * {
        box-sizing: border-box;
        margin: 0;
        padding: 0;
    }

    body {
        font-family: 'Inter', sans-serif;
        background-color: var(--bg-light);
        color: var(--slate-900);
    }

    .container {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
    }

    /* Top Bar */
    .top-bar {
        background-color: #FDD817;
        color: var(--slate-900);
        padding: 0.625rem 1rem;
        border-bottom: 1px solid rgba(0, 0, 0, 0.05);
    }

    .top-bar-inner {
        max-width: 1280px;
        margin: 0 auto;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .top-bar-contact {
        display: flex;
        align-items: center;
        gap: 1.5rem;
    }

    .top-bar-item {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        opacity: 0.8;
    }

    .top-bar-item a {
        color: inherit;
        text-decoration: none;
        transition: color 0.2s;
    }

    .top-bar-item a:hover {
        color: var(--blue-primary);
    }

    .top-bar-item i {
        color: var(--blue-primary);
        font-size: 12px;
    }

    .top-bar-social {
        display: flex;
        align-items: center;
        gap: 1.25rem;
    }

    .top-bar-social a {
        color: var(--slate-900);
        opacity: 0.6;
        transition: all 0.2s;
        font-size: 1rem;
    }

    .top-bar-social a:hover {
        color: var(--blue-primary);
        opacity: 1;
    }

    @media (max-width: 640px) {
        .top-bar-item.hide-mobile {
            display: none;
        }
    }

    /* Header / Main Nav */
    .header {
        background-color: white;
        border-bottom: 1px solid var(--slate-200);
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .header-inner {
        max-width: 1280px;
        margin: 0 auto;
        padding: 0 1rem;
        height: 5rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .header-left {
        display: flex;
        align-items: center;
        gap: 2.5rem;
    }

    .logo-link {
        display: flex;
        align-items: center;
        text-decoration: none;
        transition: transform 0.2s;
    }

    .logo-link:hover {
        transform: scale(1.05);
    }

    .logo-text {
        font-size: 1.5rem;
        font-weight: 900;
        letter-spacing: -0.05em;
        text-transform: uppercase;
    }

    .logo-text .blue {
        color: var(--blue-primary);
    }

    .logo-text .yellow {
        color: #FDD817;
    }

    .logo-link img {
        height: 40px;
    }

    /* Desktop Navigation */
    .nav-menu {
        display: none;
        align-items: center;
        gap: 0.75rem;
    }

    @media (min-width: 1024px) {
        .nav-menu {
            display: flex;
        }
    }

    .nav-menu a {
        text-decoration: none;
        font-size: 11px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        color: var(--slate-600);
        padding: 0.5rem 1rem;
        border-radius: 0.75rem;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .nav-menu a:hover {
        color: var(--blue-primary);
        background: var(--slate-50);
    }

    .nav-menu a.highlighted {
        color: var(--blue-primary);
        background: #eff6ff;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.05);
    }

    .header-right {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .connect-btn {
        display: none;
        align-items: center;
        gap: 0.5rem;
        padding: 0.75rem 1.5rem;
        background: var(--blue-primary);
        color: white;
        border-radius: 0.75rem;
        text-decoration: none;
        font-size: 10px;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        transition: all 0.2s;
        box-shadow: 0 10px 15px -3px rgba(20, 89, 221, 0.1);
    }

    .connect-btn:hover {
        background: #0f4ac4;
    }

    .connect-btn:active {
        transform: scale(0.95);
    }

    @media (min-width: 640px) {
        .connect-btn {
            display: flex;
        }
    }

    .nav-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0.75rem;
        background: var(--slate-50);
        border: none;
        border-radius: 0.75rem;
        color: var(--slate-900);
        cursor: pointer;
        transition: background 0.2s;
    }

    .nav-toggle:hover {
        background: var(--slate-100);
    }

    .nav-toggle i {
        font-size: 1.5rem;
    }

    @media (min-width: 1024px) {
        .nav-toggle {
            display: none;
        }
    }

    /* Mobile Drawer */
    .mobile-drawer-overlay {
        position: fixed;
        inset: 0;
        z-index: 100;
        visibility: hidden;
        transition: visibility 0.5s;
    }

    .mobile-drawer-overlay.open {
        visibility: visible;
    }

    .mobile-drawer-backdrop {
        position: absolute;
        inset: 0;
        background: rgba(15, 23, 42, 0.4);
        backdrop-filter: blur(8px);
        opacity: 0;
        transition: opacity 0.5s;
    }

    .mobile-drawer-overlay.open .mobile-drawer-backdrop {
        opacity: 1;
    }

    .mobile-drawer {
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 85%;
        max-width: 420px;
        background: white;
        box-shadow: -10px 0 40px rgba(0, 0, 0, 0.15);
        transform: translateX(100%);
        transition: transform 0.5s ease;
        display: flex;
        flex-direction: column;
    }

    .mobile-drawer-overlay.open .mobile-drawer {
        transform: translateX(0);
    }

    .drawer-header {
        padding: 2rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .drawer-close {
        padding: 0.5rem;
        background: none;
        border: none;
        color: var(--slate-400);
        cursor: pointer;
        transition: color 0.2s;
        font-size: 2rem;
    }

    .drawer-close:hover {
        color: var(--slate-900);
    }

    .drawer-nav {
        flex: 1;
        overflow-y: auto;
        padding: 1rem 2rem;
        display: flex;
        flex-direction: column;
        gap: 1.5rem;
    }

    .drawer-nav a {
        text-decoration: none;
        font-size: 1.125rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -0.02em;
        color: var(--slate-800);
        transition: color 0.2s;
    }

    .drawer-nav a:hover,
    .drawer-nav a.highlighted {
        color: var(--blue-primary);
    }

    .drawer-footer {
        padding: 2.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .drawer-cta {
        width: 100%;
        padding: 1.5rem;
        background: #FDD817;
        color: var(--slate-900);
        border: none;
        border-radius: 2rem;
        font-size: 0.875rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.2em;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        text-decoration: none;
        box-shadow: 0 20px 40px -10px rgba(253, 216, 23, 0.4);
        margin-bottom: 3rem;
        transition: transform 0.2s;
    }

    .drawer-cta:active {
        transform: scale(0.95);
    }

    .drawer-social {
        display: flex;
        justify-content: center;
        gap: 2.5rem;
        margin-bottom: 3rem;
    }

    .drawer-social a {
        color: var(--slate-400);
        font-size: 1.25rem;
        transition: color 0.2s;
    }

    .drawer-social a:hover {
        color: var(--slate-900);
    }

    .drawer-divider {
        width: 100%;
        height: 1px;
        background: var(--slate-100);
        margin-bottom: 2.5rem;
    }

    .drawer-support {
        text-align: center;
    }

    .drawer-support-label {
        font-size: 11px;
        font-weight: 800;
        color: var(--slate-400);
        text-transform: uppercase;
        letter-spacing: 0.3em;
        margin-bottom: 0.75rem;
    }

    .drawer-support-phone {
        font-size: 1.25rem;
        font-weight: 900;
        color: var(--slate-900);
        text-decoration: none;
        transition: color 0.2s;
    }

    .drawer-support-phone:hover {
        color: var(--blue-primary);
    }

    @media (min-width: 1024px) {
        .mobile-drawer-overlay {
            display: none;
        }
    }

    /* Layout */
    .main-content {
        padding: 9rem 1rem 3rem;
    }

    .layout-grid {
        display: grid;
        grid-template-columns: 1fr;
        grid-template-areas:
            "top"
            "sidebar"
            "bottom";
        gap: 3rem;
    }

    @media (min-width: 1024px) {
        .layout-grid {
            grid-template-columns: minmax(0, 1fr) 420px;
            grid-template-areas:
                "top sidebar"
                "bottom sidebar";
            align-items: start;
        }
    }

    .content-top {
        grid-area: top;
        min-width: 0;
    }

    .content-bottom {
        grid-area: bottom;
        min-width: 0;
    }

    .sidebar {
        grid-area: sidebar;
        align-self: stretch;
        height: 100%;
    }

    /* Typography */
    .badge-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        flex-wrap: wrap;
    }

    .badge {
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.125rem 0.5rem;
        border-radius: 4px;
    }

    .id-badge {
        background: var(--slate-100);
        color: var(--slate-600);
    }

    .certified-badge {
        background: #eff6ff;
        color: var(--blue-primary);
        border: 1px solid #dbeafe;
    }

    .particulier-badge {
        background: #eff6ff;
        color: #1d4ed8;
        border: 1px solid #bfdbfe;
    }

    .pro-badge {
        background: #fffbeb;
        color: #b45309;
        border: 1px solid #fde68a;
    }

    .main-title {
        font-size: clamp(2rem, 6vw, 3rem);
        font-weight: 900;
        text-transform: uppercase;
        line-height: 1;
        letter-spacing: -0.02em;
    }

    .text-blue {
        color: var(--blue-primary);
    }

    .specs-subtitle {
        margin-top: 1rem;
        font-size: 1.125rem;
        color: var(--slate-500);
        font-weight: 500;
    }

    /* Gallery */
    .gallery-section {
        margin-top: 2rem;
    }

    .main-image-container {
        position: relative;
        border-radius: 1.5rem;
        overflow: hidden;
        background: var(--slate-100);
    }

    .main-image-container img {
        width: 100%;
        height: auto;
        max-height: 500px;
        object-fit: contain;
        cursor: zoom-in;
        transition: transform 0.3s;
    }

    .main-image-container img:hover {
        transform: scale(1.02);
    }

    .gallery-nav-btn {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: white;
        border: none;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
        cursor: pointer;
        font-size: 1rem;
        opacity: 0;
        transition: opacity 0.3s;
    }

    .main-image-container:hover .gallery-nav-btn {
        opacity: 1;
    }

    .image-counter {
        position: absolute;
        top: 1.5rem;
        right: 1.5rem;
        background: rgba(0, 0, 0, 0.5);
        backdrop-filter: blur(4px);
        color: white;
        padding: 0.375rem 1rem;
        border-radius: 99px;
        font-size: 10px;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        z-index: 10;
        pointer-events: none;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
    }

    .gallery-nav-btn:hover {
        background: var(--blue-primary);
        color: white;
    }

    .gallery-nav-btn.prev {
        left: 1rem;
    }

    .gallery-nav-btn.next {
        right: 1rem;
    }

    .thumbnails {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
        flex-wrap: wrap;
    }

    .thumb {
        width: 193px;
        height: 120px;
        object-fit: cover;
        border-radius: 1rem;
        cursor: pointer;
        border: 2px solid transparent;
        transition: all 0.3s;
        filter: none;
    }

    @media (max-width: 768px) {
        .thumb {
            width: calc(25% - 0.38rem);
            height: auto;
            aspect-ratio: 1.6;
        }
    }

    .thumb:hover,
    .thumb.active {
        border-color: var(--blue-primary);
    }

    .thumb-video {
        position: relative;
    }

    .thumb-video::after {
        content: '▶';
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        color: white;
        font-size: 1rem;
        text-shadow: 0 0 4px rgba(0, 0, 0, 0.8);
    }

    /* About Section */
    .about-section {
        margin-top: 3rem;
    }

    .section-title {
        font-size: 1.25rem;
        font-weight: 900;
        text-transform: uppercase;
        letter-spacing: -0.05em;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .blue-line {
        width: 2.5rem;
        height: 4px;
        background-color: var(--blue-primary);
        border-radius: 99px;
    }

    .description-card {
        background: white;
        padding: 2rem;
        border-radius: 1.5rem;
        border: 1px solid var(--slate-200);
    }

    .description-text {
        font-size: 1rem;
        color: var(--slate-700);
        line-height: 1.7;
    }

    .description-text .desc-hl {
        color: var(--blue-primary);
        font-weight: 700;
    }

    .tag-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
        margin-top: 1.5rem;
    }

    .info-tag {
        padding: 0.4rem 1rem;
        background: var(--slate-50);
        border: 1px solid var(--slate-100);
        border-radius: 99px;
        font-size: 10px;
        font-weight: 800;
        color: var(--slate-500);
        text-transform: uppercase;
    }

    /* Tabs */
    .tabs-container {
        background: white;
        border-radius: 1.5rem;
        border: 1px solid var(--slate-200);
        overflow: hidden;
        margin-top: 3rem;
    }

    .tabs-header {
        display: flex;
        overflow-x: auto;
        border-bottom: 1px solid var(--slate-200);
    }

    .tabs-header::-webkit-scrollbar {
        display: none;
    }

    .tab-btn {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        padding: 1rem 1.25rem;
        font-size: 0.875rem;
        font-weight: 600;
        color: var(--slate-500);
        background: transparent;
        border: none;
        border-bottom: 2px solid transparent;
        cursor: pointer;
        white-space: nowrap;
        transition: all 0.2s;
    }

    .tab-btn i {
        font-size: 0.85rem;
    }

    .tab-btn.active {
        color: var(--blue-primary);
        border-bottom-color: var(--blue-primary);
        background: rgba(239, 246, 255, 0.5);
    }

    .tabs-content {
        padding: 2rem;
    }

    .tab-pane {
        display: none;
    }

    .tab-pane.active {
        display: block;
    }

    .specs-grid {
        display: grid;
        grid-template-columns: 1fr;
        row-gap: 1.5rem;
    }

    @media (min-width: 768px) {
        .specs-grid {
            grid-template-columns: repeat(2, 1fr);
            column-gap: 3rem;
        }
    }

    .spec-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.5rem 0;
        border-bottom: 1px solid var(--slate-50);
        font-size: 0.875rem;
    }

    .spec-item span {
        color: var(--slate-500);
        font-weight: 500;
    }

    .spec-item strong {
        color: var(--slate-900);
        font-weight: 600;
    }

    .equipment-grid {
        display: grid;
        grid-template-columns: repeat(1, 1fr);
        gap: 1rem;
    }

    @media (min-width: 768px) {
        .equipment-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    .eq-item {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding: 0.75rem;
        background: var(--slate-50);
        border-radius: 0.75rem;
        font-size: 0.875rem;
        font-weight: 500;
        color: #334155;
    }

    .eq-dot {
        width: 0.5rem;
        height: 0.5rem;
        border-radius: 50%;
        background: var(--blue-primary);
        flex-shrink: 0;
    }

    .expertise-hero {
        background: #eff6ff;
        border: 1px solid #dbeafe;
        padding: 1.5rem;
        border-radius: 1rem;
        display: flex;
        gap: 1rem;
        margin-bottom: 1.5rem;
        align-items: flex-start;
        flex-wrap: wrap;
    }

    .expertise-icon-box {
        background: var(--blue-primary);
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        font-size: 1.25rem;
        color: white;
    }

    .expertise-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    @media (min-width: 768px) {
        .expertise-grid {
            grid-template-columns: repeat(4, 1fr);
        }
    }

    .status-card {
        text-align: center;
        padding: 1rem;
        border: 1px solid var(--slate-100);
        border-radius: 0.75rem;
        background: white;
    }

    .status-card span {
        font-size: 0.65rem;
        font-weight: 800;
        color: var(--slate-400);
        text-transform: uppercase;
    }

    .text-green {
        color: #16a34a;
    }

    .timeline {
        position: relative;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 17px;
        top: 1rem;
        bottom: 1rem;
        width: 1px;
        background: var(--slate-200);
    }

    .timeline-item {
        position: relative;
        display: flex;
        gap: 1.5rem;
        align-items: center;
        margin-bottom: 1rem;
        z-index: 10;
    }

    .timeline-dot {
        width: 2.25rem;
        height: 2.25rem;
        flex-shrink: 0;
        background: white;
        border: 2px solid #3b82f6;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .timeline-dot::after {
        content: '';
        width: 0.5rem;
        height: 0.5rem;
        background: #3b82f6;
        border-radius: 50%;
    }

    .warranty-card {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        max-width: 32rem;
        margin: 0 auto;
        padding: 1.5rem 0;
    }

    .shield-icon-circle {
        width: 5rem;
        height: 5rem;
        background: #eff6ff;
        color: var(--blue-primary);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1.5rem;
    }

    .shield-icon-circle i {
        font-size: 3rem;
    }

    .secondary-button {
        margin-top: 1rem;
        padding: 0.75rem 2rem;
        background: var(--blue-primary);
        color: white;
        border: none;
        border-radius: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        font-size: 0.875rem;
        transition: background 0.2s;
    }

    .secondary-button:hover {
        background: #1d4ed8;
    }

    /* Finance Simulator */
    .finance-card {
        background: white;
        border-radius: 1.5rem;
        border: 1px solid var(--slate-200);
        overflow: hidden;
        margin-top: 3rem;
    }

    .finance-header {
        padding: 2rem;
        border-bottom: 1px solid var(--slate-100);
    }

    .header-content {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .finance-icon {
        background: #eff6ff;
        width: 48px;
        height: 48px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 0.75rem;
        font-size: 1.25rem;
        color: var(--blue-primary);
    }

    .finance-title {
        font-size: 1.5rem;
        font-weight: 800;
        color: var(--slate-900);
    }

    .finance-subtitle {
        color: var(--slate-500);
        font-size: 0.8rem;
    }

    .finance-body {
        display: grid;
        grid-template-columns: 1fr;
    }

    @media (min-width: 1024px) {
        .finance-body {
            grid-template-columns: 1.2fr 0.8fr;
        }
    }

    .finance-inputs {
        padding: 2rem;
    }

    @media (min-width: 1024px) {
        .finance-inputs {
            border-right: 1px solid var(--slate-100);
        }
    }

    .finance-result {
        padding: 2rem;
        background: var(--slate-50);
        text-align: center;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }

    .input-group {
        margin-bottom: 2rem;
    }

    .input-group:last-child {
        margin-bottom: 0;
    }

    .label-row {
        display: flex;
        justify-content: space-between;
        margin-bottom: 0.75rem;
        align-items: center;
    }

    .label-row label {
        font-size: 0.7rem;
        font-weight: 800;
        color: var(--slate-500);
        text-transform: uppercase;
    }

    .val-highlight {
        color: var(--blue-primary);
        font-weight: 900;
        font-size: 1rem;
    }

    .custom-range {
        width: 100%;
        height: 6px;
        -webkit-appearance: none;
        appearance: none;
        background: var(--slate-200);
        border-radius: 99px;
        cursor: pointer;
        outline: none;
    }

    .custom-range::-webkit-slider-thumb {
        -webkit-appearance: none;
        appearance: none;
        width: 20px;
        height: 20px;
        background: var(--blue-primary);
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(20, 89, 221, 0.3);
        margin-top: -7px;
    }

    .custom-range::-moz-range-thumb {
        width: 20px;
        height: 20px;
        background: var(--blue-primary);
        border-radius: 50%;
        cursor: pointer;
        border: none;
        box-shadow: 0 2px 6px rgba(20, 89, 221, 0.3);
    }

    .custom-range::-webkit-slider-runnable-track {
        height: 6px;
        border-radius: 99px;
    }

    .custom-range::-moz-range-track {
        height: 6px;
        background: var(--slate-200);
        border-radius: 99px;
    }

    .duration-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.4rem;
    }

    .profile-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .duration-btn {
        padding: 0.5rem;
        border: 1px solid var(--slate-100);
        background: white;
        border-radius: 0.5rem;
        font-size: 0.875rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        color: #94a3b8;
    }

    .profile-btn {
        padding: 0.75rem 1rem;
        border: 1px solid var(--slate-100);
        background: white;
        border-radius: 0.75rem;
        font-size: 0.75rem;
        font-weight: 700;
        cursor: pointer;
        transition: all 0.2s;
        color: #94a3b8;
        text-transform: uppercase;
        letter-spacing: 0.03em;
        text-align: left;
    }

    .duration-btn.active {
        border-color: var(--blue-primary);
        background: #eff6ff;
        color: var(--blue-primary);
    }

    .profile-btn.active {
        border-color: var(--blue-primary);
        background: #eff6ff;
        color: var(--blue-primary);
    }

    .result-label {
        text-transform: uppercase;
        font-size: 0.75rem;
        font-weight: 700;
        color: #94a3b8;
        letter-spacing: 0.2em;
        margin-bottom: 0.75rem;
    }

    .result-amount {
        font-size: 3.75rem;
        font-weight: 900;
        color: var(--slate-900);
        margin: 0.5rem 0;
        display: flex;
        align-items: baseline;
        justify-content: center;
        gap: 0.5rem;
    }

    .result-amount small {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--slate-400);
    }

    .disclaimer {
        font-size: 0.75rem;
        color: var(--slate-400);
        max-width: 280px;
        margin-bottom: 2rem;
        line-height: 1.5;
        text-align: center;
    }

    .chat-btn {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        background: var(--blue-primary);
        color: white;
        padding: 1rem clamp(0.75rem, 2vw, 2rem);
        border-radius: 1rem;
        border: none;
        font-weight: 700;
        /* Keep the label on one line: scale it down rather than let it wrap.
           Caps at the original 1.125rem so the desktop look is unchanged. */
        font-size: clamp(0.8rem, 1.5vw, 1.125rem);
        white-space: nowrap;
        cursor: pointer;
        box-shadow: 0 20px 25px -5px rgba(20, 89, 221, 0.25);
        text-decoration: none;
        transition: all 0.2s;
    }

    .chat-btn i {
        flex-shrink: 0;
    }

    .chat-btn:hover {
        background: #1d4ed8;
    }

    .chat-btn:active {
        transform: scale(0.95);
    }

    /* Sidebar */
    .sidebar {
        width: 100%;
    }

    @media (min-width: 1024px) {
        .sidebar {
            width: 400px;
            flex-shrink: 0;
        }
    }

    .sticky-content {
        position: sticky;
        top: 5rem;
        align-self: start;
    }

    .pricing-card {
        background: white;
        border-radius: 1.5rem;
        border: 1px solid var(--slate-200);
        box-shadow: 0 20px 40px -12px rgba(0, 0, 0, 0.12);
        overflow: hidden;
        margin-bottom: 1.5rem;
    }

    .p-6 {
        padding: 1.5rem;
    }

    .price-row {
        display: flex;
        align-items: baseline;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .price-amount {
        font-size: 3rem;
        font-weight: 900;
        color: var(--blue-primary);
        letter-spacing: -0.02em;
    }

    .price-currency {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--slate-400);
    }

    .finance-badge {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.5rem 1rem;
        background: #eff6ff;
        color: var(--blue-primary);
        border-radius: 0.5rem;
        font-weight: 700;
        font-size: 0.875rem;
    }

    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
        margin-top: 1.5rem;
    }

    .btn-primary {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        padding: 1.15rem 1.5rem;
        border-radius: 1rem;
        /* Match design: rounded-2xl */
        font-size: 1rem;
        font-weight: 800;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
    }

    .btn-whatsapp {
        background: var(--blue-primary);
        color: white;
        box-shadow: 0 4px 15px rgba(20, 89, 221, 0.3);
    }

    .btn-whatsapp:hover {
        background: #1e6aff;
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(20, 89, 221, 0.4);
    }

    .btn-call {
        background: #FDD817;
        color: var(--slate-900);
        font-size: 1.125rem;
        /* Match chat button size */
        font-weight: 900;
    }

    .btn-call:hover {
        background: #eec915;
    }

    .btn-offer {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        padding: 1rem 1.5rem;
        background: white;
        border: 2px solid var(--slate-200);
        border-radius: 1rem;
        /* Match design: rounded-2xl */
        color: var(--slate-600);
        font-weight: 700;
        cursor: pointer;
        font-size: 1rem;
        transition: all 0.2s;
    }

    .btn-offer:hover {
        background: var(--slate-50);
        border-color: var(--slate-300);
    }

    .btn-outline {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
        width: 100%;
        padding: 0.75rem;
        background: white;
        border: 2px solid var(--slate-200);
        border-radius: 99px;
        color: var(--slate-600);
        font-weight: 700;
        cursor: pointer;
        font-size: 0.875rem;
    }

    .pricing-footer-info {
        margin-top: 1.5rem;
        padding-top: 1rem;
        border-top: 1px solid var(--slate-100);
    }

    .info-item {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        margin-bottom: 0.75rem;
        font-size: 0.8rem;
        color: var(--slate-600);
    }

    .trust-badge-footer {
        background: var(--slate-50);
        padding: 0.75rem;
        text-align: center;
    }

    .trust-badge-footer span {
        font-size: 9px;
        font-weight: 900;
        color: var(--slate-400);
        letter-spacing: 0.15em;
    }

    .cta-box {
        background: var(--slate-900);
        padding: 1.5rem;
        border-radius: 1.5rem;
        color: white;
        position: relative;
        overflow: hidden;
    }

    .glow-effect {
        position: absolute;
        top: 0;
        right: 0;
        width: 6rem;
        height: 6rem;
        background: rgba(20, 89, 221, 0.3);
        filter: blur(40px);
        border-radius: 50%;
        transform: translate(40%, -40%);
    }

    .cta-title {
        font-weight: 900;
        text-transform: uppercase;
        font-size: 1rem;
        margin-bottom: 1.2rem;
        position: relative;
    }

    .cta-desc {
        font-size: 0.8rem;
        opacity: 0.7;
        margin-bottom: 1rem;
        line-height: 1.5;
        position: relative;
    }

    .cta-button {
        width: 100%;
        padding: 0.75rem;
        background: var(--blue-primary);
        border: none;
        color: white;
        border-radius: 0.75rem;
        font-weight: 800;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        cursor: pointer;
        font-size: 0.8rem;
    }

    /* Trust Section */
    /* Trust Section */
    .trust-section {
        padding: 4rem 2rem;
        background: white;
        border: 1px solid var(--slate-100);
        border-radius: 2rem;
        margin-top: 3rem;
        text-align: center;
    }

    .trust-header {
        margin-bottom: 3rem;
    }

    .trust-main-title {
        font-size: 1.75rem;
        font-weight: 900;
        color: var(--slate-900);
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .trust-subtitle {
        color: var(--slate-500);
        max-width: 700px;
        margin: 0 auto;
        font-size: 1rem;
        line-height: 1.6;
    }

    .trust-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 2rem;
        margin-bottom: 4rem;
    }

    .trust-item {
        display: flex;
        flex-direction: column;
        align-items: center;
    }

    .trust-icon-box {
        width: 80px;
        height: 80px;
        background: #eff6ff;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 2rem;
        color: var(--blue-primary);
        margin-bottom: 1.5rem;
    }

    .trust-item-title {
        font-size: 1rem;
        font-weight: 900;
        color: var(--slate-900);
        text-transform: uppercase;
        margin-bottom: 1rem;
    }

    .trust-item-desc {
        font-size: 0.9rem;
        color: var(--slate-500);
        line-height: 1.5;
        max-width: 250px;
    }

    .brand-logos {
        display: flex;
        align-items: center;
        gap: 3rem;
        overflow: hidden;
        margin-top: 3rem;
        padding-top: 3rem;
        border-top: 1px solid var(--slate-50);
        position: relative;
    }

    .brand-logos-track {
        display: flex;
        align-items: center;
        gap: 3rem;
        animation: slide 60s linear infinite;
        white-space: nowrap;
    }

    @keyframes slide {
        0% {
            transform: translateX(0);
        }

        100% {
            transform: translateX(-50%);
        }
    }

    .brand-logos-track img {
        height: 30px;
        object-fit: contain;
        filter: none;
        opacity: 1;
        transition: transform 0.3s;
        flex-shrink: 0;
    }

    .brand-logos-track img:hover {
        transform: scale(1.1);
    }

    /* Propose Price Form */
    .propose-form {
        background: #fffbeb;
        padding: 1rem;
        border-radius: 1rem;
        border: 1px solid #fef3c7;
        margin-bottom: 1rem;
    }

    .propose-form h4 {
        font-size: 0.9rem;
        font-weight: 800;
        color: var(--slate-900);
        margin-bottom: 0.75rem;
    }

    .propose-form-inner {
        display: flex;
        gap: 0.5rem;
    }

    .propose-form input {
        flex: 1;
        padding: 0.6rem;
        border: 1px solid var(--slate-200);
        border-radius: 0.5rem;
        font-size: 0.875rem;
    }

    .propose-form button {
        padding: 0.6rem 1rem;
        background: var(--blue-primary);
        color: white;
        border: none;
        border-radius: 0.5rem;
        font-weight: 700;
        cursor: pointer;
        font-size: 0.875rem;
    }

    /* Share & Favorite Buttons */
    .action-icons {
        display: flex;
        gap: 0.75rem;
        margin-top: 1rem;
    }

    .icon-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        border: 1px solid var(--slate-200);
        background: white;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .icon-btn:hover {
        background: var(--slate-50);
    }

    .icon-btn i {
        color: var(--slate-500);
    }

    .icon-btn.favorited i {
        color: #ef4444;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    /* Vue en Direct (Live View) */
    .live-view-thumbnail {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 7rem;
        height: 10rem;
        border-radius: 1rem;
        overflow: hidden;
        border: 2px solid white;
        box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        z-index: 20;
        cursor: pointer;
        transition: transform 0.3s;
    }

    .live-view-thumbnail:hover {
        transform: scale(1.1);
    }

    .live-view-thumbnail img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        opacity: 0.9;
    }

    .play-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0, 0, 0, 0.4);
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .play-button {
        width: 2.5rem;
        height: 2.5rem;
        background: rgba(255, 255, 255, 0.4);
        backdrop-filter: blur(4px);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .play-button i {
        color: white;
        margin-left: 2px;
        font-size: 1rem;
    }

    .live-tag {
        position: absolute;
        top: 0.5rem;
        right: 0.5rem;
        display: flex;
        gap: 0.25rem;
        align-items: center;
    }

    .pulsing-dot {
        width: 0.5rem;
        height: 0.5rem;
        background-color: #ef4444;
        border-radius: 50%;
        animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
    }

    @keyframes pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: .5;
        }
    }

    .live-text {
        font-size: 8px;
        font-weight: 900;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        text-shadow: 0 1px 2px rgba(0, 0, 0, 0.5);
    }

    /* Finance Chat Modal */
    .finance-chat-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0, 0, 0, 0.4);
        display: none;
        align-items: flex-end;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.3s;
    }

    @media (min-width: 1024px) {
        .finance-chat-overlay {
            align-items: center;
        }
    }

    .finance-chat-overlay.open {
        display: flex;
        opacity: 1;
    }

    .finance-chat-modal {
        width: 100%;
        height: 85vh;
        background: white;
        border-radius: 1.5rem 1.5rem 0 0;
        display: flex;
        flex-direction: column;
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        transform: translateY(100%);
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
    }

    @media (min-width: 1024px) {
        .finance-chat-modal {
            width: 420px;
            height: 600px;
            border-radius: 1.5rem;
            transform: translateY(20px);
            opacity: 0;
        }
    }

    .finance-chat-overlay.open .finance-chat-modal {
        transform: translateY(0);
        opacity: 1;
    }

    .chat-header {
        padding: 1rem;
        border-bottom: 1px solid var(--slate-100);
        display: flex;
        align-items: center;
        justify-content: space-between;
    }

    .chat-title-group p {
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        color: var(--slate-400);
        font-weight: 700;
    }

    .chat-title-group h3 {
        font-weight: 900;
        color: var(--slate-900);
        margin: 0;
    }

    .chat-close-btn {
        background: none;
        border: none;
        cursor: pointer;
        padding: 0.5rem;
        color: var(--slate-500);
        font-size: 1.25rem;
    }

    .chat-body {
        flex: 1;
        padding: 1rem;
        overflow-y: auto;
        background: var(--slate-50);
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .chat-bubble {
        max-width: 80%;
        padding: 0.75rem;
        border-radius: 0.75rem;
        font-size: 0.875rem;
        line-height: 1.5;
        white-space: pre-line;
        overflow-wrap: anywhere;
    }

    .chat-bubble.user {
        background: var(--blue-primary, #1459DD);
        color: white;
        align-self: flex-end;
        border-radius: 18px 18px 4px 18px;
    }

    .chat-bubble.agent {
        background: #e5e7eb;
        color: #0f172a;
        align-self: flex-start;
        border-radius: 18px 18px 18px 4px;
    }

    .chat-footer {
        padding: 1rem;
        border-top: 1px solid var(--slate-100);
        display: flex;
        gap: 0.5rem;
    }

    .chat-input {
        flex: 1;
        padding: 0.75rem 1rem;
        border: 1px solid var(--slate-200);
        border-radius: 0.75rem;
        font-size: 0.875rem;
        outline: none;
    }

    .chat-input:focus {
        border-color: var(--blue-primary);
    }

    .chat-send-btn {
        background: var(--blue-primary);
        color: white;
        border: none;
        padding: 0 1rem;
        border-radius: 0.75rem;
        cursor: pointer;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    #chatMicBtn {
        width: 42px; height: 42px; border-radius: 50%; border: none;
        background: #e2e8f0; color: #475569;
        display: flex; align-items: center; justify-content: center;
        cursor: pointer; flex-shrink: 0; transition: background .2s, color .2s;
    }
    #chatMicBtn:hover { background: #cbd5e1; }
    #chatMicBtn.recording { background: #ef4444; color: #fff; animation: pulse-mic .8s infinite; }
    @keyframes pulse-mic {
        0%,100% { box-shadow: 0 0 0 0 rgba(239,68,68,.4); }
        50%      { box-shadow: 0 0 0 8px rgba(239,68,68,0); }
    }
    .chat-footer audio { max-width: 200px; height: 34px; border-radius: 99px; }

    /* Redesigned Chat Button */
    .btn-chat-new {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.75rem;
        width: 100%;
        padding: 1.25rem;
        background: var(--blue-primary);
        color: white;
        border-radius: 1rem;
        font-weight: 900;
        font-size: 1.125rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        box-shadow: 0 10px 25px -5px rgba(20, 89, 221, 0.4);
        text-decoration: none;
        white-space: nowrap;
    }

    .btn-chat-new i {
        font-size: 1.375rem;
        /* 22px to match lucide-react size={22} */
    }

    .btn-chat-new:hover {
        background: #0f4ac4;
        transform: translateY(-2px);
        box-shadow: 0 15px 30px -5px rgba(20, 89, 221, 0.5);
    }

    .btn-chat-new:active {
        transform: scale(0.98);
    }
</style>

<body>
    {{-- ════════════════ NADO NAVBAR (matches front blade) ════════════════ --}}
    @php $navClient = Auth::guard('client')->user(); @endphp

    <header class="nado-navbar sticky-top">
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
        <script src="https://unpkg.com/lucide@latest"></script>

        <div class="nv-top-bar" style="background-color: #FDD817;">
            <div class="nv-container nv-top-bar-content">
                <div class="nv-contact-info">
                    <div class="nv-info-item">
                        <i data-lucide="mail"></i>
                        <a href="mailto:contact@nado.ma">contact@nado.ma</a>
                    </div>
                    <div class="nv-info-item">
                        <i data-lucide="phone-call"></i>
                        <a href="tel:+212520070611">+212 520 070 611</a>
                    </div>
                </div>
                <div class="nv-social-links">
                    <a href="#"><i data-lucide="facebook"></i></a>
                    <a href="#"><i data-lucide="instagram"></i></a>
                    <a href="#"><i data-lucide="linkedin"></i></a>
                </div>
            </div>
        </div>

        <nav class="nv-main-nav">
            <div class="nv-container nv-nav-content">
                <div class="nv-left-section">
                    <a href="{{ route('index') }}" class="nv-logo-link">
                        <img src="{{ asset('storage/logos/UAfjepY72Xe7grWDva9cqSlRFsRKNZPV3HBDbBP1.png') }}"
                             alt="NADO" style="height: 36px; width: auto; object-fit: contain;" />
                    </a>

                    <div class="nv-desktop-menu">
                        <a href="{{ route('achat.index') }}" class="nv-nav-link">{{ __('messages.nav.buy') }}</a>
                        <a href="{{ route('vente.form') }}" class="nv-nav-link">{{ __('messages.nav.sell') }}</a>
                        <a href="{{ route('nadoautoenchere.prelogin') }}" class="nv-nav-link nv-highlight-link">{{ __('messages.nav.auction') }}</a>
                        <a href="{{ route('reprise.form') }}" class="nv-nav-link">{{ __('messages.nav.tradein') }}</a>
                        <a href="{{ route('financement.index') }}" class="nv-nav-link">{{ __('messages.nav.financing') }}</a>
                        <a href="{{ route('about.index') }}" class="nv-nav-link">{{ __('messages.nav.about') }}</a>
                    </div>
                </div>

                <div class="nv-right-section">
                    @if ($navClient)
                        <div class="nv-user-dropdown-wrapper" id="nvUserDropdownWrapper">
                            <button class="nv-cta-button nv-outline-btn" type="button" aria-label="{{ __('messages.nav.account') }}">
                                <i data-lucide="user"></i>
                                <span class="nv-user-name" title="{{ $navClient->name }}">{{ $navClient->name }}</span>
                                <i data-lucide="chevron-down" class="nv-icon-sm"></i>
                            </button>
                            <div class="nv-user-dropdown-menu">
                                <a href="{{ route('client.dashboard') }}"><i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}</a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="nv-logout-btn"><i data-lucide="log-out"></i> {{ __('messages.nav.logout') }}</button>
                                </form>
                            </div>
                        </div>
                        <script>
                            // Tap / click toggles the account menu (mobile has no hover)
                            (function () {
                                var w = document.getElementById('nvUserDropdownWrapper');
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
                        <a href="{{ route('client.login') }}" class="nv-cta-button nv-outline-btn">
                            {{ __('messages.nav.publish_ad') }}
                        </a>
                    @endif

                    {{-- Language switch on the far (right) edge --}}
                    @include('partials.lang-switcher')

                    <button id="nvMobileMenuBtn" class="nv-mobile-menu-btn">
                        <i data-lucide="menu"></i>
                    </button>
                </div>
            </div>
        </nav>

        <div id="nvMobileDrawer" class="nv-mobile-drawer">
            <div id="nvDrawerBackdrop" class="nv-drawer-backdrop"></div>
            <div class="nv-drawer-content">
                <div class="nv-drawer-header">
                    <span class="nv-logo-text">
                        <span style="color: #1459DD;">NA</span><span style="color: #FDD817;">DO</span>
                    </span>
                    <button id="nvCloseDrawerBtn" class="nv-close-btn"><i data-lucide="x"></i></button>
                </div>
                <div class="nv-drawer-links">
                    <a href="{{ route('achat.index') }}">{{ __('messages.nav.buy') }}</a>
                    <a href="{{ route('vente.form') }}">{{ __('messages.nav.sell') }}</a>
                    <a href="{{ route('nadoautoenchere.prelogin') }}" style="color:#1459DD;">{{ __('messages.nav.auction') }}</a>
                    <a href="{{ route('reprise.form') }}">{{ __('messages.nav.tradein') }}</a>
                    <a href="{{ route('financement.index') }}">{{ __('messages.nav.financing') }}</a>
                    <a href="{{ route('about.index') }}">{{ __('messages.nav.about') }}</a>
                    <div style="margin-top:12px;">@include('partials.lang-switcher')</div>
                </div>
                <div class="nv-drawer-footer">
                    @if ($navClient)
                        <a href="{{ route('client.dashboard') }}" class="nv-drawer-cta" style="background-color:#FDD817;">
                            <i data-lucide="layout-dashboard"></i> {{ __('messages.nav.my_space') }}
                        </a>
                        <form method="POST" action="{{ route('logout') }}" style="margin:10px 0 0;">
                            @csrf
                            <button type="submit"
                                style="display:flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:12px 16px;border-radius:12px;background:#fef2f2;border:1px solid #fecaca;color:#dc2626;font-size:13px;font-weight:800;text-transform:uppercase;letter-spacing:.05em;cursor:pointer;">
                                <i data-lucide="log-out" style="width:16px;height:16px;"></i> {{ __('messages.nav.logout') }}
                            </button>
                        </form>
                    @else
                        <a href="{{ route('client.login') }}" class="nv-drawer-cta" style="background-color:#FDD817;">
                            <i data-lucide="user"></i> {{ __('messages.nav.publish_ad') }}
                        </a>
                    @endif
                    <div class="nv-drawer-socials">
                        <a href="#"><i data-lucide="facebook"></i></a>
                        <a href="#"><i data-lucide="instagram"></i></a>
                        <a href="#"><i data-lucide="linkedin"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .nado-navbar { font-family: 'Inter', sans-serif; width: 100%; z-index: 1000; direction: ltr; }
            .nado-navbar.sticky-top { position: fixed; top: 0; left: 0; width: 100%; }
            .nv-top-bar { padding: 10px 16px; border-bottom: 1px solid rgba(0,0,0,.05); color: #0f172a; }
            .nv-container { max-width: 1280px; margin: 0 auto; padding: 0 16px; display: flex; justify-content: space-between; align-items: center; width: 100%; }
            .nv-contact-info { display: flex; gap: 12px; }
            .nv-info-item { display: flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; opacity: .8; }
            .nv-info-item i { width: 12px; height: 12px; color: #1459DD; }
            .nv-info-item a { color: inherit; text-decoration: none; transition: color .2s; }
            .nv-info-item a:hover { color: #1459DD; }
            .nv-social-links { display: flex; gap: 20px; }
            .nv-social-links a { color: #0f172a; opacity: .6; transition: .2s; }
            .nv-social-links a:hover { color: #1459DD; opacity: 1; }
            .nv-social-links i { width: 20px; height: 20px; }

            .nv-main-nav { background: #fff; border-bottom: 1px solid #e2e8f0; height: 96px; display: flex; align-items: center; }
            .nv-nav-content { height: 100%; width: 100%; display: flex; justify-content: space-between; align-items: center; }
            .nv-left-section { display: flex; align-items: center; gap: 64px; }
            .nv-logo-link { text-decoration: none; transition: transform .2s; }
            .nv-logo-link:hover { transform: scale(1.05); }
            .nv-logo-text { font-size: 2.25rem; font-weight: 900; text-transform: uppercase; line-height: 1; letter-spacing: -.05em; }

            .nv-desktop-menu { display: none; align-items: center; gap: 12px; }
            @media (min-width: 1024px) { .nv-desktop-menu { display: flex; } }
            .nv-nav-link { font-size: 14.4px; font-weight: 700; text-transform: uppercase; letter-spacing: .025em; padding: 8px 12px; border-radius: 16px; color: #334155; text-decoration: none; transition: .2s; background: transparent; border: none; cursor: pointer; }
            .nv-nav-link:hover { background-color: #f1f5f9; color: #1459DD; }
            .nv-highlight-link { color: #1459DD; background-color: #dbeafe; }
            .nv-highlight-link:hover { background-color: #bfdbfe; }

            .nv-right-section { margin-left: auto; display: flex; align-items: center; gap: 12px; }
            .nv-cta-button { display: flex; align-items: center; gap: 8px; padding: 10px 16px; border-radius: 16px; font-size: 15px; font-weight: 800; text-transform: uppercase; letter-spacing: .1em; text-decoration: none; cursor: pointer; transition: .2s; }
            .nv-cta-button:active { transform: scale(.95); }
            /* A long client name used to wrap and stretch the navbar. Keep the button on
               one line and truncate the name with an ellipsis (full name in title=""). */
            .nv-cta-button { white-space: nowrap; }
            .nv-cta-button i { flex-shrink: 0; }
            .nv-user-name {
                display: inline-block;
                max-width: 150px;
                overflow: hidden;
                text-overflow: ellipsis;
                white-space: nowrap;
                vertical-align: middle;
            }
            @media (max-width: 1280px) { .nv-user-name { max-width: 100px; } }
            .nv-outline-btn { background-color: rgba(253,216,23,.2); border: 1px solid rgba(253,216,23,.5); color: #0f172a; }
            .nv-outline-btn:hover { background-color: #FDD817; }

            .nv-mobile-menu-btn { display: block; padding: 12px; background-color: #f8fafc; border: none; border-radius: 12px; cursor: pointer; color: #0f172a; }
            @media (min-width: 1024px) { .nv-mobile-menu-btn { display: none; } }

            /* Visible on every screen size (was hidden below 1024px, so a logged-in
               client lost "Mon espace" / "Déconnexion" on mobile) */
            .nv-user-dropdown-wrapper { position: relative; display: block; flex-shrink: 0; }
            @media (max-width: 1023px) {
                .nv-user-dropdown-wrapper .nv-user-name { display: none; }
                .nv-user-dropdown-wrapper .nv-cta-button { padding: 8px 10px; gap: 4px; }
            }
            /* Tap to open (no hover on touch screens) — toggled by JS */
            .nv-user-dropdown-wrapper.open .nv-user-dropdown-menu {
                opacity: 1; visibility: visible; transform: translateY(0);
                transition: opacity .15s ease, transform .15s ease, visibility 0s;
            }
            .nv-user-dropdown-menu { position: absolute; right: 0; top: 100%; margin-top: 8px; width: 192px; background: #fff; border-radius: 12px; box-shadow: 0 10px 15px -3px rgba(0,0,0,.1); border: 1px solid #e2e8f0; overflow: hidden;
                /* Was display:none/block, which vanished the instant hover was lost.
                   opacity+visibility can be delayed, so the menu lingers ~400ms after
                   the cursor leaves, giving time to move onto it. */
                opacity: 0;
                visibility: hidden;
                transform: translateY(-4px);
                transition: opacity .18s ease .4s, transform .18s ease .4s, visibility 0s linear .58s;
            }
            /* Keep it open while over the button OR over the menu itself. */
            .nv-user-dropdown-wrapper:hover .nv-user-dropdown-menu,
            .nv-user-dropdown-menu:hover {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                transition: opacity .15s ease, transform .15s ease, visibility 0s;
            }
            /* Invisible bridge over the 8px gap so moving down doesn't drop the hover. */
            .nv-user-dropdown-wrapper::after {
                content: '';
                position: absolute;
                top: 100%;
                right: 0;
                width: 100%;
                height: 14px;
            }
            .nv-user-dropdown-menu a, .nv-user-dropdown-menu button { display: flex; align-items: center; width: 100%; padding: 12px 16px; font-size: 14px; font-weight: 700; text-align: left; background: none; border: none; cursor: pointer; text-decoration: none; color: #334155; }
            .nv-user-dropdown-menu a:hover { background-color: #f8fafc; color: #1459DD; }
            .nv-user-dropdown-menu i { width: 16px; height: 16px; margin-right: 8px; }
            .nv-logout-btn { color: #dc2626 !important; }
            .nv-logout-btn:hover { background-color: #fef2f2 !important; }

            .nv-mobile-drawer { position: fixed; inset: 0; z-index: 1100; visibility: hidden; transition: visibility .5s; }
            .nv-mobile-drawer.visible { visibility: visible; pointer-events: auto; }
            .nv-mobile-drawer.visible .nv-drawer-backdrop { opacity: 1; }
            .nv-drawer-backdrop { position: absolute; inset: 0; background-color: rgba(15,23,42,.6); backdrop-filter: blur(8px); opacity: 0; transition: opacity .3s; }
            .nv-drawer-content { position: fixed; top: 0; right: 0; bottom: 0; height: 100vh; width: 85%; max-width: 420px; background: #fff; box-shadow: -10px 0 50px rgba(0,0,0,.3); transform: translateX(100%); transition: transform .3s cubic-bezier(.4,0,.2,1); display: flex; flex-direction: column; overflow-y: auto; }
            .nv-mobile-drawer.visible .nv-drawer-content { transform: translateX(0); }
            .nv-drawer-header { padding: 32px; display: flex; justify-content: space-between; align-items: center; }
            .nv-close-btn { background: none; border: none; color: #94a3b8; cursor: pointer; }
            .nv-close-btn:hover { color: #0f172a; }
            .nv-drawer-links { flex: 1; overflow-y: auto; padding: 16px 32px; display: flex; flex-direction: column; gap: 24px; }
            .nv-drawer-links a { font-size: 18px; font-weight: 900; text-transform: uppercase; letter-spacing: -.025em; color: #0f172a; text-decoration: none; transition: .2s; }
            .nv-drawer-links a:hover { color: #1459DD; }
            .nv-drawer-footer { padding: 40px; display: flex; flex-direction: column; align-items: center; }
            .nv-drawer-cta { width: 100%; padding: 24px; border-radius: 32px; font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em; color: #0f172a; text-decoration: none; display: flex; align-items: center; justify-content: center; gap: 12px; margin-bottom: 48px; transition: transform .2s; }
            .nv-drawer-cta:active { transform: scale(.95); }
            .nv-drawer-socials { display: flex; gap: 40px; margin-bottom: 48px; }
            .nv-drawer-socials a { color: #94a3b8; transition: .2s; }
            .nv-drawer-socials a:hover { color: #0f172a; }
            .nv-icon-sm { width: 16px; height: 16px; }

            @media (max-width: 1023px) {
                .nv-main-nav { height: 72px; }
                .nv-nav-content { flex-wrap: nowrap; }
                .nv-left-section { gap: 0; }
                .nv-logo-link img { height: 24px !important; width: auto !important; }
                .nv-logo-text { font-size: 1.4rem; }
                .nv-right-section { gap: 12px; padding-left: 12px; flex-shrink: 0; }
                .nv-right-section .nv-cta-button {
                    padding: 10px 18px;
                    font-size: 13px;
                    letter-spacing: 0.05em;
                    border-radius: 12px;
                    white-space: nowrap;
                }
            }
        </style>
    </header>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof lucide !== 'undefined') lucide.createIcons();

            const btn = document.getElementById('nvMobileMenuBtn');
            const close = document.getElementById('nvCloseDrawerBtn');
            const drawer = document.getElementById('nvMobileDrawer');
            const backdrop = document.getElementById('nvDrawerBackdrop');

            function open() { drawer?.classList.add('visible'); document.body.style.overflow = 'hidden'; }
            function shut() { drawer?.classList.remove('visible'); document.body.style.overflow = ''; }

            btn?.addEventListener('click', open);
            close?.addEventListener('click', shut);
            backdrop?.addEventListener('click', shut);
            document.addEventListener('keydown', e => { if (e.key === 'Escape') shut(); });
        });
    </script>

    <main class="container main-content">
        <div class="layout-grid">
            {{-- TOP (header + gallery) --}}
            <div class="content-top">
                {{-- Header Section --}}
                <div class="header-section">
                    <div class="badge-row">
                        <span class="badge id-badge">{{ __('messages.detail.ref') }} VC{{ $annonce->id }}</span>
                        @if(!empty($annonce->certifie))
                            <span class="badge certified-badge">{{ __('messages.detail.badge_certified') }}</span>
                        @elseif(!empty($annonce->particulie))
                            <span class="badge particulier-badge">{{ __('messages.detail.badge_particulier') }}</span>
                        @elseif(!empty($annonce->pro))
                            <span class="badge pro-badge">{{ __('messages.detail.badge_pro') }}</span>
                        @endif
                    </div>
                    <h1 class="main-title">
                        @if ($brandName || $modelName)
                            {{ $brandName }}
                            <span class="text-blue">{{ $modelName }}</span>
                        @else
                            {{-- No usable brand/model: show the ad title rather than the words
                                 "Marque Modèle". --}}
                            {{ $vehicleName }}
                        @endif
                    </h1>
                    <p class="specs-subtitle">
                        {{ $annonce->year ?? __('messages.detail.spec_year') }} • {{ number_format($annonce->km ?? 0, 0, ',', ' ') }} KM •
                        {{ $annonce->carburant ?? __('messages.detail.spec_fuel') }} • {{ ucfirst($annonce->box ?? $annonce->transmission ?? '') ?: __('messages.detail.spec_gearbox') }}
                    </p>


                </div>

                {{-- Gallery Section --}}
                <div class="gallery-section">
                    <div class="main-image-container" id="mainDisplay">
                        @php
                            $hasImages = is_array($annonce->images) && count($annonce->images) > 0;
                            $mainImage = null;
                            if ($hasImages) {
                                $mainImage = asset('storage/ads/' . $annonce->images[0]);
                            } elseif ($annonce->media->count() > 0) {
                                $mainImage = asset('storage/ads/' . $annonce->media->first()->name);
                            }
                            $imageCount = $hasImages ? count($annonce->images) : $annonce->media->count();
                            $totalMedia = $imageCount + ($annonce->reel ? 1 : 0);
                        @endphp

                        <div class="image-counter">1 / {{ $totalMedia }}</div>

                        @if ($mainImage)
                            <img id="mainImage" src="{{ $mainImage }}" alt="Image véhicule">
                        @endif

                        @if ($annonce->reel)
                            <div class="live-view-thumbnail" onclick="document.querySelector('.thumb-video').click()">
                                @php
                                    $thumbImage = null;
                                    if ($hasImages && count($annonce->images) > 1) {
                                        $thumbImage = asset('storage/ads/' . $annonce->images[1]);
                                    } elseif ($hasImages && count($annonce->images) > 0) {
                                        $thumbImage = asset('storage/ads/' . $annonce->images[0]);
                                    } elseif (!$hasImages && $annonce->media->count() > 0) {
                                        $thumbImage = asset('storage/ads/' . $annonce->media->first()->name);
                                    } else {
                                        $thumbImage = asset('assets/images/default-car.jpg');
                                    }
                                @endphp
                                <img src="{{ $thumbImage }}" alt="Aperçu vidéo">
                                <div class="play-overlay">
                                    <div class="play-button">
                                        <i class="fas fa-play"></i>
                                    </div>
                                </div>
                                <div class="live-tag">
                                    <span class="pulsing-dot"></span>
                                    <span class="live-text">VUE EN DIRECT</span>
                                </div>
                            </div>
                        @endif
                        <button id="prevBtn" class="gallery-nav-btn prev"><i
                                class="fas fa-chevron-left"></i></button>
                        <button id="nextBtn" class="gallery-nav-btn next"><i
                                class="fas fa-chevron-right"></i></button>
                    </div>

                    <div class="thumbnails" id="thumbnailContainer">
                        @if ($hasImages)
                            @foreach ($annonce->images as $index => $img)
                                <img src="{{ asset('storage/ads/' . $img) }}" data-index="{{ $index }}"
                                    data-type="image" class="thumb {{ $index === 0 ? 'active' : '' }}">
                            @endforeach
                        @else
                            @foreach ($annonce->media as $index => $media)
                                <img src="{{ asset('storage/ads/' . $media->name) }}"
                                    data-index="{{ $index }}"
                                    data-type="image" class="thumb {{ $index === 0 ? 'active' : '' }}">
                            @endforeach
                        @endif
                        @if ($annonce->reel)
                            <div class="thumb thumb-video"
                                data-index="{{ $imageCount }}"
                                data-type="video">
                                <img src="{{ asset('assets/images/play-thumbnail.png') }}"
                                    style="width:100%;height:100%;object-fit:cover;border-radius:0.5rem;">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            {{-- SIDEBAR --}}
            <aside class="sidebar">
                <div class="sticky-content">
                    <div class="pricing-card">
                        <div class="p-6">
                            <div class="price-row">
                                <span class="price-amount">{{ number_format($annonce->prix, 0, ' ', ' ') }}</span>
                                <span class="price-currency">MAD</span>
                            </div>
                            <div class="finance-badge">
                                <i class="fas fa-calendar-alt"></i>
                                <span>{{ __('messages.detail.from') }} {{ number_format($annonce->prix / 60, 0, ' ', ' ') }} MAD {{ __('messages.detail.per_month') }}</span>
                            </div>

                            <div class="action-buttons">
                                <button onclick="openFinanceChat()" class="btn-chat-new">
                                    <i class="fas fa-comment-dots"></i> {{ __('messages.detail.chat_with_seller') }}
                                </button>
                                @if ($annonce->phone)
                                    <a href="tel:{{ $annonce->phone }}" class="btn-primary btn-call">
                                        <i class="fas fa-mobile-alt"></i> {{ $annonce->phone }}
                                    </a>
                                @else
                                    <a href="tel:0520070611" class="btn-primary btn-call">
                                        <i class="fas fa-mobile-alt"></i> 05 20 07 06 11
                                    </a>
                                @endif
                                <button class="btn-offer" id="offerToggleBtn" onclick="toggleOfferPanel()" type="button">
                                    <i class="fas fa-tag"></i> {{ __('messages.detail.make_offer') }}
                                </button>
                            </div>

                            {{-- Inline Offer Panel --}}
                            <div id="offerPanel" style="display:none; margin-top:14px; background:#f0f6ff; border:1.5px solid #1459DD; border-radius:14px; padding:18px 20px; animation: fadeInPanel .25s ease;">
                                <style>
                                    @keyframes fadeInPanel { from { opacity:0; transform:translateY(-8px); } to { opacity:1; transform:translateY(0); } }
                                    #offerPanel .op-label { font-size:13px; color:#475569; margin-bottom:6px; font-weight:500; }
                                    #offerPanel .op-ref   { font-size:12px; color:#94a3b8; margin-bottom:12px; }
                                    #offerPanel .op-row   { display:flex; gap:8px; align-items:center; flex-wrap:wrap; }
                                    #offerPanel .op-input { flex:1; border:1.5px solid #cbd5e1; border-radius:9px; padding:9px 14px; font-size:15px; font-weight:600; color:#0f172a; outline:none; transition:border .2s; min-width: 160px; }
                                    #offerPanel .op-input:focus { border-color:#1459DD; }
                                    #offerPanel .op-currency { font-size:14px; color:#64748b; font-weight:600; white-space:nowrap; }
                                    #offerPanel .op-submit { background:#1459DD; color:#fff; border:none; border-radius:9px; padding:9px 20px; font-size:14px; font-weight:600; cursor:pointer; transition:background .2s; white-space:nowrap; }
                                    #offerPanel .op-submit:hover { background:#0e47b3; }
                                    #offerPanel .op-cancel { font-size:12px; color:#94a3b8; text-align:center; margin-top:10px; cursor:pointer; }
                                    #offerPanel .op-cancel:hover { color:#475569; }
                                    /* Envoyer + Annuler on one line */
                                    #offerPanel .op-actions { display:flex; align-items:center; gap:14px; margin-top:10px; flex-wrap:nowrap; }
                                    #offerPanel .op-actions .op-cancel { margin-top:0; text-align:left; white-space:nowrap; flex-shrink:0; }
                                    #offerPanel .op-success { display:none; text-align:center; color:#16a34a; font-weight:600; font-size:14px; padding:8px 0; }
                                    #offerPanel .op-error  { display:none; text-align:center; color:#dc2626; font-size:13px; margin-top:8px; }
                                    @media (max-width: 420px) {
                                        #offerPanel .op-submit { width: 100%; }
                                        /* Keep the pair on one line: let Envoyer grow instead of going full width. */
                                        #offerPanel .op-actions .op-submit { width: auto; flex: 1 1 auto; }
                                    }
                                </style>

                                @auth('client')
                                <p class="op-label"><i class="fas fa-tag" style="color:#1459DD;margin-right:5px;"></i>{{ __('messages.detail.propose_price') }}</p>
                                <p class="op-ref">{{ __('messages.detail.displayed_price') }} <strong>{{ number_format($annonce->prix, 0, ' ', ' ') }} MAD</strong></p>
                                <form id="offerForm" method="POST" action="{{ route('annonces.proposerPrix', $annonce->id) }}" style="width:100%;">
                                    @csrf
                                    <div class="op-row">
                                        <input type="number" name="proposition" id="offerInput" class="op-input" placeholder="{{ __('messages.detail.offer_placeholder') }}" min="1000" step="1" required />
                                        <span class="op-currency">MAD</span>
                                    </div>
                                    {{-- Envoyer + Annuler share one row --}}
                                    <div class="op-actions">
                                        <button type="submit" class="op-submit"><i class="fas fa-paper-plane"></i> {{ __('messages.detail.send') }}</button>
                                        <span class="op-cancel" onclick="toggleOfferPanel()">✕ {{ __('messages.detail.cancel') }}</span>
                                    </div>
                                </form>
                                <div class="op-error" id="offerError"></div>
                                @else
                                <div style="text-align:center; padding:8px 0;">
                                    <p style="font-size:13px; color:#475569; margin-bottom:12px;">{{ __('messages.detail.login_to_offer') }}</p>
                                    <a href="{{ route('client.login') }}?redirect={{ urlencode(url()->current()) }}" class="op-submit" style="text-decoration:none; display:inline-block; background:#1459DD; color:#fff; border-radius:9px; padding:9px 20px; font-size:14px; font-weight:600;">
                                        <i class="fas fa-sign-in-alt"></i> {{ __('messages.detail.login') }}
                                    </a>
                                    <p class="op-cancel" onclick="toggleOfferPanel()">✕ {{ __('messages.detail.cancel') }}</p>
                                </div>
                                @endauth
                            </div>

                            <div class="pricing-footer-info">
                                <div class="info-item">
                                    <i class="fas fa-check-circle text-green"></i>
                                    <span>{{ __('messages.detail.availability') }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="trust-badge-footer">
                            <span>{{ __('messages.detail.trust_program') }}</span>
                        </div>
                    </div>
                </div>
            </aside>

            {{-- BOTTOM (rest of content) --}}
            <div class="content-bottom">
                {{-- About Section --}}
                <section class="about-section">
                    <h2 class="section-title">
                        <span class="blue-line"></span>
                        {{ __('messages.detail.about_vehicle') }}
                    </h2>
                    <div class="description-card">
                        @php
                            // premiere_main is saved as "Oui"/"Non" by the admin form (older rows may hold 1/0)
                            $isPremiereMain = in_array(strtolower(trim((string) ($annonce->premiere_main ?? ''))), ['oui', '1', 'true'], true);
                            $sellerDescription = trim((string) ($annonce->description ?? ''));
                        @endphp
                        @if ($sellerDescription !== '')
                            <p class="description-text" style="white-space: pre-line;">{{ $sellerDescription }}</p>
                        @elseif (app()->getLocale() === 'ar')
                        <p class="description-text">
                            هذه <strong class="desc-hl">{{ $brandName || $modelName ? trim(($brandName ?? '') . ' ' . ($modelName ?? '')) : 'السيارة' }}</strong>
                            المستعملة،
                            @if ($annonce->origine)
                                ذات الأصل <strong class="desc-hl">{{ $annonce->origine }}</strong>،
                            @endif
                            موديل <strong class="desc-hl">{{ $annonce->year ?? 'غير متوفر' }}</strong>، فرصة ممتازة
                            @if ($isPremiereMain)
                                <strong class="desc-hl">أول يد</strong>
                            @endif يجب اغتنامها.
                            تتميّز بلمسة أنيقة بلون <strong class="desc-hl">{{ $annonce->colors ?? 'غير متوفر' }}</strong>،
                            وقد قطعت <strong class="desc-hl">{{ number_format($annonce->km ?? 0, 0, ',', ' ') }} كلم</strong>.
                            وهي مزوّدة بعلبة سرعة <strong class="desc-hl">{{ ucfirst($annonce->box ?? $annonce->transmission ?? '') ?: 'غير متوفر' }}</strong>
                            وبقوة جبائية تبلغ <strong class="desc-hl">{{ $annonce->cv ?? 'غير متوفر' }} حصان</strong>.
                            هذه السيارة ذات <strong class="desc-hl">{{ $annonce->nb_Door ?? '' }} أبواب</strong> <strong class="desc-hl">معتمدة من نادو</strong>@if ($annonce->garantie ?? false)، وتتوفّر على <strong class="desc-hl">ضمان</strong>@endif.
                            وهي متوفرة بـ<strong class="desc-hl">الدار البيضاء</strong>، ومعروضة بسعر
                            <strong class="desc-hl">{{ number_format($annonce->prix, 0, ',', ' ') }} درهم</strong>.
                        </p>
                        @else
                        <p class="description-text">
                            Ce <strong class="desc-hl">{{ $brandName || $modelName ? trim(($brandName ?? '') . ' ' . ($modelName ?? '')) : 'véhicule' }}</strong>
                            d'occasion,
                            @if ($annonce->origine)
                                d'origine <strong class="desc-hl">{{ $annonce->origine }}</strong>,
                            @endif
                            modèle <strong class="desc-hl">{{ $annonce->year ?? 'N/A' }}</strong>, est une excellente opportunité
                            @if ($isPremiereMain)
                                <strong class="desc-hl">première main</strong>
                            @endif à saisir.
                            Arborant une élégante finition de couleur <strong class="desc-hl">{{ $annonce->colors ?? 'N/A' }}</strong>,
                            ce véhicule affiche <strong class="desc-hl">{{ number_format($annonce->km ?? 0, 0, ',', ' ') }} km</strong> au compteur.
                            Il est équipé d'une boîte de vitesses <strong class="desc-hl">{{ ucfirst($annonce->box ?? $annonce->transmission ?? '') ?: 'N/A' }}</strong> et d'une
                            puissance fiscale de <strong class="desc-hl">{{ $annonce->cv ?? 'N/A' }} CV</strong>.
                            Ce véhicule <strong class="desc-hl">{{ $annonce->nb_Door ?? '' }} portes</strong> est <strong class="desc-hl">certifié par NADO</strong>@if ($annonce->garantie ?? false), bénéficie d'une <strong class="desc-hl">garantie</strong>@endif.
                            Disponible à <strong class="desc-hl">Casablanca</strong>, il est proposé au prix de
                            <strong class="desc-hl">{{ number_format($annonce->prix, 0, ',', ' ') }} DH</strong>.
                        </p>
                        @endif
                        <div class="tag-group">
                            @if ($isPremiereMain)
                                <span class="info-tag">{{ __('messages.detail.tag_first_hand') }}</span>
                            @endif
                            <span class="info-tag">{{ __('messages.detail.tag_service_book') }}</span>
                            <span class="info-tag">{{ __('messages.detail.tag_no_accident') }}</span>
                            @if ($annonce->garantie ?? false)
                                <span class="info-tag">{{ __('messages.detail.tag_warranty_incl') }}</span>
                            @endif
                        </div>
                    </div>
                </section>

                {{-- Tabs Section --}}
                <div class="tabs-container">
                    <div class="tabs-header">
                        <button class="tab-btn active" data-tab="details"><i class="fas fa-info-circle"></i>
                            {{ __('messages.detail.tab_details') }}</button>
                        <button class="tab-btn" data-tab="equipment"><i class="fas fa-list"></i> {{ __('messages.detail.tab_equipment') }}</button>
                        <button class="tab-btn" data-tab="expertise"><i class="fas fa-file-alt"></i>
                            {{ __('messages.detail.tab_expertise') }}</button>
                        <button class="tab-btn" data-tab="history"><i class="fas fa-history"></i> {{ __('messages.detail.tab_history') }}</button>
                        <button class="tab-btn" data-tab="warranty"><i class="fas fa-shield-alt"></i>
                            {{ __('messages.detail.tab_warranty') }}</button>
                    </div>

                    <div class="tabs-content">
                        {{-- Details Tab --}}
                        <div class="tab-pane active" id="details">
                            <div class="specs-grid">
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_price') }}</span><strong>{{ number_format($annonce->prix, 0, ',', ' ') }}
                                        DH</strong>
                                </div>
                                <div class="spec-item"><span>{{ __('messages.detail.spec_brand_model') }}</span><strong>{{ $brandName || $modelName ? trim(($brandName ?? '') . ' ' . ($modelName ?? '')) : 'N/A' }}</strong>
                                </div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_mileage') }}</span><strong>{{ number_format($annonce->km ?? 0, 0, ',', ' ') }}
                                        KM</strong>
                                </div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_fuel') }}</span><strong>{{ $annonce->carburant ?? 'N/A' }}</strong>
                                </div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_year') }}</span><strong>{{ $annonce->year ?? 'N/A' }}</strong>
                                </div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_origin') }}</span><strong>{{ $annonce->origine ?? 'N/A' }}</strong>
                                </div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_color') }}</span><strong>{{ $annonce->colors ?? 'N/A' }}</strong>
                                </div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_doors') }}</span><strong>{{ $annonce->nb_Door ?? 'N/A' }}</strong>
                                </div>
                                <div class="spec-item"><span>{{ __('messages.detail.spec_power') }}</span><strong>{{ $annonce->cv ?? 'N/A' }}</strong></div>
                                <div class="spec-item">
                                    <span>{{ __('messages.detail.spec_gearbox') }}</span><strong>{{ ucfirst($annonce->box ?? $annonce->transmission ?? '') ?: 'N/A' }}</strong>
                                </div>
                            </div>
                        </div>

                        {{-- Equipment Tab --}}
                        <div class="tab-pane" id="equipment">
                            @php
                                // Same option fields as the admin form (form.blade.php)
                                $equipementLabels = [
                                    'airbags' => 'Airbags',
                                    'abs' => 'ABS',
                                    'esp' => 'ESP',
                                    'antipatinage' => 'Antipatinage',
                                    'aide_freinage_urgence' => "Aide au freinage d'urgence",
                                    'antidemarrage_electronique' => 'Antidémarrage électronique',
                                    'aide_demarrage_cote' => 'Aide au démarrage en côte',
                                    'selecteur_mode_conduite' => 'Sélecteur de mode de conduite',
                                    'detection_fatigue' => 'Détection de fatigue',
                                    'maintien_dans_la_voie' => 'Maintien dans la voie',
                                    'detecteur_angle_mort' => "Détecteur d'angle mort",
                                    'detecteur_sous_gonflage' => 'Détecteur de sous-gonflage',
                                    'fermeture_portes_auto' => 'Fermeture de portes auto.',
                                    'preparation_isofix' => 'Préparation ISOFIX',
                                    'phares_antibrouillard' => 'Phares antibrouillard',
                                    'systeme_alarme' => 'Système d’alarme',
                                    'climatisation' => 'Climatisation',
                                    'start_stop' => 'Start & Stop',
                                    'regulateur_vitesse' => 'Régulateur de vitesse',
                                    'detecteur_pluie' => 'Détecteur de pluie',
                                    'allumage_auto_feux' => 'Allumage auto. des feux',
                                    'frein_main_electrique' => 'Frein à main électrique',
                                    'aide_stationnement' => 'Aide au stationnement',
                                    'volant_reglable' => 'Volant réglable',
                                    'retros_electriques' => 'Rétros. électriques',
                                    'retros_rabat_electriques' => 'Rétros. rabattables électriques',
                                    'coffre_electrique' => 'Coffre électrique',
                                    'sieges_electriques' => 'Sièges électriques',
                                    'sieges_elec_memoire' => 'Sièges élec. avec mémoire',
                                    'banquette_rabattable' => 'Banquette arrière rabattable 1/3-2/3',
                                    'ecran_tactile' => 'Écran tactile',
                                    'camera_recul' => 'Caméra de recul',
                                    'cockpit_digital' => 'Cockpit digital',
                                    'commandes_volant' => 'Commandes au volant',
                                    'commandes_vocales' => 'Commandes vocales',
                                    'reconnaissance_panneaux' => 'Reconnaissance de panneaux',
                                    'affichage_tete_haute' => 'Affichage Tête-Haute',
                                    'systeme_audio' => 'Système audio',
                                    'ordinateur' => 'Ordinateur de bord',
                                    'gps' => 'Navigation GPS',
                                    'wifi_bord' => 'WiFi à bord',
                                    'bluetooth' => 'Bluetooth',
                                    'compatibilite_smartphone' => 'Compatibilité smartphone',
                                    'apple_carplay_android_auto' => 'Apple CarPlay® & Android Auto®',
                                    'chargeur_sans_fil' => 'Chargeur/mobile sans fil',
                                    'jantes_aluminium' => 'Jantes aluminium',
                                    'sellerie_similicuir_tissu' => 'Sellerie Similicuir / Tissu',
                                    'volant_cuir' => 'Volant cuir',
                                    'follow_me_home' => 'Follow-me home',
                                    'lumiere_ambiance' => "Lumière d'ambiance",
                                    'feux_jour_led' => 'Feux de jour LED',
                                    'phares_full_led' => 'Phares Full LED',
                                    'toit_panoramique' => 'Toit Panoramique ouvrant',
                                    'barres_de_toit' => 'Barres de toit',
                                    'vitres_surteintees' => 'Vitres sur-teintées',

                                    // Older column names (older annonces / BuyController) — same label = shown once
                                    'jantesA' => 'Jantes aluminium',
                                    'antidemarrage' => 'Antidémarrage électronique',
                                    'maintien_voie' => 'Maintien dans la voie',
                                    'fermeture_auto' => 'Fermeture de portes auto.',
                                    'isofix' => 'Préparation ISOFIX',
                                    'alarme' => 'Système d’alarme',
                                    'allumage_auto' => 'Allumage auto. des feux',
                                    'frein_main_electriques' => 'Frein à main électrique',
                                    'coffre_electriques' => 'Coffre électrique',
                                    'sieges_mem' => 'Sièges élec. avec mémoire',
                                    'banquette_rab' => 'Banquette arrière rabattable 1/3-2/3',
                                    'ordinateur_bord' => 'Ordinateur de bord',
                                    'rv' => 'Régulateur de vitesse',
                                    'vitres_electriques' => 'Vitres électriques',
                                    'park_assist' => 'Park Assist Auto.',
                                    'lv' => 'Limiteur de vitesse',
                                    'eps' => 'EPS',
                                    'cd' => 'Cd/mp3/bluetooth',
                                    'toit_ouvrant' => 'Toit ouvrant',
                                    'double_toit_ouvrant' => 'Double toit ouvrant',
                                    'siege_cuir' => 'Siège cuir',
                                    'radar_recul' => 'Radar de recul',
                                    'verrouillage' => 'Verrouillage centralisé à distance',
                                ];
                                $equipements = [];
                                foreach ($equipementLabels as $field => $label) {
                                    $value = $annonce->$field ?? null;
                                    $isOn = is_string($value)
                                        ? !in_array(strtolower(trim($value)), ['', '0', 'non', 'false', 'off'], true)
                                        : (bool) $value;
                                    if ($isOn) {
                                        $equipements[$label] = $label;
                                    }
                                }
                                $equipements = array_values($equipements);
                            @endphp
                            @if (isset($equipements) && count($equipements))
                                <div class="equipment-grid">
                                    @foreach ($equipements as $label)
                                        <div class="eq-item"><span class="eq-dot"></span>{{ $label }}</div>
                                    @endforeach
                                </div>
                            @else
                                <p style="color:var(--slate-500);">{{ __('messages.detail.no_equipment') }}</p>
                            @endif
                        </div>

                        {{-- Expertise Tab --}}
                        <div class="tab-pane" id="expertise">
                            @php
                                // The admin uploads the rapport to storage/app/public/pdf/{name}
                                // and saves {name} in $annonce->rapport (AnnoncesController store/update).
                                $rapportUrl = null;
                                $rapportFile = trim((string) ($annonce->rapport ?? ''));
                                if ($rapportFile !== '' && \Illuminate\Support\Facades\Storage::disk('public')->exists('pdf/' . $rapportFile)) {
                                    $rapportUrl = asset('storage/pdf/' . $rapportFile);
                                } elseif (file_exists(public_path("storage/ads/pdf/{$annonce->id}.pdf"))) {
                                    // Old location
                                    $rapportUrl = asset("storage/ads/pdf/{$annonce->id}.pdf");
                                }
                                $rapportIsImage = $rapportUrl && preg_match('/\.(jpe?g|png|webp|gif)$/i', $rapportUrl);
                            @endphp
                            @if ($rapportUrl)
                                <div style="margin:0 0 1.25rem;border:1px solid var(--slate-200, #e2e8f0);border-radius:1rem;overflow:hidden;">
                                    @if ($rapportIsImage)
                                        <img src="{{ $rapportUrl }}" alt="Rapport d'expertise" style="display:block;width:100%;height:auto;">
                                    @else
                                        {{-- Rendered with PDF.js so it also previews on mobile (Android/iOS don't show PDFs in an iframe) --}}
                                        <div id="rapportPdfViewer" data-url="{{ $rapportUrl }}"
                                            style="max-height:80vh;overflow-y:auto;-webkit-overflow-scrolling:touch;background:#f1f5f9;">
                                            <p class="rapport-pdf-status" style="padding:2rem;text-align:center;color:var(--slate-500);font-size:0.875rem;margin:0;">
                                                {{ __('messages.detail.loading_report') }}
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            @else
                                <p style="color:var(--slate-500);font-size:0.875rem;">{{ __('messages.detail.no_report') }}</p>
                            @endif
                        </div>

                        {{-- History Tab --}}
                        <div class="tab-pane" id="history">
                            <div class="timeline">
                                @forelse($annonce->entretiens ?? [] as $entretien)
                                    <div class="timeline-item">
                                        <div class="timeline-dot"></div>
                                        <div>
                                            <h5 style="font-weight:700;font-size:0.9rem;">
                                                {{ $entretien->revision ?? 'Entretien' }}
                                            </h5>
                                            <p style="font-size:0.8rem;color:var(--slate-500);">
                                                {{ $entretien->date ?? '' }} - {{ $entretien->entretien ?? '' }}
                                            </p>
                                        </div>
                                    </div>
                                @empty
                                    <p style="color:var(--slate-500);font-size:0.875rem;">{{ __('messages.detail.no_history') }}</p>
                                @endforelse
                            </div>
                        </div>

                        {{-- Warranty Tab --}}
                        <div class="tab-pane" id="warranty">
                            <div class="warranty-card">
                                <div class="shield-icon-circle">
                                    <i class="fas fa-shield-alt" style="font-size:3rem;"></i>
                                </div>
                                <h3 style="font-size:1.5rem;font-weight:800;color:var(--slate-900);margin-bottom:0.5rem;">{{ __('messages.detail.warranty_title') }}</h3>
                                <p style="color:var(--slate-500);font-size:0.875rem;margin-bottom:1.5rem;">{{ __('messages.detail.warranty_desc') }}</p>
                                <button class="secondary-button">{{ __('messages.detail.warranty_conditions') }}</button>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Finance Simulator --}}
                <section class="finance-card">
                    <div class="finance-header">
                        <div class="header-content">
                            <div class="finance-icon"><i class="fas fa-border-all"></i></div>
                            <div>
                                <h2 class="finance-title">{{ __('messages.detail.fin_title') }}</h2>
                                <p class="finance-subtitle">{{ __('messages.detail.fin_subtitle') }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="finance-body">
                        <div class="finance-inputs">
                            <input type="hidden" id="prixTotal" value="{{ $annonce->prix }}">
                            <input type="hidden" id="taux" value="{{ $tauxInteretDefault ?? 13 }}">

                            {{-- 1. Profil --}}
                            <div class="input-group">
                                <div class="label-row" style="margin-bottom:0.75rem;">
                                    <label><i class="fas fa-fingerprint"></i> {{ __('messages.detail.fin_profile') }}</label>
                                </div>
                                <div class="profile-grid">
                                    @foreach ($clientTypes ?? [] as $type)
                                        <button class="profile-btn"
                                            data-taux="{{ $type->taux_interet }}">{{ client_type_label($type->name) }}</button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- 2. Apport --}}
                            <div class="input-group">
                                <div class="label-row">
                                    <label><i class="fas fa-coins"></i> {{ __('messages.detail.fin_down_payment') }}</label>
                                    <span id="apport-val" class="val-highlight">0</span>
                                </div>
                                <input type="range" id="apport-range" min="0" max="{{ $annonce->prix }}"
                                    step="5000" value="0" class="custom-range">
                            </div>

                            {{-- 3. Durée --}}
                            <div class="input-group">
                                <div class="label-row">
                                    <label><i class="fas fa-clock"></i> {{ __('messages.detail.fin_duration') }}</label>
                                    <span id="duree-val" class="val-highlight">60 {{ __('messages.detail.fin_months') }}</span>
                                </div>
                                <div class="duration-grid">
                                    <button class="duration-btn" data-months="12">12m</button>
                                    <button class="duration-btn" data-months="24">24m</button>
                                    <button class="duration-btn" data-months="36">36m</button>
                                    <button class="duration-btn" data-months="48">48m</button>
                                    <button class="duration-btn active" data-months="60">60m</button>
                                    <button class="duration-btn" data-months="72">72m</button>
                                    <button class="duration-btn" data-months="84">84m</button>
                                </div>
                            </div>
                        </div>

                        <div class="finance-result">
                            <span class="result-label">{{ __('messages.detail.fin_monthly_est') }}</span>
                            <div class="result-amount">
                                <span id="monthly-payment">0</span><small>MAD</small>
                            </div>
                            <p class="disclaimer">{{ __('messages.detail.fin_disclaimer') }}</p>
                            <button type="button" class="chat-btn" onclick="startFinanceDiscussion()">
                                <i class="fas fa-comment"></i> {{ __('messages.detail.fin_discuss') }}
                            </button>
                        </div>
                    </div>
                </section>

                {{-- Trust Section --}}
                <section class="trust-section">
                    <div class="trust-header">
                        <h2 class="trust-main-title">{{ __('messages.detail.trust_title') }}</h2>
                        <p class="trust-subtitle">{{ __('messages.detail.trust_subtitle') }}</p>
                    </div>
                    <div class="trust-grid">
                        <div class="trust-item">
                            <div class="trust-icon-box"><i class="fas fa-check-circle"></i></div>
                            <h3 class="trust-item-title">{{ __('messages.detail.trust_1_title') }}</h3>
                            <p class="trust-item-desc">{{ __('messages.detail.trust_1_desc') }}</p>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon-box"><i class="fas fa-ban"></i></div>
                            <h3 class="trust-item-title">{{ __('messages.detail.trust_2_title') }}</h3>
                            <p class="trust-item-desc">{{ __('messages.detail.trust_2_desc') }}</p>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon-box"><i class="fas fa-trophy"></i></div>
                            <h3 class="trust-item-title">{{ __('messages.detail.trust_3_title') }}</h3>
                            <p class="trust-item-desc">{{ __('messages.detail.trust_3_desc') }}</p>
                        </div>
                        <div class="trust-item">
                            <div class="trust-icon-box"><i class="fas fa-star"></i></div>
                            <h3 class="trust-item-title">{{ __('messages.detail.trust_4_title') }}</h3>
                            <p class="trust-item-desc">{{ __('messages.detail.trust_4_desc') }}</p>
                        </div>
                    </div>
                    @php
                        // These logos used to be hardcoded paths under
                        // public/assets/images/brands/ which do not exist -> 404s.
                        // The real logos are the ones the homepage ticker uses:
                        // storage/brands/{logo}, driven by the brands table.
                        $tickerBrands = collect($brands ?? []);
                        if ($tickerBrands->isEmpty()) {
                            $tickerBrands = \App\Models\Brand::whereNotNull('logo')
                                ->where('logo', '!=', '')
                                ->get();
                        }
                    @endphp
                    @if($tickerBrands->isNotEmpty())
                        <div class="brand-logos">
                            <div class="brand-logos-track">
                                @foreach($tickerBrands as $tickerBrand)
                                    <img src="{{ asset('storage/brands/' . $tickerBrand->logo) }}"
                                        alt="{{ $tickerBrand->name }}">
                                @endforeach
                                <!-- Duplicated for seamless loop -->
                                @foreach($tickerBrands as $tickerBrand)
                                    <img src="{{ asset('storage/brands/' . $tickerBrand->logo) }}"
                                        alt="" aria-hidden="true">
                                @endforeach
                            </div>
                        </div>
                    @endif
                </section>
            </div>
        </div>
    </main>

    {{-- Discussion Chat Modal --}}
    <script>
        // Renders text into an element, turning http(s) URLs into real links.
        // Built from text nodes + <a> elements (never innerHTML), so message content
        // can never inject markup.
        window.nadoSetTextWithLinks = function (el, text) {
            el.textContent = '';
            text = String(text == null ? '' : text);

            var re = /(https?:\/\/[^\s<>"']+)/g;
            var last = 0;
            var m;

            while ((m = re.exec(text)) !== null) {
                if (m.index > last) {
                    el.appendChild(document.createTextNode(text.slice(last, m.index)));
                }

                // Trailing sentence punctuation is not part of the URL.
                var url = m[1].replace(/[.,;:!?)\]]+$/, '');

                var a = document.createElement('a');
                a.href = url;
                a.textContent = url;
                a.target = '_blank';
                a.rel = 'noopener noreferrer';
                a.style.cssText = 'color:inherit;text-decoration:underline;word-break:break-all;';
                el.appendChild(a);

                last = m.index + url.length;
            }

            if (last < text.length) {
                el.appendChild(document.createTextNode(text.slice(last)));
            }
        };
    </script>
    <div id="financeChatOverlay" class="finance-chat-overlay">
        <div class="finance-chat-modal">
            <div class="chat-header">
                <div class="chat-title-group">
                    <p>{{ __('messages.detail.ref') }} VC{{ $annonce->id }}</p>
                    <h3>{{ __('messages.detail.chat_with_seller') }}</h3>
                </div>
                <button class="chat-close-btn" onclick="closeFinanceChat()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            {{-- Auth gate shown before chat loads --}}
            <div id="chatAuthGate" style="display:none; padding:2rem; text-align:center;">
                <i class="fas fa-lock" style="font-size:2.5rem; color:#003b87; margin-bottom:1rem; display:block;"></i>
                <p style="margin-bottom:1.25rem; color:#555;">{{ __('messages.detail.chat_login') }}</p>
                <a href="{{ route('client.login') }}?redirect={{ urlencode(request()->fullUrl()) }}"
                   style="background:#003b87; color:#fff; padding:0.6rem 1.5rem; border-radius:8px; text-decoration:none; font-weight:600;">
                    {{ __('messages.detail.login') }}
                </a>
            </div>
            {{-- Loading state --}}
            <div id="chatLoading" style="display:none; padding:2rem; text-align:center; color:#888;">
                <i class="fas fa-spinner fa-spin" style="font-size:1.5rem;"></i>
                <p style="margin-top:0.75rem;">{{ __('messages.detail.chat_loading') }}</p>
            </div>
            {{-- Real chat UI --}}
            <div id="chatBody" class="chat-body" style="display:none;"></div>
            <div id="chatClosedNote" style="display:none; text-align:center; color:#94a3b8; padding:1rem; border-top:1px solid #e2e8f0; font-size:0.85rem;">
                <i class="fas fa-lock"></i> {{ __('messages.detail.chat_closed') }}
            </div>
            <div class="chat-footer" id="chatFooter" style="display:none;">
                <input type="text" class="chat-input" placeholder="{{ __('messages.detail.chat_placeholder') }}" id="chatInput" autocomplete="off">
                <button class="chat-send-btn" id="chatMicBtn" title="{{ __('messages.detail.chat_voice') }}" style="background:#e2e8f0; color:#475569; margin-right:4px;">
                    <i class="fas fa-microphone"></i>
                </button>
                <button class="chat-send-btn" id="chatSendBtn">
                    <i class="fas fa-paper-plane"></i>
                </button>
            </div>
            <div id="chatRecTimer" style="display:none; text-align:center; font-size:11px; color:#ef4444; padding:.2rem 1rem .4rem;">
                <i class="fas fa-circle"></i> Enregistrement… <span id="chatRecSec">0</span>s — appuyez à nouveau pour envoyer
            </div>
        </div>
    </div>

    {{-- SCRIPTS --}}
    <script>
        // Mobile Drawer Toggle
        const navToggle = document.getElementById('navToggle');
        const mobileDrawer = document.getElementById('mobileDrawer');
        const drawerClose = document.getElementById('drawerClose');
        const drawerBackdrop = document.getElementById('drawerBackdrop');

        function openDrawer() {
            mobileDrawer.classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function closeDrawer() {
            mobileDrawer.classList.remove('open');
            document.body.style.overflow = 'unset';
        }

        navToggle?.addEventListener('click', openDrawer);
        drawerClose?.addEventListener('click', closeDrawer);
        drawerBackdrop?.addEventListener('click', closeDrawer);

        // Close drawer when clicking on nav links
        document.querySelectorAll('.drawer-nav a').forEach(link => {
            link.addEventListener('click', closeDrawer);
        });

        // Tab Switching
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabButtons.forEach(button => {
            button.addEventListener('click', () => {
                const targetTab = button.getAttribute('data-tab');
                tabButtons.forEach(btn => btn.classList.remove('active'));
                tabPanes.forEach(pane => pane.classList.remove('active'));
                button.classList.add('active');
                document.getElementById(targetTab).classList.add('active');
                if (targetTab === 'expertise') renderRapportPdf();
            });
        });

        // Rapport PDF preview (PDF.js) - works on mobile, where PDFs in an iframe don't display
        let rapportPdfRendered = false;
        function renderRapportPdf() {
            const viewer = document.getElementById('rapportPdfViewer');
            if (!viewer || rapportPdfRendered) return;
            rapportPdfRendered = true;

            const status = viewer.querySelector('.rapport-pdf-status');
            const fail = function () {
                status.innerHTML = 'Aperçu indisponible. <a href="' + viewer.dataset.url +
                    '" target="_blank" rel="noopener" style="color:var(--blue-primary);font-weight:700;">Ouvrir le rapport</a>';
            };

            const start = function () {
                const pdfjsLib = window.pdfjsLib;
                pdfjsLib.GlobalWorkerOptions.workerSrc =
                    'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.worker.min.js';

                pdfjsLib.getDocument(viewer.dataset.url).promise.then(async function (pdf) {
                    status.remove();
                    const width = viewer.clientWidth || 600;
                    const ratio = window.devicePixelRatio || 1;

                    for (let n = 1; n <= pdf.numPages; n++) {
                        const page = await pdf.getPage(n);
                        const base = page.getViewport({ scale: 1 });
                        const viewport = page.getViewport({ scale: (width / base.width) * ratio });

                        const canvas = document.createElement('canvas');
                        canvas.width = viewport.width;
                        canvas.height = viewport.height;
                        canvas.style.cssText = 'display:block;width:100%;height:auto;background:#fff;' +
                            (n > 1 ? 'margin-top:8px;' : '');
                        viewer.appendChild(canvas);

                        await page.render({ canvasContext: canvas.getContext('2d'), viewport: viewport }).promise;
                    }
                }).catch(fail);
            };

            if (window.pdfjsLib) { start(); return; }
            const s = document.createElement('script');
            s.src = 'https://cdnjs.cloudflare.com/ajax/libs/pdf.js/3.11.174/pdf.min.js';
            s.onload = start;
            s.onerror = fail;
            document.head.appendChild(s);
        }
        if (document.getElementById('expertise')?.classList.contains('active')) renderRapportPdf();

        // Share Popup
        function openSharePopup(url) {
            Swal.fire({
                title: 'Partager cette annonce',
                html: `
            <div style="display:flex;justify-content:center;gap:1.5rem;font-size:2rem;">
                <a href="https://api.whatsapp.com/send?text=${url}" target="_blank" style="color:#25D366;"><i class="fab fa-whatsapp"></i></a>
                <a href="https://www.facebook.com/sharer/sharer.php?u=${url}" target="_blank" style="color:#1877F2;"><i class="fab fa-facebook"></i></a>
                <a href="mailto:?subject=Annonce Nado.ma&body=Voici une annonce intéressante : ${url}" style="color:#EA4335;"><i class="fas fa-envelope"></i></a>
            </div>
        `,
                showCloseButton: true,
                showConfirmButton: false,
                width: 350,
            });
        }

        // Gallery Logic
        document.addEventListener('DOMContentLoaded', () => {
            const mediaList = [];
            @if ($hasImages)
                @foreach ($annonce->images as $img)
                    mediaList.push({ type: 'image', src: "{{ asset('storage/ads/' . $img) }}" });
                @endforeach
            @else
                @foreach ($annonce->media as $m)
                    mediaList.push({ type: 'image', src: "{{ asset('storage/ads/' . $m->name) }}" });
                @endforeach
            @endif
            @if ($annonce->reel)
                mediaList.push({
                    type: 'video',
                    videoId: "{{ $annonce->reel }}"
                });
            @endif

            let currentIndex = 0;
            const mainDisplay = document.getElementById('mainDisplay');
            const thumbs = document.querySelectorAll('.thumb, .thumb-video');

            function renderMain(index) {
                currentIndex = index;
                const item = mediaList[index];
                const counterHtml = `<div class="image-counter">${index + 1} / ${mediaList.length}</div>`;

                if (item.type === 'image') {
                    let overlayHtml = '';
                    const videoIndex = mediaList.findIndex(m => m.type === 'video');
                    if (videoIndex !== -1) {
                        const thumbSrc = mediaList.length > 1 && mediaList[1].type === 'image' ? mediaList[1].src :
                            mediaList[0].src;

                        overlayHtml = `
                        <div class="live-view-thumbnail">
                            <img src="${thumbSrc}" alt="Aperçu vidéo">
                            <div class="play-overlay">
                                <div class="play-button"><i class="fas fa-play"></i></div>
                            </div>
                            <div class="live-tag">
                                <span class="pulsing-dot"></span>
                                <span class="live-text">VUE EN DIRECT</span>
                            </div>
                        </div>`;
                    }

                    mainDisplay.innerHTML = `${counterHtml}
                <img src="${item.src}" alt="Image" style="width:100%;max-height:500px;object-fit:contain;cursor:zoom-in;">
                ${overlayHtml}
                <button id="prevBtn" class="gallery-nav-btn prev"><i class="fas fa-chevron-left"></i></button>
                <button id="nextBtn" class="gallery-nav-btn next"><i class="fas fa-chevron-right"></i></button>`;

                    if (videoIndex !== -1) {
                        mainDisplay.querySelector('.live-view-thumbnail')?.addEventListener('click', (e) => {
                            e.stopPropagation();
                            renderMain(videoIndex);
                        });
                    }

                } else {
                    mainDisplay.innerHTML = `${counterHtml}
                <iframe width="100%" height="400" src="https://www.youtube.com/embed/${item.videoId}" frameborder="0" allowfullscreen style="border-radius:1rem;"></iframe>
                <button id="prevBtn" class="gallery-nav-btn prev"><i class="fas fa-chevron-left"></i></button>
                <button id="nextBtn" class="gallery-nav-btn next"><i class="fas fa-chevron-right"></i></button>`;
                }
                thumbs.forEach((t, i) => t.classList.toggle('active', i === index));
                attachNavListeners();
            }

            function attachNavListeners() {
                document.getElementById('prevBtn')?.addEventListener('click', () => renderMain((currentIndex - 1 +
                    mediaList.length) % mediaList.length));
                document.getElementById('nextBtn')?.addEventListener('click', () => renderMain((currentIndex + 1) %
                    mediaList.length));
            }

            thumbs.forEach((thumb, i) => thumb.addEventListener('click', () => renderMain(i)));

            mainDisplay?.addEventListener('dblclick', () => {
                if (mediaList[currentIndex]?.type === 'image') {
                    Swal.fire({
                        imageUrl: mediaList[currentIndex].src,
                        showConfirmButton: false,
                        background: 'transparent',
                        backdrop: 'rgba(0,0,0,0.9)'
                    });
                }
            });

            attachNavListeners();
        });

        // Finance Calculator
        // ... (Existing calculator code)

        // ── Discussion Chat (real AJAX + voice) ──────────────────────────────
        (function () {
            const IS_AUTH    = {{ Auth::guard('client')->check() ? 'true' : 'false' }};
            const ANNONCE_ID = {{ $annonce->id }};
            const START_URL  = '{{ route('client.discussions.start') }}';
            const CSRF       = '{{ csrf_token() }}';

            const overlay    = document.getElementById('financeChatOverlay');
            const authGate   = document.getElementById('chatAuthGate');
            const loading    = document.getElementById('chatLoading');
            const chatBody   = document.getElementById('chatBody');
            const chatFooter = document.getElementById('chatFooter');
            const closedNote = document.getElementById('chatClosedNote');
            const chatInput  = document.getElementById('chatInput');
            const sendBtn    = document.getElementById('chatSendBtn');
            const micBtn     = document.getElementById('chatMicBtn');
            const recTimer   = document.getElementById('chatRecTimer');
            const recSec     = document.getElementById('chatRecSec');

            let sendUrl = null, pollUrl = null, lastMsgId = 0;
            let pollTimer = null, isOpen = false, discussionId = null;
            let mediaRec = null, audioChunks = [], recInterval = null, recSeconds = 0;

            // ── Fix WebM audio duration (browser bug) ──
            // MediaRecorder writes WebM/Opus with no duration in the header, so the browser
            // reports Infinity/NaN until the whole file is buffered. preload="auto" (set by
            // the caller) makes that happen quickly for short voice notes; the seek-to-end
            // trick below is the fallback for browsers that still refuse.
            function fixAudioDuration(audio) {
                var tries = 0;
                var SEEK_TARGET = 1e6;   // finite: 1e101 left currentTime at 596523:14:07

                function bad() {
                    return !isFinite(audio.duration) || isNaN(audio.duration) || audio.duration === 0;
                }

                function rewind() {
                    try {
                        if (audio.currentTime > 0 && !audio.played.length) audio.currentTime = 0;
                    } catch (e) { /* ignore */ }
                }

                function nudge() {
                    if (!bad() || tries++ > 2) { rewind(); return; }
                    try {
                        audio.currentTime = SEEK_TARGET;
                    } catch (e) {
                        /* not seekable yet - a later event will retry */
                    }
                }

                // Whatever happens, never leave the playhead parked past the end.
                audio.addEventListener('durationchange', function () {
                    if (bad()) { nudge(); } else { rewind(); }
                });
                audio.addEventListener('seeked', rewind);
                audio.addEventListener('loadedmetadata', nudge);
                audio.addEventListener('canplaythrough', function () { rewind(); });

                setTimeout(function () { if (bad()) nudge(); else rewind(); }, 400);
                setTimeout(rewind, 1500);
                setTimeout(rewind, 3000);

                if (audio.readyState >= 1) nudge();
            }

            // ── Notification beep ──
            function beep() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    const osc = ctx.createOscillator(), g = ctx.createGain();
                    osc.connect(g); g.connect(ctx.destination);
                    osc.type = 'sine'; osc.frequency.value = 880;
                    g.gain.setValueAtTime(0.2, ctx.currentTime);
                    g.gain.exponentialRampToValueAtTime(0.001, ctx.currentTime + 0.4);
                    osc.start(); osc.stop(ctx.currentTime + 0.4);
                } catch(e) {}
            }

            // ── Bubble builder ─────────────────────────────────────────────
            // localUrl: blob URL of a voice note just recorded — plays instantly, without
            // waiting for the uploaded file to be fetched back from the server
            function appendBubble(msg, sound, localUrl) {
                const isClient = msg.sender_type === 'client';
                const hint = chatBody.querySelector('[data-hint]');
                if (hint) hint.remove();

                const wrap = document.createElement('div');
                wrap.className = isClient ? 'chat-bubble user' : 'chat-bubble agent';
                wrap.setAttribute('data-msg-id', msg.id);

                if (msg.type === 'audio' && msg.attachment_url) {
                    // A native <audio controls> shows 0:00 until the browser parses a duration
                    // out of a WebM that does not carry one. Custom player instead, so the
                    // length the server stored is visible straight away, before any playback.
                    const known = msg.duration ? parseInt(msg.duration, 10) : 0;
                    const fmt = function (s) {
                        s = Math.max(0, Math.round(s || 0));
                        return Math.floor(s / 60) + ':' + String(s % 60).padStart(2, '0');
                    };

                    const audio = document.createElement('audio');
                    audio.preload = 'auto';
                    audio.src = localUrl || msg.attachment_url;
                    audio.style.display = 'none';
                    if (!known) fixAudioDuration(audio);

                    const fg = isClient ? '#fff' : '#0f172a';
                    const trackBg = isClient ? 'rgba(255,255,255,.35)' : 'rgba(15,23,42,.18)';
                    const btnBg = isClient ? 'rgba(255,255,255,.25)' : 'rgba(15,23,42,.10)';

                    const player = document.createElement('div');
                    player.style.cssText = 'display:flex;align-items:center;gap:8px;min-width:180px;color:' + fg + ';';

                    const btn = document.createElement('button');
                    btn.type = 'button';
                    btn.textContent = '\u25B6';
                    btn.style.cssText = 'flex:0 0 auto;width:28px;height:28px;border:0;border-radius:50%;cursor:pointer;font-size:11px;line-height:1;background:' + btnBg + ';color:' + fg + ';';

                    const bar = document.createElement('div');
                    bar.style.cssText = 'flex:1 1 auto;height:4px;border-radius:2px;background:' + trackBg + ';position:relative;cursor:pointer;';
                    const fill = document.createElement('div');
                    fill.style.cssText = 'position:absolute;left:0;top:0;bottom:0;width:0%;border-radius:2px;background:' + fg + ';';
                    bar.appendChild(fill);

                    const time = document.createElement('span');
                    time.style.cssText = 'flex:0 0 auto;font-size:11px;white-space:nowrap;font-variant-numeric:tabular-nums;';
                    time.textContent = fmt(known);

                    function total() {
                        return known || (isFinite(audio.duration) ? audio.duration : 0);
                    }

                    btn.addEventListener('click', function (e) {
                        e.stopPropagation();
                        if (audio.paused) { audio.play(); } else { audio.pause(); }
                    });
                    audio.addEventListener('play', function () { btn.textContent = '\u2759\u2759'; });
                    audio.addEventListener('pause', function () { btn.textContent = '\u25B6'; });
                    audio.addEventListener('ended', function () {
                        btn.textContent = '\u25B6';
                        fill.style.width = '0%';
                        time.textContent = fmt(total());
                    });
                    audio.addEventListener('timeupdate', function () {
                        const t = total();
                        if (t > 0) fill.style.width = Math.min(100, (audio.currentTime / t) * 100) + '%';
                        time.textContent = fmt(audio.currentTime) + ' / ' + fmt(t);
                    });
                    audio.addEventListener('durationchange', function () {
                        if (!known && isFinite(audio.duration) && audio.duration > 0) {
                            time.textContent = fmt(audio.duration);
                        }
                    });
                    bar.addEventListener('click', function (e) {
                        e.stopPropagation();
                        const t = total();
                        if (!t) return;
                        const r = bar.getBoundingClientRect();
                        audio.currentTime = ((e.clientX - r.left) / r.width) * t;
                    });

                    player.appendChild(btn);
                    player.appendChild(bar);
                    player.appendChild(time);
                    wrap.appendChild(player);
                    wrap.appendChild(audio);
                } else {
                    window.nadoSetTextWithLinks(wrap, msg.message);
                }

                const meta = document.createElement('div');
                meta.style.cssText = 'font-size:10px;opacity:.55;margin-top:3px;text-align:' + (isClient ? 'right' : 'left');
                meta.textContent = (isClient ? '' : (msg.label || 'NADO') + ' · ') + msg.time;

                const outer = document.createElement('div');
                outer.style.cssText = 'display:flex;flex-direction:column;align-items:' + (isClient ? 'flex-end' : 'flex-start') + ';margin-bottom:8px;';
                outer.appendChild(wrap); outer.appendChild(meta);
                chatBody.appendChild(outer);
                chatBody.scrollTop = chatBody.scrollHeight;
                if (sound) beep();
            }

            // ── Polling ─────────────────────────────────────────────────────
            function startPolling() {
                if (pollTimer) return;
                pollTimer = setInterval(function () {
                    if (!pollUrl || !isOpen) return;
                    fetch(pollUrl + '?since=' + lastMsgId, { headers: { Accept: 'application/json' } })
                        .then(r => r.json())
                        .then(msgs => {
                            msgs.forEach(m => {
                                if (!document.querySelector('[data-msg-id="' + m.id + '"]')) {
                                    appendBubble(m, true);
                                    lastMsgId = Math.max(lastMsgId, m.id);
                                }
                            });
                        }).catch(() => {});
                }, 4000);
            }
            function stopPolling() { clearInterval(pollTimer); pollTimer = null; }

            // ── Load discussion ─────────────────────────────────────────────
            function loadDiscussion() {
                authGate.style.display = 'none';
                loading.style.display = 'block';
                chatBody.style.display = chatFooter.style.display = closedNote.style.display = 'none';

                fetch(START_URL, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ annonce_id: ANNONCE_ID })
                })
                .then(r => {
    if (!r.ok) {
        if (r.status === 401) { authGate.style.display = 'block'; loading.style.display = 'none'; }
        // 422 = the client is the seller of this annonce: show the server's explanation
        if (r.status === 422) {
            return r.json().then(d => { const e = new Error('HTTP 422'); e.userMessage = d.error; throw e; });
        }
        throw new Error('HTTP ' + r.status);
    }
    return r.json();
})
                .then(data => {
                    loading.style.display = 'none';
                    discussionId = data.id; sendUrl = data.send_url; pollUrl = data.poll_url;

                    chatBody.innerHTML = '';
                    chatBody.style.display = 'flex';
                    chatBody.style.flexDirection = 'column';

                    if (data.messages && data.messages.length) {
                        data.messages.forEach(m => { appendBubble(m, false); lastMsgId = Math.max(lastMsgId, m.id); });
                    } else {
                        const hint = document.createElement('div');
                        hint.setAttribute('data-hint', '1');
                        hint.style.cssText = 'text-align:center;color:#aaa;font-size:13px;margin:auto;padding:2rem;';
                        hint.innerHTML = '<i class="fas fa-comments" style="font-size:2rem;display:block;margin-bottom:.5rem;"></i>Envoyez votre premier message.';
                        chatBody.appendChild(hint);
                    }

                    if (data.status === 'open') {
                        chatFooter.style.display = 'flex';
                        chatInput.focus();
                        startPolling();
                    } else {
                        closedNote.style.display = 'block';
                    }
                })
                .catch(err => {
                    loading.style.display = 'none';
                    chatBody.style.display = 'flex';
                    if (err && err.userMessage) {
                        const p = document.createElement('p');
                        p.style.cssText = 'color:#475569;padding:1rem;text-align:center;';
                        p.textContent = err.userMessage;
                        chatBody.innerHTML = '';
                        chatBody.appendChild(p);
                        return;
                    }
                    chatBody.innerHTML = '<p style="color:red;padding:1rem;">Erreur lors du chargement. Réessayez.</p>';
                });
            }

            // ── Send text ──────────────────────────────────────────────────
            function doSend() {
                if (!chatInput || !sendUrl) return;
                const text = chatInput.value.trim();
                if (!text) return;
                chatInput.value = ''; chatInput.disabled = true; sendBtn.disabled = true;

                fetch(sendUrl, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                    body: JSON.stringify({ message: text })
                })
                .then(r => r.json())
                .then(msg => { appendBubble(msg, false); lastMsgId = Math.max(lastMsgId, msg.id); })
                .finally(() => { chatInput.disabled = false; sendBtn.disabled = false; chatInput.focus(); });
            }

            sendBtn?.addEventListener('click', doSend);
            chatInput?.addEventListener('keydown', e => { if (e.key === 'Enter') { e.preventDefault(); doSend(); } });

            // ── Voice recorder ──────────────────────────────────────────────
            if (micBtn) {
                micBtn.addEventListener('click', async function () {
                    if (!sendUrl) { alert('Discussion non chargée.'); return; }

                    // Stop ongoing recording
                    if (mediaRec && mediaRec.state === 'recording') {
                        try { mediaRec.requestData(); } catch(e) {}
                        mediaRec.stop();
                        return;
                    }

                    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                        alert('Votre navigateur ne supporte pas l\'enregistrement audio.');
                        return;
                    }
                    if (typeof MediaRecorder === 'undefined') {
                        alert('MediaRecorder non disponible dans ce navigateur.');
                        return;
                    }

                    let stream;
                    try {
                        stream = await navigator.mediaDevices.getUserMedia({ audio: true });
                    } catch (err) {
                        console.error('[REC] mic error:', err);
                        alert('Microphone non accessible: ' + err.message);
                        return;
                    }

                    // Pick a supported MIME type
                    let mime = '', ext = 'webm';
                    const candidates = [
                        ['audio/webm;codecs=opus', 'webm'],
                        ['audio/webm', 'webm'],
                        ['audio/ogg;codecs=opus', 'ogg'],
                        ['audio/mp4', 'mp4']
                    ];
                    for (const [m, e] of candidates) {
                        if (MediaRecorder.isTypeSupported(m)) { mime = m; ext = e; break; }
                    }
                    console.log('[REC] selected mime:', mime || '(default)');

                    audioChunks = []; recSeconds = 0;

                    try {
                        mediaRec = mime ? new MediaRecorder(stream, { mimeType: mime }) : new MediaRecorder(stream);
                    } catch (err) {
                        console.error('[REC] MediaRecorder ctor error:', err);
                        alert('Erreur création recorder: ' + err.message);
                        stream.getTracks().forEach(t => t.stop());
                        return;
                    }
                    console.log('[REC] recorder mimeType:', mediaRec.mimeType);

                    mediaRec.addEventListener('dataavailable', function (e) {
                        console.log('[REC] data chunk size:', e.data && e.data.size);
                        if (e.data && e.data.size > 0) audioChunks.push(e.data);
                    });

                    mediaRec.addEventListener('stop', async function () {
                        console.log('[REC] stop fired, chunks:', audioChunks.length);
                        micBtn.classList.remove('recording');
                        if (recTimer) recTimer.style.display = 'none';
                        clearInterval(recInterval);
                        stream.getTracks().forEach(t => t.stop());

                        // Wait one tick to ensure last dataavailable has flushed
                        await new Promise(r => setTimeout(r, 50));

                        if (!audioChunks.length) {
                            alert('Aucun audio capté. Vérifiez votre micro et réessayez.');
                            return;
                        }

                        const blobType = mediaRec.mimeType || mime || 'audio/webm';
                        const blob = new Blob(audioChunks, { type: blobType });
                        console.log('[REC] blob size:', blob.size, 'type:', blob.type);

                        if (blob.size === 0) {
                            alert('Enregistrement vide (0 octets).');
                            return;
                        }

                        const fd = new FormData();
                        fd.append('audio', blob, 'voice.' + ext);
                        // We counted the seconds while recording; send it so the UI never has
                        // to wait for the browser to parse a duration-less WebM header.
                        fd.append('duration', String(recSeconds || 0));

                        try {
                            const r = await fetch(sendUrl, {
                                method: 'POST',
                                headers: { 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                                body: fd
                            });
                            console.log('[REC] response status:', r.status);
                            if (!r.ok) {
                                const txt = await r.text();
                                console.error('[REC] server error body:', txt);
                                alert('Erreur serveur (' + r.status + '). Voir console.');
                                return;
                            }
                            const msg = await r.json();
                            console.log('[REC] message saved:', msg);
                            appendBubble(msg, false, URL.createObjectURL(blob));
                            lastMsgId = Math.max(lastMsgId, msg.id);
                        } catch (err) {
                            console.error('[REC] upload failed:', err);
                            alert('Échec d\'envoi: ' + err.message);
                        }
                    });

                    // Start with timeslice — emits dataavailable every 250ms
                    mediaRec.start(250);
                    console.log('[REC] started, state:', mediaRec.state);

                    micBtn.classList.add('recording');
                    if (recTimer) { recTimer.style.display = 'block'; recSec.textContent = '0'; }
                    recInterval = setInterval(() => { if (recSec) recSec.textContent = ++recSeconds; }, 1000);
                });
            }

            // ── Open / close ────────────────────────────────────────────────
            window.openFinanceChat = function () {
                overlay.classList.add('open');
                document.body.style.overflow = 'hidden';
                isOpen = true;
                if (!IS_AUTH) { authGate.style.display = 'block'; return; }
                if (!discussionId) { loadDiscussion(); } else { chatInput?.focus(); startPolling(); }
            };

            // ── Finance simulator -> discussion ─────────────────────────────
            // Opens the chat, posts the simulation as a normal client message, then shows
            // an immediate bot reply with the monthly payment and the required documents.
            window.sendFinanceSummary = function (text, botLines) {
                openFinanceChat();
                if (!IS_AUTH) return;          // auth gate is already showing

                let attempts = 0;
                (function trySend() {
                    if (!sendUrl) {            // discussion still loading
                        if (attempts++ < 40) setTimeout(trySend, 250);
                        return;
                    }
                    fetch(sendUrl, {
                        method: 'POST',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'Accept': 'application/json' },
                        body: JSON.stringify({ message: text })
                    })
                    .then(r => r.json())
                    .then(msg => {
                        appendBubble(msg, false);
                        if (msg && msg.id) lastMsgId = Math.max(lastMsgId, msg.id);
                        if (botLines) {
                            setTimeout(function () {
                                appendBubble({
                                    id: 'bot-' + Date.now(),
                                    sender_type: 'admin',
                                    type: 'text',
                                    message: botLines,
                                    time: new Date().toTimeString().slice(0, 5)
                                }, true);
                                appendBotActions();
                            }, 600);
                        }
                    })
                    .catch(function () {
                        alert("Impossible d'envoyer la simulation. Reessayez.");
                    });
                })();
            };

            // Chat bubbles are rendered with textContent, so a pasted URL would not be
            // clickable. Render real buttons instead, under the bot reply.
            function appendBotActions() {
                if (!chatBody) return;

                const row = document.createElement('div');
                row.style.cssText = 'display:flex;flex-wrap:wrap;gap:8px;margin:0 0 12px 0;align-self:flex-start;max-width:85%;';

                const mk = function (href, label, bg, svg) {
                    const a = document.createElement('a');
                    a.href = href;
                    a.target = '_blank';
                    a.rel = 'noopener noreferrer';
                    a.innerHTML = svg + '<span>' + label + '</span>';
                    a.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:9px 14px;border-radius:999px;' +
                        'background:' + bg + ';color:#fff;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;';
                    return a;
                };

                const waIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">' +
                    '<path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.79-1.47-1.76-1.64-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.01-1.04 2.470 0 1.46 1.06 2.87 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.69.25-1.28.17-1.41-.07-.12-.27-.2-.57-.35z"/>' +
                    '<path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm0 18.15c-1.53 0-3.03-.41-4.34-1.19l-.31-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.34c0-4.54 3.7-8.23 8.24-8.23 4.54 0 8.23 3.69 8.23 8.23 0 4.54-3.69 8.24-8.23 8.24z"/></svg>';

                const pinIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
                    'stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                    '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';

                row.appendChild(mk('https://wa.me/212770080009', 'WhatsApp 07 70 08 00 09', '#25D366', waIcon));
                row.appendChild(mk('https://maps.app.goo.gl/RuP9P8wv898XNAAJ9', 'Voir l\'agence', '#1459DD', pinIcon));

                chatBody.appendChild(row);
                chatBody.scrollTop = chatBody.scrollHeight;
            }

            window.closeFinanceChat = function () {
                overlay.classList.remove('open');
                document.body.style.overflow = 'unset';
                isOpen = false;
                stopPolling();
            };

            overlay?.addEventListener('click', e => { if (e.target === overlay) closeFinanceChat(); });
        })();
        // ── Documents required per borrower profile ──────────────────────────
        // Same source of truth as the /financement page ("Pièces à fournir").
        // Keep the two lists in sync if the financing partners change them.
        const PROFILE_DOCS = {
            'Salarié(e) du privé': [
                "Copie de la Carte d'identité nationale",
                "Spécimen de chèque ou Attestation de RIB",
                "3 derniers bulletins de salaire",
                "Attestation de salaire",
                "Attestation de travail",
                "Attestation CNSS",
                "Justificatif d'adresse (moins de 3 mois)",
                "3 derniers relevés bancaires"
            ],
            'Fonctionnaire': [
                "Copie de la Carte d'identité nationale",
                "Spécimen de chèque ou Attestation de RIB",
                "Justificatif d'adresse (moins de 3 mois)",
                "3 derniers relevés bancaires",
                "État d'engagement récent ou attestation de salaire"
            ],
            'Retraité(e)': [
                "Copie de la Carte d'identité nationale",
                "Spécimen de chèque ou Attestation de RIB",
                "Dernière attestation de pension",
                "Justificatif d'adresse (moins de 3 mois)",
                "3 derniers relevés bancaires"
            ],
            'Profession libérale': [
                "Copie de la Carte d'identité nationale",
                "Spécimen de chèque ou Attestation de RIB",
                "Copie de la carte professionnelle",
                "Justificatif d'adresse (moins de 3 mois)",
                "3 derniers relevés bancaires"
            ],
            'Commerçant': [
                "Copie de la Carte d'identité nationale",
                "Spécimen de chèque ou Attestation de RIB",
                "Registre du commerce (Modèle J)",
                "Justificatif d'adresse (moins de 3 mois)",
                "3 derniers relevés bancaires"
            ],
            'Entreprise': [
                "Copie de la Carte d'identité nationale du gérant",
                "Spécimen de chèque ou Attestation de RIB de l'entreprise",
                "Statuts de l'entreprise",
                "Justificatif d'adresse du/des gérant(s) (moins de 3 mois)",
                "3 derniers relevés bancaires de l'entreprise",
                "Registre du commerce (Modèle J) récent (moins de 3 mois)"
            ],
            'Ancien client': [
                "Copie de la Carte d'identité nationale",
                "Dernier relevé bancaire",
                "Dernier bulletin de paie"
            ]
        };

        // Profile names come from the DB ($clientTypes) and may not match the keys above
        // character for character, so compare on a normalised form (no accents/punctuation).
        function normalizeProfile(name) {
            return (name || '')
                .normalize('NFD').replace(/[̀-ͯ]/g, '')
                .toLowerCase().replace(/[^a-z0-9]/g, '');
        }

        function docsForProfile(name) {
            const target = normalizeProfile(name);
            if (!target) return null;
            for (const key in PROFILE_DOCS) {
                if (normalizeProfile(key) === target) return PROFILE_DOCS[key];
            }
            // Partial match ("Salarié" vs "Salarié(e) du privé")
            for (const key in PROFILE_DOCS) {
                const k = normalizeProfile(key);
                if (k.indexOf(target) === 0 || target.indexOf(k) === 0) return PROFILE_DOCS[key];
            }
            return null;
        }

        // ── "Discuter de cette offre" : send the simulation into the discussion ──
        function startFinanceDiscussion() {
            const nf = function (n) { return new Intl.NumberFormat('fr-FR').format(Math.round(n || 0)); };

            const profilBtn = document.querySelector('.profile-btn.active');
            const profil    = profilBtn ? profilBtn.textContent.trim() : '';
            const taux      = profilBtn ? (profilBtn.dataset.taux || '') : '';

            const apport   = parseInt(document.getElementById('apport-range')?.value || 0, 10);
            const duree    = (typeof currentDuree !== 'undefined') ? currentDuree : 60;
            const mensual  = (document.getElementById('monthly-payment')?.textContent || '0').trim();
            const vehicule = @json(trim(($annonce->brand->name ?? '') . ' ' . ($annonce->modeleRelation->name ?? '')) ?: ($annonce->titre ?: 'Vehicule'));
            const ref      = 'VC' + {{ $annonce->id }};
            const prix     = VEHICLE_PRICE;
            const montant  = Math.max(0, prix - apport);

            const docs = docsForProfile(profil);

            let docsBlock;
            if (!profil) {
                docsBlock =
                    'Selectionnez votre profil dans le simulateur pour obtenir\n' +
                    'la liste exacte des pieces a fournir.';
            } else if (docs) {
                docsBlock =
                    'PIECES A FOURNIR - Profil : ' + profil + '\n' +
                    docs.map(function (d) { return '- ' + d; }).join('\n') + '\n' +
                    '\n' +
                    'Les originaux doivent etre presentes a la signature.';
            } else {
                docsBlock =
                    'PIECES A FOURNIR - Profil : ' + profil + '\n' +
                    'Un conseiller vous communiquera la liste exacte des pieces.';
            }

            const recapText =
                'Voici le recapitulatif de votre simulation :\n' +
                '\n' +
                'Vehicule : ' + vehicule + ' (Ref: ' + ref + ')\n' +
                'Prix : ' + nf(prix) + ' MAD\n' +
                'Profil : ' + (profil || 'Non precise') + '\n' +
                'Apport initial : ' + nf(apport) + ' MAD\n' +
                'Montant a financer : ' + nf(montant) + ' MAD\n' +
                'Duree du credit : ' + duree + ' mois\n' +
                '\n' +
                'MENSUALITE ESTIMEE : ' + mensual + ' MAD / mois\n' +
                '(Offre indicative, soumise a acceptation du dossier.)';

            // The simulator has its OWN bot panel. It must not post into the
            // "Discussion avec le vendeur" thread, which is a real conversation.
            if (typeof window.showFinanceBot === 'function') {
                window.showFinanceBot(recapText, docsBlock);
            }
        }
        window.startFinanceDiscussion = startFinanceDiscussion;

        const VEHICLE_PRICE = {{ $annonce->prix }};
        let currentDuree = 60;
        let currentTaux = {{ $tauxInteretDefault ?? 13 }};

        const apportRange = document.getElementById('apport-range');
        const apportVal = document.getElementById('apport-val');
        const dureeVal = document.getElementById('duree-val');
        const monthlyText = document.getElementById('monthly-payment');
        const durationBtns = document.querySelectorAll('.duration-btn');
        const profileBtns = document.querySelectorAll('.profile-btn');

        function calculateFinance() {
            const apport = parseInt(apportRange?.value || 0);
            const capital = VEHICLE_PRICE - apport;
            const monthlyRate = currentTaux / 100 / 12;
            let monthly = 0;
            if (monthlyRate > 0) {
                monthly = (capital * monthlyRate) / (1 - Math.pow(1 + monthlyRate, -currentDuree));
            } else {
                monthly = capital / currentDuree;
            }
            if (apportVal) apportVal.innerText = apport.toLocaleString('fr-FR') + ' DH';
            if (dureeVal) dureeVal.innerText = currentDuree + ' mois';
            if (monthlyText) monthlyText.innerText = Math.round(monthly).toLocaleString('fr-FR');

            // Update slider fill
            updateSliderFill();
        }

        function updateSliderFill() {
            if (!apportRange) return;
            const min = parseInt(apportRange.min) || 0;
            const max = parseInt(apportRange.max) || 100;
            const val = parseInt(apportRange.value) || 0;
            const percentage = ((val - min) / (max - min)) * 100;
            apportRange.style.background =
                `linear-gradient(to right, #1459DD 0%, #1459DD ${percentage}%, #e2e8f0 ${percentage}%, #e2e8f0 100%)`;
        }

        apportRange?.addEventListener('input', calculateFinance);
        durationBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                durationBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentDuree = parseInt(btn.getAttribute('data-months'));
                calculateFinance();
            });
        });
        profileBtns.forEach(btn => {
            btn.addEventListener('click', () => {
                profileBtns.forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                currentTaux = parseFloat(btn.getAttribute('data-taux')) || {{ $tauxInteretDefault ?? 13 }};
                calculateFinance();
            });
        });

        calculateFinance();

        // Success Message
        @if (session('success'))
            Swal.fire({
                icon: 'success',
                title: "{{ session('success') }}",
                showConfirmButton: false,
                timer: 2000
            });
        @endif

        // ── Faire une offre ──────────────────────────────────────────────────
        function toggleOfferPanel() {
            var panel = document.getElementById('offerPanel');
            if (!panel) return;
            var isVisible = panel.style.display !== 'none';
            panel.style.display = isVisible ? 'none' : 'block';
            if (!isVisible) {
                var input   = document.getElementById('offerInput');
                var success = document.getElementById('offerSuccess');
                var error   = document.getElementById('offerError');
                var form    = document.getElementById('offerForm');
                if (input)   input.value = '';
                if (success) success.style.display = 'none';
                if (error)   error.style.display = 'none';
                if (input)   setTimeout(function(){ input.focus(); }, 50);
            }
        }

    </script>
<!-- ── SIMULATOR BOT (separate from "Discussion avec le vendeur") ── -->
<div id="financeBotOverlay"
    style="display:none;position:fixed;inset:0;z-index:99998;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);align-items:center;justify-content:center;padding:20px;">
    <div
        style="background:#fff;width:100%;max-width:440px;max-height:88vh;border-radius:20px;overflow:hidden;display:flex;flex-direction:column;box-shadow:0 25px 60px rgba(0,0,0,.35);">

        <div
            style="display:flex;align-items:center;gap:10px;padding:16px 18px;border-bottom:1px solid #e2e8f0;background:#f8fafc;">
            <div
                style="width:36px;height:36px;border-radius:50%;background:#1459DD;color:#fff;display:flex;align-items:center;justify-content:center;font-weight:900;font-size:13px;flex-shrink:0;">
                N</div>
            <div style="flex:1;min-width:0;">
                <div style="font-weight:800;color:#0f172a;font-size:14px;line-height:1.2;">Assistant financement</div>
                <div style="font-size:11px;color:#64748b;">Simulateur NADO</div>
            </div>
            <button type="button" onclick="closeFinanceBot()" aria-label="Fermer"
                style="width:34px;height:34px;border:0;border-radius:50%;background:#e2e8f0;color:#475569;cursor:pointer;font-size:20px;line-height:1;display:flex;align-items:center;justify-content:center;">&times;</button>
        </div>

        <div id="financeBotBody"
            style="flex:1;overflow-y:auto;padding:18px;display:flex;flex-direction:column;gap:10px;background:#fff;">
        </div>

        <div
            style="border-top:1px solid #e2e8f0;padding:12px 14px;background:#f8fafc;display:flex;gap:8px;align-items:center;">
            <input id="financeBotInput" type="text" autocomplete="off"
                placeholder="Posez votre question (prix, options...)"
                style="flex:1;min-width:0;border:1.5px solid #cbd5e1;border-radius:999px;padding:10px 16px;font-size:13px;outline:none;font-family:inherit;">
            <button type="button" id="financeBotSend" aria-label="Envoyer"
                style="flex-shrink:0;width:38px;height:38px;border:0;border-radius:50%;background:#1459DD;color:#fff;cursor:pointer;display:flex;align-items:center;justify-content:center;">
                <svg width="17" height="17" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                    stroke-linecap="round" stroke-linejoin="round">
                    <line x1="22" y1="2" x2="11" y2="13" />
                    <polygon points="22 2 15 22 11 13 2 9 22 2" />
                </svg>
            </button>
        </div>
    </div>
</div>

<script>
    (function () {
        const botOverlay = document.getElementById('financeBotOverlay');
        const botBody = document.getElementById('financeBotBody');

        function scroll() {
            if (botBody) botBody.scrollTop = botBody.scrollHeight;
        }

        // Client bubble (right, blue) / bot bubble (left, grey) - same look as the
        // vendor chat, but this panel never touches that conversation.
        function bubble(text, fromClient) {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;flex-direction:column;align-items:' +
                (fromClient ? 'flex-end' : 'flex-start') + ';';

            const b = document.createElement('div');
            window.nadoSetTextWithLinks(b, text);
            b.style.cssText = fromClient
                ? 'background:#1459DD;color:#fff;border-radius:18px 18px 4px 18px;padding:12px 14px;' +
                  'font-size:13.5px;line-height:1.55;white-space:pre-line;overflow-wrap:anywhere;max-width:88%;'
                : 'background:#f1f5f9;color:#0f172a;border-radius:18px 18px 18px 4px;padding:12px 14px;' +
                  'font-size:13.5px;line-height:1.55;white-space:pre-line;overflow-wrap:anywhere;max-width:88%;';

            const meta = document.createElement('div');
            meta.textContent = (fromClient ? '' : 'NADO Bot · ') +
                new Date().toTimeString().slice(0, 5);
            meta.style.cssText = 'font-size:10px;opacity:.55;margin-top:3px;';

            row.appendChild(b);
            row.appendChild(meta);
            botBody.appendChild(row);
            scroll();
            return row;
        }

        // Three animated dots while the bot "types".
        function typing() {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;align-self:flex-start;';
            row.innerHTML =
                '<div style="background:#f1f5f9;border-radius:18px 18px 18px 4px;padding:14px 16px;display:flex;gap:4px;">' +
                '<span class="fb-dot"></span><span class="fb-dot"></span><span class="fb-dot"></span></div>';
            botBody.appendChild(row);
            scroll();
            return row;
        }

        function actions() {
            const row = document.createElement('div');
            row.style.cssText = 'display:flex;flex-wrap:wrap;gap:8px;align-self:flex-start;max-width:100%;margin-top:2px;';

            const mk = function (href, label, bg, svg) {
                const a = document.createElement('a');
                a.href = href;
                a.target = '_blank';
                a.rel = 'noopener noreferrer';
                a.innerHTML = svg + '<span>' + label + '</span>';
                a.style.cssText = 'display:inline-flex;align-items:center;gap:6px;padding:10px 15px;border-radius:999px;' +
                    'background:' + bg + ';color:#fff;font-size:12px;font-weight:700;text-decoration:none;white-space:nowrap;';
                return a;
            };

            const waIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor">' +
                '<path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.45 1.32 4.95L2 22l5.25-1.38c1.45.79 3.08 1.21 4.79 1.21 5.46 0 9.91-4.45 9.91-9.91C21.95 6.45 17.5 2 12.04 2zm0 18.15c-1.53 0-3.03-.41-4.34-1.19l-.31-.18-3.12.82.83-3.04-.2-.31a8.2 8.2 0 0 1-1.26-4.34c0-4.54 3.7-8.23 8.24-8.23 4.54 0 8.23 3.69 8.23 8.23 0 4.54-3.69 8.24-8.23 8.24z"/>' +
                '<path d="M17.47 14.38c-.3-.15-1.75-.86-2.02-.96-.27-.1-.47-.15-.67.15-.2.3-.77.96-.94 1.16-.17.2-.35.22-.64.07-.3-.15-1.25-.46-2.38-1.47-.88-.79-1.47-1.76-1.64-2.06-.17-.3-.02-.46.13-.61.13-.13.3-.35.45-.52.15-.17.2-.3.3-.5.1-.2.05-.37-.02-.52-.08-.15-.67-1.6-.92-2.2-.24-.58-.49-.5-.67-.51h-.57c-.2 0-.52.07-.79.37-.27.3-1.04 1.01-1.04 2.47 0 1.46 1.06 2.87 1.21 3.07.15.2 2.1 3.2 5.08 4.49.71.3 1.26.49 1.69.63.71.22 1.36.19 1.87.12.57-.09 1.75-.72 2-1.41.25-.69.25-1.28.17-1.41-.07-.12-.27-.2-.57-.35z"/></svg>';

            const pinIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
                'stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                '<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>';

            const chatIcon = '<svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" ' +
                'stroke-width="2" stroke-linecap="round" stroke-linejoin="round">' +
                '<path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>';

            row.appendChild(mk('https://wa.me/212770080009', 'WhatsApp 07 70 08 00 09', '#25D366', waIcon));
            row.appendChild(mk('https://maps.app.goo.gl/RuP9P8wv898XNAAJ9', 'Voir l\'agence', '#1459DD', pinIcon));

            botBody.appendChild(row);
            scroll();
        }

        // Plays the conversation: client question, then bot answers one after another
        // with a typing indicator in between.
        function say(steps, i) {
            i = i || 0;
            if (i >= steps.length) { actions(); return; }

            const step = steps[i];
            const dots = typing();
            setTimeout(function () {
                dots.remove();
                bubble(step, false);
                setTimeout(function () { say(steps, i + 1); }, 250);
            }, i === 0 ? 700 : 900);
        }

        // ── Vehicle facts the assistant can answer from ──────────────────────
        const BOT_DATA = {
            status:      @json(strtolower(trim($annonce->status ?? 'disponible'))),
            prix:        {{ (float) ($annonce->prix ?? 0) }},
            vehicule:    @json(trim(($annonce->brand->name ?? '') . ' ' . ($annonce->modeleRelation->name ?? '')) ?: ($annonce->titre ?: 'Ce vehicule')),
            annee:       @json($annonce->year ?? null),
            km:          {{ (float) ($annonce->km ?? 0) }},
            carburant:   @json($annonce->carburant ?? null),
            boite:       @json($annonce->box ?? $annonce->boite_vitesse ?? null),
            ville:       @json($annonce->city ?? null),
            equipements: @json(array_values((array) ($equipements ?? []))),
        };

        const nfMad = function (n) {
            return new Intl.NumberFormat('fr-FR').format(Math.round(n || 0)) + ' MAD';
        };

        function isSold() {
            const s = (BOT_DATA.status || '').toLowerCase();
            return s.indexOf('vendu') !== -1 || s.indexOf('sold') !== -1;
        }

        function soldMessage() {
            return 'Ce vehicule a ete VENDU.\n' +
                BOT_DATA.vehicule + ' n\'est plus disponible a la vente.\n' +
                '\n' +
                'Nous avons d\'autres vehicules similaires : contactez-nous sur WhatsApp\n' +
                'et un conseiller vous proposera des alternatives.';
        }

        function norm(t) {
            return (t || '')
                .normalize('NFD').replace(/[̀-ͯ]/g, '')
                .toLowerCase();
        }

        function hasAny(t, words) {
            for (let i = 0; i < words.length; i++) {
                if (t.indexOf(words[i]) !== -1) return true;
            }
            return false;
        }

        function answerFor(question) {
            const q = norm(question);

            // Price
            if (hasAny(q, ['prix', 'price', 'combien', 'coute', 'cout', 'tarif', 'montant', 'cher'])) {
                let a = 'PRIX : ' + nfMad(BOT_DATA.prix) + '\n' + BOT_DATA.vehicule;
                if (BOT_DATA.annee) a += ' - ' + BOT_DATA.annee;
                if (BOT_DATA.km) a += ' - ' + new Intl.NumberFormat('fr-FR').format(BOT_DATA.km) + ' km';
                if (isSold()) a += '\n\nAttention : ce vehicule est deja VENDU.';
                return a;
            }

            // Options / equipment
            if (hasAny(q, ['option', 'equipement', 'accessoire', 'confort', 'securite', 'finition', 'serie'])) {
                const eq = BOT_DATA.equipements || [];
                if (!eq.length) return 'Aucun equipement n\'est liste pour ce vehicule.\nUn conseiller peut vous detailler les options sur WhatsApp.';
                return 'OPTIONS ET EQUIPEMENTS (' + eq.length + ') :\n' +
                    eq.map(function (e) { return '- ' + e; }).join('\n');
            }

            // Availability
            if (hasAny(q, ['dispo', 'disponible', 'vendu', 'reserve', 'encore la', 'toujours'])) {
                return isSold()
                    ? soldMessage()
                    : BOT_DATA.vehicule + ' est DISPONIBLE.' +
                      (BOT_DATA.ville ? '\nVisible a ' + BOT_DATA.ville + '.' : '');
            }

            // Location
            if (hasAny(q, ['adresse', 'localisation', 'location', 'agence', 'ou se trouve', 'ou etes', 'situe', 'map', 'itineraire', 'venir'])) {
                return 'NOTRE AGENCE :\nhttps://maps.app.goo.gl/RuP9P8wv898XNAAJ9' +
                    (BOT_DATA.ville ? '\nVehicule visible a ' + BOT_DATA.ville + '.' : '');
            }

            // Documents
            if (hasAny(q, ['document', 'papier', 'dossier', 'piece', 'justificatif'])) {
                const profilBtn = document.querySelector('.profile-btn.active');
                const profil = profilBtn ? profilBtn.textContent.trim() : '';
                const docs = (typeof docsForProfile === 'function') ? docsForProfile(profil) : null;
                if (docs) {
                    return 'PIECES A FOURNIR - Profil : ' + profil + '\n' +
                        docs.map(function (d) { return '- ' + d; }).join('\n');
                }
                return 'Selectionnez votre profil dans le simulateur pour obtenir\nla liste exacte des pieces a fournir.';
            }

            // Vehicle specs
            if (hasAny(q, ['kilometr', 'km', 'annee', 'carburant', 'diesel', 'essence', 'boite', 'transmission', 'motorisation'])) {
                let a = BOT_DATA.vehicule + '\n';
                if (BOT_DATA.annee) a += 'Annee : ' + BOT_DATA.annee + '\n';
                if (BOT_DATA.km) a += 'Kilometrage : ' + new Intl.NumberFormat('fr-FR').format(BOT_DATA.km) + ' km\n';
                if (BOT_DATA.carburant) a += 'Carburant : ' + BOT_DATA.carburant + '\n';
                if (BOT_DATA.boite) a += 'Boite : ' + BOT_DATA.boite + '\n';
                if (BOT_DATA.ville) a += 'Ville : ' + BOT_DATA.ville;
                return a.trim();
            }

            // Financing
            if (hasAny(q, ['credit', 'financ', 'mensualite', 'apport', 'duree', 'simulation'])) {
                const mens = (document.getElementById('monthly-payment')?.textContent || '').trim();
                return mens
                    ? 'Selon votre simulation actuelle :\nMENSUALITE ESTIMEE : ' + mens + ' MAD / mois\n' +
                      '(Offre indicative, soumise a acceptation du dossier.)\n' +
                      'Ajustez l\'apport et la duree dans le simulateur pour recalculer.'
                    : 'Utilisez le simulateur pour estimer votre mensualite.';
            }

            return 'Je peux vous renseigner sur le PRIX, les OPTIONS, la DISPONIBILITE,\n' +
                'les CARACTERISTIQUES, les DOCUMENTS de financement et l\'ADRESSE de l\'agence.\n' +
                'Pour toute autre question, un conseiller vous repond sur WhatsApp.';
        }

        function askBot(question) {
            if (!question) return;
            bubble(question, true);
            const dots = typing();
            setTimeout(function () {
                dots.remove();
                bubble(answerFor(question), false);
            }, 700);
        }

        const botInput = document.getElementById('financeBotInput');
        const botSend = document.getElementById('financeBotSend');

        function submitBotQuestion() {
            if (!botInput) return;
            const v = botInput.value.trim();
            if (!v) return;
            botInput.value = '';
            askBot(v);
        }

        botSend?.addEventListener('click', submitBotQuestion);
        botInput?.addEventListener('keydown', function (e) {
            if (e.key === 'Enter') { e.preventDefault(); submitBotQuestion(); }
        });
        window.showFinanceBot = function (recapText, docsText) {
            if (!botOverlay || !botBody) return;

            botBody.innerHTML = '';
            botOverlay.style.display = 'flex';
            document.body.style.overflow = 'hidden';

            bubble('Je souhaite financer ce vehicule. Quelle est ma mensualite et quels documents dois-je fournir ?', true);

            // A sold car leads with that fact - no point quoting a financing plan for it.
            const steps = [];
            if (isSold()) {
                steps.push(soldMessage());
            } else {
                steps.push(recapText);
                if (docsText) steps.push(docsText);
            }
            steps.push('Vous pouvez aussi me poser une question : prix, options, disponibilite,\n' +
                'caracteristiques ou documents.');

            say(steps, 0);
            setTimeout(function () { botInput?.focus(); }, 400);
        };

        window.closeFinanceBot = function () {
            if (!botOverlay) return;
            botOverlay.style.display = 'none';
            document.body.style.overflow = 'unset';
        };

        botOverlay?.addEventListener('click', function (e) {
            if (e.target === botOverlay) closeFinanceBot();
        });
        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape' && botOverlay && botOverlay.style.display === 'flex') closeFinanceBot();
        });
    })();
</script>
<style>
    .fb-dot {
        width: 7px;
        height: 7px;
        border-radius: 50%;
        background: #94a3b8;
        display: inline-block;
        animation: fbBlink 1.2s infinite ease-in-out;
    }
    .fb-dot:nth-child(2) { animation-delay: .2s; }
    .fb-dot:nth-child(3) { animation-delay: .4s; }
    @keyframes fbBlink {
        0%, 80%, 100% { opacity: .3; transform: translateY(0); }
        40%           { opacity: 1;  transform: translateY(-3px); }
    }
</style>

</body>


</html>
