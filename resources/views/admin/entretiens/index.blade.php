@extends('admin.layouts.app')

@section('title' , 'Entretiens | nado.ma')

@section('content')
<div id="main-content">
<div class="page-heading">
@if(session('success'))
    <div class="bg-green-100 text-green-800 p-4 mb-6 rounded border border-green-300 shadow-sm">
        {{ session('success') }}
    </div>
@endif

@if($errors->any())
    <div class="bg-red-100 text-red-800 p-4 mb-6 rounded border border-red-300 shadow-sm">
        <ul class="list-disc list-inside">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

    <h3>Entretiens</h3>
</div>
<div class="page-content">
    <section class="row">
        <div class="row" id="table-striped">
            <div class="col-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <h4 class="card-title">Liste des entretiens</h4>
                        @if(admin_can('annonces','add'))<a href="{{ route('admin.entretiens.create', ['annonce' => 1]) }}" class="btn btn-primary">Nouvel entretien</a>@endif
                        {{-- Remplacez '1' par un ID dynamique selon votre usage --}}
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                        <div class="table-responsive mx-4 my-2">
                            <table class="table table-striped mb-0 display" id="example">
                                <thead>
                                    <tr>
                                        <th>Annonce</th>
                                        <th>Date</th>
                                        <th>Révision</th>
                                        <th>Entretien</th>
                                        <th>Réparation</th>
                                        <th>Carrosserie</th>
                                        <th>Professionnels</th>
                                        <th class="d-flex justify-content-end">Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($entretiens as $e)
                                    <tr>
                                        <td>{{ $e->annonce->titre ?? '—' }}</td>
                                        <td>{{ $e->date }}</td>
                                        <td>{{ $e->revision }}</td>
                                        <td>{{ $e->entretien }}</td>
                                        <td>{{ $e->reparation }}</td>
                                        <td>{{ $e->carrosserie }}</td>
                                        <td>{{ $e->professionnels }}</td>
                                        <td>
                                            <div class="d-flex gap-2 w-100 justify-content-end">
                                            @if(admin_can('annonces','modify'))<a href="{{ route('admin.entretiens.edit', $e->id) }}" class="btn btn-info">Modifier</a>@endif

@if(admin_can('annonces','delete'))
<form method="POST" action="{{ route('admin.entretiens.destroy', $e->id) }}" onsubmit="return confirm('Confirmer la suppression ?')" class="inline">
    @csrf
    @method('DELETE')
    <button type="submit" class="btn btn-danger">Supprimer</button>
</form>
@endif

                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th>Annonce</th>
                                        <th>Date</th>
                                        <th>Révision</th>
                                        <th>Entretien</th>
                                        <th>Réparation</th>
                                        <th>Carrosserie</th>
                                        <th>Professionnels</th>
                                        <th>Actions</th>
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
    // Ajoutez ici tout JS nécessaire (ex: modals, tooltips, etc.)
</script>
@endsection
