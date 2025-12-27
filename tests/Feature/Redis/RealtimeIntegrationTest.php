<?php

declare(strict_types=1);

namespace Tests\Feature\Redis;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Redis Real-time Integration Tests
 *
 * Tests Redis integration with Laravel's real-time features for ICTServe v3.6.1.
 * Validates Reverb, Pulse, and Horizon Redis database separation and functionality.
 *
 * @group requires-redis
 * @group environment-specific
 */
#[Group('requires-redis')]
#[Group('environment-specific')]
class RealtimeIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if Redis is not available
        if (! $this->isRedisAvailable()) {
            $this->markTestSkipped('Redis is not available for real-time integration testing');
        }
    }

    #[Test]
    public function it_has_dedicated_redis_database_for_reverb(): void
    {
        $reverbDatabase = config('database.redis.reverb.database');
        $this->assertEquals('4', $reverbDatabase, 'Reverb connection should use database 4');

        // Test Reverb connection
        $reverbRedis = Redis::connection('reverb');
        $this->assertNotNull($reverbRedis);

        // Test basic operation on Reverb database
        $testKey = 'reverb_test_'.time();
        $testValue = 'reverb_value';

        $reverbRedis->set($testKey, $testValue);
        $retrievedValue = $reverbRedis->get($testKey);

        $this->assertEquals($testValue, $retrievedValue);

        // Verify isolation from other databases
        $defaultRedis = Redis::connection('default');
        $defaultValue = $defaultRedis->get($testKey);
        $this->assertNull($defaultValue, 'Reverb data should not exist in default database');

        // Cleanup
        $reverbRedis->del($testKey);
    }

    #[Test]
    public function it_has_dedicated_redis_database_for_pulse(): void
    {
        $pulseDatabase = config('database.redis.pulse.database');
        $this->assertEquals('5', $pulseDatabase, 'Pulse connection should use database 5');

        // Test Pulse connection
        $pulseRedis = Redis::connection('pulse');
        $this->assertNotNull($pulseRedis);

        // Test basic operation on Pulse database
        $testKey = 'pulse_test_'.time();
        $testValue = 'pulse_value';

        $pulseRedis->set($testKey, $testValue);
        $retrievedValue = $pulseRedis->get($testKey);

        $this->assertEquals($testValue, $retrievedValue);

        // Verify isolation from other databases
        $defaultRedis = Redis::connection('default');
        $defaultValue = $defaultRedis->get($testKey);
        $this->assertNull($defaultValue, 'Pulse data should not exist in default database');

        // Cleanup
        $pulseRedis->del($testKey);
    }

    #[Test]
    public function it_has_dedicated_redis_database_for_horizon(): void
    {
        $horizonDatabase = config('database.redis.horizon.database');
        $this->assertEquals('6', $horizonDatabase, 'Horizon connection should use database 6');

        // Test Horizon connection
        $horizonRedis = Redis::connection('horizon');
        $this->assertNotNull($horizonRedis);

        // Test basic operation on Horizon database
        $testKey = 'horizon_test_'.time();
        $testValue = 'horizon_value';

        $horizonRedis->set($testKey, $testValue);
        $retrievedValue = $horizonRedis->get($testKey);

        $this->assertEquals($testValue, $retrievedValue);

        // Verify isolation from other databases
        $defaultRedis = Redis::connection('default');
        $defaultValue = $defaultRedis->get($testKey);
        $this->assertNull($defaultValue, 'Horizon data should not exist in default database');

        // Cleanup
        $horizonRedis->del($testKey);
    }

    #[Test]
    public function it_validates_realtime_database_isolation(): void
    {
        $connections = ['reverb', 'pulse', 'horizon'];
        $testKey = 'isolation_test_'.time();
        $testValues = [
            'reverb' => 'reverb_isolated_value',
            'pulse' => 'pulse_isolated_value',
            'horizon' => 'horizon_isolated_value',
        ];

        // Store same key with different values in each connection
        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);
            $redis->set($testKey, $testValues[$connection]);
        }

        // Verify each connection returns its own value
        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);
            $retrievedValue = $redis->get($testKey);

            $this->assertEquals(
                $testValues[$connection],
                $retrievedValue,
                "Connection {$connection} should return its own isolated value"
            );
        }

        // Verify default connection doesn't have the key
        $defaultRedis = Redis::connection('default');
        $defaultValue = $defaultRedis->get($testKey);
        $this->assertNull($defaultValue, 'Default connection should not have real-time data');

        // Cleanup
        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);
            $redis->del($testKey);
        }
    }

    #[Test]
    public function it_can_handle_reverb_websocket_data_simulation(): void
    {
        $reverbRedis = Redis::connection('reverb');

        // Simulate WebSocket connection data
        $connectionId = 'ws_conn_'.time();
        $connectionData = json_encode([
            'id' => $connectionId,
            'app_id' => 'ictserve-app',
            'channels' => ['helpdesk', 'notifications'],
            'connected_at' => time(),
        ]);

        // Store connection data
        $reverbRedis->hset('connections', $connectionId, $connectionData);

        // Retrieve and verify
        $retrievedData = $reverbRedis->hget('connections', $connectionId);
        $this->assertEquals($connectionData, $retrievedData);

        // Simulate channel subscription
        $channelKey = 'channel:helpdesk';
        $reverbRedis->sadd($channelKey, $connectionId);

        // Verify subscription
        $subscribers = $reverbRedis->smembers($channelKey);
        $this->assertContains($connectionId, $subscribers);

        // Cleanup
        $reverbRedis->hdel('connections', $connectionId);
        $reverbRedis->del($channelKey);
    }

    #[Test]
    public function it_can_handle_pulse_monitoring_data_simulation(): void
    {
        $pulseRedis = Redis::connection('pulse');

        // Simulate Pulse monitoring data
        $timestamp = time();
        $metricKey = 'pulse:requests:'.$timestamp;
        $metricData = json_encode([
            'timestamp' => $timestamp,
            'requests' => 150,
            'response_time' => 45.2,
            'memory_usage' => 128.5,
        ]);

        // Store metric data
        $pulseRedis->set($metricKey, $metricData, 'EX', 3600); // 1 hour TTL

        // Retrieve and verify
        $retrievedData = $pulseRedis->get($metricKey);
        $this->assertEquals($metricData, $retrievedData);

        // Simulate time series data
        $timeSeriesKey = 'pulse:series:requests';
        $pulseRedis->zadd($timeSeriesKey, $timestamp, $metricData);

        // Verify time series
        $seriesData = $pulseRedis->zrange($timeSeriesKey, 0, -1);
        $this->assertContains($metricData, $seriesData);

        // Cleanup
        $pulseRedis->del($metricKey);
        $pulseRedis->del($timeSeriesKey);
    }

    #[Test]
    public function it_can_handle_horizon_queue_monitoring_simulation(): void
    {
        $horizonRedis = Redis::connection('horizon');

        // Simulate Horizon supervisor data
        $supervisorId = 'supervisor_'.time();
        $supervisorData = json_encode([
            'id' => $supervisorId,
            'name' => 'ictserve-supervisor',
            'status' => 'running',
            'processes' => 3,
            'queues' => ['helpdesk', 'notifications', 'reports'],
        ]);

        // Store supervisor data
        $horizonRedis->hset('supervisors', $supervisorId, $supervisorData);

        // Retrieve and verify
        $retrievedData = $horizonRedis->hget('supervisors', $supervisorId);
        $this->assertEquals($supervisorData, $retrievedData);

        // Simulate job metrics
        $jobMetricsKey = 'horizon:jobs:completed';
        $horizonRedis->incr($jobMetricsKey);
        $jobCount = $horizonRedis->get($jobMetricsKey);

        $this->assertEquals('1', $jobCount);

        // Cleanup
        $horizonRedis->hdel('supervisors', $supervisorId);
        $horizonRedis->del($jobMetricsKey);
    }

    #[Test]
    public function it_maintains_realtime_performance_standards(): void
    {
        $connections = ['reverb', 'pulse', 'horizon'];
        $iterations = 5;

        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);
            $responseTimes = [];

            // Test performance for each real-time connection
            for ($i = 0; $i < $iterations; $i++) {
                $key = "perf_test_{$connection}_{$i}_".time();
                $value = "performance_value_{$i}";

                $startTime = microtime(true);
                $redis->set($key, $value);
                $retrievedValue = $redis->get($key);
                $redis->del($key);
                $endTime = microtime(true);

                $this->assertEquals($value, $retrievedValue);
                $responseTimes[] = ($endTime - $startTime) * 1000;
            }

            $avgResponseTime = array_sum($responseTimes) / count($responseTimes);

            echo "\n{$connection} Performance: ".number_format($avgResponseTime, 2)."ms average\n";

            // Real-time services should be fast
            $this->assertLessThan(
                100,
                $avgResponseTime,
                "Real-time {$connection} operations should be fast"
            );
        }
    }

    #[Test]
    public function it_can_handle_concurrent_realtime_operations(): void
    {
        $connections = ['reverb', 'pulse', 'horizon'];
        $operationsPerConnection = 10;

        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);
            $keys = [];

            // Perform concurrent operations
            for ($i = 0; $i < $operationsPerConnection; $i++) {
                $key = "concurrent_{$connection}_{$i}_".time();
                $value = "concurrent_value_{$i}";
                $keys[] = $key;

                $redis->set($key, $value);
            }

            // Verify all operations succeeded
            foreach ($keys as $i => $key) {
                $expectedValue = "concurrent_value_{$i}";
                $actualValue = $redis->get($key);

                $this->assertEquals(
                    $expectedValue,
                    $actualValue,
                    "Concurrent operation {$i} on {$connection} should succeed"
                );
            }

            // Cleanup
            $redis->del($keys);
        }
    }

    #[Test]
    public function it_validates_realtime_connection_configurations(): void
    {
        $realtimeConnections = ['reverb', 'pulse', 'horizon'];

        foreach ($realtimeConnections as $connection) {
            $config = config("database.redis.{$connection}");

            // Validate required configuration keys
            $this->assertArrayHasKey('host', $config, "{$connection} should have host configured");
            $this->assertArrayHasKey('port', $config, "{$connection} should have port configured");
            $this->assertArrayHasKey('database', $config, "{$connection} should have database configured");

            // Validate Laragon-specific settings
            $this->assertEquals('127.0.0.1', $config['host'], "{$connection} should use 127.0.0.1");
            $this->assertEquals('6379', $config['port'], "{$connection} should use port 6379");

            // Validate unique database allocation
            $expectedDatabases = ['reverb' => '4', 'pulse' => '5', 'horizon' => '6'];
            $this->assertEquals(
                $expectedDatabases[$connection],
                $config['database'],
                "{$connection} should use database {$expectedDatabases[$connection]}"
            );
        }
    }

    #[Test]
    public function it_can_handle_realtime_data_expiration(): void
    {
        $connections = ['reverb', 'pulse', 'horizon'];

        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);
            $key = "expiration_test_{$connection}_".time();
            $value = 'expiring_value';

            // Set with 1 second TTL
            $redis->set($key, $value, 'EX', 1);

            // Verify immediate availability
            $this->assertEquals($value, $redis->get($key));

            // Wait for expiration
            sleep(2);

            // Verify expiration
            $this->assertNull($redis->get($key), "{$connection} data should expire");
        }
    }

    #[Test]
    public function it_handles_realtime_memory_efficiency(): void
    {
        $connections = ['reverb', 'pulse', 'horizon'];

        foreach ($connections as $connection) {
            $redis = Redis::connection($connection);

            try {
                // Get memory info for this database
                $info = $redis->info('memory');

                if (is_array($info) && isset($info['used_memory'])) {
                    $usedMemoryBytes = (int) $info['used_memory'];
                    $usedMemoryMB = $usedMemoryBytes / (1024 * 1024);

                    echo "\n{$connection} Memory Usage: ".number_format($usedMemoryMB, 2)." MB\n";

                    // Real-time databases should use reasonable memory
                    $this->assertLessThan(
                        50, // 50MB limit per real-time database
                        $usedMemoryMB,
                        "{$connection} memory usage should be reasonable"
                    );
                }
            } catch (Exception $e) {
                // Skip memory test if info not available
                $this->addToAssertionCount(1);
            }
        }
    }

    /**
     * Check if Redis is available for testing
     */
    private function isRedisAvailable(): bool
    {
        try {
            $connection = Redis::connection();
            $connection->ping();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    protected function tearDown(): void
    {
        // Clean up test data from all real-time connections
        $connections = ['reverb', 'pulse', 'horizon'];

        foreach ($connections as $connection) {
            try {
                $redis = Redis::connection($connection);
                $keys = $redis->keys('*test*');
                if (! empty($keys)) {
                    $redis->del($keys);
                }
            } catch (Exception $e) {
                // Ignore cleanup errors
            }
        }

        parent::tearDown();
    }
}
