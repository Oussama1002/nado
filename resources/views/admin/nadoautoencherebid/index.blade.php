@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="mb-0">Liste des Offres</h1>
        <a href="{{ route('admin.nadoautoencherebid.export') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
    </div>

    <!-- <a href="{{ route('admin.autoenchere.create') }}" class="btn btn-primary mb-3">Nouvelle Enchère</a> -->

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>Vehicule</th>
                <th>Matricule</th>
        <th>Societe (Propriétaire)</th>
                <th>Enchéreur</th>

                <th>Proposition</th>
                                <th>Nombre D'offres</th>

                <th>Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
    @foreach ($encheres as $enchere)
        <tr @if($enchere->proposition == $bestPropositions[$enchere->enchere_id]) 
                style="background-color: #c7b134ff;"  {{-- light green --}}
            @endif>
            <td>{{ $enchere->id }}</td>
            <td>{{ $enchere->enchere->titre ?? 'N/A' }}</td>
            <td>{{ $enchere->enchere->matricule ?? 'N/A' }}</td>
            <td>{{ $enchere->enchere->client->company ?? 'N/A' }}</td>
            <td>{{ $enchere->client->name ?? 'N/A' }}</td>
            <td>{{ number_format($enchere->proposition, 0, ',', ' ') }} MAD</td>
<td>{{ count($enchere->enchere->bids) }}</td>

            <td>{{ $enchere->created_at->format('d/m/Y H:i') }}</td>
            <td>
                <a href="{{ route('admin.nadoautoencherebid.show', $enchere->id) }}" class="btn btn-sm btn-info">Voir</a>
                <form action="{{ route('admin.nadoautoencherebid.destroy', $enchere->id) }}" method="POST" class="d-inline"
                      onsubmit="return confirm('Supprimer cette enchère ?')">
                    @csrf
                    @method('DELETE')
                    <button class="btn btn-sm btn-danger">Supprimer</button>
                </form>
            </td>
        </tr>
    @endforeach
</tbody>

    </table>
   @if($encheres->count())
    <div class="col-12 d-flex justify-content-center my-3">
        <a href="{{ route('admin.nadoautoencherebid.export') }}" class="btn btn-success">
            <i class="bi bi-file-earmark-excel"></i> Exporter toutes les offres en CSV
        </a>
    </div>
@endif


</div>
@endsection
