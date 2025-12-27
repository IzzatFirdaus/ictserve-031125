<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Facades\Config;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * WebSocket Connection Test
 *
 * Tests WebSocket connection capabilities and Laravel Echo integration
 * for the ICTServe real-time communication system.
 *
 * @see resources/js/bootstrap.js - Echo configuration
 * @see config/reverb.php - Reverb server configuration
 *
 * @requirements 6.1, 6.2, 6.4, 6.5
 *
 * @group environment-specific
 */
#[Group('environment-specific')]
class WebSocketConnectionTest extends TestCase
{
    #[Test]
    public function echo_configuration_variables_are_available(): void
    {
        // Test that Vite environment variables for Echo are configured
        $this->assertNotNull(env('VITE_REVERB_APP_KEY'));
        $this->assertNotNull(env('VITE_REVERB_HOST'));
        $this->assertNotNull(env('VITE_REVERB_PORT'));
        $this->assertNotNull(env('VITE_REVERB_SCHEME'));
    }

    #[Test]
    public function reverb_server_host_configuration_is_valid(): void
    {
        $serverHost = Config::get('reverb.servers.reverb.host');
        $serverPort = Config::get('reverb.servers.reverb.port');

        $this->assertNotNull($serverHost);
        $this->assertNotNull($serverPort);
        $this->assertIsString($serverHost);
        $this->assertIsInt($serverPort);
        $this->assertGreaterThan(0, $serverPort);
        $this->assertLessThan(65536, $serverPort);
    }

    #[Test]
    public function reverb_app_configuration_matches_client_config(): void
    {
        $appKey = Config::get('reverb.apps.apps.0.key');
        $appHost = Config::get('reverb.apps.apps.0.options.host');
        $appPort = Config::get('reverb.apps.apps.0.options.port');
        $appScheme = Config::get('reverb.apps.apps.0.options.scheme');

        // These should match the VITE environment variables
        $this->assertEquals(env('REVERB_APP_KEY'), $appKey);
        $this->assertEquals(env('REVERB_HOST'), $appHost);
        $this->assertEquals(env('REVERB_PORT'), $appPort);
        $this->assertEquals(env('REVERB_SCHEME'), $appScheme);
    }

    #[Test]
    public function websocket_security_settings_are_configured(): void
    {
        $appConfig = Config::get('reverb.apps.apps.0');

        // Verify security-related settings
        $this->assertArrayHasKey('allowed_origins', $appConfig);
        $this->assertArrayHasKey('max_message_size', $appConfig);
        $this->assertArrayHasKey('ping_interval', $appConfig);
        $this->assertArrayHasKey('activity_timeout', $appConfig);

        // Verify reasonable values
        $this->assertIsInt($appConfig['max_message_size']);
        $this->assertGreaterThan(0, $appConfig['max_message_size']);
        $this->assertIsInt($appConfig['ping_interval']);
        $this->assertGreaterThan(0, $appConfig['ping_interval']);
        $this->assertIsInt($appConfig['activity_timeout']);
        $this->assertGreaterThan(0, $appConfig['activity_timeout']);
    }

    #[Test]
    public function reverb_scaling_configuration_is_valid(): void
    {
        $scalingConfig = Config::get('reverb.servers.reverb.scaling');

        $this->assertNotNull($scalingConfig);
        $this->assertIsBool($scalingConfig['enabled']);
        $this->assertIsString($scalingConfig['channel']);
        $this->assertIsArray($scalingConfig['server']);

        // Verify Redis configuration for scaling
        $redisConfig = $scalingConfig['server'];
        $this->assertArrayHasKey('host', $redisConfig);
        $this->assertArrayHasKey('port', $redisConfig);
        $this->assertArrayHasKey('database', $redisConfig);
    }

    #[Test]
    public function websocket_connection_supports_both_ws_and_wss(): void
    {
        $appScheme = Config::get('reverb.apps.apps.0.options.scheme');
        $useTLS = Config::get('reverb.apps.apps.0.options.useTLS');

        // Should support both HTTP and HTTPS schemes
        $this->assertContains($appScheme, ['http', 'https']);

        // useTLS should match scheme
        if ($appScheme === 'https') {
            $this->assertTrue($useTLS);
        } else {
            $this->assertFalse($useTLS);
        }
    }

    #[Test]
    public function reverb_monitoring_integration_is_configured(): void
    {
        $pulseInterval = Config::get('reverb.servers.reverb.pulse_ingest_interval');
        $telescopeInterval = Config::get('reverb.servers.reverb.telescope_ingest_interval');

        // Should have monitoring intervals configured
        $this->assertIsInt($pulseInterval);
        $this->assertGreaterThan(0, $pulseInterval);
        $this->assertIsInt($telescopeInterval);
        $this->assertGreaterThan(0, $telescopeInterval);
    }

    #[Test]
    public function websocket_fallback_configuration_exists(): void
    {
        // Verify Pusher fallback configuration exists
        $pusherConfig = Config::get('broadcasting.connections.pusher');
        $websocketsConfig = Config::get('broadcasting.connections.websockets');

        $this->assertNotNull($pusherConfig);
        $this->assertNotNull($websocketsConfig);

        // Both should have proper driver configuration
        $this->assertEquals('pusher', $pusherConfig['driver']);
        $this->assertEquals('pusher', $websocketsConfig['driver']);
    }

    #[Test]
    public function connection_state_management_is_configured(): void
    {
        // Test that connection management variables are properly set
        $pingInterval = Config::get('reverb.apps.apps.0.ping_interval');
        $activityTimeout = Config::get('reverb.apps.apps.0.activity_timeout');

        // Ping interval should be reasonable (not too frequent, not too rare)
        $this->assertGreaterThanOrEqual(30, $pingInterval);
        $this->assertLessThanOrEqual(300, $pingInterval);

        // Activity timeout should be reasonable
        $this->assertGreaterThanOrEqual(15, $activityTimeout);
        $this->assertLessThanOrEqual(120, $activityTimeout);
    }

    #[Test]
    public function websocket_message_size_limits_are_reasonable(): void
    {
        $maxRequestSize = Config::get('reverb.servers.reverb.max_request_size');
        $maxMessageSize = Config::get('reverb.apps.apps.0.max_message_size');

        // Should have reasonable limits (not too small, not too large)
        $this->assertGreaterThanOrEqual(1024, $maxRequestSize); // At least 1KB
        $this->assertLessThanOrEqual(1048576, $maxRequestSize); // At most 1MB

        $this->assertGreaterThanOrEqual(1024, $maxMessageSize); // At least 1KB
        $this->assertLessThanOrEqual(1048576, $maxMessageSize); // At most 1MB
    }

    #[Test]
    public function development_configuration_is_suitable_for_local_testing(): void
    {
        if (app()->environment('local', 'testing')) {
            $host = Config::get('reverb.apps.apps.0.options.host');
            $scheme = Config::get('reverb.apps.apps.0.options.scheme');

            // In development, should use localhost/127.0.0.1 and HTTP
            $this->assertContains($host, ['127.0.0.1', 'localhost']);
            $this->assertEquals('http', $scheme);
        }

        $this->assertTrue(true); // Test passes regardless of environment
    }

    #[Test]
    public function websocket_cors_configuration_allows_local_development(): void
    {
        $allowedOrigins = Config::get('reverb.apps.apps.0.allowed_origins');

        $this->assertNotNull($allowedOrigins);
        $this->assertIsArray($allowedOrigins);

        // Should allow local development (either * or specific localhost origins)
        $this->assertTrue(
            in_array('*', $allowedOrigins) ||
                ! empty(array_filter($allowedOrigins, fn ($origin) => str_contains($origin, 'localhost') || str_contains($origin, '127.0.0.1')))
        );
    }
}
