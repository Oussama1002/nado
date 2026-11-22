@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi bi-person-badge text-success"></i> Demande expert #{{ $item->id }}</h3>
    <a href="{{ route('admin.demandeexperts.index') }}" class="btn btn-sm btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Retour
    </a>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif

<div class="page-content">
<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <dl class="row mb-0">
                    <dt class="col-sm-3">Date</dt>
                    <dd class="col-sm-9">{{ $item->created_at?->format('d/m/Y H:i') }}</dd>

                    <dt class="col-sm-3">Nom</dt>
                    <dd class="col-sm-9">{{ $item->name }}</dd>

                    <dt class="col-sm-3">Téléphone</dt>
                    <dd class="col-sm-9"><a href="tel:{{ $item->phone }}">{{ $item->phone }}</a></dd>

                    <dt class="col-sm-3">Email</dt>
                    <dd class="col-sm-9"><a href="mailto:{{ $item->email }}">{{ $item->email }}</a></dd>

                    <dt class="col-sm-3">Ville</dt>
                    <dd class="col-sm-9">{{ $item->ville ?: '—' }}</dd>

                    <dt class="col-sm-3">Sujet</dt>
                    <dd class="col-sm-9">{{ $item->subject ?: '—' }}</dd>

                    <dt class="col-sm-3">Source</dt>
                    <dd class="col-sm-9"><span class="badge bg-info">{{ $item->source }}</span></dd>

                    <dt class="col-sm-3">Message</dt>
                    <dd class="col-sm-9" style="white-space: pre-wrap;">{{ $item->message ?: '—' }}</dd>
                </dl>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">Changer le statut</div>
            <div class="card-body">
                <form method="POST" action="{{ route('admin.demandeexperts.status', $item) }}">
                    @csrf
                    <select name="status" class="form-select mb-3">
                        <option value="new"       @selected($item->status === 'new')>Nouvelle</option>
                        <option value="contacted" @selected($item->status === 'contacted')>Contactée</option>
                        <option value="done"      @selected($item->status === 'done')>Terminée</option>
                        <option value="spam"      @selected($item->status === 'spam')>Spam</option>
                    </select>
                    <button class="btn btn-primary w-100"><i class="bi bi-check-lg"></i> Enregistrer</button>
                </form>
                <hr>
                <form method="POST" action="{{ route('admin.demandeexperts.destroy', $item) }}"
                      onsubmit="return confirm('Supprimer définitivement ?');">
                    @csrf @method('DELETE')
                    <button class="btn btn-outline-danger w-100"><i class="bi bi-trash"></i> Supprimer</button>
                </form>
            </div>
        </div>
    </div>
</div>
</div>
@endsection
