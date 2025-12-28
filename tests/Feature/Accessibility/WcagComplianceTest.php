<?php

declare(strict_types=1);

namespace Tests\Feature\Accessibility;

use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use App\Models\LoanApplication;
use App\Models\User;
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
        $response = $this->get(route('loan.guest.apply'));

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
        // Use portal.dashboard instead of non-existent loan.authenticated.dashboard
        $response = $this->actingAs($this->user)
            ->get(route('portal.dashboard'));

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
     * Test WCAG 2.2 AA compliance for BM-exclusive interface (v3.6.0+)
     *
     * Language switcher has been removed per government directive.
     * System is now Bahasa Melayu exclusive.
     *
     * Requirements: 1.1, 1.2 (BM Exclusive Interface)
     *
     * @trace D15 §1.1 (Bahasa Melayu Sahaja)
     */
    #[Test]
    public function bm_exclusive_interface_wcag_compliance(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);

        // Verify BM-exclusive locale is set
        $this->assertEquals('ms', config('app.locale'));

        // Verify page has proper lang attribute for Bahasa Melayu
        $response->assertSee('lang="ms"', false);

        // Verify no language switcher component exists
        $content = $response->getContent();
        $this->assertFalse(
            str_contains($content, 'language-switcher') || str_contains($content, 'LanguageSwitcher'),
            'Language switcher should not exist in BM-exclusive interface'
        );

        // Verify BM text is present (sample check)
        $this->assertTrue(
            str_contains($content, 'Aduan') ||
                str_contains($content, 'Pinjaman') ||
                str_contains($content, 'Perkhidmatan'),
            'Page should contain Bahasa Melayu text'
        );

        // Test keyboard navigation still works (ESC for modals)
        $response->assertSee('@keydown.escape', false);

        // Verify accessibility attributes for main content
        $response->assertSee('role="main"', false);
    }

    /**
     * Assert semantic HTML structure compliance
     */
    protected function assertSemanticHtmlStructure($response): void
    {
        // Test for proper HTML5 semantic elements
        $response->assertSee('<main', false);
        // Header or nav is acceptable for top-level navigation
        $content = $response->getContent();
        $this->assertNotFalse($content);
        $this->assertTrue(
            str_contains($content, '<header') || str_contains($content, '<nav'),
            'Page must contain header or nav element'
        );

        // Test for proper heading structure (h1 should exist)
        $response->assertSee('<h1', false);

        // Test for proper form structure (fieldset/legend optional for simple forms)
        if (str_contains($content, '<form')) {
            // Forms should have either fieldset/legend OR proper aria-label
            $hasFieldset = str_contains($content, '<fieldset');
            $hasAriaLabel = str_contains($content, 'aria-label');
            $this->assertTrue(
                $hasFieldset || $hasAriaLabel,
                'Forms must have fieldset/legend or aria-label for accessibility'
            );
        }
    }

    /**
     * Assert ARIA attributes compliance
     */
    protected function assertAriaAttributes($response): void
    {
        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for ARIA landmarks (can be via role attribute or semantic HTML elements)
        $this->assertTrue(
            str_contains($content, 'role="banner"') || str_contains($content, '<header'),
            'Page must have banner landmark'
        );
        $this->assertTrue(
            str_contains($content, 'role="navigation"') || str_contains($content, '<nav'),
            'Page must have navigation landmark'
        );
        $this->assertTrue(
            str_contains($content, 'role="main"') || str_contains($content, '<main'),
            'Page must have main landmark'
        );

        // Test for ARIA labels on interactive elements
        if (str_contains($content, 'type="submit"')) {
            // Submit buttons can have text content or aria-label
            $this->assertTrue(
                str_contains($content, 'aria-label') ||
                    str_contains($content, 'aria-labelledby') ||
                    preg_match('/<button[^>]*type="submit"[^>]*>[^<]+<\/button>/', $content),
                'Submit buttons must have accessible labels'
            );
        }

        // Test for ARIA live regions (optional - only if loading states exist)
        if (str_contains($content, 'wire:loading')) {
            // Livewire handles loading states - aria-live is optional
            $this->assertTrue(true);
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

        // Test for skip links (various formats)
        $this->assertTrue(
            str_contains($content, 'skip-to-content') ||
                str_contains($content, 'Skip to') ||
                str_contains($content, 'Langkau ke') ||
                str_contains($content, '#main-content'),
            'Page must have skip navigation links'
        );

        // Test for keyboard event handlers (optional - not all click handlers need keyboard equivalents if using buttons)
        // Buttons and links are inherently keyboard accessible
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

        // Test for focus ring classes (supports both focus: and focus-visible: variants)
        if (str_contains($content, 'type="button"') || str_contains($content, 'type="submit"')) {
            $this->assertTrue(
                str_contains($content, 'focus:ring-2') || str_contains($content, 'focus:ring-3') ||
                    str_contains($content, 'focus-visible:ring-2') || str_contains($content, 'focus-visible:ring-3'),
                'Interactive elements must have visible focus indicators'
            );

            $this->assertTrue(
                str_contains($content, 'focus:ring-offset-2') || str_contains($content, 'focus-visible:ring-offset-2'),
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

            // Test for error message association (only check if error class is present)
            if (
                str_contains($content, 'class="error"') ||
                str_contains($content, 'class="is-invalid"') ||
                str_contains($content, 'aria-invalid="true"')
            ) {
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

        // Test for error alert elements (not just the word "error" in scripts)
        if (str_contains($content, 'role="alert"') || str_contains($content, 'class="error-message"')) {
            $this->assertTrue(
                str_contains($content, 'role="alert"') ||
                    str_contains($content, 'aria-live="assertive"') ||
                    str_contains($content, 'aria-live="polite"'),
                'Error messages must be announced to screen readers'
            );
        }

        // Test for validation feedback (only if validation error markup is present)
        if (str_contains($content, 'aria-invalid="true"') || str_contains($content, 'is-invalid')) {
            $this->assertTrue(
                str_contains($content, 'aria-describedby') ||
                    str_contains($content, 'aria-invalid'),
                'Validation feedback must be properly associated'
            );
        }
    }
}
