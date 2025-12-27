<?php

declare(strict_types=1);

namespace Tests\Browser\Traits;

/**
 * Percy Visual Testing Trait for Laravel Dusk
 *
 * This trait provides Percy visual testing integration for Laravel Dusk tests.
 * It includes helper methods for taking Percy snapshots, waiting for content
 * stabilization, and handling graceful degradation when Percy is unavailable.
 *
 * Usage:
 *   use Tests\Browser\Traits\PercyDuskTrait;
 *
 *   class MyDuskTest extends DuskTestCase
 *   {
 *       use PercyDuskTrait;
 *
 *       public function testExample(): void
 *       {
 *           $this->browse(function ($browser) {
 *               $browser->visit('/');
 *               $this->takePercySnapshot($browser, 'My Snapshot');
 *           });
 *       }
 *   }
 *
 * Prerequisites:
 * - Laravel Dusk installed: composer require laravel/dusk --dev
 * - Percy CLI installed: npm install -g @percy/cli
 * - PERCY_TOKEN environment variable set
 *
 * Run tests with Percy:
 *   npx percy exec -- php artisan dusk
 *
 * @trace D10 Source Code Documentation
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @version 3.6.1
 *
 * @updated 2025-12-26
 */
trait PercyDuskTrait
{
    /**
     * Default Percy configuration for ICTServe v3.6.1
     */
    protected array $defaultPercyConfig = [
        'widths' => [375, 768, 1280],
        'minHeight' => 1024,
        'enableJavaScript' => true,
    ];

    /**
     * Responsive viewport configurations
     */
    protected array $responsiveViewports = [
        'mobile' => ['width' => 375, 'height' => 667],
        'tablet' => ['width' => 768, 'height' => 1024],
        'desktop' => ['width' => 1280, 'height' => 800],
        'large' => ['width' => 1920, 'height' => 1080],
    ];

    /**
     * User type configurations for Hybrid Architecture
     */
    protected array $userTypeConfigs = [
        'guest' => [
            'widths' => [375, 768, 1280],
            'selectors' => ['.guest-form', '.guest-status'],
        ],
        'authenticated' => [
            'widths' => [768, 1024, 1280, 1920],
            'selectors' => ['.dashboard', '.profile'],
        ],
        'admin' => [
            'widths' => [1024, 1280, 1920],
            'selectors' => ['.filament-admin', '.admin-panel'],
        ],
    ];

    /**
     * Check if Percy is enabled
     *
     * Percy is considered enabled when PERCY_TOKEN is set and not empty.
     */
    protected function isPercyEnabled(): bool
    {
        return ! empty(env('PERCY_TOKEN'));
    }

    /**
     * Get Percy build information
     *
     * Returns information about the current Percy build configuration.
     */
    protected function getPercyBuildInfo(): array
    {
        return [
            'token' => ! empty(env('PERCY_TOKEN')),
            'branch' => env('PERCY_BRANCH', 'develop'),
            'project' => env('PERCY_PROJECT', 'ictserve'),
            'enabled' => $this->isPercyEnabled(),
        ];
    }

    /**
     * Take a Percy snapshot
     *
     * Takes a visual snapshot using Percy CLI integration.
     * Handles graceful degradation when Percy is unavailable.
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  string  $name  Descriptive name for the snapshot
     * @param  array  $options  Optional configuration options:
     *                          - widths: Array of viewport widths to capture
     *                          - minHeight: Minimum height for the snapshot
     *                          - enableJavaScript: Whether to enable JS during capture
     *                          - percyCSS: Custom CSS to inject
     *                          - scope: CSS selector to scope the snapshot
     *                          - userType: 'guest', 'authenticated', or 'admin'
     */
    protected function takePercySnapshot($browser, string $name, array $options = []): void
    {
        // Check if Percy is enabled
        if (! $this->isPercyEnabled()) {
            $this->addWarning("Percy snapshot '{$name}' skipped - PERCY_TOKEN not set");

            return;
        }

        // Merge with default configuration
        $config = array_merge($this->defaultPercyConfig, $options);

        // Apply user type configuration if specified
        if (isset($options['userType']) && isset($this->userTypeConfigs[$options['userType']])) {
            $userConfig = $this->userTypeConfigs[$options['userType']];
            $config['widths'] = $options['widths'] ?? $userConfig['widths'];
        }

        // Build Percy snapshot options
        $percyOptions = [
            'widths' => $config['widths'],
            'minHeight' => $config['minHeight'] ?? 1024,
            'enableJavaScript' => $config['enableJavaScript'] ?? true,
        ];

        // Add optional configurations
        if (isset($config['percyCSS'])) {
            $percyOptions['percyCSS'] = $config['percyCSS'];
        }

        if (isset($config['scope'])) {
            $percyOptions['scope'] = $config['scope'];
        }

        // Execute Percy snapshot via JavaScript
        // Percy CLI intercepts this when running under `npx percy exec`
        try {
            $browser->script(sprintf(
                "if (typeof PercyDOM !== 'undefined') { PercyDOM.snapshot('%s', %s); }",
                addslashes($name),
                json_encode($percyOptions)
            ));
        } catch (\Exception $e) {
            // Graceful degradation - log warning but don't fail test
            $this->addWarning("Percy snapshot '{$name}' failed: ".$e->getMessage());
        }
    }

    /**
     * Take responsive Percy snapshots
     *
     * Takes snapshots at multiple viewport sizes (mobile, tablet, desktop).
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  string  $baseName  Base name for the snapshots
     * @param  array  $options  Optional configuration options
     */
    protected function takeResponsivePercySnapshots($browser, string $baseName, array $options = []): void
    {
        // Take snapshot with all responsive widths
        $this->takePercySnapshot($browser, $baseName, array_merge($options, [
            'widths' => [375, 768, 1280],
        ]));
    }

    /**
     * Take accessibility-focused Percy snapshot
     *
     * Takes a snapshot with WCAG-specific CSS highlighting for accessibility validation.
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  string  $name  Descriptive name for the snapshot
     * @param  array  $options  Optional configuration options
     */
    protected function takeAccessibilityPercySnapshot($browser, string $name, array $options = []): void
    {
        $accessibilityCSS = '
            /* Highlight focus indicators */
            *:focus, *:focus-visible { 
                outline: 3px solid #ff6b35 !important; 
                outline-offset: 2px !important; 
            }
            /* Highlight form labels */
            label { 
                background-color: rgba(59, 130, 246, 0.1) !important; 
            }
            /* Highlight required fields */
            [required], [aria-required="true"] { 
                border-left: 3px solid #ef4444 !important; 
            }
            /* Highlight ARIA landmarks */
            [role="main"], main { 
                outline: 2px dashed #10b981 !important; 
            }
            [role="navigation"], nav { 
                outline: 2px dashed #3b82f6 !important; 
            }
        ';

        $this->takePercySnapshot($browser, $name, array_merge($options, [
            'percyCSS' => ($options['percyCSS'] ?? '').$accessibilityCSS,
        ]));
    }

    /**
     * Wait for Livewire components to stabilize
     *
     * Ensures all Livewire loading states are complete before taking snapshots.
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  int  $timeout  Maximum wait time in seconds
     */
    protected function waitForLivewireStable($browser, int $timeout = 10): void
    {
        // Wait for Livewire loading indicators to disappear
        $browser->waitUntilMissing('[wire\\:loading]', $timeout);

        // Wait for any Livewire dirty states to resolve
        $browser->waitUntilMissing('[wire\\:dirty]', $timeout);

        // Additional stabilization time for async operations
        $browser->pause(500);
    }

    /**
     * Wait for page content to stabilize
     *
     * Comprehensive wait that handles Livewire, Alpine.js, and general async content.
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  int  $timeout  Maximum wait time in seconds
     */
    protected function waitForStableContent($browser, int $timeout = 10): void
    {
        // Wait for document ready state
        $browser->waitFor('body', $timeout);

        // Wait for Livewire if present
        $this->waitForLivewireStable($browser, $timeout);

        // Wait for any loading spinners to disappear
        $browser->waitUntilMissing('.loading-spinner, .skeleton-loader', 5);

        // Wait for images to load
        $browser->script('
            return new Promise((resolve) => {
                const images = document.querySelectorAll("img");
                let loaded = 0;
                if (images.length === 0) {
                    resolve(true);
                    return;
                }
                images.forEach((img) => {
                    if (img.complete) {
                        loaded++;
                        if (loaded === images.length) resolve(true);
                    } else {
                        img.onload = img.onerror = () => {
                            loaded++;
                            if (loaded === images.length) resolve(true);
                        };
                    }
                });
                setTimeout(() => resolve(true), 5000);
            });
        ');

        // Final stabilization pause
        $browser->pause(300);
    }

    /**
     * Set viewport size for responsive testing
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  string  $size  Viewport size: 'mobile', 'tablet', 'desktop', or 'large'
     */
    protected function setResponsiveViewport($browser, string $size): void
    {
        if (! isset($this->responsiveViewports[$size])) {
            throw new \InvalidArgumentException("Unknown viewport size: {$size}");
        }

        $viewport = $this->responsiveViewports[$size];
        $browser->resize($viewport['width'], $viewport['height']);
    }

    /**
     * Take Percy snapshot with custom CSS for hiding dynamic content
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  string  $name  Descriptive name for the snapshot
     * @param  array  $options  Optional configuration options
     */
    protected function takePercySnapshotHideDynamic($browser, string $name, array $options = []): void
    {
        $dynamicContentCSS = '
            /* Hide dynamic timestamps */
            .dynamic-timestamp, .last-updated, .created-at, time[datetime] { 
                display: none !important; 
            }
            /* Hide loading states */
            .loading-spinner, .skeleton-loader, [wire\\:loading] { 
                display: none !important; 
            }
            /* Hide user-specific content */
            .user-avatar, .notification-badge { 
                visibility: hidden !important; 
            }
            /* Disable animations */
            *, *::before, *::after {
                animation-duration: 0s !important;
                transition-duration: 0s !important;
            }
        ';

        $this->takePercySnapshot($browser, $name, array_merge($options, [
            'percyCSS' => ($options['percyCSS'] ?? '').$dynamicContentCSS,
        ]));
    }

    /**
     * Add a warning message to the test output
     *
     * @param  string  $message  Warning message
     */
    protected function addWarning(string $message): void
    {
        // PHPUnit 11+ uses addWarning method
        if (method_exists($this, 'addWarning')) {
            parent::addWarning($message);
        } else {
            // Fallback for older PHPUnit versions
            fwrite(STDERR, "[WARNING] {$message}\n");
        }
    }
}
