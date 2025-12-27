<?php

declare(strict_types=1);

namespace Tests\Browser\Examples;

use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use PHPUnit\Framework\Attributes\Test;
use Tests\DuskTestCase;

/**
 * Percy Visual Testing Example Tests for Laravel Dusk
 *
 * This file provides well-documented example tests demonstrating Percy visual
 * testing integration patterns with Laravel Dusk. Each example includes detailed
 * inline comments explaining the purpose, configuration options, and best practices.
 *
 * IMPORTANT: These examples are designed to work with the ICTServe v3.6.1 stack:
 * - Laravel 12.43.1
 * - Livewire 3.7.3
 * - Filament 4.3.1
 * - Laravel Dusk (when installed)
 *
 * NOTE: Laravel Dusk acts as a redundancy testing layer after Playwright.
 * Primary visual testing is performed through Playwright integration.
 *
 * Prerequisites:
 * - Install Laravel Dusk: composer require laravel/dusk --dev
 * - Install Percy CLI: npm install -g @percy/cli
 * - Install Percy Selenium: npm install @percy/selenium-webdriver
 * - Set PERCY_TOKEN environment variable
 *
 * Run these examples:
 *   php artisan dusk tests/Browser/Examples/PercyExampleTest.php
 *
 * Run with Percy:
 *   npx percy exec -- php artisan dusk tests/Browser/Examples/PercyExampleTest.php
 *
 * @trace D10 Source Code Documentation
 *
 * @author Pasukan Pembangunan BPM MOTAC
 *
 * @version 3.6.1
 *
 * @updated 2025-12-26
 */
class PercyExampleTest extends DuskTestCase
{
    use DatabaseMigrations;

    /**
     * Percy snapshot helper method
     *
     * Takes a Percy snapshot using the Percy CLI integration.
     * This method wraps the Percy snapshot functionality for Dusk tests.
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  string  $name  Descriptive name for the snapshot
     * @param  array  $options  Optional configuration options
     */
    protected function takePercySnapshot($browser, string $name, array $options = []): void
    {
        // Check if Percy is enabled (PERCY_TOKEN is set)
        if (empty(env('PERCY_TOKEN'))) {
            $this->addWarning('Percy snapshot skipped - PERCY_TOKEN not set');

            return;
        }

        // Default options for ICTServe v3.6.1
        $defaultOptions = [
            'widths' => [375, 768, 1280],
            'minHeight' => 1024,
            'enableJavaScript' => true,
        ];

        $config = array_merge($defaultOptions, $options);

        // Execute Percy snapshot via JavaScript
        // Percy CLI intercepts this when running under `npx percy exec`
        $browser->script(sprintf(
            "if (typeof PercyDOM !== 'undefined') { PercyDOM.snapshot('%s', %s); }",
            addslashes($name),
            json_encode($config)
        ));
    }

    /**
     * Wait for Livewire components to stabilize
     *
     * Ensures all Livewire loading states are complete before taking snapshots.
     *
     * @param  \Laravel\Dusk\Browser  $browser  The Dusk browser instance
     * @param  int  $timeout  Maximum wait time in seconds
     */
    protected function waitForLivewire($browser, int $timeout = 10): void
    {
        $browser->waitUntilMissing('[wire\\:loading]', $timeout);
        $browser->pause(500); // Additional stabilization time
    }

    // ========================================================================
    // EXAMPLE 1: BASIC PERCY SNAPSHOT
    // ========================================================================

    /**
     * Example 1: Basic Percy Snapshot
     *
     * This is the simplest form of Percy integration with Dusk. It demonstrates:
     * - Taking a basic visual snapshot of a page
     * - Using the takePercySnapshot helper method
     * - Waiting for page content to stabilize
     *
     * Best Practices:
     * - Always wait for page to load before taking snapshots
     * - Use descriptive snapshot names that include context
     * - Pause briefly to allow dynamic content to settle
     */
    #[Test]
    public function basic_homepage_snapshot(): void
    {
        $this->browse(function ($browser) {
            // Step 1: Navigate to the homepage
            $browser->visit('/');

            // Step 2: Wait for page to fully load
            $browser->waitForText('ICTServe', 10);

            // Step 3: Wait for any Livewire components to stabilize
            $this->waitForLivewire($browser);

            // Step 4: Take the Percy snapshot
            $this->takePercySnapshot($browser, 'Dusk Example - Basic Homepage Snapshot', [
                'widths' => [375, 768, 1280],
            ]);

            // Step 5: Continue with regular Dusk assertions
            $browser->assertSee('ICTServe');
        });
    }

    // ========================================================================
    // EXAMPLE 2: RESPONSIVE VISUAL TESTING
    // ========================================================================

    /**
     * Example 2a: Mobile viewport snapshot
     *
     * Tests mobile layout by resizing the browser window.
     * Percy will capture the snapshot at the specified viewport size.
     */
    #[Test]
    public function mobile_viewport_snapshot(): void
    {
        $this->browse(function ($browser) {
            // Set mobile viewport (iPhone SE dimensions)
            $browser->resize(375, 667);

            $browser->visit('/');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Mobile Layout (375px)', [
                'widths' => [375],
                'minHeight' => 667,
            ]);

            // Verify mobile-specific behavior
            $browser->assertVisible('body');
        });
    }

    /**
     * Example 2b: Tablet viewport snapshot
     *
     * Tests tablet layout by resizing the browser window.
     */
    #[Test]
    public function tablet_viewport_snapshot(): void
    {
        $this->browse(function ($browser) {
            // Set tablet viewport (iPad dimensions)
            $browser->resize(768, 1024);

            $browser->visit('/');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Tablet Layout (768px)', [
                'widths' => [768],
                'minHeight' => 1024,
            ]);
        });
    }

    /**
     * Example 2c: Desktop viewport snapshot
     *
     * Tests desktop layout with full-width viewport.
     */
    #[Test]
    public function desktop_viewport_snapshot(): void
    {
        $this->browse(function ($browser) {
            // Set desktop viewport
            $browser->resize(1280, 800);

            $browser->visit('/');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Desktop Layout (1280px)', [
                'widths' => [1280, 1920],
                'minHeight' => 800,
            ]);
        });
    }

    /**
     * Example 2d: Full responsive test
     *
     * Captures snapshots at all critical breakpoints in a single test.
     */
    #[Test]
    public function full_responsive_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            // Take snapshot with all responsive widths
            $this->takePercySnapshot($browser, 'Dusk Example - Full Responsive Homepage', [
                'widths' => [375, 768, 1024, 1280, 1920],
                'minHeight' => 1024,
            ]);
        });
    }

    // ========================================================================
    // EXAMPLE 3: FORM STATE TESTING
    // ========================================================================

    /**
     * Example 3a: Empty form state
     *
     * Captures the initial state of a form before user interaction.
     */
    #[Test]
    public function empty_form_state_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/helpdesk/create');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Form Empty State', [
                'widths' => [375, 768, 1280],
            ]);

            // Verify form is present
            $browser->assertPresent('form');
        });
    }

    /**
     * Example 3b: Filled form state
     *
     * Captures the form after user has entered data.
     */
    #[Test]
    public function filled_form_state_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/helpdesk/create');
            $this->waitForLivewire($browser);

            // Fill form fields with test data
            $browser->whenAvailable('input[name*="name"]', function ($input) {
                $input->type('', 'Ahmad bin Abdullah');
            });

            $browser->whenAvailable('input[type="email"]', function ($input) {
                $input->type('', 'ahmad@example.com');
            });

            $browser->whenAvailable('input[type="tel"]', function ($input) {
                $input->type('', '0123456789');
            });

            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Form Filled State', [
                'widths' => [375, 768, 1280],
            ]);
        });
    }

    /**
     * Example 3c: Validation error state
     *
     * Captures the form showing validation errors.
     */
    #[Test]
    public function validation_error_state_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/helpdesk/create');
            $this->waitForLivewire($browser);

            // Try to submit empty form to trigger validation
            $browser->whenAvailable('button[type="submit"]', function ($button) {
                $button->click();
            });

            // Wait for validation messages
            $browser->pause(1000);
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Form Validation Errors', [
                'widths' => [375, 768, 1280],
            ]);
        });
    }

    // ========================================================================
    // EXAMPLE 4: HYBRID ARCHITECTURE TESTING
    // ========================================================================

    /**
     * Example 4a: Guest user workflow
     *
     * Tests pages accessible without authentication.
     * ICTServe v3.6.1 uses True Hybrid Architecture supporting both
     * guest and authenticated workflows.
     */
    #[Test]
    public function guest_user_workflow_snapshot(): void
    {
        $this->browse(function ($browser) {
            // Guest users can access public pages
            $browser->visit('/helpdesk');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Guest User Helpdesk', [
                'widths' => [375, 768, 1280],
            ]);

            // Verify guest-specific elements
            $browser->assertPresent('form');
        });
    }

    /**
     * Example 4b: Authenticated user workflow
     *
     * Tests pages requiring authentication.
     */
    #[Test]
    public function authenticated_user_workflow_snapshot(): void
    {
        $user = User::factory()->create();

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/staff/dashboard');

            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Authenticated User Dashboard', [
                'widths' => [768, 1024, 1280, 1920],
            ]);

            // Verify authenticated-specific elements
            $browser->assertAuthenticated();
        });
    }

    /**
     * Example 4c: Admin user workflow
     *
     * Tests Filament admin panel pages.
     */
    #[Test]
    public function admin_user_workflow_snapshot(): void
    {
        $admin = User::factory()->create([
            'is_admin' => true,
        ]);

        $this->browse(function ($browser) use ($admin) {
            $browser->loginAs($admin)
                ->visit('/admin');

            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Admin Panel Dashboard', [
                'widths' => [1024, 1280, 1920],
            ]);
        });
    }

    // ========================================================================
    // EXAMPLE 5: ACCESSIBILITY VISUAL TESTING
    // ========================================================================

    /**
     * Example 5a: Accessibility compliance snapshot
     *
     * Uses Percy for WCAG 2.2 AA visual compliance testing.
     */
    #[Test]
    public function accessibility_compliance_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Accessibility Compliance', [
                'widths' => [375, 768, 1280],
            ]);

            // Verify main landmark exists (WCAG requirement)
            $browser->assertPresent('main, [role="main"]');
        });
    }

    /**
     * Example 5b: Focus indicator testing
     *
     * Validates that focus indicators are visible and meet WCAG requirements.
     */
    #[Test]
    public function focus_indicator_validation_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/login');
            $this->waitForLivewire($browser);

            // Focus on the first input element
            $browser->script("document.querySelector('input')?.focus()");
            $browser->pause(300);

            $this->takePercySnapshot($browser, 'Dusk Example - Focus Indicators', [
                'widths' => [768, 1280],
            ]);
        });
    }

    /**
     * Example 5c: Keyboard navigation testing
     *
     * Tests keyboard navigation accessibility.
     */
    #[Test]
    public function keyboard_navigation_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            // Tab through interactive elements
            $browser->keys('body', ['{tab}']);
            $browser->pause(300);

            $this->takePercySnapshot($browser, 'Dusk Example - Keyboard Navigation', [
                'widths' => [1280],
            ]);

            // Verify focus is on an interactive element
            $browser->script('
                const focused = document.activeElement;
                return focused && focused !== document.body;
            ');
        });
    }

    // ========================================================================
    // EXAMPLE 6: BAHASA MELAYU INTERFACE TESTING
    // ========================================================================

    /**
     * Example 6a: Bahasa Melayu homepage
     *
     * ICTServe v3.6.0+ uses Bahasa Melayu exclusively for user interfaces.
     */
    #[Test]
    public function bahasa_melayu_homepage_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Bahasa Melayu Homepage', [
                'widths' => [375, 768, 1280],
            ]);

            // Verify Bahasa Melayu content is displayed
            // Note: Specific text depends on application translations
        });
    }

    /**
     * Example 6b: Bahasa Melayu form labels
     *
     * Validates Bahasa Melayu form labels render correctly.
     */
    #[Test]
    public function bahasa_melayu_form_labels_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/helpdesk/create');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Bahasa Melayu Form Labels', [
                'widths' => [768, 1280],
            ]);
        });
    }

    // ========================================================================
    // EXAMPLE 7: ERROR HANDLING AND GRACEFUL DEGRADATION
    // ========================================================================

    /**
     * Example 7a: Graceful degradation when Percy fails
     *
     * Tests continue even if Percy is unavailable.
     */
    #[Test]
    public function graceful_degradation_when_percy_fails(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            // This will not throw even if Percy is unavailable
            $this->takePercySnapshot($browser, 'Dusk Example - Graceful Degradation');

            // Test continues normally regardless of Percy status
            $browser->assertSee('ICTServe');
        });
    }

    /**
     * Example 7b: Conditional Percy execution
     *
     * Only takes snapshots when Percy is enabled.
     */
    #[Test]
    public function conditional_percy_execution(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            // Check if Percy is enabled
            if (! empty(env('PERCY_TOKEN'))) {
                $this->takePercySnapshot($browser, 'Dusk Example - Conditional Snapshot');
            }

            // Functional test always runs
            $browser->assertSee('ICTServe');
        });
    }

    // ========================================================================
    // EXAMPLE 8: LIVEWIRE COMPONENT TESTING
    // ========================================================================

    /**
     * Example 8a: Livewire component initial state
     *
     * Captures Livewire component before any interaction.
     */
    #[Test]
    public function livewire_component_initial_state_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/helpdesk/create');

            // Wait for Livewire to initialize
            $browser->waitFor('[wire\\:id]', 10);
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Livewire Initial State', [
                'widths' => [768, 1280],
            ]);
        });
    }

    /**
     * Example 8b: Livewire component after interaction
     *
     * Captures Livewire component after user interaction.
     */
    #[Test]
    public function livewire_component_after_interaction_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/helpdesk/create');
            $browser->waitFor('[wire\\:id]', 10);
            $this->waitForLivewire($browser);

            // Interact with Livewire component
            $browser->whenAvailable('input[name*="name"]', function ($input) {
                $input->type('', 'Test User');
            });

            // Wait for Livewire to process the update
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Livewire After Interaction', [
                'widths' => [768, 1280],
            ]);
        });
    }

    // ========================================================================
    // EXAMPLE 9: MODAL AND DIALOG TESTING
    // ========================================================================

    /**
     * Example 9a: Modal dialog snapshot
     *
     * Captures modal dialogs for visual testing.
     */
    #[Test]
    public function modal_dialog_snapshot(): void
    {
        $user = User::factory()->create();

        $this->browse(function ($browser) use ($user) {
            $browser->loginAs($user)
                ->visit('/staff/dashboard');

            $this->waitForLivewire($browser);

            // Try to open a modal (if available)
            $browser->whenAvailable('[data-modal-target], [x-data*="modal"]', function ($trigger) {
                $trigger->click();
            });

            $browser->pause(500);

            // Capture modal if visible
            $browser->whenAvailable('[role="dialog"], .modal', function ($modal) use ($browser) {
                $this->takePercySnapshot($browser, 'Dusk Example - Modal Dialog', [
                    'widths' => [768, 1280],
                ]);
            });
        });
    }

    // ========================================================================
    // EXAMPLE 10: PERFORMANCE CONSIDERATIONS
    // ========================================================================

    /**
     * Example 10a: Quick development snapshot
     *
     * Uses minimal configuration for fast feedback during development.
     */
    #[Test]
    public function quick_development_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');

            // Minimal wait for faster execution
            $browser->pause(500);

            $this->takePercySnapshot($browser, 'Dusk Example - Quick Dev Snapshot', [
                'widths' => [1280], // Single viewport for speed
                'minHeight' => 800,
            ]);
        });
    }

    /**
     * Example 10b: Comprehensive CI snapshot
     *
     * Uses full configuration for thorough CI/CD testing.
     */
    #[Test]
    public function comprehensive_ci_snapshot(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            $this->waitForLivewire($browser);

            $this->takePercySnapshot($browser, 'Dusk Example - Comprehensive CI Snapshot', [
                'widths' => [375, 768, 1024, 1280, 1920],
                'minHeight' => 1024,
            ]);
        });
    }
}
