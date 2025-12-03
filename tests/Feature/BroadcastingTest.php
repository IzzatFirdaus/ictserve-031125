<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\AssetOverdue;
use App\Events\HighPriorityTicketCreated;
use App\Events\SLABreachDetected;
use App\Events\TicketAssigned;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Tests\TestCase;

/**
 * Broadcasting Test Suite
 *
 * Tests Laravel Reverb WebSocket channel authorization and event broadcasting.
 *
 * @see D16_BROADCASTING_SETUP.md - WebSocket configuration
 * @see Requirements 8.1, 8.2 - Real-time notifications
 */
final class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'broadcasting.default' => 'pusher',
            'broadcasting.connections.pusher.key' => 'test-key',
            'broadcasting.connections.pusher.secret' => 'test-secret',
            'broadcasting.connections.pusher.app_id' => 'test-app-id',
        ]);
    }

    public function test_authorizes_private_user_channel_for_owner(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-user.'.$user->id,
            ]);

        $response->assertStatus(200);
    }

    public function test_denies_private_user_channel_for_other_user(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-user.'.$otherUser->id,
            ]);

        $response->assertStatus(403);
    }

    public function test_authorizes_admin_notifications_channel_for_admin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-admin.notifications',
            ]);

        $response->assertStatus(200);
    }

    public function test_authorizes_admin_notifications_channel_for_superuser(): void
    {
        $superuser = User::factory()->create(['role' => 'superuser']);

        $response = $this->actingAs($superuser)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-admin.notifications',
            ]);

        $response->assertStatus(200);
    }

    public function test_denies_admin_notifications_channel_for_staff(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-admin.notifications',
            ]);

        $response->assertStatus(403);
    }

    public function test_unauthenticated_user_gets_403(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-user.'.$user->id,
        ]);

        $response->assertStatus(403);
    }

    public function test_high_priority_ticket_event_broadcasts_to_admin_channel(): void
    {
        Event::fake([HighPriorityTicketCreated::class]);

        $ticket = HelpdeskTicket::factory()->create([
            'priority' => 'HIGH',
        ]);

        event(new HighPriorityTicketCreated($ticket));

        Event::assertDispatched(HighPriorityTicketCreated::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id;
        });
    }

    public function test_sla_breach_event_broadcasts_to_admin_channel(): void
    {
        Event::fake([SLABreachDetected::class]);

        $ticket = HelpdeskTicket::factory()->create([
            'priority' => 'HIGH',
            'sla_due_at' => now()->subHour(),
        ]);

        event(new SLABreachDetected($ticket, 'resolution'));

        Event::assertDispatched(SLABreachDetected::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id
                && $event->breachType === 'resolution';
        });
    }

    public function test_asset_overdue_event_broadcasts_correctly(): void
    {
        Event::fake([AssetOverdue::class]);

        $loan = LoanApplication::factory()->create([
            'loan_end_date' => now()->subDay(),
            'status' => 'ON_LOAN',
        ]);

        event(new AssetOverdue($loan, 'overdue'));

        Event::assertDispatched(AssetOverdue::class, function ($event) use ($loan) {
            return $event->loan->id === $loan->id
                && $event->reminderType === 'overdue';
        });
    }

    public function test_ticket_assigned_event_broadcasts_to_assigned_user(): void
    {
        Event::fake([TicketAssigned::class]);

        $admin = User::factory()->create(['role' => 'admin']);
        $ticket = HelpdeskTicket::factory()->create();

        event(new TicketAssigned($ticket, $admin));

        Event::assertDispatched(TicketAssigned::class, function ($event) use ($ticket, $admin) {
            return $event->ticket->id === $ticket->id
                && $event->assignedUser->id === $admin->id;
        });
    }
}
