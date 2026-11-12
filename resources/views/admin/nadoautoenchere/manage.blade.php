@extends('admin.layouts.autoenchere_navigation')

@section('title', 'Gérer l\'enchère')

@section('content')
<div id="main-content">
  <div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
      <h3>Gérer l'enchère #{{ $enchere->id }} — {{ $enchere->titre ?: ($enchere->brand?->name.' '.$enchere->modeleRelation?->name) }}</h3>
      <a href="{{ route('admin.nadoautoenchere.index') }}" class="btn btn-light">← Retour</a>
    </div>
  </div>

  <div class="page-content">
    @if(session('success'))
      <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
      <div class="alert alert-danger">{{ session('error') }}</div>
    @endif

    <section class="row">
      {{-- Réglages + cycle de vie --}}
      <div class="col-12 col-lg-5">
        <div class="card">
          <div class="card-header"><h4 class="card-title">Paramètres d'enchère</h4></div>
          <div class="card-body">
            @php
              $fmtDt = fn($d) => $d ? \Carbon\Carbon::parse($d)->format('Y-m-d\TH:i') : '';
              $statusColors = ['draft'=>'secondary','scheduled'=>'info','live'=>'success','ended'=>'dark','cancelled'=>'danger'];
            @endphp
            <p>
              <span class="badge bg-{{ $statusColors[$enchere->status] ?? 'secondary' }}">{{ strtoupper($enchere->status ?? 'draft') }}</span>
              @if($enchere->extensions_count) <span class="badge bg-warning text-dark">Prolongée ×{{ $enchere->extensions_count }}</span> @endif
            </p>

            <form action="{{ route('admin.nadoautoenchere.setAuction', $enchere->id) }}" method="POST">
              @csrf
              <div class="mb-3">
                <label class="form-label">Mise à prix (MAD)</label>
                <input type="number" step="1" name="starting_price" class="form-control" value="{{ old('starting_price', $enchere->starting_price ?: $enchere->prix) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Prix de réserve (MAD) — vide = aucune</label>
                <input type="number" step="1" name="reserve_price" class="form-control" value="{{ old('reserve_price', $enchere->reserve_price) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Incrément minimum (MAD)</label>
                <input type="number" step="1" name="min_increment" class="form-control" value="{{ old('min_increment', $enchere->min_increment ?: 1000) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Ouverture</label>
                <input type="datetime-local" name="starts_at" class="form-control" value="{{ $fmtDt($enchere->starts_at) }}">
              </div>
              <div class="mb-3">
                <label class="form-label">Clôture</label>
                <input type="datetime-local" name="ends_at" class="form-control" value="{{ $fmtDt($enchere->ends_at) }}">
              </div>
              <button class="btn btn-primary w-100">Enregistrer les paramètres</button>
            </form>

            <hr>
            <label class="form-label fw-bold">Actions</label>
            <div class="d-flex flex-wrap" style="gap:8px;">
              @foreach(['start'=>['Démarrer (live)','success'],'pause'=>['Mettre en pause','warning'],'schedule'=>['Planifier','info'],'cancel'=>['Annuler','danger']] as $act => $meta)
                <form action="{{ route('admin.nadoautoenchere.action', $enchere->id) }}" method="POST"
                      onsubmit="return {{ $act==='cancel' ? "confirm('Annuler cette enchère ?')" : 'true' }};">
                  @csrf
                  <input type="hidden" name="action" value="{{ $act }}">
                  <button class="btn btn-sm btn-outline-{{ $meta[1] }}">{{ $meta[0] }}</button>
                </form>
              @endforeach
            </div>
          </div>
        </div>

        <div class="card">
          <div class="card-header"><h4 class="card-title">État courant</h4></div>
          <div class="card-body">
            <table class="table table-sm mb-0">
              <tr><th>Prix courant</th><td>{{ number_format($enchere->currentHighBid(), 0, ',', ' ') }} MAD</td></tr>
              <tr><th>Enchère min. suivante</th><td>{{ number_format($enchere->minNextBid(), 0, ',', ' ') }} MAD</td></tr>
              <tr><th>Réserve</th><td>{{ $enchere->reserve_price ? number_format($enchere->reserve_price,0,',',' ').' MAD' : '—' }}</td></tr>
              <tr><th>Gagnant</th><td>{{ $enchere->winner?->name ?? ($enchere->winner?->company ?? '—') }}</td></tr>
            </table>
          </div>
        </div>
      </div>

      {{-- Historique des enchères --}}
      <div class="col-12 col-lg-7">
        <div class="card">
          <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="card-title">Historique des enchères ({{ $bids->count() }})</h4>
            <a href="{{ route('admin.nadoautoencherebid.export') }}" class="btn btn-sm btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
          </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped mb-0">
                <thead>
                  <tr><th>#</th><th>Client</th><th>Montant / max</th><th>Type</th><th>Date</th><th></th></tr>
                </thead>
                <tbody>
                  @forelse($bids as $bid)
                    <tr class="{{ $enchere->winning_bid_id === $bid->id ? 'table-success' : '' }}">
                      <td>{{ $bid->id }}</td>
                      <td>{{ $bid->client?->name ?? ($bid->client?->company ?? ('#'.$bid->client_id)) }}</td>
                      <td>{{ number_format($bid->proposition, 0, ',', ' ') }} MAD</td>
                      <td>{!! $bid->is_auto ? '<span class="badge bg-info">auto</span>' : '<span class="badge bg-secondary">manuel</span>' !!}</td>
                      <td>{{ $bid->created_at?->format('d/m/Y H:i') }}</td>
                      <td>
                        @if($enchere->winning_bid_id !== $bid->id)
                        <form action="{{ route('admin.nadoautoenchere.declareWinner', $enchere->id) }}" method="POST"
                              onsubmit="return confirm('Désigner ce client comme gagnant et clôturer ?');">
                          @csrf
                          <input type="hidden" name="bid_id" value="{{ $bid->id }}">
                          <button class="btn btn-sm btn-outline-dark">Gagnant</button>
                        </form>
                        @else
                          <span class="badge bg-success">Gagnant</span>
                        @endif
                      </td>
                    </tr>
                  @empty
                    <tr><td colspan="6" class="text-center text-muted">Aucune enchère pour le moment.</td></tr>
                  @endforelse
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>
  </div>
</div>
@endsection
