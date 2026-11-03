@extends('admin.layouts.app')

@section('content')
<div id="main-content">
    <div class="page-heading">
        <h3>Demandes</h3>
    </div>
    <div class="page-content">
        <section class="row">
            <div class="col-12">

                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <h4 class="card-title mb-0">Toutes les demandes</h4>
                        <a href="{{ route('admin.export', 'demandes') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>

                        <form method="GET" class="d-flex gap-2">
                            <select name="status" class="form-select form-select-sm" onchange="this.form.submit()">
                                <option value="">Tous les statuts</option>
                                @foreach(['pending' => 'En attente', 'approved' => 'Approuvées', 'rejected' => 'Rejetées', 'fulfilled' => 'Satisfaites', 'closed' => 'Clôturées'] as $k => $label)
                                    <option value="{{ $k }}" @selected($status === $k)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </form>
                    </div>
                    <div class="card-content">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Demandeur</th>
                                        <th>Téléphone</th>
                                        <th>Recherche</th>
                                        <th>Ville</th>
                                        <th>Budget</th>
                                        <th>Date</th>
                                        <th>Statut</th>
                                        <th class="text-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($demandes as $d)
                                        <tr>
                                            <td>#{{ $d->id }}</td>
                                            <td><strong>{{ $d->nom }}</strong></td>
                                            <td><a href="tel:{{ $d->telephone }}">{{ $d->telephone }}</a></td>
                                            <td>
                                                {{ $d->brand?->name ?? '—' }} {{ $d->modele?->name }}
                                                @if($d->year_min || $d->year_max)
                                                    <small class="text-muted d-block">
                                                        {{ $d->year_min ?? '…' }}–{{ $d->year_max ?? '…' }}
                                                    </small>
                                                @endif
                                            </td>
                                            <td>{{ $d->ville?->name ?? '—' }}</td>
                                            <td>
                                                @if($d->price_min || $d->price_max)
                                                    {{ number_format($d->price_min ?? 0, 0, ',', ' ') }}–{{ number_format($d->price_max ?? 0, 0, ',', ' ') }} DH
                                                @else — @endif
                                            </td>
                                            <td><small>{{ $d->created_at->format('d/m/Y H:i') }}</small></td>
                                            <td>
                                                @php
                                                    $badge = [
                                                        'pending'   => ['bg-warning text-dark', 'En attente'],
                                                        'approved'  => ['bg-success',           'Approuvée'],
                                                        'rejected'  => ['bg-danger',            'Rejetée'],
                                                        'fulfilled' => ['bg-info',              'Satisfaite'],
                                                        'closed'    => ['bg-secondary',         'Clôturée'],
                                                    ][$d->status] ?? ['bg-secondary', $d->status];
                                                @endphp
                                                <span class="badge {{ $badge[0] }}">{{ $badge[1] }}</span>
                                            </td>
                                            <td class="text-end" style="white-space:nowrap;">
                                                @php $propCount = $d->replies->count(); @endphp
                                                <button type="button"
                                                        class="btn btn-sm btn-outline-info position-relative"
                                                        onclick="(function(id){var r=document.getElementById('props-'+id);r.style.display=r.style.display==='none'?'table-row':'none';})({{ $d->id }});"
                                                        title="Propositions">
                                                    <i class="bi bi-chat-left-text"></i>
                                                    @if($propCount > 0)
                                                        <span class="badge bg-danger" style="position:absolute;top:-6px;right:-6px;font-size:10px;padding:2px 5px;">{{ $propCount }}</span>
                                                    @endif
                                                </button>
                                                <a href="{{ route('demandes.show', $d->id) }}" target="_blank" class="btn btn-sm btn-outline-primary" title="Voir">
                                                    <i class="bi bi-eye"></i>
                                                </a>
                                                @if($d->status !== 'approved')
                                                    <form method="POST" action="{{ route('admin.demandes.approve', $d->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-success" title="Approuver"><i class="bi bi-check-lg"></i></button>
                                                    </form>
                                                @endif
                                                @if($d->status !== 'rejected')
                                                    <form method="POST" action="{{ route('admin.demandes.reject', $d->id) }}" class="d-inline">
                                                        @csrf
                                                        <button class="btn btn-sm btn-warning" title="Rejeter"><i class="bi bi-x-lg"></i></button>
                                                    </form>
                                                @endif
                                                <form method="POST" action="{{ route('admin.demandes.destroy', $d->id) }}" class="d-inline"
                                                      onsubmit="return confirm('Supprimer définitivement cette demande ?');">
                                                    @csrf @method('DELETE')
                                                    <button class="btn btn-sm btn-danger" title="Supprimer"><i class="bi bi-trash"></i></button>
                                                </form>
                                            </td>
                                        </tr>

                                        {{-- Dropdown row: propositions for this demande --}}
                                        <tr id="props-{{ $d->id }}" style="display:none;background:#f8fafc;">
                                            <td colspan="9" style="padding:14px 20px;">
                                                @if($d->replies->isEmpty())
                                                    <div class="text-muted small">Aucune proposition pour cette demande.</div>
                                                @else
                                                    <h6 class="mb-3" style="font-weight:700;">
                                                        <i class="bi bi-chat-left-text"></i>
                                                        Propositions ({{ $d->replies->count() }})
                                                    </h6>
                                                    <div class="table-responsive">
                                                        <table class="table table-sm table-bordered bg-white mb-0">
                                                            <thead class="table-light">
                                                                <tr>
                                                                    <th>Date</th>
                                                                    <th>Vendeur</th>
                                                                    <th>Téléphone</th>
                                                                    <th>Message / Véhicule</th>
                                                                    <th>Statut</th>
                                                                </tr>
                                                            </thead>
                                                            <tbody>
                                                                @foreach($d->replies as $r)
                                                                    @php
                                                                        // Extract proposer name from the "Contact : X — Y" line in the message.
                                                                        $proposerName = $r->client?->name;
                                                                        if (!$proposerName && preg_match('/Contact\s*:\s*(.+?)\s*—/u', $r->message, $m1)) {
                                                                            $proposerName = trim($m1[1]);
                                                                        }
                                                                        // Extract image URLs from the "Photos (N) :\n- url\n- url" block.
                                                                        $propImages = [];
                                                                        if (preg_match_all('#(https?://\S+\.(?:jpg|jpeg|png|webp|gif))#iu', $r->message, $m2)) {
                                                                            $propImages = array_unique($m2[1]);
                                                                        }
                                                                        // Strip the Contact and Photos trailing sections from the body for cleaner display.
                                                                        $cleanBody = preg_replace('/\nPhotos\s*\([^\)]*\)\s*:[\s\S]*$/u', '', $r->message);
                                                                        $cleanBody = preg_replace('/\nContact\s*:.*$/u', '', $cleanBody);
                                                                    @endphp
                                                                    <tr>
                                                                        <td><small>{{ $r->created_at->format('d/m/Y H:i') }}</small></td>
                                                                        <td><strong>{{ $proposerName ?: '—' }}</strong></td>
                                                                        <td>
                                                                            @if($r->contact_phone)
                                                                                <a href="tel:{{ $r->contact_phone }}">{{ $r->contact_phone }}</a>
                                                                            @else — @endif
                                                                        </td>
                                                                        <td style="max-width:480px;font-size:13px;">
                                                                            <div style="white-space:pre-line;">{{ trim($cleanBody) }}</div>
                                                                            @if(count($propImages))
                                                                                <div style="display:flex;flex-wrap:wrap;gap:6px;margin-top:8px;">
                                                                                    @foreach($propImages as $url)
                                                                                        <a href="{{ $url }}" target="_blank" rel="noopener">
                                                                                            <img src="{{ $url }}" alt="Photo" style="width:70px;height:70px;object-fit:cover;border-radius:6px;border:1px solid #e2e8f0;">
                                                                                        </a>
                                                                                    @endforeach
                                                                                </div>
                                                                            @endif
                                                                        </td>
                                                                        <td>
                                                                            @php
                                                                                $rb = [
                                                                                    'visible'  => ['bg-success', 'Visible'],
                                                                                    'hidden'   => ['bg-secondary', 'Masquée'],
                                                                                    'reported' => ['bg-danger', 'Signalée'],
                                                                                ][$r->status] ?? ['bg-secondary', $r->status];
                                                                            @endphp
                                                                            <span class="badge {{ $rb[0] }}">{{ $rb[1] }}</span>
                                                                        </td>
                                                                    </tr>
                                                                @endforeach
                                                            </tbody>
                                                        </table>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr><td colspan="9" class="text-center text-muted py-4">Aucune demande.</td></tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        <div class="p-3">{{ $demandes->links() }}</div>
                    </div>
                </div>

            </div>
        </section>
    </div>
</div>
@endsection
