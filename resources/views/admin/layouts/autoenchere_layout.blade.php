<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auto Enchère')</title>
    <link rel="stylesheet" href="{{ asset('assets/back/assets/css/bootstrap.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/assets/vendors/bootstrap-icons/bootstrap-icons.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/back/assets/css/app.css') }}">
</head>
<body>
<div id="app">
    <!-- 🔹 Sidebar -->
    <div id="sidebar" class="active">
        <div class="sidebar-wrapper active">
            <div class="sidebar-header text-center mt-3">
                <a href="{{ route('dashboard') }}">
                    <img src="{{ asset('assets/images/logoenchere.jpg') }}" alt="Auto Enchère" width="150">
                </a>
                <h6 class="mt-2 text-secondary">Auto Enchère</h6>
            </div>

            <div class="sidebar-menu mt-4">
                <ul class="menu">
                    <li @class(['sidebar-item','active' => request()->routeIs('admin.nadoautoenchere.index')])>
                        <a href="{{ route('admin.nadoautoenchere.index') }}" class='sidebar-link'>
                            <i class="bi bi-coin"></i>
                            <span>Auto Enchère</span>
                        </a>
                    </li>

                    <li @class(['sidebar-item','active' => request()->routeIs('admin.nadoautoencherebid.index')])>
                        <a href="{{ route('admin.nadoautoencherebid.index') }}" class='sidebar-link'>
                            <i class="bi bi-tags"></i>
                            <span>Offres Auto Enchère</span>
                        </a>
                    </li>
                </ul>
            </div>

            <div class="sidebar-footer text-center mb-3">
                <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm">
                    <i class="bi bi-arrow-left"></i> Retour Admin
                </a>
            </div>
        </div>
    </div>

    <!-- 🔹 Main content -->
    <div id="main" class="layout-navbar">
        <header class="mb-3">
            <nav class="navbar navbar-expand navbar-light bg-white shadow-sm">
                <div class="container-fluid">
                    <a href="#" class="burger-btn d-block">
                        <i class="bi bi-justify fs-3"></i>
                    </a>
                    <div class="ms-auto d-flex align-items-center">
                        <div class="dropdown">
                            <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                <div class="user-menu d-flex align-items-center">
                                    <div class="user-name text-end me-3">
                                        <h6 class="mb-0 text-gray-600"></h6>
                                        <p class="mb-0 text-sm text-gray-600"></p>
                                    </div>
                                    <div class="avatar avatar-md">
                                        <img src="{{ asset('assets/back/assets/images/logo/user.png') }}" alt="user">
                                    </div>
                                </div>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <form method="POST" action="{{ route('admin.logout') }}">
                                        @csrf
                                        <a class="dropdown-item" href="#" onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="bi bi-box-arrow-left me-2"></i> Logout
                                        </a>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </nav>
        </header>

        <div class="page-content">
            @yield('content')
        </div>
    </div>
</div>

<script src="{{ asset('assets/back/assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="{{ asset('assets/back/assets/js/app.js') }}"></script>
</body>
</html>
