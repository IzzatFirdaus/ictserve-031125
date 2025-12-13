<?php

declare(strict_types=1);

/**
 * Performance Configuration
 *
 * Core Web Vitals optimization settings for ICTServe.
 *
 * Targets per Requirements 10.1-10.5:
 * - LCP (Largest Contentful Paint): <2.5s for guest forms
 * - FID (First Input Delay): <100ms
 * - CLS (Cumulative Layout Shift): <0.1
 * - TTFB (Time to First Byte): <600ms
 * - Filament Dashboard: <3s with caching
 *
 * @trace Requirements: 10.1, 10.2, 10.3, 10.4, 10.5
 *
 * @see D03 §8.2 Performance requirements
 * @see D12 §9 Performance optimization patterns
 */

return [
    /*
    |--------------------------------------------------------------------------
    | Core Web Vitals Targets
    |--------------------------------------------------------------------------
    |
    | Target values for Core Web Vitals metrics in milliseconds (except CLS).
    |
    */
    'targets' => [
        'lcp' => (int) env('PERFORMANCE_LCP_TARGET', 2500),    // 2.5 seconds
        'fid' => (int) env('PERFORMANCE_FID_TARGET', 100),     // 100ms
        'cls' => (float) env('PERFORMANCE_CLS_TARGET', 0.1),   // 0.1 ratio
        'ttfb' => (int) env('PERFORMANCE_TTFB_TARGET', 600),   // 600ms
        'dashboard' => (int) env('PERFORMANCE_DASHBOARD_TARGET', 3000), // 3 seconds
    ],

    /*
    |--------------------------------------------------------------------------
    | Image Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for image loading optimization.
    |
    */
    'images' => [
        'preload_critical' => env('PERFORMANCE_PRELOAD_IMAGES', true),
        'lazy_load' => env('PERFORMANCE_LAZY_LOAD_IMAGES', true),
        'webp_fallback' => env('PERFORMANCE_WEBP_FALLBACK', true),
        'placeholder_blur' => env('PERFORMANCE_PLACEHOLDER_BLUR', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | JavaScript Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for JavaScript loading optimization.
    |
    */
    'javascript' => [
        'defer_non_critical' => env('PERFORMANCE_DEFER_JS', true),
        'async_analytics' => env('PERFORMANCE_ASYNC_ANALYTICS', true),
        'code_splitting' => env('PERFORMANCE_CODE_SPLITTING', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | CSS Optimization
    |--------------------------------------------------------------------------
    |
    | Settings for CSS loading optimization.
    |
    */
    'css' => [
        'critical_inline' => env('PERFORMANCE_CRITICAL_CSS', true),
        'preload_fonts' => env('PERFORMANCE_PRELOAD_FONTS', true),
        'font_display_swap' => env('PERFORMANCE_FONT_SWAP', true),
    ],

    /*
    |--------------------------------------------------------------------------
    | Caching Configuration
    |--------------------------------------------------------------------------
    |
    | Cache TTL settings for various components.
    |
    */
    'cache' => [
        'widget_ttl' => (int) env('PERFORMANCE_WIDGET_CACHE_TTL', 300),      // 5 minutes
        'stats_ttl' => (int) env('PERFORMANCE_STATS_CACHE_TTL', 60),         // 1 minute
        'dashboard_ttl' => (int) env('PERFORMANCE_DASHBOARD_CACHE_TTL', 120), // 2 minutes
        'chart_ttl' => (int) env('PERFORMANCE_CHART_CACHE_TTL', 180),        // 3 minutes
    ],

    /*
    |--------------------------------------------------------------------------
    | Monitoring Configuration
    |--------------------------------------------------------------------------
    |
    | Settings for performance monitoring and reporting.
    |
    */
    'monitoring' => [
        'enabled' => env('PERFORMANCE_MONITORING_ENABLED', true),
        'sample_rate' => (float) env('PERFORMANCE_SAMPLE_RATE', 1.0), // 100% sampling
        'report_endpoint' => env('PERFORMANCE_REPORT_ENDPOINT', '/api/analytics/web-vitals'),
        'log_slow_requests' => env('PERFORMANCE_LOG_SLOW_REQUESTS', true),
        'slow_request_threshold' => (int) env('PERFORMANCE_SLOW_THRESHOLD', 1000), // 1 second
    ],

    /*
    |--------------------------------------------------------------------------
    | Resource Hints
    |--------------------------------------------------------------------------
    |
    | DNS prefetch and preconnect domains.
    |
    */
    'resource_hints' => [
        'dns_prefetch' => [
            'https://fonts.bunny.net',
            'https://fonts.gstatic.com',
        ],
        'preconnect' => [
            'https://fonts.bunny.net',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Content Visibility
    |--------------------------------------------------------------------------
    |
    | Settings for content-visibility CSS optimization.
    |
    */
    'content_visibility' => [
        'enabled' => env('PERFORMANCE_CONTENT_VISIBILITY', true),
        'intrinsic_height' => 500, // Estimated height for off-screen content
    ],
];
