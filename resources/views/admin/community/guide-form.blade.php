@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading"><h3>{{ $guide->exists ? 'Éditer' : 'Nouveau' }} guide</h3></div>
    <div class="page-content">
        @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>@endif
        <div class="card"><div class="card-body">
            <form method="POST" action="{{ $guide->exists ? route('admin.community.guides.update', $guide->id) : route('admin.community.guides.store') }}" enctype="multipart/form-data">
                @csrf
                @if($guide->exists) @method('PUT') @endif
                <div class="row g-3">
                    <div class="col-md-6"><label class="form-label">Marque</label>
                        <select name="brand_id" class="form-select"><option value="">—</option>
                            @foreach($brands as $b)<option value="{{ $b->id }}" {{ $guide->brand_id==$b->id?'selected':'' }}>{{ $b->name }}</option>@endforeach
                        </select></div>
                    <div class="col-md-6"><label class="form-label">Modèle</label>
                        <select name="modele_id" class="form-select"><option value="">—</option>
                            @foreach($modeles as $m)<option value="{{ $m->id }}" {{ $guide->modele_id==$m->id?'selected':'' }}>{{ $m->name }}</option>@endforeach
                        </select></div>
                    <div class="col-md-8"><label class="form-label">Titre (FR) *</label><input name="title" class="form-control" value="{{ old('title',$guide->title) }}" required></div>
                    <div class="col-md-4"><label class="form-label">Slug</label><input name="slug" class="form-control" value="{{ old('slug',$guide->slug) }}" placeholder="auto"></div>
                    <div class="col-md-12"><label class="form-label">Titre (AR)</label><input name="title_ar" dir="rtl" class="form-control" value="{{ old('title_ar',$guide->title_ar) }}"></div>
                    <div class="col-md-6"><label class="form-label">Intro (FR)</label><textarea name="intro" rows="3" class="form-control">{{ old('intro',$guide->intro) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Intro (AR)</label><textarea name="intro_ar" dir="rtl" rows="3" class="form-control">{{ old('intro_ar',$guide->intro_ar) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Contenu (FR) — HTML</label><textarea name="body_fr" rows="8" class="form-control">{{ old('body_fr',$guide->body_fr) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Contenu (AR) — HTML</label><textarea name="body_ar" dir="rtl" rows="8" class="form-control">{{ old('body_ar',$guide->body_ar) }}</textarea></div>
                    <div class="col-md-6"><label class="form-label">Image</label><input type="file" name="image" class="form-control" accept="image/*">
                        @if($guide->image)<img src="{{ asset('storage/'.$guide->image) }}" style="height:60px;margin-top:8px;border-radius:8px;">@endif</div>
                    <div class="col-md-6"><label class="form-label">Meta title</label><input name="meta_title" class="form-control" value="{{ old('meta_title',$guide->meta_title) }}">
                        <label class="form-label mt-2">Meta description</label><input name="meta_description" class="form-control" value="{{ old('meta_description',$guide->meta_description) }}"></div>
                    <div class="col-12"><div class="form-check"><input type="checkbox" class="form-check-input" name="is_active" value="1" {{ old('is_active',$guide->is_active ?? true)?'checked':'' }}><label class="form-check-label">Actif</label></div></div>
                </div>
                <button class="btn btn-primary mt-3">Enregistrer</button>
                <a href="{{ route('admin.community.guides.index') }}" class="btn btn-light mt-3">Annuler</a>
            </form>
        </div></div>
    </div>
</div>
@endsection
