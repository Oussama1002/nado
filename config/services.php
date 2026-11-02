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

    'whatsapp' => [
        'provider'           => env('WHATSAPP_PROVIDER', 'ultramsg'),
        'ultramsg_instance'  => env('ULTRAMSG_INSTANCE_ID'),
        'ultramsg_token'     => env('ULTRAMSG_TOKEN'),
        'callmebot_apikey'   => env('CALLMEBOT_APIKEY'),
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
