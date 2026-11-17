@extends('admin.layouts.app')

@section('title' , $user->exists ? 'Edit User' : 'New User')


@section('content')
<div id="main-content">
<div class="page-heading">
    <h3>Users</h3>
</div>
<div class="page-content">
    <section class="row">
    <div class="row match-height">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4 class="card-title">Add User</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{ route($user->exists ? 'admin.users.update' : 'admin.users.store' , $user) }}"  method="post" class="form form-vertical">
                            @csrf
                            @method($user->exists ? 'PUT' : 'POST')
                            <div class="form-body">
                                <div class="row">
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Name" ,"name" => 'name', "value" => $user->name, "hasicon" => "person" ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Email" , "type" => "email", "name" => 'email', "value" => $user->email, "hasicon" => "envelope" ])
                                    </div>
                                    <div class="col-12 col-md-12 my-2">
                                        @include('admin.partials.select', ["class" => 'col', "label" => "Role" ,"name" => 'role', "value" => $user->role, "data" => ["admin" => "Admin", "member" => "Member"], "hasicon" => "building" ])
                                    </div>

                                    {{-- Accès aux modules (ignoré pour un admin, qui a tout) --}}
                                    <div class="col-12 my-2">
                                        <label class="form-label fw-bold d-block mb-1">Accès aux modules</label>
                                        <small class="text-muted d-block mb-2">
                                            Un utilisateur « Admin » a accès à tout. Pour un « Member », cochez
                                            pour chaque module les actions autorisées :
                                            <b>Explorer</b> (voir), <b>Ajouter</b>, <b>Modifier</b>, <b>Supprimer</b>.
                                        </small>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered align-middle mb-0" id="modules-access-table">
                                                <thead>
                                                    <tr>
                                                        <th style="min-width:200px;">Module</th>
                                                        @foreach(($abilities ?? []) as $abKey => $abLabel)
                                                            <th class="text-center">{{ $abLabel }}</th>
                                                        @endforeach
                                                        <th class="text-center">Tout</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    @foreach(($modules ?? []) as $key => $label)
                                                        @php $rowAllowed = old('abilities.'.$key, $userAbilities[$key] ?? []); @endphp
                                                        <tr>
                                                            <td>{{ $label }}</td>
                                                            @foreach(($abilities ?? []) as $abKey => $abLabel)
                                                                <td class="text-center">
                                                                    <input class="form-check-input mod-ability" type="checkbox"
                                                                           name="abilities[{{ $key }}][]" value="{{ $abKey }}"
                                                                           id="ab_{{ $key }}_{{ $abKey }}"
                                                                           data-module="{{ $key }}"
                                                                           @checked(in_array($abKey, (array) $rowAllowed, true))>
                                                                </td>
                                                            @endforeach
                                                            <td class="text-center">
                                                                <input class="form-check-input mod-ability-all" type="checkbox"
                                                                       data-module="{{ $key }}"
                                                                       aria-label="Tout cocher pour {{ $label }}">
                                                            </td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Password" , "type" => "password", "name" => 'password', "hasicon" => "lock" ])
                                    </div>
                                    <div class="col-12 col-md-6 my-2">
                                        @include('admin.partials.input', ["class" => 'col', "label" => "Confirm Password", "type" => "password", "name" => 'password_confirmation', "hasicon" => "lock" ])
                                    </div>
                                    <div class="col-12 d-flex justify-content-center">
                                        <button type="submit" class="btn btn-primary me-1 mb-1">
                                            @if ($user->exists)
                                                Update
                                            @else
                                                Add new user
                                            @endif
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</div>
</div>

<script>
(function () {
    var table = document.getElementById('modules-access-table');
    if (! table) { return; }

    function rowBoxes(module) {
        return table.querySelectorAll('.mod-ability[data-module="' + CSS.escape(module) + '"]');
    }
    function syncAll(module) {
        var boxes = rowBoxes(module);
        var all   = table.querySelector('.mod-ability-all[data-module="' + CSS.escape(module) + '"]');
        if (! all) { return; }
        var checked = 0;
        boxes.forEach(function (b) { if (b.checked) { checked++; } });
        all.checked = (checked === boxes.length && boxes.length > 0);
        all.indeterminate = (checked > 0 && checked < boxes.length);
    }

    // État initial des cases « Tout ».
    table.querySelectorAll('.mod-ability-all').forEach(function (all) {
        syncAll(all.getAttribute('data-module'));
    });

    table.addEventListener('change', function (e) {
        var el = e.target;
        if (el.classList.contains('mod-ability-all')) {
            var module = el.getAttribute('data-module');
            rowBoxes(module).forEach(function (b) { b.checked = el.checked; });
            el.indeterminate = false;
        } else if (el.classList.contains('mod-ability')) {
            syncAll(el.getAttribute('data-module'));
        }
    });
})();
</script>
@endsection
