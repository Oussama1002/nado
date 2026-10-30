@if ($errors->any())
    <div class="fixed top-4 right-4 z-[100] max-w-sm bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative"
        role="alert">
        <strong class="font-bold">Erreur!</strong>
        <ul class="mt-1 list-disc list-inside">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <span class="absolute top-0 bottom-0 right-0 px-4 py-3" onclick="this.parentElement.style.display='none';">
            <svg class="fill-current h-6 w-6 text-red-500" role="button" xmlns="http://www.w3.org/2000/svg"
                viewBox="0 0 20 20">
                <title>Fermer</title>
                <path
                    d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z" />
            </svg>
        </span>
    </div>
@endif

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Auto Enchère</title>

    <!-- Google Fonts: Inter -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">
    @include('partials.locale-head')

    <!-- Tailwind CSS -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Tailwind Config for Custom Colors/Fonts -->
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        inter: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        primary: '#1459DD',
                        accent: '#F37021',
                    }
                }
            }
        }
    </script>

    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }

        ::selection {
            background-color: #F37021;
            color: white;
        }

        /* Mobile Menu Transitions */
        .mobile-menu-enter {
            transform: translateX(100%);
        }

        .mobile-menu-enter-active {
            transform: translateX(0);
        }

        .mobile-menu-exit {
            transform: translateX(0);
        }

        .mobile-menu-exit-active {
            transform: translateX(100%);
        }
    </style>
</head>

<body class="min-h-screen bg-white text-slate-900">

    {{-- NAVBAR (shared) --}}
    @section('na_nav_links')
        @php
            $navClient = Auth::guard('client')->user();
            $isAeClient = $navClient && ($navClient->type ?? '') === 'flotte&enchere';
            $acheterUrl = $isAeClient
                ? route('nadoautoenchere.categories')
                : route('nadoautoenchere.register', ['role' => 'acheteur']);
            $vendreUrl = $isAeClient
                ? route('nadoautoenchere.vendre')
                : route('nadoautoenchere.register', ['role' => 'vendeur']);
        @endphp
        <a href="{{ $acheterUrl }}"
           class="text-[13px] font-bold uppercase tracking-wide px-3 py-2 rounded-2xl transition-all text-slate-700 hover:text-[#1459DD] hover:bg-slate-100">
            {{ __('messages.ae.buy') }}
        </a>
        <a href="{{ $vendreUrl }}"
           class="text-[13px] font-bold uppercase tracking-wide px-3 py-2 rounded-2xl transition-all text-slate-700 hover:text-[#F37021] hover:bg-orange-50">
            {{ __('messages.ae.sell') }}
        </a>
    @endsection
    @include('nadoautoenchere.partials.navbar')

    {{-- legacy mobile drawer removed (navbar is shared) --}}

    <!-- HERO SECTION -->
    <section class="relative min-h-[70vh] flex items-center justify-center overflow-hidden">
        {{-- Background video --}}
        <video autoplay muted loop playsinline
               class="absolute inset-0 w-full h-full object-cover z-0">
            <source src="{{ asset('storage/nadoautoenchere/bg/vd.mp4') }}" type="video/mp4">
        </video>
        {{-- Dark overlay for legibility --}}
        <div class="absolute inset-0 bg-slate-900/60 z-10"></div>

        <div class="relative z-20 max-w-6xl mx-auto px-6 text-center">

            <h1 class="text-4xl md:text-4xl font-black text-white tracking-tighter uppercase mb-4 drop-shadow-lg">
                {{ __('messages.ae.hero_title') }}
            </h1>

            <p class="text-sm md:text-base font-black uppercase tracking-[0.25em] text-[#F37021] mb-6">
                {{ __('messages.ae.hero_tag') }}
            </p>

            <!-- CTA SCROLL BUTTON -->
            <button onclick="document.getElementById('comment-ca-marche').scrollIntoView({ behavior: 'smooth' })"
                class="inline-flex items-center gap-3 px-8 py-4 mb-12 bg-[#F37021]/20 text-[#F37021] rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-[#F37021] hover:text-white transition-all shadow-md active:scale-95 cursor-pointer border-none backdrop-blur-sm">
                {{ __('messages.ae.how_btn') }}
                <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </button>

            <p class="text-lg text-white/90 font-medium max-w-2xl mx-auto mb-12 drop-shadow-md">
                {{ __('messages.ae.hero_desc') }}
            </p>

            <div class="flex flex-wrap justify-center gap-6">
                <!-- Participer Button -->
                @php
                    $authClient = Auth::guard('client')->user();
                    if ($authClient && $authClient->type === 'flotte&enchere') {
                        $participerUrl = route('nadoautoenchere.categories');
                    } else {
                        $participerUrl = route('nadoautoenchere.register', ['role' => 'acheteur']);
                    }
                @endphp
                <a href="{{ $participerUrl }}"
                    class="px-10 py-5 bg-[#F37021] text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 transition-all shadow-xl shadow-[#F37021]/20 active:scale-95 flex items-center gap-3 cursor-pointer border-none decoration-transparent">
                    {{ __('messages.ae.cta_participate') }} <i data-lucide="arrow-up-right" class="w-[18px] h-[18px]"></i>
                </a>

                <!-- Voir Véhicules Button -->
                @php
                    $voirVehiculesUrl = ($authClient && $authClient->type === 'flotte&enchere')
                        ? route('nadoautoenchere.index')
                        : route('nadoautoenchere.register', ['role' => 'acheteur']);
                @endphp
                <a href="{{ $voirVehiculesUrl }}"
                   class="px-10 py-5 bg-white/90 backdrop-blur-sm text-slate-900 rounded-2xl font-black text-xs uppercase tracking-widest hover:bg-white transition-all flex items-center gap-3 cursor-pointer border-none decoration-transparent active:scale-95">
                    <i data-lucide="eye" class="w-[18px] h-[18px] text-[#1459DD]"></i>
                    {{ __('messages.ae.cta_see') }}
                </a>
            </div>

        </div>
    </section>

    <!-- EXPERTISE SECTION -->
    <section class="py-32 bg-white relative overflow-hidden">
        <div class="absolute top-0 right-0 w-96 h-96 bg-[#1459DD]/5 blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-2 items-center gap-20">

                <div class="text-left">
                    <div
                        class="inline-flex items-center gap-2 px-4 py-2 bg-[#F37021]/10 border border-[#F37021]/20 rounded-full mb-8">
                        <i data-lucide="sparkles" class="w-4 h-4 text-[#F37021]"></i>
                        <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">{{ __('messages.ae.exp_eyebrow') }}</span>
                    </div>

                    <h2
                        class="text-4xl md:text-6xl font-black text-slate-900 leading-[0.9] tracking-tighter uppercase mb-8">
                        {!! __('messages.ae.exp_title') !!}
                    </h2>

                    <p class="text-xl text-slate-600 font-medium leading-relaxed mb-12">
                        {{ __('messages.ae.exp_desc') }}
                    </p>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-[#1459DD]/10 rounded-2xl flex items-center justify-center shrink-0">
                                <i data-lucide="shield-check" class="w-6 h-6 text-[#1459DD]"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase mb-2">{{ __('messages.ae.exp_f1_title') }}</h3>
                                <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.exp_f1_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-[#F37021]/10 rounded-2xl flex items-center justify-center shrink-0">
                                <i data-lucide="trending-up" class="w-6 h-6 text-[#F37021]"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase mb-2">{{ __('messages.ae.exp_f2_title') }}</h3>
                                <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.exp_f2_desc') }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4">
                            <div
                                class="w-12 h-12 bg-[#F37021]/20 rounded-2xl flex items-center justify-center shrink-0">
                                <i data-lucide="award" class="w-6 h-6 text-[#F37021]"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-black text-slate-900 uppercase mb-2">{{ __('messages.ae.exp_f3_title') }}</h3>
                                <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.exp_f3_desc') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="relative">
                    <div
                        class="relative bg-white/10 backdrop-blur-xl p-4 rounded-[3.5rem] border border-slate-100 shadow-2xl">
                        <img src="https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?auto=format&fit=crop&q=80&w=1200"
                            class="rounded-[2.5rem] w-full h-auto object-cover" alt="Expertise Auto Enchère" />
                    </div>
                    <div
                        class="absolute -bottom-8 -left-8 w-32 h-32 bg-[#1459DD] rounded-[2rem] flex items-center justify-center -rotate-12 shadow-2xl border-[6px] border-white">
                        <i data-lucide="gavel" class="w-12 h-12 text-white"></i>
                    </div>
                </div>

            </div>
        </div>
    </section>

    <!-- PARTNERS SECTION -->
    <section class="py-24 bg-slate-50 border-y border-slate-100">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center mb-16">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-full mb-6">
                    <i data-lucide="users" class="w-4 h-4 text-[#1459DD]"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">{{ __('messages.ae.partners_eyebrow') }}</span>
                </div>
                <h2 class="text-3xl md:text-5xl font-black text-slate-900 uppercase tracking-tighter mb-4">
                    {!! __('messages.ae.partners_title') !!}
                </h2>
                <p class="text-slate-600 font-medium text-lg max-w-3xl mx-auto leading-relaxed">
                    {{ __('messages.ae.partners_desc') }}
                </p>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-8 items-center">
                <!-- Partner Items -->
                <div
                    class="bg-white rounded-2xl p-8 flex items-center justify-center hover:shadow-xl transition-all group border border-slate-100 aspect-square">
                    <img src="https://www.bmci.ma/wp-content/themes/bmci/img/logo-banque-xs.png" alt="BMCI"
                        class="h-20 md:h-24 w-auto opacity-80 group-hover:opacity-100 transition-opacity" />
                </div>
                <div
                    class="bg-white rounded-2xl p-8 flex items-center justify-center hover:shadow-xl transition-all group border border-slate-100 aspect-square">
                    <img src="data:image/png;base64,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"
                        alt="Attijariwafa bank" class="h-20 md:h-24 w-auto opacity-80 group-hover:opacity-100 transition-opacity" />
                </div>
                <div
                    class="bg-white rounded-2xl p-8 flex items-center justify-center hover:shadow-xl transition-all group border border-slate-100 aspect-square">
                    <img src="https://www.cihbank.ma/themes/ciht/images/logo.png" alt="CIH Bank"
                        class="h-20 md:h-24 w-auto opacity-80 group-hover:opacity-100 transition-opacity" />
                </div>
            </div>
        </div>
    </section>

    <!-- COMMENT ÇA MARCHE SECTION -->
    <section id="comment-ca-marche" class="py-32 bg-white relative overflow-hidden">
        <div class="absolute bottom-0 left-0 w-96 h-96 bg-[#F37021]/5 blur-[120px] pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-20">
                <div
                    class="inline-flex items-center gap-2 px-4 py-2 bg-[#1459DD]/10 border border-[#1459DD]/20 rounded-full mb-6">
                    <i data-lucide="check-circle-2" class="w-4 h-4 text-[#1459DD]"></i>
                    <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-600">{{ __('messages.ae.how_eyebrow') }}</span>
                </div>
                <h2
                    class="text-4xl md:text-7xl font-black text-slate-900 uppercase tracking-tighter leading-[0.85] mb-6">
                    {!! __('messages.ae.how_title') !!}
                </h2>
                <p class="text-xl text-slate-600 font-medium max-w-3xl mx-auto">
                    {{ __('messages.ae.how_sub') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                <!-- Step 1 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">1</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="user-plus" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step1_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step1_d') }}</p>
                </div>

                <!-- Step 2 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">2</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="file-text" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step2_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step2_d') }}</p>
                </div>

                <!-- Step 3 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">3</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="heart" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step3_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step3_d') }}</p>
                </div>

                <!-- Step 4 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">4</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="map-pin" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step4_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step4_d') }}</p>
                </div>

                <!-- Step 5 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">5</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="banknote" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step5_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step5_d') }}</p>
                </div>

                <!-- Step 6 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">6</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="gavel" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step6_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step6_d') }}</p>
                </div>

                <!-- Step 7 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">7</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="file-check" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step7_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step7_d') }}</p>
                </div>

                <!-- Step 8 -->
                <div
                    class="group relative bg-white border border-slate-100 rounded-[2.5rem] p-8 hover:shadow-2xl hover:border-[#1459DD]/30 transition-all text-left">
                    <div
                        class="absolute -top-4 -right-4 w-12 h-12 bg-[#1459DD] rounded-2xl flex items-center justify-center shadow-xl">
                        <span class="text-white font-black text-xl">8</span>
                    </div>
                    <div
                        class="w-16 h-16 bg-[#F37021]/10 text-[#F37021] rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] group-hover:text-white transition-all">
                        <i data-lucide="car" class="w-7 h-7"></i>
                    </div>
                    <h3 class="text-xl font-black text-slate-900 uppercase tracking-tight mb-3 leading-tight">{{ __('messages.ae.step8_t') }}</h3>
                    <p class="text-slate-600 font-medium text-sm leading-relaxed">{{ __('messages.ae.step8_d') }}</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CONTACT SECTION -->
    <section class="py-24 bg-[#0F172A] relative overflow-hidden">
        <div
            class="absolute top-0 right-0 w-1/2 h-full bg-[#1459DD]/10 skew-x-[-15deg] translate-x-32 pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-6 relative z-10">
            <div class="text-center mb-16">
                <h2 class="text-4xl md:text-6xl font-black text-white uppercase tracking-tighter mb-6">
                    {!! __('messages.ae.contact_title') !!}
                </h2>
                <p class="text-xl text-slate-400 font-medium max-w-2xl mx-auto">
                    {{ __('messages.ae.contact_sub') }}
                </p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <a href="tel:+212663339072"
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-[2.5rem] p-10 hover:bg-white/10 hover:border-white/20 transition-all text-left decoration-transparent">
                    <div
                        class="w-16 h-16 bg-[#1459DD]/20 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#1459DD] transition-all">
                        <i data-lucide="phone"
                            class="w-7 h-7 text-[#1459DD] group-hover:text-white transition-colors"></i>
                    </div>
                    <span class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-3">{{ __('messages.ae.phone1') }}</span>
                    <span class="text-2xl font-black text-white group-hover:text-[#F37021] transition-colors">
                        +212 6 63 33 90 72
                    </span>
                </a>

                <a href="tel:+212669559315"
                    class="group bg-white/5 backdrop-blur-md border border-white/10 rounded-[2.5rem] p-10 hover:bg-white/10 hover:border-white/20 transition-all text-left decoration-transparent">
                    <div
                        class="w-16 h-16 bg-[#F37021]/20 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-[#F37021] transition-all">
                        <i data-lucide="phone"
                            class="w-7 h-7 text-[#F37021] group-hover:text-white transition-colors"></i>
                    </div>
                    <span class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-3">{{ __('messages.ae.phone2') }}</span>
                    <span class="text-2xl font-black text-white group-hover:text-[#F37021] transition-colors">
                        +212 6 69 55 93 15
                    </span>
                </a>
            </div>

            <div class="mt-12 text-center">
                <div class="max-w-4xl mx-auto bg-white/5 backdrop-blur-md border border-white/10 rounded-[2.5rem] overflow-hidden">
                    <div class="px-8 py-6 flex items-start gap-4">
                        <div class="w-12 h-12 rounded-2xl bg-[#F37021]/20 flex items-center justify-center shrink-0">
                            <i data-lucide="map-pin" class="w-6 h-6 text-[#F37021]"></i>
                        </div>
                        <div class="text-left">
                            <span class="block text-[10px] font-black text-white/60 uppercase tracking-widest mb-2">NOTRE PARKING</span>
                            <div class="text-xl font-black text-white leading-tight">NADO.MA - AUTO ENCHÈRE</div>
                            <div class="text-sm font-bold text-white/75 leading-relaxed mt-2">
                                N°11, Sakan Mounawar, Rue 93<br>
                                Bd Haifa, Casablanca 20300
                            </div>
                        </div>
                    </div>
                    <div class="bg-white">
                        <iframe
                            title="NADO.MA - AUTO ENCHÈRE (Google Maps)"
                            class="w-full"
                            style="height:360px;border:0;"
                            loading="lazy"
                            referrerpolicy="no-referrer-when-downgrade"
                            src="https://www.google.com/maps?q={{ urlencode('NADO.MA - AUTO ENCHÈRE, N°11, Sakan Mounawar, Rue 93, Bd Haifa, Casablanca 20300') }}&output=embed">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Init Lucide Icons -->
    <script>
        lucide.createIcons();

        // Navbar Interaction Script removed (using shared navbar)
    </script>
</body>

</html>
