@extends('admin.layouts.app')

@section('content')
<div class="page-heading d-flex justify-content-between align-items-center flex-wrap gap-2">
    <h3 class="mb-0"><i class="bi bi-whatsapp text-success"></i> WhatsApp — Boîte de réception</h3>
    <div class="text-muted small">
        @if(! settings('whatsapp.meta_phone_number_id'))
            <span class="text-danger"><i class="bi bi-exclamation-triangle"></i> Credentials Meta non configurées — <a href="{{ route('admin.settings.group', 'whatsapp') }}">configurer</a>.</span>
        @else
            Fenêtre de réponse libre : 24h après le dernier message reçu. Au-delà → template requis.
        @endif
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
                    <div class="alert alert-warning mb-0 small">
                        <i class="bi bi-clock-history"></i>
                        Plus de 24h depuis le dernier message du client. Meta n'autorise que l'envoi d'un
                        <strong>template approuvé</strong> (catégorie Utility ou Marketing) pour rouvrir la conversation.
                    </div>
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
