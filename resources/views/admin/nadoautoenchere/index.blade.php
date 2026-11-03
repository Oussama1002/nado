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
                      <td class="text-center"> @if ($enchere->active == 1) <i class="bi bi-check text-success"></i> @else <i class="bi bi-dash-circle text-danger"></i> @endif </td>
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
    document.querySelectorAll('.verify-toggle').forEach(function (checkbox) {
        checkbox.addEventListener('change', function () {
            fetch("{{ route('admin.annonces.toggleHomepage') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    id: this.dataset.id,
                    is_homepage: this.checked ? 1 : 0
                })
            })
            .then(response => response.json())
            .then(data => {
                if (!data.success) {
                    alert('Failed to update homepage status.');
                }
            })
            .catch(() => alert('Error updating homepage status.'));
        });
    });
});
</script>
