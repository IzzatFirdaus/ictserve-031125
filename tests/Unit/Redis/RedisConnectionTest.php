<?php

declare(strict_types=1);

namespace Tests\Unit\Redis;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Redis Connection Unit Tests
 *
 * Tests Redis connection functionality, timeout handling, and database separation
 * for ICTServe v3.6.1 Laragon optimization.
 */
class RedisConnectionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if Redis is not available
        if (! $this->isRedisAvailable()) {
            $this->markTestSkipped('Redis is not available for testing');
        }
    }

    #[Test]
    public function it_can_establish_redis_connection(): void
    {
        // Test basic Redis connection
        $connection = Redis::connection();

        $this->assertNotNull($connection);

        // Test ping command
        $response = $connection->ping();
        $this->assertEquals('PONG', $response);
    }

    #[Test]
    public function it_uses_predis_client_for_laragon_compatibility(): void
    {
        // Verify that Predis client is configured
        $this->assertEquals('predis', config('database.redis.client'));

        // Test that connection uses Predis
        $connection = Redis::connection();
        $this->assertInstanceOf(\Predis\Client::class, $connection);
    }

    #[Test]
    public function it_connects_to_correct_host_and_port(): void
    {
        // Verify Redis host configuration for Laragon
        $this->assertEquals('127.0.0.1', config('database.redis.default.host'));
        $this->assertEquals('6379', config('database.redis.default.port'));

        // Test actual connection
        $connection = Redis::connection();
        $response = $connection->ping();
        $this->assertEquals('PONG', $response);
    }

    #[Test]
    #[DataProvider('redisConnectionProvider')]
    public function it_can_connect_to_different_redis_databases(string $connectionName, int $expectedDatabase): void
    {
        // Test connection to specific Redis database
        $connection = Redis::connection($connectionName);

        $this->assertNotNull($connection);

        // Verify database number
        $this->assertEquals($expectedDatabase, config("database.redis.{$connectionName}.database"));

        // Test basic operation
        $testKey = "test_key_{$connectionName}_".time();
        $testValue = "test_value_{$connectionName}";

        $connection->set($testKey, $testValue);
        $retrievedValue = $connection->get($testKey);

        $this->assertEquals($testValue, $retrievedValue);

        // Cleanup
        $connection->del($testKey);
    }

    #[Test]
    public function it_handles_connection_timeout_gracefully(): void
    {
        // Test connection timeout handling
        $this->expectNotToPerformAssertions();

        try {
            $connection = Redis::connection();
            $connection->ping();
        } catch (Exception $e) {
            $this->fail('Redis connection should not timeout with proper configuration: '.$e->getMessage());
        }
    }

    #[Test]
    public function it_validates_database_separation(): void
    {
        $connections = ['default', 'cache', 'sessions', 'queues', 'reverb', 'pulse', 'horizon'];
        $usedDatabases = [];

        foreach ($connections as $connectionName) {
            $database = config("database.redis.{$connectionName}.database");

            // Ensure database is configured
            $this->assertNotNull($database, "Database not configured for {$connectionName} connection");

            // Ensure no database conflicts
            $this->assertNotContains(
                $database,
                $usedDatabases,
                "Database {$database} is used by multiple connections"
            );

            $usedDatabases[] = $database;
        }

        // Verify expected database allocation
        $expectedDatabases = [
            'default' => '0',
            'cache' => '1',
            'sessions' => '2',
            'queues' => '3',
            'reverb' => '4',
            'pulse' => '5',
            'horizon' => '6',
        ];

        foreach ($expectedDatabases as $connection => $expectedDb) {
            $actualDb = config("database.redis.{$connection}.database");
            $this->assertEquals(
                $expectedDb,
                $actualDb,
                "Expected {$connection} to use database {$expectedDb}, got {$actualDb}"
            );
        }
    }

    #[Test]
    public function it_can_perform_basic_redis_operations(): void
    {
        $connection = Redis::connection();
        $testKey = 'test_basic_operations_'.time();

        // Test SET operation
        $result = $connection->set($testKey, 'test_value');
        $this->assertTrue($result);

        // Test GET operation
        $value = $connection->get($testKey);
        $this->assertEquals('test_value', $value);

        // Test EXISTS operation
        $exists = $connection->exists($testKey);
        $this->assertEquals(1, $exists);

        // Test DEL operation
        $deleted = $connection->del($testKey);
        $this->assertEquals(1, $deleted);

        // Verify deletion
        $value = $connection->get($testKey);
        $this->assertNull($value);
    }

    #[Test]
    public function it_handles_redis_errors_gracefully(): void
    {
        $connection = Redis::connection();

        // Test invalid command handling
        try {
            $connection->eval('invalid lua script', 0);
            $this->fail('Expected Redis error for invalid Lua script');
        } catch (Exception $e) {
            $this->assertStringContainsString('ERR', $e->getMessage());
        }
    }

    #[Test]
    public function it_supports_redis_prefix_configuration(): void
    {
        $expectedPrefix = config('database.redis.options.prefix');
        $this->assertNotEmpty($expectedPrefix, 'Redis prefix should be configured');

        // Test that prefix is applied
        $connection = Redis::connection();
        $testKey = 'prefix_test_'.time();

        $connection->set($testKey, 'test_value');

        // The key should exist with prefix
        $exists = $connection->exists($testKey);
        $this->assertEquals(1, $exists);

        // Cleanup
        $connection->del($testKey);
    }

    #[Test]
    public function it_validates_connection_parameters(): void
    {
        $defaultConfig = config('database.redis.default');

        // Validate required parameters
        $this->assertArrayHasKey('host', $defaultConfig);
        $this->assertArrayHasKey('port', $defaultConfig);
        $this->assertArrayHasKey('database', $defaultConfig);

        // Validate Laragon-specific settings
        $this->assertEquals('127.0.0.1', $defaultConfig['host']);
        $this->assertEquals('6379', $defaultConfig['port']);

        // Validate timeout settings
        $this->assertArrayHasKey('read_timeout', $defaultConfig);
        $this->assertGreaterThan(0, $defaultConfig['read_timeout']);
    }

    /**
     * Data provider for Redis connection testing
     */
    public static function redisConnectionProvider(): array
    {
        return [
            'default connection' => ['default', 0],
            'cache connection' => ['cache', 1],
            'sessions connection' => ['sessions', 2],
            'queues connection' => ['queues', 3],
            'reverb connection' => ['reverb', 4],
            'pulse connection' => ['pulse', 5],
            'horizon connection' => ['horizon', 6],
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
            $keys = $connection->keys('test_*');
            if (! empty($keys)) {
                $connection->del($keys);
            }
        } catch (Exception $e) {
            // Ignore cleanup errors
        }

        parent::tearDown();
    }
}
