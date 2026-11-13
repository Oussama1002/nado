@php
    $authClient = Auth::guard('client')->user();
@endphp

{{-- dir=ltr keeps the bar laid out like the main NADO navbar (logo on the
     left, actions on the right) even in Arabic; the labels still render RTL. --}}
<header class="sticky top-0 z-50" dir="ltr">
    <div class="bg-[#F37021] text-slate-900 py-2.5 px-3 sm:px-4 border-b border-black/5">
        <div class="max-w-7xl mx-auto flex justify-between items-center gap-2">
            <div class="flex items-center gap-3 sm:gap-4 min-w-0">
                {{-- Email : masqué sur très petits écrans (le téléphone suffit) --}}
                <div class="hidden min-[420px]:flex items-center gap-2 text-[11px] font-black uppercase tracking-widest opacity-80 whitespace-nowrap">
                    <i data-lucide="mail" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#1459DD] flex-shrink-0"></i>
                    <a href="mailto:contact@nado.ma" class="hover:text-[#1459DD] transition-colors">contact@nado.ma</a>
                </div>
                <div class="flex items-center gap-2 text-[10px] sm:text-[11px] font-black uppercase tracking-widest opacity-80 whitespace-nowrap">
                    <i data-lucide="phone-call" class="w-2.5 h-2.5 sm:w-3 sm:h-3 text-[#1459DD] flex-shrink-0"></i>
                    <a href="tel:+212520070611" class="hover:text-[#1459DD] transition-colors">+212 520 070 611</a>
                </div>
            </div>
            {{-- Sur mobile, `contents` sort le groupe de son conteneur : le téléphone,
                 Nado Radio et le switch FR/AR deviennent 3 éléments flex directs de la
                 barre justify-between → Nado Radio se place au centre, à l'écart du
                 téléphone (évite le chevauchement). `sm:flex` rétablit le groupe. --}}
            <div class="contents sm:flex sm:items-center sm:gap-5 sm:flex-shrink-0">
                {{-- Réseaux sociaux : masqués sur mobile pour libérer de la place --}}
                <div class="hidden sm:flex items-center gap-5">
                    <a href="#" class="hover:text-[#1459DD] transition-colors opacity-60 hover:opacity-100"><i data-lucide="facebook" class="w-5 h-5" stroke-width="1.5"></i></a>
                    <a href="#" class="hover:text-[#1459DD] transition-colors opacity-60 hover:opacity-100"><i data-lucide="instagram" class="w-5 h-5" stroke-width="1.5"></i></a>
                    <a href="#" class="hover:text-[#1459DD] transition-colors opacity-60 hover:opacity-100"><i data-lucide="linkedin" class="w-5 h-5" stroke-width="1.5"></i></a>
                </div>
                @if(feature_on('radio.enabled'))
                    <a href="{{ route('nado-radio.index') }}" title="Nado Radio" aria-label="Nado Radio" class="flex items-center gap-1.5 sm:ml-3 text-[11px] font-black uppercase tracking-widest opacity-80 hover:text-[#1459DD] hover:opacity-100 transition-colors flex-shrink-0">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-3.5 h-3.5 sm:w-4 sm:h-4 flex-shrink-0" aria-hidden="true"><path d="M7 8 18 3"/><rect x="3" y="8" width="18" height="12" rx="2"/><circle cx="16" cy="14" r="2.5" fill="#e11d48" stroke="#e11d48"/><path d="M7 12h5M7 16h4"/></svg><span class="text-[9px] sm:text-[11px]">Nado Radio</span>
                    </a>
                @endif
                @include('partials.lang-switcher')
            </div>
        </div>
    </div>

    <nav class="bg-white border-b border-slate-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 h-20 flex items-center justify-between gap-3">
            <div class="flex items-center gap-10">
                <a href="{{ route('nadoautoenchere.categories') }}" class="flex items-center hover:scale-105 transition-transform no-underline">
                    <img src="{{ asset('assets/images/logoenchere.jpg') }}" alt="NADO Auto Enchère" class="h-10 w-auto object-contain" />
                </a>
                <div class="hidden lg:flex items-center gap-2">
                    @hasSection('na_nav_links')
                        @yield('na_nav_links')
                    @else
                        <a href="{{ route('nadoautoenchere.categories') }}"
                           class="text-[13px] font-bold uppercase tracking-wide px-3 py-2 rounded-2xl transition-all text-slate-700 hover:text-[#1459DD] hover:bg-slate-100">
                            Catégories
                        </a>
                        <a href="{{ route('nadoautoenchere.index') }}"
                           class="text-[13px] font-bold uppercase tracking-wide px-3 py-2 rounded-2xl transition-all text-slate-700 hover:text-[#1459DD] hover:bg-slate-100">
                            Tous les véhicules
                        </a>
                    @endif
                </div>
            </div>

            <div class="flex items-center gap-3 ml-auto">
                {{-- Retour au site NADO (front) : achat immédiat (hors enchère) --}}
                <a href="{{ route('index') }}"
                   class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-white border border-slate-200 hover:bg-slate-50 text-slate-900 text-[12px] font-black uppercase tracking-wide transition-all no-underline shrink-0"
                   title="Achat immédiat">
                    <img src="{{ asset('storage/logos/UAfjepY72Xe7grWDva9cqSlRFsRKNZPV3HBDbBP1.png') }}" alt="NADO" class="h-5 w-auto object-contain" />
                    <span class="hidden sm:inline">Achat immédiat</span>
                </a>
                @if($authClient)
                    {{-- Compte : le nom de l'entreprise ouvre le menu (Mon espace / Déconnexion) --}}
                    <div class="na-account" style="position:relative;">
                        <button type="button" id="naAccountBtn" aria-haspopup="true" aria-expanded="false"
                            class="flex items-center gap-2 px-3 py-2 rounded-2xl bg-slate-50 border border-slate-200 hover:bg-slate-100 transition-all cursor-pointer">
                            <span class="w-7 h-7 rounded-full bg-[#1459DD]/10 flex items-center justify-center shrink-0">
                                <i data-lucide="user" class="w-3.5 h-3.5 text-[#1459DD]"></i>
                            </span>
                            <span class="hidden md:inline text-[12px] font-bold uppercase tracking-wide text-slate-700 truncate max-w-[140px]"
                                  title="{{ $authClient->company ?? $authClient->name }}">
                                {{ $authClient->company ?? $authClient->name }}
                            </span>
                            <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400 shrink-0"></i>
                        </button>

                        <div id="naAccountMenu"
                             class="hidden absolute right-0 top-full mt-2 w-56 bg-white border border-slate-200 rounded-2xl shadow-xl overflow-hidden z-[1200]">
                            <a href="{{ route('client.dashboard') }}"
                               class="flex items-center gap-3 px-4 py-3 text-[13px] font-bold text-slate-700 hover:bg-slate-50 transition-colors no-underline">
                                <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                                Mon espace
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="border-t border-slate-100 m-0">
                                @csrf
                                <button type="submit"
                                    class="flex items-center gap-3 w-full px-4 py-3 text-[13px] font-bold text-red-600 hover:bg-red-50 transition-colors text-left bg-transparent border-0 cursor-pointer">
                                    <i data-lucide="log-out" class="w-4 h-4"></i>
                                    Déconnexion
                                </button>
                            </form>
                        </div>
                    </div>
                @else
                    <a href="{{ route('nadoautoenchere.login') }}"
                        class="hidden sm:inline-flex items-center gap-2 px-4 py-2.5 rounded-2xl border border-slate-200 hover:border-[#1459DD] hover:text-[#1459DD] text-slate-900 font-extrabold text-[13px] uppercase tracking-widest transition-all">
                        {{ __('messages.ae.login') }}
                    </a>
                    <a href="{{ route('nadoautoenchere.register') }}"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-2xl bg-[#F37021]/20 border border-[#F37021]/50 hover:bg-[#F37021] text-slate-900 font-extrabold text-[13px] uppercase tracking-widest transition-all active:scale-95">
                        <span class="whitespace-nowrap">{{ __('messages.nav.create_account') }}</span>
                    </a>
                @endif
            </div>
        </div>
    </nav>
</header>

@if($authClient ?? null)
<script>
    // Account menu: click to open, outside-click or Escape to close.
    (function () {
        var btn = document.getElementById('naAccountBtn');
        var menu = document.getElementById('naAccountMenu');
        if (!btn || !menu) return;

        function close() {
            menu.classList.add('hidden');
            btn.setAttribute('aria-expanded', 'false');
        }

        btn.addEventListener('click', function (e) {
            e.stopPropagation();
            var open = menu.classList.toggle('hidden');
            btn.setAttribute('aria-expanded', open ? 'false' : 'true');
        });

        document.addEventListener('click', function (e) {
            if (!menu.contains(e.target) && !btn.contains(e.target)) close();
        });

        document.addEventListener('keydown', function (e) {
            if (e.key === 'Escape') close();
        });
    })();
</script>
@endif
