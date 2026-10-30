<div id="sidebar" class="active">
    <div class="sidebar-wrapper active">
        <div class="sidebar-header position-relative">
            <div class="d-flex justify-content-between align-items-center">
                <div class="logo">
                    <a href="#"><img src="{{ asset('assets/images/logo.png')}}" alt="Logo"></a>
                </div>
                <div class="theme-toggle d-flex gap-2 align-items-center mt-2 d-none">
                    <div class="form-check form-switch fs-6">
                        <input class="form-check-input me-0" type="checkbox" id="toggle-dark">
                        <label class="form-check-label"></label>
                    </div>
                </div>
                <div class="sidebar-toggler x">
                    <a href="#" class="sidebar-hide d-xl-none d-block"><i class="bi bi-x bi-middle"></i></a>
                </div>
            </div>
        </div>

        <div class="sidebar-menu">
            @php
                $current_route = request()->route() ? request()->route()->getName() : '';
                $isAutoEnchereSection = str_contains($current_route, 'nadoautoenchere') || str_contains($current_route, 'nadoautoencherebid');
            @endphp

            <ul class="menu">
                {{-- ✅ If on Auto Enchère section: show simplified sidebar --}}
                @if ($isAutoEnchereSection)
                    <li @class(['sidebar-item', 'active' => str_contains($current_route, 'nadoautoenchere')])>
                        <a href="{{ route('admin.nadoautoenchere.index') }}" class="sidebar-link">
                            <i class="bi bi-car-front"></i>
                            <span>Auto Enchère</span>
                        </a>
                    </li>

                    <li @class(['sidebar-item', 'active' => str_contains($current_route, 'nadoautoencherebid')])>
                        <a href="{{ route('admin.nadoautoencherebid.index') }}" class="sidebar-link">
                            <i class="bi bi-cash-coin"></i>
                            <span>Offres Auto Enchère</span>
                        </a>
                    </li>

                    <li @class(['sidebar-item', 'active' => request()->routeIs('admin.nadoautoenchere.clients')])>
                        <a href="{{ route('admin.nadoautoenchere.clients') }}" class="sidebar-link">
                            <i class="bi bi-people"></i>
                            <span>Clients Enchère</span>
                        </a>
                    </li>

                {{-- ✅ Otherwise: show the normal full admin sidebar --}}
                @else
                    @role('admin')
                    <li class="sidebar-item d-none">
                        <a href="{{route('dashboard')}}" class='sidebar-link'>
                            <i class="bi bi-grid-fill"></i>
                            <span>Dashboard</span>
                        </a>
                    </li>

                    {{-- Annonces --}}
                    <li @class(['sidebar-item','has-sub', 'active' => str_contains($current_route , "annonces.")])>
                        <a href="#" class='sidebar-link'>
                            <i class="bi bi-box"></i>
                            <span>Annonces</span>
                        </a>
                        <ul @class(['submenu', 'active' => str_contains($current_route , "annonces.")])>
                            <li @class(['submenu-item', 'active' => request()->routeIs('admin.annonces.index')])>
                                <a href="{{ route('admin.annonces.index') }}">List</a>
                            </li>
                            <li @class(['submenu-item', 'active' => request()->routeIs('admin.annonces.approval')])>
                                <a href="{{ route('admin.annonces.approval') }}">En attente</a>
                            </li>
                            <li @class(['submenu-item', 'active' => request()->routeIs('admin.annonces.create')])>
                                <a href="{{ route('admin.annonces.create') }}">New</a>
                            </li>
                        </ul>
                    </li>

                    {{-- Demandes --}}
                    <li @class(['sidebar-item', 'active' => str_contains($current_route , "demandes.")])>
                        <a href="{{ route('admin.demandes.index') }}" class='sidebar-link'>
                            <i class="bi bi-megaphone"></i>
                            <span>Demandes</span>
                        </a>
                    </li>
           <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "clients.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-people"></i>
            <span>Clients</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "clients.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.clients.index')])>
                <a href="{{ route("admin.clients.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.clients.approval')])>
    <a href="{{ route('admin.clients.approval') }}">En attente</a>
</li>

            <li @class(['submenu-item', 'active' => request()->routeIs('admin.clients.create')])>
                <a href="{{ route("admin.clients.create") }}">New</a>
            </li>
        </ul>
    </li>
    
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "ventes.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-car-front"></i>
            <span>Ventes professionnels B2B</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "ventes.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.ventes.index')])>
                <a href="{{ route("admin.ventes.index") }}">List</a>
            </li>
        </ul>
    </li>
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "reprises.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-arrow-left-right"></i>
            <span>Reprises</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "reprises.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.reprises.index')])>
                <a href="{{ route("admin.reprises.index") }}">List</a>
            </li>
        </ul>
    </li>
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "finances.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-cash"></i>
            <span>Financement</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "finances.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.finances.index')])>
                <a href="{{ route("admin.finances.index") }}">List</a>
            </li>
        </ul>
    </li>
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "FinancementSettings.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-cash"></i>
            <span>Parametres de Financement</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "FinancementSettings.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.FinancementSettings.index')])>
                <a href="{{ route("admin.FinancementSettings.index") }}">List</a>
            </li>
        </ul>
    </li>
    <!-- <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "offres.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-coin"></i>
            <span>offres</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "offres.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.offres.index')])>
                <a href="{{ route("admin.offres.index") }}">List</a>
            </li>
        </ul>
    </li> -->
   
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "brands.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-inbox"></i>
            <span>Brands</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "brands.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('brands.index')])>
                <a href="{{ route("admin.brands.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.brands.create')])>
                <a href="{{ route("admin.brands.create") }}">New</a>
            </li>
        </ul>
    </li>

    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "models.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-diagram-3"></i>
            <span>Models</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "models.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.models.index')])>
                <a href="{{ route("admin.models.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.models.create')])>
                <a href="{{ route("admin.models.create") }}">New</a>
            </li>
        </ul>
    </li>
    {{-- stories  --}}
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "stories.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-diagram-3"></i>
            <span>Stories</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "stories.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.stories.index')])>
                <a href="{{ route("admin.stories.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.stories.create')])>
                <a href="{{ route("admin.stories.create") }}">New</a>
            </li>
        </ul>
    </li>

    {{-- Social Posts (TikTok / Instagram) --}}
    <li @class(['sidebar-item','has-sub', 'active' => str_contains($current_route, 'social-posts.')]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-instagram"></i>
            <span>Mur social</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains($current_route, 'social-posts.')]) >
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.social-posts.index')])>
                <a href="{{ route('admin.social-posts.index') }}">Liste</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.social-posts.create')])>
                <a href="{{ route('admin.social-posts.create') }}">Nouveau</a>
            </li>
        </ul>
    </li>

    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "ville.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-pin-map"></i>
            <span>Ville</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "ville.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.ville.index')])>
                <a href="{{ route("admin.ville.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.ville.create')])>
                <a href="{{ route("admin.ville.create") }}">New</a>
            </li>
        </ul>
    </li>
    
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "colors.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-palette"></i>
            <span>Couleurs</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "colors.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.colors.index')])>
                <a href="{{ route("admin.colors.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.colors.create')])>
                <a href="{{ route("admin.colors.create") }}">New</a>
            </li>
        </ul>
    </li>

    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "videoads.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-film"></i>
            <span>Video ads</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "videoads.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.videoads.index')])>
                <a href="{{ route("admin.videoads.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.videoads.create')])>
                <a href="{{ route("admin.videoads.create") }}">New</a>
            </li>
        </ul>
    </li>

    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "imgslider.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-file-easel"></i>
            <span>Image slider</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "imgslider.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.imgslider.index')])>
                <a href="{{ route("admin.imgslider.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.imgslider.create')])>
                <a href="{{ route("admin.imgslider.create") }}">New</a>
            </li>
        </ul>
    </li>


    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "entretiens.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-people"></i>
            <span>entretiens</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "entretiens.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.entretiens.index')])>
                <a href="{{ route("admin.entretiens.index") }}">List</a>
            </li>
        
        </ul>
    </li>

    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "avistext.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-file-earmark-font"></i>
            <span>Avis text</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "avistext.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.avistext.index')])>
                <a href="{{ route("admin.avistext.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.avistext.create')])>
                <a href="{{ route("admin.avistext.create") }}">New</a>
            </li>
        </ul>
    </li>

    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "avisvideos.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-file-earmark-play"></i>
            <span>Avis video</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "avisvideos.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.avisvideos.index')])>
                <a href="{{ route("admin.avis-vd.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.avisvideos.create')])>
                <a href="{{ route("admin.avis-vd.create") }}">New</a>
            </li>
        </ul>
    </li>
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "users.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-people"></i>
            <span>Users</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "users.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('users.index')])>
                <a href="{{ route("admin.users.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('users.create')])>
                <a href="{{ route("admin.users.create") }}">New</a>
            </li>
        </ul>
    </li>
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "publications.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-people"></i>
            <span>Publications</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "publications.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('publications.index')])>
                <a href="{{ route("admin.publications.index") }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('publications.create')])>
                <a href="{{ route("admin.publications.create") }}">New</a>
            </li>
        </ul>
    </li>
    <li @class(['sidebar-item','has-sub', 'active' => str_contains( $current_route , "autoenchere.")]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-people"></i>
            <span>Offres</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains( $current_route , "autoenchere.")]) >
            <li  @class(['submenu-item', 'active' => request()->routeIs('admin.autoenchere.index')])>
                <a href="{{ route("admin.autoenchere.index") }}">List</a>
            </li>
            <!-- <li @class(['submenu-item', 'active' => request()->routeIs('admin.autoenchere.create')])>
                <a href="{{ route("admin.autoenchere.create") }}">New</a>
            </li> -->
        </ul>
    </li>

    <li @class(['sidebar-item', 'active' => str_contains($current_route, 'discussions.')])>
        <a href="{{ route('admin.discussions.index') }}" class="sidebar-link">
            <i class="bi bi-chat-dots-fill"></i>
            <span>Discussions</span>
        </a>
    </li>

    <li @class(['sidebar-item', 'active' => str_contains($current_route, 'estimations.')])>
        <a href="{{ route('admin.estimations.index') }}" class="sidebar-link">
            <i class="bi bi-calculator-fill"></i>
            <span>Estimations</span>
        </a>
    </li>

    {{-- Blog / Articles --}}
    <li @class(['sidebar-item','has-sub', 'active' => str_contains($current_route, 'articles.')]) >
        <a href="#" class='sidebar-link'>
            <i class="bi bi-journal-text"></i>
            <span>Blog</span>
        </a>
        <ul @class(['submenu', 'active' => str_contains($current_route, 'articles.')]) >
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.articles.index')])>
                <a href="{{ route('admin.articles.index') }}">List</a>
            </li>
            <li @class(['submenu-item', 'active' => request()->routeIs('admin.articles.create')])>
                <a href="{{ route('admin.articles.create') }}">New</a>
            </li>
        </ul>
    </li>

    {{-- Configuration du marketplace (prix, taux, modules) --}}
    <li @class(['sidebar-item', 'active' => str_contains($current_route, 'settings.')])>
        <a href="{{ route('admin.settings.index') }}" class="sidebar-link">
            <i class="bi bi-gear-fill"></i>
            <span>Configuration</span>
        </a>
    </li>

    {{-- 🔹 End of Admin Sidebar --}}
    @endrole
@endif
            </ul>
        </div>
    </div>
</div>