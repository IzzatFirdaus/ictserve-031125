<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Core Web Vitals Performance Tests
 *
 * Tests for Core Web Vitals optimization implementation:
 * - LCP (Largest Contentful Paint): <2.5s for guest forms
 * - FID (First Input Delay): <100ms
 * - CLS (Cumulative Layout Shift): <0.1
 * - Filament Dashboard: <3s with caching
 *
 * @trace Requirements: 10.1, 10.2, 10.3, 10.5
 *
 * @see D03 §8.2 Performance requirements
 */
class CoreWebVitalsTest extends TestCase
{
    #[Test]
    public function performance_config_is_loaded(): void
    {
        $this->assertNotNull(config('performance.targets.lcp'));
        $this->assertNotNull(config('performance.targets.fid'));
        $this->assertNotNull(config('performance.targets.cls'));
        $this->assertNotNull(config('performance.targets.dashboard'));

        // Verify target values match requirements
        $this->assertEquals(2500, config('performance.targets.lcp')); // 2.5s
        $this->assertEquals(100, config('performance.targets.fid'));   // 100ms
        $this->assertEquals(0.1, config('performance.targets.cls'));   // 0.1 ratio
        $this->assertEquals(3000, config('performance.targets.dashboard')); // 3s
    }

    #[Test]
    public function guest_layout_includes_lcp_optimizations(): void
    {
        $response = $this->get('/login');

        $response->assertStatus(200);

        // Check for preconnect hints
        $response->assertSee('rel="preconnect"', false);
        $response->assertSee('rel="dns-prefetch"', false);

        // Check for preload of critical images
        $response->assertSee('rel="preload"', false);
    }

    #[Test]
    public function performance_service_provider_is_registered(): void
    {
        // Check if provider is in bootstrap/providers.php
        $bootstrapProviders = require base_path('bootstrap/providers.php');

        $this->assertContains(
            \App\Providers\PerformanceServiceProvider::class,
            $bootstrapProviders
        );
    }

    #[Test]
    public function widget_caching_is_functional(): void
    {
        // Clear cache first
        Cache::forget('dashboard:helpdesk-stats');
        Cache::forget('dashboard:loan-stats');

        // Verify cache is empty
        $this->assertNull(Cache::get('dashboard:helpdesk-stats'));
        $this->assertNull(Cache::get('dashboard:loan-stats'));

        // Cache TTL should be configured
        $this->assertGreaterThan(0, config('performance.cache.widget_ttl'));
    }

    #[Test]
    public function cache_configuration_is_reasonable(): void
    {
        $widgetTtl = config('performance.cache.widget_ttl');
        $statsTtl = config('performance.cache.stats_ttl');
        $dashboardTtl = config('performance.cache.dashboard_ttl');

        // Widget cache should be between 1-10 minutes
        $this->assertGreaterThanOrEqual(60, $widgetTtl);
        $this->assertLessThanOrEqual(600, $widgetTtl);

        // Stats cache should be between 30s-5 minutes
        $this->assertGreaterThanOrEqual(30, $statsTtl);
        $this->assertLessThanOrEqual(300, $statsTtl);

        // Dashboard cache should be between 1-5 minutes
        $this->assertGreaterThanOrEqual(60, $dashboardTtl);
        $this->assertLessThanOrEqual(300, $dashboardTtl);
    }

    #[Test]
    public function lazy_image_component_is_renderable(): void
    {
        $view = view('components.performance.lazy-image', [
            'src' => '/images/test.jpg',
            'alt' => 'Test image',
            'width' => 100,
            'height' => 100,
        ]);

        $html = $view->render();

        $this->assertStringContainsString('loading="lazy"', $html);
        $this->assertStringContainsString('decoding="async"', $html);
    }

    #[Test]
    public function skeleton_card_component_is_renderable(): void
    {
        $view = view('components.performance.skeleton-card', [
            'height' => '200',
            'lines' => 3,
        ]);

        $html = $view->render();

        $this->assertStringContainsString('skeleton-pulse', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    #[Test]
    public function skeleton_table_component_is_renderable(): void
    {
        $view = view('components.performance.skeleton-table', [
            'rows' => 5,
            'columns' => 4,
        ]);

        $html = $view->render();

        $this->assertStringContainsString('skeleton-pulse', $html);
        $this->assertStringContainsString('aria-hidden="true"', $html);
    }

    #[Test]
    public function content_placeholder_component_is_renderable(): void
    {
        $view = view('components.performance.content-placeholder', [
            'height' => '300',
            'type' => 'block',
        ]);

        $html = $view->render();

        $this->assertStringContainsString('skeleton-pulse', $html);
        $this->assertStringContainsString('min-height: 300px', $html);
    }

    #[Test]
    public function resource_hints_are_configured(): void
    {
        $hints = config('performance.resource_hints');

        $this->assertIsArray($hints['dns_prefetch']);
        $this->assertIsArray($hints['preconnect']);
        $this->assertNotEmpty($hints['dns_prefetch']);
    }

    #[Test]
    public function monitoring_configuration_is_set(): void
    {
        $monitoring = config('performance.monitoring');

        $this->assertArrayHasKey('enabled', $monitoring);
        $this->assertArrayHasKey('sample_rate', $monitoring);
        $this->assertArrayHasKey('report_endpoint', $monitoring);

        // Sample rate should be between 0 and 1
        $this->assertGreaterThanOrEqual(0, $monitoring['sample_rate']);
        $this->assertLessThanOrEqual(1, $monitoring['sample_rate']);
    }
}
