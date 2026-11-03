@extends('admin.layouts.app')

@section('title' , 'Videos Ads | nado.ma')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Videos Ads</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List Videos Ads</h4>
                      <div class="d-flex" style="gap:8px;"><a href="{{ route('admin.export', 'videoads') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a><a href="{{ route('admin.videoads.create')}}" class="btn btn-primary">New Videos Ads</a></div>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>title</th>
                                      <th>video key youtube</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($videoads as $av)
                                  <tr>
                                      <td>{{ $av->id }}</td>
                                      <td>{{ $av->title }}</td>
                                      <td>{{ $av->video_key }}</td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <a href="{{ route("admin.videoads.edit" , $av)}}" class="btn btn-info">Update</a>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('admin.videoads.destroy' , $av) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>

                            <tfoot>
                                <tr>
                                    <th>title</th>
                                    <th>video key youtube</th>
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
