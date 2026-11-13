@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading"><h3>{{ $episode->exists ? 'Éditer' : 'Nouvel' }} épisode</h3></div>
    <div class="page-content">
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ $episode->exists ? route('admin.community.episodes.update', $episode->id) : route('admin.community.episodes.store') }}" enctype="multipart/form-data">
                @csrf
                @if($episode->exists) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-8"><label class="form-label">Titre *</label><input name="title" class="form-control" value="{{ old('title',$episode->title) }}" required></div>
                    <div class="col-md-2"><label class="form-label">Durée</label><input name="duration" class="form-control" placeholder="24:10" value="{{ old('duration',$episode->duration) }}"></div>
                    <div class="col-md-2"><label class="form-label">Ordre</label><input type="number" name="sort_order" class="form-control" value="{{ old('sort_order',$episode->sort_order ?? 0) }}"></div>
                    <div class="col-12"><label class="form-label">Description</label><textarea name="description" rows="4" class="form-control">{{ old('description',$episode->description) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Audio (mp3/m4a/…)</label><input type="file" name="audio" class="form-control" accept="audio/*">
                        @if($episode->audio_path)<a href="{{ asset('storage/'.$episode->audio_path) }}" target="_blank" class="small">fichier actuel</a>@endif</div>
                    <div class="col-md-3"><label class="form-label">Pochette</label><input type="file" name="cover" class="form-control" accept="image/*"></div>
                    <div class="col-md-3"><label class="form-label">Publié le</label><input type="datetime-local" name="published_at" class="form-control" value="{{ old('published_at', optional($episode->published_at)->format('Y-m-d\TH:i')) }}"></div>
                    <div class="col-12"><div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active',$episode->is_active ?? true)?'checked':'' }}><label class="form-check-label">Actif</label></div></div>
                </div>
                <button class="btn btn-primary mt-3">Enregistrer</button>
                <a href="{{ route('admin.community.episodes.index') }}" class="btn btn-light mt-3">Annuler</a>
            </form>
        </div></div>
    </div>
</div>
@endsection
