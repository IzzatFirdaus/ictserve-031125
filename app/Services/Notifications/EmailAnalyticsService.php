<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Email Analytics Service
 *
 * Provides comprehensive analytics and reporting for email delivery,
 * including open rates, click-through rates, bounce handling, and alerting.
 *
 * Features:
 * - Delivery rate metrics and reporting
 * - Bounce rate monitoring and handling
 * - Open rate and click-through tracking
 * - Alerting system for delivery failures
 * - Performance reports generation
 *
 * @see D03 SRS-FR-008
 * @see D04 §6.2
 *
 * @requirements 10.1, 10.3, 10.5
 *
 * @version 1.0.0
 *
 * @updated 2025-12-30
 */
class EmailAnalyticsService
{
    /**
     * Cache TTL for analytics data (in seconds).
     */
    private const CACHE_TTL = 300; // 5 minutes

    /**
     * Alert threshold for failure rate (percentage).
     */
    private const FAILURE_ALERT_THRESHOLD = 10.0;

    /**
     * Alert threshold for bounce rate (percentage).
     */
    private const BOUNCE_ALERT_THRESHOLD = 5.0;

    /**
     * Get comprehensive delivery metrics.
     *
     * @param  Carbon|null  $from  Start date
     * @param  Carbon|null  $to  End date
     * @param  bool  $useCache  Whether to use cached data
     * @return array<string, mixed>
     */
    public function getDeliveryMetrics(
        ?Carbon $from = null,
        ?Carbon $to = null,
        bool $useCache = true
    ): array {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        $cacheKey = "email_analytics:delivery:{$from->format('Y-m-d')}:{$to->format('Y-m-d')}";

        if ($useCache && Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $metrics = $this->calculateDeliveryMetrics($from, $to);

        if ($useCache) {
            Cache::put($cacheKey, $metrics, self::CACHE_TTL);
        }

        return $metrics;
    }

    /**
     * Calculate delivery metrics from database.
     *
     * @return array<string, mixed>
     */
    private function calculateDeliveryMetrics(Carbon $from, Carbon $to): array
    {
        $baseQuery = EmailLog::whereBetween('created_at', [$from, $to]);

        // Status counts
        $statusCounts = (clone $baseQuery)
            ->select('status', DB::raw('COUNT(*) as count'))
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        $total = array_sum($statusCounts);
        $queued = $statusCounts['queued'] ?? 0;
        $sent = $statusCounts['sent'] ?? 0;
        $delivered = $statusCounts['delivered'] ?? 0;
        $failed = $statusCounts['failed'] ?? 0;
        $permanentlyFailed = $statusCounts['permanently_failed'] ?? 0;
        $bounced = $statusCounts['bounced'] ?? 0;

        // Calculate rates
        $deliveryRate = $total > 0 ? round(($delivered / $total) * 100, 2) : 0.0;
        $failureRate = $total > 0 ? round((($failed + $permanentlyFailed) / $total) * 100, 2) : 0.0;
        $bounceRate = $total > 0 ? round(($bounced / $total) * 100, 2) : 0.0;

        // Average delivery time
        $avgDeliveryTime = (clone $baseQuery)
            ->where('status', 'delivered')
            ->whereNotNull('delivered_at')
            ->whereNotNull('queued_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, queued_at, delivered_at)) as avg_time')
            ->value('avg_time');

        // Retry statistics
        $retryStats = (clone $baseQuery)
            ->select(
                DB::raw('AVG(retry_attempts) as avg_retries'),
                DB::raw('MAX(retry_attempts) as max_retries'),
                DB::raw('SUM(CASE WHEN retry_attempts > 0 THEN 1 ELSE 0 END) as emails_with_retries')
            )
            ->first();

        return [
            'period' => [
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
            ],
            'totals' => [
                'total' => $total,
                'queued' => $queued,
                'sent' => $sent,
                'delivered' => $delivered,
                'failed' => $failed,
                'permanently_failed' => $permanentlyFailed,
                'bounced' => $bounced,
            ],
            'rates' => [
                'delivery_rate' => $deliveryRate,
                'failure_rate' => $failureRate,
                'bounce_rate' => $bounceRate,
            ],
            'performance' => [
                'avg_delivery_time_seconds' => $avgDeliveryTime !== null ? (float) $avgDeliveryTime : null,
                'avg_retries' => $retryStats?->avg_retries !== null ? round((float) $retryStats->avg_retries, 2) : 0,
                'max_retries' => (int) ($retryStats?->max_retries ?? 0),
                'emails_with_retries' => (int) ($retryStats?->emails_with_retries ?? 0),
            ],
        ];
    }

    /**
     * Get bounce rate metrics and handle bounced emails.
     *
     * @param  Carbon|null  $from  Start date
     * @param  Carbon|null  $to  End date
     * @return array<string, mixed>
     */
    public function getBounceMetrics(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        $baseQuery = EmailLog::whereBetween('created_at', [$from, $to]);

        // Get bounced emails
        $bouncedEmails = (clone $baseQuery)
            ->where('status', 'bounced')
            ->orWhere('final_status', 'bounced')
            ->select('recipient_email', DB::raw('COUNT(*) as bounce_count'))
            ->groupBy('recipient_email')
            ->orderByDesc('bounce_count')
            ->limit(50)
            ->get();

        // Total bounce count
        $totalBounces = (clone $baseQuery)
            ->where(function ($q): void {
                $q->where('status', 'bounced')
                    ->orWhere('final_status', 'bounced');
            })
            ->count();

        // Total emails sent
        $totalSent = (clone $baseQuery)->count();

        // Bounce rate
        $bounceRate = $totalSent > 0 ? round(($totalBounces / $totalSent) * 100, 2) : 0.0;

        // Bounce types breakdown
        $bounceTypes = (clone $baseQuery)
            ->where(function ($q): void {
                $q->where('status', 'bounced')
                    ->orWhere('final_status', 'bounced');
            })
            ->select(
                DB::raw("JSON_UNQUOTE(JSON_EXTRACT(meta, '$.bounce_type')) as bounce_type"),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('bounce_type')
            ->pluck('count', 'bounce_type')
            ->toArray();

        return [
            'period' => [
                'from' => $from->toIso8601String(),
                'to' => $to->toIso8601String(),
            ],
            'total_bounces' => $totalBounces,
            'total_sent' => $totalSent,
            'bounce_rate' => $bounceRate,
            'bounce_types' => $bounceTypes,
            'top_bounced_addresses' => $bouncedEmails->map(fn ($item) => [
                'email' => $item->recipient_email,
                'bounce_count' => $item->bounce_count,
            ])->toArray(),
            'alert_triggered' => $bounceRate >= self::BOUNCE_ALERT_THRESHOLD,
        ];
    }

    /**
     * Get metrics by notification type.
     *
     * @param  Carbon|null  $from  Start date
     * @param  Carbon|null  $to  End date
     * @return array<string, array<string, mixed>>
     */
    public function getMetricsByNotificationType(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        return EmailLog::whereBetween('created_at', [$from, $to])
            ->whereNotNull('notification_type')
            ->select(
                'notification_type',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed"),
                DB::raw("SUM(CASE WHEN status = 'bounced' OR final_status = 'bounced' THEN 1 ELSE 0 END) as bounced"),
                DB::raw('AVG(retry_attempts) as avg_retries')
            )
            ->groupBy('notification_type')
            ->get()
            ->keyBy('notification_type')
            ->map(fn ($row) => [
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
                'bounced' => (int) $row->bounced,
                'delivery_rate' => $row->total > 0 ? round(($row->delivered / $row->total) * 100, 2) : 0.0,
                'avg_retries' => round((float) $row->avg_retries, 2),
            ])
            ->toArray();
    }

    /**
     * Get metrics by priority level.
     *
     * @param  Carbon|null  $from  Start date
     * @param  Carbon|null  $to  End date
     * @return array<string, array<string, mixed>>
     */
    public function getMetricsByPriority(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        return EmailLog::whereBetween('created_at', [$from, $to])
            ->select(
                'priority',
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed"),
                DB::raw('AVG(TIMESTAMPDIFF(SECOND, queued_at, COALESCE(delivered_at, sent_at))) as avg_processing_time')
            )
            ->groupBy('priority')
            ->get()
            ->keyBy('priority')
            ->map(fn ($row) => [
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
                'delivery_rate' => $row->total > 0 ? round(($row->delivered / $row->total) * 100, 2) : 0.0,
                'avg_processing_time_seconds' => $row->avg_processing_time !== null ? round((float) $row->avg_processing_time, 2) : null,
            ])
            ->toArray();
    }

    /**
     * Get daily breakdown of email metrics.
     *
     * @param  Carbon|null  $from  Start date
     * @param  Carbon|null  $to  End date
     * @return Collection<int, array<string, mixed>>
     */
    public function getDailyBreakdown(?Carbon $from = null, ?Carbon $to = null): Collection
    {
        $from = $from ?? now()->subDays(30);
        $to = $to ?? now();

        return EmailLog::whereBetween('created_at', [$from, $to])
            ->select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'queued' THEN 1 ELSE 0 END) as queued"),
                DB::raw("SUM(CASE WHEN status = 'sent' THEN 1 ELSE 0 END) as sent"),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed"),
                DB::raw("SUM(CASE WHEN status = 'bounced' OR final_status = 'bounced' THEN 1 ELSE 0 END) as bounced")
            )
            ->groupBy(DB::raw('DATE(created_at)'))
            ->orderBy('date')
            ->get()
            ->map(fn ($row) => [
                'date' => $row->date,
                'total' => (int) $row->total,
                'queued' => (int) $row->queued,
                'sent' => (int) $row->sent,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
                'bounced' => (int) $row->bounced,
                'delivery_rate' => $row->total > 0 ? round(($row->delivered / $row->total) * 100, 2) : 0.0,
            ]);
    }

    /**
     * Get hourly breakdown for a specific day.
     *
     * @param  Carbon|null  $date  The date to analyze (defaults to today)
     * @return Collection<int, array<string, mixed>>
     */
    public function getHourlyBreakdown(?Carbon $date = null): Collection
    {
        $date = $date ?? now();
        $startOfDay = $date->copy()->startOfDay();
        $endOfDay = $date->copy()->endOfDay();

        return EmailLog::whereBetween('created_at', [$startOfDay, $endOfDay])
            ->select(
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as total'),
                DB::raw("SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as delivered"),
                DB::raw("SUM(CASE WHEN status IN ('failed', 'permanently_failed') THEN 1 ELSE 0 END) as failed")
            )
            ->groupBy(DB::raw('HOUR(created_at)'))
            ->orderBy('hour')
            ->get()
            ->map(fn ($row) => [
                'hour' => (int) $row->hour,
                'hour_formatted' => sprintf('%02d:00', $row->hour),
                'total' => (int) $row->total,
                'delivered' => (int) $row->delivered,
                'failed' => (int) $row->failed,
            ]);
    }

    /**
     * Check for delivery failures and trigger alerts if threshold exceeded.
     *
     * @param  Carbon|null  $from  Start date
     * @param  Carbon|null  $to  End date
     * @return array{alert_triggered: bool, failure_rate: float, threshold: float, message: string|null}
     */
    public function checkDeliveryAlerts(?Carbon $from = null, ?Carbon $to = null): array
    {
        $from = $from ?? now()->subHours(1);
        $to = $to ?? now();

        $metrics = $this->calculateDeliveryMetrics($from, $to);
        $failureRate = $metrics['rates']['failure_rate'];

        $alertTriggered = $failureRate >= self::FAILURE_ALERT_THRESHOLD;

        if ($alertTriggered) {
            $message = sprintf(
                'Email delivery failure rate (%.2f%%) exceeded threshold (%.2f%%) in the last hour.',
                $failureRate,
                self::FAILURE_ALERT_THRESHOLD
            );

            Log::channel('notifications')->warning('Email delivery alert triggered', [
                'failure_rate' => $failureRate,
                'threshold' => self::FAILURE_ALERT_THRESHOLD,
                'period' => [
                    'from' => $from->toIso8601String(),
                    'to' => $to->toIso8601String(),
                ],
            ]);
        }

        return [
            'alert_triggered' => $alertTriggered,
            'failure_rate' => $failureRate,
            'threshold' => self::FAILURE_ALERT_THRESHOLD,
            'message' => $alertTriggered ? $message : null,
        ];
    }

    /**
     * Get queue health metrics.
     *
     * @return array<string, mixed>
     */
    public function getQueueHealth(): array
    {
        // Emails stuck in queued status for more than 5 minutes
        $stuckEmails = EmailLog::where('status', 'queued')
            ->where('queued_at', '<', now()->subMinutes(5))
            ->count();

        // Emails pending retry
        $pendingRetries = EmailLog::where('status', 'failed')
            ->whereNotNull('next_retry_at')
            ->where('next_retry_at', '>', now())
            ->count();

        // Average queue processing time (last hour)
        $avgProcessingTime = EmailLog::where('created_at', '>=', now()->subHour())
            ->whereNotNull('sent_at')
            ->whereNotNull('queued_at')
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, queued_at, sent_at)) as avg_time')
            ->value('avg_time');

        // Queue throughput (emails processed per minute in last hour)
        $throughput = EmailLog::where('sent_at', '>=', now()->subHour())
            ->count() / 60;

        return [
            'stuck_emails' => $stuckEmails,
            'pending_retries' => $pendingRetries,
            'avg_processing_time_seconds' => $avgProcessingTime !== null ? round((float) $avgProcessingTime, 2) : null,
            'throughput_per_minute' => round($throughput, 2),
            'health_status' => $this->determineQueueHealthStatus($stuckEmails, $avgProcessingTime),
        ];
    }

    /**
     * Determine queue health status based on metrics.
     */
    private function determineQueueHealthStatus(int $stuckEmails, ?float $avgProcessingTime): string
    {
        if ($stuckEmails > 100) {
            return 'critical';
        }

        if ($stuckEmails > 50 || ($avgProcessingTime !== null && $avgProcessingTime > 300)) {
            return 'warning';
        }

        if ($stuckEmails > 10 || ($avgProcessingTime !== null && $avgProcessingTime > 60)) {
            return 'degraded';
        }

        return 'healthy';
    }
}
