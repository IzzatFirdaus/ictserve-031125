<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\GoogleOAuthVerificationServiceInterface;
use App\Models\GoogleOAuthVerification;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

/**
 * Google OAuth Verification Service Implementation for ICTServe v3.6.1
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
class GoogleOAuthVerificationService implements GoogleOAuthVerificationServiceInterface
{
    /**
     * Verification status constants
     */
    public const STATUS_VERIFIED = 'verified';

    public const STATUS_PENDING = 'pending';

    public const STATUS_TESTING = 'testing';

    public const STATUS_REJECTED = 'rejected';

    /**
     * Maximum test users allowed by Google (OAuth consent screen limit)
     */
    private const MAX_TEST_USERS = 100;

    /**
     * Cache key for verification status
     */
    private const CACHE_KEY_STATUS = 'google_oauth_verification_status';

    /**
     * Cache key for test users
     */
    private const CACHE_KEY_TEST_USERS = 'google_oauth_test_users';

    /**
     * Cache TTL in seconds (15 minutes)
     */
    private const CACHE_TTL = 900;

    /**
     * Allowed email domain for MOTAC staff
     */
    private const ALLOWED_DOMAIN = 'motac.gov.my';

    /**
     * Get current OAuth verification status
     */
    public function getVerificationStatus(): string
    {
        // Check cache first
        $cached = Cache::get(self::CACHE_KEY_STATUS);
        if ($cached !== null) {
            return $cached;
        }

        // Check environment configuration
        $configuredStatus = config('services.google.oauth_verification_status');
        if ($configuredStatus && in_array($configuredStatus, [
            self::STATUS_VERIFIED,
            self::STATUS_PENDING,
            self::STATUS_TESTING,
            self::STATUS_REJECTED,
        ], true)) {
            Cache::put(self::CACHE_KEY_STATUS, $configuredStatus, self::CACHE_TTL);

            return $configuredStatus;
        }

        // Check database for verification record
        $verification = $this->getVerificationRecord();
        $status = $verification?->verification_status ?? self::STATUS_TESTING;

        Cache::put(self::CACHE_KEY_STATUS, $status, self::CACHE_TTL);

        return $status;
    }

    /**
     * Check if OAuth app is in testing mode
     */
    public function isInTestingMode(): bool
    {
        return $this->getVerificationStatus() === self::STATUS_TESTING;
    }

    /**
     * Check if OAuth app is in production mode (verified)
     */
    public function isInProductionMode(): bool
    {
        return $this->getVerificationStatus() === self::STATUS_VERIFIED;
    }

    /**
     * Add a test user to the OAuth consent screen
     */
    public function addTestUser(string $email): bool
    {
        $email = Str::lower(trim($email));

        // Validate email format
        if (! filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Log::warning('Invalid email format for test user', ['email' => $email]);

            return false;
        }

        // Validate domain
        if (! $this->isMotacEmail($email)) {
            Log::warning('Test user must be @motac.gov.my', ['email' => $email]);

            return false;
        }

        // Check if limit reached
        if ($this->isTestUserLimitReached()) {
            Log::warning('Test user limit reached', [
                'current_count' => $this->getTestUserCount(),
                'max_users' => self::MAX_TEST_USERS,
            ]);

            return false;
        }

        // Check if already a test user
        if ($this->isTestUser($email)) {
            Log::info('User already registered as test user', ['email' => $email]);

            return true;
        }

        // Add to database
        $verification = $this->getOrCreateVerificationRecord();
        $testUsers = $verification->test_users ?? [];
        $testUsers[] = $email;
        $verification->test_users = array_unique($testUsers);
        $verification->save();

        // Clear cache
        $this->clearTestUsersCache();

        Log::info('Test user added', ['email' => $email]);

        // Log activity for audit
        activity('google_oauth_verification')
            ->withProperties([
                'action' => 'add_test_user',
                'email' => $email,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('Test user added to OAuth consent screen');

        return true;
    }

    /**
     * Remove a test user from the OAuth consent screen
     */
    public function removeTestUser(string $email): bool
    {
        $email = Str::lower(trim($email));

        $verification = $this->getVerificationRecord();
        if (! $verification) {
            return false;
        }

        $testUsers = $verification->test_users ?? [];
        $originalCount = count($testUsers);

        $testUsers = array_values(array_filter($testUsers, fn ($user) => $user !== $email));

        if (count($testUsers) === $originalCount) {
            Log::info('Test user not found for removal', ['email' => $email]);

            return false;
        }

        $verification->test_users = $testUsers;
        $verification->save();

        // Clear cache
        $this->clearTestUsersCache();

        Log::info('Test user removed', ['email' => $email]);

        // Log activity for audit
        activity('google_oauth_verification')
            ->withProperties([
                'action' => 'remove_test_user',
                'email' => $email,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('Test user removed from OAuth consent screen');

        return true;
    }

    /**
     * Get list of all test users
     */
    public function getTestUsers(): array
    {
        // Check cache first
        $cached = Cache::get(self::CACHE_KEY_TEST_USERS);
        if ($cached !== null) {
            return $cached;
        }

        // Check environment configuration
        $configuredUsers = config('services.google.oauth_test_users');
        if (is_array($configuredUsers) && ! empty($configuredUsers)) {
            $users = array_map(fn ($email) => Str::lower(trim($email)), $configuredUsers);
            Cache::put(self::CACHE_KEY_TEST_USERS, $users, self::CACHE_TTL);

            return $users;
        }

        // Get from database
        $verification = $this->getVerificationRecord();
        $users = $verification?->test_users ?? [];

        Cache::put(self::CACHE_KEY_TEST_USERS, $users, self::CACHE_TTL);

        return $users;
    }

    /**
     * Check if a user can authenticate based on verification status
     */
    public function canUserAuthenticate(string $email): bool
    {
        $email = Str::lower(trim($email));

        // Validate domain first
        if (! $this->isMotacEmail($email)) {
            return false;
        }

        // In production mode, any @motac.gov.my user can authenticate
        if ($this->isInProductionMode()) {
            return true;
        }

        // In testing mode, only test users can authenticate
        if ($this->isInTestingMode()) {
            return $this->isTestUser($email);
        }

        // Pending or rejected status - check if test user
        return $this->isTestUser($email);
    }

    /**
     * Get verification requirements for Google OAuth
     */
    public function getVerificationRequirements(): array
    {
        return [
            'privacy_policy' => [
                'required' => true,
                'description' => 'Privacy policy URL must be publicly accessible',
                'status' => $this->checkPrivacyPolicyStatus(),
            ],
            'terms_of_service' => [
                'required' => true,
                'description' => 'Terms of service URL must be publicly accessible',
                'status' => $this->checkTermsOfServiceStatus(),
            ],
            'domain_verification' => [
                'required' => true,
                'description' => 'Domain ownership must be verified in Google Search Console',
                'status' => $this->checkDomainVerificationStatus(),
            ],
            'authorized_domains' => [
                'required' => true,
                'description' => 'Authorized domains must be configured',
                'status' => $this->checkAuthorizedDomainsStatus(),
            ],
            'app_homepage' => [
                'required' => true,
                'description' => 'Application homepage URL must be accessible',
                'status' => $this->checkAppHomepageStatus(),
            ],
            'scopes_justification' => [
                'required' => true,
                'description' => 'Justification for requested OAuth scopes',
                'status' => $this->checkScopesJustificationStatus(),
            ],
        ];
    }

    /**
     * Check if a specific email is a registered test user
     */
    public function isTestUser(string $email): bool
    {
        $email = Str::lower(trim($email));
        $testUsers = $this->getTestUsers();

        return in_array($email, $testUsers, true);
    }

    /**
     * Get the maximum number of test users allowed
     */
    public function getMaxTestUsers(): int
    {
        return self::MAX_TEST_USERS;
    }

    /**
     * Get current test user count
     */
    public function getTestUserCount(): int
    {
        return count($this->getTestUsers());
    }

    /**
     * Check if test user limit has been reached
     */
    public function isTestUserLimitReached(): bool
    {
        return $this->getTestUserCount() >= self::MAX_TEST_USERS;
    }

    /**
     * Get verification status details for admin display
     */
    public function getVerificationDetails(): array
    {
        return [
            'status' => $this->getVerificationStatus(),
            'status_label' => $this->getStatusLabel(),
            'is_production_mode' => $this->isInProductionMode(),
            'is_testing_mode' => $this->isInTestingMode(),
            'test_users_count' => $this->getTestUserCount(),
            'max_test_users' => self::MAX_TEST_USERS,
            'can_add_users' => ! $this->isTestUserLimitReached(),
            'requirements' => $this->getVerificationRequirements(),
            'last_checked' => now()->toIso8601String(),
        ];
    }

    /**
     * Get human-readable status label
     */
    public function getStatusLabel(): string
    {
        return match ($this->getVerificationStatus()) {
            self::STATUS_VERIFIED => __('auth.oauth_status.verified'),
            self::STATUS_PENDING => __('auth.oauth_status.pending'),
            self::STATUS_TESTING => __('auth.oauth_status.testing'),
            self::STATUS_REJECTED => __('auth.oauth_status.rejected'),
            default => __('auth.oauth_status.unknown'),
        };
    }

    /**
     * Set verification status (for admin use)
     */
    public function setVerificationStatus(string $status): bool
    {
        if (! in_array($status, [
            self::STATUS_VERIFIED,
            self::STATUS_PENDING,
            self::STATUS_TESTING,
            self::STATUS_REJECTED,
        ], true)) {
            return false;
        }

        $verification = $this->getOrCreateVerificationRecord();
        $oldStatus = $verification->verification_status;
        $verification->verification_status = $status;

        if ($status === self::STATUS_VERIFIED && ! $verification->verification_approved_at) {
            $verification->verification_approved_at = now();
        }

        $verification->last_status_check = now();
        $verification->save();

        // Clear cache
        Cache::forget(self::CACHE_KEY_STATUS);

        Log::info('OAuth verification status updated', [
            'old_status' => $oldStatus,
            'new_status' => $status,
        ]);

        // Log activity for audit
        activity('google_oauth_verification')
            ->withProperties([
                'action' => 'status_change',
                'old_status' => $oldStatus,
                'new_status' => $status,
                'ip_address' => request()->ip(),
                'timestamp' => now()->toIso8601String(),
            ])
            ->log('OAuth verification status changed');

        return true;
    }

    /**
     * Clear all verification caches
     */
    public function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_STATUS);
        Cache::forget(self::CACHE_KEY_TEST_USERS);
    }

    /**
     * Clear test users cache
     */
    private function clearTestUsersCache(): void
    {
        Cache::forget(self::CACHE_KEY_TEST_USERS);
    }

    /**
     * Get verification record from database
     */
    private function getVerificationRecord(): ?GoogleOAuthVerification
    {
        $clientId = config('services.google.client_id');
        if (! $clientId) {
            return null;
        }

        return GoogleOAuthVerification::where('client_id', $clientId)->first();
    }

    /**
     * Get or create verification record
     */
    private function getOrCreateVerificationRecord(): GoogleOAuthVerification
    {
        $clientId = config('services.google.client_id');

        return GoogleOAuthVerification::firstOrCreate(
            ['client_id' => $clientId],
            [
                'verification_status' => self::STATUS_TESTING,
                'test_users' => [],
                'verification_documents' => [],
                'quota_limits' => [
                    'daily_requests' => 10000,
                    'per_user_requests' => 100,
                ],
                'last_status_check' => now(),
            ]
        );
    }

    /**
     * Check if email is @motac.gov.my
     */
    private function isMotacEmail(string $email): bool
    {
        $parts = explode('@', $email);
        if (count($parts) !== 2) {
            return false;
        }

        return Str::lower($parts[1]) === self::ALLOWED_DOMAIN;
    }

    /**
     * Check privacy policy status
     */
    private function checkPrivacyPolicyStatus(): string
    {
        $url = config('app.privacy_policy_url');

        return ! empty($url) ? 'configured' : 'missing';
    }

    /**
     * Check terms of service status
     */
    private function checkTermsOfServiceStatus(): string
    {
        $url = config('app.terms_of_service_url');

        return ! empty($url) ? 'configured' : 'missing';
    }

    /**
     * Check domain verification status
     */
    private function checkDomainVerificationStatus(): string
    {
        // This would typically check Google Search Console API
        // For now, return based on configuration
        $verified = config('services.google.domain_verified', false);

        return $verified ? 'verified' : 'pending';
    }

    /**
     * Check authorized domains status
     */
    private function checkAuthorizedDomainsStatus(): string
    {
        $domains = config('services.google.authorized_domains', []);

        return ! empty($domains) ? 'configured' : 'missing';
    }

    /**
     * Check app homepage status
     */
    private function checkAppHomepageStatus(): string
    {
        $url = config('app.url');

        return ! empty($url) ? 'configured' : 'missing';
    }

    /**
     * Check scopes justification status
     */
    private function checkScopesJustificationStatus(): string
    {
        // Check if scopes are documented
        $verification = $this->getVerificationRecord();
        $documents = $verification?->verification_documents ?? [];

        return isset($documents['scopes_justification']) ? 'documented' : 'pending';
    }

    /**
     * Bulk add test users
     *
     * @param  array<string>  $emails  List of email addresses to add
     * @return array{added: int, failed: int, errors: array<string>}
     */
    public function bulkAddTestUsers(array $emails): array
    {
        $added = 0;
        $failed = 0;
        $errors = [];

        foreach ($emails as $email) {
            if ($this->addTestUser($email)) {
                $added++;
            } else {
                $failed++;
                $errors[] = "Failed to add: {$email}";
            }
        }

        return [
            'added' => $added,
            'failed' => $failed,
            'errors' => $errors,
        ];
    }

    /**
     * Get test user limitation message for user feedback
     */
    public function getTestUserLimitationMessage(string $email): string
    {
        if ($this->isInProductionMode()) {
            return '';
        }

        if ($this->isTestUser($email)) {
            return '';
        }

        return __('auth.test_user_required', [
            'email' => $email,
            'status' => $this->getStatusLabel(),
        ]);
    }

    /**
     * Export test users for backup
     *
     * @return array<string, mixed>
     */
    public function exportTestUsers(): array
    {
        return [
            'exported_at' => now()->toIso8601String(),
            'verification_status' => $this->getVerificationStatus(),
            'test_users' => $this->getTestUsers(),
            'count' => $this->getTestUserCount(),
        ];
    }

    /**
     * Import test users from backup
     *
     * @param  array<string>  $emails  List of email addresses to import
     * @return array{imported: int, skipped: int}
     */
    public function importTestUsers(array $emails): array
    {
        $imported = 0;
        $skipped = 0;

        foreach ($emails as $email) {
            if ($this->isTestUser($email)) {
                $skipped++;

                continue;
            }

            if ($this->addTestUser($email)) {
                $imported++;
            } else {
                $skipped++;
            }
        }

        return [
            'imported' => $imported,
            'skipped' => $skipped,
        ];
    }
}
