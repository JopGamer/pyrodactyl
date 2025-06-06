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
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'resend' => [
        'key' => env('RESEND_KEY'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'openid' => [
        'client_id' => config('openid.client_id') ?: env('OPENID_CLIENT_ID'),
        'client_secret' => config('openid.client_secret') ?: env('OPENID_CLIENT_SECRET'),
        'redirect' => config('openid.redirect') ?: env('OPENID_REDIRECT_URI', env('APP_URL') . '/auth/openid/callback'),
        'issuer' => config('openid.issuer') ?: env('OPENID_ISSUER'),
        'discovery_url' => config('openid.discovery_url') ?: env('OPENID_DISCOVERY_URL'),
        'disable_registration' => config('openid.disable_registration', false) ?: env('OPENID_DISABLE_REGISTRATION', false),
    ],
];
