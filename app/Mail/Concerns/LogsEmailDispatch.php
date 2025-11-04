<?php

declare(strict_types=1);

namespace App\Mail\Concerns;

use App\Models\EmailLog;
use Laravel\SerializableClosure\SerializableClosure;
use Symfony\Component\Mime\Email as SymfonyEmail;

/**
 * Adds email logging metadata and queue configuration to mailables.
 */
trait LogsEmailDispatch
{
    public int $tries = 3;

    /**
     * @var array<int, int>
     */
    public array $backoff = [30, 120, 300];

    protected ?EmailLog $emailLog = null;

    public function withEmailLog(EmailLog $emailLog): static
    {
        $this->emailLog = $emailLog;

        $this->withSymfonyMessage(new SerializableClosure(function (SymfonyEmail $message) use ($emailLog): void {
            if ($emailLog->getKey()) {
                $message->getHeaders()->addTextHeader('X-Email-Log-Id', (string) $emailLog->getKey());
            }
        }));

        return $this;
    }
}
