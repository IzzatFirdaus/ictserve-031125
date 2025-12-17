<?php

declare(strict_types=1);

use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\ValidateCsrfToken;
use Laravel\Sanctum\Http\Middleware\AuthenticateSession;

/**
 * Laravel Sanctum Configuration for ICTServe API Authentication
 *
 * This configuration supports the True Hybrid Architecture v3.5.0
 * API authentication requirements per D03 SRS-API-001.
 *
 * @see Requirements 37.1, 37.2 - API Token Authentication
 * @see .kiro/specs/ictserve-update-v3/design.md - ApiTokenService interface
 */
return [

    /*
    |--------------------------------------------------------------------------
    | Stateful Domains
    |--------------------------------------------------------------------------
    |
    | Requests from the following domains / hosts will receive stateful API
    | authentication cookies. Typically, these should include your local
    | and production domains which access your API via a frontend SPA.
    |
    | ICTServe Configuration:
    | - Local development: localhost, 127.0.0.1
    | - Production: ictserve.motac.gov.my (configured via SANCTUM_STATEFUL_DOMAINS)
    |
    */

    'stateful' => explode(',', env('SANCTUM_STATEFUL_DOMAINS', sprintf(
        '%s%s%s',
        'localhost,localhost:3000,localhost:8000,127.0.0.1,127.0.0.1:8000,::1',
        env('APP_URL') ? ','.parse_url(env('APP_URL'), PHP_URL_HOST) : '',
        env('APP_URL') && parse_url(env('APP_URL'), PHP_URL_PORT) ? ':'.parse_url(env('APP_URL'), PHP_URL_PORT) : '',
    ))),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Guards
    |--------------------------------------------------------------------------
    |
    | This array contains the authentication guards that will be checked when
    | Sanctum is trying to authenticate a request. If none of these guards
    | are able to authenticate the request, Sanctum will use the bearer
    | token that's present on an incoming request for authentication.
    |
    */

    'guard' => ['web'],

    /*
    |--------------------------------------------------------------------------
    | Expiration Minutes
    |--------------------------------------------------------------------------
    |
    | This value controls the number of minutes until an issued token will be
    | considered expired. This will override any values set in the token's
    | "expires_at" attribute, but first-party sessions are not affected.
    |
    | ICTServe Configuration:
    | - Default: 30 days (43200 minutes) per Requirements 37.1, 37.2
    | - Can be overridden via SANCTUM_TOKEN_EXPIRATION_MINUTES env variable
    | - Individual tokens can have custom expiration via ApiTokenService
    |
    */

    'expiration' => env('SANCTUM_TOKEN_EXPIRATION_MINUTES', 43200), // 30 days default

    /*
    |--------------------------------------------------------------------------
    | Token Prefix
    |--------------------------------------------------------------------------
    |
    | Sanctum can prefix new tokens in order to take advantage of numerous
    | security scanning initiatives maintained by open source platforms
    | that notify developers if they commit tokens into repositories.
    |
    | ICTServe Configuration:
    | - Prefix: 'ict_' to identify ICTServe API tokens
    | - Helps with secret scanning and token identification
    |
    | See: https://docs.github.com/en/code-security/secret-scanning/about-secret-scanning
    |
    */

    'token_prefix' => env('SANCTUM_TOKEN_PREFIX', 'ict_'),

    /*
    |--------------------------------------------------------------------------
    | Sanctum Middleware
    |--------------------------------------------------------------------------
    |
    | When authenticating your first-party SPA with Sanctum you may need to
    | customize some of the middleware Sanctum uses while processing the
    | request. You may change the middleware listed below as required.
    |
    */

    'middleware' => [
        'authenticate_session' => AuthenticateSession::class,
        'encrypt_cookies' => EncryptCookies::class,
        'validate_csrf_token' => ValidateCsrfToken::class,
    ],

    /*
    |--------------------------------------------------------------------------
    | Token Abilities (ICTServe Custom)
    |--------------------------------------------------------------------------
    |
    | Define the available token abilities for fine-grained API access control.
    | These abilities are used by the ApiTokenService and enforced via
    | the 'ability' middleware on API routes.
    |
    | @see Requirements 37.3 - Token Abilities
    |
    */

    'abilities' => [
        'read:tickets' => 'Read helpdesk tickets',
        'write:tickets' => 'Create and update helpdesk tickets',
        'read:loans' => 'Read loan applications',
        'write:loans' => 'Create and update loan applications',
        'read:assets' => 'Read asset information',
        'write:assets' => 'Create and update assets',
        'admin:all' => 'Full administrative access',
    ],

    /*
    |--------------------------------------------------------------------------
    | Default Token Abilities (ICTServe Custom)
    |--------------------------------------------------------------------------
    |
    | Default abilities assigned to new tokens when no specific abilities
    | are provided during token creation.
    |
    */

    'default_abilities' => [
        'read:tickets',
        'read:loans',
        'read:assets',
    ],

];
