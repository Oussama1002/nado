@extends('admin.layouts.autoenchere_navigation')

@section('title', 'Demandes de vente')

@section('content')
<div id="main-content">
    <div class="page-heading"><h3>Demandes de vente (vendeurs)</h3></div>
    <div class="page-content">
        @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h4 class="card-title mb-0">Véhicules soumis via « Soumettre mes véhicules »</h4>
                <form method="GET" class="d-flex gap-2">
                    <select name="status" class="form-select form-select-sm" style="max-width:200px;" onchange="this.form.submit()">
                        <option value="">Tous les statuts</option>
                        @foreach(['new'=>'Nouvelles','processed'=>'Traitées','archived'=>'Archivées'] as $k=>$v)
                            <option value="{{ $k }}" {{ $status===$k?'selected':'' }}>{{ $v }}</option>
                        @endforeach
                    </select>
                </form>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped align-middle">
                        <thead>
                            <tr><th>#</th><th>Vendeur</th><th>Véhicules</th><th>Message</th><th>Date</th><th>Statut</th><th></th></tr>
                        </thead>
                        <tbody>
                        @forelse($requests as $r)
                            @php $wa = preg_replace('/\D/', '', (string) $r->phone); if($wa && str_starts_with($wa,'0')) $wa='212'.substr($wa,1); @endphp
                            <tr>
                                <td>{{ $r->id }}</td>
                                <td>
                                    <strong>{{ $r->name ?: ($r->client->name ?? '—') }}</strong><br>
                                    <small class="text-muted">{{ $r->phone ?: '—' }}</small>
                                    @if($r->client)<br><a href="{{ route('admin.nadoautoenchere.clients') }}" class="small">#{{ $r->client->id }}</a>@endif
                                </td>
                                <td>
                                    <span class="badge bg-primary mb-1">{{ $r->vehicleCount() }} véhicule(s)</span>
                                    <ul class="mb-0 ps-3" style="font-size:13px;">
                                        @foreach((array) $r->vehicles as $v)
                                            <li>{{ trim(($v['marque'] ?? '').' '.($v['modele'] ?? '').' '.($v['annee'] ?? '')) }}
                                                @if(($v['km'] ?? '') !== '')— {{ number_format((int)$v['km'],0,',',' ') }} km @endif
                                                @if(($v['etat'] ?? '') !== '')— {{ $v['etat'] }}@endif
                                            </li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td><small>{{ \Illuminate\Support\Str::limit($r->message, 120) ?: '—' }}</small></td>
                                <td><small>{{ $r->created_at->format('d/m/Y H:i') }}</small></td>
                                <td>
                                    @php $sc = ['new'=>'warning','processed'=>'success','archived'=>'secondary'][$r->status] ?? 'secondary'; @endphp
                                    <span class="badge bg-{{ $sc }}">{{ $r->status }}</span>
                                </td>
                                <td class="d-flex flex-wrap gap-1">
                                    @if($wa)<a href="https://wa.me/{{ $wa }}" target="_blank" class="btn btn-sm btn-outline-success"><i class="bi bi-whatsapp"></i></a>@endif
                                    @if($r->status !== 'processed')
                                        <form method="POST" action="{{ route('admin.nadoautoenchere.sellRequests.status', $r->id) }}">@csrf<input type="hidden" name="status" value="processed"><button class="btn btn-sm btn-success" title="Traitée">✓</button></form>
                                    @endif
                                    @if($r->status !== 'archived')
                                        <form method="POST" action="{{ route('admin.nadoautoenchere.sellRequests.status', $r->id) }}">@csrf<input type="hidden" name="status" value="archived"><button class="btn btn-sm btn-secondary" title="Archiver">⇩</button></form>
                                    @endif
                                    <form method="POST" action="{{ route('admin.nadoautoenchere.sellRequests.destroy', $r->id) }}" onsubmit="return confirm('Supprimer ?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">🗑</button></form>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="7" class="text-center text-muted">Aucune demande de vente.</td></tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
                {{ $requests->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
