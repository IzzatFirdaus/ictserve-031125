<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\File;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Frontend Asset Loading Performance Tests
 *
 * Tests frontend asset compilation, loading performance, and optimization.
 * Validates Vite build output, asset compression, and Core Web Vitals compliance.
 *
 * @see D03-FR-007.2 Performance requirements
 * @see D03-FR-014.1 Core Web Vitals targets (LCP <2.5s)
 * @see D03-FR-015.4 Frontend optimization
 * @see Task 7.5 - Frontend asset loading performance tests
 */
class FrontendAssetPerformanceTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test Vite manifest exists and is valid
     *
     * @see D03-FR-015.4 Asset bundling
     */
    #[Test]
    public function vite_manifest_exists_and_valid(): void
    {
        $manifestPath = public_path('build/manifest.json');

        $this->assertFileExists($manifestPath, 'Vite manifest file does not exist. Run: npm run build');

        $manifest = json_decode(File::get($manifestPath), true);

        $this->assertIsArray($manifest, 'Vite manifest is not valid JSON');
        $this->assertNotEmpty($manifest, 'Vite manifest is empty');

        // Verify critical assets are present
        $this->assertArrayHasKey('resources/js/app.js', $manifest, 'Main JS entry point missing from manifest');
        $this->assertArrayHasKey('resources/css/app.css', $manifest, 'Main CSS entry point missing from manifest');
    }

    /**
     * Test compiled assets exist in build directory
     *
     * @see D03-FR-015.4 Asset compilation
     */
    #[Test]
    public function compiled_assets_exist(): void
    {
        $buildPath = public_path('build');

        $this->assertDirectoryExists($buildPath, 'Build directory does not exist. Run: npm run build');

        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        // Verify each asset in manifest actually exists
        foreach ($manifest as $entry) {
            if (isset($entry['file'])) {
                $assetPath = public_path('build/'.$entry['file']);
                $this->assertFileExists($assetPath, "Asset file missing: {$entry['file']}");
            }
        }
    }

    /**
     * Test CSS file size is optimized
     *
     * @see D03-FR-007.2 Asset optimization
     * @see D03-FR-014.1 LCP performance target
     */
    #[Test]
    public function css_file_size_optimization(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        $cssEntry = $manifest['resources/css/app.css'] ?? null;
        $this->assertNotNull($cssEntry, 'CSS entry not found in manifest');

        $cssPath = public_path('build/'.$cssEntry['file']);
        $this->assertFileExists($cssPath);

        $cssSize = filesize($cssPath);
        $this->assertNotFalse($cssSize);

        // CSS should be minified and optimized (< 500KB for good LCP)
        $maxCssSize = 500 * 1024; // 500KB
        $this->assertLessThan(
            $maxCssSize,
            $cssSize,
            "CSS file too large ({$cssSize} bytes). Should be < {$maxCssSize} bytes for optimal LCP"
        );
    }

    /**
     * Test JavaScript file size is optimized
     *
     * @see D03-FR-007.2 Asset optimization
     * @see D03-FR-014.1 FID performance target
     */
    #[Test]
    public function javascript_file_size_optimization(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        $jsEntry = $manifest['resources/js/app.js'] ?? null;
        $this->assertNotNull($jsEntry, 'JavaScript entry not found in manifest');

        $jsPath = public_path('build/'.$jsEntry['file']);
        $this->assertFileExists($jsPath);

        $jsSize = filesize($jsPath);
        $this->assertNotFalse($jsSize);

        // JavaScript should be minified and tree-shaken (< 1MB for good performance)
        $maxJsSize = 1024 * 1024; // 1MB
        $this->assertLessThan(
            $maxJsSize,
            $jsSize,
            "JavaScript file too large ({$jsSize} bytes). Should be < {$maxJsSize} bytes for optimal FID"
        );
    }

    /**
     * Test assets have cache-busting hashes
     *
     * @see D03-FR-007.2 Caching strategy
     */
    #[Test]
    public function assets_have_cache_busting_hashes(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        foreach ($manifest as $source => $entry) {
            if (isset($entry['file'])) {
                // Verify file has hash in name (e.g., app-abc123.js, app-AbC123.js, app-Me0AgjMC.css)
                // Vite uses base64-like hashes which can include letters and numbers
                $this->assertMatchesRegularExpression(
                    '/\-[a-zA-Z0-9]{8,}\.(js|css)$/',
                    $entry['file'],
                    "Asset {$source} does not have cache-busting hash: {$entry['file']}"
                );
            }
        }
    }

    /**
     * Test critical CSS is inlined or preloaded
     *
     * @see D03-FR-014.1 LCP optimization
     */
    #[Test]
    public function critical_css_optimization(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Check for Vite preload directives or inline critical CSS
        $hasPreload = str_contains($content, 'rel="preload"') ||
            str_contains($content, 'rel="modulepreload"');

        if (! $hasPreload) {
            $this->markTestIncomplete(
                'No asset preloading detected. Consider adding critical CSS preloading for better LCP'
            );
        }

        $this->assertTrue($hasPreload);
    }

    /**
     * Test asset loading performance on homepage
     *
     * @see D03-FR-014.1 Core Web Vitals compliance
     */
    #[Test]
    public function homepage_asset_loading_performance(): void
    {
        $startTime = microtime(true);

        $response = $this->get('/');

        $loadTime = microtime(true) - $startTime;

        $response->assertOk();

        // TTFB should be < 600ms
        $this->assertLessThan(0.6, $loadTime, 'Homepage TTFB too high (> 600ms)');

        // Verify assets are referenced
        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertStringContainsString('build/', $content, 'No Vite build assets referenced');
    }

    /**
     * Test guest loan application page asset loading
     *
     * @see D03-FR-014.1 LCP target for interactive pages
     */
    #[Test]
    public function guest_loan_application_asset_loading(): void
    {
        $startTime = microtime(true);

        $response = $this->get('/loan-application');

        $loadTime = microtime(true) - $startTime;

        $response->assertOk();

        // Page should load quickly for good LCP
        $this->assertLessThan(1.0, $loadTime, 'Loan application page loading too slow');

        // Verify Livewire assets are loaded
        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertStringContainsString('livewire', $content, 'Livewire assets not loaded');
    }

    /**
     * Test authenticated dashboard asset loading
     *
     * @see D03-FR-011.1 Dashboard performance
     */
    #[Test]
    public function authenticated_dashboard_asset_loading(): void
    {
        $user = \App\Models\User::factory()->create();
        $this->actingAs($user);

        $startTime = microtime(true);

        $response = $this->get('/dashboard');

        $loadTime = microtime(true) - $startTime;

        $response->assertOk();

        // Dashboard should load within performance target
        $this->assertLessThan(1.5, $loadTime, 'Dashboard asset loading too slow');
    }

    /**
     * Test Filament admin panel asset loading
     *
     * @see D03-FR-004.1 Admin panel performance
     */
    #[Test]
    public function filament_admin_asset_loading(): void
    {
        $admin = \App\Models\User::factory()->create();
        $admin->assignRole('admin');

        $this->actingAs($admin);

        $startTime = microtime(true);

        $response = $this->get('/admin');

        $loadTime = microtime(true) - $startTime;

        $response->assertOk();

        // Admin panel should load within acceptable time
        $this->assertLessThan(2.0, $loadTime, 'Filament admin panel loading too slow');

        // Verify Filament assets are loaded
        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertStringContainsString('filament', $content, 'Filament assets not loaded');
    }

    /**
     * Test image optimization and lazy loading
     *
     * @see D03-FR-014.1 LCP optimization
     */
    #[Test]
    public function image_optimization_attributes(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Check for lazy loading attributes on images
        if (str_contains($content, '<img')) {
            // Images should have loading="lazy" for non-critical images
            // or be optimized for LCP if they're above the fold
            $this->assertTrue(
                str_contains($content, 'loading="lazy"') ||
                    str_contains($content, 'loading="eager"'),
                'Images should have explicit loading attributes for optimization'
            );
        }
    }

    /**
     * Test font loading optimization
     *
     * @see D03-FR-014.1 CLS prevention
     */
    #[Test]
    public function font_loading_optimization(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Check for font preloading or font-display optimization
        if (str_contains($content, '@font-face') || str_contains($content, 'fonts.')) {
            // Fonts should use font-display: swap or be preloaded
            $hasFontOptimization = str_contains($content, 'font-display') ||
                str_contains($content, 'rel="preload"') ||
                str_contains($content, 'as="font"');

            $this->assertTrue(
                $hasFontOptimization,
                'Fonts should be optimized with font-display or preloading to prevent CLS'
            );
        }
    }

    /**
     * Test asset compression (gzip/brotli)
     *
     * @see D03-FR-007.2 Asset delivery optimization
     */
    #[Test]
    public function asset_compression_headers(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        $jsEntry = $manifest['resources/js/app.js'] ?? null;
        $this->assertNotNull($jsEntry);

        // Test if compressed versions exist (optional but recommended)
        $jsPath = public_path('build/'.$jsEntry['file']);
        $gzipPath = $jsPath.'.gz';
        $brotliPath = $jsPath.'.br';

        // At least one compression format should be available for production
        $hasCompression = file_exists($gzipPath) || file_exists($brotliPath);

        if (! $hasCompression) {
            $this->markTestSkipped(
                'Asset compression not configured. Consider enabling gzip/brotli compression for production'
            );
        }

        $this->assertTrue($hasCompression, 'Assets should be pre-compressed for optimal delivery');
    }

    /**
     * Test resource hints (preconnect, dns-prefetch)
     *
     * @see D03-FR-014.1 TTFB optimization
     */
    #[Test]
    public function resource_hints_present(): void
    {
        $response = $this->get('/');

        $response->assertOk();

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Check for resource hints for external resources
        $hasResourceHints = str_contains($content, 'rel="preconnect"') ||
            str_contains($content, 'rel="dns-prefetch"') ||
            str_contains($content, 'rel="prefetch"');

        // Resource hints are optional but recommended for external resources
        if (! $hasResourceHints) {
            $this->markTestIncomplete(
                'Consider adding resource hints (preconnect, dns-prefetch) for external resources'
            );
        }
    }

    /**
     * Test bundle splitting and code splitting
     *
     * @see D03-FR-007.2 Asset optimization
     */
    #[Test]
    public function bundle_splitting_configuration(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        // Count number of JavaScript chunks
        $jsChunks = array_filter($manifest, function ($entry) {
            return isset($entry['file']) && str_ends_with($entry['file'], '.js');
        });

        // Should have multiple chunks for code splitting (vendor, app, etc.)
        $chunkCount = count($jsChunks);

        $this->assertGreaterThan(
            1,
            $chunkCount,
            'Consider implementing code splitting for better performance (vendor chunks, lazy loading)'
        );
    }

    /**
     * Test CSS purging effectiveness
     *
     * @see D03-FR-015.4 Tailwind CSS optimization
     */
    #[Test]
    public function css_purging_effectiveness(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        $cssEntry = $manifest['resources/css/app.css'] ?? null;
        $this->assertNotNull($cssEntry);

        $cssPath = public_path('build/'.$cssEntry['file']);
        $cssContent = File::get($cssPath);

        // Check that CSS is minified (no unnecessary whitespace)
        $hasExcessiveWhitespace = preg_match('/\n\s{4,}/', $cssContent);
        $this->assertFalse($hasExcessiveWhitespace, 'CSS should be minified (excessive whitespace detected)');

        // Check that unused Tailwind classes are purged
        // (CSS file should be relatively small if purging is working)
        $cssSize = strlen($cssContent);
        $maxUnpurgedSize = 2 * 1024 * 1024; // 2MB (unpurged Tailwind is much larger)

        $this->assertLessThan(
            $maxUnpurgedSize,
            $cssSize,
            'CSS file suggests Tailwind purging may not be working effectively'
        );
    }

    /**
     * Test performance budget compliance
     *
     * @see D03-FR-007.2 Performance budgets
     */
    #[Test]
    public function performance_budget_compliance(): void
    {
        $manifestPath = public_path('build/manifest.json');
        $manifest = json_decode(File::get($manifestPath), true);

        $totalSize = 0;

        foreach ($manifest as $entry) {
            if (isset($entry['file'])) {
                $filePath = public_path('build/'.$entry['file']);
                if (file_exists($filePath)) {
                    $size = filesize($filePath);
                    if ($size !== false) {
                        $totalSize += $size;
                    }
                }
            }
        }

        // Total initial bundle size should be < 2MB for good performance
        $performanceBudget = 2 * 1024 * 1024; // 2MB

        $this->assertLessThan(
            $performanceBudget,
            $totalSize,
            sprintf(
                'Total asset size (%s) exceeds performance budget (%s)',
                $this->formatBytes($totalSize),
                $this->formatBytes($performanceBudget)
            )
        );
    }

    /**
     * Format bytes to human-readable format
     */
    private function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $i = 0;

        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
