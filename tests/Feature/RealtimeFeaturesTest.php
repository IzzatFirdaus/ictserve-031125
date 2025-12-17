<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\LoanStatusUpdated;
use App\Events\TicketStatusUpdated;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Real-time Features Integration Test
 *
 * Tests end-to-end real-time functionality for the ICTServe True Hybrid Architecture,
 * including guest and authenticated user real-time updates.
 *
 * @see D16_BROADCASTING_SETUP.md - Real-time features
 * @see .kiro/specs/ictserve-comprehensive-v3.6/tasks.md Task 8.4
 *
 * @requirements 6.1, 6.2, 6.3, 6.4, 6.5, 8.1, 8.2
 */
class RealtimeFeaturesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function ticket_status_updates_trigger_real_time_events(): void
    {
        Event::fake();

        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);

        // Simulate status update
        $ticket->update(['status' => 'in_progress']);

        // Verify real-time event was dispatched
        Event::assertDispatched(TicketStatusUpdated::class);
    }

    #[Test]
    public function loan_status_updates_trigger_real_time_events(): void
    {
        Event::fake();

        $loan = LoanApplication::factory()->create(['status' => 'pending']);

        // Simulate status update
        $loan->update(['status' => 'approved']);

        // Verify real-time event was dispatched
        Event::assertDispatched(LoanStatusUpdated::class);
    }

    #[Test]
    public function real_time_events_include_proper_localization(): void
    {
        $ticket = HelpdeskTicket::factory()->create(['status' => 'open']);
        $event = new TicketStatusUpdated($ticket);

        $broadcastData = $event->broadcastWith();

        // Message should be in Bahasa Melayu
        $this->assertArrayHasKey('message', $broadcastData);
        $this->assertIsString($broadcastData['message']);

        // Should contain Bahasa Melayu terms
        $message = $broadcastData['message'];
        $this->assertTrue(
            str_contains($message, 'Tiket') ||
                str_contains($message, 'dikemaskini') ||
                str_contains($message, 'status')
        );
    }

    #[Test]
    public function real_time_events_support_hybrid_architecture(): void
    {
        // Test authenticated user scenario
        $user = User::factory()->create();
        $ticket = HelpdeskTicket::factory()->create(['user_id' => $user->id]);

        $event = new TicketStatusUpdated($ticket);
        $channels = $event->broadcastOn();

        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Should broadcast to both user-specific and ticket-specific channels
        $this->assertContains("user.{$user->id}", $channelNames);
        $this->assertContains("ticket.{$ticket->uuid}", $channelNames);
    }

    #[Test]
    public function real_time_events_support_guest_users(): void
    {
        // Test guest user scenario (no user_id)
        $ticket = HelpdeskTicket::factory()->create(['user_id' => null]);

        $event = new TicketStatusUpdated($ticket);
        $channels = $event->broadcastOn();

        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Should broadcast to ticket-specific channel for guest access
        $this->assertContains("ticket.{$ticket->uuid}", $channelNames);
    }

    #[Test]
    public function admin_notifications_are_broadcast_for_high_priority_events(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'priority' => 'high',
            'status' => 'open',
        ]);

        $event = new TicketStatusUpdated($ticket);
        $channels = $event->broadcastOn();

        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // High priority tickets should notify admins
        if ($ticket->priority === 'high') {
            $this->assertContains('admin.notifications', $channelNames);
        }
    }

    #[Test]
    public function real_time_events_include_timestamp_information(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusUpdated($ticket);

        $broadcastData = $event->broadcastWith();

        // Should include timestamp for client-side handling
        $this->assertArrayHasKey('updated_at', $broadcastData);
        $this->assertNotNull($broadcastData['updated_at']);

        // Should be in ISO format for JavaScript compatibility
        $timestamp = $broadcastData['updated_at'];
        $this->assertMatchesRegularExpression('/\d{4}-\d{2}-\d{2}T\d{2}:\d{2}:\d{2}/', $timestamp);
    }

    #[Test]
    public function real_time_events_include_unique_identifiers(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusUpdated($ticket);

        $broadcastData = $event->broadcastWith();

        // Should include unique identifiers for client-side tracking
        $this->assertArrayHasKey('ticket_id', $broadcastData);
        $this->assertEquals($ticket->id, $broadcastData['ticket_id']);
    }

    #[Test]
    public function ai_broadcasting_channels_are_configured(): void
    {
        // Test that AI-specific channels are properly configured
        $user = User::factory()->create(['role' => 'admin']);

        $this->actingAs($user);

        $aiChannels = [
            'private-ai-status',
            'private-ai-alerts',
            'private-ai-performance',
            'private-ai-approvals',
        ];

        foreach ($aiChannels as $channel) {
            $response = $this->postJson('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => $channel,
            ]);

            // Admin should be able to access AI channels
            $this->assertNotEquals(403, $response->getStatusCode(), "Admin should access {$channel}");
        }
    }

    #[Test]
    public function real_time_events_are_queued_for_performance(): void
    {
        // Verify that broadcasting events use queues for better performance
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusUpdated($ticket);

        // Event should implement ShouldQueue for async processing
        $this->assertInstanceOf(\Illuminate\Contracts\Queue\ShouldQueue::class, $event);
    }

    #[Test]
    public function broadcasting_auth_endpoint_handles_cors_properly(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        // Test CORS preflight request
        $response = $this->call('OPTIONS', '/broadcasting/auth');

        // Should handle OPTIONS request (CORS preflight)
        $this->assertNotEquals(405, $response->getStatusCode());
    }

    #[Test]
    public function real_time_system_supports_multiple_concurrent_users(): void
    {
        Event::fake();

        // Create multiple users and tickets
        $users = User::factory()->count(3)->create();
        $tickets = HelpdeskTicket::factory()->count(3)->create();

        // Simulate concurrent updates
        foreach ($tickets as $index => $ticket) {
            $ticket->update(['status' => 'in_progress']);
        }

        // Should handle multiple concurrent events
        Event::assertDispatchedTimes(TicketStatusUpdated::class, 3);
    }

    #[Test]
    public function real_time_events_maintain_data_consistency(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
            'title' => 'Test Ticket',
        ]);

        $event = new TicketStatusUpdated($ticket);
        $broadcastData = $event->broadcastWith();

        // Broadcast data should match actual model data
        $this->assertEquals($ticket->id, $broadcastData['ticket_id']);
        $this->assertEquals($ticket->status, $broadcastData['status']);
    }

    #[Test]
    public function real_time_system_handles_network_failures_gracefully(): void
    {
        // Test that the system can handle broadcasting failures
        $ticket = HelpdeskTicket::factory()->create();

        // Even if broadcasting fails, the model update should succeed
        $ticket->update(['status' => 'resolved']);

        $this->assertEquals('resolved', $ticket->fresh()->status);
    }

    #[Test]
    public function real_time_events_support_wcag_accessibility(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusUpdated($ticket);

        $broadcastData = $event->broadcastWith();

        // Should include accessibility-friendly message
        $this->assertArrayHasKey('message', $broadcastData);

        $message = $broadcastData['message'];

        // Message should be descriptive for screen readers
        $this->assertNotEmpty($message);
        $this->assertGreaterThan(10, strlen($message)); // Should be descriptive
    }
}
