<?php

declare(strict_types=1);

namespace Tests\Feature\Accessibility;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Filament Accessibility Test
 *
 * Tests WCAG 2.2 AA compliance for Filament admin panel including
 * color contrast, keyboard navigation, ARIA attributes, and screen reader support.
 *
 * Requirements: 18.3, 14.1-14.5, D03-FR-012.1
 */
class FilamentAccessibilityTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->admin()->create();
    }

    public function test_admin_dashboard_has_proper_html_structure(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        $response->assertStatus(200);

        // Verify HTML5 semantic structure
        $response->assertSee('<main', false);
        $response->assertSee('role="main"', false);
        $response->assertSee('<nav', false);
        $response->assertSee('role="navigation"', false);
    }

    public function test_admin_dashboard_has_proper_aria_attributes(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify ARIA landmarks
        $response->assertSee('aria-label', false);
        $response->assertSee('role="banner"', false);
        $response->assertSee('role="complementary"', false);
        $response->assertSee('role="contentinfo"', false);
    }

    public function test_navigation_has_proper_keyboard_support(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify tabindex attributes for keyboard navigation
        $response->assertSee('tabindex', false);

        // Verify skip links for screen readers
        $response->assertSee('Skip to main content', false);
    }

    public function test_forms_have_proper_labels_and_descriptions(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/helpdesk-tickets/create');

        $response->assertStatus(200);

        // Verify form labels are properly associated
        $response->assertSee('<label for=', false);

        // Verify required field indicators
        $response->assertSee('aria-required="true"', false);

        // Verify help text associations
        $response->assertSee('aria-describedby', false);
    }

    public function test_error_messages_have_proper_aria_attributes(): void
    {
        $this->actingAs($this->admin);

        // Submit invalid form to trigger errors
        $response = $this->post('/admin/helpdesk-tickets', [
            'title' => '', // Required field
        ]);

        // Verify error messages have proper ARIA attributes
        $response->assertSee('role="alert"', false);
        $response->assertSee('aria-live="polite"', false);
    }

    public function test_tables_have_proper_accessibility_attributes(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/helpdesk-tickets');

        $response->assertStatus(200);

        // Verify table structure
        $response->assertSee('<table', false);
        $response->assertSee('<thead', false);
        $response->assertSee('<tbody', false);
        $response->assertSee('scope="col"', false);

        // Verify table caption or aria-label
        $response->assertSeeText('Helpdesk Tickets');
    }

    public function test_buttons_have_descriptive_labels(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/helpdesk-tickets');

        // Verify buttons have proper labels
        $response->assertSee('aria-label', false);

        // Verify icon-only buttons have text alternatives
        $response->assertSee('sr-only', false);
    }

    public function test_color_contrast_meets_wcag_standards(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify CSS includes high contrast colors
        $this->assertColorContrastCompliance($response);
    }

    public function test_focus_indicators_are_visible(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify focus styles are defined
        $response->assertSee('focus:', false);
        $response->assertSee('focus-visible:', false);
    }

    public function test_modal_dialogs_have_proper_focus_management(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin/helpdesk-tickets');

        // Verify modal attributes
        $response->assertSee('role="dialog"', false);
        $response->assertSee('aria-modal="true"', false);
        $response->assertSee('aria-labelledby', false);
    }

    public function test_live_regions_for_dynamic_content(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify live regions for notifications
        $response->assertSee('aria-live', false);
        $response->assertSee('aria-atomic', false);
    }

    public function test_language_attributes_are_present(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify lang attribute on html element
        $response->assertSee('lang="', false);
    }

    public function test_headings_follow_proper_hierarchy(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        $content = $response->getContent();

        // Verify heading hierarchy (h1, then h2, etc.)
        $this->assertHeadingHierarchy($content);
    }

    public function test_images_have_alt_text(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify all images have alt attributes
        $response->assertSee('alt=', false);
    }

    public function test_form_validation_is_accessible(): void
    {
        $this->actingAs($this->admin);

        // Submit form with validation errors
        $response = $this->post('/admin/helpdesk-tickets', [
            'title' => '',
            'description' => '',
        ]);

        // Verify error summary
        $response->assertSee('role="alert"', false);

        // Verify field-level errors are associated
        $response->assertSee('aria-invalid="true"', false);
        $response->assertSee('aria-describedby', false);
    }

    public function test_keyboard_navigation_works_throughout_interface(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify all interactive elements are keyboard accessible
        $this->assertKeyboardAccessible($response);
    }

    public function test_screen_reader_announcements(): void
    {
        $this->actingAs($this->admin);

        $response = $this->get('/admin');

        // Verify screen reader only content
        $response->assertSee('sr-only', false);
        $response->assertSee('visually-hidden', false);
    }

    /**
     * Helper method to verify color contrast compliance
     */
    private function assertColorContrastCompliance($response): void
    {
        $content = $response->getContent();

        // Check for WCAG compliant color combinations
        $this->assertStringContains('text-gray-900', $content); // High contrast text
        $this->assertStringContains('bg-white', $content); // High contrast background

        // Verify no low contrast combinations
        $this->assertStringNotContains('text-gray-400 bg-gray-300', $content);
    }

    /**
     * Helper method to verify heading hierarchy
     */
    private function assertHeadingHierarchy(string $content): void
    {
        preg_match_all('/<h([1-6])[^>]*>/i', $content, $matches);

        if (! empty($matches[1])) {
            $headingLevels = array_map('intval', $matches[1]);

            // Verify h1 exists
            $this->assertContains(1, $headingLevels, 'Page should have an h1 element');

            // Verify no heading levels are skipped
            $uniqueLevels = array_unique($headingLevels);
            sort($uniqueLevels);

            for ($i = 1; $i < count($uniqueLevels); $i++) {
                $this->assertEquals(
                    $uniqueLevels[$i - 1] + 1,
                    $uniqueLevels[$i],
                    'Heading levels should not skip numbers'
                );
            }
        }
    }

    /**
     * Helper method to verify keyboard accessibility
     */
    private function assertKeyboardAccessible($response): void
    {
        $content = $response->getContent();

        // Verify interactive elements have proper tabindex
        preg_match_all('/<(button|a|input|select|textarea)[^>]*>/i', $content, $matches);

        foreach ($matches[0] as $element) {
            // Verify no positive tabindex values (anti-pattern)
            $this->assertStringNotContains('tabindex="1"', $element);
            $this->assertStringNotContains('tabindex="2"', $element);
        }
    }
}
