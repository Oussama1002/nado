@extends('layouts.client')

@section('title', 'Modifier ma demande')

@section('client_title', 'Modifier ma demande')
@section('client_subtitle', 'Mettez à jour les critères de votre recherche.')
@section('client_actions')
    <a href="{{ route('client.mes-demandes') }}" class="btn btn-secondary">← Retour</a>
@endsection

@section('client_content')
<style>
    .dmf-grid { display:grid; grid-template-columns:repeat(2,1fr); gap:14px; }
    @media (max-width:640px){ .dmf-grid{ grid-template-columns:1fr; } }
    .dmf-field label { display:block; font-size:13px; font-weight:700; margin-bottom:6px; color:#0f172a; }
    .dmf-field input, .dmf-field select, .dmf-field textarea {
        width:100%; padding:10px 12px; border:1px solid #d1d5db; border-radius:10px; font-size:14px; background:#fff;
    }
    .dmf-full { grid-column:1 / -1; }
    .dmf-actions { display:flex; gap:10px; margin-top:18px; flex-wrap:wrap; }
    .dmf-save { background:#1459DD; color:#fff; border:0; border-radius:999px; font-weight:800; padding:10px 22px; cursor:pointer; }
    .dmf-save:hover { background:#0f47b3; }
</style>

@if ($errors->any())
    <div style="background:#fef2f2;color:#b91c1c;border:1px solid #fecaca;border-radius:10px;padding:14px;margin-bottom:16px;">
        <ul style="margin:0;padding-inline-start:18px;">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="POST" action="{{ route('client.demandes.update', $demande->id) }}">
    @csrf
    @method('PUT')

    <div class="dmf-grid">
        <div class="dmf-field">
            <label for="nom">Nom</label>
            <input type="text" id="nom" name="nom" value="{{ old('nom', $demande->nom) }}" required>
        </div>
        <div class="dmf-field">
            <label for="telephone">Téléphone</label>
            <input type="tel" id="telephone" name="telephone" value="{{ old('telephone', $demande->telephone) }}" required>
        </div>

        <div class="dmf-field dmf-full">
            <label for="email">Email</label>
            <input type="email" id="email" name="email" value="{{ old('email', $demande->email) }}">
        </div>

        <div class="dmf-field">
            <label for="brand_id">Marque</label>
            <select id="brand_id" name="brand_id" data-models-url="{{ url('/achat/models-by-brand') }}">
                <option value="">— Toutes —</option>
                @foreach($brands as $b)
                    <option value="{{ $b->id }}" @selected(old('brand_id', $demande->brand_id) == $b->id)>{{ $b->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="dmf-field">
            <label for="modele_id">Modèle</label>
            <select id="modele_id" name="modele_id" data-selected="{{ old('modele_id', $demande->modele_id) }}">
                <option value="">— Tous —</option>
                @foreach($modeles as $m)
                    <option value="{{ $m->id }}" @selected(old('modele_id', $demande->modele_id) == $m->id)>{{ $m->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="dmf-field">
            <label for="ville_id">Ville</label>
            <select id="ville_id" name="ville_id">
                <option value="">— Toutes —</option>
                @foreach($villes as $v)
                    <option value="{{ $v->id }}" @selected(old('ville_id', $demande->ville_id) == $v->id)>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="dmf-field">
            <label for="color_id">Couleur</label>
            <select id="color_id" name="color_id">
                <option value="">— Toutes —</option>
                @foreach($colors as $c)
                    <option value="{{ $c->id }}" @selected(old('color_id', $demande->color_id) == $c->id)>{{ $c->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="dmf-field">
            <label for="year_min">Année min.</label>
            <input type="number" id="year_min" name="year_min" value="{{ old('year_min', $demande->year_min) }}" min="1980" max="{{ date('Y') + 1 }}">
        </div>
        <div class="dmf-field">
            <label for="year_max">Année max.</label>
            <input type="number" id="year_max" name="year_max" value="{{ old('year_max', $demande->year_max) }}" min="1980" max="{{ date('Y') + 1 }}">
        </div>

        <div class="dmf-field">
            <label for="price_min">Prix min. (DH)</label>
            <input type="number" id="price_min" name="price_min" value="{{ old('price_min', $demande->price_min) }}" min="0">
        </div>
        <div class="dmf-field">
            <label for="price_max">Prix max. (DH)</label>
            <input type="number" id="price_max" name="price_max" value="{{ old('price_max', $demande->price_max) }}" min="0">
        </div>

        <div class="dmf-field">
            <label for="km_max">Kilométrage max.</label>
            <input type="number" id="km_max" name="km_max" value="{{ old('km_max', $demande->km_max) }}" min="0">
        </div>
        <div class="dmf-field">
            <label for="boite">Boîte</label>
            <select id="boite" name="boite">
                @foreach(['any' => 'Indifférent', 'auto' => 'Automatique', 'manuelle' => 'Manuelle'] as $val => $lbl)
                    <option value="{{ $val }}" @selected(old('boite', $demande->boite ?: 'any') === $val)>{{ $lbl }}</option>
                @endforeach
            </select>
        </div>

        <div class="dmf-field">
            <label for="carburant">Carburant</label>
            <input type="text" id="carburant" name="carburant" value="{{ old('carburant', $demande->carburant) }}" maxlength="40">
        </div>

        <div class="dmf-field dmf-full">
            <label for="comment">Commentaire</label>
            <textarea id="comment" name="comment" rows="3" maxlength="1000">{{ old('comment', $demande->comment) }}</textarea>
        </div>
    </div>

    <div class="dmf-actions">
        <button type="submit" class="dmf-save">💾 Enregistrer</button>
        <a href="{{ route('client.mes-demandes') }}" class="btn btn-secondary">Annuler</a>
    </div>
</form>

<script>
(function () {
    var brand  = document.getElementById('brand_id');
    var modele = document.getElementById('modele_id');
    if (! brand || ! modele) { return; }
    var baseUrl = brand.getAttribute('data-models-url');

    brand.addEventListener('change', function () {
        var id = brand.value;
        modele.innerHTML = '<option value="">— Tous —</option>';
        if (! id) { return; }
        fetch(baseUrl + '/' + encodeURIComponent(id))
            .then(function (r) { return r.json(); })
            .then(function (models) {
                (models || []).forEach(function (m) {
                    var o = document.createElement('option');
                    o.value = m.id;
                    o.textContent = m.name;
                    modele.appendChild(o);
                });
            })
            .catch(function () {});
    });
})();
</script>
@endsection
