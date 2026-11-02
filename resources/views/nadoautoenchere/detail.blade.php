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
    if (!$endsTs || \Carbon\Carbon::createFromTimestamp($endsTs)->isPast()) {
        $status = 'Terminé'; $statusClass = 'bg-slate-100 text-slate-600 border-slate-200';
    } elseif ($startsTs && \Carbon\Carbon::createFromTimestamp($startsTs)->isFuture()) {
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
              <i data-lucide="heart" class="w-5 h-5 fill-[#F37021] text-[#F37021]"></i>
            @else
              <i data-lucide="heart" class="w-5 h-5"></i>
            @endif
          </button>
        </form>
        <button onclick="openSharePopup('{{ urlencode(request()->fullUrl()) }}')" class="w-11 h-11 rounded-xl border border-slate-200 bg-white hover:border-[#1459DD] hover:text-[#1459DD] transition flex items-center justify-center" title="Partager">
          <i data-lucide="share-2" class="w-4 h-4"></i>
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
          <button data-tab="details" class="tab-btn active py-4 px-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-[#1459DD] whitespace-nowrap">Détails</button>
          <button data-tab="equip" class="tab-btn py-4 px-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-[#1459DD] whitespace-nowrap">Équipements</button>
          <button data-tab="expertise" class="tab-btn py-4 px-4 text-xs font-black uppercase tracking-widest text-slate-400 border-b-2 border-transparent hover:text-[#1459DD] whitespace-nowrap">Expertise</button>
        </div>

        <div class="p-6 md:p-8">
          {{-- Détails --}}
          <div class="tab-panel active" id="panel-details">
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mb-6 uppercase">Caractéristiques</h3>
            <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
              @php
                $specs = [
                  ['calendar', 'Année', $enchere->year],
                  ['gauge', 'Kilométrage', number_format($enchere->km, 0, ',', ' ').' km'],
                  ['fuel', 'Carburant', $enchere->carburant],
                  ['settings', 'Boîte', $enchere->box],
                  ['door-open', 'Portes', $enchere->nb_Door],
                  ['zap', 'Puissance', $enchere->cv.' CV'],
                  ['globe', 'Origine', $enchere->origine],
                  ['car', 'Carrosserie', $enchere->bodywork ?? '—'],
                  ['user-check', '1ère main', $enchere->premiere_main ? 'Oui' : 'Non'],
                  ['key', 'Clés', $enchere->nb_cles ? $enchere->nb_cles.' '.($enchere->nb_cles > 1 ? 'clés' : 'clé') : null],
                  ['circle-dot', 'Pneu de secours', $enchere->pneu_secours ? 'Oui' : 'Non'],
                  ['wrench', 'Outillage', $enchere->outillage ? 'Oui' : 'Non'],
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
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mt-10 mb-6 uppercase">Documents</h3>
            @php
              $vignetteAnnee = $enchere->vignette_annee ?? null;
              $vtDate        = $enchere->visite_technique ? \Carbon\Carbon::parse($enchere->visite_technique) : null;
              $vtValide      = $vtDate && $vtDate->isFuture();

              $documents = [
                [
                  'icon'  => 'file-text',
                  'label' => 'Carte grise',
                  'value' => $enchere->carte_grise ? 'Disponible' : 'Non disponible',
                  'ok'    => (bool) $enchere->carte_grise,
                ],
                [
                  'icon'  => 'receipt',
                  'label' => 'Vignette',
                  'value' => $vignetteAnnee ? 'Payée — '.$vignetteAnnee : 'Non renseignée',
                  // À jour si la dernière année payée est l'année en cours ou plus récente.
                  'ok'    => $vignetteAnnee && (int) $vignetteAnnee >= (int) date('Y'),
                ],
                [
                  'icon'  => 'clipboard-check',
                  'label' => 'Visite technique',
                  'value' => $vtDate ? 'Valide jusqu\'au '.$vtDate->format('d/m/Y') : 'Non renseignée',
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
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mb-6 uppercase">Équipements</h3>
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
              <p class="text-sm text-slate-500">Aucun équipement listé.</p>
            @endif
          </div>

          {{-- Expertise PDF --}}
          <div class="tab-panel" id="panel-expertise">
            <h3 class="text-2xl font-black tracking-tighter text-slate-900 mb-6 uppercase">Rapport d'expertise</h3>
            @if($pdfExists)
              <div class="rounded-2xl overflow-hidden border border-slate-200">
                <iframe src="{{ route('nadoautoenchere.pdf', $pdfName) }}" width="100%" height="600" style="border:none;"></iframe>
              </div>
              <a href="{{ route('nadoautoenchere.pdf', $pdfName) }}" target="_blank" class="inline-flex items-center gap-2 mt-4 text-sm font-bold text-[#1459DD] hover:underline">
                <i data-lucide="external-link" class="w-4 h-4"></i> Ouvrir dans un nouvel onglet
              </a>
            @else
              <div class="p-8 rounded-2xl bg-slate-50 border border-slate-100 text-center">
                <i data-lucide="file-x" class="w-10 h-10 text-slate-300 mx-auto mb-3"></i>
                <p class="text-sm text-slate-500">Aucun rapport disponible pour cette enchère.</p>
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
            <div class="text-[10px] font-black uppercase tracking-widest text-[#F37021] mb-2">Meilleure offre actuelle</div>
            <div class="flex items-baseline gap-1 mb-1">
              <span class="text-4xl font-black tracking-tighter text-slate-900">{{ number_format($prixTarget, 0, ',', ' ') }}</span>
              <span class="text-lg font-bold text-slate-500">DH</span>
            </div>
            <div class="text-xs text-slate-400 font-medium line-through">
              Prix estimé {{ number_format($enchere->prix, 0, ',', ' ') }} DH
            </div>

            @if($endsTs)
            <div class="mt-5 p-4 rounded-2xl bg-slate-900 text-white">
              <div class="flex items-center gap-2 mb-2">
                <i data-lucide="clock" class="w-4 h-4 text-[#F37021]"></i>
                <span class="text-[10px] font-black uppercase tracking-widest opacity-70">
                  @if($status === 'À venir') Démarre dans @elseif($status === 'Terminé') Terminé @else Fin dans @endif
                </span>
              </div>
              <div class="countdown text-2xl font-black tracking-tighter" data-end="{{ $enchere->ends_at }}" data-start="{{ $enchere->starts_at }}">—</div>
            </div>
            @endif

            {{-- Bid form --}}
            @if($status === 'En cours')
            @php $minBid = (int) $prixTarget + 1000; @endphp
            <form id="bidForm"
                  action="{{ route('nadoautoenchere.proposerPrix', $enchere->id) }}"
                  method="POST" class="mt-5"
                  data-min-bid="{{ $minBid }}">
              @csrf
              <label class="text-[10px] font-black uppercase tracking-widest text-slate-500 mb-2 block">Votre enchère max</label>
              <div class="flex gap-2">
                <input id="bidInput" type="number" name="p" step="1000" min="{{ $minBid }}" required
                       placeholder="{{ number_format($minBid, 0, ',', ' ') }}"
                       class="flex-1 px-4 py-3 rounded-xl border border-slate-200 text-sm font-bold focus:border-[#1459DD] focus:outline-none">
                <button type="submit" class="px-5 py-3 rounded-xl bg-[#1459DD] text-white text-sm font-black uppercase tracking-widest hover:bg-[#0f47b5] transition">
                  Enchérir
                </button>
              </div>
              <p class="text-[11px] text-slate-500 mt-2 flex items-start gap-1.5">
                <i data-lucide="info" class="w-3.5 h-3.5 flex-shrink-0 mt-0.5"></i>
                <span>L'enchère monte automatiquement par paliers de 1000 DH jusqu'à votre max.</span>
              </p>
            </form>

            <script>
            (function () {
                var form  = document.getElementById('bidForm');
                var input = document.getElementById('bidInput');
                if (!form || !input || typeof Swal === 'undefined') return;

                form.addEventListener('submit', function (e) {
                    // Si le navigateur a déjà bloqué (ex: input vide / min non respecté), on laisse faire.
                    if (!form.reportValidity()) return;

                    var raw = parseInt(input.value, 10);
                    var min = parseInt(form.getAttribute('data-min-bid'), 10) || 0;
                    if (isNaN(raw) || raw < min) {
                        e.preventDefault();
                        Swal.fire({
                            icon: 'error',
                            title: 'Enchère invalide',
                            text: 'Le minimum requis est de ' + min.toLocaleString('fr-FR') + ' DH.',
                            confirmButtonColor: '#1459DD'
                        });
                        return;
                    }

                    // Formulaire valide : on demande confirmation avant d'envoyer.
                    if (form.dataset.confirmed === '1') return;
                    e.preventDefault();

                    Swal.fire({
                        title: 'Confirmer votre enchère',
                        html: 'Vous êtes sur le point d\'enchérir à hauteur de '
                            + '<strong style="color:#1459DD;font-size:1.3em">'
                            + raw.toLocaleString('fr-FR') + ' DH</strong>.'
                            + '<br><br><small style="color:#64748b">'
                            + 'L\'enchère monte automatiquement par paliers de 1000 DH jusqu\'à votre maximum.'
                            + '<br>Cette action est <strong>définitive</strong>.</small>',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Oui, enchérir',
                        cancelButtonText: 'Annuler',
                        confirmButtonColor: '#1459DD',
                        cancelButtonColor: '#64748b',
                        reverseButtons: true,
                        focusCancel: true
                    }).then(function (result) {
                        if (result.isConfirmed) {
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
            <h4 class="text-xl font-black tracking-tighter text-white mb-2 uppercase">Où voir la voiture</h4>
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
              Itinéraire <i data-lucide="arrow-right" class="w-4 h-4"></i>
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

  // ===== Countdown =====
  document.querySelectorAll('.countdown').forEach(el => {
    const endTime = new Date(el.dataset.end).getTime();
    const startTime = el.dataset.start ? new Date(el.dataset.start).getTime() : null;
    function tick() {
      const now = Date.now();
      let target = endTime, label = '';
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
