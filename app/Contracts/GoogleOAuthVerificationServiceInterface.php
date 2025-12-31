<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Google OAuth Verification Service Interface for ICTServe v3.6.1
 *
 * Manages OAuth app verification process and test user management:
 * - Verification status detection and handling
 * - Test user management (add, remove, list)
 * - Verification requirement validation
 * - Production mode transition support
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D03-FR-001.3 Google SSO Authentication
 * @see Requirements 1.1, 1.2, 2.5, 4.1
 */
interface GoogleOAuthVerificationServiceInterface
{
    /**
     * Get current OAuth verification status
     *
     * @return string One of: 'verified', 'pending', 'testing', 'rejected'
     */
    public function getVerificationStatus(): string;

    /**
     * Check if OAuth app is in testing mode
     *
     * @return bool True if app is in testing mode (not verified)
     */
    public function isInTestingMode(): bool;

    /**
     * Check if OAuth app is in production mode (verified)
     *
     * @return bool True if app is verified and in production mode
     */
    public function isInProductionMode(): bool;

    /**
     * Add a test user to the OAuth consent screen
     *
     * @param  string  $email  The email address to add as test user
     * @return bool True if user was added successfully
     */
    public function addTestUser(string $email): bool;

    /**
     * Remove a test user from the OAuth consent screen
     *
     * @param  string  $email  The email address to remove
     * @return bool True if user was removed successfully
     */
    public function removeTestUser(string $email): bool;

    /**
     * Get list of all test users
     *
     * @return array<string> List of test user email addresses
     */
    public function getTestUsers(): array;

    /**
     * Check if a user can authenticate based on verification status
     *
     * In testing mode, only test users can authenticate.
     * In production mode, any @motac.gov.my user can authenticate.
     *
     * @param  string  $email  The email address to check
     * @return bool True if user can authenticate
     */
    public function canUserAuthenticate(string $email): bool;

    /**
     * Get verification requirements for Google OAuth
     *
     * @return array<string, mixed> List of requirements and their status
     */
    public function getVerificationRequirements(): array;

    /**
     * Check if a specific email is a registered test user
     *
     * @param  string  $email  The email address to check
     * @return bool True if email is a test user
     */
    public function isTestUser(string $email): bool;

    /**
     * Get the maximum number of test users allowed
     *
     * @return int Maximum test user count (Google's limit is 100)
     */
    public function getMaxTestUsers(): int;

    /**
     * Get current test user count
     *
     * @return int Number of registered test users
     */
    public function getTestUserCount(): int;

    /**
     * Check if test user limit has been reached
     *
     * @return bool True if at maximum test user capacity
     */
    public function isTestUserLimitReached(): bool;

    /**
     * Get verification status details for admin display
     *
     * @return array{status: string, test_users_count: int, max_test_users: int, can_add_users: bool, requirements: array<string, mixed>}
     */
    public function getVerificationDetails(): array;
}
