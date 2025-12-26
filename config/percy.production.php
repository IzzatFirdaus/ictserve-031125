<?php

declare(strict_types=1);

/**
 * Percy Visual Testing Configuration for Production Environment
 * 
 * This file provides environment-specific overrides for Percy configuration
 * in the production environment.
 * 
 * @package ICTServe
 * @version 3.6.1
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Production Environment Overrides
    |--------------------------------------------------------------------------
    |
    | These settings override the main Percy configuration for production environment.
    | Optimized for production deployments with strict error handling.
    |
    */

    // Percy is disabled by default in production for security
    'enabled' => env('PERCY_ENABLED', false),

    // Production environment specific settings
    'debug' => false,
    'upload_timeout' => 300,

    // Production snapshot settings - comprehensive testing
    'snapshot' => [
        'widths' => [375, 768, 1024, 1280, 1920], // Full responsive testing
        'min_height' => 1024,
        'wait_for_timeout' => 2000, // Longer timeout for production stability
        'network_idle_timeout' => 1000,
    ],

    // Production error handling - very strict
    'error_handling' => [
        'graceful_degradation' => false, // Never ignore errors in production
        'retry_attempts' => 5, // More retries for production reliability
        'retry_delay' => 10,
        'timeout' => 120,
        'log_errors' => true,
        'fail_on_error' => true, // Always fail on Percy errors in production
    ],

    // Performance settings for production
    'performance' => [
        'async_upload' => true,
        'cache_enabled' => true,
        'cache_ttl' => 7200, // 2 hours
        'max_concurrent_uploads' => 5,
        'compress_snapshots' => true,
    ],

    // Production security settings
    'security' => [
        'require_https' => true,
        'validate_ssl' => true,
        'token_encryption' => true,
        'audit_logging' => true,
    ],

    // Production monitoring
    'monitoring' => [
        'performance_tracking' => true,
        'error_alerting' => true,
        'usage_analytics' => true,
        'cost_tracking' => true,
    ],
];
