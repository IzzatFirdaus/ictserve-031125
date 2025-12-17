<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * MOTAC Email Domain Validation Rule
 *
 * Validates that email addresses end with @motac.gov.my (case-insensitive).
 * Used for self-registration to ensure only MOTAC staff can register.
 *
 * @trace D00 §4.1 (True Hybrid Architecture)
 * @trace D03 SRS-AUTH-001 (Self-Registration)
 * @trace Requirements 5.1, 5.5 (Email Domain Restriction)
 *
 * @version 1.0.0
 * @created 2025-12-17
 */
class MotacEmailDomain implements ValidationRule
{
    /**
     * Allowed email domain for MOTAC staff
     */
    private const ALLOWED_DOMAIN = '@motac.gov.my';

    /**
     * Run the validation rule.
     *
     * Validates that the email address ends with @motac.gov.my
     * in a case-insensitive manner.
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (!\is_string($value)) {
            $fail('Medan :attribute mesti menggunakan alamat e-mel @motac.gov.my.');
            return;
        }

        // Convert to lowercase for case-insensitive comparison
        $email = \strtolower(\trim($value));

        // Check if email ends with the allowed domain
        if (!\str_ends_with($email, self::ALLOWED_DOMAIN)) {
            $fail('Medan :attribute mesti menggunakan alamat e-mel @motac.gov.my.');
        }
    }
}
