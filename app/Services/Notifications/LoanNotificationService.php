<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Mail\ApprovalConfirmation;
use App\Mail\AssetPreparationNotification;
use App\Mail\LoanApplicationDecision;
use App\Mail\LoanApplicationSubmitted;
use App\Mail\LoanApprovalRequest;
use App\Mail\LoanStatusUpdated;
use App\Models\LoanApplication;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class LoanNotificationService
{
    public function __construct(
        private EmailDispatcher $dispatcher
    ) {}

    public function sendApplicationConfirmation(LoanApplication $application): void
    {
        $email = $application->user?->email ?? $application->applicant_email;
        $name = $application->user?->name ?? $application->applicant_name;

        $this->dispatcher->queue(
            (new LoanApplicationSubmitted($application))->onQueue('emails'),
            $email,
            $name,
            [
                'application_number' => $application->application_number,
                'is_guest' => $application->isGuestSubmission(),
            ]
        );

        Log::info('Loan application confirmation queued', [
            'application_number' => $application->application_number,
            'recipient' => $email,
        ]);
    }

    /**
     * @param  array{email: string, name?: string|null}  $approver
     */
    public function sendApprovalRequest(LoanApplication $application, array $approver, string $token): void
    {
        $this->dispatcher->queue(
            (new LoanApprovalRequest($application, $token))->onQueue('emails'),
            $approver['email'],
            $approver['name'] ?? null,
            [
                'application_number' => $application->application_number,
                'token_expires_at' => $application->approval_token_expires_at,
            ]
        );

        Log::info('Loan approval request queued', [
            'application_number' => $application->application_number,
            'approver_email' => $approver['email'],
        ]);
    }

    public function sendApprovalDecision(LoanApplication $application, bool $approved, ?string $remarks = null): void
    {
        $email = $application->user?->email ?? $application->applicant_email;
        $name = $application->user?->name ?? $application->applicant_name;

        $this->dispatcher->queue(
            (new LoanApplicationDecision($application, $approved))->onQueue('emails'),
            $email,
            $name,
            [
                'application_number' => $application->application_number,
                'approved' => $approved,
                'remarks' => $remarks,
            ]
        );

        Log::info('Loan approval decision queued', [
            'application_number' => $application->application_number,
            'recipient' => $email,
            'approved' => $approved,
        ]);
    }

    public function sendApprovalConfirmation(LoanApplication $application, bool $approved): void
    {
        if (! $application->approver_email) {
            return;
        }

        $this->dispatcher->queue(
            (new ApprovalConfirmation($application, $approved))->onQueue('emails'),
            $application->approver_email,
            $application->approved_by_name,
            [
                'application_number' => $application->application_number,
                'approved' => $approved,
            ]
        );

        Log::info('Approval confirmation queued for approver', [
            'application_number' => $application->application_number,
            'approver_email' => $application->approver_email,
        ]);
    }

    public function notifyAdminForAssetPreparation(LoanApplication $application): void
    {
        $admins = User::query()
            ->whereIn('role', ['admin', 'superuser'])
            ->pluck('email', 'name');

        foreach ($admins as $name => $email) {
            $this->dispatcher->queue(
                (new AssetPreparationNotification($application))->onQueue('notifications'),
                $email,
                is_string($name) ? $name : null,
                [
                    'application_number' => $application->application_number,
                    'loan_start_date' => $application->loan_start_date,
                    'loan_end_date' => $application->loan_end_date,
                ]
            );
        }

        Log::info('Asset preparation notifications queued', [
            'application_number' => $application->application_number,
            'admin_count' => $admins->count(),
        ]);
    }

    public function sendStatusUpdate(LoanApplication $application, ?string $previousStatus = null): void
    {
        $email = $application->user?->email ?? $application->applicant_email;
        $name = $application->user?->name ?? $application->applicant_name;

        $this->dispatcher->queue(
            (new LoanStatusUpdated($application, $previousStatus))->onQueue('emails'),
            $email,
            $name,
            [
                'application_number' => $application->application_number,
                'status' => $application->status->value,
                'previous_status' => $previousStatus,
            ]
        );

        Log::info('Loan status update notification queued', [
            'application_number' => $application->application_number,
            'status' => $application->status->value,
        ]);
    }
}
