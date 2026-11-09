@extends('admin.layouts.app')

@section('title' , $encheres->exists ? 'Edit encheres' : 'New enchere')

@section('link')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/back/assets/css/uploader/image-uploader.min.css') }}">
@endsection
@section('content')
<div id="main-content">
      @if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="page-heading">
    <h3>Auto Encheres</h3>
</div>
<div class="page-content">
    <section class="row">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> Nouveau Auto Enchere</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
          <form 
    action="{{ $encheres->exists ? route('admin.nadoautoenchere.update', $encheres->id) : route('admin.nadoautoenchere.store') }}" 
    method="POST" 
    enctype="multipart/form-data">
    @csrf
    @method($encheres->exists ? 'PUT' : 'POST')

                            <div class="form-body">
                                <div class="row">
                                    @if($encheres->exists)
                                    <div class="col-12 my-2">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input  me-0" type="checkbox" name="active" id="active" value="1" @if($encheres->active != 0) checked=""  @endif>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="client-select">Client (remplit le téléphone)</label>
                                            <select id="client-select" class="form-select round">
                                                <option value="">— Sélectionner un client —</option>
                                                @foreach(($clients ?? []) as $c)
                                                    <option value="{{ $c->phone }}">{{ $c->name }}@if($c->phone) — {{ $c->phone }}@endif</option>
                                                @endforeach
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Téléphone" , "type" => "text",  "value" => $encheres->phone, "name" => 'phone', ])
                                    </div>
                                    <script>
                                        // Sélection d'un client → remplit automatiquement le téléphone.
                                        document.getElementById('client-select')?.addEventListener('change', function () {
                                            var phone = document.getElementById('phone');
                                            if (phone && this.value) { phone.value = this.value; }
                                        });
                                    </script>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Prix" , "type" => "text",  "value" => $encheres->prix, "name" => 'prix', ])
                                    </div>
                                    <div class="col-6 my-2">
    <div class="form-group">
        <label for="brand-select">Marque</label>
        <select id="brand-select"
                name="mark"
                class="form-control @error('mark') is-invalid @enderror">
            <option value="">Sélectionnez une marque</option>
            @foreach($brands as $brand)
                <option value="{{ $brand->id }}"
                    @if(old('mark', $encheres->mark ?? '') == $brand->id) selected @endif>
                    {{ $brand->name }}
                </option>
            @endforeach
        </select>
        @error('mark')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-6 my-2">
    <div class="form-group">
        <label for="model-select">Modèle</label>
        <select id="model-select"
                name="modele"
                class="form-control @error('modele') is-invalid @enderror">
            <option value="">Sélectionnez un modèle</option>
            {{-- Les modèles seront chargés dynamiquement par JS --}}
        </select>
        @error('modele')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
 <div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="starts_at">Date et heure de Demarrage</label>
        <input type="datetime-local" 
               id="starts_at" 
               name="starts_at" 
               class="form-control @error('starts_at') is-invalid @enderror"
               value="{{ old('starts_at', $encheres->starts_at ? \Carbon\Carbon::parse($encheres->starts_at)->format('Y-m-d\TH:i') : '') }}">
        @error('starts_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
     
                                    <div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="ends_at">Date et heure de fin</label>
        <input type="datetime-local" 
               id="ends_at" 
               name="ends_at" 
               class="form-control @error('ends_at') is-invalid @enderror"
               value="{{ old('ends_at', $encheres->ends_at ? \Carbon\Carbon::parse($encheres->ends_at)->format('Y-m-d\TH:i') : '') }}">
        @error('ends_at')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="matricule">Matricule</label>
        <input type="text" 
               id="matricule" 
               name="matricule" 
               class="form-control @error('matricule') is-invalid @enderror"
               placeholder="ex: 71134-H-6"
               value="{{ old('matricule', $encheres->matricule) }}">
        @error('matricule')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const matriculeInput = document.getElementById("matricule");

    matriculeInput.addEventListener("input", function (e) {
        let value = matriculeInput.value.toUpperCase().replace(/[^A-Z0-9]/g, ""); // uniquement lettres/chiffres

        // Format: 00000-X-00
        let formatted = "";

        if (value.length > 0) {
            formatted += value.substring(0, 5); // 5 chiffres
        }
        if (value.length > 5) {
            formatted += "-" + value.substring(5, 6); // 1 lettre
        }
        if (value.length > 6) {
            formatted += "-" + value.substring(6, 8); // 2 chiffres
        }

        matriculeInput.value = formatted;
    });
});
</script>

                                    <div class="col-6 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="city">Ville</label>
                                            <select class="form-control" name="city" id="city" required>
                                                <option value="">Sélectionner</option>
                                                @foreach($city as $c)
                                                    <option title="{{$c->name}}" value="{{$c->name}}" @selected($encheres->city == $c->name )>
                                                        {{$c->name}}
                                                    </option>
                                                @endforeach
                                            </select>

                                            @error('city')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                               
                                
                        
                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="year">Année</label>
                                            <select class="form-control" name="year" id="year" required>
                                                <option value="">Sélectionner</option>
                                                @for ($i = date("Y"); $i >= 1992; $i--)
                                                    <option title="{{$i}}" value="{{$i}}" @selected($encheres->year == $i )>
                                                        {{$i}}
                                                    </option>
                                                @endfor
                                            </select>

                                            @error('year')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Km" , "type" => "text", "name" => 'km', "value" => $encheres->km, ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="cv">Puissance fiscale</label>
                                            <select class="form-control" name="cv" id="cv" required>
                                                <option value="">Sélectionner</option>
                                                @for ($i = 2; $i <= 60; $i++)
                                                    <option title="{{$i}}" value="{{$i}}" @selected($encheres->cv == $i )>
                                                        {{$i}}
                                                    </option>
                                                @endfor
                                            </select>

                                            @error('cv')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Carburant" ,"name" => 'carburant', "value" => $encheres->carburant, "data" => ["" => "Sélectionner", "essence" => "Essence", "diesel" => "Diesel", "electrique" => "Electrique", "hybride" => "Hybride"]])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Boite de vitesses" ,"name" => 'box', "value" => $encheres->box, "data" => ["" => "Sélectionner", "manuelle" => "Manuelle", "automatique" => "Automatique"]])
                                    </div>
                                   
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Origine" ,"name" => 'origine', "value" => $encheres->origine, "data" => ["" => "Sélectionner", "dédouané" => "Dédouané", "WW au Maroc" => "WW au Maroc", "importé neuf" => "Importé neuf", "pas encore dédouané" => "Pas encore dédouané"]])
                                    </div>
                                 <div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="nb_Door">Nombre de portes</label>
        <select class="form-control" name="nb_Door" id="nb_Door" required>
            <option value="">Sélectionner</option>
            @for ($i = 2; $i <= 6; $i++)
                <option title="{{ $i }}" value="{{ $i }}" @selected($encheres->nb_Door == $i)>
                    {{ $i }}
                </option>
            @endfor
        </select>

        @error('nb_Door')
            <div class="invalid-feedback">
                {{ $message }}
            </div>
        @enderror
    </div>
</div>

{{-- ===== Caractéristiques complémentaires ===== --}}
<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="nb_cles">Nombre de clés</label>
        <select class="form-control @error('nb_cles') is-invalid @enderror" name="nb_cles" id="nb_cles">
            <option value="">Sélectionner</option>
            @for ($i = 1; $i <= 2; $i++)
                <option value="{{ $i }}" @selected(old('nb_cles', $encheres->nb_cles) == $i)>{{ $i }}</option>
            @endfor
        </select>
        @error('nb_cles')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="pneu_secours">Pneu de secours</label>
        <select class="form-control @error('pneu_secours') is-invalid @enderror" name="pneu_secours" id="pneu_secours">
            <option value="0" @selected(!old('pneu_secours', $encheres->pneu_secours))>Non</option>
            <option value="1" @selected(old('pneu_secours', $encheres->pneu_secours))>Oui</option>
        </select>
        @error('pneu_secours')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="outillage">Outillage</label>
        <select class="form-control @error('outillage') is-invalid @enderror" name="outillage" id="outillage">
            <option value="0" @selected(!old('outillage', $encheres->outillage))>Non</option>
            <option value="1" @selected(old('outillage', $encheres->outillage))>Oui</option>
        </select>
        @error('outillage')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- ===== Documents du véhicule ===== --}}
<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="carte_grise">Carte grise</label>
        <select class="form-control @error('carte_grise') is-invalid @enderror" name="carte_grise" id="carte_grise">
            <option value="0" @selected(!old('carte_grise', $encheres->carte_grise))>Non disponible</option>
            <option value="1" @selected(old('carte_grise', $encheres->carte_grise))>Disponible</option>
        </select>
        @error('carte_grise')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="vignette_annee">Vignette — dernière année payée</label>
        <select class="form-control @error('vignette_annee') is-invalid @enderror" name="vignette_annee" id="vignette_annee">
            <option value="">Non renseignée</option>
            @for ($y = date('Y') + 1; $y >= 2010; $y--)
                <option value="{{ $y }}" @selected(old('vignette_annee', $encheres->vignette_annee) == $y)>{{ $y }}</option>
            @endfor
        </select>
        @error('vignette_annee')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="visite_technique">Visite technique — valide jusqu'au</label>
        <input type="date"
               id="visite_technique"
               name="visite_technique"
               class="form-control @error('visite_technique') is-invalid @enderror"
               value="{{ old('visite_technique', optional($encheres->visite_technique)->format('Y-m-d')) }}">
        @error('visite_technique')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

{{-- ===== Localisation (carte affichée sur la fiche) ===== --}}
<div class="col-12 my-2">
    <div class="form-group col">
        <label for="maps_url">Lien Google Maps</label>
        <input type="text"
               id="maps_url"
               name="maps_url"
               class="form-control @error('maps_url') is-invalid @enderror"
               placeholder="Collez ici le lien de partage Google Maps"
               value="{{ old('maps_url', $encheres->maps_url) }}">
        <small class="text-muted">
            Collez le lien de partage : la latitude et la longitude se remplissent
            automatiquement si le lien les contient. Sinon, saisissez-les à la main
            (dans Google Maps, clic droit sur le point → les coordonnées sont copiées).
            Laissez vide pour utiliser le showroom NADO par défaut.
        </small>
        @error('maps_url')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12 col-md-6 my-2">
    <div class="form-group col">
        <label for="adresse">Adresse affichée</label>
        <input type="text"
               id="adresse"
               name="adresse"
               class="form-control @error('adresse') is-invalid @enderror"
               placeholder="ex: NADO.ma — Auto Enchère, Casablanca"
               value="{{ old('adresse', $encheres->adresse) }}">
        @error('adresse')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-6 col-md-3 my-2">
    <div class="form-group col">
        <label for="latitude">Latitude</label>
        <input type="text"
               id="latitude"
               name="latitude"
               class="form-control @error('latitude') is-invalid @enderror"
               placeholder="ex: 33.5277478 (auto depuis le lien)"
               value="{{ old('latitude', $encheres->latitude) }}">
        @error('latitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-6 col-md-3 my-2">
    <div class="form-group col">
        <label for="longitude">Longitude</label>
        <input type="text"
               id="longitude"
               name="longitude"
               class="form-control @error('longitude') is-invalid @enderror"
               placeholder="ex: -7.6037163 (auto depuis le lien)"
               value="{{ old('longitude', $encheres->longitude) }}">
        @error('longitude')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>

<div class="col-12 my-2">
    <div class="form-group col">
        <label>Aperçu de la carte</label>
        <div id="mapPreviewWrap" class="rounded" style="overflow:hidden;border:1px solid #dee2e6;">
            <iframe id="mapPreview" src="" width="100%" height="220"
                    style="border:0;display:block;" loading="lazy"></iframe>
        </div>
        <small class="text-muted" id="mapPreviewHint">Saisissez des coordonnées pour voir l'aperçu.</small>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var urlInput = document.getElementById('maps_url');
    var latInput = document.getElementById('latitude');
    var lngInput = document.getElementById('longitude');
    var preview  = document.getElementById('mapPreview');
    var hint     = document.getElementById('mapPreviewHint');
    if (!urlInput || !latInput || !lngInput) return;

    // Extrait lat/lng d'une URL Google Maps.
    // !3d<lat>!4d<lng> = position réelle du lieu (prioritaire)
    // @<lat>,<lng>     = centre de la vue (fallback)
    function extractLatLng(url) {
        var m = url.match(/!3d(-?\d+\.\d+)!4d(-?\d+\.\d+)/);
        if (m) return { lat: m[1], lng: m[2] };
        m = url.match(/@(-?\d+\.\d+),(-?\d+\.\d+)/);
        if (m) return { lat: m[1], lng: m[2] };
        m = url.match(/[?&]q=(-?\d+\.\d+),\s*(-?\d+\.\d+)/);
        if (m) return { lat: m[1], lng: m[2] };
        return null;
    }

    function refreshPreview() {
        var lat = parseFloat(latInput.value);
        var lng = parseFloat(lngInput.value);
        if (isNaN(lat) || isNaN(lng)) {
            preview.src = '';
            hint.textContent = "Saisissez des coordonnées pour voir l'aperçu.";
            return;
        }
        preview.src = 'https://maps.google.com/maps?q=' + lat + ',' + lng + '&z=16&hl=fr&output=embed';
        hint.textContent = 'Aperçu : ' + lat + ', ' + lng;
    }

    var adrInput = document.getElementById('adresse');

    // Appel au serveur pour les liens courts maps.app.goo.gl : le navigateur ne
    // peut pas suivre la redirection (CORS), c'est l'admin qui résout l'URL.
    function resolveOnServer(url) {
        hint.textContent = 'Résolution du lien en cours…';
        var token = document.querySelector('meta[name="csrf-token"]')?.content
                 || document.querySelector('input[name="_token"]')?.value || '';
        fetch("{{ url('/admin/nadoautoenchere/resolve-maps') }}", {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ url: url })
        })
        .then(function (r) { return r.ok ? r.json() : { ok: false }; })
        .then(function (data) {
            if (data && data.ok) {
                if (data.lat)  latInput.value = data.lat;
                if (data.lng)  lngInput.value = data.lng;
                if (data.name && adrInput && !adrInput.value.trim()) adrInput.value = data.name;
                refreshPreview();
            } else {
                hint.textContent = "Impossible de résoudre ce lien. Ouvrez-le dans Google Maps "
                    + "puis copiez le lien complet depuis la barre d'adresse, "
                    + "ou saisissez la latitude et la longitude à la main.";
            }
        })
        .catch(function () {
            hint.textContent = "Erreur réseau pendant la résolution du lien. "
                + "Essayez à nouveau ou saisissez les coordonnées à la main.";
        });
    }

    function handleUrl() {
        var url = (urlInput.value || '').trim();
        if (url === '') return;

        // 1) Essaie une extraction locale (URL longue avec !3d/!4d ou @lat,lng).
        var found = extractLatLng(url);
        if (found) {
            latInput.value = found.lat;
            lngInput.value = found.lng;
            refreshPreview();
            return;
        }

        // 2) Lien court ou format inconnu : on demande au serveur.
        resolveOnServer(url);
    }

    urlInput.addEventListener('input', handleUrl);
    urlInput.addEventListener('paste', function () { setTimeout(handleUrl, 50); });
    latInput.addEventListener('input', refreshPreview);
    lngInput.addEventListener('input', refreshPreview);
    refreshPreview();
});
</script>

                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="bodywork">Carrosserie</label>
                                            <select class="form-control" id="bodywork" name="bodywork" >
                                                <option value="">Sélectionner</option>
                                                <option title="Cabriolet" value="coupé cabriolet" @selected($encheres->bodywork == "coupé cabriolet" )>COUPé CABRIOLET</option>
                                                <option title="Suv et 4x4" value="suv et 4x4" @selected($encheres->bodywork == "suv et 4x4" )>SUV ET 4X4</option>
                                                <option title="Citadine" value="citadine" @selected($encheres->bodywork == "citadine" )>CITADINE</option>
                                                <option title="Berline" value="berline" @selected($encheres->bodywork == "berline" )>BERLINE</option>
                                                <option title="Compact" value="compact" @selected($encheres->bodywork == "compact" )>COMPACT</option>
                                                <option title="Crossover" value="crossover" @selected($encheres->bodywork == "crossover" )>CROSSOVER</option>
                                                <option title="Pick up" value="pick up" @selected($encheres->bodywork == "pick up" )>PICK UP</option>
                                            </select>

                                            @error('bodywork')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
@include('admin.partials.select', [
    "class" => 'col',
    "label" => "Première main",
    "name" => 'premiere_main',
    "value" => $encheres->premiere_main,
    "data" => ["" => "Sélectionner", "0" => "Non", "1" => "Oui"]
])
                                    </div>
                                    <div class="col-6 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="city">Couleur</label>
                              <select name="colors" id="color" class="form-control" required>
        <option value="">Sélectionnez une couleur</option>
        @foreach($colors as $color)
            <option value="{{ $color->name }}" 
                {{ old('colors', $encheres->colors) == $color->name ? 'selected' : '' }}>
                {{ $color->name }}
            </option>
        @endforeach
    </select>

                                            @error('city')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Titre" , "type" => "text",  "value" => $encheres->titre, "name" => 'titre', ])
                                    </div>

                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Reel" , "type" => "text",  "value" => $encheres->reel, "name" => 'reel', ])
                                    </div>
                               
                                   
{{-- SECTION OPTIONS --}}
<h4 class="mt-4">Options</h4>
<div class="row mb-3">
    <div class="col-12">
        <input type="checkbox" id="select-all-options" class="form-check-input">
        <label for="select-all-options">Tout sélectionner</label>
    </div>
</div>

<div class="row">
    @php
        $labels = [
            // Sécurité
            'airbags' => 'Airbags',
            'abs' => 'ABS',
            'esp' => 'ESP',
            'antipatinage' => 'Antipatinage',
            'aide_freinage_urgence' => "Aide au freinage d'urgence",
            'antidemarrage_electronique' => "Antidémarrage électronique",
            'aide_demarrage_cote' => "Aide au démarrage en côte",
            'selecteur_mode_conduite' => "Sélecteur de mode de conduite",
            'detection_fatigue' => "Détection de fatigue",
            'maintien_dans_la_voie' => "Maintien dans la voie",
            'detecteur_angle_mort' => "Détecteur d'angle mort",
            'detecteur_sous_gonflage' => "Détecteur de sous-gonflage",
            'fermeture_portes_auto' => "Fermeture de portes auto.",
            'preparation_isofix' => "Préparation ISOFIX",
            'phares_antibrouillard' => "Phares antibrouillard",
            'systeme_alarme' => "Système d’alarme",

            // Confort
            'climatisation' => 'Climatisation',
            'start_stop' => 'Start & Stop',
            'regulateur_vitesse' => 'Régulateur de vitesse',
            'detecteur_pluie' => 'Détecteur de pluie',
            'allumage_auto_feux' => 'Allumage auto. des feux',
            'frein_main_electrique' => 'Frein à main électrique',
            'aide_stationnement' => 'Aide au stationnement',
            'volant_reglable' => 'Volant réglable',
            'retros_electriques' => 'Rétros. électriques',
            'retros_rabat_electriques' => 'Rétros. rabattables électriques',
            'coffre_electrique' => 'Coffre électrique',
            'sieges_electriques' => 'Sièges électriques',
            'sieges_elec_memoire' => 'Sièges élec. avec mémoire',
            'banquette_rabattable' => 'Banquette arrière rabattable 1/3-2/3',

            // Multimédia
            'ecran_tactile' => 'Écran tactile',
            'camera_recul' => 'Caméra de recul',
            'cockpit_digital' => 'Cockpit digital',
            'commandes_volant' => 'Commandes au volant',
            'commandes_vocales' => 'Commandes vocales',
            'reconnaissance_panneaux' => 'Reconnaissance de panneaux',
            'affichage_tete_haute' => 'Affichage Tête-Haute',
            'systeme_audio' => 'Système audio',
            'ordinateur_bord' => 'Ordinateur de bord',
            'gps' => 'Navigation GPS',
            'wifi_bord' => 'WiFi à bord',
            'bluetooth' => 'Bluetooth',
            'compatibilite_smartphone' => 'Compatibilité smartphone',
            'apple_carplay_android_auto' => 'Apple CarPlay® & Android Auto®',
            'chargeur_sans_fil' => 'Chargeur/mobile sans fil',

            // Design & Extérieur
            'jantes_aluminium' => 'Jantes aluminium',
            'sellerie_similicuir_tissu' => 'Sellerie Similicuir / Tissu',
            'volant_cuir' => 'Volant cuir',
            'follow_me_home' => 'Follow-me home',
            'lumiere_ambiance' => "Lumière d'ambiance",
            'feux_jour_led' => 'Feux de jour LED',
            'phares_full_led' => 'Phares Full LED',
            'toit_panoramique' => 'Toit Panoramique ouvrant',
            'barres_de_toit' => 'Barres de toit',
            'vitres_surteintees' => 'Vitres sur-teintées',
        ];
    @endphp

    @foreach ($labels as $field => $label)
        <div class="col-6 col-md-3 my-2">
            <div class="form-check form-switch">
                <input class="form-check-input option-checkbox"
                       type="checkbox"
                       id="{{ $field }}"
                       name="{{ $field }}"
                       value="1"
                       @if(!empty($encheres) && $encheres->$field == 1) checked @endif>
                <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
            </div>
        </div>
    @endforeach
</div>

<div class="col-12 col-md-12 my-2">
    <div class="form-group col">
        <label for="images">Photos</label>
        <div class="choose-file text-center py-2 rounded bg-white">
            <div class="input-images"></div>
        </div>
    </div>
</div>

{{-- Rapport PDF --}}
<div class="col-6 my-2">
    <div class="form-group">
        <label for="rapport">Rapport PDF</label>
        <input type="file" name="rapport" id="rapport" class="form-control @error('rapport') is-invalid @enderror" accept="application/pdf">
        @error('rapport')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($encheres->rapport)
            <embed src="{{ asset('storage/nadoautoenchere/pdf/'.$encheres->rapport) }}" type="application/pdf" width="40%" />
        @endif
    </div>
</div>

                                   
                                    <div class="col-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            @if ($encheres->exists)
                                                Update
                                            @else
                                                Add new Annonce
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
</div>
@endsection
@section('script')
<script src="{{ asset('assets/back/assets/js/uploader/image-uploader.min.js') }}"></script>

<script>
$(document).ready(function() {

    // === Select All Options ===
    $('#select-all-options').on('change', function() {
        $('.option-checkbox').prop('checked', $(this).is(':checked'));
    });

    // === Brand / Model dynamic loading ===
    const brandSelect = $('#brand-select');
    const modelSelect = $('#model-select');

    function loadModels(brandId, selectedModelId = null) {
        if(!brandId){
            modelSelect.html('<option value="">Sélectionnez un modèle</option>');
            return;
        }

        modelSelect.html('<option value="">Chargement...</option>');

        $.getJSON('/client/models-by-brand/' + brandId, function(data){
            let options = '<option value="">Sélectionnez un modèle</option>';
            $.each(data, function(i, model){
                let selected = (selectedModelId && selectedModelId == model.id) ? 'selected' : '';
                options += `<option value="${model.id}" ${selected}>${model.name}</option>`;
            });
            modelSelect.html(options);
        }).fail(function(){
            modelSelect.html('<option value="">Erreur de chargement</option>');
        });
    }

    // On brand change
    brandSelect.change(function(){
        loadModels($(this).val());
    });

    // Preload old value after validation fail or edit
    @if(old('mark'))
        loadModels("{{ old('mark') }}", "{{ old('modele') }}");
    @elseif($encheres->mark)
        loadModels("{{ $encheres->mark }}", "{{ $encheres->modele }}");
    @endif

    // === Image uploader ===
let preloaded = [
    @if($encheres->images)
        @foreach($encheres->images as $index => $img)
            {id: {{ $index }}, src: "{{ asset('storage/nadoautoenchere/'.$img) }}" },
        @endforeach
    @endif
];



    $('.input-images').imageUploader({
        preloaded: preloaded,
        imagesInputName: 'images',
        preloadedInputName: 'old_images'
    });

});
</script>

@endsection
