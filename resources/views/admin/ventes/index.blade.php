@extends('admin.layouts.app')

@section('title' , 'Vente | nado.ma')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Vente</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List vente</h4>
                      <a href="{{ route('admin.export', 'ventes') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Name</th>
                                      <th>Phone</th>
                                      <th>Mark</th>
                                      <th>Modele</th>
                                      <th>ville</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($ventes as $vente)
                                  <tr>
                                      <td>{{ $vente->id }}</td>
                                      <td>{{ $vente->name }}</td>
                                      <td>{{ $vente->phone }}</td>
                                      <td>{{ $vente->mark }}</td>
                                      <td>{{ $vente->modele }}</td>
                                      <td>{{ $vente->city }}</td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <a href="{{ route("admin.ventes.show" , $vente)}}" class="btn btn-info">Voire </a>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('admin.ventes.destroy' , $vente) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>

                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Phone</th>
                                    <th>Mark</th>
                                    <th>Modele</th>
                                    <th>ville</th>
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


@endsection

