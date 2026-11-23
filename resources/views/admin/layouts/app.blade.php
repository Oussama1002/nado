<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    @include('admin.layouts.header')
<body class="theme-light" data-theme="light">
    <div id="app">
        @auth
            @include('admin.layouts.navigation')
        @endauth
        <div id="main" class="layout-navbar">
            @auth
                @if(!isset($exception) )
                    @include('admin.layouts.topbar')
                @endif
                @include('admin.partials.flashdata')
                @include('admin.partials.breadcrumbs')
            @endauth
            @yield('content')
            @include('admin.layouts.footer')
        </div>
    </div>
</body>

</html>
