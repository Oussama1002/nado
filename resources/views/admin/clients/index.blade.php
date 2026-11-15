@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Liste des Clients</h1>
        <a href="{{ route('admin.export', 'clients') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Téléphone</th>
                                <th>Type</th>
                <th>Rôle enchère</th>

                <th>Entreprise</th>

                <th>Date d’inscription</th>
                <th>Accès illimité</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($clients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                                        <td>{{ $client->phone ?? '-' }}</td>
                    <td>{{ $client->type ?? '-' }}</td>
                    <td>{{ $client->ae_role ? ucfirst($client->ae_role) : '-' }}</td>

                    <td>{{ $client->company }}</td>

                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                    <td class="text-center">
                        <div class="form-check form-switch d-inline-block" title="Accès illimité à toutes les fonctions payantes">
                            <input class="form-check-input unlimited-toggle" type="checkbox"
                                   data-id="{{ $client->id }}"
                                   {{ $client->unlimited_tokens ? 'checked' : '' }}>
                        </div>
                        <span class="unlimited-label badge {{ $client->unlimited_tokens ? 'bg-success' : 'bg-secondary' }}">
                            {{ $client->unlimited_tokens ? 'Illimité' : 'Standard' }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST"
                            onsubmit="return confirm('Supprimer ce client ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                                                <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-info btn-sm">Voir</a>

                        </form>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
<script>
document.querySelectorAll('.unlimited-toggle').forEach(checkbox => {
    checkbox.addEventListener('change', function () {
        const clientId  = this.dataset.id;
        const unlimited = this.checked ? 1 : 0;
        const label     = this.closest('td').querySelector('.unlimited-label');

        fetch(`/admin/clients/${clientId}/unlimited`, {
            method: 'PATCH',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            },
            body: JSON.stringify({ unlimited })
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                if (label) {
                    label.textContent = data.unlimited ? 'Illimité' : 'Standard';
                    label.classList.toggle('bg-success', data.unlimited);
                    label.classList.toggle('bg-secondary', !data.unlimited);
                }
            } else {
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
                this.checked = !this.checked; // revert if fail
            }
        })
        .catch(() => {
            alert('Erreur réseau.');
            this.checked = !this.checked; // revert if fail
        });
    });
});
</script>

@endsection
