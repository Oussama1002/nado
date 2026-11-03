@extends('admin.layouts.app')

@section('title' , 'Colors | nado.ma')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Couleurs</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List Couleur</h4>
                      <div class="d-flex" style="gap:8px;"><a href="{{ route('admin.export', 'colors') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a><a href="{{ route('admin.colors.create')}}" class="btn btn-primary">New Couleur</a></div>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>id</th>
                                      <th>name</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($colors as $c)
                                  <tr>
                                      <td>{{ $c->id }}</td>
                                      <td>{{ $c->name }}</td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <a href="{{ route("admin.colors.edit" , $c)}}" class="btn btn-info">Update</a>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('admin.colors.destroy' , $c) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>

                            <tfoot>
                                <tr>
                                    <th>id</th>
                                    <th>name</th>
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
