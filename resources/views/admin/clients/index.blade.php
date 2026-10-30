@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Liste des Clients</h1>

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

                <th>Vérifié</th>
                <th>Date d’inscription</th>
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
                   <td>
    <input type="checkbox" 
           class="verify-toggle" 
           data-id="{{ $client->id }}"
           {{ $client->email_verified_at ? 'checked' : '' }}>
</td>

                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
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
