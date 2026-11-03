@forelse ($categories as $category)
    @php
        $now = \Carbon\Carbon::now();

        // Prefer a still-open enchere (ends_at in the future) as representative.
        // If none exists, fall back to the most recent one just for display.
        $enchere = \App\Models\NadoAutoEnchere::where('phone', $category->phone)
                    ->where('active', 1)
                    ->where('ends_at', '>', $now)
                    ->orderBy('ends_at', 'asc')
                    ->first()
            ?? \App\Models\NadoAutoEnchere::where('phone', $category->phone)
                    ->where('active', 1)
                    ->orderBy('ends_at', 'desc')
                    ->first();

        // Count only vehicles still open for this category — expired ones
        // shouldn't inflate the "véhicules disponibles" number.
        $openCount = \App\Models\NadoAutoEnchere::where('phone', $category->phone)
                    ->where('active', 1)
                    ->where('ends_at', '>', $now)
                    ->count();

        $endsAt   = $enchere?->ends_at ? \Carbon\Carbon::parse($enchere->ends_at) : null;
        $startsAt = $enchere?->starts_at ? \Carbon\Carbon::parse($enchere->starts_at) : null;

        if (!$endsAt) {
            $status = 'Terminé';
            $ended  = true;
        } elseif ($endsAt->isPast()) {
            $status = 'Terminé';
            $ended  = true;
        } elseif ($startsAt && $startsAt->isFuture()) {
            $status = 'À venir';
            $ended  = false;
        } else {
            $status = 'En cours';
            $ended  = false;
        }

        $remaining       = $endsAt ? $endsAt->timestamp : null;
        $endsAtFormatted = $endsAt ? $endsAt->format('d/m/Y H:i') : '—';

        $companyName = $category->company ?? $category->client_name ?? 'NADO.ma';

        $logoFile = $category->logo ?? null;
        $logoUrl  = ($logoFile && file_exists(public_path('storage/nadoautoenchere/logo/' . $logoFile)))
                    ? asset('storage/nadoautoenchere/logo/' . $logoFile)
                    : asset('assets/images/logoenchere.jpg');

        $city = $enchere?->city ?? '—';

        $pillClasses = [
            'En cours' => 'bg-green-50 text-green-600 border-green-200',
            'À venir'  => 'bg-blue-50 text-blue-600 border-blue-200',
            'Terminé'  => 'bg-slate-50 text-slate-500 border-slate-200',
        ][$status];

        $clockColor = $status === 'En cours' ? 'text-green-600' : 'text-slate-400';

        $href = $ended ? '#' : route('nadoautoenchere.index', ['category' => $category->phone]);
    @endphp

    <a href="{{ $href }}"
       data-ends="{{ $remaining }}"
       aria-label="{{ __('messages.nadoae.view_auctions_of') }} {{ $companyName }}"
       class="group bg-white rounded-3xl overflow-hidden border border-slate-100 card-shadow hover:card-shadow-hover hover:border-[#1459DD]/20 transition-all duration-300 flex flex-col no-underline {{ $ended ? 'pointer-events-none opacity-60' : 'cursor-pointer' }}">

        {{-- Image / logo header --}}
        <div class="relative overflow-hidden bg-slate-100 h-40">
            <img src="{{ $logoUrl }}" alt="{{ $companyName }} logo"
                 class="w-full h-full object-cover blur-[2px] brightness-[0.7] transition-transform duration-500 group-hover:scale-105">
            <div class="absolute inset-0 bg-gradient-to-t from-slate-900/80 to-transparent"></div>
            <div class="absolute inset-x-0 bottom-0 p-5 flex items-end justify-between">
                <h4 class="text-2xl font-black text-white truncate max-w-[80%] drop-shadow-md">
                    {{ $companyName }}
                </h4>
                <div class="w-10 h-10 bg-white/20 backdrop-blur-md rounded-xl border border-white/20 flex items-center justify-center transition-all group-hover:bg-[#F37021] group-hover:border-[#F37021]">
                    <i data-lucide="arrow-right" class="w-4 h-4 text-white"></i>
                </div>
            </div>
        </div>

        {{-- Body --}}
        <div class="p-6 flex-1 flex flex-col">
            <div class="flex items-center justify-between gap-3 mb-5">
                <span class="w-fit px-3 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $pillClasses }}">
                    {{ ['En cours' => __('messages.nadoae.status_ongoing'), 'À venir' => __('messages.nadoae.status_upcoming'), 'Terminé' => __('messages.nadoae.status_ended')][$status] ?? $status }}
                </span>
                <div class="flex items-center gap-1.5 text-slate-500 text-[11px] font-bold uppercase tracking-wide">
                    <i data-lucide="clock" class="w-3 h-3 {{ $clockColor }}"></i>
                    <span>{{ __('messages.nadoae.ends_on') }}</span>
                    <span class="text-slate-700">{{ $endsAtFormatted }}</span>
                </div>
            </div>

            <div class="space-y-4 mb-6">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                        <i data-lucide="map-pin" class="w-4 h-4 text-[#1459DD]"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">{{ $city }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-slate-50 flex items-center justify-center">
                        <i data-lucide="car" class="w-4 h-4 text-[#F37021]"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-600">
                        <span class="text-slate-900 font-bold">{{ $openCount }}</span>
                        {{ __('messages.nadoae.vehicles_available') }}
                    </span>
                </div>
            </div>

            @if(!$ended)
                <div class="mt-auto pt-4 border-t border-slate-100 flex items-center justify-between">
                    <span class="remaining-time text-xs font-black uppercase tracking-widest {{ $status === 'En cours' ? 'text-green-600' : 'text-[#1459DD]' }}">
                        @if($status === 'À venir') {{ __('messages.nadoae.starts_soon') }} @else {{ __('messages.nadoae.loading') }} @endif
                    </span>
                    <span class="text-[11px] font-bold uppercase tracking-widest text-[#F37021] group-hover:translate-x-1 transition-transform inline-flex items-center gap-1">
                        {{ __('messages.nadoae.see_vehicles') }} <i data-lucide="arrow-up-right" class="w-3 h-3"></i>
                    </span>
                </div>
            @endif
        </div>
    </a>
@empty
    <div class="col-span-1 md:col-span-2 py-20 text-center bg-white rounded-3xl border border-slate-100">
        <div class="w-16 h-16 bg-slate-50 rounded-full mx-auto flex items-center justify-center mb-4">
            <i data-lucide="search" class="w-7 h-7 text-slate-400"></i>
        </div>
        <h3 class="text-xl font-black text-slate-900 mb-2">{{ __('messages.nadoae.no_results') }}</h3>
        <p class="text-slate-500 font-medium">{{ __('messages.nadoae.no_results_desc') }}</p>
    </div>
@endforelse
