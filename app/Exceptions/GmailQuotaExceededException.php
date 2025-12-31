<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when Gmail API quota is exceeded
 *
 * @see Requirements 3.4, 6.3
 */
class GmailQuotaExceededException extends Exception
{
    public function __construct(
        string $message = '',
        private int $maxEmails = 500,
        private int $emailsSent = 0,
        int $code = 429,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message ?: __('auth.gmail_quota_exceeded'), $code, $previous);
    }

    /**
     * Get the maximum daily email limit
     */
    public function getMaxEmails(): int
    {
        return $this->maxEmails;
    }

    /**
     * Get the number of emails sent today
     */
    public function getEmailsSent(): int
    {
        return $this->emailsSent;
    }

    /**
     * Get remaining emails that could be sent
     */
    public function getRemainingEmails(): int
    {
        return max(0, $this->maxEmails - $this->emailsSent);
    }

    /**
     * Get the reset time for quota
     */
    public function getResetTime(): string
    {
        return now()->addDay()->startOfDay()->toIso8601String();
    }

    /**
     * Get exception context for logging
     */
    public function context(): array
    {
        return [
            'max_emails' => $this->maxEmails,
            'emails_sent' => $this->emailsSent,
            'remaining' => $this->getRemainingEmails(),
            'reset_time' => $this->getResetTime(),
        ];
    }
}
