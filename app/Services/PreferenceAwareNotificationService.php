<?php

declare(strict_types=1);

// name: PreferenceAwareNotificationService
// description: Wraps NotificationService with user notification preference checking
// author: dev-team@motac.gov.my
// trace: D03 SRS-FR-003, D04 §4.4, D11 §8 (Requirement 3.2 - granular email preferences)
// last-updated: 2025-11-06

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use App\Models\UserNotificationPreference;

class PreferenceAwareNotificationService
{
    public function __construct(
        private NotificationService $notificationService
    ) {}

    /**
     * Check if user has enabled a specific notification preference.
     */
    protected function userHasPreference(User $user, string $preferenceKey): bool
    {
        $preference = UserNotificationPreference::forUser($user->id)
            ->where('preference_key', $preferenceKey)
            ->first();

        // Default to enabled if no preference record exists
        return $preference?->preference_value ?? true;
    }

    /**
     * Send ticket confirmation if user has enabled ticket notifications.
     */
    public function sendTicketConfirmation(HelpdeskTicket $ticket): void
    {
        if ($this->userHasPreference($ticket->user, 'ticket_submission_confirmation')) {
            $this->notificationService->sendTicketConfirmation($ticket);
        }
    }

    /**
     * Send ticket status update if user has enabled ticket update notifications.
     */
    public function sendTicketStatusUpdate(HelpdeskTicket $ticket): void
    {
        if ($this->userHasPreference($ticket->user, 'ticket_status_updates')) {
            // TODO: Implement sendTicketStatusUpdate in NotificationService
            // $this->notificationService->sendTicketStatusUpdate($ticket);
        }
    }

    /**
     * Send new ticket notification to admins (always send, no user preference).
     */
    public function sendNewTicketNotification(HelpdeskTicket $ticket): void
    {
        $this->notificationService->sendNewTicketNotification($ticket);
    }

    /**
     * Send loan application confirmation if user has enabled loan notifications.
     */
    public function sendLoanApplicationConfirmation(LoanApplication $application): void
    {
        if ($this->userHasPreference($application->user, 'loan_submission_confirmation')) {
            $this->notificationService->sendLoanApplicationConfirmation($application);
        }
    }

    /**
     * Send approval request (always send to approver, no user preference).
     */
    public function sendApprovalRequest(LoanApplication $application, array $approver, string $token): void
    {
        $this->notificationService->sendApprovalRequest($application, $approver, $token);
    }

    /**
     * Send approval decision if user has enabled loan update notifications.
     */
    public function sendApprovalDecision(LoanApplication $application, bool $approved, ?string $remarks): void
    {
        if ($this->userHasPreference($application->user, 'loan_status_updates')) {
            $this->notificationService->sendApprovalDecision($application, $approved, $remarks);
        }
    }

    /**
     * Send approval confirmation if user has enabled loan update notifications.
     */
    public function sendApprovalConfirmation(LoanApplication $application, bool $approved): void
    {
        if ($this->userHasPreference($application->user, 'loan_status_updates')) {
            $this->notificationService->sendApprovalConfirmation($application, $approved);
        }
    }

    /**
     * Notify admin for asset preparation (always send, no user preference).
     */
    public function notifyAdminForAssetPreparation(LoanApplication $application): void
    {
        $this->notificationService->notifyAdminForAssetPreparation($application);
    }

    /**
     * Send loan status update if user has enabled loan update notifications.
     */
    public function sendLoanStatusUpdate(LoanApplication $application, ?string $previousStatus = null): void
    {
        if ($this->userHasPreference($application->user, 'loan_status_updates')) {
            $this->notificationService->sendLoanStatusUpdate($application, $previousStatus);
        }
    }

    /**
     * Send return reminder if user has enabled return reminders.
     */
    public function sendReturnReminder(LoanApplication $application): void
    {
        if ($this->userHasPreference($application->user, 'return_reminders')) {
            $this->notificationService->sendReturnReminder($application);
        }
    }

    /**
     * Send overdue reminder if user has enabled overdue reminders.
     */
    public function sendOverdueReminder(LoanApplication $application): void
    {
        if ($this->userHasPreference($application->user, 'overdue_reminders')) {
            $this->notificationService->sendOverdueReminder($application);
        }
    }

    /**
     * Send overdue notification if user has enabled overdue reminders.
     */
    public function sendOverdueNotification(LoanApplication $application): void
    {
        if ($this->userHasPreference($application->user, 'overdue_reminders')) {
            $this->notificationService->sendOverdueNotification($application);
        }
    }

    /**
     * Send maintenance notification if user has enabled system notifications.
     */
    public function sendMaintenanceNotification(
        HelpdeskTicket $ticket,
        Asset $asset,
        LoanApplication $application
    ): void {
        if ($this->userHasPreference($application->user, 'system_notifications')) {
            $this->notificationService->sendMaintenanceNotification($ticket, $asset, $application);
        }
    }

    /**
     * Send SLA breach warning (always send, critical notification).
     */
    public function sendSlaBreachWarning(HelpdeskTicket $ticket): void
    {
        $this->notificationService->sendSlaBreachWarning($ticket);
    }

    /**
     * Get all notification preferences for a user.
     *
     * @return array Associative array of preference_key => preference_value
     */
    public function getUserPreferences(User $user): array
    {
        $preferences = UserNotificationPreference::forUser($user->id)->get();

        $preferencesArray = [];
        foreach ($preferences as $pref) {
            $preferencesArray[$pref->preference_key] = $pref->preference_value;
        }

        // Fill in defaults for missing preferences
        foreach (UserNotificationPreference::availableKeys() as $key) {
            if (! isset($preferencesArray[$key])) {
                $preferencesArray[$key] = true; // Default to enabled
            }
        }

        return $preferencesArray;
    }

    /**
     * Update user notification preferences.
     *
     * @param  array  $preferences  Associative array of preference_key => boolean
     */
    public function updateUserPreferences(User $user, array $preferences): void
    {
        foreach ($preferences as $key => $value) {
            UserNotificationPreference::updateOrCreate(
                [
                    'user_id' => $user->id,
                    'preference_key' => $key,
                ],
                [
                    'preference_value' => (bool) $value,
                ]
            );
        }
    }
}
