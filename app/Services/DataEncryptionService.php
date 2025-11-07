<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * Data Encryption Service
 *
 * Provides AES-256 encryption for sensitive data at rest, token encryption,
 * and secure data handling for PDPA compliance.
 *
 * Requirements: 17.4, D03-FR-017.4
 *
 * @see D04 §11.2 Data encryption
 */
class DataEncryptionService
{
    private const SENSITIVE_FIELDS = [
        'ic_number',
        'phone_number',
        'personal_details',
        'approval_token',
        'two_factor_secret',
        'two_factor_backup_codes',
    ];

    /**
     * Encrypt sensitive data
     *
     * @param  mixed  $data
     */
    public function encrypt($data): string
    {
        if (is_null($data)) {
            return '';
        }

        if (is_array($data) || is_object($data)) {
            $data = json_encode($data);
        }

        return Crypt::encryptString((string) $data);
    }

    /**
     * Decrypt sensitive data
     *
     * @return mixed
     */
    public function decrypt(string $encryptedData)
    {
        if (empty($encryptedData)) {
            return null;
        }

        try {
            $decrypted = Crypt::decryptString($encryptedData);

            // Try to decode as JSON first
            $jsonDecoded = json_decode($decrypted, true);
            if (json_last_error() === JSON_ERROR_NONE) {
                return $jsonDecoded;
            }

            return $decrypted;
        } catch (\Exception $e) {
            \Log::error('Failed to decrypt data', [
                'error' => $e->getMessage(),
                'data_length' => strlen($encryptedData),
            ]);

            return null;
        }
    }

    /**
     * Generate secure approval token
     *
     * @param  array<string, mixed>  $payload
     */
    public function generateApprovalToken(array $payload, int $expiryHours = 168): string
    {
        $tokenData = [
            'payload' => $payload,
            'expires_at' => now()->addHours($expiryHours)->timestamp,
            'nonce' => Str::random(32),
        ];

        return $this->encrypt($tokenData);
    }

    /**
     * Verify and decode approval token
     *
     * @return array<string, mixed>|null
     */
    public function verifyApprovalToken(string $token): ?array
    {
        $tokenData = $this->decrypt($token);

        if (! is_array($tokenData) || ! isset($tokenData['expires_at'], $tokenData['payload'])) {
            return null;
        }

        // Check if token has expired
        if (time() > $tokenData['expires_at']) {
            return null;
        }

        return $tokenData['payload'];
    }

    /**
     * Hash sensitive data for searching
     */
    public function hashForSearch(string $data): string
    {
        // Use a consistent salt for searchable hashes
        $salt = config('app.key');

        return hash_hmac('sha256', strtolower(trim($data)), $salt);
    }

    /**
     * Encrypt model attributes
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function encryptModelAttributes(array $attributes): array
    {
        $encrypted = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, self::SENSITIVE_FIELDS) && ! is_null($value)) {
                $encrypted[$key] = $this->encrypt($value);

                // Create searchable hash for certain fields
                if (in_array($key, ['ic_number', 'phone_number'])) {
                    $encrypted[$key.'_hash'] = $this->hashForSearch((string) $value);
                }
            } else {
                $encrypted[$key] = $value;
            }
        }

        return $encrypted;
    }

    /**
     * Decrypt model attributes
     *
     * @param  array<string, mixed>  $attributes
     * @return array<string, mixed>
     */
    public function decryptModelAttributes(array $attributes): array
    {
        $decrypted = [];

        foreach ($attributes as $key => $value) {
            if (in_array($key, self::SENSITIVE_FIELDS) && ! is_null($value)) {
                $decrypted[$key] = $this->decrypt((string) $value);
            } else {
                $decrypted[$key] = $value;
            }
        }

        return $decrypted;
    }

    /**
     * Generate secure random password
     */
    public function generateSecurePassword(int $length = 16): string
    {
        $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
        $password = '';

        for ($i = 0; $i < $length; $i++) {
            $password .= $characters[random_int(0, strlen($characters) - 1)];
        }

        return $password;
    }

    /**
     * Validate password strength
     *
     * @return array<string, mixed>
     */
    public function validatePasswordStrength(string $password): array
    {
        $errors = [];
        $score = 0;

        // Length check
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        } else {
            $score += 1;
        }

        // Uppercase check
        if (! preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        } else {
            $score += 1;
        }

        // Lowercase check
        if (! preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        } else {
            $score += 1;
        }

        // Number check
        if (! preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        } else {
            $score += 1;
        }

        // Special character check
        if (! preg_match('/[!@#$%^&*(),.?":{}|<>]/', $password)) {
            $errors[] = 'Password must contain at least one special character';
        } else {
            $score += 1;
        }

        // Common password check
        $commonPasswords = [
            'password', '123456', 'password123', 'admin', 'qwerty',
            'letmein', 'welcome', 'monkey', '1234567890',
        ];

        if (in_array(strtolower($password), $commonPasswords)) {
            $errors[] = 'Password is too common';
            $score = 0;
        }

        $strength = match (true) {
            $score >= 5 => 'very_strong',
            $score >= 4 => 'strong',
            $score >= 3 => 'medium',
            $score >= 2 => 'weak',
            default => 'very_weak',
        };

        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'score' => $score,
            'strength' => $strength,
        ];
    }

    /**
     * Securely wipe sensitive data from memory
     */
    public function secureWipe(string &$data): void
    {
        if (function_exists('sodium_memzero')) {
            sodium_memzero($data);
        } else {
            // Fallback: overwrite with random data
            $length = strlen($data);
            for ($i = 0; $i < 3; $i++) {
                $data = str_repeat(chr(random_int(0, 255)), $length);
            }
            $data = '';
        }
    }

    /**
     * Create encrypted backup of sensitive data
     *
     * @param  array<string, mixed>  $data
     */
    public function createEncryptedBackup(array $data, string $backupKey): string
    {
        $serialized = serialize($data);
        $compressed = gzcompress($serialized, 9);

        // Use backup key for additional encryption layer
        $encrypted = openssl_encrypt(
            $compressed,
            'AES-256-GCM',
            hash('sha256', $backupKey),
            OPENSSL_RAW_DATA,
            $iv = random_bytes(12),
            $tag
        );

        return base64_encode($iv.$tag.$encrypted);
    }

    /**
     * Restore encrypted backup
     *
     * @return array<string, mixed>|null
     */
    public function restoreEncryptedBackup(string $encryptedBackup, string $backupKey): ?array
    {
        try {
            $data = base64_decode($encryptedBackup);
            $iv = substr($data, 0, 12);
            $tag = substr($data, 12, 16);
            $encrypted = substr($data, 28);

            $compressed = openssl_decrypt(
                $encrypted,
                'AES-256-GCM',
                hash('sha256', $backupKey),
                OPENSSL_RAW_DATA,
                $iv,
                $tag
            );

            if ($compressed === false) {
                return null;
            }

            $serialized = gzuncompress($compressed);
            if ($serialized === false) {
                return null;
            }

            return unserialize($serialized);
        } catch (\Exception $e) {
            \Log::error('Failed to restore encrypted backup', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Get encryption status for model
     *
     * @return array<string, mixed>
     */
    public function getEncryptionStatus(object $model): array
    {
        $status = [
            'encrypted_fields' => [],
            'unencrypted_sensitive_fields' => [],
            'total_sensitive_fields' => 0,
            'encryption_percentage' => 0,
        ];

        $attributes = $model->getAttributes();

        foreach (self::SENSITIVE_FIELDS as $field) {
            if (array_key_exists($field, $attributes)) {
                $status['total_sensitive_fields']++;

                if (! is_null($attributes[$field])) {
                    // Try to decrypt to check if it's encrypted
                    try {
                        $this->decrypt($attributes[$field]);
                        $status['encrypted_fields'][] = $field;
                    } catch (\Exception $e) {
                        $status['unencrypted_sensitive_fields'][] = $field;
                    }
                }
            }
        }

        if ($status['total_sensitive_fields'] > 0) {
            $status['encryption_percentage'] = round(
                (count($status['encrypted_fields']) / $status['total_sensitive_fields']) * 100,
                1
            );
        }

        return $status;
    }
}
