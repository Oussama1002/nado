@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Vérification #{{ $legalCheck->id }}</h1>
        <a href="{{ route('admin.legalchecks.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left"></i> Retour
        </a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="row">
        <div class="col-md-5">
            <table class="table table-bordered">
                <tr><th>Matricule</th><td><strong dir="ltr" style="unicode-bidi:isolate;">{{ $legalCheck->matricule_full ?: '—' }}</strong></td></tr>
                <tr><th>Résultat déclaré</th>
                    <td>
                        @if($legalCheck->declared_status === 'clear')
                            <span class="badge bg-success">Aucune opposition</span>
                        @elseif($legalCheck->declared_status === 'opposition')
                            <span class="badge bg-warning text-dark">Opposition active</span>
                        @else
                            <span class="badge bg-secondary">Inconnu</span>
                        @endif
                    </td>
                </tr>
                <tr><th>Vérifié le</th><td>{{ optional($legalCheck->checked_at)->format('d/m/Y H:i') ?? '—' }}</td></tr>
                <tr><th>Vendeur</th>
                    <td>
                        @if($legalCheck->client)
                            {{ $legalCheck->client->name }}<br>
                            <small class="text-muted">{{ $legalCheck->client->phone ?? $legalCheck->client->email }}</small>
                        @else — @endif
                    </td>
                </tr>
                <tr><th>Annonce</th>
                    <td>
                        @if($legalCheck->annonce)
                            <a href="{{ route('admin.annonces.show', $legalCheck->annonce->id) }}">#{{ $legalCheck->annonce->id }} {{ $legalCheck->annonce->titre }}</a>
                        @else <span class="text-muted">Standalone</span> @endif
                    </td>
                </tr>
                <tr><th>Statut admin</th>
                    <td>
                        @switch($legalCheck->admin_status)
                            @case('confirmed') <span class="badge bg-success">Confirmée</span> @break
                            @case('rejected')  <span class="badge bg-danger">Rejetée</span> @break
                            @default           <span class="badge bg-secondary">En attente</span>
                        @endswitch
                        @if($legalCheck->reviewed_at)
                            <br><small class="text-muted">le {{ $legalCheck->reviewed_at->format('d/m/Y H:i') }}</small>
                        @endif
                    </td>
                </tr>
                @if($legalCheck->notes)
                    <tr><th>Notes</th><td>{{ $legalCheck->notes }}</td></tr>
                @endif
            </table>

            <form action="{{ route('admin.legalchecks.confirm', $legalCheck->id) }}" method="POST" class="mb-2">
                @csrf
                <div class="mb-2">
                    <textarea name="notes" class="form-control" rows="2" placeholder="Notes (optionnel)">{{ $legalCheck->notes }}</textarea>
                </div>
                <button type="submit" class="btn btn-success">
                    <i class="bi bi-check-circle"></i> Confirmer
                </button>
                <button type="submit" formaction="{{ route('admin.legalchecks.reject', $legalCheck->id) }}" class="btn btn-danger">
                    <i class="bi bi-x-circle"></i> Rejeter
                </button>
            </form>
            <p class="text-muted small">
                Astuce : vérifiez que le matricule sur la capture correspond exactement et que le résultat NARSA
                affiche bien « aucune opposition » avant de confirmer.
            </p>
        </div>

        <div class="col-md-7">
            <h5>Capture d'écran fournie</h5>
            @if($legalCheck->screenshot_path)
                <a href="{{ route('admin.legalchecks.screenshot', $legalCheck->id) }}" target="_blank" rel="noopener">
                    <img src="{{ route('admin.legalchecks.screenshot', $legalCheck->id) }}"
                         alt="Capture NARSA" class="img-fluid border rounded" style="max-height:600px;">
                </a>
            @else
                <p class="text-muted">Aucune capture.</p>
            @endif
        </div>
    </div>
</div>
@endsection
