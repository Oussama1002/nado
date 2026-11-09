@extends('admin.layouts.app')

@section('content')
<div class="page-heading">
    <h3>Modifier la Story</h3>
</div>

<section class="section">
    <div class="card">
        <div class="card-body">
            <form action="{{ route('admin.stories.update', $story->id) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="media">Remplacer le média (Image) — laisser vide pour garder l'actuel</label>
                            <input type="file" name="media" id="media"
                                   class="form-control @error('media') is-invalid @enderror">
                            @error('media')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror

                            @if ($story->media_path)
                                <div class="mt-2">
                                    @if ($story->media_type === 'video')
                                        <video src="{{ asset('storage/' . $story->media_path) }}" width="120" controls></video>
                                    @else
                                        <img src="{{ asset('storage/' . $story->media_path) }}" alt="Story" width="100">
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-3">
                            <label for="expires_at">Expire le (optionnel)</label>
                            <input type="datetime-local" name="expires_at" id="expires_at" class="form-control"
                                   value="{{ old('expires_at', $story->expires_at ? \Illuminate\Support\Carbon::parse($story->expires_at)->format('Y-m-d\TH:i') : '') }}">
                            <small class="text-muted">Laisser vide : la story reste affichée jusqu'à sa suppression.</small>
                        </div>

                        <div class="form-group mb-3">
                            <label for="pin_position">Épingler en position</label>
                            <select name="pin_position" id="pin_position" class="form-select">
                                <option value="" {{ is_null($story->pin_position) ? 'selected' : '' }}>Non épinglée</option>
                                <option value="1" {{ (int) $story->pin_position === 1 ? 'selected' : '' }}>Position 1</option>
                                <option value="2" {{ (int) $story->pin_position === 2 ? 'selected' : '' }}>Position 2</option>
                                <option value="3" {{ (int) $story->pin_position === 3 ? 'selected' : '' }}>Position 3</option>
                            </select>
                            <small class="text-muted">Chaque position (1, 2, 3) est unique : l'assigner ici la libère sur l'autre story.</small>
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group mb-3">
                            <label for="instagram_link">Lien Instagram (optionnel)</label>
                            <input type="url" name="instagram_link" id="instagram_link"
                                   class="form-control @error('instagram_link') is-invalid @enderror"
                                   value="{{ old('instagram_link', $story->instagram_link) }}"
                                   placeholder="https://www.instagram.com/p/…">
                            @error('instagram_link')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="mt-3 d-flex gap-2">
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                    <a href="{{ route('admin.stories.index') }}" class="btn btn-secondary">Annuler</a>
                </div>
            </form>
        </div>
    </div>
</section>
@endsection
