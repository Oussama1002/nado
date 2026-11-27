{{--
    Popup "Découvrir Auto-Enchère B2B" — slide-in non bloquant en bas à droite.
    Injecté par PopupAutoEnchere middleware sur toutes les pages publiques
    (hors nadoautoenchere/*, admin/*, la page d'accueil du module).

    Public cible : visiteurs qui ne sont PAS déjà un pro approuvé
    (type != flotte&enchere OU ae_approved = false, incluant les invités).

    Timing : 10 s après le chargement, 1 fois toutes les 14 jours.
--}}
@php
    $isRtl = app()->getLocale() === 'ar';
@endphp

<div id="ppAeBoxWrap" aria-hidden="true"
     style="position:fixed;bottom:20px;{{ $isRtl ? 'left' : 'right' }}:20px;z-index:9998;max-width:360px;width:calc(100% - 40px);font-family:'Inter',sans-serif;display:none;direction:{{ $isRtl ? 'rtl' : 'ltr' }};">
    <div id="ppAeBox" role="dialog" aria-labelledby="ppAeTitle"
         style="background:linear-gradient(135deg,#0f172a,#1e293b);color:#fff;border-radius:18px;padding:18px 18px 16px;box-shadow:0 24px 50px -12px rgba(0,0,0,.45);position:relative;transform:translateY(20px);opacity:0;transition:transform .35s ease,opacity .35s ease;">
        <button type="button" id="ppAeClose" aria-label="Fermer"
                style="position:absolute;top:8px;{{ $isRtl ? 'left' : 'right' }}:10px;background:rgba(255,255,255,.1);border:0;color:#fff;cursor:pointer;width:26px;height:26px;border-radius:50%;font-size:14px;display:flex;align-items:center;justify-content:center;">×</button>

        <div style="display:flex;align-items:center;gap:12px;margin-bottom:10px;">
            <div style="width:44px;height:44px;border-radius:12px;background:linear-gradient(135deg,#FDD817,#f59e0b);display:flex;align-items:center;justify-content:center;flex-shrink:0;">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="#0f172a" stroke-width="2.4" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 2v6M12 22v-6M4.93 4.93l4.24 4.24M19.07 19.07l-4.24-4.24M2 12h6M22 12h-6M4.93 19.07l4.24-4.24M19.07 4.93l-4.24 4.24"/>
                </svg>
            </div>
            <div style="flex:1;min-width:0;">
                <div style="font-size:.7rem;font-weight:800;color:#FDD817;letter-spacing:.1em;text-transform:uppercase;">{{ __('messages.autoenchere.popup_badge') }}</div>
                <div id="ppAeTitle" style="font-size:1rem;font-weight:900;line-height:1.3;">{{ __('messages.autoenchere.popup_title') }}</div>
            </div>
        </div>

        <p style="margin:0 0 14px;font-size:.88rem;line-height:1.5;color:rgba(255,255,255,.82);">{{ __('messages.autoenchere.popup_desc') }}</p>

        <div style="display:flex;gap:10px;align-items:center;">
            <a href="{{ route('nadoautoenchere.prelogin') }}" id="ppAeCta"
               style="flex:1;background:#FDD817;color:#0f172a;padding:11px 14px;border-radius:10px;font-weight:800;text-decoration:none;font-size:.9rem;text-align:center;display:inline-flex;align-items:center;justify-content:center;gap:6px;">
                {{ __('messages.autoenchere.popup_cta') }} →
            </a>
            <button type="button" id="ppAeSkip"
                    style="background:transparent;border:0;color:rgba(255,255,255,.65);cursor:pointer;font-size:.82rem;padding:6px 8px;font-weight:600;">
                {{ __('messages.autoenchere.popup_skip') }}
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    if (location.search.indexOf('no_popups=1') !== -1) return;

    var COOKIE  = 'nado_pp_ae_until';
    var DELAY   = 10000;             // 10 s
    var SNOOZE  = 14 * 86400000;     // 14 jours

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

    var wrap  = document.getElementById('ppAeBoxWrap');
    var box   = document.getElementById('ppAeBox');
    var closeBtn = document.getElementById('ppAeClose');
    var skipBtn  = document.getElementById('ppAeSkip');
    var cta      = document.getElementById('ppAeCta');
    if (!wrap || !box) return;

    function dismiss(days) {
        box.style.transform = 'translateY(20px)';
        box.style.opacity   = '0';
        setTimeout(function () { wrap.style.display = 'none'; }, 350);
        setCookie(COOKIE, String(Date.now() + days * 86400000), days);
    }
    function open() {
        wrap.style.display = 'block';
        // Force reflow pour que la transition s'applique.
        void box.offsetWidth;
        box.style.transform = 'translateY(0)';
        box.style.opacity   = '1';
        wrap.setAttribute('aria-hidden', 'false');
    }

    closeBtn && closeBtn.addEventListener('click', function () { dismiss(14); });
    skipBtn  && skipBtn.addEventListener('click',  function () { dismiss(30); }); // "Pas intéressé" → 30 jours
    cta      && cta.addEventListener('click',      function () { setCookie(COOKIE, String(Date.now() + SNOOZE), 14); });

    setTimeout(open, DELAY);
})();
</script>
