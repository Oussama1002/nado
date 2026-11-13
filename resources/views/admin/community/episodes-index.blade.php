@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading d-flex justify-content-between align-items-center">
        <h3>Épisodes Nado Radio</h3>
        <a href="{{ route('admin.community.episodes.create') }}" class="btn btn-primary">Nouvel épisode</a>
    </div>
    <div class="page-content">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body"><div class="table-responsive">
            <table class="table table-striped">
                <thead><tr><th>#</th><th>Titre</th><th>Durée</th><th>Publié</th><th>Actif</th><th></th></tr></thead>
                <tbody>
                @forelse($episodes as $e)
                    <tr>
                        <td>{{ $e->id }}</td>
                        <td>{{ $e->title }}</td>
                        <td>{{ $e->duration ?: '—' }}</td>
                        <td>{{ optional($e->published_at)->format('d/m/Y') ?: '—' }}</td>
                        <td>{!! $e->is_active ? '<span class="badge bg-success">Oui</span>' : '<span class="badge bg-secondary">Non</span>' !!}</td>
                        <td class="d-flex gap-1">
                            <a href="{{ route('admin.community.episodes.edit', $e->id) }}" class="btn btn-sm btn-info">Éditer</a>
                            <form method="POST" action="{{ route('admin.community.episodes.destroy', $e->id) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucun épisode.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>{{ $episodes->links() }}</div></div>
    </div>
</div>
@endsection
