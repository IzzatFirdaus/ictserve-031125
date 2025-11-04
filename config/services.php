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

    'resend' => [
        'key' => env('RESEND_KEY'),
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
    | HRMIS Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for HRMIS (Human Resource Management Information System)
    | integration for user data synchronization and approval authority mapping.
    |
    | @see D03-FR-002.1 Grade-based approval matrix
    | @see D03-FR-006.1 Automated approval routing
    |
    */

    'hrmis' => [
        'base_url' => env('HRMIS_BASE_URL', 'https://hrmis.motac.gov.my'),
        'api_key' => env('HRMIS_API_KEY', ''),
        'timeout' => env('HRMIS_TIMEOUT', 30),
        'cache_minutes' => env('HRMIS_CACHE_MINUTES', 60),
        'enabled' => env('HRMIS_ENABLED', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Calendar Integration Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for calendar system integration (Outlook/Google Calendar)
    | for booking management and scheduling.
    |
    | @see D03-FR-006.4 Calendar integration
    |
    */

    'calendar' => [
        'provider' => env('CALENDAR_PROVIDER', 'outlook'), // outlook or google
        'outlook' => [
            'client_id' => env('OUTLOOK_CLIENT_ID', ''),
            'client_secret' => env('OUTLOOK_CLIENT_SECRET', ''),
            'tenant_id' => env('OUTLOOK_TENANT_ID', ''),
            'redirect_uri' => env('OUTLOOK_REDIRECT_URI', ''),
        ],
        'google' => [
            'client_id' => env('GOOGLE_CALENDAR_CLIENT_ID', ''),
            'client_secret' => env('GOOGLE_CALENDAR_CLIENT_SECRET', ''),
            'redirect_uri' => env('GOOGLE_CALENDAR_REDIRECT_URI', ''),
        ],
        'enabled' => env('CALENDAR_INTEGRATION_ENABLED', false),
    ],

];
