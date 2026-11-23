@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi bi-whatsapp text-success"></i> WhatsApp — Boîte de réception</h3>
    <div class="d-flex align-items-center gap-2">
        <div class="text-muted small text-end">
            @if(! settings('whatsapp.meta_phone_number_id'))
                <span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Credentials Meta non configurées — <a href="{{ route('admin.settings.group', 'whatsapp') }}">configurer</a>.</span>
            @else
                Fenêtre libre : 24h après le dernier msg reçu. Sinon → template.
            @endif
        </div>
        <a href="{{ route('admin.whatsapp.templates.index') }}" class="btn btn-outline-success btn-sm">
            <i class="bi bi-file-earmark-text"></i> Modèles
        </a>
        <button type="button" class="btn btn-success btn-sm" data-bs-toggle="modal" data-bs-target="#newWaChatModal">
            <i class="bi bi-plus-lg"></i> Nouvelle discussion
        </button>
    </div>
</div>

@if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif

<div class="page-content">
<div class="card">
<div class="card-body p-0">

<div class="row g-0" style="min-height: 70vh;">

    {{-- ─────────────────────── Liste conversations ─────────────────────── --}}
    <div class="col-12 col-md-4 border-end" style="max-height: 75vh; overflow-y: auto;">
        <form method="GET" action="{{ route('admin.whatsapp.index') }}" class="p-2 border-bottom bg-light">
            <div class="input-group input-group-sm">
                <span class="input-group-text"><i class="bi bi-search"></i></span>
                <input type="text" class="form-control" name="q" value="{{ $q }}" placeholder="Nom ou numéro…">
                @if($q)
                    <a href="{{ route('admin.whatsapp.index') }}" class="btn btn-outline-secondary"><i class="bi bi-x"></i></a>
                @endif
            </div>
        </form>

        @forelse($conversations as $c)
            <a href="{{ route('admin.whatsapp.show', $c) }}"
               class="d-block text-decoration-none text-reset border-bottom px-3 py-2 {{ $active && $active->id === $c->id ? 'bg-light' : '' }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="fw-semibold text-truncate" style="max-width: 70%;">
                        {{ $c->display_name ?: $c->wa_phone }}
                        @if($c->unread_count > 0)
                            <span class="badge bg-success rounded-pill ms-1">{{ $c->unread_count }}</span>
                        @endif
                    </div>
                    <div class="text-muted small flex-shrink-0">
                        {{ $c->last_message_at?->diffForHumans(null, true) }}
                    </div>
                </div>
                <div class="text-muted small text-truncate">{{ $c->wa_phone }}</div>
            </a>
        @empty
            <div class="p-4 text-center text-muted">
                <i class="bi bi-whatsapp fs-1 d-block mb-2"></i>
                Aucune conversation.<br>
                <small>Les messages reçus sur votre numéro WhatsApp apparaîtront ici.</small>
            </div>
        @endforelse

        <div class="p-2">{{ $conversations->links() }}</div>
    </div>

    {{-- ─────────────────────── Thread ─────────────────────── --}}
    <div class="col-12 col-md-8 d-flex flex-column" style="max-height: 75vh;">
        @if($active)
            <div class="p-3 border-bottom d-flex justify-content-between align-items-center">
                <div>
                    <div class="fw-semibold">{{ $active->display_name ?: $active->wa_phone }}</div>
                    <div class="text-muted small">{{ $active->wa_phone }}</div>
                </div>
                <div class="text-end">
                    @if($active->canSendFreeform())
                        <span class="badge bg-success">Fenêtre 24h ouverte</span>
                    @else
                        <span class="badge bg-warning text-dark">Hors 24h — template requis</span>
                    @endif
                </div>
            </div>

            <div class="flex-grow-1 p-3" style="overflow-y: auto; background: #efeae2;" id="wa-thread">
                @foreach($messages as $m)
                    <div class="d-flex mb-2 {{ $m->direction === 'out' ? 'justify-content-end' : 'justify-content-start' }}">
                        <div class="p-2 rounded shadow-sm" style="max-width: 70%; background: {{ $m->direction === 'out' ? '#dcf8c6' : '#fff' }};">
                            @if($m->media_path)
                                @php $mediaUrl = asset('storage/' . $m->media_path); @endphp
                                @if(str_starts_with((string) $m->media_mime, 'image/'))
                                    <a href="{{ $mediaUrl }}" target="_blank">
                                        <img src="{{ $mediaUrl }}" alt="image" style="max-width: 100%; max-height: 300px; border-radius: 6px; display: block;">
                                    </a>
                                @elseif(str_starts_with((string) $m->media_mime, 'video/'))
                                    <video controls style="max-width: 100%; max-height: 300px; border-radius: 6px; display: block;">
                                        <source src="{{ $mediaUrl }}" type="{{ $m->media_mime }}">
                                    </video>
                                @elseif(str_starts_with((string) $m->media_mime, 'audio/'))
                                    <audio controls style="max-width: 100%; display: block;">
                                        <source src="{{ $mediaUrl }}" type="{{ $m->media_mime }}">
                                    </audio>
                                @else
                                    <a href="{{ $mediaUrl }}" target="_blank" class="d-inline-flex align-items-center gap-2 text-decoration-none">
                                        <i class="bi bi-file-earmark fs-4"></i>
                                        <span>Télécharger le fichier</span>
                                    </a>
                                @endif
                            @endif
                            <div style="white-space: pre-wrap; word-break: break-word;">{{ $m->body }}</div>
                            <div class="text-muted small mt-1 d-flex gap-2 justify-content-end align-items-center">
                                <span>{{ $m->sent_at?->format('d/m H:i') }}</span>
                                @if($m->direction === 'out')
                                    @switch($m->status)
                                        @case('sent')      <i class="bi bi-check" title="Envoyé"></i> @break
                                        @case('delivered') <i class="bi bi-check-all" title="Reçu"></i> @break
                                        @case('read')      <i class="bi bi-check-all text-primary" title="Lu"></i> @break
                                        @case('failed')    <i class="bi bi-exclamation-circle text-danger" title="{{ $m->error }}"></i> @break
                                    @endswitch
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="p-3 border-top bg-white">
                @if($active->canSendFreeform())
                    <form method="POST" action="{{ route('admin.whatsapp.reply', $active) }}">
                        @csrf
                        <div class="input-group">
                            <textarea name="body" class="form-control" rows="2" maxlength="4096"
                                      placeholder="Votre réponse…" required></textarea>
                            <button class="btn btn-success" type="submit">
                                <i class="bi bi-send"></i> Envoyer
                            </button>
                        </div>
                    </form>
                @else
                    <div class="alert alert-warning mb-2 small d-flex align-items-start gap-2">
                        <i class="bi bi-clock-history mt-1"></i>
                        <div class="flex-grow-1">
                            Plus de 24h depuis le dernier message du client. Meta n'autorise que l'envoi d'un
                            <strong>template approuvé</strong> pour rouvrir la conversation.
                        </div>
                    </div>
                    <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#sendTemplateModal">
                        <i class="bi bi-file-earmark-text"></i> Choisir un template &amp; envoyer
                    </button>
                @endif
            </div>
        @else
            <div class="d-flex align-items-center justify-content-center flex-grow-1 text-muted">
                <div class="text-center">
                    <i class="bi bi-chat-dots fs-1"></i>
                    <p class="mt-2">Sélectionnez une conversation</p>
                </div>
            </div>
        @endif
    </div>
</div>

</div>
</div>
</div>

{{-- ─────────────────────── Modal "Nouvelle discussion" ─────────────────────── --}}
<div class="modal fade" id="newWaChatModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <form class="modal-content" method="POST" action="{{ route('admin.whatsapp.start') }}">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-whatsapp text-success"></i> Nouvelle discussion WhatsApp</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fermer"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning small mb-3">
                    <i class="bi bi-exclamation-triangle"></i>
                    Rappel : Meta n'autorise un message libre que si le destinataire vous a écrit dans les <strong>24 dernières heures</strong>.
                    Au-delà, l'envoi sera refusé par Meta et vous devrez utiliser un template.
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Numéro de téléphone <span class="text-danger">*</span></label>
                    <input type="tel" class="form-control" name="phone" required
                           placeholder="+212 6 12 34 56 78" inputmode="tel">
                    <div class="form-text">Format international (ex. +212 6…). Avec un numéro test Meta, le destinataire doit aussi être pré-approuvé dans la console Meta.</div>
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Nom (optionnel)</label>
                    <input type="text" class="form-control" name="display_name" maxlength="120" placeholder="Jean Dupont">
                </div>

                <div class="mb-3">
                    <label class="form-label fw-semibold">Message <span class="text-danger">*</span></label>
                    <textarea class="form-control" name="body" rows="4" maxlength="4096" required
                              placeholder="Bonjour, …"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                <button type="submit" class="btn btn-success"><i class="bi bi-send"></i> Envoyer</button>
            </div>
        </form>
    </div>
</div>

{{-- ─────────────────── Modal "Envoyer un template" ─────────────────── --}}
@if($active && ! $active->canSendFreeform())
<div class="modal fade" id="sendTemplateModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <form class="modal-content" method="POST" action="{{ route('admin.whatsapp.send-template', $active) }}" id="waTemplateForm">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-file-earmark-text text-success"></i> Choisir un template approuvé</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                @if(empty($templates))
                    <div class="alert alert-warning mb-0">
                        Aucun template approuvé n'est disponible sur votre compte Meta.
                        <a href="{{ route('admin.whatsapp.templates.create') }}">Créez-en un</a> puis attendez l'approbation.
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Template <span class="text-danger">*</span></label>
                        <select class="form-select" id="waTplSelect" required>
                            <option value="">— Sélectionnez un template —</option>
                            @foreach($templates as $i => $t)
                                <option value="{{ $i }}"
                                        data-name="{{ $t['name'] }}"
                                        data-language="{{ $t['language'] }}"
                                        data-category="{{ $t['category'] }}"
                                        data-varcount="{{ $t['var_count'] }}"
                                        data-body="{{ $t['body'] }}">
                                    {{ $t['name'] }} — {{ $t['category'] }} · {{ $t['language'] }}@if($t['var_count']) · {{ $t['var_count'] }} variable(s) @endif
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Hidden fields filled from JS. --}}
                    <input type="hidden" name="template_name" id="waTplName">
                    <input type="hidden" name="language"      id="waTplLang">
                    <input type="hidden" name="preview"       id="waTplPreview">

                    {{-- Dynamic variables. --}}
                    <div id="waTplVariables"></div>

                    {{-- Live preview. --}}
                    <div id="waTplPreviewWrap" class="mt-3" style="display:none;">
                        <label class="form-label fw-semibold">Aperçu</label>
                        <div class="p-3 rounded" style="background:#dcf8c6;border:1px solid #cce9b5;white-space:pre-wrap;word-break:break-word;" id="waTplPreviewBox"></div>
                    </div>
                @endif
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
                @if(!empty($templates))
                    <button class="btn btn-success" id="waTplSendBtn" disabled>
                        <i class="bi bi-send"></i> Envoyer
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

<script>
    (function () {
        var sel    = document.getElementById('waTplSelect');
        if (!sel) return;
        var vars   = document.getElementById('waTplVariables');
        var prevW  = document.getElementById('waTplPreviewWrap');
        var prevB  = document.getElementById('waTplPreviewBox');
        var hName  = document.getElementById('waTplName');
        var hLang  = document.getElementById('waTplLang');
        var hPrev  = document.getElementById('waTplPreview');
        var btn    = document.getElementById('waTplSendBtn');

        function render() {
            var opt = sel.options[sel.selectedIndex];
            if (!opt || !opt.value) {
                vars.innerHTML = '';
                prevW.style.display = 'none';
                btn.disabled = true;
                return;
            }
            var name = opt.getAttribute('data-name');
            var lang = opt.getAttribute('data-language');
            var body = opt.getAttribute('data-body') || '';
            var n    = parseInt(opt.getAttribute('data-varcount') || '0', 10);

            hName.value = name;
            hLang.value = lang;

            // Build inputs for @{{1}}…@{{N}}.
            var OPEN = '{' + '{';
            var CLOSE = '}' + '}';
            var html = '';
            for (var i = 1; i <= n; i++) {
                html += '<div class="mb-2">' +
                            '<label class="form-label fw-semibold small mb-1">Variable ' + OPEN + i + CLOSE + ' <span class="text-danger">*</span></label>' +
                            '<input type="text" class="form-control wa-var" name="variables[' + (i - 1) + ']" data-idx="' + i + '" required maxlength="1000">' +
                        '</div>';
            }
            vars.innerHTML = html;

            // Re-render preview as the user types.
            var inputs = vars.querySelectorAll('.wa-var');
            function refresh() {
                var out = body;
                var filled = inputs.length === 0;
                inputs.forEach(function (inp) {
                    var idx = inp.getAttribute('data-idx');
                    var v = (inp.value || '').trim();
                    var placeholder = OPEN + idx + CLOSE;
                    out = out.split(placeholder).join(v || placeholder);
                    if (v) filled = true;
                    else   filled = false;
                });
                if (inputs.length === 0) filled = true;
                prevB.textContent = out;
                hPrev.value = out;
                prevW.style.display = '';
                btn.disabled = !filled;
            }
            inputs.forEach(function (inp) { inp.addEventListener('input', refresh); });
            refresh();
        }
        sel.addEventListener('change', render);
    })();
</script>
@endif

<script>
    // Auto-scroll au bas du thread.
    (function () {
        var t = document.getElementById('wa-thread');
        if (t) t.scrollTop = t.scrollHeight;
    })();
    // Auto-refresh toutes les 20s si une conversation est ouverte.
    @if($active)
        setTimeout(function () { window.location.reload(); }, 20000);
    @endif
</script>
@endsection
