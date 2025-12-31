<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when Gmail API rate limit is exceeded
 *
 * @see Requirements 3.4, 9.2
 */
class GmailRateLimitException extends Exception
{
    public function __construct(
        string $message = '',
        private int $retryAfterSeconds = 60,
        int $code = 429,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message ?: __('auth.gmail_rate_limit_exceeded'), $code, $previous);
    }

    /**
     * Get the number of seconds to wait before retrying
     */
    public function getRetryAfterSeconds(): int
    {
        return $this->retryAfterSeconds;
    }

    /**
     * Get the retry time as a Carbon instance
     */
    public function getRetryTime(): string
    {
        return now()->addSeconds($this->retryAfterSeconds)->toIso8601String();
    }

    /**
     * Get exception context for logging
     */
    public function context(): array
    {
        return [
            'retry_after_seconds' => $this->retryAfterSeconds,
            'retry_time' => $this->getRetryTime(),
        ];
    }
}
