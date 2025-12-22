<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\LoanApplicationApproved;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendLoanApprovedEmail Job
 *
 * Queues the LoanApplicationApproved mailable. Used by EmailNotificationService retry workflow.
 * Payload expects 'loan_application_id'.
 *
 * @trace D03-FR-002.1; D03-FR-008.1; D04 §12.1
 */
class SendLoanApprovedEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @param  array<string,mixed>  $payload
     */
    

/**
 * @param array<string, mixed> $payload
 */
public function __construct(private array $payload) {}

    public function handle(): void
    {
        $id = $this->payload['loan_application_id'] ?? null;
        if ($id === null) {
            Log::warning('SendLoanApprovedEmail missing loan_application_id', [
                'payload' => $this->payload,
            ]);

            return;
        }

        /** @var LoanApplication|null $application */
        $application = LoanApplication::find($id);
        if (! $application instanceof LoanApplication) {
            Log::warning('SendLoanApprovedEmail application not found', [
                'loan_application_id' => $id,
            ]);

            return;
        }

        try {
            // Access dynamic properties using string access for PHPStan
            $email = $application->{'applicant_email'} ?? '';
            $name = $application->{'applicant_name'} ?? 'Applicant';

            Mail::to($email, $name)
                ->queue(new LoanApplicationApproved($application));

            Log::info('Loan approved email queued (job)', [
                'loan_application_id' => $application->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed dispatching loan approved email (job)', [
                'loan_application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
