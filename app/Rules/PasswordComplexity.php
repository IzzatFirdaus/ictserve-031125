<?php

declare(strict_types=1);

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;

/**
 * Password Complexity Validation Rule
 *
 * Enforces password complexity requirements for admin users.
 * Requirement 17.2: Password complexity requirements
 *
 * Requirements:
 * - Minimum 12 characters
 * - At least one uppercase letter
 * - At least one lowercase letter
 * - At least one number
 * - At least one special character
 *
 * @version 1.0.0
 *
 * @since 2025-11-07
 */
class PasswordComplexity implements ValidationRule
{
    /**
     * Minimum password length
     */
    private const MIN_LENGTH = 12;

    /**
     * Run the validation rule.
     *
     * @param  \Closure(string, ?string=): \Illuminate\Translation\PotentiallyTranslatedString  $fail
     */
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        // Check minimum length
        if (strlen((string) $value) < self::MIN_LENGTH) {
            $fail(__('The :attribute must be at least :min characters.', [
                'attribute' => $attribute,
                'min' => self::MIN_LENGTH,
            ]));

            return;
        }

        // Check for uppercase letter
        if (! preg_match('/[A-Z]/', (string) $value)) {
            $fail(__('The :attribute must contain at least one uppercase letter.', [
                'attribute' => $attribute,
            ]));

            return;
        }

        // Check for lowercase letter
        if (! preg_match('/[a-z]/', (string) $value)) {
            $fail(__('The :attribute must contain at least one lowercase letter.', [
                'attribute' => $attribute,
            ]));

            return;
        }

        // Check for number
        if (! preg_match('/[0-9]/', (string) $value)) {
            $fail(__('The :attribute must contain at least one number.', [
                'attribute' => $attribute,
            ]));

            return;
        }

        // Check for special character
        if (! preg_match('/[^A-Za-z0-9]/', (string) $value)) {
            $fail(__('The :attribute must contain at least one special character.', [
                'attribute' => $attribute,
            ]));

            return;
        }
    }
}
