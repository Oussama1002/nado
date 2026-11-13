@extends('layouts.app')

@section('title', ($guide->meta_title ?: $guide->localTitle()) . ' | NADO.ma')
@section('meta_description', $guide->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($guide->localIntro() ?? ''), 160))

@section('content')
@php
    $stars = function ($avg, $size = 16) {
        $full = floor($avg); $out = '';
        for ($i = 1; $i <= 5; $i++) {
            $fill = $i <= round($avg) ? '#f59e0b' : '#e2e8f0';
            $out .= '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;"><path fill="'.$fill.'" d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l7.1-1.01z"/></svg>';
        }
        return $out;
    };
    $fmt = fn($n) => number_format((int)$n, 0, ',', ' ') . ' MAD';
@endphp

<style>
    .gd-wrap { max-width:860px; margin:0 auto; padding:24px 16px 60px; }
    .gd-hero { border-radius:18px; overflow:hidden; margin-bottom:22px; }
    .gd-hero img { width:100%; height:300px; object-fit:cover; display:block; background:#f1f5f9; }
    .gd-h1 { font-size:28px; font-weight:900; color:#0f172a; margin:0 0 8px; }
    .gd-intro { color:#475569; font-size:16px; line-height:1.7; margin:0 0 20px; }
    .gd-pricebox { background:#eff5ff; border:1px solid #cfe0ff; border-radius:16px; padding:18px 20px; margin:20px 0; }
    .gd-pricebox .gd-pl { font-size:12px; font-weight:800; text-transform:uppercase; letter-spacing:.1em; color:#64748b; }
    .gd-pricebox .gd-pv { font-size:22px; font-weight:900; color:#1459DD; margin-top:4px; }
    .gd-specs { display:grid; grid-template-columns:repeat(auto-fill, minmax(150px,1fr)); gap:10px; margin:16px 0 24px; }
    .gd-spec { background:#fff; border:1px solid #e2e8f0; border-radius:12px; padding:10px 14px; }
    .gd-spec .k { font-size:12px; color:#64748b; font-weight:700; }
    .gd-spec .v { font-size:15px; color:#0f172a; font-weight:800; }
    .gd-body { color:#334155; font-size:16px; line-height:1.8; }
    .gd-body h2, .gd-body h3 { color:#0f172a; font-weight:900; margin:24px 0 10px; }
    .gd-cta { display:inline-block; background:#F37021; color:#fff; text-decoration:none; font-weight:900; padding:14px 26px; border-radius:12px; margin:24px 0; }
    .gd-sec { margin-top:34px; }
    .gd-sec h2 { font-size:20px; font-weight:900; color:#0f172a; margin:0 0 14px; }
    .gd-rev { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:14px 16px; margin-bottom:10px; }
    .gd-rev .a { font-weight:800; color:#0f172a; margin-left:8px; }
    .gd-form { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:18px; }
    .gd-form label { display:block; font-weight:700; color:#334155; font-size:14px; margin-bottom:6px; }
    .gd-form input[type=text], .gd-form textarea { width:100%; border:1.5px solid #cbd5e1; border-radius:10px; padding:10px 12px; font-size:14px; margin-bottom:12px; }
    .gd-starpick { display:inline-flex; flex-direction:row-reverse; gap:4px; margin-bottom:12px; }
    .gd-starpick input { display:none; }
    .gd-starpick label { cursor:pointer; font-size:28px; color:#e2e8f0; }
    .gd-starpick input:checked ~ label, .gd-starpick label:hover, .gd-starpick label:hover ~ label { color:#f59e0b; }
    .gd-btn { background:#1459DD; color:#fff; border:none; border-radius:10px; padding:11px 22px; font-weight:800; cursor:pointer; }
    html[dir="rtl"] .gd-starpick { flex-direction:row; }
</style>

<div class="gd-wrap">
    @if($guide->image)
    <div class="gd-hero"><img src="{{ asset('storage/'.$guide->image) }}" alt="{{ $guide->localTitle() }}"></div>
    @endif

    <h1 class="gd-h1">{{ $guide->localTitle() }}</h1>
    @if($guide->localIntro())<p class="gd-intro">{{ $guide->localIntro() }}</p>@endif

    @if($priceRange['count'] > 0)
    <div class="gd-pricebox">
        <div class="gd-pl">{{ __('messages.community.guide_price_range') }}</div>
        <div class="gd-pv">{{ $fmt($priceRange['min']) }} — {{ $fmt($priceRange['max']) }}</div>
        <div style="color:#64748b;font-size:13px;margin-top:4px;">{{ $priceRange['count'] }} {{ __('messages.catalog.count_many') ?? 'annonces' }}</div>
    </div>
    @endif

    @if(!empty($guide->specs) && is_array($guide->specs))
    <div class="gd-sec"><h2>{{ __('messages.community.key_specs') }}</h2>
        <div class="gd-specs">
            @foreach($guide->specs as $k => $v)
                <div class="gd-spec"><div class="k">{{ $k }}</div><div class="v">{{ $v }}</div></div>
            @endforeach
        </div>
    </div>
    @endif

    @if($guide->localBody())
    <div class="gd-body">{!! $guide->localBody() !!}</div>
    @endif

    <a class="gd-cta" href="{{ $catalogUrl }}">{{ __('messages.community.see_listings') }} →</a>

    {{-- Avis propriétaires --}}
    <div class="gd-sec" id="avis">
        <h2>{{ __('messages.community.owner_reviews') }}
            @if($ownerAgg['count'] > 0)
                <span style="font-size:15px;font-weight:700;color:#64748b;">· {!! $stars($ownerAgg['avg']) !!} {{ number_format($ownerAgg['avg'],1) }} ({{ $ownerAgg['count'] }})</span>
            @endif
        </h2>

        @if(session('success'))<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:12px;margin-bottom:14px;font-weight:700;">{{ session('success') }}</div>@endif

        @forelse($ownerReviews as $r)
            <div class="gd-rev">
                {!! $stars($r->rating, 14) !!}<span class="a">{{ $r->reviewer->name ?? 'Propriétaire' }}</span>
                @if($r->title)<div style="font-weight:800;margin-top:6px;">{{ $r->title }}</div>@endif
                @if($r->body)<div style="color:#475569;font-size:14px;line-height:1.6;margin-top:4px;white-space:pre-line;">{{ $r->body }}</div>@endif
            </div>
        @empty
            <p style="color:#64748b;">{{ __('messages.community.no_reviews') }}</p>
        @endforelse

        <div style="margin-top:16px;">
        @auth('client')
            <form class="gd-form" action="{{ route('guides.review.store', $guide->id) }}" method="POST">
                @csrf
                <label>{{ __('messages.community.your_rating') }}</label>
                <div class="gd-starpick">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" id="gstar{{ $i }}" name="rating" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                        <label for="gstar{{ $i }}">★</label>
                    @endfor
                </div>
                <label>{{ __('messages.community.review_title') }}</label>
                <input type="text" name="title" maxlength="120">
                <label>{{ __('messages.community.review_body') }}</label>
                <textarea name="body" rows="3" maxlength="2000"></textarea>
                <button class="gd-btn">{{ __('messages.community.submit_review') }}</button>
            </form>
        @else
            <p style="color:#64748b;">{{ __('messages.community.login_to_review') }}</p>
        @endauth
        </div>
    </div>
</div>

<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Product',
    'name' => $guide->localTitle(),
    'description' => $guide->meta_description ?: strip_tags((string) $guide->localIntro()),
    'image' => $guide->image ? asset('storage/'.$guide->image) : null,
    'brand' => $guide->brand->name ?? null,
    'aggregateRating' => $ownerAgg['count'] > 0 ? [
        '@type' => 'AggregateRating',
        'ratingValue' => $ownerAgg['avg'],
        'reviewCount' => $ownerAgg['count'],
        'bestRating' => 5, 'worstRating' => 1,
    ] : null,
    'offers' => $priceRange['count'] > 0 ? [
        '@type' => 'AggregateOffer',
        'priceCurrency' => 'MAD',
        'lowPrice' => $priceRange['min'],
        'highPrice' => $priceRange['max'],
        'offerCount' => $priceRange['count'],
    ] : null,
]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection
