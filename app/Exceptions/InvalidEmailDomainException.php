<?php

declare(strict_types=1);

namespace App\Exceptions;

use Exception;

/**
 * Exception thrown when email domain validation fails during registration
 *
 * Per D00 §4.1, only @motac.gov.my email addresses are allowed for self-registration.
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03 SRS-AUTH-001 Authentication requirements
 * @see Requirements 15.2
 */
class InvalidEmailDomainException extends Exception
{
    /**
     * The invalid email address
     */
    protected string $email;

    /**
     * The allowed domains
     *
     * @var array<string>
     */
    protected array $allowedDomains;

    /**
     * Create a new InvalidEmailDomainException
     *
     * @param  string  $email  The invalid email address
     * @param  array<string>  $allowedDomains  List of allowed domains
     */
    public function __construct(string $email, array $allowedDomains = ['motac.gov.my'])
    {
        $this->email = $email;
        $this->allowedDomains = $allowedDomains;

        $domainsString = implode(', ', array_map(fn ($d) => "@{$d}", $allowedDomains));

        parent::__construct(
            "Email domain not allowed. Only {$domainsString} addresses can register."
        );
    }

    /**
     * Get the invalid email address
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * Get the allowed domains
     *
     * @return array<string>
     */
    public function getAllowedDomains(): array
    {
        return $this->allowedDomains;
    }

    /**
     * Get the extracted domain from the invalid email
     */
    public function getProvidedDomain(): string
    {
        $parts = explode('@', $this->email);

        return $parts[1] ?? '';
    }
}
