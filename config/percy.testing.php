<?php

declare(strict_types=1);

/**
 * Percy Visual Testing Configuration for Testing Environment
 * 
 * This file provides environment-specific overrides for Percy configuration
 * in the testing environment.
 * 
 * @package ICTServe
 * @version 3.6.1
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Testing Environment Overrides
    |--------------------------------------------------------------------------
    |
    | These settings override the main Percy configuration for testing environment.
    | Optimized for CI/CD pipelines and automated testing.
    |
    */

    // Always enable Percy in testing if token is available
    'enabled' => !empty(env('PERCY_TOKEN')),

    // Testing environment specific settings
    'debug' => false,
    'upload_timeout' => 120,

    // Testing snapshot settings
    'snapshot' => [
        'widths' => [375, 768, 1280, 1920], // Full responsive testing
        'min_height' => 1024,
        'wait_for_timeout' => 1000,
        'network_idle_timeout' => 750,
    ],

    // Testing error handling - more strict
    'error_handling' => [
        'graceful_degradation' => false, // Fail tests if Percy fails
        'retry_attempts' => 3,
        'retry_delay' => 5,
        'timeout' => 60,
        'log_errors' => true,
        'fail_on_error' => env('PERCY_FAIL_ON_ERROR', true), // Fail tests on Percy errors
    ],

    // Performance settings for testing
    'performance' => [
        'async_upload' => true,
        'cache_enabled' => true,
        'cache_ttl' => 1800, // 30 minutes
        'max_concurrent_uploads' => 3,
        'compress_snapshots' => true,
    ],

    // CI/CD specific settings
    'ci_cd' => [
        'parallel_execution' => true,
        'build_timeout' => 600, // 10 minutes
        'snapshot_timeout' => 30,
        'max_build_size' => 100, // Maximum snapshots per build
    ],
];
