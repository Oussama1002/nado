@extends('layouts.app')

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
                    Publié {{ $formatDate($article->created_at) }}
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
