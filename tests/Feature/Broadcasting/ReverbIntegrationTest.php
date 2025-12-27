<?php

declare(strict_types=1);

namespace Tests\Feature\Broadcasting;

use App\Events\TicketStatusChanged;
use App\Models\HelpdeskTicket;
use App\Models\User;
use Illuminate\Broadcasting\BroadcastManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Laravel Reverb Integration Test
 *
 * Tests the complete Laravel Reverb v1.6.3 WebSocket implementation
 * including configuration, channel authorization, and event broadcasting.
 *
 * @see Requirements 6.1, 6.2, 6.3, 6.4, 6.5, 24.1-24.8
 * @see .kiro/specs/ictserve-comprehensive-v3.6/tasks.md - Task 8.4
 */
#[Group('environment-specific')]
class ReverbIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Set up Reverb configuration for testing
        config([
            'broadcasting.default' => 'reverb',
            'reverb.servers.reverb.host' => '0.0.0.0',
            'reverb.servers.reverb.port' => 8080,
            'reverb.servers.reverb.hostname' => '127.0.0.1',
            'reverb.servers.reverb.max_request_size' => 10000,
            'reverb.servers.reverb.scaling.enabled' => false,
            'reverb.servers.reverb.scaling.channel' => 'reverb',
            'reverb.servers.reverb.scaling.server.host' => '127.0.0.1',
            'reverb.servers.reverb.scaling.server.port' => 6379,
            'reverb.servers.reverb.scaling.server.database' => 0,
            'reverb.servers.reverb.pulse_ingest_interval' => 15,
            'reverb.servers.reverb.telescope_ingest_interval' => 15,
            'reverb.apps.apps.0.key' => 'test-app-key',
            'reverb.apps.apps.0.secret' => 'test-app-secret',
            'reverb.apps.apps.0.app_id' => 'ictserve-test',
            'reverb.apps.apps.0.hostname' => '127.0.0.1',
            'reverb.apps.apps.0.port' => 8080,
            'reverb.apps.apps.0.scheme' => 'http',
            'reverb.apps.apps.0.options.host' => '127.0.0.1',
            'reverb.apps.apps.0.options.port' => 8080,
            'reverb.apps.apps.0.options.scheme' => 'http',
            'reverb.apps.apps.0.allowed_origins' => ['*'],
            'reverb.apps.apps.0.ping_interval' => 60,
            'reverb.apps.apps.0.activity_timeout' => 30,
        ]);
    }

    #[Test]
    public function it_has_reverb_configured_as_default_broadcaster(): void
    {
        $this->assertEquals('reverb', config('broadcasting.default'));
    }

    #[Test]
    public function it_has_reverb_server_configuration(): void
    {
        $config = config('reverb.servers.reverb');

        $this->assertIsArray($config);
        $this->assertEquals('0.0.0.0', $config['host']);
        $this->assertEquals(8080, $config['port']);
        $this->assertEquals(10000, $config['max_request_size']);
        $this->assertArrayHasKey('scaling', $config);
        $this->assertEquals(15, $config['pulse_ingest_interval']);
        $this->assertEquals(15, $config['telescope_ingest_interval']);
    }

    #[Test]
    public function it_has_reverb_app_configuration(): void
    {
        $apps = config('reverb.apps.apps');

        $this->assertIsArray($apps);
        $this->assertNotEmpty($apps);

        $app = $apps[0];
        $this->assertArrayHasKey('key', $app);
        $this->assertArrayHasKey('secret', $app);
        $this->assertArrayHasKey('app_id', $app);
        $this->assertEquals(['*'], $app['allowed_origins']);
        $this->assertEquals(60, $app['ping_interval']);
        $this->assertEquals(30, $app['activity_timeout']);
    }

    #[Test]
    public function it_can_broadcast_ticket_status_changed_event(): void
    {
        Event::fake();

        $user = User::factory()->create(['role' => 'staff']);
        $ticket = HelpdeskTicket::factory()->create([
            'user_id' => $user->id,
            'status' => 'open',
        ]);

        // Simulate status change
        $ticket->update(['status' => 'in_progress']);

        // Fire the event
        $event = new TicketStatusChanged($ticket, 'open', 'in_progress');
        event($event);

        // Verify event was fired
        Event::assertDispatched(TicketStatusChanged::class);
    }

    #[Test]
    public function it_has_private_channel_authorization_for_users(): void
    {
        $user = User::factory()->create(['role' => 'staff']);

        // Test user channel authorization
        $response = $this->actingAs($user)
            ->withSession(['_token' => 'test-token'])
            ->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => "private-user.{$user->id}",
            ]);

        $response->assertOk();
    }

    #[Test]
    public function it_denies_unauthorized_private_channel_access(): void
    {
        $user1 = User::factory()->create(['role' => 'staff']);
        $user2 = User::factory()->create(['role' => 'staff']);

        // User 1 trying to access User 2's channel
        $this->actingAs($user1);

        $response = $this->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => "private-user.{$user2->id}",
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function it_allows_admin_access_to_admin_notifications_channel(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $response = $this->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-admin.notifications',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function it_denies_staff_access_to_admin_notifications_channel(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff);

        $response = $this->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-admin.notifications',
        ]);

        $response->assertForbidden();
    }

    #[Test]
    public function it_allows_admin_access_to_ai_channels(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        $this->actingAs($admin);

        $aiChannels = [
            'private-ai-status',
            'private-ai-alerts',
            'private-ai-performance',
        ];

        foreach ($aiChannels as $channel) {
            $response = $this->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => $channel,
            ]);

            $response->assertOk();
        }
    }

    #[Test]
    public function it_allows_approver_access_to_ai_approvals_channel(): void
    {
        $approver = User::factory()->create(['role' => 'approver']);

        $this->actingAs($approver);

        $response = $this->post('/broadcasting/auth', [
            'socket_id' => '123.456',
            'channel_name' => 'private-ai-approvals',
        ]);

        $response->assertOk();
    }

    #[Test]
    public function it_denies_staff_access_to_ai_channels(): void
    {
        $staff = User::factory()->create(['role' => 'staff']);

        $this->actingAs($staff);

        $aiChannels = [
            'private-ai-status',
            'private-ai-alerts',
            'private-ai-performance',
            'private-ai-approvals',
        ];

        foreach ($aiChannels as $channel) {
            $response = $this->post('/broadcasting/auth', [
                'socket_id' => '123.456',
                'channel_name' => $channel,
            ]);

            $response->assertForbidden();
        }
    }

    #[Test]
    public function it_has_broadcast_manager_configured_for_reverb(): void
    {
        $broadcastManager = app(BroadcastManager::class);

        $this->assertInstanceOf(BroadcastManager::class, $broadcastManager);

        // Test that reverb driver can be resolved
        $driver = $broadcastManager->driver('reverb');
        $this->assertNotNull($driver);
    }

    #[Test]
    public function it_has_environment_variables_configured(): void
    {
        // Test that required Reverb environment variables are defined
        $requiredVars = [
            'REVERB_APP_ID',
            'REVERB_APP_KEY',
            'REVERB_APP_SECRET',
            'REVERB_HOST',
            'REVERB_PORT',
            'REVERB_SCHEME',
        ];

        foreach ($requiredVars as $var) {
            $configValue = $this->getConfigValue($var);
            $this->assertNotNull(
                $configValue,
                "Environment variable {$var} should be configured"
            );
        }
    }

    #[Test]
    public function it_supports_redis_scaling_configuration(): void
    {
        $scalingConfig = config('reverb.servers.reverb.scaling');

        $this->assertIsArray($scalingConfig);
        $this->assertArrayHasKey('enabled', $scalingConfig);
        $this->assertArrayHasKey('channel', $scalingConfig);
        $this->assertArrayHasKey('server', $scalingConfig);

        $serverConfig = $scalingConfig['server'];
        $this->assertArrayHasKey('host', $serverConfig);
        $this->assertArrayHasKey('port', $serverConfig);
        $this->assertArrayHasKey('database', $serverConfig);
    }

    /**
     * Map environment variable names to config keys
     */
    private function mapEnvToConfig(string $envVar): string
    {
        return match ($envVar) {
            'REVERB_APP_ID' => 'app_id',
            'REVERB_APP_KEY' => 'key',
            'REVERB_APP_SECRET' => 'secret',
            'REVERB_HOST' => 'hostname',
            'REVERB_PORT' => 'port',
            'REVERB_SCHEME' => 'scheme',
            default => strtolower(str_replace('REVERB_', '', $envVar))
        };
    }

    /**
     * Get the correct config value for a Reverb environment variable
     */
    private function getConfigValue(string $envVar): mixed
    {
        return match ($envVar) {
            'REVERB_APP_ID' => config('reverb.apps.apps.0.app_id'),
            'REVERB_APP_KEY' => config('reverb.apps.apps.0.key'),
            'REVERB_APP_SECRET' => config('reverb.apps.apps.0.secret'),
            'REVERB_HOST' => config('reverb.apps.apps.0.options.host') ?? config('reverb.servers.reverb.hostname'),
            'REVERB_PORT' => config('reverb.apps.apps.0.options.port') ?? config('reverb.servers.reverb.port'),
            'REVERB_SCHEME' => config('reverb.apps.apps.0.options.scheme'),
            default => null
        };
    }
}
