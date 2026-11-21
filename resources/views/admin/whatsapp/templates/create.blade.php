@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi bi-plus-circle text-success"></i> Nouveau template WhatsApp</h3>
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
    Les templates sont soumis à Meta pour approbation (généralement sous 1 heure).
    Un template <strong>approuvé</strong> peut être envoyé à n'importe quel numéro à tout moment ;
    un template en <strong>PENDING</strong> ou <strong>REJECTED</strong> ne peut pas être utilisé.
    Pour des variables dans le corps, utilisez <code>&#123;&#123;1&#125;&#125;</code>, <code>&#123;&#123;2&#125;&#125;</code>…
</div>

<form method="POST" action="{{ route('admin.whatsapp.templates.store') }}">
    @csrf

    <div class="mb-3">
        <label class="form-label fw-semibold">Nom (slug) <span class="text-danger">*</span></label>
        <input type="text" class="form-control" name="name" required
               value="{{ old('name') }}"
               pattern="[a-z0-9_]+"
               placeholder="ex. order_confirmation">
        <div class="form-text">Minuscules, chiffres et underscores uniquement. Ce nom sera utilisé dans l'API.</div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label fw-semibold">Catégorie <span class="text-danger">*</span></label>
            <select name="category" class="form-select" required>
                @foreach($categories as $c)
                    <option value="{{ $c }}" @selected(old('category') === $c)>{{ $c }}</option>
                @endforeach
            </select>
            <div class="form-text">
                <strong>AUTHENTICATION</strong> : OTP / codes.
                <strong>UTILITY</strong> : confirmations, rappels, mises à jour liées à une action du client.
                <strong>MARKETING</strong> : promotions, offres (nécessite opt-in).
            </div>
        </div>
        <div class="col-md-6">
            <label class="form-label fw-semibold">Langue <span class="text-danger">*</span></label>
            <input type="text" class="form-control" name="language" required
                   value="{{ old('language', 'fr') }}" placeholder="fr">
            <div class="form-text">Code BCP-47 (ex. <code>fr</code>, <code>ar</code>, <code>en_US</code>).</div>
        </div>
    </div>

    <div class="mb-3 mt-3">
        <label class="form-label fw-semibold">Corps du message <span class="text-danger">*</span></label>
        <textarea name="body" class="form-control" rows="5" maxlength="1024" required>{{ old('body') }}</textarea>
        <div class="form-text">
            Jusqu'à 1024 caractères. Exemple :
            « Bonjour &#123;&#123;1&#125;&#125;, votre annonce « &#123;&#123;2&#125;&#125; » a été approuvée. »
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label fw-semibold">Pied de page (optionnel)</label>
        <input type="text" class="form-control" name="footer" maxlength="60" value="{{ old('footer') }}"
               placeholder="ex. Équipe NADO">
        <div class="form-text">Max 60 caractères, sans variables.</div>
    </div>

    <div class="pt-2">
        <button type="submit" class="btn btn-success">
            <i class="bi bi-send"></i> Soumettre à Meta
        </button>
        <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary">Annuler</a>
    </div>
</form>

</div>
</div>
</div>
@endsection
