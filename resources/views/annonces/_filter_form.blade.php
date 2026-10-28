<form action="{{ route('achat.index') }}" method="GET" id="{{ $formId ?? 'filterForm' }}">
    @if(request('bodywork'))
        <input type="hidden" name="bodywork" value="{{ request('bodywork') }}">
    @endif

    <div class="pp-filter-blocks">

        {{-- 1. Véhicule --}}
        <div class="pp-filter-block">
            <div class="pp-filter-block-head">
                <div class="pp-icon-box blue-light">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M5 17H3a2 2 0 01-2-2V5a2 2 0 012-2h11l5 5v5a2 2 0 01-2 2h-2"/>
                        <circle cx="7" cy="17" r="2"/><circle cx="17" cy="17" r="2"/>
                    </svg>
                </div>
                <span>{{ __('messages.catalog.vehicle') }}</span>
            </div>
            <select name="marque" class="pp-select nado-marks">
                <option value="">{{ __('messages.catalog.all_brands') }}</option>
                @foreach ($brands as $brand)
                    <option value="{{ $brand->name }}" data-id="{{ $brand->id }}"
                        {{ request('marque') == $brand->name ? 'selected' : '' }}>
                        {{ $brand->name }}
                    </option>
                @endforeach
            </select>
            <select name="modele" class="pp-select nado-models">
                <option value="">{{ __('messages.catalog.all_models') }}</option>
            </select>
        </div>

        {{-- 2. Année --}}
        <div class="pp-filter-block">
            <div class="pp-filter-block-head">
                <div class="pp-icon-box blue-light">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/>
                        <line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span>{{ __('messages.catalog.year') }}</span>
            </div>
            <div class="pp-grid-2">
                <select name="annee_min" class="pp-select" style="margin-bottom:0;">
                    <option value="">{{ __('messages.catalog.from') }}</option>
                    @for ($i = date('Y'); $i >= 1992; $i--)
                        <option value="{{ $i }}" {{ request('annee_min') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
                <select name="annee_max" class="pp-select" style="margin-bottom:0;">
                    <option value="">{{ __('messages.catalog.to') }}</option>
                    @for ($i = date('Y'); $i >= 1992; $i--)
                        <option value="{{ $i }}" {{ request('annee_max') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>
        </div>

        {{-- 3. Motorisation --}}
        <div class="pp-filter-block">
            <div class="pp-filter-block-head">
                <div class="pp-icon-box blue-light">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M4 18V8a2 2 0 012-2h12a2 2 0 012 2v10M4 18h16M8 18v2m8-2v2M12 6V4m0 0a2 2 0 00-2 2m2-2a2 2 0 012 2"/>
                    </svg>
                </div>
                <span>{{ __('messages.catalog.motorisation') }}</span>
            </div>
            <span class="pp-chip-label">{{ __('messages.catalog.energy') }}</span>
            <div class="pp-chips">
                @foreach (['Diesel', 'Essence', 'Électrique', 'Hybride'] as $fuel)
                    <button type="button" class="pp-chip {{ request('carburant') === $fuel ? 'active' : '' }}"
                        data-fuel="{{ $fuel }}">{{ __('messages.catalog.fuel')[$fuel] ?? $fuel }}</button>
                @endforeach
            </div>
            <input type="hidden" name="carburant" value="{{ request('carburant') }}">

            <span class="pp-chip-label" style="margin-top:8px;">{{ __('messages.catalog.gearbox') }}</span>
            <div class="pp-chips">
                @foreach (['Manuelle', 'Automatique'] as $trans)
                    <button type="button" class="pp-chip grid {{ request('box') === $trans ? 'active' : '' }}"
                        data-trans="{{ $trans }}">{{ __('messages.catalog.box')[$trans] ?? $trans }}</button>
                @endforeach
            </div>
            <input type="hidden" name="box" value="{{ request('box') }}">
        </div>

        {{-- 4. Budget --}}
        <div class="pp-filter-block">
            <div class="pp-filter-block-head">
                <div class="pp-icon-box blue-light">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <span>{{ __('messages.catalog.budget') }}</span>
            </div>
            <div class="pp-slider-head">
                <label>{{ __('messages.catalog.max_price') }}</label>
                <span class="pp-slider-val" data-display="priceVal">{{ number_format(request('prix', 800000), 0, ',', ' ') }} MAD</span>
            </div>
            <input type="range" name="prix" id="priceRange" class="pp-range"
                min="0" max="5000000" step="50000" value="{{ request('prix', 800000) }}">
            <div class="pp-slider-ticks"><span>0</span><span>5 000 000 MAD</span></div>
        </div>

        {{-- 5. Kilométrage --}}
        <div class="pp-filter-block">
            <div class="pp-filter-block-head">
                <div class="pp-icon-box blue-light">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M12 2a10 10 0 100 20A10 10 0 0012 2z"/><path d="M12 6v6l4 2"/>
                    </svg>
                </div>
                <span>{{ __('messages.catalog.mileage') }}</span>
            </div>
            <div class="pp-slider-head">
                <label>{{ __('messages.catalog.max') }}</label>
                <span class="pp-slider-val" data-display="kmVal">{{ number_format(request('km', 150000), 0, ',', ' ') }} KM</span>
            </div>
            <input type="range" name="km" id="kmRange" class="pp-range"
                min="0" max="250000" step="5000" value="{{ request('km', 150000) }}">
            <div class="pp-slider-ticks"><span>0</span><span>250 000 KM</span></div>
        </div>

        {{-- 6. Localisation --}}
        <div class="pp-filter-block">
            <div class="pp-filter-block-head">
                <div class="pp-icon-box blue-light">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/>
                    </svg>
                </div>
                <span>{{ __('messages.catalog.location') }}</span>
            </div>
            <select name="ville" class="pp-select" style="margin-bottom:0;">
                <option value="">{{ __('messages.catalog.all_cities') }}</option>
                @foreach ($ville as $v)
                    <option value="{{ $v->name }}" {{ request('ville') === $v->name ? 'selected' : '' }}>{{ $v->name }}</option>
                @endforeach
            </select>
        </div>

        {{-- CTA --}}
        <button type="submit" class="pp-search-btn">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
            </svg>
            {{ __('messages.catalog.search') }}
        </button>

    </div>
</form>
