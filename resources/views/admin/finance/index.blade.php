@extends('admin.layouts.app')

@section('title' , 'Finance | nado.ma')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Financement</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List finance</h4>
                      <a href="{{ route('admin.export', 'finances') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
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
                                      <th>ville</th>
                                      <th>Mark</th>
                                      <th>Modele</th>
                                      <th>Année</th>
                                      <th>Situation professionnelle</th>
                                      <th>Crédits en cours</th>
                                      <th>Durée</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($finances as $finance)
                                  <tr>
                                      <td>{{ $finance->id }}</td>
                                      <td>{{ $finance->name }}</td>
                                      <td>{{ $finance->phone }}</td>
                                      <td>{{ $finance->city }}</td>
                                      <td>{{ $finance->mark }}</td>
                                      <td>{{ $finance->modele }}</td>
                                      <td>{{ $finance->year }}</td>
                                      <td>{{ $finance->situationprof }}</td>
                                      <td>{{ $finance->creditsencours }}</td>
                                      <td>{{ $finance->duree }}</td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <a href="{{ route("finances.show" , $finance)}}" class="btn btn-info">Voire </a>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('finances.destroy' , $finance) ])
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
                                    <th>ville</th>
                                    <th>Mark</th>
                                    <th>Modele</th>
                                    <th>Année</th>
                                    <th>Situation professionnelle</th>
                                    <th>Crédits en cours</th>
                                    <th>Durée</th>
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

