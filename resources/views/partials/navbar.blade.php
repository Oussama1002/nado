    <!-- header -->
    <header class="site-header header"style="height: 80px;">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css" />

		<!-- main header -->
        <div class="sticky-header main-bar-wraper">
            <div class="main-bar clearfix ">
                <div class="container clearfix">
                    <!-- website logo -->
                    <div class="logo-header mostion">
						<a href="{{ route('index') }}"><img src="{{ asset('assets/images/logo.png') }}" alt=""></a>
					</div>
                    <!-- nav toggle button -->
                    <button data-target=".header-nav" data-toggle="collapse" type="button" class="navbar-toggle collapsed" aria-expanded="false" >
						<i class="fa fa-bars"></i>
					</button>
                    <!-- main nav -->
                    <div class="header-nav navbar-collapse collapse">
                        <ul class="nav navbar-nav">
							<li> <a href="{{ route('achat.index') }}" style="color: #1f519b !important" >Achat</a></li>
							<li> <a href="{{ route('vente.form') }}" style="color: #1f519b !important" >Vente</a></li>
							<li> <a href="{{ route('reprise.form') }}" style="color: #1f519b !important" >Nado reprise</a></li>
							<li> <a href="{{ route('financement.index') }}" style="color: #1f519b !important">Auto finance</a></li>
							<li> <a href="{{ route('about.index') }}" style="color: #1f519b !important" >à-propos</a></li>
                            <li> <a href="{{ route('contactez-nous.index') }}" style="color: #1f519b !important" >Contactez-nous</a></li>
                            @if(feature_on('radio.enabled'))
                                <li> <a href="{{ route('nado-radio.index') }}" style="color: #1f519b !important" ><i class="fa fa-radio"></i> Nado Radio</a></li>
                            @endif

							<!-- <li> <a href="{{ route('services.index') }}" class="hide" >Services <i class="fa fa-chevron-down"></i></a>
								<ul class="sub-menu"> 
									<li><a href="{{ route('services.index') }}#intermediation" >Intermediation</a></li>
									<li><a href="{{ route('services.index') }}#reprise" >Reprise</a></li>
									<li><a href="{{ route('services.index') }}#nadopro" >Nado pro</a></li>
								</ul>
							</li>
							<li> <a href="{{ route('vendre.index') }}" class="hide">Vendre/Reprise</a></li> -->
                            @php 
    $client = Auth::guard('client')->user();
@endphp

@if ($client)
    {{-- ── Publier une annonce (logged in) ── --}}
    <li class="nav-item">
        <a href="{{ route('vente.form') }}?open_modal=1"
           style="background-color: #1459DD;
                  color: #fff !important;
                  padding: 8px 14px;
                  margin-top: 20px;
                  border-radius: 20px;
                  font-weight: bold;
                  text-decoration: none;
                  white-space: nowrap;">
            + Publier une annonce
        </a>
    </li>

    {{-- ── User dropdown ── --}}
    <li class="nav-item nado-dropdown" style="position: relative;">
        <a href="#" class="nado-dropdown-toggle"
           style="background-color: #FFD700;
                  color: #000 !important;
                  padding: 8px 16px;
                  border-radius: 20px;
                  display: inline-flex;
                  align-items: center;
                  gap: 8px;
                  margin-top: 20px;
                  font-weight: bold;
                  text-decoration: none;">
            <i class="fa fa-user"></i> {{ $client->name }}
            <i class="fa fa-chevron-down" style="margin-left: 5px;"></i>
        </a>

        <ul class="nado-sub-menu"
            style="position: absolute;
                   background: white;
                   list-style: none;
                   padding: 0;
                   margin: 5px 0 0;
                   border: 1px solid #ddd;
                   min-width: 180px;
                   display: none;
                   border-radius: 8px;
                   box-shadow: 0 2px 8px rgba(0,0,0,0.15);
                   z-index: 9999;">

            <li style="list-style: none;">
                <a href="{{ route('client.dashboard') }}"
                   style="display: block;
                          padding: 10px 15px;
                          text-decoration: none;
                          color: #333;
                          font-size: 14px;">
                    <i class="fa fa-home"></i> Mon espace
                </a>
            </li>

            <li style="list-style: none;">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                            style="width: 100%;
                                   background: none;
                                   border: none;
                                   padding: 10px 15px;
                                   text-align: left;
                                   color: #c00;
                                   font-size: 14px;
                                   cursor: pointer;">
                        <i class="fa fa-sign-out-alt"></i> Déconnexion
                    </button>
                </form>
            </li>
        </ul>
    </li>

    {{-- JS: click-only toggle (no hover conflicts) --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle  = document.querySelector('.nado-dropdown-toggle');
            const submenu = document.querySelector('.nado-sub-menu');
            if (!toggle || !submenu) return;

            toggle.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                const isOpen = submenu.style.display === 'block';
                submenu.style.display = isOpen ? 'none' : 'block';
            });

            // Close when clicking anywhere outside the dropdown
            document.addEventListener('click', function (e) {
                if (!toggle.contains(e.target) && !submenu.contains(e.target)) {
                    submenu.style.display = 'none';
                }
            });
        });
    </script>

@else
    {{-- ── Publier une annonce (guest → login first) ── --}}
    <li class="nav-item">
        <a href="{{ route('client.login') }}?redirect={{ urlencode(route('vente.form') . '?open_modal=1') }}"
           style="background-color: #1459DD;
                  color: #fff !important;
                  padding: 8px 14px;
                  margin-top: 20px;
                  border-radius: 20px;
                  font-weight: bold;
                  text-decoration: none;
                  white-space: nowrap;">
            + Publier une annonce
        </a>
    </li>

    {{-- ── Espace Client ── --}}
    <li class="nav-item">
        <a href="{{ route('client.login') }}"
           style="background-color: #FFD700;
                  color: #000 !important;
                  padding: 8px 14px;
                  margin-top: 20px;
                  border-radius: 20px;
                  font-weight: bold;
                  text-decoration: none;">
            Espace Client
        </a>
    </li>
@endif

</ul>


						</ul>
                    </div>
                </div>
            </div>
        </div>
        <!-- main header END -->
        <style>
.navbar-nav .nado-dropdown-toggle {
    display: inline-block !important;
}

.navbar-nav .nav-item {
    list-style: none !important;
}

/* Remove default bullets and spacing */
.navbar-nav,
.navbar-nav ul,
.navbar-nav li,
.navbar-nav .nado-sub-menu {
    list-style-type: none !important;
    list-style: none !important;
    padding-left: 0 !important;
    margin-left: 0 !important;
}

.navbar-nav li::marker,
.navbar-nav li::before {
    content: none !important;
    display: none !important;
}

/* Align main nav items horizontally */
.navbar-nav > li {
    display: inline-block !important;
    margin-right: 20px !important;
}

/* Submenu base — shown/hidden only via JS (no hover rule) */
.nado-sub-menu {
    background: white;
    border: 1px solid #ddd;
    position: absolute;
    min-width: 180px;
    z-index: 9999;
    border-radius: 8px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.15);
}

.nado-sub-menu li a {
    display: block;
    padding: 10px 15px;
    color: #333 !important;
    text-decoration: none;
    font-size: 14px;
}

.nado-sub-menu li a:hover {
    background: #f5f5f5;
}

.nado-sub-menu form button {
    width: 100%;
    background: none;
    border: none;
    padding: 10px 15px;
    text-align: left;
    color: #c00 !important;
    font-size: 14px;
    cursor: pointer;
}

.nado-sub-menu form button:hover {
    background: #fff5f5;
}
</style>

    </header>
    <!-- header END -->
