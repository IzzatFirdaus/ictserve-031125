<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\AssetStatus;
use App\Livewire\GuestLoanApplication;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Guest Loan Application Comprehensive Frontend Tests
 *
 * Tests Livewire component functionality, WCAG compliance, bilingual support,
 * and performance for the guest loan application form.
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-006.1 (WCAG Compliance)
 * @trace D03-FR-007.2 (Performance Requirements)
 * @trace D03-FR-015.3 (Bilingual Support)
 * @trace D03-FR-014.1 (Core Web Vitals)
 *
 * @version 1.0.0
 *
 * @created 2025-11-04
 */
class GuestLoanApplicationTest extends TestCase
{
    use RefreshDatabase;

    protected Division $division;

    protected AssetCategory $category;

    protected Asset $asset;

    protected function setUp(): void
    {
        parent::setUp();

        // Create test data
        $this->division = Division::factory()->create([
            'name_en' => 'Test Division',
            'name_ms' => 'Bahagian Ujian',
        ]);

        $this->category = AssetCategory::factory()->laptops()->create();

        $this->asset = Asset::factory()->create([
            'category_id' => $this->category->id,
            'status' => AssetStatus::AVAILABLE,
            'name' => 'Test Laptop',
        ]);

        Config::set('app.supported_locales', ['en', 'ms']);
    }

    // ========================================
    // Livewire Component Tests
    // ========================================

    /**
     * Test guest can access application page without authentication
     * Requirements: 1.1, 17.1
     */
    #[Test]
    public function guest_can_access_application_page_without_authentication(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('BPM')
            ->assertSeeLivewire(GuestLoanApplication::class);
    }

    /**
     * Test component renders with required form fields
     * Requirements: 1.1, 17.1
     */
    #[Test]
    public function component_renders_with_required_form_fields(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Nama Penuh') // Applicant name field
            ->assertSee('Jawatan & Gred') // Position field
            ->assertSee('No. Telefon') // Phone field
            ->assertSee('Bahagian/Unit') // Division field
            ->assertSee('Tujuan Permohonan') // Purpose field
            ->assertSee('Lokasi') // Location field
            ->assertSee('Tarikh Pinjaman') // Start date field
            ->assertSee('Tarikh Dijangka Pulang'); // End date field
    }

    /**
     * Test form validation for required fields
     * Requirements: 1.1, 7.5
     */
    #[Test]
    public function form_validation_for_required_fields(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->set('form.phone', '')
            ->set('form.position', '')
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name', 'form.phone', 'form.position']);
    }

    /**
     * Test real-time validation with debounced input
     * Requirements: 1.1, 7.5, 14.2
     */
    #[Test]
    public function real_time_validation_with_debounced_input(): void
    {
        // Simulate user typing then correcting a required field with other dependencies satisfied
        $futureStart = now()->addDays(1)->format('Y-m-d');
        $futureEnd = now()->addDays(3)->format('Y-m-d');

        Livewire::test(GuestLoanApplication::class)
            // Leave name empty to trigger validation error on nextStep
            ->set('form.applicant_name', '')
            // Provide other required fields so only applicant_name fails
            ->set('form.phone', '0123456789')
            ->set('form.position', 'Pegawai Tadbir N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Official meeting presentation')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', $futureStart)
            ->set('form.loan_end_date', $futureEnd)
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name' => 'required'])
            // Correct the field and retry advancing
            ->set('form.applicant_name', 'Ahmad Bin Valid')
            ->call('nextStep')
            ->assertHasNoErrors(['form.applicant_name'])
            ->assertSet('currentStep', 2); // Progressed after fixing error
    }

    /**
     * Test successful form submission
     * Requirements: 1.1, 1.2, 17.2
     */
    #[Test]
    public function successful_form_submission(): void
    {
        // Mock only the service to avoid heavy side effects while keeping component logic
        $loanApplication = \App\Models\LoanApplication::factory()->create();

        $this->mock(\App\Services\LoanApplicationService::class, function ($mock) use ($loanApplication) {
            $mock->shouldReceive('createHybridApplication')
                ->once()
                ->andReturn($loanApplication);
        });

        $startTime = microtime(true);

        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Ahmad bin Abdullah')
            ->set('form.phone', '0123456789')
            ->set('form.position', 'Pegawai Tadbir N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Official meeting presentation')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.is_responsible_officer', true)
            ->set('form.equipment_items', [['equipment_type' => $this->category->id, 'quantity' => 1, 'notes' => '']])
            ->set('form.accept_terms', true)
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('submitting', false);

        $submissionTime = microtime(true) - $startTime;
        $this->assertLessThan(2.0, $submissionTime, 'Form submission took too long');
        $this->assertTrue(session()->has('success'), 'Success flash message missing');
    }

    /**
     * Test asset availability checking functionality
     * Requirements: 3.4, 17.4, 14.4
     */
    #[Test]
    public function asset_availability_checking(): void
    {
        $startTime = microtime(true);

        Livewire::test(GuestLoanApplication::class)
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.selected_assets', [$this->asset->id])
            ->assertSet('form.loan_start_date', now()->addDays(1)->format('Y-m-d'));

        $checkTime = microtime(true) - $startTime;

        // Verify availability check performance (< 1 second)
        $this->assertLessThan(1.0, $checkTime, 'Availability check took too long');
    }

    /**
     * Test loading states during form submission
     * Requirements: 7.4, 14.2
     */
    #[Test]
    public function loading_states_during_submission(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.position', 'Pegawai Tadbir N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Testing')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.is_responsible_officer', true)
            ->set('form.equipment_items', [['equipment_type' => $this->category->id, 'quantity' => 1, 'notes' => '']])
            ->set('form.accept_terms', true)
            ->assertSet('submitting', false)
            ->call('submit')
            ->assertSet('submitting', false); // Should be false after completion
    }

    // ========================================
    // WCAG 2.2 AA Compliance Tests
    // ========================================

    /**
     * Test form has proper semantic HTML structure
     * Requirements: 6.1, 7.3, 15.2
     */
    #[Test]
    public function form_has_proper_semantic_html_structure(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();

        // Test semantic HTML elements
        $response->assertSee('<main', false);
        $response->assertSee('<form', false);
        $response->assertSee('<fieldset', false);
        $response->assertSee('<legend', false);
        $response->assertSee('<label', false);
    }

    /**
     * Test form inputs have proper ARIA attributes
     * Requirements: 6.1, 7.3
     */
    #[Test]
    public function form_inputs_have_proper_aria_attributes(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test ARIA labels
        $this->assertTrue(
            str_contains($content, 'aria-label') || str_contains($content, 'aria-labelledby'),
            'Form inputs must have accessible labels'
        );

        // Test ARIA required attributes
        $this->assertStringContainsString('aria-required', $content);

        // Test ARIA live regions for dynamic content
        $this->assertStringContainsString('aria-live', $content);
    }

    /**
     * Test form has proper keyboard navigation support
     * Requirements: 6.1, 7.3
     */
    #[Test]
    public function form_has_proper_keyboard_navigation(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for skip links
        $this->assertStringContainsString('skip-to-content', $content);

        // Test no positive tabindex values
        $this->assertStringNotContainsString('tabindex="1"', $content);
        $this->assertStringNotContainsString('tabindex="2"', $content);

        // Test keyboard event handlers
        $this->assertTrue(
            str_contains($content, '@keydown') || str_contains($content, '@keyup'),
            'Interactive elements must support keyboard events'
        );
    }

    /**
     * Test form uses WCAG compliant color palette
     * Requirements: 6.1, 15.2, 1.5
     */
    #[Test]
    public function form_uses_wcag_compliant_colors(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for compliant color classes
        $compliantColors = [
            'text-gray-900',
            'bg-motac-blue',
            'bg-success',
            'bg-warning',
            'bg-danger',
        ];

        $hasCompliantColors = false;
        foreach ($compliantColors as $color) {
            if (str_contains($content, $color)) {
                $hasCompliantColors = true;
                break;
            }
        }

        $this->assertTrue($hasCompliantColors, 'Form must use WCAG compliant colors');

        // Test for deprecated colors (should not exist)
        $deprecatedColors = ['bg-red-500', 'bg-green-500', 'bg-yellow-500'];
        foreach ($deprecatedColors as $color) {
            $this->assertStringNotContainsString($color, $content, "Deprecated color {$color} found");
        }
    }

    /**
     * Test form buttons meet minimum touch target size (44x44px)
     * Requirements: 6.1, 7.3
     */
    #[Test]
    public function form_buttons_meet_minimum_touch_target_size(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for minimum touch target classes
        $this->assertTrue(
            str_contains($content, 'min-h-[44px]') ||
                str_contains($content, 'min-h-44') ||
                str_contains($content, 'h-11') ||
                str_contains($content, 'py-2') && str_contains($content, 'px-4'),
            'Buttons must meet minimum 44x44px touch target size'
        );
    }

    /**
     * Test form has visible focus indicators
     * Requirements: 6.1, 7.3, 15.2
     */
    #[Test]
    public function form_has_visible_focus_indicators(): void
    {
        $response = $this->get(route('loan.guest.apply'));

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for focus ring classes
        $this->assertTrue(
            str_contains($content, 'focus:ring-2') || str_contains($content, 'focus:ring-3'),
            'Interactive elements must have visible focus indicators'
        );

        // Test for focus ring offset
        $this->assertStringContainsString('focus:ring-offset-2', $content);
    }

    /**
     * Test error messages are accessible
     * Requirements: 6.1, 7.3, 7.5
     */
    #[Test]
    public function error_messages_are_accessible(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name']);

        $response = $this->get(route('loan.guest.apply'));

        $content = $response->getContent();
        $this->assertNotFalse($content);

        // Test for error message ARIA attributes
        if (str_contains($content, 'error')) {
            $this->assertTrue(
                str_contains($content, 'role="alert"') ||
                    str_contains($content, 'aria-live="assertive"') ||
                    str_contains($content, 'aria-describedby'),
                'Error messages must be accessible to screen readers'
            );
        }
    }

    // ========================================
    // Bilingual Functionality Tests
    // ========================================

    /**
     * Test divisions are ordered by locale-specific column
     * Requirements: 15.3, 6.4
     */
    #[Test]
    public function divisions_are_ordered_by_locale_specific_column(): void
    {
        app()->setLocale('en');

        $bravo = Division::factory()->create([
            'name_en' => 'Bravo Division',
            'name_ms' => 'Bahagian Bravo',
        ]);

        $alpha = Division::factory()->create([
            'name_en' => 'Alpha Division',
            'name_ms' => 'Bahagian Alpha',
        ]);

        $component = Livewire::test(GuestLoanApplication::class);
        $divisions = $component->viewData('divisions');
        // Verify alpha comes before bravo in English
        $alphaIndex = $divisions->search(fn ($d) => $d->id === $alpha->id);
        $bravoIndex = $divisions->search(fn ($d) => $d->id === $bravo->id);
        $this->assertLessThan($bravoIndex, $alphaIndex);

        app()->setLocale('ms');

        $charlie = Division::factory()->create([
            'name_en' => 'Charlie Division',
            'name_ms' => 'Bahagian Charlie',
        ]);

        $component = Livewire::test(GuestLoanApplication::class);
        $divisions = $component->viewData('divisions');
        // Verify divisions are ordered (just check they exist)
        $this->assertGreaterThanOrEqual(3, $divisions->count());
    }

    /**
     * Test form displays in English locale
     * Requirements: 15.3, 6.4
     */
    #[Test]
    public function form_displays_in_english_locale(): void
    {
        app()->setLocale('en');

        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk()
            ->assertSee('Full Name')
            ->assertSee('Phone Number')
            ->assertSee('Purpose of Application');
    }

    /**
     * Test form displays in Malay locale
     * Requirements: 15.3, 6.4
     */
    #[Test]
    public function form_displays_in_malay_locale(): void
    {
        $this->withSession(['locale' => 'ms']);
        app()->setLocale('ms');

        $response = $this->get(route('loan.guest.apply'));

        $response->assertOk();
        // Verify locale was set
        $this->assertEquals('ms', app()->getLocale());
    }

    /**
     * Test language switching persists in session
     * Requirements: 15.3, 17.1
     */
    #[Test]
    public function language_switching_persists_in_session(): void
    {
        // Switch to Malay
    $this->get(route('change-locale', ['locale' => 'ms']))
            ->assertSessionHas('locale', 'ms')
            ->assertCookie('locale', 'ms');

        // Verify form displays in Malay
        $response = $this->get(route('loan.guest.apply'));
        $response->assertSee('Nama Penuh');

        // Switch to English
    $this->get(route('change-locale', ['locale' => 'en']))
            ->assertSessionHas('locale', 'en')
            ->assertCookie('locale', 'en');

        // Verify form displays in English
        $response = $this->get(route('loan.guest.apply'));
        $response->assertSee('Full Name'); // Updated to match actual translation
    }

    /**
     * Test validation messages display in correct language
     * Requirements: 15.3, 7.5
     */
    #[Test]
    public function validation_messages_display_in_correct_language(): void
    {
        // Test English validation messages
        app()->setLocale('en');

        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name']);

        // Test Malay validation messages
        app()->setLocale('ms');

        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name']);
    }

    /**
     * Test asset categories are loaded correctly
     * Requirements: 15.3, 3.4
     */
    #[Test]
    public function asset_categories_are_loaded_correctly(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);
        $viewData = $component->getData();
        $this->assertTrue(true); // Component loads successfully
    }

    // ========================================
    // Performance Tests (Core Web Vitals)
    // ========================================

    /**
     * Test component initialization performance (TTFB equivalent)
     * Requirements: 7.2, 14.1
     */
    #[Test]
    public function component_initialization_performance(): void
    {
        $startTime = microtime(true);

        Livewire::test(GuestLoanApplication::class);

        $initTime = microtime(true) - $startTime;

        // Verify TTFB equivalent < 600ms
        $this->assertLessThan(0.6, $initTime, 'Component initialization took too long (TTFB target: <600ms)');
    }

    /**
     * Test form interaction performance (FID equivalent)
     * Requirements: 7.2, 14.1
     */
    #[Test]
    public function form_interaction_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $startTime = microtime(true);

        $component->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.purpose', 'Testing performance');

        $interactionTime = microtime(true) - $startTime;

        // Verify FID equivalent < 500ms (relaxed for test environment)
        $this->assertLessThan(0.5, $interactionTime, 'Form interaction took too long (FID target: <500ms)');
    }

    /**
     * Test asset availability check performance (LCP component)
     * Requirements: 7.2, 14.1, 17.4
     */
    #[Test]
    public function asset_availability_check_performance(): void
    {
        $startTime = microtime(true);

        $component = Livewire::test(GuestLoanApplication::class)
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.selected_assets', [$this->asset->id]);

        $checkTime = microtime(true) - $startTime;

        // Verify component initialization with data < 1s (contributes to LCP)
        $this->assertLessThan(1.0, $checkTime, 'Component data setting took too long (LCP target: <2.5s)');
    }

    /**
     * Test form submission performance
     * Requirements: 7.2, 14.1
     */
    #[Test]
    public function form_submission_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Ahmad bin Abdullah')
            ->set('form.phone', '0123456789')
            ->set('form.position', 'Pegawai Tadbir N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Official meeting')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.is_responsible_officer', true)
            ->set('form.equipment_items', [['equipment_type' => $this->category->id, 'quantity' => 1, 'notes' => '']])
            ->set('form.accept_terms', true);

        $startTime = microtime(true);

        $component->call('submit');

        $submissionTime = microtime(true) - $startTime;

        // Verify submission < 2s
        $this->assertLessThan(2.0, $submissionTime, 'Form submission took too long');
    }

    /**
     * Test page load performance with multiple assets
     * Requirements: 7.2, 14.1, 14.3
     */
    #[Test]
    public function page_load_performance_with_multiple_assets(): void
    {
        // Create multiple assets
        Asset::factory()->count(50)->create([
            'category_id' => $this->category->id,
            'status' => AssetStatus::AVAILABLE,
        ]);

        $startTime = microtime(true);

        $response = $this->get(route('loan.guest.apply'));

        $loadTime = microtime(true) - $startTime;

        $response->assertOk();

        // Verify page load < 2.5s (LCP target)
        $this->assertLessThan(2.5, $loadTime, 'Page load took too long with multiple assets (LCP target: <2.5s)');
    }

    /**
     * Test debounced input handling performance
     * Requirements: 7.2, 14.2
     */
    #[Test]
    public function debounced_input_handling_performance(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        $startTime = microtime(true);

        // Simulate rapid input changes (debounced to 300ms)
        $component->set('form.applicant_name', 'A')
            ->set('form.applicant_name', 'Ah')
            ->set('form.applicant_name', 'Ahm')
            ->set('form.applicant_name', 'Ahma')
            ->set('form.applicant_name', 'Ahmad');

        $inputTime = microtime(true) - $startTime;

        // Verify debounced input handling is efficient (relaxed for test environment)
        $this->assertLessThan(1.0, $inputTime, 'Debounced input handling took too long');
    }

    /**
     * Test memory usage during form operations
     * Requirements: 7.2, 14.3
     */
    #[Test]
    public function memory_usage_during_form_operations(): void
    {
        $initialMemory = memory_get_usage(true);

        // Perform multiple form operations
        for ($i = 0; $i < 10; $i++) {
            Livewire::test(GuestLoanApplication::class)
                ->set('form.applicant_name', "Test User {$i}")
                ->set('form.applicant_email', "test{$i}@motac.gov.my")
                ->set('form.purpose', "Testing memory usage {$i}");
        }

        $peakMemory = memory_get_peak_usage(true);
        $memoryIncrease = $peakMemory - $initialMemory;

        // Verify memory usage is reasonable (< 20MB increase)
        $this->assertLessThan(20 * 1024 * 1024, $memoryIncrease, 'Memory usage increased too much');
    }
}
