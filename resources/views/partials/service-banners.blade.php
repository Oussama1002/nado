{{--
    Partials de bannières services — même design que la bannière "Expert NADO"
    sur la page d'accueil (dégradé sombre, glow jaune, shimmer, badge pulsant,
    CTA jaune, flèche nudge, pop-in entrance, bounce permanent).

    Utilisation (bannière fixe) :
        @include('partials.service-banners', ['service' => 'demandes'])

    Utilisation (rotation quotidienne, 24 h, parmi les Publications actives) :
        @include('partials.service-banners', ['service' => 'auto', 'slot' => 'achat_promo'])
        @include('partials.service-banners', ['service' => 'auto', 'offset' => 2])

    `offset` (int) prime sur `slot` (string) : passer 0, 1, 2… à des includes
    adjacents garantit des bannières différentes entre elles le même jour.

    Chaque include est self-contained : il vérifie l'activation en base et
    rend (ou pas) sa propre carte. La feuille de styles est injectée une
    seule fois par requête grâce à @once.
--}}

@php
    $__srvMap = [
        'demandes' => [
            'placement' => 'front-demandes-banner',
            'icon'      => 'shopping-cart',
            'title'     => __('messages.home.services_demandes_title'),
            'desc'      => __('messages.home.services_demandes_desc'),
            'cta'       => __('messages.home.services_demandes_cta'),
            'badge'     => __('messages.home.services_demandes_title'),
            'url'       => route('demandes.create'),
        ],
        'estim' => [
            'placement' => 'front-estim-banner',
            'icon'      => 'calculator',
            'title'     => __('messages.home.services_estim_title'),
            'desc'      => __('messages.home.services_estim_desc'),
            'cta'       => __('messages.home.services_estim_cta'),
            'badge'     => __('messages.home.services_estim_title'),
            'url'       => route('estimate.page'),
        ],
        'finance' => [
            'placement' => 'front-finance-banner',
            'icon'      => 'credit-card',
            'title'     => __('messages.home.services_finance_title'),
            'desc'      => __('messages.home.services_finance_desc'),
            'cta'       => __('messages.home.services_finance_cta'),
            'badge'     => __('messages.home.services_finance_title'),
            'url'       => url('/financement'),
        ],
        'legal' => [
            'placement' => 'front-legal-banner',
            'icon'      => 'shield-check',
            'title'     => __('messages.home.services_legal_title'),
            'desc'      => __('messages.home.services_legal_desc'),
            'cta'       => __('messages.home.services_legal_cta'),
            'badge'     => __('messages.home.services_legal_title'),
            'url'       => url('/verification-legale'),
        ],
        'ae' => [
            'placement' => 'front-ae-banner',
            'icon'      => 'gavel',
            'title'     => __('messages.home.services_ae_title'),
            'desc'      => __('messages.home.services_ae_desc'),
            'cta'       => __('messages.home.services_ae_cta'),
            'badge'     => __('messages.home.services_ae_title'),
            'url'       => route('nadoautoenchere.prelogin'),
        ],
    ];

    // Mode 'auto' : rotation quotidienne parmi les bannières actives.
    if (($service ?? '') === 'auto') {
        $__srvActivePlacements = \App\Models\Publication::whereIn('placement', array_column($__srvMap, 'placement'))
            ->where('status', 1)
            ->pluck('placement')
            ->all();
        $__srvActiveList = [];
        foreach ($__srvMap as $k => $cfg) {
            if (in_array($cfg['placement'], $__srvActivePlacements, true)) {
                $__srvActiveList[$k] = $cfg;
            }
        }
        $__srvCfg = null;
        if (count($__srvActiveList)) {
            $__dayIdx = (int) floor(time() / 86400);
            if (isset($offset) && is_numeric($offset)) {
                $__slotOff = (int) $offset;
            } else {
                $__slotOff = abs(crc32((string) ($slot ?? 'default')));
            }
            $__pickIdx   = ($__dayIdx + $__slotOff) % count($__srvActiveList);
            $__pickedKey = array_keys($__srvActiveList)[$__pickIdx];
            $__srvCfg    = $__srvActiveList[$__pickedKey];
        }
        $__srvActive = $__srvCfg !== null;
    } else {
        $__srvCfg = $__srvMap[$service ?? ''] ?? null;
        $__srvActive = $__srvCfg && \App\Models\Publication::where('placement', $__srvCfg['placement'])
            ->where('status', 1)
            ->exists();
    }
@endphp

@if($__srvActive)
    <a href="{{ $__srvCfg['url'] }}" class="svc-promo-link">
        <div class="svc-promo-card">
            <div class="svc-promo-glow" aria-hidden="true"></div>
            <div class="svc-promo-shimmer" aria-hidden="true"></div>
            <div class="svc-promo-content">
                <div class="svc-promo-badge">
                    <span class="svc-promo-dot" aria-hidden="true"></span>
                    <span>{{ $__srvCfg['badge'] }}</span>
                </div>
                <h3 class="svc-promo-title">{{ $__srvCfg['title'] }}</h3>
                <p class="svc-promo-desc">{{ $__srvCfg['desc'] }}</p>
            </div>
            <div class="svc-promo-cta-wrap">
                <span class="svc-promo-cta">
                    <span>{{ $__srvCfg['cta'] }}</span>
                    <svg class="svc-promo-arrow" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                        <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
                    </svg>
                </span>
            </div>
        </div>
    </a>

    @once
        <style>
            .svc-promo-link { display: block; text-decoration: none; color: inherit; margin: 16px 0; }
            .svc-promo-card {
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
                /* Zoom IN/OUT permanent — déclaration unique, !important pour
                   échapper à toute surcharge locale. Non gated par
                   prefers-reduced-motion (demande explicite utilisateur). */
                animation: svcCardZoom 2.2s ease-in-out infinite !important;
                will-change: transform;
            }
            .svc-promo-glow {
                position: absolute;
                top: -60px;
                right: -60px;
                width: 240px;
                height: 240px;
                background: radial-gradient(circle, rgba(253,216,23,.35) 0%, transparent 70%);
                pointer-events: none;
                animation: svcGlow 3.5s ease-in-out infinite;
            }
            html[dir="rtl"] .svc-promo-glow { right: auto; left: -60px; }
            .svc-promo-shimmer {
                position: absolute;
                top: 0; bottom: 0;
                width: 140px;
                background: linear-gradient(100deg, transparent 0%, rgba(255,255,255,.14) 50%, transparent 100%);
                transform: skewX(-18deg);
                pointer-events: none;
                animation: svcShimmer 4.5s ease-in-out infinite;
            }
            .svc-promo-content { flex: 1; min-width: 0; color: #fff; position: relative; z-index: 1; }
            .svc-promo-badge {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                padding: 4px 10px;
                background: rgba(253,216,23,.18);
                color: #FDD817;
                border-radius: 999px;
                font-size: .72rem;
                font-weight: 800;
                letter-spacing: .08em;
                text-transform: uppercase;
                margin-bottom: 8px;
            }
            .svc-promo-dot {
                width: 7px; height: 7px; border-radius: 50%;
                background: #FDD817;
                box-shadow: 0 0 0 0 rgba(253,216,23,.7);
                animation: svcPulse 1.8s ease-in-out infinite;
            }
            .svc-promo-title { margin: 0 0 4px; font-size: 1.4rem; font-weight: 900; line-height: 1.15; letter-spacing: -.01em; color: #fff; }
            .svc-promo-desc  { margin: 0; font-size: .92rem; letter-spacing: .02em; color: rgba(255,255,255,.82); }
            .svc-promo-cta-wrap { flex-shrink: 0; position: relative; z-index: 1; }
            .svc-promo-cta {
                display: inline-flex;
                align-items: center;
                gap: 8px;
                padding: 13px 22px;
                background: #FDD817;
                color: #0f172a;
                border-radius: 12px;
                font-weight: 800;
                font-size: .95rem;
                box-shadow: 0 8px 20px -6px rgba(253,216,23,.55);
                position: relative;
                overflow: hidden;
            }
            .svc-promo-cta::after {
                content: '';
                position: absolute; inset: 0;
                background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,.55) 50%, transparent 80%);
                transform: translateX(-120%);
                animation: svcCtaShine 3s ease-in-out infinite;
                pointer-events: none;
            }
            .svc-promo-link:hover .svc-promo-cta { transform: translateY(-2px); box-shadow: 0 12px 26px -6px rgba(253,216,23,.65); transition: transform .15s ease, box-shadow .15s ease; }
            .svc-promo-arrow { animation: svcArrow 1.4s ease-in-out infinite; }
            html[dir="rtl"] .svc-promo-arrow { transform: scaleX(-1); animation: svcArrowRtl 1.4s ease-in-out infinite; }

            @keyframes svcCardZoom {
                0%   { transform: scale(0.94); }
                50%  { transform: scale(1.06); }
                100% { transform: scale(0.94); }
            }
            @keyframes svcGlow       { 0%,100% { opacity:.55; transform:scale(1); } 50% { opacity:1; transform:scale(1.15); } }
            @keyframes svcShimmer    { 0% { left:-25%; } 60%,100% { left:120%; } }
            @keyframes svcPulse      { 0% { box-shadow:0 0 0 0 rgba(253,216,23,.7); } 70% { box-shadow:0 0 0 10px rgba(253,216,23,0); } 100% { box-shadow:0 0 0 0 rgba(253,216,23,0); } }
            @keyframes svcCtaShine   { 0% { transform:translateX(-120%); } 60%,100% { transform:translateX(120%); } }
            @keyframes svcArrow      { 0%,100% { transform:translateX(0); } 50% { transform:translateX(4px); } }
            @keyframes svcArrowRtl   { 0%,100% { transform:scaleX(-1) translateX(0); } 50% { transform:scaleX(-1) translateX(4px); } }

            /* prefers-reduced-motion volontairement NON respecté ici :
               l'utilisateur veut le bounce visible pour tout le monde. */
            @media (max-width: 720px) {
                .svc-promo-card { flex-direction: column; align-items: stretch; text-align: center; gap: 14px; padding: 20px; }
                .svc-promo-title { font-size: 1.2rem; }
                .svc-promo-cta { width: 100%; justify-content: center; }
            }
        </style>
    @endonce
@endif
