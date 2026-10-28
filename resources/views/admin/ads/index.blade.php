@extends('admin.layouts.app')

@section('title' , 'Annonces | nado.ma')


@section('content')
<div id="main-content">
    <div class="page-heading">
        <h3>Annonces</h3>
    </div>
    <div class="page-content">
    <section class="section">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">List Annonces</h4>
            <div class="d-flex gap-2">
                <a href="{{ route('admin.annonces.archive') }}" class="btn btn-secondary"><i class="bi bi-archive"></i> Archive</a>
                <a href="{{ route('admin.annonces.create')}}" class="btn btn-primary float-right">New Annonce</a>
            </div>
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
                          <th>views</th>
                          <th>Home Page</th>

                          <th>date</th>
                          <th>Active</th>
                          <th class="d-flex justify-content-end">Action</th>
                      </tr>
                  </thead>
                  <tbody>
                    @foreach($ads as $ad)
                    <tr>
                      <th scope="row">{{ $ad->id; }}</th>
                      <td>{{ $ad->titre; }}</td>
                     <td>{{ $ad->brand ? $ad->brand->name : '-' }}</td>
<td>{{ $ad->modeleRelation ? $ad->modeleRelation->name : '-' }}</td>

                      <td>{{ $ad->views; }}</td>
  <td>
    <input type="checkbox" 
           class="verify-toggle" 
           data-id="{{ $ad->id }}"
           {{ $ad->is_homepage ? 'checked' : '' }}>
</td>
                      <td>{{ $ad->created_at; }}</td>
                      <td class="text-center"> @if ($ad->active == 1) <i class="bi bi-check text-success"></i> @else <i class="bi bi-dash-circle text-danger"></i> @endif </td>
                      <td>
                        <div class="d-flex gap-2 w-100 justify-content-end">
                          <a href="{{ route("admin.annonces.edit" , $ad)}}" class="btn btn-info">Update</a>
                          @include('admin.partials.deleteBtn', [  "url" =>  route('admin.annonces.destroy' , $ad) ])
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
