<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Real-Time Notification Service
 *
 * Provides real-time notification detection and delivery for ICTServe admin panel.
 * Handles SLA breach detection, overdue alerts, and critical system notifications.
 *
 * Requirements: 10.2, 10.5
 *
 * @see D03-FR-008.2 Real-time notifications
 * @see D04 §8.1 Notification system
 */
class RealTimeNotificationService
{
    private const SLA_BREACH_DETECTION_MINUTES = 15;

    private const OVERDUE_ALERT_HOURS = 24;

    private const PENDING_APPROVAL_HOURS = 48;

    private const CRITICAL_SYSTEM_DETECTION_MINUTES = 5;

    /**
     * Detect and create SLA breach notifications
     */
    public function detectSLABreaches(): Collection
    {
        $breaches = collect();

        // Check for helpdesk ticket SLA breaches
        $overdueTickets = DB::table('helpdesk_tickets')
            ->where('status', '!=', 'closed')
            ->where('status', '!=', 'resolved')
            ->whereNotNull('sla_deadline')
            ->where('sla_deadline', '<', now()->subMinutes(self::SLA_BREACH_DETECTION_MINUTES))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->whereRaw('JSON_EXTRACT(data, "$.ticket_id") = helpdesk_tickets.id')
                    ->where('type', 'App\\Notifications\\SLABreach')
                    ->where('created_at', '>', now()->subHour());
            })
            ->get();

        foreach ($overdueTickets as $ticket) {
            $breaches->push([
                'type' => 'sla_breach',
                'entity_type' => 'helpdesk_ticket',
                'entity_id' => $ticket->id,
                'title' => 'SLA Breach Alert',
                'message' => "Ticket #{$ticket->ticket_number} has breached its SLA deadline",
                'priority' => 'high',
                'category' => 'helpdesk',
                'action_url' => "/admin/helpdesk-tickets/{$ticket->id}",
                'action_label' => 'View Ticket',
                'metadata' => [
                    'ticket_number' => $ticket->ticket_number,
                    'sla_deadline' => $ticket->sla_deadline,
                    'breach_duration' => now()->diffInMinutes($ticket->sla_deadline),
                ],
            ]);
        }

        return $breaches;
    }

    /**
     * Detect overdue asset returns
     */
    public function detectOverdueReturns(): Collection
    {
        $overdueAlerts = collect();

        // Check for overdue asset returns
        $overdueLoans = DB::table('loan_applications')
            ->join('loan_items', 'loan_applications.id', '=', 'loan_items.loan_application_id')
            ->where('loan_applications.status', 'issued')
            ->where('loan_items.expected_return_date', '<', now()->subHours(self::OVERDUE_ALERT_HOURS))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->whereRaw('JSON_EXTRACT(data, "$.loan_id") = loan_applications.id')
                    ->where('type', 'App\\Notifications\\AssetOverdue')
                    ->where('created_at', '>', now()->subDay());
            })
            ->select('loan_applications.*', 'loan_items.expected_return_date')
            ->get();

        foreach ($overdueLoans as $loan) {
            $overdueAlerts->push([
                'type' => 'asset_overdue',
                'entity_type' => 'loan_application',
                'entity_id' => $loan->id,
                'title' => 'Overdue Asset Alert',
                'message' => "Loan application #{$loan->application_number} has overdue assets",
                'priority' => 'high',
                'category' => 'loans',
                'action_url' => "/admin/loan-applications/{$loan->id}",
                'action_label' => 'View Loan',
                'metadata' => [
                    'application_number' => $loan->application_number,
                    'expected_return_date' => $loan->expected_return_date,
                    'overdue_days' => now()->diffInDays($loan->expected_return_date),
                ],
            ]);
        }

        return $overdueAlerts;
    }

    /**
     * Detect pending approvals
     */
    public function detectPendingApprovals(): Collection
    {
        $pendingAlerts = collect();

        // Check for loan applications pending approval for too long
        $pendingLoans = DB::table('loan_applications')
            ->where('status', 'pending_approval')
            ->where('created_at', '<', now()->subHours(self::PENDING_APPROVAL_HOURS))
            ->whereNotExists(function ($query) {
                $query->select(DB::raw(1))
                    ->from('notifications')
                    ->whereRaw('JSON_EXTRACT(data, "$.loan_id") = loan_applications.id')
                    ->where('type', 'App\\Notifications\\PendingApproval')
                    ->where('created_at', '>', now()->subDay());
            })
            ->get();

        foreach ($pendingLoans as $loan) {
            $pendingAlerts->push([
                'type' => 'pending_approval',
                'entity_type' => 'loan_application',
                'entity_id' => $loan->id,
                'title' => 'Pending Approval Alert',
                'message' => "Loan application #{$loan->application_number} has been pending approval for over 48 hours",
                'priority' => 'medium',
                'category' => 'loans',
                'action_url' => "/admin/loan-applications/{$loan->id}",
                'action_label' => 'Review Application',
                'metadata' => [
                    'application_number' => $loan->application_number,
                    'submitted_at' => $loan->created_at,
                    'pending_hours' => now()->diffInHours($loan->created_at),
                ],
            ]);
        }

        return $pendingAlerts;
    }

    /**
     * Detect critical system issues
     */
    public function detectCriticalSystemIssues(): Collection
    {
        $systemAlerts = collect();

        // Check queue processing issues
        $failedJobs = DB::table('failed_jobs')
            ->where('failed_at', '>', now()->subMinutes(self::CRITICAL_SYSTEM_DETECTION_MINUTES))
            ->count();

        if ($failedJobs > 5) {
            $systemAlerts->push([
                'type' => 'system_issue',
                'entity_type' => 'system',
                'entity_id' => null,
                'title' => 'Queue Processing Issues',
                'message' => "Multiple job failures detected ({$failedJobs} failed jobs in last 5 minutes)",
                'priority' => 'urgent',
                'category' => 'system',
                'action_url' => '/admin/system-monitoring',
                'action_label' => 'View System Status',
                'metadata' => [
                    'failed_jobs_count' => $failedJobs,
                    'detection_window' => '5 minutes',
                ],
            ]);
        }

        // Check database connection issues
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $systemAlerts->push([
                'type' => 'system_issue',
                'entity_type' => 'system',
                'entity_id' => null,
                'title' => 'Database Connection Issue',
                'message' => 'Database connection problems detected',
                'priority' => 'urgent',
                'category' => 'system',
                'action_url' => '/admin/system-monitoring',
                'action_label' => 'Check System Status',
                'metadata' => [
                    'error_message' => $e->getMessage(),
                ],
            ]);
        }

        return $systemAlerts;
    }

    /**
     * Get all real-time notifications for a user
     */
    public function getRealTimeNotifications(User $user): Collection
    {
        $notifications = collect();

        // Only generate notifications for admin/superuser roles
        if (! $user->hasAnyRole(['admin', 'superuser'])) {
            return $notifications;
        }

        // Get user preferences
        $preferences = $this->getUserNotificationPreferences($user);

        // Collect all notification types based on preferences
        if ($this->shouldReceiveNotificationType($preferences, 'helpdesk_notifications', 'sla_breach')) {
            $notifications = $notifications->concat($this->detectSLABreaches());
        }

        if ($this->shouldReceiveNotificationType($preferences, 'loan_notifications', 'asset_overdue')) {
            $notifications = $notifications->concat($this->detectOverdueReturns());
        }

        if ($this->shouldReceiveNotificationType($preferences, 'loan_notifications', 'application_submitted')) {
            $notifications = $notifications->concat($this->detectPendingApprovals());
        }

        if ($this->shouldReceiveNotificationType($preferences, 'system_notifications', 'performance_alerts')) {
            $notifications = $notifications->concat($this->detectCriticalSystemIssues());
        }

        // Filter by priority threshold
        $priorityThreshold = $preferences['priority_threshold'] ?? 'medium';
        $notifications = $this->filterByPriority($notifications, $priorityThreshold);

        // Apply quiet hours if enabled
        if ($this->isQuietHours($preferences)) {
            $notifications = $notifications->filter(fn ($notification) => $notification['priority'] === 'urgent');
        }

        return $notifications->sortByDesc('priority');
    }

    /**
     * Create and send notifications
     */
    public function createAndSendNotifications(Collection $notifications): void
    {
        foreach ($notifications as $notificationData) {
            $this->createNotification($notificationData);
        }
    }

    /**
     * Create a single notification
     */
    private function createNotification(array $notificationData): void
    {
        // Get all admin/superuser users
        $users = User::role(['admin', 'superuser'])->get();

        foreach ($users as $user) {
            $preferences = $this->getUserNotificationPreferences($user);

            // Check if user should receive this notification
            if (! $this->shouldUserReceiveNotification($user, $notificationData, $preferences)) {
                continue;
            }

            // Create database notification
            $user->notify(new \App\Notifications\RealTimeNotification($notificationData));

            Log::info('Real-time notification created', [
                'user_id' => $user->id,
                'notification_type' => $notificationData['type'],
                'priority' => $notificationData['priority'],
                'entity_type' => $notificationData['entity_type'],
                'entity_id' => $notificationData['entity_id'],
            ]);
        }
    }

    /**
     * Get user notification preferences with caching
     */
    private function getUserNotificationPreferences(User $user): array
    {
        return Cache::remember(
            "user_notification_preferences_{$user->id}",
            now()->addMinutes(30),
            fn () => $user->notification_preferences ?? []
        );
    }

    /**
     * Check if user should receive a specific notification type
     */
    private function shouldReceiveNotificationType(array $preferences, string $category, string $type): bool
    {
        return $preferences[$category][$type] ?? true;
    }

    /**
     * Check if user should receive a notification based on preferences
     */
    private function shouldUserReceiveNotification(User $user, array $notificationData, array $preferences): bool
    {
        // Always send urgent notifications
        if ($notificationData['priority'] === 'urgent') {
            return true;
        }

        // Check urgent only mode
        if ($preferences['urgent_only_mode'] ?? false) {
            return false;
        }

        // Check priority threshold
        $priorityThreshold = $preferences['priority_threshold'] ?? 'medium';
        if (! $this->meetsPriorityThreshold($notificationData['priority'], $priorityThreshold)) {
            return false;
        }

        // Check quiet hours
        if ($this->isQuietHours($preferences) && $notificationData['priority'] !== 'urgent') {
            return false;
        }

        // Check weekend notifications
        if (! ($preferences['weekend_notifications'] ?? false) && now()->isWeekend()) {
            return false;
        }

        return true;
    }

    /**
     * Filter notifications by priority threshold
     */
    private function filterByPriority(Collection $notifications, string $threshold): Collection
    {
        $priorityLevels = ['low' => 1, 'medium' => 2, 'high' => 3, 'urgent' => 4];
        $thresholdLevel = $priorityLevels[$threshold] ?? 2;

        return $notifications->filter(function ($notification) use ($priorityLevels, $thresholdLevel) {
            $notificationLevel = $priorityLevels[$notification['priority']] ?? 1;

            return $notificationLevel >= $thresholdLevel;
        });
    }

    /**
     * Check if notification priority meets threshold
     */
    private function meetsPriorityThreshold(string $priority, string $threshold): bool
    {
        $priorityLevels = ['low' => 1, 'medium' => 2, 'high' => 3, 'urgent' => 4];
        $priorityLevel = $priorityLevels[$priority] ?? 1;
        $thresholdLevel = $priorityLevels[$threshold] ?? 2;

        return $priorityLevel >= $thresholdLevel;
    }

    /**
     * Check if current time is within quiet hours
     */
    private function isQuietHours(array $preferences): bool
    {
        if (! ($preferences['quiet_hours_enabled'] ?? false)) {
            return false;
        }

        $start = Carbon::createFromTimeString($preferences['quiet_hours_start'] ?? '22:00');
        $end = Carbon::createFromTimeString($preferences['quiet_hours_end'] ?? '08:00');
        $now = now();

        // Handle overnight quiet hours (e.g., 22:00 to 08:00)
        if ($start->greaterThan($end)) {
            return $now->greaterThanOrEqualTo($start) || $now->lessThanOrEqualTo($end);
        }

        return $now->between($start, $end);
    }

    /**
     * Get notification statistics
     */
    public function getNotificationStatistics(): array
    {
        return [
            'sla_breaches_detected' => $this->detectSLABreaches()->count(),
            'overdue_returns_detected' => $this->detectOverdueReturns()->count(),
            'pending_approvals_detected' => $this->detectPendingApprovals()->count(),
            'system_issues_detected' => $this->detectCriticalSystemIssues()->count(),
            'last_check' => now(),
        ];
    }
}
