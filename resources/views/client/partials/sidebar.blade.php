@php
    $client = Auth::guard('client')->user();
    $isFlotte = ($client->type ?? '') === 'flotte&enchere';
    $logo = $client->logo ?? null;
    if (is_string($logo) && str_starts_with($logo, '[')) {
        $decoded = json_decode($logo, true);
        $logo = $decoded[0] ?? null;
    }

    $active = fn($name) => request()->routeIs($name) ? 'active' : '';
@endphp

<style>
    .cs-profile { padding-top: 24px; }
    /* removed NADO logo block per request */

    .cs-profile { padding: 18px 22px; display:flex; align-items:center; gap: 12px; border-bottom: 1px solid rgba(255,255,255,.07); }
    .cs-avatar { width: 44px; height: 44px; border-radius: 50%; object-fit: cover; border: 2px solid rgba(255,255,255,.2); flex-shrink:0; }
    .cs-name { font-size: 14px; font-weight: 800; color: #fff; white-space: nowrap; overflow:hidden; text-overflow: ellipsis; }
    .cs-type { font-size: 11px; font-weight: 650; color: rgba(255,255,255,.45); text-transform: capitalize; }

    .cs-nav { padding: 12px; }
    .cs-label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: rgba(255,255,255,.28); padding: 10px 12px 6px; }
    .cs-link {
        display:flex; align-items:center; gap: 12px;
        padding: 10px 12px; border-radius: 10px;
        color: rgba(255,255,255,.65); text-decoration:none;
        font-size: 13.5px; font-weight: 650;
        transition: background .15s, color .15s;
        margin-bottom: 2px;
    }
    .cs-link i.fa { width: 18px; text-align:center; font-size: 14px; }
    .cs-link:hover { background: rgba(255,255,255,.09); color:#fff; }
    .cs-link.active { background: rgba(20,89,221,.55); color:#fff; }
    .cs-link.danger { color: rgba(248,113,113,.85); }
    .cs-link.danger:hover { background: rgba(239,68,68,.15); color:#f87171; }
    .cs-logout button {
        background:none;border:none;color:inherit;font:inherit;cursor:pointer;width:100%;
        display:flex;align-items:center;gap:12px;padding:0;
    }
</style>

<aside class="client-sidebar" id="clientSidebar">
    <div class="cs-profile">
        @if($logo)
            <img class="cs-avatar" src="{{ asset('storage/nadoautoenchere/logo/' . $logo) }}" alt="Logo">
        @else
            <img class="cs-avatar" src="{{ asset('assets/images/user.png') }}" alt="Avatar">
        @endif
        <div style="min-width:0;">
            <div class="cs-name">{{ $client->name }}</div>
            <div class="cs-type">
                {{ $client->type ?? 'client' }}
                @if($isFlotte && $client->ae_role)
                    <span style="margin-left:6px;padding:1px 8px;border-radius:99px;background:rgba(255,255,255,.16);font-size:10px;font-weight:800;text-transform:uppercase;letter-spacing:.04em;">{{ $client->ae_role === 'vendeur' ? 'Vendeur' : 'Acheteur' }}</span>
                @endif
            </div>
        </div>
    </div>

    <nav class="cs-nav">
        <div class="cs-label">Tableau de bord</div>
        <a class="cs-link {{ $active('client.dashboard') }}" href="{{ route('client.dashboard') }}">
            <i class="fa fa-th-large"></i> Vue d'ensemble
        </a>

        @if($isFlotte)
            <div class="cs-label">Flotte</div>
            <a class="cs-link {{ $active('client.nadoautoencheres.index') }}" href="{{ route('client.nadoautoencheres.index') }}">
                <i class="fa fa-car"></i> Mes Véhicules
            </a>
            <a class="cs-link {{ $active('client.nadoautoencheresbid.mybids') }}" href="{{ route('client.nadoautoencheresbid.mybids') }}">
                <i class="fa fa-gavel"></i> Mes Offres
            </a>
            <a class="cs-link {{ $active('client.nadoautoencheresbid.index') }}" href="{{ route('client.nadoautoencheresbid.index') }}">
                <i class="fa fa-list"></i> Offres reçues
            </a>
            <a class="cs-link {{ $active('client.equipe.index') }}" href="{{ route('client.equipe.index') }}">
                <i class="fa fa-users"></i> Mon Équipe
            </a>
        @else
            <div class="cs-label">Annonces</div>
            <a class="cs-link" href="{{ route('vente.form', ['open_modal' => 1]) }}">
                <i class="fa fa-plus-circle"></i> Déposer une annonce
            </a>
            <a class="cs-link {{ $active('client.annonces.index') }}" href="{{ route('client.annonces.index') }}">
                <i class="fa fa-car"></i> Mes Annonces
            </a>
        @endif

        <div class="cs-label">Compte</div>
        <a class="cs-link {{ $active('client.profile.edit') }}" href="{{ route('client.profile.edit') }}">
            <i class="fa fa-user"></i> Mon Profil
        </a>
        <a class="cs-link {{ $active('client.favorites') }}" href="{{ route('client.favorites') }}">
            <i class="fa fa-heart"></i> Mes Favoris
        </a>
        <a class="cs-link {{ $active('client.mes-demandes') }}" href="{{ route('client.mes-demandes') }}">
            <i class="fa fa-search"></i> Mes demandes
        </a>
        @if(feature_on('discovery.saved_searches'))
        <a class="cs-link {{ $active('saved-searches.index') }}" href="{{ route('saved-searches.index') }}">
            <i class="fa fa-bell"></i> {{ __('messages.discovery.saved_searches') }}
        </a>
        @endif
        <a class="cs-link {{ $active('client.discussions.index') }}" href="{{ route('client.discussions.index') }}">
            <i class="fa fa-comments"></i> Mes Discussions
        </a>

        <div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(255,255,255,.07);">
            <div class="cs-link danger cs-logout">
                <form method="POST" action="{{ route('logout') }}" style="width:100%;margin:0;">
                    @csrf
                    <button type="submit"><i class="fa fa-sign-out"></i> Déconnexion</button>
                </form>
            </div>
        </div>
    </nav>
</aside>

