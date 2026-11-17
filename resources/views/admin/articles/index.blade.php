@extends('admin.layouts.app')

@section('content')
<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Gestion des Articles</h1>
        @if(admin_can('contenu','add'))
        <a href="{{ route('admin.articles.create') }}" class="btn btn-primary">
            + Nouvel article
        </a>
        @endif
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table table-bordered table-hover align-middle">
        <thead>
            <tr>
                <th style="width:60px;">#</th>
                <th style="width:120px;">Image</th>
                <th>Titre</th>
                <th>Slug</th>
                <th style="width:120px;">Date</th>
                <th style="width:100px;">Actif</th>
                <th style="width:220px;">Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($articles as $article)
                <tr>
                    <td>{{ $article->id }}</td>
                    <td>
                        @if($article->image)
                            <img src="{{ asset('storage/' . $article->image) }}"
                                 alt="{{ $article->title }}"
                                 style="width:100px; height:70px; object-fit:cover; border-radius:6px;">
                        @else
                            <span class="text-muted">—</span>
                        @endif
                    </td>
                    <td><strong>{{ $article->title }}</strong></td>
                    <td><code style="font-size:12px;">{{ $article->slug }}</code></td>
                    <td>{{ $article->created_at->format('d/m/Y') }}</td>
                    <td>
                        <form action="{{ route('admin.articles.toggle', $article->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-sm {{ $article->is_active ? 'btn-success' : 'btn-secondary' }}">
                                {{ $article->is_active ? 'Actif' : 'Inactif' }}
                            </button>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('articles.show', $article->slug) }}" target="_blank" class="btn btn-sm btn-info">Voir</a>
                        @if(admin_can('contenu','modify'))<a href="{{ route('admin.articles.edit', $article->id) }}" class="btn btn-sm btn-warning">Modifier</a>@endif
                        @if(admin_can('contenu','delete'))
                        <form action="{{ route('admin.articles.destroy', $article->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-danger"
                                    onclick="return confirm('Supprimer cet article ?')">Supprimer</button>
                        </form>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center text-muted py-4">Aucun article pour le moment.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{ $articles->links() }}
</div>
@endsection
