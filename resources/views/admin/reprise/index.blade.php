@extends('admin.layouts.app')

@section('title' , 'Reprise | nado.ma')


@section('content')
@php
    $photoLabels = [
        'gauche'  => 'Côté gauche',
        'droite'  => 'Côté droit',
        'avant'   => 'Avant',
        'arriere' => 'Arrière',
    ];
@endphp
<div id="main-content">
<div class="page-heading">
    <h3>Reprise</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List Reprise</h4>
                      <a href="{{ route('admin.export', 'reprises') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Name</th>
                                      <th>ville</th>
                                      <th>Mark</th>
                                      <th>Modele</th>
                                      <th>Année</th>
                                      <th>KM</th>
                                      <th>Phone</th>
                                      <th>Email</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($reprises as $reprise)
                                  <tr>
                                      <td>{{ $reprise->id }}</td>
                                      <td>{{ $reprise->name }}</td>
                                      <td>{{ $reprise->city }}</td>
                                      <td>{{ $reprise->mark }}</td>
                                      <td>{{ $reprise->modele }}</td>
                                      <td>{{ $reprise->year }}</td>
                                      <td>
                                        @if (!is_null($reprise->km) && $reprise->km !== '')
                                            {{ number_format($reprise->km, 0, ',', ' ') }} km
                                        @else
                                            &mdash;
                                        @endif
                                      </td>
                                      <td>{{ $reprise->phone }}</td>
                                      <td>{{ $reprise->email }}</td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <button type="button" class="btn btn-sm btn-primary"
                                              data-bs-toggle="modal"
                                              data-bs-target="#repriseDetail{{ $reprise->id }}">
                                              Détails
                                          </button>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('reprises.destroy' , $reprise) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>

                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>ville</th>
                                    <th>Mark</th>
                                    <th>Modele</th>
                                    <th>Année</th>
                                    <th>KM</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th></th>
                                </tr>
                            </tfoot>
                          </table>
                      </div>
                      </div>
                  </div>
              </div>
          </div>
      </div>
</section>
</div>
</div>

{{-- ===== Modales de détail : photos par côté + description ===== --}}
@foreach ($reprises as $reprise)
    @php
        $photos = $reprise->photos;
        if (is_string($photos)) {
            $photos = json_decode($photos, true);
        }
        $photos = is_array($photos) ? array_filter($photos) : [];
    @endphp
    <div class="modal fade" id="repriseDetail{{ $reprise->id }}" tabindex="-1"
        aria-labelledby="repriseDetailLabel{{ $reprise->id }}" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="repriseDetailLabel{{ $reprise->id }}">
                        Reprise #{{ $reprise->id }} &mdash; {{ $reprise->mark }} {{ $reprise->modele }}
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
                </div>
                <div class="modal-body">

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Client :</strong> {{ $reprise->name }}</p>
                            <p class="mb-1"><strong>Téléphone :</strong> {{ $reprise->phone }}</p>
                            <p class="mb-1"><strong>Email :</strong> {{ $reprise->email ?: '—' }}</p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Ville :</strong> {{ $reprise->city }}</p>
                            <p class="mb-1"><strong>Année :</strong> {{ $reprise->year }}</p>
                            <p class="mb-1">
                                <strong>Kilométrage :</strong>
                                @if (!is_null($reprise->km) && $reprise->km !== '')
                                    {{ number_format($reprise->km, 0, ',', ' ') }} km
                                @else
                                    &mdash;
                                @endif
                            </p>
                        </div>
                    </div>

                    <h6 class="fw-bold mb-2">Photos du véhicule</h6>
                    @if (count($photos))
                        <div class="row g-3 mb-4">
                            @foreach ($photoLabels as $slot => $label)
                                @if (!empty($photos[$slot]))
                                    <div class="col-6 col-md-3">
                                        <a href="{{ asset('storage/reprises/' . $photos[$slot]) }}" target="_blank" rel="noopener">
                                            <img src="{{ asset('storage/reprises/' . $photos[$slot]) }}"
                                                alt="{{ $label }}" class="img-fluid rounded border"
                                                style="width:100%;aspect-ratio:4/3;object-fit:cover;">
                                        </a>
                                        <small class="d-block text-center mt-1 fw-bold">{{ $label }}</small>
                                    </div>
                                @endif
                            @endforeach

                            @foreach ($photos as $slot => $file)
                                @if (!array_key_exists($slot, $photoLabels))
                                    <div class="col-6 col-md-3">
                                        <a href="{{ asset('storage/reprises/' . $file) }}" target="_blank" rel="noopener">
                                            <img src="{{ asset('storage/reprises/' . $file) }}" alt="Photo"
                                                class="img-fluid rounded border"
                                                style="width:100%;aspect-ratio:4/3;object-fit:cover;">
                                        </a>
                                        <small class="d-block text-center mt-1 text-muted">Photo</small>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted mb-4">Aucune photo envoyée.</p>
                    @endif

                    <h6 class="fw-bold mb-2">Description</h6>
                    <p class="mb-0" style="white-space: pre-line;">{{ $reprise->description ?: '—' }}</p>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fermer</button>
                </div>
            </div>
        </div>
    </div>
@endforeach


@endsection
