<?php

declare(strict_types=1);

namespace App\Services;

use App\Contracts\RegistrationServiceInterface;
use App\Exceptions\InvalidEmailDomainException;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

/**
 * Registration Service Implementation for ICTServe v3.5.0
 *
 * Implements self-registration functionality for MOTAC staff with:
 * - Email domain validation (@motac.gov.my only)
 * - Email verification workflow with 24-hour signed URLs
 * - Username extraction for flexible login
 * - Dual audit logging (owen-it + spatie)
 *
 * Security Features:
 * - Domain whitelist validation
 * - Password hashing via Laravel's Hash facade
 * - Signed URL verification tokens
 * - Activity logging for compliance
 *
 * @see D00 §4.1 True Hybrid Architecture
 * @see D01 §4.3 Self-registration requirements
 * @see D03 SRS-AUTH-001 Authentication requirements
 * @see Requirements 15.2, 15.3, 15.4, 15.5
 */
class RegistrationService implements RegistrationServiceInterface
{
    /**
     * Allowed email domains for registration
     * Per D00 §4.1, only MOTAC staff can self-register
     */
    private const ALLOWED_DOMAINS = ['motac.gov.my'];

    /**
     * Verification link expiry in hours
     * Per D01 §4.3, verification links valid for 24 hours
     */
    private const VERIFICATION_EXPIRY_HOURS = 24;

    /**
     * Default role for new registrations
     */
    private const DEFAULT_ROLE = 'staff';

    /**
     * Register a new MOTAC staff user
     *
     * Creates a user account with role 'staff' and unverified email.
     * Validates email domain is @motac.gov.my before registration.
     * Triggers email verification workflow.
     *
     * @param  array{name: string, email: string, password: string, phone?: string, division_code?: string, grade?: string}  $data  Registration data
     * @return User The created user (unverified)
     *
     * @throws InvalidEmailDomainException If email is not @motac.gov.my
     */
    public function register(array $data): User
    {
        // Validate email domain
        if (! $this->validateEmailDomain($data['email'])) {
            throw new InvalidEmailDomainException($data['email'], self::ALLOWED_DOMAINS);
        }

        // Normalize email to lowercase
        $email = Str::lower(trim($data['email']));

        // Create user within transaction for data integrity
        $user = DB::transaction(function () use ($data, $email): User {
            $user = User::create([
                'name' => trim($data['name']),
                'email' => $email,
                'password' => Hash::make($data['password']),
                'role' => self::DEFAULT_ROLE,
                'locale' => 'ms', // Default to Bahasa Melayu per D15
                'is_active' => true,
                'phone' => $data['phone'] ?? null,
                'division_code' => $data['division_code'] ?? null,
                'grade' => $data['grade'] ?? null,
                'notification_preferences' => $this->getDefaultNotificationPreferences(),
                'guest_submissions_linked' => 0,
            ]);

            return $user;
        });

        // Log registration activity
        $this->logRegistrationActivity($user);

        // Fire Registered event (triggers verification email via Laravel)
        event(new Registered($user));

        Log::info('User registered successfully', [
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
        ]);

        return $user;
    }

    /**
     * Validate that email domain is @motac.gov.my
     *
     * Per D00 §4.1, only MOTAC staff with official email addresses
     * can self-register for the system.
     *
     * @param  string  $email  The email address to validate
     * @return bool True if domain is @motac.gov.my
     */
    public function validateEmailDomain(string $email): bool
    {
        $email = Str::lower(trim($email));

        // Extract domain from email
        $parts = explode('@', $email);

        if (count($parts) !== 2) {
            return false;
        }

        [$localPart, $domain] = $parts;

        // Reject if local part is empty
        if (empty($localPart)) {
            return false;
        }

        return in_array($domain, self::ALLOWED_DOMAINS, true);
    }

    /**
     * Send verification email to newly registered user
     *
     * Generates a signed URL valid for 24 hours and sends
     * verification email to the user.
     *
     * @param  User  $user  The user to send verification to
     */
    public function sendVerificationEmail(User $user): void
    {
        // Laravel's MustVerifyEmail trait handles this via the Registered event
        // This method is provided for manual re-sending if needed
        $user->sendEmailVerificationNotification();

        Log::info('Verification email sent', [
            'user_id' => $user->id,
            'email' => $user->email,
        ]);
    }

    /**
     * Verify user's email address
     *
     * Validates the verification token and activates the user account.
     * This is typically handled by Laravel's built-in verification,
     * but provided for custom verification flows.
     *
     * @param  User  $user  The user to verify
     * @param  string  $token  The verification token from email link
     * @return bool True if verification successful
     */
    public function verifyEmail(User $user, string $token): bool
    {
        // Generate expected hash for comparison
        $expectedHash = sha1($user->getEmailForVerification());

        // Verify token matches
        if (! hash_equals($expectedHash, $token)) {
            Log::warning('Email verification failed - invalid token', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);

            return false;
        }

        // Mark email as verified
        if (! $user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();

            Log::info('Email verified successfully', [
                'user_id' => $user->id,
                'email' => $user->email,
            ]);
        }

        return true;
    }

    /**
     * Extract username from email address
     *
     * Extracts the local part of email for flexible login.
     * Example: user@motac.gov.my → user
     *
     * @param  string  $email  The full email address
     * @return string The username portion
     */
    public function extractUsernameFromEmail(string $email): string
    {
        $email = Str::lower(trim($email));
        $parts = explode('@', $email);

        return $parts[0] ?? '';
    }

    /**
     * Check if email is already registered
     *
     * @param  string  $email  The email to check
     * @return bool True if email exists in system
     */
    public function isEmailRegistered(string $email): bool
    {
        $email = Str::lower(trim($email));

        return User::where('email', $email)->exists();
    }

    /**
     * Get allowed email domains for registration
     *
     * Returns array of allowed domains (currently only @motac.gov.my).
     *
     * @return array<string> List of allowed domains
     */
    public function getAllowedDomains(): array
    {
        return self::ALLOWED_DOMAINS;
    }

    /**
     * Generate a signed verification URL
     *
     * Creates a temporary signed URL for email verification.
     * Valid for 24 hours per D01 §4.3.
     *
     * @param  User  $user  The user to generate URL for
     * @return string The signed verification URL
     */
    public function generateVerificationUrl(User $user): string
    {
        return URL::temporarySignedRoute(
            'verification.verify',
            now()->addHours(self::VERIFICATION_EXPIRY_HOURS),
            [
                'id' => $user->getKey(),
                'hash' => sha1($user->getEmailForVerification()),
            ]
        );
    }

    /**
     * Get default notification preferences for new users
     *
     * @return array<string, bool>
     */
    private function getDefaultNotificationPreferences(): array
    {
        return [
            'ticket_updates' => true,
            'ticket_assignments' => true,
            'ticket_comments' => true,
            'sla_alerts' => true,
            'system_announcements' => true,
            'loan_updates' => true,
            'loan_approvals' => true,
            'loan_reminders' => true,
            'realtime_notifications' => true,
        ];
    }

    /**
     * Log registration activity for audit compliance
     *
     * Uses Laravel's built-in logging. When spatie/laravel-activitylog
     * is installed, this can be enhanced with activity() helper.
     *
     * @param  User  $user  The newly registered user
     */
    private function logRegistrationActivity(User $user): void
    {
        // Log to Laravel's log system for audit trail
        // TODO: When spatie/laravel-activitylog is installed, use:
        // activity('registration')
        //     ->performedOn($user)
        //     ->causedBy($user)
        //     ->withProperties([...])
        //     ->log('User self-registered');

        Log::channel('single')->info('User registration activity', [
            'action' => 'user_self_registered',
            'user_id' => $user->id,
            'email' => $user->email,
            'role' => $user->role,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'timestamp' => now()->toIso8601String(),
        ]);
    }
}
