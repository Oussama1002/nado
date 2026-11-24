@extends('admin.layouts.app')

@section('content')
<div class="page-heading">
    <h3><i class="bi bi-image"></i> Images illustratives du site</h3>
    <p class="text-muted mb-0">
        Remplacez les images des sections principales (hero Vente, hero Reprise,
        Notre expertise, Estimation). Formats acceptés : JPG, PNG, WEBP, GIF.
        Taille max : 8 Mo.
    </p>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if($errors->any())
    <div class="alert alert-danger">
        <ul class="mb-0">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div class="page-content">
<div class="row g-3">
    @foreach($slots as $slot => $meta)
        @php
            $path    = $values[$slot] ?? null;
            $hasOwn  = is_string($path) && $path !== '';
            $preview = $hasOwn ? asset('storage/' . $path) : $meta['default'];
        @endphp
        <div class="col-12 col-lg-6">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">{{ $meta['label'] }}</h5>
                    @if($hasOwn)
                        <span class="badge bg-success">Personnalisée</span>
                    @else
                        <span class="badge bg-secondary">Par défaut</span>
                    @endif
                </div>
                <div class="card-body">
                    <p class="text-muted small">{{ $meta['desc'] }}</p>

                    <div class="mb-3 rounded border bg-light d-flex align-items-center justify-content-center"
                         style="aspect-ratio: 16/9; overflow: hidden;">
                        <img src="{{ $preview }}" alt="{{ $meta['label'] }}"
                             style="width:100%;height:100%;object-fit:cover;display:block;"
                             onerror="this.style.display='none'; this.parentNode.innerHTML='<div class=\'text-muted small p-4\'>Aperçu indisponible</div>';">
                    </div>

                    <form method="POST"
                          action="{{ route('admin.site-images.update', $slot) }}"
                          enctype="multipart/form-data"
                          class="d-flex flex-column gap-2">
                        @csrf
                        <input type="hidden" name="previous" value="{{ $path }}">
                        <div class="input-group">
                            <input type="file" name="image" accept="image/jpeg,image/png,image/webp,image/gif"
                                   class="form-control" required>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-upload"></i> Remplacer
                            </button>
                        </div>
                    </form>

                    @if($hasOwn)
                        <form method="POST"
                              action="{{ route('admin.site-images.destroy', $slot) }}"
                              class="mt-2"
                              onsubmit="return confirm('Rétablir l\'image par défaut ?');">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-outline-danger btn-sm">
                                <i class="bi bi-arrow-counterclockwise"></i> Rétablir l'image par défaut
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        </div>
    @endforeach
</div>
</div>
@endsection
