{{--
    Partials de bannières services — chaque bannière est une mini-carte codée,
    affichée uniquement si sa Publication (placement correspondant) est status=1.

    Utilisation (bannière fixe) :
        @include('partials.service-banners', ['service' => 'demandes'])
        @include('partials.service-banners', ['service' => 'estim'])
        @include('partials.service-banners', ['service' => 'finance'])
        @include('partials.service-banners', ['service' => 'legal'])
        @include('partials.service-banners', ['service' => 'ae'])

    Utilisation (rotation quotidienne, 24 h) :
        @include('partials.service-banners', ['service' => 'auto', 'slot' => 'achat_promo'])
        @include('partials.service-banners', ['service' => 'auto', 'slot' => 'home_mid'])

    En mode 'auto', la bannière change chaque jour UTC parmi les Publications
    actives. Deux emplacements avec des `slot` différents affichent des
    bannières différentes le même jour (offset dérivé du nom du slot) ; même
    `slot` ⇒ même bannière partout. Si une seule Publication est active,
    elle s'affiche toujours.

    Chaque include est self-contained : il vérifie l'activation en base et
    rend (ou pas) sa propre carte. La feuille de styles est injectée une
    seule fois par requête grâce à @once.
--}}

@php
    $__srvMap = [
        'demandes' => [
            'placement' => 'front-demandes-banner',
            'icon'      => 'shopping-cart',
            'grad'      => 'linear-gradient(135deg,#1459DD,#2563eb)',
            'title'     => __('messages.home.services_demandes_title'),
            'desc'      => __('messages.home.services_demandes_desc'),
            'cta'       => __('messages.home.services_demandes_cta'),
            'url'       => route('demandes.create'),
        ],
        'estim' => [
            'placement' => 'front-estim-banner',
            'icon'      => 'calculator',
            'grad'      => 'linear-gradient(135deg,#16a34a,#22c55e)',
            'title'     => __('messages.home.services_estim_title'),
            'desc'      => __('messages.home.services_estim_desc'),
            'cta'       => __('messages.home.services_estim_cta'),
            'url'       => route('estimate.page'),
        ],
        'finance' => [
            'placement' => 'front-finance-banner',
            'icon'      => 'credit-card',
            'grad'      => 'linear-gradient(135deg,#d97706,#f59e0b)',
            'title'     => __('messages.home.services_finance_title'),
            'desc'      => __('messages.home.services_finance_desc'),
            'cta'       => __('messages.home.services_finance_cta'),
            'url'       => url('/financement'),
        ],
        'legal' => [
            'placement' => 'front-legal-banner',
            'icon'      => 'shield-check',
            'grad'      => 'linear-gradient(135deg,#7c3aed,#a855f7)',
            'title'     => __('messages.home.services_legal_title'),
            'desc'      => __('messages.home.services_legal_desc'),
            'cta'       => __('messages.home.services_legal_cta'),
            'url'       => url('/verification-legale'),
        ],
        'ae' => [
            'placement' => 'front-ae-banner',
            'icon'      => 'gavel',
            'grad'      => 'linear-gradient(135deg,#0f172a,#1e293b)',
            'title'     => __('messages.home.services_ae_title'),
            'desc'      => __('messages.home.services_ae_desc'),
            'cta'       => __('messages.home.services_ae_cta'),
            'url'       => route('nadoautoenchere.prelogin'),
        ],
    ];
    // Mode 'auto' : rotation quotidienne parmi les bannières actives.
    if (($service ?? '') === 'auto') {
        $__srvActivePlacements = \App\Models\Publication::whereIn('placement', array_column($__srvMap, 'placement'))
            ->where('status', 1)
            ->pluck('placement')
            ->all();
        // Reconstruit un tableau ordonné { key => cfg } des services actifs
        // en préservant l'ordre déclaré dans $__srvMap (déterministe).
        $__srvActiveList = [];
        foreach ($__srvMap as $k => $cfg) {
            if (in_array($cfg['placement'], $__srvActivePlacements, true)) {
                $__srvActiveList[$k] = $cfg;
            }
        }
        $__srvCfg = null;
        if (count($__srvActiveList)) {
            // Index du jour UTC (incrément toutes les 24h) + offset.
            // `offset` (int) prime sur le `slot` (string) : passer des offsets
            // séquentiels (0, 1, 2, …) garantit des bannières différentes
            // entre includes adjacents — pas de collision CRC32 possible.
            $__dayIdx    = (int) floor(time() / 86400);
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
    <a href="{{ $__srvCfg['url'] }}" class="srv-card-std" style="--srv-grad: {{ $__srvCfg['grad'] }};">
        <div class="srv-card-std__ico" aria-hidden="true">
            <i data-lucide="{{ $__srvCfg['icon'] }}" style="width:22px;height:22px;"></i>
        </div>
        <div class="srv-card-std__body">
            <div class="srv-card-std__title">{{ $__srvCfg['title'] }}</div>
            <div class="srv-card-std__desc">{{ $__srvCfg['desc'] }}</div>
        </div>
        <div class="srv-card-std__cta">
            <span>{{ $__srvCfg['cta'] }}</span>
            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.6" stroke-linecap="round" stroke-linejoin="round">
                <line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>
            </svg>
        </div>
    </a>

    @once
        <style>
            .srv-card-std {
                position: relative;
                display: flex;
                align-items: center;
                gap: 12px;
                padding: 16px 18px;
                margin: 8px auto;
                max-width: 100%;
                background: var(--srv-grad, #1459DD);
                color: #fff;
                border-radius: 16px;
                text-decoration: none;
                box-shadow: 0 12px 26px -14px rgba(15,23,42,.4);
                overflow: hidden;
                transition: transform .15s ease, box-shadow .15s ease;
                font-family: 'Inter', sans-serif;
            }
            .srv-card-std::after {
                content: '';
                position: absolute; inset: 0;
                background: linear-gradient(120deg, transparent 20%, rgba(255,255,255,.18) 50%, transparent 80%);
                transform: translateX(-120%);
                transition: transform .6s ease;
                pointer-events: none;
            }
            .srv-card-std:hover { transform: translateY(-3px); box-shadow: 0 18px 34px -12px rgba(15,23,42,.5); color: #fff; }
            .srv-card-std:hover::after { transform: translateX(120%); }
            .srv-card-std__ico {
                width: 44px; height: 44px; flex-shrink: 0;
                border-radius: 12px;
                background: rgba(255,255,255,.18);
                display: flex; align-items: center; justify-content: center;
                position: relative; z-index: 1;
            }
            .srv-card-std__body { flex: 1; min-width: 0; position: relative; z-index: 1; }
            .srv-card-std__title { font-weight: 900; font-size: 1.02rem; line-height: 1.2; }
            .srv-card-std__desc  { font-size: .85rem; opacity: .85; line-height: 1.35; margin-top: 2px; }
            .srv-card-std__cta {
                flex-shrink: 0;
                display: inline-flex; align-items: center; gap: 6px;
                padding: 8px 14px;
                background: rgba(255,255,255,.2);
                border-radius: 999px;
                font-size: .82rem; font-weight: 800;
                position: relative; z-index: 1;
            }
            @media (max-width: 560px) {
                .srv-card-std { padding: 14px; gap: 10px; }
                .srv-card-std__title { font-size: .95rem; }
                .srv-card-std__desc  { display: none; }
                .srv-card-std__cta   { padding: 6px 10px; font-size: .75rem; }
                .srv-card-std__ico   { width: 38px; height: 38px; }
            }
        </style>
    @endonce
@endif
