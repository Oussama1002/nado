@extends('admin.layouts.app')

@section('content')
<div class="page-heading">
    <h3 class="mb-0"><i class="bi bi-plug-fill text-primary"></i> Intégrations</h3>
    <p class="text-muted mb-0">Configuration Meta &amp; WhatsApp Business Cloud API.</p>
</div>

@if(session('success'))<div class="alert alert-success mt-3">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger mt-3">{{ session('error') }}</div>@endif
@if($errors->any())
    <div class="alert alert-danger mt-3"><ul class="mb-0">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul></div>
@endif

<div class="page-content">

{{-- ─────────────────── Numéros WhatsApp ─────────────────── --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-white">
        <div>
            <h5 class="mb-0"><i class="bi bi-whatsapp text-success"></i> Numéros WhatsApp</h5>
            <small class="text-muted">Importez vos numéros depuis Meta, ou ajoutez-les manuellement.</small>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addNumberModal">
                <i class="bi bi-plus-lg"></i> Ajouter manuellement
            </button>
            <form method="POST" action="{{ route('admin.integrations.numbers.import') }}" class="d-inline">
                @csrf
                <button class="btn btn-success btn-sm">
                    <i class="bi bi-cloud-arrow-down"></i> Importer depuis Meta
                </button>
            </form>
        </div>
    </div>
    <div class="card-body p-0">
        @if($numbers->isEmpty())
            <div class="p-4 text-center text-muted">
                <i class="bi bi-telephone fs-2 d-block mb-2"></i>
                Aucun numéro. Ajoutez-en un manuellement ou importez depuis votre WABA.
            </div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($numbers as $n)
                    <li class="list-group-item d-flex justify-content-between align-items-center flex-wrap gap-2">
                        <div>
                            <div class="fw-semibold">
                                <i class="bi bi-whatsapp text-success"></i>
                                {{ $n->display_number }}
                                @if($n->verified_name) · <span class="text-muted">{{ $n->verified_name }}</span>@endif
                                @if($n->is_default)<span class="badge bg-primary ms-2">Par défaut</span>@endif
                                @if($n->quality_rating)
                                    <span class="badge bg-{{ $n->quality_rating === 'GREEN' ? 'success' : ($n->quality_rating === 'YELLOW' ? 'warning text-dark' : 'danger') }} ms-1">
                                        {{ $n->quality_rating }}
                                    </span>
                                @endif
                            </div>
                            <div class="small text-muted">Phone ID : <code>{{ $n->phone_id }}</code>
                                @if($n->label) · <em>{{ $n->label }}</em>@endif
                            </div>
                        </div>
                        <div class="d-flex gap-2">
                            @if(! $n->is_default)
                                <form method="POST" action="{{ route('admin.integrations.numbers.default', $n) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-star"></i> Par défaut</button>
                                </form>
                            @endif
                            <form method="POST" action="{{ route('admin.integrations.numbers.destroy', $n) }}"
                                  onsubmit="return confirm('Supprimer ce numéro ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- ─────────────────── Modèles de messages ─────────────────── --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-white">
        <div>
            <h5 class="mb-0"><i class="bi bi-file-earmark-text text-primary"></i> Modèles de messages</h5>
            <small class="text-muted">Modèles WhatsApp approuvés par Meta — nécessaires hors fenêtre 24h.</small>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-secondary btn-sm">
                <i class="bi bi-arrow-clockwise"></i> Actualiser
            </a>
            <a href="{{ route('admin.whatsapp.templates.create') }}" class="btn btn-primary btn-sm">
                <i class="bi bi-plus-lg"></i> Nouveau modèle
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        @if($templatesError)
            <div class="alert alert-warning m-3 mb-0 small">
                <i class="bi bi-exclamation-triangle"></i> {{ $templatesError }}
            </div>
        @elseif(empty($templates))
            <div class="p-4 text-center text-muted small">Aucun modèle sur votre WABA.</div>
        @else
            <ul class="list-group list-group-flush">
                @foreach($templates as $t)
                    @php
                        $body = collect($t['components'] ?? [])->firstWhere('type', 'BODY')['text'] ?? '';
                        $varCount = preg_match_all('/\{\{(\d+)\}\}/', $body, $m) ? max($m[1]) : 0;
                        $statusClass = match($t['status'] ?? '') {
                            'APPROVED' => 'bg-success',
                            'PENDING'  => 'bg-warning text-dark',
                            'REJECTED', 'DISABLED', 'PAUSED' => 'bg-danger',
                            default    => 'bg-secondary',
                        };
                    @endphp
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between align-items-start gap-3">
                            <div class="flex-grow-1 min-w-0">
                                <div class="d-flex flex-wrap align-items-center gap-2 mb-1">
                                    <code class="fw-semibold">{{ $t['name'] }}</code>
                                    <span class="badge {{ $statusClass }}">{{ $t['status'] ?? '—' }}</span>
                                    <span class="badge bg-info">{{ $t['category'] ?? '—' }}</span>
                                    <span class="badge bg-light text-dark border">{{ $t['language'] ?? '—' }}</span>
                                    @if($varCount > 0)
                                        <span class="badge bg-secondary">{{ $varCount }} variable(s)</span>
                                    @endif
                                </div>
                                <div class="text-muted small" style="white-space: pre-wrap;">{{ \Illuminate\Support\Str::limit($body, 220) }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.whatsapp.templates.destroy', $t['name']) }}"
                                  onsubmit="return confirm('Supprimer le modèle « {{ $t['name'] }} » ?');">
                                @csrf @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                            </form>
                        </div>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>

{{-- ─────────────────── Connexion WhatsApp ─────────────────── --}}
<div class="card mb-4 shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center bg-white">
        <div>
            <h5 class="mb-0"><i class="bi bi-key-fill text-warning"></i> Connexion WhatsApp</h5>
            <small class="text-muted">Jeton API et paramètres par défaut — utilisés si un numéro n'a pas son propre jeton.</small>
        </div>
        <form method="POST" action="{{ route('admin.integrations.whatsapp.test') }}" class="d-inline">
            @csrf
            <button class="btn btn-outline-success btn-sm">
                <i class="bi bi-lightning-charge"></i> Tester WhatsApp
            </button>
        </form>
    </div>
    <div class="card-body">
        <form method="POST" action="{{ route('admin.integrations.whatsapp.save') }}">
            @csrf

            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Fournisseur</label>
                    <input type="text" class="form-control" name="provider"
                           value="{{ settings('whatsapp.provider', 'meta') }}" readonly>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold">URL de base API</label>
                    <input type="text" class="form-control" value="https://graph.facebook.com/{{ config('services.whatsapp.meta_api_version', 'v21.0') }}" readonly>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Business Account ID (WABA)</label>
                    <input type="text" class="form-control" name="meta_waba_id"
                           value="{{ settings('whatsapp.meta_waba_id') }}"
                           placeholder="ex. 1499541424751562">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Phone ID (défaut)</label>
                    <input type="text" class="form-control" name="meta_phone_number_id"
                           value="{{ settings('whatsapp.meta_phone_number_id') }}"
                           placeholder="ex. 1219470361254773">
                    <div class="form-text">Utilisé quand aucun numéro de la liste n'est marqué « Par défaut ».</div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Jeton API</label>
                    <input type="password" class="form-control" name="meta_access_token"
                           placeholder="•••••• (laisser vide pour ne pas changer)">
                    <div class="form-text">Jeton permanent avec les permissions <code>whatsapp_business_messaging</code> et <code>whatsapp_business_management</code>.</div>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Nom du template OTP</label>
                    <input type="text" class="form-control" name="meta_template_name"
                           value="{{ settings('whatsapp.meta_template_name', 'otp_verification') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Langue du template OTP</label>
                    <input type="text" class="form-control" name="meta_template_lang"
                           value="{{ settings('whatsapp.meta_template_lang', 'fr') }}" placeholder="fr">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold">Jeton de vérification webhook</label>
                    <input type="password" class="form-control" name="webhook_verify_token"
                           placeholder="•••••• (laisser vide pour ne pas changer)">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">App Secret (signature webhook)</label>
                    <input type="password" class="form-control" name="webhook_app_secret"
                           placeholder="•••••• (laisser vide pour ne pas changer)">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">URL webhook (référence)</label>
                    <div class="input-group">
                        <input type="text" class="form-control" value="{{ $webhookUrl }}" readonly id="webhookUrl">
                        <button type="button" class="btn btn-outline-secondary" onclick="navigator.clipboard.writeText(document.getElementById('webhookUrl').value)">
                            <i class="bi bi-clipboard"></i>
                        </button>
                    </div>
                    <div class="form-text">À déclarer dans Meta : WhatsApp → Configuration → Webhook → Callback URL.</div>
                </div>
            </div>

            <div class="pt-3 mt-3 border-top">
                <button class="btn btn-primary">
                    <i class="bi bi-save"></i> Enregistrer la connexion
                </button>
            </div>
        </form>
    </div>
</div>

</div>

{{-- ─────────────────── Modal "Ajouter manuellement" ─────────────────── --}}
<div class="modal fade" id="addNumberModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.integrations.numbers.store') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title">Ajouter un numéro WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Numéro affiché <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" name="display_number" required placeholder="+212 657 14 00 50">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Phone ID (Meta) <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" name="phone_id" required placeholder="ex. 1219470361254773">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Libellé interne</label>
                    <input type="text" class="form-control" name="label" maxlength="120" placeholder="ex. SAV Casablanca">
                </div>
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="is_default" value="1" id="chkDefault">
                    <label class="form-check-label" for="chkDefault">Définir comme numéro par défaut</label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button class="btn btn-success">Ajouter</button>
            </div>
        </form>
    </div>
</div>
@endsection
