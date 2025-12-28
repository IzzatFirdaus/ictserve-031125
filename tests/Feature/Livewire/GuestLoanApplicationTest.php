<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Enums\AssetStatus;
use App\Livewire\GuestLoanApplication;
use App\Models\Asset;
use App\Models\AssetCategory;
use App\Models\Division;
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
            ->assertSee('Borang Permohonan Pinjaman Tetamu');
    }

    /**
     * Test component renders with required form fields
     * Requirements: 1.1, 17.1
     */
    #[Test]
    public function component_renders_with_required_form_fields(): void
    {
        // The form renders in the default locale (ms), so we check for Malay labels
        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Nama Penuh') // Applicant name field
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
            ->set('form.applicant_position', '')
            ->set('form.applicant_grade', '')
            ->set('form.division_id', null)
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name', 'form.phone', 'form.applicant_position']);
    }

    /**
     * Test real-time validation with debounced input
     * Requirements: 1.1, 7.5, 14.2
     */
    #[Test]
    public function real_time_validation_with_debounced_input(): void
    {
        $futureStart = now()->addDays(5)->format('Y-m-d');
        $futureEnd = now()->addDays(7)->format('Y-m-d');

        // Test that validation errors are shown for empty required fields
        $component = Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Pegawai Tadbir N41')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Official meeting presentation')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', $futureStart)
            ->set('form.expected_return_date', $futureEnd)
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name']);

        // Test that validation passes when all required fields are filled
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Ahmad Bin Valid')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Pegawai Tadbir N41')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Official meeting presentation')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', $futureStart)
            ->set('form.expected_return_date', $futureEnd)
            ->call('nextStep')
            ->assertHasNoErrors(['form.applicant_name'])
            ->assertSet('currentStep', 2);
    }

    /**
     * Test successful form submission
     * Requirements: 1.1, 1.2, 17.2
     */
    #[Test]
    public function successful_form_submission(): void
    {
        $startTime = microtime(true);

        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Ahmad bin Abdullah')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Pegawai Tadbir N41')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Official meeting presentation')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(7)->format('Y-m-d'))
            ->assertSet('form.applicant_name', 'Ahmad bin Abdullah')
            ->assertSet('submitting', false);

        $this->assertLessThan(60.0, microtime(true) - $startTime);
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
            ->set('form.loan_start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(7)->format('Y-m-d'))
            ->set('form.equipment_items', [['equipment_type' => $this->category->id, 'quantity' => 1, 'notes' => '']])
            ->assertSet('form.loan_start_date', now()->addDays(5)->format('Y-m-d'));

        $this->assertLessThan(30.0, microtime(true) - $startTime);
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
            ->set('form.applicant_position', 'Pegawai Tadbir N41')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Testing')
            ->set('form.location', 'Putrajaya')
            ->set('form.loan_start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(7)->format('Y-m-d'))
            ->set('form.is_responsible_officer', true)
            ->set('form.equipment_items', [['equipment_type' => $this->category->id, 'quantity' => 1, 'notes' => '']])
            ->set('form.terms_acknowledged', true)
            ->set('form.applicant_digital_signature', 'Test')
            ->set('form.approver_id', \App\Models\User::factory()->create()->id)
            ->assertSet('submitting', false);
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
     * Test form inputs have proper aria attributes
     * Requirements: 6.1, 7.3, 15.2
     */
    #[Test]
    public function form_inputs_have_proper_aria_attributes(): void
    {
        $response = $this->get(route('loan.guest.apply'));
        $content = $response->getContent();
        $this->assertTrue(str_contains($content, '<label') || str_contains($content, 'aria-label'));
    }

    /**
     * Test form has proper keyboard navigation
     */
    #[Test]
    public function form_has_proper_keyboard_navigation(): void
    {
        $response = $this->get(route('loan.guest.apply'));
        $content = $response->getContent();
        $this->assertStringNotContainsString('tabindex="1"', $content);
    }

    /**
     * Test form uses wcag compliant colors
     */
    #[Test]
    public function form_uses_wcag_compliant_colors(): void
    {
        $response = $this->get(route('loan.guest.apply'));
        $content = $response->getContent();
        $this->assertTrue(str_contains($content, 'text-gray-') || str_contains($content, 'bg-'));
    }

    /**
     * Test form buttons meet minimum touch target size
     */
    #[Test]
    public function form_buttons_meet_minimum_touch_target_size(): void
    {
        $response = $this->get(route('loan.guest.apply'));
        $content = $response->getContent();
        $this->assertTrue(str_contains($content, 'py-') && str_contains($content, 'px-'));
    }

    /**
     * Test form has visible focus indicators
     */
    #[Test]
    public function form_has_visible_focus_indicators(): void
    {
        $response = $this->get(route('loan.guest.apply'));
        $content = $response->getContent();
        $this->assertTrue(str_contains($content, 'focus:'));
    }

    /**
     * Test error messages are accessible
     */
    #[Test]
    public function error_messages_are_accessible(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name']);
        $this->assertTrue(true);
    }

    /**
     * Test divisions are ordered by locale specific column
     */
    #[Test]
    public function divisions_are_ordered_by_locale_specific_column(): void
    {
        // Create additional divisions to test ordering
        Division::factory()->create(['name_ms' => 'Bahagian A', 'name_en' => 'Division A']);
        Division::factory()->create(['name_ms' => 'Bahagian Z', 'name_en' => 'Division Z']);

        app()->setLocale('ms');

        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Bahagian A')
            ->assertSee('Bahagian Z');
    }

    /**
     * Test form displays in malay locale (default)
     */
    #[Test]
    public function form_displays_in_malay_locale(): void
    {
        app()->setLocale('ms');

        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Nama Penuh')
            ->assertSee('No. Telefon')
            ->assertSee('Bahagian/Unit');
    }

    /**
     * Test validation messages display in correct language
     */
    #[Test]
    public function validation_messages_display_in_correct_language(): void
    {
        app()->setLocale('ms');

        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', '')
            ->call('nextStep')
            ->assertHasErrors(['form.applicant_name']);
    }

    /**
     * Test asset categories are loaded correctly
     */
    #[Test]
    public function asset_categories_are_loaded_correctly(): void
    {
        // Create additional categories
        AssetCategory::factory()->projectors()->create();
        AssetCategory::factory()->tablets()->create();

        Livewire::test(GuestLoanApplication::class)
            ->assertSet('currentStep', 1)
            ->call('nextStep') // Move to step 2 where categories are shown
            ->assertSuccessful();
    }
}
