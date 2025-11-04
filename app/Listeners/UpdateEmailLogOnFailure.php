<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\SendQueuedMailable;
use Illuminate\Queue\Events\JobFailed;

class UpdateEmailLogOnFailure
{
    public function handle(JobFailed $event): void
    {
        try {
            $payload = $event->job->payload();
            $command = $payload['data']['command'] ?? null;

            if (! is_string($command)) {
                return;
            }

            $job = unserialize($command, ['allowed_classes' => true]);

            if (! $job instanceof SendQueuedMailable) {
                return;
            }

            $mailable = $job->mailable;

            if (! property_exists($mailable, 'emailLog')) {
                return;
            }

            $emailLog = (function () {
                return $this->emailLog ?? null;
            })->call($mailable);

            if (! $emailLog instanceof EmailLog) {
                return;
            }

            $emailLog->markAsFailed($event->exception?->getMessage() ?? 'Queued mailable failed.');
        } catch (\Throwable $throwable) {
            // Silently fail to avoid cascading queue issues.
        }
    }
}
