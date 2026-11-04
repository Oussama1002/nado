@extends('layouts.client')

@section('client_title', __('messages.cannonce.edit_title'))
@section('client_subtitle', __('messages.cannonce.edit_subtitle'))
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

    <h1 class="mb-4">Modifier une Annonce</h1>

    <form action="{{ route('client.annonces.update', $annonce->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        {{-- SECTION 1 : Informations générales --}}
        <h4 class="mt-4">{{ __('messages.cannonce.sec_general') }}</h4>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label>{{ __('messages.cannonce.title') }}</label>
                <input type="text" name="titre" class="form-control"
                       value="{{ old('titre', $annonce->titre) }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label>{{ __('messages.cannonce.price') }}</label>
                <input type="number" name="prix" class="form-control"
                       value="{{ old('prix', $annonce->prix) }}" required>
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
                        <option value="{{ $brand->id }}"
                            {{ old('mark', $annonce->mark) == $brand->id ? 'selected' : '' }}>
                            {{ $brand->name }}
                        </option>
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
    <label for="color">{{ __('messages.cannonce.color') }}</label>
    <select name="colors" id="color" class="form-control" required>
        <option value="">{{ __('messages.cannonce.select_color') }}</option>
        @foreach($colors as $color)
            <option value="{{ $color->name }}" 
                {{ old('colors', $annonce->colors) == $color->name ? 'selected' : '' }}>
                {{ $color->name }}
            </option>
        @endforeach
    </select>
</div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.km') }}</label>
                <input type="number" name="km" class="form-control"
                       value="{{ old('km', $annonce->km) }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.cv') }}</label>
                <input type="number" name="cv" class="form-control"
                       value="{{ old('cv', $annonce->cv) }}" required>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.doors') }}</label>
                <select name="nb_Door" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="3" {{ old('nb_Door', $annonce->nb_Door) == 3 ? 'selected' : '' }}>{{ __('messages.cannonce.doors_3') }}</option>
                    <option value="5" {{ old('nb_Door', $annonce->nb_Door) == 5 ? 'selected' : '' }}>{{ __('messages.cannonce.doors_5') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.year') }}</label>
                <select name="year" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select_year') }}</option>
                    @for ($y = date('Y'); $y >= 1990; $y--)
                        <option value="{{ $y }}"
                            {{ old('year', $annonce->year) == $y ? 'selected' : '' }}>
                            {{ $y }}
                        </option>
                    @endfor
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.origin') }}</label>
                <select name="origine" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="WW au Maroc" {{ old('origine', $annonce->origine) == "WW au Maroc" ? 'selected' : '' }}>{{ __('messages.ventemodal.origin_ww') }}</option>
                    <option value="dédouané" {{ old('origine', $annonce->origine) == "dédouané" ? 'selected' : '' }}>{{ __('messages.ventemodal.origin_cleared') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.cannonce.first_main') }}</label>
                <select name="premiere_main" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="Oui" {{ old('premiere_main', $annonce->premiere_main) == "Oui" ? 'selected' : '' }}>{{ __('messages.nadoae.yes') }}</option>
                    <option value="Non" {{ old('premiere_main', $annonce->premiere_main) == "Non" ? 'selected' : '' }}>{{ __('messages.nadoae.no') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.state') }}</label>
                <select name="etat" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="Neuf" {{ old('etat', $annonce->etat) == "Neuf" ? 'selected' : '' }}>{{ __('messages.ventemodal.state_new') }}</option>
                    <option value="Très bon état" {{ old('etat', $annonce->etat) == "Très bon état" ? 'selected' : '' }}>{{ __('messages.cannonce.state_tres_bon') }}</option>
                    <option value="Bon état" {{ old('etat', $annonce->etat) == "Bon état" ? 'selected' : '' }}>{{ __('messages.cannonce.state_bon') }}</option>
                    <option value="Accidenté" {{ old('etat', $annonce->etat) == "Accidenté" ? 'selected' : '' }}>{{ __('messages.cannonce.state_accidente') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.fuel') }}</label>
                <select name="carburant" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="diesel" {{ old('carburant', $annonce->carburant) == "diesel" ? 'selected' : '' }}>{{ __('messages.ventemodal.fuel_diesel') }}</option>
                    <option value="essence" {{ old('carburant', $annonce->carburant) == "essence" ? 'selected' : '' }}>{{ __('messages.ventemodal.fuel_essence') }}</option>
                    <option value="hybride" {{ old('carburant', $annonce->carburant) == "hybride" ? 'selected' : '' }}>{{ __('messages.ventemodal.fuel_hybrid') }}</option>
                    <option value="electrique" {{ old('carburant', $annonce->carburant) == "electrique" ? 'selected' : '' }}>{{ __('messages.ventemodal.fuel_electric') }}</option>
                </select>
            </div>

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.box') }}</label>
                <select name="box" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select') }}</option>
                    <option value="automatique" {{ old('box', $annonce->box) == "automatique" ? 'selected' : '' }}>{{ __('messages.ventemodal.box_auto') }}</option>
                    <option value="manuelle" {{ old('box', $annonce->box) == "manuelle" ? 'selected' : '' }}>{{ __('messages.ventemodal.box_manual') }}</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
            

            <div class="col-md-4 mb-3">
                <label>{{ __('messages.ventemodal.city') }}</label>
                <select name="city" class="form-control" required>
                    <option value="">{{ __('messages.cannonce.select_city') }}</option>
                    @foreach ($villes as $ville)
                        <option value="{{ $ville->name }}"
                            {{ old('city', $annonce->city) == $ville->name ? 'selected' : '' }}>
                            {{ $ville->name }}
                        </option>
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
           // Reverse map from the French equipment label to its key, so each
           // option is translated regardless of this form's own field names.
           $equipFr  = trans('messages.equip', [], 'fr');
           $equipRev = is_array($equipFr) ? array_flip($equipFr) : [];
           $options = [
            // Sécurité
    'airbags' => 'Airbags',
    'abs' => 'ABS',
    'esp' => 'ESP',
    'antipatinage' => 'Antipatinage',
    'aide_freinage_urgence' => "Aide au freinage d'urgence",
    'antidemarrage' => "Antidémarrage électronique",
    'aide_demarrage_cote' => "Aide au démarrage en côte",
    'selecteur_mode_conduite' => "Sélecteur de mode de conduite",
    'detection_fatigue' => "Détection de fatigue",
    'maintien_voie' => "Maintien dans la voie",
    'detecteur_angle_mort' => "Détecteur d'angle mort",
    'detecteur_sous_gonflage' => "Détecteur de sous-gonflage",
    'fermeture_auto' => "Fermeture de portes auto.",
    'isofix' => "Préparation ISOFIX",
    'phares_antibrouillard' => "Phares antibrouillard",
    'alarme' => "Système d’alarme",

    // Confort
    'climatisation' => 'Climatisation',
    'start_stop' => 'Start & Stop',
    'regulateur_vitesse' => 'Régulateur de vitesse',
    'detecteur_pluie' => 'Détecteur de pluie',
    'allumage_auto' => 'Allumage auto. des feux',
    'frein_main_elec' => 'Frein à main électrique',
    'aide_stationnement' => 'Aide au stationnement',
    'volant_reglable' => 'Volant réglable',
    'vitres_elec' => 'Vitres électriques',
    'retros_elec' => 'Rétros. électriques',
    'retros_rab_elec' => 'Rétros. rabattables électriques',
    'coffre_elec' => 'Coffre électrique',
    'sieges_elec' => 'Sièges électriques',
    'sieges_mem' => 'Sièges élec. avec mémoire',
    'banquette_rab' => 'Banquette arrière rabattable 1/3-2/3',

    // Multimédia
    'ecran_tactile' => 'Écran tactile',
    'camera_recul' => 'Caméra de recul',
    'park_assist' => 'Park Assist Auto.',
    'cockpit_digital' => 'Cockpit digital',
    'cmd_volant' => 'Commandes au volant',
    'cmd_vocale' => 'Commandes vocales',
    'reco_panneaux' => 'Reconnaissance de panneaux',
    'affichage_tete_haute' => 'Affichage Tête-Haute',
    'systeme_audio' => 'Système audio',
    'ordinateur_bord' => 'Ordinateur de bord',
    'gps' => 'Navigation GPS',
    'wifi' => 'WiFi à bord',
    'bluetooth' => 'Bluetooth',
    'smartphone' => 'Compatibilité smartphone',
    'carplay_android' => 'Apple CarPlay® & Android Auto®',
    'chargeur_sans_fil' => 'Chargeur/mobile sans fil',

    // Design
    'jantes_aluminium' => 'Jantes aluminium',
    'sellerie' => 'Sellerie Similicuir / Tissu',
    'volant_cuir' => 'Volant cuir',
    'follow_me_home' => 'Follow-me home',
    'lumiere_ambiance' => "Lumière d'ambiance",
    'feux_jour_led' => 'Feux de jour LED',
    'phares_led' => 'Phares Full LED',
    'toit_panoramique' => 'Toit Panoramique ouvrant',
    'barres_toit' => 'Barres de toit',
    'vitres_teintees' => 'Vitres sur-teintées',
        ];
    @endphp

    @foreach ($options as $field => $label)
        <div class="col-md-3 mb-2 form-check">
            <input type="checkbox"
                   class="form-check-input option-checkbox"
                   id="{{ $field }}"
                   name="{{ $field }}"
                   value="1"
                   {{ old($field, $annonce->$field) ? 'checked' : '' }}>
            <label class="form-check-label" for="{{ $field }}">{{ isset($equipRev[$label]) ? __('messages.equip.'.$equipRev[$label]) : $label }}</label>
        </div>
    @endforeach
</div>

       <div class="form-group mt-4">
    <label for="images">{{ __('messages.cannonce.images') }}</label>
    <div id="image-uploader" class="dropzone"></div>

   <div class="mt-3 d-flex flex-wrap gap-2">
    @foreach (['image','imgrleft','imgrtop','imgrmed','imgrbottom','imgrright','imginterior','imgrapport'] as $col)
        @if(!empty($annonce->$col))
            <div class="position-relative">
                <img src="{{ asset('storage/annonces/'.$annonce->$col) }}" width="120" class="rounded border">
                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 delete-image"
                        data-field="{{ $col }}">X</button>
            </div>
        @endif
    @endforeach
</div>

</div>


{{-- Modal packs --}}
<div class="modal fade" id="packModal" tabindex="-1">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header"><h5 class="modal-title">{{ __('messages.cannonce.choose_pack') }}</h5></div>
      <div class="modal-body row">
        @foreach($packs as $pack)
          <div class="col-md-4">
            <div class="card p-3 {{ $annonce->pack_id == $pack->id ? 'border-success' : '' }}">
              <h5>{{ $pack->name }}</h5>
              <p>{{ $pack->description }}</p>
              <button type="button" class="btn btn-outline-primary select-pack"
                      data-id="{{ $pack->id }}">{{ __('messages.cannonce.choose') }}</button>
            </div>
          </div>
        @endforeach
      </div>
    </div>
  </div>
</div>

        <div class="mt-4">
            <button type="submit" class="btn btn-success">{{ __('messages.cannonce.update_btn') }}</button>
            <a href="{{ route('client.annonces.index') }}" class="btn btn-secondary">{{ __('messages.cannonce.cancel') }}</a>
        </div>
    </form>
</div>
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

        modelSelect.innerHTML = '<option value="">' + @json(__('messages.cannonce.loading')) + '</option>';

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
{{-- garde tes scripts JS identiques à create --}}
@endsection
