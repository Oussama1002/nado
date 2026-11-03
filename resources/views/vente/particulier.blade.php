@extends('layouts.app')

@section('title', __('messages.ventepages.pp_pill') . ' – NADO')

@section('link')
<script src="https://unpkg.com/lucide@latest"></script>
<style>
:root {
    --pp-blue   : #1459DD;
    --pp-orange : #1459DD;
    --pp-slate  : #0f172a;
    --pp-muted  : #64748b;
    --pp-border : #e2e8f0;
    --pp-white  : #ffffff;
    --pp-slate50: #f8fafc;
}
.pp-page { font-family: 'Inter', -apple-system, sans-serif; background: var(--pp-slate50); min-height: 100vh; padding-top: 40px; padding-bottom: 80px; color: var(--pp-slate); }
.pp-page *, .pp-page *::before, .pp-page *::after { box-sizing: border-box; }
.pp-wrap { max-width: 1280px; margin: 0 auto; padding: 0 24px; }

/* Breadcrumb */
.pp-breadcrumb { background: var(--pp-white); border-bottom: 1px solid var(--pp-border); padding: 12px 0; margin-bottom: 48px; }
.pp-breadcrumb nav { display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: var(--pp-muted); }
.pp-breadcrumb nav a { color: inherit; text-decoration: none; transition: color .2s; }
.pp-breadcrumb nav a:hover { color: var(--pp-blue); }
.pp-breadcrumb .bc-cur { color: var(--pp-slate); }

/* Header */
.pp-pill {
    display: inline-flex; align-items: center; gap: 8px;
    padding: 8px 20px; background: #dbeafe; color: var(--pp-orange);
    border-radius: 9999px; border: 1px solid #bfdbfe;
    font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .25em;
    margin-bottom: 20px;
}
.pp-h1 { font-size: clamp(2.2rem, 5vw, 3.5rem); font-weight: 900; text-transform: uppercase; letter-spacing: -.04em; line-height: .95; margin-bottom: 20px; }
.pp-h1 .accent { color: var(--pp-orange); }
.pp-subtitle { font-size: 1rem; color: var(--pp-muted); font-weight: 500; line-height: 1.65; max-width: 520px; margin-bottom: 32px; }

/* Layout grid */
.pp-grid { display: grid; grid-template-columns: 1fr; gap: 48px; }
@media (min-width: 1024px) { .pp-grid { grid-template-columns: 1fr 380px; } }

/* Main column */
.pp-card { background: var(--pp-white); border-radius: 1.5rem; border: 1px solid var(--pp-border); overflow: hidden; margin-bottom: 24px; }
.pp-card-body { padding: 32px; }
.pp-section-label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .3em; color: var(--pp-blue); margin-bottom: 16px; }
.pp-conditions { display: flex; flex-direction: column; gap: 14px; }
.pp-condition { display: flex; align-items: flex-start; gap: 14px; padding: 16px; background: var(--pp-slate50); border-radius: .75rem; }
.pp-cond-icon { width: 36px; height: 36px; border-radius: .5rem; background: var(--pp-blue); color: white; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.pp-cond-title { font-size: 14px; font-weight: 800; color: var(--pp-slate); margin-bottom: 4px; }
.pp-cond-desc  { font-size: 12px; color: var(--pp-muted); font-weight: 500; line-height: 1.5; }

/* Quote card */
.pp-quote-card { background: #0f172a; border-radius: 1.5rem; padding: 32px; color: white; margin-bottom: 24px; }
.pp-quote-text { font-size: 1.15rem; font-weight: 700; line-height: 1.6; margin-bottom: 20px; font-style: italic; color: rgba(255,255,255,.85); }
.pp-quote-author { display: flex; align-items: center; gap: 12px; }
.pp-quote-avatar { width: 44px; height: 44px; border-radius: 50%; background: var(--pp-blue); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; font-weight: 900; color: white; }
.pp-quote-name { font-size: 13px; font-weight: 800; color: white; }
.pp-quote-role { font-size: 11px; color: rgba(255,255,255,.45); font-weight: 500; }

/* Sidebar */
.pp-sidebar { }

/* Phone card */
.pp-phone-card { background: var(--pp-blue); border-radius: 1.5rem; padding: 32px; color: white; margin-bottom: 24px; }
.pp-phone-label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .25em; color: rgba(255,255,255,.6); margin-bottom: 16px; }
.pp-phone-number { font-size: 1.6rem; font-weight: 900; letter-spacing: -.02em; margin-bottom: 4px; }
.pp-phone-number a { color: white; text-decoration: none; transition: color .2s; }
.pp-phone-number a:hover { color: #FDD817; }
.pp-phone-hours { font-size: 12px; font-weight: 500; color: rgba(255,255,255,.6); margin-bottom: 24px; }
.pp-phone-btn { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 16px; background: rgba(255,255,255,.15); border: 1px solid rgba(255,255,255,.25); border-radius: .75rem; color: white; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; cursor: pointer; text-decoration: none; transition: background .2s; }
.pp-phone-btn:hover { background: rgba(255,255,255,.25); color: white; }

/* Map card */
.pp-map-card { background: var(--pp-white); border-radius: 1.5rem; border: 1px solid var(--pp-border); overflow: hidden; margin-bottom: 24px; }
.pp-map-card iframe { display: block; width: 100%; height: 200px; border: none; }
.pp-map-body { padding: 20px; }
.pp-map-address { font-size: 13px; font-weight: 600; color: var(--pp-slate); display: flex; align-items: flex-start; gap: 8px; }

/* Trust card */
.pp-trust-card { background: var(--pp-white); border-radius: 1.5rem; border: 1px solid var(--pp-border); padding: 28px; }
.pp-trust-title { font-size: 14px; font-weight: 900; text-transform: uppercase; letter-spacing: -.01em; margin-bottom: 16px; }
.pp-trust-list { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px; }
.pp-trust-item { display: flex; align-items: center; gap: 10px; font-size: 13px; font-weight: 600; color: #334155; }
.pp-trust-check { color: #22c55e; flex-shrink: 0; }

/* CTA */
.pp-cta { display: inline-flex; align-items: center; gap: 10px; padding: 18px 40px; background: var(--pp-blue); color: white; border: none; border-radius: 9999px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em; cursor: pointer; transition: background .2s; text-decoration: none; }
.pp-cta:hover { background: #0f46b1; color: white; }
</style>
@endsection

@section('content')
<div class="pp-page">

    {{-- Breadcrumb --}}
    <div class="pp-breadcrumb">
        <div class="pp-wrap">
            <nav>
                <a href="{{ route('index') }}">{{ __('messages.common.home') }}</a>
                <i data-lucide="chevron-right" style="width:10px;height:10px;"></i>
                <a href="{{ route('vente.form') }}">{{ __('messages.ventepages.sell_my_car') }}</a>
                <i data-lucide="chevron-right" style="width:10px;height:10px;"></i>
                <span class="bc-cur">{{ __('messages.ventepages.pp_bc') }}</span>
            </nav>
        </div>
    </div>

    <div class="pp-wrap">

        {{-- Header --}}
        <div style="margin-bottom:48px;">
            <a href="{{ route('vente.form') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--pp-muted);font-size:12px;font-weight:700;text-decoration:none;margin-bottom:24px;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> {{ __('messages.ventepages.back') }}
            </a>
            <div class="pp-pill">
                <i data-lucide="user" style="width:12px;height:12px;"></i>
                {{ __('messages.ventepages.pp_pill') }}
            </div>
            <h1 class="pp-h1">
                {{ __('messages.ventepages.pp_h1_1') }}<br>
                <span class="accent">{{ __('messages.ventepages.pp_h1_accent') }}</span> {{ __('messages.ventepages.pp_h1_mid') }}<br>{{ __('messages.ventepages.pp_h1_3') }}
            </h1>
            <p class="pp-subtitle">{{ __('messages.ventepages.pp_subtitle') }}</p>
            <a class="pp-cta" href="{{ route('vente.form', ['open_modal' => 1]) }}" style="text-decoration:none;">
                <i data-lucide="plus-circle" style="width:16px;height:16px;"></i>
                {{ __('messages.ventepages.pp_cta') }}
            </a>
        </div>

        <div class="pp-grid">

            {{-- Main column --}}
            <div>

                {{-- Conditions --}}
                <div class="pp-card">
                    <div class="pp-card-body">
                        <p class="pp-section-label">{{ __('messages.ventepages.pp_how') }}</p>
                        <div class="pp-conditions">
                            <div class="pp-condition">
                                <div class="pp-cond-icon"><i data-lucide="camera" style="width:16px;height:16px;"></i></div>
                                <div>
                                    <div class="pp-cond-title">{{ __('messages.ventepages.pp_s1_title') }}</div>
                                    <div class="pp-cond-desc">{{ __('messages.ventepages.pp_s1_desc') }}</div>
                                </div>
                            </div>
                            <div class="pp-condition">
                                <div class="pp-cond-icon"><i data-lucide="megaphone" style="width:16px;height:16px;"></i></div>
                                <div>
                                    <div class="pp-cond-title">{{ __('messages.ventepages.pp_s2_title') }}</div>
                                    <div class="pp-cond-desc">{{ __('messages.ventepages.pp_s2_desc') }}</div>
                                </div>
                            </div>
                            <div class="pp-condition">
                                <div class="pp-cond-icon"><i data-lucide="shield-check" style="width:16px;height:16px;"></i></div>
                                <div>
                                    <div class="pp-cond-title">{{ __('messages.ventepages.pp_s3_title') }}</div>
                                    <div class="pp-cond-desc">{{ __('messages.ventepages.pp_s3_desc') }}</div>
                                </div>
                            </div>
                            <div class="pp-condition">
                                <div class="pp-cond-icon" style="background:#22c55e;"><i data-lucide="check-circle" style="width:16px;height:16px;"></i></div>
                                <div>
                                    <div class="pp-cond-title">{{ __('messages.ventepages.pp_s4_title') }}</div>
                                    <div class="pp-cond-desc">{{ __('messages.ventepages.pp_s4_desc') }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Quote card --}}
                <div class="pp-quote-card">
                    <div class="pp-quote-text">
                        {{ __('messages.ventepages.pp_quote') }}
                    </div>
                    <div class="pp-quote-author">
                        <div class="pp-quote-avatar">AK</div>
                        <div>
                            <div class="pp-quote-name">Adil K.</div>
                            <div class="pp-quote-role">{{ __('messages.ventepages.pp_quote_role') }}</div>
                        </div>
                    </div>
                </div>

            </div>

            {{-- Sidebar --}}
            <div class="pp-sidebar">

                {{-- Phone --}}
                <div class="pp-phone-card">
                    <p class="pp-phone-label">{{ __('messages.ventepages.pp_phone_label') }}</p>
                    <div class="pp-phone-number"><a href="tel:+212520070611">+212 520 070 611</a></div>
                    <div class="pp-phone-hours">{{ __('messages.ventepages.pp_phone_hours') }}</div>
                    <a href="https://wa.me/212520070611" target="_blank" class="pp-phone-btn">
                        <i data-lucide="message-circle" style="width:16px;height:16px;"></i>
                        {{ __('messages.ventepages.pp_whatsapp') }}
                    </a>
                </div>

                {{-- Map --}}
                <div class="pp-map-card">
                    <iframe
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3323.9!2d-7.61!3d33.59!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMzPCsDM1JzI0LjAiTiA3wrAzNic0NS42Ilc!5e0!3m2!1sfr!2sma!4v1700000000000"
                        allowfullscreen="" loading="lazy"
                        referrerpolicy="no-referrer-when-downgrade"></iframe>
                    <div class="pp-map-body">
                        <div class="pp-map-address">
                            <i data-lucide="map-pin" style="width:16px;height:16px;color:#1459DD;flex-shrink:0;margin-top:2px;"></i>
                            <span><a href="https://maps.app.goo.gl/wrR8THmnk8ix3MVy9" target="_blank" rel="noopener" style="color:inherit;text-decoration:none;">{{ __('messages.ventepages.pp_map_addr') }}</a><br><span style="color:var(--pp-muted);font-size:12px;">{{ __('messages.ventepages.pp_map_hours') }}</span></span>
                        </div>
                    </div>
                </div>

                {{-- Trust --}}
                <div class="pp-trust-card">
                    <div class="pp-trust-title">{{ __('messages.ventepages.pp_trust_title') }}</div>
                    <ul class="pp-trust-list">
                        <li class="pp-trust-item">
                            <i data-lucide="check-circle" class="pp-trust-check" style="width:16px;height:16px;"></i>
                            {{ __('messages.ventepages.pp_trust1') }}
                        </li>
                        <li class="pp-trust-item">
                            <i data-lucide="check-circle" class="pp-trust-check" style="width:16px;height:16px;"></i>
                            {{ __('messages.ventepages.pp_trust2') }}
                        </li>
                        <li class="pp-trust-item">
                            <i data-lucide="check-circle" class="pp-trust-check" style="width:16px;height:16px;"></i>
                            {{ __('messages.ventepages.pp_trust3') }}
                        </li>
                        <li class="pp-trust-item">
                            <i data-lucide="check-circle" class="pp-trust-check" style="width:16px;height:16px;"></i>
                            {{ __('messages.ventepages.pp_trust4') }}
                        </li>
                        <li class="pp-trust-item">
                            <i data-lucide="check-circle" class="pp-trust-check" style="width:16px;height:16px;"></i>
                            {{ __('messages.ventepages.pp_trust5') }}
                        </li>
                    </ul>
                </div>

            </div>
        </div>
    </div>

</div>

@endsection

@section('script')
<script>
document.addEventListener('DOMContentLoaded', function () {
    if (typeof lucide !== 'undefined') lucide.createIcons();
});
</script>
@endsection
