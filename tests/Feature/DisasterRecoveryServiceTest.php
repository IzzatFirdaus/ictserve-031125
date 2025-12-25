<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\DisasterRecoveryLog;
use App\Models\User;
use App\Services\DisasterRecoveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Disaster Recovery Service Test
 *
 * PKS Business Continuity (Requirement 29) - Tests for DR procedures
 *
 * @see D03-FR-029 (Business Continuity)
 *
 * @trace Requirements 29.2, 29.3, 29.4
 */
class DisasterRecoveryServiceTest extends TestCase
{
    use RefreshDatabase;

    private DisasterRecoveryService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(DisasterRecoveryService::class);
    }

    /**
     * Test DR health check returns expected structure
     */
    public function test_dr_health_check_returns_expected_structure(): void
    {
        $result = $this->service->checkDRHealth();

        $this->assertArrayHasKey('timestamp', $result);
        $this->assertArrayHasKey('dr_enabled', $result);
        $this->assertArrayHasKey('primary_host', $result);
        $this->assertArrayHasKey('overall_status', $result);
        $this->assertArrayHasKey('rto_hours', $result);
        $this->assertArrayHasKey('rpo_hours', $result);
    }

    /**
     * Test DR health check when DR is disabled
     */
    public function test_dr_health_check_when_disabled(): void
    {
        config(['dr.enabled' => false]);

        $service = new DisasterRecoveryService;
        $result = $service->checkDRHealth();

        $this->assertFalse($result['dr_enabled']);
        $this->assertEquals(DisasterRecoveryService::STATUS_UNKNOWN, $result['overall_status']);
        $this->assertStringContainsString('tidak diaktifkan', $result['message']);
    }

    /**
     * Test database replication check
     */
    public function test_database_replication_check(): void
    {
        $result = $this->service->checkDatabaseReplication();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
        $this->assertContains($result['status'], [
            DisasterRecoveryService::STATUS_HEALTHY,
            DisasterRecoveryService::STATUS_DEGRADED,
            DisasterRecoveryService::STATUS_FAILED,
            DisasterRecoveryService::STATUS_UNKNOWN,
        ]);
    }

    /**
     * Test Redis replication check
     */
    public function test_redis_replication_check(): void
    {
        $result = $this->service->checkRedisReplication();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test file replication check
     */
    public function test_file_replication_check(): void
    {
        $result = $this->service->checkFileReplication();

        $this->assertArrayHasKey('status', $result);
        $this->assertArrayHasKey('message', $result);
    }

    /**
     * Test failover initiation
     */
    public function test_failover_initiation(): void
    {
        $user = User::factory()->create();

        $result = $this->service->initiateFailover('Test failover', $user->id);

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['failover_id']);
        $this->assertEquals('initiated', $result['status']);
        $this->assertArrayHasKey('steps', $result);
        $this->assertCount(6, $result['steps']);

        // Verify log was created
        $log = DisasterRecoveryLog::where('event_id', $result['failover_id'])->first();
        $this->assertNotNull($log);
        $this->assertEquals('failover_initiated', $log->event_type);
        $this->assertEquals($user->id, $log->user_id);
    }

    /**
     * Test failover test (dry run)
     */
    public function test_failover_test_dry_run(): void
    {
        $user = User::factory()->create();

        $result = $this->service->testFailover($user->id);

        $this->assertNotNull($result['test_id']);
        $this->assertArrayHasKey('tests', $result);
        $this->assertArrayHasKey('connectivity', $result['tests']);
        $this->assertArrayHasKey('database_replication', $result['tests']);
        $this->assertArrayHasKey('redis_replication', $result['tests']);
        $this->assertArrayHasKey('file_sync', $result['tests']);
        $this->assertArrayHasKey('rto_estimate', $result['tests']);
        $this->assertContains($result['overall_result'], ['LULUS', 'GAGAL']);

        // Verify log was created
        $log = DisasterRecoveryLog::where('event_id', $result['test_id'])->first();
        $this->assertNotNull($log);
        $this->assertEquals('failover_test', $log->event_type);
    }

    /**
     * Test DR statistics
     */
    public function test_dr_statistics(): void
    {
        // Create some test logs
        DisasterRecoveryLog::create([
            'event_id' => 'HC_TEST_001',
            'event_type' => 'health_check',
            'status' => 'healthy',
        ]);

        DisasterRecoveryLog::create([
            'event_id' => 'FT_TEST_001',
            'event_type' => 'failover_test',
            'status' => 'passed',
        ]);

        $stats = $this->service->getDRStats(30);

        $this->assertArrayHasKey('period_days', $stats);
        $this->assertArrayHasKey('total_health_checks', $stats);
        $this->assertArrayHasKey('failover_tests', $stats);
        $this->assertArrayHasKey('failover_tests_passed', $stats);
        $this->assertArrayHasKey('actual_failovers', $stats);
        $this->assertArrayHasKey('current_health', $stats);

        $this->assertEquals(1, $stats['total_health_checks']);
        $this->assertEquals(1, $stats['failover_tests']);
        $this->assertEquals(1, $stats['failover_tests_passed']);
    }

    /**
     * Test RTO constant is 4 hours per PKS 29.1
     */
    public function test_rto_target_is_4_hours(): void
    {
        $this->assertEquals(4, DisasterRecoveryService::RTO_HOURS);
    }

    /**
     * Test RPO constant is 24 hours per PKS 29.1
     */
    public function test_rpo_target_is_24_hours(): void
    {
        $this->assertEquals(24, DisasterRecoveryService::RPO_HOURS);
    }

    /**
     * Test DR log event types in Bahasa Melayu
     */
    public function test_dr_log_event_types_bahasa_melayu(): void
    {
        $types = DisasterRecoveryLog::getEventTypes();

        $this->assertArrayHasKey('health_check', $types);
        $this->assertEquals('Semakan Kesihatan', $types['health_check']);
        $this->assertEquals('Ujian Failover', $types['failover_test']);
        $this->assertEquals('Failover Dimulakan', $types['failover_initiated']);
    }

    /**
     * Test DR log statuses in Bahasa Melayu
     */
    public function test_dr_log_statuses_bahasa_melayu(): void
    {
        $statuses = DisasterRecoveryLog::getStatuses();

        $this->assertArrayHasKey('healthy', $statuses);
        $this->assertEquals('Sihat', $statuses['healthy']);
        $this->assertEquals('Merosot', $statuses['degraded']);
        $this->assertEquals('Gagal', $statuses['failed']);
        $this->assertEquals('Lulus', $statuses['passed']);
    }

    /**
     * Test health check creates log entry
     */
    public function test_health_check_creates_log_entry(): void
    {
        config(['dr.enabled' => true]);

        $initialCount = DisasterRecoveryLog::count();

        $service = new DisasterRecoveryService;
        $service->checkDRHealth();

        $this->assertEquals($initialCount + 1, DisasterRecoveryLog::count());

        $log = DisasterRecoveryLog::latest()->first();
        $this->assertEquals('health_check', $log->event_type);
    }

    /**
     * Test failover steps are in correct order
     */
    public function test_failover_steps_order(): void
    {
        $user = User::factory()->create();
        $result = $this->service->initiateFailover('Test', $user->id);

        $steps = $result['steps'];

        $this->assertEquals(1, $steps[0]['step']);
        $this->assertEquals('Verify DR site health', $steps[0]['action']);

        $this->assertEquals(6, $steps[5]['step']);
        $this->assertEquals('Notify stakeholders', $steps[5]['action']);
    }
}
