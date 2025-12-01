<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;

/**
 * Registration Service Interface for ICTServe v3.5.0
 *
 * Provides self-registration functionality for MOTAC staff with:
 * - Email domain validation (@motac.gov.my only)
 * - Email verification workflow
 * - Username extraction for flexible login
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D01 §4.3 Self-registration requirements
 * @see D03 SRS-AUTH-001 Authentication requirements
 * @see Requirements 15.2, 15.3, 15.4, 15.5
 */
interface RegistrationServiceInterface
{
    /**
     * Register a new MOTAC staff user
     *
     * Creates a user account with role 'staff' and status 'pending_verification'.
     * Validates email domain is @motac.gov.my before registration.
     *
     * @param  array{name: string, email: string, password: string}  $data  Registration data
     * @return User The created user (unverified)
     *
     * @throws \App\Exceptions\InvalidEmailDomainException If email is not @motac.gov.my
     * @throws \Illuminate\Validation\ValidationException If validation fails
     */
    public function register(array $data): User;

    /**
     * Validate that email domain is @motac.gov.my
     *
     * Per D00 §4.1, only MOTAC staff with official email addresses
     * can self-register for the system.
     *
     * @param  string  $email  The email address to validate
     * @return bool True if domain is @motac.gov.my
     */
    public function validateEmailDomain(string $email): bool;

    /**
     * Send verification email to newly registered user
     *
     * Generates a signed URL valid for 24 hours and sends
     * verification email to the user.
     *
     * @param  User  $user  The user to send verification to
     *
     * @throws \Exception If email sending fails
     */
    public function sendVerificationEmail(User $user): void;

    /**
     * Verify user's email address
     *
     * Validates the verification token and activates the user account.
     *
     * @param  User  $user  The user to verify
     * @param  string  $token  The verification token from email link
     * @return bool True if verification successful
     */
    public function verifyEmail(User $user, string $token): bool;

    /**
     * Extract username from email address
     *
     * Extracts the local part of email for flexible login.
     * Example: user@motac.gov.my → user
     *
     * @param  string  $email  The full email address
     * @return string The username portion
     */
    public function extractUsernameFromEmail(string $email): string;

    /**
     * Check if email is already registered
     *
     * @param  string  $email  The email to check
     * @return bool True if email exists in system
     */
    public function isEmailRegistered(string $email): bool;

    /**
     * Get allowed email domains for registration
     *
     * Returns array of allowed domains (currently only @motac.gov.my).
     *
     * @return array<string> List of allowed domains
     */
    public function getAllowedDomains(): array;
}
