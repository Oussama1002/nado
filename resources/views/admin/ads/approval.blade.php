@extends('admin.layouts.app') {{-- Or whatever your admin layout is --}}

@section('content')
<div class="container">
    <h1>Annonces en attente d'approbation</h1>

    @if($pendingAnnonces->count() > 0)
        <table class="table">
            <thead>
                <tr>
                    <th>#ID</th>
                    <th>Titre</th>
                    <th>Client</th>
                    <th>Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($pendingAnnonces as $annonce)
                <tr>
                    <td>{{ $annonce->id }}</td>
  <td>{{ $annonce->brand ? $annonce->brand->name : '-' }}
{{ $annonce->modeleRelation ? $annonce->modeleRelation->name : '-' }}</td>
                    <td>{{ $annonce->client->name ?? 'N/A' }}</td>
                    <td>{{ $annonce->created_at->format('d/m/Y') }}</td>
                    <td>
                    <a href="{{ route('admin.annonces.show', $annonce->id) }}" class="btn btn-primary btn-sm" target="_blank" rel="noopener">Voir</a>
                    @if(admin_can('annonces','modify'))<a href="{{ route('admin.annonces.edit', $annonce->id) }}" class="btn btn-warning btn-sm">Modifier</a>@endif
                        <form method="POST" action="{{ route('admin.annonces.approve', $annonce->id) }}" style="display:inline-block;">
                            @csrf
                            @method('PATCH')
                            <button class="btn btn-success btn-sm">Approuver</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <p>Aucune annonce en attente.</p>
    @endif
</div>
@endsection
