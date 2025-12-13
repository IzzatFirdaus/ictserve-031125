<?php

declare(strict_types=1);

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Performance Service Provider
 *
 * Registers performance optimization services and Blade directives
 * for Core Web Vitals optimization.
 *
 * Targets:
 * - LCP (Largest Contentful Paint): <2.5s for guest forms
 * - FID (First Input Delay): <100ms
 * - CLS (Cumulative Layout Shift): <0.1
 * - Filament Dashboard: <3s with caching
 *
 * @trace Requirements: 10.1, 10.2, 10.3, 10.5
 *
 * @see D03 §8.2 Performance requirements
 * @see D12 §9 Performance optimization patterns
 */
class PerformanceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton('performance.config', fn () => [
            'lcp_target' => 2500, // 2.5 seconds in ms
            'fid_target' => 100,  // 100ms
            'cls_target' => 0.1,  // 0.1 ratio
            'ttfb_target' => 600, // 600ms
            'dashboard_target' => 3000, // 3 seconds in ms
            'widget_cache_ttl' => 300, // 5 minutes
            'stats_cache_ttl' => 60, // 1 minute for real-time stats
        ]);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerBladeDirectives();
        $this->registerViewComposers();
    }

    /**
     * Register Blade directives for performance optimization
     */
    protected function registerBladeDirectives(): void
    {
        // Preload critical resources
        Blade::directive('preloadCritical', function () {
            return <<<'HTML'
<?php
$criticalResources = [
    ['href' => asset('images/motac-logo.jpeg'), 'as' => 'image', 'type' => 'image/jpeg'],
    ['href' => asset('images/jata-negara.svg'), 'as' => 'image', 'type' => 'image/svg+xml'],
];
foreach ($criticalResources as $resource) {
    echo '<link rel="preload" href="' . e($resource['href']) . '" as="' . e($resource['as']) . '" type="' . e($resource['type']) . '">' . "\n";
}
?>
HTML;
        });

        // DNS prefetch for external resources
        Blade::directive('dnsPrefetch', function () {
            return <<<'HTML'
<?php
$domains = [
    'https://fonts.bunny.net',
    'https://fonts.gstatic.com',
];
foreach ($domains as $domain) {
    echo '<link rel="dns-prefetch" href="' . e($domain) . '">' . "\n";
    echo '<link rel="preconnect" href="' . e($domain) . '" crossorigin>' . "\n";
}
?>
HTML;
        });

        // Lazy load images with native loading attribute
        Blade::directive('lazyImage', function (string $expression) {
            return "<?php echo app('App\View\Components\Performance\LazyImage')->render({$expression}); ?>";
        });

        // Critical CSS inline
        Blade::directive('criticalCss', function () {
            return <<<'HTML'
<style>
/* Critical CSS for above-the-fold content */
.skeleton-pulse{animation:skeleton-pulse 2s cubic-bezier(.4,0,.6,1) infinite}
@keyframes skeleton-pulse{0%,100%{opacity:1}50%{opacity:.5}}
.content-placeholder{min-height:200px;background:linear-gradient(90deg,#f0f0f0 25%,#e0e0e0 50%,#f0f0f0 75%);background-size:200% 100%;animation:shimmer 1.5s infinite}
@keyframes shimmer{0%{background-position:200% 0}100%{background-position:-200% 0}}
</style>
HTML;
        });

        // Reserve space for dynamic content (CLS prevention)
        Blade::directive('reserveSpace', function (string $expression) {
            $params = explode(',', str_replace(['\'', '"', ' '], '', $expression));
            $height = $params[0] ?? '200';
            $class = $params[1] ?? '';

            return "<div style=\"min-height: {$height}px;\" class=\"{$class} content-placeholder skeleton-pulse\"></div>";
        });
    }

    /**
     * Register view composers for performance data
     */
    protected function registerViewComposers(): void
    {
        // Add performance hints to all views
        view()->composer('*', function ($view) {
            $view->with('performanceHints', [
                'preloadImages' => config('performance.preload_images', true),
                'lazyLoadImages' => config('performance.lazy_load_images', true),
                'deferNonCriticalJs' => config('performance.defer_js', true),
            ]);
        });
    }
}
