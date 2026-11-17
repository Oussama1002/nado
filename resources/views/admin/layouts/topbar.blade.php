<header class="mb-3">
    <nav class="navbar navbar-expand navbar-light navbar-top bg-white shadow-sm">
        <div class="container-fluid d-flex justify-content-between align-items-center">

            <!-- 🔹 Sidebar Toggle Button -->
            <a href="#" class="burger-btn d-block">
                <i class="bi bi-justify fs-3"></i>
            </a>

            <!-- 🔹 Auto Enchère Link -->
            <ul class="navbar-nav ms-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark" style="color:black!important;" href="{{ route('admin.nadoautoenchere.index') }}">
                         Auto Enchère
                    </a>
                </li>
            </ul>
              <ul class="navbar-nav ms-3">
                <li class="nav-item">
                    <a class="nav-link fw-semibold text-dark" style="color:black!important;" href="{{ route('admin.dashboard') }}">
                        Nado
                    </a>
                </li>
            </ul>

            <!-- 🔹 User Menu -->
            <div class="dropdown">
                <a href="#" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="user-menu d-flex align-items-center">
                        <div class="user-name text-end me-3">
                            <h6 class="mb-0 text-gray-800">{{ ucwords(Auth::user()->name) }}</h6>
                            <p class="mb-0 text-sm text-muted">{{ strtoupper(Auth::user()->job) }}</p>
                        </div>
                        <div class="user-img d-flex align-items-center">
                            <div class="avatar avatar-md">
                                <img src="{{ asset('assets/back/assets/images/logo/user.png') }}" alt="User Avatar">
                            </div>
                        </div>
                    </div>
                </a>

                <ul class="dropdown-menu dropdown-menu-end" style="min-width: 11rem;">
                    <li>
                        <h6 class="dropdown-header">Hello, {{ ucwords(Auth::user()->name) }}!</h6>
                    </li>
                    <li>
                        <a class="dropdown-item" href="#">
                            <i class="icon-mid bi bi-person me-2"></i> My Profile
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <form method="POST" action="{{ route('admin.logout') }}">
                            @csrf
                            <a class="dropdown-item" href="{{ route('admin.logout') }}"
                               onclick="event.preventDefault(); this.closest('form').submit();">
                                <i class="icon-mid bi bi-box-arrow-left me-2"></i> Logout
                            </a>
                        </form>
                    </li>
                </ul>
            </div>

        </div>
    </nav>
</header>
