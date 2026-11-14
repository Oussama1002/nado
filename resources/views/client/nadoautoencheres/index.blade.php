@extends('layouts.client')


@section('title', 'Mes Vehicules')

@section('client_title', 'Mes véhicules')
@section('client_subtitle', 'Gestion de vos véhicules (flotte & enchère).')
@section('client_content')

{{-- Accès rapides : catégories, véhicules (flotte & enchère), annonces --}}
<style>
    .cv-tiles { display:grid; grid-template-columns:repeat(3,1fr); gap:14px; margin-bottom:26px; }
    @media (max-width:700px){ .cv-tiles { grid-template-columns:1fr; } }
    .cv-tile { display:flex; align-items:center; gap:12px; text-decoration:none;
        background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:16px 18px;
        box-shadow:0 1px 3px rgba(0,0,0,.05); transition:box-shadow .15s, transform .15s; }
    .cv-tile:hover { box-shadow:0 10px 26px -10px rgba(15,23,42,.18); transform:translateY(-2px); }
    .cv-tile i { font-size:22px; color:#1459DD; width:30px; text-align:center; flex-shrink:0; }
    .cv-tile .t { font-weight:800; color:#0f172a; font-size:14px; }
    .cv-tile .s { font-size:12px; color:#64748b; }
</style>
<div class="cv-tiles">
    <a href="{{ route('nadoautoenchere.categories') }}" class="cv-tile">
        <i class="fa fa-th-large"></i>
        <div><div class="t">Catégories</div><div class="s">Parcourir par catégorie</div></div>
    </a>
    <a href="{{ route('client.nadoautoencheres.index') }}" class="cv-tile">
        <i class="fa fa-gavel"></i>
        <div><div class="t">Mes véhicules</div><div class="s">Gestion (flotte &amp; enchère)</div></div>
    </a>
    <a href="{{ route('client.annonces.index') }}" class="cv-tile">
        <i class="fa fa-car"></i>
        <div><div class="t">Mes annonces</div><div class="s">Gestion de vos annonces</div></div>
    </a>
</div>

<div class="container my-5">
        <h2 class="text-center mb-4">Mes {{ $vehicules->count() }} vehicules disponibles</h2>

            @foreach($vehicules as $vehicule)


    <div class="row g-4">
            <div class="col-md-4 mb-4">
                <div class="card annonce-card shadow-sm">
                    <img src="{{ asset('storage/nadoautoenchere/' . (is_array($vehicule->images) ? $vehicule->images[0] : $vehicule->images)) 
             }}" 
                         class="card-img-top annonce-img" 
                         alt="{{ $vehicule->title }}">
                    
                    <div class="card-body">
                        <h5 class="card-title"> {{ strtoupper($vehicule->brand?->name ?? '') }}
                                {{ strtoupper($vehicule->modeleRelation?->name ?? '') }}</h5>

                        <div class="annonce-info">
                            <p><strong>Titre:</strong> {{ $vehicule->titre }}</p>

                            <p><strong>Prix:</strong> {{ number_format($vehicule->prix ?? 0, 0, ',', ' ') }} MAD</p>
                            <p><strong>Views:</strong> {{ number_format($vehicule->views ?? 0, 0, ',', ' ') }} </p>

                            <p><strong>Ville:</strong> {{ $vehicule->city }}</p>
                            <p><strong>Status:</strong> 
                                <span class="badge bg-success">Disponible</span>
                            </p>
                        </div>

                            <a href="{{ route('nadoautoenchere.index', ['category' => $vehicule->phone]) }}" 
   class="btn btn-primary w-100">
    Voir Détails
</a>

                    </div>
                </div>
            </div>
        @endforeach
    </div>
</div>

<style>
    .annonce-card {
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .annonce-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .annonce-img {
        width: 100%;
        height: 200px;
        object-fit: cover;
    }

    .annonce-info {
        margin-bottom: 15px;
    }

    .annonce-info p {
        margin: 5px 0;
        font-size: 14px;
        color: #444;
        list-style: none; /* ✅ no bullets */
    }

    .btn-primary {
        border-radius: 8px;
        font-weight: 600;
    }
     /* ✅ Extra padding for mobile */
    @media (max-width: 576px) {
        .annonce-card {
            margin-bottom: 15px;
        }
    }
</style>
@endsection