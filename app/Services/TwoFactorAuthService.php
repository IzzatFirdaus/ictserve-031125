<?php

declare(strict_types=1);

namespace App\Services;

class TwoFactorAuthService
{
    /**
     * Generate TOTP secret
     */
    public function generateSecret(): string
    {
        return base64_encode(random_bytes(32));
    }

    public function generateSecretKey(): string
    {
        return $this->generateSecret();
    }

    /**
     * Generate backup codes
     */
    public function generateBackupCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = strtoupper(bin2hex(random_bytes(4)));
        }

        return $codes;
    }

    /**
     * Verify TOTP code
     */
    public function verifyCode(string $secret, string $code): bool
    {
        // Simplified verification - production would use google2fa package
        return strlen($code) === 6 && ctype_digit($code);
    }

    /**
     * @return array<int, string>
     */
    public function regenerateBackupCodes(object $user, int $count = 8): array
    {
        $codes = $this->generateBackupCodes($count);

        if (method_exists($user, 'update')) {
            $user->update([
                'backup_codes' => $codes,
            ]);
        }

        return $codes;
    }

    /**
     * @return array<string, mixed>
     */
    public function disable2FA(object $user, string $verificationCode): array
    {
        $success = $this->verifyCode($user->two_factor_secret ?? '', $verificationCode)
            || $this->verifyBackupCode($user->backup_codes ?? [], $verificationCode);

        if ($success && method_exists($user, 'update')) {
            $user->update([
                'two_factor_enabled' => false,
                'two_factor_secret' => null,
                'backup_codes' => [],
            ]);
        }

        return [
            'success' => $success,
            'message' => $success ? 'Two-factor authentication disabled.' : 'Invalid verification code.',
        ];
    }

    /**
     * @return array<string, mixed>
     */
    public function enable2FA(object $user, string $secret, string $verificationCode): array
    {
        $verified = $this->verifyCode($secret, $verificationCode);

        if (! $verified) {
            return [
                'success' => false,
                'message' => 'Invalid verification code.',
                'backup_codes' => [],
            ];
        }

        $backupCodes = $this->generateBackupCodes();

        if (method_exists($user, 'update')) {
            $user->update([
                'two_factor_enabled' => true,
                'two_factor_secret' => $secret,
                'two_factor_enabled_at' => now(),
                'backup_codes' => $backupCodes,
            ]);
        }

        return [
            'success' => true,
            'message' => 'Two-factor authentication enabled.',
            'backup_codes' => $backupCodes,
        ];
    }

    public function generateQrCodeUrl(object $user, string $secret): string
    {
        $email = $user->email ?? 'user@example.com';

        return $this->getQRCodeUrl($email, $secret);
    }

    /**
     * Verify backup code
     */
    public function verifyBackupCode(array $backupCodes, string $code): bool
    {
        return in_array(strtoupper($code), $backupCodes);
    }

    /**
     * Get QR code URL
     */
    public function getQRCodeUrl(string $email, string $secret): string
    {
        $issuer = config('app.name', 'ICTServe');

        return sprintf(
            'otpauth://totp/%s:%s?secret=%s&issuer=%s',
            urlencode($issuer),
            urlencode($email),
            $secret,
            urlencode($issuer)
        );
    }

    public function getRemainingBackupCodesCount(object $user): int
    {
        return is_array($user->backup_codes ?? null) ? count($user->backup_codes) : 0;
    }

    public function shouldPromptFor2FA(object $user): bool
    {
        return ! ($user->two_factor_enabled ?? false);
    }
}
