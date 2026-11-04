@extends('layouts.client')

@section('client_title', __('messages.cannonce.create_title'))
@section('client_subtitle', __('messages.cannonce.create_subtitle'))
@section('client_content')

<div class="container">
@if ($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <h1 class="mb-4">{{ __('messages.cannonce.h1_create') }}</h1>

    <form action="{{ route('client.annonces.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION 1 : Informations générales --}}
        <h4 class="mt-4">{{ __('messages.cannonce.sec_general') }}</h4>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>{{ __('messages.cannonce.title') }}</label>
<input type="text" name="titre" class="form-control" value="{{ old('titre') }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('messages.cannonce.price') }}</label>
<input type="number" name="prix" class="form-control" value="{{ old('prix') }}" required>
            </div>
        </div>

        {{-- SECTION 2 : Véhicule --}}
        <h4 class="mt-4">{{ __('messages.cannonce.sec_vehicle') }}</h4>
        <div class="row">
            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.marque') }}</label>
                <select name="mark" id="brand-select" class="form-control" required>
    <option value="">{{ __('messages.cannonce.choose_brand') }}</option>
    @foreach($brands as $brand)
        <option value="{{ $brand->id }}" {{ old('mark') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
    @endforeach
</select>

            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.modele') }}</label>
                <select name="modele" id="model-select" class="form-control" required>
    <option value="">{{ __('messages.cannonce.select_model') }}</option>
</select>

            </div>

          <div class="col-md-4 mb-3">
    <label for="color">{{ __('messages.cannonce.color') }}</label>
   <select name="colors" id="color" class="form-control" required>
    <option value="">{{ __('messages.cannonce.select_color') }}</option>
    @foreach($colors as $color)
<option value="{{ $color->name }}" {{ old('colors') == $color->name ? 'selected' : '' }}>
    {{ $color->name }}
</option>
    @endforeach
</select>

</div>
<div class="form-group mb-3">
    <label for="matricule" class="form-label">{{ __('messages.cannonce.matricule') }}</label>
    <input type="text" name="matricule" id="matricule" class="form-control"
           value="{{ old('matricule') }}" placeholder="{{ __('messages.cannonce.matricule_ph') }}">
    @error('matricule')
        <span class="text-danger">{{ $message }}</span>
    @enderror
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

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.km') }}</label>
<input type="number" name="km" class="form-control" value="{{ old('km') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.cv') }}</label>
<input type="number" name="cv" class="form-control" value="{{ old('cv') }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.doors') }}</label>
              <select name="nb_Door" class="form-control" required>
    <option value="">{{ __('messages.cannonce.select') }}</option>
    <option value="3" {{ old('nb_Door') == 3 ? 'selected' : '' }}>{{ __('messages.cannonce.doors_3') }}</option>
    <option value="5" {{ old('nb_Door') == 5 ? 'selected' : '' }}>{{ __('messages.cannonce.doors_5') }}</option>
</select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.year') }}</label>
                <select name="year" class="form-control" required>
    <option value="">{{ __('messages.cannonce.select_year') }}</option>
    @for ($y = date('Y'); $y >= 1990; $y--)
        <option value="{{ $y }}" {{ old('year') == $y ? 'selected' : '' }}>{{ $y }}</option>
    @endfor
</select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.bodywork') }}</label>
                <select name="bodywork" class="form-control" >
                    <option value="">{{ __('messages.cannonce.select2') }}</option>
                                                <option value="coupé cabriolet" >{{ __('messages.cannonce.body_cabriolet') }}</option>
                                                <option value="suv et 4x4" >{{ __('messages.cannonce.body_suv') }}</option>
                                                <option value="citadine">{{ __('messages.cannonce.body_citadine') }}</option>
                                                <option value="berline" >{{ __('messages.cannonce.body_berline') }}</option>
                                                <option value="compact" >{{ __('messages.cannonce.body_compact') }}</option>
                                                <option value="crossover" >{{ __('messages.cannonce.body_crossover') }}</option>
                                                <option value="pick up" >{{ __('messages.cannonce.body_pickup') }}</option>
                                          </select>
            </div>
 <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.origin') }}</label>
                <select name="origine" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="WW au Maroc">{{ __('messages.ventemodal.origin_ww') }}</option>
                    <option value="dédouané">{{ __('messages.ventemodal.origin_cleared') }}</option>
                </select>
            </div>
           <div class="col-md-4 mb-3">
    <label>{{ __('messages.cannonce.first_main') }}</label>
    <select name="premiere_main" class="form-control" required>
        <option value="">{{ __('messages.cannonce.select') }}</option>
       <option value="Oui" {{ old('premiere_main') == 'Oui' ? 'selected' : '' }}>{{ __('messages.nadoae.yes') }}</option>
<option value="Non" {{ old('premiere_main') == 'Non' ? 'selected' : '' }}>{{ __('messages.nadoae.no') }}</option>
</select>
</div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.state') }}</label>
                <select name="etat" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="Neuf">{{ __('messages.ventemodal.state_new') }}</option>
                    <option value="Très bon état">{{ __('messages.cannonce.state_tres_bon') }}</option>
                    <option value="Bon état">{{ __('messages.cannonce.state_bon') }}</option>
                    <option value="Accidenté">{{ __('messages.cannonce.state_accidente') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.fuel') }}</label>
                <select name="carburant" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="diesel">{{ __('messages.ventemodal.fuel_diesel') }}</option>
                    <option value="essence">{{ __('messages.ventemodal.fuel_essence') }}</option>
                    <option value="hybride">{{ __('messages.ventemodal.fuel_hybrid') }}</option>
                    <option value="electrique">{{ __('messages.ventemodal.fuel_electric') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.box') }}</label>
                <select name="box" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="automatique">{{ __('messages.ventemodal.box_auto') }}</option>
                    <option value="manuelle">{{ __('messages.ventemodal.box_manual') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.city') }}</label>
                <select name="city" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select_city') }}</option>
                    @foreach ($villes as $ville)
                        <option value="{{ $ville->name }}">{{ $ville->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

       {{-- SECTION 3 : Options --}}
{{-- SECTION OPTIONS --}}
<h4 class="mt-4">{{ __('messages.cannonce.options') }}</h4>
<div class="row mb-3">
    <div class="col-12">
        <input type="checkbox" id="select-all-options" class="form-check-input">
        <label for="select-all-options">{{ __('messages.cannonce.select_all') }}</label>
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
            'jantes_aluminium' => 'Jantes aluminium',
            'sellerie_similicuir_tissu' => 'Sellerie Similicuir / Tissu',
            'volant_cuir' => 'Volant cuir',
            'follow_me_home' => 'Follow-me home',
            'lumiere_ambiance' => "Lumière d'ambiance",
            'feux_jour_led' => 'Feux de jour LED',
            'phares_full_led' => 'Phares Full LED',
            'toit_panoramique' => 'Toit Panoramique ouvrant',
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
                <label class="form-check-label" for="{{ $field }}">{{ __('messages.equip.'.$field) }}</label>
            </div>
        </div>
    @endforeach
</div>
<div class="mt-4">
    <label class="form-label">{{ __('messages.cannonce.video') }}</label>
    <input type="file" name="video" accept="video/*" class="form-control">
</div>

     {{-- SECTION 4 : Images --}}
<h4 class="mt-4">{{ __('messages.cannonce.images') }}</h4>
<div class="mb-3">
  <label>{{ __('messages.cannonce.add_images') }}</label>
  <div id="dropzone" class="dropzone"></div>

  <!-- hidden native input that Laravel will receive (kept for compatibility, but Dropzone uploads async) -->
  <!-- <input type="file" name="images[]" id="imagesInput" multiple hidden> -->

  <!-- container for hidden filename inputs created by JS -->
  <div id="uploadedImagesContainer" class="d-none"></div>
</div>

      <!-- {{-- SECTION 5 : Packs --}}
<h4 class="mt-4">Choisissez un Pack</h4>
@foreach($packs as $index => $pack)
    <div class="form-check mb-2">
        <input class="form-check-input" type="radio" name="pack_id" id="pack{{ $pack->id }}"
               value="{{ $pack->id }}"
               {{ old('pack_id', $packs->first()->id) == $pack->id ? 'checked' : '' }}>
        <label class="form-check-label" for="pack{{ $pack->id }}">
            <strong>{{ $pack->name }}</strong> - {{ $pack->price }} DH : {{ $pack->description }}
        </label>
    </div>
@endforeach -->



      {{-- SECTION 6 : Actions --}}
<div class="mt-4">
    {{-- Bouton Publier -> Ouvre uniquement le modal --}}
    <button type="button" id="showPackModal" class="btn btn-success">{{ __('messages.cannonce.publish') }}</button>

    <a href="{{ route('client.annonces.index') }}" class="btn btn-secondary">{{ __('messages.cannonce.cancel') }}</a>
</div>

{{-- Champ caché pour pack choisi --}}
<input type="hidden" name="pack_id" id="selectedPackId">

{{-- Bouton submit caché (sera cliqué en JS) --}}
<button type="submit" id="hiddenSubmitBtn" class="d-none"></button>

    </form>
  <style>
    /* Force Pack Modal to be truly full screen */
.pack-modal-dialog {
    margin: 50px !important;
    /* max-width: 100% !important; */
    width: 93% !important;
    height: 100% !important;
}

#packModal .modal-content {
    border-radius: 0 !important; /* remove rounded corners */
    height: 100%;
}

  </style>
   <!-- PACK MODAL -->
<div class="modal fade" id="packModal" tabindex="-1" aria-labelledby="packModalLabel" aria-hidden="true">
<div class="modal-dialog modal-fullscreen pack-modal-dialog">
    <div class="modal-content rounded-4 shadow-lg">
      <div class="modal-header border-0 bg-white">
        <h5 class="modal-title fw-bold text-primary" id="packModalLabel">{{ __('messages.plans.heading_1') }} <span class="text-dark">PACKs</span></h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>

      <div class="modal-body bg-white">
        <div class="row text-center">
          <!-- BASIC -->
          <div class="col-md-4 mb-4">
            <div class="pack-card border border-primary rounded-4 h-100 p-4 d-flex flex-column justify-content-between" data-pack-id="2">
              <div>
                <span class="badge bg-primary mb-2 px-3 py-1 fs-6">BASIC</span>
                <h6 class="fw-semibold mt-3 mb-3 text-primary">{{ __('messages.plans.duration_7') }}</h6>
                <ul class="list-unstyled small text-start px-2">
                  <li>• {{ __('messages.plans.feat_renew_1') }}</li>
                  <li>• {{ __('messages.plans.feat_social') }}</li>
                  <li>• {{ __('messages.plans.feat_vitrine') }}</li>
                  <li>• {{ __('messages.plans.feat_show_number') }}</li>
                </ul>
              </div>
              <div class="mt-4 fw-bold fs-5 text-primary">199 DH</div>
            </div>
          </div>

          <!-- MEDIUM -->
          <div class="col-md-4 mb-4">
            <div class="pack-card border border-primary rounded-4 h-100 p-4 d-flex flex-column justify-content-between" data-pack-id="3">
              <div>
                <span class="badge bg-primary mb-2 px-3 py-1 fs-6">MEDIUM</span>
                <h6 class="fw-semibold mt-3 mb-3 text-primary">{{ __('messages.plans.duration_15') }}</h6>
                <ul class="list-unstyled small text-start px-2">
                  <li>• {{ __('messages.plans.feat_renew_1') }}</li>
                  <li>• {{ __('messages.plans.feat_social') }}</li>
                  <li>• {{ __('messages.plans.feat_vitrine') }}</li>
                  <li>• {{ __('messages.plans.feat_show_number') }}</li>
                </ul>
              </div>
              <div class="mt-4 fw-bold fs-5 text-primary">299 DH</div>
            </div>
          </div>

          <!-- PREMIUM -->
          <div class="col-md-4 mb-4">
            <div class="pack-card border border-primary rounded-4 h-100 p-4 d-flex flex-column justify-content-between" data-pack-id="4">
              <div>
                <span class="badge bg-primary mb-2 px-3 py-1 fs-6">PREMIUM</span>
                <h6 class="fw-semibold mt-3 mb-3 text-primary">{{ __('messages.plans.duration_30') }}</h6>
                <ul class="list-unstyled small text-start px-2">
                  <li>• {{ __('messages.plans.feat_renew_2') }}</li>
                  <li>• {{ __('messages.plans.feat_social') }}</li>
                  <li>• {{ __('messages.plans.feat_vitrine') }}</li>
                  <li>• {{ __('messages.plans.feat_reels') }}</li>
                  <li>• {{ __('messages.plans.feat_youtube') }}</li>
                  <li>• {{ __('messages.plans.feat_calls') }}</li>

                  <li>• {{ __('messages.plans.feat_appointments') }}</li>
                </ul>
              </div>
              <div class="mt-4 fw-bold fs-5 text-primary">499 DH</div>
            </div>
          </div>
        </div>

        <!-- FREE PACK BUTTON -->
        <div class="text-center mt-4">
          <button class="btn btn-outline-primary fw-bold" id="select-free-pack" style="border-radius: 30px;">
            {{ __('messages.plans.publish_free') }}
          </button>
        </div>
      </div>
    </div>
  </div>
</div>


<style>
/* Corrige les propriétés essentielles du modal Bootstrap */
.modal {
    z-index: 1055 !important;
}

.modal.fade {
    opacity: 0;
    transition: opacity 0.15s linear;
}

.modal.fade.show {
    opacity: 1;
    display: block !important;
}

.modal-backdrop.show {
    opacity: 0.5;
}
</style>
<style>
/* Pack Modal - agrandir les cartes et le texte */
.pack-card {
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #fefefe;
    border: 3px solid #007bff;
    border-radius: 20px;

    /* Taille ++ */
    min-height: 550px; /* hauteur des cartes */
    padding: 2.5rem !important;
    font-size: 1.2rem; /* taille de base du texte */
}

.pack-card:hover {
    transform: scale(1.05);
    background-color: #f0f8ff;
    box-shadow: 0 0 25px rgba(0, 123, 255, 0.3);
}

/* Titre + badge plus gros */
.pack-card .badge {
    font-size: 1.2rem !important;
    padding: 0.6rem 1.2rem !important;
}

.pack-card h6 {
    font-size: 1.6rem;
    font-weight: 700;
}

/* Texte de la liste plus grand */
.pack-card ul li {
    font-size: 2.1rem;
    margin-bottom: 0.5rem;
}

/* Prix bien visible */
.pack-card .fw-bold.fs-5 {
    font-size: 2rem !important;
    color: #007bff !important;
}

/* Sélection visuelle */
.selected-pack {
    border-color: #28a745 !important;
    background-color: #e9fbe9;
    box-shadow: 0 0 20px rgba(40, 167, 69, 0.4);
}

.badge.bg-primary {
    border-radius: 20px;
    font-weight: 600;
}

.modal-content {
    border-radius: 1rem;
}
.is-invalid {
    border-color: #dc3545 !important;
    box-shadow: 0 0 5px rgba(220, 53, 69, 0.5);
}

</style>


</div>
<!-- === Dépendances Dropzone et jQuery === -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>

<!-- === Sélection globale des options === -->
<script>
$('#select-all-options').on('change', function() {
  $('.option-checkbox').prop('checked', $(this).is(':checked'));
});
</script>
<!-- Dropzone CSS/JS must be loaded (you already have these lines earlier) -->
<!-- <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"/> -->
<!-- <script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script> -->

<script>
Dropzone.autoDiscover = false;

(function () {
  const uploadUrl = "{{ route('client.annonces.uploadTemp') }}";
  const csrf = "{{ csrf_token() }}";
  const formEl = document.querySelector('form');

  // Hidden inputs container (we created it in HTML)
  const hiddenContainer = document.getElementById('uploadedImagesContainer');

  // Map Dropzone file -> server filename
  const fileToServerName = new WeakMap();

  const dz = new Dropzone("#dropzone", {
    url: uploadUrl,
    paramName: "file",
    method: "post",
    autoProcessQueue: true,    // upload immediately when a file is added
    uploadMultiple: false,
    maxFilesize: 8, // MB
    acceptedFiles: "image/*",
    addRemoveLinks: true,
    clickable: true,
    headers: { 'X-CSRF-TOKEN': csrf },
    dictDefaultMessage: @json(__('messages.cannonce.dropzone_msg')),
    init: function() {
      // If you want to display server-side preloaded images (editing) you'll need to emit events here.
    }
  });

  // On successful upload -> server responds with { filename: 'uniq.jpg' }
  dz.on('success', function(file, response) {
    const serverFilename = response && (response.filename || response.path || response.name) ? (response.filename || response.path || response.name) : null;
    if (!serverFilename) {
      // mark as error
      console.error('Upload response missing filename:', response);
      if (file.previewElement) file.previewElement.classList.add('dz-error');
      return;
    }

    // remember mapping
    fileToServerName.set(file, serverFilename);

    // create hidden input so Laravel receives filenames in request->uploaded_images
    const hidden = document.createElement('input');
    hidden.type = 'hidden';
    hidden.name = 'uploaded_images[]';
    hidden.value = serverFilename;
    // attach to file object so we can remove it later
    file._uploadedHiddenInput = hidden;
    hiddenContainer.appendChild(hidden);
  });

  // If upload fails
  dz.on('error', function(file, error) {
    console.error('Dropzone upload error:', error);
  });

  // When user removes a file from Dropzone UI
  dz.on('removedfile', function(file) {
    // Remove corresponding hidden input
    try {
      if (file._uploadedHiddenInput && file._uploadedHiddenInput.parentNode) {
        file._uploadedHiddenInput.parentNode.removeChild(file._uploadedHiddenInput);
      }
      // Optionally, call server to delete temp file (not required). You can implement delete-temp route if desired.
      // const serverFilename = fileToServerName.get(file);
      // if (serverFilename) { fetch('/client/annonces/delete-temp', ...) }
    } catch (err) {
      console.warn('Error removing uploaded hidden input', err);
    }
  });

  // Final form submit validation: ensure at least one uploaded_images[] exists
  formEl.addEventListener('submit', function(e) {
    const uploadedInputs = hiddenContainer.querySelectorAll('input[name="uploaded_images[]"]');
    if (!uploadedInputs.length) {
      e.preventDefault();
      alert('Veuillez ajouter au moins une image !');
      document.getElementById('dropzone').scrollIntoView({ behavior: 'smooth' });
    }
  });

})();
</script>

<!-- === Gestion des Packs (Modal + Validation) === -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const form = document.querySelector('form');
  const selectedPackInput = document.getElementById('selectedPackId');
  const publishBtn = document.getElementById('showPackModal');
  const hiddenSubmitBtn = document.getElementById('hiddenSubmitBtn');
  const packCards = document.querySelectorAll('.pack-card');
  const freeBtn = document.getElementById('select-free-pack');
  let packModal;

  publishBtn.addEventListener('click', () => {
    if (!form.checkValidity()) {
      form.reportValidity();
      return;
    }

    // Show modal
    const modalEl = document.getElementById('packModal');
    packModal = new bootstrap.Modal(modalEl);
    packModal.show();
  });

  packCards.forEach(card => {
    card.addEventListener('click', function() {
      selectedPackInput.value = this.dataset.packId;
      packModal?.hide();
      hiddenSubmitBtn.click(); // will submit the form
    });
  });

  freeBtn?.addEventListener('click', e => {
    e.preventDefault();
    selectedPackInput.value = 1;
    packModal?.hide();
    hiddenSubmitBtn.click();
  });
});
</script>


<!-- === Gestion Marque / Modèle et Options === -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const brandSelect = document.getElementById('brand-select');
  const modelSelect = document.getElementById('model-select');
  const checkAll = document.getElementById('checkAllOptions');
  const optionCheckboxes = [...document.querySelectorAll('input.option-checkbox')];

  function loadModels(brandId, selectedModelId = null) {
    if (!brandId) {
      modelSelect.innerHTML = '<option value="">' + @json(__('messages.cannonce.select_model')) + '</option>';
      return;
    }
    modelSelect.innerHTML = '<option value="">Chargement...</option>';

    fetch(`/client/models-by-brand/${brandId}`)
      .then(res => res.json())
      .then(data => {
        modelSelect.innerHTML = '<option value="">' + @json(__('messages.cannonce.select_model')) + '</option>';
        data.forEach(model => {
          const opt = document.createElement('option');
          opt.value = model.id;
          opt.textContent = model.name;
          if (selectedModelId && selectedModelId.toString() === model.id.toString())
            opt.selected = true;
          modelSelect.appendChild(opt);
        });
      })
      .catch(() => modelSelect.innerHTML = '<option value="">Erreur de chargement</option>');
  }

  if (brandSelect) {
    brandSelect.addEventListener('change', function() {
      loadModels(this.value);
    });
  }

  @if(old('mark'))
    loadModels("{{ old('mark') }}", "{{ old('modele') }}");
  @endif

  function updateMasterCheckbox() {
    if (!checkAll) return;
    checkAll.checked = optionCheckboxes.every(cb => cb.checked);
  }

  checkAll?.addEventListener('change', function() {
    optionCheckboxes.forEach(cb => cb.checked = this.checked);
  });

  optionCheckboxes.forEach(cb => cb.addEventListener('change', updateMasterCheckbox));
  updateMasterCheckbox();
});
</script>

@endsection
