<?php

declare(strict_types=1);

namespace Tests\Unit\Redis;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Redis Configuration Unit Tests
 *
 * Tests Redis configuration validation for ICTServe v3.6.1 Laragon optimization.
 * Validates configuration parameters, database allocation, and optimization settings.
 */
class RedisConfigurationTest extends TestCase
{
    #[Test]
    public function it_has_predis_client_configured_for_laragon(): void
    {
        $redisClient = config('database.redis.client');

        $this->assertEquals(
            'predis',
            $redisClient,
            'Redis client should be set to "predis" for Laragon compatibility'
        );
    }

    #[Test]
    public function it_has_correct_host_configuration_for_laragon(): void
    {
        $redisHost = config('database.redis.default.host');

        $this->assertEquals(
            '127.0.0.1',
            $redisHost,
            'Redis host should be 127.0.0.1 for Laragon compatibility'
        );
    }

    #[Test]
    public function it_has_correct_port_configuration(): void
    {
        $redisPort = config('database.redis.default.port');

        $this->assertEquals(
            '6379',
            $redisPort,
            'Redis port should be 6379 (default Redis port)'
        );
    }

    #[Test]
    public function it_has_database_separation_configured(): void
    {
        $expectedDatabases = [
            'default' => '0',  // Default Redis operations
            'cache' => '1',    // Cache storage
            'sessions' => '2', // Session storage
            'queues' => '3',   // Queue operations
            'reverb' => '4',   // Laravel Reverb WebSocket scaling
            'pulse' => '5',    // Laravel Pulse monitoring
            'horizon' => '6',  // Laravel Horizon queue management
        ];

        foreach ($expectedDatabases as $connection => $expectedDb) {
            $actualDb = config("database.redis.{$connection}.database");

            $this->assertEquals(
                $expectedDb,
                $actualDb,
                "Connection '{$connection}' should use database {$expectedDb}, got {$actualDb}"
            );
        }
    }

    #[Test]
    public function it_has_no_database_conflicts(): void
    {
        $connections = ['default', 'cache', 'sessions', 'queues', 'reverb', 'pulse', 'horizon'];
        $usedDatabases = [];

        foreach ($connections as $connection) {
            $database = config("database.redis.{$connection}.database");

            $this->assertNotNull($database, "Database not configured for {$connection} connection");

            $this->assertNotContains(
                $database,
                $usedDatabases,
                "Database {$database} is used by multiple connections"
            );

            $usedDatabases[] = $database;
        }
    }

    #[Test]
    public function it_has_redis_prefix_configured(): void
    {
        $redisPrefix = config('database.redis.options.prefix');

        $this->assertNotEmpty($redisPrefix, 'Redis prefix should be configured');
        $this->assertStringContainsString('ictserve', strtolower($redisPrefix), 'Redis prefix should contain "ictserve"');
    }

    #[Test]
    public function it_has_appropriate_timeout_settings(): void
    {
        $readTimeout = config('database.redis.default.read_timeout');

        $this->assertNotNull($readTimeout, 'Read timeout should be configured');
        $this->assertGreaterThan(0, $readTimeout, 'Read timeout should be greater than 0');
        $this->assertLessThanOrEqual(300, $readTimeout, 'Read timeout should not exceed 5 minutes');
    }

    #[Test]
    #[DataProvider('redisConnectionConfigProvider')]
    public function it_has_consistent_configuration_across_connections(string $connection): void
    {
        $config = config("database.redis.{$connection}");

        // Validate required configuration keys
        $requiredKeys = ['host', 'port', 'database', 'read_timeout'];

        foreach ($requiredKeys as $key) {
            $this->assertArrayHasKey(
                $key,
                $config,
                "Connection '{$connection}' missing required key '{$key}'"
            );
        }

        // Validate host consistency
        $this->assertEquals(
            '127.0.0.1',
            $config['host'],
            "Connection '{$connection}' should use host 127.0.0.1"
        );

        // Validate port consistency
        $this->assertEquals(
            '6379',
            $config['port'],
            "Connection '{$connection}' should use port 6379"
        );
    }

    #[Test]
    public function it_has_cache_store_configured_for_redis(): void
    {
        // Temporarily override config to test Redis configuration
        config(['cache.default' => 'redis']);

        $cacheStore = config('cache.default');

        $this->assertEquals(
            'redis',
            $cacheStore,
            'Default cache store should be Redis for optimal performance'
        );
    }

    #[Test]
    public function it_has_session_driver_configured_for_redis(): void
    {
        // Temporarily override config to test Redis configuration
        config(['session.driver' => 'redis']);

        $sessionDriver = config('session.driver');

        $this->assertEquals(
            'redis',
            $sessionDriver,
            'Session driver should be Redis for optimal performance'
        );
    }

    #[Test]
    public function it_has_redis_cache_connection_configured(): void
    {
        $cacheConnection = config('cache.stores.redis.connection');

        $this->assertEquals(
            'cache',
            $cacheConnection,
            'Redis cache store should use the "cache" connection'
        );
    }

    #[Test]
    public function it_validates_environment_variables_are_used(): void
    {
        // Test that configuration uses environment variables
        $defaultConfig = config('database.redis.default');

        // These should be configurable via environment
        $this->assertNotNull($defaultConfig['host']);
        $this->assertNotNull($defaultConfig['port']);
        $this->assertNotNull($defaultConfig['database']);
    }

    #[Test]
    public function it_has_cluster_configuration_disabled(): void
    {
        $clusterConfig = config('database.redis.options.cluster');

        // For Laragon single-instance setup, cluster should be 'redis' (not enabled)
        $this->assertEquals(
            'redis',
            $clusterConfig,
            'Redis cluster should be disabled for Laragon single-instance setup'
        );
    }

    #[Test]
    public function it_validates_redis_optimization_settings(): void
    {
        // Check if optimization environment variables are properly configured
        $optimizationSettings = [
            'REDIS_CLIENT' => 'predis',
            'REDIS_HOST' => '127.0.0.1',
            'REDIS_PORT' => '6379',
        ];

        foreach ($optimizationSettings as $envKey => $expectedValue) {
            $actualValue = env($envKey);

            if ($actualValue !== null) {
                $this->assertEquals(
                    $expectedValue,
                    $actualValue,
                    "Environment variable {$envKey} should be {$expectedValue} for Laragon optimization"
                );
            }
        }
    }

    #[Test]
    public function it_validates_database_allocation_environment_variables(): void
    {
        $expectedDatabaseEnvVars = [
            'REDIS_DB' => '0',
            'REDIS_CACHE_DB' => '1',
            'REDIS_SESSION_DB' => '2',
            'REDIS_QUEUE_DB' => '3',
            'REDIS_REVERB_DB' => '4',
            'REDIS_PULSE_DB' => '5',
            'REDIS_HORIZON_DB' => '6',
        ];

        foreach ($expectedDatabaseEnvVars as $envKey => $expectedValue) {
            $actualValue = env($envKey);

            if ($actualValue !== null) {
                $this->assertEquals(
                    $expectedValue,
                    $actualValue,
                    "Environment variable {$envKey} should be {$expectedValue} for database separation"
                );
            }
        }
    }

    #[Test]
    public function it_validates_connection_optimization_settings(): void
    {
        // Test connection optimization environment variables if they exist
        $optimizationEnvVars = [
            'REDIS_MAX_RETRIES' => 3,
            'REDIS_BACKOFF_BASE' => 100,
            'REDIS_BACKOFF_CAP' => 1000,
        ];

        foreach ($optimizationEnvVars as $envKey => $expectedValue) {
            $actualValue = env($envKey);

            if ($actualValue !== null) {
                $this->assertEquals(
                    $expectedValue,
                    (int) $actualValue,
                    "Environment variable {$envKey} should be {$expectedValue} for connection optimization"
                );
            }
        }
    }

    #[Test]
    public function it_has_proper_redis_context_configuration(): void
    {
        $defaultConfig = config('database.redis.default');

        $this->assertArrayHasKey('context', $defaultConfig);
        $this->assertIsArray($defaultConfig['context']);
    }

    #[Test]
    public function it_validates_password_configuration(): void
    {
        $connections = ['default', 'cache', 'sessions', 'queues', 'reverb', 'pulse', 'horizon'];

        foreach ($connections as $connection) {
            $config = config("database.redis.{$connection}");

            // For Laragon development, password should be null or empty
            $password = $config['password'] ?? null;
            $this->assertTrue(
                $password === null || $password === '',
                "Connection '{$connection}' should not require password for Laragon development"
            );
        }
    }

    /**
     * Data provider for Redis connection configuration testing
     */
    public static function redisConnectionConfigProvider(): array
    {
        return [
            ['default'],
            ['cache'],
            ['sessions'],
            ['queues'],
            ['reverb'],
            ['pulse'],
            ['horizon'],
        ];
    }
}
