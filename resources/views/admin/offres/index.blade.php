@extends('admin.layouts.app')

@section('title' , 'Offre | nado.ma')


@section('link')
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/css/bootstrap-editable.css">
  <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.1/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
  <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.8.2/css/all.min.css">
    <style id="compiled-css" type="text/css">
  .glyphicon-ok::before {
    content: "\f00c";
  }

  .glyphicon-remove::before {
    content: "\f00d";
  }

  .glyphicon {
    font-family: 'Font Awesome 5 Free';
    font-weight: 900;
    font-style: normal;
  }

    </style>
@endsection
@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Offre</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">List offre</h4>
                      <a href="{{ route('admin.export', 'offres') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>#</th>
                                      <th>Phone</th>
                                      <th>Offre</th>
                                      <th>Voiture</th>
                                      <th>Prix</th>
                                      <th>Date</th>
                                      <th>Status</th>
                                      <th>Commentaire</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($offres as $offre)
                                  <tr>
                                      <td>{{ $offre->id }}</td>
                                      <td>{{ $offre->phone }}</td>
                                      <td>{{ $offre->offre }}</td>
                                      <td>{{ $offre->car }}</td>
                                      <td>{{ $offre->prix }}</td>
                                      <td>{{ $offre->created_at }}</td>
                                      <td class="align-middle">
										<a href="#" id="status" data-type="select" data-placement="right" data-title="Enter status" class="editable editable-click status" data-pk="{{ $offre->id }}">
										    {{ $offre->status }}
										</a>
								      </td>
								      <td class="align-middle">
										<a href="#" id="status" data-type="text" data-placement="right" data-title="Enter comment" class="editable editable-click comment" data-pk="{{ $offre->id }}">
										    {{ $offre->comment }}
										</a>
								      </td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          @include('admin.partials.deleteBtn', ["url" =>  route('admin.offres.destroy' , $offre) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>

                            <tfoot>
                                <tr>
                                    <th>#</th>
                                    <th>Phone</th>
                                    <th>Offre</th>
                                    <th>Voiture</th>
                                    <th>Prix</th>
                                    <th>Date</th>
                                    <th>Status</th>
                                    <th>Commentaire</th>
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
@section('script')
<script>
	$(document).ready(function() {

		$.fn.editable.defaults.mode = 'inline';
		var url = "{{route('admin.offres.updatesc')}}";
		$.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': '{{csrf_token()}}'
            }
        });

        $('.status').editable({
            url: url,
            type: 'select',
            pk: 1,
            name: 'status',
			source: [{
				value: 'interesse',
				text: 'Intéressé'
			},
			 {
				 value: 'qualifie',
				 text: 'Qualifié'
			 },
			 {
				 value: 'supprime',
				 text: 'Supprimé'
			 },
			 {
				 value: 'se rappeler',
				 text: 'Se rappeler'
			 }
			 ]
        }); 
        $('.comment').editable({
            url: url,
            type: 'text',
            pk: 1,
            name: 'comment',
        });

		

	});
</script>
@endsection

