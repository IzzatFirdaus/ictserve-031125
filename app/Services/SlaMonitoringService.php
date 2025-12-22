<?php

declare(strict_types=1);

namespace App\Services;

use App\Enums\LoanStatus;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SLA Monitoring Service
 *
 * Monitors Service Level Agreement compliance for loan applications.
 * Tracks approval times and sends alerts when SLAs are at risk of being breached.
 *
 * @see D03-FR-023.7 (SLA monitoring and alerts)
 * @see D04 §6.6 (Approval Interface Component)
 *
 * SLA Thresholds (in business hours):
 * - Warning: 24 hours (1 business day)
 * - Critical: 48 hours (2 business days)
 * - Breach: 72 hours (3 business days)
 */
class SlaMonitoringService
{
    /**
     * SLA thresholds in business hours
     */
    public const SLA_WARNING_HOURS = 24;

    public const SLA_CRITICAL_HOURS = 48;

    public const SLA_BREACH_HOURS = 72;

    /**
     * Business hours configuration
     */
    public const BUSINESS_START_HOUR = 8;

    public const BUSINESS_END_HOUR = 18;

    /**
     * Get SLA status for a loan application
     *
     * @return array{status: string, hours_elapsed: float, hours_remaining: float|null, percentage: float}
     */
    public function getSlaStatus(LoanApplication $application): array
    {
        // Only calculate SLA for pending applications
        if ($application->status !== LoanStatus::UNDER_REVIEW) {
            return [
                'status' => 'completed',
                'hours_elapsed' => 0.0,
                'hours_remaining' => null,
                'percentage' => 100.0,
            ];
        }

        $createdAt = $application->created_at;
        if ($createdAt === null) {
            return [
                'status' => 'ok',
                'hours_elapsed' => 0.0,
                'hours_remaining' => (float) self::SLA_BREACH_HOURS,
                'percentage' => 0.0,
            ];
        }

        $hoursElapsed = $this->calculateBusinessHours($createdAt, now());
        $hoursRemaining = max(0, self::SLA_BREACH_HOURS - $hoursElapsed);
        $percentage = min(100, ($hoursElapsed / self::SLA_BREACH_HOURS) * 100);

        $status = match (true) {
            $hoursElapsed >= self::SLA_BREACH_HOURS => 'breached',
            $hoursElapsed >= self::SLA_CRITICAL_HOURS => 'critical',
            $hoursElapsed >= self::SLA_WARNING_HOURS => 'warning',
            default => 'ok',
        };

        return [
            'status' => $status,
            'hours_elapsed' => round($hoursElapsed, 1),
            'hours_remaining' => round($hoursRemaining, 1),
            'percentage' => round($percentage, 1),
        ];
    }

    /**
     * Get SLA badge color class based on status
     */
    public function getSlaColorClass(string $status): string
    {
        return match ($status) {
            'breached' => 'bg-red-100 text-red-800 border-red-200',
            'critical' => 'bg-orange-100 text-orange-800 border-orange-200',
            'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'ok' => 'bg-green-100 text-green-800 border-green-200',
            default => 'bg-gray-100 text-gray-800 border-gray-200',
        };
    }

    /**
     * Get SLA icon based on status
     */
    public function getSlaIcon(string $status): string
    {
        return match ($status) {
            'breached' => 'heroicon-o-exclamation-circle',
            'critical' => 'heroicon-o-exclamation-triangle',
            'warning' => 'heroicon-o-clock',
            'ok' => 'heroicon-o-check-circle',
            default => 'heroicon-o-question-mark-circle',
        };
    }

    /**
     * Get all applications at risk of SLA breach
     *
     * @return Collection<int, LoanApplication>
     */
    public function getApplicationsAtRisk(): Collection
    {
        return LoanApplication::query()
            ->where('status', LoanStatus::UNDER_REVIEW)
            ->get()
            ->filter(function (LoanApplication $app) {
                $sla = $this->getSlaStatus($app);

                return \in_array($sla['status'], ['warning', 'critical', 'breached'], true);
            });
    }

    /**
     * Get SLA summary statistics for dashboard
     *
     * @return array{total_pending: int, ok: int, warning: int, critical: int, breached: int, compliance_rate: float}
     */
    public function getSlaSummary(): array
    {
        $pendingApplications = LoanApplication::query()
            ->where('status', LoanStatus::UNDER_REVIEW)
            ->get();

        $totalPending = $pendingApplications->count();
        $ok = 0;
        $warning = 0;
        $critical = 0;
        $breached = 0;

        foreach ($pendingApplications as $application) {
            $sla = $this->getSlaStatus($application);
            match ($sla['status']) {
                'ok' => $ok++,
                'warning' => $warning++,
                'critical' => $critical++,
                'breached' => $breached++,
                default => null,
            };
        }

        // Calculate compliance rate (applications not breached)
        $compliant = $ok + $warning + $critical;
        $complianceRate = $totalPending > 0
            ? round(($compliant / $totalPending) * 100, 1)
            : 100.0;

        return [
            'total_pending' => $totalPending,
            'ok' => $ok,
            'warning' => $warning,
            'critical' => $critical,
            'breached' => $breached,
            'compliance_rate' => $complianceRate,
        ];
    }

    /**
     * Send SLA alerts for applications at risk
     *
     * @return array{sent: int, failed: int}
     */
    public function sendSlaAlerts(): array
    {
        $results = ['sent' => 0, 'failed' => 0];

        $applicationsAtRisk = $this->getApplicationsAtRisk();

        foreach ($applicationsAtRisk as $application) {
            try {
                $sla = $this->getSlaStatus($application);

                // Get approver
                $approver = $this->getApproverForApplication($application);

                if ($approver && $approver->email) {
                    // Check if we should send alert (avoid spam)
                    if ($this->shouldSendAlert($application, $sla['status'])) {
                        $this->sendAlertEmail($application, $approver, $sla);
                        $results['sent']++;

                        Log::info('SLA alert sent', [
                            'application_id' => $application->id,
                            'application_number' => $application->application_number,
                            'sla_status' => $sla['status'],
                            'approver_email' => $approver->email,
                        ]);
                    }
                }
            } catch (\Throwable $e) {
                $results['failed']++;
                Log::error('Failed to send SLA alert', [
                    'application_id' => $application->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return $results;
    }

    /**
     * Calculate business hours between two dates
     * Excludes weekends and non-business hours
     */
    public function calculateBusinessHours(Carbon $start, Carbon $end): float
    {
        $totalHours = 0.0;
        $current = $start->copy();

        while ($current->lt($end)) {
            // Skip weekends
            if ($current->isWeekend()) {
                $current->addDay()->startOfDay();

                continue;
            }

            // Calculate hours for this day
            $dayStart = $current->copy()->setTime(self::BUSINESS_START_HOUR, 0);
            $dayEnd = $current->copy()->setTime(self::BUSINESS_END_HOUR, 0);

            // Adjust start time if current is before business hours
            if ($current->lt($dayStart)) {
                $current = $dayStart->copy();
            }

            // Adjust end time if we're past business hours
            if ($current->gte($dayEnd)) {
                $current->addDay()->startOfDay();

                continue;
            }

            // Calculate end time for this day
            $effectiveEnd = $end->lt($dayEnd) && $end->isSameDay($current) ? $end : $dayEnd;

            // Only count if we're within business hours
            if ($current->lt($effectiveEnd) && $current->gte($dayStart)) {
                $totalHours += (float) $current->diffInMinutes($effectiveEnd) / 60.0;
            }

            $current->addDay()->startOfDay();
        }

        return $totalHours;
    }

    /**
     * Get the approver for an application
     */
    private function getApproverForApplication(LoanApplication $application): ?User
    {
        // First try to get by approver_id
        if ($application->approver_id) {
            return User::find($application->approver_id);
        }

        // Then try by approver_email
        if ($application->approver_email) {
            return User::where('email', $application->approver_email)->first();
        }

        return null;
    }

    /**
     * Check if we should send an alert (to avoid spam)
     * Only send one alert per status level per day
     */
    private function shouldSendAlert(LoanApplication $application, string $status): bool
    {
        // Check cache for last alert sent
        $cacheKey = "sla_alert_{$application->id}_{$status}";
        /** @var string|null $lastAlert */
        $lastAlert = cache()->get($cacheKey);

        if (\is_string($lastAlert) && Carbon::parse($lastAlert)->isToday()) {
            return false;
        }

        // Mark alert as sent
        cache()->put($cacheKey, now()->toIso8601String(), now()->addDay());

        return true;
    }

    /**
     * Send alert email to approver
     *
     * @param  array{status: string, hours_elapsed: float, hours_remaining: float|null, percentage: float}  $sla
     */
    

/**
 * @param array<string, mixed> $sla
 */
private function sendAlertEmail(LoanApplication $application, User $approver, array $sla): void
    {
        $subject = match ($sla['status']) {
            'breached' => __('sla.email.subject_breached', ['number' => $application->application_number]),
            'critical' => __('sla.email.subject_critical', ['number' => $application->application_number]),
            'warning' => __('sla.email.subject_warning', ['number' => $application->application_number]),
            default => __('sla.email.subject_reminder', ['number' => $application->application_number]),
        };

        Mail::send('emails.sla-alert', [
            'application' => $application,
            'approver' => $approver,
            'sla' => $sla,
            'approvalUrl' => route('portal.approvals'),
        ], function ($message) use ($approver, $subject) {
            $message->to($approver->email, $approver->name)
                ->subject($subject);
        });
    }
}
