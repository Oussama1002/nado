<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Espace Client — NADO</title>
    <link rel="icon" href="{{ asset('assets/images/favicon.png') }}" type="image/x-icon">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
    <script src="https://unpkg.com/lucide@latest" defer></script>
    {{-- Arabic font + RTL base styles --}}
    @include('partials.locale-head')
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        html, body { height: 100%; }
        body { font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif; }
        html[dir="rtl"] body { font-family: 'Cairo', 'Inter', sans-serif; }
        /* Floating language switch on the auth screens. */
        .auth-lang-switch { position: fixed; top: 16px; inset-inline-end: 16px; z-index: 1000; }
    </style>
</head>
<body>
    <div class="auth-lang-switch">@include('partials.lang-switcher')</div>
    @yield('content')
</body>
</html>
