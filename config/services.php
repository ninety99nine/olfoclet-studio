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

    /*
    |--------------------------------------------------------------------------
    | Browsershot / PDF (Chromium)
    |--------------------------------------------------------------------------
    | Path to Chromium/Chrome for generating PDFs. Set CHROMIUM_PATH in .env
    | to override. On macOS, defaults to Homebrew Chromium if installed.
    */
    'browsershot' => [
        'chromium_path' => env('CHROMIUM_PATH', PHP_OS_FAMILY === 'Darwin'
            ? '/Applications/Chromium.app/Contents/MacOS/Chromium'
            : null),
    ],

];
