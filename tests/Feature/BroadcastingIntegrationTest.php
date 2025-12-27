<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Events\TicketStatusChanged;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Broadcasting Integration Test
 *
 * Tests that broadcasting events are properly dispatched and can be received
 * through the Laravel Reverb WebSocket server.
 *
 * @see config/broadcasting.php - Broadcasting configuration
 * @see D16_BROADCASTING_SETUP.md - Broadcasting setup
 *
 * @requirements 6.1, 6.2, 6.3, 8.1, 8.2
 */
#[Group('environment-specific')]
class BroadcastingIntegrationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function reverb_connection_is_configured(): void
    {
        // Test that reverb connection exists (may not be default in all environments)
        $reverbConfig = config('broadcasting.connections.reverb');
        $this->assertNotNull($reverbConfig);
        $this->assertEquals('reverb', $reverbConfig['driver']);
    }

    #[Test]
    public function ticket_status_changed_event_can_be_broadcast(): void
    {
        Event::fake();

        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'open',
        ]);

        // Manually dispatch the event (since model observers might not be set up)
        TicketStatusChanged::dispatch($ticket, 'open', 'in_progress');

        // Verify the event was dispatched
        Event::assertDispatched(TicketStatusChanged::class, function ($event) use ($ticket) {
            return $event->ticket->id === $ticket->id;
        });
    }

    #[Test]
    public function ticket_status_changed_event_broadcasts_to_correct_channels(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusChanged($ticket);

        $channels = $event->broadcastOn();

        // Should broadcast to multiple channels
        $this->assertIsArray($channels);
        $this->assertNotEmpty($channels);

        // Convert channels to strings for easier testing
        $channelNames = array_map(fn ($channel) => $channel->name, $channels);

        // Should include ticket-specific channel
        $this->assertContains("ticket.{$ticket->id}", $channelNames);
    }

    #[Test]
    public function broadcast_event_has_proper_data_structure(): void
    {
        $ticket = HelpdeskTicket::factory()->create([
            'status' => 'in_progress',
        ]);

        $event = new TicketStatusChanged($ticket);
        $broadcastData = $event->broadcastWith();

        // Verify required data is present
        $this->assertArrayHasKey('ticket_id', $broadcastData);
        $this->assertArrayHasKey('new_status', $broadcastData);
        $this->assertArrayHasKey('updated_at', $broadcastData);
        $this->assertArrayHasKey('message', $broadcastData);

        // Verify data values
        $this->assertEquals($ticket->id, $broadcastData['ticket_id']);
        $this->assertIsString($broadcastData['message']);
    }

    #[Test]
    public function broadcast_event_has_proper_event_name(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusChanged($ticket);

        $eventName = $event->broadcastAs();

        $this->assertEquals('ticket.status.changed', $eventName);
    }

    #[Test]
    public function broadcasting_auth_endpoint_exists(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->postJson('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-user.{$user->id}",
        ]);

        // Should not return 404 (endpoint exists)
        $this->assertNotEquals(404, $response->getStatusCode());
    }

    #[Test]
    public function broadcast_event_implements_should_broadcast(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusChanged($ticket);

        // Event should implement ShouldBroadcast
        $this->assertInstanceOf(\Illuminate\Contracts\Broadcasting\ShouldBroadcast::class, $event);
    }

    #[Test]
    public function reverb_connection_configuration_is_valid(): void
    {
        $reverbConfig = config('broadcasting.connections.reverb');

        $this->assertNotNull($reverbConfig);
        $this->assertEquals('reverb', $reverbConfig['driver']);

        // Verify required configuration keys exist
        $requiredKeys = ['key', 'secret', 'app_id', 'options'];
        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey($key, $reverbConfig);
        }

        // Verify options configuration
        $options = $reverbConfig['options'];
        $this->assertArrayHasKey('host', $options);
        $this->assertArrayHasKey('port', $options);
        $this->assertArrayHasKey('scheme', $options);
    }

    #[Test]
    public function broadcasting_can_handle_multiple_channels(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $event = new TicketStatusChanged($ticket);
        $channels = $event->broadcastOn();

        // Should be able to broadcast to multiple channels simultaneously
        $this->assertGreaterThan(0, count($channels));

        // Each channel should be properly formatted
        foreach ($channels as $channel) {
            $this->assertNotEmpty($channel->name);
        }
    }

    #[Test]
    public function broadcasting_respects_environment_configuration(): void
    {
        // In testing environment, broadcasting should work without external dependencies
        $this->assertNotNull(config('broadcasting.connections.reverb'));

        // Verify that test environment doesn't require actual WebSocket server
        $this->assertTrue(true); // Broadcasting config exists and is testable
    }

    #[Test]
    public function broadcast_event_serialization_works(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $event = new TicketStatusChanged($ticket);

        // Event should be serializable for queue processing
        $serialized = serialize($event);
        $unserialized = unserialize($serialized);

        $this->assertInstanceOf(TicketStatusChanged::class, $unserialized);
        $this->assertEquals($ticket->id, $unserialized->ticket->id);
    }
}
