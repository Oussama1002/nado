@extends('admin.layouts.app')

@section('title' , $ads->exists ? 'Edit ads' : 'New ads')

@section('link')
<link rel="stylesheet" type="text/css" href="{{ asset('assets/back/assets/css/uploader/image-uploader.min.css') }}">
@endsection
@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Annonces</h3>
</div>
<div class="page-content">
    <section class="row">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title"> Annonce</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route($ads->exists ? 'admin.annonces.update' : 'admin.annonces.store' , $ads) }}"  method="post" class="form form-vertical" enctype="multipart/form-data">
                            @csrf
                            @method($ads->exists ? 'PUT' : 'POST')
                            <div class="form-body">
                                <div class="row">
                                    @if($ads->exists)
                                    <div class="col-12 my-2">
                                        <div class="form-check form-switch fs-6">
                                            <input class="form-check-input  me-0" type="checkbox" name="active" id="active" value="1" @if($ads->active != 0) checked=""  @endif>
                                            <label class="form-check-label">Active</label>
                                        </div>
                                    </div>
                                    @endif
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Téléphone" , "type" => "text",  "value" => $ads->phone, "name" => 'phone', ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Prix" , "type" => "text",  "value" => $ads->prix, "name" => 'prix', ])
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
                    @if(old('mark', $ads->mark ?? '') == $brand->id) selected @endif>
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

                                    <div class="col-6 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="city">Ville</label>
                                            <select class="form-control" name="city" id="city" required>
                                                <option value="">Sélectionner</option>
                                                @foreach($city as $c)
                                                   <option value="{{ $c->name }}" @selected(old('city', $ads->city) == $c->name)>
    {{ $c->name }}
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
                                    <div class="col-6 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="status">status</label>
                                            <select class="form-control" name="status" id="status" required>
                                                <option value="">Sélectionner</option>
                                                <option value="disponible" @selected(old('status', $ads->status) == "disponible")>Disponible</option>
<option value="réserve" @selected(old('status', $ads->status) == "réserve")>Réserve</option>
<option value="vendu" @selected(old('status', $ads->status) == "vendu")>Vendu</option>

                                            </select>

                                            @error('status')
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
                                                   <option value="{{ $i }}" @selected(old('year', $ads->year) == $i)>
    {{ $i }}
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
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Km" , "type" => "text", "name" => 'km', "value" => $ads->km, ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="cv">Puissance fiscale</label>
                                            <select class="form-control" name="cv" id="cv" required>
                                                <option value="">Sélectionner</option>
                                                @for ($i = 2; $i <= 60; $i++)
                                                   <option value="{{ $i }}" @selected(old('cv', $ads->cv) == $i)>
    {{ $i }}
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
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Carburant" ,"name" => 'carburant', "value" => old('carburant', $ads->carburant),  "data" => ["" => "Sélectionner", "essence" => "Essence", "diesel" => "Diesel", "electrique" => "Electrique", "hybride" => "Hybride"]])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Boite de vitesses" ,"name" => 'box', "value" => old('box', $ads->box), "data" => ["" => "Sélectionner", "manuelle" => "Manuelle", "automatique" => "Automatique"]])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
@include('admin.partials.select', [
    "class" => 'col',
    "label" => "État",
    "name" => 'etat',
    "value" => old('etat', $ads->etat),
    "data" => [
        "" => "Sélectionner",
        "excellent" => "Excellent",
        "très bon" => "Très bon",
        "bon" => "Bon",
        "correct" => "Correct",
        "endommagé" => "Endommagé",
        "neuf" => "Neuf"
    ]
])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Origine" ,"name" => 'origine', "value" => old('origine', $ads->origine), "data" => ["" => "Sélectionner", "dédouané" => "Dédouané", "WW au Maroc" => "WW au Maroc", "importé neuf" => "Importé neuf", "pas encore dédouané" => "Pas encore dédouané"]])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="nb_Door">Nombre de portes</label>
                                            <select class="form-control" name="nb_Door" id="nb_Door" required>
                                                <option value="">Sélectionner</option>
            @for ($i = 2; $i <= 6; $i++)
                                                    <option value="{{ $i }}" @selected(old('nb_Door', $ads->nb_Door) == $i)>
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
                                    <div class="col-12 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="bodywork">Carrosserie</label>
                                            <select class="form-control" id="bodywork" name="bodywork" >
                                                <option value="">Sélectionner</option>
                                                <option title="Cabriolet" value="coupé cabriolet" @selected(old('bodywork', $ads->bodywork )== "coupé cabriolet" )>COUPé CABRIOLET</option>
                                                <option title="Suv et 4x4" value="suv et 4x4" @selected(old('bodywork', $ads->bodywork )== "suv et 4x4" )>SUV ET 4X4</option>
                                                <option title="Citadine" value="citadine" @selected(old('bodywork', $ads->bodywork) == "citadine" )>CITADINE</option>
                                                <option value="berline" @selected(old('bodywork', $ads->bodywork) == "berline")>Berline</option>
                                                <option title="Compact" value="compact" @selected(old('bodywork', $ads->bodywork )== "compact" )>COMPACT</option>
                                                <option title="Crossover" value="crossover" @selected(old('bodywork', $ads->bodywork )== "crossover" )>CROSSOVER</option>
                                                <option title="Pick up" value="pick up" @selected(old('bodywork', $ads->bodywork )== "pick up" )>PICK UP</option>
                                            </select>

                                            @error('bodywork')
                                                <div class="invalid-feedback">
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
@php
    // Stored values vary across migrated rows: "Oui"/"Non", but also 1/0 and "1"/"0".
    // Normalise onto the option values so the right one is preselected; anything else
    // recognisable is surfaced as-is rather than silently showing "Sélectionner".
    $rawPremiereMain = old('premiere_main', $ads->premiere_main);
    $premiereMain = match (true) {
        in_array($rawPremiereMain, [1, '1', true, 'oui', 'Oui', 'OUI'], true) => 'Oui',
        in_array($rawPremiereMain, [0, '0', false, 'non', 'Non', 'NON'], true) => 'Non',
        default => (string) ($rawPremiereMain ?? ''),
    };
    $premiereMainOptions = ["" => "Sélectionner", "Non" => "Non", "Oui" => "Oui"];
    if (filled($premiereMain) && !array_key_exists($premiereMain, $premiereMainOptions)) {
        $premiereMainOptions[$premiereMain] = $premiereMain . ' (valeur actuelle)';
    }
@endphp
@include('admin.partials.select', [
    "class" => 'col',
    "label" => "Première main",
    "name" => 'premiere_main',
    "value" => $premiereMain,
    "data" => $premiereMainOptions
])
                                    </div>
                                    <div class="col-6 col-md-6 my-2">
                                        <div class="form-group col">
                                            <label for="city">Couleur</label>
                              @php
                                  // Migrated annonces can hold a colour that no longer exists in the
                                  // colors table (e.g. "Bleu Ravenna"). Without this, no option matches,
                                  // the select renders empty, and submitting the form posts "" — which
                                  // silently erased the stored colour.
                                  $currentColor  = old('colors', $ads->colors);
                                  $knownColors   = collect($colors)->pluck('name')->all();
                                  $colorIsCustom = filled($currentColor)
                                      && !in_array($currentColor, $knownColors, true);
                              @endphp
                              <select name="colors" id="color" class="form-control" required>
        <option value="">Sélectionnez une couleur</option>
        @if($colorIsCustom)
            <option value="{{ $currentColor }}" selected>{{ $currentColor }} (valeur actuelle)</option>
        @endif
        @foreach($colors as $color)
            <option value="{{ $color->name }}"
                {{ $currentColor == $color->name ? 'selected' : '' }}>
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
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Titre" , "type" => "text",  "value" => $ads->titre, "name" => 'titre', ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Video" , "type" => "text",  "value" => $ads->video, "name" => 'video', ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Reel" , "type" => "text",  "value" => $ads->reel, "name" => 'reel', ])
                                    </div>
                                    <div class="col-6 col-md-3 my-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="garantie" name="garantie"  value="1" @if($ads->garantie == 1) checked @endif>
                                            <label class="form-check-label" for="garantie">Garantie</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 my-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="showroom" name="showroom"  value="1" @if($ads->showroom == 1) checked @endif>
                                            <label class="form-check-label" for="showroom">Showroom</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 my-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="finance" name="finance"  value="1" @if($ads->finance == 1) checked @endif>
                                            <label class="form-check-label" for="finance">Finance</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 my-2">
                                        <div class="form-check form-switch">
                                            {{-- New admin annonces are always certifié (forced in AnnoncesController::store) --}}
                                            <input class="form-check-input" type="checkbox" id="certifie" name="certifie"  value="1"
                                                   @if(!$ads->exists || $ads->certifie == 1) checked @endif
                                                   @unless($ads->exists) disabled @endunless>
                                            <label class="form-check-label" for="certifie">certifié</label>
                                        </div>
                                    </div>
                                    @if($ads->exists)
                                    <div class="col-6 col-md-3 my-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="particulie" name="particulie"  value="1" @if($ads->particulie == 1) checked @endif>
                                            <label class="form-check-label" for="particulie">particulie</label>
                                        </div>
                                    </div>
                                    <div class="col-6 col-md-3 my-2">
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="pro" name="pro" value="1" @if(($ads->pro ?? 0) == 1) checked @endif>
                                            <label class="form-check-label" for="pro">Pro</label>
                                        </div>
                                    </div>
                                    @endif
                                   
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
            'ordinateur' => 'Ordinateur de bord',
            'gps' => 'Navigation GPS',
            'wifi_bord' => 'WiFi à bord',
            'bluetooth' => 'Bluetooth',
            'compatibilite_smartphone' => 'Compatibilité smartphone',
            'apple_carplay_android_auto' => 'Apple CarPlay® & Android Auto®',
            'chargeur_sans_fil' => 'Chargeur/mobile sans fil',

            // Design & Extérieur
            'jantesA' => 'Jantes aluminium',
            'sellerie_similicuir_tissu' => 'Sellerie similicuir',
            'sellerie_tissu' => 'Sellerie tissu',
            'volant_cuir' => 'Volant cuir',
            'follow_me_home' => 'Follow-me home',
            'lumiere_ambiance' => "Lumière d'ambiance",
            'feux_jour_led' => 'Feux de jour LED',
            'phares_full_led' => 'Phares Full LED',
            'toit_ouvrant' => 'Toit ouvrant',
            'toit_panoramique' => 'Toit panoramique',
            'barres_de_toit' => 'Barres de toit',
            'vitres_surteintees' => 'Vitres sur-teintées',    ];
    @endphp

      @foreach ($labels as $field => $label)
        <div class="col-6 col-md-3 my-2">
            <div class="form-check form-switch">
                <input class="form-check-input option-checkbox"
                       type="checkbox"
                       id="{{ $field }}"
                       name="{{ $field }}"
                       value="1"
                       @if(!empty($ads) && $ads->$field == 1) checked @endif>
                <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
            </div>
        </div>
    @endforeach
</div>
<div class="col-12 col-md-12 my-2">
    <div class="form-group col">
        <label for="userfile">Photos</label>
        <div class="choose-file text-center py-2 rounded bg-white">
            <div class="msgImage"></div>
            <div class="input-images"></div>
        </div>
    </div>
</div>

<div class="col-6 my-2">
    <div class="form-group">
        <label for="rapport">Rapport PDF</label>
        <input type="file" name="rapport" id="rapport" class="form-control @error('rapport') is-invalid @enderror">
        @error('rapport')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
        @if($ads->rapport)
            <embed src="{{ asset('storage/pdf/'.$ads->rapport) }}" type="application/pdf" width="40%" />
        @endif
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
               value="{{ old('matricule', $ads->matricule) }}">
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
                                    <div class="col-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            @if ($ads->exists)
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
<script  src="{{ asset('assets/back/assets/js/uploader/image-uploader.min.js') }}"></script>
<script>
    $('#select-all-options').on('change', function() {
    $('.option-checkbox').prop('checked', $(this).is(':checked'));
});

</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const brandSelect = document.getElementById('brand-select');
    const modelSelect = document.getElementById('model-select');

    function loadModels(brandId, selectedModelId = null) {
        if (!brandId) {
            modelSelect.innerHTML = '<option value="">Sélectionnez un modèle</option>';
            return;
        }

        modelSelect.innerHTML = '<option value="">Chargement...</option>';

        fetch(`/client/models-by-brand/${brandId}`)
            .then(response => response.json())
            .then(data => {
                modelSelect.innerHTML = '<option value="">Sélectionnez un modèle</option>';
                data.forEach(model => {
                    const isSelected = selectedModelId && selectedModelId.toString() === model.id.toString();
                    const opt = document.createElement('option');
                    opt.value = model.id;
                    opt.textContent = model.name;
                    if (isSelected) opt.selected = true;
                    modelSelect.appendChild(opt);
                });
            });
    }

    // Charger automatiquement les modèles si on est en mode édition
    @if($ads->exists)
        loadModels("{{ $ads->mark }}", "{{ $ads->modele }}");
    @endif

    // En cas de vieux input (validation échouée)
    @if(old('mark'))
        loadModels("{{ old('mark') }}", "{{ old('modele') }}");
    @endif

    if (brandSelect) {
        brandSelect.addEventListener('change', function () {
            loadModels(this.value);
        });
    }
});
</script>

   <script>
$(function (){
    let preloaded = [];

    @if($ads->exists && is_array($ads->images))
        preloaded = @json(
            collect($ads->images)->map(fn($img) => [
                'id' => $img,
                'src' => asset('storage/ads/' . $img)
            ])
        );
    @endif

   $('.input-images').imageUploader({
    preloaded: preloaded,
    imagesInputName: 'uploaded_images',  // Changed from 'images' to 'uploaded_images'
    preloadedInputName: 'old_images',    // Keep as-is (matches validation)
    extensions: ['.jpg', '.jpeg', '.png', '.gif', '.svg', '.JPG', '.JPEG', '.PNG', '.GIF', '.SVG']
});
});
</script>
<script>

$(document).ready(function(){
    $("select.nado-marks").change(function () {
        $('select.nado-models').html("<option> Chargement en cours ... </option>").prop('disabled', 'disabled');
        var mark = $('select.nado-marks option:selected').attr('data-id');
        var formData = {
            'mark': mark,
            '_token' : $('input[name="_token"]').val()
        };
        var ajaxurl = "/admin/models/"+mark+"/getmodels";
        var models = '<option value="">Selectionner un modele</option>';
        $.ajax({
            type: "POST",
            url: ajaxurl,
            data: formData,
            dataType: 'json',
            success: function (data) {
                for(var i =0;i < data.length;i++){
                    models += '<option value="' + data[i].name + '">' + data[i].name + '</option>';
                 }

                console.log(models);
            },
            complete:function(){
                $('select.nado-models').html("").append(models).prop('disabled', false);
            }
        });
    });


    $('.delete-image').click(function(){
    	let id = $(this).parent('.uploaded-image').children('input').val();
        //delete media
         $.ajax({
           url: '/admin/annonces/'+id+'/deleteImage',
           type: 'post',
           data: {
        	"_token": "{{ csrf_token() }}",
        	},
           success: function(response){
               console.log(response);
           }
        });
    });


    });

</script>
@endsection
