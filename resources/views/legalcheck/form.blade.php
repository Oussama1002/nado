@extends('layouts.app')

@section('title', __('messages.legalcheck.page_title'))

@php $isAr = app()->getLocale() === 'ar'; @endphp

@section('styles')
<style>
    .lc-wrap { max-width: 760px; margin: 2.5rem auto; padding: 0 16px; }
    .lc-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
        padding: 1.75rem; box-shadow: 0 10px 30px rgba(15,23,42,.06); margin-bottom: 1.25rem;
    }
    .lc-head h1 { font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0 0 .25rem; }
    .lc-head .lc-sub { color: #1459DD; font-weight: 700; font-size: .95rem; }
    .lc-intro { color: #475569; line-height: 1.6; margin: .9rem 0 0; }
    .lc-attached {
        display: inline-flex; align-items: center; gap: .4rem; margin-top: .75rem;
        background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe;
        border-radius: 999px; padding: .3rem .8rem; font-weight: 700; font-size: .85rem;
    }
    .lc-legend { font-weight: 800; color: #0f172a; margin: 0 0 .75rem; font-size: 1.05rem; }
    .lc-matricule { display: flex; gap: .6rem; flex-wrap: wrap; }
    .lc-field { flex: 1 1 120px; min-width: 100px; }
    .lc-field label { display: block; font-size: .8rem; font-weight: 700; color: #64748b; margin-bottom: .3rem; }
    .lc-field input,
    .lc-field select {
        width: 100%; border: 1.5px solid #cbd5e1; border-radius: 10px;
        padding: .7rem .8rem; font-size: 1.1rem; font-weight: 700; text-align: center;
        background: #fff; height: calc(1.1rem + 1.4rem + 3px);
    }
    .lc-field input:focus,
    .lc-field select:focus { outline: none; border-color: #1459DD; }
    .lc-hint { color: #94a3b8; font-size: .82rem; margin-top: .5rem; }
    .lc-channel { margin-top: 1rem; }
    .lc-channel-legend { display: block; font-size: .8rem; font-weight: 700; color: #64748b; margin-bottom: .45rem; }
    .lc-channel-opts { display: flex; gap: .5rem; flex-wrap: wrap; }
    .lc-channel-opt { flex: 1 1 110px; display: flex; align-items: center; gap: .4rem; cursor: pointer;
        border: 1.5px solid #cbd5e1; border-radius: 10px; padding: .55rem .7rem; font-size: .9rem; color: #0f172a; }
    .lc-channel-opt input { accent-color: #1459DD; margin: 0; }
    .lc-channel-opt:has(input:checked) { border-color: #1459DD; background: #eff5ff; }
    .lc-channel-opt .fa-whatsapp { color: #25D366; }
    .lc-wa-field { margin-top: .75rem; }
    html[dir="rtl"] .lc-channel-opt { flex-direction: row-reverse; }
    .lc-preview-box {
        margin-top: 1rem; background: #0f172a; color: #fff; border-radius: 12px;
        padding: .9rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }
    .lc-preview-box .lbl { font-size: .78rem; opacity: .7; text-transform: uppercase; letter-spacing: .08em; }
    .lc-preview-val { font-size: 1.5rem; font-weight: 900; letter-spacing: .04em; direction: ltr; unicode-bidi: isolate; flex: 1; }
    .lc-copy-btn {
        display: inline-flex; align-items: center; gap: .4rem; background: rgba(255,255,255,.12);
        color: #fff; border: 1px solid rgba(255,255,255,.25); border-radius: 8px;
        padding: .45rem .75rem; font-weight: 700; font-size: .85rem; cursor: pointer; white-space: nowrap;
    }
    .lc-copy-btn:hover { background: rgba(255,255,255,.22); }
    .lc-copy-btn.copied { background: #16a34a; border-color: #16a34a; }
    .lc-toast {
        position: fixed; left: 50%; bottom: 28px; transform: translateX(-50%) translateY(20px);
        background: #0f172a; color: #fff; padding: .8rem 1.3rem; border-radius: 999px; font-weight: 700;
        box-shadow: 0 10px 30px rgba(0,0,0,.25); opacity: 0; pointer-events: none;
        transition: opacity .2s, transform .2s; z-index: 9999;
    }
    .lc-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .lc-steps { counter-reset: step; list-style: none; padding: 0; margin: 1rem 0 0; }
    .lc-steps li {
        counter-increment: step; position: relative; padding-{{ $isAr ? 'right' : 'left' }}: 2.4rem;
        margin-bottom: .9rem; color: #334155; line-height: 1.5;
    }
    .lc-steps li::before {
        content: counter(step); position: absolute; {{ $isAr ? 'right' : 'left' }}: 0; top: -2px;
        width: 1.7rem; height: 1.7rem; background: #1459DD; color: #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: .9rem;
    }
    .lc-open-btn {
        display: inline-flex; align-items: center; gap: .5rem; margin-top: .5rem;
        background: #1459DD; color: #fff; font-weight: 800; text-decoration: none;
        padding: .85rem 1.4rem; border-radius: 10px; font-size: 1rem;
    }
    .lc-open-btn:hover { background: #0e47b0; color: #fff; }
    .lc-embed { margin-top: 1rem; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
    .lc-embed-bar {
        display: flex; align-items: center; justify-content: space-between; gap: .6rem;
        background: #0f172a; color: #fff; padding: .5rem .9rem; font-weight: 700; font-size: .85rem;
    }
    .lc-embed-open {
        display: inline-flex; align-items: center; gap: .4rem; color: #fff; text-decoration: none;
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25);
        border-radius: 8px; padding: .35rem .7rem; white-space: nowrap;
    }
    .lc-embed-open:hover { background: rgba(255,255,255,.22); color: #fff; }
    .lc-embed iframe { width: 100%; height: 820px; border: 0; display: block; background: #fff; }
    .lc-embed-fallback { padding: .6rem .9rem 0; }
    .lc-results { display: flex; gap: .75rem; flex-wrap: wrap; }
    .lc-radio {
        flex: 1 1 200px; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem;
        cursor: pointer; display: flex; align-items: center; gap: .6rem; font-weight: 700; color: #334155;
    }
    .lc-radio input { accent-color: #1459DD; width: 1.1rem; height: 1.1rem; }
    .lc-radio.clear:has(input:checked) { border-color: #16a34a; background: #f0fdf4; color: #15803d; }
    .lc-radio.opp:has(input:checked) { border-color: #d97706; background: #fffbeb; color: #b45309; }
    .lc-file label.filebtn {
        display: inline-flex; align-items: center; gap: .5rem; cursor: pointer;
        border: 1.5px dashed #94a3b8; border-radius: 10px; padding: .8rem 1rem; color: #475569; font-weight: 700;
    }
    .lc-file input[type=file] { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
    .lc-file .fname { color: #1459DD; font-weight: 700; margin-{{ $isAr ? 'right' : 'left' }}: .6rem; }
    .lc-submit {
        width: 100%; background: #16a34a; color: #fff; border: 0; border-radius: 12px;
        padding: 1rem; font-size: 1.05rem; font-weight: 900; cursor: pointer; margin-top: 1.25rem;
    }
    .lc-submit:hover { background: #15803d; }
    .lc-disclaimer { color: #64748b; font-size: .82rem; line-height: 1.5; margin-top: 1rem; display: flex; gap: .5rem; }
    .lc-disclaimer i { color: #94a3b8; margin-top: .15rem; }
    .lc-alert { border-radius: 12px; padding: .9rem 1.1rem; margin-bottom: 1.25rem; font-weight: 700; }
    .lc-alert.ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .lc-alert.err { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .lc-status { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: .8rem 1rem; color: #475569; margin-bottom: 1rem; }

    /* Demande différée (service NARSA en panne) */
    .lc-fallback { background: #fffbeb; border: 1px solid #fde68a; }
    .lc-fallback .lc-legend { color: #b45309; }
    .lc-fallback p { color: #78350f; line-height: 1.55; margin: 0 0 1rem; }
    .lc-fallback-btn {
        display: inline-flex; align-items: center; gap: .5rem; background: #1459DD; color: #fff;
        font-weight: 800; border: 0; border-radius: 10px; padding: .85rem 1.4rem; font-size: 1rem; cursor: pointer;
    }
    .lc-fallback-btn:hover { background: #0e47b0; }
    .lc-modal-overlay {
        position: fixed; inset: 0; background: rgba(15,23,42,.55); display: none;
        align-items: center; justify-content: center; z-index: 10000; padding: 16px;
    }
    .lc-modal-overlay.open { display: flex; }
    .lc-modal {
        background: #fff; border-radius: 16px; width: 100%; max-width: 480px;
        padding: 1.6rem; box-shadow: 0 20px 60px rgba(0,0,0,.3);
    }
    .lc-modal h2 { font-size: 1.25rem; font-weight: 900; color: #0f172a; margin: 0 0 .5rem; }
    .lc-modal p.lc-modal-text { color: #475569; line-height: 1.5; margin: 0 0 1.1rem; font-size: .92rem; }
    .lc-modal-err { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; border-radius: 10px; padding: .7rem .9rem; margin-bottom: 1rem; font-weight: 700; font-size: .9rem; display: none; }
    .lc-modal-ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; border-radius: 10px; padding: .9rem 1rem; font-weight: 700; line-height: 1.5; display: none; }
    .lc-modal-actions { display: flex; gap: .6rem; margin-top: 1.25rem; }
    .lc-modal-send { flex: 1; background: #16a34a; color: #fff; border: 0; border-radius: 12px; padding: .9rem; font-size: 1rem; font-weight: 900; cursor: pointer; }
    .lc-modal-send:hover { background: #15803d; }
    .lc-modal-send:disabled { opacity: .6; cursor: default; }
    .lc-modal-cancel { background: #f1f5f9; color: #475569; border: 0; border-radius: 12px; padding: .9rem 1.2rem; font-weight: 800; cursor: pointer; }
    .lc-modal-cancel:hover { background: #e2e8f0; }
</style>
@endsection

@section('content')
<div class="lc-wrap">

    <div class="lc-card lc-head">
        <h1>{{ __('messages.legalcheck.heading') }}</h1>
        <div class="lc-sub">{{ __('messages.legalcheck.subheading') }}</div>
        <p class="lc-intro">{{ __('messages.legalcheck.intro') }}</p>
        @if($annonce)
            <span class="lc-attached"><i class="fa-solid fa-link"></i>
                {{ __('messages.legalcheck.attached_to') }} : {{ $annonce->titre ?? ('#' . $annonce->id) }}</span>
        @endif
    </div>

    @if(session('success'))
        <div class="lc-alert ok">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="lc-alert err">{{ $errors->first() }}</div>
    @endif

    @if($existing)
        <div class="lc-status">
            @switch($existing->admin_status)
                @case('confirmed') ✅ {{ __('messages.legalcheck.status_confirmed') }} @break
                @case('rejected')  ⚠️ {{ __('messages.legalcheck.status_rejected') }} @break
                @default           ⏳ {{ __('messages.legalcheck.status_pending') }}
            @endswitch
            —
            {{ $existing->declared_status === 'clear'
                ? __('messages.legalcheck.declared_clear')
                : __('messages.legalcheck.declared_opposition') }}
        </div>
    @endif

    {{-- Service NARSA intégré (iframe cross-origin : le vendeur agit dedans,
         on ne lit rien à travers). Ne s'affiche que depuis une IP marocaine. --}}
    <div class="lc-card">
        <p class="lc-legend">{{ __('messages.legalcheck.narsa_service') }}</p>
        <div class="lc-embed" id="lc_embed">
            <iframe id="lc_iframe"
                    src="https://opposition.narsa.gov.ma:82/consultation/opposition"
                    title="NARSA" loading="lazy"
                    referrerpolicy="no-referrer"></iframe>
            <p class="lc-hint lc-embed-fallback">
                <i class="fa-solid fa-triangle-exclamation"></i> {{ __('messages.legalcheck.embed_fallback') }}
            </p>
        </div>
        <p class="lc-disclaimer">
            <i class="fa-solid fa-circle-info"></i>
            <span>{{ __('messages.legalcheck.disclaimer') }}</span>
        </p>
    </div>

    {{-- Repli : le service NARSA intégré peut afficher « Une erreur est survenue,
         réessayez plus tard ». Le vendeur laisse son matricule et on lui envoie
         le résultat par email dès que le service est disponible. --}}
    <div class="lc-card lc-fallback">
        <p class="lc-legend"><i class="fa-solid fa-triangle-exclamation"></i> {{ __('messages.legalcheck.request_unavailable_title') }}</p>
        <p>{{ __('messages.legalcheck.request_unavailable_text') }}</p>
        <button type="button" class="lc-fallback-btn" id="lc_open_request">
            <i class="fa-solid fa-envelope"></i> {{ __('messages.legalcheck.request_open_btn') }}
        </button>
    </div>
</div>

{{-- Modale : demande différée --}}
<div class="lc-modal-overlay" id="lc_modal_overlay" role="dialog" aria-modal="true" aria-labelledby="lc_modal_title">
    <div class="lc-modal">
        <h2 id="lc_modal_title">{{ __('messages.legalcheck.request_modal_title') }}</h2>
        <p class="lc-modal-text">{{ __('messages.legalcheck.request_modal_text') }}</p>

        <div class="lc-modal-err" id="lc_modal_err"></div>
        <div class="lc-modal-ok" id="lc_modal_ok"></div>

        <form id="lc_request_form">
            @if($annonce)
                <input type="hidden" name="annonce_id" value="{{ $annonce->id }}">
            @endif
            <div class="lc-matricule">
                <div class="lc-field">
                    <label for="lc_m_number">{{ __('messages.legalcheck.matricule_number') }}</label>
                    <input type="text" id="lc_m_number" name="matricule_number" inputmode="numeric" maxlength="10" autocomplete="off">
                </div>
                <div class="lc-field">
                    <label for="lc_m_letter">{{ __('messages.legalcheck.matricule_letter') }}</label>
                    @php
                        // Lettres de série utilisées sur les plaques marocaines (partie
                        // centrale). On affiche la lettre arabe + son équivalent latin
                        // officiel (réforme NARSA) quand il est fixé. La valeur postée
                        // reste la lettre arabe (assainie côté contrôleur).
                        $lcSeries = [
                            'أ'   => 'A',
                            'ب'   => 'B',
                            'ج'   => 'J',
                            'د'   => 'D',
                            'ه'   => 'H',
                            'و'   => 'E',
                            'ط'   => 'T',
                            'ش'   => 'C',
                            'ق'   => 'Q',
                            'م'   => 'M',
                            'ن'   => 'N',
                            'ي'   => 'Y',
                            'و و' => 'WW',
                        ];
                    @endphp
                    <select id="lc_m_letter" name="matricule_letter">
                        <option value="">—</option>
                        @foreach($lcSeries as $ar => $lat)
                            <option value="{{ $ar }}">{{ $ar }} — {{ $lat }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="lc-field">
                    <label for="lc_m_region">{{ __('messages.legalcheck.matricule_region') }}</label>
                    <input type="text" id="lc_m_region" name="matricule_region" inputmode="numeric" maxlength="6" autocomplete="off">
                </div>
            </div>
            <p class="lc-hint">{{ __('messages.legalcheck.matricule_hint') }}</p>

            {{-- Canal de réception du résultat : email et/ou WhatsApp. --}}
            <div class="lc-channel">
                <span class="lc-channel-legend">{{ __('messages.legalcheck.channel_legend') }}</span>
                <div class="lc-channel-opts">
                    <label class="lc-channel-opt">
                        <input type="radio" name="contact_channel" value="email" checked>
                        <span><i class="fa-solid fa-envelope"></i> {{ __('messages.legalcheck.channel_email') }}</span>
                    </label>
                    <label class="lc-channel-opt">
                        <input type="radio" name="contact_channel" value="whatsapp">
                        <span><i class="fa-brands fa-whatsapp"></i> {{ __('messages.legalcheck.channel_whatsapp') }}</span>
                    </label>
                    <label class="lc-channel-opt">
                        <input type="radio" name="contact_channel" value="both">
                        <span>{{ __('messages.legalcheck.channel_both') }}</span>
                    </label>
                </div>
            </div>

            {{-- Numéro WhatsApp (affiché si WhatsApp choisi). Pré-rempli avec le téléphone du compte. --}}
            <div class="lc-field lc-wa-field" id="lc_wa_wrap" style="display:none;">
                <label for="lc_m_whatsapp"><i class="fa-brands fa-whatsapp"></i> {{ __('messages.legalcheck.whatsapp_label') }}</label>
                <input type="tel" id="lc_m_whatsapp" name="contact_whatsapp" maxlength="30" autocomplete="off"
                       placeholder="{{ __('messages.legalcheck.whatsapp_hint') }}"
                       value="{{ optional(Auth::guard('client')->user())->phone }}">
            </div>

            <div class="lc-modal-actions">
                <button type="submit" class="lc-modal-send" id="lc_request_send">{{ __('messages.legalcheck.request_send') }}</button>
                <button type="button" class="lc-modal-cancel" id="lc_request_cancel">{{ __('messages.legalcheck.request_cancel') }}</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('script')
<script>
(function () {
    const overlay = document.getElementById('lc_modal_overlay');
    const openBtn = document.getElementById('lc_open_request');
    const cancelBtn = document.getElementById('lc_request_cancel');
    const form = document.getElementById('lc_request_form');
    const sendBtn = document.getElementById('lc_request_send');
    const errBox = document.getElementById('lc_modal_err');
    const okBox = document.getElementById('lc_modal_ok');
    if (!overlay || !openBtn || !form) return;

    const TXT_SENDING = @json(__('messages.legalcheck.request_sending'));
    const TXT_SEND = @json(__('messages.legalcheck.request_send'));
    const TXT_GENERIC_ERR = @json(__('messages.legalcheck.request_error'));
    const ENDPOINT = @json(route('legalcheck.request'));

    // Affiche le champ WhatsApp quand le canal « WhatsApp » ou « les deux » est choisi.
    const waWrap = document.getElementById('lc_wa_wrap');
    const channelRadios = form.querySelectorAll('input[name="contact_channel"]');
    function syncChannel() {
        const val = (form.querySelector('input[name="contact_channel"]:checked') || {}).value;
        if (waWrap) waWrap.style.display = (val === 'whatsapp' || val === 'both') ? 'block' : 'none';
    }
    channelRadios.forEach(function (r) { r.addEventListener('change', syncChannel); });
    syncChannel();

    function openModal() {
        // Réinitialise à l'état « formulaire » (après un envoi réussi, le formulaire
        // était masqué et le message de succès affiché — on remet tout à zéro).
        errBox.style.display = 'none';
        okBox.style.display = 'none';
        form.style.display = '';
        sendBtn.disabled = false;
        sendBtn.textContent = TXT_SEND;
        syncChannel();
        overlay.classList.add('open');
    }
    function closeModal() { overlay.classList.remove('open'); }

    openBtn.addEventListener('click', openModal);
    cancelBtn.addEventListener('click', closeModal);
    overlay.addEventListener('click', function (e) { if (e.target === overlay) closeModal(); });

    form.addEventListener('submit', async function (e) {
        e.preventDefault();
        errBox.style.display = 'none';
        sendBtn.disabled = true;
        sendBtn.textContent = TXT_SENDING;

        try {
            // Jeton CSRF frais (la session peut avoir été régénérée dans un autre onglet).
            const tokRes = await fetch(@json(route('csrf.token')), { headers: { 'Accept': 'application/json' } });
            const tokJson = await tokRes.json();

            const fd = new FormData(form);
            fd.append('_token', tokJson.token);

            const res = await fetch(ENDPOINT, {
                method: 'POST',
                headers: { 'X-CSRF-TOKEN': tokJson.token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: fd,
            });
            const data = await res.json().catch(() => ({}));

            if (res.ok && data.success) {
                form.reset();
                form.style.display = 'none';
                okBox.textContent = data.message || '';
                okBox.style.display = 'block';
            } else {
                errBox.textContent = data.message || TXT_GENERIC_ERR;
                errBox.style.display = 'block';
            }
        } catch (err) {
            errBox.textContent = TXT_GENERIC_ERR;
            errBox.style.display = 'block';
        } finally {
            sendBtn.disabled = false;
            sendBtn.textContent = TXT_SEND;
        }
    });
})();
</script>
@endsection
