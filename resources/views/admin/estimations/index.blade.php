@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Estimations clients</h1>
        <span class="text-muted">{{ $estimations->total() }} estimation(s)</span>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th style="width:60px;">#</th>
                <th style="width:130px;">Date</th>
                <th>Client</th>
                <th>Véhicule</th>
                <th style="width:110px;">KM</th>
                <th style="width:150px;">Valeur estimée</th>
                <th style="width:170px;">Fourchette</th>
                <th style="width:170px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($estimations as $estimation)
                <tr>
                    <td>{{ $estimation->id }}</td>
                    <td>{{ $estimation->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        @if($estimation->client)
                            <strong>{{ $estimation->client->name }}</strong><br>
                            <small class="text-muted">{{ $estimation->client->phone ?? $estimation->client->email }}</small>
                        @else
                            <span class="text-muted">Invité</span>
                        @endif
                    </td>
                    <td>
                        <strong>{{ $estimation->brand }} {{ $estimation->model }}</strong>
                        @if($estimation->version)<br><small>{{ $estimation->version }}</small>@endif
                        @if($estimation->year)<br><small class="text-muted">{{ $estimation->year }} · {{ $estimation->fuel }}</small>@endif
                    </td>
                    <td>{{ $estimation->km !== null ? number_format($estimation->km, 0, ',', ' ') . ' km' : '—' }}</td>
                    <td>
                        @if($estimation->estimated_value !== null)
                            <strong>{{ number_format($estimation->estimated_value, 0, ',', ' ') }} DH</strong>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if($estimation->range_min !== null && $estimation->range_max !== null)
                            <small>{{ number_format($estimation->range_min, 0, ',', ' ') }} – {{ number_format($estimation->range_max, 0, ',', ' ') }} DH</small>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        <a href="{{ route('admin.estimations.show', $estimation->id) }}" class="btn btn-sm btn-info">Détails</a>
                        <form action="{{ route('admin.estimations.destroy', $estimation->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cette estimation ?')">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">Aucune estimation pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $estimations->links() }}
</div>
@endsection
