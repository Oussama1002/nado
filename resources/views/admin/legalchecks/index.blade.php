@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Vérifications situation légale (NARSA)</h1>
        <span class="text-muted">{{ $checks->total() }} vérification(s)</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <div class="btn-group mb-3" role="group">
        @php $tabs = ['pending' => 'En attente', 'confirmed' => 'Confirmées', 'rejected' => 'Rejetées', 'all' => 'Toutes']; @endphp
        @foreach ($tabs as $key => $label)
            <a href="{{ route('admin.legalchecks.index', ['status' => $key]) }}"
               class="btn btn-sm {{ $filter === $key ? 'btn-primary' : 'btn-outline-primary' }}">
                {{ $label }}
                @if($key !== 'all')
                    <span class="badge bg-light text-dark">{{ $counts[$key] ?? 0 }}</span>
                @endif
            </a>
        @endforeach
    </div>

    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th style="width:60px;">#</th>
                <th style="width:140px;">Date</th>
                <th>Vendeur</th>
                <th>Annonce</th>
                <th style="width:150px;">Matricule</th>
                <th style="width:140px;">Résultat</th>
                <th style="width:130px;">Statut</th>
                <th style="width:120px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($checks as $check)
                <tr>
                    <td>{{ $check->id }}</td>
                    <td>{{ $check->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($check->client)
                            <strong>{{ $check->client->name }}</strong><br>
                            <small class="text-muted">{{ $check->client->phone ?? $check->client->email }}</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($check->annonce)
                            <a href="{{ route('admin.annonces.show', $check->annonce->id) }}">#{{ $check->annonce->id }} {{ \Illuminate\Support\Str::limit($check->annonce->titre, 30) }}</a>
                        @else
                            <span class="text-muted">Standalone</span>
                        @endif
                    </td>
                    <td><span dir="ltr" style="unicode-bidi:isolate;">{{ $check->matricule_full }}</span></td>
                    <td>
                        @if($check->declared_status === 'clear')
                            <span class="badge bg-success">Aucune opposition</span>
                        @elseif($check->declared_status === 'opposition')
                            <span class="badge bg-warning text-dark">Opposition active</span>
                        @else
                            <span class="badge bg-secondary">Inconnu</span>
                        @endif
                    </td>
                    <td>
                        @switch($check->admin_status)
                            @case('confirmed') <span class="badge bg-success">Confirmée</span> @break
                            @case('rejected')  <span class="badge bg-danger">Rejetée</span> @break
                            @default           <span class="badge bg-secondary">En attente</span>
                        @endswitch
                    </td>
                    <td>
                        <a href="{{ route('admin.legalchecks.show', $check->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-eye"></i> Examiner
                        </a>
                    </td>
                </tr>
            @empty
                <tr><td colspan="8" class="text-center text-muted py-4">Aucune vérification.</td></tr>
            @endforelse
        </tbody>
    </table>

    {{ $checks->links() }}
</div>
@endsection
