@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading d-flex justify-content-between align-items-center">
        <h3>Guides modèles</h3>
        <a href="{{ route('admin.community.guides.create') }}" class="btn btn-primary">Nouveau guide</a>
    </div>
    <div class="page-content">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body"><div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Titre</th><th>Marque / Modèle</th><th>Vues</th><th>Actif</th><th></th></tr></thead>
                <tbody>
                @forelse($guides as $g)
                    <tr>
                        <td>{{ $g->id }}</td>
                        <td>{{ $g->title }}<br><small class="text-muted">/{{ $g->slug }}</small></td>
                        <td>{{ $g->brand->name ?? '—' }} {{ $g->modele->name ?? '' }}</td>
                        <td>{{ $g->views }}</td>
                        <td>{!! $g->is_active ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('admin.community.guides.edit', $g->id) }}" class="btn btn-sm btn-info">Éditer</a>
                            <form method="POST" action="{{ route('admin.community.guides.destroy', $g->id) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucun guide.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>{{ $guides->links() }}</div></div>
    </div>
</div>
@endsection
