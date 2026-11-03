<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $company->name ?? 'Véhicules' }} — Auto Enchères | NADO</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: { extend: {
                fontFamily: { inter: ['Inter', 'sans-serif'] },
                colors:    { primary: '#1459DD', accent: '#F37021' },
            }}
        }
    </script>
    <script src="https://unpkg.com/lucide@latest"></script>
    <style>
        body { font-family: 'Inter', sans-serif; }
        ::selection { background-color: #F37021; color: white; }
        .sidebar-shadow { box-shadow: 0 8px 30px rgb(0 0 0 / 0.04); }
        .select-reset   { -webkit-appearance: none; -moz-appearance: none; appearance: none; background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 24 24' fill='none' stroke='%2364748b' stroke-width='2.5' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E"); background-repeat: no-repeat; background-position: right 1rem center; padding-right: 2.5rem; }
        .line-clamp-2   { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
    </style>
</head>
<body class="min-h-screen bg-slate-50 pb-12 font-inter selection:bg-[#F37021] selection:text-white">

    @php
        $currentCategory = request('category');
    @endphp

    {{-- ── NAVBAR (shared) ── --}}
    @include('nadoautoenchere.partials.navbar')

    <div class="max-w-7xl mx-auto px-6 pt-10">

        {{-- ── Hero ── --}}
        <header class="flex flex-col md:flex-row md:items-end md:justify-between gap-6 mb-12">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1.5 bg-[#F37021]/10 rounded-full mb-4">
                    <i data-lucide="car" class="w-[14px] h-[14px] text-[#F37021]"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-[#F37021]">{{ __('messages.nadoae.hero_badge') }}</span>
                </div>
                <h1 class="text-4xl md:text-5xl font-black text-slate-900 uppercase tracking-tighter">
                    @if($company)
                        {{ __('messages.nadoae.fleet_of') }} <span class="text-[#1459DD]">{{ $company->name }}</span>
                    @else
                        {{ __('messages.nadoae.all_prefix') }} <span class="text-[#1459DD]">{{ __('messages.nadoae.vehicles') }}</span>
                    @endif
                </h1>
                @if($company)
                    <p class="mt-4 text-slate-600 font-medium max-w-xl text-lg flex items-center gap-2">
                        <i data-lucide="map-pin" class="w-[18px] h-[18px] text-slate-400"></i>
                        {{ __('messages.nadoae.based_in') }} <span class="text-slate-900 font-bold">{{ $company->city }}</span>
                    </p>
                @else
                    <p class="mt-4 text-slate-600 font-medium max-w-xl text-lg">
                        {{ __('messages.nadoae.all_vehicles_desc') }}
                    </p>
                @endif
            </div>
            <a href="{{ route('nadoautoenchere.categories') }}"
               class="inline-flex items-center gap-2 px-6 py-3 bg-white text-slate-900 border border-slate-200 rounded-xl font-bold text-sm uppercase tracking-widest hover:bg-slate-50 transition-all self-start md:self-end">
                <i data-lucide="arrow-left" class="w-4 h-4"></i> {{ __('messages.nadoae.back_categories') }}
            </a>
        </header>

        {{-- ── Content ── --}}
        <div class="flex flex-col lg:flex-row gap-8">

            {{-- ── Sidebar / Advanced Search ── --}}
            <aside class="lg:w-80 lg:shrink-0">
                <form method="GET" action="{{ route('nadoautoenchere.index') }}"
                      class="bg-white rounded-3xl p-6 border border-slate-100 sidebar-shadow lg:sticky lg:top-28">

                    @if($currentCategory)
                        <input type="hidden" name="category" value="{{ $currentCategory }}">
                    @endif

                    <div class="flex items-center gap-3 mb-7 border-b border-slate-100 pb-4">
                        <div class="w-9 h-9 rounded-xl bg-[#1459DD]/10 flex items-center justify-center">
                            <i data-lucide="filter" class="w-4 h-4 text-[#1459DD]"></i>
                        </div>
                        <h2 class="text-lg font-black uppercase tracking-tight text-slate-900">{{ __('messages.nadoae.adv_search') }}</h2>
                    </div>

                    <div class="space-y-5">
                        {{-- Search (q — optional, non-breaking) --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('messages.nadoae.search') }}</label>
                            <div class="relative">
                                <i data-lucide="search" class="w-4 h-4 text-slate-400 absolute left-4 top-1/2 -translate-y-1/2 pointer-events-none"></i>
                                <input type="text" name="q" value="{{ request('q') }}" placeholder="{{ __('messages.nadoae.search_ph') }}"
                                    class="w-full pl-11 pr-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 placeholder:text-slate-400 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none">
                            </div>
                        </div>

                        {{-- Marque --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('messages.ventemodal.marque') }}</label>
                            <select name="mark" class="select-reset nado-marks w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_marks') }}</option>
                                @foreach($brands as $brand)
                                    <option value="{{ $brand->id }}" data-id="{{ $brand->id }}" {{ request('mark') == $brand->id ? 'selected' : '' }}>{{ $brand->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Modèle --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">{{ __('messages.ventemodal.modele') }}</label>
                            <select name="modele" class="select-reset nado-models w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_models') }}</option>
                                @foreach($modeles as $model)
                                    <option value="{{ $model->id }}" {{ request('modele') == $model->id ? 'selected' : '' }}>{{ $model->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Année --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">
                                <i data-lucide="calendar" class="w-3 h-3"></i> {{ __('messages.nadoae.year') }}
                            </label>
                            <select name="year" class="select-reset w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_years') }}</option>
                                @for ($i = date('Y'); $i >= 1992; $i--)
                                    <option value="{{ $i }}" {{ request('year') == $i ? 'selected' : '' }}>{{ $i }}</option>
                                @endfor
                            </select>
                        </div>

                        {{-- Carburant --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">
                                <i data-lucide="fuel" class="w-3 h-3"></i> {{ __('messages.ventemodal.fuel') }}
                            </label>
                            <select name="carburant" class="select-reset w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_m') }}</option>
                                @php $fuelLabels = ['Essence'=>'fuel_essence','Diesel'=>'fuel_diesel','Hybride'=>'fuel_hybrid','Electrique'=>'fuel_electric']; @endphp
                                @foreach(['Essence','Diesel','Hybride','Electrique'] as $fuel)
                                    <option value="{{ $fuel }}" {{ request('carburant') == $fuel ? 'selected' : '' }}>{{ __('messages.ventemodal.'.$fuelLabels[$fuel]) }}</option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Boîte --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">
                                <i data-lucide="settings" class="w-3 h-3"></i> {{ __('messages.nadoae.gearbox') }}
                            </label>
                            <select name="box" class="select-reset w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_f') }}</option>
                                <option value="Manuelle"    {{ request('box') == 'Manuelle' ? 'selected' : '' }}>{{ __('messages.ventemodal.box_manual') }}</option>
                                <option value="Automatique" {{ request('box') == 'Automatique' ? 'selected' : '' }}>{{ __('messages.ventemodal.box_auto') }}</option>
                            </select>
                        </div>

                        {{-- État --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">
                                <i data-lucide="alert-circle" class="w-3 h-3"></i> {{ __('messages.nadoae.state') }}
                            </label>
                            <select name="etat" class="select-reset w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_m') }}</option>
                                <option value="Neuf"     {{ request('etat') == 'Neuf' ? 'selected' : '' }}>{{ __('messages.nadoae.neuf') }}</option>
                                <option value="Occasion" {{ request('etat') == 'Occasion' ? 'selected' : '' }}>{{ __('messages.nadoae.occasion') }}</option>
                            </select>
                        </div>

                        {{-- Ville --}}
                        <div>
                            <label class="flex items-center gap-1.5 text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2">
                                <i data-lucide="map-pin" class="w-3 h-3"></i> {{ __('messages.ventemodal.city') }}
                            </label>
                            <select name="city" class="select-reset w-full px-4 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm font-medium text-slate-700 focus:ring-2 focus:ring-[#1459DD]/20 focus:border-[#1459DD] transition-all outline-none cursor-pointer">
                                <option value="">{{ __('messages.nadoae.all_cities') }}</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city }}" {{ request('city') == $city ? 'selected' : '' }}>{{ $city }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="pt-2 flex flex-col gap-2">
                            <button type="submit"
                                class="w-full py-3 rounded-xl bg-[#1459DD] hover:bg-[#0f47b3] text-white text-[11px] font-black uppercase tracking-widest transition-all active:scale-95">
                                {{ __('messages.nadoae.search_btn') }}
                            </button>
                            <a href="{{ route('nadoautoenchere.index', $currentCategory ? ['category' => $currentCategory] : []) }}"
                                class="w-full py-3 text-center rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-slate-900 text-[11px] font-black uppercase tracking-widest transition-all">
                                {{ __('messages.nadoae.reset') }}
                            </a>
                        </div>
                    </div>
                </form>
            </aside>

            {{-- ── Main ── --}}
            <main class="flex-1 min-w-0">
                <div class="flex items-center justify-between mb-8" role="status" aria-live="polite">
                    <p class="text-slate-500 font-medium">
                        <strong class="text-slate-900 font-black">{{ $encheres->count() }}</strong>
                        {{ __('messages.nadoae.vehicles_available') }}
                        @if($company) {{ __('messages.nadoae.in_category') }} @endif
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                    @forelse($encheres as $enchere)
                        @php
                            $img = $enchere->images[0] ?? 'default.jpg';
                            $imgUrl = asset('storage/nadoautoenchere/' . $img);
                            $brandName = strtoupper($enchere->brand?->name ?? '');
                            $modelName = $enchere->modeleRelation?->name ?? '';
                            $certified = (bool) ($enchere->certifie ?? false);
                            $endTs = $enchere->ends_at ? \Carbon\Carbon::parse($enchere->ends_at)->timestamp : null;
                            $isExpired = $endTs && $endTs < time();
                            // Classes communes de la carte, variables selon l'état.
                            $cardBase = 'bg-white rounded-3xl overflow-hidden border shadow-sm flex flex-col no-underline';
                            $cardLive = 'group border-slate-100 hover:shadow-xl hover:border-[#1459DD]/20 transition-all duration-300 cursor-pointer';
                            $cardDead = 'border-slate-200 opacity-60 grayscale cursor-not-allowed pointer-events-none select-none';
                        @endphp

                        @if($isExpired)
                            {{-- Enchère expirée : plus de lien, carte désaturée --}}
                            <div class="{{ $cardBase }} {{ $cardDead }}"
                                 aria-disabled="true" title="{{ __('messages.nadoae.expired_title') }}">
                        @else
                            <a href="{{ route('nadoautoenchere.show', $enchere->id) }}"
                               class="{{ $cardBase }} {{ $cardLive }}">
                        @endif

                            {{-- Image --}}
                            <div class="relative overflow-hidden bg-slate-100 h-48">
                                <img src="{{ $imgUrl }}" alt="{{ $brandName }} {{ $modelName }}"
                                     class="w-full h-full object-cover transition-transform duration-500 {{ $isExpired ? '' : 'group-hover:scale-105' }}">

                                @if($certified && !$isExpired)
                                    <span aria-label="{{ __('messages.nadoae.nado_expert_aria') }}"
                                          class="absolute top-4 left-4 px-3 py-1.5 bg-white/95 backdrop-blur-sm text-[#1459DD] rounded-lg text-xs font-black uppercase tracking-wider shadow-sm flex items-center gap-1">
                                        <i data-lucide="alert-circle" class="w-3 h-3"></i> NADO Expert
                                    </span>
                                @endif

                                @if($isExpired)
                                    <span class="absolute top-4 left-4 px-3 py-1.5 bg-slate-900 text-white rounded-lg text-xs font-black uppercase tracking-wider shadow-sm">
                                        {{ __('messages.nadoae.expired') }}
                                    </span>
                                    <div class="absolute inset-0 bg-slate-900/10"></div>
                                @elseif($endTs)
                                    <span class="absolute bottom-3 right-3 countdown px-2.5 py-1 bg-slate-900/80 backdrop-blur-sm text-white rounded-md text-[10px] font-black uppercase tracking-wider"
                                          data-end="{{ $endTs }}">…</span>
                                @endif
                            </div>

                            {{-- Body --}}
                            <div class="p-6 flex-1 flex flex-col">
                                <div class="mb-4">
                                    <div class="text-xs font-black uppercase tracking-widest text-slate-400 mb-1">
                                        {{ $brandName ?: '—' }}
                                    </div>
                                    <h3 class="text-lg font-black text-slate-900 leading-tight group-hover:text-[#1459DD] transition-colors line-clamp-2">
                                        {{ $modelName ?: ($enchere->titre ?? __('messages.nadoae.vehicle')) }}
                                    </h3>
                                </div>

                                <div class="grid grid-cols-2 gap-y-3 gap-x-2 mb-6">
                                    <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                                        <i data-lucide="calendar" class="w-[14px] h-[14px] text-slate-400" aria-hidden="true"></i>
                                        <span>{{ $enchere->year ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                                        <i data-lucide="gauge" class="w-[14px] h-[14px] text-slate-400" aria-hidden="true"></i>
                                        <span>{{ number_format($enchere->km ?? 0, 0, ',', ' ') }} km</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                                        <i data-lucide="fuel" class="w-[14px] h-[14px] text-slate-400" aria-hidden="true"></i>
                                        <span>{{ $enchere->carburant ?? '—' }}</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-sm text-slate-600 font-medium">
                                        <i data-lucide="settings" class="w-[14px] h-[14px] text-slate-400" aria-hidden="true"></i>
                                        <span>{{ $enchere->box ?? '—' }}</span>
                                    </div>
                                </div>

                                <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                                    <div>
                                        <div class="text-[10px] font-black text-slate-400 uppercase tracking-widest mb-0.5">{{ __('messages.nadoae.mise_a_prix') }}</div>
                                        <div class="text-xl font-black text-[#F37021]">
                                            <span class="sr-only">{{ __('messages.nadoae.price_sr') }}&nbsp;</span>
                                            {{ number_format($enchere->prix ?? 0, 0, ',', ' ') }}
                                            <span class="text-sm">DH</span>
                                        </div>
                                    </div>
                                    <div class="w-10 h-10 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center transition-all group-hover:bg-[#1459DD] group-hover:border-[#1459DD]">
                                        <i data-lucide="arrow-right" class="w-4 h-4 text-slate-400 group-hover:text-white transition-colors"></i>
                                    </div>
                                </div>
                            </div>
                        @if($isExpired)
                            </div>
                        @else
                            </a>
                        @endif
                    @empty
                        <div class="col-span-1 md:col-span-2 xl:col-span-3 py-20 text-center bg-white rounded-3xl border border-slate-100">
                            <div class="w-16 h-16 bg-slate-50 rounded-full mx-auto flex items-center justify-center mb-4">
                                <i data-lucide="search" class="w-7 h-7 text-slate-400"></i>
                            </div>
                            <h3 class="text-xl font-black text-slate-900 mb-2">{{ __('messages.nadoae.no_vehicle') }}</h3>
                            <p class="text-slate-500 font-medium max-w-md mx-auto">
                                {{ __('messages.nadoae.no_vehicle_desc') }}
                            </p>
                        </div>
                    @endforelse
                </div>
            </main>
        </div>
    </div>

    <script>
        lucide.createIcons();

        // Cascade models from brand
        document.querySelectorAll('select.nado-marks').forEach(brandSelect => {
            brandSelect.addEventListener('change', function () {
                const brandId = this.value;
                const modelSelect = this.closest('form').querySelector('select.nado-models');
                if (!modelSelect) return;
                modelSelect.innerHTML = '<option value="">Chargement…</option>';
                if (!brandId) { modelSelect.innerHTML = '<option value="">' + @json(__('messages.nadoae.all_models')) + '</option>'; return; }
                fetch(`/client/models-by-brand/${brandId}`)
                    .then(r => r.json())
                    .then(data => {
                        modelSelect.innerHTML = '<option value="">' + @json(__('messages.nadoae.all_models')) + '</option>';
                        data.forEach(m => {
                            modelSelect.insertAdjacentHTML('beforeend',
                                `<option value="${m.id}">${m.name}</option>`);
                        });
                    })
                    .catch(() => { modelSelect.innerHTML = '<option value="">Erreur</option>'; });
            });
        });

        // Countdown timers on each card
        document.querySelectorAll('.countdown').forEach(el => {
            const endTs = parseInt(el.dataset.end, 10);
            function tick() {
                const now = Math.floor(Date.now() / 1000);
                const diff = endTs - now;
                if (!endTs || diff <= 0) { el.textContent = 'Expirée'; return; }
                const d = Math.floor(diff / 86400);
                const h = Math.floor((diff % 86400) / 3600);
                const m = Math.floor((diff % 3600) / 60);
                const s = diff % 60;
                el.textContent = (d > 0 ? d + 'j ' : '') + String(h).padStart(2,'0') + 'h ' + String(m).padStart(2,'0') + 'm ' + String(s).padStart(2,'0') + 's';
            }
            tick();
            setInterval(tick, 1000);
        });
    </script>
</body>
</html>
