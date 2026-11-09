@extends('admin.layouts.autoenchere_navigation')

@section('title', 'Auto Enchère Navigation')

@section('content')
<div id="main-content">
  

    <div class="page-heading">
        <h3>Auto Encheres</h3>
    </div>
    <div class="page-content">
    <section class="section">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">List Auto Encheres</h4>
            <div class="d-flex" style="gap:8px;"><a href="{{ route('admin.export', 'nadoautoenchere') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a><a href="{{ route('admin.nadoautoenchere.create')}}" class="btn btn-primary">New Auto Encheres</a></div>
        </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped mb-0 display" id="example">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>titre</th>
                          <th>mark</th>
                          <th>modele</th>
                          <th>Entreprise</th>
                          <th>views</th>
                          <th>date de demarrage</th>
                          <th>date de fin</th>

                          <!-- <th>date</th> -->
                          <th>Active</th>
                          <th>Notifié</th>
                          <th class="d-flex justify-content-end">Action</th>
                      </tr>
                  </thead>
                  <tbody>
@foreach($encheres as $enchere)
                    <tr>
                      <th scope="row">{{ $enchere->id; }}</th>
                      <td>{{ $enchere->titre; }}</td>
                      <td>{{ $enchere->brand?->name ?? 'N/A' }}</td>
<td>{{ $enchere->modeleRelation?->name ?? 'N/A' }}</td>
                      {{-- Entreprise du client (relation via le telephone) --}}
                      <td>{{ $enchere->client?->company ?: '—' }}</td>

                      <td>{{ $enchere->views; }}</td>
                      <td>{{ $enchere->starts_at; }}</td>
                      <td>{{ $enchere->ends_at; }}</td>

                      <!-- <td>{{ $enchere->created_at; }}</td> -->
                      <td class="text-center">
                          {{-- Curseur pour activer / désactiver l'enchère --}}
                          <div class="form-check form-switch d-inline-block">
                              <input class="form-check-input enchere-active-toggle" type="checkbox" role="switch"
                                     data-id="{{ $enchere->id }}" {{ $enchere->active ? 'checked' : '' }}
                                     style="cursor:pointer;">
                          </div>
                      </td>
                      <td class="text-center">
                          {{-- Rappel 24h envoyé aux clients / vendeur ? --}}
                          @if ($enchere->reminder_sent_at)
                              <span class="badge bg-success">Oui</span><br>
                              <small class="text-muted">{{ \Illuminate\Support\Carbon::parse($enchere->reminder_sent_at)->format('d/m/Y H:i') }}</small>
                          @else
                              <span class="badge bg-secondary">Non</span>
                          @endif
                      </td>
                      <td>
                        <div class="d-flex gap-2 w-100 justify-content-end">
                         <a href="{{ route("admin.nadoautoenchere.edit" , $enchere)}}" class="btn btn-info">Update</a>
@include('admin.partials.deleteBtn', [ "url" =>  route('admin.nadoautoenchere.destroy' , $enchere) ])
  </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                      <tr>
                        <th>Id</th>
                        <th>titre</th>
                        <th>mark</th>
                        <th>modele</th>
                        <th>Entreprise</th>
                        <th>views</th>
                        <th>date</th>
                        <th>Active</th>
                      </tr>
                  </tfoot>
              </table>
          </div>
      </div>
  </section>
</div>
</div>
@endsection
<script>
document.addEventListener('DOMContentLoaded', function () {
    // Curseur Active : active / désactive l'enchère sans recharger la page.
    document.querySelectorAll('.enchere-active-toggle').forEach(function (toggle) {
        toggle.addEventListener('change', function () {
            var id = this.dataset.id;
            var checked = this.checked;
            var self = this;
            fetch('/admin/nadoautoenchere/' + encodeURIComponent(id) + '/toggle-active', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ active: checked ? 1 : 0 })
            })
            .then(function (r) { return r.json(); })
            .then(function (data) {
                if (!data.success) { self.checked = !checked; alert('Échec de la mise à jour.'); }
            })
            .catch(function () { self.checked = !checked; alert('Erreur réseau.'); });
        });
    });
});
</script>
