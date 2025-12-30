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


    'google' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect' => '',
    ],

    'facebook' => [
        'client_id' => '',
        'client_secret' => '',
        'redirect' => '',
    ],

'sms' => [
    'username' => env('SMS_USERNAME'),
    'password' => env('SMS_PASSWORD'),
    'sender'   => env('SMS_SENDER'),
],

'shiprocket' => [
    'email' => env('SHIPROCKET_EMAIL'),
    'password' => env('SHIPROCKET_PASSWORD'),
    'base_url' => env('SHIPROCKET_BASE_URL'),
    'pickup' => env('SHIPROCKET_PICKUP'),
],

'zoho' => [
    'client_id'     => env('ZOHO_CLIENT_ID'),
    'client_secret' => env('ZOHO_CLIENT_SECRET'),
    'accounts_url'  => env('ZOHO_ACCOUNTS_URL', 'https://accounts.zoho.in'),
    'redirect_uri'  => env('ZOHO_REDIRECT_URI'),
    'refresh_token' => env('ZOHO_REFRESH_TOKEN'),
],
];
