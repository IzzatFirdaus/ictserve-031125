<?php

declare(strict_types=1);

namespace Tests\Feature\Redis;

use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Test Job for Queue Integration Testing
 */
class TestRedisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $data;

    public static array $processedJobs = [];

    public function __construct(string $data)
    {
        $this->data = $data;
    }

    public function handle(): void
    {
        self::$processedJobs[] = $this->data;
    }
}

/**
 * Redis Queue Integration Tests
 *
 * Tests Redis integration with Laravel's queue system for ICTServe v3.6.1.
 * Validates job dispatching, processing, and Redis database separation.
 */
#[Group('requires-redis')]
#[Group('environment-specific')]
class QueueIntegrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // Skip tests if Redis is not available
        if (! $this->isRedisAvailable()) {
            $this->markTestSkipped('Redis is not available for queue integration testing');
        }

        // Clear processed jobs tracker
        TestRedisJob::$processedJobs = [];

        // Clear any existing queue jobs
        $this->clearRedisQueues();
    }

    #[Test]
    public function it_can_dispatch_jobs_to_redis_queue(): void
    {
        $testData = 'test_job_data_'.time();
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Dispatch job
        $job = new TestRedisJob($testData);
        Queue::push($job, '', $queueName);

        // Verify job was queued
        $this->assertGreaterThan(0, $connection->size($queueName), 'Job should be queued');
    }

    #[Test]
    public function it_uses_dedicated_redis_database_for_queues(): void
    {
        // Verify queue connection configuration
        $queueConnection = config('queue.connections.redis.connection');
        $this->assertEquals('queues', $queueConnection, 'Queue should use dedicated "queues" connection');

        $queueDatabase = config('database.redis.queues.database');
        $this->assertEquals('3', $queueDatabase, 'Queue connection should use database 3');

        // Dispatch a job
        $testData = 'db_isolation_test_'.time();
        $job = new TestRedisJob($testData);
        Queue::push($job);

        // Check that job exists in queue database
        $queueRedis = Redis::connection('queues');
        $queueKeys = $queueRedis->keys('*queue*');
        $this->assertNotEmpty($queueKeys, 'Queue data should exist in queue database');

        // Verify job does NOT exist in default database
        $defaultRedis = Redis::connection('default');
        $defaultQueueKeys = $defaultRedis->keys('*queue*');
        $this->assertEmpty($defaultQueueKeys, 'Queue data should NOT exist in default database');
    }

    #[Test]
    public function it_can_process_queued_jobs(): void
    {
        $testData = 'processable_job_'.time();

        // Dispatch job
        $job = new TestRedisJob($testData);
        Queue::push($job);

        // Process the queue
        $this->artisan('queue:work', [
            '--once' => true,
            '--timeout' => 10,
        ]);

        // Verify job was processed
        $this->assertContains($testData, TestRedisJob::$processedJobs, 'Job should be processed');
    }

    #[Test]
    public function it_can_handle_multiple_queued_jobs(): void
    {
        $jobCount = 5;
        $testJobs = [];
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Dispatch multiple jobs
        for ($i = 0; $i < $jobCount; $i++) {
            $testData = 'multi_job_'.$i.'_'.time();
            $testJobs[] = $testData;

            $job = new TestRedisJob($testData);
            Queue::push($job, '', $queueName);
        }

        // Verify all jobs are queued
        $this->assertEquals($jobCount, $connection->size($queueName), 'All jobs should be queued');

        // Process all jobs
        for ($i = 0; $i < $jobCount; $i++) {
            $this->artisan('queue:work', [
                '--once' => true,
                '--timeout' => 10,
            ]);
        }

        // Verify all jobs were processed
        foreach ($testJobs as $testData) {
            $this->assertContains($testData, TestRedisJob::$processedJobs, "Job {$testData} should be processed");
        }
    }

    #[Test]
    public function it_can_handle_delayed_jobs(): void
    {
        $testData = 'delayed_job_'.time();
        $delay = 2; // 2 seconds delay
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Dispatch delayed job
        $job = new TestRedisJob($testData);
        Queue::later($delay, $job, '', $queueName);

        // Verify job is not immediately available
        $this->assertEquals(0, $connection->size($queueName), 'Delayed job should not be immediately available');

        // Wait for delay
        sleep($delay + 1);

        // Process the queue
        $this->artisan('queue:work', [
            '--once' => true,
            '--timeout' => 10,
        ]);

        // Verify delayed job was processed
        $this->assertContains($testData, TestRedisJob::$processedJobs, 'Delayed job should be processed after delay');
    }

    #[Test]
    public function it_can_handle_job_priorities(): void
    {
        $highPriorityData = 'high_priority_'.time();
        $lowPriorityData = 'low_priority_'.time();

        // Dispatch low priority job first
        $lowPriorityJob = new TestRedisJob($lowPriorityData);
        Queue::pushOn('low', $lowPriorityJob);

        // Dispatch high priority job second
        $highPriorityJob = new TestRedisJob($highPriorityData);
        Queue::pushOn('high', $highPriorityJob);

        // Process high priority queue first
        $this->artisan('queue:work', [
            '--once' => true,
            '--queue' => 'high',
            '--timeout' => 10,
        ]);

        // Verify high priority job was processed first
        $this->assertContains($highPriorityData, TestRedisJob::$processedJobs, 'High priority job should be processed');
        $this->assertNotContains($lowPriorityData, TestRedisJob::$processedJobs, 'Low priority job should not be processed yet');

        // Process low priority queue
        $this->artisan('queue:work', [
            '--once' => true,
            '--queue' => 'low',
            '--timeout' => 10,
        ]);

        // Verify low priority job was processed
        $this->assertContains($lowPriorityData, TestRedisJob::$processedJobs, 'Low priority job should be processed');
    }

    #[Test]
    public function it_maintains_queue_performance_standards(): void
    {
        $jobCount = 10;
        $dispatchTimes = [];
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Test job dispatch performance
        for ($i = 0; $i < $jobCount; $i++) {
            $testData = 'performance_job_'.$i.'_'.time();
            $job = new TestRedisJob($testData);

            $startTime = microtime(true);
            Queue::push($job, '', $queueName);
            $endTime = microtime(true);

            $dispatchTimes[] = ($endTime - $startTime) * 1000;
        }

        $avgDispatchTime = array_sum($dispatchTimes) / count($dispatchTimes);

        echo "\nQueue Performance Metrics:\n";
        echo 'Average Dispatch Time: '.number_format($avgDispatchTime, 2)."ms\n";
        echo 'Jobs Queued: '.$connection->size($queueName)."\n";

        // Performance assertions
        $this->assertLessThan(100, $avgDispatchTime, 'Job dispatch should be fast');
        $this->assertEquals($jobCount, $connection->size($queueName), 'All jobs should be queued');
    }

    #[Test]
    public function it_handles_queue_size_operations(): void
    {
        $queueName = 'default';
        $connection = Queue::connection('redis');
        $initialSize = $connection->size($queueName);

        // Dispatch jobs
        $jobCount = 3;
        for ($i = 0; $i < $jobCount; $i++) {
            $job = new TestRedisJob('size_test_'.$i);
            Queue::push($job, '', $queueName);
        }

        // Verify queue size increased
        $newSize = $connection->size($queueName);
        $this->assertEquals($initialSize + $jobCount, $newSize, 'Queue size should increase by job count');

        // Process one job
        $this->artisan('queue:work', [
            '--once' => true,
            '--timeout' => 10,
        ]);

        // Verify queue size decreased
        $processedSize = $connection->size($queueName);
        $this->assertEquals($newSize - 1, $processedSize, 'Queue size should decrease after processing');
    }

    #[Test]
    public function it_handles_queue_isolation_between_connections(): void
    {
        $testData = 'isolation_test_'.time();
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Dispatch job to queue
        $job = new TestRedisJob($testData);
        Queue::push($job, '', $queueName);

        // Store data in default Redis connection with queue-like key
        $defaultRedis = Redis::connection('default');
        $defaultRedis->lpush('queues:default', 'default_queue_data');

        // Verify queue isolation
        $queueSize = $connection->size($queueName);
        $this->assertGreaterThan(0, $queueSize, 'Queue should have jobs');

        $defaultQueueSize = $defaultRedis->llen('queues:default');
        $this->assertGreaterThan(0, $defaultQueueSize, 'Default connection should have data');

        // Process queue job
        $this->artisan('queue:work', [
            '--once' => true,
            '--timeout' => 10,
        ]);

        // Verify job was processed from queue connection
        $this->assertContains($testData, TestRedisJob::$processedJobs, 'Queue job should be processed');

        // Verify default connection data is unaffected
        $remainingDefaultData = $defaultRedis->llen('queues:default');
        $this->assertEquals($defaultQueueSize, $remainingDefaultData, 'Default connection data should be unaffected');

        // Cleanup
        $defaultRedis->del('queues:default');
    }

    #[Test]
    public function it_can_handle_concurrent_queue_operations(): void
    {
        $jobCount = 20;
        $testJobs = [];
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Dispatch multiple jobs concurrently
        for ($i = 0; $i < $jobCount; $i++) {
            $testData = 'concurrent_job_'.$i.'_'.time();
            $testJobs[] = $testData;

            $job = new TestRedisJob($testData);
            Queue::push($job, '', $queueName);
        }

        // Verify all jobs are queued
        $this->assertEquals($jobCount, $connection->size($queueName), 'All concurrent jobs should be queued');

        // Process all jobs
        for ($i = 0; $i < $jobCount; $i++) {
            $this->artisan('queue:work', [
                '--once' => true,
                '--timeout' => 10,
            ]);
        }

        // Verify all jobs were processed
        $this->assertEquals($jobCount, count(TestRedisJob::$processedJobs), 'All concurrent jobs should be processed');

        foreach ($testJobs as $testData) {
            $this->assertContains($testData, TestRedisJob::$processedJobs, "Concurrent job {$testData} should be processed");
        }
    }

    #[Test]
    public function it_handles_queue_connection_configuration(): void
    {
        // Verify Redis queue connection configuration
        $queueConfig = config('queue.connections.redis');

        $this->assertArrayHasKey('connection', $queueConfig);
        $this->assertEquals('queues', $queueConfig['connection']);

        $this->assertArrayHasKey('queue', $queueConfig);
        $this->assertEquals('default', $queueConfig['queue']);

        $this->assertArrayHasKey('retry_after', $queueConfig);
        $this->assertGreaterThan(0, $queueConfig['retry_after']);
    }

    #[Test]
    public function it_can_clear_queue_operations(): void
    {
        $queueName = 'default';
        $connection = Queue::connection('redis');

        // Dispatch test jobs
        $jobCount = 5;
        for ($i = 0; $i < $jobCount; $i++) {
            $job = new TestRedisJob('clear_test_'.$i);
            Queue::push($job, '', $queueName);
        }

        // Verify jobs are queued
        $this->assertEquals($jobCount, $connection->size($queueName), 'Jobs should be queued');

        // Clear the queue
        $this->clearRedisQueues();

        // Verify queue is empty
        $this->assertEquals(0, $connection->size($queueName), 'Queue should be empty after clearing');
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

    /**
     * Clear Redis queues for testing
     */
    private function clearRedisQueues(): void
    {
        try {
            $queueRedis = Redis::connection('queues');
            $keys = $queueRedis->keys('*');
            if (! empty($keys)) {
                $queueRedis->del($keys);
            }
        } catch (Exception $e) {
            // Ignore cleanup errors
        }
    }

    protected function tearDown(): void
    {
        // Clear queues and processed jobs
        $this->clearRedisQueues();
        TestRedisJob::$processedJobs = [];

        parent::tearDown();
    }
}
