<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire;

use App\Livewire\GuestLoanTracking;
use App\Models\LoanApplication;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestLoanTrackingTest extends TestCase
{
    #[Test]
    public function guest_can_view_tracking_page(): void
    {
        $response = $this->get(route('loan.guest.track-token'));
        $response->assertOk();
        // Check for Livewire component presence via wire:id attribute
        $response->assertSee('wire:id', false);
    }

    #[Test]
    public function guest_can_track_application(): void
    {
        $application = LoanApplication::factory()->create();

        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', $application->application_number)
            ->call('track')
            ->assertSet('searched', true)
            ->assertSee($application->application_number)
            ->assertSee($application->applicant_name);
    }

    #[Test]
    public function invalid_application_number_shows_error(): void
    {
        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', 'INVALID-NUMBER')
            ->call('track')
            ->assertSet('searched', true)
            ->assertSee(__('loan.messages.application_not_found'))
            ->assertHasErrors(['applicationNumber']);
    }
}
