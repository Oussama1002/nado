@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading"><h3>Questions annonces</h3></div>
    <div class="page-content">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="max-width:220px;" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach(['pending'=>'En attente','approved'=>'Approuvées','rejected'=>'Rejetées'] as $k=>$v)
                        <option value="{{ $k }}" {{ $status===$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>#</th><th>Annonce</th><th>Question</th><th>Réponse</th><th>Statut</th><th></th></tr></thead>
                    <tbody>
                    @forelse($questions as $q)
                        <tr>
                            <td>{{ $q->id }}</td>
                            <td>#{{ $q->annonce_id }} {{ $q->annonce->mark ?? '' }} {{ $q->annonce->modele ?? '' }}</td>
                            <td>{{ \Illuminate\Support\Str::limit($q->question, 120) }}</td>
                            <td><small>{{ \Illuminate\Support\Str::limit($q->answer, 100) ?: '—' }}</small></td>
                            <td><span class="badge bg-{{ ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$q->status] }}">{{ $q->status }}</span></td>
                            <td class="d-flex gap-1">
                                @if($q->status !== 'approved')<form method="POST" action="{{ route('admin.community.questions.approve', $q->id) }}">@csrf<button class="btn btn-sm btn-success">✓</button></form>@endif
                                @if($q->status !== 'rejected')<form method="POST" action="{{ route('admin.community.questions.reject', $q->id) }}">@csrf<button class="btn btn-sm btn-warning">✕</button></form>@endif
                                <form method="POST" action="{{ route('admin.community.questions.destroy', $q->id) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="text-center text-muted">Aucune question.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $questions->links() }}
        </div></div>
    </div>
</div>
@endsection
