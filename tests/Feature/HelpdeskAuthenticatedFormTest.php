<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Division;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test Helpdesk Form Authentication-Aware Validation
 *
 * Validates that the helpdesk form correctly handles:
 * - Guest users: requires all contact fields
 * - Authenticated users: skips contact field validation
 *
 * @trace Bug Fix: Authenticated users getting validation errors on step 1
 */
class HelpdeskAuthenticatedFormTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed divisions for the test
        Division::factory()->create([
            'name_ms' => 'Bahagian ICT',
            'name_en' => 'ICT Division',
            'is_active' => true,
        ]);
    }

    /**
     * Test that authenticated users can advance from step 1 without filling guest fields
     */
    public function test_authenticated_user_can_advance_from_step_1_without_guest_validation(): void
    {
        // Arrange: Create and authenticate a user
        $user = User::factory()->create([
            'name' => 'Test User',
            'email' => 'test@motac.gov.my',
            'phone' => '03-12345678',
            'staff_id' => 'MOTAC001',
        ]);

        // Act: Load the form as authenticated user and try to advance to step 2
        $component = Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class);

        // Assert: Starting at step 1
        $component->assertSet('currentStep', 1);

        // Act: Advance to step 2
        $component->call('nextStep');

        // Assert: Should successfully advance without validation errors
        $component->assertHasNoErrors();
        $component->assertSet('currentStep', 2);
    }

    /**
     * Test that guest users MUST fill contact fields on step 1
     */
    public function test_guest_user_must_fill_contact_fields_on_step_1(): void
    {
        // Act: Load the form as guest (not authenticated) and try to advance without filling fields
        $component = Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class);

        // Assert: Starting at step 1
        $component->assertSet('currentStep', 1);

        // Act: Try to advance to step 2 without filling required fields
        $component->call('nextStep');

        // Assert: Should have validation errors for guest fields
        $component->assertHasErrors([
            'guest_name',
            'guest_email',
            'guest_phone',
            'division_id',
        ]);

        // Assert: Should still be on step 1
        $component->assertSet('currentStep', 1);
    }

    /**
     * Test that guest users can advance when all fields are filled
     */
    #[Test]
    public function guest_user_can_advance_when_contact_fields_filled(): void
    {
        // Arrange: Create a division
        $division = Division::first();

        // Act: Load the form as guest and fill all required fields
        $component = Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->set('guest_name', 'John Doe')
            ->set('guest_email', 'john@example.com')
            ->set('guest_phone', '012-3456789')
            ->set('division_id', $division->id);

        // Assert: Starting at step 1
        $component->assertSet('currentStep', 1);

        // Act: Advance to step 2
        $component->call('nextStep');

        // Assert: Should successfully advance without validation errors
        $component->assertHasNoErrors();
        $component->assertSet('currentStep', 2);
    }

    /**
     * Test that authenticated user sees their info displayed (not form fields)
     */
    #[Test]
    public function authenticated_user_sees_their_info_displayed(): void
    {
        // Arrange: Create and authenticate a user
        $user = User::factory()->create([
            'name' => 'Lee Superuser',
            'email' => 'superuser@motac.gov.my',
            'phone' => '03-12345681',
            'staff_id' => 'MOTAC004',
        ]);

        // Act & Assert: Authenticated user should see their info
        Livewire::actingAs($user)
            ->test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->assertSee('Lee Superuser')
            ->assertSee('superuser@motac.gov.my')
            ->assertSee('03-12345681')
            ->assertSee('MOTAC004')
            // Should NOT see input fields for guest data
            ->assertDontSee('wire:model.live.debounce.300ms="guest_name"')
            ->assertDontSee('wire:model.live.debounce.300ms="guest_email"');
    }

    /**
     * Test that guest user sees input form fields (not user info)
     */
    #[Test]
    public function guest_user_sees_form_fields(): void
    {
        // Act & Assert: Guest user should see input fields
        Livewire::test(\App\Livewire\Helpdesk\SubmitTicket::class)
            ->assertSee(__('helpdesk.full_name'))
            ->assertSee(__('helpdesk.email_address'))
            ->assertSee(__('helpdesk.phone_number'))
            ->assertSee(__('helpdesk.division'))
            // Should NOT see authenticated user info display
            ->assertDontSee(__('helpdesk.your_information'));
    }
}
