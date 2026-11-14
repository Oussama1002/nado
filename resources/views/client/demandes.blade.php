@extends('layouts.client')

@section('title', 'Mes demandes')

@section('client_title', 'Mes demandes')
@section('client_subtitle', 'Vos recherches et les propositions reçues.')
@section('client_actions')
    <a href="{{ route('demandes.create') }}" class="btn btn-primary">➕ Nouvelle demande</a>
@endsection

@section('client_content')
<style>
    .md-list { display:flex; flex-direction:column; gap:18px; }
    .md-card { background:#fff; border:1px solid #e2e8f0; border-radius:18px; box-shadow:0 1px 3px rgba(0,0,0,.05); overflow:hidden; }
    .md-head { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; flex-wrap:wrap; padding:16px 18px; border-bottom:1px solid #f1f5f9; }
    .md-title { font-size:16px; font-weight:900; color:#0f172a; margin:0; }
    .md-meta { font-size:12.5px; color:#64748b; margin-top:4px; }
    .md-count { background:#eff4ff; color:#1459DD; font-size:12px; font-weight:800; padding:5px 12px; border-radius:999px; white-space:nowrap; }
    .md-replies { padding:14px 18px; display:flex; flex-direction:column; gap:10px; }
    .md-reply { background:#f8fafc; border:1px solid #e2e8f0; border-radius:12px; padding:12px 14px; }
    .md-reply-top { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .md-rname { font-weight:800; color:#0f172a; font-size:13.5px; }
    .md-rtime { font-size:12px; color:#94a3b8; }
    .md-rbadge { font-size:10px; font-weight:800; text-transform:uppercase; letter-spacing:.04em; color:#1459DD; background:#eff4ff; padding:3px 9px; border-radius:999px; }
    .md-rprop { margin-top:8px; font-size:13.5px; color:#334155; }
    .md-rprop b { color:#0f172a; }
    .md-rmsg { margin:6px 0 0; font-size:13.5px; color:#334155; white-space:pre-line; }
    .md-rlink { display:inline-block; margin-top:8px; font-size:12.5px; font-weight:800; color:#fff; background:#0f172a; padding:7px 14px; border-radius:10px; text-decoration:none; }
    .md-none { color:#64748b; font-size:13px; padding:4px 0; }
    .md-empty { text-align:center; color:#94a3b8; padding:48px 20px; }
</style>

@if($demandes->isEmpty())
    <div class="md-card"><div class="md-empty">
        <div style="font-size:36px;margin-bottom:10px;">🔎</div>
        <p style="font-weight:700;">Vous n'avez pas encore publié de demande.</p>
        <a href="{{ route('demandes.create') }}" class="btn btn-primary" style="margin-top:12px;">➕ Publier une demande</a>
    </div></div>
@else
<div class="md-list">
    @foreach($demandes as $d)
        @php
            $dTitle = trim(($d->brand?->name ?? '') . ' ' . ($d->modele?->name ?? '')) ?: 'Toutes marques';
            $visible = $d->replies;
        @endphp
        <div class="md-card">
            <div class="md-head">
                <div>
                    <h3 class="md-title">{{ $dTitle }}</h3>
                    <div class="md-meta">
                        <a href="{{ route('demandes.show', $d->id) }}" style="color:#1459DD;font-weight:700;">Voir la demande</a>
                        · {{ $d->ville?->name ?? '—' }}
                        · {{ $d->created_at->locale(app()->getLocale())->isoFormat('D MMM Y') }}
                    </div>
                </div>
                <span class="md-count">{{ $visible->count() }} {{ $visible->count() > 1 ? 'réponses' : 'réponse' }}</span>
            </div>

            <div class="md-replies">
                @forelse($visible as $r)
                    @php $meta = $r->proposalData(); @endphp
                    <div class="md-reply">
                        <div class="md-reply-top">
                            <span class="md-rname">{{ ($meta['proposer_name'] ?? null) ?: ($r->client?->name ?? 'Membre') }}</span>
                            <span>
                                @if($meta)<span class="md-rbadge">Proposition</span>@endif
                                <span class="md-rtime">{{ $r->created_at->locale(app()->getLocale())->diffForHumans() }}</span>
                            </span>
                        </div>

                        @if($meta)
                            @php
                                $pTitle = $meta['title'] ?? trim(($meta['mark'] ?? '').' '.($meta['modele'] ?? ''));
                                $bits = [];
                                if (!empty($meta['year'])) { $bits[] = $meta['year']; }
                                if (isset($meta['km']))    { $bits[] = number_format((int)$meta['km'], 0, ',', ' ').' km'; }
                                if (!empty($meta['prix'])) { $bits[] = number_format((int)$meta['prix'], 0, ',', ' ').' DH'; }
                            @endphp
                            <div class="md-rprop">
                                <b>{{ $pTitle ?: 'Véhicule' }}</b>
                                {{ count($bits) ? ' · '.implode(' · ', $bits) : '' }}
                            </div>
                            <a href="{{ route('demandes.proposition.show', [$d->id, $r->id]) }}" class="md-rlink">Voir le détail</a>
                        @else
                            <p class="md-rmsg">{{ $r->message }}</p>
                        @endif
                    </div>
                @empty
                    <p class="md-none">Aucune réponse pour le moment.</p>
                @endforelse
            </div>
        </div>
    @endforeach
</div>
@endif
@endsection
