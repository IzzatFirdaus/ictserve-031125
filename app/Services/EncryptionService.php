<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Hash;

/**
 * Encryption Service for ICTServe Security
 *
 * Provides AES-256 encryption for sensitive data at rest,
 * secure token generation, and data validation.
 *
 * @see D03-FR-010.3 Data encryption requirements
 * @see D03-FR-010.4 Security standards
 * @see D11 Technical Design - Security implementation
 */
class EncryptionService
{
    /**
     * Encrypt sensitive data using AES-256
     */
    public function encryptSensitiveData(string $data): string
    {
        try {
            return Crypt::encryptString($data);
        } catch (EncryptException $e) {
            throw new \Exception('Failed to encrypt sensitive data: '.$e->getMessage());
        }
    }

    /**
     * Decrypt sensitive data
     */
    public function decryptSensitiveData(string $encryptedData): string
    {
        try {
            return Crypt::decryptString($encryptedData);
        } catch (DecryptException $e) {
            throw new \Exception('Failed to decrypt sensitive data: '.$e->getMessage());
        }
    }

    /**
     * Generate secure token for email approvals
     */
    public function generateSecureToken(int $length = 64): string
    {
        return bin2hex(random_bytes($length / 2));
    }

    /**
     * Generate secure hash for passwords
     */
    public function hashPassword(string $password): string
    {
        return Hash::make($password);
    }

    /**
     * Verify password against hash
     */
    public function verifyPassword(string $password, string $hash): bool
    {
        return Hash::check($password, $hash);
    }

    /**
     * Generate CSRF-safe token
     */
    public function generateCSRFToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Validate encryption configuration
     */
    public function validateEncryptionConfig(): array
    {
        $results = [
            'cipher' => config('app.cipher'),
            'key_set' => ! empty(config('app.key')),
            'key_length' => strlen(base64_decode(substr(config('app.key'), 7))),
            'encryption_working' => false,
            'hash_working' => false,
        ];

        // Test encryption
        try {
            $testData = 'test_encryption_data';
            $encrypted = $this->encryptSensitiveData($testData);
            $decrypted = $this->decryptSensitiveData($encrypted);
            $results['encryption_working'] = ($testData === $decrypted);
        } catch (\Exception $e) {
            $results['encryption_error'] = $e->getMessage();
        }

        // Test hashing
        try {
            $testPassword = 'test_password';
            $hash = $this->hashPassword($testPassword);
            $results['hash_working'] = $this->verifyPassword($testPassword, $hash);
        } catch (\Exception $e) {
            $results['hash_error'] = $e->getMessage();
        }

        return $results;
    }

    /**
     * Encrypt array data for database storage
     */
    public function encryptArray(array $data): string
    {
        return $this->encryptSensitiveData(json_encode($data));
    }

    /**
     * Decrypt array data from database
     */
    public function decryptArray(string $encryptedData): array
    {
        $decrypted = $this->decryptSensitiveData($encryptedData);

        return json_decode($decrypted, true) ?? [];
    }

    /**
     * Sanitize sensitive data for logging
     */
    public function sanitizeForLogging(array $data, array $sensitiveFields = []): array
    {
        $defaultSensitiveFields = [
            'password',
            'password_confirmation',
            'token',
            'api_key',
            'secret',
            'credit_card',
            'ssn',
            'ic_number',
        ];

        $allSensitiveFields = array_merge($defaultSensitiveFields, $sensitiveFields);

        foreach ($data as $key => $value) {
            if (in_array(strtolower($key), $allSensitiveFields)) {
                $data[$key] = '[REDACTED]';
            } elseif (is_array($value)) {
                $data[$key] = $this->sanitizeForLogging($value, $sensitiveFields);
            }
        }

        return $data;
    }

    /**
     * Generate secure approval token with expiration
     */
    public function generateApprovalToken(): array
    {
        return [
            'token' => $this->generateSecureToken(),
            'expires_at' => now()->addDays(7),
            'created_at' => now(),
        ];
    }

    /**
     * Validate approval token
     */
    public function validateApprovalToken(string $token, string $storedToken, \Carbon\Carbon $expiresAt): bool
    {
        return hash_equals($token, $storedToken) && $expiresAt->isFuture();
    }

    /**
     * Encrypt personal identifiable information (PII)
     */
    public function encryptPII(string $pii): string
    {
        // Add additional security layer for PII
        $salted = hash('sha256', config('app.key')).$pii;

        return $this->encryptSensitiveData($salted);
    }

    /**
     * Decrypt personal identifiable information (PII)
     */
    public function decryptPII(string $encryptedPII): string
    {
        $decrypted = $this->decryptSensitiveData($encryptedPII);
        $salt = hash('sha256', config('app.key'));

        if (str_starts_with($decrypted, $salt)) {
            return substr($decrypted, strlen($salt));
        }

        throw new \Exception('Invalid PII data or corrupted encryption');
    }

    /**
     * Generate secure session token
     */
    public function generateSessionToken(): string
    {
        return hash('sha256', $this->generateSecureToken().microtime(true));
    }

    /**
     * Validate system security configuration
     */
    public function validateSecurityConfig(): array
    {
        return [
            'encryption' => $this->validateEncryptionConfig(),
            'https_enabled' => request()->isSecure(),
            'session_secure' => config('session.secure'),
            'session_http_only' => config('session.http_only'),
            'session_same_site' => config('session.same_site') ? true : false,
            'csrf_protection' => config('app.debug') === false,
            'app_debug' => config('app.debug'),
            'app_env' => config('app.env'),
        ];
    }
}
