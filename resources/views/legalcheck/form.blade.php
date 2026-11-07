@extends('layouts.app')

@section('title', __('messages.legalcheck.page_title'))

@php $isAr = app()->getLocale() === 'ar'; @endphp

@section('styles')
<style>
    .lc-wrap { max-width: 760px; margin: 2.5rem auto; padding: 0 16px; }
    .lc-card {
        background: #fff; border: 1px solid #e5e7eb; border-radius: 16px;
        padding: 1.75rem; box-shadow: 0 10px 30px rgba(15,23,42,.06); margin-bottom: 1.25rem;
    }
    .lc-head h1 { font-size: 1.6rem; font-weight: 900; color: #0f172a; margin: 0 0 .25rem; }
    .lc-head .lc-sub { color: #1459DD; font-weight: 700; font-size: .95rem; }
    .lc-intro { color: #475569; line-height: 1.6; margin: .9rem 0 0; }
    .lc-attached {
        display: inline-flex; align-items: center; gap: .4rem; margin-top: .75rem;
        background: #eff6ff; color: #1d4ed8; border: 1px solid #dbeafe;
        border-radius: 999px; padding: .3rem .8rem; font-weight: 700; font-size: .85rem;
    }
    .lc-legend { font-weight: 800; color: #0f172a; margin: 0 0 .75rem; font-size: 1.05rem; }
    .lc-matricule { display: flex; gap: .6rem; flex-wrap: wrap; }
    .lc-field { flex: 1 1 120px; min-width: 100px; }
    .lc-field label { display: block; font-size: .8rem; font-weight: 700; color: #64748b; margin-bottom: .3rem; }
    .lc-field input,
    .lc-field select {
        width: 100%; border: 1.5px solid #cbd5e1; border-radius: 10px;
        padding: .7rem .8rem; font-size: 1.1rem; font-weight: 700; text-align: center;
        background: #fff; height: calc(1.1rem + 1.4rem + 3px);
    }
    .lc-field input:focus,
    .lc-field select:focus { outline: none; border-color: #1459DD; }
    .lc-hint { color: #94a3b8; font-size: .82rem; margin-top: .5rem; }
    .lc-preview-box {
        margin-top: 1rem; background: #0f172a; color: #fff; border-radius: 12px;
        padding: .9rem 1rem; display: flex; align-items: center; justify-content: space-between; gap: 1rem;
    }
    .lc-preview-box .lbl { font-size: .78rem; opacity: .7; text-transform: uppercase; letter-spacing: .08em; }
    .lc-preview-val { font-size: 1.5rem; font-weight: 900; letter-spacing: .04em; direction: ltr; unicode-bidi: isolate; flex: 1; }
    .lc-copy-btn {
        display: inline-flex; align-items: center; gap: .4rem; background: rgba(255,255,255,.12);
        color: #fff; border: 1px solid rgba(255,255,255,.25); border-radius: 8px;
        padding: .45rem .75rem; font-weight: 700; font-size: .85rem; cursor: pointer; white-space: nowrap;
    }
    .lc-copy-btn:hover { background: rgba(255,255,255,.22); }
    .lc-copy-btn.copied { background: #16a34a; border-color: #16a34a; }
    .lc-toast {
        position: fixed; left: 50%; bottom: 28px; transform: translateX(-50%) translateY(20px);
        background: #0f172a; color: #fff; padding: .8rem 1.3rem; border-radius: 999px; font-weight: 700;
        box-shadow: 0 10px 30px rgba(0,0,0,.25); opacity: 0; pointer-events: none;
        transition: opacity .2s, transform .2s; z-index: 9999;
    }
    .lc-toast.show { opacity: 1; transform: translateX(-50%) translateY(0); }
    .lc-steps { counter-reset: step; list-style: none; padding: 0; margin: 1rem 0 0; }
    .lc-steps li {
        counter-increment: step; position: relative; padding-{{ $isAr ? 'right' : 'left' }}: 2.4rem;
        margin-bottom: .9rem; color: #334155; line-height: 1.5;
    }
    .lc-steps li::before {
        content: counter(step); position: absolute; {{ $isAr ? 'right' : 'left' }}: 0; top: -2px;
        width: 1.7rem; height: 1.7rem; background: #1459DD; color: #fff; border-radius: 50%;
        display: flex; align-items: center; justify-content: center; font-weight: 900; font-size: .9rem;
    }
    .lc-open-btn {
        display: inline-flex; align-items: center; gap: .5rem; margin-top: .5rem;
        background: #1459DD; color: #fff; font-weight: 800; text-decoration: none;
        padding: .85rem 1.4rem; border-radius: 10px; font-size: 1rem;
    }
    .lc-open-btn:hover { background: #0e47b0; color: #fff; }
    .lc-embed { margin-top: 1rem; border: 1px solid #e2e8f0; border-radius: 12px; overflow: hidden; }
    .lc-embed-bar {
        display: flex; align-items: center; justify-content: space-between; gap: .6rem;
        background: #0f172a; color: #fff; padding: .5rem .9rem; font-weight: 700; font-size: .85rem;
    }
    .lc-embed-open {
        display: inline-flex; align-items: center; gap: .4rem; color: #fff; text-decoration: none;
        background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.25);
        border-radius: 8px; padding: .35rem .7rem; white-space: nowrap;
    }
    .lc-embed-open:hover { background: rgba(255,255,255,.22); color: #fff; }
    .lc-embed iframe { width: 100%; height: 820px; border: 0; display: block; background: #fff; }
    .lc-embed-fallback { padding: .6rem .9rem 0; }
    .lc-results { display: flex; gap: .75rem; flex-wrap: wrap; }
    .lc-radio {
        flex: 1 1 200px; border: 2px solid #e2e8f0; border-radius: 12px; padding: 1rem;
        cursor: pointer; display: flex; align-items: center; gap: .6rem; font-weight: 700; color: #334155;
    }
    .lc-radio input { accent-color: #1459DD; width: 1.1rem; height: 1.1rem; }
    .lc-radio.clear:has(input:checked) { border-color: #16a34a; background: #f0fdf4; color: #15803d; }
    .lc-radio.opp:has(input:checked) { border-color: #d97706; background: #fffbeb; color: #b45309; }
    .lc-file label.filebtn {
        display: inline-flex; align-items: center; gap: .5rem; cursor: pointer;
        border: 1.5px dashed #94a3b8; border-radius: 10px; padding: .8rem 1rem; color: #475569; font-weight: 700;
    }
    .lc-file input[type=file] { position: absolute; width: 1px; height: 1px; overflow: hidden; clip: rect(0 0 0 0); }
    .lc-file .fname { color: #1459DD; font-weight: 700; margin-{{ $isAr ? 'right' : 'left' }}: .6rem; }
    .lc-submit {
        width: 100%; background: #16a34a; color: #fff; border: 0; border-radius: 12px;
        padding: 1rem; font-size: 1.05rem; font-weight: 900; cursor: pointer; margin-top: 1.25rem;
    }
    .lc-submit:hover { background: #15803d; }
    .lc-disclaimer { color: #64748b; font-size: .82rem; line-height: 1.5; margin-top: 1rem; display: flex; gap: .5rem; }
    .lc-disclaimer i { color: #94a3b8; margin-top: .15rem; }
    .lc-alert { border-radius: 12px; padding: .9rem 1.1rem; margin-bottom: 1.25rem; font-weight: 700; }
    .lc-alert.ok { background: #f0fdf4; color: #15803d; border: 1px solid #bbf7d0; }
    .lc-alert.err { background: #fef2f2; color: #b91c1c; border: 1px solid #fecaca; }
    .lc-status { background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 10px; padding: .8rem 1rem; color: #475569; margin-bottom: 1rem; }
</style>
@endsection

@section('content')
<div class="lc-wrap">

    <div class="lc-card lc-head">
        <h1>{{ __('messages.legalcheck.heading') }}</h1>
        <div class="lc-sub">{{ __('messages.legalcheck.subheading') }}</div>
        <p class="lc-intro">{{ __('messages.legalcheck.intro') }}</p>
        @if($annonce)
            <span class="lc-attached"><i class="fa-solid fa-link"></i>
                {{ __('messages.legalcheck.attached_to') }} : {{ $annonce->titre ?? ('#' . $annonce->id) }}</span>
        @endif
    </div>

    @if(session('success'))
        <div class="lc-alert ok">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="lc-alert err">{{ $errors->first() }}</div>
    @endif

    @if($existing)
        <div class="lc-status">
            @switch($existing->admin_status)
                @case('confirmed') ✅ {{ __('messages.legalcheck.status_confirmed') }} @break
                @case('rejected')  ⚠️ {{ __('messages.legalcheck.status_rejected') }} @break
                @default           ⏳ {{ __('messages.legalcheck.status_pending') }}
            @endswitch
            —
            {{ $existing->declared_status === 'clear'
                ? __('messages.legalcheck.declared_clear')
                : __('messages.legalcheck.declared_opposition') }}
        </div>
    @endif

    <form action="{{ route('legalcheck.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        @if($annonce)
            <input type="hidden" name="annonce_id" value="{{ $annonce->id }}">
        @endif

        {{-- 1. Matricule --}}
        <div class="lc-card">
            <p class="lc-legend">{{ __('messages.legalcheck.matricule_legend') }}</p>
            @php
                // Lettres de série des plaques marocaines (valeur = lettre arabe, label = équivalent latin).
                $plateLetters = [
                    'أ' => 'A', 'ب' => 'B', 'ج' => 'J', 'د' => 'D', 'ه' => 'H',
                    'و' => 'W', 'ط' => 'T', 'ق' => 'Q', 'ش' => 'CH', 'WW' => 'WW',
                ];
            @endphp
            <div class="lc-matricule">
                <div class="lc-field">
                    <label for="lc_number">{{ __('messages.legalcheck.matricule_number') }}</label>
                    <input type="tel" inputmode="numeric" id="lc_number" name="matricule_number"
                           value="{{ old('matricule_number') }}" placeholder="00000" autocomplete="off">
                </div>
                <div class="lc-field">
                    <label for="lc_letter">{{ __('messages.legalcheck.matricule_letter') }}</label>
                    <select id="lc_letter" name="matricule_letter">
                        <option value="" disabled {{ old('matricule_letter') ? '' : 'selected' }}>—</option>
                        @foreach($plateLetters as $ar => $latin)
                            <option value="{{ $ar }}" {{ old('matricule_letter') === $ar ? 'selected' : '' }}>
                                {{ $ar }} ({{ $latin }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="lc-field">
                    <label for="lc_region">{{ __('messages.legalcheck.matricule_region') }}</label>
                    <input type="tel" inputmode="numeric" id="lc_region" name="matricule_region"
                           value="{{ old('matricule_region') }}" placeholder="00" autocomplete="off">
                </div>
            </div>
            <div class="lc-hint">{{ __('messages.legalcheck.matricule_hint') }}</div>

            <div class="lc-preview-box">
                <span class="lbl">{{ __('messages.legalcheck.matricule_legend') }}</span>
                <span class="lc-preview-val" id="lc_preview">—</span>
                <button type="button" id="lc_copy" class="lc-copy-btn">
                    <i class="fa-solid fa-copy"></i> <span>{{ __('messages.legalcheck.copy') }}</span>
                </button>
            </div>

            {{-- Service NARSA intégré, affiché par défaut dans la section matricule
                 (iframe cross-origin : le vendeur agit dedans, on ne lit rien à
                 travers). Ne s'affiche que depuis une IP marocaine, d'où le repli. --}}
            <div class="lc-embed" id="lc_embed">
                <div class="lc-embed-bar">
                    <span><i class="fa-solid fa-shield-halved"></i> opposition.narsa.gov.ma</span>
                    <a href="{{ $narsaUrl }}" id="lc_open" target="_blank" rel="noopener noreferrer" class="lc-embed-open">
                        <i class="fa-solid fa-arrow-up-right-from-square"></i>
                        {{ __('messages.legalcheck.open_narsa') }}
                    </a>
                </div>
                <iframe id="lc_iframe"
                        src="https://opposition.narsa.gov.ma:82/consultation/opposition"
                        title="NARSA" loading="lazy"
                        referrerpolicy="no-referrer"></iframe>
                <p class="lc-hint lc-embed-fallback">
                    <i class="fa-solid fa-triangle-exclamation"></i> {{ __('messages.legalcheck.embed_fallback') }}
                </p>
            </div>
        </div>

        {{-- 2. Guided steps --}}
        <div class="lc-card">
            <p class="lc-legend">{{ __('messages.legalcheck.steps_title') }}</p>
            <ol class="lc-steps">
                <li>{{ __('messages.legalcheck.step_open') }}</li>
                <li>{{ __('messages.legalcheck.step_retype') }}</li>
                <li>{{ __('messages.legalcheck.step_captcha') }}</li>
                <li>{{ __('messages.legalcheck.step_return') }}</li>
            </ol>
            <p class="lc-hint" style="margin-top:.2rem;">
                <i class="fa-solid fa-circle-info"></i> {{ __('messages.legalcheck.open_narsa_note') }}
            </p>
        </div>

        {{-- 3. Result + screenshot --}}
        <div class="lc-card">
            <p class="lc-legend">{{ __('messages.legalcheck.result_legend') }}</p>
            <div class="lc-results">
                <label class="lc-radio clear">
                    <input type="radio" name="declared_status" value="clear"
                        {{ old('declared_status') === 'clear' ? 'checked' : '' }}>
                    ✅ {{ __('messages.legalcheck.result_clear') }}
                </label>
                <label class="lc-radio opp">
                    <input type="radio" name="declared_status" value="opposition"
                        {{ old('declared_status') === 'opposition' ? 'checked' : '' }}>
                    ⚠️ {{ __('messages.legalcheck.result_opposition') }}
                </label>
            </div>

            <div class="lc-file" style="margin-top:1.25rem;">
                <label class="filebtn" for="lc_screenshot">
                    <i class="fa-solid fa-image"></i> {{ __('messages.legalcheck.screenshot_label') }}
                </label>
                <input type="file" id="lc_screenshot" name="screenshot" accept="image/*">
                <span class="fname" id="lc_fname"></span>
                <div class="lc-hint">{{ __('messages.legalcheck.screenshot_hint') }}</div>
            </div>

            <button type="submit" class="lc-submit">{{ __('messages.legalcheck.submit') }}</button>

            <p class="lc-disclaimer">
                <i class="fa-solid fa-circle-info"></i>
                <span>{{ __('messages.legalcheck.disclaimer') }}</span>
            </p>
        </div>
    </form>
</div>
<div class="lc-toast" id="lc_toast"></div>
@endsection

@section('script')
<script>
    (function () {
        var isAr = @json(app()->getLocale() === 'ar');
        var T = {
            copied:   isAr ? 'تم نسخ رقم التسجيل — الصقه في خانة NARSA' : 'Matricule copié — collez-le dans le champ NARSA',
            copyBtn:  isAr ? 'نسخ' : 'Copier',
            copiedBtn:isAr ? 'تم النسخ' : 'Copié',
            nothing:  isAr ? 'أدخل رقم التسجيل أولاً' : 'Saisissez d’abord le matricule',
        };

        var num = document.getElementById('lc_number');
        var let = document.getElementById('lc_letter');
        var reg = document.getElementById('lc_region');
        var preview = document.getElementById('lc_preview');
        var file = document.getElementById('lc_screenshot');
        var fname = document.getElementById('lc_fname');
        var copyBtn = document.getElementById('lc_copy');
        var openBtn = document.getElementById('lc_open');
        var toast = document.getElementById('lc_toast');
        var toastTimer;

        function matricule() {
            return [num.value.trim(), let.value.trim(), reg.value.trim()].filter(Boolean).join(' ');
        }
        function refresh() {
            var m = matricule();
            preview.textContent = m ? m : '—';
        }
        [num, let, reg].forEach(function (el) { el && el.addEventListener('input', refresh); });
        refresh();

        file && file.addEventListener('change', function () {
            fname.textContent = this.files && this.files[0] ? this.files[0].name : '';
        });

        function showToast(msg) {
            if (!toast) return;
            toast.textContent = msg;
            toast.classList.add('show');
            clearTimeout(toastTimer);
            toastTimer = setTimeout(function () { toast.classList.remove('show'); }, 2600);
        }

        function copyMatricule() {
            var m = matricule();
            if (!m) { showToast(T.nothing); return false; }

            var done = function () {
                showToast(T.copied);
                if (copyBtn) {
                    copyBtn.classList.add('copied');
                    var span = copyBtn.querySelector('span');
                    if (span) span.textContent = T.copiedBtn;
                    setTimeout(function () {
                        copyBtn.classList.remove('copied');
                        if (span) span.textContent = T.copyBtn;
                    }, 2000);
                }
            };

            if (navigator.clipboard && navigator.clipboard.writeText) {
                navigator.clipboard.writeText(m).then(done).catch(fallbackCopy);
            } else {
                fallbackCopy();
            }
            function fallbackCopy() {
                try {
                    var ta = document.createElement('textarea');
                    ta.value = m; ta.style.position = 'fixed'; ta.style.opacity = '0';
                    document.body.appendChild(ta); ta.focus(); ta.select();
                    document.execCommand('copy');
                    document.body.removeChild(ta);
                    done();
                } catch (e) { /* clipboard unavailable — seller types manually */ }
            }
            return true;
        }

        copyBtn && copyBtn.addEventListener('click', copyMatricule);
        // Copier au moment d'ouvrir NARSA (nouvel onglet) : plus qu'à coller.
        openBtn && openBtn.addEventListener('click', function () { copyMatricule(); });
    })();
</script>
@endsection
