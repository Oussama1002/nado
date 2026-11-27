@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1>{{ $publication->exists ? 'Modifier' : 'Nouvelle' }} publication</h1>

    <form method="POST" action="{{ $publication->exists ? route('admin.publications.update', $publication) : route('admin.publications.store') }}" enctype="multipart/form-data">
        @csrf
        @if($publication->exists)
            @method('PUT')
        @endif

        <div class="mb-3">
            <label>Titre</label>
            <input type="text" name="title" class="form-control" value="{{ old('title', $publication->title) }}" required>
        </div>

        <div class="mb-3">
            <label>Contenu</label>
            <textarea name="content" class="form-control">{{ old('content', $publication->content) }}</textarea>
        </div>

        <div class="mb-3">
            <label>Image</label>
            <input type="file" name="image" class="form-control">
            @if($publication->image)
                <img src="{{ asset('storage/' . $publication->image) }}" alt="image" style="max-width: 150px; margin-top: 10px;">
            @endif
        </div>

        <div class="mb-500">
            <label>Placement</label>
            <select name="placement" class="form-control" required>
                <option value="center-banner" {{ old('placement', $publication->placement) == 'center-banner' ? 'selected' : '' }}>Bannière Centre(1000x500)</option>
                <option value="first-banner" {{ old('placement', $publication->placement) == 'first-banner' ? 'selected' : '' }}>1er Bannière sur page achat</option>
                <option value="second-banner" {{ old('placement', $publication->placement) == 'second-banner' ? 'selected' : '' }}>2eme Bannière sur page achat</option>
                <option value="third-banner" {{ old('placement', $publication->placement) == 'third-banner' ? 'selected' : '' }}>3eme Bannière sur page achat</option>
                <option value="rightonindexenchere-banner" {{ old('placement', $publication->placement) == 'rightonindexenchere-banner' ? 'selected' : '' }}>Bannière Droite sur page des Encheres (205x805)</option>
                <option value="vente-hero" {{ old('placement', $publication->placement) == 'vente-hero' ? 'selected' : '' }}>Image hero — Vente (/vente/formulaire)</option>
                <option value="ae-expertise" {{ old('placement', $publication->placement) == 'ae-expertise' ? 'selected' : '' }}>Image « Notre expertise » — Auto-Enchère (/nadoautoenchere/prelogin)</option>
                <option value="reprise-hero" {{ old('placement', $publication->placement) == 'reprise-hero' ? 'selected' : '' }}>Image hero — Reprise (/reprises/formulaire)</option>
                <option value="front-estimation" {{ old('placement', $publication->placement) == 'front-estimation' ? 'selected' : '' }}>Image section « Estimation » — Accueil</option>
                <option value="front-expert-banner" {{ old('placement', $publication->placement) == 'front-expert-banner' ? 'selected' : '' }}>Bannière « Expert NADO » — Accueil (visuel codé, image ignorée)</option>


            </select>
        </div>

        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="status" value="1" id="status" {{ old('status', $publication->status) ? 'checked' : '' }}>
            <label class="form-check-label" for="status">
                Activer cette publication
            </label>
        </div>

        <button class="btn btn-success">Enregistrer</button>
    </form>
</div>
@endsection
