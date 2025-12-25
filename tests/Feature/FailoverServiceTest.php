<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\FailoverEvent;
use App\Models\User;
use App\Services\DisasterRecoveryService;
use App\Services\FailoverService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Failover Service Test
 *
 * PKS Business Continuity (Requirement 29) - Tests for failover mechanisms
 *
 * @see D03-FR-029 (Business Continuity)
 *
 * @trace Requirements 29.3, 29.4
 */
class FailoverServiceTest extends TestCase
{
    use RefreshDatabase;

    private FailoverService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $drService = app(DisasterRecoveryService::class);
        $this->service = new FailoverService($drService);
    }

    /**
     * Test comprehensive health check returns expected structure
     */
    public function test_health_check_returns_expected_structure(): void
    {
        $result = $this->service->checkAllComponentsHealth();

        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('components', $result);
        $this->assertArrayHasKey('overall_health', $result);
        $this->assertArrayHasKey('failover_recommended', $result);

        // Check all components are present
        $this->assertArrayHasKey(FailoverService::COMPONENT_DATABASE, $result['components']);
        $this->assertArrayHasKey(FailoverService::COMPONENT_REDIS, $result['components']);
        $this->assertArrayHasKey(FailoverService::COMPONENT_STORAGE, $result['components']);
        $this->assertArrayHasKey(FailoverService::COMPONENT_QUEUE, $result['components']);
        $this->assertArrayHasKey(FailoverService::COMPONENT_APPLICATION, $result['components']);
    }

    /**
     * Test database health check
     */
    public function test_database_health_check(): void
    {
        $result = $this->service->checkDatabaseHealth();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertContains($result['status'], [
            FailoverService::HEALTH_HEALTHY,
            FailoverService::HEALTH_DEGRADED,
            FailoverService::HEALTH_CRITICAL,
            FailoverService::HEALTH_FAILED,
        ]);
    }

    /**
     * Test Redis health check
     */
    public function test_redis_health_check(): void
    {
        $result = $this->service->checkRedisHealth();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test storage health check
     */
    public function test_storage_health_check(): void
    {
        $result = $this->service->checkStorageHealth();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);

        if ($result['status'] !== FailoverService::HEALTH_FAILED) {
            $this->assertArrayHasKey('disk', $result);
            $this->assertArrayHasKey('writable', $result);
            $this->assertTrue($result['writable']);
        }
    }

    /**
     * Test queue health check
     */
    public function test_queue_health_check(): void
    {
        $result = $this->service->checkQueueHealth();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test application health check
     */
    public function test_application_health_check(): void
    {
        $result = $this->service->checkApplicationHealth();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertArrayHasKey('maintenance_mode', $result);
        $this->assertFalse($result['maintenance_mode']);
    }

    /**
     * Test failover test (dry run)
     */
    public function test_failover_test_dry_run(): void
    {
        $user = User::factory()->create();

        $result = $this->service->runFailoverTest($user->id);

        $this->assertNotNull($result['test_id']);
        $this->assertArrayHasKey('tests', $result);
        $this->assertArrayHasKey('component_health', $result['tests']);
        $this->assertArrayHasKey('dr_readiness', $result['tests']);
        $this->assertArrayHasKey('procedure_simulation', $result['tests']);
        $this->assertArrayHasKey('rto_estimate', $result['tests']);
        $this->assertArrayHasKey('communication', $result['tests']);
        $this->assertContains($result['overall_result'], ['LULUS', 'GAGAL']);

        // Verify event was logged
        $event = FailoverEvent::where('event_id', $result['test_id'])->first();
        $this->assertNotNull($event);
        $this->assertEquals('test', $event->type);
    }

    /**
     * Test automated failover trigger
     */
    public function test_automated_failover_trigger(): void
    {
        $user = User::factory()->create();

        $result = $this->service->triggerAutomatedFailover('Test failover', $user->id);

        $this->assertNotNull($result['failover_id']);
        $this->assertArrayHasKey('steps', $result);
        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('duration_seconds', $result);

        // Verify event was logged
        $event = FailoverEvent::where('event_id', $result['failover_id'])->first();
        $this->assertNotNull($event);
        $this->assertEquals('automated', $event->type);
        $this->assertEquals($user->id, $event->triggered_by);
    }

    /**
     * Test RTO constant is 4 hours per PKS 29.1
     */
    public function test_rto_target_is_4_hours(): void
    {
        $this->assertEquals(4, FailoverService::RTO_HOURS);
    }

    /**
     * Test RPO constant is 24 hours per PKS 29.1
     */
    public function test_rpo_target_is_24_hours(): void
    {
        $this->assertEquals(24, FailoverService::RPO_HOURS);
    }

    /**
     * Test failover event types in Bahasa Melayu
     */
    public function test_failover_event_types_bahasa_melayu(): void
    {
        $types = FailoverEvent::getTypes();

        $this->assertArrayHasKey('automated', $types);
        $this->assertEquals('Automatik', $types['automated']);
        $this->assertEquals('Manual', $types['manual']);
        $this->assertEquals('Ujian', $types['test']);
    }

    /**
     * Test failover event statuses in Bahasa Melayu
     */
    public function test_failover_event_statuses_bahasa_melayu(): void
    {
        $statuses = FailoverEvent::getStatuses();

        $this->assertArrayHasKey('completed', $statuses);
        $this->assertEquals('Selesai', $statuses['completed']);
        $this->assertEquals('Gagal', $statuses['failed']);
        $this->assertEquals('Sedang Berjalan', $statuses['in_progress']);
    }

    /**
     * Test health check creates log entry
     */
    public function test_health_check_creates_log_entry(): void
    {
        $initialCount = \App\Models\DisasterRecoveryLog::count();

        $this->service->checkAllComponentsHealth();

        $this->assertGreaterThan($initialCount, \App\Models\DisasterRecoveryLog::count());
    }

    /**
     * Test consecutive failures threshold
     */
    public function test_consecutive_failures_threshold(): void
    {
        $this->assertEquals(3, FailoverService::CONSECUTIVE_FAILURES_THRESHOLD);
    }

    /**
     * Test failover steps are executed in order
     */
    public function test_failover_steps_executed_in_order(): void
    {
        $user = User::factory()->create();
        $result = $this->service->triggerAutomatedFailover('Test', $user->id);

        $steps = $result['steps'];

        $this->assertCount(6, $steps);
        $this->assertEquals(1, $steps[0]['step']);
        $this->assertEquals(6, $steps[5]['step']);
    }
}
