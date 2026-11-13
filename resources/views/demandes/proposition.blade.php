@extends('layouts.app')

@section('title', __('messages.demande_show.vehicle_proposal') . ' — NADO')

@section('content')
@php
    $meta   = $reply->proposalData() ?? [];
    $imgs   = collect($meta['images'] ?? [])->map(fn ($p) => asset('storage/' . $p))->values();
    $main   = $imgs->first();
    $title  = $meta['title'] ?? (trim(($meta['mark'] ?? '') . ' ' . ($meta['modele'] ?? '')) ?: __('messages.demande_show.vehicle'));
    $digits = preg_replace('/\D+/', '', (string) $reply->contact_phone);
    $masked = strlen($digits) >= 6 ? substr($digits, 0, 2) . ' •• •• ' . substr($digits, -2) : '•• •• ••';
@endphp

<style>
    .pp-wrap { max-width: 1120px; margin: 0 auto; padding: 24px 16px 60px; font-family: 'Inter', sans-serif; }
    .pp-back { display:inline-flex; align-items:center; gap:6px; color:#64748b; font-size:13px; font-weight:700; text-decoration:none; margin-bottom:18px; }
    .pp-back:hover { color:#1459DD; }
    .pp-grid { display:grid; grid-template-columns: 1.3fr 1fr; gap: 24px; }
    @media (max-width: 880px) { .pp-grid { grid-template-columns: 1fr; } }

    .pp-gallery { background:#fff; border:1px solid #e2e8f0; border-radius:20px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,.05); }
    .pp-main-wrap { position:relative; }
    .pp-main { width:100%; aspect-ratio: 4/3; object-fit:cover; display:block; background:#f1f5f9; cursor:zoom-in; }
    .pp-nav { position:absolute; top:50%; transform:translateY(-50%); width:44px; height:44px; border-radius:50%; border:0; background:rgba(15,23,42,.55); color:#fff; display:flex; align-items:center; justify-content:center; cursor:pointer; transition:background .15s; }
    .pp-nav:hover { background:rgba(15,23,42,.8); }
    .pp-nav-prev { left:12px; }
    .pp-nav-next { right:12px; }
    .pp-main-ph { width:100%; aspect-ratio:4/3; display:flex; align-items:center; justify-content:center; background:#f1f5f9; color:#94a3b8; font-size:48px; }
    .pp-thumbs { display:flex; gap:8px; padding:12px; overflow-x:auto; }
    .pp-thumbs img { width:84px; height:62px; object-fit:cover; border-radius:10px; cursor:pointer; border:2px solid transparent; flex-shrink:0; }
    .pp-thumbs img.active { border-color:#1459DD; }

    .pp-info { background:#fff; border:1px solid #e2e8f0; border-radius:20px; padding:22px; box-shadow:0 1px 3px rgba(0,0,0,.05); align-self:start; }
    .pp-badge { display:inline-block; font-size:11px; font-weight:800; text-transform:uppercase; letter-spacing:.05em; color:#1459DD; background:#eff4ff; padding:4px 10px; border-radius:999px; margin-bottom:12px; }
    .pp-title { font-size:24px; font-weight:900; color:#0f172a; margin:0 0 4px; }
    .pp-price { font-size:26px; font-weight:900; color:#1459DD; margin:0 0 16px; }
    .pp-specs { display:grid; grid-template-columns: repeat(2, 1fr); gap:10px; margin-bottom:18px; }
    .pp-spec { background:#f8fafc; border:1px solid #f1f5f9; border-radius:12px; padding:10px 12px; }
    .pp-spec .k { font-size:11px; color:#94a3b8; font-weight:700; text-transform:uppercase; letter-spacing:.04em; }
    .pp-spec .v { font-size:14px; color:#0f172a; font-weight:800; }
    .pp-desc { font-size:14px; color:#334155; line-height:1.6; white-space:pre-line; margin:0 0 18px; }
    .pp-contact { border-top:1px solid #f1f5f9; padding-top:16px; }
    .pp-contact-name { font-size:13px; font-weight:800; color:#0f172a; margin-bottom:4px; }
    .pp-phone { font-size:20px; font-weight:900; color:#0f172a; letter-spacing:.04em; margin-bottom:12px; }
    .pp-reveal { display:inline-flex; align-items:center; gap:8px; background:#1459DD; color:#fff; border:0; border-radius:14px; padding:12px 20px; font-size:14px; font-weight:800; cursor:pointer; }
    .pp-reveal:hover { background:#1047b8; }
    .pp-lightbox { display:none; position:fixed; inset:0; background:rgba(0,0,0,.9); z-index:9999; align-items:center; justify-content:center; padding:20px; }
    .pp-lightbox.open { display:flex; }
    .pp-lightbox img { max-width:100%; max-height:90vh; border-radius:12px; }
</style>

<div class="pp-wrap">
    <a href="{{ route('demandes.show', $demande->id) }}" class="pp-back">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
        {{ __('messages.demande_show.vehicle_proposal') }}
    </a>

    <div class="pp-grid">
        <div class="pp-gallery">
            @if($main)
                <div class="pp-main-wrap">
                    <img src="{{ $main }}" id="ppMain" class="pp-main" alt="{{ $title }}" onclick="ppOpenLightbox()">
                    @if($imgs->count() > 1)
                        <button type="button" class="pp-nav pp-nav-prev" onclick="ppNav(-1)" aria-label="Précédent">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="15 18 9 12 15 6"/></svg>
                        </button>
                        <button type="button" class="pp-nav pp-nav-next" onclick="ppNav(1)" aria-label="Suivant">
                            <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="9 18 15 12 9 6"/></svg>
                        </button>
                    @endif
                </div>
                @if($imgs->count() > 1)
                    <div class="pp-thumbs">
                        @foreach($imgs as $i => $u)
                            <img src="{{ $u }}" class="{{ $i === 0 ? 'active' : '' }}" data-idx="{{ $i }}" alt="" onclick="ppSetMain(this, '{{ $u }}')">
                        @endforeach
                    </div>
                @endif
            @else
                <div class="pp-main-ph">🚗</div>
            @endif
        </div>

        <div class="pp-info">
            <span class="pp-badge">{{ __('messages.demande_show.vehicle_proposal') }}</span>
            <h1 class="pp-title">{{ $title }}</h1>
            <div class="pp-price"><span class="pp-hidden" data-real="{{ number_format((int) ($meta['prix'] ?? 0), 0, ',', ' ') }} DH">**** DH</span></div>

            <div class="pp-specs">
                @if(!empty($meta['year']))      <div class="pp-spec"><div class="k">{{ __('messages.demande_show.year') }}</div><div class="v"><span class="pp-hidden" data-real="{{ $meta['year'] }}">****</span></div></div> @endif
                @if(isset($meta['km']))         <div class="pp-spec"><div class="k">{{ __('messages.demande_show.km') }}</div><div class="v"><span class="pp-hidden" data-real="{{ number_format((int) $meta['km'], 0, ',', ' ') }} km">**** km</span></div></div> @endif
                @if(!empty($meta['carburant'])) <div class="pp-spec"><div class="k">{{ __('messages.ventemodal.fuel') }}</div><div class="v">{{ $meta['carburant'] }}</div></div> @endif
                @if(!empty($meta['box']))       <div class="pp-spec"><div class="k">{{ __('messages.ventemodal.box') }}</div><div class="v">{{ $meta['box'] }}</div></div> @endif
                @if(!empty($meta['city']))      <div class="pp-spec"><div class="k">{{ __('messages.demande_show.city') }}</div><div class="v">{{ $meta['city'] }}</div></div> @endif
            </div>

            @if(!empty($meta['description']))
                <p class="pp-desc">{{ $meta['description'] }}</p>
            @endif

            <div class="pp-contact">
                <div class="pp-contact-name">{{ $meta['proposer_name'] ?? ($reply->client?->name ?? __('messages.demande_show.member')) }}</div>
                <div class="pp-phone" id="ppPhone" dir="ltr"><span class="pp-hidden" data-real="📞 {{ $reply->contact_phone }}">📞 ****</span></div>
                <button type="button" class="pp-reveal" onclick="ppReveal(this)">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.68 2.81a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.9.32 1.85.55 2.81.68A2 2 0 0 1 22 16.92z"/></svg>
                    {{ __('messages.demande_show.reveal_info') }}
                </button>
            </div>
        </div>
    </div>
</div>

<div class="pp-lightbox" id="ppLightbox" onclick="this.classList.remove('open')">
    <img src="{{ $main }}" id="ppLightboxImg" alt="">
</div>

<script>
    var ppImages = @json($imgs);
    var ppIdx = 0;
    function ppShow(i) {
        if (!ppImages.length) return;
        ppIdx = (i + ppImages.length) % ppImages.length;
        var url = ppImages[ppIdx];
        document.getElementById('ppMain').src = url;
        document.getElementById('ppLightboxImg').src = url;
        document.querySelectorAll('.pp-thumbs img').forEach(function (t) {
            t.classList.toggle('active', parseInt(t.dataset.idx) === ppIdx);
        });
    }
    function ppNav(dir) { ppShow(ppIdx + dir); }
    function ppSetMain(el, url) { ppShow(parseInt(el.dataset.idx)); }
    function ppOpenLightbox() {
        document.getElementById('ppLightboxImg').src = document.getElementById('ppMain').src;
        document.getElementById('ppLightbox').classList.add('open');
    }
    function ppReveal(btn) {
        document.querySelectorAll('.pp-hidden').forEach(function (el) {
            if (el.dataset.real) el.textContent = el.dataset.real;
        });
        btn.style.display = 'none';
    }
</script>
@endsection
