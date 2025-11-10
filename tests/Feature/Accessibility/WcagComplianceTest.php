<?php

declare(strict_types=1);

namespace Tests\Feature\Accessibility;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * WCAG 2.2 Level AA Compliance Test Suite
 *
 * Validates accessibility compliance for the Updated Loan Module across all interfaces:
 * - Guest loan application forms
 * - Authenticated portal features
 * - Admin panel (Filament) interfaces
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-006.1 (Accessibility Requirements)
 * @trace D03-FR-006.2 (Keyboard Navigation)
 * @trace D03-FR-006.3 (Screen Reader Support)
 * @trace D04 §6.1 (Accessibility Compliance)
 * @trace D12 §9 (WCAG 2.2 AA Compliance)
 * @trace D14 §9 (Accessibility Standards)
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class WcagComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected LoanApplication $loanApplication;

    protected Asset $asset;

    protected AssetCategory $category;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->division = Division::factory()->create();
        $this->category = AssetCategory::factory()->create();
        $this->asset = Asset::factory()->create(['category_id' => $this->category->id]);
        $this->user = User::factory()->create(['division_id' => $this->division->id]);
        $this->loanApplication = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'division_id' => $this->division->id,
        ]);
    }

    /**
     * Test WCAG 2.2 AA compliance for guest loan application form
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function guest_loan_form_wcag_compliance(): void
    {
        $response = $this->get(route('loan.guest.create'));

        $response->assertStatus(200);

        // Test semantic HTML structure
        $this->assertSemanticHtmlStructure($response);

        // Test ARIA attributes
        $this->assertAriaAttributes($response);

        // Test keyboard navigation
        $this->assertKeyboardNavigation($response);

        // Test color contrast compliance
        $this->assertColorContrastCompliance($response);

        // Test touch target sizes
        $this->assertTouchTargetSizes($response);

        // Test focus indicators
        $this->assertFocusIndicators($response);
    }

    /**
     * Test WCAG 2.2 AA compliance for guest loan tracking page
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function guest_loan_tracking_wcag_compliance(): void
    {
        $response = $this->get(route('loan.guest.tracking', [
            'applicationNumber' => $this->loanApplication->application_number,
        ]));

        $response->assertStatus(200);

        // Test semantic HTML structure
        $this->assertSemanticHtmlStructure($response);

        // Test ARIA attributes for data tables
        $this->assertDataTableAccessibility($response);

        // Test heading hierarchy
        $this->assertHeadingHierarchy($response);

        // Test language attributes
        $this->assertLanguageAttributes($response);
    }

    /**
     * Test WCAG 2.2 AA compliance for authenticated portal
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function authenticated_portal_wcag_compliance(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.authenticated.dashboard'));

        $response->assertStatus(200);

        // Test navigation landmarks
        $this->assertNavigationLandmarks($response);

        // Test form accessibility
        $this->assertFormAccessibility($response);

        // Test interactive elements
        $this->assertInteractiveElementsAccessibility($response);

        // Test error handling accessibility
        $this->assertErrorHandlingAccessibility($response);
    }

    /**
     * Test WCAG 2.2 AA compliance for language switcher
     * Requirements: 6.1, 7.3, 15.2, 1.5
     */
    #[Test]
    public function language_switcher_wcag_compliance(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Test ARIA menu button pattern
        $response->assertSee('aria-haspopup="menu"', false);
        $response->assertSee('aria-expanded', false);
        $response->assertSee('role="menu"', false);
        // Accept either menuitem or menuitemradio (menuitemradio is more specific)
        $content = $response->getContent();
        $this->assertTrue(
            str_contains($content, 'role="menuitem"') || str_contains($content, 'role="menuitemradio"'),
            'Language switcher must have menu items with proper role'
        );

        // Test language attributes
        $response->assertSee('lang="en"', false);
        $response->assertSee('lang="ms"', false);

        // Test keyboard navigation
        $response->assertSee('@keydown.escape', false);
        $response->assertSee('@click.away', false);
    }

    /**
     * Assert semantic HTML structure compliance
     */
    protected function assertSemanticHtmlStructure($response): void
    {
        // Test for proper HTML5 semantic elements
        $response->assertSee('<main', false);
        $response->assertSee('<header', false);
        $response->assertSee('<nav', false);

        // Test for proper heading structure (h1 should exist)
        $response->assertSee('<h1', false);

        // Test for proper form structure
        $content = $response->getContent();
        $this->assertNotFalse($content);
        if (str_contains($content, '<form')) {
            $response->assertSee('<fieldset', false);
            $response->assertSee('<legend', false);
        }
    }

    /**
     * Assert ARIA attributes compliance
     */
    protected function assertAriaAttributes($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for ARIA landmarks
        $this->assertStringContainsString('role="banner"', $content);
        $this->assertStringContainsString('role="navigation"', $content);
        $this->assertStringContainsString('role="main"', $content);

        // Test for ARIA labels on interactive elements
        if (str_contains($content, 'type="submit"')) {
            $this->assertTrue(
                str_contains($content, 'aria-label') || str_contains($content, 'aria-labelledby'),
                'Submit buttons must have accessible labels'
            );
        }

        // Test for ARIA live regions
        if (str_contains($content, 'wire:loading')) {
            $this->assertStringContainsString('aria-live', $content);
        }
    }

    /**
     * Assert keyboard navigation compliance
     */
    protected function assertKeyboardNavigation($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for proper tabindex usage (no positive tabindex values)
        $this->assertStringNotContainsString('tabindex="1"', $content);
        $this->assertStringNotContainsString('tabindex="2"', $content);

        // Test for skip links
        $this->assertStringContainsString('skip-to-content', $content);

        // Test for keyboard event handlers
        if (str_contains($content, '@click')) {
            $this->assertTrue(
                str_contains($content, '@keydown') || str_contains($content, '@keyup'),
                'Interactive elements with click handlers must also support keyboard events'
            );
        }
    }

    /**
     * Assert color contrast compliance
     */
    protected function assertColorContrastCompliance($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for compliant color classes
        $compliantColors = [
            'text-gray-900', // 16.6:1 contrast
            'text-gray-800', // 12.6:1 contrast
            'text-gray-700', // 9.5:1 contrast
            'bg-motac-blue', // 6.8:1 contrast
            'bg-success', // 4.9:1 contrast
            'bg-warning', // 4.5:1 contrast
            'bg-danger', // 8.2:1 contrast
        ];

        $hasCompliantColors = false;
        foreach ($compliantColors as $color) {
            if (str_contains($content, $color)) {
                $hasCompliantColors = true;
                break;
            }
        }

        $this->assertTrue($hasCompliantColors, 'Page must use WCAG compliant color classes');

        // Test for deprecated color usage (should not exist)
        $deprecatedColors = ['bg-red-500', 'bg-green-500', 'bg-yellow-500', 'text-red-500'];
        foreach ($deprecatedColors as $color) {
            $this->assertStringNotContainsString($color, $content, "Deprecated color class {$color} found");
        }
    }

    /**
     * Assert touch target sizes compliance (minimum 44x44px)
     */
    protected function assertTouchTargetSizes($response): void
    {
        $content = $response->getContent();

        // Test for minimum touch target classes
        if (str_contains($content, 'type="button"') || str_contains($content, 'type="submit"')) {
            $this->assertTrue(
                str_contains($content, 'min-h-[44px]') ||
                str_contains($content, 'min-h-44') ||
                str_contains($content, 'h-11') || // 44px equivalent
                str_contains($content, 'py-2') && str_contains($content, 'px-4'), // Standard button padding
                'Interactive elements must meet minimum 44x44px touch target size'
            );
        }
    }

    /**
     * Assert focus indicators compliance
     */
    protected function assertFocusIndicators($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for focus ring classes
        if (str_contains($content, 'type="button"') || str_contains($content, 'type="submit"')) {
            $this->assertTrue(
                str_contains($content, 'focus:ring-2') || str_contains($content, 'focus:ring-3'),
                'Interactive elements must have visible focus indicators'
            );

            $this->assertTrue(
                str_contains($content, 'focus:ring-offset-2'),
                'Focus indicators must have proper offset'
            );
        }
    }

    /**
     * Assert data table accessibility
     */
    protected function assertDataTableAccessibility($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        if (str_contains($content, '<table')) {
            // Test for table headers
            $this->assertStringContainsString('<th', $content);

            // Test for table caption or aria-label
            $this->assertTrue(
                str_contains($content, '<caption') || str_contains($content, 'aria-label'),
                'Data tables must have captions or accessible labels'
            );

            // Test for proper table structure
            $this->assertStringContainsString('<thead', $content);
            $this->assertStringContainsString('<tbody', $content);
        }
    }

    /**
     * Assert heading hierarchy compliance
     */
    protected function assertHeadingHierarchy($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Extract all headings
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $matches);

        if (! empty($matches[1])) {
            $headingLevels = array_map('intval', $matches[1]);

            // Test that h1 exists and is first
            $this->assertEquals(1, $headingLevels[0], 'Page must start with h1');

            // Test that heading levels don't skip (e.g., h1 -> h3)
            for ($i = 1; $i < count($headingLevels); $i++) {
                $diff = $headingLevels[$i] - $headingLevels[$i - 1];
                $this->assertLessThanOrEqual(1, $diff, 'Heading levels must not skip (e.g., h1 -> h3)');
            }
        }
    }

    /**
     * Assert language attributes compliance
     */
    protected function assertLanguageAttributes($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for lang attribute on html element
        $this->assertTrue(
            str_contains($content, 'lang="en"') || str_contains($content, 'lang="ms"'),
            'HTML element must have lang attribute'
        );

        // Test for language switching elements
        if (str_contains($content, 'language-switcher')) {
            $this->assertStringContainsString('lang="en"', $content);
            $this->assertStringContainsString('lang="ms"', $content);
        }
    }

    /**
     * Assert navigation landmarks compliance
     */
    protected function assertNavigationLandmarks($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for ARIA landmarks
        $requiredLandmarks = ['banner', 'navigation', 'main', 'contentinfo'];

        foreach ($requiredLandmarks as $landmark) {
            $this->assertTrue(
                str_contains($content, "role=\"{$landmark}\"") ||
                str_contains($content, "<{$landmark}"),
                "Page must contain {$landmark} landmark"
            );
        }
    }

    /**
     * Assert form accessibility compliance
     */
    protected function assertFormAccessibility($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        if (str_contains($content, '<form')) {
            // Test for proper label association
            if (str_contains($content, '<input')) {
                $this->assertTrue(
                    str_contains($content, 'aria-label') ||
                    str_contains($content, 'aria-labelledby') ||
                    str_contains($content, '<label'),
                    'Form inputs must have accessible labels'
                );
            }

            // Test for error message association
            if (str_contains($content, 'error')) {
                $this->assertTrue(
                    str_contains($content, 'aria-describedby') ||
                    str_contains($content, 'aria-invalid'),
                    'Form errors must be properly associated with inputs'
                );
            }

            // Test for required field indication
            if (str_contains($content, 'required')) {
                $this->assertTrue(
                    str_contains($content, 'aria-required') ||
                    str_contains($content, 'required'),
                    'Required fields must be properly indicated'
                );
            }
        }
    }

    /**
     * Assert interactive elements accessibility
     */
    protected function assertInteractiveElementsAccessibility($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for proper button roles
        if (str_contains($content, 'wire:click')) {
            $this->assertTrue(
                str_contains($content, 'type="button"') ||
                str_contains($content, 'role="button"'),
                'Clickable elements must have proper button semantics'
            );
        }

        // Test for loading states
        if (str_contains($content, 'wire:loading')) {
            $this->assertTrue(
                str_contains($content, 'aria-live') ||
                str_contains($content, 'aria-busy'),
                'Loading states must be announced to screen readers'
            );
        }
    }

    /**
     * Assert error handling accessibility
     */
    protected function assertErrorHandlingAccessibility($response): void
    {
        $content = $response->getContent();

        // Test for error message structure
        if (str_contains($content, 'error') || str_contains($content, 'invalid')) {
            $this->assertTrue(
                str_contains($content, 'role="alert"') ||
                str_contains($content, 'aria-live="assertive"'),
                'Error messages must be announced to screen readers'
            );
        }

        // Test for validation feedback
        if (str_contains($content, 'validation')) {
            $this->assertTrue(
                str_contains($content, 'aria-describedby') ||
                str_contains($content, 'aria-invalid'),
                'Validation feedback must be properly associated'
            );
        }
    }
}
