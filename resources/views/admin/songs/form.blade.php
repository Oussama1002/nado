@extends('admin.layouts.app')

@section('title' , $song->exists ? 'Modifier un titre' : 'Nouveau titre')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Nado Radio</h3>
</div>
<div class="page-content">
    <section class="row">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">{{ $song->exists ? 'Modifier le titre' : 'Nouveau titre' }}</h4>
                </div>
                <div class="card-content">
                    <div class="card-body row">
                        <div class="col-md-6 col-12 offset-md-3">
                            <form action="{{ route($song->exists ? 'admin.songs.update' : 'admin.songs.store' , $song) }}" method="post" class="form form-vertical" enctype="multipart/form-data">
                                @csrf
                                @method($song->exists ? 'PUT' : 'POST')
                                <div class="form-body">
                                    <div class="row">
                                        <div class="col-12 my-2">
                                            @include('admin.partials.input', ["class" => 'col', "label" => "Titre" ,"name" => 'title', "value" => $song->title])
                                        </div>
                                        <div class="col-12 my-2">
                                            @include('admin.partials.input', ["class" => 'col', "label" => "Artiste" ,"name" => 'artist', "value" => $song->artist])
                                        </div>
                                        <div class="col-12 my-2">
                                            @include('admin.partials.input', ["class" => 'col', "label" => "Durée (ex. 3:45)" ,"name" => 'duration', "value" => $song->duration])
                                        </div>
                                        <div class="col-12 my-2">
                                            @include('admin.partials.input', ["class" => 'col', "label" => "Ordre d'affichage" ,"name" => 'sort_order', "type" => 'number', "value" => $song->sort_order ?? 0])
                                        </div>

                                        <div class="col-12 my-2">
                                            <div class="form-group">
                                                <label for="file">Fichier audio (MP3, OGG, WAV, AAC, M4A — 30 Mo max)</label>
                                                <input type="file" name="file" id="file" accept="audio/*,.mp3,.ogg,.wav,.aac,.m4a" class="form-control round @error('file') is-invalid @enderror">
                                                @error('file')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @if($song->exists && $song->file_path)
                                                    <audio controls preload="none" class="mt-2" style="width:100%;">
                                                        <source src="{{ asset('storage/'.$song->file_path) }}">
                                                    </audio>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 my-2">
                                            <div class="form-group">
                                                <label for="cover">Pochette (optionnelle — JPG, PNG, WEBP — 5 Mo max)</label>
                                                <input type="file" name="cover" id="cover" accept=".jpg,.jpeg,.png,.webp" class="form-control round @error('cover') is-invalid @enderror">
                                                @error('cover')
                                                    <div class="invalid-feedback">{{ $message }}</div>
                                                @enderror
                                                @if($song->exists && $song->cover_path)
                                                    <img src="{{ asset('storage/'.$song->cover_path) }}" width="30%" class="mt-2" style="border-radius:8px;">
                                                @endif
                                            </div>
                                        </div>

                                        <div class="col-12 my-2">
                                            <div class="form-check form-switch fs-6">
                                                <input class="form-check-input me-0" type="checkbox" name="active" id="active" value="1" @if(!$song->exists || $song->active) checked @endif>
                                                <label class="form-check-label" for="active">Actif (diffusé sur la page Nado Radio)</label>
                                            </div>
                                        </div>

                                        <div class="col-12 d-flex justify-content-center">
                                            <button type="submit" class="btn btn-primary me-1 mb-1">
                                                {{ $song->exists ? 'Mettre à jour' : 'Enregistrer' }}
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
</div>
@endsection
