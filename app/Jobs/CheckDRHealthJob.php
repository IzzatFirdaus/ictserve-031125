<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Services\DisasterRecoveryService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Check DR Health Job
 *
 * PKS Business Continuity (Requirement 29) - Automated DR Health Monitoring
 *
 * Runs periodic health checks on disaster recovery components and
 * sends alerts when issues are detected.
 *
 * @trace D03-FR-029 (Business Continuity)
 * @trace Requirements 29.3, 29.4
 */
class CheckDRHealthJob implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * The number of seconds to wait before retrying the job.
     */
    public int $backoff = 60;

    /**
     * Execute the job.
     */
    public function handle(DisasterRecoveryService $drService): void
    {
        Log::info('Starting DR health check');

        $results = $drService->checkDRHealth();

        // Log the results
        Log::info('DR health check completed', [
            'overall_status' => $results['overall_status'],
            'dr_enabled' => $results['dr_enabled'],
        ]);

        // Send alerts if status is degraded or failed
        if (in_array($results['overall_status'], [
            DisasterRecoveryService::STATUS_DEGRADED,
            DisasterRecoveryService::STATUS_FAILED,
        ], true)) {
            $this->sendAlert($results);
        }
    }

    /**
     * Send alert for DR issues.
     *
     * @param  array<string, mixed>  $results
     */
    private function sendAlert(array $results): void
    {
        $recipients = config('dr.alert_recipients', []);

        if (empty($recipients)) {
            Log::warning('DR alert triggered but no recipients configured');

            return;
        }

        $severity = $results['overall_status'] === DisasterRecoveryService::STATUS_FAILED
            ? 'KRITIKAL'
            : 'AMARAN';

        $message = "[{$severity}] Status DR: {$results['message']}";

        Log::alert('DR health alert', [
            'severity' => $severity,
            'status' => $results['overall_status'],
            'components' => $results['components'] ?? [],
        ]);

        // In production, send actual notifications
        // Notification::route('mail', $recipients)->notify(new DRHealthAlert($results));
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('DR health check job failed', [
            'error' => $exception->getMessage(),
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
