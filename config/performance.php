<?php

declare(strict_types=1);

/**
 * Performance Configuration
 *
 * Core Web Vitals targets and performance optimization settings.
 *
 * @see D12 UI/UX Design Guide - Performance Requirements
 * @see D13 UI/UX Frontend Framework - Performance Monitoring
 *
 * @requirements R08 Performance Optimization and Core Web Vitals
 *
 * @version 1.0.0
 */

return [

    /*
    |--------------------------------------------------------------------------
    | Core Web Vitals Targets
    |--------------------------------------------------------------------------
    |
    | Target values for Core Web Vitals metrics.
    | These are used for monitoring and alerting.
    |
    */

    'core_web_vitals' => [
        'lcp' => env('PERFORMANCE_LCP_TARGET', 2500),    // Largest Contentful Paint (ms)
        'fid' => env('PERFORMANCE_FID_TARGET', 100),     // First Input Delay (ms)
        'cls' => env('PERFORMANCE_CLS_TARGET', 0.1),     // Cumulative Layout Shift
        'ttfb' => env('PERFORMANCE_TTFB_TARGET', 600),   // Time to First Byte (ms)
    ],

    /*
    |--------------------------------------------------------------------------
    | Lighthouse Score Targets
    |--------------------------------------------------------------------------
    |
    | Target scores for Lighthouse audits.
    |
    */

    'lighthouse_targets' => [
        'performance' => env('LIGHTHOUSE_PERFORMANCE_TARGET', 90),
        'accessibility' => env('LIGHTHOUSE_ACCESSIBILITY_TARGET', 100),
        'best_practices' => env('LIGHTHOUSE_BEST_PRACTICES_TARGET', 100),
        'seo' => env('LIGHTHOUSE_SEO_TARGET', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Cache TTL settings for various data types.
    |
    */

    'cache' => [
        'dashboard_stats' => env('CACHE_DASHBOARD_STATS_TTL', 300),      // 5 minutes
        'asset_availability' => env('CACHE_ASSET_AVAILABILITY_TTL', 300), // 5 minutes
        'user_permissions' => env('CACHE_USER_PERMISSIONS_TTL', 3600),   // 1 hour
        'system_settings' => env('CACHE_SYSTEM_SETTINGS_TTL', 86400),    // 24 hours
        'static_content' => env('CACHE_STATIC_CONTENT_TTL', 604800),     // 7 days
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for image optimization and lazy loading.
    |
    */

    'images' => [
        'max_width' => env('IMAGE_MAX_WIDTH', 1920),
        'max_height' => env('IMAGE_MAX_HEIGHT', 1080),
        'quality' => env('IMAGE_QUALITY', 85),
        'format' => env('IMAGE_FORMAT', 'webp'),
        'lazy_loading' => env('IMAGE_LAZY_LOADING', true),
        'placeholder_blur' => env('IMAGE_PLACEHOLDER_BLUR', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for CSS and JavaScript optimization.
    |
    */

    'assets' => [
        'minify_css' => env('ASSETS_MINIFY_CSS', true),
        'minify_js' => env('ASSETS_MINIFY_JS', true),
        'code_splitting' => env('ASSETS_CODE_SPLITTING', true),
        'tree_shaking' => env('ASSETS_TREE_SHAKING', true),
        'gzip_compression' => env('ASSETS_GZIP_COMPRESSION', true),
        'brotli_compression' => env('ASSETS_BROTLI_COMPRESSION', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for database query optimization.
    |
    */

    'database' => [
        'query_logging' => env('DB_QUERY_LOGGING', false),
        'slow_query_threshold' => env('DB_SLOW_QUERY_THRESHOLD', 1000), // ms
        'connection_pooling' => env('DB_CONNECTION_POOLING', true),
        'read_replica' => env('DB_READ_REPLICA', false),
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for performance monitoring and alerting.
    |
    */

    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING_ENABLED', true),
        'sample_rate' => env('PERFORMANCE_SAMPLE_RATE', 0.1), // 10% of requests
        'alert_threshold' => env('PERFORMANCE_ALERT_THRESHOLD', 1.5), // 1.5x target
        'report_endpoint' => env('PERFORMANCE_REPORT_ENDPOINT', '/api/analytics/web-vitals'),
    ],

];
