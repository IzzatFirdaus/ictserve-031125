<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\HelpdeskTicket;
use App\Models\LoanApplication;
use App\Services\TokenService;
use Carbon\Carbon;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * Token Service Tests
 *
 * Tests secure token generation and validation for:
 * - Status checking (guest access to ticket/loan status)
 * - Approval workflows (email-based loan approvals)
 *
 * @trace Requirements 1.5, 4.1, 14.4
 * @trace D03 SRS-HELP-004 (Status token requirements)
 * @trace D03 SRS-LOAN-004 (Approval token requirements)
 * @trace D03 §8.1 (SHA-512 hashing requirement)
 */
class TokenServiceTest extends TestCase
{
    private TokenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(TokenService::class);
    }

    /**
     * Test: Generate status token for helpdesk ticket
     *
     * @trace Requirement 1.5
     */
    #[Test]
    public function generates_status_token_for_helpdesk_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $token = $this->service->generateStatusToken($ticket);

        // Token should be 64-character hex string
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $token);

        // Hash should be stored in database (128 characters for SHA-512)
        $ticket->refresh();
        $this->assertNotNull($ticket->status_token_hash);
        $this->assertEquals(128, strlen($ticket->status_token_hash));
    }

    /**
     * Test: Generate status token for loan application
     *
     * @trace Requirement 1.5
     */
    #[Test]
    public function generates_status_token_for_loan_application(): void
    {
        $loan = LoanApplication::factory()->create();

        $token = $this->service->generateStatusToken($loan);

        // Token should be 64-character hex string
        $this->assertIsString($token);
        $this->assertEquals(64, strlen($token));

        // Hash should be stored in database
        $loan->refresh();
        $this->assertNotNull($loan->status_token_hash);
        $this->assertEquals(128, strlen($loan->status_token_hash));
    }

    /**
     * Test: Status token uses SHA-512 hashing
     *
     * @trace Requirement 14.4
     * @trace D03 §8.1
     */
    #[Test]
    public function status_token_uses_sha512_hashing(): void
    {
        $ticket = HelpdeskTicket::factory()->create();

        $token = $this->service->generateStatusToken($ticket);

        // Verify hash matches SHA-512 of token
        $expectedHash = hash('sha512', $token);
        $ticket->refresh();
        $this->assertEquals($expectedHash, $ticket->status_token_hash);
    }

    /**
     * Test: Validate status token for helpdesk ticket
     *
     * @trace Requirement 1.5
     */
    #[Test]
    public function validates_status_token_for_ticket(): void
    {
        $ticket = HelpdeskTicket::factory()->create();
        $token = $this->service->generateStatusToken($ticket);

        $foundTicket = $this->service->validateStatusToken($token, 'ticket');

        $this->assertNotNull($foundTicket);
        $this->assertInstanceOf(HelpdeskTicket::class, $foundTicket);
        $this->assertEquals($ticket->id, $foundTicket->id);
    }

    /**
     * Test: Validate status token for loan application
     *
     * @trace Requirement 1.5
     */
    #[Test]
    public function validates_status_token_for_loan(): void
    {
        $loan = LoanApplication::factory()->create();
        $token = $this->service->generateStatusToken($loan);

        $foundLoan = $this->service->validateStatusToken($token, 'loan');

        $this->assertNotNull($foundLoan);
        $this->assertInstanceOf(LoanApplication::class, $foundLoan);
        $this->assertEquals($loan->id, $foundLoan->id);
    }

    /**
     * Test: Invalid status token returns null
     *
     * @trace Requirement 14.4
     */
    #[Test]
    public function invalid_status_token_returns_null(): void
    {
        $invalidToken = str_repeat('a', 64);

        $result = $this->service->validateStatusToken($invalidToken, 'ticket');

        $this->assertNull($result);
    }

    /**
     * Test: Generate approval token for loan application
     *
     * @trace Requirement 4.1
     */
    #[Test]
    public function generates_approval_token_for_loan(): void
    {
        $loan = LoanApplication::factory()->create();

        $result = $this->service->generateApprovalToken($loan);

        // Check return structure
        $this->assertIsArray($result);
        $this->assertArrayHasKey('token', $result);
        $this->assertArrayHasKey('hash', $result);
        $this->assertArrayHasKey('expires_at', $result);

        // Token should be 64-character hex string
        $this->assertEquals(64, strlen($result['token']));
        $this->assertMatchesRegularExpression('/^[a-f0-9]{64}$/', $result['token']);

        // Hash should be 128 characters (SHA-512)
        $this->assertEquals(128, strlen($result['hash']));

        // Expiry should be Carbon instance
        $this->assertInstanceOf(Carbon::class, $result['expires_at']);
    }

    /**
     * Test: Approval token has default 72-hour expiry
     *
     * @trace Requirement 4.1
     */
    #[Test]
    public function approval_token_has_default72_hour_expiry(): void
    {
        $loan = LoanApplication::factory()->create();

        $result = $this->service->generateApprovalToken($loan);

        $expectedExpiry = Carbon::now()->addHours(72);
        $this->assertTrue($result['expires_at']->diffInMinutes($expectedExpiry) < 1);
    }

    /**
     * Test: Approval token accepts custom expiry hours
     *
     * @trace Requirement 4.1
     */
    #[Test]
    public function approval_token_accepts_custom_expiry(): void
    {
        $loan = LoanApplication::factory()->create();

        $result = $this->service->generateApprovalToken($loan, 48);

        $expectedExpiry = Carbon::now()->addHours(48);
        $this->assertTrue($result['expires_at']->diffInMinutes($expectedExpiry) < 1);
    }

    /**
     * Test: Approval token uses SHA-512 hashing
     *
     * @trace Requirement 14.4
     * @trace D03 §8.1
     */
    #[Test]
    public function approval_token_uses_sha512_hashing(): void
    {
        $loan = LoanApplication::factory()->create();

        $result = $this->service->generateApprovalToken($loan);

        // Verify hash matches SHA-512 of token
        $expectedHash = hash('sha512', $result['token']);
        $this->assertEquals($expectedHash, $result['hash']);
    }

    /**
     * Test: Validate approval token with valid token
     *
     * @trace Requirement 4.1
     */
    #[Test]
    public function validates_approval_token_with_valid_token(): void
    {
        $loan = LoanApplication::factory()->create();
        $result = $this->service->generateApprovalToken($loan);

        $isValid = $this->service->validateApprovalToken($loan, $result['token']);

        $this->assertTrue($isValid);
    }

    /**
     * Test: Validate approval token rejects invalid token
     *
     * @trace Requirement 14.4
     */
    #[Test]
    public function validates_approval_token_rejects_invalid_token(): void
    {
        $loan = LoanApplication::factory()->create();
        $this->service->generateApprovalToken($loan);

        $invalidToken = str_repeat('a', 64);
        $isValid = $this->service->validateApprovalToken($loan, $invalidToken);

        $this->assertFalse($isValid);
    }

    /**
     * Test: Validate approval token rejects expired token
     *
     * @trace Requirement 4.1
     */
    #[Test]
    public function validates_approval_token_rejects_expired_token(): void
    {
        $loan = LoanApplication::factory()->create();
        $result = $this->service->generateApprovalToken($loan, 1);

        // Travel forward in time to expire the token
        $this->travel(2)->hours();

        $isValid = $this->service->validateApprovalToken($loan, $result['token']);

        $this->assertFalse($isValid);
    }

    /**
     * Test: Regenerate approval token creates new token
     *
     * @trace Requirement 4.1
     */
    #[Test]
    public function regenerates_approval_token(): void
    {
        $loan = LoanApplication::factory()->create();
        $originalResult = $this->service->generateApprovalToken($loan);

        // Regenerate token
        $newResult = $this->service->regenerateApprovalToken($loan);

        // New token should be different
        $this->assertNotEquals($originalResult['token'], $newResult['token']);
        $this->assertNotEquals($originalResult['hash'], $newResult['hash']);

        // Old token should no longer be valid
        $isOldValid = $this->service->validateApprovalToken($loan, $originalResult['token']);
        $this->assertFalse($isOldValid);

        // New token should be valid
        $isNewValid = $this->service->validateApprovalToken($loan, $newResult['token']);
        $this->assertTrue($isNewValid);
    }

    /**
     * Test: Tokens are cryptographically unique
     *
     * @trace Requirement 14.4
     */
    #[Test]
    public function tokens_are_cryptographically_unique(): void
    {
        $ticket1 = HelpdeskTicket::factory()->create();
        $ticket2 = HelpdeskTicket::factory()->create();

        $token1 = $this->service->generateStatusToken($ticket1);
        $token2 = $this->service->generateStatusToken($ticket2);

        $this->assertNotEquals($token1, $token2);
        $this->assertNotEquals($ticket1->status_token_hash, $ticket2->status_token_hash);
    }

    /**
     * Test: Token validation uses constant-time comparison
     *
     * This test ensures timing attacks are prevented by using hash_equals()
     *
     * @trace Requirement 14.4
     * @trace D03 §8.1
     */
    #[Test]
    public function token_validation_prevents_timing_attacks(): void
    {
        $loan = LoanApplication::factory()->create();
        $result = $this->service->generateApprovalToken($loan);

        // Create similar but invalid token (differs by one character)
        $similarToken = substr($result['token'], 0, -1).'0';

        // Both should return false, but timing should be similar
        $startValid = microtime(true);
        $this->service->validateApprovalToken($loan, $result['token']);
        $timeValid = microtime(true) - $startValid;

        $startInvalid = microtime(true);
        $this->service->validateApprovalToken($loan, $similarToken);
        $timeInvalid = microtime(true) - $startInvalid;

        // Timing difference should be minimal (< 1ms)
        // This is a basic check; real timing attack prevention is in hash_equals()
        $this->assertLessThan(0.001, abs($timeValid - $timeInvalid));
    }
}
