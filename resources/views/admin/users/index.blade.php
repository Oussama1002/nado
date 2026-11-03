@extends('admin.layouts.app')

@section('title' , 'Users | nado.ma')


@section('content')
<div id="main-content">
    <div class="page-heading">
        <h3>Users</h3>
    </div>
    <div class="page-content">
    <section class="section">
      <div class="card">
        <div class="card-header d-flex justify-content-between">
            <h4 class="card-title">List Users</h4>
            <div class="d-flex" style="gap:8px;"><a href="{{ route('admin.export', 'users') }}" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Export Excel</a><a href="{{ route('admin.users.create')}}" class="btn btn-primary">New User</a></div>
        </div>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-striped mb-0 display" id="example">
                  <thead>
                      <tr>
                          <th>#</th>
                          <th>Name</th>
                          <th>Email</th>
                          <th>role</th>
                          <th class="d-flex justify-content-end">Action</th>
                      </tr>
                  </thead>
                  <tbody>
                    @foreach($users as $user)
                    <tr>
                      <th scope="row">{{ $user->id; }}</th>
                      <td>{{ $user->name; }}</td>
                      <td>{{ $user->email; }}</td>
                      <td>{{ $user->role; }}</td>
                      <td>
                        <div class="d-flex gap-2 w-100 justify-content-end">
                          <a href="{{ route("admin.users.edit" , $user)}}" class="btn btn-info">Update</a>
                          @include('admin.partials.deleteBtn', [  "url" =>  route('admin.users.destroy' , $user) ])
                        </div>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                  <tfoot>
                      <tr>
                        <th>Id</th>
                        <th>Name</th>
                        <th>Email</th>
                      </tr>
                  </tfoot>
              </table>
          </div>
      </div>
  </section>
</div>
</div>
@endsection
