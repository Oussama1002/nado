@php
    $btn_text  ??= 'Delete';
    $class ??= null;
    $url ??= '/';
    // Optionnel : si un module est fourni, masquer le bouton pour un « member »
    // qui n'a pas l'action « Supprimer » (admin_can renvoie true pour un admin).
    $module ??= null;
@endphp

@if($module === null || admin_can($module, 'delete'))
<a @class(['btn-del-row btn btn-danger', $class ])  data-bs-toggle="modal" data-bs-target="#md-del-row" data-url="{{ $url }}">
    {{ $btn_text }}
</a>
@endif
