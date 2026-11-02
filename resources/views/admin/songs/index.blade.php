@extends('admin.layouts.app')

@section('title' , 'Nado Radio | nado.ma')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Nado Radio</h3>
</div>
<div class="page-content">
    <section class="row">
      <div class="row" id="table-striped">
          <div class="col-12">
              <div class="card">
                  <div class="card-header d-flex justify-content-between">
                      <h4 class="card-title">Titres Nado Radio</h4>
                      <a href="{{ route('admin.songs.create')}}" class="btn btn-primary float-right">Nouveau titre</a>
                  </div>
                  <div class="card-content">
                      <div class="card-body">
                      <div class="table-responsive mx-4 my-2">
                          <table class="table table-striped mb-0 display" id="example">
                              <thead>
                                  <tr>
                                      <th>Ordre</th>
                                      <th>Pochette</th>
                                      <th>Titre</th>
                                      <th>Artiste</th>
                                      <th>Durée</th>
                                      <th>Audio</th>
                                      <th>Actif</th>
                                      <th class="d-flex justify-content-end">Action</th>
                                  </tr>
                              </thead>
                              <tbody>
                                @foreach ($songs as $song)
                                  <tr>
                                      <td>{{ $song->sort_order }}</td>
                                      <td width="8%">
                                        @if ($song->cover_path)
                                            <img src="{{ asset('storage/'.$song->cover_path) }}" width="100%" style="border-radius:8px;">
                                        @else
                                            <span class="text-muted"><i class="bi bi-music-note-beamed"></i></span>
                                        @endif
                                      </td>
                                      <td>{{ $song->title }}</td>
                                      <td>{{ $song->artist }}</td>
                                      <td>{{ $song->duration }}</td>
                                      <td width="20%">
                                        <audio controls preload="none" style="width:100%;height:36px;">
                                            <source src="{{ asset('storage/'.$song->file_path) }}">
                                        </audio>
                                      </td>
                                      <td class="text-center"> @if ($song->active) <i class="bi bi-check text-success"></i> @else <i class="bi bi-dash-circle text-danger"></i> @endif </td>
                                      <td>
                                        <div class="d-flex gap-2 w-100 justify-content-end">
                                          <a href="{{ route("admin.songs.edit" , $song)}}" class="btn btn-info">Modifier</a>
                                          @include('admin.partials.deleteBtn', ["url" =>  route('admin.songs.destroy' , $song) ])
                                        </div>
                                      </td>
                                  </tr>
                                @endforeach
                              </tbody>
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
