@extends('layouts.app')

@section('title', 'Contact | Nado.ma')

@section('content')
<style>
    .cx-wrap { font-family: 'Inter', sans-serif; color: #0f172a; background: #f8fafc; padding: 0 0 4rem; }
    .cx-hero {
        background: linear-gradient(135deg, #1459DD 0%, #0b3fa0 100%);
        color: #fff; padding: 3.5rem 1rem 6rem; text-align: center; position: relative; overflow: hidden;
    }
    .cx-hero::before {
        content: ''; position: absolute; inset: 0; opacity: .15; pointer-events: none;
        background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 34px 34px;
    }
    .cx-hero-inner { position: relative; z-index: 1; max-width: 640px; margin: 0 auto; }
    .cx-badge {
        display: inline-block; font-size: .68rem; font-weight: 900; text-transform: uppercase;
        letter-spacing: .25em; background: #FDD817; color: #0f172a; padding: .4rem 1rem; border-radius: 999px; margin-bottom: 1.25rem;
    }
    .cx-hero h1 { font-size: clamp(1.9rem, 5vw, 3rem); font-weight: 900; line-height: 1.1; margin: 0 0 1rem; letter-spacing: -.02em; }
    .cx-hl { color: #FDD817; }
    .cx-hero p { font-size: 1rem; color: rgba(255,255,255,.85); margin: 0; line-height: 1.7; }

    .cx-container { max-width: 1120px; margin: -4rem auto 0; padding: 0 1rem; position: relative; z-index: 2; }

    .cx-cards { display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; margin-bottom: 2.5rem; }
    @media (max-width: 860px) { .cx-cards { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 460px) { .cx-cards { grid-template-columns: 1fr; } }
    .cx-card {
        background: #fff; border: 1px solid #eef2f7; border-radius: 1.25rem; padding: 1.5rem;
        box-shadow: 0 18px 40px -22px rgba(15,23,42,.25); text-align: center;
    }
    .cx-card-ic {
        width: 3rem; height: 3rem; border-radius: .9rem; background: #eaf1fe; color: #1459DD;
        display: inline-flex; align-items: center; justify-content: center; font-size: 1.1rem; margin-bottom: .9rem;
    }
    .cx-card h3 { font-size: .72rem; font-weight: 900; text-transform: uppercase; letter-spacing: .12em; color: #94a3b8; margin: 0 0 .35rem; }
    .cx-card p, .cx-card a { font-size: .9rem; font-weight: 700; color: #0f172a; margin: 0; text-decoration: none; word-break: break-word; }
    .cx-card a:hover { color: #1459DD; }

    .cx-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; align-items: stretch; }
    @media (max-width: 860px) { .cx-grid { grid-template-columns: 1fr; } }

    .cx-form-card, .cx-map-card {
        background: #fff; border: 1px solid #eef2f7; border-radius: 1.5rem; overflow: hidden;
        box-shadow: 0 18px 40px -24px rgba(15,23,42,.25);
    }
    .cx-form-card { padding: 2rem; }
    .cx-form-card h2 { font-size: 1.35rem; font-weight: 900; margin: 0 0 1.25rem; }
    .cx-alert { background: #ecfdf5; border: 1px solid #a7f3d0; color: #047857; padding: .85rem 1rem; border-radius: .75rem; font-size: .9rem; font-weight: 600; margin-bottom: 1rem; }

    .cx-form { display: flex; flex-direction: column; gap: .9rem; }
    .cx-row { display: grid; grid-template-columns: 1fr 1fr; gap: .9rem; }
    @media (max-width: 520px) { .cx-row { grid-template-columns: 1fr; } }
    .cx-input {
        width: 100%; padding: .85rem 1rem; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: .75rem;
        color: #0f172a; font-size: .95rem; font-family: inherit; outline: none; transition: border-color .2s, background .2s;
    }
    .cx-input::placeholder { color: #94a3b8; }
    .cx-input:focus { border-color: #1459DD; background: #fff; }
    textarea.cx-input { resize: vertical; min-height: 120px; }
    .cx-submit {
        margin-top: .25rem; padding: 1rem; border: none; border-radius: .75rem; background: #1459DD; color: #fff;
        font-weight: 900; font-size: .95rem; text-transform: uppercase; letter-spacing: .03em; cursor: pointer;
        display: inline-flex; align-items: center; justify-content: center; gap: .5rem; transition: transform .15s, background .2s;
    }
    .cx-submit:hover { transform: translateY(-2px); background: #0b3fa0; }

    .cx-map-card iframe { display: block; width: 100%; height: 100%; min-height: 360px; border: 0; }

    .cx-err { color: #dc2626; font-size: .78rem; font-weight: 600; margin: -.4rem 0 0; }

    html[dir="rtl"] .cx-card, html[dir="rtl"] .cx-hero, html[dir="rtl"] .cx-form-card { text-align: right; }
    html[dir="rtl"] .cx-card { text-align: center; }
</style>

<div class="cx-wrap">
    <section class="cx-hero">
        <div class="cx-hero-inner">
            <span class="cx-badge">{{ __('messages.contact.badge') }}</span>
            <h1>{!! __('messages.contact.title') !!}</h1>
            <p>{{ __('messages.contact.subtitle') }}</p>
        </div>
    </section>

    <div class="cx-container">
        <div class="cx-cards">
            <div class="cx-card">
                <div class="cx-card-ic"><i class="fas fa-map-marker-alt"></i></div>
                <h3>{{ __('messages.contact.addr_label') }}</h3>
                <p>{{ __('messages.contact.addr_value') }}</p>
            </div>
            <div class="cx-card">
                <div class="cx-card-ic"><i class="fas fa-envelope"></i></div>
                <h3>{{ __('messages.contact.email_label') }}</h3>
                <a href="mailto:contact@nado.ma"><bdi dir="ltr">contact@nado.ma</bdi></a>
            </div>
            <div class="cx-card">
                <div class="cx-card-ic"><i class="fas fa-phone"></i></div>
                <h3>{{ __('messages.contact.phone_label') }}</h3>
                <a href="tel:+212520070611"><bdi dir="ltr">+212 520 070 611</bdi></a>
            </div>
            <div class="cx-card">
                <div class="cx-card-ic"><i class="fas fa-clock"></i></div>
                <h3>{{ __('messages.contact.hours_label') }}</h3>
                <p>{{ __('messages.contact.hours_value') }}</p>
            </div>
        </div>

        <div class="cx-grid">
            <div class="cx-form-card">
                <h2>{{ __('messages.contact.form_title') }}</h2>

                @if (session('success'))
                    <div class="cx-alert">{{ session('success') }}</div>
                @endif

                <form method="post" class="cx-form" action="{{ route('contactez-nous.store') }}">
                    @csrf
                    <div class="cx-row">
                        <div>
                            <input name="name" type="text" required class="cx-input" placeholder="{{ __('messages.contact.name_ph') }}" value="{{ old('name') }}">
                            @error('name')<p class="cx-err">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input name="email" type="email" required class="cx-input" placeholder="{{ __('messages.contact.email_ph') }}" value="{{ old('email') }}">
                            @error('email')<p class="cx-err">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="cx-row">
                        <div>
                            <input name="phone" type="tel" required class="cx-input" placeholder="{{ __('messages.contact.phone_ph') }}" value="{{ old('phone') }}">
                            @error('phone')<p class="cx-err">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <input name="subject" type="text" class="cx-input" placeholder="{{ __('messages.contact.subject_ph') }}" value="{{ old('subject', request('subject')) }}">
                            @error('subject')<p class="cx-err">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <textarea name="message" rows="5" class="cx-input" placeholder="{{ __('messages.contact.message_ph') }}">{{ old('message', request('message')) }}</textarea>
                    @error('message')<p class="cx-err">{{ $message }}</p>@enderror
                    <button type="submit" class="cx-submit"><i class="fas fa-paper-plane"></i> {{ __('messages.contact.send') }}</button>
                </form>
            </div>

            <div class="cx-map-card">
                <iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d831.497999123923!2d-7.607901806936464!3d33.527593717096664!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0xda62d2b43f5fa89%3A0x4a08c15bb60afbf5!2sNADO.ma%20-%20Auto%20Ench%C3%A8re!5e0!3m2!1sfr!2sma!4v1704650848180!5m2!1sfr!2sma"
                        allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="NADO.ma"></iframe>
            </div>
        </div>
    </div>
</div>
@endsection
