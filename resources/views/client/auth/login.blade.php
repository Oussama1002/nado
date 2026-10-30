?@extends('layouts.auth')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    /* ========== ROOT ========== */
    .auth-page {
        min-height: 100vh;
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        position: relative;
    }

    /* ========== GRID BG ========== */
    .auth-grid-bg {
        position: fixed;
        inset: 0;
        opacity: 0.03;
        pointer-events: none;
        background-image:
            linear-gradient(#1459DD 1px, transparent 1px),
            linear-gradient(90deg, #1459DD 1px, transparent 1px);
        background-size: 32px 32px;
        z-index: 0;
    }

    /* ========== CONTENT WRAPPER ========== */
    .auth-z {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }

    /* ========== HEADER ========== */
    .auth-header {
        position: sticky;
        top: 0;
        z-index: 20;
        flex-shrink: 0;
        background: rgba(255, 255, 255, 0.85);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid #f1f5f9;
    }
    .auth-header-inner {
        max-width: 72rem;
        margin: 0 auto;
        width: 100%;
        padding: 12px 16px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
    }
    @media (min-width: 640px) {
        .auth-header-inner { padding: 16px 24px; }
    }
    @media (min-width: 1024px) {
        .auth-header-inner { padding: 16px 32px; }
    }

    .auth-back-btn {
        display: flex;
        align-items: center;
        gap: 8px;
        color: #475569;
        font-size: 14px;
        font-weight: 600;
        background: none;
        border: none;
        cursor: pointer;
        padding: 8px 0;
        text-decoration: none;
        transition: color 0.15s;
        touch-action: manipulation;
    }
    .auth-back-btn:hover { color: #0f172a; }
    @media (min-width: 640px) {
        .auth-back-btn { font-size: 16px; }
    }

    .auth-wordmark {
        font-size: 18px;
        font-weight: 900;
        letter-spacing: -0.03em;
    }
    @media (min-width: 640px) { .auth-wordmark { font-size: 20px; } }
    .auth-wordmark .na { color: #1459DD; }
    .auth-wordmark .do { color: #FDD817; }

    .auth-header-spacer { width: 64px; }
    @media (min-width: 640px) { .auth-header-spacer { width: 80px; } }

    /* ========== MAIN ========== */
    .auth-main {
        flex: 1;
        overflow-y: auto;
        padding: 32px 16px;
    }
    @media (min-width: 640px) { .auth-main { padding: 40px 24px; } }
    @media (min-width: 1024px) { .auth-main { padding: 48px 32px; } }
    .auth-main-inner {
        max-width: 72rem;
        margin: 0 auto;
        display: flex;
        flex-direction: column;
    }

    /* ========== TITLE ========== */
    .auth-title-block {
        text-align: center;
        margin-bottom: 24px;
    }
    @media (min-width: 640px) { .auth-title-block { margin-bottom: 32px; } }
    @media (min-width: 1024px) { .auth-title-block { margin-bottom: 40px; } }
    .auth-title-block h1 {
        font-size: 20px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.03em;
        margin-bottom: 4px;
    }
    @media (min-width: 640px) { .auth-title-block h1 { font-size: 24px; margin-bottom: 8px; } }
    @media (min-width: 1024px) { .auth-title-block h1 { font-size: 30px; } }
    .auth-title-block p {
        color: #475569;
        font-size: 14px;
        font-weight: 500;
    }
    @media (min-width: 640px) { .auth-title-block p { font-size: 16px; } }

    /* ========== FLASH MESSAGES ========== */
    .auth-alert {
        max-width: 640px;
        margin: 0 auto 24px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }
    .auth-alert.success { background: #dcfce7; color: #15803d; }
    .auth-alert.danger  { background: #fee2e2; color: #b91c1c; }
    .auth-alert ul { padding-left: 18px; }

    /* ========== MOBILE TABS ========== */
    .auth-tabs {
        display: flex;
        border-radius: 1rem;
        background: #fff;
        padding: 6px;
        border: 1px solid #e2e8f0;
        box-shadow: 0 1px 3px rgba(0,0,0,0.06);
        margin-bottom: 24px;
    }
    @media (min-width: 1024px) { .auth-tabs { display: none; } }
    .auth-tab-btn {
        flex: 1;
        padding: 12px 16px;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        border: none;
        cursor: pointer;
        transition: all 0.2s;
        touch-action: manipulation;
        background: transparent;
        color: #475569;
    }
    .auth-tab-btn:hover:not(.active) { color: #0f172a; }
    .auth-tab-btn.active {
        background: #1459DD;
        color: #fff;
        box-shadow: 0 4px 12px rgba(20,89,221,0.25);
    }

    /* ========== FORMS GRID ========== */
    .auth-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        flex: 1;
    }
    @media (min-width: 1024px) {
        .auth-grid {
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
    }
    @media (min-width: 1280px) {
        .auth-grid { gap: 48px; }
    }

    /* Single-column centered layout (login-only page) */
    .auth-grid-single {
        max-width: 480px;
        margin-left: auto !important;
        margin-right: auto !important;
        width: 100%;
        grid-template-columns: 1fr !important;
        justify-content: center;
        place-items: center;
    }
    @media (min-width: 1024px) {
        .auth-grid-single { grid-template-columns: 1fr !important; }
    }
    .auth-grid-single > .auth-card { width: 100%; }

    /* ========== CARD ========== */
    .auth-card {
        border-radius: 1rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        box-shadow: 0 8px 24px -4px rgba(148,163,184,0.15);
        overflow: hidden;
        display: flex;
        flex-direction: column;
    }
    @media (min-width: 640px) { .auth-card { border-radius: 1.5rem; } }
    .auth-card.hidden-mobile { display: none; }
    @media (min-width: 1024px) { .auth-card.hidden-mobile { display: flex; } }

    .auth-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    @media (min-width: 640px) { .auth-card-body { padding: 24px; } }
    @media (min-width: 1024px) { .auth-card-body { padding: 32px; } }

    /* Card header */
    .auth-card-head {
        display: flex;
        align-items: center;
        gap: 12px;
        margin-bottom: 16px;
    }
    @media (min-width: 640px) { .auth-card-head { margin-bottom: 20px; } }
    .auth-card-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
    }
    @media (min-width: 640px) {
        .auth-card-icon { width: 48px; height: 48px; }
    }
    .auth-card-icon.blue { background: rgba(20,89,221,0.1); color: #1459DD; }
    .auth-card-icon.yellow { background: rgba(253,216,23,0.2); color: #d97706; }

    .auth-card-head h2 {
        font-size: 18px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.03em;
        margin: 0 0 2px;
    }
    @media (min-width: 640px) { .auth-card-head h2 { font-size: 20px; } }
    .auth-card-head p {
        font-size: 12px;
        font-weight: 500;
        color: #64748b;
        margin: 0;
    }
    @media (min-width: 640px) { .auth-card-head p { font-size: 14px; } }

    /* ========== FORM ========== */
    .auth-form {
        display: flex;
        flex-direction: column;
        flex: 1;
        gap: 16px;
    }
    @media (min-width: 640px) { .auth-form { gap: 20px; } }

    .auth-field { display: flex; flex-direction: column; }
    .auth-label {
        display: block;
        font-size: 11px;
        font-weight: 800;
        color: #334155;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        margin-bottom: 6px;
    }
    .auth-input-wrap { position: relative; }
    .auth-input-icon {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        pointer-events: none;
        display: flex;
        align-items: center;
    }
    .auth-input {
        width: 100%;
        padding: 12px 16px 12px 42px;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 500;
        color: #0f172a;
        outline: none;
        transition: border-color 0.15s, box-shadow 0.15s;
        min-height: 48px;
        appearance: none;
        font-family: inherit;
    }
    @media (min-width: 640px) {
        .auth-input { padding-top: 14px; padding-bottom: 14px; }
    }
    .auth-input::placeholder { color: #94a3b8; }
    .auth-input:focus {
        border-color: #1459DD;
        background: #fff;
        box-shadow: 0 0 0 3px rgba(20,89,221,0.15);
    }
    .auth-card:last-child .auth-input:focus {
        border-color: #1459DD;
        box-shadow: 0 0 0 3px rgba(20,89,221,0.12);
    }

    /* Submit area */
    .auth-submit-area { padding-top: 8px; margin-top: auto; }
    @media (min-width: 640px) { .auth-submit-area { padding-top: 16px; } }

    .auth-btn-login {
        width: 100%;
        padding: 16px;
        background: #1459DD;
        color: #fff;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
        box-shadow: 0 8px 20px -4px rgba(20,89,221,0.3);
        min-height: 48px;
        touch-action: manipulation;
        font-family: inherit;
    }
    .auth-btn-login:hover { background: #0d47b4; }
    .auth-btn-login:active { transform: scale(0.98); }

    .auth-btn-register {
        width: 100%;
        padding: 16px;
        background: #FDD817;
        color: #0f172a;
        border: none;
        border-radius: 10px;
        font-size: 14px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.08em;
        cursor: pointer;
        transition: background 0.15s, transform 0.1s;
        box-shadow: 0 8px 20px -4px rgba(253,216,23,0.35);
        min-height: 48px;
        touch-action: manipulation;
        font-family: inherit;
    }
    .auth-btn-register:hover { background: #f0c90a; }
    .auth-btn-register:active { transform: scale(0.98); }

    /* Inline error per field */
    .auth-field-error {
        font-size: 12px;
        color: #dc2626;
        font-weight: 600;
        margin-top: 4px;
    }

    /* ========== FOOTER NOTE ========== */
    .auth-footer-note {
        text-align: center;
        color: #64748b;
        font-size: 12px;
        font-weight: 500;
        margin-top: 24px;
        padding-bottom: 24px;
        max-width: 36rem;
        margin-left: auto;
        margin-right: auto;
        line-height: 1.6;
    }
    @media (min-width: 640px) {
        .auth-footer-note { font-size: 14px; margin-top: 32px; padding-bottom: 32px; }
    }
    @media (min-width: 1024px) {
        .auth-footer-note { margin-top: 40px; padding-bottom: 40px; }
    }

    /* Show / hide password */
    .pw-toggle {
        position: absolute;
        top: 50%;
        right: 10px;
        transform: translateY(-50%);
        background: none;
        border: 0;
        padding: 4px;
        cursor: pointer;
        color: #94a3b8;
        line-height: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .pw-toggle:hover { color: #1459DD; }
    .auth-input-wrap:has(.pw-toggle) .auth-input { padding-right: 40px !important; }

    /* ========== PHONE REGISTER MODAL ========== */
    .pr-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.55);
        backdrop-filter: blur(4px);
        -webkit-backdrop-filter: blur(4px);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 16px;
    }
    .pr-modal-backdrop.show { display: flex; }
    .pr-modal {
        background: #fff;
        border-radius: 1rem;
        width: 100%;
        max-width: 440px;
        box-shadow: 0 20px 50px -10px rgba(0,0,0,0.3);
        overflow: hidden;
        animation: pr-pop 0.18s ease-out;
    }
    @keyframes pr-pop {
        from { opacity: 0; transform: translateY(10px) scale(0.98); }
        to   { opacity: 1; transform: translateY(0)    scale(1); }
    }
    .pr-modal-head {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 20px 24px;
        border-bottom: 1px solid #f1f5f9;
    }
    .pr-modal-head h3 {
        font-size: 18px;
        font-weight: 900;
        color: #0f172a;
        letter-spacing: -0.03em;
        margin: 0;
    }
    .pr-modal-close {
        background: none;
        border: 0;
        cursor: pointer;
        color: #64748b;
        padding: 4px;
        line-height: 0;
    }
    .pr-modal-close:hover { color: #0f172a; }
    .pr-modal-body { padding: 24px; }
    .pr-modal-body .auth-form { gap: 14px; }
</style>

@php
    $activeTab = 'login';
@endphp

<div class="auth-page">

    {{-- Fixed grid background --}}
    <div class="auth-grid-bg"></div>

    <div class="auth-z">

        {{-- ====== HEADER — full NADO navbar ====== --}}
        @include('partials.nado-navbar')
        <div style="height:110px;"></div>{{-- spacer for fixed navbar --}}

        {{-- ====== MAIN ====== --}}
        <main class="auth-main">
            <div class="auth-main-inner">

                {{-- Title --}}
                <div class="auth-title-block">
                    <h1>{{ __('messages.auth.page_title') }}</h1>
                    <p>{{ __('messages.auth.page_subtitle') }}</p>
                </div>

                {{-- Flash messages --}}
                @if(session('success'))
                    <div class="auth-alert success">{{ session('success') }}</div>
                @endif
                @if(session('flotte_enchere_blocked'))
                    <div class="auth-alert danger">{{ session('flotte_enchere_blocked') }}</div>
                @endif
                @if($errors->any() && $activeTab === 'login')
                    <div class="auth-alert danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Forms grid --}}
                <div class="auth-grid auth-grid-single">

                    {{-- ====== CARD A — LOGIN ====== --}}
                    <div class="auth-card {{ $activeTab !== 'login' ? 'hidden-mobile' : '' }}" id="cardLogin">
                        <div class="auth-card-body">
                            <div class="auth-card-head">
                                <div class="auth-card-icon blue">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <rect x="1" y="3" width="15" height="13" rx="2"/><path d="M16 8h5a1 1 0 011 1v10a1 1 0 01-1 1H8a1 1 0 01-1-1v-3"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2>{{ __('messages.auth.login_heading') }}</h2>
                                    <p>{{ __('messages.auth.login_sub') }}</p>
                                </div>
                            </div>

                            <form class="auth-form" method="POST" action="{{ route('client.login') }}">
                                @csrf
                                <input type="hidden" name="_form" value="login">

                                {{-- Email --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="login_email">{{ __('messages.auth.email') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                <polyline points="22,6 12,13 2,6"/>
                                            </svg>
                                        </span>
                                        <input
                                            type="email"
                                            id="login_email"
                                            name="email"
                                            class="auth-input"
                                            placeholder="vous@exemple.com"
                                            value="{{ old('email') }}"
                                            autocomplete="email"
                                            required
                                        >
                                    </div>
                                    @error('email')
                                        <span class="auth-field-error">{{ $message }}</span>
                                    @enderror
                                </div>

                                {{-- Mot de passe --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="login_password">{{ __('messages.auth.password') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                                            </svg>
                                        </span>
                                        <input
                                            type="password"
                                            id="login_password"
                                            name="password"
                                            class="auth-input"
                                            placeholder="{{ __('messages.auth.password_ph') }}"
                                            autocomplete="current-password"
                                            required
                                        >
                                        <button type="button" class="pw-toggle" data-target="login_password"
                                            aria-label="Afficher le mot de passe" title="Afficher / masquer">
                                            <svg class="pw-eye" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                                            </svg>
                                            <svg class="pw-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none"
                                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                style="display:none;">
                                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                                <line x1="1" y1="1" x2="23" y2="23"/>
                                            </svg>
                                        </button>
                                    </div>
                                    <div style="text-align:right;margin-top:8px;">
                                        <a href="{{ route('client.password.request') }}" style="font-size:13px;color:#1459DD;font-weight:600;text-decoration:none;">{{ __('messages.auth.forgot') }}</a>
                                    </div>
                                </div>

                                <div class="auth-submit-area">
                                    <button type="submit" class="auth-btn-login">{{ __('messages.auth.login_btn') }}</button>
                                </div>

                                <p style="text-align:center;margin-top:16px;font-size:14px;color:#475569;font-weight:500;">
                                    {{ __('messages.auth.not_registered') }}
                                    <a href="{{ route('client.register') }}" style="color:#1459DD;font-weight:700;text-decoration:none;">{{ __('messages.auth.create_account') }}</a>
                                </p>

                                <p style="text-align:center;margin-top:8px;font-size:14px;color:#475569;font-weight:500;">
                                    {{ __('messages.auth.no_email') }}
                                    <a href="#" id="openPhoneRegister" style="color:#1459DD;font-weight:700;text-decoration:none;">{{ __('messages.auth.click_here') }}</a>
                                </p>
                            </form>
                        </div>
                    </div>


                </div>{{-- end .auth-grid --}}

                {{-- Legal footer --}}
                <p class="auth-footer-note">
                    {!! __('messages.auth.legal_note') !!}
                </p>

            </div>
        </main>
    </div>
</div>


{{-- ====== PHONE REGISTER MODAL ====== --}}
<div class="pr-modal-backdrop" id="phoneRegisterModal" aria-hidden="true">
    <div class="pr-modal" role="dialog" aria-modal="true" aria-labelledby="prModalTitle">
        <div class="pr-modal-head">
            <h3 id="prModalTitle">{{ __('messages.auth.phone_title') }}</h3>
            <button type="button" class="pr-modal-close" id="closePhoneRegister" aria-label="{{ __('messages.auth.close') }}">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="pr-modal-body">
            @if ($errors->any() && old('_form') === 'phone_register')
                <div class="auth-alert danger">
                    <ul>
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <form class="auth-form" method="POST" action="{{ route('client.register.phone') }}">
                @csrf
                <input type="hidden" name="_form" value="phone_register">

                {{-- Nom --}}
                <div class="auth-field">
                    <label class="auth-label" for="pr_name">{{ __('messages.auth.full_name') }}</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                            </svg>
                        </span>
                        <input type="text" id="pr_name" name="name" class="auth-input" placeholder="{{ __('messages.auth.name_ph') }}" value="{{ old('name') }}" required>
                    </div>
                </div>

                {{-- Téléphone --}}
                <div class="auth-field">
                    <label class="auth-label" for="pr_phone">{{ __('messages.auth.phone') }}</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07 19.5 19.5 0 01-6-6 19.79 19.79 0 01-3.07-8.67A2 2 0 014.11 2h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L8.09 9.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16.92z"/>
                            </svg>
                        </span>
                        <input type="tel" id="pr_phone" name="phone" class="auth-input" placeholder="06 00 00 00 00" value="{{ old('phone') }}" required>
                    </div>
                </div>

                {{-- Mot de passe --}}
                <div class="auth-field">
                    <label class="auth-label" for="pr_password">{{ __('messages.auth.password') }}</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </span>
                        <input type="password" id="pr_password" name="password" class="auth-input" placeholder="{{ __('messages.auth.min_chars') }}" autocomplete="new-password" required>
                        <button type="button" class="pw-toggle" data-target="pr_password" aria-label="{{ __('messages.auth.show_password') }}">
                            <svg class="pw-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="pw-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                {{-- Confirmation --}}
                <div class="auth-field">
                    <label class="auth-label" for="pr_password_confirmation">{{ __('messages.auth.confirm_password') }}</label>
                    <div class="auth-input-wrap">
                        <span class="auth-input-icon">
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>
                            </svg>
                        </span>
                        <input type="password" id="pr_password_confirmation" name="password_confirmation" class="auth-input" placeholder="{{ __('messages.auth.retype_password') }}" autocomplete="new-password" required>
                        <button type="button" class="pw-toggle" data-target="pr_password_confirmation" aria-label="{{ __('messages.auth.show_password') }}">
                            <svg class="pw-eye" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                            <svg class="pw-eye-off" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;">
                                <path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/>
                                <line x1="1" y1="1" x2="23" y2="23"/>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="auth-submit-area">
                    <button type="submit" class="auth-btn-login">{{ __('messages.auth.create_my_account') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    // Phone-register modal open/close
    (function () {
        var modal   = document.getElementById('phoneRegisterModal');
        var openBtn = document.getElementById('openPhoneRegister');
        var closeBtn = document.getElementById('closePhoneRegister');
        if (!modal || !openBtn) return;
        function open(e)  { if (e) e.preventDefault(); modal.classList.add('show'); modal.setAttribute('aria-hidden','false'); document.body.style.overflow = 'hidden'; }
        function close()  { modal.classList.remove('show'); modal.setAttribute('aria-hidden','true'); document.body.style.overflow = ''; }
        openBtn.addEventListener('click', open);
        if (closeBtn) closeBtn.addEventListener('click', close);
        modal.addEventListener('click', function (e) { if (e.target === modal) close(); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape' && modal.classList.contains('show')) close(); });

        // Auto-open if validation errors came from this form
        @if ($errors->any() && old('_form') === 'phone_register')
            open();
        @endif
    })();
</script>

<script>
    // Show / hide password. Icons swap between eye and eye-off.
    document.querySelectorAll('.pw-toggle').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var input = document.getElementById(btn.dataset.target);
            if (!input) return;
            var show = input.type === 'password';
            input.type = show ? 'text' : 'password';
            btn.querySelector('.pw-eye').style.display = show ? 'none' : '';
            btn.querySelector('.pw-eye-off').style.display = show ? '' : 'none';
            btn.setAttribute('aria-label', show ? 'Masquer le mot de passe' : 'Afficher le mot de passe');
            input.focus();
        });
    });
</script>
@endsection
