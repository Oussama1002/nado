@extends('layouts.auth')

@section('content')
<style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    .auth-page {
        min-height: 100vh;
        min-height: 100dvh;
        display: flex;
        flex-direction: column;
        background: #f8fafc;
        font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;
        position: relative;
    }

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

    .auth-z {
        position: relative;
        z-index: 10;
        display: flex;
        flex-direction: column;
        flex: 1;
        min-height: 0;
    }

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

    .auth-alert {
        max-width: 480px;
        margin: 0 auto 24px;
        padding: 12px 16px;
        border-radius: 12px;
        font-size: 14px;
        font-weight: 600;
    }
    .auth-alert.success { background: #dcfce7; color: #15803d; }
    .auth-alert.danger  { background: #fee2e2; color: #b91c1c; }
    .auth-alert ul { padding-left: 18px; }

    .auth-grid {
        display: grid;
        grid-template-columns: 1fr;
        gap: 24px;
        flex: 1;
    }
    .auth-grid-single {
        max-width: 480px;
        margin-left: auto !important;
        margin-right: auto !important;
        width: 100%;
        grid-template-columns: 1fr !important;
    }
    .auth-grid-single > .auth-card { width: 100%; }

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

    .auth-card-body {
        padding: 20px;
        display: flex;
        flex-direction: column;
        flex: 1;
    }
    @media (min-width: 640px) { .auth-card-body { padding: 24px; } }
    @media (min-width: 1024px) { .auth-card-body { padding: 32px; } }

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
    @media (min-width: 640px) { .auth-card-icon { width: 48px; height: 48px; } }
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

    .auth-toggle-password {
        position: absolute;
        right: 12px;
        top: 50%;
        transform: translateY(-50%);
        background: transparent;
        border: none;
        cursor: pointer;
        color: #94a3b8;
        padding: 6px;
        display: inline-flex;
        align-items: center;
    }
    .auth-toggle-password:hover { color: #1459DD; }
    .auth-input.has-toggle { padding-right: 44px; }

    .auth-submit-area { padding-top: 8px; margin-top: auto; }
    @media (min-width: 640px) { .auth-submit-area { padding-top: 16px; } }

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

    .auth-field-error {
        font-size: 12px;
        color: #dc2626;
        font-weight: 600;
        margin-top: 4px;
    }

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
</style>

<div class="auth-page">
    <div class="auth-grid-bg"></div>

    <div class="auth-z">
        @include('partials.nado-navbar')
        <div style="height:110px;"></div>

        <main class="auth-main">
            <div class="auth-main-inner">

                <div class="auth-title-block">
                    <h1>{{ __('messages.auth.create_account') }}</h1>
                    <p>{{ __('messages.auth.register_subtitle') }}</p>
                </div>

                @if(session('success'))
                    <div class="auth-alert success">{{ session('success') }}</div>
                @endif

                @if($errors->any())
                    <div class="auth-alert danger">
                        <ul>
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="auth-grid auth-grid-single">

                    <div class="auth-card" id="cardCreate">
                        <div class="auth-card-body">
                            <div class="auth-card-head">
                                <div class="auth-card-icon yellow">
                                    <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                    </svg>
                                </div>
                                <div>
                                    <h2>{{ __('messages.auth.welcome') }}</h2>
                                    <p>{{ __('messages.auth.fill_form') }}</p>
                                </div>
                            </div>

                            <form class="auth-form" method="POST" action="{{ route('client.register') }}">
                                @csrf

                                {{-- Type de compte --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="clientType">{{ __('messages.auth.account_type') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                                <polyline points="9 22 9 12 15 12 15 22"/>
                                            </svg>
                                        </span>
                                        <select id="clientType" name="type" class="auth-input" required style="color:#0f172a;">
                                            <option value="" disabled {{ old('type') ? '' : 'selected' }}>{{ __('messages.auth.client_type') }}</option>
                                            <option value="particulier" {{ old('type') === 'particulier' ? 'selected' : '' }}>{{ __('messages.auth.individual') }}</option>
                                            <option value="pro" {{ old('type') === 'pro' ? 'selected' : '' }}>{{ __('messages.auth.professional') }}</option>
                                            <option value="flotte&enchere" {{ old('type') === 'flotte&enchere' ? 'selected' : '' }}>{{ __('messages.auth.fleet_auction') }}</option>
                                        </select>
                                    </div>
                                    @error('type')<span class="auth-field-error">{{ $message }}</span>@enderror
                                </div>

                                {{-- Nom complet --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="reg_name">{{ __('messages.auth.full_name') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                                <circle cx="12" cy="7" r="4"/>
                                            </svg>
                                        </span>
                                        <input type="text" id="reg_name" name="name" class="auth-input" placeholder="{{ __('messages.auth.name_full_ph') }}" value="{{ old('name') }}" autocomplete="name" required>
                                    </div>
                                    @error('name')<span class="auth-field-error">{{ $message }}</span>@enderror
                                </div>

                                {{-- Entreprise --}}
                                <div class="auth-field entreprise-field">
                                    <label class="auth-label" for="reg_company">{{ __('messages.auth.company_name') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M3 21h18M5 21V7l7-4 7 4v14M9 9h.01M9 13h.01M9 17h.01M15 9h.01M15 13h.01M15 17h.01"/>
                                            </svg>
                                        </span>
                                        <input type="text" id="reg_company" name="company" class="auth-input" placeholder="{{ __('messages.auth.company_name') }}" value="{{ old('company') }}">
                                    </div>
                                    @error('company')<span class="auth-field-error">{{ $message }}</span>@enderror
                                </div>

                                {{-- Email --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="reg_email">{{ __('messages.auth.email') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                                                <polyline points="22,6 12,13 2,6"/>
                                            </svg>
                                        </span>
                                        <input type="email" id="reg_email" name="email" class="auth-input" placeholder="vous@exemple.com" value="{{ old('email') }}" autocomplete="email" required>
                                    </div>
                                    @error('email')<span class="auth-field-error">{{ $message }}</span>@enderror
                                </div>

                                {{-- Téléphone --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="reg_phone">{{ __('messages.auth.phone') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.81a19.79 19.79 0 01-3.07-8.64A2 2 0 012 1h3a2 2 0 012 1.72 12.84 12.84 0 00.7 2.81 2 2 0 01-.45 2.11L6.09 8.91a16 16 0 006 6l1.27-1.27a2 2 0 012.11-.45 12.84 12.84 0 002.81.7A2 2 0 0122 16"/>
                                            </svg>
                                        </span>
                                        <input type="tel" id="reg_phone" name="phone" class="auth-input" placeholder="06 xx xx xx xx" value="{{ old('phone') }}" autocomplete="tel" required>
                                    </div>
                                    @error('phone')<span class="auth-field-error">{{ $message }}</span>@enderror
                                </div>

                                {{-- Mot de passe --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="password">{{ __('messages.auth.password') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                                                <path d="M7 11V7a5 5 0 0110 0v4"/>
                                            </svg>
                                        </span>
                                        <input type="password" id="password" name="password" class="auth-input has-toggle" placeholder="{{ __('messages.auth.password') }}" autocomplete="new-password" required>
                                        <button type="button" class="auth-toggle-password" data-target="#password" aria-label="{{ __('messages.auth.show_password') }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>
                                    @error('password')<span class="auth-field-error">{{ $message }}</span>@enderror
                                </div>

                                {{-- Confirmer mot de passe --}}
                                <div class="auth-field">
                                    <label class="auth-label" for="password_confirmation">{{ __('messages.auth.confirm_password') }}</label>
                                    <div class="auth-input-wrap">
                                        <span class="auth-input-icon">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M21 2l-2 2m-7.61 7.61a5.5 5.5 0 11-7.778 7.778 5.5 5.5 0 017.777-7.777zm0 0L15.5 7.5m0 0l3 3L22 7l-3-3m-3.5 3.5L19 4"/>
                                            </svg>
                                        </span>
                                        <input type="password" id="password_confirmation" name="password_confirmation" class="auth-input has-toggle" placeholder="{{ __('messages.auth.confirm_password') }}" autocomplete="new-password" required>
                                        <button type="button" class="auth-toggle-password" data-target="#password_confirmation" aria-label="{{ __('messages.auth.show_password') }}">
                                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                                <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/>
                                                <circle cx="12" cy="12" r="3"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                <div class="auth-submit-area">
                                    <button type="submit" class="auth-btn-register">{{ __('messages.auth.register_btn') }}</button>
                                </div>

                                <p style="text-align:center;margin-top:16px;font-size:14px;color:#475569;font-weight:500;">
                                    {{ __('messages.auth.already_registered') }}
                                    <a href="{{ route('client.login') }}" style="color:#1459DD;font-weight:700;text-decoration:none;">{{ __('messages.auth.login_btn') }}</a>
                                </p>
                            </form>
                        </div>
                    </div>

                </div>

                <p class="auth-footer-note">
                    {!! __('messages.auth.legal_note') !!}
                </p>

            </div>
        </main>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var clientType = document.getElementById('clientType');
    var entrepriseFields = document.querySelectorAll('.entreprise-field');

    function updateFieldsByType() {
        var isParticulier = clientType.value === 'particulier';
        entrepriseFields.forEach(function(el){ el.style.display = isParticulier ? 'none' : ''; });
    }
    if (clientType) {
        clientType.addEventListener('change', updateFieldsByType);
        updateFieldsByType();
    }

    document.querySelectorAll('.auth-toggle-password').forEach(function(btn) {
        btn.addEventListener('click', function (e) {
            e.preventDefault();
            var input = document.querySelector(this.getAttribute('data-target'));
            if (!input) return;
            input.type = input.type === 'password' ? 'text' : 'password';
        });
    });
});
</script>
@endsection
