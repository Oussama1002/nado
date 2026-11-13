@extends('layouts.client')

@section('title', 'Mes favoris')

@section('client_title', '')
@section('client_subtitle', '')
@section('client_content')
<style>
    :root {
        --blue:#1459DD;
        --slate-50:#f8fafc; --slate-100:#f1f5f9; --slate-200:#e2e8f0;
        --slate-400:#94a3b8; --slate-500:#64748b; --slate-700:#334155; --slate-900:#0f172a;
        --radius:16px;
        --shadow: 0 1px 3px rgba(0,0,0,.06), 0 10px 30px rgba(0,0,0,.08);
    }
    body { background: var(--slate-50); }
    .page-wrap { padding: 0 0 10px; }
    .page-head { display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom: 22px; }
    .page-title { font-size: 28px; font-weight: 900; color: var(--slate-900); letter-spacing: -.02em; margin:0; }
    .page-sub { margin: 6px 0 0; color: var(--slate-500); font-size: 15px; font-weight: 600; }
    .grid { display:grid; grid-template-columns: repeat(auto-fill, minmax(280px,1fr)); gap: 16px; }
    .cardx {
        background:#fff; border:1px solid var(--slate-200); border-radius: var(--radius);
        overflow:hidden; box-shadow: var(--shadow);
        transition: transform .15s ease, box-shadow .2s ease, border-color .2s ease;
    }
    .cardx:hover { transform: translateY(-2px); border-color: rgba(20,89,221,.35); box-shadow: 0 16px 45px rgba(0,0,0,.12); }
    .cardx-img { width:100%; height: 190px; object-fit: cover; background: var(--slate-100); display:block; }
    .cardx-body { padding: 14px 14px 16px; }
    .cardx-title { font-size: 16px; font-weight: 900; color: var(--slate-900); margin: 0 0 6px; }
    .cardx-meta { font-size: 14px; color: var(--slate-500); font-weight: 650; margin: 0 0 10px; }
    .row2 { display:flex; align-items:center; justify-content:space-between; gap:12px; margin-top: 12px; }
    .price { font-size: 18px; font-weight: 900; color: var(--slate-900); }
    .btnx {
        display:inline-flex; align-items:center; justify-content:center;
        padding: 10px 12px; border-radius: 12px;
        background: var(--slate-100); border:1px solid var(--slate-200);
        color: var(--slate-900); text-decoration:none;
        font-weight: 800; font-size: 13px;
    }
    .btnx:hover { background:#dbeafe; border-color:#bfdbfe; color: var(--blue); }
    .empty {
        background:#fff; border:1px dashed var(--slate-200); border-radius: var(--radius);
        padding: 50px 16px; text-align:center; color: var(--slate-500);
        font-size: 16px; font-weight: 700;
    }
</style>

<div class="page-wrap">
    <div class="container">
        <div class="page-head">
            <div>
                <h2 class="page-title">Mes favoris</h2>
                <p class="page-sub">Retrouvez vos annonces enregistrées.</p>
            </div>
        </div>

        @if($favorites->isEmpty())
            <div class="empty">Vous n’avez pas encore de favoris.</div>
        @else
            <div class="grid">
                @foreach($favorites as $annonce)
                    @php
                        $flotteFav = !empty($isFlotte);
                        if ($flotteFav) {
                            // Favori d'enchère.
                            $imgs  = is_array($annonce->images) ? $annonce->images : [];
                            $img   = !empty($imgs[0]) ? asset('storage/nadoautoenchere/' . $imgs[0]) : null;
                            $title = trim(($annonce->brand?->name ?? $annonce->mark ?? '') . ' ' . ($annonce->modeleRelation?->name ?? $annonce->modele ?? '')) ?: ($annonce->titre ?? 'Enchère');
                            $detailUrl = route('nadoautoenchere.show', $annonce->id);
                            $removeUrl = route('nadoautoenchere.favorite', $annonce->id);
                        } else {
                            // Favori d'annonce.
                            $img   = $annonce->image ? asset('storage/ads/' . $annonce->image) : null;
                            $title = trim(($annonce->mark ?? '') . ' ' . ($annonce->modele ?? ''));
                            $detailUrl = route('achat.detail', ['marque' => str_replace(' ', '', $annonce->mark), 'modele' => str_replace(' ', '', $annonce->modele), 'id' => $annonce->id]);
                            $removeUrl = route('favorite.toggle', $annonce->id);
                        }
                    @endphp
                    <div class="cardx">
                        @if($img)
                            <img class="cardx-img" src="{{ $img }}" alt="{{ $annonce->titre }}" loading="lazy">
                        @else
                            <div class="cardx-img" style="display:flex;align-items:center;justify-content:center;color:var(--slate-400);font-size:32px;">❤️</div>
                        @endif
                        <div class="cardx-body">
                            <h3 class="cardx-title">{{ $title }}</h3>
                            <p class="cardx-meta">{{ $annonce->year ?? '—' }} · {{ number_format($annonce->km ?? 0, 0, ',', ' ') }} km · {{ $annonce->city ?? '—' }}</p>
                            <div class="row2">
                                <div class="price">{{ number_format($annonce->prix ?? 0, 0, ',', ' ') }} MAD</div>
                                <div style="display:flex;gap:8px;">
                                    <a class="btnx" href="{{ $detailUrl }}">
                                        <i class="fa fa-eye"></i>
                                    </a>
                                    <form action="{{ $removeUrl }}" method="POST" onsubmit="return confirm('{{ __('messages.discovery.remove_fav') }} ?');">
                                        @csrf
                                        <button type="submit" class="btnx" style="color:#e11d48;border-color:#fecdd3;background:#fff1f2;" aria-label="{{ __('messages.discovery.remove_fav') }}">
                                            <i class="fa fa-heart-broken"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</div>
@endsection
