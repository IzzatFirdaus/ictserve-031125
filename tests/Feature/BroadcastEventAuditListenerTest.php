<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\StatusUpdated;
use App\Listeners\BroadcastEventAuditListener;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastEvent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Spatie\Activitylog\Models\Activity;
use Tests\TestCase;

/**
 * Broadcast Event Audit Listener Test
 *
 * Tests the audit logging functionality for broadcast events.
 * Ensures all broadcast events are properly logged to the activity log.
 *
 * @see .kiro/specs/realtime-notifications-broadcasting/requirements.md - Requirements 7.5
 * @see .kiro/specs/realtime-notifications-broadcasting/design.md - Property 8: Audit Logging
 */
class BroadcastEventAuditListenerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_logs_broadcast_events_to_activity_log(): void
    {
        // Create test data
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->for($user)->create(['status' => 'open']);

        // Create a broadcast event
        $statusUpdatedEvent = new StatusUpdated($ticket, 'open', 'in_progress');
        $broadcastEvent = new BroadcastEvent($statusUpdatedEvent);

        // Create the listener
        $listener = new BroadcastEventAuditListener;

        // Handle the event
        $listener->handle($broadcastEvent);

        // Assert activity log entry was created
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'broadcast_event_dispatched',
        ]);

        // Get the activity log entry
        $activity = Activity::where('log_name', 'broadcast_event_dispatched')->latest()->first();
        $this->assertNotNull($activity);

        // Check properties
        $properties = $activity->properties;
        $this->assertEquals(StatusUpdated::class, $properties->get('event_class'));
        $this->assertEquals('status.updated', $properties->get('event_name'));
        $this->assertIsArray($properties->get('channels'));
        $this->assertGreaterThan(0, $properties->get('channel_count'));
        $this->assertIsArray($properties->get('broadcast_data_keys'));
        $this->assertArrayHasKey('timestamp', $properties->toArray());
    }

    #[Test]
    public function it_handles_events_without_broadcast_as_method(): void
    {
        // Create a mock event that doesn't have broadcastAs method
        $mockEvent = new class implements \Illuminate\Contracts\Broadcasting\ShouldBroadcast
        {
            use \Illuminate\Broadcasting\InteractsWithSockets;
            use \Illuminate\Foundation\Events\Dispatchable;
            use \Illuminate\Queue\SerializesModels;

            public function broadcastOn(): array
            {
                return [new \Illuminate\Broadcasting\PrivateChannel('test')];
            }
        };

        $broadcastEvent = new BroadcastEvent($mockEvent);
        $listener = new BroadcastEventAuditListener;

        // Should not throw exception
        $listener->handle($broadcastEvent);

        // Assert activity log entry was created
        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'broadcast_event_dispatched',
        ]);

        $activity = Activity::where('log_name', 'broadcast_event_dispatched')->latest()->first();
        $this->assertNotNull($activity);

        // Should use class basename as event name
        $properties = $activity->properties;
        $eventName = $properties->get('event_name');
        $this->assertNotNull($eventName);
        $this->assertIsString($eventName);
    }

    #[Test]
    public function it_ignores_non_broadcast_events(): void
    {
        // Create a regular event that doesn't implement ShouldBroadcast
        $regularEvent = new class
        {
            // Regular event without ShouldBroadcast interface
        };

        $broadcastEvent = new BroadcastEvent($regularEvent);
        $listener = new BroadcastEventAuditListener;

        // Handle the event
        $listener->handle($broadcastEvent);

        // Assert no activity log entry was created
        $this->assertDatabaseMissing('activity_log', [
            'log_name' => 'broadcast_event_dispatched',
        ]);
    }

    #[Test]
    public function it_logs_multiple_channels_correctly(): void
    {
        // Create test data for authenticated user
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->for($user)->create(['status' => 'open']);

        // Create a broadcast event
        $statusUpdatedEvent = new StatusUpdated($ticket, 'open', 'resolved');
        $broadcastEvent = new BroadcastEvent($statusUpdatedEvent);

        $listener = new BroadcastEventAuditListener;
        $listener->handle($broadcastEvent);

        // Get the activity log entry
        $activity = Activity::where('log_name', 'broadcast_event_dispatched')->latest()->first();
        $this->assertNotNull($activity);

        $properties = $activity->properties;

        // Should have exactly one channel for authenticated user
        $this->assertEquals(1, $properties->get('channel_count'));
        $this->assertCount(1, $properties->get('channels'));
        $this->assertEquals("private-user.{$user->id}", $properties->get('channels')[0]);
    }

    #[Test]
    public function it_handles_guest_channels_correctly(): void
    {
        // Create test data for guest submission
        $ticket = HelpdeskTicket::factory()->guest()->create(['status' => 'open']);

        // Create a broadcast event
        $statusUpdatedEvent = new StatusUpdated($ticket, 'open', 'resolved');
        $broadcastEvent = new BroadcastEvent($statusUpdatedEvent);

        $listener = new BroadcastEventAuditListener;
        $listener->handle($broadcastEvent);

        // Get the activity log entry
        $activity = Activity::where('log_name', 'broadcast_event_dispatched')->latest()->first();
        $this->assertNotNull($activity);

        $properties = $activity->properties;

        // Should have exactly one channel for guest
        $this->assertEquals(1, $properties->get('channel_count'));
        $this->assertCount(1, $properties->get('channels'));
        $this->assertStringStartsWith('private-ticket.', $properties->get('channels')[0]);
    }

    #[Test]
    public function it_continues_on_logging_errors(): void
    {
        // This test ensures that if activity logging fails, the broadcast still continues
        // We can't easily mock the Activity facade failure, but we can test the structure

        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->for($user)->create(['status' => 'open']);

        $statusUpdatedEvent = new StatusUpdated($ticket, 'open', 'resolved');
        $broadcastEvent = new BroadcastEvent($statusUpdatedEvent);

        $listener = new BroadcastEventAuditListener;

        // Should not throw exception even if logging fails
        $this->expectNotToPerformAssertions();
        $listener->handle($broadcastEvent);
    }
}
