@extends('admin.layouts.app')

@section('content')
<div class="page-heading">
    <h3>⚙️ Configuration du marketplace</h3>
    <p class="text-muted">Tous les prix, taux, durées et modules sont pilotés ici — aucune valeur n'est en dur dans le code.</p>
</div>

@if(session('success'))
    <div class="alert alert-success">{{ session('success') }}</div>
@endif

<div class="page-content">
    <div class="row">
        @foreach($groups as $key => $meta)
            @php $total = $counts[$key] ?? 0; @endphp
            <div class="col-12 col-md-6 col-lg-4 mb-3">
                <a href="{{ $total ? route('admin.settings.group', $key) : '#' }}"
                   class="text-decoration-none {{ $total ? '' : 'pe-none opacity-50' }}">
                    <div class="card h-100">
                        <div class="card-body d-flex align-items-center gap-3">
                            <div class="fs-2"><i class="bi {{ $meta['icon'] }}"></i></div>
                            <div>
                                <h5 class="card-title mb-1">{{ $meta['label'] }}</h5>
                                <small class="text-muted">
                                    {{ $total ? $total . ' réglage(s)' : 'Non configuré' }}
                                </small>
                            </div>
                        </div>
                    </div>
                </a>
            </div>
        @endforeach
    </div>
</div>
@endsection
