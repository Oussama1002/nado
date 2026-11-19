{{-- NADO-DEMANDE-CREATE-V5 --}}
@extends('layouts.app')

@section('title', __('messages.demande_form.page_title'))

@section('content')
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

<style>
    .dmd-form-wrap { max-width:560px; margin:50px auto 70px; padding:0 20px; font-family:'Inter',sans-serif; color:#0f172a; }
    .dmd-form-wrap h1 { font-size:26px; font-weight:800; margin:0 0 6px; letter-spacing:-0.02em; }
    .dmd-form-wrap .intro { color:#64748b; font-size:14px; margin:0 0 24px; line-height:1.5; }

    .dmd-form {
        background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:28px;
        display:grid; gap:16px; box-shadow:0 4px 16px -4px rgba(15,23,42,.06);
    }
    .dmd-form label { display:block; font-size:13px; font-weight:600; color:#334155; margin-bottom:6px; }
    .dmd-form input,
    .dmd-form select {
        width:100%; padding:12px 14px; border:1px solid #e2e8f0; border-radius:10px;
        font-size:15px; outline:none; background:#fff; font-family:inherit; color:#0f172a;
    }
    .dmd-form input:focus,
    .dmd-form select:focus { border-color:#1459DD; box-shadow:0 0 0 3px rgba(20,89,221,.1); }
    .dmd-form select {
        appearance:none; -webkit-appearance:none;
        background-image:url("data:image/svg+xml;utf8,<svg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'><polyline points='6 9 12 15 18 9'/></svg>");
        background-repeat:no-repeat; background-position:right 14px center; padding-right:36px;
    }

    .dmd-cars { display:grid; gap:14px; }
    .dmd-car-row {
        display:grid; grid-template-columns:1fr; gap:12px; align-items:stretch;
        padding:16px 14px; background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px;
        position:relative;
    }
    .dmd-car-row label { margin-bottom:4px; font-size:11px; text-transform:uppercase; letter-spacing:.06em; color:#475569; }
    .dmd-car-remove {
        position:absolute; top:10px; right:10px; width:30px; height:30px; line-height:1;
        padding:0; background:#fff; color:#dc2626; border:1px solid #fecaca;
        border-radius:8px; cursor:pointer; font-weight:700; font-size:16px;
        display:flex; align-items:center; justify-content:center;
    }
    .dmd-car-remove:hover { background:#fef2f2; }

    .dmd-add-car {
        padding:12px 14px; background:#eff6ff; color:#1459DD; border:1px dashed #93c5fd;
        border-radius:10px; cursor:pointer; font-weight:700; font-family:inherit; font-size:14px;
        display:flex; align-items:center; justify-content:center; gap:6px;
    }
    .dmd-add-car:hover { background:#dbeafe; }

    .dmd-btn {
        padding:14px 20px; background:#1459DD; color:#fff; border:0; border-radius:12px;
        font-weight:700; cursor:pointer; font-family:inherit; font-size:15px;
        box-shadow:0 6px 18px rgba(20,89,221,.25);
        transition:transform .15s, box-shadow .15s;
    }
    .dmd-btn:hover { transform:translateY(-1px); box-shadow:0 10px 24px rgba(20,89,221,.35); }

    .dmd-errors {
        background:#fef2f2; color:#991b1b; padding:12px 14px; border-radius:10px;
        border:1px solid #fecaca; font-size:13px;
    }
    .dmd-errors ul { margin:0; padding-left:18px; }

    .dmd-note { font-size:12px; color:#64748b; text-align:center; }

    /* Brand logo picker (replaces the Marque <select>) */
    .dmd-brand-grid {
        display:grid; grid-template-columns:repeat(auto-fill, minmax(72px, 1fr));
        gap:8px; margin-top:4px; max-height:280px; overflow-y:auto;
        padding:8px; background:#fff; border:1px solid #e2e8f0; border-radius:10px;
    }
    .dmd-brand-tile {
        display:flex; flex-direction:column; align-items:center; justify-content:center;
        gap:4px; padding:8px 4px; background:#fff; border:1px solid #e2e8f0;
        border-radius:8px; cursor:pointer; transition:border-color .15s, box-shadow .15s, transform .15s;
    }
    .dmd-brand-tile:hover { border-color:#1459DD; box-shadow:0 2px 8px rgba(20,89,221,.1); transform:translateY(-1px); }
    .dmd-brand-tile img { width:40px; height:40px; object-fit:contain; }
    .dmd-brand-tile .nolg { width:40px; height:40px; display:flex; align-items:center; justify-content:center; font-weight:800; color:#64748b; background:#f1f5f9; border-radius:6px; font-size:14px; }
    .dmd-brand-tile .bn { font-size:10px; font-weight:700; color:#334155; text-align:center; line-height:1.1; max-width:100%; overflow:hidden; text-overflow:ellipsis; white-space:nowrap; }
    .dmd-brand-search {
        width:100%; padding:10px 12px; border:1px solid #e2e8f0; border-radius:8px;
        font-size:14px; outline:none; margin-bottom:6px;
    }
    .dmd-brand-search:focus { border-color:#1459DD; box-shadow:0 0 0 3px rgba(20,89,221,.1); }
    .dmd-brand-chosen {
        display:flex; align-items:center; gap:10px; padding:10px 12px;
        background:#eff6ff; border:1px solid #bfdbfe; border-radius:10px;
    }
    .dmd-brand-chosen img { width:34px; height:34px; object-fit:contain; }
    .dmd-brand-chosen .nolg { width:34px; height:34px; display:flex; align-items:center; justify-content:center; font-weight:800; color:#64748b; background:#fff; border-radius:6px; }
    .dmd-brand-chosen .name { font-weight:700; font-size:14px; color:#0f172a; flex:1; }
    .dmd-brand-change {
        background:#fff; color:#1459DD; border:1px solid #bfdbfe; border-radius:8px;
        padding:6px 10px; font-weight:700; cursor:pointer; font-family:inherit; font-size:12px;
    }
    .dmd-brand-change:hover { background:#dbeafe; }
    .dmd-modele-block[hidden] { display:none; }
</style>

<div class="dmd-form-wrap">
    <h1>{{ __('messages.demande_form.page_title') }}</h1>
    <p class="intro">
        {{ __('messages.demande_form.intro') }}
    </p>

    @if ($errors->any())
        <div class="dmd-errors">
            <ul>
                @foreach ($errors->all() as $e) <li>{{ $e }}</li> @endforeach
            </ul>
        </div>
    @endif

    <form id="dmdForm" method="POST" action="{{ route('demandes.store') }}" class="dmd-form">
        @csrf

        {{-- Hidden fields filled on submit by the JS below --}}
        <input type="hidden" name="source"    value="{{ $prefill['source']   ?? 'direct' }}">
        <input type="hidden" name="ville_id"  value="{{ $prefill['ville_id'] ?? '' }}">
        <input type="hidden" name="raw_query" value="{{ $prefill['q']        ?? '' }}">
        <input type="hidden" name="brand_id"  id="dmdBrandId"  value="">
        <input type="hidden" name="modele_id" id="dmdModeleId" value="">
        <input type="hidden" name="comment"   id="dmdComment"  value="">

        {{-- Honeypot --}}
        <input type="text" name="website" value="" style="display:none;" tabindex="-1" autocomplete="off">

        <div>
            <label for="dmdNom">{{ __('messages.demande_form.fullname') }}</label>
            <input id="dmdNom" type="text" name="nom" required
                   value="{{ old('nom', auth('client')->user()?->name) }}"
                   placeholder="{{ __('messages.demande_form.fullname_ph') }}">
        </div>

        <div>
            <label for="dmdTel">{{ __('messages.ventemodal.phone') }}</label>
            <input id="dmdTel" type="tel" name="telephone" required
                   value="{{ old('telephone', auth('client')->user()?->phone) }}"
                   placeholder="06XXXXXXXX">
        </div>

        <div>
            <label for="dmdEmail">{{ __('messages.demande_form.email') }}</label>
            <input id="dmdEmail" type="email" name="email"
                   value="{{ old('email', auth('client')->user()?->email) }}"
                   placeholder="{{ __('messages.demande_form.email_ph') }}">
        </div>

        <div>
            <label>{{ __('messages.demande_form.cars_label') }}</label>
            <div class="dmd-cars" id="dmdCars">
                {{-- initial row rendered by JS so brand + model stay in sync --}}
            </div>

            <button type="button" class="dmd-add-car" id="dmdAddCar" style="margin-top:12px;">
                {{ __('messages.demande_form.add_car') }}
            </button>
        </div>

        <button type="submit" class="dmd-btn">{{ __('messages.demande_form.submit') }}</button>

        <p class="dmd-note">{{ __('messages.demande_form.note') }}</p>
    </form>
</div>

@include('partials.token-cost-confirm', [
    'formId'  => 'dmdForm',
    'costKey' => 'demande',
    'service' => __('messages.tokens.reason.demande'),
])

<script>
    (function () {
        var DMD_I18N = {
            chooseBrand: @json(__('messages.demande_form.choose_brand')),
            chooseModel: @json(__('messages.demande_form.choose_model')),
            marque:      @json(__('messages.ventemodal.marque')),
            modele:      @json(__('messages.ventemodal.modele')),
            remove:      @json(__('messages.ventemodal.remove')),
            yearMin:     @json(__('messages.catalog.from')),
            yearMax:     @json(__('messages.catalog.to')),
            yearLabel:   @json(__('messages.demande_form.year_range')),
        };
        // Options d'années (une seule fois), injectées dans la 1re ligne.
        var YEAR_OPTS = '';
        (function(){ for (var y = {{ (int) date('Y') }}; y >= 1980; y--) { YEAR_OPTS += '<option value="'+y+'">'+y+'</option>'; } })();
        var brandsData = @json($brands->map(fn($b) => ['id' => $b->id, 'name' => $b->name, 'logo' => $b->logo ? asset('storage/brands/'.$b->logo) : ''])->values());
        var prefillBrandId  = @json((string)($prefill['brand_id']  ?? ''));
        var prefillModeleId = @json((string)($prefill['modele_id'] ?? ''));

        var container = document.getElementById('dmdCars');
        var addBtn    = document.getElementById('dmdAddCar');
        var form      = document.getElementById('dmdForm');
        var MODELS_URL = "{{ url('/achat/models-by-brand') }}"; // + /{brandId}

        var rowId = 0;

        function brandOptions(selectedId) {
            var html = '<option value="">' + DMD_I18N.chooseBrand + '</option>';
            brandsData.forEach(function (b) {
                html += '<option value="' + b.id + '"' + (String(b.id) === String(selectedId) ? ' selected' : '') + '>' + b.name + '</option>';
            });
            return html;
        }

        function newRow(preBrandId, preModeleId, isFirst) {
            rowId++;
            var id = 'r' + rowId;
            var row = document.createElement('div');
            row.className = 'dmd-car-row';
            row.dataset.rowId = id;
            // Toutes les lignes ont une plage d'années ; seule la 1re est stockée
            // (name=year_min/max) — les autres sont ajoutées au commentaire au submit.
            var nameMin = isFirst ? ' name="year_min"' : '';
            var nameMax = isFirst ? ' name="year_max"' : '';
            var yearHtml =
                  '<div>'
                   + '<label>' + DMD_I18N.yearMin + '</label>'
                   + '<select class="dmd-year-min"' + nameMin + '><option value="">' + DMD_I18N.yearMin + '</option>' + YEAR_OPTS + '</select>'
                 + '</div>'
                 + '<div>'
                   + '<label>' + DMD_I18N.yearMax + '</label>'
                   + '<select class="dmd-year-max"' + nameMax + '><option value="">' + DMD_I18N.yearMax + '</option>' + YEAR_OPTS + '</select>'
                 + '</div>';
            row.innerHTML =
                '<div class="dmd-brand-block">'
                 + '<label>' + DMD_I18N.marque + '</label>'
                 + '<input type="hidden" class="dmd-brand" value="">'
                 + '<div class="dmd-brand-picker">'
                   + '<input type="text" class="dmd-brand-search" placeholder="' + DMD_I18N.chooseBrand + '" autocomplete="off">'
                   + '<div class="dmd-brand-grid"></div>'
                 + '</div>'
                 + '<div class="dmd-brand-chosen" hidden>'
                   + '<span class="dmd-brand-chosen-logo"></span>'
                   + '<span class="name"></span>'
                   + '<button type="button" class="dmd-brand-change">' + DMD_I18N.chooseBrand + '</button>'
                 + '</div>'
                + '</div>'
                + '<div class="dmd-modele-block" hidden>'
                 + '<label>' + DMD_I18N.modele + '</label>'
                 + '<select class="dmd-modele"><option value="">' + DMD_I18N.chooseModel + '</option></select>'
                + '</div>'
                + yearHtml
                + '<button type="button" class="dmd-car-remove" title="' + DMD_I18N.remove + '">×</button>';

            container.appendChild(row);

            var brandInp    = row.querySelector('.dmd-brand');
            var picker      = row.querySelector('.dmd-brand-picker');
            var grid        = row.querySelector('.dmd-brand-grid');
            var search      = row.querySelector('.dmd-brand-search');
            var chosen      = row.querySelector('.dmd-brand-chosen');
            var chosenLogo  = row.querySelector('.dmd-brand-chosen-logo');
            var chosenName  = chosen.querySelector('.name');
            var changeBtn   = row.querySelector('.dmd-brand-change');
            var modeleBlock = row.querySelector('.dmd-modele-block');
            var modeleSel   = row.querySelector('.dmd-modele');
            var removeBtn   = row.querySelector('.dmd-car-remove');

            function logoNode(b) {
                if (b.logo) {
                    var im = document.createElement('img');
                    im.src = b.logo; im.alt = b.name; im.loading = 'lazy';
                    return im;
                }
                var sp = document.createElement('span');
                sp.className = 'nolg';
                sp.textContent = (b.name || '?').charAt(0).toUpperCase();
                return sp;
            }

            function renderGrid(filter) {
                grid.innerHTML = '';
                var q = (filter || '').toLowerCase().trim();
                brandsData.forEach(function (b) {
                    if (q && b.name.toLowerCase().indexOf(q) === -1) return;
                    var t = document.createElement('div');
                    t.className = 'dmd-brand-tile';
                    t.appendChild(logoNode(b));
                    var n = document.createElement('div');
                    n.className = 'bn'; n.textContent = b.name;
                    t.appendChild(n);
                    t.addEventListener('click', function () { pickBrand(b, null); });
                    grid.appendChild(t);
                });
            }

            function pickBrand(b, preselectModeleId) {
                brandInp.value = b.id;
                chosenLogo.innerHTML = '';
                chosenLogo.appendChild(logoNode(b));
                chosenName.textContent = b.name;
                picker.hidden = true;
                chosen.hidden = false;
                modeleBlock.hidden = false;
                loadModels(b.id, modeleSel, preselectModeleId);
            }

            function resetBrand() {
                brandInp.value = '';
                picker.hidden = false;
                chosen.hidden = true;
                modeleBlock.hidden = true;
                modeleSel.innerHTML = '<option value="">' + DMD_I18N.chooseModel + '</option>';
                search.value = '';
                renderGrid('');
            }

            search.addEventListener('input', function () { renderGrid(search.value); });
            changeBtn.addEventListener('click', resetBrand);
            removeBtn.addEventListener('click', function () {
                if (container.children.length > 1) row.remove();
            });

            renderGrid('');

            if (preBrandId) {
                var pre = brandsData.find(function (b) { return String(b.id) === String(preBrandId); });
                if (pre) pickBrand(pre, preModeleId);
            }
            return row;
        }

        function loadModels(brandId, modeleSel, preselectId) {
            modeleSel.innerHTML = '<option value="">' + DMD_I18N.chooseModel + '</option>';
            if (!brandId) return;
            fetch(MODELS_URL + '/' + encodeURIComponent(brandId), { headers: { 'Accept': 'application/json' } })
                .then(function (r) { return r.ok ? r.json() : []; })
                .then(function (list) {
                    (list || []).forEach(function (m) {
                        var opt = document.createElement('option');
                        opt.value = m.id;
                        opt.textContent = m.name;
                        if (preselectId && String(m.id) === String(preselectId)) opt.selected = true;
                        modeleSel.appendChild(opt);
                    });
                })
                .catch(function () { /* non-fatal */ });
        }

        // Start with one row — pre-filled if the user came from the filter page
        newRow(prefillBrandId, prefillModeleId, true);

        addBtn.addEventListener('click', function () { newRow('', '', false); });

        // On submit: first row → brand_id / modele_id. Extra rows → comment field.
        form.addEventListener('submit', function () {
            var rows = container.querySelectorAll('.dmd-car-row');
            var first = rows[0];
            if (first) {
                document.getElementById('dmdBrandId').value  = first.querySelector('.dmd-brand').value  || '';
                document.getElementById('dmdModeleId').value = first.querySelector('.dmd-modele').value || '';
            }
            var extras = [];
            for (var i = 1; i < rows.length; i++) {
                var r = rows[i];
                var bSel = r.querySelector('.dmd-brand');
                var mSel = r.querySelector('.dmd-modele');
                var yMin = r.querySelector('.dmd-year-min');
                var yMax = r.querySelector('.dmd-year-max');
                var bInfo = brandsData.find(function (b) { return String(b.id) === String(bSel.value); });
                var bTxt = bInfo ? bInfo.name : '';
                var mTxt = mSel.options[mSel.selectedIndex]?.text || '';
                if (bSel.value) {
                    var label = bTxt + (mSel.value ? ' ' + mTxt : '');
                    var yr = '';
                    if (yMin && yMin.value && yMax && yMax.value) yr = ' (' + yMin.value + '-' + yMax.value + ')';
                    else if (yMin && yMin.value) yr = ' (≥' + yMin.value + ')';
                    else if (yMax && yMax.value) yr = ' (≤' + yMax.value + ')';
                    extras.push(label + yr);
                }
            }
            if (extras.length) {
                document.getElementById('dmdComment').value = 'Autres voitures souhaitées : ' + extras.join(', ');
            }
        });
    })();
</script>
@endsection
