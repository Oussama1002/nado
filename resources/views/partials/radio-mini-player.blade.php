@php
    /*
     | Site-wide floating mini-player for Nado Radio — rendered on every public
     | page via the InjectRadioMiniPlayer middleware (except the full radio page,
     | which has its own player). Muted + paused by default: nothing plays until
     | the user clicks play.
     */
    $miniOn = ! request()->routeIs('nado-radio.*')
        && feature_on('radio.enabled')
        && (bool) settings('radio.mini_player', false);

    $miniTracks = $miniOn
        ? \App\Models\Song::activeOrdered()->get()->map(fn ($s) => [
            'title'  => $s->title,
            'artist' => $s->artist ?? '',
            'src'    => asset('storage/' . $s->file_path),
            'cover'  => $s->cover_path ? asset('storage/' . $s->cover_path) : null,
          ])->values()
        : collect();
@endphp

@if($miniOn && $miniTracks->isNotEmpty())
<div id="nadoMini" class="nado-mini" dir="ltr">
    <button type="button" id="nadoMiniToggle" class="nado-mini-fab" aria-label="Nado Radio">
        {{-- Radio classique en SVG inline (fiable même sans Font Awesome/Lucide sur la page) --}}
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M7 8 18 3"/><rect x="3" y="8" width="18" height="12" rx="2"/><circle cx="16" cy="14" r="2.5"/><path d="M7 12h5M7 16h4"/></svg>
    </button>

    <div class="nado-mini-panel" id="nadoMiniPanel">
        <div class="nado-mini-cover" id="nadoMiniCover"><i data-lucide="music"></i></div>
        <div class="nado-mini-meta">
            <div class="nado-mini-title" id="nadoMiniTitle">Nado Radio</div>
            <div class="nado-mini-artist" id="nadoMiniArtist"></div>
        </div>
        <button type="button" id="nadoMiniPrev" class="nado-mini-ctrl" aria-label="Précédent"><i data-lucide="skip-back"></i></button>
        <button type="button" id="nadoMiniPlay" class="nado-mini-ctrl nado-mini-play" aria-label="Lecture"><i data-lucide="play"></i></button>
        <button type="button" id="nadoMiniNext" class="nado-mini-ctrl" aria-label="Suivant"><i data-lucide="skip-forward"></i></button>
        <button type="button" id="nadoMiniClose" class="nado-mini-ctrl" aria-label="Fermer"><i data-lucide="x"></i></button>
        <audio id="nadoMiniAudio" preload="none"></audio>
    </div>
</div>

<style>
    /* Bottom-LEFT so it never collides with the bottom-right chat widget; very high z-index. */
    .nado-mini { position: fixed; bottom: 20px; left: 20px; right: auto; z-index: 2147483000; font-family: 'Inter', sans-serif; }
    .nado-mini-fab { width: 56px; height: 56px; border-radius: 50%; background: #1459DD; color: #fff; display: flex; align-items: center; justify-content: center; box-shadow: 0 10px 25px -8px rgba(20,89,221,.7); cursor: pointer; transition: transform .2s; }
    .nado-mini-fab:hover { transform: scale(1.06); }
    .nado-mini-fab i { width: 24px; height: 24px; }
    .nado-mini.open .nado-mini-fab { display: none; }
    /* Visibility is driven by the .open class, NOT the [hidden] attr (which an
       author display rule would override). */
    .nado-mini-panel { display: none; align-items: center; gap: 10px; background: #fff; border: 1px solid #e2e8f0; border-radius: 16px; padding: 10px 12px; box-shadow: 0 16px 40px -16px rgba(15,23,42,.4); max-width: min(92vw, 380px); }
    .nado-mini.open .nado-mini-panel { display: flex; }
    .nado-mini-cover { width: 44px; height: 44px; flex: 0 0 44px; border-radius: 10px; background: linear-gradient(135deg,#1459DD,#0b3fa0); background-size: cover; background-position: center; display: flex; align-items: center; justify-content: center; color: rgba(255,255,255,.85); }
    .nado-mini-cover i { width: 20px; height: 20px; }
    .nado-mini-cover.has-img i { display: none; }
    .nado-mini-meta { min-width: 0; flex: 1; }
    .nado-mini-title { font-size: 13px; font-weight: 800; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .nado-mini-artist { font-size: 11px; color: #64748b; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
    .nado-mini-ctrl { width: 34px; height: 34px; flex: 0 0 34px; border-radius: 50%; background: #f1f5f9; color: #1e293b; display: flex; align-items: center; justify-content: center; cursor: pointer; transition: background .2s; }
    .nado-mini-ctrl:hover { background: #e2e8f0; }
    .nado-mini-ctrl i { width: 16px; height: 16px; }
    .nado-mini-play { background: #1459DD; color: #fff; }
    .nado-mini-play:hover { background: #0b3fa0; }
    html[dir=rtl] .nado-mini { right: auto; left: 20px; }
</style>

<script>
    (function () {
        var KEY = 'nadoRadioMini';
        var tracks = @json($miniTracks);
        if (!tracks.length) return;

        var wrap    = document.getElementById('nadoMini');
        var fab     = document.getElementById('nadoMiniToggle');
        var panel   = document.getElementById('nadoMiniPanel');
        var closeB  = document.getElementById('nadoMiniClose');
        var audio   = document.getElementById('nadoMiniAudio');
        var cover   = document.getElementById('nadoMiniCover');
        var elTitle = document.getElementById('nadoMiniTitle');
        var elArt   = document.getElementById('nadoMiniArtist');
        var playB   = document.getElementById('nadoMiniPlay');
        var prevB   = document.getElementById('nadoMiniPrev');
        var nextB   = document.getElementById('nadoMiniNext');
        var current = 0;

        function readState() {
            try { return JSON.parse(localStorage.getItem(KEY)) || {}; } catch (e) { return {}; }
        }
        function saveState(extra) {
            try {
                var s = readState();
                s.index = current;
                s.time  = audio.currentTime || 0;
                s.open  = wrap.classList.contains('open');
                if (extra) Object.assign(s, extra);
                localStorage.setItem(KEY, JSON.stringify(s));
            } catch (e) {}
        }

        function icon(el, name) { el.innerHTML = '<i data-lucide="' + name + '"></i>'; if (typeof lucide !== 'undefined') lucide.createIcons(); }

        function load(i, resumeTime) {
            if (i < 0) i = tracks.length - 1;
            if (i >= tracks.length) i = 0;
            current = i;
            var t = tracks[i];
            audio.src = t.src;
            elTitle.textContent = t.title;
            elArt.textContent = t.artist || '';
            if (t.cover) { cover.style.backgroundImage = 'url("' + t.cover + '")'; cover.classList.add('has-img'); }
            else { cover.style.backgroundImage = ''; cover.classList.remove('has-img'); }
            if (resumeTime) {
                audio.addEventListener('loadedmetadata', function once () {
                    try { audio.currentTime = resumeTime; } catch (e) {}
                    audio.removeEventListener('loadedmetadata', once);
                });
            }
        }

        function openPanel()  { wrap.classList.add('open'); saveState(); }
        function closePanel() { wrap.classList.remove('open'); audio.pause(); saveState(); }

        fab.addEventListener('click', openPanel);
        closeB.addEventListener('click', closePanel);

        // Explicit click required to start audio — never autoplay.
        playB.addEventListener('click', function () {
            if (audio.paused) audio.play().catch(function () {}); else audio.pause();
        });
        prevB.addEventListener('click', function () { var was = !audio.paused; load(current - 1); if (was) audio.play().catch(function () {}); });
        nextB.addEventListener('click', function () { var was = !audio.paused; load(current + 1); if (was) audio.play().catch(function () {}); });

        audio.addEventListener('play',  function () { icon(playB, 'pause'); saveState({ playing: true }); });
        audio.addEventListener('pause', function () { icon(playB, 'play'); saveState({ playing: false }); });
        audio.addEventListener('ended', function () { nextB.click(); });
        audio.addEventListener('timeupdate', function () { saveState(); });

        // Restore last UI state (track + position + open), but stay PAUSED:
        // audio only resumes when the user clicks play again (no autoplay).
        var st = readState();
        load(st.index || 0, st.time || 0);
        icon(playB, 'play');
        if (st.open) openPanel();

        // Rend les icônes (radio, contrôles) même si la page n'a pas encore appelé lucide.
        function drawIcons() { if (typeof lucide !== 'undefined') lucide.createIcons(); }
        drawIcons();
        document.addEventListener('DOMContentLoaded', drawIcons);
    })();
</script>
@endif
