<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\BackupLog;
use App\Services\BackupService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Backup Service Test
 *
 * PKS Business Continuity (Requirement 29) - Tests for backup procedures
 *
 * @see D03-FR-029 (Business Continuity)
 *
 * @trace Requirements 29.1, 29.3
 */
class BackupServiceTest extends TestCase
{
    use RefreshDatabase;

    private BackupService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(BackupService::class);
    }

    /**
     * Test full backup execution
     */
    public function test_can_perform_full_backup(): void
    {
        $result = $this->service->performFullBackup();

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['backup_id']);
        $this->assertEquals('full', $result['type']);
        $this->assertArrayHasKey('results', $result);
        $this->assertArrayHasKey('database', $result['results']);
        $this->assertArrayHasKey('files', $result['results']);
        $this->assertArrayHasKey('config', $result['results']);

        // Verify backup log was created
        $log = BackupLog::where('backup_id', $result['backup_id'])->first();
        $this->assertNotNull($log);
        $this->assertEquals('completed', $log->status);
    }

    /**
     * Test incremental backup execution
     */
    public function test_can_perform_incremental_backup(): void
    {
        // First create a full backup
        $fullResult = $this->service->performFullBackup();
        $this->assertTrue($fullResult['success']);

        // Then perform incremental
        $result = $this->service->performIncrementalBackup();

        $this->assertTrue($result['success']);
        $this->assertNotNull($result['backup_id']);
        $this->assertEquals('incremental', $result['type']);
    }

    /**
     * Test backup verification
     */
    public function test_can_verify_backup(): void
    {
        $backupResult = $this->service->performFullBackup();
        $this->assertTrue($backupResult['success']);

        $verifyResult = $this->service->verifyBackup($backupResult['backup_id']);

        $this->assertTrue($verifyResult['success']);
        $this->assertArrayHasKey('verification_results', $verifyResult);

        // Check log was updated to verified
        $log = BackupLog::where('backup_id', $backupResult['backup_id'])->first();
        $this->assertEquals('verified', $log->status);
        $this->assertNotNull($log->verified_at);
    }

    /**
     * Test RPO compliance check
     */
    public function test_rpo_compliance_check(): void
    {
        // No backups - should be non-compliant
        $result = $this->service->checkRPOCompliance();
        $this->assertFalse($result['compliant']);

        // Perform backup
        $this->service->performFullBackup();

        // Now should be compliant
        $result = $this->service->checkRPOCompliance();
        $this->assertTrue($result['compliant']);
        $this->assertEquals(BackupService::RPO_HOURS, $result['rpo_target_hours']);
    }

    /**
     * Test backup statistics
     */
    public function test_backup_statistics(): void
    {
        // Perform some backups
        $this->service->performFullBackup();
        $this->service->performIncrementalBackup();

        $stats = $this->service->getBackupStats(30);

        $this->assertEquals(2, $stats['total_backups']);
        $this->assertEquals(2, $stats['successful_backups']);
        $this->assertEquals(0, $stats['failed_backups']);
        $this->assertEquals(100, $stats['success_rate']);
        $this->assertArrayHasKey('by_type', $stats);
        $this->assertArrayHasKey('rpo_compliance', $stats);
    }

    /**
     * Test backup listing
     */
    public function test_list_backups(): void
    {
        $this->service->performFullBackup();
        $this->service->performIncrementalBackup();

        $backups = $this->service->listBackups();

        $this->assertCount(2, $backups);
        $this->assertEquals('incremental', $backups->first()->type); // Most recent first
    }

    /**
     * Test backup cleanup
     */
    public function test_backup_cleanup(): void
    {
        // Create an old backup log
        BackupLog::create([
            'backup_id' => 'OLD_TEST_123',
            'type' => 'full',
            'status' => 'completed',
            'started_at' => now()->subDays(60),
            'completed_at' => now()->subDays(60),
        ]);

        // Create a recent backup
        $this->service->performFullBackup();

        // Cleanup with 30-day retention
        $deletedCount = $this->service->cleanupOldBackups(30);

        $this->assertEquals(1, $deletedCount);
        $this->assertNull(BackupLog::where('backup_id', 'OLD_TEST_123')->first());
    }

    /**
     * Test backup ID generation is unique
     */
    public function test_backup_id_is_unique(): void
    {
        $result1 = $this->service->performFullBackup();
        $result2 = $this->service->performFullBackup();

        $this->assertNotEquals($result1['backup_id'], $result2['backup_id']);
    }

    /**
     * Test backup log types in Bahasa Melayu
     */
    public function test_backup_type_labels_bahasa_melayu(): void
    {
        $types = BackupLog::getTypes();

        $this->assertArrayHasKey('full', $types);
        $this->assertEquals('Sandaran Penuh', $types['full']);
        $this->assertEquals('Sandaran Tambahan', $types['incremental']);
    }

    /**
     * Test backup log statuses in Bahasa Melayu
     */
    public function test_backup_status_labels_bahasa_melayu(): void
    {
        $statuses = BackupLog::getStatuses();

        $this->assertArrayHasKey('completed', $statuses);
        $this->assertEquals('Selesai', $statuses['completed']);
        $this->assertEquals('Gagal', $statuses['failed']);
        $this->assertEquals('Disahkan', $statuses['verified']);
    }

    /**
     * Test RTO constant is 4 hours per PKS 29.1
     */
    public function test_rto_target_is_4_hours(): void
    {
        $this->assertEquals(4, BackupService::RTO_HOURS);
    }

    /**
     * Test RPO constant is 24 hours per PKS 29.1
     */
    public function test_rpo_target_is_24_hours(): void
    {
        $this->assertEquals(24, BackupService::RPO_HOURS);
    }
}
