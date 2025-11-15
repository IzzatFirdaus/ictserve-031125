<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Notification;

/**
 * Real-Time Notification Service
 *
 * Monitors and sends real-time notifications for critical events:
 * - SLA breaches (15min detection)
 * - Overdue returns (24h alert)
 * - Pending approvals (48h alert)
 *
 * @version 1.0.0
 *
 * @since 2025-01-06
 *
 * @author ICTServe Development Team
 * @copyright 2025 MOTAC BPM
 *
 * Requirements: D03-FR-011 (Real-time Notifications)
 * Traceability: Phase 10.2 - Real-Time Notification Service
 * WCAG 2.2 AA: N/A (Backend service)
 * Bilingual: N/A (Backend service)
 */
class RealTimeNotificationService
{
    /**
     * SLA breach detection threshold (minutes)
     */
    private const SLA_BREACH_THRESHOLD = 15;

    /**
     * Overdue return alert threshold (hours)
     */
    private const OVERDUE_ALERT_THRESHOLD = 24;

    /**
     * Pending approval alert threshold (hours)
     */
    private const PENDING_APPROVAL_THRESHOLD = 48;

    /**
     * Check for SLA breaches and send notifications
     *
     * @return int Number of notifications sent
     */
    public function checkSLABreaches(): int
    {
        $breachedTickets = HelpdeskTicket::query()
            ->where('status', '!=', 'closed')
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', Carbon::now())
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('type', 'App\\Notifications\\SLABreach')
                    ->where('created_at', '>', Carbon::now()->subMinutes(self::SLA_BREACH_THRESHOLD));
            })
            ->with(['assignedTo', 'user'])
            ->get();

        $notificationsSent = 0;

        foreach ($breachedTickets as $ticket) {
            $this->notifySLABreach($ticket);
            $notificationsSent++;
        }

        return $notificationsSent;
    }

    /**
     * Check for overdue returns and send notifications
     *
     * @return int Number of notifications sent
     */
    public function checkOverdueReturns(): int
    {
        $overdueLoans = LoanApplication::query()
            ->where('status', 'issued')
            ->whereNotNull('return_date')
            ->where('return_date', '<', Carbon::now())
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('type', 'App\\Notifications\\AssetOverdue')
                    ->where('created_at', '>', Carbon::now()->subHours(self::OVERDUE_ALERT_THRESHOLD));
            })
            ->with(['applicant', 'loanItems.asset'])
            ->get();

        $notificationsSent = 0;

        foreach ($overdueLoans as $loan) {
            $this->notifyOverdueReturn($loan);
            $notificationsSent++;
        }

        return $notificationsSent;
    }

    /**
     * Check for pending approvals and send notifications
     *
     * @return int Number of notifications sent
     */
    public function checkPendingApprovals(): int
    {
        $pendingApprovals = LoanApplication::query()
            ->where('status', 'pending')
            ->where('created_at', '<', Carbon::now()->subHours(self::PENDING_APPROVAL_THRESHOLD))
            ->whereDoesntHave('notifications', function ($query) {
                $query->where('type', 'App\\Notifications\\PendingApprovalReminder')
                    ->where('created_at', '>', Carbon::now()->subHours(self::PENDING_APPROVAL_THRESHOLD));
            })
            ->with(['applicant', 'approver'])
            ->get();

        $notificationsSent = 0;

        foreach ($pendingApprovals as $application) {
            $this->notifyPendingApproval($application);
            $notificationsSent++;
        }

        return $notificationsSent;
    }

    /**
     * Notify about SLA breach
     */
    private function notifySLABreach(HelpdeskTicket $ticket): void
    {
        $recipients = $this->getSLABreachRecipients($ticket);

        $data = [
            'title' => __('SLA Breach Alert'),
            'message' => __('Ticket :number has breached its SLA deadline', ['number' => $ticket->ticket_number]),
            'action_url' => route('filament.admin.resources.helpdesk.tickets.view', $ticket),
            'action_label' => __('View Ticket'),
            'priority' => 'urgent',
            'category' => 'sla_breach',
            'metadata' => [
                'ticket_id' => $ticket->id,
                'ticket_number' => $ticket->ticket_number,
                'sla_deadline' => $ticket->sla_deadline?->toIso8601String(),
                'breach_duration' => $ticket->sla_deadline?->diffInMinutes(Carbon::now()),
            ],
        ];

        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\SLABreach($ticket, $data));
        }
    }

    /**
     * Notify about overdue return
     */
    private function notifyOverdueReturn(LoanApplication $loan): void
    {
        $recipients = $this->getOverdueReturnRecipients($loan);

        $data = [
            'title' => __('Overdue Asset Return'),
            'message' => __('Loan application :number is overdue for return', ['number' => $loan->application_number]),
            'action_url' => route('filament.admin.resources.loans.applications.view', $loan),
            'action_label' => __('View Loan'),
            'priority' => 'high',
            'category' => 'overdue_return',
            'metadata' => [
                'loan_id' => $loan->id,
                'application_number' => $loan->application_number,
                'return_date' => $loan->return_date?->toIso8601String(),
                'overdue_days' => $loan->return_date?->diffInDays(Carbon::now()),
                'assets' => $loan->loanItems->pluck('asset.name')->toArray(),
            ],
        ];

        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\AssetOverdue($loan, $data));
        }
    }

    /**
     * Notify about pending approval
     */
    private function notifyPendingApproval(LoanApplication $application): void
    {
        $recipients = $this->getPendingApprovalRecipients($application);

        $data = [
            'title' => __('Pending Approval Reminder'),
            'message' => __('Loan application :number is pending approval for :hours hours', [
                'number' => $application->application_number,
                'hours' => $application->created_at->diffInHours(Carbon::now()),
            ]),
            'action_url' => route('filament.admin.resources.loans.applications.view', $application),
            'action_label' => __('Review Application'),
            'priority' => 'medium',
            'category' => 'pending_approval',
            'metadata' => [
                'application_id' => $application->id,
                'application_number' => $application->application_number,
                'created_at' => $application->created_at->toIso8601String(),
                'pending_duration' => $application->created_at->diffInHours(Carbon::now()),
            ],
        ];

        foreach ($recipients as $recipient) {
            $recipient->notify(new \App\Notifications\PendingApprovalReminder($application, $data));
        }
    }

    /**
     * Get recipients for SLA breach notifications
     */
    private function getSLABreachRecipients(HelpdeskTicket $ticket): Collection
    {
        $recipients = collect();

        // Add assigned user
        if ($ticket->assignedTo) {
            $recipients->push($ticket->assignedTo);
        }

        // Add admin users
        $admins = User::role(['admin', 'superuser'])->get();
        $recipients = $recipients->merge($admins);

        return $recipients->unique('id');
    }

    /**
     * Get recipients for overdue return notifications
     */
    private function getOverdueReturnRecipients(LoanApplication $loan): Collection
    {
        $recipients = collect();

        // Add applicant
        if ($loan->applicant) {
            $recipients->push($loan->applicant);
        }

        // Add admin users
        $admins = User::role(['admin', 'superuser'])->get();
        $recipients = $recipients->merge($admins);

        return $recipients->unique('id');
    }

    /**
     * Get recipients for pending approval notifications
     */
    private function getPendingApprovalRecipients(LoanApplication $application): Collection
    {
        $recipients = collect();

        // Add approver
        if ($application->approver) {
            $recipients->push($application->approver);
        }

        // Add admin users
        $admins = User::role(['admin', 'superuser'])->get();
        $recipients = $recipients->merge($admins);

        return $recipients->unique('id');
    }

    /**
     * Run all real-time checks
     *
     * @return array<string, int>
     */
    public function runAllChecks(): array
    {
        return [
            'sla_breaches' => $this->checkSLABreaches(),
            'overdue_returns' => $this->checkOverdueReturns(),
            'pending_approvals' => $this->checkPendingApprovals(),
        ];
    }

    /**
     * Get notification statistics
     *
     * @param  int  $hours  Time window in hours
     * @return array<string, mixed>
     */
    public function getNotificationStats(int $hours = 24): array
    {
        $threshold = Carbon::now()->subHours($hours);

        return [
            'total_sent' => \DB::table('notifications')
                ->where('created_at', '>=', $threshold)
                ->count(),
            'by_type' => \DB::table('notifications')
                ->where('created_at', '>=', $threshold)
                ->select('type', \DB::raw('count(*) as count'))
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'unread_count' => \DB::table('notifications')
                ->whereNull('read_at')
                ->count(),
            'time_window' => $hours.' hours',
        ];
    }
}
