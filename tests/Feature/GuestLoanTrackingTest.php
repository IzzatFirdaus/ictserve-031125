<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Livewire\GuestLoanTracking;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Guest Loan Tracking Tests
 *
 * Tests the guest loan tracking functionality using application number lookup.
 * The component uses applicationNumber property and track() method.
 */
class GuestLoanTrackingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_tracking_page(): void
    {
        $response = $this->get(route('loan.guest.track-token'))
            ->assertSuccessful();

        // Check that the page contains the Livewire component
        $response->assertSee('wire:id');
    }

    #[Test]
    public function guest_can_track_application_with_valid_number(): void
    {
        $application = LoanApplication::factory()->create();

        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', $application->application_number)
            ->call('track')
            ->assertSet('searched', true)
            ->assertSet('application.id', $application->id);
    }

    #[Test]
    public function guest_cannot_track_with_invalid_application_number(): void
    {
        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', 'INVALID-NUMBER-12345')
            ->call('track')
            ->assertSet('searched', true)
            ->assertSet('application', null)
            ->assertHasErrors(['applicationNumber']);
    }

    #[Test]
    public function guest_can_track_via_url_parameter(): void
    {
        $application = LoanApplication::factory()->create();

        // Test that the component can be initialized with a ref parameter
        Livewire::test(GuestLoanTracking::class, ['ref' => $application->application_number])
            ->assertSet('applicationNumber', $application->application_number)
            ->assertSet('searched', true)
            ->assertSet('application.id', $application->id);
    }

    #[Test]
    public function tracking_requires_minimum_length_application_number(): void
    {
        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', 'AB')
            ->call('track')
            ->assertHasErrors(['applicationNumber']);
    }
}
