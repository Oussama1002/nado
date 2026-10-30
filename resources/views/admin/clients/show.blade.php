@extends('admin.layouts.app')

@section('content')
    <h1>Détails du Client</h1>
    <p><strong>ID:</strong> {{ $client->id }}</p>
    <p><strong>Nom:</strong> {{ $client->name }}</p>
    <p><strong>Email:</strong> {{ $client->email }}</p>
    <p><strong>Rôle Auto Enchère:</strong> {{ $client->ae_role ? ucfirst($client->ae_role) : '—' }}</p>
<p><strong>Type de client:</strong></p>

<select id="client-type" class="form-control" style="width:200px;">
    <option value="pro" {{ $client->type == 'pro' ? 'selected' : '' }}>Pro</option>
    <option value="particulier" {{ $client->type == 'particulier' ? 'selected' : '' }}>Particulier</option>
    <option value="flotte&enchere" {{ $client->type == 'flotte&enchere' ? 'selected' : '' }}>Flotte & Enchère</option>
</select>

<div id="type-status" style="margin-top:10px;"></div>

<script>
document.getElementById('client-type').addEventListener('change', function() {
    const newType = this.value;

    fetch("{{ route('admin.clients.updateType', $client->id) }}", {
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({ type: newType })
    })
    .then(response => response.json())
    .then(data => {
        const statusDiv = document.getElementById('type-status');
        if (data.success) {
            statusDiv.innerHTML = `<span style="color:green;">Type mis à jour avec succès → ${data.type}</span>`;
        } else {
            statusDiv.innerHTML = `<span style="color:red;">Erreur de mise à jour</span>`;
        }
    })
    .catch(() => {
        document.getElementById('type-status').innerHTML = `<span style="color:red;">Erreur de serveur</span>`;
    });
});
</script>

    <p><strong>Téléphone:</strong> {{ $client->phone }}</p>
    <p><strong>Date d'inscription:</strong> {{ $client->created_at }}</p>
    <form action="{{ route('admin.clients.approve', $client->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-success">Approuver</button>
                        </form>
@endsection
