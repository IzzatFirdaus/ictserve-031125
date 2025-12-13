<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Models\User;
use Laravel\Socialite\Contracts\User as SocialiteUser;

/**
 * Google SSO Service Interface for ICTServe v3.6.0
 *
 * Provides Google Single Sign-On functionality for MOTAC staff with:
 * - Email domain validation (@motac.gov.my only)
 * - User creation and account linking
 * - Comprehensive audit logging
 * - Error handling and graceful degradation
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see D04 §6.1 Security
 * @see Requirements 1.1, 1.2, 2.1, 4.1
 */
interface GoogleSsoServiceInterface
{
    /**
     * Validate that email domain is @motac.gov.my
     *
     * Per D00 §4.1, only MOTAC staff with official email addresses
     * can authenticate via Google SSO.
     *
     * @param  string  $email  The email address to validate
     * @return bool True if domain is @motac.gov.my (case-insensitive)
     */
    public function validateDomain(string $email): bool;

    /**
     * Create or update user from Google OAuth profile
     *
     * Creates a new user if email doesn't exist, or updates existing user
     * with Google SSO credentials. Ensures idempotent behavior.
     *
     * @param  SocialiteUser  $googleUser  The Google OAuth user profile
     * @return User The created or updated user
     *
     * @throws \App\Exceptions\InvalidEmailDomainException If email is not @motac.gov.my
     */
    public function createOrUpdateUser(SocialiteUser $googleUser): User;

    /**
     * Link existing user account to Google SSO
     *
     * Updates an existing user's Google SSO credentials without
     * modifying other user data.
     *
     * @param  User  $user  The existing user to link
     * @param  SocialiteUser  $googleUser  The Google OAuth user profile
     */
    public function linkExistingAccount(User $user, SocialiteUser $googleUser): void;

    /**
     * Log authentication attempt for audit compliance
     *
     * Creates audit log entry for all SSO authentication attempts,
     * both successful and failed, per D09 audit requirements.
     *
     * @param  string  $email  The email address used in attempt
     * @param  bool  $success  Whether authentication succeeded
     * @param  string|null  $error  Error message if authentication failed
     * @param  int|null  $userId  User ID if authentication succeeded
     */
    public function logAuthenticationAttempt(
        string $email,
        bool $success,
        ?string $error = null,
        ?int $userId = null
    ): void;

    /**
     * Get health status of Google SSO service
     *
     * Returns array with service availability and configuration status.
     *
     * @return array{available: bool, configured: bool, message: string}
     */
    public function getHealthStatus(): array;

    /**
     * Get allowed email domains for SSO
     *
     * @return array<string> List of allowed domains
     */
    public function getAllowedDomains(): array;

    /**
     * Check if user has Google SSO linked
     *
     * @param  User  $user  The user to check
     * @return bool True if user has Google SSO linked
     */
    public function hasGoogleSsoLinked(User $user): bool;

    /**
     * Unlink Google SSO from user account
     *
     * Removes Google SSO credentials from user account while
     * preserving other user data.
     *
     * @param  User  $user  The user to unlink
     * @return bool True if unlink was successful
     */
    public function unlinkGoogleSso(User $user): bool;
}
