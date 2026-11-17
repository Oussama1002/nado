@extends('admin.layouts.app')

@section('title', 'Publications sociales | nado.ma')

@section('content')
<div id="main-content">
    <div class="page-heading">
        <h3>Publications sociales (TikTok / Instagram)</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h4 class="card-title mb-0">Liste</h4>
                        @if(admin_can('contenu','add'))
                        <a href="{{ route('admin.social-posts.create') }}" class="btn btn-primary">
                            + Ajouter une publication
                        </a>
                        @endif
                    </div>

                    <div class="card-body">
                        @if(session('success'))
                            <div class="alert alert-success">{{ session('success') }}</div>
                        @endif

                        @if($posts->isEmpty())
                            <div class="alert alert-info mb-0">
                                Aucune publication pour le moment. Cliquez sur
                                <strong>« Ajouter une publication »</strong> pour créer la première.
                            </div>
                        @else
                            <div class="table-responsive">
                                <table class="table table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width:80px">Aperçu</th>
                                            <th>Titre</th>
                                            <th>Plateforme</th>
                                            <th>Compte</th>
                                            <th>URL</th>
                                            <th class="text-center">Position</th>
                                            <th class="text-center">Actif</th>
                                            <th class="text-end">Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($posts as $post)
                                            <tr>
                                                <td>
                                                    @if($post->thumbnail_url)
                                                        <img src="{{ $post->thumbnail_url }}" alt="" style="width:60px;height:60px;object-fit:cover;border-radius:8px;">
                                                    @else
                                                        <span class="text-muted">—</span>
                                                    @endif
                                                </td>
                                                <td>{{ $post->title }}</td>
                                                <td>
                                                    <span class="badge {{ $post->platform === 'tiktok' ? 'bg-dark' : 'bg-danger' }}">
                                                        {{ $post->platform_label }}
                                                    </span>
                                                </td>
                                                <td>{{ $post->handle ?? '—' }}</td>
                                                <td style="max-width:220px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">
                                                    <a href="{{ $post->url }}" target="_blank" rel="noopener">{{ $post->url }}</a>
                                                </td>
                                                <td class="text-center">{{ $post->position }}</td>
                                                <td class="text-center">
                                                    @if($post->is_active)
                                                        <span class="badge bg-success">Oui</span>
                                                    @else
                                                        <span class="badge bg-secondary">Non</span>
                                                    @endif
                                                </td>
                                                <td class="text-end">
                                                    @if(admin_can('contenu','modify'))
                                                    <a href="{{ route('admin.social-posts.edit', $post) }}"
                                                       class="btn btn-sm btn-warning">Modifier</a>
                                                    @endif
                                                    @if(admin_can('contenu','delete'))
                                                    <form action="{{ route('admin.social-posts.destroy', $post) }}"
                                                          method="POST" class="d-inline"
                                                          onsubmit="return confirm('Supprimer cette publication ?');">
                                                        @csrf @method('DELETE')
                                                        <button class="btn btn-sm btn-danger">Supprimer</button>
                                                    </form>
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
@endsection
