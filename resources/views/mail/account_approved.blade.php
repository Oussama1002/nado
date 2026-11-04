<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="UTF-8">
    <title>{{ __('messages.mail.aa_title') }}</title>
</head>
<body style="font-family: Arial, sans-serif; background-color: #f9f9f9; padding: 20px;">
    <div style="max-width: 600px; margin: auto; background-color: white; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1);">
        <h2 style="color: #003b87;">{{ __('messages.mail.aa_greeting', ['name' => $name]) }}</h2>

        <p>{!! __('messages.mail.aa_verified') !!}</p>

        <p>{{ __('messages.mail.aa_login') }}</p>

        <br>
        <a href="{{ route('client.login') }}" style="display:inline-block; background-color: #003b87; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">{{ __('messages.mail.aa_login_btn') }}</a>

        <br><br>
        <p style="font-size: 12px; color: #999;">{{ __('messages.mail.aa_not_you') }}</p>

        <p style="margin-top: 30px;">{{ __('messages.mail.aa_team') }}</p>
    </div>
</body>
</html>
