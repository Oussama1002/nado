@php
    use Illuminate\Support\Facades\Auth;
    $brandName = $enchere->brand?->name ?? 'N/A';
    $modelName = $enchere->modeleRelation?->name ?? '';
    $mainImg   = asset('storage/nadoautoenchere/' . ($enchere->images[0] ?? $enchere->image));
    $isCert    = !empty($enchere->certifie);
    $images    = $enchere->images ?? [];
    $endsTs    = $enchere->ends_at ? \Carbon\Carbon::parse($enchere->ends_at)->timestamp : null;
    $startsTs  = $enchere->starts_at ? \Carbon\Carbon::parse($enchere->starts_at)->timestamp : null;
    $now       = now();
    // Le statut en base (draft/scheduled/live/ended/cancelled) fait foi quand il
    // est renseigné ; on ne retombe sur les dates que pour les lignes anciennes.
    // IMPORTANT : une enchère SANS date de fin n'est PAS « Terminée » — elle est
    // en cours tant qu'aucune fin passée ni statut ended/cancelled ne l'arrête.
    $dbStatus    = $enchere->status ?? null;
    $endPast     = $endsTs && \Carbon\Carbon::createFromTimestamp($endsTs)->isPast();
    $startFuture = $startsTs && \Carbon\Carbon::createFromTimestamp($startsTs)->isFuture();
    if (in_array($dbStatus, ['ended', 'cancelled'], true) || $endPast) {
        $status = 'Terminé'; $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
    } elseif ($dbStatus === 'scheduled' || $startFuture) {
        $status = 'À venir'; $statusClass = 'bg-blue-50 text-blue-700 border-blue-200';
    } else {
        $status = 'En cours'; $statusClass = 'bg-green-50 text-green-700 border-green-200';
    }
@endphp
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{{ $brandName }} {{ $modelName }} — Enchère NADO</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
<script src="https://cdn.tailwindcss.com"></script>
<script src="https://unpkg.com/lucide@latest"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<style>
  body { font-family: 'Inter', sans-serif; background:#F8FAFC; color:#0F172A; }
  .font-black { font-weight: 900; }
  .card-shadow { box-shadow: 0 1px 3px rgba(15,23,42,0.05), 0 4px 20px rgba(15,23,42,0.04); }
  .card-shadow-hover { box-shadow: 0 8px 30px rgba(20,89,221,0.12); }
  .tab-panel { display: none; }
  .tab-panel.active { display: block; }
  .tab-btn.active { color:#1459DD; border-color:#1459DD; }
  .hero-img { transition: transform .5s ease; }
  .hero-img:hover { transform: scale(1.03); }
  .thumb.active { outline: 3px solid #1459DD; outline-offset: 2px; }
  .halo::before {
    content:''; position:absolute; inset:-40px; border-radius:50%;
    background: radial-gradient(circle at center, rgba(20,89,221,0.35), transparent 60%);
    filter: blur(40px); z-index:0;
  }
</style>
</head>
<body>

{{-- ===== NAVBAR (shared – same as categories) ===== --}}
@section('na_nav_links')
  <a href="{{ route('nadoautoenchere.vendre') }}"
     class="text-[13px] font-bold uppercase tracking-wide px-3 py-2 rounded-2xl transition-all text-slate-700 hover:text-[#F37021] hover:bg-orange-50">
    Vendre
  </a>
  <a href="{{ route('nadoautoenchere.index') }}"
     class="text-[13px] font-bold uppercase tracking-wide px-3 py-2 rounded-2xl transition-all text-slate-700 hover:text-[#1459DD] hover:bg-slate-100">
    Tous les véhicules
  </a>
@endsection
@include('nadoautoenchere.partials.navbar')

{{-- ===== BREADCRUMB ===== --}}
<div class="max-w-7xl mx-auto px-6 py-4">
  <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-slate-500">
    <a href="{{ route('nadoautoenchere.categories') }}" class="hover:text-[#1459DD]">Enchères</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <a href="{{ route('nadoautoenchere.index', ['category' => $enchere->phone]) }}" class="hover:text-[#1459DD]">Véhicules</a>
    <i data-lucide="chevron-right" class="w-3 h-3"></i>
    <span class="text-slate-900">{{ $brandName }} {{ $modelName }}</span>
  </nav>
</div>

{{-- ===== HEADER ===== --}}
<section class="max-w-7xl mx-auto px-6">
  <div class="bg-white rounded-3xl border border-slate-100 card-shadow p-6 md:p-8 mb-6">
    <div class="flex flex-col md:flex-row md:items-start md:justify-between gap-6">
      <div class="flex-1 min-w-0">
        <div class="flex flex-wrap items-center gap-2 mb-4">
          <span class="px-3 py-1.5 rounded-full bg-slate-100 text-slate-700 text-[10px] font-black uppercase tracking-widest">
            Réf · NA{{ $enchere->id }}
          </span>
          <span class="px-3 py-1.5 rounded-full border text-[10px] font-black uppercase tracking-widest {{ $statusClass }}">
            {{ $status }}
          </span>
          @if($isCert)
          <span class="px-3 py-1.5 rounded-full bg-gradient-to-r from-[#1459DD] to-[#0f47b5] text-white text-[10px] font-black uppercase tracking-widest inline-flex items-center gap-1.5">
            <i data-lucide="badge-check" class="w-3 h-3"></i> NADO Expert
          </span>
          @endif
        </div>
        <h1 class="text-4xl md:text-5xl font-black tracking-tighter leading-[0.9] text-slate-900 uppercase">
          {{ $brandName }} <span class="text-[#1459DD]">{{ $modelName }}</span>
        </h1>
        <div class="flex flex-wrap items-center gap-4 mt-4 text-sm text-slate-500 font-medium">
          <span class="flex items-center gap-1.5"><i data-lucide="calendar" class="w-4 h-4"></i> {{ $enchere->year }}</span>
          <span class="flex items-center gap-1.5"><i data-lucide="gauge" class="w-4 h-4"></i> {{ number_format($enchere->km, 0, ',', ' ') }} km</span>
          <span class="flex items-center gap-1.5"><i data-lucide="fuel" class="w-4 h-4"></i> {{ $enchere->carburant }}</span>
          <span class="flex items-center gap-1.5"><i data-lucide="map-pin" class="w-4 h-4"></i> {{ $enchere->city ?? '—' }}</span>
        </div>
      </div>
      <div class="flex items-center gap-2">
        <form action="{{ route('favorite.toggle', ['id' => $enchere->id]) }}" method="POST">
          @csrf
          <button type="submit" class="w-11 h-11 rounded-xl border border-slate-200 bg-white hover:border-[#F37021] hover:text-[#F37021] transition flex items-center justify-center" title="Favori">
            @if(Auth::guard('client')->check() && Auth::guard('client')->user()->favorites->contains($enchere->id))
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="#F37021" stroke="#F37021" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg>
            @else
              <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 9.5a5.5 5.5 0 0 1 9.591-3.676.56.56 0 0 0 .818 0A5.49 5.49 0 0 1 22 9.5c0 2.29-1.5 4-3 5.5l-5.492 5.313a2 2 0 0 1-3 .019L5 15c-1.5-1.5-3-3.2-3-5.5"/></svg>
            @endif
          </button>
        </form>
        <button onclick="openSharePopup('{{ urlencode(request()->fullUrl()) }}')" class="w-11 h-11 rounded-xl border border-slate-200 bg-white hover:border-[#1459DD] hover:text-[#1459DD] transition flex items-center justify-center" title="Partager">
          <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" x2="15.42" y1="13.51" y2="17.49"/><line x1="15.41" x2="8.59" y1="6.51" y2="10.49"/></svg>
        </button>
        <button onclick="window.print()" class="w-11 h-11 rounded-xl border border-slate-200 bg-white hover:border-[#1459DD] hover:text-[#1459DD] transition flex items-center justify-center" title="Imprimer">
          <i data-lucide="printer" class="w-4 h-4"></i>
        </button>
      </div>
    </div>
  </div>
</section>

{{-- ===== MAIN LAYOUT ===== --}}
<section class="max-w-7xl mx-auto px-6 pb-12">
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- LEFT: Gallery + Tabs --}}
    <div class="lg:col-span-2 space-y-6">

      {{-- Gallery --}}
      <div class="bg-white rounded-3xl border border-slate-100 card-shadow overflow-hidden">
        <div class="relative bg-slate-100" style="aspect-ratio: 16/10;">
          <div id="mainDisplay" class="w-full h-full"></div>
          <button id="prevBtn" class="absolute left-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 backdrop-blur shadow-lg hover:bg-[#1459DD] hover:text-white transition flex items-center justify-center">
            <i data-lucide="chevron-left" class="w-5 h-5"></i>
          </button>
          <button id="nextBtn" class="absolute right-4 top-1/2 -translate-y-1/2 w-11 h-11 rounded-full bg-white/95 backdrop-blur shadow-lg hover:bg-[#1459DD] hover:text-white transition flex items-center justify-center">
            <i data-lucide="chevron-right" class="w-5 h-5"></i>
          </button>
          <button id="fullscreenBtn" class="absolute top-4 right-4 w-10 h-10 rounded-xl bg-white/90 backdrop-blur shadow hover:bg-white flex items-center justify-center" title="Plein écran">
            <i data-lucide="maximize" class="w-4 h-4"></i>
          </button>
          <span id="counter" class="absolute bottom-4 left-4 px-3 py-1.5 rounded-lg bg-slate-900/70 text-white text-xs font-bold backdrop-blur">1 / {{ count($images) + ($enchere->reel ? 1 : 0) }}</span>
        </div>
        <div id="thumbnailContainer" class="p-4 flex gap-2 overflow-x-auto"></div>
      </div>

      {{-- Tabs --}}
      <div class="bg-white rounded-3xl border border-slate-100 card-shadow">
        <div class="border-b border-slate-100 px-6 flex gap-1 overflow-x-auto">
          <button data-tab="details" class="tab-btn active py-4 px-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-[#1459DD] whitespace-nowrap">{{ __('messages.nadoae.tab_details') }}</button>
          <button data-tab="equip" class="tab-btn py-4 px-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-[#1459DD] whitespace-nowrap">{{ __('messages.nadoae.tab_equip') }}</button>
          <button data-tab="expertise" class="tab-btn py-4 px-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-[#1459DD] whitespace-nowrap">{{ __('messages.nadoae.tab_expertise') }}</button>
        </div>

        <div class="p-6 md:p-8">
          {{-- Détails --}}
          <div class="tab-panel active" id="panel-details">
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mb-6 uppercase">{{ __('messages.nadoae.specs_title') }}</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              @php
                $specs = [
                  ['calendar', __('messages.nadoae.year'), $enchere->year],
                  ['gauge', __('messages.nadoae.spec_km'), number_format($enchere->km, 0, ',', ' ').' km'],
                  ['fuel', __('messages.ventemodal.fuel'), $enchere->carburant],
                  ['settings', __('messages.ventemodal.box'), $enchere->box],
                  ['door-open', __('messages.nadoae.spec_doors'), $enchere->nb_Door],
                  ['zap', __('messages.nadoae.spec_power'), $enchere->cv.' CV'],
                  ['globe', __('messages.compare.origin'), $enchere->origine],
                  ['car', __('messages.compare.body'), $enchere->bodywork ?? '—'],
                  ['user-check', __('messages.nadoae.spec_first_hand'), $enchere->premiere_main ? __('messages.nadoae.yes') : __('messages.nadoae.no')],
                  ['key', __('messages.nadoae.spec_keys'), $enchere->nb_cles ? $enchere->nb_cles.' '.($enchere->nb_cles > 1 ? __('messages.nadoae.key_plural') : __('messages.nadoae.key_singular')) : null],
                  ['circle-dot', __('messages.nadoae.spec_spare'), $enchere->pneu_secours ? __('messages.nadoae.yes') : __('messages.nadoae.no')],
                  ['wrench', __('messages.nadoae.spec_tools'), $enchere->outillage ? __('messages.nadoae.yes') : __('messages.nadoae.no')],
                ];
              @endphp
              @foreach($specs as [$icon,$label,$value])
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center">
                      <i data-lucide="{{ $icon }}" class="w-4 h-4 text-[#1459DD]"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $label }}</span>
                  </div>
                  <div class="text-sm font-bold text-slate-900">{{ $value ?? '—' }}</div>
                </div>
              @endforeach
            </div>

            {{-- Documents du véhicule --}}
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mt-10 mb-6 uppercase">{{ __('messages.nadoae.documents_title') }}</h3>
            @php
              $vignetteAnnee = $enchere->vignette_annee ?? null;
              $vtDate        = $enchere->visite_technique ? \Carbon\Carbon::parse($enchere->visite_technique) : null;
              $vtValide      = $vtDate && $vtDate->isFuture();

              $documents = [
                [
                  'icon'  => 'file-text',
                  'label' => __('messages.nadoae.doc_carte_grise'),
                  'value' => $enchere->carte_grise ? __('messages.nadoae.doc_available') : __('messages.nadoae.doc_unavailable'),
                  'ok'    => (bool) $enchere->carte_grise,
                ],
                [
                  'icon'  => 'receipt',
                  'label' => __('messages.nadoae.doc_vignette'),
                  'value' => $vignetteAnnee ? __('messages.nadoae.doc_paid').' '.$vignetteAnnee : __('messages.nadoae.doc_not_filled'),
                  // À jour si la dernière année payée est l'année en cours ou plus récente.
                  'ok'    => $vignetteAnnee && (int) $vignetteAnnee >= (int) date('Y'),
                ],
                [
                  'icon'  => 'clipboard-check',
                  'label' => __('messages.nadoae.doc_vt'),
                  'value' => $vtDate ? __('messages.nadoae.doc_valid_until').' '.$vtDate->format('d/m/Y') : __('messages.nadoae.doc_not_filled'),
                  'ok'    => $vtValide,
                ],
              ];
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
              @foreach($documents as $doc)
                <div class="p-4 rounded-2xl bg-slate-50 border border-slate-100">
                  <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-white flex items-center justify-center">
                      <i data-lucide="{{ $doc['icon'] }}" class="w-4 h-4 text-[#1459DD]"></i>
                    </div>
                    <span class="text-[10px] font-black uppercase tracking-widest text-slate-500">{{ $doc['label'] }}</span>
                  </div>
                  <div class="flex items-center gap-2">
                    <i data-lucide="{{ $doc['ok'] ? 'check-circle-2' : 'alert-circle' }}"
                       class="w-4 h-4 flex-shrink-0 {{ $doc['ok'] ? 'text-green-600' : 'text-slate-400' }}"></i>
                    <span class="text-sm font-bold {{ $doc['ok'] ? 'text-slate-900' : 'text-slate-500' }}">{{ $doc['value'] }}</span>
                  </div>
                </div>
              @endforeach
            </div>
          </div>

          {{-- Équipements --}}
          <div class="tab-panel" id="panel-equip">
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mb-6 uppercase">{{ __('messages.nadoae.tab_equip') }}</h3>
            @if(count($equipements))
              <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($equipements as $label)
                  <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 border border-slate-100">
                    <div class="w-7 h-7 rounded-lg bg-green-50 flex items-center justify-center">
                      <i data-lucide="check" class="w-4 h-4 text-green-600"></i>
                    </div>
                    <span class="text-sm font-medium text-slate-700">{{ $label }}</span>
                  </div>
                @endforeach
              </div>
            @else
              <p class="text-sm text-slate-500">{{ __('messages.nadoae.no_equip') }}</p>
            @endif
          </div>

          {{-- Expertise PDF --}}
          <div class="tab-panel" id="panel-expertise">
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mb-6 uppercase">{{ __('messages.nadoae.report_title') }}</h3>
            @if($pdfExists)
              <div class="rounded-2xl overflow-hidden border border-slate-200">
                <iframe src="{{ route('nadoautoenchere.pdf', $pdfName) }}" width="100%" height="600" style="border:none;"></iframe>
              </div>
              <a href="{{ route('nadoautoenchere.pdf', $pdfName) }}" target="_blank" class="inline-flex items-center gap-2 mt-4 text-sm font-bold text-[#1459DD] hover:underline">
                <i data-lucide="external-link" class="w-4 h-4"></i> {{ __('messages.nadoae.open_new_tab') }}
              </a>
            @else
              <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <i data-lucide="file-x" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500">{{ __('messages.nadoae.no_report') }}</p>
              </div>
            @endif
          </div>
        </div>
      </div>
    </div>

    {{-- RIGHT: Sticky rail --}}
    <aside class="lg:col-span-1">
      <div class="lg:sticky lg:top-28 space-y-6">
        {{-- Pricing card --}}
        <div class="bg-white rounded-3xl border border-slate-100 card-shadow overflow-hidden">
          <div class="p-6">
            <div class="text-[10px] font-black uppercase tracking-widest text-[#F37021] mb-2">
              {{ $liveBidding ? __('messages.enchere.current_bid') : __('messages.nadoae.best_offer') }}
            </div>
            <div class="flex items-baseline gap-1 mb-1">
              <span id="liveCurrentPrice" class="text-4xl font-black tracking-tighter text-slate-900">{{ number_format($liveBidding ? $enchere->currentHighBid() : $prixTarget, 0, ',', ' ') }}</span>
              <span class="text-lg font-bold text-slate-500">DH</span>
            </div>
            <div class="text-xs text-slate-400 font-medium line-through">
              {{ __('messages.nadoae.estimated_price') }} {{ number_format($enchere->prix, 0, ',', ' ') }} DH
            </div>

            @if($liveBidding)
            {{-- Stats enchère live (mises à jour par polling) --}}
            <div class="mt-4 grid grid-cols-2 gap-2">
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('messages.enchere.min_next') }}</div>
                <div id="liveMinNext" class="text-sm font-black text-slate-900">{{ number_format($enchere->minNextBid(), 0, ',', ' ') }} DH</div>
              </div>
              <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                <div class="text-[10px] font-black uppercase tracking-widest text-slate-400">{{ __('messages.enchere.leader') }}</div>
                <div class="flex items-baseline gap-2">
                  <span id="liveBidsCount" class="text-sm font-black text-slate-900">{{ $bidsCount }}</span>
                  <span class="text-[10px] text-slate-400">{{ __('messages.enchere.bids_count') }}</span>
                  <span class="text-[10px] text-slate-300">·</span>
                  <span id="liveBiddersCount" class="text-sm font-black text-slate-900">{{ $bidderCount }}</span>
                  <span class="text-[10px] text-slate-400">{{ __('messages.enchere.bidders_count') }}</span>
                </div>
              </div>
            </div>

            @if($youBid)
            <div id="liveStatusBadge" class="mt-3 px-3 py-2 rounded-xl text-xs font-black text-center {{ $youLead ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-orange-50 text-orange-700 border border-orange-200' }}">
              {{ $youLead ? __('messages.enchere.you_lead') : __('messages.enchere.you_outbid') }}
            </div>
            @else
            <div id="liveStatusBadge" class="mt-3 px-3 py-2 rounded-xl text-xs font-black text-center bg-slate-50 text-slate-500 border border-slate-200" style="display:none;"></div>
            @endif

            @if($enchere->reserve_price)
            <div class="mt-2 text-[11px] font-bold text-center {{ $enchere->currentHighBid() >= (float)$enchere->reserve_price ? 'text-green-600' : 'text-slate-400' }}">
              {{ $enchere->currentHighBid() >= (float)$enchere->reserve_price ? __('messages.enchere.reserve_met') : __('messages.enchere.reserve_not_met') }}
            </div>
            @endif
            @endif

            @if($endsTs)
            <div class="mt-5 p-4 rounded-2xl bg-slate-900 text-white">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="clock" class="w-4 h-4 text-[#F37021]"></i>
                <span class="text-[10px] font-black uppercase tracking-widest opacity-70">
                  @if($status === 'À venir') {{ __('messages.nadoae.starts_in') }} @elseif($status === 'Terminé') {{ __('messages.nadoae.status_ended') }} @else {{ __('messages.nadoae.ends_in') }} @endif
                </span>
              </div>
              <div class="countdown text-2xl font-black tracking-tighter" data-end="{{ $enchere->ends_at }}" data-start="{{ $enchere->starts_at }}">—</div>
            </div>
            @endif

            {{-- Bid form --}}
            @if($status === 'En cours')
            @php $minBid = $liveBidding ? (int) $enchere->minNextBid() : (int) $prixTarget + 1000; @endphp
            <form id="bidForm"
                  action="{{ route('nadoautoenchere.proposerPrix', $enchere->id) }}"
                  method="POST" class="mt-5"
                  data-live="{{ $liveBidding ? '1' : '0' }}"
                  data-min-bid="{{ $minBid }}"
                  data-state-url="{{ route('nadoautoenchere.state', $enchere->id) }}"
                  data-csrf-url="{{ route('csrf.token') }}">
              @csrf
              <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 block">
                {{ $liveBidding ? __('messages.enchere.your_max') : __('messages.nadoae.your_max_bid') }}
              </label>
              <div class="flex gap-2">
                <input id="bidInput" type="number" name="p" step="1000" min="{{ $minBid }}" required
                       placeholder="{{ number_format($minBid, 0, ',', ' ') }}"
                       class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold focus:border-[#1459DD] focus:outline-none">
                <button type="submit" id="bidBtn" class="px-5 py-3 rounded-xl bg-[#1459DD] text-white text-sm font-black uppercase tracking-widest hover:bg-[#0f47b5] transition">
                  {{ $liveBidding ? __('messages.enchere.place_bid') : __('messages.nadoae.bid') }}
                </button>
              </div>
              <p class="text-[11px] text-slate-500 mt-2 flex items-start gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"></i>
                <span>{{ $liveBidding ? __('messages.enchere.proxy_hint') : __('messages.nadoae.bid_info') }}</span>
              </p>
            </form>

            <script>
            (function () {
                var form  = document.getElementById('bidForm');
                var input = document.getElementById('bidInput');
                var btn   = document.getElementById('bidBtn');
                if (!form || !input || typeof Swal === 'undefined') return;

                var isLive = form.getAttribute('data-live') === '1';

                function currentMin() { return parseInt(form.getAttribute('data-min-bid'), 10) || 0; }

                // Récupère un jeton CSRF frais (session partagée avec le login client).
                async function freshToken() {
                    try {
                        var r = await fetch(form.getAttribute('data-csrf-url'), { headers: { 'Accept': 'application/json' } });
                        var j = await r.json();
                        return j.token;
                    } catch (e) {
                        return document.querySelector('input[name="_token"]').value;
                    }
                }

                async function submitLive(raw) {
                    btn.disabled = true;
                    var prevLabel = btn.textContent;
                    btn.textContent = @json(__('messages.enchere.placing'));
                    try {
                        var token = await freshToken();
                        var fd = new FormData();
                        fd.append('_token', token);
                        fd.append('p', raw);
                        var res = await fetch(form.getAttribute('action'), {
                            method: 'POST',
                            headers: { 'X-CSRF-TOKEN': token, 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                            body: fd
                        });
                        var data = await res.json().catch(function () { return {}; });
                        if (res.ok && data.success) {
                            if (window.applyAuctionState) window.applyAuctionState(data);
                            input.value = '';
                            Swal.fire({
                                icon: data.is_leader ? 'success' : 'warning',
                                title: data.is_leader ? @json(__('messages.enchere.you_lead')) : @json(__('messages.enchere.you_outbid')),
                                text: data.message || '',
                                confirmButtonColor: '#1459DD'
                            });
                        } else {
                            Swal.fire({ icon: 'error', title: @json(__('messages.nadoae.bid_invalid')), text: (data && data.message) || '', confirmButtonColor: '#1459DD' });
                        }
                    } catch (e) {
                        Swal.fire({ icon: 'error', title: @json(__('messages.nadoae.bid_invalid')), confirmButtonColor: '#1459DD' });
                    } finally {
                        btn.disabled = false;
                        btn.textContent = prevLabel;
                    }
                }

                form.addEventListener('submit', function (e) {
                    if (!form.reportValidity()) return;

                    var raw = parseInt(input.value, 10);
                    var min = currentMin();
                    if (isNaN(raw) || raw < min) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: @json(__('messages.nadoae.bid_invalid')),
                            text: @json(__('messages.nadoae.min_required')) + ' ' + min.toLocaleString('fr-FR') + ' DH.',
                            confirmButtonColor: '#1459DD'
                        });
                        return;
                    }

                    if (form.dataset.confirmed === '1') return;
                    e.preventDefault();

                    Swal.fire({
                        title: isLive ? @json(__('messages.enchere.confirm_title')) : @json(__('messages.nadoae.confirm_title')),
                        html: (isLive ? @json(__('messages.enchere.confirm_text')) : @json(__('messages.nadoae.confirm_about'))) + ' '
                            + '<strong style="color:#1459DD;font-size:1.3em">'
                            + raw.toLocaleString('fr-FR') + ' DH</strong>.'
                            + '<br><br><small style="color:#64748b">'
                            + @json(__('messages.nadoae.confirm_auto'))
                            + '<br>' + @json(__('messages.nadoae.confirm_final')) + '</small>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: isLive ? @json(__('messages.enchere.yes_bid')) : @json(__('messages.nadoae.yes_bid')),
                        cancelButtonText: isLive ? @json(__('messages.enchere.cancel')) : @json(__('messages.nadoae.cancel')),
                        confirmButtonColor: '#1459DD',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true,
                        focusCancel: true
                    }).then(function (result) {
                        if (!result.isConfirmed) return;
                        if (isLive) {
                            submitLive(raw);
                        } else {
                            form.dataset.confirmed = '1';
                            form.submit();
                        }
                    });
                });
            })();
            </script>
            @endif
          </div>
        </div>

        {{-- Où voir la voiture --}}
        @php
          // Localisation propre à l'enchère, sinon le showroom NADO par défaut.
          $locLat  = $enchere->latitude  ?: 33.5275864;
          $locLng  = $enchere->longitude ?: -7.6078363;
          $locAdr  = $enchere->adresse   ?: 'NADO.ma — Auto Enchère';
          $locLink = $enchere->maps_url  ?: 'https://maps.app.goo.gl/77FFcz1AvqRo6aGBA';
        @endphp
        <div class="relative bg-slate-900 rounded-3xl overflow-hidden halo">
          <div class="relative z-10 p-6">
            <div class="w-10 h-10 rounded-xl bg-[#1459DD]/20 flex items-center justify-center mb-4">
              <i data-lucide="map-pin" class="w-5 h-5 text-[#1459DD]"></i>
            </div>
            <h4 class="text-xl font-black tracking-tighter text-white mb-2 uppercase">{{ __('messages.nadoae.where_see') }}</h4>
            <p class="text-xs text-slate-400 mb-4 leading-relaxed">
              {{ $locAdr }}
            </p>

            {{-- Carte Google Maps (embed classique, sans clé API) --}}
            <div class="rounded-2xl overflow-hidden border border-white/10 mb-4">
              <iframe
                src="https://maps.google.com/maps?q={{ $locLat }},{{ $locLng }}&amp;z=16&amp;hl=fr&amp;output=embed"
                width="100%" height="200" style="border:0;display:block;"
                loading="lazy" referrerpolicy="no-referrer-when-downgrade"
                title="Localisation — {{ $locAdr }}"
                allowfullscreen></iframe>
            </div>

            <a href="{{ $locLink }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-2 text-xs font-black uppercase tracking-widest text-[#F37021] hover:text-[#FDD817]">
              {{ __('messages.nadoae.itinerary') }} <i data-lucide="arrow-right" class="w-4 h-4"></i>
            </a>
          </div>
        </div>
      </div>
    </aside>

  </div>
</section>

{{-- ===== FOOTER ===== --}}
<footer class="bg-slate-900 text-slate-400 py-10 mt-10">
  <div class="max-w-7xl mx-auto px-6 flex flex-col md:flex-row items-center justify-between gap-4">
    <div class="text-xs font-medium">© {{ date('Y') }} NADO.ma — Tous droits réservés</div>
    <div class="flex gap-5 text-xs">
      <a href="{{ route('index') }}" class="hover:text-white">Accueil</a>
      <a href="{{ route('nadoautoenchere.categories') }}" class="hover:text-white">Enchères</a>
      <a href="{{ route('contactez-nous.index') }}" class="hover:text-white">Contact</a>
    </div>
  </div>
</footer>

@if(session('success'))
<script>
  document.addEventListener('DOMContentLoaded', () => {
    Swal.fire({ icon:'success', title:'{{ session('success') }}', showConfirmButton:false, timer:2200 });
  });
</script>
@endif

<script>
  lucide.createIcons();

  // ===== Gallery =====
  const mediaList = [
    @foreach($images as $img)
      { type:'image', src:"{{ asset('storage/nadoautoenchere/' . $img) }}" },
    @endforeach
    @if($enchere->reel)
      { type:'video', videoId:"{{ $enchere->reel }}", thumb:"{{ asset('assets/images/play-thumbnail.png') }}" }
    @endif
  ];
  const mainDisplay = document.getElementById('mainDisplay');
  const thumbContainer = document.getElementById('thumbnailContainer');
  const counter = document.getElementById('counter');
  let current = 0;

  function renderThumbs() {
    thumbContainer.innerHTML = '';
    mediaList.forEach((m, i) => {
      const el = document.createElement('div');
      el.className = 'thumb relative flex-shrink-0 w-20 h-16 rounded-xl overflow-hidden cursor-pointer border border-slate-200';
      el.dataset.index = i;
      if (m.type === 'image') {
        el.innerHTML = `<img src="${m.src}" class="w-full h-full object-cover">`;
      } else {
        el.innerHTML = `<img src="${m.thumb}" class="w-full h-full object-cover"><div class="absolute inset-0 flex items-center justify-center bg-black/30"><i class="fas fa-play text-white"></i></div>`;
      }
      el.addEventListener('click', () => renderMain(i));
      thumbContainer.appendChild(el);
    });
  }

  function renderMain(i) {
    current = i;
    const m = mediaList[i];
    if (!m) return;
    if (m.type === 'image') {
      mainDisplay.innerHTML = `<img src="${m.src}" class="hero-img w-full h-full object-cover cursor-zoom-in" onclick="openLightbox('${m.src}')">`;
    } else {
      mainDisplay.innerHTML = `<iframe src="https://www.youtube.com/embed/${m.videoId}?rel=0" frameborder="0" allowfullscreen class="w-full h-full"></iframe>`;
    }
    counter.textContent = `${i+1} / ${mediaList.length}`;
    thumbContainer.querySelectorAll('.thumb').forEach(t => t.classList.toggle('active', parseInt(t.dataset.index) === i));
  }

  function openLightbox(src) {
    Swal.fire({ imageUrl:src, showConfirmButton:false, width:'90%', padding:0, background:'transparent', backdrop:'rgba(0,0,0,0.92)' });
  }

  document.getElementById('prevBtn')?.addEventListener('click', () => renderMain((current - 1 + mediaList.length) % mediaList.length));
  document.getElementById('nextBtn')?.addEventListener('click', () => renderMain((current + 1) % mediaList.length));
  document.getElementById('fullscreenBtn')?.addEventListener('click', () => {
    const m = mediaList[current];
    if (m?.type === 'image') openLightbox(m.src);
  });

  if (mediaList.length) { renderThumbs(); renderMain(0); }

  // ===== Tabs =====
  document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
      document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      document.getElementById('panel-' + btn.dataset.tab)?.classList.add('active');
    });
  });

  // ===== Countdown ===== (lit data-end à chaque tick → l'anti-snipe peut prolonger)
  document.querySelectorAll('.countdown').forEach(el => {
    const startTime = el.dataset.start ? new Date(el.dataset.start).getTime() : null;
    function tick() {
      const now = Date.now();
      const endTime = new Date(el.dataset.end).getTime();
      // Pas de date de fin valide → enchère non chronométrée : pas de « Terminé ».
      if (isNaN(endTime)) { el.textContent = '—'; return; }
      let target = endTime;
      if (startTime && startTime > now) target = startTime;
      const diff = target - now;
      if (diff <= 0) { el.textContent = 'Terminé'; return; }
      const d = Math.floor(diff / 86400000);
      const h = Math.floor((diff % 86400000) / 3600000);
      const m = Math.floor((diff % 3600000) / 60000);
      const s = Math.floor((diff % 60000) / 1000);
      el.textContent = `${d}j ${String(h).padStart(2,'0')}h ${String(m).padStart(2,'0')}m ${String(s).padStart(2,'0')}s`;
    }
    tick(); setInterval(tick, 1000);
  });

  @if($liveBidding)
  // ===== Enchère live : polling JSON (pas de WebSocket) =====
  // Pour passer au temps réel : remplacer ce poll par un abonnement Laravel Echo
  // (Pusher/Reverb) alimenté par un event diffusé après placeBid(). Même UI.
  (function () {
    var POLL_MS = {{ (int) max(2, settings('enchere.poll_seconds', 4)) }} * 1000;
    var fmt = function (n) { return Number(n).toLocaleString('fr-FR'); };
    var $price   = document.getElementById('liveCurrentPrice');
    var $minNext = document.getElementById('liveMinNext');
    var $bids    = document.getElementById('liveBidsCount');
    var $bidders = document.getElementById('liveBiddersCount');
    var $badge   = document.getElementById('liveStatusBadge');
    var $count   = document.querySelector('.countdown');
    var form     = document.getElementById('bidForm');

    window.applyAuctionState = function (d) {
      if (d.current_price != null && $price) $price.textContent = fmt(d.current_price);
      if (d.min_next != null) {
        if ($minNext) $minNext.textContent = fmt(d.min_next) + ' DH';
        if (form) { form.setAttribute('data-min-bid', Math.round(d.min_next)); }
        var inp = document.getElementById('bidInput');
        if (inp) { inp.setAttribute('min', Math.round(d.min_next)); inp.setAttribute('placeholder', fmt(d.min_next)); }
      }
      if (d.bids_count != null && $bids) $bids.textContent = d.bids_count;
      if (d.bidder_count != null && $bidders) $bidders.textContent = d.bidder_count;
      if (d.ends_at && $count) { $count.dataset.end = d.ends_at; }
      // Statut « vous êtes en tête / surenchéri »
      if ($badge && (typeof d.you_lead !== 'undefined' || typeof d.is_leader !== 'undefined')) {
        var lead = (typeof d.is_leader !== 'undefined') ? d.is_leader : d.you_lead;
        $badge.style.display = 'block';
        $badge.className = 'mt-3 px-3 py-2 rounded-xl text-xs font-black text-center ' +
          (lead ? 'bg-green-50 text-green-700 border border-green-200'
                : 'bg-orange-50 text-orange-700 border border-orange-200');
        $badge.textContent = lead ? @json(__('messages.enchere.you_lead')) : @json(__('messages.enchere.you_outbid'));
      }
    };

    async function poll() {
      try {
        var r = await fetch(@json(route('nadoautoenchere.state', $enchere->id)), { headers: { 'Accept': 'application/json' } });
        if (!r.ok) return;
        var d = await r.json();
        window.applyAuctionState(d);
        if (d.status && d.status !== 'live' && d.status !== 'scheduled') {
          // Enchère clôturée : on laisse le countdown afficher « Terminé ».
        }
      } catch (e) { /* réseau : on réessaie au prochain tick */ }
    }
    setInterval(poll, POLL_MS);
  })();
  @endif
  // ===== Share =====
  function openSharePopup(url) {
    Swal.fire({
      title: 'Partager',
      html: `<div class="flex justify-around text-2xl pt-2">
        <a href="https://api.whatsapp.com/send?text=${url}" target="_blank" class="text-green-500"><i class="fab fa-whatsapp"></i></a>
        <a href="https://www.facebook.com/sharer/sharer.php?u=${url}" target="_blank" class="text-blue-600"><i class="fab fa-facebook"></i></a>
        <a href="https://www.messenger.com/t/?link=${url}" target="_blank" class="text-blue-500"><i class="fab fa-facebook-messenger"></i></a>
        <a href="mailto:?subject=Annonce NADO&body=${url}" class="text-slate-700"><i class="fas fa-envelope"></i></a>
      </div>`,
      showCloseButton: true, showConfirmButton: false, width: 360
    });
  }
  window.openSharePopup = openSharePopup;
</script>

</body>
</html>
