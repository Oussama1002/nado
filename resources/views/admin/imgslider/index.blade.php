@extends('admin.layouts.app')

@section('title' , 'Image slider | nado.ma')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Image slider</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List Image slider</h4>
                      <div class="d-flex" style="gap:8px;"><a href="{{ route('admin.export', 'imgslider') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a><a href="{{ route('admin.imgslider.create')}}" class="btn btn-primary">New Image slider</a></div>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>Name</th>
                                      <th>Link</th>
                                      <th>Image</th>
                                      <th>Active</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($imgslider as $av)
                                  <tr>
                                      <td>{{ $av->name }}</td>
                                      <td>{{ $av->link }}</td>
                                      <td width="10%">
                                        <a href="#" data-bs-toggle="modal" data-bs-target="#galleryModal">
                                            <img class="activeimgnews" src="{{ asset('storage/imgslider/'.$av->image) }}" width="100%" data-bs-target="#Gallerycarousel" >
                                        </a>
                                      </td>
                                      <td class="text-center"> @if ($av->active == 1) <i class="bi bi-check text-success"></i> @else <i class="bi bi-dash-circle text-danger"></i> @endif </td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <a href="{{ route("admin.imgslider.edit" , $av)}}" class="btn btn-info">Update</a>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('admin.imgslider.destroy' , $av) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>

                            <tfoot>
                                <tr>
                                    <th>Name</th>
                                    <th>Link</th>
                                    <th>Image</th>
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
<div class="modal fade" id="galleryModal" tabindex="-1" role="dialog"
aria-labelledby="galleryModalTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-dialog-centered"
role="document">
        <div class="modal-content">
            <div class="modal-body">
                <div id="Gallerycarousel" class="carousel slide carousel-fade" data-bs-ride="carousel">
                    <div class="carousel-inner">
                        <div class="carousel-item active">
                            <img class="d-block w-100 imgCarouselNews" src="">
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bx bx-x d-block d-sm-none"></i>
                    <span class="d-none d-sm-block">Close</span>
                </button>
            </div>
        </div>
    </div>
</div>

@endsection
@section('script')
    <script>

        document.querySelectorAll('.activeimgnews').forEach(el => el.addEventListener('click', event => {
            document.querySelector('.carousel-item .imgCarouselNews').setAttribute("src", event.target.getAttribute("src"));
        }));

    </script>
@endsection
