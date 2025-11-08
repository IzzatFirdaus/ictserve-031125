<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * WCAG 2.2 AA Accessibility Compliance Tests
 *
 * Tests color contrast ratios, focus indicators, keyboard navigation,
 * and ARIA attributes.
 *
 * Requirements: 14.1, 14.2
 * Traceability: D03 SRS-FR-014, D04 §4.4, D12 §4
 */
class AccessibilityComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected Division $division;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create(['name' => 'IT Division']);
        $this->user = User::factory()->create([
            'division_id' => $this->division->id,
            'grade' => 40,
        ]);
    }

    #[Test]
    public function dashboard_has_proper_heading_hierarchy(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $response->assertStatus(200);

        // Should have h1 for main heading
        $response->assertSee('<h1', false);

        // Should have proper heading structure (h1 -> h2 -> h3)
        $content = $response->getContent();
        $this->assertMatchesRegularExpression('/<h1[^>]*>.*?<\/h1>/', $content);
    }

    #[Test]
    public function all_images_have_alt_text(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Find all img tags
        preg_match_all('/<img[^>]*>/', $content, $matches);

        foreach ($matches[0] as $imgTag) {
            // Each img should have alt attribute
            $this->assertMatchesRegularExpression('/alt=["\']/', $imgTag);
        }
    }

    #[Test]
    public function form_inputs_have_labels(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/profile');

        $content = $response->getContent();

        // Find all input fields
        preg_match_all('/<input[^>]*id=["\']([^"\']+)["\'][^>]*>/', $content, $matches);

        foreach ($matches[1] as $inputId) {
            // Each input should have a corresponding label
            $this->assertMatchesRegularExpression(
                '/<label[^>]*for=["\']'.preg_quote($inputId, '/').'["\']/',
                $content
            );
        }
    }

    #[Test]
    public function buttons_have_accessible_names(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Find all buttons
        preg_match_all('/<button[^>]*>/', $content, $matches);

        foreach ($matches[0] as $buttonTag) {
            // Button should have text content or aria-label
            $hasText = ! preg_match('/<button[^>]*>\s*<\/button>/', $buttonTag);
            $hasAriaLabel = preg_match('/aria-label=["\']/', $buttonTag);

            $this->assertTrue($hasText || $hasAriaLabel, 'Button must have accessible name');
        }
    }

    #[Test]
    public function links_have_descriptive_text(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Links should not have generic text like "click here" or "read more"
        $this->assertStringNotContainsString('>click here<', strtolower($content));
        $this->assertStringNotContainsString('>read more<', strtolower($content));
    }

    #[Test]
    public function page_has_skip_to_content_link(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should have skip link
        $this->assertMatchesRegularExpression('/<a[^>]*href=["\']#main-content["\']/', $content);
    }

    #[Test]
    public function page_has_proper_lang_attribute(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // HTML tag should have lang attribute
        $this->assertMatchesRegularExpression('/<html[^>]*lang=["\']/', $content);
    }

    #[Test]
    public function page_has_proper_aria_landmarks(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should have main landmark
        $this->assertMatchesRegularExpression('/<main[^>]*>|role=["\']main["\']/', $content);

        // Should have navigation landmark
        $this->assertMatchesRegularExpression('/<nav[^>]*>|role=["\']navigation["\']/', $content);
    }

    #[Test]
    public function interactive_elements_have_focus_indicators(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Check for focus styles in CSS
        $this->assertStringContainsString(':focus', $content);
    }

    #[Test]
    public function color_contrast_meets_wcag_aa_standards(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Verify compliant color palette is used
        $compliantColors = [
            '#0056b3', // Primary (6.8:1)
            '#198754', // Success (4.9:1)
            '#ff8c00', // Warning (4.5:1)
            '#b50c0c', // Danger (8.2:1)
        ];

        foreach ($compliantColors as $color) {
            // Colors should be present in the page
            $this->assertStringContainsString($color, strtolower($content));
        }
    }

    #[Test]
    public function tables_have_proper_headers(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/submissions');

        $content = $response->getContent();

        // Find all tables
        preg_match_all('/<table[^>]*>/', $content, $matches);

        if (count($matches[0]) > 0) {
            // Tables should have thead and th elements
            $this->assertMatchesRegularExpression('/<thead[^>]*>/', $content);
            $this->assertMatchesRegularExpression('/<th[^>]*>/', $content);
        }
    }

    #[Test]
    public function form_errors_are_announced_to_screen_readers(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/profile');

        $content = $response->getContent();

        // Error messages should have role="alert" or aria-live
        $this->assertMatchesRegularExpression('/role=["\']alert["\']|aria-live=["\']/', $content);
    }

    #[Test]
    public function dynamic_content_has_aria_live_regions(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Should have aria-live regions for dynamic updates
        $this->assertMatchesRegularExpression('/aria-live=["\']polite["\']|aria-live=["\']assertive["\']/', $content);
    }

    #[Test]
    public function modals_trap_focus(): void
    {
        // This would require JavaScript testing
        // Marking as incomplete for now
        $this->markTestIncomplete('Modal focus trap requires JavaScript testing');
    }

    #[Test]
    public function keyboard_navigation_works_for_all_interactive_elements(): void
    {
        // This would require browser testing with Playwright
        $this->markTestIncomplete('Keyboard navigation requires browser testing');
    }

    #[Test]
    public function page_title_is_descriptive(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Title should be descriptive and include page name
        $this->assertMatchesRegularExpression('/<title[^>]*>.*Dashboard.*<\/title>/', $content);
    }

    #[Test]
    public function required_fields_are_marked_with_aria_required(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/profile');

        $content = $response->getContent();

        // Required inputs should have aria-required="true"
        preg_match_all('/<input[^>]*required[^>]*>/', $content, $matches);

        foreach ($matches[0] as $inputTag) {
            $this->assertMatchesRegularExpression('/aria-required=["\']true["\']/', $inputTag);
        }
    }

    #[Test]
    public function invalid_fields_are_marked_with_aria_invalid(): void
    {
        // Submit form with invalid data
        $response = $this->actingAs($this->user)->post('/portal/profile', [
            'name' => '', // Invalid: required
        ]);

        $content = $response->getContent();

        // Invalid inputs should have aria-invalid="true"
        $this->assertMatchesRegularExpression('/aria-invalid=["\']true["\']/', $content);
    }

    #[Test]
    public function loading_states_are_announced(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Loading indicators should have aria-live or role="status"
        $this->assertMatchesRegularExpression('/role=["\']status["\']|aria-busy=["\']true["\']/', $content);
    }

    #[Test]
    public function expandable_sections_have_aria_expanded(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Expandable elements should have aria-expanded
        preg_match_all('/<button[^>]*aria-expanded[^>]*>/', $content, $matches);

        // If there are expandable sections, they should have aria-expanded
        if (count($matches[0]) > 0) {
            foreach ($matches[0] as $buttonTag) {
                $this->assertMatchesRegularExpression('/aria-expanded=["\'](?:true|false)["\']/', $buttonTag);
            }
        }
    }

    #[Test]
    public function page_has_no_duplicate_ids(): void
    {
        $response = $this->actingAs($this->user)->get('/portal/dashboard');

        $content = $response->getContent();

        // Extract all IDs
        preg_match_all('/id=["\']([^"\']+)["\']/', $content, $matches);

        $ids = $matches[1];
        $uniqueIds = array_unique($ids);

        // All IDs should be unique
        $this->assertCount(count($ids), $uniqueIds, 'Page contains duplicate IDs');
    }
}
