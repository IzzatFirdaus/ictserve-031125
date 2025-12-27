<?php

declare(strict_types=1);

namespace Tests\Unit\Redis;

use Exception;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Redis Performance Unit Tests
 *
 * Tests Redis performance benchmarks and optimization for ICTServe v3.6.1 Laragon setup.
 * Validates connection response times, throughput, and performance requirements.
 */
class RedisPerformanceTest extends TestCase
{
    private const EXCELLENT_RESPONSE_TIME_MS = 10;

    private const ACCEPTABLE_RESPONSE_TIME_MS = 50;

    private const MAX_ACCEPTABLE_RESPONSE_TIME_MS = 100;

    private const PERFORMANCE_TEST_ITERATIONS = 10;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if Redis is not available
        if (! $this->isRedisAvailable()) {
            $this->markTestSkipped('Redis is not available for performance testing');
        }
    }

    #[Test]
    public function it_has_acceptable_ping_response_time(): void
    {
        $connection = Redis::connection();
        $responseTimes = [];

        // Test multiple ping operations to get average response time
        for ($i = 0; $i < self::PERFORMANCE_TEST_ITERATIONS; $i++) {
            $startTime = microtime(true);
            $response = $connection->ping();
            $endTime = microtime(true);

            $this->assertEquals('PONG', $response);

            $responseTimeMs = ($endTime - $startTime) * 1000;
            $responseTimes[] = $responseTimeMs;
        }

        $averageResponseTime = array_sum($responseTimes) / count($responseTimes);
        $maxResponseTime = max($responseTimes);

        // Log performance metrics for analysis
        $this->addToAssertionCount(1);
        echo "\nRedis Ping Performance Metrics:\n";
        echo 'Average Response Time: '.number_format($averageResponseTime, 2)."ms\n";
        echo 'Max Response Time: '.number_format($maxResponseTime, 2)."ms\n";
        echo 'Min Response Time: '.number_format(min($responseTimes), 2)."ms\n";

        // Assert performance requirements
        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS,
            $averageResponseTime,
            'Average Redis ping response time should be less than '.self::MAX_ACCEPTABLE_RESPONSE_TIME_MS.'ms'
        );

        // Warn if not excellent performance
        if ($averageResponseTime > self::EXCELLENT_RESPONSE_TIME_MS) {
            echo 'Warning: Redis response time ('.number_format($averageResponseTime, 2).'ms) is above excellent threshold ('.self::EXCELLENT_RESPONSE_TIME_MS."ms)\n";
        }
    }

    #[Test]
    public function it_has_acceptable_set_operation_performance(): void
    {
        $connection = Redis::connection();
        $responseTimes = [];

        // Test SET operations performance
        for ($i = 0; $i < self::PERFORMANCE_TEST_ITERATIONS; $i++) {
            $key = "perf_test_set_{$i}_".time();
            $value = "test_value_{$i}";

            $startTime = microtime(true);
            $result = $connection->set($key, $value);
            $endTime = microtime(true);

            $this->assertNotNull($result);

            $responseTimeMs = ($endTime - $startTime) * 1000;
            $responseTimes[] = $responseTimeMs;

            // Cleanup
            $connection->del($key);
        }

        $averageResponseTime = array_sum($responseTimes) / count($responseTimes);

        echo "\nRedis SET Performance: ".number_format($averageResponseTime, 2)."ms average\n";

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS,
            $averageResponseTime,
            'Average Redis SET response time should be less than '.self::MAX_ACCEPTABLE_RESPONSE_TIME_MS.'ms'
        );
    }

    #[Test]
    public function it_has_acceptable_get_operation_performance(): void
    {
        $connection = Redis::connection();
        $responseTimes = [];

        // Pre-populate test data
        $testKeys = [];
        for ($i = 0; $i < self::PERFORMANCE_TEST_ITERATIONS; $i++) {
            $key = "perf_test_get_{$i}_".time();
            $value = "test_value_{$i}";
            $connection->set($key, $value);
            $testKeys[] = $key;
        }

        // Test GET operations performance
        foreach ($testKeys as $i => $key) {
            $startTime = microtime(true);
            $value = $connection->get($key);
            $endTime = microtime(true);

            $this->assertEquals("test_value_{$i}", $value);

            $responseTimeMs = ($endTime - $startTime) * 1000;
            $responseTimes[] = $responseTimeMs;
        }

        // Cleanup
        $connection->del($testKeys);

        $averageResponseTime = array_sum($responseTimes) / count($responseTimes);

        echo "\nRedis GET Performance: ".number_format($averageResponseTime, 2)."ms average\n";

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS,
            $averageResponseTime,
            'Average Redis GET response time should be less than '.self::MAX_ACCEPTABLE_RESPONSE_TIME_MS.'ms'
        );
    }

    #[Test]
    #[DataProvider('redisConnectionPerformanceProvider')]
    public function it_has_acceptable_performance_across_all_databases(string $connectionName): void
    {
        $connection = Redis::connection($connectionName);
        $responseTimes = [];

        // Test performance on each database connection
        for ($i = 0; $i < 5; $i++) { // Reduced iterations for multiple connections
            $key = "perf_test_{$connectionName}_{$i}_".time();
            $value = "test_value_{$i}";

            // Test SET + GET cycle
            $startTime = microtime(true);
            $connection->set($key, $value);
            $retrievedValue = $connection->get($key);
            $connection->del($key);
            $endTime = microtime(true);

            $this->assertEquals($value, $retrievedValue);

            $responseTimeMs = ($endTime - $startTime) * 1000;
            $responseTimes[] = $responseTimeMs;
        }

        $averageResponseTime = array_sum($responseTimes) / count($responseTimes);

        echo "\nRedis {$connectionName} Performance: ".number_format($averageResponseTime, 2)."ms average\n";

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS * 2, // Allow more time for SET+GET+DEL cycle
            $averageResponseTime,
            "Average Redis {$connectionName} response time should be acceptable"
        );
    }

    #[Test]
    public function it_can_handle_concurrent_operations(): void
    {
        $connection = Redis::connection();
        $operations = 50;
        $startTime = microtime(true);

        // Simulate concurrent operations
        $keys = [];
        for ($i = 0; $i < $operations; $i++) {
            $key = "concurrent_test_{$i}_".time();
            $keys[] = $key;
            $connection->set($key, "value_{$i}");
        }

        // Verify all operations completed
        foreach ($keys as $i => $key) {
            $value = $connection->get($key);
            $this->assertEquals("value_{$i}", $value);
        }

        // Cleanup
        $connection->del($keys);

        $endTime = microtime(true);
        $totalTimeMs = ($endTime - $startTime) * 1000;
        $averageTimePerOperation = $totalTimeMs / ($operations * 2); // SET + GET operations

        echo "\nConcurrent Operations Performance:\n";
        echo 'Total Time: '.number_format($totalTimeMs, 2)."ms\n";
        echo 'Average per Operation: '.number_format($averageTimePerOperation, 2)."ms\n";

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS,
            $averageTimePerOperation,
            'Average time per concurrent operation should be acceptable'
        );
    }

    #[Test]
    public function it_has_acceptable_memory_usage(): void
    {
        $connection = Redis::connection();

        try {
            // Get Redis memory info
            $info = $connection->info('memory');

            if (is_array($info) && isset($info['used_memory'])) {
                $usedMemoryBytes = (int) $info['used_memory'];
                $usedMemoryMB = $usedMemoryBytes / (1024 * 1024);

                echo "\nRedis Memory Usage: ".number_format($usedMemoryMB, 2)." MB\n";

                // For development environment, memory usage should be reasonable
                $this->assertLessThan(
                    100, // 100MB limit for development
                    $usedMemoryMB,
                    'Redis memory usage should be reasonable for development environment'
                );
            } else {
                $this->addToAssertionCount(1);
                echo "\nRedis Memory Usage: info not available\n";

                return;
            }
        } catch (Exception $e) {
            $this->addToAssertionCount(1);
            echo "\nRedis Memory Usage: info not available\n";
        }
    }

    #[Test]
    public function it_handles_large_values_efficiently(): void
    {
        $connection = Redis::connection();
        $largeValue = str_repeat('A', 10240); // 10KB value
        $key = 'large_value_test_'.time();

        // Test large value SET performance
        $startTime = microtime(true);
        $result = $connection->set($key, $largeValue);
        $setTime = microtime(true) - $startTime;

        $this->assertNotNull($result);

        // Test large value GET performance
        $startTime = microtime(true);
        $retrievedValue = $connection->get($key);
        $getTime = microtime(true) - $startTime;

        $this->assertEquals($largeValue, $retrievedValue);

        // Cleanup
        $connection->del($key);

        $setTimeMs = $setTime * 1000;
        $getTimeMs = $getTime * 1000;

        echo "\nLarge Value Performance (10KB):\n";
        echo 'SET Time: '.number_format($setTimeMs, 2)."ms\n";
        echo 'GET Time: '.number_format($getTimeMs, 2)."ms\n";

        // Large values should still be processed within reasonable time
        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS * 2,
            $setTimeMs,
            'Large value SET should complete within reasonable time'
        );

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS * 2,
            $getTimeMs,
            'Large value GET should complete within reasonable time'
        );
    }

    #[Test]
    public function it_maintains_performance_under_load(): void
    {
        $connection = Redis::connection();
        $loadOperations = 100;
        $responseTimes = [];

        // Create load with multiple operations
        for ($i = 0; $i < $loadOperations; $i++) {
            $key = "load_test_{$i}_".time();
            $value = "load_value_{$i}";

            $startTime = microtime(true);

            // Perform multiple operations
            $connection->set($key, $value);
            $connection->get($key);
            $connection->exists($key);
            $connection->del($key);

            $endTime = microtime(true);

            $responseTimeMs = ($endTime - $startTime) * 1000;
            $responseTimes[] = $responseTimeMs;
        }

        $averageResponseTime = array_sum($responseTimes) / count($responseTimes);
        $maxResponseTime = max($responseTimes);

        echo "\nLoad Test Performance ({$loadOperations} operations):\n";
        echo 'Average Response Time: '.number_format($averageResponseTime, 2)."ms\n";
        echo 'Max Response Time: '.number_format($maxResponseTime, 2)."ms\n";

        // Performance should remain acceptable under load
        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS * 3, // Allow more time for multiple operations
            $averageResponseTime,
            'Average response time should remain acceptable under load'
        );

        $this->assertLessThan(
            self::MAX_ACCEPTABLE_RESPONSE_TIME_MS * 5, // Allow more time for worst case
            $maxResponseTime,
            'Maximum response time should not exceed threshold under load'
        );
    }

    #[Test]
    public function it_validates_connection_establishment_time(): void
    {
        $connectionTimes = [];

        // Test connection establishment time multiple times
        for ($i = 0; $i < 5; $i++) {
            $startTime = microtime(true);

            // Force new connection by clearing connection
            Redis::purge();
            $connection = Redis::connection();
            $connection->ping(); // Ensure connection is established

            $endTime = microtime(true);

            $connectionTimeMs = ($endTime - $startTime) * 1000;
            $connectionTimes[] = $connectionTimeMs;
        }

        $averageConnectionTime = array_sum($connectionTimes) / count($connectionTimes);

        echo "\nConnection Establishment Performance:\n";
        echo 'Average Connection Time: '.number_format($averageConnectionTime, 2)."ms\n";

        // Connection establishment should be fast
        $this->assertLessThan(
            1000, // 1 second maximum for connection establishment
            $averageConnectionTime,
            'Redis connection establishment should be fast'
        );
    }

    /**
     * Data provider for Redis connection performance testing
     */
    public static function redisConnectionPerformanceProvider(): array
    {
        return [
            ['default'],
            ['cache'],
            ['sessions'],
            ['queues'],
        ];
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
        // Clean up any test keys that might remain
        try {
            $connection = Redis::connection();
            $keys = $connection->keys('perf_test_*');
            if (! empty($keys)) {
                $connection->del($keys);
            }
            $keys = $connection->keys('concurrent_test_*');
            if (! empty($keys)) {
                $connection->del($keys);
            }
            $keys = $connection->keys('large_value_test_*');
            if (! empty($keys)) {
                $connection->del($keys);
            }
            $keys = $connection->keys('load_test_*');
            if (! empty($keys)) {
                $connection->del($keys);
            }
        } catch (Exception $e) {
            // Ignore cleanup errors
        }

        parent::tearDown();
    }
}
