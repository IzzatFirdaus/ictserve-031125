<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * Two-Factor Authentication Service
 *
 * Manages TOTP-based two-factor authentication for superuser accounts.
 * Provides setup, verification, backup codes, and recovery mechanisms.
 *
 * Requirements: 17.3, D03-FR-017.3
 *
 * @see D04 §11.1 Security enhancements
 */
class TwoFactorAuthService
{
    private Google2FA $google2fa;

    private const BACKUP_CODES_COUNT = 8;

    private const RECOVERY_WINDOW = 1; // Allow 1 time step tolerance

    public function __construct()
    {
        $this->google2fa = new Google2FA;
    }

    /**
     * Generate secret key for 2FA setup
     */
    public function generateSecretKey(): string
    {
        return $this->google2fa->generateSecretKey();
    }

    /**
     * Generate QR code URL for 2FA setup
     */
    public function generateQrCodeUrl(User $user, string $secretKey): string
    {
        $companyName = config('app.name', 'ICTServe');
        $companyEmail = $user->email;

        return $this->google2fa->getQRCodeUrl(
            $companyName,
            $companyEmail,
            $secretKey
        );
    }

    /**
     * Enable 2FA for user
     *
     * @return array<string, mixed>
     */
    public function enable2FA(User $user, string $secretKey, string $verificationCode): array
    {
        // Verify the code before enabling
        if (! $this->verifyCode($secretKey, $verificationCode)) {
            return [
                'success' => false,
                'message' => 'Invalid verification code. Please try again.',
            ];
        }

        // Generate backup codes
        $backupCodes = $this->generateBackupCodes();

        // Save 2FA settings
        $user->update([
            'two_factor_secret' => Crypt::encryptString($secretKey),
            'two_factor_backup_codes' => Crypt::encryptString(json_encode($backupCodes)),
            'two_factor_enabled' => true,
            'two_factor_enabled_at' => now(),
        ]);

        // Log the security event
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['action' => '2fa_enabled'])
            ->log('Two-factor authentication enabled');

        return [
            'success' => true,
            'backup_codes' => $backupCodes,
            'message' => 'Two-factor authentication has been enabled successfully.',
        ];
    }

    /**
     * Disable 2FA for user
     *
     * @return array<string, mixed>
     */
    public function disable2FA(User $user, string $verificationCode): array
    {
        if (! $user->two_factor_enabled) {
            return [
                'success' => false,
                'message' => 'Two-factor authentication is not enabled.',
            ];
        }

        // Verify current code or backup code
        if (! $this->verifyUserCode($user, $verificationCode)) {
            return [
                'success' => false,
                'message' => 'Invalid verification code. Please try again.',
            ];
        }

        // Disable 2FA
        $user->update([
            'two_factor_secret' => null,
            'two_factor_backup_codes' => null,
            'two_factor_enabled' => false,
            'two_factor_enabled_at' => null,
        ]);

        // Log the security event
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['action' => '2fa_disabled'])
            ->log('Two-factor authentication disabled');

        return [
            'success' => true,
            'message' => 'Two-factor authentication has been disabled.',
        ];
    }

    /**
     * Verify 2FA code for user
     */
    public function verifyUserCode(User $user, string $code): bool
    {
        if (! $user->two_factor_enabled || ! $user->two_factor_secret) {
            return false;
        }

        $secretKey = Crypt::decryptString($user->two_factor_secret);

        // First try TOTP verification
        if ($this->verifyCode($secretKey, $code)) {
            return true;
        }

        // If TOTP fails, try backup codes
        return $this->verifyBackupCode($user, $code);
    }

    /**
     * Verify TOTP code
     */
    public function verifyCode(string $secretKey, string $code): bool
    {
        $timestamp = $this->google2fa->getCurrentTimestamp();

        // Check current window and previous/next windows for clock drift
        for ($i = -self::RECOVERY_WINDOW; $i <= self::RECOVERY_WINDOW; $i++) {
            $testTimestamp = $timestamp + ($i * $this->google2fa->getKeyRegeneration());

            if ($this->google2fa->verifyKeyNewer($secretKey, $code, $testTimestamp)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode(User $user, string $code): bool
    {
        if (! $user->two_factor_backup_codes) {
            return false;
        }

        $backupCodes = json_decode(Crypt::decryptString($user->two_factor_backup_codes), true);

        if (! is_array($backupCodes)) {
            return false;
        }

        // Check if code exists and hasn't been used
        $codeIndex = array_search($code, $backupCodes, true);

        if ($codeIndex === false) {
            return false;
        }

        // Mark code as used by removing it
        unset($backupCodes[$codeIndex]);

        // Update user's backup codes
        $user->update([
            'two_factor_backup_codes' => Crypt::encryptString(json_encode(array_values($backupCodes))),
        ]);

        // Log backup code usage
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'action' => 'backup_code_used',
                'remaining_codes' => count($backupCodes),
            ])
            ->log('Two-factor authentication backup code used');

        return true;
    }

    /**
     * Generate new backup codes
     *
     * @return array<string>
     */
    public function regenerateBackupCodes(User $user): array
    {
        if (! $user->two_factor_enabled) {
            return [];
        }

        $backupCodes = $this->generateBackupCodes();

        $user->update([
            'two_factor_backup_codes' => Crypt::encryptString(json_encode($backupCodes)),
        ]);

        // Log backup code regeneration
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties(['action' => 'backup_codes_regenerated'])
            ->log('Two-factor authentication backup codes regenerated');

        return $backupCodes;
    }

    /**
     * Get remaining backup codes count
     */
    public function getRemainingBackupCodesCount(User $user): int
    {
        if (! $user->two_factor_backup_codes) {
            return 0;
        }

        $backupCodes = json_decode(Crypt::decryptString($user->two_factor_backup_codes), true);

        return is_array($backupCodes) ? count($backupCodes) : 0;
    }

    /**
     * Check if user should be prompted for 2FA setup
     */
    public function shouldPromptFor2FA(User $user): bool
    {
        // Only prompt superusers who haven't enabled 2FA
        return $user->hasRole('superuser') && ! $user->two_factor_enabled;
    }

    /**
     * Rate limit 2FA attempts
     */
    public function isRateLimited(User $user): bool
    {
        $key = "2fa_attempts:{$user->id}";
        $attempts = Cache::get($key, 0);

        return $attempts >= 5; // Max 5 attempts per hour
    }

    /**
     * Record failed 2FA attempt
     */
    public function recordFailedAttempt(User $user): void
    {
        $key = "2fa_attempts:{$user->id}";
        $attempts = Cache::get($key, 0);

        Cache::put($key, $attempts + 1, now()->addHour());

        // Log failed attempt
        activity()
            ->performedOn($user)
            ->causedBy($user)
            ->withProperties([
                'action' => '2fa_failed_attempt',
                'attempts' => $attempts + 1,
            ])
            ->log('Two-factor authentication failed attempt');
    }

    /**
     * Clear failed attempts
     */
    public function clearFailedAttempts(User $user): void
    {
        $key = "2fa_attempts:{$user->id}";
        Cache::forget($key);
    }

    /**
     * Generate backup codes
     *
     * @return array<string>
     */
    private function generateBackupCodes(): array
    {
        $codes = [];

        for ($i = 0; $i < self::BACKUP_CODES_COUNT; $i++) {
            $codes[] = strtoupper(Str::random(8));
        }

        return $codes;
    }

    /**
     * Get 2FA setup instructions
     *
     * @return array<string, string>
     */
    public function getSetupInstructions(): array
    {
        return [
            'step1' => 'Install a TOTP authenticator app like Google Authenticator, Authy, or Microsoft Authenticator on your mobile device.',
            'step2' => 'Scan the QR code below with your authenticator app, or manually enter the secret key.',
            'step3' => 'Enter the 6-digit verification code from your authenticator app to complete setup.',
            'step4' => 'Save the backup codes in a secure location. You can use them to access your account if you lose your device.',
        ];
    }
}
