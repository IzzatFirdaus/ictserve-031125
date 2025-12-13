<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\LoanApprovalRequest;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendApprovalRequest Job
 *
 * Dispatches approval request emails to Grade 41+ officers for loan applications.
 * Generates signed URLs with JWT tokens valid for 72 hours per D03 SRS-LOAN-004.
 * Processes within 30-second queue SLA per D17.
 *
 * @trace D03-FR-002.1; D03-SRS-LOAN-004; D04 §12.1
 * @trace Requirements 4.1, 10.4, 13.3
 */
class SendApprovalRequest implements ShouldQueue
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
     * @param  array<string,mixed>  $payload  Approval request data
     *                                        - loan_application_id: int (required)
     *                                        - approver_email: string (required)
     *                                        - approver_name: string (optional)
     *                                        - token: string (required, approval token)
     */
    public function __construct(private array $payload)
    {
        $this->onQueue('emails');
    }

    public function handle(): void
    {
        $applicationId = $this->payload['loan_application_id'] ?? null;
        $approverEmail = $this->payload['approver_email'] ?? null;
        $token = $this->payload['token'] ?? null;

        if ($applicationId === null) {
            Log::warning('SendApprovalRequest missing loan_application_id in payload', [
                'payload' => $this->payload,
            ]);

            return;
        }

        if ($approverEmail === null) {
            Log::warning('SendApprovalRequest missing approver_email in payload', [
                'loan_application_id' => $applicationId,
            ]);

            return;
        }

        if ($token === null) {
            Log::warning('SendApprovalRequest missing token in payload', [
                'loan_application_id' => $applicationId,
            ]);

            return;
        }

        $application = LoanApplication::find($applicationId);
        if (! $application instanceof LoanApplication) {
            Log::warning('SendApprovalRequest application not found', [
                'loan_application_id' => $applicationId,
            ]);

            return;
        }

        try {
            $approverName = $this->payload['approver_name'] ?? 'Approver';

            Mail::to($approverEmail, $approverName)
                ->queue(new LoanApprovalRequest($application, $token));

            Log::info('Approval request email queued', [
                'loan_application_id' => $application->id,
                'approver_email' => $approverEmail,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed dispatching approval request email', [
                'loan_application_id' => $application->id,
                'approver_email' => $approverEmail,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
