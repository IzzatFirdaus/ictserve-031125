<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\GuestLoanApplication;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestLoanResponsibleOfficerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function fields_show_and_require_based_on_checkbox(): void
    {
        $component = Livewire::test(GuestLoanApplication::class);

        // Initially the checkbox is false and fields should not be shown
        $this->assertFalse($component->get('form.is_responsible_officer'));

        // Input name attribute not present when fields are hidden
        $component->assertDontSee('form.responsible_officer_name');

        // Move to step 2 where responsible officer fields live
        // Fill step 1 with valid data so nextStep passes
        $division = Division::factory()->create();

        $component->set('form.applicant_name', 'Test User')
            ->set('form.position', 'Pegawai Tadbir N41')
            ->set('form.phone', '0123456789')
            ->set('form.division_id', $division->id)
            ->set('form.purpose', 'Testing')
            ->set('form.location', 'HQ')
            ->set('form.loan_start_date', now()->addDay()->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(3)->format('Y-m-d'));

        $component->call('nextStep');

        // When checkbox is checked, fields should be visible
        $component->set('form.is_responsible_officer', true);
        $this->assertTrue($component->get('form.is_responsible_officer'));

        // Input name attribute present when fields are shown
        $component->assertSee('form.responsible_officer_name');
        $component->assertSee('form.responsible_officer_phone');

        // If fields are missing, moving to the next step should trigger validation errors
        $component->set('form.responsible_officer_name', '')
            ->set('form.responsible_officer_position', '')
            ->set('form.responsible_officer_phone', '')
            ->call('nextStep')
            ->assertHasErrors(['form.responsible_officer_name', 'form.responsible_officer_position', 'form.responsible_officer_phone']);

        // Provide fields and ensure no validation errors when the checkbox is checked
        $component->set('form.responsible_officer_name', 'John Doe')
            ->set('form.responsible_officer_position', 'Officer')
            ->set('form.responsible_officer_phone', '0123456789')
            ->call('nextStep')
            // Move on to step 3
            ->call('nextStep')
            // Step 3 expects equipment_items; since we didn't populate equipment type, we should see that error
            ->assertHasErrors(['form.equipment_items.0.equipment_type']);

        // Confirm responsible officer fields are no longer in the errors bag
        $this->assertFalse($component->errors()->has('form.responsible_officer_name'));
        $this->assertFalse($component->errors()->has('form.responsible_officer_position'));
        $this->assertFalse($component->errors()->has('form.responsible_officer_phone'));

        // Uncheck checkbox - fields should not be required
        $component->set('form.is_responsible_officer', false);
        $component->set('form.responsible_officer_name', '')
            ->set('form.responsible_officer_position', '')
            ->set('form.responsible_officer_phone', '')
            ->call('nextStep')
            // Move to step 4 to trigger validation on step 3 and assert errors exist
            ->call('nextStep')
            ->assertHasErrors(['form.equipment_items.0.equipment_type']);
    }
}
