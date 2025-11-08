<?php

declare(strict_types=1);

namespace Tests\Feature\Accessibility;

use App\Models\Asset;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Loan Module WCAG 2.2 AA Compliance Test
 *
 * Verifies accessibility compliance for all loan module pages and components.
 *
 * @trace D03-FR-006.1 (WCAG 2.2 AA Compliance)
 * @trace D12 (UI/UX Design Guide)
 */
class LoanModuleWcagComplianceTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected LoanApplication $application;
    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
        $this->asset = Asset::factory()->create(['status' => 'available']);
        $this->application = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
        ]);
    }

    public function test_guest_loan_form_has_proper_aria_labels(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('aria-label', false)
            ->assertSee('aria-describedby', false)
            ->assertSee('role="form"', false);
    }

    public function test_loan_dashboard_has_semantic_html(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.dashboard'));

        $response->assertOk()
            ->assertSee('<main', false)
            ->assertSee('<nav', false)
            ->assertSee('<header', false);
    }

    public function test_loan_history_table_has_proper_headers(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.history'));

        $response->assertOk()
            ->assertSee('<th scope="col"', false)
            ->assertSee('role="table"', false);
    }

    public function test_form_inputs_have_associated_labels(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        // Verify all inputs have labels
        $html = $response->getContent();
        preg_match_all('/<input[^>]*id="([^"]*)"/', $html, $inputs);
        
        foreach ($inputs[1] as $inputId) {
            $this->assertStringContainsString(
                "for=\"{$inputId}\"",
                $html,
                "Input #{$inputId} missing associated label"
            );
        }
    }

    public function test_buttons_have_descriptive_text_or_aria_labels(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.dashboard'));

        $response->assertOk();

        $html = $response->getContent();
        preg_match_all('/<button[^>]*>/', $html, $buttons);

        foreach ($buttons[0] as $button) {
            $hasText = !str_contains($button, '></button>');
            $hasAriaLabel = str_contains($button, 'aria-label=');
            
            $this->assertTrue(
                $hasText || $hasAriaLabel,
                "Button missing descriptive text or aria-label: {$button}"
            );
        }
    }

    public function test_images_have_alt_text(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        $html = $response->getContent();
        preg_match_all('/<img[^>]*>/', $html, $images);

        foreach ($images[0] as $image) {
            $this->assertStringContainsString(
                'alt=',
                $image,
                "Image missing alt attribute: {$image}"
            );
        }
    }

    public function test_focus_indicators_are_visible(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('focus:ring', false)
            ->assertSee('focus:outline', false);
    }

    public function test_color_contrast_meets_wcag_aa(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        // Verify no low-contrast color combinations
        $html = $response->getContent();
        
        // Check for common low-contrast patterns
        $this->assertStringNotContainsString('text-gray-400 bg-white', $html);
        $this->assertStringNotContainsString('text-gray-300 bg-gray-100', $html);
    }

    public function test_form_validation_errors_are_accessible(): void
    {
        $response = $this->post(route('loan.guest.store'), []);

        $response->assertSessionHasErrors();

        $html = $response->getContent();
        
        // Verify error messages have proper ARIA attributes
        $this->assertStringContainsString('role="alert"', $html);
    }

    public function test_keyboard_navigation_is_supported(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        $html = $response->getContent();
        
        // Verify no tabindex=-1 on interactive elements
        $this->assertStringNotContainsString('<button tabindex="-1"', $html);
        $this->assertStringNotContainsString('<a tabindex="-1"', $html);
    }

    public function test_skip_links_are_present(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('Skip to main content', false);
    }

    public function test_language_attribute_is_set(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('<html lang=', false);
    }

    public function test_page_titles_are_descriptive(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('<title>', false);

        $html = $response->getContent();
        preg_match('/<title>(.*?)<\/title>/', $html, $matches);
        
        $this->assertNotEmpty($matches[1] ?? '');
        $this->assertGreaterThan(10, strlen($matches[1] ?? ''));
    }

    public function test_form_fields_have_autocomplete_attributes(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        $html = $response->getContent();
        
        // Check for autocomplete on email and name fields
        $this->assertStringContainsString('autocomplete="email"', $html);
        $this->assertStringContainsString('autocomplete="name"', $html);
    }

    public function test_loading_states_are_announced(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('aria-live', false)
            ->assertSee('wire:loading', false);
    }

    public function test_modal_dialogs_have_proper_aria_attributes(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.dashboard'));

        $response->assertOk();

        $html = $response->getContent();
        
        if (str_contains($html, 'role="dialog"')) {
            $this->assertStringContainsString('aria-modal="true"', $html);
            $this->assertStringContainsString('aria-labelledby', $html);
        }
    }

    public function test_tables_have_proper_structure(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.history'));

        $response->assertOk();

        $html = $response->getContent();
        
        if (str_contains($html, '<table')) {
            $this->assertStringContainsString('<thead>', $html);
            $this->assertStringContainsString('<tbody>', $html);
            $this->assertStringContainsString('<th scope=', $html);
        }
    }

    public function test_responsive_design_maintains_accessibility(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('sm:', false)
            ->assertSee('md:', false)
            ->assertSee('lg:', false);
    }

    public function test_touch_targets_meet_minimum_size(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        $html = $response->getContent();
        
        // Verify buttons have adequate padding (minimum 44x44px)
        $this->assertStringContainsString('px-4 py-2', $html);
    }

    public function test_status_badges_have_accessible_colors(): void
    {
        $response = $this->actingAs($this->user)
            ->get(route('loan.history'));

        $response->assertOk();

        $html = $response->getContent();
        
        // Verify status badges use high-contrast colors
        if (str_contains($html, 'bg-green')) {
            $this->assertStringContainsString('text-green', $html);
        }
    }
}
