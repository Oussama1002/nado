<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.mail.pp_title') }}</title>
</head>
<body style="font-family: Arial, sans-serif; color: #333;">
    <p>{{ __('messages.mail.pp_subject') }} <strong>{{ $enchere->id }}</strong> - <strong>{{ $enchere->brand?->name }} {{ $enchere->modeleRelation?->name }}</strong> - {{ __('messages.mail.pp_thanks') }}</p>

    <p>{{ __('messages.mail.pp_dear', ['name' => $client->name]) }}</p>

    <p>{{ __('messages.mail.pp_thank_1') }} <strong>{{ __('messages.mail.pp_ref') }} {{ $enchere->id }} / {{ $enchere->brand?->name }} {{ $enchere->modeleRelation?->name }}</strong> {{ __('messages.mail.pp_thank_2') }}</p>

    <p>{{ __('messages.mail.pp_offer_1') }} <strong>{{ number_format($montant, 0, ',', ' ') }} MAD</strong> {{ __('messages.mail.pp_offer_2') }}</p>

    <h3>{{ __('messages.mail.pp_next') }}</h3>
    <p>{{ __('messages.mail.pp_ongoing_1') }} <strong>{{ \Carbon\Carbon::parse($enchere->ends_at)->format('d/m/Y H:i') }}</strong>.</p>

    <p>👉 <a href="{{ route('nadoautoenchere.show', $enchere->id) }}">{{ __('messages.mail.pp_follow') }}</a></p>

    <h3>{{ __('messages.mail.pp_important') }}</h3>
    <p>{{ __('messages.mail.pp_winning') }}</p>

    <p>{{ __('messages.mail.pp_delay') }}</p>

    <p>{{ __('messages.mail.pp_terms_1') }} <a href="#">{{ __('messages.mail.pp_terms_link') }}</a> {{ __('messages.mail.pp_terms_2') }}</p>

    <p>{{ __('messages.mail.pp_regards') }}<br>
    <strong>{{ __('messages.mail.pp_team') }}</strong><br>
    <a href="#">www.autoenchere.ma</a><br>
    <a href="mailto:support@autoenchere.ma">support@autoenchere.ma</a></p>
</body>
</html>
