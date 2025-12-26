<?php

declare(strict_types=1);

/**
 * Percy Visual Testing Configuration for Staging Environment
 * 
 * This file provides environment-specific overrides for Percy configuration
 * in the staging environment.
 * 
 * @package ICTServe
 * @version 3.6.1
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Staging Environment Overrides
    |--------------------------------------------------------------------------
    |
    | These settings override the main Percy configuration for staging environment.
    | Optimized for pre-production testing and validation.
    |
    */

    // Enable Percy in staging for pre-production validation
    'enabled' => !empty(env('PERCY_TOKEN')),

    // Staging environment specific settings
    'debug' => false,
    'upload_timeout' => 180,

    // Staging snapshot settings - comprehensive but faster than production
    'snapshot' => [
        'widths' => [375, 768, 1280], // Reduced widths for faster staging tests
        'min_height' => 1024,
        'wait_for_timeout' => 1500,
        'network_idle_timeout' => 1000,
    ],

    // Staging error handling - balanced approach
    'error_handling' => [
        'graceful_degradation' => true, // Allow graceful degradation in staging
        'retry_attempts' => 3,
        'retry_delay' => 7,
        'timeout' => 90,
        'log_errors' => true,
        'fail_on_error' => env('PERCY_FAIL_ON_ERROR', false), // Don't fail staging by default
    ],

    // Performance settings for staging
    'performance' => [
        'async_upload' => true,
        'cache_enabled' => true,
        'cache_ttl' => 3600, // 1 hour
        'max_concurrent_uploads' => 4,
        'compress_snapshots' => true,
    ],

    // Staging validation settings
    'validation' => [
        'pre_production_checks' => true,
        'performance_validation' => true,
        'accessibility_validation' => true,
        'cross_browser_validation' => true,
    ],

    // Staging monitoring
    'monitoring' => [
        'performance_tracking' => true,
        'error_alerting' => false, // Reduced alerting in staging
        'usage_analytics' => true,
        'cost_tracking' => false,
    ],
];
