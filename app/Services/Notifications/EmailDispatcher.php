<?php

declare(strict_types=1);

namespace App\Services\Notifications;

use App\Models\EmailLog;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EmailDispatcher
{
    /**
     * Queue an email and create a tracking log entry.
     *
     * @param  array<string, mixed>  $meta
     */
    public function queue(
        Mailable $mailable,
        string $recipientEmail,
        ?string $recipientName = null,
        array $meta = []
    ): EmailLog {
        $log = EmailLog::create([
            'recipient_email' => $recipientEmail,
            'recipient_name' => $recipientName,
            'subject' => method_exists($mailable, 'envelope')
                ? $mailable->envelope()->subject ?? 'No Subject'
                : 'No Subject',
            'mailable_class' => $mailable::class,
            'status' => 'queued',
            'queued_at' => now(),
            'meta' => $meta,
        ]);

        if (method_exists($mailable, 'withEmailLog')) {
            $mailable->withEmailLog($log);
        }

        try {
            Mail::to($recipientEmail, $recipientName)->queue($mailable);

            Log::info('Email queued for delivery', [
                'email_log_id' => $log->id,
                'recipient' => $recipientEmail,
                'mailable' => $mailable::class,
            ]);
        } catch (\Throwable $exception) {
            $log->markAsFailed($exception->getMessage());

            Log::error('Failed to queue email', [
                'email_log_id' => $log->id,
                'recipient' => $recipientEmail,
                'mailable' => $mailable::class,
                'error' => $exception->getMessage(),
            ]);

            throw $exception;
        }

        return $log;
    }
}
