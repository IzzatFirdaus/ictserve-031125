<?php

declare(strict_types=1);

/**
 * reCAPTCHA Enterprise Configuration
 *
 * @see https://cloud.google.com/recaptcha-enterprise/docs
 * @see Requirements 14.2 - Invisible reCAPTCHA on all guest forms
 */

return [
    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Enterprise Site Key
    |--------------------------------------------------------------------------
    |
    | The site key for reCAPTCHA Enterprise. This is used in the frontend
    | to render the invisible reCAPTCHA widget.
    |
    */
    'site_key' => env('RECAPTCHA_SITE_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | reCAPTCHA Enterprise API Key
    |--------------------------------------------------------------------------
    |
    | The API key for reCAPTCHA Enterprise verification. This is used
    | server-side to verify the reCAPTCHA token.
    |
    */
    'api_key' => env('RECAPTCHA_API_KEY', ''),

    /*
    |--------------------------------------------------------------------------
    | Google Cloud Project ID
    |--------------------------------------------------------------------------
    |
    | The Google Cloud project ID where reCAPTCHA Enterprise is configured.
    |
    */
    'project_id' => env('RECAPTCHA_PROJECT_ID', ''),

    /*
    |--------------------------------------------------------------------------
    | Minimum Score Threshold
    |--------------------------------------------------------------------------
    |
    | The minimum score (0.0 to 1.0) required to pass reCAPTCHA verification.
    | Higher scores indicate more likely human interaction.
    | Recommended: 0.5 for balanced security/usability
    |
    */
    'min_score' => env('RECAPTCHA_MIN_SCORE', 0.5),

    /*
    |--------------------------------------------------------------------------
    | Enable/Disable reCAPTCHA
    |--------------------------------------------------------------------------
    |
    | Set to false to disable reCAPTCHA verification (useful for testing).
    | In production, this should always be true.
    |
    */
    'enabled' => env('RECAPTCHA_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Actions
    |--------------------------------------------------------------------------
    |
    | Define action names for different form submissions.
    | These help identify the context of the reCAPTCHA verification.
    |
    */
    'actions' => [
        'helpdesk_submit' => 'helpdesk_ticket_submit',
        'loan_submit' => 'loan_application_submit',
        'status_check' => 'status_check',
        'registration' => 'user_registration',
        'login' => 'user_login',
    ],
];
