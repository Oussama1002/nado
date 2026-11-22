@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi bi-person-badge text-success"></i> Demandes expert</h3>
    <div class="text-muted small">{{ $total }} demande(s) au total</div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="page-content">
<div class="card">
<div class="card-body">

    {{-- Filtres statut --}}
    <div class="d-flex flex-wrap gap-2 mb-3">
        <a href="{{ route('admin.demandeexperts.index') }}"
           class="btn btn-sm {{ $filter === 'all' ? 'btn-dark' : 'btn-outline-dark' }}">
            Tout <span class="badge bg-light text-dark ms-1">{{ $total }}</span>
        </a>
        @foreach(['new' => ['Nouvelles', 'danger'], 'contacted' => ['Contactées', 'warning'], 'done' => ['Terminées', 'success'], 'spam' => ['Spam', 'secondary']] as $k => $meta)
            <a href="{{ route('admin.demandeexperts.index', ['status' => $k]) }}"
               class="btn btn-sm {{ $filter === $k ? 'btn-'.$meta[1] : 'btn-outline-'.$meta[1] }}">
                {{ $meta[0] }}
                <span class="badge bg-light text-dark ms-1">{{ $counts[$k] ?? 0 }}</span>
            </a>
        @endforeach
    </div>

    @if($items->isEmpty())
        <div class="text-center text-muted py-5">
            <i class="bi bi-person-badge fs-1 d-block mb-2"></i>
            Aucune demande pour ce filtre.
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Date</th>
                        <th>Nom</th>
                        <th>Téléphone</th>
                        <th>Email</th>
                        <th>Ville</th>
                        <th>Sujet</th>
                        <th>Statut</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($items as $it)
                        <tr>
                            <td>{{ $it->id }}</td>
                            <td class="small">{{ $it->created_at?->format('d/m/Y H:i') }}</td>
                            <td class="fw-semibold">{{ $it->name }}</td>
                            <td><a href="tel:{{ $it->phone }}">{{ $it->phone }}</a></td>
                            <td><a href="mailto:{{ $it->email }}">{{ $it->email }}</a></td>
                            <td>{{ $it->ville ?: '—' }}</td>
                            <td class="small text-muted">{{ \Illuminate\Support\Str::limit($it->subject, 40) }}</td>
                            <td>
                                @php
                                    $badge = match($it->status) {
                                        'new'       => 'danger',
                                        'contacted' => 'warning',
                                        'done'      => 'success',
                                        'spam'      => 'secondary',
                                        default     => 'light',
                                    };
                                @endphp
                                <span class="badge bg-{{ $badge }}">{{ ucfirst($it->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('admin.demandeexperts.show', $it) }}" class="btn btn-sm btn-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <form action="{{ route('admin.demandeexperts.destroy', $it) }}" method="POST" class="d-inline"
                                      onsubmit="return confirm('Supprimer cette demande ?');">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">{{ $items->links() }}</div>
    @endif

</div>
</div>
</div>
@endsection
