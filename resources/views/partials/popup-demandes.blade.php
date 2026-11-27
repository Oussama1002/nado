{{--
    Popup "Publier une demande" — modal centré.
    Injecté par InjectPopupDemandes sur la page d'accueil et /achat.
    30 s après le chargement, 7 jours de cooldown via cookie.
    Prefill le formulaire demande avec la recherche courante si on est sur /achat.
--}}
@php
    $isRtl   = app()->getLocale() === 'ar';
    $onAchat = request()->is('achat', 'achat/*');
    $qs      = $onAchat
        ? array_filter([
            'marque' => request()->query('marque'),
            'modele' => request()->query('modele'),
            'ville'  => request()->query('ville'),
            'source' => 'popup',
        ])
        : ['source' => 'popup'];
    $popupUrl = route('demandes.create') . ($qs ? ('?' . http_build_query($qs)) : '');
@endphp

<div id="ppDmdBackdrop" role="dialog" aria-modal="true" aria-labelledby="ppDmdTitle"
     style="position:fixed;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(4px);z-index:10050;display:none;align-items:center;justify-content:center;padding:16px;animation:ppDmdFade .25s ease;">
    <div style="background:#fff;max-width:440px;width:100%;border-radius:20px;box-shadow:0 30px 60px -12px rgba(0,0,0,.35);overflow:hidden;position:relative;font-family:'Inter',sans-serif;direction:{{ $isRtl ? 'rtl' : 'ltr' }};">
        <button type="button" id="ppDmdClose" aria-label="Fermer"
                style="position:absolute;top:10px;{{ $isRtl ? 'left' : 'right' }}:12px;background:#f1f5f9;border:0;width:34px;height:34px;border-radius:50%;cursor:pointer;font-size:18px;color:#0f172a;display:flex;align-items:center;justify-content:center;">×</button>

        <div style="padding:32px 28px 20px;text-align:center;">
            <div style="width:68px;height:68px;margin:0 auto 18px;border-radius:50%;background:linear-gradient(135deg,#1459DD,#2563eb);display:flex;align-items:center;justify-content:center;box-shadow:0 10px 24px -6px rgba(20,89,221,.45);">
                <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 13l2-7h14l2 7"/><path d="M3 13v5a1 1 0 001 1h2a1 1 0 001-1v-1h10v1a1 1 0 001 1h2a1 1 0 001-1v-5"/><circle cx="7" cy="16" r="1.3"/><circle cx="17" cy="16" r="1.3"/>
                </svg>
            </div>
            <h3 id="ppDmdTitle" style="margin:0 0 10px;font-size:1.35rem;font-weight:900;color:#0f172a;line-height:1.25;">{{ __('messages.demandes.noresult_popup_title') }}</h3>
            <p style="margin:0 0 22px;font-size:.98rem;color:#475569;line-height:1.55;">{{ __('messages.demandes.noresult_popup_desc') }}</p>
        </div>

        <div style="padding:0 28px 28px;display:flex;flex-direction:column;gap:10px;">
            <a href="{{ $popupUrl }}" id="ppDmdCta"
               style="display:inline-flex;align-items:center;justify-content:center;gap:8px;background:linear-gradient(135deg,#1459DD,#2563eb);color:#fff;padding:14px 20px;border-radius:12px;font-weight:800;text-decoration:none;font-size:1rem;box-shadow:0 10px 20px -8px rgba(20,89,221,.5);">
                {{ __('messages.demandes.noresult_popup_cta') }} →
            </a>
            <button type="button" id="ppDmdSkip"
                    style="background:transparent;border:0;color:#64748b;padding:8px;cursor:pointer;font-size:.92rem;font-weight:600;">
                {{ __('messages.demandes.noresult_popup_skip') }}
            </button>
        </div>
    </div>
</div>

<style>@keyframes ppDmdFade { from { opacity:0 } to { opacity:1 } }</style>

<script>
(function () {
    if (location.search.indexOf('no_popups=1') !== -1) return;

    var COOKIE = 'nado_pp_dmd_until';
    var DELAY  = 30000;         // 30 s
    var SNOOZE = 7 * 86400000;  // 7 jours

    function getCookie(k) {
        var m = document.cookie.match('(^|; )' + k + '=([^;]*)');
        return m ? decodeURIComponent(m[2]) : null;
    }
    function setCookie(k, v, days) {
        var d = new Date();
        d.setTime(d.getTime() + days * 86400000);
        document.cookie = k + '=' + encodeURIComponent(v) + ';expires=' + d.toUTCString() + ';path=/;SameSite=Lax';
    }

    var until = parseInt(getCookie(COOKIE) || '0', 10);
    if (until && Date.now() < until) return;

    var backdrop = document.getElementById('ppDmdBackdrop');
    var closeBtn = document.getElementById('ppDmdClose');
    var skipBtn  = document.getElementById('ppDmdSkip');
    var cta      = document.getElementById('ppDmdCta');
    if (!backdrop) return;

    function dismiss() {
        backdrop.style.display = 'none';
        document.body.style.overflow = '';
        setCookie(COOKIE, String(Date.now() + SNOOZE), 7);
    }
    function open() {
        backdrop.style.display = 'flex';
        document.body.style.overflow = 'hidden';
    }

    closeBtn && closeBtn.addEventListener('click', dismiss);
    skipBtn  && skipBtn.addEventListener('click',  dismiss);
    backdrop.addEventListener('click', function (e) { if (e.target === backdrop) dismiss(); });
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && backdrop.style.display === 'flex') dismiss();
    });
    cta && cta.addEventListener('click', function () { setCookie(COOKIE, String(Date.now() + SNOOZE), 7); });

    setTimeout(open, DELAY);
})();
</script>
