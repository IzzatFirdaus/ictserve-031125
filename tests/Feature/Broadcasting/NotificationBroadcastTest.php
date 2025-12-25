<?php

declare(strict_types=1);

namespace Tests\Feature\Broadcasting;

use App\Events\NotificationCreated;
use App\Events\StatusUpdated;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Str;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Tests for Laravel Echo broadcast events
 *
 * Verifies that broadcast events are fired correctly with proper payload structures
 * for frontend Echo listeners (portal-echo.js, submission-echo.js).
 *
 * @trace D03 SRS-FR-043, D04 §6.2 (Multi-channel notifications)
 */
class NotificationBroadcastTest extends TestCase
{
    use RefreshDatabase;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create();
    }

    /**
     * Test NotificationCreated event broadcasts with correct payload structure
     */
    #[Test]
    public function notification_created_event_broadcasts_correct_payload(): void
    {
        Event::fake([NotificationCreated::class]);

        // Create a database notification
        $notification = $this->user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => [
                'title' => 'New Ticket Assigned',
                'message' => 'You have been assigned ticket #TKT-001',
                'url' => '/portal/tickets/1',
            ],
            'read_at' => null,
        ]);

        // Fire the broadcast event
        event(new NotificationCreated($this->user, $notification));

        // Assert event was dispatched
        Event::assertDispatched(NotificationCreated::class, function ($event) use ($notification) {
            // Verify it's for the correct user
            if ($event->user->id !== $this->user->id) {
                return false;
            }

            // Verify it has the correct notification
            if ($event->notification->id !== $notification->id) {
                return false;
            }

            // Verify broadcast channel
            $channels = $event->broadcastOn();
            $this->assertCount(1, $channels);
            $this->assertEquals("private-user.{$this->user->id}", $channels[0]->name);

            // Verify broadcast name
            $this->assertEquals('notification.created', $event->broadcastAs());

            // Verify payload structure matches what portal-echo.js expects
            $payload = $event->broadcastWith();
            $this->assertArrayHasKey('id', $payload);
            $this->assertArrayHasKey('type', $payload);
            $this->assertArrayHasKey('data', $payload);
            $this->assertArrayHasKey('created_at', $payload);
            $this->assertArrayHasKey('read_at', $payload);

            // Verify data structure
            $this->assertEquals($notification->id, $payload['id']);
            $this->assertEquals($notification->type, $payload['type']);
            $this->assertEquals('New Ticket Assigned', $payload['data']['title']);
            $this->assertEquals('You have been assigned ticket #TKT-001', $payload['data']['message']);
            $this->assertEquals('/portal/tickets/1', $payload['data']['url']);

            return true;
        });
    }

    /**
     * Test StatusUpdated event broadcasts with correct payload structure
     *
     * PKS 5.2.1: StatusUpdated now broadcasts to both user channel and entity channel
     */
    #[Test]
    public function status_updated_event_broadcasts_correct_payload_for_ticket(): void
    {
        Event::fake([StatusUpdated::class]);

        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'open',
        ]);

        // Simulate status update
        $oldStatus = 'open';
        $newStatus = 'in_progress';

        event(new StatusUpdated($ticket, $oldStatus, $newStatus));

        // Assert event was dispatched
        Event::assertDispatched(StatusUpdated::class, function ($event) use ($ticket, $oldStatus, $newStatus) {
            // PKS 5.2.1: Verify broadcast channels - now includes both user and entity channels
            $channels = $event->broadcastOn();
            $this->assertCount(2, $channels);

            $channelNames = array_map(fn ($ch) => $ch->name, $channels);
            $this->assertContains("private-user.{$this->user->id}", $channelNames);
            $this->assertContains("private-ticket.{$this->user->id}.{$ticket->id}", $channelNames);

            // Verify broadcast name
            $this->assertEquals('status.updated', $event->broadcastAs());

            // Verify payload structure matches what portal-echo.js expects
            $payload = $event->broadcastWith();
            $this->assertArrayHasKey('model_type', $payload);
            $this->assertArrayHasKey('model_id', $payload);
            $this->assertArrayHasKey('old_status', $payload);
            $this->assertArrayHasKey('new_status', $payload);
            $this->assertArrayHasKey('updated_at', $payload);

            // Verify values
            $this->assertEquals('HelpdeskTicket', $payload['model_type']);
            $this->assertEquals($ticket->id, $payload['model_id']);
            $this->assertEquals($oldStatus, $payload['old_status']);
            $this->assertEquals($newStatus, $payload['new_status']);

            return true;
        });
    }

    /**
     * Test StatusUpdated event broadcasts correctly for loan applications
     */
    #[Test]
    public function status_updated_event_broadcasts_correct_payload_for_loan(): void
    {
        Event::fake([StatusUpdated::class]);

        $loan = LoanApplication::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'submitted',
        ]);

        // Simulate status update
        $oldStatus = 'submitted';
        $newStatus = 'approved';

        event(new StatusUpdated($loan, $oldStatus, $newStatus, $this->user->id));

        // Assert event was dispatched
        Event::assertDispatched(StatusUpdated::class, function ($event) use ($loan, $oldStatus, $newStatus) {
            $payload = $event->broadcastWith();

            // Verify model type is correct for loan
            $this->assertEquals('LoanApplication', $payload['model_type']);
            $this->assertEquals($loan->id, $payload['model_id']);
            $this->assertEquals($oldStatus, $payload['old_status']);
            $this->assertEquals($newStatus, $payload['new_status']);

            return true;
        });
    }

    /**
     * Test that broadcast events use private channels for security
     *
     * PKS 5.2.1: StatusUpdated now broadcasts to both user channel and entity channel
     */
    #[Test]
    public function broadcast_events_use_private_channels(): void
    {
        $notification = $this->user->notifications()->create([
            'id' => Str::uuid(),
            'type' => 'App\Notifications\TicketAssigned',
            'data' => ['message' => 'Test'],
            'read_at' => null,
        ]);

        $notificationEvent = new NotificationCreated($this->user, $notification);
        $channels = $notificationEvent->broadcastOn();

        // Verify private channel
        $this->assertCount(1, $channels);
        $this->assertInstanceOf(\Illuminate\Broadcasting\PrivateChannel::class, $channels[0]);
        $this->assertEquals("private-user.{$this->user->id}", $channels[0]->name);

        // Test status update event - PKS 5.2.1: now broadcasts to both user and entity channels
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $this->user->id]);
        $statusEvent = new StatusUpdated($ticket, 'open', 'closed');
        $statusChannels = $statusEvent->broadcastOn();

        // PKS 5.2.1: Should have 2 channels - user channel and entity channel
        $this->assertCount(2, $statusChannels);
        $this->assertInstanceOf(\Illuminate\Broadcasting\PrivateChannel::class, $statusChannels[0]);
        $this->assertInstanceOf(\Illuminate\Broadcasting\PrivateChannel::class, $statusChannels[1]);

        $channelNames = array_map(fn ($ch) => $ch->name, $statusChannels);
        $this->assertContains("private-user.{$this->user->id}", $channelNames);
        $this->assertContains("private-ticket.{$this->user->id}.{$ticket->id}", $channelNames);
    }
}
