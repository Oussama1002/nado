@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Estimation #{{ $estimation->id }}</h1>
        <a href="{{ route('admin.estimations.index') }}" class="btn btn-light">← Retour</a>
    </div>

    <div class="row g-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-bold">Client</div>
                <div class="card-body">
                    @if($estimation->client)
                        <p class="mb-1"><strong>{{ $estimation->client->name }}</strong></p>
                        <p class="mb-1">{{ $estimation->client->email }}</p>
                        <p class="mb-0">{{ $estimation->client->phone }}</p>
                    @else
                        <span class="text-muted">Estimation réalisée en tant qu'invité.</span>
                    @endif
                    <hr>
                    <small class="text-muted">Soumise le {{ $estimation->created_at->format('d/m/Y à H:i') }}</small>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header fw-bold">Résultat</div>
                <div class="card-body">
                    <p class="mb-1">Valeur estimée :
                        <strong>{{ $estimation->estimated_value !== null ? number_format($estimation->estimated_value, 0, ',', ' ') . ' DH' : '—' }}</strong>
                    </p>
                    <p class="mb-1">Fourchette :
                        @if($estimation->range_min !== null && $estimation->range_max !== null)
                            {{ number_format($estimation->range_min, 0, ',', ' ') }} – {{ number_format($estimation->range_max, 0, ',', ' ') }} DH
                        @else — @endif
                    </p>
                    <p class="mb-0">Confiance : {{ $estimation->confidence ?? '—' }}</p>
                </div>
            </div>
        </div>

        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold">Véhicule</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4 mb-2"><strong>Marque :</strong> {{ $estimation->brand ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Modèle :</strong> {{ $estimation->model ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Version :</strong> {{ $estimation->version ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Année :</strong> {{ $estimation->year ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>1ère immat. :</strong> {{ $estimation->first_registration?->format('d/m/Y') ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Kilométrage :</strong> {{ $estimation->km !== null ? number_format($estimation->km, 0, ',', ' ') . ' km' : '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Carburant :</strong> {{ $estimation->fuel ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Boîte :</strong> {{ $estimation->gearbox ?? '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Puissance :</strong> {{ $estimation->power_cv ? $estimation->power_cv . ' CV' : '—' }}</div>
                        <div class="col-md-4 mb-2"><strong>Origine :</strong> {{ $estimation->origin ?? '—' }}</div>
                    </div>
                </div>
            </div>
        </div>

        @if(!empty($estimation->payload))
        <div class="col-12">
            <div class="card">
                <div class="card-header fw-bold">Détail soumis (payload)</div>
                <div class="card-body">
                    <pre style="margin:0; white-space:pre-wrap; font-size:13px;">{{ json_encode($estimation->payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}</pre>
                </div>
            </div>
        </div>
        @endif
    </div>

    <div class="mt-4">
        <form action="{{ route('admin.estimations.destroy', $estimation->id) }}" method="POST" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger"
                    onclick="return confirm('Supprimer cette estimation ?')">Supprimer</button>
        </form>
    </div>
</div>
@endsection
