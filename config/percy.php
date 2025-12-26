<?php

declare(strict_types=1);

/**
 * Percy Visual Testing Configuration for ICTServe v3.6.1
 *
 * This configuration file manages Percy visual testing settings
 * compatible with Laravel 12.43.1 and the ICTServe technology stack.
 *
 * @version 3.6.1
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Percy Authentication
    |--------------------------------------------------------------------------
    |
    | Percy token for authentication with Percy services.
    | This should be set in your environment variables.
    |
    */
    'token' => env('PERCY_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Percy Project Configuration
    |--------------------------------------------------------------------------
    |
    | Project name and basic configuration for Percy builds.
    |
    */
    'project' => env('PERCY_PROJECT', 'ictserve'),
    'enabled' => env('PERCY_ENABLED', true),

    /*
    |--------------------------------------------------------------------------
    | Build Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for Percy builds and parallel execution.
    |
    */
    'branch' => env('PERCY_BRANCH', 'develop'),
    'target_branch' => env('PERCY_TARGET_BRANCH', 'develop'),
    'parallel_nonce' => env('PERCY_PARALLEL_NONCE'),
    'parallel_total' => env('PERCY_PARALLEL_TOTAL', 1),

    /*
    |--------------------------------------------------------------------------
    | Snapshot Configuration
    |--------------------------------------------------------------------------
    |
    | Default settings for visual snapshots across the application.
    |
    */
    'snapshot' => [
        // Responsive breakpoints for ICTServe v3.6.1
        'widths' => [375, 768, 1024, 1280, 1920],
        'min_height' => 1024,

        // Default CSS to hide dynamic content
        'percy_css' => [
            '.dynamic-timestamp { display: none !important; }',
            '.loading-spinner { visibility: hidden !important; }',
            '.skeleton-loader { display: none !important; }',
            '.language-switcher { display: none !important; }', // v3.6.0+ Bahasa Melayu only
            '.user-avatar { visibility: hidden !important; }',
            '.last-login-time { display: none !important; }',
            '.notification-badge { display: none !important; }',
            '.realtime-counter { display: none !important; }',
            '.validation-message { display: none !important; }',
            '[wire\\:loading] { display: none !important; }',
            '.wire-loading { display: none !important; }',
            '.fi-loading { display: none !important; }',
            '.fi-notification { display: none !important; }',
            '*:focus { outline: 2px solid #3b82f6 !important; }',
        ],

        // JavaScript and timing configuration
        'enable_javascript' => true,
        'wait_for_timeout' => 1000,
        'network_idle_timeout' => 750,
    ],

    /*
    |--------------------------------------------------------------------------
    | ICTServe v3.6.1 Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration specific to ICTServe's True Hybrid Architecture
    | and technology stack.
    |
    */
    'ictserve' => [
        // True Hybrid Architecture support
        'hybrid_architecture' => [
            'guest_selectors' => ['.guest-form', '.guest-status', '.guest-workflow'],
            'authenticated_selectors' => ['.dashboard', '.profile', '.user-menu'],
            'admin_selectors' => ['.filament-admin', '.admin-panel', '.fi-sidebar'],
        ],

        // Bahasa Melayu interface support (v3.6.0+)
        'bahasa_melayu' => [
            'validate_language' => true,
            'exclude_language_switcher' => true,
            'interface_version' => '3.6.0+',
        ],

        // Technology stack versions
        'technology_stack' => [
            'laravel' => '12.43.1',
            'livewire' => '3.7.3',
            'filament' => '4.3.1',
            'playwright' => '1.56.1',
            'tailwind' => '4.1.18',
        ],

        // WCAG 2.2 AA compliance
        'accessibility' => [
            'wcag_level' => 'AA',
            'wcag_version' => '2.2',
            'validate_contrast' => true,
            'validate_focus_indicators' => true,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Environment-Specific Configuration
    |--------------------------------------------------------------------------
    |
    | Different settings for different environments.
    |
    */
    'environments' => [
        'local' => [
            'enabled' => env('PERCY_ENABLED', false), // Disabled by default for local dev
            'debug' => true,
            'upload_timeout' => 60,
        ],
        'testing' => [
            'enabled' => true,
            'debug' => false,
            'upload_timeout' => 120,
        ],
        'staging' => [
            'enabled' => true,
            'debug' => false,
            'upload_timeout' => 180,
        ],
        'production' => [
            'enabled' => env('PERCY_ENABLED', false), // Explicit control in production
            'debug' => false,
            'upload_timeout' => 300,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Error Handling Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration for error handling and graceful degradation.
    |
    */
    'error_handling' => [
        'graceful_degradation' => true,
        'retry_attempts' => 3,
        'retry_delay' => 5, // seconds
        'timeout' => 60, // seconds
        'log_errors' => true,
        'fail_on_error' => env('PERCY_FAIL_ON_ERROR', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Performance Configuration
    |--------------------------------------------------------------------------
    |
    | Settings to optimize Percy performance and minimize test impact.
    |
    */
    'performance' => [
        'async_upload' => true,
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 hour
        'max_concurrent_uploads' => 3,
        'compress_snapshots' => true,
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Messages
    |--------------------------------------------------------------------------
    |
    | Error messages in Bahasa Melayu for configuration validation.
    |
    */
    'messages' => [
        'token_missing' => 'Token Percy tidak ditemui. Sila tetapkan PERCY_TOKEN dalam pembolehubah persekitaran.',
        'token_invalid' => 'Token Percy tidak sah. Sila semak token anda.',
        'project_missing' => 'Nama projek Percy tidak ditemui. Sila tetapkan PERCY_PROJECT.',
        'config_invalid' => 'Konfigurasi Percy tidak sah. Sila semak tetapan anda.',
        'service_unavailable' => 'Perkhidmatan Percy tidak tersedia. Ujian akan diteruskan tanpa tangkapan visual.',
        'upload_failed' => 'Gagal memuat naik tangkapan ke Percy. Sila cuba lagi.',
        'build_failed' => 'Gagal mencipta build Percy. Sila semak konfigurasi anda.',
    ],
];
