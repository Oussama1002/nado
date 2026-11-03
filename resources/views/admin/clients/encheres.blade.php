@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Clients Auto Enchère</h1>
        <a href="{{ route('admin.export', 'enchere-clients') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Nom</th>
                <th>Entreprise</th>
                <th>Email</th>
                <th>Téléphone</th>
                <th>Rôle</th>
                <th>Accès enchères</th>
                <th>Vérifié</th>
                <th>Date d’inscription</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->company ?? '-' }}</td>
                    <td>{{ $client->email }}</td>
                    <td>{{ $client->phone ?? '-' }}</td>
                    <td>
                        @if ($client->ae_role === 'vendeur')
                            <span class="badge" style="background:#F37021;color:#fff;">Vendeur</span>
                        @elseif ($client->ae_role === 'acheteur')
                            <span class="badge" style="background:#1459DD;color:#fff;">Acheteur</span>
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td>
                        @if ($client->ae_approved)
                            <span class="badge" style="background:#16a34a;color:#fff;">Validé</span>
                        @else
                            <span class="badge" style="background:#f59e0b;color:#fff;">En attente</span>
                        @endif
                        <form action="{{ route('admin.nadoautoenchere.clients.approve', $client->id) }}" method="POST" style="display:inline;">
                            @csrf
                            <button class="btn btn-sm {{ $client->ae_approved ? 'btn-outline-secondary' : 'btn-success' }}">
                                {{ $client->ae_approved ? 'Retirer' : 'Valider' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <input type="checkbox"
                               class="verify-toggle"
                               data-id="{{ $client->id }}"
                               {{ $client->email_verified_at ? 'checked' : '' }}>
                    </td>
                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                    <td>
                        <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-info btn-sm">Voir</a>
                        <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST"
                            onsubmit="return confirm('Supprimer ce client ?')" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" class="text-center text-muted">Aucun client Auto Enchère pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<script>
document.querySelectorAll('.verify-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const clientId = this.dataset.id;
        const verified = this.checked ? 1 : 0;

        fetch(`/admin/clients/${clientId}/verify`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ verified })
        })
        .then(res => res.json())
        .then(data => {
            if (!data.success) {
                alert('Erreur lors de la mise à jour.');
                this.checked = !this.checked;
            }
        })
        .catch(() => {
            alert('Erreur réseau.');
            this.checked = !this.checked;
        });
    });
});
</script>
@endsection
