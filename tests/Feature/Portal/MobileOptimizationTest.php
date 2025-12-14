<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Models\User;
use App\Services\MobileOptimizationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Mobile Optimization Test
 *
 * Tests mobile-specific optimizations for ICTServe v3.6.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-015 (Mobile Optimization)
 * @trace D12 §6.8 (Performance Optimization)
 *
 * @version 1.0.0
 */
class MobileOptimizationTest extends TestCase
{
    use RefreshDatabase;

    private MobileOptimizationService $mobileService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->mobileService = app(MobileOptimizationService::class);
    }

    // =========================================================================
    // Device Detection Tests
    // =========================================================================

    #[Test]
    #[DataProvider('mobileUserAgentProvider')]
    public function it_detects_mobile_devices(string $userAgent, bool $expectedMobile): void
    {
        $request = $this->createRequestWithUserAgent($userAgent);
        $result = $this->mobileService->isMobileDevice($request);

        $this->assertSame($expectedMobile, $result);
    }

    #[Test]
    #[DataProvider('tabletUserAgentProvider')]
    public function it_detects_tablet_devices(string $userAgent, bool $expectedTablet): void
    {
        $request = $this->createRequestWithUserAgent($userAgent);
        $result = $this->mobileService->isTabletDevice($request);

        $this->assertSame($expectedTablet, $result);
    }

    #[Test]
    public function it_returns_correct_device_type(): void
    {
        // Mobile
        $mobileRequest = $this->createRequestWithUserAgent('Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)');
        $this->assertSame('mobile', $this->mobileService->getDeviceType($mobileRequest));

        // Tablet
        $tabletRequest = $this->createRequestWithUserAgent('Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X)');
        $this->assertSame('tablet', $this->mobileService->getDeviceType($tabletRequest));

        // Desktop
        $desktopRequest = $this->createRequestWithUserAgent('Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36');
        $this->assertSame('desktop', $this->mobileService->getDeviceType($desktopRequest));
    }

    // =========================================================================
    // Breakpoint Configuration Tests
    // =========================================================================

    #[Test]
    public function it_provides_correct_breakpoint_configuration(): void
    {
        $breakpoints = $this->mobileService->getBreakpoints();

        $this->assertArrayHasKey('xs', $breakpoints);
        $this->assertArrayHasKey('sm', $breakpoints);
        $this->assertArrayHasKey('md', $breakpoints);
        $this->assertArrayHasKey('lg', $breakpoints);
        $this->assertArrayHasKey('xl', $breakpoints);
        $this->assertArrayHasKey('2xl', $breakpoints);

        // Verify MyDS column system
        $this->assertSame(4, $breakpoints['xs']['columns']); // Mobile: 4 columns
        $this->assertSame(8, $breakpoints['md']['columns']); // Tablet: 8 columns
        $this->assertSame(12, $breakpoints['xl']['columns']); // Desktop: 12 columns
    }

    #[Test]
    public function it_provides_bahasa_melayu_breakpoint_labels(): void
    {
        $breakpoints = $this->mobileService->getBreakpoints();

        $this->assertSame('Mudah Alih Kecil', $breakpoints['xs']['label']);
        $this->assertSame('Mudah Alih', $breakpoints['sm']['label']);
        $this->assertSame('Tablet', $breakpoints['md']['label']);
        $this->assertSame('Desktop', $breakpoints['xl']['label']);
    }

    // =========================================================================
    // Touch Target Tests
    // =========================================================================

    #[Test]
    public function it_provides_wcag_compliant_touch_target_config(): void
    {
        $config = $this->mobileService->getTouchTargetConfig();

        // WCAG 2.2 AA requires minimum 44×44px touch targets
        $this->assertSame(44, $config['minimum_size']);
        $this->assertGreaterThanOrEqual(44, $config['recommended_size']);
        $this->assertArrayHasKey('spacing', $config);
    }

    // =========================================================================
    // Image Optimization Tests
    // =========================================================================

    #[Test]
    #[DataProvider('deviceTypeProvider')]
    public function it_provides_optimized_image_sizes_for_device_type(string $deviceType): void
    {
        $sizes = $this->mobileService->getOptimizedImageSizes($deviceType);

        $this->assertArrayHasKey('thumbnail', $sizes);
        $this->assertArrayHasKey('small', $sizes);
        $this->assertArrayHasKey('medium', $sizes);
        $this->assertArrayHasKey('large', $sizes);

        // Mobile should have smaller sizes
        if ($deviceType === 'mobile') {
            $this->assertLessThan(800, $sizes['large']);
        }
    }

    #[Test]
    public function it_generates_responsive_srcset(): void
    {
        $srcset = $this->mobileService->generateSrcset('/images/test.jpg', 'mobile');

        $this->assertStringContainsString('?w=', $srcset);
        $this->assertStringContainsString('w', $srcset);
    }

    // =========================================================================
    // Pagination Tests
    // =========================================================================

    #[Test]
    #[DataProvider('deviceTypeProvider')]
    public function it_provides_appropriate_pagination_limits(string $deviceType): void
    {
        $limit = $this->mobileService->getPaginationLimit($deviceType);

        $this->assertGreaterThan(0, $limit);

        // Mobile should have smaller pagination
        if ($deviceType === 'mobile') {
            $this->assertLessThanOrEqual(15, $limit);
        }
    }

    // =========================================================================
    // Mobile Meta Tags Tests
    // =========================================================================

    #[Test]
    public function it_provides_mobile_meta_tags(): void
    {
        $metaTags = $this->mobileService->getMobileMetaTags();

        $this->assertArrayHasKey('viewport', $metaTags);
        $this->assertArrayHasKey('mobile-web-app-capable', $metaTags);
        $this->assertArrayHasKey('theme-color', $metaTags);

        // Verify viewport includes viewport-fit for notched devices
        $this->assertStringContainsString('viewport-fit=cover', $metaTags['viewport']);
    }

    // =========================================================================
    // Offline Configuration Tests
    // =========================================================================

    #[Test]
    public function it_provides_offline_configuration(): void
    {
        $config = $this->mobileService->getOfflineConfig();

        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('cache_name', $config);
        $this->assertArrayHasKey('cache_urls', $config);
        $this->assertArrayHasKey('fallback_page', $config);
    }

    // =========================================================================
    // Mobile Navigation Tests
    // =========================================================================

    #[Test]
    public function it_limits_mobile_navigation_items(): void
    {
        $items = [
            ['label' => 'Item 1', 'priority' => 1],
            ['label' => 'Item 2', 'priority' => 5],
            ['label' => 'Item 3', 'priority' => 3],
            ['label' => 'Item 4', 'priority' => 2],
            ['label' => 'Item 5', 'priority' => 4],
            ['label' => 'Item 6', 'priority' => 6],
        ];

        $mobileNav = $this->mobileService->getMobileNavigation($items, 4);

        $this->assertCount(4, $mobileNav);
    }

    // =========================================================================
    // Component Rendering Tests
    // =========================================================================

    #[Test]
    public function it_renders_bottom_navigation_component(): void
    {
        $view = $this->blade('<x-responsive.bottom-navigation :items="$items" />', [
            'items' => [
                ['label' => 'Laman Utama', 'href' => '/', 'icon' => 'home', 'active' => true],
                ['label' => 'Tiket', 'href' => '/helpdesk', 'icon' => 'ticket'],
            ],
        ]);

        $view->assertSee('Laman Utama');
        $view->assertSee('Tiket');
        $view->assertSee('aria-label');
        $view->assertSee('role="navigation"');
    }

    #[Test]
    public function it_renders_floating_action_button_component(): void
    {
        $view = $this->blade('<x-responsive.floating-action-button href="/create" label="Cipta Baharu" />');

        $view->assertSee('aria-label="Cipta Baharu"');
        $view->assertSee('href="/create"');
    }

    #[Test]
    public function it_renders_mobile_menu_component(): void
    {
        $view = $this->blade('<x-responsive.mobile-menu><nav>Menu Content</nav></x-responsive.mobile-menu>');

        $view->assertSee('Menu Content');
        $view->assertSee('aria-label');
        $view->assertSee('role="dialog"');
    }

    #[Test]
    public function it_renders_touch_input_component(): void
    {
        $view = $this->blade('<x-responsive.touch-input name="email" label="Alamat E-mel" type="email" required />');

        $view->assertSee('Alamat E-mel');
        $view->assertSee('type="email"');
        $view->assertSee('aria-required="true"');
        $view->assertSee('inputmode="email"');
    }

    // =========================================================================
    // WCAG Compliance Tests
    // =========================================================================

    #[Test]
    public function bottom_navigation_has_minimum_touch_targets(): void
    {
        $view = $this->blade('<x-responsive.bottom-navigation :items="$items" />', [
            'items' => [
                ['label' => 'Test', 'href' => '/', 'icon' => 'home'],
            ],
        ]);

        // Check for min-h-11 (44px) and min-w classes
        $view->assertSee('min-h-11');
        $view->assertSee('min-w-');
    }

    #[Test]
    public function floating_action_button_meets_touch_target_size(): void
    {
        $view = $this->blade('<x-responsive.floating-action-button href="/" label="Test" />');

        // FAB should be 56×56px (w-14 h-14)
        $view->assertSee('w-14');
        $view->assertSee('h-14');
    }

    #[Test]
    public function mobile_components_support_reduced_motion(): void
    {
        $view = $this->blade('<x-responsive.mobile-menu><nav>Content</nav></x-responsive.mobile-menu>');

        $view->assertSee('prefers-reduced-motion');
    }

    // =========================================================================
    // Middleware Integration Tests
    // =========================================================================

    #[Test]
    public function middleware_shares_device_type_with_views(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)'])
            ->get('/dashboard');

        $response->assertSuccessful();
    }

    // =========================================================================
    // Data Providers
    // =========================================================================

    /**
     * @return array<string, array{string, bool}>
     */
    public static function mobileUserAgentProvider(): array
    {
        return [
            'iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)', true],
            'Android Mobile' => ['Mozilla/5.0 (Linux; Android 10; SM-G960F) AppleWebKit/537.36 Mobile', true],
            'Windows Desktop' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', false],
            'Mac Desktop' => ['Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36', false],
        ];
    }

    /**
     * @return array<string, array{string, bool}>
     */
    public static function tabletUserAgentProvider(): array
    {
        return [
            'iPad' => ['Mozilla/5.0 (iPad; CPU OS 14_0 like Mac OS X) AppleWebKit/605.1.15', true],
            'Android Tablet' => ['Mozilla/5.0 (Linux; Android 10; SM-T860) AppleWebKit/537.36', true],
            'iPhone' => ['Mozilla/5.0 (iPhone; CPU iPhone OS 14_0 like Mac OS X)', false],
            'Desktop' => ['Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36', false],
        ];
    }

    /**
     * @return array<string, array{string}>
     */
    public static function deviceTypeProvider(): array
    {
        return [
            'mobile' => ['mobile'],
            'tablet' => ['tablet'],
            'desktop' => ['desktop'],
        ];
    }

    // =========================================================================
    // Helper Methods
    // =========================================================================

    private function createRequestWithUserAgent(string $userAgent): \Illuminate\Http\Request
    {
        return \Illuminate\Http\Request::create('/', 'GET', [], [], [], [
            'HTTP_USER_AGENT' => $userAgent,
        ]);
    }
}
