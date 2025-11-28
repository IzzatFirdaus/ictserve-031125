<?php

declare(strict_types=1);

namespace Tests\Unit\Services;

use App\Services\DataEncryptionService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

/**
 * Unit tests for DataEncryptionService.
 *
 * Tests encryption, decryption, and token handling.
 */
#[CoversClass(DataEncryptionService::class)]
class DataEncryptionServiceTest extends TestCase
{
    private DataEncryptionService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = new DataEncryptionService;
    }

    public function test_encrypt_returns_encrypted_string(): void
    {
        $plaintext = 'sensitive data';

        $encrypted = $this->service->encrypt($plaintext);

        $this->assertNotEquals($plaintext, $encrypted);
        $this->assertNotEmpty($encrypted);
    }

    public function test_decrypt_returns_original_string(): void
    {
        $plaintext = 'sensitive data';

        $encrypted = $this->service->encrypt($plaintext);
        $decrypted = $this->service->decrypt($encrypted);

        $this->assertEquals($plaintext, $decrypted);
    }

    public function test_encrypt_produces_different_output_each_time(): void
    {
        $plaintext = 'sensitive data';

        $encrypted1 = $this->service->encrypt($plaintext);
        $encrypted2 = $this->service->encrypt($plaintext);

        // Laravel's encryption includes random IV, so outputs differ
        $this->assertNotEquals($encrypted1, $encrypted2);
    }

    public function test_encrypt_approval_token_creates_valid_token(): void
    {
        $loanId = 123;
        $approverId = 456;

        $token = $this->service->encryptApprovalToken($loanId, $approverId);

        $this->assertNotEmpty($token);
        $this->assertIsString($token);
    }

    public function test_decrypt_approval_token_returns_data(): void
    {
        $loanId = 123;
        $approverId = 456;

        $token = $this->service->encryptApprovalToken($loanId, $approverId);
        $data = $this->service->decryptApprovalToken($token);

        $this->assertIsArray($data);
        $this->assertEquals($loanId, $data['loan_id']);
        $this->assertEquals($approverId, $data['approver_id']);
        $this->assertArrayHasKey('expires_at', $data);
    }

    public function test_decrypt_approval_token_returns_null_for_expired(): void
    {
        $loanId = 123;
        $approverId = 456;

        // Create token
        $token = $this->service->encryptApprovalToken($loanId, $approverId);

        // Travel 8 days into the future (token expires in 7 days)
        Carbon::setTestNow(now()->addDays(8));

        $data = $this->service->decryptApprovalToken($token);

        $this->assertNull($data);

        Carbon::setTestNow();
    }

    public function test_decrypt_approval_token_returns_null_for_invalid(): void
    {
        $data = $this->service->decryptApprovalToken('invalid-token');

        $this->assertNull($data);
    }

    public function test_hash_personal_data_returns_consistent_hash(): void
    {
        $data = 'personal@email.com';

        $hash1 = $this->service->hashPersonalData($data);
        $hash2 = $this->service->hashPersonalData($data);

        $this->assertEquals($hash1, $hash2);
    }

    public function test_hash_personal_data_returns_different_hash_for_different_data(): void
    {
        $hash1 = $this->service->hashPersonalData('email1@example.com');
        $hash2 = $this->service->hashPersonalData('email2@example.com');

        $this->assertNotEquals($hash1, $hash2);
    }

    public function test_hash_personal_data_is_sha256(): void
    {
        $data = 'test data';

        $hash = $this->service->hashPersonalData($data);

        // SHA256 produces 64 character hex string
        $this->assertEquals(64, strlen($hash));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $hash);
    }

    public function test_hash_is_one_way(): void
    {
        $data = 'sensitive personal data';

        $hash = $this->service->hashPersonalData($data);

        // Hash should not contain original data
        $this->assertStringNotContainsString($data, $hash);
    }
}
