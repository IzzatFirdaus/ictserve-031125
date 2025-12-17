<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use App\Notifications\PerformanceThresholdBreached;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

/**
 * Performance Alert Service for ICTServe v3.6.0
 *
 * Monitors Pulse metrics and triggers notifications when performance
 * thresholds are breached.
 *
 * @trace Requirements 16.3 - Automated alerting for performance threshold breaches
 * @trace Requirements 4.1, 4.2, 14.1, 14.2, 14.5
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class PerformanceAlertService
{
    /**
     * Performance threshold constants.
     */
    private const RESPONSE_TIME_THRESHOLD_MS = 2000;

    private const DATABASE_QUERY_THRESHOLD_MS = 500;

    private const QUEUE_JOB_FAILURE_THRESHOLD = 5;

    private const CACHE_MISS_RATE_THRESHOLD = 0.3; // 30%

    private const ALERT_COOLDOWN_MINUTES = 15;

    /**
     * Check all performance metrics and trigger alerts if needed.
     */
    public function checkPerformanceMetrics(): void
    {
        $this->checkSlowRequests();
        $this->checkSlowQueries();
        $this->checkQueueJobFailures();
        $this->checkCachePerformance();
        $this->checkTicketProcessingMetrics();
        $this->checkLoanApprovalMetrics();
        $this->checkAssetAvailabilityMetrics();
    }

    /**
     * Check for slow HTTP requests.
     */
    protected function checkSlowRequests(): void
    {
        $slowRequests = $this->getSlowRequestCount();

        if ($slowRequests > 10 && $this->canSendAlert('slow_requests')) {
            $this->sendAlert(
                'Permintaan HTTP Perlahan Dikesan',
                "Terdapat {$slowRequests} permintaan HTTP yang melebihi ".self::RESPONSE_TIME_THRESHOLD_MS.'ms dalam 1 jam terakhir.',
                'warning',
                [
                    'metric' => 'slow_requests',
                    'count' => $slowRequests,
                    'threshold_ms' => self::RESPONSE_TIME_THRESHOLD_MS,
                ]
            );
        }
    }

    /**
     * Check for slow database queries.
     */
    protected function checkSlowQueries(): void
    {
        $slowQueries = $this->getSlowQueryCount();

        if ($slowQueries > 20 && $this->canSendAlert('slow_queries')) {
            $this->sendAlert(
                'Pertanyaan Pangkalan Data Perlahan Dikesan',
                "Terdapat {$slowQueries} pertanyaan pangkalan data yang melebihi ".self::DATABASE_QUERY_THRESHOLD_MS.'ms dalam 1 jam terakhir.',
                'warning',
                [
                    'metric' => 'slow_queries',
                    'count' => $slowQueries,
                    'threshold_ms' => self::DATABASE_QUERY_THRESHOLD_MS,
                ]
            );
        }
    }

    /**
     * Check for queue job failures.
     */
    protected function checkQueueJobFailures(): void
    {
        $failedJobs = $this->getFailedJobCount();

        if ($failedJobs >= self::QUEUE_JOB_FAILURE_THRESHOLD && $this->canSendAlert('queue_failures')) {
            $this->sendAlert(
                'Kegagalan Kerja Baris Gilir Dikesan',
                "Terdapat {$failedJobs} kerja baris gilir yang gagal dalam 1 jam terakhir.",
                'error',
                [
                    'metric' => 'queue_failures',
                    'count' => $failedJobs,
                    'threshold' => self::QUEUE_JOB_FAILURE_THRESHOLD,
                ]
            );
        }
    }

    /**
     * Check cache performance.
     */
    protected function checkCachePerformance(): void
    {
        $missRate = $this->getCacheMissRate();

        if ($missRate > self::CACHE_MISS_RATE_THRESHOLD && $this->canSendAlert('cache_miss_rate')) {
            $missRatePercent = round($missRate * 100, 1);
            $this->sendAlert(
                'Kadar Cache Miss Tinggi Dikesan',
                "Kadar cache miss adalah {$missRatePercent}%, melebihi ambang ".(self::CACHE_MISS_RATE_THRESHOLD * 100).'%.',
                'warning',
                [
                    'metric' => 'cache_miss_rate',
                    'rate' => $missRate,
                    'threshold' => self::CACHE_MISS_RATE_THRESHOLD,
                ]
            );
        }
    }

    /**
     * Check ticket processing metrics.
     */
    protected function checkTicketProcessingMetrics(): void
    {
        $avgResolutionTime = $this->getAverageTicketResolutionTime();
        $slaBreachCount = $this->getSLABreachCount();

        // Alert if average resolution time exceeds 24 hours
        if ($avgResolutionTime > 1440 && $this->canSendAlert('ticket_resolution_time')) {
            $hours = round($avgResolutionTime / 60, 1);
            $this->sendAlert(
                'Masa Penyelesaian Tiket Tinggi',
                "Purata masa penyelesaian tiket adalah {$hours} jam, melebihi sasaran 24 jam.",
                'warning',
                [
                    'metric' => 'ticket_resolution_time',
                    'avg_minutes' => $avgResolutionTime,
                ]
            );
        }

        // Alert if SLA breaches exceed threshold
        if ($slaBreachCount > 5 && $this->canSendAlert('sla_breaches')) {
            $this->sendAlert(
                'Pelanggaran SLA Dikesan',
                "Terdapat {$slaBreachCount} pelanggaran SLA dalam 24 jam terakhir.",
                'error',
                [
                    'metric' => 'sla_breaches',
                    'count' => $slaBreachCount,
                ]
            );
        }
    }

    /**
     * Check loan approval metrics.
     */
    protected function checkLoanApprovalMetrics(): void
    {
        $avgApprovalTime = $this->getAverageLoanApprovalTime();
        $pendingCount = $this->getPendingLoanCount();

        // Alert if average approval time exceeds 48 hours
        if ($avgApprovalTime > 48 && $this->canSendAlert('loan_approval_time')) {
            $this->sendAlert(
                'Masa Kelulusan Pinjaman Tinggi',
                "Purata masa kelulusan pinjaman adalah {$avgApprovalTime} jam, melebihi sasaran 48 jam.",
                'warning',
                [
                    'metric' => 'loan_approval_time',
                    'avg_hours' => $avgApprovalTime,
                ]
            );
        }

        // Alert if pending loans exceed threshold
        if ($pendingCount > 20 && $this->canSendAlert('pending_loans')) {
            $this->sendAlert(
                'Permohonan Pinjaman Tertunggak',
                "Terdapat {$pendingCount} permohonan pinjaman yang menunggu kelulusan.",
                'warning',
                [
                    'metric' => 'pending_loans',
                    'count' => $pendingCount,
                ]
            );
        }
    }

    /**
     * Check asset availability metrics.
     */
    protected function checkAssetAvailabilityMetrics(): void
    {
        $avgCheckLatency = $this->getAverageAssetCheckLatency();
        $overdueCount = $this->getOverdueAssetCount();

        // Alert if availability check latency exceeds 500ms
        if ($avgCheckLatency > 500 && $this->canSendAlert('asset_check_latency')) {
            $this->sendAlert(
                'Latensi Semakan Aset Tinggi',
                "Purata latensi semakan ketersediaan aset adalah {$avgCheckLatency}ms, melebihi sasaran 500ms.",
                'warning',
                [
                    'metric' => 'asset_check_latency',
                    'avg_ms' => $avgCheckLatency,
                ]
            );
        }

        // Alert if overdue assets exceed threshold
        if ($overdueCount > 10 && $this->canSendAlert('overdue_assets')) {
            $this->sendAlert(
                'Aset Tertunggak Dikesan',
                "Terdapat {$overdueCount} aset yang belum dipulangkan melebihi tarikh akhir.",
                'error',
                [
                    'metric' => 'overdue_assets',
                    'count' => $overdueCount,
                ]
            );
        }
    }

    /**
     * Send performance alert to admin users.
     */
    protected function sendAlert(string $title, string $message, string $severity, array $data = []): void
    {
        // Log the alert
        Log::channel('performance')->{$severity}($title, [
            'message' => $message,
            'data' => $data,
        ]);

        // Get admin and superuser users to notify
        $admins = User::whereIn('role', ['admin', 'superuser'])->get();

        if ($admins->isNotEmpty()) {
            Notification::send($admins, new PerformanceThresholdBreached(
                $title,
                $message,
                $severity,
                $data
            ));
        }

        // Set cooldown to prevent alert spam
        $this->setAlertCooldown($data['metric'] ?? 'unknown');
    }

    /**
     * Check if we can send an alert (cooldown check).
     */
    protected function canSendAlert(string $metric): bool
    {
        $cacheKey = "performance_alert_cooldown:{$metric}";

        return ! Cache::has($cacheKey);
    }

    /**
     * Set alert cooldown.
     */
    protected function setAlertCooldown(string $metric): void
    {
        $cacheKey = "performance_alert_cooldown:{$metric}";
        Cache::put($cacheKey, true, now()->addMinutes(self::ALERT_COOLDOWN_MINUTES));
    }

    /**
     * Get slow request count from Pulse data.
     */
    protected function getSlowRequestCount(): int
    {
        try {
            return (int) DB::table('pulse_entries')
                ->where('type', 'slow_request')
                ->where('timestamp', '>=', now()->subHour()->timestamp)
                ->count();
        } catch (\Exception $e) {
            Log::warning('Failed to get slow request count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get slow query count from Pulse data.
     */
    protected function getSlowQueryCount(): int
    {
        try {
            return (int) DB::table('pulse_entries')
                ->where('type', 'slow_query')
                ->where('timestamp', '>=', now()->subHour()->timestamp)
                ->count();
        } catch (\Exception $e) {
            Log::warning('Failed to get slow query count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get failed job count.
     */
    protected function getFailedJobCount(): int
    {
        try {
            return (int) DB::table('failed_jobs')
                ->where('failed_at', '>=', now()->subHour())
                ->count();
        } catch (\Exception $e) {
            Log::warning('Failed to get failed job count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get cache miss rate.
     */
    protected function getCacheMissRate(): float
    {
        try {
            $hits = (int) DB::table('pulse_aggregates')
                ->where('type', 'cache_hit')
                ->where('bucket', '>=', now()->subHour()->timestamp)
                ->sum('value');

            $misses = (int) DB::table('pulse_aggregates')
                ->where('type', 'cache_miss')
                ->where('bucket', '>=', now()->subHour()->timestamp)
                ->sum('value');

            $total = $hits + $misses;

            return $total > 0 ? $misses / $total : 0.0;
        } catch (\Exception $e) {
            Log::warning('Failed to get cache miss rate', ['error' => $e->getMessage()]);

            return 0.0;
        }
    }

    /**
     * Get average ticket resolution time in minutes.
     */
    protected function getAverageTicketResolutionTime(): float
    {
        try {
            return (float) DB::table('pulse_aggregates')
                ->where('type', 'ticket_resolution_time')
                ->where('bucket', '>=', now()->subDay()->timestamp)
                ->avg('value') ?? 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get ticket resolution time', ['error' => $e->getMessage()]);

            return 0.0;
        }
    }

    /**
     * Get SLA breach count.
     */
    protected function getSLABreachCount(): int
    {
        try {
            return (int) DB::table('pulse_aggregates')
                ->where('type', 'ticket_sla_status')
                ->where('key', 'breached')
                ->where('bucket', '>=', now()->subDay()->timestamp)
                ->sum('value');
        } catch (\Exception $e) {
            Log::warning('Failed to get SLA breach count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get average loan approval time in hours.
     */
    protected function getAverageLoanApprovalTime(): float
    {
        try {
            return (float) DB::table('pulse_aggregates')
                ->where('type', 'loan_approval_duration')
                ->where('bucket', '>=', now()->subDay()->timestamp)
                ->avg('value') ?? 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get loan approval time', ['error' => $e->getMessage()]);

            return 0.0;
        }
    }

    /**
     * Get pending loan count.
     */
    protected function getPendingLoanCount(): int
    {
        try {
            return (int) DB::table('loan_applications')
                ->whereIn('status', ['pending_approval', 'pending_support', 'under_review'])
                ->count();
        } catch (\Exception $e) {
            Log::warning('Failed to get pending loan count', ['error' => $e->getMessage()]);

            return 0;
        }
    }

    /**
     * Get average asset availability check latency in ms.
     */
    protected function getAverageAssetCheckLatency(): float
    {
        try {
            return (float) DB::table('pulse_aggregates')
                ->where('type', 'asset_availability_latency')
                ->where('bucket', '>=', now()->subHour()->timestamp)
                ->avg('value') ?? 0;
        } catch (\Exception $e) {
            Log::warning('Failed to get asset check latency', ['error' => $e->getMessage()]);

            return 0.0;
        }
    }

    /**
     * Get overdue asset count.
     *
     * Counts loans that are either:
     * - Already marked as OVERDUE status
     * - Still in active/in_use/issued status but past their loan_end_date
     */
    protected function getOverdueAssetCount(): int
    {
        try {
            return (int) DB::table('loan_applications')
                ->where(function ($query) {
                    // Loans explicitly marked as overdue
                    $query->where('status', 'overdue')
                        // OR loans still active but past end date
                        ->orWhere(function ($q) {
                            $q->whereIn('status', ['active', 'in_use', 'issued'])
                                ->where('loan_end_date', '<', now()->toDateString());
                        });
                })
                ->count();
        } catch (\Exception $e) {
            Log::warning('Failed to get overdue asset count', ['error' => $e->getMessage()]);

            return 0;
        }
    }
}
