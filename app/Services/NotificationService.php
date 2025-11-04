<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\Notifications\LoanNotificationService;
use App\Services\Notifications\ReminderNotificationService;
use App\Services\Notifications\SLANotificationService;
use App\Services\Notifications\TicketNotificationService;

/**
 * Backwards-compatible notification facade that delegates to specialised services.
 *
 * @deprecated Prefer injecting the granular services directly.
 */
class NotificationService
{
    public function __construct(
        private TicketNotificationService $ticketNotifications,
        private LoanNotificationService $loanNotifications,
        private ReminderNotificationService $reminderNotifications,
        private SLANotificationService $slaNotifications
    ) {}

    public function sendTicketConfirmation(HelpdeskTicket $ticket): void
    {
        $this->ticketNotifications->sendTicketConfirmation($ticket);
    }

    public function sendNewTicketNotification(HelpdeskTicket $ticket): void
    {
        $this->ticketNotifications->notifyAdmins($ticket);
    }

    public function sendLoanApplicationConfirmation(LoanApplication $application): void
    {
        $this->loanNotifications->sendApplicationConfirmation($application);
    }

    /**
     * @param  array{email: string, name?: string|null}  $approver
     */
    public function sendApprovalRequest(LoanApplication $application, array $approver, string $token): void
    {
        $this->loanNotifications->sendApprovalRequest($application, $approver, $token);
    }

    public function sendApprovalDecision(LoanApplication $application, bool $approved, ?string $remarks): void
    {
        $this->loanNotifications->sendApprovalDecision($application, $approved, $remarks);
    }

    public function sendApprovalConfirmation(LoanApplication $application, bool $approved): void
    {
        $this->loanNotifications->sendApprovalConfirmation($application, $approved);
    }

    public function notifyAdminForAssetPreparation(LoanApplication $application): void
    {
        $this->loanNotifications->notifyAdminForAssetPreparation($application);
    }

    public function sendLoanStatusUpdate(LoanApplication $application, ?string $previousStatus = null): void
    {
        $this->loanNotifications->sendStatusUpdate($application, $previousStatus);
    }

    public function sendReturnReminder(LoanApplication $application): void
    {
        $this->reminderNotifications->sendReturnReminder($application);
    }

    public function sendOverdueReminder(LoanApplication $application): void
    {
        $this->reminderNotifications->sendOverdueReminder($application);
    }

    public function sendOverdueNotification(LoanApplication $application): void
    {
        $this->reminderNotifications->sendOverdueNotification($application);
    }

    public function sendMaintenanceNotification(
        HelpdeskTicket $ticket,
        Asset $asset,
        LoanApplication $application
    ): void {
        $this->ticketNotifications->sendMaintenanceNotification($ticket, $asset, $application);
    }

    public function sendSlaBreachWarning(HelpdeskTicket $ticket): void
    {
        $this->slaNotifications->sendBreachWarning($ticket);
    }
}
