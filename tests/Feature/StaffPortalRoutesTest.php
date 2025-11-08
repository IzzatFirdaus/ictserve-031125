<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Staff Portal Route Availability Tests
 *
 * Ensures the key staff navigation links render successfully for authenticated users.
 */
class StaffPortalRoutesTest extends TestCase
{
    use RefreshDatabase;

    private User $staff;

    protected function setUp(): void
    {
        parent::setUp();

        $this->staff = User::factory()->create([
            'role' => 'staff',
        ]);
    }

    #[Test]
    public function staff_my_tickets_route_is_registered(): void
    {
        $this->assertTrue(Route::has('staff.tickets.index'));
        $route = Route::getRoutes()->getByName('staff.tickets.index');

        $this->assertStringStartsWith(\App\Livewire\Helpdesk\MyTickets::class, $route->getAction()['uses']);
    }

    #[Test]
    public function staff_my_loans_route_is_registered(): void
    {
        $this->assertTrue(Route::has('staff.loans.index'));
        $route = Route::getRoutes()->getByName('staff.loans.index');

        $this->assertStringStartsWith(\App\Livewire\Loans\LoanHistory::class, $route->getAction()['uses']);
    }

    #[Test]
    public function staff_can_view_claim_submission_page(): void
    {
        $response = $this->actingAs($this->staff)->get(route('staff.claim-submissions'));

        $response->assertOk()
            ->assertSeeText(__('staff.claims.title'));
    }
}
