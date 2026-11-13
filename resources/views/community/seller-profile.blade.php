@extends('layouts.app')

@section('title', ($seller->company ?: $seller->name) . ' — ' . __('messages.community.seller_profile') . ' | NADO.ma')

@section('content')
@php
    $stars = function ($avg, $size = 18) {
        $full = floor($avg); $half = ($avg - $full) >= 0.5;
        $out = '';
        for ($i = 1; $i <= 5; $i++) {
            $fill = $i <= $full ? '#f59e0b' : (($i == $full + 1 && $half) ? 'url(#halfg)' : '#e2e8f0');
            $out .= '<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 24 24" style="display:inline-block;vertical-align:middle;"><path fill="'.$fill.'" d="M12 2l2.9 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l7.1-1.01z"/></svg>';
        }
        return $out;
    };
    $sellerName = $seller->company ?: $seller->name;
@endphp

<style>
    .sp-wrap { max-width:900px; margin:0 auto; padding:24px 16px 60px; }
    .sp-head { display:flex; align-items:center; gap:18px; flex-wrap:wrap; background:#fff; border:1px solid #e2e8f0;
        border-radius:18px; padding:22px; box-shadow:0 1px 3px rgba(0,0,0,.05), 0 10px 30px rgba(0,0,0,.05); }
    .sp-avatar { width:66px; height:66px; border-radius:50%; background:#1459DD; color:#fff; display:flex;
        align-items:center; justify-content:center; font-size:26px; font-weight:900; }
    .sp-name { font-size:22px; font-weight:900; color:#0f172a; margin:0; }
    .sp-rating { color:#64748b; font-size:14px; margin-top:4px; }
    .sp-section { margin-top:28px; }
    .sp-section h2 { font-size:18px; font-weight:900; color:#0f172a; margin:0 0 14px; }
    .sp-review { background:#fff; border:1px solid #e2e8f0; border-radius:14px; padding:16px 18px; margin-bottom:12px; }
    .sp-review .sp-rev-head { display:flex; align-items:center; justify-content:space-between; gap:10px; flex-wrap:wrap; }
    .sp-rev-author { font-weight:800; color:#0f172a; }
    .sp-badge { font-size:11px; font-weight:800; padding:3px 9px; border-radius:9999px; background:#dcfce7; color:#166534; }
    .sp-rev-title { font-weight:800; color:#0f172a; margin:8px 0 4px; }
    .sp-rev-body { color:#475569; font-size:14px; line-height:1.6; white-space:pre-line; }
    .sp-form { background:#fff; border:1px solid #e2e8f0; border-radius:16px; padding:20px; }
    .sp-form label { display:block; font-weight:700; color:#334155; font-size:14px; margin:0 0 6px; }
    .sp-form input[type=text], .sp-form textarea { width:100%; border:1.5px solid #cbd5e1; border-radius:10px; padding:10px 12px; font-size:14px; margin-bottom:14px; }
    .sp-starpick { display:inline-flex; flex-direction:row-reverse; gap:4px; margin-bottom:14px; }
    .sp-starpick input { display:none; }
    .sp-starpick label { cursor:pointer; font-size:30px; color:#e2e8f0; margin:0; }
    .sp-starpick input:checked ~ label, .sp-starpick label:hover, .sp-starpick label:hover ~ label { color:#f59e0b; }
    .sp-btn { background:#1459DD; color:#fff; border:none; border-radius:10px; padding:11px 22px; font-weight:800; cursor:pointer; }
    .sp-empty { color:#64748b; background:#fff; border:1px dashed #e2e8f0; border-radius:14px; padding:30px; text-align:center; }
    html[dir="rtl"] .sp-starpick { flex-direction:row; }
    svg linearGradient { }
</style>

<svg width="0" height="0" style="position:absolute;"><defs><linearGradient id="halfg"><stop offset="50%" stop-color="#f59e0b"/><stop offset="50%" stop-color="#e2e8f0"/></linearGradient></defs></svg>

<div class="sp-wrap">
    <div class="sp-head">
        <div class="sp-avatar">{{ mb_strtoupper(mb_substr($sellerName, 0, 1)) }}</div>
        <div>
            <h1 class="sp-name">{{ $sellerName }}</h1>
            <div class="sp-rating">
                @if($agg['count'] > 0)
                    {!! $stars($agg['avg']) !!}
                    <strong style="color:#0f172a;">{{ number_format($agg['avg'], 1) }}</strong>
                    <span>{{ __('messages.community.based_on', ['count' => $agg['count']]) }}</span>
                @else
                    <span>{{ __('messages.community.no_reviews') }}</span>
                @endif
            </div>
            <div style="color:#94a3b8;font-size:12px;margin-top:2px;">{{ __('messages.community.seller_since') }}</div>
        </div>
    </div>

    @if(session('success'))<div style="background:#dcfce7;color:#166534;padding:12px 16px;border-radius:12px;margin-top:16px;font-weight:700;">{{ session('success') }}</div>@endif
    @if(session('error'))<div style="background:#fee2e2;color:#991b1b;padding:12px 16px;border-radius:12px;margin-top:16px;font-weight:700;">{{ session('error') }}</div>@endif

    <div class="sp-section">
        <h2>{{ __('messages.community.seller_reviews') }}</h2>
        @forelse($reviews as $r)
            <div class="sp-review">
                <div class="sp-rev-head">
                    <div>
                        {!! $stars($r->rating, 15) !!}
                        <span class="sp-rev-author">{{ $r->reviewer->name ?? 'Client' }}</span>
                    </div>
                    @if($r->verified)<span class="sp-badge">{{ __('messages.community.verified_buyer') }}</span>@endif
                </div>
                @if($r->title)<div class="sp-rev-title">{{ $r->title }}</div>@endif
                @if($r->body)<div class="sp-rev-body">{{ $r->body }}</div>@endif
            </div>
        @empty
            <div class="sp-empty">{{ __('messages.community.no_reviews') }}</div>
        @endforelse
        {{ $reviews->links() }}
    </div>

    <div class="sp-section">
        <h2>{{ __('messages.community.leave_review') }}</h2>
        @auth('client')
            <form class="sp-form" action="{{ route('sellers.review.store', $seller->id) }}" method="POST">
                @csrf
                <label>{{ __('messages.community.your_rating') }}</label>
                <div class="sp-starpick">
                    @for($i = 5; $i >= 1; $i--)
                        <input type="radio" id="star{{ $i }}" name="rating" value="{{ $i }}" {{ $i === 5 ? 'checked' : '' }}>
                        <label for="star{{ $i }}">★</label>
                    @endfor
                </div>
                <label>{{ __('messages.community.review_title') }}</label>
                <input type="text" name="title" maxlength="120">
                <label>{{ __('messages.community.review_body') }}</label>
                <textarea name="body" rows="4" maxlength="2000"></textarea>
                <button type="submit" class="sp-btn">{{ __('messages.community.submit_review') }}</button>
            </form>
        @else
            <div class="sp-empty">
                {{ __('messages.community.login_to_review') }}
                <a href="{{ route('client.login') }}?redirect={{ urlencode(url()->current()) }}" class="sp-btn" style="display:inline-block;margin-top:12px;text-decoration:none;">{{ __('messages.common.login') ?? 'Connexion' }}</a>
            </div>
        @endauth
    </div>
</div>

@if($agg['count'] > 0)
<script type="application/ld+json">
{!! json_encode([
    '@context' => 'https://schema.org',
    '@type' => 'Organization',
    'name' => $sellerName,
    'aggregateRating' => [
        '@type' => 'AggregateRating',
        'ratingValue' => $agg['avg'],
        'reviewCount' => $agg['count'],
        'bestRating' => 5, 'worstRating' => 1,
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endif
@endsection
