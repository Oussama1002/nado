@extends('layouts.app')

@section('title', ($article->meta_title ?: $article->title) . ' | NADO.ma')

@section('link')
@php $ogImg = $article->image ? asset($article->image) : asset('assets/images/logo.png'); @endphp
<meta name="description" content="{{ $article->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?: $article->content), 160) }}">
<meta property="og:type" content="article">
<meta property="og:title" content="{{ $article->meta_title ?: $article->title }}">
<meta property="og:description" content="{{ $article->meta_description ?: \Illuminate\Support\Str::limit(strip_tags($article->excerpt ?: $article->content), 160) }}">
<meta property="og:image" content="{{ $ogImg }}">
<meta property="og:url" content="{{ url()->current() }}">
<meta name="twitter:card" content="summary_large_image">
<script type="application/ld+json">
{!! json_encode(array_filter([
    '@context' => 'https://schema.org',
    '@type' => 'Article',
    'headline' => $article->title,
    'image' => $ogImg,
    'datePublished' => optional($article->created_at)->toAtomString(),
    'dateModified' => optional($article->updated_at)->toAtomString(),
    'author' => ['@type' => 'Organization', 'name' => $article->author ?: 'NADO.ma'],
    'publisher' => ['@type' => 'Organization', 'name' => 'NADO.ma'],
    'description' => $article->meta_description ?: \Illuminate\Support\Str::limit(strip_tags((string) $article->content), 160),
]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}
</script>
@endsection

@section('content')
<section class="article-detail-section">
    <div class="container">

        <nav class="article-breadcrumb">
            <a href="{{ route('articles.index') }}">Blog</a>
            <span>/</span>
            <span class="current">{{ \Illuminate\Support\Str::limit($article->title, 60) }}</span>
        </nav>

        <article class="article-detail">

            <header class="article-header">
                <h1 class="article-title">{{ $article->title }}</h1>
                <div class="article-meta">
                    @if($article->category)<span style="color:#1459DD;font-weight:800;">{{ $article->category }}</span> · @endif
                    {{ $article->author ? $article->author . ' · ' : '' }}
                    Publié {{ $formatDate($article->created_at) }}
                    · {{ $article->readingTime() }} {{ __('messages.community.reading_time') }}
                    @isset($article->views) · {{ number_format($article->views ?? 0, 0, ',', ' ') }} {{ __('messages.community.views') }} @endisset
                </div>
            </header>

            @if($article->image)
                <figure class="article-hero">
                    <img src="{{ asset($article->image) }}"
                         alt="{{ $article->title }}"
                         loading="lazy"
                         onerror="this.closest('.article-hero').style.display='none';">
                </figure>
            @endif

            <div class="article-content">
                {!! $article->content !!}
            </div>

            @isset($related)
            @if($related->isNotEmpty())
            <div style="margin-top:40px;border-top:1px solid #e2e8f0;padding-top:24px;">
                <h3 style="font-size:18px;font-weight:900;color:#0f172a;margin:0 0 16px;">{{ __('messages.community.related_articles') }}</h3>
                <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(200px,1fr));gap:14px;">
                    @foreach($related as $rel)
                        <a href="{{ route('articles.show', $rel->slug) }}" style="display:block;text-decoration:none;background:#fff;border:1px solid #e2e8f0;border-radius:12px;overflow:hidden;">
                            @if($rel->image)<img src="{{ asset($rel->image) }}" alt="" style="width:100%;height:110px;object-fit:cover;" loading="lazy">@endif
                            <div style="padding:12px;"><div style="font-weight:800;color:#0f172a;font-size:14px;line-height:1.3;">{{ \Illuminate\Support\Str::limit($rel->title, 70) }}</div></div>
                        </a>
                    @endforeach
                </div>
            </div>
            @endif
            @endisset

        </article>

    </div>
</section>

<style>
.article-detail-section {
    background: #f7f9fc;
    padding: 50px 0 80px;
}

.article-detail-section .container {
    max-width: 820px;
    margin: 0 auto;
    padding-left: 16px;
    padding-right: 16px;
}

.article-breadcrumb {
    font-size: 13px;
    color: #94a3b8;
    margin-bottom: 20px;
}
.article-breadcrumb a {
    color: #1e40af;
    text-decoration: none;
    font-weight: 600;
}
.article-breadcrumb a:hover { text-decoration: underline; }
.article-breadcrumb span { margin: 0 6px; }
.article-breadcrumb .current { color: #64748b; margin: 0; }

.article-detail {
    background: #fff;
    border-radius: 18px;
    box-shadow: 0 15px 40px rgba(0,0,0,.08);
    padding: 40px;
    overflow: hidden;
}

.article-header { margin-bottom: 24px; }

.article-title {
    font-size: 34px;
    line-height: 1.25;
    font-weight: 800;
    color: #0f172a;
    margin: 0 0 12px;
    word-wrap: break-word;
}

.article-meta {
    font-size: 13px;
    color: #94a3b8;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .04em;
}

.article-hero {
    margin: 0 0 32px;
    border-radius: 14px;
    overflow: hidden;
}
.article-hero img {
    display: block;
    width: 100%;
    height: auto;
    object-fit: cover;
}

.article-content {
    font-size: 17px;
    line-height: 1.85;
    color: #334155;
    word-wrap: break-word;
}
.article-content p { margin: 0 0 1.2em; }
.article-content h2,
.article-content h3,
.article-content h4 {
    color: #0f172a;
    font-weight: 700;
    line-height: 1.3;
    margin: 1.6em 0 .6em;
}
.article-content h2 { font-size: 24px; }
.article-content h3 { font-size: 20px; }
.article-content ul,
.article-content ol { margin: 0 0 1.2em 1.4em; }
.article-content li { margin-bottom: .5em; }
.article-content img {
    max-width: 100%;
    height: auto;
    border-radius: 10px;
    margin: 1em 0;
}
.article-content a { color: #1e40af; font-weight: 600; }

@media (max-width: 768px) {
    .article-detail { padding: 24px; border-radius: 14px; }
    .article-title { font-size: 26px; }
    .article-content { font-size: 16px; line-height: 1.75; }
}
</style>
@endsection
