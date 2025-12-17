<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * Process SLA Alert Job
 *
 * Monitors SLA compliance and sends escalation notifications when
 * thresholds are within 25% of breach time per Requirement 8.3.
 *
 * @see Requirements 8.3, 13.2, 23.1, 23.6, 23.7
 */
class ProcessSLAAlertJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out
     */
    public int $timeout = 300;

    /**
     * The backoff delays between retry attempts (exponential backoff)
     *
     * @var array<int>
     */
    public array $backoff = [10, 30, 60];

    /**
     * Create a new job instance
     */
    public function __construct(
        public string $alertType = 'all'
    ) {
        $this->onQueue('helpdesk');
    }

    /**
     * Execute the job
     */
    public function handle(): void
    {
        $startTime = microtime(true);

        Log::info('ProcessSLAAlertJob started', [
            'alert_type' => $this->alertType,
            'attempt' => $this->attempts(),
        ]);

        try {
            $alertsSent = 0;

            if (in_array($this->alertType, ['all', 'helpdesk'])) {
                $alertsSent += $this->processHelpdeskSLAAlerts();
            }

            if (in_array($this->alertType, ['all', 'loans'])) {
                $alertsSent += $this->processLoanSLAAlerts();
            }

            $processingTime = microtime(true) - $startTime;

            Log::info('ProcessSLAAlertJob completed successfully', [
                'alert_type' => $this->alertType,
                'alerts_sent' => $alertsSent,
                'processing_time' => $processingTime,
            ]);
        } catch (\Exception $e) {
            $this->handleFailure($e, microtime(true) - $startTime);
            throw $e;
        }
    }

    /**
     * Process helpdesk ticket SLA alerts
     */
    private function processHelpdeskSLAAlerts(): int
    {
        $alertsSent = 0;

        // Get tickets approaching SLA breach (within 25% of deadline)
        $tickets = HelpdeskTicket::whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('sla_due_at')
            ->get()
            ->filter(function ($ticket) {
                if (! $ticket->sla_due_at) {
                    return false;
                }

                $totalTime = $ticket->created_at->diffInMinutes($ticket->sla_due_at);
                $remainingTime = now()->diffInMinutes($ticket->sla_due_at, false);

                // Alert when 25% or less time remaining
                return $remainingTime > 0 && ($remainingTime / $totalTime) <= 0.25;
            });

        foreach ($tickets as $ticket) {
            $this->sendHelpdeskSLAAlert($ticket);
            $alertsSent++;
        }

        // Check for already breached tickets
        $breachedTickets = HelpdeskTicket::whereNotIn('status', ['resolved', 'closed'])
            ->whereNotNull('sla_due_at')
            ->where('sla_due_at', '<', now())
            ->get();

        foreach ($breachedTickets as $ticket) {
            $this->sendHelpdeskSLABreachAlert($ticket);
            $alertsSent++;
        }

        return $alertsSent;
    }

    /**
     * Process loan application SLA alerts
     */
    private function processLoanSLAAlerts(): int
    {
        $alertsSent = 0;

        // Get loan applications pending approval for more than 5 days
        $pendingLoans = LoanApplication::where('status', 'pending_approval')
            ->where('created_at', '<', now()->subDays(5))
            ->get();

        foreach ($pendingLoans as $loan) {
            $this->sendLoanSLAAlert($loan);
            $alertsSent++;
        }

        // Get overdue asset returns
        $overdueReturns = LoanApplication::where('status', 'active')
            ->where('end_date', '<', now()->toDateString())
            ->get();

        foreach ($overdueReturns as $loan) {
            $this->sendAssetOverdueAlert($loan);
            $alertsSent++;
        }

        return $alertsSent;
    }

    /**
     * Send helpdesk SLA warning alert
     */
    private function sendHelpdeskSLAAlert(HelpdeskTicket $ticket): void
    {
        $remainingTime = now()->diffInHours($ticket->sla_due_at, false);

        $alertData = [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'priority' => $ticket->priority,
            'sla_due_at' => $ticket->sla_due_at->format('d/m/Y H:i'),
            'remaining_hours' => round($remainingTime, 1),
            'message' => "Tiket #{$ticket->ticket_number} akan melanggar SLA dalam {$remainingTime} jam.",
        ];

        // Send to assigned user or admin
        $recipient = $ticket->assignedUser ?? $this->getDefaultAdmin();

        if ($recipient) {
            SendNotificationJob::dispatch(
                'sla_warning',
                $alertData,
                $recipient
            )->onQueue('notifications');
        }

        Log::info('Helpdesk SLA alert sent', [
            'ticket_id' => $ticket->id,
            'remaining_hours' => $remainingTime,
            'recipient_id' => $recipient?->id,
        ]);
    }

    /**
     * Send helpdesk SLA breach alert
     */
    private function sendHelpdeskSLABreachAlert(HelpdeskTicket $ticket): void
    {
        $breachTime = $ticket->sla_due_at->diffInHours(now());

        $alertData = [
            'ticket_id' => $ticket->id,
            'ticket_number' => $ticket->ticket_number,
            'subject' => $ticket->subject,
            'priority' => $ticket->priority,
            'sla_due_at' => $ticket->sla_due_at->format('d/m/Y H:i'),
            'breach_hours' => round($breachTime, 1),
            'message' => "KRITIKAL: Tiket #{$ticket->ticket_number} telah melanggar SLA sebanyak {$breachTime} jam.",
        ];

        // Send to admin and supervisor
        $admins = User::whereIn('role', ['admin', 'superuser'])->get();

        foreach ($admins as $admin) {
            SendNotificationJob::dispatch(
                'sla_breach',
                $alertData,
                $admin
            )->onQueue('notifications');
        }

        Log::warning('Helpdesk SLA breach alert sent', [
            'ticket_id' => $ticket->id,
            'breach_hours' => $breachTime,
            'admin_count' => $admins->count(),
        ]);
    }

    /**
     * Send loan approval SLA alert
     */
    private function sendLoanSLAAlert(LoanApplication $loan): void
    {
        $pendingDays = $loan->created_at->diffInDays(now());

        $alertData = [
            'loan_id' => $loan->id,
            'application_number' => $loan->application_number,
            'applicant_name' => $loan->applicant_name,
            'asset_name' => $loan->asset->name ?? 'Unknown',
            'pending_days' => $pendingDays,
            'message' => "Permohonan pinjaman #{$loan->application_number} telah tertunda selama {$pendingDays} hari.",
        ];

        // Send to approver
        if ($loan->approver) {
            SendNotificationJob::dispatch(
                'loan_sla_warning',
                $alertData,
                $loan->approver
            )->onQueue('notifications');
        }

        Log::info('Loan SLA alert sent', [
            'loan_id' => $loan->id,
            'pending_days' => $pendingDays,
            'approver_id' => $loan->approver?->id,
        ]);
    }

    /**
     * Send asset overdue alert
     */
    private function sendAssetOverdueAlert(LoanApplication $loan): void
    {
        $overdueDays = now()->diffInDays($loan->end_date);

        $alertData = [
            'loan_id' => $loan->id,
            'application_number' => $loan->application_number,
            'applicant_name' => $loan->applicant_name,
            'asset_name' => $loan->asset->name ?? 'Unknown',
            'end_date' => $loan->end_date,
            'overdue_days' => $overdueDays,
            'message' => "Aset #{$loan->asset->name} telah tertunggak selama {$overdueDays} hari.",
        ];

        // Send to applicant and admin
        if ($loan->user) {
            SendNotificationJob::dispatch(
                'asset_overdue',
                $alertData,
                $loan->user
            )->onQueue('notifications');
        }

        // Also notify admin
        $admin = $this->getDefaultAdmin();
        if ($admin) {
            SendNotificationJob::dispatch(
                'asset_overdue_admin',
                $alertData,
                $admin
            )->onQueue('notifications');
        }

        Log::warning('Asset overdue alert sent', [
            'loan_id' => $loan->id,
            'overdue_days' => $overdueDays,
            'applicant_id' => $loan->user?->id,
        ]);
    }

    /**
     * Get default admin user for notifications
     */
    private function getDefaultAdmin(): ?User
    {
        return User::where('role', 'admin')->first() ??
            User::where('role', 'superuser')->first();
    }

    /**
     * Handle job failure
     */
    private function handleFailure(\Exception $e, float $processingTime): void
    {
        Log::error('ProcessSLAAlertJob failed', [
            'alert_type' => $this->alertType,
            'attempt' => $this->attempts(),
            'max_tries' => $this->tries,
            'processing_time' => $processingTime,
            'error' => $e->getMessage(),
        ]);
    }

    /**
     * Handle permanent job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::critical('ProcessSLAAlertJob permanently failed', [
            'alert_type' => $this->alertType,
            'error' => $exception->getMessage(),
        ]);
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * Requirement 23.7: Job tagging for ICTServe operations
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'helpdesk',
            'sla-monitoring',
            'alert-type:'.$this->alertType,
            'priority:high',
        ];
    }
}
