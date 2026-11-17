@extends('admin.layouts.app')

@section('content')
<div class="container">
    <h1 class="mb-4">Liste des Offres</h1>

    <!-- <a href="{{ route('admin.autoenchere.create') }}" class="btn btn-primary mb-3">Nouvelle Enchère</a> -->

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Annonce</th>
                <th>Client</th>
                <th>Proposition</th>
                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($enchers as $enchere)
                <tr>
                    <td>{{ $enchere->id }}</td>
                    <td>{{ $enchere->annonce->titre ?? 'N/A' }}</td>
                    <td>{{ $enchere->client->name ?? 'N/A' }}</td>
                    <td>{{ number_format($enchere->proposition, 0, ',', ' ') }} MAD</td>
                    <td>{{ $enchere->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                    <a href="{{ route('admin.autoenchere.show', $enchere->id) }}" class="btn btn-sm btn-info">Voir</a>

                        @if(admin_can('encheres','modify'))<a href="{{ route('admin.autoenchere.edit', $enchere->id) }}" class="btn btn-sm btn-warning">Modifier</a>@endif
                        @if(admin_can('encheres','delete'))
                        <form action="{{ route('admin.autoenchere.destroy', $enchere->id) }}" method="POST" class="d-inline"
                              onsubmit="return confirm('Supprimer cette enchère ?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger">Supprimer</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
