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

    // WhatsApp gateway. UltraMsg (unofficial, works with groups) is the
    // default; CallmeBot is kept as a fallback for the OTP flow only.
    //
    // admin_group:   WhatsApp group id (xxxxxxxxxxxx@g.us). Where new
    //                demandes and mirrored emails land.
    // admin_numbers: comma-separated phones that ALSO receive admin
    //                broadcasts (useful before the group is set up, or
    //                for owners who prefer DMs).
    'whatsapp' => [
        'provider'           => env('WHATSAPP_PROVIDER', 'ultramsg'),
        'ultramsg_instance'  => env('ULTRAMSG_INSTANCE_ID'),
        'ultramsg_token'     => env('ULTRAMSG_TOKEN'),
        'callmebot_apikey'   => env('CALLMEBOT_APIKEY'),
        'admin_group'        => env('WHATSAPP_ADMIN_GROUP', ''),
        'admin_numbers'      => env('WHATSAPP_ADMIN_NUMBERS', ''),
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
