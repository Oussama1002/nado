@extends('layouts.client')

@section('client_title', __('messages.cannonce.veh_create_title'))
@section('client_subtitle', __('messages.cannonce.veh_create_subtitle'))
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

    <h1 class="mb-4">{{ __('messages.cannonce.veh_create_title') }}</h1>

    <form action="{{ route('client.annonces.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        {{-- SECTION 1 : Informations générales --}}
        <h4 class="mt-4">{{ __('messages.cannonce.sec_general') }}</h4>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>Titre</label>
                <input type="text" name="titre" class="form-control" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>Prix (DH)</label>
                <input type="number" name="prix" class="form-control" required>
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
        <option value="{{ $color->name }}">{{ $color->name }}</option>
    @endforeach
</select>

</div>


            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.km') }}</label>
                <input type="number" name="km" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.cv') }}</label>
                <input type="number" name="cv" class="form-control" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.doors') }}</label>
                <select name="nb_Door" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="3">{{ __('messages.cannonce.doors_3') }}</option>
                    <option value="5">{{ __('messages.cannonce.doors_5') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.year') }}</label>
                <select name="year" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select_year') }}</option>
                    @for ($y = date('Y'); $y >= 1990; $y--)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endfor
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
                    <option value="Oui">{{ __('messages.nadoae.yes') }}</option>
                    <option value="Non">{{ __('messages.nadoae.no') }}</option>
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
<h4 class="mt-4">{{ __('messages.cannonce.options') }}</h4>

<div class="mb-2">
    <input type="checkbox" id="checkAllOptions" class="form-check-input">
    <label for="checkAllOptions" class="form-check-label fw-bold text-primary">{{ __('messages.cannonce.select_all') }}</label>
</div>

<div class="row">
    @php
    $options = [
        // SÉCURITÉ
        'airbags','abs','eps','antipatinage','aide_freinage_urgence','antidemarrage',
        'aide_demarrage_cote','selecteur_mode_conduite','detection_fatigue','maintien_voie',
        'detecteur_angle_mort','detecteur_sous_gonflage','fermeture_auto','isofix',
        'phares_antibrouillard','alarme',

        // CONFORT
        'climatisation','start_stop','regulateur_vitesse','detecteur_pluie','allumage_auto',
        'frein_main_elec','aide_stationnement','volant_reglable','vitreselec',
        'retros_elec','retros_rab_elec','coffre_elec','siege_cuir','sieges_mem',
        'banquette_rab',

        // MULTIMÉDIA
        'ecran_tactile','camera_recul','park_assist','cockpit_digital','cmd_volant',
        'cmd_vocale','reco_panneaux','affichage_tete_haute','systeme_audio','ordinateur',
        'gps','wifi','bluetooth','smartphone','carplay_android','chargeur_sans_fil',

        // ESTHÉTIQUE
        'jantesA','sellerie','volant_cuir','follow_me_home','lumiere_ambiance',
        'feux_jour_led','phares_led','toit_ouvrant','double_toit_ouvrant',
        'barres_toit','vitres_teintees'
    ];


    @endphp

   @foreach ($options as $field => $label)
        <div class="col-md-3 mb-2 form-check">
            <input type="checkbox"
                   class="form-check-input option-checkbox"
                   id="{{ $field }}"
                   name="{{ $field }}"
                   value="1"
                   {{ old($field) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $field }}">{{ $label }}</label>
        </div>
    @endforeach
</div>

      {{-- SECTION 4 : Images --}}
<h4 class="mt-4">{{ __('messages.cannonce.images') }}</h4>
<div class="mb-3">
<label>{{ __('messages.cannonce.add_images') }}</label>
<div id="dropzone" class="dropzone"></div>
<!-- <input type="file" name="images[]" id="imagesInput" multiple hidden> -->
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
  
   <!-- PACK MODAL -->
<div class="modal fade" id="packModal" tabindex="-1" aria-labelledby="packModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
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
            Publiez gratuitement vos annonces sur Nado.ma
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
.pack-card {
    cursor: pointer;
    transition: all 0.3s ease;
    background-color: #fefefe;
    border: 2px solid #007bff;
    border-radius: 12px;
}

.pack-card:hover {
    transform: scale(1.03);
    background-color: #f0f8ff;
    box-shadow: 0 0 15px rgba(0, 123, 255, 0.2);
}

/* Style quand une carte est sélectionnée */
.selected-pack {
    border-color: #28a745 !important;
    background-color: #e9fbe9;
    box-shadow: 0 0 10px rgba(40, 167, 69, 0.3);
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
<script src="https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/min/dropzone.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/5.9.3/dropzone.min.css"/>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<!-- <script src="/path/to/custom.min.js"></script> -->

<script>
Dropzone.autoDiscover = false;

let uploadedImages = [];

function updateImageList() {
    const form = document.querySelector('form');

    // Reset existing inputs
    ['imagesInput', 'mainImageInput', 'imgrleft', 'imgrtop', 'imgrmed', 'imgrbottom', 'imgrright', 'imginterior'].forEach(id => {
        const el = document.getElementById(id);
        if (el) el.remove();
    });

    // First image → main image
    if (uploadedImages.length > 0) {
        const dtMain = new DataTransfer();
        dtMain.items.add(uploadedImages[0]);
        const mainInput = document.createElement('input');
        mainInput.type = 'file';
        mainInput.name = 'image';
        mainInput.id = 'mainImageInput';
        mainInput.style.display = 'none';
        mainInput.files = dtMain.files;
        form.appendChild(mainInput);
    }

    // Other images → imgrleft, imgrtop, imgrmed, imgrbottom, imgrright, imginterior
    const otherFields = ['imgrleft','imgrtop','imgrmed','imgrbottom','imgrright','imginterior'];
    uploadedImages.slice(1).forEach((file, index) => {
        if (index >= otherFields.length) return; // ignore extra files
        const dt = new DataTransfer();
        dt.items.add(file);
        const input = document.createElement('input');
        input.type = 'file';
        input.name = otherFields[index];
        input.id = otherFields[index];
        input.style.display = 'none';
        input.files = dt.files;
        form.appendChild(input);
    });
}

Dropzone.autoDiscover = false;
const dropzone = new Dropzone("#dropzone", {
    url: "#", // inutile si on submit le form
    autoProcessQueue: false,
    addRemoveLinks: true,
    acceptedFiles: "image/*"
});

const form = document.querySelector('form'); // ajoute ça AVANT
form.addEventListener('submit', function() {
    updateImageList();
});



// document.querySelector("form").addEventListener("submit", function(e) {
//     e.preventDefault();
//     e.stopPropagation();

//     if (dropzone.getQueuedFiles().length === 0) {
//         alert("Veuillez ajouter au moins une image !");
//         return;
//     }

//     dropzone.processQueue(); // envoie les fichiers
// });

// Quand Dropzone a fini
dropzone.on("sendingmultiple", function(data, xhr, formData) {
    // Ajoute tous les autres champs du formulaire
    const form = document.querySelector("form");
    new FormData(form).forEach((value, key) => {
        formData.append(key, value);
    });
});

dropzone.on("addedfile", function(file) {
    uploadedImages.push(file);
});
dropzone.on("removedfile", function(file) {
    uploadedImages = uploadedImages.filter(f => f !== file);
});


</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('form');
    const selectedPackInput = document.getElementById('selectedPackId');
    const publishBtn = document.getElementById('showPackModal');
    const packCards = document.querySelectorAll('.pack-card');
    const freeBtn = document.getElementById('select-free-pack');
    const hiddenSubmitBtn = document.getElementById('hiddenSubmitBtn');

    let packModal;

  publishBtn.addEventListener('click', function () {
    if (!form.checkValidity()) {
        form.reportValidity();
        return;
    }

    // if (uploadedImages.length === 0) {
    //     alert("Veuillez ajouter au moins une image !");
    //     return;
    // }

    updateImageList();

    // show modal
    const packModalEl = document.getElementById('packModal');
    packModal = new bootstrap.Modal(packModalEl);
    packModal.show();
});



    // 👉 Choix pack payant
    packCards.forEach(card => {
        card.addEventListener('click', function () {
            const packId = this.dataset.packId;
            selectedPackInput.value = packId;
            if (packModal) packModal.hide();
            hiddenSubmitBtn.click();
        });
    });

    // 👉 Choix pack gratuit dans popup
    if (freeBtn) {
        freeBtn.addEventListener('click', function (e) {
            e.preventDefault();
            selectedPackInput.value = 1;
            if (packModal) packModal.hide();
            hiddenSubmitBtn.click();
        });
    }
});


</script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const brandSelect = document.getElementById('brand-select');
    const modelSelect = document.getElementById('model-select');
    const checkAll = document.getElementById('checkAllOptions');
    const optionCheckboxes = Array.from(document.querySelectorAll('input.option-checkbox'));

    function loadModels(brandId, selectedModelId = null) {
        if (!brandId) {
            modelSelect.innerHTML = '<option value="">' + @json(__('messages.cannonce.select_model')) + '</option>';
            return;
        }

        modelSelect.innerHTML = '<option value="">Chargement...</option>';

        fetch(`/client/models-by-brand/${brandId}`)
            .then(response => {
                if (!response.ok) throw new Error('Network error');
                return response.json();
            })
            .then(data => {
                modelSelect.innerHTML = '<option value="">' + @json(__('messages.cannonce.select_model')) + '</option>';
                data.forEach(model => {
                    const isSelected = selectedModelId && selectedModelId.toString() === model.id.toString();
                    const opt = document.createElement('option');
                    opt.value = model.id;
                    opt.textContent = model.name;
                    if (isSelected) opt.selected = true;
                    modelSelect.appendChild(opt);
                });
            })
            .catch(() => {
                modelSelect.innerHTML = '<option value="">Erreur de chargement</option>';
            });
    }

    // Quand la marque change
    if (brandSelect) {
        brandSelect.addEventListener('change', function () {
            loadModels(this.value);
        });
    }

    // Si on a une ancienne sélection après validation échouée, pré-remplir
    @if(old('mark'))
        loadModels("{{ old('mark') }}", "{{ old('modele') }}");
    @endif

    // Tout sélectionner / désélectionner
    function updateMasterCheckbox() {
        if (!checkAll) return;
        const allChecked = optionCheckboxes.every(cb => cb.checked);
        checkAll.checked = allChecked;
    }

    if (checkAll) {
        checkAll.addEventListener('change', function () {
            optionCheckboxes.forEach(cb => cb.checked = this.checked);
        });
    }

    optionCheckboxes.forEach(cb => {
        cb.addEventListener('change', updateMasterCheckbox);
    });

    // initialisation de l'état du master
    updateMasterCheckbox();
});
</script>

@endsection
