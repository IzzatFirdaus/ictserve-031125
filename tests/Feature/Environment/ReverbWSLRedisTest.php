<?php

declare(strict_types=1);

namespace Tests\Feature\Environment;

use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

/**
 * Test Laravel Reverb WebSocket integration with WSL Redis
 * 
 * Requirements: 6.4, 6.5
 *
 * @group requires-wsl
 * @group requires-redis
 * @group environment-specific
 */
#[Group('requires-wsl')]
#[Group('requires-redis')]
#[Group('environment-specific')]
class ReverbWSLRedisTest extends TestCase
{
    #[Test]
    public function it_has_correct_reverb_configuration(): void
    {
        $config = config('reverb');

        // Verify basic Reverb configuration
        $this->assertEquals('reverb', $config['default']);
        $this->assertArrayHasKey('servers', $config);
        $this->assertArrayHasKey('apps', $config);

        // Verify server configuration
        $this->assertArrayHasKey('reverb', $config['servers']);
        $serverConfig = $config['servers']['reverb'];
        $this->assertEquals('0.0.0.0', $serverConfig['host']);
        $this->assertEquals(8080, $serverConfig['port']);
    }

    #[Test]
    public function it_has_correct_app_configuration(): void
    {
        $config = config('reverb.apps');

        // Verify app configuration exists
        $this->assertArrayHasKey('provider', $config);
        $this->assertArrayHasKey('apps', $config);
        $this->assertEquals('config', $config['provider']);

        // Verify first app configuration
        $appConfig = $config['apps'][0];
        $this->assertArrayHasKey('app_id', $appConfig);
        $this->assertArrayHasKey('key', $appConfig);
        $this->assertArrayHasKey('secret', $appConfig);
        $this->assertArrayHasKey('options', $appConfig);

        // Verify options configuration
        $options = $appConfig['options'];
        $this->assertArrayHasKey('host', $options);
        $this->assertArrayHasKey('port', $options);
        $this->assertArrayHasKey('scheme', $options);
    }

    #[Test]
    public function it_has_correct_scaling_configuration(): void
    {
        $config = config('reverb.servers.reverb.scaling');

        // Verify scaling configuration for WSL Redis
        $this->assertArrayHasKey('enabled', $config);
        $this->assertArrayHasKey('channel', $config);
        $this->assertArrayHasKey('server', $config);

        // Verify Redis server configuration
        $serverConfig = $config['server'];
        $this->assertArrayHasKey('host', $serverConfig);
        $this->assertArrayHasKey('port', $serverConfig);
        $this->assertEquals('127.0.0.1', $serverConfig['host']);
        $this->assertEquals(6379, $serverConfig['port']);
    }

    #[Test]
    public function it_has_correct_broadcast_configuration(): void
    {
        // Verify reverb connection configuration exists
        $reverbConnection = config('broadcasting.connections.reverb');
        $this->assertArrayHasKey('driver', $reverbConnection);
        $this->assertArrayHasKey('key', $reverbConnection);
        $this->assertArrayHasKey('secret', $reverbConnection);
        $this->assertArrayHasKey('app_id', $reverbConnection);
        $this->assertEquals('reverb', $reverbConnection['driver']);

        // Verify reverb options configuration
        $options = $reverbConnection['options'];
        $this->assertArrayHasKey('host', $options);
        $this->assertArrayHasKey('port', $options);
        $this->assertArrayHasKey('scheme', $options);
    }

    #[Test]
    public function it_can_access_reverb_server_configuration(): void
    {
        $config = config('reverb.servers.reverb');

        // Verify server configuration details
        $this->assertEquals('0.0.0.0', $config['host']);
        $this->assertEquals(8080, $config['port']);
        $this->assertArrayHasKey('options', $config);
        $this->assertEquals(10000, $config['max_request_size']);

        // Verify pulse and telescope integration
        $this->assertEquals(15, $config['pulse_ingest_interval']);
        $this->assertEquals(15, $config['telescope_ingest_interval']);
    }

    #[Test]
    public function it_has_redis_connection_for_scaling(): void
    {
        // Skip Redis test if Redis is not available
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not available');
        }

        // Verify Redis connection configuration for Reverb scaling
        $redisConfig = config('database.redis.default');
        $this->assertEquals('127.0.0.1', $redisConfig['host']);
        $this->assertEquals(6379, $redisConfig['port']);
        $this->assertEquals('phpredis', config('database.redis.client'));
    }
}
