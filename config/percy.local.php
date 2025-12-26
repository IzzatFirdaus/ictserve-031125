<?php

declare(strict_types=1);

/**
 * Percy Visual Testing Configuration for Local Development
 * 
 * This file provides environment-specific overrides for Percy configuration
 * in the local development environment.
 * 
 * @package ICTServe
 * @version 3.6.1
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Local Development Overrides
    |--------------------------------------------------------------------------
    |
    | These settings override the main Percy configuration for local development.
    | This allows developers to have different settings without modifying
    | the main configuration file.
    |
    */

    // Enable Percy for local development if token is available
    'enabled' => !empty(env('PERCY_TOKEN')),

    // Local development specific settings
    'debug' => true,
    'upload_timeout' => 60,

    // Override snapshot settings for faster local testing
    'snapshot' => [
        'widths' => [768, 1280], // Fewer widths for faster local testing
        'min_height' => 800,
        'wait_for_timeout' => 500, // Faster timeout for local dev
        'network_idle_timeout' => 500,
    ],

    // Local development error handling
    'error_handling' => [
        'graceful_degradation' => true,
        'retry_attempts' => 1, // Fewer retries for local dev
        'retry_delay' => 2,
        'timeout' => 30,
        'log_errors' => true,
        'fail_on_error' => false,
    ],

    // Performance settings for local development
    'performance' => [
        'async_upload' => false, // Synchronous for easier debugging
        'cache_enabled' => false, // Disable cache for development
        'max_concurrent_uploads' => 1,
        'compress_snapshots' => false,
    ],
];
