<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\GuestLoanApplication;
use App\Models\Division;
use App\Models\Grade;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class LoanAuthenticatedFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_authenticated_user_can_advance_from_step_1_without_contact_field_validation(): void
    {
        // Create a user with grade and division
        $division = Division::first();
        $grade = Grade::first();

        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@motac.gov.my',
            'phone' => '03-12345678',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
            'position_id' => null,
        ]);

        // Act as authenticated user
        $this->actingAs($user);

        // Test the component
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('currentStep', 1)
            // Fill only loan-specific fields (not contact fields)
            ->set('form.purpose', 'Testing loan application')
            ->set('form.location', 'Office Building A')
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(7)->format('Y-m-d'))
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2);
    }

    public function test_guest_user_must_fill_contact_fields_on_step_1(): void
    {
        // Test as guest (not authenticated)
        Livewire::test(GuestLoanApplication::class)
            ->assertSet('currentStep', 1)
            // Try to advance without filling any fields
            ->call('nextStep')
            ->assertHasErrors([
                'form.applicant_name',
                'form.position',
                'form.phone',
                'form.division_id',
                'form.purpose',
                'form.location',
                // Note: Date fields have default values set in mount(), so they won't trigger errors
            ]);
    }

    #[Test]
    public function guest_user_can_advance_when_all_contact_fields_filled(): void
    {
        $division = Division::first();

        Livewire::test(GuestLoanApplication::class)
            ->assertSet('currentStep', 1)
            // Fill all required fields for guest
            ->set('form.applicant_name', 'Guest User')
            ->set('form.position', 'Administrative Officer N41')
            ->set('form.phone', '03-98765432')
            ->set('form.division_id', $division->id)
            ->set('form.purpose', 'Testing loan application')
            ->set('form.location', 'Office Building A')
            ->set('form.loan_start_date', now()->addDays(1)->format('Y-m-d'))
            ->set('form.loan_end_date', now()->addDays(7)->format('Y-m-d'))
            ->call('nextStep')
            ->assertHasNoErrors()
            ->assertSet('currentStep', 2);
    }

    #[Test]
    public function authenticated_user_info_is_pre_filled(): void
    {
        $division = Division::first();
        $grade = Grade::first();

        $user = User::factory()->create([
            'name' => 'Pre-filled User',
            'email' => 'prefilled@motac.gov.my',
            'phone' => '03-11111111',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        Livewire::test(GuestLoanApplication::class)
            ->assertSet('form.applicant_name', $user->name)
            ->assertSet('form.phone', $user->phone)
            ->assertSet('form.division_id', $user->division_id);
    }

    #[Test]
    public function authenticated_user_sees_info_display_in_view(): void
    {
        app()->setLocale('en'); // Set English locale for translation assertions

        $division = Division::first();
        $grade = Grade::first();

        $user = User::factory()->create([
            'name' => 'Display User',
            'email' => 'display@motac.gov.my',
            'phone' => '03-22222222',
            'division_id' => $division->id,
            'grade_id' => $grade->id,
        ]);

        $this->actingAs($user);

        Livewire::test(GuestLoanApplication::class)
            ->assertSee($user->name)
            ->assertSee($user->phone)
            ->assertSee('Your Information');
    }

    #[Test]
    public function guest_user_sees_form_input_fields(): void
    {
        app()->setLocale('en'); // Set English locale for translation assertions

        Livewire::test(GuestLoanApplication::class)
            ->assertSee('Full Name')
            ->assertSee('Enter your full name')
            ->assertDontSee('Your Information');
    }
}
