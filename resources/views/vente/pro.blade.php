@extends('layouts.app')

@section('title', __('messages.ventepages.sp_bc') . ' – NADO')

@section('link')
<script src="https://unpkg.com/lucide@latest"></script>
<style>
:root {
    --sp-blue   : #1459DD;
    --sp-slate  : #0f172a;
    --sp-muted  : #64748b;
    --sp-border : #e2e8f0;
    --sp-white  : #ffffff;
    --sp-slate50: #f8fafc;
}
.sp-page { font-family: 'Inter', -apple-system, sans-serif; background: var(--sp-slate50); min-height: 100vh; padding-top: 40px; padding-bottom: 80px; color: var(--sp-slate); }
.sp-page *, .sp-page *::before, .sp-page *::after { box-sizing: border-box; }
.sp-wrap { max-width: 1280px; margin: 0 auto; padding: 0 24px; }

/* Breadcrumb */
.sp-breadcrumb { background: var(--sp-white); border-bottom: 1px solid var(--sp-border); padding: 12px 0; margin-bottom: 48px; }
.sp-breadcrumb nav { display: flex; align-items: center; gap: 8px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: var(--sp-muted); }
.sp-breadcrumb nav a { color: inherit; text-decoration: none; transition: color .2s; }
.sp-breadcrumb nav a:hover { color: var(--sp-blue); }
.sp-breadcrumb .bc-cur { color: var(--sp-slate); }

/* Pill */
.sp-pill { display: inline-flex; align-items: center; gap: 8px; padding: 8px 20px; background: #dbeafe; color: var(--sp-blue); border-radius: 9999px; font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em; margin-bottom: 20px; }
.sp-h1 { font-size: clamp(2rem, 5vw, 3.2rem); font-weight: 900; text-transform: uppercase; letter-spacing: -.04em; line-height: .95; margin-bottom: 20px; }
.sp-h1 .accent { color: var(--sp-blue); }
.sp-subtitle { font-size: 1rem; color: var(--sp-muted); font-weight: 500; line-height: 1.65; max-width: 520px; margin-bottom: 32px; }

/* Grid */
.sp-grid { display: grid; grid-template-columns: 1fr; gap: 48px; }
@media (min-width: 1024px) { .sp-grid { grid-template-columns: 1fr 1fr; } }

/* Form */
.sp-form-card { background: var(--sp-white); border-radius: 1.5rem; border: 1px solid var(--sp-border); padding: 40px; }
.sp-form-title { font-size: 16px; font-weight: 900; text-transform: uppercase; letter-spacing: -.01em; margin-bottom: 8px; }
.sp-form-sub   { font-size: 13px; color: var(--sp-muted); font-weight: 500; margin-bottom: 28px; }
.sp-form-grid  { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px; }
.sp-form-group { display: flex; flex-direction: column; gap: 6px; }
.sp-form-group.full { grid-column: 1 / -1; }
.sp-form-group label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: var(--sp-muted); margin-left: 4px; }
.sp-input { background: var(--sp-slate50); border: 2px solid transparent; border-radius: .75rem; padding: 14px 16px; font-size: 14px; font-weight: 600; color: var(--sp-slate); width: 100%; outline: none; font-family: 'Inter', sans-serif; transition: border-color .2s, background .2s; }
.sp-input:focus { border-color: var(--sp-blue); background: white; }
.sp-submit { display: flex; align-items: center; justify-content: center; gap: 10px; width: 100%; padding: 18px; background: var(--sp-blue); color: white; border: none; border-radius: .75rem; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .15em; cursor: pointer; margin-top: 24px; transition: background .2s; }
.sp-submit:hover { background: #0f46b1; }

/* Right column */
.sp-right { display: flex; flex-direction: column; gap: 24px; }
.sp-cond-card { background: var(--sp-white); border-radius: 1.5rem; border: 1px solid var(--sp-border); padding: 32px; }
.sp-cond-title { font-size: 13px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; color: var(--sp-blue); margin-bottom: 16px; }
.sp-cond-list  { list-style: none; padding: 0; display: flex; flex-direction: column; gap: 12px; }
.sp-cond-item  { display: flex; align-items: flex-start; gap: 10px; font-size: 13px; font-weight: 600; color: #334155; }
.sp-check { color: #22c55e; flex-shrink: 0; margin-top: 1px; }

/* Accompagnement card */
.sp-accomp { background: #eff6ff; border: 1px solid #bfdbfe; border-radius: 1.5rem; padding: 32px; }
.sp-accomp-label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .25em; color: var(--sp-blue); margin-bottom: 10px; }
.sp-accomp-title { font-size: 1.2rem; font-weight: 900; color: var(--sp-slate); margin-bottom: 12px; }
.sp-accomp-desc  { font-size: 13px; color: var(--sp-muted); font-weight: 500; line-height: 1.6; }

/* Support card */
.sp-support { background: var(--sp-slate); border-radius: 1.5rem; padding: 32px; color: white; }
.sp-support-label { font-size: 10px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em; color: rgba(255,255,255,.5); margin-bottom: 10px; }
.sp-support-title { font-size: 1.15rem; font-weight: 900; margin-bottom: 10px; }
.sp-support-desc  { font-size: 13px; color: rgba(255,255,255,.6); font-weight: 500; line-height: 1.6; margin-bottom: 20px; }
.sp-support-btn { display: inline-flex; align-items: center; gap: 8px; padding: 12px 24px; background: rgba(255,255,255,.12); border: 1px solid rgba(255,255,255,.2); border-radius: .75rem; color: white; font-size: 12px; font-weight: 900; text-transform: uppercase; letter-spacing: .1em; text-decoration: none; transition: background .2s; cursor: pointer; }
.sp-support-btn:hover { background: rgba(255,255,255,.22); color: white; }

/* Success screen */
.sp-success { display: none; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 80px 24px; }
.sp-success.show { display: flex; }
.sp-success-icon { width: 96px; height: 96px; background: #dcfce7; border-radius: 50%; display: flex; align-items: center; justify-content: center; margin-bottom: 28px; }
.sp-success-title { font-size: clamp(1.8rem, 4vw, 2.5rem); font-weight: 900; text-transform: uppercase; letter-spacing: -.03em; margin-bottom: 16px; }
.sp-success-sub   { font-size: 1rem; color: var(--sp-muted); font-weight: 500; margin-bottom: 32px; }
.sp-back-btn { display: inline-flex; align-items: center; gap: 8px; padding: 16px 36px; background: var(--sp-blue); color: white; border: none; border-radius: 9999px; font-size: 11px; font-weight: 900; text-transform: uppercase; letter-spacing: .2em; cursor: pointer; text-decoration: none; transition: background .2s; }
.sp-back-btn:hover { background: #0f46b1; color: white; }
</style>
@endsection

@section('content')
<div class="sp-page">

    {{-- Breadcrumb --}}
    <div class="sp-breadcrumb">
        <div class="sp-wrap">
            <nav>
                <a href="{{ route('index') }}">{{ __('messages.common.home') }}</a>
                <i data-lucide="chevron-right" style="width:10px;height:10px;"></i>
                <a href="{{ route('vente.form') }}">{{ __('messages.ventepages.sell_my_car') }}</a>
                <i data-lucide="chevron-right" style="width:10px;height:10px;"></i>
                <span class="bc-cur">{{ __('messages.ventepages.sp_bc') }}</span>
            </nav>
        </div>
    </div>

    <div class="sp-wrap">

        {{-- Header --}}
        <div style="margin-bottom:48px;">
            <a href="{{ route('vente.form') }}" style="display:inline-flex;align-items:center;gap:6px;color:var(--sp-muted);font-size:12px;font-weight:700;text-decoration:none;margin-bottom:24px;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> {{ __('messages.ventepages.back') }}
            </a>
            <div class="sp-pill">
                <i data-lucide="building-2" style="width:12px;height:12px;"></i>
                {{ __('messages.ventepages.sp_pill') }}
            </div>
            <h1 class="sp-h1">
                {{ __('messages.ventepages.sp_h1_1') }}<br>
                {{ __('messages.ventepages.sp_h1_with') }} <span class="accent">{{ __('messages.ventepages.sp_h1_accent') }}</span>
            </h1>
            <p class="sp-subtitle">{{ __('messages.ventepages.sp_subtitle') }}</p>
        </div>

        {{-- Main content or success screen --}}
        <div id="sp-main">
            <div class="sp-grid">

                {{-- Left: Company form --}}
                <div>
                    <div class="sp-form-card">
                        <div class="sp-form-title">{{ __('messages.ventepages.sp_form_title') }}</div>
                        <div class="sp-form-sub">{{ __('messages.ventepages.sp_form_sub') }}</div>

                        <form id="sp-form" action="{{ route('vente.pro.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="jeveux" value="vendre en tant que professionnel">
                            <input type="hidden" name="mode" value="B2B Professionnel">

                            <div class="sp-form-grid">
                                <div class="sp-form-group">
                                    <label>{{ __('messages.ventepages.sp_company') }}</label>
                                    <input type="text" name="company_name" class="sp-input" placeholder="{{ __('messages.ventepages.sp_company_ph') }}" required>
                                </div>
                                <div class="sp-form-group">
                                    <label>{{ __('messages.ventepages.sp_ice') }}</label>
                                    <input type="text" name="ice" class="sp-input" placeholder="000 000 000 00000">
                                </div>
                                <div class="sp-form-group">
                                    <label>{{ __('messages.ventepages.sp_sector') }}</label>
                                    <select name="sector" class="sp-input" required>
                                        <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                        <option value="Concessionnaire">{{ __('messages.ventepages.sp_sector_concess') }}</option>
                                        <option value="Location longue durée">{{ __('messages.ventepages.sp_sector_lld') }}</option>
                                        <option value="Loueur courte durée">{{ __('messages.ventepages.sp_sector_llc') }}</option>
                                        <option value="Entreprise (flotte)">{{ __('messages.ventepages.sp_sector_fleet') }}</option>
                                        <option value="Autre">{{ __('messages.ventepages.sp_sector_other') }}</option>
                                    </select>
                                </div>
                                <div class="sp-form-group">
                                    <label>{{ __('messages.ventepages.sp_fleet') }}</label>
                                    <select name="fleet_size" class="sp-input">
                                        <option value="">{{ __('messages.ventemodal.choose') }}</option>
                                        <option value="1 – 10 véhicules">{{ __('messages.ventepages.sp_fleet_1') }}</option>
                                        <option value="11 – 50 véhicules">{{ __('messages.ventepages.sp_fleet_2') }}</option>
                                        <option value="51 – 200 véhicules">{{ __('messages.ventepages.sp_fleet_3') }}</option>
                                        <option value="+200 véhicules">{{ __('messages.ventepages.sp_fleet_4') }}</option>
                                    </select>
                                </div>
                                <div class="sp-form-group">
                                    <label>{{ __('messages.ventemodal.fullname') }}</label>
                                    <input type="text" name="name" class="sp-input" placeholder="{{ __('messages.ventemodal.fullname_ph') }}" required>
                                </div>
                                <div class="sp-form-group">
                                    <label>{{ __('messages.ventemodal.phone') }}</label>
                                    <input type="tel" name="phone" class="sp-input" placeholder="{{ __('messages.ventemodal.phone_ph') }}" required>
                                </div>
                                <div class="sp-form-group full">
                                    <label>{{ __('messages.ventepages.sp_email') }}</label>
                                    <input type="email" name="email" class="sp-input" placeholder="{{ __('messages.ventepages.sp_email_ph') }}">
                                </div>
                                <div class="sp-form-group full">
                                    <label>{{ __('messages.ventepages.sp_message') }}</label>
                                    <textarea name="message" class="sp-input" rows="4" placeholder="{{ __('messages.ventepages.sp_message_ph') }}" style="resize:vertical;"></textarea>
                                </div>
                            </div>

                            <button type="submit" class="sp-submit">
                                <i data-lucide="send" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_submit') }}
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right: info --}}
                <div class="sp-right">

                    {{-- Conditions --}}
                    <div class="sp-cond-card">
                        <div class="sp-cond-title">{{ __('messages.ventepages.sp_offer_title') }}</div>
                        <ul class="sp-cond-list">
                            <li class="sp-cond-item">
                                <i data-lucide="check-circle" class="sp-check" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_offer1') }}
                            </li>
                            <li class="sp-cond-item">
                                <i data-lucide="check-circle" class="sp-check" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_offer2') }}
                            </li>
                            <li class="sp-cond-item">
                                <i data-lucide="check-circle" class="sp-check" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_offer3') }}
                            </li>
                            <li class="sp-cond-item">
                                <i data-lucide="check-circle" class="sp-check" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_offer4') }}
                            </li>
                            <li class="sp-cond-item">
                                <i data-lucide="check-circle" class="sp-check" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_offer5') }}
                            </li>
                            <li class="sp-cond-item">
                                <i data-lucide="check-circle" class="sp-check" style="width:16px;height:16px;"></i>
                                {{ __('messages.ventepages.sp_offer6') }}
                            </li>
                        </ul>
                    </div>

                    {{-- Accompagnement --}}
                    <div class="sp-accomp">
                        <div class="sp-accomp-label">{{ __('messages.ventepages.sp_accomp_label') }}</div>
                        <div class="sp-accomp-title">{{ __('messages.ventepages.sp_accomp_title') }}</div>
                        <div class="sp-accomp-desc">{{ __('messages.ventepages.sp_accomp_desc') }}</div>
                    </div>

                    {{-- Support Express --}}
                    <div class="sp-support">
                        <div class="sp-support-label">{{ __('messages.ventepages.sp_support_label') }}</div>
                        <div class="sp-support-title">{{ __('messages.ventepages.sp_support_title') }}</div>
                        <div class="sp-support-desc">{{ __('messages.ventepages.sp_support_desc') }}</div>
                        <a href="tel:+212520070611" class="sp-support-btn">
                            <i data-lucide="phone" style="width:14px;height:14px;"></i>
                            +212 520 070 611
                        </a>
                    </div>

                </div>
            </div>
        </div>

        {{-- Success popup (shown after form submission via session flash) --}}
        @if(session('success'))
        <div id="spSuccessOverlay" class="sp-success-overlay" onclick="if(event.target===this){this.remove();document.body.style.overflow='';}">
            <div class="sp-success-modal" role="dialog" aria-modal="true" aria-labelledby="spSuccessTitle">
                <button type="button" class="sp-success-close" aria-label="{{ __('messages.ventemodal.close') }}"
                        onclick="document.getElementById('spSuccessOverlay').remove();document.body.style.overflow='';">
                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
                <div class="sp-success-icon">
                    <i data-lucide="check" style="width:48px;height:48px;color:#16a34a;stroke-width:2.5;"></i>
                </div>
                <h2 id="spSuccessTitle" class="sp-success-title">{{ __('messages.ventepages.sp_success_title') }}</h2>
                <p class="sp-success-sub">{{ session('success') }}<br>{{ __('messages.ventepages.sp_success_sub2') }}</p>
                <a href="{{ route('index') }}" class="sp-back-btn">
                    <i data-lucide="arrow-left" style="width:16px;height:16px;"></i>
                    {{ __('messages.ventepages.sp_back_site') }}
                </a>
            </div>
        </div>
        <style>
            .sp-success-overlay {
                position: fixed; inset: 0; z-index: 1300;
                background: rgba(15, 23, 42, 0.65);
                backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px);
                display: flex; align-items: center; justify-content: center;
                padding: 24px 16px; animation: spFadeIn .2s ease-out;
            }
            .sp-success-modal {
                position: relative; background: #fff; border-radius: 20px;
                width: 100%; max-width: 440px; padding: 48px 32px 36px;
                text-align: center;
                box-shadow: 0 24px 60px -10px rgba(0,0,0,0.35);
                animation: spPop .22s cubic-bezier(.2,.9,.3,1.3);
            }
            @keyframes spFadeIn { from { opacity: 0; } to { opacity: 1; } }
            @keyframes spPop   { from { opacity: 0; transform: translateY(10px) scale(.95); } to { opacity: 1; transform: translateY(0) scale(1); } }
            .sp-success-close {
                position: absolute; top: 14px; right: 14px;
                background: #f1f5f9; border: 0; width: 34px; height: 34px;
                border-radius: 50%; cursor: pointer; color: #475569;
                display: flex; align-items: center; justify-content: center;
                transition: background .15s, color .15s;
            }
            .sp-success-close:hover { background: #e2e8f0; color: #0f172a; }
            .sp-success-modal .sp-success-icon { margin: 0 auto 20px; }
            .sp-success-modal .sp-success-title { margin-bottom: 12px; }
            .sp-success-modal .sp-success-sub   { margin-bottom: 28px; }
            body.sp-no-scroll { overflow: hidden; }
        </style>
        <script>
            document.body.classList.add('sp-no-scroll');
            document.addEventListener('keydown', function (e) {
                if (e.key === 'Escape') {
                    var ov = document.getElementById('spSuccessOverlay');
                    if (ov) { ov.remove(); document.body.classList.remove('sp-no-scroll'); }
                }
            });
        </script>
        @endif

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
