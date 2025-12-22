<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\AssetOverdueNotification;
use App\Models\LoanApplication;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * SendAssetOverdueEmail Job
 *
 * Queues the AssetOverdueNotification mailable. Used by EmailNotificationService retry workflow.
 * Payload expects 'loan_application_id'.
 *
 * @trace D03-FR-009.1; D03-FR-008.1; D04 §12.1
 */
class SendAssetOverdueEmail implements ShouldQueue
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
            Log::warning('SendAssetOverdueEmail missing loan_application_id', [
                'payload' => $this->payload,
            ]);

            return;
        }

        /** @var LoanApplication|null $application */
        $application = LoanApplication::find($id);
        if (! $application instanceof LoanApplication) {
            Log::warning('SendAssetOverdueEmail application not found', [
                'loan_application_id' => $id,
            ]);

            return;
        }

        // PHPStan now knows $application is LoanApplication
        try {
            // Get email and name with fallbacks for guest applications
            $email = $application->user->email ?? $application->{'applicant_email'} ?? '';
            $name = $application->user->name ?? $application->{'applicant_name'} ?? 'Guest';

            Mail::to($email, $name)
                ->queue(new AssetOverdueNotification($application));

            Log::info('Asset overdue notification email queued (job)', [
                'loan_application_id' => $application->id,
            ]);
        } catch (\Throwable $e) {
            Log::error('Failed dispatching asset overdue email (job)', [
                'loan_application_id' => $application->id,
                'error' => $e->getMessage(),
            ]);
            $this->fail($e);
        }
    }
}
