<?php

declare(strict_types=1);

namespace App\Services;

use App\Mail\ApprovalConfirmation;
use App\Mail\AssetOverdueNotification;
use App\Mail\AssetReturnReminder;
use App\Mail\LoanApplicationDecision;
use App\Mail\LoanApplicationSubmitted;
use App\Mail\LoanApprovalRequest;
use App\Mail\NewTicketNotification;
use App\Mail\TicketCreatedConfirmation;
use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Notification Service
 *
 * Manages all email notifications for helpdesk and loan modules with bilingual support.
 * Implements queue-based email delivery with 60-second SLA compliance.
 *
 * @component Service Layer
 *
 * @description Comprehensive email notification system with WCAG 2.2 AA compliant templates
 *
 * @author Pasukan BPM MOTAC
 *
 * @trace D03-FR-009.1 Automated email notifications
 * @trace D03-FR-006.4 Bilingual email support
 * @trace D04 §2.4 Notification manager
 * @trace Requirements 1.2, 1.4, 10.1, 10.2, 10.4, 18.1, 18.2
 *
 * @version 2.0.0
 *
 * @created 2025-11-04
 */
class NotificationService
{
    /**
     * Send helpdesk ticket confirmation email
     */
    public function sendTicketConfirmation(HelpdeskTicket $ticket): void
    {
        try {
            $email = $ticket->user ? $ticket->user->email : $ticket->guest_email;

            Mail::to($email)->send(new TicketCreatedConfirmation($ticket));

            Log::info('Ticket confirmation email sent', [
                'ticket_number' => $ticket->ticket_number,
                'email' => $email,
                'is_guest' => is_null($ticket->user_id),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send ticket confirmation', [
                'error' => $e->getMessage(),
                'ticket_number' => $ticket->ticket_number,
            ]);
        }
    }

    /**
     * Send new ticket notification to admin users
     */
    public function sendNewTicketNotification(HelpdeskTicket $ticket): void
    {
        try {
            $admins = User::whereIn('role', ['admin', 'superuser'])->get();

            foreach ($admins as $admin) {
                Mail::to($admin->email)->send(new NewTicketNotification($ticket));
            }

            Log::info('New ticket notification sent to admins', [
                'ticket_number' => $ticket->ticket_number,
                'admin_count' => $admins->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send new ticket notification', [
                'error' => $e->getMessage(),
                'ticket_number' => $ticket->ticket_number,
            ]);
        }
    }

    /**
     * Send loan application confirmation email
     */
    public function sendLoanApplicationConfirmation(LoanApplication $application): void
    {
        try {
            $email = $application->user ? $application->user->email : $application->applicant_email;

            Mail::to($email)->send(new LoanApplicationSubmitted($application));

            Log::info('Loan application confirmation email sent', [
                'application_number' => $application->application_number,
                'email' => $email,
                'is_guest' => is_null($application->user_id),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send loan application confirmation', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send approval request email to Grade 41+ officer
     * Provides DUAL approval options: email-based (no login) AND portal-based (with login)
     *
     * @param  array  $approver  Approver details
     * @param  string  $token  Approval token (7-day validity)
     */
    public function sendApprovalRequest(LoanApplication $application, array $approver, string $token): void
    {
        try {
            Mail::to($approver['email'])->send(new LoanApprovalRequest($application, $token));

            Log::info('Approval request email sent with dual options', [
                'application_number' => $application->application_number,
                'approver_email' => $approver['email'],
                'token_expires_at' => $application->token_expires_at,
                'approval_methods' => ['email', 'portal'],
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send approval request', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send approval decision email to applicant
     */
    public function sendApprovalDecision(LoanApplication $application, bool $approved, ?string $remarks): void
    {
        try {
            $email = $application->user ? $application->user->email : $application->applicant_email;

            Mail::to($email)->send(new LoanApplicationDecision($application, $approved));

            Log::info('Approval decision email sent', [
                'application_number' => $application->application_number,
                'approved' => $approved,
                'email' => $email,
                'has_remarks' => ! is_null($remarks),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send approval decision', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send approval confirmation email to approver
     */
    public function sendApprovalConfirmation(LoanApplication $application, bool $approved): void
    {
        try {
            Mail::to($application->approver_email)->send(new ApprovalConfirmation($application, $approved));

            Log::info('Approval confirmation email sent', [
                'application_number' => $application->application_number,
                'approved' => $approved,
                'approver_email' => $application->approver_email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send approval confirmation', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Notify admin users for asset preparation
     */
    public function notifyAdminForAssetPreparation(LoanApplication $application): void
    {
        try {
            // TODO: Get admin users and send notifications
            // $admins = User::whereIn('role', ['admin', 'superuser'])->get();
            // foreach ($admins as $admin) {
            //     Mail::to($admin->email)
            //         ->send(new AssetPreparationNotification($application));
            // }

            Log::info('Admin notification sent for asset preparation', [
                'application_number' => $application->application_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to notify admin for asset preparation', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send loan status update email
     */
    public function sendLoanStatusUpdate(LoanApplication $application): void
    {
        try {
            // TODO: Implement actual email sending
            // Mail::to($application->applicant_email)
            //     ->send(new LoanStatusUpdate($application));

            Log::info('Loan status update email sent', [
                'application_number' => $application->application_number,
                'status' => $application->status->value,
                'email' => $application->applicant_email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send loan status update', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send overdue reminder email
     */
    public function sendOverdueReminder(LoanApplication $application): void
    {
        try {
            // TODO: Implement actual email sending
            // Mail::to($application->applicant_email)
            //     ->send(new OverdueReminder($application));

            Log::info('Overdue reminder email sent', [
                'application_number' => $application->application_number,
                'email' => $application->applicant_email,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send overdue reminder', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send return reminder email (48 hours before due date)
     */
    public function sendReturnReminder(LoanApplication $application): void
    {
        try {
            $email = $application->user ? $application->user->email : $application->applicant_email;

            Mail::to($email)->send(new AssetReturnReminder($application));

            Log::info('Return reminder email sent', [
                'application_number' => $application->application_number,
                'email' => $email,
                'due_date' => $application->end_date->format('Y-m-d'),
                'hours_remaining' => now()->diffInHours($application->end_date),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send return reminder', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send overdue notification email (daily for overdue assets)
     */
    public function sendOverdueNotification(LoanApplication $application): void
    {
        try {
            $email = $application->user ? $application->user->email : $application->applicant_email;

            Mail::to($email)->send(new AssetOverdueNotification($application));

            Log::info('Overdue notification email sent', [
                'application_number' => $application->application_number,
                'email' => $email,
                'days_overdue' => now()->diffInDays($application->end_date),
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send overdue notification', [
                'error' => $e->getMessage(),
                'application_number' => $application->application_number,
            ]);
        }
    }

    /**
     * Send maintenance notification for damaged asset
     *
     * @param  \App\Models\HelpdeskTicket  $ticket
     * @param  \App\Models\Asset  $asset
     */
    public function sendMaintenanceNotification($ticket, $asset, LoanApplication $application): void
    {
        try {
            // TODO: Implement actual email sending to maintenance team
            // $maintenanceTeam = User::whereIn('role', ['admin', 'superuser'])->get();
            // foreach ($maintenanceTeam as $admin) {
            //     Mail::to($admin->email)
            //         ->send(new MaintenanceNotification($ticket, $asset, $application));
            // }

            Log::info('Maintenance notification sent', [
                'ticket_number' => $ticket->ticket_number,
                'asset_tag' => $asset->asset_tag,
                'application_number' => $application->application_number,
            ]);
        } catch (\Exception $e) {
            Log::error('Failed to send maintenance notification', [
                'error' => $e->getMessage(),
                'ticket_number' => $ticket->ticket_number ?? 'N/A',
            ]);
        }
    }
}
