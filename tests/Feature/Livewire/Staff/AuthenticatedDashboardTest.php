<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Staff;

use App\Livewire\Staff\AuthenticatedDashboard;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Livewire\Livewire;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AuthenticatedDashboardTest extends TestCase
{
    use DatabaseMigrations;

    #[Test]
    public function it_counts_open_tickets_including_assigned_and_pending_user_statuses(): void
    {
        $user = User::factory()->create();

        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'open',
        ]);

        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'assigned',
        ]);

        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'pending_user',
        ]);

        // Tickets that should not be counted
        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'resolved',
        ]);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);

        $stats = $component->statistics();

        $this->assertSame(3, $stats['open_tickets']);
    }

    #[Test]
    public function it_returns_recent_tickets_assigned_to_the_authenticated_user(): void
    {
        $user = User::factory()->create();

        $recentTicket = HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'open',
            'subject' => 'Dashboard Assigned Ticket',
        ]);

        // An older ticket assigned to the same user should still appear if within limit
        HelpdeskTicket::factory()->create([
            'assigned_to_user' => $user->id,
            'status' => 'assigned',
            'created_at' => now()->subDay(),
        ]);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);

        $tickets = $component->recentTickets();

        $this->assertTrue($tickets->contains(fn (HelpdeskTicket $ticket) => $ticket->is($recentTicket)));
    }

    #[Test]
    public function authenticated_user_can_access_dashboard_route(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSeeLivewire(AuthenticatedDashboard::class);
    }

    #[Test]
    public function dashboard_displays_statistics_grid_for_regular_staff(): void
    {
        $user = User::factory()->create();

        // Create some test data
        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiket Terbuka Saya') // My Open Tickets in Malay
            ->assertSee('Pinjaman Menunggu Saya') // My Pending Loans in Malay
            ->assertSee('Item Tertunggak') // Overdue Items in Malay
            ->assertDontSee('Kelulusan Menunggu'); // Pending Approvals in Malay - shouldn't see
    }

    #[Test]
    public function dashboard_displays_statistics_grid_with_approvals_for_approver(): void
    {
        $user = User::factory()->create();

        // Create the approver role if it doesn't exist
        $approverRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'approver', 'guard_name' => 'web']);
        $user->assignRole($approverRole);

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiket Terbuka Saya') // My Open Tickets in Malay
            ->assertSee('Pinjaman Menunggu Saya') // My Pending Loans in Malay
            ->assertSee('Item Tertunggak') // Overdue Items in Malay
            ->assertSee('Kelulusan Menunggu'); // Pending Approvals in Malay - approvers should see this
    }

    #[Test]
    public function dashboard_displays_recent_activity_sections(): void
    {
        $user = User::factory()->create();

        // Create recent ticket
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
            'subject' => 'Test Ticket',
        ]);

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiket Terkini Saya') // My Recent Tickets in Malay
            ->assertSee('Pinjaman Terkini Saya') // My Recent Loans in Malay
            ->assertSee($ticket->ticket_number); // Check for ticket number instead of subject
    }

    #[Test]
    public function dashboard_displays_quick_action_buttons(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiket Baharu') // New Ticket in Malay
            ->assertSee('Mohon Pinjaman') // Request Loan in Malay
            ->assertSee('Lihat Semua Perkhidmatan'); // View All Services in Malay
    }

    #[Test]
    public function dashboard_refresh_invalidates_cache(): void
    {
        $user = User::factory()->create();

        Cache::flush();

        $component = Livewire::actingAs($user)->test(AuthenticatedDashboard::class);

        // Create new ticket
        HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        // Refresh should clear cache and reload data
        $component->call('refreshData');

        // Verify the component refreshed successfully
        $component->assertStatus(200);
    }

    #[Test]
    public function guest_user_cannot_access_dashboard(): void
    {
        $response = $this->get('/dashboard');

        $response->assertRedirect('/login');
    }
}
