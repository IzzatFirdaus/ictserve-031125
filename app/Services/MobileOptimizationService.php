<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * Mobile Optimization Service
 *
 * Provides mobile-specific optimizations for ICTServe v3.6.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-015 (Mobile Optimization)
 * @trace D12 §6.8 (Performance Optimization)
 * @trace D13 §2.6 (Responsive Design)
 *
 * @version 1.0.0
 */
class MobileOptimizationService
{
    /**
     * Mobile breakpoint in pixels
     */
    private const MOBILE_BREAKPOINT = 768;

    /**
     * Tablet breakpoint in pixels
     */
    private const TABLET_BREAKPOINT = 1024;

    /**
     * Cache TTL for device detection (1 hour)
     */
    private const DEVICE_CACHE_TTL = 3600;

    /**
     * Detect if request is from mobile device
     */
    public function isMobileDevice(Request $request): bool
    {
        $userAgent = $request->userAgent() ?? '';
        $cacheKey = 'mobile_detect_'.md5($userAgent);

        return Cache::remember($cacheKey, self::DEVICE_CACHE_TTL, function () use ($userAgent) {
            $mobilePatterns = [
                '/Mobile/i',
                '/Android/i',
                '/iPhone/i',
                '/iPad/i',
                '/iPod/i',
                '/webOS/i',
                '/BlackBerry/i',
                '/Opera Mini/i',
                '/IEMobile/i',
                '/Windows Phone/i',
            ];

            foreach ($mobilePatterns as $pattern) {
                if (preg_match($pattern, $userAgent)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Detect if request is from tablet device
     */
    public function isTabletDevice(Request $request): bool
    {
        $userAgent = $request->userAgent() ?? '';
        $cacheKey = 'tablet_detect_'.md5($userAgent);

        return Cache::remember($cacheKey, self::DEVICE_CACHE_TTL, function () use ($userAgent) {
            $tabletPatterns = [
                '/iPad/i',
                '/Android(?!.*Mobile)/i',
                '/Tablet/i',
            ];

            foreach ($tabletPatterns as $pattern) {
                if (preg_match($pattern, $userAgent)) {
                    return true;
                }
            }

            return false;
        });
    }

    /**
     * Get device type string
     */
    public function getDeviceType(Request $request): string
    {
        if ($this->isTabletDevice($request)) {
            return 'tablet';
        }

        if ($this->isMobileDevice($request)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Get responsive breakpoint configuration
     *
     * @return array<string, array<string, mixed>>
     */
    public function getBreakpoints(): array
    {
        return [
            'xs' => [
                'min' => 0,
                'max' => 639,
                'columns' => 4,
                'label' => 'Mudah Alih Kecil',
            ],
            'sm' => [
                'min' => 640,
                'max' => 767,
                'columns' => 4,
                'label' => 'Mudah Alih',
            ],
            'md' => [
                'min' => 768,
                'max' => 1023,
                'columns' => 8,
                'label' => 'Tablet',
            ],
            'lg' => [
                'min' => 1024,
                'max' => 1279,
                'columns' => 12,
                'label' => 'Desktop Kecil',
            ],
            'xl' => [
                'min' => 1280,
                'max' => 1535,
                'columns' => 12,
                'label' => 'Desktop',
            ],
            '2xl' => [
                'min' => 1536,
                'max' => null,
                'columns' => 12,
                'label' => 'Desktop Besar',
            ],
        ];
    }

    /**
     * Get optimized image sizes for device type
     *
     * @return array<string, int>
     */
    public function getOptimizedImageSizes(string $deviceType): array
    {
        return match ($deviceType) {
            'mobile' => [
                'thumbnail' => 100,
                'small' => 200,
                'medium' => 400,
                'large' => 600,
            ],
            'tablet' => [
                'thumbnail' => 150,
                'small' => 300,
                'medium' => 600,
                'large' => 900,
            ],
            default => [
                'thumbnail' => 200,
                'small' => 400,
                'medium' => 800,
                'large' => 1200,
            ],
        };
    }

    /**
     * Get pagination limit based on device type
     */
    public function getPaginationLimit(string $deviceType): int
    {
        return match ($deviceType) {
            'mobile' => 10,
            'tablet' => 15,
            default => 20,
        };
    }

    /**
     * Get touch target configuration
     *
     * @return array<string, int>
     */
    public function getTouchTargetConfig(): array
    {
        return [
            'minimum_size' => 44, // WCAG 2.2 AA minimum
            'recommended_size' => 48,
            'spacing' => 8,
        ];
    }

    /**
     * Generate mobile-optimized navigation items
     *
     * @param  array<int, array<string, mixed>>  $items
     * @return array<int, array<string, mixed>>
     */
    public function getMobileNavigation(array $items, int $maxItems = 5): array
    {
        // Prioritize items for mobile bottom navigation
        $prioritized = collect($items)
            ->sortByDesc('priority')
            ->take($maxItems)
            ->values()
            ->toArray();

        return $prioritized;
    }

    /**
     * Get mobile-specific meta tags
     *
     * @return array<string, string>
     */
    public function getMobileMetaTags(): array
    {
        return [
            'viewport' => 'width=device-width, initial-scale=1, maximum-scale=5, viewport-fit=cover',
            'mobile-web-app-capable' => 'yes',
            'apple-mobile-web-app-capable' => 'yes',
            'apple-mobile-web-app-status-bar-style' => 'default',
            'format-detection' => 'telephone=no',
            'theme-color' => '#0056B3',
        ];
    }

    /**
     * Check if reduced motion is preferred
     */
    public function prefersReducedMotion(Request $request): bool
    {
        $header = $request->header('Sec-CH-Prefers-Reduced-Motion');

        return $header === 'reduce';
    }

    /**
     * Get offline capability configuration
     *
     * @return array<string, mixed>
     */
    public function getOfflineConfig(): array
    {
        return [
            'enabled' => true,
            'cache_name' => 'ictserve-offline-v1',
            'cache_urls' => [
                '/',
                '/offline',
                '/css/app.css',
                '/js/app.js',
            ],
            'fallback_page' => '/offline',
        ];
    }

    /**
     * Generate responsive srcset for images
     */
    public function generateSrcset(string $imagePath, string $deviceType): string
    {
        $sizes = $this->getOptimizedImageSizes($deviceType);
        $srcset = [];

        foreach ($sizes as $name => $width) {
            $srcset[] = "{$imagePath}?w={$width} {$width}w";
        }

        return implode(', ', $srcset);
    }

    /**
     * Get mobile performance hints
     *
     * @return array<string, mixed>
     */
    public function getPerformanceHints(string $deviceType): array
    {
        $baseHints = [
            'preconnect' => [
                'https://fonts.googleapis.com',
                'https://fonts.gstatic.com',
            ],
            'dns-prefetch' => [],
            'preload' => [],
        ];

        if ($deviceType === 'mobile') {
            $baseHints['preload'][] = [
                'href' => '/css/critical-mobile.css',
                'as' => 'style',
            ];
        }

        return $baseHints;
    }
}
