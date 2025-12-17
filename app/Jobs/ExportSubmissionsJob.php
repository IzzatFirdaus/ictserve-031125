<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Mail\ExportReadyMail;
use App\Models\User;
use App\Services\ExportService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Export Submissions Job
 *
 * Processes large submission exports asynchronously in the background.
 * Sends email notification with download link when export is ready.
 *
 * @see .kiro/specs/staff-dashboard-profile/design.md - Export Service Design
 * @see .kiro/specs/staff-dashboard-profile/requirements.md - Requirements 9.4, 9.5
 */
class ExportSubmissionsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted
     */
    public int $tries = 3;

    /**
     * The number of seconds the job can run before timing out
     */
    public int $timeout = 300;

    /**
     * Create a new job instance
     *
     * @param  array<string, mixed>  $filters
     */
    public function __construct(
        public User $user,
        public string $format,
        public array $filters,
        public string $jobId
    ) {}

    /**
     * Execute the job
     */
    public function handle(ExportService $exportService): void
    {
        try {
            // Generate export
            $filename = $exportService->exportSubmissions(
                $this->user,
                $this->format,
                $this->filters
            );

            // Send email notification with download link
            Mail::to($this->user->email)->send(
                new ExportReadyMail($this->user, $filename, $this->jobId)
            );
        } catch (\Exception $e) {
            // Log error and re-throw for retry
            Log::error('Export job failed', [
                'job_id' => $this->jobId,
                'user_id' => $this->user->id,
                'format' => $this->format,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }

    /**
     * Handle a job failure
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('Export job failed permanently', [
            'job_id' => $this->jobId,
            'user_id' => $this->user->id,
            'format' => $this->format,
            'error' => $exception->getMessage(),
        ]);

        try {
            Mail::raw(
                "We couldn't complete your {$this->format} export (Job ID: {$this->jobId}). Please try again or contact support if the issue persists.",
                function (\Illuminate\Mail\Message $message): void {
                    $message->to($this->user->email, $this->user->name)
                        ->subject('Export Failed - ICTServe');
                }
            );
        } catch (\Throwable $notificationException) {
            Log::error('Failed to send export failure notification', [
                'job_id' => $this->jobId,
                'user_id' => $this->user->id,
                'error' => $notificationException->getMessage(),
            ]);
        }
    }

    /**
     * Get the tags that should be assigned to the job.
     *
     * Requirement 23.7: Job tagging for ICTServe operations
     *
     * @return array<string>
     */
    public function tags(): array
    {
        return [
            'reports',
            'export',
            'user:'.$this->user->id,
            'format:'.$this->format,
            'job_id:'.$this->jobId,
        ];
    }
}
