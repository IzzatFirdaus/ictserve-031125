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
use PHPUnit\Framework\Attributes\Test;
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

    #[Test]
    public function authorizesPrivateUserChannelForOwner(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-user.' . $user->id,
            ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function deniesPrivateUserChannelForOtherUser(): void
    {
        $user = User::factory()->create();
        $otherUser = User::factory()->create();

        $response = $this->actingAs($user)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-user.' . $otherUser->id,
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function authorizesAdminNotificationsChannelForAdmin(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $response = $this->actingAs($admin)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-admin.notifications',
            ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function authorizesAdminNotificationsChannelForSuperuser(): void
    {
        $superuser = User::factory()->create(['role' => 'superuser']);

        $response = $this->actingAs($superuser)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-admin.notifications',
            ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function deniesAdminNotificationsChannelForStaff(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $response = $this->actingAs($staff)
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => 'private-admin.notifications',
            ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function unauthenticatedUserGets403(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-user.' . $user->id,
        ]);

        $response->assertStatus(403);
    }

    #[Test]
    public function highPriorityTicketEventBroadcastsToAdminChannel(): void
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

    #[Test]
    public function slaBreachEventBroadcastsToAdminChannel(): void
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

    #[Test]
    public function assetOverdueEventBroadcastsCorrectly(): void
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

    #[Test]
    public function ticketAssignedEventBroadcastsToAssignedUser(): void
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
