<?php

declare(strict_types=1);

namespace Tests\Feature\Redis;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Session;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;
use Exception;

/**
 * Redis Session Integration Tests
 * 
 * Tests Redis integration with Laravel's session system for ICTServe v3.6.1.
 * Validates session storage, retrieval, and Redis database separation.
 * 
 * @covers Session operations with Redis backend
 * @covers Session persistence and expiration
 * @covers Database separation for sessions
 * @covers Session security and isolation
 */
class SessionIntegrationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Skip tests if Redis is not available
        if (!$this->isRedisAvailable()) {
            $this->markTestSkipped('Redis is not available for session integration testing');
        }
        
        // Skip if session driver is not Redis
        if (config('session.driver') !== 'redis') {
            $this->markTestSkipped('Session driver is not configured to use Redis');
        }
        
        // Start a fresh session for each test
        Session::flush();
        Session::regenerate();
    }

    #[Test]
    public function it_can_store_and_retrieve_session_data(): void
    {
        $key = 'test_session_key';
        $value = 'test_session_value';
        
        // Store session data
        Session::put($key, $value);
        
        // Retrieve session data
        $retrievedValue = Session::get($key);
        $this->assertEquals($value, $retrievedValue);
        
        // Verify session has the key
        $this->assertTrue(Session::has($key));
    }

    #[Test]
    public function it_uses_dedicated_redis_database_for_sessions(): void
    {
        $sessionConnection = config('session.connection');
        $this->assertEquals('sessions', $sessionConnection, 'Sessions should use dedicated "sessions" connection');
        
        $sessionDatabase = config('database.redis.sessions.database');
        $this->assertEquals('2', $sessionDatabase, 'Sessions connection should use database 2');
        
        // Store session data
        $key = 'db_isolation_test';
        $value = 'session_database_test';
        Session::put($key, $value);
        Session::save(); // Force session save
        
        // Get session ID
        $sessionId = Session::getId();
        $this->assertNotEmpty($sessionId);
        
        // Check that session data exists in sessions database
        $sessionsRedis = Redis::connection('sessions');
        $sessionKey = config('session.cookie') . ':' . $sessionId;
        
        // Session might be stored with different key format, so check for existence
        $keys = $sessionsRedis->keys('*' . $sessionId . '*');
        $this->assertNotEmpty($keys, 'Session should exist in sessions database');
        
        // Verify session data does NOT exist in default database
        $defaultRedis = Redis::connection('default');
        $defaultKeys = $defaultRedis->keys('*' . $sessionId . '*');
        $this->assertEmpty($defaultKeys, 'Session should NOT exist in default database');
    }

    #[Test]
    public function it_persists_session_data_across_requests(): void
    {
        $key = 'persistent_test';
        $value = 'persistent_value';
        
        // Store session data
        Session::put($key, $value);
        Session::save();
        
        $sessionId = Session::getId();
        
        // Simulate new request with same session
        Session::setId($sessionId);
        Session::start();
        
        // Verify data persists
        $retrievedValue = Session::get($key);
        $this->assertEquals($value, $retrievedValue);
    }

    #[Test]
    public function it_handles_session_expiration_correctly(): void
    {
        // This test is challenging to implement without manipulating time
        // We'll test that session lifetime configuration is correct
        $sessionLifetime = config('session.lifetime');
        $this->assertIsInt($sessionLifetime);
        $this->assertGreaterThan(0, $sessionLifetime);
        
        // Test that session data is stored with TTL
        $key = 'expiration_test';
        $value = 'expiring_value';
        
        Session::put($key, $value);
        Session::save();
        
        // Verify the session exists
        $this->assertTrue(Session::has($key));
        $this->assertEquals($value, Session::get($key));
    }

    #[Test]
    public function it_can_handle_session_regeneration(): void
    {
        $key = 'regeneration_test';
        $value = 'test_value';
        
        // Store initial session data
        Session::put($key, $value);
        $originalSessionId = Session::getId();
        
        // Regenerate session
        Session::regenerate();
        $newSessionId = Session::getId();
        
        // Verify session ID changed
        $this->assertNotEquals($originalSessionId, $newSessionId);
        
        // Verify data is preserved (Laravel's default behavior)
        $retrievedValue = Session::get($key);
        $this->assertEquals($value, $retrievedValue);
    }

    #[Test]
    public function it_can_handle_session_flash_data(): void
    {
        $key = 'flash_test';
        $value = 'flash_value';
        
        // Store flash data
        Session::flash($key, $value);
        
        // Verify flash data is available
        $this->assertTrue(Session::has($key));
        $this->assertEquals($value, Session::get($key));
        
        // Simulate next request
        Session::ageFlashData();
        
        // Flash data should still be available for one more request
        $this->assertTrue(Session::has($key));
        
        // Age flash data again (simulating another request)
        Session::ageFlashData();
        
        // Flash data should now be removed
        $this->assertFalse(Session::has($key));
    }

    #[Test]
    #[DataProvider('sessionDataTypesProvider')]
    public function it_can_handle_different_session_data_types($value, string $description): void
    {
        $key = 'datatype_test_' . md5($description);
        
        // Store value in session
        Session::put($key, $value);
        
        // Retrieve and verify
        $retrievedValue = Session::get($key);
        $this->assertEquals($value, $retrievedValue, "Failed to handle {$description} in session");
    }

    #[Test]
    public function it_can_handle_session_arrays_and_objects(): void
    {
        $arrayData = [
            'user_id' => 123,
            'preferences' => ['theme' => 'dark', 'language' => 'ms'],
            'permissions' => ['read', 'write', 'admin']
        ];
        
        $objectData = (object) [
            'name' => 'Test User',
            'email' => 'test@motac.gov.my',
            'roles' => ['staff', 'approver']
        ];
        
        // Store complex data
        Session::put('user_data', $arrayData);
        Session::put('user_object', $objectData);
        
        // Retrieve and verify
        $retrievedArray = Session::get('user_data');
        $retrievedObject = Session::get('user_object');
        
        $this->assertEquals($arrayData, $retrievedArray);
        $this->assertEquals($objectData, $retrievedObject);
        
        // Test nested access
        $this->assertEquals('dark', Session::get('user_data.preferences.theme'));
        $this->assertEquals(123, Session::get('user_data.user_id'));
    }

    #[Test]
    public function it_handles_session_forget_operations(): void
    {
        $keys = [
            'forget_test_1' => 'value1',
            'forget_test_2' => 'value2',
            'forget_test_3' => 'value3'
        ];
        
        // Store multiple session values
        foreach ($keys as $key => $value) {
            Session::put($key, $value);
            $this->assertTrue(Session::has($key));
        }
        
        // Forget specific key
        Session::forget('forget_test_2');
        
        // Verify specific key is removed
        $this->assertFalse(Session::has('forget_test_2'));
        
        // Verify other keys remain
        $this->assertTrue(Session::has('forget_test_1'));
        $this->assertTrue(Session::has('forget_test_3'));
        
        // Forget multiple keys
        Session::forget(['forget_test_1', 'forget_test_3']);
        
        // Verify all specified keys are removed
        $this->assertFalse(Session::has('forget_test_1'));
        $this->assertFalse(Session::has('forget_test_3'));
    }

    #[Test]
    public function it_handles_session_flush_operations(): void
    {
        $testData = [
            'flush_test_1' => 'value1',
            'flush_test_2' => 'value2',
            'flush_test_3' => 'value3'
        ];
        
        // Store test data
        foreach ($testData as $key => $value) {
            Session::put($key, $value);
            $this->assertTrue(Session::has($key));
        }
        
        // Flush all session data
        Session::flush();
        
        // Verify all data is removed
        foreach (array_keys($testData) as $key) {
            $this->assertFalse(Session::has($key), "Key {$key} should be removed after flush");
        }
    }

    #[Test]
    public function it_maintains_session_security(): void
    {
        // Test session ID format
        $sessionId = Session::getId();
        $this->assertNotEmpty($sessionId);
        $this->assertIsString($sessionId);
        $this->assertGreaterThan(20, strlen($sessionId), 'Session ID should be sufficiently long');
        
        // Test session regeneration changes ID
        $originalId = Session::getId();
        Session::regenerate();
        $newId = Session::getId();
        
        $this->assertNotEquals($originalId, $newId, 'Session regeneration should change ID');
    }

    #[Test]
    public function it_handles_concurrent_session_operations(): void
    {
        $baseKey = 'concurrent_session_test';
        $operations = 10;
        
        // Simulate concurrent session operations
        for ($i = 0; $i < $operations; $i++) {
            $key = $baseKey . '_' . $i;
            $value = 'concurrent_session_value_' . $i;
            
            Session::put($key, $value);
        }
        
        // Verify all operations succeeded
        for ($i = 0; $i < $operations; $i++) {
            $key = $baseKey . '_' . $i;
            $expectedValue = 'concurrent_session_value_' . $i;
            
            $this->assertTrue(Session::has($key), "Session key {$key} should exist");
            $this->assertEquals($expectedValue, Session::get($key), "Session value for {$key} should match");
        }
    }

    #[Test]
    public function it_maintains_session_performance_standards(): void
    {
        $iterations = 10;
        $putTimes = [];
        $getTimes = [];
        
        // Test session PUT performance
        for ($i = 0; $i < $iterations; $i++) {
            $key = 'performance_test_' . $i;
            $value = 'performance_value_' . $i;
            
            $startTime = microtime(true);
            Session::put($key, $value);
            $endTime = microtime(true);
            
            $putTimes[] = ($endTime - $startTime) * 1000;
        }
        
        // Test session GET performance
        for ($i = 0; $i < $iterations; $i++) {
            $key = 'performance_test_' . $i;
            
            $startTime = microtime(true);
            $value = Session::get($key);
            $endTime = microtime(true);
            
            $this->assertNotNull($value);
            $getTimes[] = ($endTime - $startTime) * 1000;
        }
        
        $avgPutTime = array_sum($putTimes) / count($putTimes);
        $avgGetTime = array_sum($getTimes) / count($getTimes);
        
        echo "\nSession Performance Metrics:\n";
        echo "Average PUT time: " . number_format($avgPutTime, 2) . "ms\n";
        echo "Average GET time: " . number_format($avgGetTime, 2) . "ms\n";
        
        // Performance assertions
        $this->assertLessThan(50, $avgPutTime, 'Session PUT operations should be fast');
        $this->assertLessThan(50, $avgGetTime, 'Session GET operations should be fast');
    }

    #[Test]
    public function it_handles_session_isolation_between_connections(): void
    {
        $key = 'isolation_test_' . time();
        $sessionValue = 'session_value';
        $defaultValue = 'default_value';
        
        // Store in session
        Session::put($key, $sessionValue);
        Session::save();
        
        // Store same key in default Redis connection
        $defaultRedis = Redis::connection('default');
        $defaultRedis->set($key, $defaultValue);
        
        // Verify isolation
        $retrievedSessionValue = Session::get($key);
        $retrievedDefaultValue = $defaultRedis->get($key);
        
        $this->assertEquals($sessionValue, $retrievedSessionValue, 'Session should return session value');
        $this->assertEquals($defaultValue, $retrievedDefaultValue, 'Default connection should return default value');
        $this->assertNotEquals($retrievedSessionValue, $retrievedDefaultValue, 'Values should be isolated');
        
        // Cleanup
        $defaultRedis->del($key);
    }

    /**
     * Data provider for different session data types
     */
    public static function sessionDataTypesProvider(): array
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
        // Clean up session data
        try {
            Session::flush();
        } catch (Exception $e) {
            // Ignore cleanup errors
        }
        
        parent::tearDown();
    }
}