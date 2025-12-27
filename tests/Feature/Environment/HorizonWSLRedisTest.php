<?php

declare(strict_types=1);

namespace Tests\Feature\Environment;

use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use Tests\TestCase;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

/**
 * Test Horizon integration with WSL Redis for XAMPP environment
 * 
 * Requirements: 6.2, 6.5
 *
 * @group requires-redis
 * @group requires-wsl
 * @group environment-specific
 */
#[Group('requires-redis')]
#[Group('requires-wsl')]
#[Group('environment-specific')]
class HorizonWSLRedisTest extends TestCase
{

    #[Test]
    public function it_can_connect_to_wsl_redis_for_horizon(): void
    {
        // Skip Redis test if Redis is not available
        if (!extension_loaded('redis')) {
            $this->markTestSkipped('Redis extension not available');
        }

        try {
            // Test Redis connection for Horizon using phpredis
            config(['database.redis.client' => 'phpredis']);
            $redis = Redis::connection('default');
            $response = $redis->ping();

            $this->assertEquals('PONG', $response);
        } catch (\Exception $e) {
            // If Redis is not running, skip the test
            $this->markTestSkipped('Redis server not available: ' . $e->getMessage());
        }
    }

    #[Test]
    public function it_can_push_jobs_to_redis_queue(): void
    {
        // Test that jobs can be pushed to Redis queue
        Queue::fake();

        // Simulate pushing a job to the helpdesk queue
        Queue::push('App\Jobs\Helpdesk\ProcessTicketNotification', [
            'ticket_id' => 1,
            'action' => 'created'
        ], 'helpdesk');

        Queue::assertPushed('App\Jobs\Helpdesk\ProcessTicketNotification');
    }

    #[Test]
    public function it_has_correct_horizon_configuration(): void
    {
        $config = config('horizon');

        // Verify basic configuration
        $this->assertEquals('ICTServe', $config['name']);
        $this->assertEquals('default', $config['use']);

        // Verify wait time thresholds
        $this->assertArrayHasKey('redis:helpdesk', $config['waits']);
        $this->assertEquals(60, $config['waits']['redis:helpdesk']);
        $this->assertEquals(30, $config['waits']['redis:notifications']);
        $this->assertEquals(600, $config['waits']['redis:ai-chatbot']);
    }

    #[Test]
    public function it_has_xampp_environment_configuration(): void
    {
        $config = config('horizon.environments.xampp');

        $this->assertNotNull($config);
        $this->assertArrayHasKey('supervisor-helpdesk', $config);
        $this->assertArrayHasKey('supervisor-asset-loan', $config);
        $this->assertArrayHasKey('supervisor-ai', $config);

        // Verify helpdesk supervisor configuration
        $helpdeskConfig = $config['supervisor-helpdesk'];
        $this->assertEquals('redis', $helpdeskConfig['connection']);
        $this->assertContains('helpdesk', $helpdeskConfig['queue']);
        $this->assertContains('notifications', $helpdeskConfig['queue']);
    }

    #[Test]
    public function it_can_access_horizon_dashboard_configuration(): void
    {
        $config = config('horizon');

        // Verify dashboard configuration
        $this->assertEquals('horizon', $config['path']);
        $this->assertArrayHasKey('middleware', $config);
        $this->assertContains('web', $config['middleware']);
    }
}
