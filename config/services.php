<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    // WhatsApp — Meta Cloud API (graph.facebook.com). Shares the same
    // Meta Business account as Facebook/Instagram Ads.
    //
    // phone_number_id / access_token: from Meta Business → WhatsApp →
    //   API setup. Use a System User permanent token in production.
    // api_version: graph API version (e.g. v21.0, v22.0).
    // admin_numbers: comma-separated phones (international format, with
    //   or without "+"; the service normalizes). Mirrored emails and
    //   demande notifications fan out 1-to-1 to these numbers. Meta's
    //   Cloud API has no group endpoint — Telegram still handles the
    //   group broadcast in parallel.
    // admin_template: name of a Meta-approved utility template with a
    //   single body parameter {{1}}. If set, admin notifications use
    //   it (works outside the 24h customer-service window).
    // otp_template: name of an authentication template with {{1}}.
    //   Required to send OTPs outside an open session.
    'whatsapp' => [
        'phone_number_id'    => env('WHATSAPP_PHONE_NUMBER_ID'),
        'access_token'       => env('WHATSAPP_ACCESS_TOKEN'),
        'api_version'        => env('WHATSAPP_API_VERSION', 'v21.0'),
        'admin_numbers'      => env('WHATSAPP_ADMIN_NUMBERS', ''),
        'admin_template'     => env('WHATSAPP_ADMIN_TEMPLATE', ''),
        'admin_template_lang'=> env('WHATSAPP_ADMIN_TEMPLATE_LANG', 'fr'),
        'otp_template'       => env('WHATSAPP_OTP_TEMPLATE', ''),
        'otp_template_lang'  => env('WHATSAPP_OTP_TEMPLATE_LANG', 'fr'),
    ],

    // Telegram bot — used to broadcast new demandes to a channel.
    // bot_token: token from @BotFather (secret, never commit).
    // demandes_chat: destination channel. For a public channel, the @username
    // (with the @). For a private one, the numeric id like -1001234567890.
    'telegram' => [
        'bot_token'     => env('TELEGRAM_BOT_TOKEN'),
        'demandes_chat' => env('TELEGRAM_DEMANDES_CHAT', '@nado_voitures_maroc'),
    ],

    // OCR local de la carte grise (Tesseract). 100 % sur le serveur.
    // Sur hébergement sans root, pointer TESSERACT_PATH vers un binaire installé
    // dans le home (ex. ~/tess/bin/tesseract) et TESSDATA_PREFIX vers ses .traineddata.
    'tesseract' => [
        'path'     => env('TESSERACT_PATH', 'tesseract'),
        'lang'     => env('TESSERACT_LANG', 'fra+eng'),
        'tessdata' => env('TESSDATA_PREFIX'), // dossier des fichiers .traineddata (optionnel)
    ],

];
