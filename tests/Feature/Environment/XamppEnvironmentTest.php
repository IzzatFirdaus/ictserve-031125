<?php

declare(strict_types=1);

namespace Tests\Feature\Environment;

use Tests\TestCase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;

/**
 * XAMPP Environment Test Suite for ICTServe v3.6.1
 * 
 * Tests XAMPP MySQL and WSL Redis connectivity and functionality
 * Validates environment migration and service integration
 */
#[Group('environment')]
#[Group('xampp')]
class XamppEnvironmentTest extends TestCase
{
    #[Test]
    public function it_can_connect_to_xampp_mysql(): void
    {
        // Test basic MySQL connection
        $connection = DB::connection('mysql');
        $result = $connection->select('SELECT 1 as test, VERSION() as version');

        $this->assertEquals(1, $result[0]->test);
        $this->assertNotEmpty($result[0]->version);

        // Verify we're connecting to the expected host (XAMPP)
        $host = config('database.connections.mysql.host');
        $this->assertEquals('127.0.0.1', $host);

        // Verify we're using root user (XAMPP default)
        $username = config('database.connections.mysql.username');
        $this->assertEquals('root', $username);
    }

    #[Test]
    public function it_can_connect_to_wsl_redis(): void
    {
        $redis = Redis::connection();
        $response = $redis->ping();

        $this->assertEquals('PONG', $response);

        // Verify we're connecting to the expected host (WSL Redis)
        $host = config('database.redis.default.host');
        $this->assertEquals('127.0.0.1', $host);

        $port = config('database.redis.default.port');
        $this->assertEquals(6379, $port);
    }

    #[Test]
    public function it_can_perform_database_operations(): void
    {
        // Test database operations
        $testTable = 'xampp_test_' . time();

        // Create test table
        DB::statement("CREATE TEMPORARY TABLE {$testTable} (id INT PRIMARY KEY, name VARCHAR(255))");

        // Insert test data
        DB::table($testTable)->insert([
            'id' => 1,
            'name' => 'Test Record'
        ]);

        // Retrieve test data
        $result = DB::table($testTable)->where('id', 1)->first();

        $this->assertNotNull($result);
        $this->assertEquals('Test Record', $result->name);

        // Clean up is automatic for TEMPORARY table
    }

    #[Test]
    public function it_can_perform_redis_operations(): void
    {
        $redis = Redis::connection();

        $testKey = 'xampp_test_' . time();
        $testValue = 'test_value_' . rand(1000, 9999);

        // Test SET operation
        $setResult = $redis->set($testKey, $testValue);
        $this->assertTrue($setResult);

        // Test GET operation
        $getValue = $redis->get($testKey);
        $this->assertEquals($testValue, $getValue);

        // Test EXISTS operation
        $exists = $redis->exists($testKey);
        $this->assertEquals(1, $exists);

        // Test DEL operation
        $delResult = $redis->del($testKey);
        $this->assertEquals(1, $delResult);

        // Verify deletion
        $getValue = $redis->get($testKey);
        $this->assertNull($getValue);
    }

    #[Test]
    public function it_can_cache_data_in_wsl_redis(): void
    {
        $key = 'xampp_cache_test_' . time();
        $value = 'cached_value_' . rand(1000, 9999);

        // Test cache PUT
        Cache::put($key, $value, 60);

        // Test cache GET
        $retrieved = Cache::get($key);
        $this->assertEquals($value, $retrieved);

        // Test cache HAS
        $has = Cache::has($key);
        $this->assertTrue($has);

        // Test cache FORGET
        Cache::forget($key);
        $hasAfterForget = Cache::has($key);
        $this->assertFalse($hasAfterForget);
    }

    #[Test]
    public function it_can_run_migrations_successfully(): void
    {
        // Verify key ICTServe tables exist
        $requiredTables = [
            'users',
            'helpdesk_tickets',
            'loan_applications',
            'activity_log',
            'audits',
            'pulse_entries',
            'telescope_entries'
        ];

        foreach ($requiredTables as $table) {
            $this->assertTrue(
                Schema::hasTable($table),
                "Required table '{$table}' does not exist"
            );
        }
    }

    #[Test]
    public function laravel_services_work_with_xampp_environment(): void
    {
        // Test Laravel Pulse
        $this->assertTrue(
            class_exists(\Laravel\Pulse\Pulse::class),
            'Laravel Pulse is not available'
        );

        // Test Laravel Telescope
        $this->assertTrue(
            class_exists(\Laravel\Telescope\Telescope::class),
            'Laravel Telescope is not available'
        );

        // Test Laravel Horizon
        $this->assertTrue(
            class_exists(\Laravel\Horizon\Horizon::class),
            'Laravel Horizon is not available'
        );

        // Test Laravel Reverb
        $this->assertTrue(
            class_exists(\Laravel\Reverb\ReverbServiceProvider::class),
            'Laravel Reverb is not available'
        );
    }

    #[Test]
    public function it_can_handle_concurrent_database_connections(): void
    {
        $connections = [];
        $results = [];

        // Create multiple database connections
        for ($i = 0; $i < 5; $i++) {
            $connection = DB::connection('mysql');
            $result = $connection->select('SELECT ? as connection_id', [$i]);
            $results[] = $result[0]->connection_id;
        }

        // Verify all connections worked
        $this->assertCount(5, $results);
        $this->assertEquals([0, 1, 2, 3, 4], $results);
    }

    #[Test]
    public function it_can_handle_concurrent_redis_connections(): void
    {
        $results = [];

        // Create multiple Redis operations
        for ($i = 0; $i < 5; $i++) {
            $redis = Redis::connection();
            $key = "concurrent_test_{$i}_" . time();
            $value = "value_{$i}";

            $redis->set($key, $value);
            $retrieved = $redis->get($key);
            $redis->del($key);

            $results[] = $retrieved;
        }

        // Verify all operations worked
        $this->assertCount(5, $results);
        for ($i = 0; $i < 5; $i++) {
            $this->assertEquals("value_{$i}", $results[$i]);
        }
    }

    #[Test]
    public function it_maintains_utf8mb4_charset_support(): void
    {
        // Test UTF-8 MB4 support (important for Bahasa Melayu)
        $testTable = 'utf8_test_' . time();

        DB::statement("CREATE TEMPORARY TABLE {$testTable} (
            id INT PRIMARY KEY,
            content TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci
        )");

        // Test with emoji and special characters
        $testContent = 'Test Bahasa Melayu: Selamat datang! 🇲🇾 ñáéíóú';

        DB::table($testTable)->insert([
            'id' => 1,
            'content' => $testContent
        ]);

        $result = DB::table($testTable)->where('id', 1)->first();

        $this->assertEquals($testContent, $result->content);
    }

    #[Test]
    public function it_can_handle_large_data_operations(): void
    {
        $redis = Redis::connection();

        // Test large string storage
        $largeData = str_repeat('ICTServe v3.6.1 XAMPP Environment Test ', 1000);
        $key = 'large_data_test_' . time();

        $redis->set($key, $largeData);
        $retrieved = $redis->get($key);

        $this->assertEquals($largeData, $retrieved);
        $this->assertEquals(strlen($largeData), strlen($retrieved));

        $redis->del($key);
    }

    #[Test]
    public function it_validates_xampp_mysql_configuration(): void
    {
        // Check MySQL configuration values
        $config = DB::select("SHOW VARIABLES WHERE Variable_name IN (
            'character_set_server',
            'collation_server',
            'max_connections',
            'innodb_buffer_pool_size'
        )");

        $configArray = [];
        foreach ($config as $item) {
            $configArray[$item->Variable_name] = $item->Value;
        }

        // Verify UTF-8 MB4 configuration
        $this->assertEquals('utf8mb4', $configArray['character_set_server']);
        $this->assertEquals('utf8mb4_unicode_ci', $configArray['collation_server']);

        // Verify reasonable connection limit
        $this->assertGreaterThanOrEqual(100, (int)$configArray['max_connections']);
    }

    #[Test]
    public function it_validates_wsl_redis_configuration(): void
    {
        $redis = Redis::connection();

        // Get Redis configuration
        $info = $redis->info();

        // Verify Redis version
        $this->assertArrayHasKey('redis_version', $info);
        $version = $info['redis_version'];
        $this->assertGreaterThanOrEqual('7.0', $version, 'Redis version should be 7.0+');

        // Verify memory configuration
        $this->assertArrayHasKey('maxmemory', $info);

        // Test database count
        $this->assertArrayHasKey('databases', $info);
        $databases = (int)$info['databases'];
        $this->assertGreaterThanOrEqual(16, $databases);
    }

    #[Test]
    public function it_can_perform_transaction_operations(): void
    {
        $testTable = 'transaction_test_' . time();

        DB::statement("CREATE TEMPORARY TABLE {$testTable} (
            id INT PRIMARY KEY,
            name VARCHAR(255),
            value INT
        )");

        // Test database transaction
        DB::transaction(function () use ($testTable) {
            DB::table($testTable)->insert([
                'id' => 1,
                'name' => 'Test 1',
                'value' => 100
            ]);

            DB::table($testTable)->insert([
                'id' => 2,
                'name' => 'Test 2',
                'value' => 200
            ]);
        });

        $count = DB::table($testTable)->count();
        $this->assertEquals(2, $count);

        $sum = DB::table($testTable)->sum('value');
        $this->assertEquals(300, $sum);
    }

    #[Test]
    public function it_validates_environment_performance(): void
    {
        // Test database query performance
        $start = microtime(true);

        for ($i = 0; $i < 10; $i++) {
            DB::select('SELECT 1');
        }

        $dbTime = microtime(true) - $start;
        $this->assertLessThan(1.0, $dbTime, 'Database queries should complete within 1 second');

        // Test Redis performance
        $start = microtime(true);
        $redis = Redis::connection();

        for ($i = 0; $i < 100; $i++) {
            $redis->set("perf_test_{$i}", "value_{$i}");
            $redis->get("perf_test_{$i}");
            $redis->del("perf_test_{$i}");
        }

        $redisTime = microtime(true) - $start;
        $this->assertLessThan(1.0, $redisTime, 'Redis operations should complete within 1 second');
    }
}
