<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Nado.ma')</title>

    <script src="https://unpkg.com/lucide@latest"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    @include('partials.locale-head')

    <!-- Owl Carousel CSS (kept for pages that still use it) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">

    <style>
        :root {
            --primary:    #1459DD;
            --secondary:  #FDD817;
            --slate-50:   #f8fafc;
            --slate-100:  #f1f5f9;
            --slate-200:  #e2e8f0;
            --slate-300:  #cbd5e1;
            --slate-400:  #94a3b8;
            --slate-500:  #64748b;
            --slate-600:  #475569;
            --slate-800:  #1e293b;
            --slate-900:  #0f172a;
            --white:      #ffffff;
            --black:      #000000;
        }

        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--white);
            color: var(--slate-900);
            min-height: 100vh;
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }
        a      { text-decoration: none; color: inherit; }
        ul     { list-style: none; }
        button { border: none; background: none; cursor: pointer; font-family: inherit; }
    </style>

    @yield('link')
    @yield('styles')
</head>

<body class="no-scrollbar">

    @php
        $client        = Auth::guard('client')->user();
        $isAutoEnchere = request()->routeIs('nadoautoenchere.*');
        $accentColor   = $isAutoEnchere ? '#F37021' : '#FDD817';
    @endphp

    {{-- Full NADO navbar (same partial as the home page) --}}
    @include('partials.nado-navbar')

    {{-- Spacer so content never sits under the fixed navbar --}}
    <div style="height:110px;"></div>

    <main>
        @yield('content')
    </main>

    @yield('script')
</body>
</html>
