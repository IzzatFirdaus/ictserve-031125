<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\AssetOverdueNotification;
use App\Mail\LoanApplicationApproved;
use App\Mail\LoanApplicationRejected;
use App\Mail\LoanApplicationSubmitted;
use App\Mail\LoanStatusUpdated;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendLoanNotification Job
 *
 * Unified job for dispatching loan application notifications.
 * Supports multiple notification types: submitted, approved, rejected, status_updated, overdue.
 * Processes within 30-second queue SLA per D17.
 *
 * @trace D03-FR-002.1; D03-FR-008.1; D04 §12.1
 * @trace Requirements 10.4, 13.3
 */
class SendLoanNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Number of times the job may be attempted.
     */
    public int $tries = 3;

    /**
     * Number of seconds to wait before retrying.
     */
    public int $backoff = 10;

    /**
     * @param  array<string,mixed>  $payload  Notification data
     */
    public function __construct(private array $payload)
    {
        $this->onQueue('notifications');
    }

    public function handle(): void
    {
        $applicationId = $this->payload['loan_application_id'] ?? null;
        $type = $this->payload['type'] ?? 'submitted';

        if ($applicationId === null) {
            Log::warning('SendLoanNotification missing loan_application_id in payload', [
                'payload' => $this->payload,
            ]);

            return;
        }

        $application = LoanApplication::find($applicationId);
        if (! $application instanceof LoanApplication) {
            Log::warning('SendLoanNotification application not found', [
                'loan_application_id' => $applicationId,
            ]);

            return;
        }

        try {
            match ($type) {
                'submitted' => $this->sendSubmittedNotification($application),
                'approved' => $this->sendApprovedNotification($application),
                'rejected' => $this->sendRejectedNotification($application),
                'status_updated' => $this->sendStatusUpdatedNotification($application),
                'overdue' => $this->sendOverdueNotification($application),
                default => Log::warning('SendLoanNotification unknown type', [
                    'loan_application_id' => $applicationId,
                    'type' => $type,
                ]),
            };
        } catch (\Throwable $e) {
            Log::error('Failed dispatching loan notification', [
                'loan_application_id' => $application->id,
                'type' => $type,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }

    private function sendSubmittedNotification(LoanApplication $application): void
    {
        $email = $this->getRecipientEmail($application);
        $name = $this->getRecipientName($application);

        if (empty($email)) {
            return;
        }

        Mail::to($email, $name)->queue(new LoanApplicationSubmitted($application));
        Log::info('Loan submitted notification queued', ['loan_application_id' => $application->id]);
    }

    private function sendApprovedNotification(LoanApplication $application): void
    {
        $email = $this->getRecipientEmail($application);
        $name = $this->getRecipientName($application);

        if (empty($email)) {
            return;
        }

        Mail::to($email, $name)->queue(new LoanApplicationApproved($application));
        Log::info('Loan approved notification queued', ['loan_application_id' => $application->id]);
    }

    private function sendRejectedNotification(LoanApplication $application): void
    {
        $email = $this->getRecipientEmail($application);
        $name = $this->getRecipientName($application);

        if (empty($email)) {
            return;
        }

        Mail::to($email, $name)->queue(new LoanApplicationRejected($application));
        Log::info('Loan rejected notification queued', ['loan_application_id' => $application->id]);
    }

    private function sendStatusUpdatedNotification(LoanApplication $application): void
    {
        $email = $this->getRecipientEmail($application);
        $name = $this->getRecipientName($application);

        if (empty($email)) {
            return;
        }

        Mail::to($email, $name)->queue(new LoanStatusUpdated($application));
        Log::info('Loan status updated notification queued', ['loan_application_id' => $application->id]);
    }

    private function sendOverdueNotification(LoanApplication $application): void
    {
        $email = $this->getRecipientEmail($application);
        $name = $this->getRecipientName($application);

        if (empty($email)) {
            return;
        }

        Mail::to($email, $name)->queue(new AssetOverdueNotification($application));
        Log::info('Loan overdue notification queued', ['loan_application_id' => $application->id]);
    }

    private function getRecipientEmail(LoanApplication $application): string
    {
        return $this->payload['recipient_email']
            ?? $application->user?->email
            ?? $application->{'applicant_email'}
            ?? '';
    }

    private function getRecipientName(LoanApplication $application): string
    {
        return $this->payload['recipient_name']
            ?? $application->user?->name
            ?? $application->{'applicant_name'}
            ?? 'Applicant';
    }
}
