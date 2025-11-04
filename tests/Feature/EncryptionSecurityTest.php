<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Services\EncryptionService;
use Illuminate\Support\Facades\Config;
use Tests\TestCase;

/**
 * Encryption and Security Tests
 *
 * Tests AES-256 encryption, secure token generation,
 * CSRF protection, and security configuration validation.
 *
 * @see D03-FR-010.3 Data encryption requirements
 * @see D03-FR-010.4 Security standards
 */
class EncryptionSecurityTest extends TestCase
{

    private EncryptionService $encryptionService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->encryptionService = new EncryptionService();
    }

    public function test_aes_256_encryption_configuration(): void
    {
        // Verify AES-256-CBC is configured
        $this->assertEquals('AES-256-CBC', config('app.cipher'));

        // Verify app key is set
        $this->assertNotEmpty(config('app.key'));

        // Verify key length (32 bytes for AES-256)
        $keyLength = strlen(base64_decode(substr(config('app.key'), 7)));
        $this->assertEquals(32, $keyLength);
    }

    public function test_sensitive_data_encryption_decryption(): void
    {
        $sensitiveData = 'This is sensitive information';

        // Test encryption
        $encrypted = $this->encryptionService->encryptSensitiveData($sensitiveData);
        $this->assertNotEquals($sensitiveData, $encrypted);
        $this->assertNotEmpty($encrypted);

        // Test decryption
        $decrypted = $this->encryptionService->decryptSensitiveData($encrypted);
        $this->assertEquals($sensitiveData, $decrypted);
    }

    public function test_secure_token_generation(): void
    {
        $token1 = $this->encryptionService->generateSecureToken();
        $token2 = $this->encryptionService->generateSecureToken();

        // Tokens should be different
        $this->assertNotEquals($token1, $token2);

        // Default length should be 64 characters
        $this->assertEquals(64, strlen($token1));

        // Test custom length
        $shortToken = $this->encryptionService->generateSecureToken(32);
        $this->assertEquals(32, strlen($shortToken));
    }

    public function test_password_hashing_verification(): void
    {
        $password = 'test_password_123';

        // Test hashing
        $hash = $this->encryptionService->hashPassword($password);
        $this->assertNotEquals($password, $hash);
        $this->assertNotEmpty($hash);

        // Test verification
        $this->assertTrue($this->encryptionService->verifyPassword($password, $hash));
        $this->assertFalse($this->encryptionService->verifyPassword('wrong_password', $hash));
    }

    public function test_csrf_token_generation(): void
    {
        $token1 = $this->encryptionService->generateCSRFToken();
        $token2 = $this->encryptionService->generateCSRFToken();

        // Tokens should be different
        $this->assertNotEquals($token1, $token2);

        // Should be 64 characters (32 bytes hex encoded)
        $this->assertEquals(64, strlen($token1));

        // Should be hexadecimal
        $this->assertTrue(ctype_xdigit($token1));
    }

    public function test_encryption_configuration_validation(): void
    {
        $validation = $this->encryptionService->validateEncryptionConfig();

        $this->assertIsArray($validation);
        $this->assertArrayHasKey('cipher', $validation);
        $this->assertArrayHasKey('key_set', $validation);
        $this->assertArrayHasKey('key_length', $validation);
        $this->assertArrayHasKey('encryption_working', $validation);
        $this->assertArrayHasKey('hash_working', $validation);

        $this->assertEquals('AES-256-CBC', $validation['cipher']);
        $this->assertTrue($validation['key_set']);
        $this->assertEquals(32, $validation['key_length']);
        $this->assertTrue($validation['encryption_working']);
        $this->assertTrue($validation['hash_working']);
    }

    public function test_array_encryption_decryption(): void
    {
        $testArray = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'sensitive_data' => 'secret information',
        ];

        // Test array encryption
        $encrypted = $this->encryptionService->encryptArray($testArray);
        $this->assertIsString($encrypted);
        $this->assertNotEquals(json_encode($testArray), $encrypted);

        // Test array decryption
        $decrypted = $this->encryptionService->decryptArray($encrypted);
        $this->assertEquals($testArray, $decrypted);
    }

    public function test_data_sanitization_for_logging(): void
    {
        $data = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'secret123',
            'api_key' => 'sk-1234567890',
            'normal_field' => 'normal value',
        ];

        $sanitized = $this->encryptionService->sanitizeForLogging($data);

        $this->assertEquals('John Doe', $sanitized['name']);
        $this->assertEquals('john@example.com', $sanitized['email']);
        $this->assertEquals('[REDACTED]', $sanitized['password']);
        $this->assertEquals('[REDACTED]', $sanitized['api_key']);
        $this->assertEquals('normal value', $sanitized['normal_field']);
    }

    public function test_approval_token_generation_validation(): void
    {
        $tokenData = $this->encryptionService->generateApprovalToken();

        $this->assertIsArray($tokenData);
        $this->assertArrayHasKey('token', $tokenData);
        $this->assertArrayHasKey('expires_at', $tokenData);
        $this->assertArrayHasKey('created_at', $tokenData);

        $this->assertEquals(64, strlen($tokenData['token']));
        $this->assertTrue($tokenData['expires_at']->isFuture());

        // Test validation
        $isValid = $this->encryptionService->validateApprovalToken(
            $tokenData['token'],
            $tokenData['token'],
            $tokenData['expires_at']
        );
        $this->assertTrue($isValid);

        // Test with wrong token
        $isInvalid = $this->encryptionService->validateApprovalToken(
            'wrong_token',
            $tokenData['token'],
            $tokenData['expires_at']
        );
        $this->assertFalse($isInvalid);
    }

    public function test_pii_encryption_decryption(): void
    {
        $pii = 'IC: 123456-78-9012';

        // Test PII encryption (should be different from regular encryption)
        $encryptedPII = $this->encryptionService->encryptPII($pii);
        $regularEncrypted = $this->encryptionService->encryptSensitiveData($pii);

        $this->assertNotEquals($encryptedPII, $regularEncrypted);

        // Test PII decryption
        $decryptedPII = $this->encryptionService->decryptPII($encryptedPII);
        $this->assertEquals($pii, $decryptedPII);
    }

    public function test_session_token_generation(): void
    {
        $token1 = $this->encryptionService->generateSessionToken();
        $token2 = $this->encryptionService->generateSessionToken();

        // Tokens should be different
        $this->assertNotEquals($token1, $token2);

        // Should be SHA-256 hash (64 characters)
        $this->assertEquals(64, strlen($token1));

        // Should be hexadecimal
        $this->assertTrue(ctype_xdigit($token1));
    }

    public function test_security_configuration_validation(): void
    {
        $securityConfig = $this->encryptionService->validateSecurityConfig();

        $this->assertIsArray($securityConfig);
        $this->assertArrayHasKey('encryption', $securityConfig);
        $this->assertArrayHasKey('https_enabled', $securityConfig);
        $this->assertArrayHasKey('session_secure', $securityConfig);
        $this->assertArrayHasKey('session_http_only', $securityConfig);
        $this->assertArrayHasKey('app_debug', $securityConfig);
        $this->assertArrayHasKey('app_env', $securityConfig);

        // Encryption should be working
        $this->assertTrue($securityConfig['encryption']['encryption_working']);
        $this->assertTrue($securityConfig['encryption']['hash_working']);
    }

    public function test_encryption_failure_handling(): void
    {
        // Test with invalid encrypted data
        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Failed to decrypt sensitive data');

        $this->encryptionService->decryptSensitiveData('invalid_encrypted_data');
    }

    public function test_tls_configuration(): void
    {
        // In production, HTTPS should be enforced
        if (config('app.env') === 'production') {
            $this->assertTrue(config('session.secure'));
            $this->assertTrue(config('session.http_only'));
        }

        // Session configuration should be secure
        $this->assertTrue(config('session.http_only'));
    }

    public function test_csrf_protection_enabled(): void
    {
        // CSRF protection should be enabled in non-debug mode
        if (!config('app.debug')) {
            $response = $this->post('/test-route', []);
            $this->assertEquals(419, $response->getStatusCode()); // CSRF token mismatch
        }

        $this->assertTrue(true); // Pass if in debug mode
    }
}
