<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\GuestLoanApplication;
use App\Models\AssetCategory;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestLoanApplicationEnhancementTest extends TestCase
{
    use RefreshDatabase;

    protected Division $division;

    protected AssetCategory $category;

    protected function setUp(): void
    {
        parent::setUp();

        $this->division = Division::factory()->create();
        $this->category = AssetCategory::factory()->laptops()->create();
    }

    #[Test]
    public function three_day_rule_validation(): void
    {
        // Tests that 3-day rule is enforced during step validation
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Test Position')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Test Purpose')
            ->set('form.location', 'Test Location')
            ->set('form.loan_start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(3)->format('Y-m-d'))
            ->call('nextStep')
            ->assertHasErrors(['form.loan_start_date']);
    }

    #[Test]
    public function three_day_rule_bypassed_with_emergency_request(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Test Position')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Test Purpose')
            ->set('form.location', 'Test Location')
            ->set('form.emergency_request', true)
            ->set('form.emergency_justification', 'This is an urgent request that requires immediate attention due to critical business needs.')
            ->set('form.loan_start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(3)->format('Y-m-d'))
            ->call('nextStep')
            ->assertHasNoErrors(['form.loan_start_date'])
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function emergency_request_requires_justification(): void
    {
        // Test that emergency_justification is required when emergency_request is true
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Test Position')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Test Purpose')
            ->set('form.location', 'Test Location')
            ->set('form.loan_start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.emergency_request', true)
            ->set('form.emergency_justification', '')
            ->call('nextStep')
            ->assertHasErrors(['form.emergency_justification']);

        // Test that emergency_justification must be at least 50 characters
        Livewire::test(GuestLoanApplication::class)
            ->set('form.applicant_name', 'Test User')
            ->set('form.phone', '0123456789')
            ->set('form.applicant_position', 'Test Position')
            ->set('form.applicant_grade', 'N41')
            ->set('form.division_id', $this->division->id)
            ->set('form.purpose', 'Test Purpose')
            ->set('form.location', 'Test Location')
            ->set('form.loan_start_date', now()->addDays(2)->format('Y-m-d'))
            ->set('form.expected_return_date', now()->addDays(3)->format('Y-m-d'))
            ->set('form.emergency_request', true)
            ->set('form.emergency_justification', 'Short')
            ->call('nextStep')
            ->assertHasErrors(['form.emergency_justification']); // Min 50 chars
    }

    #[Test]
    public function responsible_officer_fields_validation(): void
    {
        Livewire::test(GuestLoanApplication::class)
            ->set('form.is_responsible_officer', false) // Applying for self
            ->set('currentStep', 2)
            ->call('nextStep')
            ->assertHasNoErrors(['form.responsible_officer_name']);

        Livewire::test(GuestLoanApplication::class)
            ->set('form.is_responsible_officer', false) // Logic inverted in component: false means "Applying for self" is unchecked, so "Applying for another"
            // Wait, let's check the component logic.
            // Component: 'is_responsible_officer' => false (default).
            // Blade: "Applying on behalf of another officer?" toggle maps to form.is_responsible_officer?
            // Let's check Blade: <x-form.toggle wire:model.live="form.is_responsible_officer" ... />
            // If toggle is ON (true), then we are applying on behalf of someone else?
            // Let's check GuestLoanApplication.php:
            // if (! $this->form['is_responsible_officer']) { ... add responsible officer data ... }
            // This implies if is_responsible_officer is FALSE, we add data. So FALSE means "I am NOT the responsible officer" (i.e. I am applying for someone else).
            // But the toggle usually asks "Are you applying for someone else?".
            // Let's check the Blade label.
            // I need to verify the logic.
            // In GuestLoanApplication.php:
            // 'is_responsible_officer' => false,
            // In submitForm:
            // if (! $this->form['is_responsible_officer']) { ... }
            // This suggests 'is_responsible_officer' means "Is the applicant the responsible officer?".
            // If TRUE, then Applicant == Responsible Officer.
            // If FALSE, then Applicant != Responsible Officer (Delegation).

            // So if I toggle "Applying on behalf of another officer", that should set 'is_responsible_officer' to FALSE?
            // Or maybe the toggle is "I am the responsible officer"?
            // Let's assume the toggle in Blade is "Applying on behalf of another officer".
            // If checked, it means Delegation.
            // If Delegation, then 'is_responsible_officer' should be FALSE.

            // Let's check the validation rules in GuestLoanApplication.php:
            // 'form.responsible_officer_name' => 'required_if:form.is_responsible_officer,true'
            // Wait, if required_if is true, then when is_responsible_officer is TRUE, we need the name.
            // This means if is_responsible_officer is TRUE, we are providing responsible officer details.
            // So TRUE means "There IS a separate responsible officer".
            // So 'is_responsible_officer' variable name is slightly confusing. It probably means "has_responsible_officer" or "is_delegated".

            // Let's check the Blade file again.
            // Step 2: Pegawai Bertanggungjawab (conditional)
            // If form.is_responsible_officer is true, show fields.

            // So:
            // is_responsible_officer = TRUE -> Show fields -> Delegation Mode.
            // is_responsible_officer = FALSE -> Hide fields -> Self Mode.

            ->set('form.is_responsible_officer', true)
            ->set('currentStep', 2)
            ->call('nextStep')
            ->assertHasErrors(['form.responsible_officer_name']);
    }
}
