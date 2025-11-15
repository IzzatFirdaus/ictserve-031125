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
}
