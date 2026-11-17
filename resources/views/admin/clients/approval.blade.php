@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Clients à Approuver</h1>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($unapprovedClients->isEmpty())
        <p>Aucun client en attente de validation.</p>
    @else
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#ID</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Type de Client</th>

                 <th>Entreprise</th>
                 <th>Logo</th>

                <th>Date d’inscription</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($unapprovedClients as $client)
                <tr>
                    <td>{{ $client->id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->email }}</td>
                                        <td>{{ $client->type }}</td>

                    <td>{{ $client->company ?? "Pas d'entreprise "}}</td>
                                        <td>{{ $client->logo ?? 'Pas de Logo'}}</td>


                    <td>{{ $client->created_at->format('d/m/Y') }}</td>
                    <td>
                        <form action="{{ route('admin.clients.approve', $client->id) }}" method="POST">
                            @csrf
                            <button class="btn btn-sm btn-success">Approuver</button>
                        </form>
                         <!-- View Button -->
                    <a href="{{ route('admin.clients.show', $client->id) }}" class="btn btn-info btn-sm">Voir</a>

<!-- Delete Button -->
@if(admin_can('clients','delete'))
<form action="{{ route('admin.clients.destroy', $client->id) }}" method="POST" style="display:inline;">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Confirmer la suppression ?')">Supprimer</button>
</form>
@endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
