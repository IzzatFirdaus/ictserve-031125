<?php

declare(strict_types=1);

namespace App\Listeners;

use App\Models\EmailLog;
use Illuminate\Mail\Events\MessageSent;

class UpdateEmailLogOnSend
{
    public function handle(MessageSent $event): void
    {
        $logIdHeader = $event->message->getHeaders()->get('X-Email-Log-Id');

        if (! $logIdHeader) {
            return;
        }

        $logId = (int) $logIdHeader->getBodyAsString();

        if ($logId === 0) {
            return;
        }

        $emailLog = EmailLog::find($logId);

        if (! $emailLog) {
            return;
        }

        $messageIdHeader = $event->message->getHeaders()->get('Message-ID');
        $messageId = $messageIdHeader?->getBodyAsString();

        $emailLog->markAsSent($messageId);
    }
}
