<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\EmailLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Email Notification Service
 *
 * Manages email notification tracking, delivery status monitoring, and retry mechanisms.
 * Provides comprehensive email analytics and SLA compliance tracking.
 *
 * Requirements: 17.1, 17.2, D03-FR-014.1, D03-FR-014.2
 *
 * @see D04 §12.1 Email notification management
 */
class EmailNotificationService
{
    private const CACHE_TTL = 300; // 5 minutes

    private const SLA_TARGET_SECONDS = 60;

    private const MAX_RETRY_ATTEMPTS = 3;

    /**
     * Get email notification dashboard statistics
     *
     * @return array<string, mixed>
     */
    public function getDashboardStats(): array
    {
        return Cache::remember('email_notification_stats', self::CACHE_TTL, function () {
            $today = Carbon::today();
            $thisWeek = Carbon::now()->startOfWeek();
            $thisMonth = Carbon::now()->startOfMonth();

            return [
                'total_sent_today' => EmailLog::whereDate('created_at', $today)->count(),
                'total_sent_week' => EmailLog::where('created_at', '>=', $thisWeek)->count(),
                'total_sent_month' => EmailLog::where('created_at', '>=', $thisMonth)->count(),
                'failed_today' => EmailLog::whereDate('created_at', $today)
                    ->where('status', 'failed')->count(),
                'pending_delivery' => EmailLog::where('status', 'pending')->count(),
                'retry_queue' => EmailLog::where('status', 'failed')
                    ->where('retry_attempts', '<', self::MAX_RETRY_ATTEMPTS)->count(),
                'sla_compliance_rate' => $this->calculateSLACompliance(),
                'average_delivery_time' => $this->getAverageDeliveryTime(),
            ];
        });
    }

    /**
     * Get email delivery statistics by type
     *
     * @return Collection<int, array<string, mixed>>
     */
    public function getDeliveryStatsByType(): Collection
    {
        return Cache::remember('email_delivery_stats_by_type', self::CACHE_TTL, function () {
            return EmailLog::select('email_type')
                ->selectRaw('COUNT(*) as total_sent')
                ->selectRaw('SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered')
                ->selectRaw('SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed')
                ->selectRaw('SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending')
                ->selectRaw('AVG(CASE WHEN delivered_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, created_at, delivered_at) END) as avg_delivery_time')
                ->where('created_at', '>=', Carbon::now()->subDays(30))
                ->groupBy('email_type')
                ->get();
        });
    }

    /**
     * Get failed emails with retry eligibility
     *
     * @return Collection<int, EmailLog>
     */
    public function getFailedEmailsForRetry(): Collection
    {
        return EmailLog::where('status', 'failed')
            ->where('retry_attempts', '<', self::MAX_RETRY_ATTEMPTS)
            ->where('created_at', '>=', Carbon::now()->subDays(7))
            ->with(['user'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Retry failed email delivery
     */
    public function retryEmailDelivery(EmailLog $emailLog): bool
    {
        if ($emailLog->retry_attempts >= self::MAX_RETRY_ATTEMPTS) {
            Log::warning('Email retry limit exceeded', [
                'email_log_id' => $emailLog->id,
                'retry_attempts' => $emailLog->retry_attempts,
            ]);

            return false;
        }

        try {
            // Increment retry attempts
            $emailLog->increment('retry_attempts');
            $emailLog->update([
                'status' => 'pending',
                'last_retry_at' => now(),
                'error_message' => null,
            ]);

            // Re-queue the email job
            $this->requeueEmail($emailLog);

            Log::info('Email queued for retry', [
                'email_log_id' => $emailLog->id,
                'retry_attempt' => $emailLog->retry_attempts,
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to retry email delivery', [
                'email_log_id' => $emailLog->id,
                'error' => $e->getMessage(),
            ]);

            $emailLog->update([
                'status' => 'failed',
                'error_message' => 'Retry failed: '.$e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Bulk retry failed emails
     *
     * @param  array<int>  $emailLogIds
     * @return array<string, int>
     */
    public function bulkRetryEmails(array $emailLogIds): array
    {
        $results = ['success' => 0, 'failed' => 0];

        $emailLogs = EmailLog::whereIn('id', $emailLogIds)
            ->where('status', 'failed')
            ->where('retry_attempts', '<', self::MAX_RETRY_ATTEMPTS)
            ->get();

        foreach ($emailLogs as $emailLog) {
            if ($this->retryEmailDelivery($emailLog)) {
                $results['success']++;
            } else {
                $results['failed']++;
            }
        }

        // Clear cache after bulk operation
        $this->clearCache();

        return $results;
    }

    /**
     * Get email delivery trends for the last 30 days
     *
     * @return array<string, array<string, mixed>>
     */
    public function getDeliveryTrends(): array
    {
        return Cache::remember('email_delivery_trends', self::CACHE_TTL, function () {
            $trends = [];
            $startDate = Carbon::now()->subDays(30);

            for ($i = 0; $i < 30; $i++) {
                $date = $startDate->copy()->addDays($i);
                $dateKey = $date->format('Y-m-d');

                $dayStats = EmailLog::whereDate('created_at', $date)
                    ->selectRaw('
                        COUNT(*) as total,
                        SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                        SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed,
                        AVG(CASE WHEN delivered_at IS NOT NULL THEN TIMESTAMPDIFF(SECOND, created_at, delivered_at) END) as avg_delivery_time
                    ')
                    ->first();

                $trends[$dateKey] = [
                    'date' => $date->format('M j'),
                    'total' => $dayStats->total ?? 0,
                    'delivered' => $dayStats->delivered ?? 0,
                    'failed' => $dayStats->failed ?? 0,
                    'delivery_rate' => $dayStats->total > 0 ?
                        round(($dayStats->delivered / $dayStats->total) * 100, 1) : 0,
                    'avg_delivery_time' => round($dayStats->avg_delivery_time ?? 0, 1),
                ];
            }

            return $trends;
        });
    }

    /**
     * Calculate SLA compliance rate (60-second target)
     */
    private function calculateSLACompliance(): float
    {
        $totalDelivered = EmailLog::where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->count();

        if ($totalDelivered === 0) {
            return 100.0;
        }

        $withinSLA = EmailLog::where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->whereRaw('TIMESTAMPDIFF(SECOND, created_at, delivered_at) <= ?', [self::SLA_TARGET_SECONDS])
            ->count();

        return round(($withinSLA / $totalDelivered) * 100, 1);
    }

    /**
     * Get average delivery time in seconds
     */
    private function getAverageDeliveryTime(): float
    {
        $avgTime = EmailLog::where('status', 'delivered')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->selectRaw('AVG(TIMESTAMPDIFF(SECOND, created_at, delivered_at)) as avg_time')
            ->value('avg_time');

        return round($avgTime ?? 0, 1);
    }

    /**
     * Re-queue email for delivery
     */
    private function requeueEmail(EmailLog $emailLog): void
    {
        // Calculate exponential backoff delay
        $delay = min(pow(2, $emailLog->retry_attempts) * 60, 3600); // Max 1 hour

        // Dispatch appropriate email job based on type
        match ($emailLog->email_type) {
            'ticket_created' => \App\Jobs\SendTicketCreatedEmail::dispatch($emailLog->data)
                ->delay(now()->addSeconds($delay)),
            'loan_approved' => \App\Jobs\SendLoanApprovedEmail::dispatch($emailLog->data)
                ->delay(now()->addSeconds($delay)),
            'asset_overdue' => \App\Jobs\SendAssetOverdueEmail::dispatch($emailLog->data)
                ->delay(now()->addSeconds($delay)),
            default => Log::warning('Unknown email type for retry', [
                'email_type' => $emailLog->email_type,
                'email_log_id' => $emailLog->id,
            ]),
        };
    }

    /**
     * Clear email notification cache
     */
    public function clearCache(): void
    {
        Cache::forget('email_notification_stats');
        Cache::forget('email_delivery_stats_by_type');
        Cache::forget('email_delivery_trends');
    }

    /**
     * Get email template validation results
     *
     * @return array<string, mixed>
     */
    public function validateEmailTemplate(string $templateName): array
    {
        // Basic template validation
        $templatePath = resource_path("views/emails/{$templateName}.blade.php");

        if (! file_exists($templatePath)) {
            return [
                'valid' => false,
                'errors' => ['Template file not found'],
            ];
        }

        $content = file_get_contents($templatePath);
        $errors = [];

        // Check for required accessibility attributes
        if (! str_contains($content, 'lang=')) {
            $errors[] = 'Missing lang attribute for accessibility';
        }

        if (! str_contains($content, 'role=')) {
            $errors[] = 'Missing ARIA role attributes';
        }

        // Check for WCAG 2.2 AA compliance indicators
        if (! str_contains($content, 'color:') && ! str_contains($content, 'background-color:')) {
            $errors[] = 'No color styling detected - verify contrast ratios';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'template_path' => $templatePath,
            'last_modified' => filemtime($templatePath),
        ];
    }
}
