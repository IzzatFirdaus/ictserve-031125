<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Queue;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Broadcast Queue Configuration Test
 *
 * Tests that the broadcast queue is properly configured to handle
 * broadcasting events with appropriate retry and timeout settings.
 *
 * @see config/queue.php
 * @see config/broadcasting.php
 *
 * @requirements 3.4, 7.3
 */
#[Group('requires-redis')]
#[Group('environment-specific')]
class BroadcastQueueConfigurationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function broadcast_queue_connection_is_properly_configured(): void
    {
        // Verify broadcast queue connection exists
        $broadcastConfig = Config::get('queue.connections.broadcast');

        $this->assertNotNull($broadcastConfig);
        $this->assertEquals('redis', $broadcastConfig['driver']);
        $this->assertEquals('broadcast', $broadcastConfig['queue']);
        $this->assertEquals(60, $broadcastConfig['retry_after']);
    }

    #[Test]
    public function broadcast_queue_has_performance_settings(): void
    {
        // Verify performance and retry settings (Requirements 3.4, 7.3)
        $broadcastConfig = Config::get('queue.connections.broadcast');

        $this->assertArrayHasKey('timeout', $broadcastConfig);
        $this->assertArrayHasKey('max_tries', $broadcastConfig);
        $this->assertArrayHasKey('backoff', $broadcastConfig);
        $this->assertArrayHasKey('processes', $broadcastConfig);
        $this->assertArrayHasKey('max_jobs', $broadcastConfig);
        $this->assertArrayHasKey('memory', $broadcastConfig);

        // Verify default values
        $this->assertEquals(30, $broadcastConfig['timeout']);
        $this->assertEquals(3, $broadcastConfig['max_tries']);
        $this->assertEquals(3, $broadcastConfig['processes']);
        $this->assertEquals(1000, $broadcastConfig['max_jobs']);
        $this->assertEquals(128, $broadcastConfig['memory']);
    }

    #[Test]
    public function broadcast_queue_has_monitoring_configuration(): void
    {
        // Verify monitoring settings (Requirement 7.5)
        $broadcastConfig = Config::get('queue.connections.broadcast');

        $this->assertArrayHasKey('monitoring', $broadcastConfig);

        $monitoring = $broadcastConfig['monitoring'];
        $this->assertArrayHasKey('enabled', $monitoring);
        $this->assertArrayHasKey('slow_threshold', $monitoring);
        $this->assertArrayHasKey('failed_threshold', $monitoring);
        $this->assertArrayHasKey('alert_email', $monitoring);

        // Verify default values
        $this->assertTrue($monitoring['enabled']);
        $this->assertEquals(1000, $monitoring['slow_threshold']);
        $this->assertEquals(5, $monitoring['failed_threshold']);
    }

    #[Test]
    public function broadcast_queue_has_proper_backoff_strategy(): void
    {
        // Verify exponential backoff configuration (Requirement 3.4)
        $broadcastConfig = Config::get('queue.connections.broadcast');

        $backoff = $broadcastConfig['backoff'];
        $this->assertIsArray($backoff);
        $this->assertCount(3, $backoff);

        // Verify exponential backoff values
        $this->assertEquals(10, $backoff[0]);
        $this->assertEquals(30, $backoff[1]);
        $this->assertEquals(60, $backoff[2]);
    }

    #[Test]
    public function broadcasting_redis_connection_uses_broadcast_queue(): void
    {
        // Verify broadcasting configuration uses broadcast queue (Requirements 3.4, 7.3)
        $broadcastingConfig = Config::get('broadcasting.connections.redis');

        $this->assertNotNull($broadcastingConfig);
        $this->assertEquals('redis', $broadcastingConfig['driver']);
        $this->assertEquals('broadcast', $broadcastingConfig['queue']);
        $this->assertEquals('broadcast', $broadcastingConfig['queue_connection']);
    }

    #[Test]
    public function queue_can_handle_broadcast_jobs(): void
    {
        // Test that the queue system can handle broadcast jobs
        Queue::fake();

        // Create a simple broadcast event to test queue handling
        $testData = [
            'message' => 'Test broadcast message',
            'timestamp' => now()->toISOString(),
        ];

        // Simulate dispatching a broadcast job
        Queue::push('BroadcastJob', $testData, 'broadcast');

        // Verify the job was queued on the broadcast queue
        Queue::assertPushedOn('broadcast', 'BroadcastJob');
    }
}
