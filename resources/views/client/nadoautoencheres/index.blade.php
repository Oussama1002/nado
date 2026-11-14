@extends('layouts.client')

@section('title', 'Mes Véhicules')

@section('client_title', 'Mes véhicules')
@section('client_content')

<style>
    .cv-section-head { margin:4px 0 16px; }
    .cv-section-head h2 { font-size:18px; font-weight:900; color:#0f172a; margin:0; }
    .cv-section-head p  { font-size:13px; color:#64748b; margin:2px 0 0; }
    .cv-section + .cv-section { margin-top:36px; }

    .cv-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px, 1fr)); gap:18px; }
    .cv-card { background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden;
        box-shadow:0 1px 3px rgba(0,0,0,.05); transition:transform .2s, box-shadow .2s; display:flex; flex-direction:column; }
    .cv-card:hover { transform:translateY(-3px); box-shadow:0 12px 30px -10px rgba(15,23,42,.18); }
    .cv-imgwrap { position:relative; aspect-ratio:16/10; background:#f1f5f9; }
    .cv-imgwrap img { width:100%; height:100%; object-fit:cover; display:block; }
    .cv-imgwrap .ph { width:100%; height:100%; display:flex; align-items:center; justify-content:center; font-size:40px; color:#94a3b8; }
    .cv-price { position:absolute; left:10px; bottom:10px; background:#1459DD; color:#fff;
        font-weight:800; font-size:13px; padding:6px 12px; border-radius:999px; }
    .cv-status { position:absolute; right:10px; top:10px; background:rgba(15,23,42,.8); color:#fff;
        font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; padding:4px 10px; border-radius:999px; }
    .cv-body { padding:14px 16px 16px; display:flex; flex-direction:column; gap:8px; flex:1; }
    .cv-title { font-size:15px; font-weight:800; color:#0f172a; margin:0; text-transform:uppercase; }
    .cv-meta { font-size:12.5px; color:#64748b; display:flex; flex-wrap:wrap; gap:4px 12px; }
    .cv-btn { margin-top:auto; display:inline-flex; align-items:center; justify-content:center; gap:7px;
        background:#0f172a; color:#fff; text-decoration:none; font-weight:800; font-size:13px;
        padding:10px 14px; border-radius:12px; transition:background .15s; }
    .cv-btn:hover { background:#1e293b; color:#fff; }
    .cv-empty { background:#fff; border:1px dashed #cbd5e1; border-radius:16px; padding:36px 20px;
        text-align:center; color:#94a3b8; font-weight:600; }
</style>

{{-- ── Section 1 : Véhicules (flotte & enchère) ───────────────── --}}
<div class="cv-section">
    <div class="cv-section-head">
        <h2>Mes véhicules</h2>
        <p>Gestion (flotte &amp; enchère) — {{ $vehicules->count() }} véhicule(s)</p>
    </div>

    @if($vehicules->isEmpty())
        <div class="cv-empty">🚗 Aucun véhicule pour le moment.</div>
    @else
        <div class="cv-grid">
            @foreach($vehicules as $v)
                @php
                    $vImg = is_array($v->images) ? ($v->images[0] ?? null) : $v->images;
                    $vUrl = $vImg ? asset('storage/nadoautoenchere/' . $vImg) : null;
                    $vTitle = trim(($v->brand?->name ?? '') . ' ' . ($v->modeleRelation?->name ?? '')) ?: ($v->titre ?? 'Véhicule');
                @endphp
                <div class="cv-card">
                    <div class="cv-imgwrap">
                        @if($vUrl) <img src="{{ $vUrl }}" alt="{{ $vTitle }}" loading="lazy"> @else <div class="ph">🚗</div> @endif
                        <span class="cv-price">{{ number_format($v->prix ?? 0, 0, ',', ' ') }} DH</span>
                        <span class="cv-status">Enchère</span>
                    </div>
                    <div class="cv-body">
                        <h3 class="cv-title">{{ $vTitle }}</h3>
                        <div class="cv-meta">
                            @if($v->year)<span>📅 {{ $v->year }}</span>@endif
                            @if($v->km)<span>🛣 {{ number_format($v->km, 0, ',', ' ') }} km</span>@endif
                            @if($v->city)<span>📍 {{ $v->city }}</span>@endif
                            <span>👁 {{ $v->views ?? 0 }}</span>
                        </div>
                        <a href="{{ route('client.nadoautoencheres.edit', $v->id) }}" class="cv-btn">
                            <i class="fa fa-pencil"></i> Modifier
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

{{-- ── Section 2 : Annonces ───────────────────────────────────── --}}
<div class="cv-section">
    <div class="cv-section-head">
        <h2>Mes annonces</h2>
        <p>Gestion de vos annonces — {{ $annonces->count() }} annonce(s)</p>
    </div>

    @if($annonces->isEmpty())
        <div class="cv-empty">🚗 Aucune annonce pour le moment.</div>
    @else
        <div class="cv-grid">
            @foreach($annonces as $a)
                @php
                    $aImg = is_array($a->images) ? ($a->images[0] ?? null) : $a->images;
                    $aUrl = $aImg ? asset('storage/ads/' . $aImg) : (optional($a->media->first())->name ? asset('storage/ads/' . $a->media->first()->name) : null);
                    $aBrand = $a->brand?->name ?? (is_numeric($a->mark ?? null) ? null : $a->mark);
                    $aModel = $a->modeleRelation?->name ?? (is_numeric($a->modele ?? null) ? null : $a->modele);
                    $aTitle = trim(($aBrand ?? '') . ' ' . ($aModel ?? '')) ?: ($a->titre ?? 'Véhicule');
                    $aUrlMarque = str_replace([' ', '_'], '', (string) ($a->mark ?? '')) ?: 'voiture';
                    $aUrlModele = str_replace([' ', '_'], '', (string) ($a->modele ?? '')) ?: 'auto';
                @endphp
                <div class="cv-card">
                    <div class="cv-imgwrap">
                        @if($aUrl) <img src="{{ $aUrl }}" alt="{{ $aTitle }}" loading="lazy"> @else <div class="ph">🚗</div> @endif
                        <span class="cv-price">{{ number_format($a->prix ?? 0, 0, ',', ' ') }} DH</span>
                        <span class="cv-status">Annonce</span>
                    </div>
                    <div class="cv-body">
                        <h3 class="cv-title">{{ $aTitle }}</h3>
                        <div class="cv-meta">
                            @if($a->year)<span>📅 {{ $a->year }}</span>@endif
                            @if($a->km)<span>🛣 {{ number_format($a->km, 0, ',', ' ') }} km</span>@endif
                            @if($a->city)<span>📍 {{ $a->city }}</span>@endif
                            <span>👁 {{ $a->views ?? 0 }}</span>
                        </div>
                        <a href="{{ route('achat.detail', ['marque' => $aUrlMarque, 'modele' => $aUrlModele, 'id' => $a->id]) }}" class="cv-btn">
                            <i class="fa fa-eye"></i> Voir l'annonce
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</div>

@endsection
