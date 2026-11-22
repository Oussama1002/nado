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

            @php $adminNotifs = \App\Services\AdminNotifications::recent(); @endphp
            <div class="ms-auto d-flex align-items-center gap-3">

                {{-- 🔔 Cloche notifications --}}
                <div class="dropdown">
                    <a href="#" class="position-relative text-dark" role="button"
                       id="adminNotifBell" data-bs-toggle="dropdown"
                       aria-expanded="false" aria-label="Notifications"
                       style="font-size: 1.4rem; text-decoration: none;">
                        <i class="bi bi-bell-fill"></i>
                        @if(count($adminNotifs) > 0)
                            <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                  style="font-size: .7rem;">
                                {{ count($adminNotifs) > 99 ? '99+' : count($adminNotifs) }}
                                <span class="visually-hidden">notifications</span>
                            </span>
                        @endif
                    </a>
                    <div class="dropdown-menu dropdown-menu-end shadow p-0"
                         aria-labelledby="adminNotifBell"
                         style="width: 380px; max-width: 95vw;">
                        <div class="d-flex justify-content-between align-items-center px-3 py-2 border-bottom bg-light">
                            <strong class="small text-uppercase text-muted">Notifications</strong>
                            @if(count($adminNotifs) > 0)
                                <span class="badge bg-danger rounded-pill">{{ count($adminNotifs) }}</span>
                            @endif
                        </div>
                        <div style="max-height: 420px; overflow-y: auto;">
                            @forelse($adminNotifs as $n)
                                <a href="{{ $n['url'] }}"
                                   class="dropdown-item d-flex align-items-start gap-3 py-2 border-bottom"
                                   style="white-space: normal;">
                                    <span class="flex-shrink-0 d-inline-flex align-items-center justify-content-center rounded-circle text-white"
                                          style="width:36px;height:36px;background:var(--bs-{{ $n['tone'] }}, #6c757d);">
                                        <i class="bi {{ $n['icon'] }}"></i>
                                    </span>
                                    <span class="flex-grow-1 min-w-0">
                                        <span class="d-flex justify-content-between align-items-baseline gap-2">
                                            <span class="fw-semibold small text-truncate">{{ $n['title'] }}</span>
                                            <small class="text-muted flex-shrink-0">{{ $n['at']?->diffForHumans(null, true) ?? '—' }}</small>
                                        </span>
                                        <span class="text-muted small d-block text-truncate">{{ $n['body'] }}</span>
                                    </span>
                                </a>
                            @empty
                                <div class="text-center text-muted py-4 small">
                                    <i class="bi bi-bell-slash fs-3 d-block mb-1"></i>
                                    Aucune notification.
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

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

            </div> {{-- /end right cluster (bell + user menu) --}}

        </div>
    </nav>
</header>
