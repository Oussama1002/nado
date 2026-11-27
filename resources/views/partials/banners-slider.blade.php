{{--
    Slider "bannières services" — remplace l'ancienne bannière Expert seule
    à l'emplacement homepage (entre logos & Derniers véhicules).

    Chaque slide (expert, demandes, estim, finance, legal, ae) est gated
    par sa propre Publication (status=1 pour apparaître). Rien ne s'affiche
    si aucune n'est active.

    Auto-avance toutes les 5 s, pause au hover. Dots en bas pour nav
    manuelle. Swipe tactile basique sur mobile.
--}}
@php
    $__slides = [
        'expert' => [
            'placement' => 'front-expert-banner',
            'badge'     => __('messages.expert.promo_badge'),
            'title'     => __('messages.expert.promo_title'),
            'desc'      => __('messages.expert.promo_desc'),
            'cta'       => __('messages.expert.promo_cta'),
            // L'expert ouvre le modal plutôt que de naviguer.
            'url'       => '#',
            'onclick'   => 'openExpertModal(); return false;',
        ],
        'demandes' => [
            'placement' => 'front-demandes-banner',
            'badge'     => __('messages.home.services_demandes_title'),
            'title'     => __('messages.home.services_demandes_title'),
            'desc'      => __('messages.home.services_demandes_desc'),
            'cta'       => __('messages.home.services_demandes_cta'),
            'url'       => route('demandes.create'),
            'onclick'   => null,
        ],
        'estim' => [
            'placement' => 'front-estim-banner',
            'badge'     => __('messages.home.services_estim_title'),
            'title'     => __('messages.home.services_estim_title'),
            'desc'      => __('messages.home.services_estim_desc'),
            'cta'       => __('messages.home.services_estim_cta'),
            'url'       => route('estimate.page'),
            'onclick'   => null,
        ],
        'finance' => [
            'placement' => 'front-finance-banner',
            'badge'     => __('messages.home.services_finance_title'),
            'title'     => __('messages.home.services_finance_title'),
            'desc'      => __('messages.home.services_finance_desc'),
            'cta'       => __('messages.home.services_finance_cta'),
            'url'       => url('/financement'),
            'onclick'   => null,
        ],
        'legal' => [
            'placement' => 'front-legal-banner',
            'badge'     => __('messages.home.services_legal_title'),
            'title'     => __('messages.home.services_legal_title'),
            'desc'      => __('messages.home.services_legal_desc'),
            'cta'       => __('messages.home.services_legal_cta'),
            'url'       => url('/verification-legale'),
            'onclick'   => null,
            // Palette "sécurité / conformité" : nuances de vert.
            'theme'     => 'legal',
        ],
        'ae' => [
            'placement' => 'front-ae-banner',
            'badge'     => __('messages.home.services_ae_title'),
            'title'     => __('messages.home.services_ae_title'),
            'desc'      => __('messages.home.services_ae_desc'),
            'cta'       => __('messages.home.services_ae_cta'),
            'url'       => route('nadoautoenchere.prelogin'),
            'onclick'   => null,
            // Palette Auto-Enchère : bleu primaire + orange accent.
            // Override des tokens CSS pour n'affecter QUE cette slide.
            'theme'     => 'ae',
        ],
    ];

    // Filtre : garde seulement les slides dont la Publication est active.
    $__activePlacements = \App\Models\Publication::whereIn('placement', array_column($__slides, 'placement'))
        ->where('status', 1)
        ->pluck('placement')
        ->all();
    $__activeSlides = array_filter($__slides, fn ($cfg) => in_array($cfg['placement'], $__activePlacements, true));
@endphp

@if(count($__activeSlides))
<section class="bsl-section" aria-label="Services NADO">
    <div class="bsl-wrap">
        <div class="bsl-viewport">
            <div class="bsl-track" id="bslTrack" data-count="{{ count($__activeSlides) }}">
                @foreach($__activeSlides as $key => $cfg)
                    <div class="bsl-slide">
                        <a href="{{ $cfg['url'] }}"
                           @if($cfg['onclick']) onclick="{{ $cfg['onclick'] }}" @endif
                           class="bsl-link">
                            <div class="bsl-card {{ !empty($cfg['theme']) ? 'bsl-card--' . $cfg['theme'] : '' }}">
                                <div class="bsl-glow" aria-hidden="true"></div>
                                <div class="bsl-content">
                                    <div class="bsl-badge">
                                        <span class="bsl-dot" aria-hidden="true"></span>
                                        <span>{{ $cfg['badge'] }}</span>
                                    </div>
                                    <h3 class="bsl-title">{{ $cfg['title'] }}</h3>
                                    <p class="bsl-desc">{{ $cfg['desc'] }}</p>
                                </div>
                                <div class="bsl-cta-wrap">
                                    <span class="bsl-cta">
                                        <span>{{ $cfg['cta'] }}</span>
                                        <svg class="bsl-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                                            <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                                        </svg>
                                    </span>
                                </div>
                            </div>
                        </a>
                    </div>
                @endforeach
            </div>
        </div>

        @if(count($__activeSlides) > 1)
            <div class="bsl-dots" id="bslDots" role="tablist">
                @foreach($__activeSlides as $i => $cfg)
                    <button type="button" class="bsl-dot-btn {{ $loop->first ? 'is-active' : '' }}"
                            data-idx="{{ $loop->index }}" aria-label="Slide {{ $loop->iteration }}"></button>
                @endforeach
            </div>
        @endif
    </div>
</section>

<style>
    .bsl-section { padding: 20px 0; background: var(--white); }
    .bsl-wrap { max-width: 1280px; margin: 0 auto; padding: 0 16px; }
    .bsl-viewport { overflow: hidden; border-radius: 18px; }
    .bsl-track {
        display: flex;
        width: 100%;
        transition: transform .6s cubic-bezier(.22,.9,.32,1);
        will-change: transform;
    }
    .bsl-slide { flex: 0 0 100%; min-width: 0; }
    .bsl-link { display: block; text-decoration: none; color: inherit; }

    .bsl-card {
        position: relative;
        display: flex;
        align-items: center;
        gap: 24px;
        padding: 22px 28px;
        background: linear-gradient(135deg, #0f172a 0%, #1459DD 55%, #2563eb 100%);
        border-radius: 18px;
        overflow: hidden;
        box-shadow: 0 16px 36px -14px rgba(20,89,221,.45);
        font-family: 'Inter', sans-serif;
        animation: bslCardZoom 2.2s ease-in-out infinite !important;
        will-change: transform;
    }
    /* ── Thème "Auto-Enchère" : NOIR + orange accent (#F37021). ── */
    .bsl-card--ae {
        background: linear-gradient(135deg, #000000 0%, #171717 50%, #F37021 100%);
        box-shadow: 0 16px 36px -14px rgba(243,112,33,.45);
    }
    .bsl-card--ae .bsl-glow {
        background: radial-gradient(circle, rgba(243,112,33,.5) 0%, transparent 70%);
    }
    .bsl-card--ae .bsl-badge {
        background: rgba(243,112,33,.22);
        color: #FFB183;
    }
    .bsl-card--ae .bsl-dot {
        background: #F37021;
        box-shadow: 0 0 0 0 rgba(243,112,33,.7);
        animation: bslPulseAe 1.8s ease-in-out infinite;
    }
    .bsl-card--ae .bsl-cta {
        background: #F37021;
        color: #fff;
        box-shadow: 0 8px 20px -6px rgba(243,112,33,.55);
    }
    .bsl-link:hover .bsl-card--ae .bsl-cta { box-shadow: 0 12px 26px -6px rgba(243,112,33,.65); }
    @keyframes bslPulseAe { 0% { box-shadow:0 0 0 0 rgba(243,112,33,.7); } 70% { box-shadow:0 0 0 10px rgba(243,112,33,0); } 100% { box-shadow:0 0 0 0 rgba(243,112,33,0); } }

    /* ── Thème "Vérification légale" : vert (confiance / conformité). ── */
    .bsl-card--legal {
        background: linear-gradient(135deg, #052e16 0%, #15803d 55%, #22c55e 100%);
        box-shadow: 0 16px 36px -14px rgba(34,197,94,.45);
    }
    .bsl-card--legal .bsl-glow {
        background: radial-gradient(circle, rgba(187,247,208,.5) 0%, transparent 70%);
    }
    .bsl-card--legal .bsl-badge {
        background: rgba(187,247,208,.18);
        color: #bbf7d0;
    }
    .bsl-card--legal .bsl-dot {
        background: #bbf7d0;
        box-shadow: 0 0 0 0 rgba(187,247,208,.7);
        animation: bslPulseLegal 1.8s ease-in-out infinite;
    }
    .bsl-card--legal .bsl-cta {
        background: #bbf7d0;
        color: #052e16;
        box-shadow: 0 8px 20px -6px rgba(187,247,208,.55);
    }
    .bsl-link:hover .bsl-card--legal .bsl-cta { box-shadow: 0 12px 26px -6px rgba(187,247,208,.7); }
    @keyframes bslPulseLegal { 0% { box-shadow:0 0 0 0 rgba(187,247,208,.7); } 70% { box-shadow:0 0 0 10px rgba(187,247,208,0); } 100% { box-shadow:0 0 0 0 rgba(187,247,208,0); } }
    .bsl-glow {
        position: absolute;
        top: -60px;
        right: -60px;
        width: 240px;
        height: 240px;
        background: radial-gradient(circle, rgba(253,216,23,.35) 0%, transparent 70%);
        pointer-events: none;
        animation: bslGlow 3.5s ease-in-out infinite;
    }
    html[dir="rtl"] .bsl-glow { right: auto; left: -60px; }
    .bsl-content { flex: 1; min-width: 0; color: #fff; position: relative; z-index: 1; }
    .bsl-badge {
        display: inline-flex; align-items: center; gap: 6px;
        padding: 4px 10px;
        background: rgba(253,216,23,.18);
        color: #FDD817;
        border-radius: 999px;
        font-size: .72rem; font-weight: 800; letter-spacing: .08em;
        text-transform: uppercase; margin-bottom: 8px;
    }
    .bsl-dot {
        width: 7px; height: 7px; border-radius: 50%;
        background: #FDD817;
        box-shadow: 0 0 0 0 rgba(253,216,23,.7);
        animation: bslPulse 1.8s ease-in-out infinite;
    }
    .bsl-title { margin: 0 0 4px; font-size: 1.4rem; font-weight: 900; line-height: 1.15; letter-spacing: -.01em; color: #fff; }
    .bsl-desc  { margin: 0; font-size: .92rem; letter-spacing: .02em; color: rgba(255,255,255,.82); }
    .bsl-cta-wrap { flex-shrink: 0; position: relative; z-index: 1; }
    .bsl-cta {
        display: inline-flex; align-items: center; gap: 8px;
        padding: 13px 22px;
        background: #FDD817;
        color: #0f172a;
        border-radius: 12px;
        font-weight: 800; font-size: .95rem;
        box-shadow: 0 8px 20px -6px rgba(253,216,23,.55);
        position: relative;
    }
    .bsl-link:hover .bsl-cta { transform: translateY(-2px); box-shadow: 0 12px 26px -6px rgba(253,216,23,.65); transition: transform .15s ease, box-shadow .15s ease; }
    .bsl-arrow { animation: bslArrow 1.4s ease-in-out infinite; }
    html[dir="rtl"] .bsl-arrow { transform: scaleX(-1); animation: bslArrowRtl 1.4s ease-in-out infinite; }

    /* Dots */
    .bsl-dots { display: flex; justify-content: center; gap: 8px; margin-top: 14px; }
    .bsl-dot-btn {
        width: 10px; height: 10px; border-radius: 50%;
        background: #cbd5e1;
        border: 0;
        padding: 0;
        cursor: pointer;
        transition: background .2s, transform .2s, width .25s;
    }
    .bsl-dot-btn:hover { background: #94a3b8; }
    .bsl-dot-btn.is-active { background: #1459DD; width: 26px; border-radius: 6px; }

    @keyframes bslCardZoom { 0% { transform: scale(0.94); } 50% { transform: scale(1.06); } 100% { transform: scale(0.94); } }
    @keyframes bslGlow     { 0%,100% { opacity:.55; transform:scale(1); } 50% { opacity:1; transform:scale(1.15); } }
    @keyframes bslPulse    { 0% { box-shadow:0 0 0 0 rgba(253,216,23,.7); } 70% { box-shadow:0 0 0 10px rgba(253,216,23,0); } 100% { box-shadow:0 0 0 0 rgba(253,216,23,0); } }
    @keyframes bslArrow    { 0%,100% { transform:translateX(0); } 50% { transform:translateX(4px); } }
    @keyframes bslArrowRtl { 0%,100% { transform:scaleX(-1) translateX(0); } 50% { transform:scaleX(-1) translateX(4px); } }

    @media (max-width: 720px) {
        .bsl-card { flex-direction: column; align-items: stretch; text-align: center; gap: 14px; padding: 20px; }
        .bsl-title { font-size: 1.2rem; }
        .bsl-cta { width: 100%; justify-content: center; }
    }
</style>

<script>
(function () {
    var track = document.getElementById('bslTrack');
    if (!track) return;
    var count = parseInt(track.dataset.count || '1', 10);
    if (count <= 1) return;

    var dots    = document.querySelectorAll('.bsl-dot-btn');
    var current = 0;
    var timer   = null;
    var AUTO    = 5000;
    var isRtl   = document.documentElement.dir === 'rtl';

    function goTo(i) {
        current = ((i % count) + count) % count;
        // RTL : on inverse le sens du translate pour que "slide suivante"
        // aille à gauche visuellement (direction naturelle de lecture AR).
        var offset = (isRtl ? 1 : -1) * current * 100;
        track.style.transform = 'translateX(' + offset + '%)';
        dots.forEach(function (d, idx) {
            d.classList.toggle('is-active', idx === current);
        });
    }
    function next() { goTo(current + 1); }
    function start() { stop(); timer = setInterval(next, AUTO); }
    function stop()  { if (timer) { clearInterval(timer); timer = null; } }

    dots.forEach(function (d) {
        d.addEventListener('click', function () {
            goTo(parseInt(d.dataset.idx, 10));
            start(); // redémarre le timer après action manuelle
        });
    });

    // Pause au hover (desktop).
    var section = track.closest('.bsl-section');
    if (section) {
        section.addEventListener('mouseenter', stop);
        section.addEventListener('mouseleave', start);
    }

    // Swipe tactile basique.
    var startX = null;
    track.addEventListener('touchstart', function (e) { startX = e.touches[0].clientX; stop(); }, { passive: true });
    track.addEventListener('touchend',   function (e) {
        if (startX === null) return;
        var dx = e.changedTouches[0].clientX - startX;
        if (Math.abs(dx) > 40) { dx < 0 ? next() : goTo(current - 1); }
        startX = null;
        start();
    }, { passive: true });

    start();
})();
</script>
@endif
