@extends('admin.layouts.app')

@php
    $editing   = isset($draft) && $draft;
    $formAction = $editing
        ? route('admin.whatsapp.templates.drafts.update', $draft)
        : route('admin.whatsapp.templates.store');
@endphp

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0">
        <i class="bi bi-{{ $editing ? 'pencil-square' : 'plus-circle' }} text-success"></i>
        {{ $editing ? 'Modifier le brouillon' : 'Nouveau template WhatsApp' }}
    </h3>
    <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Liste des templates
    </a>
</div>

@if($errors->any())
    <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="page-content">
<div class="card">
<div class="card-body">

<div class="alert alert-info small">
    <i class="bi bi-info-circle"></i>
    Le template est d'abord enregistré en <strong>BROUILLON</strong> local. Il n'est pas envoyé à Meta
    avant que vous ne cliquiez sur <strong>« Soumettre à Meta »</strong> depuis la liste des templates.
    Cela vous permet de préparer plusieurs modèles tranquillement puis de les soumettre un par un.
    Pour des variables dans le corps, utilisez <code>&#123;&#123;1&#125;&#125;</code>, <code>&#123;&#123;2&#125;&#125;</code>…
</div>

<form method="POST" action="{{ $formAction }}">
    @csrf
    @if($editing) @method('PUT') @endif

    <div class="mb-3">
        <label class="form-label fw-semibold">Nom (slug) <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" required
               value="{{ old('name', $editing ? $draft->name : '') }}"
               pattern="[a-z0-9_]+"
               placeholder="ex. order_confirmation">
        <div class="form-text">Minuscules, chiffres et underscores uniquement. Ce nom sera utilisé dans l'API.</div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
            <select name="category" class="form-select" required id="tplCategory">
                @foreach($categories as $c)
                    <option value="{{ $c }}" @selected(old('category', $editing ? $draft->category : null) === $c)>{{ $c }}</option>
                @endforeach
            </select>
            <div class="form-text">
                <strong>AUTHENTICATION</strong> : OTP / codes (corps généré par Meta).
                <strong>UTILITY</strong> : confirmations, rappels, mises à jour.
                <strong>MARKETING</strong> : promotions, offres (nécessite opt-in).
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Langue <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="language" required
                   value="{{ old('language', $editing ? $draft->language : 'fr') }}" placeholder="fr">
            <div class="form-text">Code BCP-47 (ex. <code>fr</code>, <code>ar</code>, <code>en_US</code>).</div>
        </div>
    </div>

    {{-- Bloc corps/footer : masqué pour AUTHENTICATION (corps fixe généré par Meta). --}}
    <div id="tplBodyBlock">
        <div class="mb-3 mt-3">
            <label class="form-label fw-semibold">Corps du message <span class="text-danger">*</span></label>
            <textarea name="body" class="form-control" rows="5" maxlength="1024">{{ old('body', $editing ? $draft->body : '') }}</textarea>
            <div class="form-text">
                Jusqu'à 1024 caractères. Exemple :
                « Bonjour &#123;&#123;1&#125;&#125;, votre annonce « &#123;&#123;2&#125;&#125; » a été approuvée. »
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-semibold">Pied de page (optionnel)</label>
            <input type="text" class="form-control" name="footer" maxlength="60"
                   value="{{ old('footer', $editing ? $draft->footer : '') }}"
                   placeholder="ex. Équipe NADO">
            <div class="form-text">Max 60 caractères, sans variables.</div>
        </div>
    </div>

    <div class="alert alert-info mt-3" id="tplAuthInfo" style="display:none;">
        <i class="bi bi-info-circle"></i>
        <strong>Catégorie AUTHENTICATION :</strong> Meta génère automatiquement le corps du message
        (« <em>{&#123;1&#125;} est votre code de vérification</em> »), ajoute une recommandation de sécurité,
        un bouton <strong>« Copier le code »</strong> et une expiration de 10 minutes.
        Vous ne pouvez pas modifier le texte.
    </div>

    <script>
        (function () {
            var sel = document.getElementById('tplCategory');
            var body = document.getElementById('tplBodyBlock');
            var info = document.getElementById('tplAuthInfo');
            var textarea = body.querySelector('textarea[name="body"]');

            function sync() {
                var isAuth = sel.value === 'AUTHENTICATION';
                body.style.display = isAuth ? 'none' : '';
                info.style.display = isAuth ? '' : 'none';
                if (textarea) textarea.required = !isAuth;
            }
            sel.addEventListener('change', sync);
            sync();
        })();
    </script>

    <div class="pt-2">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-save"></i> {{ $editing ? 'Enregistrer les modifications' : 'Enregistrer comme brouillon' }}
        </button>
        <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>

</div>
</div>
</div>
@endsection
