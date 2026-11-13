@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading"><h3>Avis vendeurs</h3></div>
    <div class="page-content">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
        <div class="card"><div class="card-body">
            <form method="GET" class="mb-3 d-flex gap-2">
                <select name="status" class="form-select form-select-sm" style="max-width:220px;" onchange="this.form.submit()">
                    <option value="">Tous les statuts</option>
                    @foreach(['pending'=>'En attente','approved'=>'Approuvés','rejected'=>'Rejetés'] as $k=>$v)
                        <option value="{{ $k }}" {{ $status===$k?'selected':'' }}>{{ $v }}</option>
                    @endforeach
                </select>
            </form>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead><tr><th>#</th><th>Note</th><th>Vendeur</th><th>Auteur</th><th>Avis</th><th>Statut</th><th></th></tr></thead>
                    <tbody>
                    @forelse($reviews as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ str_repeat('★', $r->rating) }}{{ str_repeat('☆', 5-$r->rating) }}</td>
                            <td>{{ $r->seller->company ?? $r->seller->name ?? ('#'.$r->seller_client_id) }}</td>
                            <td>{{ $r->reviewer->name ?? ('#'.$r->reviewer_client_id) }} @if($r->verified)<span class="badge bg-success">vérifié</span>@endif</td>
                            <td><strong>{{ $r->title }}</strong><br><small>{{ \Illuminate\Support\Str::limit($r->body, 120) }}</small></td>
                            <td><span class="badge bg-{{ ['pending'=>'warning','approved'=>'success','rejected'=>'danger'][$r->status] }}">{{ $r->status }}</span></td>
                            <td class="d-flex gap-1">
                                @if($r->status !== 'approved')<form method="POST" action="{{ route('admin.community.seller-reviews.approve', $r->id) }}">@csrf<button class="btn btn-sm btn-success">✓</button></form>@endif
                                @if($r->status !== 'rejected')<form method="POST" action="{{ route('admin.community.seller-reviews.reject', $r->id) }}">@csrf<button class="btn btn-sm btn-warning">✕</button></form>@endif
                                <form method="POST" action="{{ route('admin.community.seller-reviews.destroy', $r->id) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑</button></form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted">Aucun avis.</td></tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
            {{ $reviews->links() }}
        </div></div>
    </div>
</div>
@endsection
