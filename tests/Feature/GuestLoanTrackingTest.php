<?php

namespace Tests\Feature;

use App\Livewire\GuestLoanTracking;
use App\Models\LoanApplication;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class GuestLoanTrackingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function guest_can_view_tracking_page()
    {
        $this->get(route('loan.guest.tracking'))
            ->assertSuccessful()
            ->assertSeeLivewire(GuestLoanTracking::class);
    }

    #[Test]
    public function guest_can_track_application_with_token()
    {
        $application = LoanApplication::factory()->create([
            'tracking_token' => 'valid-token',
            'tracking_token_expires_at' => now()->addDays(1),
        ]);

        Livewire::test(GuestLoanTracking::class)
            ->call('trackByToken', 'valid-token')
            ->assertSet('application.id', $application->id)
            ->assertSet('showResults', true);
    }

    #[Test]
    public function guest_cannot_track_with_invalid_token()
    {
        Livewire::test(GuestLoanTracking::class)
            ->call('trackByToken', 'invalid-token')
            ->assertSet('application', null)
            ->assertSet('notFound', true);
    }

    #[Test]
    public function guest_can_track_with_application_number_and_email()
    {
        $application = LoanApplication::factory()->create([
            'application_number' => 'LA123456',
            'applicant_email' => 'test@example.com',
        ]);

        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', 'LA123456')
            ->set('email', 'test@example.com')
            ->call('track')
            ->assertSet('application.id', $application->id)
            ->assertSet('showResults', true);
    }

    #[Test]
    public function guest_cannot_track_with_mismatched_email()
    {
        $application = LoanApplication::factory()->create([
            'application_number' => 'LA123456',
            'applicant_email' => 'test@example.com',
        ]);

        Livewire::test(GuestLoanTracking::class)
            ->set('applicationNumber', 'LA123456')
            ->set('email', 'wrong@example.com')
            ->call('track')
            ->assertSet('application', null)
            ->assertSet('notFound', true);
    }
}
