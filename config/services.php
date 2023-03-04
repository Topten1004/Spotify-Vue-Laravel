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
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],
    'passport' => [
        'login_endpoint' => env('APP_URL') . '/oauth/token',
        'client_id' => env('PASSPORT_CLIENT_ID'),
        'client_secret' => env('PASSPORT_CLIENT_SECRET')
    ],

    'google' => [
        'client_id' => env('GOOGLE_CLIENT_ID'),     
        'client_secret' => env('GOOGLE_CLIENT_SECRET'), 
        'redirect' => env('APP_URL').'/login/google/callback',
    ],

    'stripe' => [
        'key' => env('STRIPE_KEY'),     
        'secret' => env('STRIPE_SECRET'),     
    ],

    'paypal' => [
        // 'sandbox_key' => env('PAYPAL_SANDBOX_CLIENT_ID'),     
        // 'sandbox_secret' => env('PAYPAL_SANDBOX_SECRET'),     
        // 'live_key' => env('PAYPAL_LIVE_CLIENT_ID'),     
        // 'live_secret' => env('PAYPAL_LIVE_SECRET'),
        'key' => env('PAYPAL_CLIENT_ID'),
        'secret' => env('PAYPAL_SECRET_ID'),
        'sandbox' => env('PAYPAL_SANDBOX')
    ],

];
