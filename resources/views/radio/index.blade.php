@extends('layouts.app')

@section('title', ($title ?: 'Nado Radio') . ' | nado.ma')

@section('content')
@php
    $playlist = $songs->map(fn ($s) => [
        'title'  => $s->title,
        'artist' => $s->artist ?? '',
        'src'    => asset('storage/' . $s->file_path),
        'cover'  => $s->cover_path ? asset('storage/' . $s->cover_path) : null,
    ])->values();
@endphp

<section class="radio-page">
    <div class="radio-wrap">
        <header class="radio-head">
            <div class="radio-badge"><i data-lucide="boom-box"></i> {{ __('messages.radio.badge') }}</div>
            <h1>{{ $title ?: 'Nado Radio' }}</h1>
            @if($tagline)
                <p class="radio-tagline">{{ $tagline }}</p>
            @endif
        </header>

        @if($playlist->isEmpty())
            <div class="radio-empty">
                <i data-lucide="music-2"></i>
                <p>{{ __('messages.radio.empty') }}</p>
            </div>
        @else
            <div class="radio-player" id="radioPlayer">
                <div class="radio-cover" id="radioCover">
                    <i data-lucide="music" class="radio-cover-fallback"></i>
                </div>

                <div class="radio-now">
                    <div class="radio-now-title" id="radioTitle">—</div>
                    <div class="radio-now-artist" id="radioArtist"></div>
                </div>

                <div class="radio-progress">
                    <span class="radio-time" id="radioCurrent">0:00</span>
                    <input type="range" id="radioSeek" value="0" min="0" max="100" step="0.1" aria-label="{{ __('messages.radio.seek') }}">
                    <span class="radio-time" id="radioDuration">0:00</span>
                </div>

                <div class="radio-controls">
                    <button type="button" id="radioPrev" class="radio-btn" aria-label="{{ __('messages.radio.prev') }}"><i data-lucide="skip-back"></i></button>
                    <button type="button" id="radioPlay" class="radio-btn radio-btn-main" aria-label="{{ __('messages.radio.play') }}"><i data-lucide="play"></i></button>
                    <button type="button" id="radioNext" class="radio-btn" aria-label="{{ __('messages.radio.next') }}"><i data-lucide="skip-forward"></i></button>
                </div>

                {{-- muted + paused by default: no autoplay attribute, playback starts only on click --}}
                <audio id="radioAudio" preload="none"></audio>
            </div>

            <ol class="radio-list" id="radioList">
                @foreach($playlist as $i => $track)
                    <li class="radio-list-item" data-index="{{ $i }}">
                        <span class="radio-list-index">{{ $i + 1 }}</span>
                        <span class="radio-list-meta">
                            <span class="radio-list-title">{{ $track['title'] }}</span>
                            @if($track['artist'])<span class="radio-list-artist">{{ $track['artist'] }}</span>@endif
                        </span>
                        <i data-lucide="play" class="radio-list-icon"></i>
                    </li>
                @endforeach
            </ol>
        @endif
    </div>
</section>

<style>
    .radio-page { max-width: 760px; margin: 0 auto; padding: 24px 16px 80px; }
    .radio-head { text-align: center; margin-bottom: 32px; }
    .radio-badge { display: inline-flex; align-items: center; gap: 8px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: var(--primary); background: #dbeafe; padding: 7px 14px; border-radius: 999px; }
    .radio-badge i { width: 14px; height: 14px; }
    .radio-head h1 { font-size: clamp(1.8rem, 5vw, 2.6rem); font-weight: 900; letter-spacing: -.03em; margin-top: 14px; }
    .radio-tagline { color: var(--slate-500); font-size: 15px; margin-top: 8px; }

    .radio-empty { text-align: center; color: var(--slate-400); padding: 60px 0; }
    .radio-empty i { width: 48px; height: 48px; }
    .radio-empty p { margin-top: 12px; font-weight: 600; }

    .radio-player { background: var(--white); border: 1px solid var(--slate-200); border-radius: 24px; padding: 28px; box-shadow: 0 20px 40px -20px rgba(15,23,42,.25); text-align: center; }
    .radio-cover { width: 200px; height: 200px; margin: 0 auto 20px; border-radius: 20px; background: linear-gradient(135deg, var(--primary), #0b3fa0); display: flex; align-items: center; justify-content: center; background-size: cover; background-position: center; overflow: hidden; }
    .radio-cover-fallback { width: 64px; height: 64px; color: rgba(255,255,255,.85); }
    .radio-cover.has-img .radio-cover-fallback { display: none; }

    .radio-now-title { font-size: 20px; font-weight: 800; }
    .radio-now-artist { color: var(--slate-500); font-size: 14px; margin-top: 4px; min-height: 18px; }

    .radio-progress { display: flex; align-items: center; gap: 10px; margin: 22px 0 6px; }
    .radio-time { font-size: 12px; font-weight: 700; color: var(--slate-400); min-width: 36px; }
    .radio-progress input[type=range] { flex: 1; accent-color: var(--primary); cursor: pointer; }

    .radio-controls { display: flex; align-items: center; justify-content: center; gap: 18px; margin-top: 14px; }
    .radio-btn { display: inline-flex; align-items: center; justify-content: center; width: 48px; height: 48px; border-radius: 50%; background: var(--slate-100); color: var(--slate-800); cursor: pointer; transition: .2s; }
    .radio-btn:hover { background: var(--slate-200); }
    .radio-btn i { width: 22px; height: 22px; }
    .radio-btn-main { width: 64px; height: 64px; background: var(--primary); color: #fff; }
    .radio-btn-main:hover { background: #0b3fa0; }
    .radio-btn-main i { width: 28px; height: 28px; }

    .radio-list { margin-top: 28px; display: flex; flex-direction: column; gap: 6px; }
    .radio-list-item { display: flex; align-items: center; gap: 14px; padding: 12px 16px; border-radius: 14px; cursor: pointer; transition: .15s; border: 1px solid transparent; }
    .radio-list-item:hover { background: var(--slate-50); }
    .radio-list-item.active { background: #dbeafe; border-color: #bfdbfe; }
    .radio-list-index { font-weight: 800; color: var(--slate-400); min-width: 20px; text-align: center; }
    .radio-list-meta { display: flex; flex-direction: column; flex: 1; text-align: start; }
    .radio-list-title { font-weight: 700; }
    .radio-list-artist { font-size: 13px; color: var(--slate-500); }
    .radio-list-icon { width: 18px; height: 18px; color: var(--slate-400); }
    .radio-list-item.active .radio-list-icon { color: var(--primary); }

    html[dir=rtl] .radio-time { direction: ltr; }
</style>

@if(!$playlist->isEmpty())
<script>
    (function () {
        var tracks = @json($playlist);
        var audio   = document.getElementById('radioAudio');
        var cover   = document.getElementById('radioCover');
        var elTitle = document.getElementById('radioTitle');
        var elArtist= document.getElementById('radioArtist');
        var playBtn = document.getElementById('radioPlay');
        var prevBtn = document.getElementById('radioPrev');
        var nextBtn = document.getElementById('radioNext');
        var seek    = document.getElementById('radioSeek');
        var elCur   = document.getElementById('radioCurrent');
        var elDur   = document.getElementById('radioDuration');
        var items   = Array.prototype.slice.call(document.querySelectorAll('.radio-list-item'));
        var current = -1;

        function fmt(s) {
            if (!s || isNaN(s)) return '0:00';
            var m = Math.floor(s / 60), sec = Math.floor(s % 60);
            return m + ':' + (sec < 10 ? '0' : '') + sec;
        }

        function setPlayIcon(playing) {
            playBtn.innerHTML = '<i data-lucide="' + (playing ? 'pause' : 'play') + '"></i>';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }

        function load(i, autoplay) {
            if (i < 0 || i >= tracks.length) return;
            current = i;
            var t = tracks[i];
            audio.src = t.src;
            elTitle.textContent = t.title;
            elArtist.textContent = t.artist || '';
            if (t.cover) {
                cover.style.backgroundImage = 'url("' + t.cover + '")';
                cover.classList.add('has-img');
            } else {
                cover.style.backgroundImage = '';
                cover.classList.remove('has-img');
            }
            items.forEach(function (el, idx) { el.classList.toggle('active', idx === i); });
            if (autoplay) audio.play().catch(function () {});
        }

        // Explicit user action required: nothing plays until a button/track is clicked.
        playBtn.addEventListener('click', function () {
            if (current === -1) { load(0, true); return; }
            if (audio.paused) audio.play().catch(function () {}); else audio.pause();
        });
        prevBtn.addEventListener('click', function () { load(current <= 0 ? tracks.length - 1 : current - 1, true); });
        nextBtn.addEventListener('click', function () { load(current >= tracks.length - 1 ? 0 : current + 1, true); });
        items.forEach(function (el) {
            el.addEventListener('click', function () { load(parseInt(el.dataset.index, 10), true); });
        });

        audio.addEventListener('play',  function () { setPlayIcon(true); });
        audio.addEventListener('pause', function () { setPlayIcon(false); });
        audio.addEventListener('ended', function () { nextBtn.click(); });
        audio.addEventListener('loadedmetadata', function () { elDur.textContent = fmt(audio.duration); seek.max = audio.duration || 100; });
        audio.addEventListener('timeupdate', function () {
            elCur.textContent = fmt(audio.currentTime);
            if (!seek.dragging) seek.value = audio.currentTime;
        });
        seek.addEventListener('input', function () { seek.dragging = true; });
        seek.addEventListener('change', function () { audio.currentTime = parseFloat(seek.value); seek.dragging = false; });

        // Preload first track's metadata/label without playing.
        load(0, false);
        setPlayIcon(false);
    })();
</script>
@endif
@endsection
