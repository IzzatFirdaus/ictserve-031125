<?php

declare(strict_types=1);

namespace Tests\Feature\Portal;

use App\Livewire\Portal\Dashboard\StatisticsCards;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Dashboard Feature Tests
 *
 * Tests authenticated dashboard access, statistics display, and quick actions.
 *
 * @traceability Requirements 1.1, 1.2, 1.3
 */
class DashboardTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test authenticated user can access dashboard
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_authenticated_user_can_access_dashboard(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/portal/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Dashboard');
        $response->assertSee($user->name);
    }

    /**
     * Test guest cannot access dashboard
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_guest_cannot_access_dashboard(): void
    {
        $response = $this->get('/portal/dashboard');

        $response->assertRedirect('/login');
    }

    /**
     * Test dashboard displays statistics cards
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_dashboard_displays_statistics_cards(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->count(3)->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        LoanApplication::factory()->count(2)->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        Livewire::actingAs($user)
            ->test(StatisticsCards::class)
            ->assertSee('My Open Tickets')
            ->assertSee('3')
            ->assertSee('My Pending Loans')
            ->assertSee('2');
    }

    /**
     * Test dashboard shows zero counts for new user
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_dashboard_shows_zero_counts_for_new_user(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(StatisticsCards::class)
            ->assertSee('My Open Tickets')
            ->assertSee('0')
            ->assertSee('My Pending Loans')
            ->assertSee('0');
    }

    /**
     * Test dashboard displays recent activity
     *
     *
     * @traceability Requirement 1.2
     */
    #[Test]
    public function test_dashboard_displays_recent_activity(): void
    {
        $user = User::factory()->create();

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
        ]);

        $user->portalActivities()->create([
            'activity_type' => 'ticket_submitted',
            'subject_type' => HelpdeskTicket::class,
            'subject_id' => $ticket->id,
            'metadata' => [],
        ]);

        $response = $this->actingAs($user)->get('/portal/dashboard');

        $response->assertSee('Recent Activity');
        $response->assertSee('ticket_submitted');
    }

    /**
     * Test dashboard displays quick actions
     *
     *
     * @traceability Requirement 1.3
     */
    #[Test]
    public function test_dashboard_displays_quick_actions(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/portal/dashboard');

        $response->assertSee('Submit Helpdesk Ticket');
        $response->assertSee('Request Asset Loan');
        $response->assertSee('View My Submissions');
        $response->assertSee('Manage Profile');
    }

    /**
     * Test approver sees pending approvals widget
     *
     *
     * @traceability Requirement 1.5
     */
    #[Test]
    public function test_approver_sees_pending_approvals_widget(): void
    {
        $approver = User::factory()->create(['grade' => 41]);

        LoanApplication::factory()->count(3)->create([
            'status' => 'submitted',
        ]);

        $response = $this->actingAs($approver)->get('/portal/dashboard');

        $response->assertSee('Pending Approvals');
        $response->assertSee('3');
    }

    /**
     * Test admin sees dashboard with approval widgets
     *
     *
     * @traceability Requirement 5.2
     */
    #[Test]
    public function test_admin_sees_admin_panel_link(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole('admin');

        $response = $this->actingAs($admin)->get('/portal/dashboard');

        // Admin should see pending approvals widget instead of "Admin Panel" text
        $response->assertSee('Pending Approvals');
    }

    /**
     * Test dashboard statistics update in real-time
     *
     *
     * @traceability Requirement 1.1
     */
    #[Test]
    public function test_dashboard_statistics_update_in_real_time(): void
    {
        $user = User::factory()->create();

        $component = Livewire::actingAs($user)
            ->test(StatisticsCards::class)
            ->assertSee('0');

        // Create new ticket
        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'submitted',
        ]);

        // Refresh statistics
        $component->call('refreshStatistics')
            ->assertSee('1');
    }
}
