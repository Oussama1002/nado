@extends('layouts.app')

@section('title', 'Comparaison | Nado.ma')

@section('content')
@php
    $noPhoto = 'data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI2NDAiIGhlaWdodD0iNDQwIiB2aWV3Qm94PSIwIDAgNjQwIDQ0MCI+PHJlY3Qgd2lkdGg9IjY0MCIgaGVpZ2h0PSI0NDAiIGZpbGw9IiNmMWY1ZjkiLz48dGV4dCB4PSIzMjAiIHk9IjIzMCIgZm9udC1mYW1pbHk9IkFyaWFsIiBmb250LXNpemU9IjIyIiBmaWxsPSIjOTRhM2I4IiB0ZXh0LWFuY2hvcj0ibWlkZGxlIj5OQURPPC90ZXh0Pjwvc3ZnPg==';
    $img = function ($ad) use ($noPhoto) {
        $p = method_exists($ad, 'pathImageAds') ? $ad->pathImageAds() : null;
        if (is_string($p) && trim($p) !== '') {
            if (preg_match('#^(https?:)?//|^data:#', $p)) return $p;
            $path = preg_replace('#^(public/|storage/|ads/)#', '', ltrim(str_replace('\\','/',$p), '/'));
            return asset('storage/ads/' . $path);
        }
        return $noPhoto;
    };
    // Lignes de caractéristiques : [label clé, valeur par annonce]
    $specs = [
        'marque'       => fn($a) => $a->brand->name ?? $a->mark,
        'modele'       => fn($a) => $a->modeleRelation->name ?? $a->modele,
        'year'         => fn($a) => $a->year ?: '—',
        'fuel'         => fn($a) => $a->carburant ?: '—',
        'gearbox'      => fn($a) => $a->box ?: '—',
        'mileage'      => fn($a) => $a->km !== null ? number_format($a->km, 0, ',', ' ').' km' : '—',
        'fiscal_power' => fn($a) => $a->cv ? $a->cv.' CV' : '—',
        'doors'        => fn($a) => $a->nb_Door ?: '—',
        'body'         => fn($a) => $a->bodywork ?: '—',
        'origin'       => fn($a) => $a->origine ?: '—',
    ];
    $equip = [
        'airbags'       => 'airbags',
        'ac'            => 'climatisation',
        'navigation'    => 'gps',
        'sunroof'       => 'toit_ouvrant',
        'leather_seats' => 'siege_cuir',
        'rear_radar'    => 'radar_recul',
        'rear_camera'   => 'camera_recul',
        'abs'           => 'abs',
        'cruise'        => 'regulateur_vitesse',
        'cd'            => 'bluetooth',
    ];
@endphp

<style>
    .cmp-wrap { max-width:1200px; margin:0 auto; padding:24px 16px 60px; }
    .cmp-h1 { font-weight:900; font-size:1.6rem; color:#0f172a; margin:0 0 6px; }
    .cmp-sub { color:#64748b; font-size:.95rem; margin:0 0 20px; }
    .cmp-scroll { overflow-x:auto; -webkit-overflow-scrolling:touch; }
    .cmp-table { border-collapse:separate; border-spacing:0; width:100%; min-width:520px; }
    .cmp-table th, .cmp-table td { padding:12px 14px; text-align:center; border-bottom:1px solid #eef2f7; vertical-align:middle; }
    .cmp-table th.cmp-row-label, .cmp-table td.cmp-row-label { text-align:left; font-weight:800; color:#64748b; font-size:.82rem;
        background:#f8fafc; position:sticky; left:0; z-index:2; min-width:150px; }
    html[dir="rtl"] .cmp-table th.cmp-row-label, html[dir="rtl"] .cmp-table td.cmp-row-label { text-align:right; left:auto; right:0; }
    .cmp-carhead { min-width:170px; }
    .cmp-carhead img { width:100%; height:110px; object-fit:cover; border-radius:12px; display:block; margin-bottom:8px; }
    .cmp-carhead .cmp-title { font-weight:900; color:#0f172a; font-size:.95rem; line-height:1.2; }
    .cmp-carhead .cmp-price { color:#1459DD; font-weight:900; margin-top:4px; }
    .cmp-carhead .cmp-month { color:#64748b; font-size:.78rem; }
    .cmp-carhead a.cmp-view { display:inline-block; margin-top:8px; font-size:.75rem; font-weight:800; color:#F37021; text-decoration:none; }
    .cmp-sec { text-align:left; font-weight:900; color:#0f172a; background:#eef4ff; }
    html[dir="rtl"] .cmp-sec { text-align:right; }
    .cmp-yes { color:#16a34a; font-weight:900; }
    .cmp-no { color:#cbd5e1; }
    .cmp-empty { text-align:center; padding:60px 20px; color:#64748b; }
    .cmp-empty a { display:inline-block; margin-top:14px; background:#1459DD; color:#fff; text-decoration:none; font-weight:800; padding:11px 20px; border-radius:12px; }
</style>

<div class="cmp-wrap">
    <h1 class="cmp-h1">{{ __('messages.compare.title') }}</h1>

    @if($compareAds->isEmpty())
        <div class="cmp-empty">
            <p>{{ __('messages.discovery.compare_empty') }}</p>
            <a href="{{ route('achat.index') }}">{{ __('messages.catalog.search') }}</a>
        </div>
    @else
        <p class="cmp-sub">{{ $compareAds->count() }} / 3</p>
        <div class="cmp-scroll">
            <table class="cmp-table">
                <thead>
                    <tr>
                        <th class="cmp-row-label"></th>
                        @foreach($compareAds as $ad)
                            <th class="cmp-carhead">
                                <img src="{{ $img($ad) }}" alt="" onerror="this.onerror=null;this.src='{{ $noPhoto }}';">
                                <div class="cmp-title">{{ $ad->brand->name ?? $ad->mark }} {{ $ad->modeleRelation->name ?? $ad->modele }}</div>
                                <div class="cmp-price">{{ number_format($ad->prix ?? 0, 0, ',', ' ') }} MAD</div>
                                <div class="cmp-month">{{ number_format(monthly_estimate($ad->prix ?? 0), 0, ',', ' ') }} {{ __('messages.discovery.per_month') }}</div>
                                <a class="cmp-view" href="{{ route('achat.detail', ['marque' => str_replace(' ', '', $ad->mark), 'modele' => str_replace(' ', '', $ad->modele), 'id' => $ad->id]) }}">{{ __('messages.discovery.view_vehicle') }}</a>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    <tr><td class="cmp-sec" colspan="{{ $compareAds->count() + 1 }}">{{ __('messages.compare.tech_specs') }}</td></tr>
                    @foreach($specs as $key => $resolver)
                        <tr>
                            <td class="cmp-row-label">{{ __('messages.compare.'.$key) }}</td>
                            @foreach($compareAds as $ad)
                                <td>{{ $resolver($ad) }}</td>
                            @endforeach
                        </tr>
                    @endforeach

                    <tr><td class="cmp-sec" colspan="{{ $compareAds->count() + 1 }}">{{ __('messages.compare.equipment') }}</td></tr>
                    @foreach($equip as $labelKey => $col)
                        <tr>
                            <td class="cmp-row-label">{{ __('messages.compare.'.$labelKey) }}</td>
                            @foreach($compareAds as $ad)
                                <td>
                                    @if(!empty($ad->$col))
                                        <span class="cmp-yes">✓</span>
                                    @else
                                        <span class="cmp-no">—</span>
                                    @endif
                                </td>
                            @endforeach
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection
