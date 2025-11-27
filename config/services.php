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

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | FioBanka API
    |--------------------------------------------------------------------------
    |
    | Налаштування для інтеграції з FioBanka API.
    | Документація: https://www.fio.cz/docs/cz/API_Bankovnictvi.pdf
    |
    | Важливі обмеження:
    | - 1 запит на 30 секунд для одного токену
    | - Токен дійсний 180 днів з моменту генерації
    |
    */

    'fio_banka' => [
        'base_url' => env('FIO_BANKA_BASE_URL', 'https://fioapi.fio.cz/v1/rest'),
    ],

];
