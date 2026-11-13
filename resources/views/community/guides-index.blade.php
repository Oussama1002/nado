@extends('layouts.app')

@section('title', __('messages.community.guides_title') . ' | NADO.ma')

@section('content')
<style>
    .gi-wrap { max-width:1100px; margin:0 auto; padding:24px 16px 60px; }
    .gi-h1 { font-size:26px; font-weight:900; color:#0f172a; margin:0 0 6px; }
    .gi-sub { color:#64748b; margin:0 0 24px; }
    .gi-grid { display:grid; grid-template-columns:repeat(auto-fill, minmax(260px,1fr)); gap:16px; }
    .gi-card { display:block; background:#fff; border:1px solid #e2e8f0; border-radius:16px; overflow:hidden; text-decoration:none;
        box-shadow:0 1px 3px rgba(0,0,0,.05), 0 10px 30px rgba(0,0,0,.05); transition:transform .15s; }
    .gi-card:hover { transform:translateY(-3px); }
    .gi-card img { width:100%; height:160px; object-fit:cover; background:#f1f5f9; }
    .gi-card .gi-body { padding:14px 16px; }
    .gi-card h3 { font-size:16px; font-weight:900; color:#0f172a; margin:0 0 6px; }
    .gi-card p { color:#64748b; font-size:13px; margin:0; line-height:1.5; }
    .gi-empty { text-align:center; color:#64748b; padding:50px; }
</style>

<div class="gi-wrap">
    <h1 class="gi-h1">{{ __('messages.community.guides_title') }}</h1>
    <p class="gi-sub">{{ __('messages.community.guides_intro') }}</p>

    @if($guides->isEmpty())
        <div class="gi-empty">{{ __('messages.community.no_guides') }}</div>
    @else
        <div class="gi-grid">
            @foreach($guides as $g)
                <a class="gi-card" href="{{ route('guides.show', $g->slug) }}">
                    <img src="{{ $g->image ? asset('storage/'.$g->image) : asset('assets/images/banner/achat-voiture.jpg') }}" alt="{{ $g->localTitle() }}" loading="lazy">
                    <div class="gi-body">
                        <h3>{{ $g->localTitle() }}</h3>
                        <p>{{ \Illuminate\Support\Str::limit(strip_tags($g->localIntro() ?? ''), 110) }}</p>
                    </div>
                </a>
            @endforeach
        </div>
        <div style="margin-top:24px;">{{ $guides->links() }}</div>
    @endif
</div>
@endsection
