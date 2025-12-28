<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\TicketStatusUpdated;
use App\Models\HelpdeskTicket;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Ticket Status Broadcasting Integration Test
 *
 * Tests that ticket status changes automatically dispatch broadcasting events
 * through the HelpdeskTicketObserver.
 *
 * @see app/Observers/HelpdeskTicketObserver.php
 * @see app/Events/TicketStatusChanged.php
 *
 * @requirements 6.1, 6.2, 6.3, 8.1, 8.2
 */
#[Group('environment-specific')]
class TicketStatusBroadcastingTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config([
            'audit.enabled' => false,
            'activitylog.enabled' => false,
        ]);
    }

    #[Test]
    public function observer_integration_works_with_status_updates(): void
    {
        config(['broadcasting.default' => 'null']);
        Event::fake([TicketStatusUpdated::class]);

        // Create a ticket with initial status
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
        ]);

        // Verify ticket was created successfully
        $this->assertEquals('open', $ticket->status);
        $this->assertNotNull($ticket->ticket_number);

        // Update the ticket status - this should trigger the observer
        $ticket->update(['status' => 'in_progress']);

        // Verify the status was updated
        $this->assertEquals('in_progress', $ticket->fresh()->status);

        Event::assertDispatched(TicketStatusUpdated::class, function ($event) use ($ticket) {
            return $event->ticket->is($ticket)
                && $event->oldStatus === 'open'
                && $event->newStatus === 'in_progress';
        });
    }

    #[Test]
    public function event_can_be_manually_dispatched(): void
    {
        Event::fake();

        // Create a ticket
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
        ]);

        // Manually dispatch the event (simulating what the observer does)
        TicketStatusUpdated::dispatch($ticket, 'open', 'in_progress');

        // Verify the event was dispatched
        Event::assertDispatched(TicketStatusUpdated::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id &&
                $event->oldStatus === 'open' &&
                $event->newStatus === 'in_progress';
        });
    }

    #[Test]
    public function event_has_correct_broadcast_data(): void
    {
        // Create a ticket
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'in_progress',
            'subject' => 'Test Ticket Subject',
        ]);

        // Create the event
        $event = new TicketStatusUpdated($ticket, 'open', 'in_progress');

        // Test broadcast data
        $broadcastData = $event->broadcastWith();

        $this->assertArrayHasKey('ticket_id', $broadcastData);
        $this->assertArrayHasKey('ticket_number', $broadcastData);
        $this->assertArrayHasKey('ticket_uuid', $broadcastData);
        $this->assertArrayHasKey('old_status', $broadcastData);
        $this->assertArrayHasKey('new_status', $broadcastData);
        $this->assertArrayHasKey('subject', $broadcastData);
        $this->assertArrayHasKey('priority', $broadcastData);
        $this->assertArrayHasKey('updated_at', $broadcastData);
        $this->assertArrayHasKey('message', $broadcastData);
        $this->assertArrayHasKey('user_id', $broadcastData);
        $this->assertArrayHasKey('user_name', $broadcastData);

        $this->assertEquals($ticket->id, $broadcastData['ticket_id']);
        $this->assertEquals('open', $broadcastData['old_status']);
        $this->assertEquals('in_progress', $broadcastData['new_status']);
        $this->assertEquals('Test Ticket Subject', $broadcastData['subject']);
    }

    #[Test]
    public function event_broadcasts_to_correct_channels(): void
    {
        // Create a ticket with user
        $ticket = HelpdeskTicket::factory()->create();

        // Create the event
        $event = new TicketStatusUpdated($ticket);

        // Get broadcast channels
        $channels = $event->broadcastOn();

        // Convert to channel names for testing
        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Should include public helpdesk channel
        $this->assertContains('helpdesk', $channelNames);

        // Should include ticket-specific channel
        $this->assertContains("ticket.{$ticket->uuid}", $channelNames);

        // Should include user-specific channel if user exists
        if ($ticket->user_id) {
            $this->assertContains("user.{$ticket->user_id}", $channelNames);
        }
    }

    #[Test]
    public function guest_ticket_broadcasts_without_user_channel(): void
    {
        // Create a guest ticket (no user_id)
        $ticket = HelpdeskTicket::factory()->guest()->create();

        // Create the event
        $event = new TicketStatusUpdated($ticket);

        // Get broadcast channels
        $channels = $event->broadcastOn();
        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Should include public channels but not user-specific
        $this->assertContains('helpdesk', $channelNames);
        $this->assertContains("ticket.{$ticket->uuid}", $channelNames);

        // Should NOT include user channel for guest tickets
        $userChannels = array_filter($channelNames, fn ($name) => str_starts_with($name, 'user.'));
        $this->assertEmpty($userChannels);
    }
}
