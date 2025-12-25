<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\BackupService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Perform Backup Job
 *
 * PKS Business Continuity (Requirement 29) - Automated Backup Execution
 *
 * Scheduled job for automated backup procedures with:
 * - Daily full backups
 * - Hourly incremental backups
 * - Automatic verification
 * - Failure notifications
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.1
 */
class PerformBackupJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    public int $tries = 3;

    public int $backoff = 300; // 5 minutes

    public int $timeout = 3600; // 1 hour

    public function __construct(
        public string $backupType = 'full',
        public bool $verify = true
    ) {}

    public function handle(BackupService $backupService): void
    {
        Log::info('Starting backup job', ['type' => $this->backupType]);

        $result = match ($this->backupType) {
            'full' => $backupService->performFullBackup(),
            'incremental' => $backupService->performIncrementalBackup(),
            default => $backupService->performFullBackup(),
        };

        if (! $result['success']) {
            Log::error('Backup failed', [
                'type' => $this->backupType,
                'error' => $result['error'] ?? 'Unknown error',
            ]);

            // Notify administrators
            $this->notifyBackupFailure($result);

            throw new \RuntimeException('Backup failed: '.($result['error'] ?? 'Unknown error'));
        }

        Log::info('Backup completed successfully', [
            'backup_id' => $result['backup_id'],
            'type' => $this->backupType,
            'size_bytes' => $result['size_bytes'] ?? 0,
        ]);

        // Verify backup if requested
        if ($this->verify && isset($result['backup_id'])) {
            $verifyResult = $backupService->verifyBackup($result['backup_id']);

            if (! $verifyResult['success']) {
                Log::warning('Backup verification failed', [
                    'backup_id' => $result['backup_id'],
                    'verification_results' => $verifyResult['verification_results'] ?? [],
                ]);
            }
        }

        // Check RPO compliance
        $rpoStatus = $backupService->checkRPOCompliance();
        if (! $rpoStatus['compliant']) {
            Log::warning('RPO compliance warning', $rpoStatus);
        }
    }

    /**
     * Notify administrators of backup failure
     *
     * @param  array<string, mixed>  $result
     */
    private function notifyBackupFailure(array $result): void
    {
        // In production, send notification to admins
        Log::critical('BACKUP FAILURE ALERT', [
            'type' => $this->backupType,
            'error' => $result['error'] ?? 'Unknown error',
            'timestamp' => now()->toIso8601String(),
        ]);
    }

    /**
     * Handle job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('Backup job failed permanently', [
            'type' => $this->backupType,
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }

    /**
     * Get unique job ID for deduplication
     */
    public function uniqueId(): string
    {
        return 'backup_'.$this->backupType.'_'.date('Y-m-d-H');
    }
}
