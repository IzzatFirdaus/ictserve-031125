<?php

namespace Tests\Feature\Livewire\Loan;

use App\Models\Asset;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Volt\Volt;
use Tests\TestCase;

class GuestApplicationFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_application_form_renders()
    {
        $response = $this->get('/loan/apply'); // Assuming route exists, if not we test component directly
        // Since we haven't defined the route yet, let's test the component directly.

        Volt::test('loan.guest-application-form')
            ->assertSee('Borang Permohonan Pinjaman Peralatan ICT');
    }

    public function test_can_submit_application_as_guest()
    {
        $division = Division::factory()->create();
        $assets = Asset::factory()->count(2)->create(['status' => 'available']);

        Volt::test('loan.guest-application-form')
            ->set('applicant_name', 'John Doe')
            ->set('applicant_email', 'john@example.com')
            ->set('applicant_phone', '0123456789')
            ->set('applicant_staff_id', '12345')
            ->set('division_id', $division->id)
            ->set('applicant_position', 'Assistant')
            ->set('applicant_grade', 'N19')
            ->set('selected_assets', $assets->pluck('id')->toArray())
            ->set('purpose', 'Meeting')
            ->set('location', 'Meeting Room 1')
            ->set('loan_start_date', now()->addDays(5)->format('Y-m-d')) // Valid date > 3 days
            ->set('loan_end_date', now()->addDays(6)->format('Y-m-d'))
            ->set('terms_accepted', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'applicant_name' => 'John Doe',
            'applicant_email' => 'john@example.com',
            'is_applicant_responsible' => true,
        ]);
    }

    public function test_3_day_rule_validation()
    {
        $division = Division::factory()->create();

        Volt::test('loan.guest-application-form')
            ->set('applicant_name', 'Test User')
            ->set('applicant_email', 'test@example.com')
            ->set('applicant_phone', '0123456789')
            ->set('applicant_staff_id', '12345')
            ->set('division_id', $division->id)
            ->set('applicant_position', 'Officer')
            ->set('applicant_grade', 'N41')
            ->set('purpose', 'Testing')
            ->set('location', 'Office')
            ->set('loan_start_date', now()->addDay()->format('Y-m-d')) // Invalid date < 3 days
            ->set('loan_end_date', now()->addDays(2)->format('Y-m-d'))
            ->set('terms_accepted', true)
            ->call('save')
            ->assertHasErrors(['loan_start_date']);
    }

    public function test_responsible_officer_delegation()
    {
        $division = Division::factory()->create();
        $assets = Asset::factory()->count(1)->create(['status' => 'available']);

        Volt::test('loan.guest-application-form')
            ->set('applicant_name', 'Assistant Jane')
            ->set('applicant_email', 'jane@example.com')
            ->set('applicant_phone', '0123456789')
            ->set('applicant_staff_id', '12345')
            ->set('division_id', $division->id)
            ->set('applicant_position', 'PA')
            ->set('applicant_grade', 'N19')
            ->set('is_applicant_responsible', false) // Toggle OFF
            ->set('responsible_officer_name', 'Boss Big')
            ->set('responsible_officer_email', 'boss@example.com')
            ->set('responsible_officer_phone', '0198765432')
            ->set('responsible_officer_position', 'Director')
            ->set('responsible_officer_grade', 'JUSA C')
            ->set('selected_assets', $assets->pluck('id')->toArray())
            ->set('purpose', 'VIP Meeting')
            ->set('location', 'Grand Hall')
            ->set('loan_start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('loan_end_date', now()->addDays(6)->format('Y-m-d'))
            ->set('terms_accepted', true)
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('loan_applications', [
            'applicant_name' => 'Assistant Jane',
            'is_applicant_responsible' => false,
            'responsible_officer_name' => 'Boss Big',
            'responsible_officer_email' => 'boss@example.com',
        ]);
    }

    public function test_requires_asset_selection()
    {
        $division = Division::factory()->create();

        Volt::test('loan.guest-application-form')
            ->set('applicant_name', 'Test User')
            ->set('applicant_email', 'test@example.com')
            ->set('applicant_phone', '0123456789')
            ->set('applicant_staff_id', '12345')
            ->set('division_id', $division->id)
            ->set('applicant_position', 'Officer')
            ->set('applicant_grade', 'N41')
            ->set('purpose', 'Testing')
            ->set('location', 'Office')
            ->set('loan_start_date', now()->addDays(5)->format('Y-m-d'))
            ->set('loan_end_date', now()->addDays(6)->format('Y-m-d'))
            ->set('selected_assets', []) // No assets selected
            ->set('terms_accepted', true)
            ->call('save')
            ->assertHasErrors(['selected_assets']);
    }
}
