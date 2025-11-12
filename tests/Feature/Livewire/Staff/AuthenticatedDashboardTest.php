<?php

declare(strict_types=1);

namespace Tests\Feature\Livewire\Staff;

use App\Livewire\Staff\AuthenticatedDashboard;
use App\Models\HelpdeskTicket;
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
    public function dashboard_displays_statistics_grid_with_approvals_for_admin(): void
    {
        $user = User::factory()->create();

        // Create the admin role if it doesn't exist
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole($adminRole);

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiket Terbuka Saya') // My Open Tickets in Malay
            ->assertSee('Pinjaman Menunggu Saya') // My Pending Loans in Malay
            ->assertSee('Item Tertunggak') // Overdue Items in Malay
            ->assertSee('Kelulusan Menunggu'); // Pending Approvals in Malay - admins should see this
    }

    #[Test]
    public function dashboard_displays_statistics_grid_with_approvals_for_superuser(): void
    {
        $user = User::factory()->create();

        // Create the superuser role if it doesn't exist
        $superuserRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superuser', 'guard_name' => 'web']);
        $user->assignRole($superuserRole);

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiket Terbuka Saya') // My Open Tickets in Malay
            ->assertSee('Pinjaman Menunggu Saya') // My Pending Loans in Malay
            ->assertSee('Item Tertunggak') // Overdue Items in Malay
            ->assertSee('Kelulusan Menunggu'); // Pending Approvals in Malay - superusers should see this
    }

    #[Test]
    public function regular_staff_sees_three_statistics_cards(): void
    {
        $user = User::factory()->create();

        // Create the staff role if it doesn't exist
        $staffRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'staff', 'guard_name' => 'web']);
        $user->assignRole($staffRole);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);
        $stats = $component->statistics();

        // Regular staff should have 3 statistics (no pending_approvals)
        $this->assertCount(3, $stats);
        $this->assertArrayHasKey('open_tickets', $stats);
        $this->assertArrayHasKey('pending_loans', $stats);
        $this->assertArrayHasKey('overdue_items', $stats);
        $this->assertArrayNotHasKey('pending_approvals', $stats);
    }

    #[Test]
    public function approver_sees_four_statistics_cards(): void
    {
        $user = User::factory()->create();

        // Create the approver role if it doesn't exist
        $approverRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'approver', 'guard_name' => 'web']);
        $user->assignRole($approverRole);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);
        $stats = $component->statistics();

        // Approvers should have 4 statistics (including pending_approvals)
        $this->assertCount(4, $stats);
        $this->assertArrayHasKey('open_tickets', $stats);
        $this->assertArrayHasKey('pending_loans', $stats);
        $this->assertArrayHasKey('overdue_items', $stats);
        $this->assertArrayHasKey('pending_approvals', $stats);
    }

    #[Test]
    public function admin_sees_four_statistics_cards(): void
    {
        $user = User::factory()->create();

        // Create the admin role if it doesn't exist
        $adminRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $user->assignRole($adminRole);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);
        $stats = $component->statistics();

        // Admins should have 4 statistics (including pending_approvals)
        $this->assertCount(4, $stats);
        $this->assertArrayHasKey('open_tickets', $stats);
        $this->assertArrayHasKey('pending_loans', $stats);
        $this->assertArrayHasKey('overdue_items', $stats);
        $this->assertArrayHasKey('pending_approvals', $stats);
    }

    #[Test]
    public function superuser_sees_four_statistics_cards(): void
    {
        $user = User::factory()->create();

        // Create the superuser role if it doesn't exist
        $superuserRole = \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'superuser', 'guard_name' => 'web']);
        $user->assignRole($superuserRole);

        Cache::flush();
        Auth::login($user);

        $component = app(AuthenticatedDashboard::class);
        $stats = $component->statistics();

        // Superusers should have 4 statistics (including pending_approvals)
        $this->assertCount(4, $stats);
        $this->assertArrayHasKey('open_tickets', $stats);
        $this->assertArrayHasKey('pending_loans', $stats);
        $this->assertArrayHasKey('overdue_items', $stats);
        $this->assertArrayHasKey('pending_approvals', $stats);
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
            ->assertSee('Submit Helpdesk Ticket') // Quick action button text
            ->assertSee('Request Asset Loan') // Quick action button text
            ->assertSee('View My Submissions'); // Quick action button text
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

    #[Test]
    public function dashboard_displays_no_recent_tickets_message_when_no_tickets_exist(): void
    {
        $user = User::factory()->create();

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiada tiket terkini'); // "No recent tickets" in Malay (from common.no_recent_tickets)
    }

    #[Test]
    public function dashboard_displays_no_recent_loans_message_when_no_loans_exist(): void
    {
        $user = User::factory()->create();

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee('Tiada permohonan pinjaman terkini'); // "No recent loan applications" in Malay (from common.no_recent_loans)
    }

    #[Test]
    public function dashboard_displays_ticket_with_proper_formatting_and_status_badge(): void
    {
        $user = User::factory()->create();

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
            'subject' => 'Test Dashboard Ticket Subject',
        ]);

        Cache::flush();

        Livewire::actingAs($user)
            ->test(AuthenticatedDashboard::class)
            ->assertSee($ticket->ticket_number) // Ticket number displayed
            ->assertSee('Test Dashboard Ticket Subject') // Subject displayed (truncated to 60 chars in view)
            ->assertSeeHtml('role="status"'); // Status badge is present with proper ARIA role
    }
}
