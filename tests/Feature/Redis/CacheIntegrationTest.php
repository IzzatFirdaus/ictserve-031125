<?php

declare(strict_types=1);

namespace Tests\Feature\Redis;

use Exception;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

/**
 * Redis Cache Integration Tests
 *
 * Tests Redis integration with Laravel's cache system for ICTServe v3.6.1.
 * Validates cache operations, TTL handling, and Redis database separation.
 */
#[Group('requires-redis')]
#[Group('environment-specific')]
class CacheIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if Redis is not available
        if (! $this->isRedisAvailable()) {
            $this->markTestSkipped('Redis is not available for cache integration testing');
        }

        // Ensure cache is using Redis
        if (config('cache.default') !== 'redis') {
            $this->markTestSkipped('Cache is not configured to use Redis');
        }

        // Clear cache before each test
        Cache::flush();
    }

    #[Test]
    public function it_can_store_and_retrieve_cache_values(): void
    {
        $key = 'test_cache_key_'.time();
        $value = 'test_cache_value';

        // Store value in cache
        $result = Cache::put($key, $value, 3600);
        $this->assertTrue($result);

        // Retrieve value from cache
        $retrievedValue = Cache::get($key);
        $this->assertEquals($value, $retrievedValue);

        // Verify cache hit
        $this->assertTrue(Cache::has($key));
    }

    #[Test]
    public function it_uses_dedicated_redis_database_for_cache(): void
    {
        $cacheConnection = config('cache.stores.redis.connection');
        $this->assertEquals('cache', $cacheConnection, 'Cache should use dedicated "cache" connection');

        $cacheDatabase = config('database.redis.cache.database');
        $this->assertEquals('1', $cacheDatabase, 'Cache connection should use database 1');

        // Test that cache operations use the correct database
        $key = 'db_test_'.time();
        $value = 'database_test_value';

        Cache::put($key, $value, 3600);

        // Verify the key exists in the cache database
        $cacheRedis = Redis::connection('cache');
        $exists = $cacheRedis->exists($key);
        $this->assertEquals(1, $exists, 'Cache key should exist in cache database');

        // Verify the key does NOT exist in default database
        $defaultRedis = Redis::connection('default');
        $existsInDefault = $defaultRedis->exists($key);
        $this->assertEquals(0, $existsInDefault, 'Cache key should NOT exist in default database');
    }

    #[Test]
    public function it_handles_cache_expiration_correctly(): void
    {
        $key = 'expiration_test_'.time();
        $value = 'expiring_value';

        // Store with 1 second TTL
        Cache::put($key, $value, 1);

        // Verify immediate availability
        $this->assertTrue(Cache::has($key));
        $this->assertEquals($value, Cache::get($key));

        // Wait for expiration
        sleep(2);

        // Verify expiration
        $this->assertFalse(Cache::has($key));
        $this->assertNull(Cache::get($key));
    }

    #[Test]
    public function it_can_handle_cache_remember_operations(): void
    {
        $key = 'remember_test_'.time();
        $expectedValue = 'remembered_value';
        $callCount = 0;

        // First call should execute callback
        $value1 = Cache::remember($key, 3600, function () use ($expectedValue, &$callCount) {
            $callCount++;

            return $expectedValue;
        });

        $this->assertEquals($expectedValue, $value1);
        $this->assertEquals(1, $callCount);

        // Second call should use cached value
        $value2 = Cache::remember($key, 3600, function () use (&$callCount) {
            $callCount++;

            return 'should_not_be_called';
        });

        $this->assertEquals($expectedValue, $value2);
        $this->assertEquals(1, $callCount, 'Callback should not be called on cache hit');
    }

    #[Test]
    public function it_can_handle_cache_forget_operations(): void
    {
        $key = 'forget_test_'.time();
        $value = 'value_to_forget';

        // Store value
        Cache::put($key, $value, 3600);
        $this->assertTrue(Cache::has($key));

        // Forget value
        $result = Cache::forget($key);
        $this->assertTrue($result);

        // Verify removal
        $this->assertFalse(Cache::has($key));
        $this->assertNull(Cache::get($key));
    }

    #[Test]
    public function it_can_handle_cache_increment_and_decrement(): void
    {
        $key = 'counter_test_'.time();

        // Set initial value
        Cache::put($key, 10, 3600);

        // Test increment
        $newValue = Cache::increment($key, 5);
        $this->assertEquals(15, $newValue);
        $this->assertEquals(15, Cache::get($key));

        // Test decrement
        $newValue = Cache::decrement($key, 3);
        $this->assertEquals(12, $newValue);
        $this->assertEquals(12, Cache::get($key));
    }

    #[Test]
    #[DataProvider('cacheDataTypesProvider')]
    public function it_can_handle_different_data_types($value, string $description): void
    {
        $key = 'datatype_test_'.time().'_'.md5($description);

        // Store value
        Cache::put($key, $value, 3600);

        // Retrieve and verify
        $retrievedValue = Cache::get($key);
        $this->assertEquals($value, $retrievedValue, "Failed to handle {$description}");
    }

    #[Test]
    public function it_can_handle_cache_many_operations(): void
    {
        $values = [
            'key1_'.time() => 'value1',
            'key2_'.time() => 'value2',
            'key3_'.time() => 'value3',
        ];

        // Store multiple values
        $result = Cache::putMany($values, 3600);
        $this->assertTrue($result);

        // Retrieve multiple values
        $retrievedValues = Cache::many(array_keys($values));

        foreach ($values as $key => $expectedValue) {
            $this->assertArrayHasKey($key, $retrievedValues);
            $this->assertEquals($expectedValue, $retrievedValues[$key]);
        }
    }

    #[Test]
    public function it_handles_cache_flush_operations(): void
    {
        $keys = [
            'flush_test_1_'.time() => 'value1',
            'flush_test_2_'.time() => 'value2',
            'flush_test_3_'.time() => 'value3',
        ];

        // Store multiple values
        foreach ($keys as $key => $value) {
            Cache::put($key, $value, 3600);
            $this->assertTrue(Cache::has($key));
        }

        // Flush cache
        $result = Cache::flush();
        $this->assertTrue($result);

        // Verify all keys are removed
        foreach (array_keys($keys) as $key) {
            $this->assertFalse(Cache::has($key), "Key {$key} should be removed after flush");
        }
    }

    #[Test]
    public function it_maintains_cache_performance_standards(): void
    {
        $key = 'performance_test_'.time();
        $value = str_repeat('A', 1024); // 1KB value
        $iterations = 10;

        $putTimes = [];
        $getTimes = [];

        // Test PUT performance
        for ($i = 0; $i < $iterations; $i++) {
            $testKey = $key.'_'.$i;

            $startTime = microtime(true);
            Cache::put($testKey, $value, 3600);
            $endTime = microtime(true);

            $putTimes[] = ($endTime - $startTime) * 1000;
        }

        // Test GET performance
        for ($i = 0; $i < $iterations; $i++) {
            $testKey = $key.'_'.$i;

            $startTime = microtime(true);
            $retrievedValue = Cache::get($testKey);
            $endTime = microtime(true);

            $this->assertEquals($value, $retrievedValue);
            $getTimes[] = ($endTime - $startTime) * 1000;
        }

        $avgPutTime = array_sum($putTimes) / count($putTimes);
        $avgGetTime = array_sum($getTimes) / count($getTimes);

        echo "\nCache Performance Metrics:\n";
        echo 'Average PUT time: '.number_format($avgPutTime, 2)."ms\n";
        echo 'Average GET time: '.number_format($avgGetTime, 2)."ms\n";

        // Performance assertions
        $this->assertLessThan(50, $avgPutTime, 'Cache PUT operations should be fast');
        $this->assertLessThan(50, $avgGetTime, 'Cache GET operations should be fast');
    }

    #[Test]
    public function it_handles_cache_isolation_between_connections(): void
    {
        $key = 'isolation_test_'.time();
        $cacheValue = 'cache_value';
        $defaultValue = 'default_value';

        // Store in cache connection
        Cache::put($key, $cacheValue, 3600);

        // Store same key in default Redis connection
        $defaultRedis = Redis::connection('default');
        $defaultRedis->set($key, $defaultValue);

        // Verify isolation
        $retrievedCacheValue = Cache::get($key);
        $retrievedDefaultValue = $defaultRedis->get($key);

        $this->assertEquals($cacheValue, $retrievedCacheValue, 'Cache should return cache value');
        $this->assertEquals($defaultValue, $retrievedDefaultValue, 'Default connection should return default value');
        $this->assertNotEquals($retrievedCacheValue, $retrievedDefaultValue, 'Values should be isolated');

        // Cleanup
        $defaultRedis->del($key);
    }

    #[Test]
    public function it_handles_cache_prefix_correctly(): void
    {
        $key = 'prefix_test_'.time();
        $value = 'prefixed_value';

        // Store via Laravel Cache
        Cache::put($key, $value, 3600);

        // Check if prefix is applied in Redis
        $cacheRedis = Redis::connection('cache');
        $prefix = config('database.redis.options.prefix', '');

        if (! empty($prefix)) {
            $prefixedKey = $prefix.$key;
            $exists = $cacheRedis->exists($prefixedKey);
            $this->assertEquals(1, $exists, 'Key should exist with prefix in Redis');
        }

        // Verify Laravel Cache can still retrieve it
        $retrievedValue = Cache::get($key);
        $this->assertEquals($value, $retrievedValue);
    }

    #[Test]
    public function it_handles_concurrent_cache_operations(): void
    {
        $baseKey = 'concurrent_test_'.time();
        $operations = 20;

        // Simulate concurrent cache operations
        for ($i = 0; $i < $operations; $i++) {
            $key = $baseKey.'_'.$i;
            $value = 'concurrent_value_'.$i;

            Cache::put($key, $value, 3600);
        }

        // Verify all operations succeeded
        for ($i = 0; $i < $operations; $i++) {
            $key = $baseKey.'_'.$i;
            $expectedValue = 'concurrent_value_'.$i;

            $this->assertTrue(Cache::has($key), "Key {$key} should exist");
            $this->assertEquals($expectedValue, Cache::get($key), "Value for {$key} should match");
        }
    }

    /**
     * Data provider for different cache data types
     */
    public static function cacheDataTypesProvider(): array
    {
        return [
            ['string_value', 'string'],
            [12345, 'integer'],
            [123.45, 'float'],
            [true, 'boolean true'],
            [false, 'boolean false'],
            [['array', 'values'], 'array'],
            [['key' => 'value', 'nested' => ['data']], 'associative array'],
            [null, 'null value'],
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
        // Clean up test cache entries
        try {
            Cache::flush();
        } catch (Exception $e) {
            // Ignore cleanup errors
        }

        parent::tearDown();
    }
}
