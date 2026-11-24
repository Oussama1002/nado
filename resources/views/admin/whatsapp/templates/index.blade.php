@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi bi-file-earmark-text text-success"></i> WhatsApp — Modèles de messages</h3>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-sm btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Boîte de réception
        </a>
        <a href="{{ route('admin.whatsapp.templates.create') }}" class="btn btn-success btn-sm">
            <i class="bi bi-plus-lg"></i> Nouveau template
        </a>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="page-content">
<div class="card">
<div class="card-body">

    @if(! $ok)
        <div class="alert alert-warning">
            <i class="bi bi-exclamation-triangle"></i>
            Impossible de charger les templates Meta : {{ $error }}
            <div class="small mt-2">
                Vérifiez que « Meta — WhatsApp Business Account ID » et « Meta — Access Token » sont renseignés dans
                <a href="{{ route('admin.settings.group', 'whatsapp') }}">Paramètres → WhatsApp OTP</a>, et que le token
                a bien la permission <code>whatsapp_business_management</code>.
            </div>
        </div>
    @endif

    @if(count($drafts) === 0 && count($templates) === 0 && $ok)
        <div class="text-center text-muted py-4">
            <i class="bi bi-file-earmark-text fs-1 d-block mb-2"></i>
            Aucun brouillon local ni template sur ce compte WhatsApp Business.<br>
            <small>Créez un brouillon puis soumettez-le à Meta depuis la liste.</small>
        </div>
    @else
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Nom</th>
                        <th>Catégorie</th>
                        <th>Langue</th>
                        <th>Statut</th>
                        <th>Corps</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- ─── Brouillons locaux (statut DRAFT) ─── --}}
                    @foreach($drafts as $d)
                        <tr style="background:#fff8e1;">
                            <td><code>{{ $d->name }}</code></td>
                            <td><span class="badge bg-info">{{ $d->category }}</span></td>
                            <td>{{ $d->language }}</td>
                            <td><span class="badge bg-secondary"><i class="bi bi-pencil-square"></i> BROUILLON</span></td>
                            <td class="small text-muted" style="max-width: 400px;">
                                <span style="white-space: pre-wrap;">{{ \Illuminate\Support\Str::limit($d->body, 200) }}</span>
                                @if($d->footer)
                                    <div class="mt-1 fst-italic">{{ $d->footer }}</div>
                                @endif
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.whatsapp.templates.drafts.submit', $d) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Soumettre « {{ $d->name }} · {{ $d->language }} » à Meta pour approbation ?');">
                                    @csrf
                                    <button type="submit" class="btn btn-sm btn-success" title="Soumettre à Meta">
                                        <i class="bi bi-send"></i> Soumettre à Meta
                                    </button>
                                </form>
                                <form method="POST" action="{{ route('admin.whatsapp.templates.drafts.destroy', $d) }}"
                                      class="d-inline"
                                      onsubmit="return confirm('Supprimer ce brouillon ?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" title="Supprimer le brouillon">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach

                    {{-- ─── Templates Meta (APPROVED / PENDING / REJECTED) ─── --}}
                    @foreach($templates as $t)
                        @php
                            $body = collect($t['components'] ?? [])->firstWhere('type', 'BODY')['text'] ?? '';
                            $statusClass = match($t['status'] ?? '') {
                                'APPROVED'            => 'bg-success',
                                'PENDING'             => 'bg-warning text-dark',
                                'REJECTED', 'DISABLED', 'PAUSED' => 'bg-danger',
                                default               => 'bg-secondary',
                            };
                        @endphp
                        <tr>
                            <td><code>{{ $t['name'] }}</code></td>
                            <td><span class="badge bg-info">{{ $t['category'] ?? '—' }}</span></td>
                            <td>{{ $t['language'] ?? '—' }}</td>
                            <td><span class="badge {{ $statusClass }}">{{ $t['status'] ?? '—' }}</span></td>
                            <td class="small text-muted" style="max-width: 400px;">
                                <span style="white-space: pre-wrap;">{{ \Illuminate\Support\Str::limit($body, 200) }}</span>
                            </td>
                            <td class="text-end">
                                <form method="POST" action="{{ route('admin.whatsapp.templates.destroy', $t['name']) }}"
                                      onsubmit="return confirm('Supprimer le template « {{ $t['name'] }} » (toutes langues confondues) ?');"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif

</div>
</div>
</div>
@endsection
