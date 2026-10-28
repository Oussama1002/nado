<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home | Nado.ma</title>
    <script src="https://unpkg.com/lucide@latest"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @include('partials.locale-head')
    <!-- Owl Carousel CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <style>
        :root {
            --primary: #1459DD;
            --secondary: #FDD817;
            --slate-50: #f8fafc;
            --slate-100: #f1f5f9;
            --slate-200: #e2e8f0;
            --slate-300: #cbd5e1;
            --slate-400: #94a3b8;
            --slate-500: #64748b;
            --slate-600: #475569;
            --slate-800: #1e293b;
            --slate-900: #0f172a;
            --white: #ffffff;
            --black: #000000;
        }

        /* Global Reset & Base Styles */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--white);
            color: var(--slate-900);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        ul {
            list-style: none;
        }

        button {
            border: none;
            background: none;
            cursor: pointer;
            font-family: inherit;
        }

        img {
            max-width: 100%;
            display: block;
        }

        /* Utilities */
        .container {
            max-width: 80rem;
            /* 7xl equivalent */
            margin: 0 auto;
            padding: 0 1rem;
        }

        .flex {
            display: flex;
        }

        .flex-col {
            flex-direction: column;
        }

        .items-center {
            align-items: center;
        }

        .justify-between {
            justify-content: space-between;
        }

        .justify-center {
            justify-content: center;
        }

        .gap-2 {
            gap: 0.5rem;
        }

        .gap-4 {
            gap: 1rem;
        }

        .gap-6 {
            gap: 1.5rem;
        }

        .gap-10 {
            gap: 2.5rem;
        }

        .hidden {
            display: none;
        }

        @media (min-width: 640px) {
            .sm\:flex {
                display: flex;
            }

            .sm\:hidden {
                display: none;
            }
        }

        @media (min-width: 1024px) {
            .lg\:flex {
                display: flex;
            }

            .lg\:hidden {
                display: none;
            }

            .lg\:grid-cols-2 {
                grid-template-columns: repeat(2, 1fr);
            }

            .lg\:col-span-8 {
                grid-column: span 8;
            }

            .lg\:col-span-4 {
                grid-column: span 4;
            }
        }

        .text-primary {
            color: var(--primary);
        }

        .text-secondary {
            color: var(--secondary);
        }

        .text-white {
            color: var(--white);
        }

        .bg-white {
            background-color: var(--white);
        }

        .uppercase {
            text-transform: uppercase;
        }

        .font-black {
            font-weight: 900;
        }

        .font-bold {
            font-weight: 700;
        }

        /* Custom Scrollbar */
        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }

        .no-scrollbar {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        /* Animations */
        @keyframes infinite-scroll {
            from {
                transform: translateX(0);
            }

            to {
                transform: translateX(-50%);
            }
        }

        .animate-infinite-scroll {
            animation: infinite-scroll 35s linear infinite;
        }

        .animate-pulse {
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



        /* HERO SECTION */
        .hero {
            position: relative;
            min-height: 80vh;
            display: flex;
            align-items: center;
            overflow: hidden;
            /* Linear background behind everything; the photo only covers the right side. */
            background: linear-gradient(115deg, #ffffff 0%, #fbfcfe 42%, #f4f7fb 72%, #eef2f8 100%);
        }

        .hero-bg {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: auto;
            width: 62%;
            z-index: 0;
            /* Fade the photo's left edge into the gradient so the text stays readable
               without needing a heavy dark scrim on top of it. */
            -webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, .45) 14%, #000 42%);
            mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, .45) 14%, #000 42%);
        }

        .hero-bg img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center right;
            opacity: 1;
        }

        /* Hero Slider Styles */
        .hero-slider {
            width: 100%;
            height: 100%;
        }

        .hero-slider .owl-stage-outer,
        .hero-slider .owl-stage,
        .hero-slider .owl-item,
        .hero-slider .item {
            height: 100%;
        }

        .hero-slider .item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center right;
            opacity: 1;
        }

        /* Scrim over the photo only: darkens its left half so the headline never sits
           on a bright part of the image. */
        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to right,
                    rgba(255, 255, 255, .92) 0%,
                    rgba(255, 255, 255, .55) 35%,
                    rgba(255, 255, 255, .15) 70%,
                    rgba(255, 255, 255, .30) 100%);
        }

        .hero-content {
            position: relative;
            z-index: 20;
            width: 100%;
            padding: 0 1.5rem;
        }

        /* Keep the copy in the left half, clear of the photo. */
        @media (min-width: 1024px) {
            .hero-content > * {
                max-width: 560px;
            }
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.25rem 0.75rem;
            /* A white pill is invisible on the light hero: use a subtle slate chip. */
            background-color: rgba(15, 23, 42, 0.05);
            border: 1px solid rgba(15, 23, 42, 0.12);
            border-radius: 9999px;
            margin-bottom: 1.5rem;
            box-shadow: 0 6px 16px rgba(15, 23, 42, 0.06);
        }

        .hero-badge span {
            color: var(--slate-900);
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.3em;
        }

        .hero-title {
            font-size: 3.75rem;
            font-weight: 900;
            /* Dark copy: the hero background is white/light now. */
            color: #0f172a;
            text-shadow: 0 6px 18px rgba(255, 255, 255, 0.35);
            line-height: 0.85;
            letter-spacing: -0.05em;
            text-transform: uppercase;
            margin-bottom: 2.5rem;
        }

        @media (min-width: 1024px) {
            .hero-title {
                font-size: 100px;
            }
        }

        .text-gradient {
            color: transparent;
            background-clip: text;
            -webkit-background-clip: text;
            background-image: linear-gradient(to right, var(--primary), #7ea9ff);
        }

        .hero-desc {
            font-size: 1.25rem;
            color: rgba(15, 23, 42, 0.88);
            text-shadow: 0 4px 14px rgba(255, 255, 255, 0.35);
            max-width: 28rem;
            font-weight: 500;
            line-height: 1.625;
            margin-bottom: 3rem;
            border-left: 4px solid var(--secondary);
            padding-left: 1.5rem;
        }

        /* ---- Hero on tablet / mobile -------------------------------------
           A side-by-side split has no room below 1024px, so the photo becomes
           a band on the right that fades out, and a vertical scrim keeps the
           copy readable over it. */
        @media (max-width: 1023px) {
            .hero {
                min-height: 72vh;
            }

            .hero-bg {
                width: 78%;
                -webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, .5) 22%, #000 55%);
                mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, .5) 22%, #000 55%);
            }

            .hero-overlay {
                background:
                    linear-gradient(to bottom, rgba(255, 255, 255, .55) 0%, rgba(255, 255, 255, .25) 45%, rgba(255, 255, 255, .75) 100%),
                    linear-gradient(to right, rgba(255, 255, 255, .90) 0%, rgba(255, 255, 255, .45) 55%, rgba(255, 255, 255, .20) 100%);
            }
        }

        @media (max-width: 640px) {
            .hero {
                min-height: 70vh;
            }

            /* Same layout as desktop: the photo stays on the RIGHT over the linear
               background, it just takes a bit more width and the type scales down
               so the copy still fits beside it. */
            .hero-bg {
                width: 66%;
                -webkit-mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, .5) 20%, #000 52%);
                mask-image: linear-gradient(to right, transparent 0%, rgba(0, 0, 0, .5) 20%, #000 52%);
            }

            .hero-overlay {
                background: linear-gradient(to right,
                        rgba(255, 255, 255, .94) 0%,
                        rgba(255, 255, 255, .70) 42%,
                        rgba(255, 255, 255, .25) 78%,
                        rgba(255, 255, 255, .35) 100%);
            }

            .hero-content {
                padding: 0 1rem;
            }

            /* Copy keeps clear of the photo. */
            .hero-content > * {
                max-width: 62%;
            }

            .hero-badge {
                margin-bottom: 1rem;
                padding: 0.2rem 0.6rem;
            }

            .hero-badge span {
                font-size: 8px;
                letter-spacing: 0.2em;
            }

            .hero-title {
                font-size: 2.1rem;
                margin-bottom: 1.25rem;
            }

            .hero-desc {
                font-size: 0.9rem;
                margin-bottom: 1.5rem;
                padding-left: 0.9rem;
                border-left-width: 3px;
                line-height: 1.5;
            }
        }

        /* STORIES SECTION */
        .stories {
            padding: 5rem 0;
            background-color: #ffffff;
            overflow: hidden;
        }

        .story-header {
            margin-bottom: 3rem;
        }

        .story-subtitle {
            font-size: 10px;
            font-weight: 900;
            color: var(--primary);
            text-transform: uppercase;
            letter-spacing: 0.4em;
            display: block;
            margin-bottom: 0.5rem;
        }

        .story-title {
            /* One single line at every width: the floor is small enough for a 320px
               phone, and it scales up to 3rem on desktop. Casing is left as written. */
            font-size: clamp(1rem, 4.1vw, 3rem);   /* fallback; fitStoryTitle() refines it */
            font-weight: 900;
            font-style: italic;
            color: var(--slate-900);
            text-transform: none;
            letter-spacing: -0.02em;
            line-height: 1.15;
            white-space: nowrap;
            margin: 0;
        }

        .nado-y { color: #FDD817; }
        .nado-b { color: #1459DD; }

        .story-scroll {
            display: flex;
            gap: 1rem;
            overflow-x: auto;
            padding-bottom: 1.5rem;
            padding-left: 0.25rem;
        }

        .story-card {
            flex-shrink: 0;
            width: 145px;
            aspect-ratio: 9/16;
            position: relative;
            border-radius: 2rem;
            overflow: hidden;
            cursor: pointer;
            border: 1px solid #e2e8f0;
            box-shadow: 0 20px 25px -5px rgba(15, 23, 42, 0.08), 0 10px 10px -5px rgba(15, 23, 42, 0.04);
        }

        @media (min-width: 768px) {
            .story-card {
                width: 170px;
            }
        }

        .story-card img.bg-img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 3s;
        }

        .story-card:hover img.bg-img {
            transform: scale(1.1);
        }

        .story-gradient {
            inset: 0;
            background: linear-gradient(to top, rgba(0, 0, 0, 0.8), transparent, rgba(0, 0, 0, 0.1));
            pointer-events: none;
            position: absolute;
            width: 100%;
            height: 100%;
        }

        .story-avatar {
            position: absolute;
            top: 1rem;
            left: 1rem;
            z-index: 20;
            width: 2.75rem;
            height: 2.75rem;
            border-radius: 50%;
            border: 3px solid var(--primary);
            padding: 0.125rem;
            background-color: var(--slate-900);
        }

        .story-avatar img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .story-info {
            position: absolute;
            bottom: 1.25rem;
            left: 1.25rem;
            right: 1.25rem;
            z-index: 20;
            text-align: left;
        }

        .story-name {
            font-size: 11px;
            font-weight: 900;
            color: var(--white);
            display: block;
            margin-bottom: 0.25rem;
        }

        .story-time {
            font-size: 9px;
            font-weight: 700;
            color: rgba(255, 255, 255, 0.6);
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        /* SEARCH & BRANDS */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.95);
            z-index: 99999;
            display: flex;
            align-items: center;
            justify-content: center;
            /* opacity: 0; */
            /* pointer-events: none; */
            transition: opacity 0.3s ease;
        }

        .search-section {
            padding: 3rem 1rem;
            background-color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .search-bar {
            background-color: var(--white);
            border-radius: 1rem;
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08);
            padding: 0.5rem;
            border: 1px solid var(--slate-100);
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        @media (min-width: 768px) {
            .search-bar {
                flex-direction: row;
                border-radius: 9999px;
                align-items: center;
                padding: 0.75rem;
            }
        }

        .search-item {
            flex: 1;
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 0.75rem 1.5rem;
            border-radius: 0.75rem;
            cursor: pointer;
            transition: background-color 0.2s;
        }

        .search-item:hover {
            background-color: var(--slate-50);
        }

        .search-item label {
            display: block;
            font-size: 0.9375rem;
            font-weight: 800;
            color: #0f172a;         /* black-ish, same tone as slate-900 */
            margin-bottom: 0.125rem;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            line-height: 1.2;
        }

        .search-item select {
            width: 100%;
            background: transparent;
            border: none;
            font-size: 0.75rem;
            font-weight: 600;
            color: var(--slate-500);
            text-transform: none;
            letter-spacing: 0;
            outline: none;
            cursor: pointer;
            appearance: none;
            line-height: 1.3;
            padding: 0;
        }

        .divider {
            display: none;
            width: 1px;
            height: 2rem;
            background-color: var(--slate-100);
            margin-left: 1rem;
        }

        @media (min-width: 768px) {
            .divider {
                display: block;
            }
        }

        .btn-find {
            background-color: var(--primary);
            color: var(--white);
            padding: 1rem 2rem;
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            transition: all 0.2s;
        }

        @media (min-width: 768px) {
            .btn-find {
                border-radius: 9999px;
                margin-left: 0.5rem;
            }
        }

        .btn-find:hover {
            background-color: #1d4ed8;
            transform: scale(1.02);
        }

        /* ---- Search bar on mobile: one single line, like desktop -------------
           Three dropdowns plus the button have to share a phone's width, so the
           icons and the button label are dropped and the type scales down. */
        @media (max-width: 767px) {
            /* Full screen width: drop the section padding and release the
               container's max-width so the bar spans edge to edge. */
            .search-section {
                padding: 2rem 0;
            }

            .search-section > .container {
                padding: 0 0.5rem;
                max-width: 100% !important;
            }

            .search-bar {
                flex-direction: row;
                align-items: center;
                border-radius: 9999px;
                padding: 0.3rem;
                gap: 0.15rem;
                flex-wrap: nowrap;
            }

            .search-item {
                flex: 1 1 0;
                min-width: 0;
                padding: 0.5rem 0.4rem;
                gap: 0;
                border-radius: 9999px;
            }

            /* The leading icon of each field costs ~30 px we do not have. */
            .search-item > i,
            .search-item > svg {
                display: none;
            }

            .search-item > div {
                min-width: 0;
            }

            .search-item label {
                font-size: 0.72rem;
                letter-spacing: 0;
                margin-bottom: 1px;
                white-space: nowrap;
                overflow: hidden;
                text-overflow: ellipsis;
            }

            .search-item select {
                font-size: 0.78rem;
                line-height: 1.25;
                text-overflow: ellipsis;
            }

            /* Icon-only button: "Trouver" does not fit next to three fields. */
            .btn-find {
                flex: 0 0 auto;
                padding: 0.65rem;
                border-radius: 50%;
                margin-left: 0.15rem;
                width: 40px;
                height: 40px;
                gap: 0;
            }

            .btn-find span {
                display: none;
            }
        }

        .brands-ticker {
            overflow: hidden;
            padding: 2.5rem 0;
            margin-top: 1.5rem;
            border-top: 1px solid var(--slate-50);
        }

        .ticker-track {
            display: flex;
            gap: 5rem;
            white-space: nowrap;
            animation: infinite-scroll 15s linear infinite;
            cursor: grab;
            user-select: none;
        }


        .brand-logo-link {
            display: inline-flex;
            align-items: center;
            text-decoration: none;
            /* Without this the anchor shrinks as a flex item and, because the global
               img{max-width:100%} then resolves against the anchor's own content-derived
               width, the logo collapses to zero and disappears. */
            flex: 0 0 auto;
        }

        .brand-logo {
            height: 3.5rem;
            /* Override the global img{max-width:100%} reset inside the ticker. */
            width: auto;
            max-width: none;
            filter: grayscale(0);
            opacity: 1;
            transition: all 0.2s;
            cursor: pointer;
        }

        .brand-logo:hover {
            transform: scale(1.1);
            opacity: 0.85;
        }

        /* ADS SECTION */
        .listings-section {
            padding: 6rem 0;
            background-color: var(--slate-50);
        }

        .section-title {
            font-size: 2.25rem;
            font-weight: 900;
            color: var(--slate-900);
            line-height: 0.9;
            letter-spacing: -0.05em;
            text-transform: uppercase;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 768px) {
            .section-title {
                font-size: 3.75rem;
                margin-bottom: 4rem;
            }
        }

        .listings-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }

        @media (min-width: 768px) {
            .listings-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .listings-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        /* ---- "Derniers véhicules": slider de 6 annonces, 3 visibles ---- */
        /* The arrows sit on the left and right edges of the track, vertically centred. */
        #derniers-vehicules .dv-wrap {
            position: relative;
        }

        #derniers-vehicules .dv-btn {
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 5;
            width: 48px;
            height: 48px;
            border-radius: 50%;
            border: 1px solid var(--slate-200, #e2e8f0);
            background: #fff;
            color: var(--slate-900, #0f172a);
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            box-shadow: 0 10px 25px rgba(15, 23, 42, .12);
            transition: background .2s, color .2s, opacity .2s;
        }

        #derniers-vehicules .dv-btn-prev { left: -20px; }
        #derniers-vehicules .dv-btn-next { right: -20px; }

        /* No room to sit outside the container on smaller screens: tuck them inside. */
        @media (max-width: 1380px) {
            #derniers-vehicules .dv-btn-prev { left: 6px; }
            #derniers-vehicules .dv-btn-next { right: 6px; }
        }

        #derniers-vehicules .dv-btn:hover:not(:disabled) {
            background: var(--primary, #1459DD);
            color: #fff;
            border-color: var(--primary, #1459DD);
        }

        #derniers-vehicules .dv-btn:disabled {
            opacity: .35;
            cursor: default;
        }

        /* The shared .listings-grid is a CSS grid; here it becomes a scroll track. */
        #derniers-vehicules .listings-grid {
            display: flex;
            gap: 1.75rem;
            overflow-x: auto;
            scroll-behavior: smooth;
            scroll-snap-type: x mandatory;
            scrollbar-width: none;
            -ms-overflow-style: none;
            padding-bottom: 0.5rem;
        }

        #derniers-vehicules .listings-grid::-webkit-scrollbar {
            display: none;
        }

        #derniers-vehicules .listing-card {
            flex: 0 0 85%;
            scroll-snap-align: start;
        }

        @media (min-width: 768px) {
            #derniers-vehicules .listing-card {
                flex: 0 0 calc((100% - 1.75rem) / 2);
            }
        }

        @media (min-width: 1024px) {
            #derniers-vehicules .listing-card {
                flex: 0 0 calc((100% - 2 * 1.75rem) / 3);
            }
        }


        /* REFINE AD CARD (Match Home.tsx) */
        .listing-card {
            background-color: var(--white);
            border-radius: 2.5rem;
            /* rounded-[2.5rem] */
            overflow: hidden;
            border: 1px solid var(--slate-200);
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: all 0.3s;
            cursor: pointer;
            box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
            /* shadow-sm */
        }

        .listing-card:hover {
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            /* hover:shadow-2xl */
        }

        .listing-image-container {
            position: relative;
            aspect-ratio: 16/11;
            overflow: hidden;
        }

        .listing-main-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s;
        }

        .listing-card:hover .listing-main-img {
            transform: scale(1.1);
        }

        /* Video Preview */
        .listing-video-preview {
            position: absolute;
            bottom: 1rem;
            /* bottom-4 */
            right: 1rem;
            /* right-4 */
            width: 6rem;
            /* w-24 */
            height: 9rem;
            /* h-36 */
            border-radius: 1rem;
            /* rounded-2xl */
            overflow: hidden;
            border: 2px solid white;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            /* shadow-2xl */
            z-index: 20;
            transition: transform 0.3s;
        }

        .listing-card:hover .listing-video-preview {
            transform: scale(1.1);
        }

        .listing-video-preview img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            opacity: 0.9;
        }

        .listing-play-btn {
            position: absolute;
            inset: 0;
            background-color: rgba(0, 0, 0, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .listing-play-icon {
            width: 2rem;
            /* w-8 */
            height: 2rem;
            /* h-8 */
            background-color: rgba(255, 255, 255, 0.4);
            backdrop-filter: blur(4px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
        }

        /* Status Badge */
        .listing-status-badge {
            position: absolute;
            top: 1.5rem;
            /* top-6 */
            left: 1.5rem;
            /* left-6 */
            padding: 0.5rem 1rem;
            /* px-4 py-2 */
            border-radius: 9999px;
            font-weight: 900;
            font-size: 10px;
            /* text-[10px] */
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            /* shadow-xl */
            backdrop-filter: blur(4px);
            z-index: 20;
            background-color: #22c55e;
        }

        .listing-type-badge {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: white;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            backdrop-filter: blur(4px);
            z-index: 20;
        }
        .type-certifie   { background-color: rgba(16, 185, 129, 0.9); }
        .type-particulier{ background-color: rgba(59, 130, 246, 0.9); }
        .type-pro        { background-color: rgba(245, 158, 11, 0.9); }

        .status-available {
            background-color: rgba(34, 197, 94, 0.9);
            /* bg-green-500/90 */
        }

        .status-reserved {
            background-color: rgba(249, 115, 22, 0.9);
            /* bg-orange-500/90 */
        }

        /* Price Tag (Overlay on Image) */
        .listing-price-tag {
            position: absolute;
            bottom: 1.5rem;
            /* bottom-6 */
            left: 1.5rem;
            /* left-6 */
            background-color: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(4px);
            padding: 1rem 1.25rem;
            /* px-5 py-4 */
            border-radius: 1rem;
            /* rounded-2xl */
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            /* shadow-2xl */
            border: 1px solid var(--slate-100);
            z-index: 20;
            display: flex;
            align-items: center;
        }

        .listing-price-text {
            color: #1459DD;
            font-size: 1.5rem;
            /* text-2xl */
            font-weight: 900;
            letter-spacing: -0.05em;
            line-height: 1;
        }

        /* Details Section */
        .listing-content {
            padding: 2rem;
            /* p-8 */
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .listing-title-new {
            font-size: 1.5rem;
            /* text-2xl */
            font-weight: 900;
            text-transform: uppercase;
            color: var(--slate-900);
            margin-bottom: 1.5rem;
            /* mb-6 */
            letter-spacing: -0.025em;
            line-height: 1.2;
            transition: color 0.2s;
        }

        .listing-card:hover .listing-title-new {
            color: #1459DD;
        }

        /* Specs Grid */
        .listing-specs-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem 2rem;
            /* gap-x-8 gap-y-4 */
            margin-bottom: 2rem;
            /* mb-8 */
        }

        .listing-spec-item {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            /* gap-3 */
            color: var(--slate-400);
            font-size: 0.75rem;
            /* text-xs */
            font-weight: 700;
            text-transform: uppercase;
        }

        /* Footer (Blue Full Width) */
        .listing-footer-btn {
            background-color: var(--primary);
            /* bg-[#1459DD] */
            padding: 1.5rem;
            /* p-6 */
            border-radius: 2rem;
            /* rounded-[2rem] */
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            /* shadow-xl */
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: white;
            margin: 0;
            margin-top: auto;
            /* mt-auto */
            transition: transform 0.2s;
        }

        .listing-card:hover .listing-footer-btn {
            transform: scale(1.02);
        }

        .monthly-label {
            font-size: 9px;
            /* text-[9px] */
            font-weight: 900;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: block;
            margin-bottom: 0.25rem;
        }

        .monthly-price {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
        }

        .monthly-amount {
            font-size: 1.5rem;
            /* text-2xl */
            font-weight: 900;
            line-height: 1;
            color: white;
        }

        .monthly-unit {
            font-size: 0.75rem;
            /* text-xs */
            font-weight: 900;
            color: #FDD817;
        }

        .arrow-circle {
            width: 2.5rem;
            /* w-10 */
            height: 2.5rem;
            /* h-10 */
            border-radius: 50%;
            background-color: rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            transition: all 0.2s;
        }

        .listing-card:hover .arrow-circle {
            background-color: var(--white);
            color: var(--primary);
            transform: translateX(5px);
        }

        .btn-view-all {
            padding: 1.25rem 2.5rem;
            background-color: var(--slate-900);
            color: var(--white);
            border-radius: 1rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.75rem;
            margin: 4rem auto 0;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            /* It is an <a> now: a button shrink-wraps its content, a flex anchor would
               stretch full-width, so pin the width and keep text-decoration off. */
            width: fit-content;
            text-decoration: none;
            white-space: nowrap;
        }

        .listing-footer {
            margin-top: auto;
            padding: 1.5rem;
            background-color: var(--primary);
            border-radius: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: transform 0.2s;
        }

        .listing-card:hover .listing-footer {
            transform: scale(1.02);
        }

        .payment-info {
            display: flex;
            flex-direction: column;
        }

        .payment-label {
            font-size: 9px;
            font-weight: 900;
            color: rgba(255, 255, 255, 0.7);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.25rem;
        }

        .payment-amount {
            display: flex;
            align-items: baseline;
            gap: 0.25rem;
        }

        .amount-value {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--white);
        }

        .amount-unit {
            font-size: 0.75rem;
            font-weight: 900;
            color: var(--secondary);
        }

        .btn-arrow {
            width: 2.5rem;
            height: 2.5rem;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--white);
        }


        /* FOOTER */
        footer {
            background-color: var(--slate-900);
            color: var(--white);
            padding: 6rem 0;
        }

        .footer-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 3rem;
            margin-bottom: 4rem;
        }

        @media (min-width: 768px) {
            .footer-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }

        .footer-heading {
            font-weight: 700;
            margin-bottom: 1.5rem;
            text-transform: uppercase;
            font-size: 0.75rem;
            letter-spacing: 0.1em;
            color: var(--secondary);
        }

        .footer-links li {
            margin-bottom: 1rem;
        }

        .footer-links a {
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--slate-300);
            transition: color 0.2s;
        }

        .footer-links a:hover {
            color: var(--white);
        }

        .social-btn {
            width: 2.5rem;
            height: 2.5rem;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 0.75rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s;
        }

        .social-btn:hover {
            background-color: var(--primary);
        }


        /* PUB NADO EXPERT */
        .promo-section {
            padding: 2rem 1rem;
            background-color: var(--white);
        }

        .expert-card {
            background-color: var(--slate-900);
            border-radius: 3rem;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            gap: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .expert-card {
                flex-direction: row;
                padding: 4rem;
            }
        }

        .expert-content {
            position: relative;
            z-index: 10;
            display: flex;
            flex-direction: column;
            max-width: 42rem;
            width: 100%;
            text-align: left;
        }

        .label-trust {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }

        .label-trust span {
            color: var(--secondary);
            font-weight: 900;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
        }

        .expert-title {
            font-size: 2.25rem;
            font-weight: 900;
            color: var(--white);
            text-transform: uppercase;
            letter-spacing: -0.05em;
            margin-bottom: 1.5rem;
            line-height: 1;
        }

        @media (min-width: 768px) {
            .expert-title {
                font-size: 3rem;
            }
        }

        .expert-desc {
            color: var(--slate-400);
            font-weight: 500;
            margin-bottom: 2rem;
            line-height: 1.625;
            max-width: 28rem;
        }

        .btn-discover {
            width: fit-content;
            padding: 1rem 2rem;
            background-color: var(--primary);
            color: var(--white);
            border-radius: 1rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .expert-image {
            width: 100%;
            aspect-ratio: 16/9;
            border-radius: 2.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 4px solid var(--slate-800);
            position: relative;
            z-index: 10;
        }

        @media (min-width: 768px) {
            .expert-image {
                width: 33.333333%;
            }
        }

        .expert-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* NADO PRICE */
        .price-section {
            padding: 6rem 0;
            background-color: var(--primary);
            position: relative;
            overflow: hidden;
        }

        .price-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 4rem;
            align-items: center;
            position: relative;
            z-index: 10;
        }

        @media (min-width: 1024px) {
            .price-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        .price-title {
            font-size: 2.25rem;
            font-weight: 900;
            color: var(--white);
            line-height: 0.9;
            letter-spacing: -0.05em;
            text-transform: uppercase;
            margin-bottom: 2rem;
        }

        @media (min-width: 768px) {
            .price-title {
                font-size: 3.75rem;
            }
        }

        .price-desc {
            color: #eff6ff;
            font-size: 1.25rem;
            font-weight: 500;
            line-height: 1.625;
            margin-bottom: 3rem;
            max-width: 28rem;
        }

        .btn-estimate {
            padding: 1.5rem 2.5rem;
            background-color: var(--white);
            color: var(--primary);
            border-radius: 2rem;
            font-weight: 900;
            font-size: 0.875rem;
            text-transform: uppercase;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .price-image-container {
            position: relative;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(24px);
            padding: 1rem;
            border-radius: 3.5rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .price-image-container img {
            border-radius: 2.5rem;
            filter: grayscale(100%);
            width: 100%;
        }

        /* IMMERSIVE EXPERIENCES */
        .immersive-section {
            padding: 6rem 0;
            background-color: #020617;
            position: relative;
            overflow: hidden;
        }

        .immersive-scroll {
            display: flex;
            gap: 1.5rem;
            overflow-x: auto;
            padding-bottom: 2.5rem;
        }

        .video-card {
            flex-shrink: 0;
            width: 320px;
            aspect-ratio: 9/16;
            position: relative;
            border-radius: 2.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid rgba(255, 255, 255, 0.05);
            cursor: pointer;
        }

        .video-card img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.7s;
        }

        .video-card:hover img {
            transform: scale(1.1);
        }

        .video-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top, #020617, rgba(2, 6, 23, 0.2), transparent);
        }

        .video-info {
            position: absolute;
            bottom: 2rem;
            left: 2rem;
            right: 2rem;
            z-index: 20;
        }

        .video-title {
            color: var(--white);
            font-weight: 900;
            font-size: 1.25rem;
            margin-bottom: 0.5rem;
        }

        .video-meta {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            color: var(--slate-300);
        }

        .video-meta span {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .play-icon {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.2s;
            z-index: 10;
        }

        .video-card:hover .play-icon {
            opacity: 1;
        }

        /* LIVE AUCTIONS */
        .live-section {
            padding: 6rem 0;
            background-color: var(--white);
            position: relative;
            overflow: hidden;
        }

        .auction-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: flex-start;
        }

        @media (min-width: 1024px) {
            .auction-grid {
                grid-template-columns: repeat(12, 1fr);
            }

            .auction-main {
                grid-column: span 8;
            }

            .auction-sidebar {
                grid-column: span 4;
            }
        }

        .main-auction-card {
            position: relative;
            background-color: var(--white);
            border: 1px solid var(--slate-100);
            border-radius: 3.5rem;
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .main-auction-img {
            position: relative;
            aspect-ratio: 16/9;
            overflow: hidden;
        }

        .main-auction-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .main-auction-content {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
            justify-content: space-between;
            align-items: flex-end;
        }

        @media (min-width: 768px) {
            .main-auction-content {
                flex-direction: row;
            }
        }

        .current-bid {
            text-align: right;
            display: flex;
            flex-direction: column;
            align-items: flex-end;
        }

        .bid-label {
            font-size: 10px;
            font-weight: 900;
            color: var(--slate-400);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            display: block;
            margin-bottom: 0.25rem;
        }

        .bid-amount {
            font-size: 2.25rem;
            font-weight: 900;
            color: var(--primary);
        }

        .btn-bid {
            margin-top: 1.5rem;
            padding: 1rem 2.5rem;
            background-color: var(--primary);
            color: var(--white);
            border-radius: 1rem;
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .side-auction-card {
            background-color: var(--slate-50);
            border: 1px solid var(--slate-100);
            border-radius: 2.5rem;
            padding: 1.5rem;
            transition: all 0.2s;
            cursor: pointer;
            display: flex;
            gap: 1.5rem;
            align-items: center;
        }

        .side-auction-card:hover {
            background-color: var(--white);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .side-img {
            width: 6rem;
            height: 6rem;
            border-radius: 1rem;
            overflow: hidden;
            flex-shrink: 0;
            border: 1px solid var(--slate-200);
        }

        .side-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* TESTIMONIALS */
        .testimonials {
            padding: 3rem 0;
            background-color: var(--slate-50);
        }

        @media (min-width: 768px) {
            .testimonials {
                padding: 6rem 0;
            }
        }

        .testimonial-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2rem;
            align-items: center;
        }

        @media (min-width: 768px) {
            .testimonial-grid {
                gap: 4rem;
            }
        }

        @media (min-width: 1024px) {
            .testimonial-grid {
                grid-template-columns: repeat(12, 1fr);
            }

            .testimonial-left {
                grid-column: span 5;
            }

            .testimonial-right {
                grid-column: span 7;
            }
        }

        .testimonial-right {
            max-width: 100%;
            overflow: hidden;
        }

        .testimonial-carousel-row {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            width: 100%;
            max-width: 100%;
            box-sizing: border-box;
        }

        .testimonial-carousel-row .owl-carousel {
            flex: 1 1 0% !important;
            width: 0 !important;
            min-width: 0 !important;
            max-width: 100% !important;
            overflow: hidden !important;
        }

        .testimonial-carousel-row .owl-stage-outer {
            overflow: hidden !important;
            max-width: 100% !important;
        }

        .testimonial-carousel-row .owl-item,
        .testimonial-carousel-row .item,
        .testimonial-card {
            box-sizing: border-box !important;
            max-width: 100% !important;
        }

        .btn-nav-prev,
        .btn-nav-next {
            flex-shrink: 0;
        }

        .btn-nav-prev {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            border: 1px solid var(--slate-200);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--slate-400);
            transition: all 0.2s;
        }

        .btn-nav-prev:hover {
            background-color: var(--slate-50);
        }

        .btn-nav-next {
            width: 3rem;
            height: 3rem;
            border-radius: 50%;
            background-color: var(--secondary);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--slate-900);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .testimonial-card {
            background-color: var(--white);
            border-radius: 2rem;
            padding: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            border: 1px solid var(--slate-100);
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 640px) {
            .testimonial-card {
                padding: 2.5rem;
                border-radius: 2.5rem;
            }
        }

        @media (min-width: 768px) {
            .testimonial-card {
                padding: 4rem;
                border-radius: 3.5rem;
            }
        }

        .quote-icon {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            color: var(--slate-100);
            opacity: 0.2;
            width: 3rem;
            height: 3rem;
        }

        @media (min-width: 768px) {
            .quote-icon {
                top: 2.5rem;
                left: 2.5rem;
                width: 5rem;
                height: 5rem;
            }
        }

        .stars {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            color: var(--secondary);
            margin-bottom: 1rem;
        }

        @media (min-width: 768px) {
            .stars {
                margin-bottom: 2rem;
            }
        }

        .quote-text {
            font-size: 1rem;
            font-style: italic;
            color: var(--slate-900);
            line-height: 1.625;
            margin-bottom: 1.5rem;
        }

        @media (min-width: 640px) {
            .quote-text {
                font-size: 1.25rem;
                margin-bottom: 2rem;
            }
        }

        @media (min-width: 768px) {
            .quote-text {
                font-size: 1.875rem;
                margin-bottom: 3rem;
            }
        }

        .client-info {
            display: flex;
            align-items: center;
            gap: 1.25rem;
            padding-top: 1rem;
            border-top: 1px solid var(--slate-50);
        }

        @media (min-width: 768px) {
            .client-info {
                padding-top: 2rem;
            }
        }

        .client-img {
            width: 4rem;
            height: 4rem;
            border-radius: 50%;
            overflow: hidden;
            border: 2px solid var(--white);
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .client-img img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* BLOG SECTION */
        .blog-section {
            padding: 6rem 0;
            background-color: var(--white);
        }

        .blog-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }

        @media (min-width: 768px) {
            .blog-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (min-width: 1024px) {
            .blog-grid {
                grid-template-columns: repeat(3, 1fr);
            }
        }

        .blog-card {
            background-color: var(--white);
            border-radius: 2rem;
            overflow: hidden;
            border: 1px solid var(--slate-100);
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.05);
            transition: all 0.3s;
            cursor: pointer;
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .blog-card:hover {
            transform: translateY(-0.5rem);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.15);
        }

        .blog-img-container {
            position: relative;
            aspect-ratio: 16/10;
            overflow: hidden;
        }

        .blog-img-container img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }

        .blog-card:hover .blog-img-container img {
            transform: scale(1.05);
        }

        .blog-category {
            position: absolute;
            top: 1.25rem;
            left: 1.25rem;
            background-color: var(--white);
            color: var(--primary);
            padding: 0.5rem 1rem;
            border-radius: 9999px;
            font-weight: 900;
            font-size: 0.625rem;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .blog-content {
            padding: 2rem;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .blog-meta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
            color: var(--slate-400);
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
        }

        .blog-title {
            font-size: 1.25rem;
            font-weight: 900;
            color: var(--slate-900);
            margin-bottom: 1rem;
            line-height: 1.4;
            transition: color 0.2s;
        }

        .blog-card:hover .blog-title {
            color: var(--primary);
        }

        .blog-excerpt {
            color: var(--slate-500);
            font-weight: 500;
            font-size: 0.875rem;
            line-height: 1.625;
            margin-bottom: 1.5rem;
            flex: 1;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-link {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            color: var(--primary);
            font-weight: 900;
            font-size: 0.75rem;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        /* MOBILE MENU DRAWER */
        .mobile-drawer-overlay {
            position: fixed;
            inset: 0;
            background-color: rgba(15, 23, 42, 0.4);
            backdrop-filter: blur(12px);
            z-index: 100;
            opacity: 0;
            visibility: hidden;
            transition: all 0.5s;
        }

        .mobile-drawer-overlay.open {
            opacity: 1;
            visibility: visible;
        }

        .mobile-drawer {
            position: fixed;
            inset: 0;
            z-index: 1100;
            visibility: hidden;
            transition: visibility 0.3s;
            pointer-events: none;
        }

        .mobile-drawer.open {
            transform: translateX(0);
        }

        .drawer-header {
            padding: 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .drawer-nav {
            flex: 1;
            overflow-y: auto;
            padding: 1rem 2rem;
            display: flex;
            flex-direction: column;
            gap: 1.5rem;
        }

        .drawer-link {
            font-size: 1.125rem;
            font-weight: 900;
            color: var(--slate-900);
            text-transform: uppercase;
            text-align: left;
            transition: color 0.2s;
        }

        .drawer-link:hover {
            color: var(--primary);
        }

        .drawer-footer {
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            border-top: 1px solid var(--slate-100);
        }

        /* FINANCEMENT FLASH */
        .finance-section {
            padding: 2rem 1rem;
            background-color: var(--white);
        }

        .finance-card {
            background-color: var(--secondary);
            border-radius: 3rem;
            padding: 2.5rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 3rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            position: relative;
            overflow: hidden;
        }

        @media (min-width: 768px) {
            .finance-card {
                flex-direction: row;
                padding: 4rem;
            }

            .finance-image {
                width: 33.333%;
                max-width: 24rem;
            }
        }

        .finance-image {
            aspect-ratio: 1/1;
            border-radius: 50%;
            border: 8px solid var(--white);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            overflow: hidden;
            transform: rotate(12deg);
            width: 100%;
        }

        .finance-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transform: scale(1.25);
        }

        /* BANNER WHATSAPP */
        .whatsapp-section {
            padding: 6rem 0;
            background-color: var(--white);
            text-align: left;
        }

        .whatsapp-card {
            background-color: var(--primary);
            border-radius: 4rem;
            padding: 2.5rem;
            position: relative;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            gap: 4rem;
            align-items: center;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        @media (min-width: 768px) {
            .whatsapp-card {
                flex-direction: row;
                padding: 5rem;
            }
        }

        .whatsapp-content {
            flex: 1;
            text-align: left;
            width: 100%;
        }

        .whatsapp-title {
            font-size: 3rem;
            font-weight: 900;
            color: var(--white);
            text-transform: uppercase;
            line-height: 0.9;
            letter-spacing: -0.05em;
            margin-bottom: 3rem;
        }

        @media (min-width: 768px) {
            .whatsapp-title {
                font-size: 4.5rem;
            }
        }

        .btn-whatsapp {
            display: inline-flex;
            align-items: center;
            gap: 1rem;
            padding: 1.5rem 2.5rem;
            background-color: var(--white);
            color: var(--primary);
            border-radius: 1.5rem;
            font-weight: 900;
            font-size: 0.875rem;
            text-transform: uppercase;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            transition: transform 0.2s;
        }

        .btn-whatsapp:hover {
            transform: scale(1.05);
        }

        .whatsapp-image {
            flex: 1;
            width: 100%;
            max-width: 500px;
            background-color: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(24px);
            padding: 1rem;
            border-radius: 4rem;
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 30px 60px -12px rgba(0, 0, 0, 0.5);
        }

        .whatsapp-image img {
            border-radius: 3.5rem;
            width: 100%;
            height: auto;
            object-fit: cover;
        }


        /* BLOG UPDATE (DARK REDESIGN) */
        .blog-section {
            padding: 8rem 0;
            background-color: #020617;
            position: relative;
        }

        .blog-header {
            display: flex;
            flex-direction: column;
            margin-bottom: 6rem;
            gap: 2rem;
        }

        @media (min-width: 768px) {
            .blog-header {
                flex-direction: row;
                align-items: flex-end;
                justify-content: space-between;
            }
        }

        .blog-title-wrapper {
            max-width: 36rem;
        }

        .blog-big-title {
            font-size: 3rem;
            font-weight: 900;
            color: var(--white);
            line-height: 0.8;
            letter-spacing: -0.05em;
            text-transform: uppercase;
            margin: 0;
        }

        @media (min-width: 768px) {
            .blog-big-title {
                font-size: 6.875rem;
            }
        }

        /* ~110px */

        .btn-explore-blog {
            display: flex;
            align-items: center;
            gap: 1rem;
            background-color: var(--white);
            color: #020617;
            padding: 1rem 2rem;
            border-radius: 1rem;
            font-weight: 900;
            font-size: 11px;
            text-transform: uppercase;
            transition: transform 0.2s;
        }

        .btn-explore-blog:hover {
            transform: scale(1.05);
        }

        .blog-grid-hero {
            display: grid;
            grid-template-columns: 1fr;
            gap: 2.5rem;
        }

        @media (min-width: 1024px) {
            .blog-grid-hero {
                grid-template-columns: repeat(12, 1fr);
            }

            .blog-hero {
                grid-column: span 8;
                /* Sit the main card in the middle, between the two small ones */
                align-self: center;
            }

            .blog-sidebar {
                grid-column: span 4;
            }
        }

        .blog-featured {
            position: relative;
            aspect-ratio: 16/10;
            border-radius: clamp(1.5rem, 6cqw, 3rem);
            overflow: hidden;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
            cursor: pointer;
            /* Let the overlay content scale to THIS card's width, so the same
               design works at both the big (featured) and small (sidebar) sizes. */
            container-type: inline-size;
        }

        .blog-featured img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 2s;
        }

        .blog-featured:hover img {
            transform: scale(1.05);
        }

        .blog-featured-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(to top,
                #020617 8%,
                rgba(2, 6, 23, 0.55) 40%,
                rgba(2, 6, 23, 0) 72%);
        }

        .blog-featured-content {
            position: absolute;
            bottom: clamp(1.25rem, 7cqw, 3rem);
            left: clamp(1.25rem, 7cqw, 3rem);
            right: clamp(1.25rem, 7cqw, 3rem);
        }

        .blog-meta-tag {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: rgba(255, 255, 255, 0.6);
            margin-bottom: clamp(0.5rem, 4cqw, 1.5rem);
        }

        .blog-featured-title {
            font-size: clamp(1.1rem, 7cqw, 2.25rem);
            font-weight: 900;
            color: var(--white);
            margin-bottom: 0;
            line-height: 0.95;
            text-transform: uppercase;
            letter-spacing: -0.05em;
            transition: color 0.2s;
        }

        .blog-featured:hover .blog-featured-title {
            color: var(--secondary);
        }

        /* Wrapper only — the overlay card (.blog-featured) inside carries the design */
        .blog-sidebar-card {
            display: block;
            cursor: pointer;
        }

        .blog-sidebar-title {
            font-size: 1.5rem;
            font-weight: 900;
            color: var(--white);
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 1rem;
            letter-spacing: -0.05em;
            transition: color 0.2s;
        }

        .blog-sidebar-card:hover .blog-sidebar-title {
            color: var(--primary);
        }

        .blog-read-more {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 10px;
            font-weight: 900;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--white);
            transition: color 0.2s;
        }

        .blog-sidebar-card:hover .blog-read-more {
            color: var(--secondary);
        }

        .blog-read-more i {
            transition: transform 0.2s;
        }

        .blog-sidebar-card:hover .blog-read-more i {
            transform: translateX(4px);
        }

        .btn-explore-blog i {
            transition: transform 0.2s;
        }

        .btn-explore-blog:hover i {
            transform: translateX(4px);
        }

        /* BLOG — mobile spacing + stack order (all cards share the overlay design) */
        @media (max-width: 767px) {
            .blog-section {
                padding: 4rem 0;
            }

            .blog-header {
                margin-bottom: 3rem;
                gap: 1.25rem;
            }

            .blog-big-title {
                font-size: 2.75rem;
                line-height: 0.9;
            }

            .btn-explore-blog {
                align-self: flex-start;
            }

            /* Main blog stays first (natural order), and is bigger than the
               two small cards below it: taller, more prominent, larger title. */
            .blog-hero .blog-featured {
                aspect-ratio: 4 / 5;
            }

            .blog-hero .blog-featured-title {
                font-size: clamp(1.6rem, 9cqw, 2.6rem);
                line-height: 1;
            }
        }

        .sidebar-content {
            padding: 2.5rem;
            text-align: left;
        }


        /* STORY MODAL */
        .modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 100;
            background-color: rgba(2, 6, 23, 0.95);
            backdrop-filter: blur(24px);
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.3s;
        }

        .modal-overlay.open {
            opacity: 1;
            pointer-events: auto;
        }

        .story-viewer {
            width: 100%;
            max-width: 450px;
            aspect-ratio: 9/16;
            background-color: var(--slate-900);
            border-radius: 3rem;
            overflow: hidden;
            position: relative;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
            display: flex;
            flex-direction: column;
        }

        .story-progress {
            position: absolute;
            top: 1rem;
            left: 1rem;
            right: 1rem;
            height: 0.25rem;
            display: flex;
            gap: 0.25rem;
            z-index: 110;
        }

        .progress-segment {
            flex: 1;
            height: 100%;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 9999px;
            overflow: hidden;
        }

        .progress-bar {
            height: 100%;
            background-color: var(--white);
            width: 0%;
            transition: width 0.05s linear;
        }

        .close-modal {
            position: absolute;
            top: 1.5rem;
            right: 1.5rem;
            z-index: 110;
            color: var(--white);
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            width: 3rem;
            height: 3rem;
            display: flex;
            align-items: center;
            justify-content: center;
            cursor: pointer;
            transition: transform 0.2s;
        }

        .close-modal:hover {
            transform: rotate(90deg);
        }
    </style>
</head>

<body class="no-scrollbar">

    @php
        $client = Auth::guard('client')->user();
        // Check if on Auto Enchère route
        $isAutoEnchere = request()->routeIs('nadoautoenchere.*');

        // Theme Colors
        $accentColor = $isAutoEnchere ? '#F37021' : '#FDD817';
        $buttonHoverBg = $isAutoEnchere ? '#F37021' : '#FDD817';
        $logoText = $isAutoEnchere ? 'AUTO ENCHÈRE' : 'NADO';

        // Recent demandes for the live-notification toast (bottom-left).
        $recentDemandes = \App\Models\Demande::public()
            ->with(['brand', 'modele', 'ville'])
            ->latest()
            ->limit(8)
            ->get()
            ->map(function ($d) {
                $firstName = trim(explode(' ', (string) $d->nom)[0]) ?: 'Un acheteur';
                $car = trim(($d->brand?->name ?? '') . ' ' . ($d->modele?->name ?? ''));
                return [
                    'name' => $firstName,
                    'car'  => $car !== '' ? $car : 'une voiture',
                    'ville'=> $d->ville?->name,
                    'url'  => route('demandes.show', $d->id),
                    'ago'  => $d->created_at->locale('fr')->diffForHumans(),
                ];
            })
            ->values()
            ->toArray();
    @endphp

    {{-- ============== DEMANDE LIVE FEED (bottom-left) ============== --}}
    @if(!empty($recentDemandes))
        <div id="dmdLive" style="position:fixed;left:20px;bottom:20px;z-index:9999;width:400px;max-width:calc(100vw - 20px);font-family:'Inter',sans-serif;pointer-events:none;">
            {{-- Persistent banner on top (bigger & more prominent than the live cards) --}}
            <a href="{{ route('demandes.index') }}" id="dmdLiveBanner"
               style="pointer-events:auto;display:flex;align-items:center;gap:14px;background:linear-gradient(135deg,#1459DD,#2563eb);color:#fff;padding:20px 22px;border-radius:18px;text-decoration:none;box-shadow:0 24px 48px -14px rgba(20,89,221,.5);margin-bottom:14px;position:relative;">
                <div style="width:52px;height:52px;border-radius:50%;background:rgba(255,255,255,.2);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2">
                        <path d="M3 13l2-7h14l2 7"/><path d="M3 13v5a1 1 0 001 1h2a1 1 0 001-1v-1h10v1a1 1 0 001 1h2a1 1 0 001-1v-5"/><circle cx="7" cy="16" r="1.3"/><circle cx="17" cy="16" r="1.3"/>
                    </svg>
                </div>
                <div style="min-width:0;flex:1;">
                    <div style="font-size:14px;opacity:.95;font-weight:600;line-height:1.3;">Vous avez une voiture à vendre ?</div>
                    <div style="font-size:18px;font-weight:800;letter-spacing:-0.01em;margin-top:3px;">Voir toutes les demandes →</div>
                </div>
                <button type="button" id="dmdLiveClose" aria-label="Fermer"
                        onclick="event.preventDefault();event.stopPropagation();document.getElementById('dmdLive').style.display='none';"
                        style="pointer-events:auto;position:absolute;top:10px;right:12px;background:rgba(255,255,255,.15);border:0;color:#fff;cursor:pointer;font-size:16px;line-height:1;width:24px;height:24px;border-radius:50%;display:flex;align-items:center;justify-content:center;">×</button>
            </a>

            {{-- Stacked live demande cards --}}
            <div id="dmdLiveStack" style="display:flex;flex-direction:column-reverse;gap:8px;"></div>
        </div>

        <style>
            @keyframes dmdPulse {
                0%,100% { box-shadow:0 0 0 4px rgba(16,185,129,.25); }
                50%     { box-shadow:0 0 0 7px rgba(16,185,129,0); }
            }
            @keyframes dmdSlideIn {
                from { opacity:0; transform:translateY(16px); }
                to   { opacity:1; transform:translateY(0); }
            }
            @keyframes dmdSlideOut {
                from { opacity:1; transform:translateY(0) scale(1); max-height:200px; margin-bottom:8px; }
                to   { opacity:0; transform:translateY(-14px) scale(.96); max-height:0; margin-bottom:0; padding-top:0; padding-bottom:0; }
            }
            @keyframes dmdSlideInRight {
                from { opacity:0; transform:translateX(110%); }
                to   { opacity:1; transform:translateX(0); }
            }
            @keyframes dmdSlideOutLeft {
                from { opacity:1; transform:translateX(0) scale(1); max-height:200px; margin-bottom:8px; }
                to   { opacity:0; transform:translateX(-30%) scale(.96); max-height:0; margin-bottom:0; padding-top:0; padding-bottom:0; }
            }
            .dmd-live-card {
                pointer-events:auto; background:#fff; border:1px solid #e2e8f0; border-radius:12px;
                padding:12px 14px; box-shadow:0 12px 24px -10px rgba(15,23,42,.18);
                display:flex; gap:10px; align-items:flex-start;
                animation:dmdSlideIn .4s cubic-bezier(.2,.9,.3,1.3) both;
                overflow:hidden;
            }
            .dmd-live-card.leaving { animation:dmdSlideOut .35s ease forwards; }
            .dmd-live-av {
                width:34px; height:34px; border-radius:50%;
                background:linear-gradient(135deg,#1459DD,#F37021);
                color:#fff; display:flex; align-items:center; justify-content:center;
                font-weight:800; font-size:13px; flex-shrink:0;
            }
            .dmd-live-tag {
                font-size:10px; font-weight:700; color:#1459DD;
                text-transform:uppercase; letter-spacing:.04em;
                display:flex; align-items:center; gap:5px;
            }
            .dmd-live-dot {
                width:7px; height:7px; border-radius:50%; background:#10b981;
                animation:dmdPulse 1.6s infinite;
            }
            .dmd-live-text { font-size:13px; color:#0f172a; line-height:1.4; margin-top:2px; }
            .dmd-live-text strong { color:#0f172a; }
            .dmd-live-link { font-size:11px; color:#1459DD; font-weight:700; text-decoration:none; }
            .dmd-live-ago { font-size:10px; color:#94a3b8; margin-top:1px; }
            /* Hide the demandes feed while the mobile drawer is open (any viewport) */
            body:has(.mobile-drawer.visible) #dmdLive,
            body:has(.mobile-drawer.nb-visible) #dmdLive { display: none !important; }

            @media (max-width:600px) {
                /* Sit just under the sticky navbar, right side, small width.
                   `top` is set dynamically by JS below based on the real navbar height. */
                #dmdLive {
                    left:auto;
                    right:8px;
                    bottom:auto;
                    top:120px;
                    width:240px;
                    max-width:70vw;
                }
                /* Keep the blue banner, just make it smaller to fit */
                #dmdLiveBanner {
                    padding:12px 14px;
                    gap:10px;
                    border-radius:14px;
                    margin-bottom:10px;
                    box-shadow:0 14px 28px -10px rgba(20,89,221,.45);
                }
                #dmdLiveBanner > div:first-child { width:34px; height:34px; }
                #dmdLiveBanner > div:first-child svg { width:20px; height:20px; }
                #dmdLiveBanner > div:nth-child(2) > div:first-child { font-size:11px; }
                #dmdLiveBanner > div:nth-child(2) > div:last-child { font-size:13px; }
                #dmdLiveClose { top:6px; right:6px; width:20px; height:20px; font-size:13px; }

                /* Stack grows downward (banner stays on top, newest card right below it) */
                #dmdLiveStack { flex-direction:column; }

                /* Smaller cards + horizontal slide animations (right → left) */
                .dmd-live-card {
                    padding:8px 10px;
                    gap:7px;
                    border-radius:10px;
                    animation:dmdSlideInRight .35s cubic-bezier(.2,.9,.3,1.3) both;
                    box-shadow:0 8px 18px -8px rgba(15,23,42,.22);
                }
                .dmd-live-card.leaving { animation:dmdSlideOutLeft .3s ease forwards; }
                .dmd-live-av { width:26px; height:26px; font-size:11px; }
                .dmd-live-tag { font-size:9px; }
                .dmd-live-dot { width:6px; height:6px; }
                .dmd-live-text { font-size:11px; line-height:1.3; }
                .dmd-live-link { font-size:10px; }
                .dmd-live-ago { font-size:9px; }
            }
        </style>

        <script>
            (function () {
                var demandes = @json($recentDemandes);
                if (!demandes.length) return;

                var stack = document.getElementById('dmdLiveStack');
                var live  = document.getElementById('dmdLive');
                var isMobile = function () { return window.matchMedia('(max-width:600px)').matches; };
                var MAX_VISIBLE = isMobile() ? 1 : 3;

                // Position the widget just under the real navbar height on mobile
                function placeBelowNavbar() {
                    if (!live) return;
                    if (isMobile()) {
                        var nav = document.querySelector('.nado-navbar');
                        var h = nav ? nav.getBoundingClientRect().height : 110;
                        live.style.top = (h + 10) + 'px';
                    } else {
                        live.style.top = ''; // desktop keeps CSS default (bottom-left)
                    }
                }
                placeBelowNavbar();
                window.addEventListener('resize', function () {
                    MAX_VISIBLE = isMobile() ? 1 : 3;
                    placeBelowNavbar();
                });
                window.addEventListener('load', placeBelowNavbar);

                // JS fallback: hide/show #dmdLive when the mobile drawer opens/closes
                (function () {
                    var drawer = document.getElementById('mobileDrawer');
                    if (!drawer || !live) return;
                    var mo = new MutationObserver(function () {
                        var open = drawer.classList.contains('visible') || drawer.classList.contains('nb-visible');
                        live.style.display = open ? 'none' : '';
                    });
                    mo.observe(drawer, { attributes: true, attributeFilter: ['class'] });
                })();
                var i = 0;

                function escapeHtml(s) {
                    return String(s).replace(/[&<>"']/g, function (c) {
                        return {'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'}[c];
                    });
                }

                function pushCard(d) {
                    var card = document.createElement('div');
                    card.className = 'dmd-live-card';
                    card.innerHTML =
                        '<div class="dmd-live-av">' + escapeHtml((d.name.charAt(0) || '?').toUpperCase()) + '</div>' +
                        '<div style="min-width:0;flex:1;">' +
                            '<div class="dmd-live-tag"><span class="dmd-live-dot"></span>Nouvelle demande</div>' +
                            '<div class="dmd-live-text"><strong>' + escapeHtml(d.name) + '</strong> cherche ' + escapeHtml(d.car) + (d.ville ? ' à ' + escapeHtml(d.ville) : '') + '</div>' +
                            '<a class="dmd-live-link" href="' + d.url + '">Voir la demande →</a>' +
                            '<div class="dmd-live-ago">' + escapeHtml(d.ago) + '</div>' +
                        '</div>';
                    // column-reverse → prepending puts the new card at the BOTTOM of the visual stack
                    stack.insertBefore(card, stack.firstChild);

                    // If too many visible, remove the oldest (last in the DOM because of column-reverse)
                    var cards = stack.querySelectorAll('.dmd-live-card:not(.leaving)');
                    if (cards.length > MAX_VISIBLE) {
                        var oldest = cards[cards.length - 1];
                        oldest.classList.add('leaving');
                        setTimeout(function () { oldest.remove(); }, 400);
                    }
                }

                function tick() {
                    // On mobile (MAX_VISIBLE=1), make the current card leave BEFORE the new one arrives,
                    // so the slider effect is: old card slides out to the left, then new card slides in from the right.
                    if (MAX_VISIBLE === 1) {
                        var current = stack.querySelector('.dmd-live-card:not(.leaving)');
                        if (current) {
                            current.classList.add('leaving');
                            setTimeout(function () { current.remove(); }, 300);
                            setTimeout(function () { pushCard(demandes[i]); i = (i + 1) % demandes.length; }, 320);
                            return;
                        }
                    }
                    pushCard(demandes[i]);
                    i = (i + 1) % demandes.length;
                }

                // First card after 1.5 s, then one every 5 s.
                setTimeout(function () {
                    tick();
                    setInterval(tick, 5000);
                }, 1500);
            })();
        </script>
    @endif
    {{-- ============ /DEMANDE LIVE FEED ============== --}}


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
                        <a href="{{ route('client.login') }}" class="cta-button outline-btn cta-publish">
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
                    <span class="logo-text">
                        @if ($isAutoEnchere)
                            <span class="text-dark">AUTO </span>
                            <span style="color: #F37021;">ENCHÈRE</span>
                        @else
                            <span style="color: #1459DD;">NA</span>
                            <span style="color: #FDD817;">DO</span>
                        @endif
                    </span>
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

                    <div style="margin:18px 0 6px;display:flex;justify-content:center;">@include('partials.lang-switcher')</div>

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
                gap: 20px;
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
                /* Was display:none/block, which vanished the instant hover was lost.
                   opacity+visibility can be delayed, so the menu lingers ~400ms after
                   the cursor leaves, giving time to move onto it. */
                opacity: 0;
                visibility: hidden;
                transform: translateY(-4px);
                transition: opacity .18s ease .4s, transform .18s ease .4s, visibility 0s linear .58s;
            }

            /* Keep it open while over the button OR over the menu itself. */
            .user-dropdown-wrapper:hover .user-dropdown-menu,
            .user-dropdown-menu:hover {
                opacity: 1;
                visibility: visible;
                transform: translateY(0);
                transition: opacity .15s ease, transform .15s ease, visibility 0s;
            }

            /* Invisible bridge over the 8px gap so moving down doesn't drop the hover. */
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

            /* ===== Navbar single-line fixes (must stay last in this block) ===== */

            /* The base .cta-button rule above sets no white-space, so the label wrapped. */
            .cta-button,
            .cta-button span {
                white-space: nowrap !important;
                word-break: keep-all !important;
                overflow-wrap: normal !important;
                hyphens: none !important;
            }
            .cta-button { flex-shrink: 0 !important; text-align: center !important; }

            /* Keep the full "Publier une annonce" on one line by scaling it with the
               viewport. clamp() shrinks continuously instead of stepping at breakpoints,
               so there is no width where the label is still too wide for its box. */
            @media (max-width: 1023px) {
                .right-section .cta-button {
                    font-size: clamp(8.5px, 2.5vw, 13px) !important;
                    padding: 9px 11px !important;
                    letter-spacing: 0.01em !important;
                    border-radius: 12px !important;
                    gap: 6px !important;
                }
                /* Give the button the room it needs; the logo absorbs the difference. */
                .right-section { gap: 8px !important; padding-left: 8px !important; flex-shrink: 0 !important; }
                .left-section  { min-width: 0 !important; flex-shrink: 1 !important; overflow: hidden !important; }
            }

            /* Drawer CTA: same problem as the header button. At 14px with 0.2em tracking
               the label needs more width than the drawer leaves inside its padding, so
               scale it and tighten the tracking. Keeps display:flex centering intact. */
            .drawer-cta,
            .drawer-cta span {
                white-space: nowrap !important;
                word-break: keep-all !important;
                overflow-wrap: normal !important;
                hyphens: none !important;
            }
            .drawer-cta { text-align: center !important; }
            .drawer-cta i,
            .drawer-cta svg { flex-shrink: 0 !important; }

            @media (max-width: 1023px) {
                .drawer-cta {
                    font-size: clamp(9px, 2.9vw, 14px) !important;
                    letter-spacing: 0.06em !important;
                    padding: 18px 14px !important;
                    gap: 8px !important;
                }
            }
            @media (max-width: 380px) {
                .drawer-cta {
                    letter-spacing: 0.03em !important;
                    padding: 16px 10px !important;
                }
            }

            /* Top bar: keep the phone number on one line. */
            .info-item,
            .info-item a {
                white-space: nowrap !important;
                word-break: keep-all !important;
            }
            .info-item i { flex-shrink: 0 !important; }

            @media (max-width: 600px) {
                /* Socials are duplicated in the drawer; contact details matter more. */
                .top-bar .social-links { display: none !important; }
                .top-bar { padding: 8px 0 !important; }
                .top-bar .custom-container { padding: 0 10px !important; }
                .contact-info { gap: 10px !important; }
                .info-item { font-size: 9px !important; letter-spacing: 0.02em !important; gap: 5px !important; }
            }
            @media (max-width: 380px) {
                /* Last resort: drop the email, keep the phone. */
                .contact-info .info-item:first-child { display: none !important; }
                .info-item { font-size: 10px !important; }
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

    <!-- CONTENT -->
    <div class="flex flex-col bg-white" style="padding-top: 140px;">

        <!-- 1. HERO -->
        <section class="hero">
            <div class="hero-bg">
                {{-- Hero image(s): the active images of the admin "Image slider" tab.
                     1 active image → fixed; several → they rotate; none → default photo. --}}
                @php
                    $heroSlides = collect($imgslider ?? [])->filter(fn ($slide) => !empty($slide->image))->values();
                @endphp
                @if ($heroSlides->count() > 1)
                    <div class="hero-slider owl-carousel">
                        @foreach ($heroSlides as $slide)
                            <div class="item">
                                <img src="{{ asset('storage/imgslider/' . $slide->image) }}" alt="{{ $slide->name }}"
                                    @if ($loop->first) fetchpriority="high" @else loading="lazy" @endif />
                            </div>
                        @endforeach
                    </div>
                @elseif ($heroSlides->count() === 1)
                    <img src="{{ asset('storage/imgslider/' . $heroSlides[0]->image) }}" alt="{{ $heroSlides[0]->name }}"
                        fetchpriority="high" />
                @else
                    <img src="https://images.unsplash.com/photo-1614162692292-7ac56d7f7f1e?auto=format&fit=crop&q=80&w=2400"
                        alt="Hero" />
                @endif
                <div class="hero-overlay"></div>
            </div>
            <div class="container hero-content">
                <div class="hero-badge">
                    <span
                        style="width: 8px; height: 8px; border-radius: 50%; background-color: var(--secondary); display: inline-block;"></span>
                    <span>{{ __('messages.hero.badge') }}</span>
                </div>
                <h1 class="hero-title">{{ __('messages.hero.title_1') }} <br /><span class="text-gradient">{{ __('messages.hero.title_2') }}</span></h1>
                <p class="hero-desc">{{ __('messages.hero.desc') }}</p>
            </div>
        </section>

        <!-- 3. SEARCH & BRANDS -->
        <section class="search-section">
            <div class="container" style="max-width: 64rem;">
                <div class="search-bar">
                    <!-- Marque -->
                    <div class="search-item">
                        <i data-lucide="car-front" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <div style="flex: 1;">
                            <label>{{ __('messages.search.brand') }}</label>
                            <select id="search-brand" name="brand">
                                <option value="" data-name="">{{ __('messages.search.all_brands') }}</option>
                                @foreach ($brands as $brand)
                                    <option value="{{ $brand->id }}" data-name="{{ $brand->name }}">{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="divider"></div>
                    </div>

                    <!-- Modele -->
                    <div class="search-item">
                        <i data-lucide="settings-2" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <div style="flex: 1;">
                            <label>{{ __('messages.search.model') }}</label>
                            <select id="search-model" name="model">
                                <option value="">{{ __('messages.search.all_models') }}</option>
                            </select>
                        </div>
                        <div class="divider"></div>
                    </div>

                    <!-- Budget -->
                    <div class="search-item">
                        <i data-lucide="tags" style="color: var(--primary); width: 18px; height: 18px;"></i>
                        <div style="flex: 1;">
                            <label>Budget Max</label>
                            <select id="search-budget">
                                <option value="">Indifférent</option>
                                <option value="100000">100 000 MAD</option>
                                <option value="200000">200 000 MAD</option>
                                <option value="300000">300 000 MAD</option>
                                <option value="500000">500 000 MAD</option>
                                <option value="800000">800 000 MAD</option>
                                <option value="1000000">1 000 000 MAD</option>
                                <option value="1500000">1 500 000 MAD</option>
                            </select>
                        </div>
                    </div>

                    <button class="btn-find" id="btn-search-find">
                        <i data-lucide="search" style="width: 18px; height: 18px; stroke-width: 3;"></i>
                        <span>Trouver</span>
                    </button>
                </div>
            </div>

            <!-- Logos -->
            <div class="brands-ticker">

                <div class="ticker-track">
                    @foreach ($brands as $brand)
                        <a href="{{ route('achat.index', ['marque' => str_replace(' ', '-', $brand->name)]) }}"
                            class="brand-logo-link"
                            aria-label="Voir les annonces {{ $brand->name }}">
                            <img src="{{ asset('storage/brands/' . $brand->logo) }}" class="brand-logo"
                                alt="{{ $brand->name }}" />
                        </a>
                    @endforeach
                    <!-- Duplicate for infinite scroll -->
                    @foreach ($brands as $brand)
                        <a href="{{ route('achat.index', ['marque' => str_replace(' ', '-', $brand->name)]) }}"
                            class="brand-logo-link"
                            aria-hidden="true"
                            tabindex="-1">
                            <img src="{{ asset('storage/brands/' . $brand->logo) }}" class="brand-logo"
                                alt="" />
                        </a>
                    @endforeach
                </div>
            </div>
        </section>


        <!-- 3b. DERNIERS VEHICULES -->
        <section class="listings-section" id="derniers-vehicules" style="background-color: var(--white);">
            <div class="container">
                <h2 class="section-title">{!! __('messages.home.vehicles_heading') !!}</h2>

                <div class="dv-wrap">
                    <button type="button" class="dv-btn dv-btn-prev" id="dv-prev" aria-label="Précédent">
                        <i data-lucide="chevron-left" style="width:20px;height:20px;"></i>
                    </button>

                <div class="listings-grid">
                    @foreach ($latestAds->take(6) as $ad)
                        <a href="{{ route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad['id']]) }}"
                            class="listing-card" style="text-decoration: none; color: inherit; display: block;">
                            <div class="listing-image-container">
                                <img src="{{ asset('storage/ads/' . $ad->pathImageAds()) }}" class="listing-main-img"
                                    alt="Annonce" style="width: 100%; height: 100%; object-fit: cover;" />
                                <div class="listing-status-badge status-available">DISPONIBLE</div>
                                @if(!empty($ad->certifie))
                                    <div class="listing-type-badge type-certifie">Certifié</div>
                                @elseif(!empty($ad->particulie))
                                    <div class="listing-type-badge type-particulier">Particulier</div>
                                @elseif(!empty($ad->pro))
                                    <div class="listing-type-badge type-pro">Pro</div>
                                @endif
                                <div class="listing-price-tag">
                                    <span class="listing-price-text">{{ number_format($ad->prix, 0, ',', ' ') }}
                                        MAD</span>
                                </div>
                                <div class="listing-video-preview">
                                    <img src="{{ asset('storage/ads/' . $ad->pathImageAds()) }}"
                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                    <div class="listing-play-btn">
                                        <div class="listing-play-icon">
                                            <i data-lucide="play"
                                                style="width: 12px; height: 12px; fill: white; margin-left: 2px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="listing-content">
                                <h3 class="listing-title-new">
                                    @php
                                        // Fall back to the raw columns, then the ad title, instead of printing
                                        // the literal words "Marque"/"Modèle" when a relation is null.
                                        // mark/modele hold ids on newer rows and plain names on migrated ones,
                                        // so a numeric value is treated as "no usable name".
                                        $adBrandName = $ad->brand?->name
                                            ?? (is_numeric($ad->mark ?? null) ? null : $ad->mark);
                                        $adModelName = $ad->modeleRelation?->name
                                            ?? (is_numeric($ad->modele ?? null) ? null : $ad->modele);
                                        $adCardTitle = trim(($adBrandName ?? '') . ' ' . ($adModelName ?? ''));
                                    @endphp
                                    {{ $adCardTitle !== '' ? $adCardTitle : ($ad->titre ?? 'Véhicule') }}
                                </h3>
                                <div class="listing-specs-grid">
                                    <div class="listing-spec-item"><i data-lucide="calendar"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->year }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="gauge"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->km }} km</span></div>
                                    <div class="listing-spec-item"><i data-lucide="settings-2"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->boite_vitesse ?? 'Auto' }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="fuel"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->carburant }}</span></div>
                                </div>
                                <div class="listing-footer-btn">
                                    <div class="flex flex-col">
                                        <span class="monthly-label">VOTRE MENSUALITÉ À PARTIR DE</span>
                                        <div class="monthly-price">
                                            <span
                                                class="monthly-amount">{{ number_format(($ad->prix ?? 0) / 72, 0, ',', ' ') }}</span>
                                            <span class="monthly-unit">MAD / MOIS</span>
                                        </div>
                                    </div>
                                    <div class="arrow-circle"><i data-lucide="arrow-right"
                                            style="width: 18px; height: 18px;"></i></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
                    <button type="button" class="dv-btn dv-btn-next" id="dv-next" aria-label="Suivant">
                        <i data-lucide="chevron-right" style="width:20px;height:20px;"></i>
                    </button>
                </div>

                <a href="{{ route('achat.index') }}" class="btn-view-all"><span>{{ __('messages.home.view_all_catalog') }}</span><i
                        data-lucide="arrow-right" style="width: 20px; height: 20px;"></i></a>
            </div>
        </section>


        <!-- 2. STORIES -->
        <section class="stories">
            <div class="container">
                <div class="story-header">
                    <h2 class="story-title">{!! __('messages.home.story_heading') !!}</h2>
                </div>

                <div class="story-scroll no-scrollbar">
                    @foreach ($stories as $story)
                        @php
                            // $story is a stdClass here (raw query / cached payload), not an Eloquent
                            // model, so a missing property throws instead of returning null.
                            $instaUrl = $story->instagram_link ?? '';
                            $instaEmbed = '';
                            $instaThumb = '';
                            // /p/, /reel/, /reels/ and /tv/ all embed via {type}/{code}/embed
                            if ($instaUrl && preg_match('#instagram\.com/(p|reel|reels|tv)/([A-Za-z0-9_-]+)#', $instaUrl, $m)) {
                                $instaType = $m[1] === 'reels' ? 'reel' : $m[1];
                                $instaEmbed = 'https://www.instagram.com/' . $instaType . '/' . $m[2] . '/embed';
                                // Instagram's public thumbnail endpoint, hotlinked directly.
                                // If it is blocked the <img> onerror swaps in the placeholder.
                                $instaThumb = 'https://www.instagram.com/p/' . $m[2] . '/media/?size=l';
                            }
                        @endphp
                        <div class="nado-story-item" onclick="event.stopPropagation(); launchNadoStory(this)"
                            {{-- Empty when there is no file: asset('storage/' . null) returns
                                 ".../storage", which is a valid-looking URL and used to be fed
                                 to <video>, showing an empty black player. --}}
                            data-src="{{ $story->media_path ? asset('storage/' . $story->media_path) : '' }}"
                            data-type="{{ $story->media_type }}"
                            data-insta="{{ $instaUrl }}"
                            data-insta-embed="{{ $instaEmbed }}"
                            style="flex-shrink: 0; width: 230px; aspect-ratio: 9/16; position: relative; border-radius: 2rem; overflow: hidden; cursor: pointer; border: 1px solid rgba(255, 255, 255, 0.05); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);">
                            @if (empty($story->media_path) && $instaEmbed)
                                {{-- Instagram blocks both server-side download and thumbnail
                                     hotlinking, so show the embed itself: scaled up and offset
                                     so only the post's media fills the tile (its header and
                                     footer are cropped off). pointer-events:none keeps the
                                     click on the tile, which opens our popup. --}}
                                <div class="insta-thumb-wrap"
                                    style="position: absolute; inset: 0; overflow: hidden; background: #000;">
                                    <iframe src="{{ $instaEmbed }}" scrolling="no" frameborder="0" loading="lazy"
                                        tabindex="-1" aria-hidden="true"
                                        style="position: absolute; top: -52px; left: -71px; width: 400px; height: 900px; border: 0; transform: scale(.93); transform-origin: top left; pointer-events: none;"></iframe>
                                </div>
                                {{-- Play badge over the preview --}}
                                <span
                                    style="position: absolute; top: 50%; left: 50%; transform: translate(-50%,-50%); width: 60px; height: 60px; border-radius: 50%; background: rgba(255,255,255,.92); display: flex; align-items: center; justify-content: center; pointer-events: none; z-index: 2;">
                                    <svg width="24" height="24" viewBox="0 0 24 24" fill="#DD2A7B">
                                        <polygon points="6 4 20 12 6 20 6 4" />
                                    </svg>
                                </span>
                            @endif
                            @if (empty($story->media_path) && !$instaEmbed)
                                {{-- Last resort: no local media and no parsable Instagram post. --}}
                                <div class="bg-img"
                                    style="width: 100%; height: 100%; display: flex; flex-direction: column; gap: 10px; align-items: center; justify-content: center;
                                           background: linear-gradient(45deg, #F58529, #DD2A7B, #8134AF, #515BD4);">
                                    <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff"
                                        stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                                    </svg>
                                    {{-- Play badge: this tile opens a playable embed, not a link. --}}
                                    <span
                                        style="width: 42px; height: 42px; border-radius: 50%; background: rgba(255,255,255,.92); display: flex; align-items: center; justify-content: center;">
                                        <svg width="18" height="18" viewBox="0 0 24 24" fill="#DD2A7B">
                                            <polygon points="6 4 20 12 6 20 6 4" />
                                        </svg>
                                    </span>
                                </div>
                            @elseif ($story->media_type == 'image')
                                <img src="{{ asset('storage/' . $story->media_path) }}" class="bg-img"
                                    alt="Story" style="width: 100%; height: 100%; object-fit: cover;" />
                            @else
                                <video src="{{ asset('storage/' . $story->media_path) }}" class="bg-img" muted loop
                                    autoplay playsinline style="width: 100%; height: 100%; object-fit: cover;"></video>
                            @endif
                            <div class="story-gradient"
                                style="position: absolute; inset: 0; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                            </div>

                            <div class="story-info" style="position: absolute; bottom: 1.25rem; left: 1.25rem;">
                                <span class="story-name" style="color: white; font-weight: 900; font-size: 11px;">NADO
                                    Story</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- STORY MODAL (Popup) - RENAMED -->

        <div id="nado-story-popup"
            style="display: none; position: fixed; top: 0; left: 0; width: 100vw; height: 100vh; background-color: rgba(0, 0, 0, 0.85); backdrop-filter: blur(4px); z-index: 999999; align-items: center; justify-content: center; overflow-y: auto; padding: 24px 12px;">

            <button onclick="dismissNadoStory()"
                style="position: absolute; top: 20px; right: 20px; color: white; background: rgba(255, 255, 255, 0.2); width: 44px; height: 44px; border-radius: 50%; border: none; cursor: pointer; z-index: 1000000; font-size: 2rem; display: flex; align-items: center; justify-content: center; transition: background 0.2s;">
                &times;
            </button>

            <!-- Card Container -->
            <div
                style="position: relative; background: #ffffff; padding: 12px; border-radius: 20px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); display: flex; flex-direction: column; width: auto; max-width: 90vw; max-height: 92vh; overflow-y: auto; margin: auto;">

                <!-- Card Header -->
                <div style="display: flex; align-items: center; gap: 10px; margin-bottom: 12px; padding: 0 4px;">
                    <div
                        style="width: 32px; height: 32px; border-radius: 50%; background: #1459DD; display: flex; align-items: center; justify-content: center; color: white; font-weight: bold; font-size: 10px;">
                        N</div>
                    <div style="display: flex; flex-direction: column;">
                        <span style="font-weight: 700; color: #1e293b; font-size: 14px; line-height: 1;">NADO
                            Officiel</span>
                        <span style="font-size: 11px; color: #64748b;">En direct</span>
                    </div>
                </div>

                <!-- Media Content -->
                <div style="position: relative; border-radius: 12px; overflow: hidden; background: #000;">
                    <img id="nado-story-img" src=""
                        style="max-height: 75vh; height: auto; max-width: 100%; object-fit: contain; display: none;" />
                    <video id="nado-story-video" src=""
                        style="max-height: 75vh; height: auto; max-width: 100%; object-fit: contain; display: none;"
                        controls autoplay></video>
                    {{-- Instagram post played inline via the official /embed endpoint. --}}
                    <iframe id="nado-story-insta-frame" src="" title="Publication Instagram"
                        frameborder="0" scrolling="no" allowtransparency="true" allowfullscreen
                        allow="autoplay; clipboard-write; encrypted-media; picture-in-picture; web-share"
                        style="display: none; width: 400px; max-width: 86vw; height: 78vh; max-height: 700px; border: 0; background: #fff;"></iframe>
                </div>

                <!-- Instagram link (shown only when the story has one) -->
                <a id="nado-story-insta" href="#" target="_blank" rel="noopener noreferrer"
                    style="display: none; align-items: center; justify-content: center; gap: 8px;
                          margin-top: 12px; padding: 12px 20px; border-radius: 999px;
                          background: linear-gradient(45deg, #F58529, #DD2A7B, #8134AF, #515BD4);
                          color: #fff; font-weight: 800; font-size: 13px; letter-spacing: .06em;
                          text-transform: uppercase; text-decoration: none;">
                    {{-- Inline SVG, not <i data-lucide="instagram">: current Lucide dropped
                         brand icons, so that renders nothing (see console warnings). --}}
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                        stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;">
                        <rect x="2" y="2" width="20" height="20" rx="5" ry="5" />
                        <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z" />
                        <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" />
                    </svg>
                    <span>Voir sur Instagram</span>
                </a>
            </div>
        </div>

        <!-- 4. ANNONCES CERTIFIÉES -->
        <section class="listings-section" id="annonces-certifiees">
            <div class="container">
                <h2 class="section-title">{!! __('messages.home.certified_heading') !!}</h2>

                <div class="listings-grid">
                    @foreach ($similar_ads->take(3) as $ad)
                        <a href="{{ route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad['id']]) }}"
                            class="listing-card" style="text-decoration: none; color: inherit; display: block;">
                            <div class="listing-image-container">
                                <img src="{{ asset('storage/ads/' . $ad->pathImageAds()) }}" class="listing-main-img"
                                    alt="Annonce" style="width: 100%; height: 100%; object-fit: cover;" />
                                <div class="listing-status-badge status-available">DISPONIBLE</div>
                                @if(!empty($ad->certifie))
                                    <div class="listing-type-badge type-certifie">Certifié</div>
                                @elseif(!empty($ad->particulie))
                                    <div class="listing-type-badge type-particulier">Particulier</div>
                                @elseif(!empty($ad->pro))
                                    <div class="listing-type-badge type-pro">Pro</div>
                                @endif
                                <div class="listing-price-tag">
                                    <span class="listing-price-text">{{ number_format($ad->prix, 0, ',', ' ') }}
                                        MAD</span>
                                </div>
                                <div class="listing-video-preview">
                                    <img src="{{ asset('storage/ads/' . $ad->pathImageAds()) }}"
                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                    <div class="listing-play-btn">
                                        <div class="listing-play-icon">
                                            <i data-lucide="play"
                                                style="width: 12px; height: 12px; fill: white; margin-left: 2px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="listing-content">
                                <h3 class="listing-title-new">
                                    @php
                                        // Fall back to the raw columns, then the ad title, instead of printing
                                        // the literal words "Marque"/"Modèle" when a relation is null.
                                        // mark/modele hold ids on newer rows and plain names on migrated ones,
                                        // so a numeric value is treated as "no usable name".
                                        $adBrandName = $ad->brand?->name
                                            ?? (is_numeric($ad->mark ?? null) ? null : $ad->mark);
                                        $adModelName = $ad->modeleRelation?->name
                                            ?? (is_numeric($ad->modele ?? null) ? null : $ad->modele);
                                        $adCardTitle = trim(($adBrandName ?? '') . ' ' . ($adModelName ?? ''));
                                    @endphp
                                    {{ $adCardTitle !== '' ? $adCardTitle : ($ad->titre ?? 'Véhicule') }}
                                </h3>
                                <div class="listing-specs-grid">
                                    <div class="listing-spec-item"><i data-lucide="calendar"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->year }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="gauge"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->km }} km</span></div>
                                    <div class="listing-spec-item"><i data-lucide="settings-2"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->boite_vitesse ?? 'Auto' }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="fuel"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->carburant }}</span></div>
                                </div>
                                <div class="listing-footer-btn">
                                    <div class="flex flex-col">
                                        <span class="monthly-label">VOTRE MENSUALITÉ À PARTIR DE</span>
                                        <div class="monthly-price">
                                            <span
                                                class="monthly-amount">{{ number_format(($ad->prix ?? 0) / 72, 0, ',', ' ') }}</span>
                                            <span class="monthly-unit">MAD / MOIS</span>
                                        </div>
                                    </div>
                                    <div class="arrow-circle"><i data-lucide="arrow-right"
                                            style="width: 18px; height: 18px;"></i></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>

                <a href="{{ route('achat.index') }}" class="btn-view-all"><span>{{ __('messages.home.view_all_catalog') }}</span><i
                        data-lucide="arrow-right" style="width: 20px; height: 20px;"></i></a>
            </div>
        </section>


        <!-- 6. PUB NADO EXPERT -->
        <section class="promo-section">
            <div class="container">
                <div class="expert-card">
                    <div class="expert-content">
                        <div class="label-trust"><i data-lucide="shield-check"
                                style="color: var(--secondary); width: 24px; height: 24px;"></i><span>{{ __('messages.home.trust_label') }}</span></div>
                        <h2 class="expert-title">{!! __('messages.home.expertise_heading') !!}</h2>
                        <p class="expert-desc">{{ __('messages.home.expertise_desc') }}</p>
                    </div>
                    <div class="expert-image"><img
                            src="https://images.unsplash.com/photo-1619642751034-765dfdf7c58e?auto=format&fit=crop&q=80&w=800"
                            alt="Expertise" /></div>
                </div>
            </div>
        </section>

        <!-- 7. ANNONCES PARTICULIERS -->
        @if(($similar_adsp ?? collect())->isNotEmpty())
        <section class="listings-section" style="background-color: var(--white);">
            <div class="container">
                <h2 class="section-title">{!! __('messages.home.particuliers_heading') !!}</h2>

                <div class="listings-grid">
                    @foreach ($similar_adsp->take(3) as $ad)
                        <a href="{{ route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad['id']]) }}"
                            class="listing-card" style="text-decoration: none; color: inherit; display: block;">
                            <div class="listing-image-container">
                                <img src="{{ isset($ad->images[0]) ? asset('storage/ads/' . $ad->images[0]) : asset('images/default.jpg') }}"
                                    class="listing-main-img" alt="Annonce"
                                    style="width: 100%; height: 100%; object-fit: cover;" />
                                <div class="listing-status-badge status-available">DISPONIBLE</div>
                                @if(!empty($ad->certifie))
                                    <div class="listing-type-badge type-certifie">Certifié</div>
                                @elseif(!empty($ad->particulie))
                                    <div class="listing-type-badge type-particulier">Particulier</div>
                                @elseif(!empty($ad->pro))
                                    <div class="listing-type-badge type-pro">Pro</div>
                                @endif
                                <div class="listing-price-tag">
                                    <span class="listing-price-text">{{ number_format($ad->prix, 0, ',', ' ') }}
                                        MAD</span>
                                </div>
                                <div class="listing-video-preview">
                                    <img src="{{ isset($ad->images[0]) ? asset('storage/ads/' . $ad->images[0]) : asset('images/default.jpg') }}"
                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                    <div class="listing-play-btn">
                                        <div class="listing-play-icon">
                                            <i data-lucide="play"
                                                style="width: 12px; height: 12px; fill: white; margin-left: 2px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="listing-content">
                                <h3 class="listing-title-new">
                                    @php
                                        // Fall back to the raw columns, then the ad title, instead of printing
                                        // the literal words "Marque"/"Modèle" when a relation is null.
                                        // mark/modele hold ids on newer rows and plain names on migrated ones,
                                        // so a numeric value is treated as "no usable name".
                                        $adBrandName = $ad->brand?->name
                                            ?? (is_numeric($ad->mark ?? null) ? null : $ad->mark);
                                        $adModelName = $ad->modeleRelation?->name
                                            ?? (is_numeric($ad->modele ?? null) ? null : $ad->modele);
                                        $adCardTitle = trim(($adBrandName ?? '') . ' ' . ($adModelName ?? ''));
                                    @endphp
                                    {{ $adCardTitle !== '' ? $adCardTitle : ($ad->titre ?? 'Véhicule') }}
                                </h3>
                                <div class="listing-specs-grid">
                                    <div class="listing-spec-item"><i data-lucide="calendar"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->year }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="gauge"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->km }} km</span></div>
                                    <div class="listing-spec-item"><i data-lucide="settings-2"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->boite_vitesse ?? 'Auto' }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="fuel"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->carburant }}</span></div>
                                </div>
                                <div class="listing-footer-btn">
                                    <div class="flex flex-col">
                                        <span class="monthly-label">VOTRE MENSUALITÉ À PARTIR DE</span>
                                        <div class="monthly-price">
                                            <span
                                                class="monthly-amount">{{ number_format(($ad->prix ?? 0) / 72, 0, ',', ' ') }}</span>
                                            <span class="monthly-unit">MAD / MOIS</span>
                                        </div>
                                    </div>
                                    <div class="arrow-circle"><i data-lucide="arrow-right"
                                            style="width: 18px; height: 18px;"></i></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- 8. FINANCEMENT FLASH -->
        <section class="finance-section">
            <div class="container">
                <div class="finance-card">
                    <div class="expert-content">
                        <div class="label-trust"><i data-lucide="hand-coins"
                                style="color: var(--slate-900); width: 24px; height: 24px;"></i><span
                                style="color: var(--slate-900);">{{ __('messages.home.finance_solutions') }}</span></div>
                        <h2 class="expert-title" style="color: var(--slate-900);">{!! __('messages.home.financement_heading') !!}</h2>
                        <p class="expert-desc" style="color: rgba(15, 23, 42, 0.7); font-weight: 700;">{{ __('messages.home.financement_desc') }}</p>
                        <a class="btn-discover" href="{{ route('financement.index') }}"
                            style="background-color: var(--slate-900); color: var(--white); text-decoration: none;">
                            {{ __('messages.home.simulate_credit') }}
                            <i data-lucide="trending-up" style="width: 16px; height: 16px;"></i>
                        </a>
                    </div>
                    <div class="finance-image"><img
                            src="https://images.unsplash.com/photo-1554224155-169641357599?auto=format&fit=crop&q=80&w=800"
                            alt="Finance" /></div>
                </div>
            </div>
        </section>

        <!-- 9. ANNONCES PROS -->
        @if(($similar_ads_pro ?? collect())->isNotEmpty())
        <section class="listings-section">
            <div class="container">
                <div class="section-header">
                    <h2 class="section-title">{!! __('messages.home.pros_heading') !!}</h2>
                </div>
                <div class="listings-grid">
                    @foreach (($similar_ads_pro ?? collect())->take(3) as $ad)
                        <a href="{{ route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad['id']]) }}"
                            class="listing-card" style="text-decoration: none; color: inherit; display: block;">
                            <div class="listing-image-container">
                                <img src="{{ isset($ad->images[0]) ? asset('storage/ads/' . $ad->images[0]) : asset('images/default.jpg') }}"
                                    class="listing-main-img" alt="Annonce"
                                    style="width: 100%; height: 100%; object-fit: cover;" />
                                <div class="listing-status-badge status-available">DISPONIBLE</div>
                                @if(!empty($ad->certifie))
                                    <div class="listing-type-badge type-certifie">Certifié</div>
                                @elseif(!empty($ad->particulie))
                                    <div class="listing-type-badge type-particulier">Particulier</div>
                                @elseif(!empty($ad->pro))
                                    <div class="listing-type-badge type-pro">Pro</div>
                                @endif
                                <div class="listing-price-tag">
                                    <span class="listing-price-text">{{ number_format($ad->prix, 0, ',', ' ') }}
                                        MAD</span>
                                </div>
                                <div class="listing-video-preview">
                                    <img src="{{ isset($ad->images[0]) ? asset('storage/ads/' . $ad->images[0]) : asset('images/default.jpg') }}"
                                        style="width: 100%; height: 100%; object-fit: cover;" />
                                    <div class="listing-play-btn">
                                        <div class="listing-play-icon">
                                            <i data-lucide="play"
                                                style="width: 12px; height: 12px; fill: white; margin-left: 2px;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="listing-content">
                                <h3 class="listing-title-new">
                                    @php
                                        // Fall back to the raw columns, then the ad title, instead of printing
                                        // the literal words "Marque"/"Modèle" when a relation is null.
                                        // mark/modele hold ids on newer rows and plain names on migrated ones,
                                        // so a numeric value is treated as "no usable name".
                                        $adBrandName = $ad->brand?->name
                                            ?? (is_numeric($ad->mark ?? null) ? null : $ad->mark);
                                        $adModelName = $ad->modeleRelation?->name
                                            ?? (is_numeric($ad->modele ?? null) ? null : $ad->modele);
                                        $adCardTitle = trim(($adBrandName ?? '') . ' ' . ($adModelName ?? ''));
                                    @endphp
                                    {{ $adCardTitle !== '' ? $adCardTitle : ($ad->titre ?? 'Véhicule') }}
                                </h3>
                                <div class="listing-specs-grid">
                                    <div class="listing-spec-item"><i data-lucide="calendar"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->year }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="gauge"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->km }} km</span></div>
                                    <div class="listing-spec-item"><i data-lucide="settings-2"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->boite_vitesse ?? 'Auto' }}</span></div>
                                    <div class="listing-spec-item"><i data-lucide="fuel"
                                            style="width: 14px; height: 14px; color: #1459DD;"></i><span
                                            class="listing-spec-label">{{ $ad->carburant }}</span></div>
                                </div>
                                <div class="listing-footer-btn">
                                    <div class="flex flex-col">
                                        <span class="monthly-label">VOTRE MENSUALITÉ À PARTIR DE</span>
                                        <div class="monthly-price">
                                            <span
                                                class="monthly-amount">{{ number_format(($ad->prix ?? 0) / 72, 0, ',', ' ') }}</span>
                                            <span class="monthly-unit">MAD / MOIS</span>
                                        </div>
                                    </div>
                                    <div class="arrow-circle"><i data-lucide="arrow-right"
                                            style="width: 18px; height: 18px;"></i></div>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
        @endif

        <!-- 5. NADO PRICE -->
        <section class="price-section">
            <div class="container relative z-10">
                <div class="price-grid">
                    <div>
                        <h2 class="price-title">{!! __('messages.home.price_heading') !!}
                        </h2>
                        <p class="price-desc">{{ __('messages.home.price_desc') }}</p>
                        <a href="{{ route('estimate.page') }}" class="btn-estimate" style="text-decoration: none; display: inline-flex;"><span>{{ __('messages.home.estimate_my_car') }}</span><i data-lucide="arrow-right"
                                style="width: 20px; height: 20px;"></i></a>
                    </div>
                    <div class="price-image-container"><img
                            src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1200"
                            alt="Estimation" /></div>
                </div>
            </div>
        </section>

        <!-- 10. EXPÉRIENCES IMMERSIVES -->
        <section class="immersive-section">
            <div class="container">
                <div style="margin-bottom: 4rem;">
                    <h2 class="section-title " style="color: white">{!! __('messages.home.immersive_heading') !!}</h2>
                </div>
                <div class="immersive-scroll no-scrollbar">
                    @forelse ($videoAds as $vid)
                    {{-- href kept as a fallback: the JS below intercepts the click and
                         plays the video in a modal on the site instead. --}}
                    <a href="https://www.youtube.com/watch?v={{ $vid->video_key }}" target="_blank"
                       class="video-card js-video" data-yt="{{ $vid->video_key }}"
                       data-title="{{ $vid->title }}" style="text-decoration: none;">
                        {{-- hqdefault exists for every video; maxres does not, which is why
                             the previous thumbnails were often blank. --}}
                        <img src="https://img.youtube.com/vi/{{ $vid->video_key }}/hqdefault.jpg"
                             alt="{{ $vid->title }}" loading="lazy"
                             onerror="if(!this.dataset.fb){this.dataset.fb=1;this.src='https://img.youtube.com/vi/{{ $vid->video_key }}/mqdefault.jpg';}" />
                        <div class="video-overlay"></div>
                        <div class="video-info">
                            <h4 class="video-title">{{ $vid->title }}</h4>
                        </div>
                        <div class="play-icon"><i data-lucide="play"
                                style="width: 24px; height: 24px; color: white; fill: white; margin-left: 4px;"></i>
                        </div>
                    </a>
                    @empty
                    <p style="color: rgba(255,255,255,0.5); padding: 2rem;">Aucune vidéo disponible.</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- 11. ENCHÈRES LIVE -->
        <section class="live-section">
            <div class="container">
                <div style="margin-bottom: 4rem;">
                    <h2 class="section-title">{!! __('messages.home.live_heading') !!}</h2>
                </div>
                @if ($autoEncheres->isNotEmpty())
                <div class="auction-grid">
                    {{-- Main card: first enchere --}}
                    @php $mainEnchere = $autoEncheres->first(); @endphp
                    @php
                        $liveClient = Auth::guard('client')->user();
                        $canSeeEnchere = $liveClient && ($liveClient->type ?? '') === 'flotte&enchere';
                        $mainUrl = $canSeeEnchere ? route('nadoautoenchere.show', $mainEnchere->id) : route('nadoautoenchere.register');
                        $mainBrand  = $mainEnchere->brand?->name ?? $mainEnchere->mark;
                        $mainModel  = $mainEnchere->modeleRelation?->name ?? $mainEnchere->modele;
                    @endphp
                    <a href="{{ $mainUrl }}"
                       class="auction-main group main-auction-card" style="text-decoration: none;">
                        <div class="main-auction-img">
                            <img src="{{ asset('storage/nadoautoenchere/' . ($mainEnchere->images[0] ?? 'default.jpg')) }}"
                                 alt="{{ $mainEnchere->titre }}" />
                        </div>
                        <div class="main-auction-content">
                            <div>
                                <h3 style="font-size: 1.875rem; font-weight: 900; color: var(--slate-900); text-transform: uppercase; margin-bottom: 0.5rem;">
                                    {{ $mainBrand }} {{ $mainModel }}</h3>
                                <p style="color: var(--slate-500); font-weight: 700; text-transform: uppercase; font-size: 0.75rem; letter-spacing: 0.1em;">
                                    {{ $mainEnchere->year }}</p>
                            </div>
                            <div class="current-bid">
                                <span class="bid-label">Prix de départ</span>
                                <span class="bid-amount">{{ number_format($mainEnchere->prix, 0, ',', ' ') }} MAD</span>
                                <span class="btn-bid">Placer une enchère
                                    <i data-lucide="gavel" style="width: 16px; height: 16px; margin-left: 0.5rem;"></i>
                                </span>
                            </div>
                        </div>
                    </a>
                    {{-- Sidebar: next 2 encheres --}}
                    <div class="auction-sidebar flex flex-col gap-6">
                        @foreach ($autoEncheres->skip(1)->take(2) as $enc)
                        @php
                            $encUrl = $canSeeEnchere ? route('nadoautoenchere.show', $enc->id) : route('nadoautoenchere.register');
                            $encBrand = $enc->brand?->name ?? $enc->mark;
                            $encModel = $enc->modeleRelation?->name ?? $enc->modele;
                        @endphp
                        <a href="{{ $encUrl }}" class="side-auction-card" style="text-decoration: none;">
                            <div class="side-img">
                                <img src="{{ asset('storage/nadoautoenchere/' . ($enc->images[0] ?? 'default.jpg')) }}"
                                     alt="{{ $enc->titre }}" />
                            </div>
                            <div style="flex: 1;">
                                <h4 style="font-weight: 900; color: var(--slate-900); text-transform: uppercase; font-size: 1.125rem; margin-bottom: 0.25rem;">
                                    {{ $encBrand }} {{ $encModel }}</h4>
                                <div class="flex flex-col">
                                    <span style="font-size: 9px; font-weight: 700; color: var(--slate-400); text-transform: uppercase;">Prix de départ</span>
                                    <span style="font-size: 1.125rem; font-weight: 900; color: var(--primary);">{{ number_format($enc->prix, 0, ',', ' ') }} MAD</span>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                @else
                <p style="color: var(--slate-400); text-align: center; padding: 3rem 0; font-weight: 600;">Aucune enchère disponible pour le moment.</p>
                @endif
            </div>
        </section>

        <!-- 12. BANNER WHATSAPP -->
        <section class="whatsapp-section">
            <div class="container">
                <div class="whatsapp-card">
                    <div class="whatsapp-content">
                        <h2 class="whatsapp-title">{!! __('messages.home.expert_heading') !!}</h2>
                        <button type="button" class="btn-whatsapp" onclick="openExpertModal()" style="border:none; cursor:pointer;">
                            <i data-lucide="mail" style="width: 24px; height: 24px; stroke-width: 3;"></i>
                            {{ __('messages.expert.form_btn') }}
                        </button>
                    </div>
                    <div class="whatsapp-image">
                        <img src="https://images.unsplash.com/photo-1544636331-e26879cd4d9b?auto=format&fit=crop&q=80&w=1200"
                            alt="Expert NADO" />
                    </div>
                </div>
            </div>
        </section>

        <!-- Expert NADO — popup contact form -->
        <div id="expertModal" class="expert-modal" aria-hidden="true">
            <div class="expert-modal__backdrop" onclick="closeExpertModal()"></div>
            <div class="expert-modal__dialog" role="dialog" aria-modal="true" aria-labelledby="expertModalTitle">
                <button type="button" class="expert-modal__close" onclick="closeExpertModal()" aria-label="Fermer">&times;</button>
                <h3 id="expertModalTitle" class="expert-modal__title">{!! __('messages.expert.title') !!}</h3>
                <p class="expert-modal__subtitle">{{ __('messages.expert.subtitle') }}</p>

                <form id="expertForm" class="expert-modal__form" method="post" action="{{ route('contactez-nous.store') }}">
                    @csrf
                    <input type="text" name="name" required placeholder="{{ __('messages.expert.name') }}" class="expert-input">
                    <div class="expert-modal__grid">
                        <input type="tel" name="phone" required placeholder="{{ __('messages.expert.phone') }}" class="expert-input">
                        <input type="email" name="email" required placeholder="{{ __('messages.expert.email') }}" class="expert-input">
                    </div>
                    <select name="ville" required class="expert-input">
                        <option value="" disabled selected>{{ __('messages.expert.ville') }}</option>
                        @foreach($villes as $ville)
                            <option value="{{ $ville->name }}">{{ $ville->name }}</option>
                        @endforeach
                    </select>
                    <textarea name="message" rows="4" placeholder="{{ __('messages.expert.message') }}" class="expert-input"></textarea>
                    <div id="expertFormFeedback" class="expert-modal__feedback" role="status"></div>
                    <button type="submit" id="expertSubmitBtn" class="expert-modal__submit">{{ __('messages.expert.submit') }}</button>
                </form>
            </div>
        </div>

        <style>
            .expert-modal {
                position: fixed;
                inset: 0;
                z-index: 1000;
                display: none;
                align-items: center;
                justify-content: center;
                padding: 1rem;
            }
            .expert-modal.open { display: flex; }
            .expert-modal__backdrop {
                position: absolute;
                inset: 0;
                background: rgba(2, 6, 23, 0.7);
                backdrop-filter: blur(6px);
            }
            .expert-modal__dialog {
                position: relative;
                width: 100%;
                max-width: 540px;
                max-height: 92vh;
                overflow-y: auto;
                background: #0b1220;
                border: 1px solid rgba(255, 255, 255, 0.08);
                border-radius: 1.5rem;
                padding: 2.5rem;
                box-shadow: 0 30px 60px -15px rgba(0, 0, 0, 0.6);
                animation: expertPop 0.25s ease;
            }
            @keyframes expertPop {
                from { transform: translateY(16px) scale(0.98); opacity: 0; }
                to   { transform: translateY(0) scale(1); opacity: 1; }
            }
            .expert-modal__close {
                position: absolute;
                top: 1rem;
                right: 1.25rem;
                background: none;
                border: none;
                color: rgba(255, 255, 255, 0.6);
                font-size: 2rem;
                line-height: 1;
                cursor: pointer;
                transition: color 0.2s;
            }
            .expert-modal__close:hover { color: #fff; }
            .expert-modal__title {
                font-size: 1.75rem;
                font-weight: 900;
                color: #fff;
                text-transform: uppercase;
                letter-spacing: -0.03em;
                margin: 0 0 0.5rem;
            }
            .expert-modal__subtitle {
                color: rgba(255, 255, 255, 0.6);
                font-size: 0.95rem;
                margin: 0 0 1.75rem;
            }
            .expert-modal__form {
                display: flex;
                flex-direction: column;
                gap: 1rem;
            }
            .expert-modal__grid {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 1rem;
            }
            @media (max-width: 520px) {
                .expert-modal__grid { grid-template-columns: 1fr; }
                .expert-modal__dialog { padding: 2rem 1.5rem; }
            }
            .expert-input {
                width: 100%;
                padding: 0.9rem 1rem;
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.12);
                border-radius: 0.75rem;
                color: #fff;
                font-size: 0.95rem;
                font-family: inherit;
                outline: none;
                transition: border-color 0.2s, background 0.2s;
            }
            .expert-input::placeholder { color: rgba(255, 255, 255, 0.4); }
            .expert-input:focus {
                border-color: var(--primary, #0b84e3);
                background: rgba(255, 255, 255, 0.08);
            }
            select.expert-input { appearance: none; cursor: pointer; }
            select.expert-input option { color: #0b1220; }
            textarea.expert-input { resize: vertical; min-height: 90px; }
            .expert-modal__feedback {
                font-size: 0.9rem;
                font-weight: 600;
                min-height: 1.2em;
            }
            .expert-modal__feedback.is-success { color: #22c55e; }
            .expert-modal__feedback.is-error { color: #f87171; }
            .expert-modal__submit {
                width: 100%;
                padding: 1rem;
                border: none;
                border-radius: 0.75rem;
                background: var(--primary, #0b84e3);
                color: #fff;
                font-weight: 900;
                font-size: 0.95rem;
                text-transform: uppercase;
                letter-spacing: 0.03em;
                cursor: pointer;
                transition: transform 0.15s, opacity 0.2s;
            }
            .expert-modal__submit:hover { transform: translateY(-2px); }
            .expert-modal__submit:disabled { opacity: 0.6; cursor: not-allowed; transform: none; }
        </style>

        <script>
            function openExpertModal() {
                var m = document.getElementById('expertModal');
                m.classList.add('open');
                m.setAttribute('aria-hidden', 'false');
                document.body.style.overflow = 'hidden';
            }
            function closeExpertModal() {
                var m = document.getElementById('expertModal');
                m.classList.remove('open');
                m.setAttribute('aria-hidden', 'true');
                document.body.style.overflow = '';
            }
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') closeExpertModal();
            });

            (function () {
                var form = document.getElementById('expertForm');
                if (!form) return;
                form.addEventListener('submit', function (e) {
                    e.preventDefault();
                    var btn = document.getElementById('expertSubmitBtn');
                    var fb = document.getElementById('expertFormFeedback');
                    var oldLabel = btn.textContent;
                    btn.disabled = true;
                    btn.textContent = 'Envoi…';
                    fb.textContent = '';
                    fb.className = 'expert-modal__feedback';

                    fetch(form.action, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': form.querySelector('input[name=_token]').value
                        },
                        body: new FormData(form)
                    })
                    .then(function (r) { return r.json().then(function (j) { return { ok: r.ok, body: j }; }); })
                    .then(function (res) {
                        if (res.ok && res.body && res.body.success) {
                            fb.textContent = res.body.message || 'Demande envoyée.';
                            fb.classList.add('is-success');
                            form.reset();
                            setTimeout(closeExpertModal, 2500);
                        } else {
                            var msg = 'Veuillez vérifier les champs du formulaire.';
                            if (res.body && res.body.errors) {
                                msg = Object.keys(res.body.errors)
                                    .map(function (k) { return res.body.errors[k][0]; })
                                    .join(' ');
                            }
                            fb.textContent = msg;
                            fb.classList.add('is-error');
                        }
                    })
                    .catch(function () {
                        fb.textContent = 'Une erreur est survenue. Merci de réessayer.';
                        fb.classList.add('is-error');
                    })
                    .finally(function () {
                        btn.disabled = false;
                        btn.textContent = oldLabel;
                    });
                });
            })();
        </script>

        <!-- 13. TÉMOIGNAGES -->
        <section class="testimonials">
            <div class="container">
                <div class="testimonial-grid">
                    <div class="testimonial-left text-left">
                        <h2 class="section-title">{!! __('messages.home.clients_heading') !!}</h2>
                        <p
                            style="color: var(--slate-500); font-weight: 500; font-size: 1.125rem; max-width: 24rem;">
                            {{ __('messages.home.clients_desc') }}</p>
                    </div>
                    <div class="testimonial-right">
                        <div class="testimonial-carousel-row">
                            <button class="btn-nav-prev"><i data-lucide="chevron-left" style="width: 20px; height: 20px;"></i></button>
                            <div class="owl-carousel owl-theme" style="min-width:0;flex:1;">
                                @foreach ($avistext as $atext)
                                    <div class="item">
                                        <div class="testimonial-card">
                                            <div class="quote-icon"><i data-lucide="quote"
                                                    style="width: 100%; height: 100%; fill: currentColor;"></i></div>
                                            <div style="position: relative; z-index: 10;">
                                                <div class="stars">
                                                    <i data-lucide="star" style="width: 20px; height: 20px; fill: currentColor;"></i>
                                                    <i data-lucide="star" style="width: 20px; height: 20px; fill: currentColor;"></i>
                                                    <i data-lucide="star" style="width: 20px; height: 20px; fill: currentColor;"></i>
                                                    <i data-lucide="star" style="width: 20px; height: 20px; fill: currentColor;"></i>
                                                    <i data-lucide="star" style="width: 20px; height: 20px; fill: currentColor;"></i>
                                                </div>
                                                <blockquote class="quote-text">"{{ $atext->text }}"</blockquote>
                                                <div class="client-info">
                                                    <div class="client-img"><img
                                                            src="{{ asset('storage/avistext/' . $atext->image) }}"
                                                            alt="{{ $atext->name }}" /></div>
                                                    <div class="text-left">
                                                        <h4
                                                            style="font-weight: 900; color: var(--slate-900); font-size: 1.125rem; text-transform: uppercase; margin-bottom: 0.25rem;">
                                                            {{ $atext->name }}</h4>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <button class="btn-nav-next"><i data-lucide="chevron-right" style="width: 20px; height: 20px; stroke-width: 3;"></i></button>
                        </div>
                    </div>
                </div>
            </div>
        </section>


        <!-- 14. NADO BLOG -->
        <section class="blog-section">
            <div class="container">
                <div class="blog-header">
                    <div class="blog-title-wrapper">
                        <h2 class="blog-big-title">{!! __('messages.home.blog_heading') !!}</h2>
                    </div>
                    <a href="{{ route('articles.index') }}" class="btn-explore-blog" style="text-decoration:none; display:inline-flex; align-items:center; gap:8px;">
                        {{ __('messages.home.explore_blog') }} <i data-lucide="arrow-right" style="width: 18px; height: 18px;"></i>
                    </a>
                </div>

                @php
                    $featuredArticle = $articles->first();
                    $sideArticles    = $articles->slice(1)->take(2)->values();
                @endphp

                <div class="blog-grid-hero">
                    {{-- Featured Article --}}
                    @if($featuredArticle)
                        <a href="{{ route('articles.show', $featuredArticle->slug) }}" class="blog-hero" style="text-decoration:none; color:inherit; display:block;">
                            <div class="blog-featured">
                                <img src="{{ $featuredArticle->image ? asset('storage/' . ltrim($featuredArticle->image, '/')) : 'https://images.unsplash.com/photo-1550355291-bbee04a92027?auto=format&fit=crop&q=80&w=1200' }}"
                                     alt="{{ $featuredArticle->title }}" />
                                <div class="blog-featured-overlay"></div>
                                <div class="blog-featured-content">
                                    <div class="blog-meta-tag">
                                        <i data-lucide="calendar" style="width: 12px; height: 12px; color: var(--primary);"></i>
                                        {{ strtoupper($featuredArticle->created_at->translatedFormat('d M Y')) }}
                                    </div>
                                    <h3 class="blog-featured-title">{{ $featuredArticle->title }}</h3>
                                </div>
                            </div>
                        </a>
                    @endif

                    {{-- Sidebar Articles --}}
                    <div class="blog-sidebar" style="display: flex; flex-direction: column; gap: 2.5rem;">
                        @foreach($sideArticles as $sideArticle)
                            <a href="{{ route('articles.show', $sideArticle->slug) }}" class="blog-sidebar-card" style="text-decoration:none; color:inherit; display:block;">
                                <div class="blog-featured">
                                    <img src="{{ $sideArticle->image ? asset('storage/' . ltrim($sideArticle->image, '/')) : 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=800' }}"
                                         alt="{{ $sideArticle->title }}" />
                                    <div class="blog-featured-overlay"></div>
                                    <div class="blog-featured-content">
                                        <div class="blog-meta-tag">
                                            <i data-lucide="calendar" style="width: 12px; height: 12px; color: var(--primary);"></i>
                                            {{ strtoupper($sideArticle->created_at->translatedFormat('d M Y')) }}
                                        </div>
                                        <h3 class="blog-featured-title">{{ $sideArticle->title }}</h3>
                                    </div>
                                </div>
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </section>

    </div>

    <!-- FOOTER -->
    <footer>
        <div class="container">
            <div class="footer-grid">
                <div>
                    <div class="logo"
                        style="font-size: 1.875rem; font-weight: 900; margin-bottom: 1.5rem; text-transform: uppercase;">
                        <span class="text-primary">NA</span>
                        <span class="text-secondary">DO</span>
                    </div>
                    <p style="color: var(--slate-400); font-weight: 500; line-height: 1.625;">
                        Le premier portail automobile premium au Maroc. Expertise, confiance et innovation.
                    </p>
                </div>
                <div>
                    <h4 class="footer-heading">Navigation</h4>
                    <ul class="footer-links">
                        <li><a href="#">Accueil</a></li>
                        <li><a href="#">Acheter une voiture</a></li>
                        <li><a href="#">Vendre ma voiture</a></li>
                        <li><a href="#">À propos de NADO</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Services</h4>
                    <ul class="footer-links">
                        <li><a href="#" class="text-secondary">NADO Connect</a></li>
                        <li><a href="#">Auto Enchère</a></li>
                        <li><a href="#">Vente cash</a></li>
                    </ul>
                </div>
                <div>
                    <h4 class="footer-heading">Réseaux Sociaux</h4>
                    <p style="font-size: 0.75rem; color: var(--slate-400); margin-bottom: 1.5rem;">Suivez nos
                        livraisons en direct sur les réseaux.</p>
                    <div class="flex gap-4">
                        <button class="social-btn"><i data-lucide="instagram"
                                style="width: 16px; height: 16px; color: var(--white);"></i></button>
                        <button class="social-btn"><i data-lucide="facebook"
                                style="width: 16px; height: 16px; color: var(--white);"></i></button>
                        <button class="social-btn"><i data-lucide="linkedin"
                                style="width: 16px; height: 16px; color: var(--white);"></i></button>
                    </div>
                </div>
            </div>
            <div
                style="padding-top: 2rem; border-top: 1px solid var(--slate-600); text-align: center; font-size: 10px; font-weight: 700; color: var(--slate-500); text-transform: uppercase; letter-spacing: 0.1em;">
                © 2025 NADO PREMIUM AUTOMOTIVE • TOUS DROITS RÉSERVÉS
            </div>
        </div>
    </footer>

    <!-- MODAL HISTOIRE -->
    <!-- Story Modal -->
    <div class="modal-overlay" id="story-modal" style="display: none;">
        <div class="close-modal"><i data-lucide="x" style="width: 24px; height: 24px;"></i></div>
        <div class="story-viewer">
            <div class="story-progress">
                <div class="progress-segment">
                    <div class="progress-bar" style="width: 100%;"></div>
                </div>
            </div>

            <!-- Dynamic Content Container -->
            <img id="story-image-viewer" src=""
                style="width: 100%; height: 100%; object-fit: cover; display: none;" />
            <video id="story-video-viewer" src=""
                style="width: 100%; height: 100%; object-fit: cover; display: none;" playsinline loop></video>

            <div
                style="position: absolute; bottom: 0; left: 0; right: 0; padding: 2rem; background: linear-gradient(to top, rgba(0,0,0,0.8), transparent);">
                <div style="display: flex; align-items: center; gap: 1rem;">
                    <img src="{{ asset('images/logoValidator.png') }}"
                        style="width: 3rem; height: 3rem; border-radius: 50%; border: 2px solid var(--primary);"
                        onerror="this.src='https://ui-avatars.com/api/?name=NADO&background=1459DD&color=fff'" />
                    <div>
                        <h4 style="color: var(--white); font-weight: 900; text-transform: uppercase;">NADO Officiel
                        </h4>
                        <span style="color: var(--slate-400); font-size: 0.75rem; font-weight: 700;">En direct</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        lucide.createIcons();
    </script>

    <script>
        (function () {
            var track = document.querySelector('.ticker-track');
            if (!track) return;

            // Kill the CSS animation — JS drives the position instead
            track.style.animation = 'none';

            var PX_PER_SEC = 120; // scroll speed in pixels/second
            var currentX   = 0;
            var isDragging = false;
            var dragStartX = 0;
            var dragStartCurrentX = 0;
            var lastTs     = null;

            // Logos are links now. mousedown's preventDefault() stops selection/native
            // image-drag but a click still fires on mouseup, so a drag that ends over a
            // logo would navigate. Track how far the pointer moved and swallow the click.
            var DRAG_THRESHOLD = 5; // px
            var dragMoved      = 0;

            function loop(ts) {
                if (!isDragging) {
                    if (lastTs !== null) {
                        var dt      = (ts - lastTs) / 1000;
                        var halfW   = track.scrollWidth / 2;
                        currentX   -= PX_PER_SEC * dt;
                        // wrap: keep currentX in (-halfW, 0]
                        if (currentX < -halfW) currentX += halfW;
                    }
                    lastTs = ts;
                    track.style.transform = 'translateX(' + currentX + 'px)';
                } else {
                    lastTs = null; // reset so no jump after drag ends
                }
                requestAnimationFrame(loop);
            }
            requestAnimationFrame(loop);

            // ── Mouse ──
            track.addEventListener('mousedown', function (e) {
                isDragging        = true;
                dragStartX        = e.clientX;
                dragStartCurrentX = currentX;
                dragMoved         = 0;
                track.style.cursor = 'grabbing';
                e.preventDefault();
            });

            document.addEventListener('mousemove', function (e) {
                if (!isDragging) return;
                var delta  = e.clientX - dragStartX;
                dragMoved  = Math.max(dragMoved, Math.abs(delta));
                var halfW  = track.scrollWidth / 2;
                currentX   = dragStartCurrentX + delta;
                // normalize so the duplicate loop is seamless
                currentX   = ((currentX % halfW) + halfW) % halfW - halfW;
                if (currentX > 0) currentX = 0;
                track.style.transform = 'translateX(' + currentX + 'px)';
            });

            document.addEventListener('mouseup', function () {
                if (!isDragging) return;
                isDragging = false;
                track.style.cursor = 'grab';
            });

            // ── Touch ──
            track.addEventListener('touchstart', function (e) {
                isDragging        = true;
                dragStartX        = e.touches[0].clientX;
                dragStartCurrentX = currentX;
                dragMoved         = 0;
            }, { passive: true });

            document.addEventListener('touchmove', function (e) {
                if (!isDragging) return;
                var delta  = e.touches[0].clientX - dragStartX;
                dragMoved  = Math.max(dragMoved, Math.abs(delta));
                var halfW  = track.scrollWidth / 2;
                currentX   = dragStartCurrentX + delta;
                currentX   = ((currentX % halfW) + halfW) % halfW - halfW;
                if (currentX > 0) currentX = 0;
                track.style.transform = 'translateX(' + currentX + 'px)';
            }, { passive: true });

            document.addEventListener('touchend', function () {
                if (!isDragging) return;
                isDragging = false;
            });

            // Swallow the click that follows a drag. Capture phase so it runs before the
            // anchor's default navigation; a genuine click leaves dragMoved at ~0.
            track.addEventListener('click', function (e) {
                if (dragMoved > DRAG_THRESHOLD) {
                    e.preventDefault();
                    e.stopPropagation();
                }
            }, true);
        })();
    </script>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Owl Carousel JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    <script>
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Initialize Owl Carousel for Testimonials
        $(document).ready(function() {
            console.log('Document ready - initializing scripts');

            // --- PRIORITY: Story Modal Logic (Vanilla JS) ---
            // (Removed redundant listener - using global openStory instead)
            // ----------------------------------

            // Testimonials Slider
            var testimonialOwl = $('.testimonial-right .owl-carousel');
            if (testimonialOwl.length > 0) {
                testimonialOwl.owlCarousel({
                    items: 1,
                    loop: true,
                    margin: 16,
                    nav: false,
                    dots: true,
                    touchDrag: true,
                    mouseDrag: true,
                    autoplay: false,
                    smartSpeed: 500,
                    responsive: {
                        0: { items: 1, dots: true },
                        768: { items: 1, dots: false }
                    }
                });
            }

            // Hero Slider
            var heroSlider = $('.hero-slider');
            console.log('Hero slider count:', heroSlider.length);

            if (heroSlider.length > 0) {
                try {
                    heroSlider.owlCarousel({
                        items: 1,
                        loop: true,
                        margin: 0,
                        nav: false,
                        dots: false,
                        autoplay: true,
                        autoplayTimeout: 5000,
                        animateOut: 'fadeOut',
                        onInitialized: function() {
                            console.log('Hero Slider Initialized');
                        }
                    });
                } catch (err) {
                    console.error('Hero Slider Init Error:', err);
                }
            }

            // Custom Navigation Events (Testimonials)
            $('.btn-nav-next').click(function() {
                testimonialOwl.trigger('next.owl.carousel');
            });
            $('.btn-nav-prev').click(function() {
                testimonialOwl.trigger('prev.owl.carousel');
            });

        });

        // --- NADO STORY FUNCTIONS (RENAMED) ---
        function launchNadoStory(element) {
            console.log('launchNadoStory called');
            var src = element.getAttribute('data-src');
            var type = element.getAttribute('data-type');
            var insta = element.dataset.insta;
            var instaEmbed = element.dataset.instaEmbed;

            var popup = document.getElementById('nado-story-popup');
            var img = document.getElementById('nado-story-img');
            var vid = document.getElementById('nado-story-video');
            var instaBtn = document.getElementById('nado-story-insta');
            var instaFrame = document.getElementById('nado-story-insta-frame');

            // Instagram post plays inline in the iframe; the button is the fallback for
            // links we could not parse a post code out of.
            if (instaFrame) {
                if (instaEmbed) {
                    instaFrame.src = instaEmbed;
                    instaFrame.style.display = 'block';
                } else {
                    instaFrame.src = '';
                    instaFrame.style.display = 'none';
                }
            }
            if (instaBtn) {
                // Only a fallback: when the embed is showing it already links out, so a
                // second button would be redundant.
                if (insta && !instaEmbed) {
                    instaBtn.href = insta;
                    instaBtn.style.display = 'inline-flex';
                } else {
                    instaBtn.style.display = 'none';
                    instaBtn.href = '#';
                }
            }

            if (popup && img && vid) {
                console.log('Popup elements found. Opening...');

                // Reset
                img.style.display = 'none';
                vid.style.display = 'none';
                vid.src = '';

                if (!src || (instaEmbed && type !== 'video')) {
                    // Nothing to play locally, or all we have is an Instagram thumbnail:
                    // the embed is the content. A downloaded MP4 (type 'video') wins.
                } else if (type === 'image') {
                    img.src = src;
                    img.style.display = 'block';
                } else {
                    vid.src = src;
                    vid.style.display = 'block';
                    var playPromise = vid.play();
                    if (playPromise !== undefined) {
                        playPromise.catch(error => {
                            console.log("Video autoplay failed: ", error);
                        });
                    }
                }

                // Force Show
                popup.style.display = 'flex';
            } else {
                console.error('Popup elements NOT found!');
            }
        }

        function dismissNadoStory() {
            var popup = document.getElementById('nado-story-popup');
            var vid = document.getElementById('nado-story-video');
            var instaBtn = document.getElementById('nado-story-insta');
            var instaFrame = document.getElementById('nado-story-insta-frame');

            if (popup) {
                popup.style.display = 'none';
            }
            if (vid) {
                vid.pause();
                vid.src = '';
            }
            if (instaFrame) {
                // Clearing src unloads the embed, which stops playback and audio.
                instaFrame.src = '';
                instaFrame.style.display = 'none';
            }
            if (instaBtn) {
                instaBtn.style.display = 'none';
                instaBtn.href = '#';
            }
        }

        // Global click listener to close if clicked outside
        document.addEventListener('click', function(e) {
            var popup = document.getElementById('nado-story-popup');
            if (e.target === popup) {
                dismissNadoStory();
            }
        });

        // Trouver button → redirect to achat with filters
        document.getElementById('btn-search-find').addEventListener('click', function () {
            var brandSelect = document.getElementById('search-brand');
            var marque = brandSelect.options[brandSelect.selectedIndex].dataset.name || '';
            var modele = document.getElementById('search-model').value || '';
            var prix   = document.getElementById('search-budget').value || '';
            var base   = '{{ route('achat.index') }}';
            var params = new URLSearchParams({
                marque:    marque,
                modele:    modele,
                annee_min: '',
                annee_max: '',
                carburant: '',
                box:       '',
                prix:      prix,
                km:        '',
                ville:     ''
            });
            window.location.href = base + '?' + params.toString();
        });

        // Brand → Model dynamic loading
        document.getElementById('search-brand').addEventListener('change', function () {
            var brandId = this.value;
            var modelSelect = document.getElementById('search-model');
            modelSelect.innerHTML = '<option value="">Tous modèles</option>';
            if (!brandId) return;
            fetch('{{ route('achat.models.by.brand', '') }}/' + brandId)
                .then(function(res){ return res.json(); })
                .then(function(models){
                    models.forEach(function(m){
                        var opt = document.createElement('option');
                        opt.value = m.name;
                        opt.textContent = m.name;
                        modelSelect.appendChild(opt);
                    });
                });
        });
    </script>

<!-- ═══════════════════════════════════════════════
     NADO CHATBOT WIDGET
════════════════════════════════════════════════ -->
<style>
/* ── Floating button ── */
#nado-chat-btn {
    position: fixed;
    bottom: 28px;
    right: 28px;
    width: 62px;
    height: 62px;
    border-radius: 50%;
    background: #1459DD;
    color: #fff;
    border: none;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 24px rgba(20,89,221,.45);
    z-index: 9998;
    transition: transform .2s, box-shadow .2s;
}
#nado-chat-btn:hover { transform: scale(1.08); box-shadow: 0 12px 32px rgba(20,89,221,.55); }
#nado-chat-btn svg { width: 28px; height: 28px; }
#nado-chat-badge {
    position: absolute;
    top: -2px; right: -2px;
    width: 18px; height: 18px;
    background: #FDD817;
    border-radius: 50%;
    border: 2px solid #fff;
    font-size: 10px;
    font-weight: 900;
    color: #1459DD;
    display: flex; align-items: center; justify-content: center;
}

/* ── Panel ── */
#nado-chat-panel {
    position: fixed;
    bottom: 104px;
    right: 28px;
    width: 360px;
    max-height: 520px;
    background: #fff;
    border-radius: 24px;
    box-shadow: 0 24px 64px rgba(15,23,42,.18);
    display: flex;
    flex-direction: column;
    overflow: hidden;
    z-index: 9999;
    transform: scale(.95) translateY(10px);
    opacity: 0;
    pointer-events: none;
    transition: transform .25s cubic-bezier(.34,1.56,.64,1), opacity .2s;
    font-family: 'Inter', sans-serif;
}
#nado-chat-panel.open {
    transform: scale(1) translateY(0);
    opacity: 1;
    pointer-events: all;
}

/* ── Header ── */
.nc-header {
    background: #1459DD;
    color: #fff;
    padding: 18px 20px;
    display: flex;
    align-items: center;
    gap: 12px;
    flex-shrink: 0;
}
.nc-avatar {
    width: 42px; height: 42px;
    border-radius: 50%;
    background: rgba(255,255,255,.15);
    display: flex; align-items: center; justify-content: center;
    font-size: 20px;
    flex-shrink: 0;
}
.nc-header-info { flex: 1; }
.nc-header-name { font-weight: 800; font-size: 14px; line-height: 1.2; }
.nc-header-status { font-size: 11px; opacity: .75; margin-top: 2px; }
.nc-close {
    background: rgba(255,255,255,.15);
    border: none; color: #fff;
    width: 32px; height: 32px;
    border-radius: 50%;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: background .2s;
    font-size: 18px;
    flex-shrink: 0;
}
.nc-close:hover { background: rgba(255,255,255,.3); }

/* ── Messages area ── */
#nc-messages {
    flex: 1;
    overflow-y: auto;
    padding: 16px;
    display: flex;
    flex-direction: column;
    gap: 10px;
    scroll-behavior: smooth;
}
.nc-msg { display: flex; }
.nc-msg.bot { justify-content: flex-start; }
.nc-msg.user { justify-content: flex-end; }
.nc-bubble {
    max-width: 82%;
    padding: 10px 14px;
    border-radius: 18px;
    font-size: 13.5px;
    line-height: 1.5;
}
.nc-msg.bot .nc-bubble {
    background: #f1f5f9;
    color: #0f172a;
    border-bottom-left-radius: 4px;
}
.nc-msg.user .nc-bubble {
    background: #1459DD;
    color: #fff;
    border-bottom-right-radius: 4px;
}
.nc-bubble a { color: #1459DD; font-weight: 700; text-decoration: underline; }
.nc-msg.user .nc-bubble a { color: #FDD817; }
.nc-bubble strong { font-weight: 800; }

/* ── Quick replies ── */
#nc-chips {
    padding: 0 16px 10px;
    display: flex;
    flex-wrap: nowrap;
    overflow-x: auto;
    -webkit-overflow-scrolling: touch;
    scrollbar-width: none;
    gap: 6px;
    flex-shrink: 0;
}
#nc-chips::-webkit-scrollbar { display: none; }
.nc-chip {
    padding: 7px 13px;
    background: #f1f5f9;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    font-size: 12px;
    font-weight: 700;
    color: #334155;
    cursor: pointer;
    white-space: nowrap;
    transition: background .15s, border-color .15s;
    font-family: 'Inter', sans-serif;
}
.nc-chip:hover { background: #dbeafe; border-color: #1459DD; color: #1459DD; }

/* Chips shown inside a bot bubble (profile picker for the documents answer) */
.nc-inline-chips {
    display: flex;
    flex-wrap: wrap;
    gap: 6px;
    margin-top: 10px;
}
.nc-inline-chip {
    flex: 0 0 auto;
    cursor: pointer;
    font-family: inherit;
}

/* ── Input bar ── */
.nc-input-bar {
    padding: 12px 16px;
    border-top: 1px solid #f1f5f9;
    display: flex;
    gap: 8px;
    flex-shrink: 0;
}
#nc-input {
    flex: 1;
    padding: 10px 14px;
    border: 1px solid #e2e8f0;
    border-radius: 999px;
    font-size: 13px;
    font-family: 'Inter', sans-serif;
    outline: none;
    transition: border-color .2s;
    color: #0f172a;
}
#nc-input:focus { border-color: #1459DD; }
#nc-send {
    width: 40px; height: 40px;
    border-radius: 50%;
    background: #1459DD;
    border: none;
    color: #fff;
    cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    transition: background .2s;
}
#nc-send:hover { background: #0e47b8; }
#nc-send svg { width: 18px; height: 18px; }

/* ── Mobile ── */
@media (max-width: 480px) {
    #nado-chat-panel { width: calc(100vw - 24px); right: 12px; bottom: 90px; }
    #nado-chat-btn { bottom: 16px; right: 16px; }
}
</style>

<!-- Floating Button -->
<button id="nado-chat-btn" aria-label="Chat NADO" title="Discuter avec nous">
    <span id="nado-chat-badge">1</span>
    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
    </svg>
</button>

<!-- Chat Panel -->
<div id="nado-chat-panel" role="dialog" aria-label="MOHSINE CHAWKI">
    <div class="nc-header">
        <div class="nc-avatar">🤖</div>
        <div class="nc-header-info">
            <div class="nc-header-name">MOHSINE CHAWKI</div>
            <div class="nc-header-status">● En ligne — répond instantanément</div>
        </div>
        <button class="nc-close" id="nc-close-btn" aria-label="Fermer">✕</button>
    </div>

    <div id="nc-messages"></div>

    <div id="nc-chips">
        <button class="nc-chip" data-q="telephone">📞 Téléphone</button>
        <button class="nc-chip" data-q="adresse">📍 Adresse</button>
        <button class="nc-chip" data-q="horaires">🕐 Horaires</button>
        <button class="nc-chip" data-q="acheter">🚗 Acheter</button>
        <button class="nc-chip" data-q="vendre">💰 Vendre</button>
        <button class="nc-chip" data-q="financement">🏦 Financement</button>
        <button class="nc-chip" data-q="documents">📋 Documents</button>
        <button class="nc-chip" data-q="reprise">🔁 Reprise</button>
        <button class="nc-chip" data-q="estimation">📊 Estimation</button>
        <button class="nc-chip" data-q="garantie">🛡️ Garantie</button>
        <button class="nc-chip" data-q="whatsapp">💬 WhatsApp</button>
    </div>

    <div class="nc-input-bar">
        <input id="nc-input" type="text" placeholder="Posez votre question…" autocomplete="off" />
        <button id="nc-send" aria-label="Envoyer">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                <line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/>
            </svg>
        </button>
    </div>
</div>

<script>
(function () {
    var btn     = document.getElementById('nado-chat-btn');
    var panel   = document.getElementById('nado-chat-panel');
    var closeBtn= document.getElementById('nc-close-btn');
    var msgBox  = document.getElementById('nc-messages');
    var input   = document.getElementById('nc-input');
    var sendBtn = document.getElementById('nc-send');
    var chips   = document.querySelectorAll('.nc-chip');
    var badge   = document.getElementById('nado-chat-badge');
    var opened  = false;

    /* ── Contact facts (single place to update) ── */
    var TEL      = '<a href="tel:+212520070611"><strong>+212 520 070 611</strong></a>';
    var WHATSAPP = '<a href="https://wa.me/212770080009" target="_blank" rel="noopener"><strong>WhatsApp 07 70 08 00 09</strong></a>';
    var EMAIL    = '<a href="mailto:contact@nado.ma"><strong>contact@nado.ma</strong></a>';
    var MAPS     = '<a href="https://maps.app.goo.gl/RuP9P8wv898XNAAJ9" target="_blank" rel="noopener"><strong>Voir sur Google Maps</strong></a>';
    var HOURS    = '<strong>Lundi – Samedi : 9h00 – 20h00</strong> (fermé le dimanche)';

    function docList(items) {
        return '<ul style="margin:6px 0 0;padding-left:18px;">' +
            items.map(function (d) { return '<li>' + d + '</li>'; }).join('') + '</ul>';
    }

    /* ── Knowledge base ──
       keys are matched as whole words/phrases (accents ignored). The entry whose keys
       match best wins, so "vendre ma voiture" goes to Vente, not Achat. */
    var KB = [
        // ── Contact ──
        { keys: ['telephone','tel','phone','numero','appeler','appel','joindre','contacter','contact','fixe'],
          answer: '📞 Téléphone : ' + TEL + '<br>💬 ' + WHATSAPP + '<br>✉️ ' + EMAIL + '<br>🕐 ' + HOURS + '.' },
        { keys: ['whatsapp','whats app','wtsp','watsap','wa'],
          answer: '💬 Écrivez-nous sur ' + WHATSAPP + ' : un conseiller vous répond pendant nos horaires (' + HOURS + ').' },
        { keys: ['email','e-mail','mail','courriel','ecrire'],
          answer: '✉️ Notre adresse email : ' + EMAIL + '<br>Nous répondons sous <strong>24h ouvrées</strong>. Pour une réponse plus rapide : ' + WHATSAPP + '.' },
        { keys: ['adresse','localisation','ou etes','ou se trouve','ou vous trouver','situe','situes','agence','showroom','map','maps','carte','itineraire','venir','casablanca','jnane californie'],
          answer: '📍 Notre agence : <strong>Casablanca – Jnane Californie</strong><br>' + MAPS + '<br>🕐 ' + HOURS + '.' },
        { keys: ['horaire','horaires','heure','heures','ouvert','ouverte','ouverture','ferme','fermeture','dimanche','samedi'],
          answer: '🕐 Nos horaires d\'ouverture :<br>' + HOURS + '.<br>En dehors de ces horaires, laissez-nous un message sur ' + WHATSAPP + '.' },

        // ── Acheter ──
        { keys: ['acheter','achat','achete','chercher voiture','cherche une voiture','voitures disponibles','catalogue','annonces','occasion'],
          answer: '🚗 Parcourez nos <a href="/achat"><strong>véhicules à vendre</strong></a>.<br>Filtrez par <strong>marque, modèle, prix, année, kilométrage, carburant et boîte</strong>. Chaque annonce affiche photos, équipements, rapport d\'expertise et un <strong>simulateur de crédit</strong>.' },
        { keys: ['certifie','certification','expertise','rapport','inspection','controle','fiable','etat du vehicule'],
          answer: '✅ Les véhicules <strong>certifiés NADO</strong> sont contrôlés par nos experts avant la mise en vente.<br>Le <strong>rapport d\'expertise</strong> est consultable dans l\'onglet <strong>« Expertise NADO »</strong> de chaque annonce.' },
        { keys: ['garantie','garanti','panne','serenite'],
          answer: '🛡️ <strong>Garantie NADO Sérénité</strong> : <strong>1 mois</strong> couvrant <strong>pièces et main d\'œuvre</strong>, dans la limite de <strong>1 000 km</strong>.<br>Les annonces concernées affichent le badge « Garantie incluse ».' },
        { keys: ['essai','essayer','visite','visiter','voir la voiture','voir le vehicule','rendez-vous','rdv'],
          answer: '📅 Pour voir ou essayer un véhicule, contactez-nous avec la référence de l\'annonce :<br>' + WHATSAPP + ' ou ' + TEL + '.<br>📍 Agence : <strong>Casablanca – Jnane Californie</strong> (' + MAPS + ').' },
        { keys: ['comparer','comparaison','comparateur','compare'],
          answer: '⚖️ Comparez deux véhicules côte à côte (prix, km, carburant, boîte, équipements…) avec notre <a href="/comparison"><strong>comparateur</strong></a>.' },
        { keys: ['favori','favoris','sauvegarder','enregistrer','coeur'],
          answer: '❤️ Cliquez sur le cœur d\'une annonce pour l\'ajouter à vos <a href="/favorites"><strong>favoris</strong></a> (connexion à votre compte requise).' },
        { keys: ['reserver','reservation','acompte','bloquer','negocier','negociation','proposer un prix','offre','remise','reduction'],
          answer: '🤝 Pour réserver un véhicule ou faire une offre, utilisez <strong>« Proposer un prix »</strong> ou <strong>« Discussion avec le vendeur »</strong> sur l\'annonce, ou écrivez-nous sur ' + WHATSAPP + ' avec la référence du véhicule.' },
        { keys: ['livraison','livrer','autre ville','rabat','marrakech','tanger','agadir','fes'],
          answer: '🚚 Vous êtes hors de Casablanca ? Contactez un conseiller sur ' + WHATSAPP + ' pour étudier les modalités (visite, papiers, remise du véhicule).' },
        { keys: ['papiers','carte grise','mutation','immatriculation','dedouane','vignette','assurance voiture'],
          answer: '📄 Pour les démarches administratives (carte grise, mutation…), un conseiller vous accompagne : ' + WHATSAPP + ' ou ' + TEL + '.' },
        { keys: ['accessoire','accessoires','pieces','equipement auto'],
          answer: '🧰 Découvrez nos <a href="/accessoires"><strong>accessoires</strong></a> disponibles.' },

        // ── Financement ──
        { keys: ['financement','financer','credit','pret','mensualite','mensualites','simulation','simulateur','taux','apport','leasing','loa'],
          answer: '🏦 <strong>Financement auto</strong> : chaque annonce propose un <strong>simulateur</strong> (apport, durée, mensualité). Le taux dépend de votre <strong>profil</strong> (salarié, fonctionnaire, retraité, profession libérale, commerçant, entreprise).<br>👉 <a href="/financement"><strong>Page Financement</strong></a> — offre indicative, soumise à acceptation du dossier.<br>Tapez « documents » pour la liste des pièces à fournir.' },
        { keys: ['document','documents','dossier','pieces a fournir','piece','justificatif','papiers credit'],
          answer: '📋 <strong>Pièces à fournir</strong> — choisissez votre profil :'
                  + '<div class="nc-inline-chips">'
                  + '<button type="button" class="nc-chip nc-inline-chip" data-q="documents salarié">Salarié(e) du privé</button>'
                  + '<button type="button" class="nc-chip nc-inline-chip" data-q="documents fonctionnaire">Fonctionnaire</button>'
                  + '<button type="button" class="nc-chip nc-inline-chip" data-q="documents retraité">Retraité(e)</button>'
                  + '<button type="button" class="nc-chip nc-inline-chip" data-q="documents profession libérale">Profession libérale</button>'
                  + '<button type="button" class="nc-chip nc-inline-chip" data-q="documents commerçant">Commerçant</button>'
                  + '<button type="button" class="nc-chip nc-inline-chip" data-q="documents entreprise">Entreprise</button>'
                  + '</div>' },
        { keys: ['salarie','salariee','prive','bulletin'],
          answer: '📋 <strong>Salarié(e) du privé</strong> :' + docList(["Copie de la CIN", "Spécimen de chèque ou attestation de RIB", "3 derniers bulletins de salaire", "Attestation de salaire", "Attestation de travail", "Attestation CNSS", "Justificatif d'adresse (moins de 3 mois)", "3 derniers relevés bancaires"]) },
        { keys: ['fonctionnaire','etat d engagement'],
          answer: '📋 <strong>Fonctionnaire</strong> :' + docList(["Copie de la CIN", "Spécimen de chèque ou attestation de RIB", "Justificatif d'adresse (moins de 3 mois)", "3 derniers relevés bancaires", "État d'engagement récent ou attestation de salaire"]) },
        { keys: ['retraite','retraitee','pension'],
          answer: '📋 <strong>Retraité(e)</strong> :' + docList(["Copie de la CIN", "Spécimen de chèque ou attestation de RIB", "Dernière attestation de pension", "Justificatif d'adresse (moins de 3 mois)", "3 derniers relevés bancaires"]) },
        { keys: ['profession liberale','liberal','liberale','medecin','avocat','architecte'],
          answer: '📋 <strong>Profession libérale</strong> :' + docList(["Copie de la CIN", "Spécimen de chèque ou attestation de RIB", "Copie de la carte professionnelle", "Justificatif d'adresse (moins de 3 mois)", "3 derniers relevés bancaires"]) },
        { keys: ['commercant','commerce','registre de commerce','modele j'],
          answer: '📋 <strong>Commerçant</strong> :' + docList(["Copie de la CIN", "Spécimen de chèque ou attestation de RIB", "Registre du commerce (Modèle J)", "Justificatif d'adresse (moins de 3 mois)", "3 derniers relevés bancaires"]) },
        { keys: ['entreprise','societe','sarl','gerant','statuts'],
          answer: '📋 <strong>Entreprise</strong> :' + docList(["Copie de la CIN du gérant", "Spécimen de chèque ou attestation de RIB de l'entreprise", "Statuts de l'entreprise", "Justificatif d'adresse du/des gérant(s) (moins de 3 mois)", "3 derniers relevés bancaires de l'entreprise", "Registre du commerce (Modèle J) de moins de 3 mois"]) },

        // ── Vendre / reprise / estimation ──
        { keys: ['vendre','vente','vends','vend','publier','deposer','mettre en vente','deposer une annonce'],
          answer: '💰 <strong>Vendre votre voiture</strong> :<br>• Particulier : <a href="/vente/particulier"><strong>déposer une annonce</strong></a><br>• Professionnel : <a href="/vente/pro"><strong>espace pro</strong></a><br>• Ou remplissez le <a href="/vente/formulaire"><strong>formulaire de vente</strong></a> et nous vous rappelons.<br>Votre annonce est publiée après validation par notre équipe.' },
        { keys: ['reprise','reprendre','rachat','racheter','echange','echanger','troquer'],
          answer: '🔁 <strong>NADO Reprise</strong> : nous reprenons votre véhicule, y compris en échange d\'une voiture de notre stock.<br>👉 Remplissez le <a href="/reprise/formulaire"><strong>formulaire de reprise</strong></a> (marque, modèle, année, km, photos).' },
        { keys: ['estimation','estimer','valeur','cote','combien vaut','combien ca vaut','argus'],
          answer: '📊 <strong>Estimation gratuite</strong> de votre voiture en quelques clics :<br>👉 <a href="/estimate-value"><strong>Estimer mon véhicule</strong></a> (marque, modèle, année, km, carburant, boîte).' },

        // ── Enchères ──
        { keys: ['enchere','encheres','auction','miser','mise','enchérir','encherir'],
          answer: '🔨 <strong>NADO Auto Enchère</strong> :<br>• Voir les <a href="/nadoautoenchere"><strong>ventes aux enchères</strong></a><br>• <a href="/nadoautoenchere/register"><strong>S\'inscrire</strong></a> pour enchérir<br>• <a href="/nadoautoenchere/vendre"><strong>Vendre aux enchères</strong></a>' },

        // ── Compte ──
        { keys: ['compte','inscription','inscrire','connexion','connecter','login','mot de passe','profil'],
          answer: '👤 Gérez vos annonces, favoris et discussions depuis <a href="/profil"><strong>votre profil</strong></a>.<br>Un souci de connexion ? Écrivez-nous sur ' + WHATSAPP + '.' },

        // ── Prix d'une annonce ──
        { keys: ['prix','tarif','combien','cout','cher','pas cher','budget'],
          answer: '💶 Le prix de chaque véhicule est affiché sur son annonce, avec une <strong>mensualité estimée</strong>.<br>Filtrez par budget sur la page <a href="/achat"><strong>Acheter</strong></a>.<br>Vous voulez connaître la valeur de <em>votre</em> voiture ? Tapez « estimation ».' },

        // ── À propos / services ──
        { keys: ['nado','qui etes','a propos','entreprise nado','societe nado','confiance'],
          answer: 'ℹ️ <strong>NADO</strong> : achat, vente, reprise, financement et enchères de véhicules d\'occasion, basé à <strong>Casablanca</strong>.<br>👉 <a href="/a-propos"><strong>En savoir plus</strong></a> · <a href="/services"><strong>Nos services</strong></a>' },

        // ── Politesse ──
        { keys: ['bonjour','salut','hello','bonsoir','salam','bjr','slt','cc','coucou','hey'],
          answer: 'Bonjour ! 👋 Bienvenue chez <strong>NADO</strong>.<br>Je peux vous aider à <strong>acheter</strong>, <strong>vendre</strong>, faire <strong>reprendre</strong> ou <strong>financer</strong> une voiture, et vous donner nos <strong>coordonnées</strong>.' },
        { keys: ['merci','thanks','thank you','shukran','choukran','top','parfait'],
          answer: 'Avec plaisir ! 😊 N\'hésitez pas si vous avez d\'autres questions.' },
        { keys: ['au revoir','bye','bonne journee','bonne soiree','a bientot'],
          answer: 'À bientôt chez <strong>NADO</strong> ! 👋' },
    ];

    var DEFAULT = 'Je n\'ai pas trouvé de réponse précise 🤔<br>Essayez par exemple : <strong>acheter</strong>, <strong>vendre</strong>, <strong>reprise</strong>, <strong>estimation</strong>, <strong>financement</strong>, <strong>documents</strong>, <strong>garantie</strong>, <strong>enchères</strong>, <strong>adresse</strong>, <strong>horaires</strong>.<br>Ou parlez à un conseiller : ' + WHATSAPP + ' · ' + TEL + '.';

    /* ── Helpers ── */
    function addMsg(text, who) {
        var div = document.createElement('div');
        div.className = 'nc-msg ' + who;
        var bub = document.createElement('div');
        bub.className = 'nc-bubble';
        bub.innerHTML = text;
        div.appendChild(bub);
        msgBox.appendChild(div);
        msgBox.scrollTop = msgBox.scrollHeight;
    }

    // Lowercase, drop accents and punctuation: "O\u00f9 \u00eates-vous ?" -> "ou etes vous"
    function normalize(s) {
        return s.toLowerCase().normalize('NFD').replace(/[\u0300-\u036f]/g, '')
            .replace(/[^a-z0-9]+/g, ' ').trim();
    }

    // Keys are normalized once, so accented keys ("cr\u00e9dit") match too
    KB.forEach(function (entry) {
        entry.norm = entry.keys.map(normalize);
    });

    // Every entry is scored by the length of the keys it matches (whole words,
    // plural "s" allowed); the best score wins. A profile word ("salari\u00e9") beats
    // the generic "documents" entry because it is more specific.
    var SPECIFIC = ['salarie', 'fonctionnaire', 'retraite', 'profession liberale', 'commercant', 'entreprise'];
    function getAnswer(q) {
        var text = ' ' + normalize(q) + ' ';
        var best = null, bestScore = 0;
        for (var i = 0; i < KB.length; i++) {
            var score = 0;
            for (var j = 0; j < KB[i].norm.length; j++) {
                var k = KB[i].norm[j];
                if (k && (text.indexOf(' ' + k + ' ') !== -1 || text.indexOf(' ' + k + 's ') !== -1)) {
                    score += k.length;
                }
            }
            if (score && SPECIFIC.indexOf(KB[i].norm[0]) !== -1) score += 20;
            if (score > bestScore) { best = KB[i]; bestScore = score; }
        }
        return best ? best.answer : DEFAULT;
    }

    function respond(q) {
        addMsg(q, 'user');
        var ans = getAnswer(q);
        setTimeout(function () { addMsg(ans, 'bot'); }, 420);
    }

    // Chips rendered inside a bot answer are created after load, so the click is
    // delegated from the message list rather than bound per button.
    msgBox.addEventListener('click', function (e) {
        var chip = e.target.closest('.nc-inline-chip');
        if (!chip) return;
        e.preventDefault();
        respond(chip.dataset.q);
    });

    /* ── Open / close ── */
    function openChat() {
        if (!opened) {
            opened = true;
            badge.style.display = 'none';
            addMsg('Bonjour ! 👋 Je suis l\'assistant <strong>NADO</strong>.<br>Posez vos questions ou choisissez un sujet ci-dessous.', 'bot');
        }
        panel.classList.add('open');
    }

    function closeChat() { panel.classList.remove('open'); }

    btn.addEventListener('click', function () {
        panel.classList.contains('open') ? closeChat() : openChat();
    });
    closeBtn.addEventListener('click', closeChat);

    /* ── Chip questions ── */
    var chipAnswers = {
        telephone: 'Quel est votre numéro de téléphone ?',
        adresse:   'Où êtes-vous situés ?',
        horaires:  'Quels sont vos horaires ?',
        acheter:   'Je souhaite acheter une voiture',
        vendre:    'Je veux vendre ma voiture',
        financement: 'Comment financer une voiture à crédit ?',
        documents: 'Quels documents faut-il pour le dossier de financement ?',
        reprise:   'Pouvez-vous reprendre ma voiture ?',
        estimation: 'Je voudrais une estimation de ma voiture',
        garantie:  'Quelle garantie proposez-vous ?',
        whatsapp:  'Je veux parler à un conseiller sur WhatsApp',
    };
    chips.forEach(function (chip) {
        chip.addEventListener('click', function () {
            respond(chipAnswers[chip.dataset.q] || chip.textContent);
        });
    });

    /* ── Send text ── */
    function send() {
        var v = input.value.trim();
        if (!v) return;
        input.value = '';
        if (!panel.classList.contains('open')) openChat();
        respond(v);
    }
    sendBtn.addEventListener('click', send);
    input.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') send();
    });
})();
</script>
<!-- ── END CHATBOT ── -->

<script>
    // The stories heading must stay on ONE line at any width. Rather than guess a vw
    // value, measure the container and scale the text to fill it exactly.
    (function () {
        function fitStoryTitle() {
            var el = document.querySelector('.story-title');
            if (!el || !el.parentElement) return;

            var avail = el.parentElement.clientWidth;
            if (!avail) return;

            var PROBE = 100;                       // measure at a known size
            var prev = el.style.fontSize;
            el.style.fontSize = PROBE + 'px';
            var needed = el.scrollWidth;
            el.style.fontSize = prev;
            if (!needed) return;

            var size = Math.floor(PROBE * avail / needed) - 1;   // -1px safety margin
            size = Math.max(12, Math.min(size, 48));             // floor / desktop cap
            el.style.fontSize = size + 'px';
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', fitStoryTitle);
        } else {
            fitStoryTitle();
        }
        window.addEventListener('resize', fitStoryTitle);
        window.addEventListener('orientationchange', fitStoryTitle);
        // Re-run once webfonts have swapped in, since metrics change.
        if (document.fonts && document.fonts.ready) document.fonts.ready.then(fitStoryTitle);
    })();
</script>

<script>
    // "Derniers véhicules": 6 cards on a scroll track, 3 visible, arrows move one page.
    (function () {
        function init() {
            var track = document.querySelector('#derniers-vehicules .listings-grid');
            var prev = document.getElementById('dv-prev');
            var next = document.getElementById('dv-next');
            if (!track || !prev || !next) return;

            function page() {
                // Scroll by whatever is currently visible (3 cards on desktop, 2, then 1).
                return track.clientWidth;
            }

            function sync() {
                var max = track.scrollWidth - track.clientWidth - 2;   // 2px tolerance
                prev.disabled = track.scrollLeft <= 2;
                next.disabled = track.scrollLeft >= max;
            }

            prev.addEventListener('click', function () {
                track.scrollBy({ left: -page(), behavior: 'smooth' });
            });
            next.addEventListener('click', function () {
                track.scrollBy({ left: page(), behavior: 'smooth' });
            });

            track.addEventListener('scroll', sync, { passive: true });
            window.addEventListener('resize', sync);
            sync();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', init);
        } else {
            init();
        }
    })();
</script>
<!-- ── VIDEO PLAYER MODAL ── -->
<div id="yt-modal"
    style="display:none;position:fixed;inset:0;z-index:999999;background:rgba(7,13,26,.92);backdrop-filter:blur(6px);align-items:center;justify-content:center;padding:20px;">
    <div style="width:100%;max-width:960px;">
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
            <h4 id="yt-modal-title"
                style="flex:1;min-width:0;color:#fff;font-size:15px;font-weight:800;margin:0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
            </h4>
            <button type="button" id="yt-modal-close" aria-label="Fermer"
                style="flex-shrink:0;width:40px;height:40px;border:0;border-radius:50%;background:rgba(255,255,255,.15);color:#fff;font-size:22px;line-height:1;cursor:pointer;">&times;</button>
        </div>
        <div style="position:relative;width:100%;padding-top:56.25%;border-radius:16px;overflow:hidden;background:#000;">
            <iframe id="yt-modal-frame" src="" title="Vidéo NADO"
                style="position:absolute;inset:0;width:100%;height:100%;border:0;"
                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                allowfullscreen referrerpolicy="strict-origin-when-cross-origin"></iframe>
        </div>
    </div>
</div>

<script>
    // Plays the "Expériences Immersives" videos on the site instead of sending the
    // visitor to YouTube. The cards keep their href, so without JS the link still works.
    (function () {
        var modal = document.getElementById('yt-modal');
        var frame = document.getElementById('yt-modal-frame');
        var title = document.getElementById('yt-modal-title');
        var closeBtn = document.getElementById('yt-modal-close');
        if (!modal || !frame) return;

        function open(key, label) {
            if (!key) return;
            // nocookie host + rel=0 keeps suggested videos to the same channel.
            frame.src = 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(key)
                + '?autoplay=1&rel=0&modestbranding=1&playsinline=1';
            title.textContent = label || '';
            modal.style.display = 'flex';
            document.body.style.overflow = 'hidden';
        }

        function close() {
            modal.style.display = 'none';
            frame.src = '';                 // unload: stops playback and audio
            document.body.style.overflow = '';
        }

        document.addEventListener('click', function (e) {
            var card = e.target.closest('.js-video');
            if (!card) return;
            e.preventDefault();
            open(card.dataset.yt, card.dataset.title);
        });

        closeBtn && closeBtn.addEventListener('click', close);
        modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
    })();
</script>
</body>

</html>
