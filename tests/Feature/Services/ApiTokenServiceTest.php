<?php

declare(strict_types=1);

namespace Tests\Feature\Services;

use App\Models\ApiTokenUsageLog;
use App\Models\User;
use App\Services\ApiTokenService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * API Token Service Tests
 *
 * Tests secure API token management using Laravel Sanctum:
 * - Token creation with abilities and expiration
 * - Token revocation (single and all)
 * - Token validation and abilities checking
 * - Usage logging for audit trail
 *
 * @trace Requirements 37.1, 37.2, 37.3, 37.5
 * @trace D03 SRS-API-001 (API Authentication Requirements)
 * @trace D09 §4.6 (Dual Audit System)
 */
class ApiTokenServiceTest extends TestCase
{
    use RefreshDatabase;

    private ApiTokenService $service;

    protected function setUp(): void
    {
        parent::setUp();
        $this->service = app(ApiTokenService::class);
    }

    /**
     * Test: Create token with default abilities and expiration
     *
     * @trace Requirement 37.1, 37.2
     */
    public function test_creates_token_with_default_abilities_and_expiration(): void
    {
        $user = User::factory()->create();

        $token = $this->service->createToken($user, 'Test Token');

        $this->assertNotNull($token);
        $this->assertNotEmpty($token->plainTextToken);

        // Check token was created in database
        $this->assertDatabaseHas('personal_access_tokens', [
            'tokenable_id' => $user->id,
            'tokenable_type' => User::class,
            'name' => 'Test Token',
        ]);

        // Check default abilities
        $this->assertEquals(['*'], $token->accessToken->abilities);

        // Check default expiration (30 days)
        $expectedExpiry = Carbon::now()->addDays(30);
        $this->assertNotNull($token->accessToken->expires_at);
        $this->assertTrue(
            Carbon::parse($token->accessToken->expires_at)->diffInMinutes($expectedExpiry) < 1
        );
    }

    /**
     * Test: Create token with custom abilities
     *
     * @trace Requirement 37.3
     */
    public function test_creates_token_with_custom_abilities(): void
    {
        $user = User::factory()->create();
        $abilities = ['read:tickets', 'write:tickets'];

        $token = $this->service->createToken($user, 'Limited Token', $abilities);

        $this->assertEquals($abilities, $token->accessToken->abilities);
    }

    /**
     * Test: Create token with custom expiration
     *
     * @trace Requirement 37.2
     */
    public function test_creates_token_with_custom_expiration(): void
    {
        $user = User::factory()->create();

        $token = $this->service->createToken($user, 'Short-lived Token', ['*'], 7);

        $expectedExpiry = Carbon::now()->addDays(7);
        $this->assertTrue(
            Carbon::parse($token->accessToken->expires_at)->diffInMinutes($expectedExpiry) < 1
        );
    }

    /**
     * Test: Create token with no expiration
     *
     * @trace Requirement 37.2
     */
    public function test_creates_token_with_no_expiration(): void
    {
        $user = User::factory()->create();

        $token = $this->service->createToken($user, 'Permanent Token', ['*'], null);

        $this->assertNull($token->accessToken->expires_at);
    }

    /**
     * Test: Revoke specific token
     *
     * @trace Requirement 37.3
     */
    public function test_revokes_specific_token(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Token to Revoke');
        $tokenId = $token->accessToken->id;

        $result = $this->service->revokeToken($user, $tokenId);

        $this->assertTrue($result);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test: Revoke non-existent token returns false
     *
     * @trace Requirement 37.3
     */
    public function test_revoke_nonexistent_token_returns_false(): void
    {
        $user = User::factory()->create();

        $result = $this->service->revokeToken($user, 99999);

        $this->assertFalse($result);
    }

    /**
     * Test: Revoke all tokens for user
     *
     * @trace Requirement 37.3
     */
    public function test_revokes_all_tokens_for_user(): void
    {
        $user = User::factory()->create();
        $this->service->createToken($user, 'Token 1');
        $this->service->createToken($user, 'Token 2');
        $this->service->createToken($user, 'Token 3');

        $count = $this->service->revokeAllTokens($user);

        $this->assertEquals(3, $count);
        $this->assertDatabaseMissing('personal_access_tokens', [
            'tokenable_id' => $user->id,
        ]);
    }

    /**
     * Test: Revoke all tokens when user has none
     *
     * @trace Requirement 37.3
     */
    public function test_revoke_all_tokens_when_none_exist(): void
    {
        $user = User::factory()->create();

        $count = $this->service->revokeAllTokens($user);

        $this->assertEquals(0, $count);
    }

    /**
     * Test: Get active tokens excludes expired
     *
     * @trace Requirement 37.2
     */
    public function test_get_active_tokens_excludes_expired(): void
    {
        $user = User::factory()->create();

        // Create active token
        $activeToken = $this->service->createToken($user, 'Active Token', ['*'], 30);

        // Create expired token manually
        $expiredToken = $user->createToken('Expired Token', ['*'], Carbon::now()->subDay());

        $activeTokens = $this->service->getActiveTokens($user);

        $this->assertCount(1, $activeTokens);
        $this->assertEquals($activeToken->accessToken->id, $activeTokens->first()->id);
    }

    /**
     * Test: Get active tokens includes non-expiring tokens
     *
     * @trace Requirement 37.2
     */
    public function test_get_active_tokens_includes_non_expiring(): void
    {
        $user = User::factory()->create();

        $this->service->createToken($user, 'Permanent Token', ['*'], null);

        $activeTokens = $this->service->getActiveTokens($user);

        $this->assertCount(1, $activeTokens);
    }

    /**
     * Test: Validate token abilities with wildcard
     *
     * @trace Requirement 37.3
     */
    public function test_validate_token_abilities_with_wildcard(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Admin Token', ['*']);

        $result = $this->service->validateTokenAbilities(
            $token->accessToken,
            ['read:tickets', 'write:tickets', 'admin:all']
        );

        $this->assertTrue($result);
    }

    /**
     * Test: Validate token abilities with admin:all
     *
     * @trace Requirement 37.3
     */
    public function test_validate_token_abilities_with_admin_all(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Admin Token', ['admin:all']);

        $result = $this->service->validateTokenAbilities(
            $token->accessToken,
            ['read:tickets', 'write:loans']
        );

        $this->assertTrue($result);
    }

    /**
     * Test: Validate token abilities with specific abilities
     *
     * @trace Requirement 37.3
     */
    public function test_validate_token_abilities_with_specific_abilities(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Limited Token', ['read:tickets', 'read:loans']);

        $resultValid = $this->service->validateTokenAbilities(
            $token->accessToken,
            ['read:tickets']
        );
        $this->assertTrue($resultValid);

        $resultInvalid = $this->service->validateTokenAbilities(
            $token->accessToken,
            ['write:tickets']
        );
        $this->assertFalse($resultInvalid);
    }

    /**
     * Test: Log token usage creates audit record
     *
     * @trace Requirement 37.5
     */
    public function test_log_token_usage_creates_audit_record(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Test Token');

        $this->service->logTokenUsage(
            $token->accessToken,
            'api.tickets.index',
            '/api/v1/tickets',
            '192.168.1.1',
            'Mozilla/5.0',
            200
        );

        $this->assertDatabaseHas('api_token_usage_logs', [
            'personal_access_token_id' => $token->accessToken->id,
            'user_id' => $user->id,
            'action' => 'api.tickets.index',
            'endpoint' => '/api/v1/tickets',
            'response_status' => 200,
        ]);
    }

    /**
     * Test: Log token usage hashes IP address
     *
     * @trace Requirement 37.5
     * @trace D03 §8.1 (SHA-512 hashing)
     */
    public function test_log_token_usage_hashes_ip_address(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Test Token');
        $ipAddress = '192.168.1.100';

        $this->service->logTokenUsage(
            $token->accessToken,
            'api.tickets.index',
            '/api/v1/tickets',
            $ipAddress,
            null,
            200
        );

        // Get the log entry for the specific action (not the token creation log)
        $log = ApiTokenUsageLog::where('personal_access_token_id', $token->accessToken->id)
            ->where('action', 'api.tickets.index')
            ->first();

        // IP should be hashed with SHA-512 (128 characters)
        $this->assertNotNull($log);
        $this->assertNotEquals($ipAddress, $log->ip_hash);
        $this->assertEquals(128, \strlen($log->ip_hash));
        $this->assertEquals(hash('sha512', $ipAddress), $log->ip_hash);
    }

    /**
     * Test: Check if token is expired
     *
     * @trace Requirement 37.2
     */
    public function test_check_if_token_is_expired(): void
    {
        $user = User::factory()->create();

        // Create non-expired token
        $activeToken = $this->service->createToken($user, 'Active Token', ['*'], 30);
        $this->assertFalse($this->service->isTokenExpired($activeToken->accessToken));

        // Create expired token
        $expiredToken = $user->createToken('Expired Token', ['*'], Carbon::now()->subDay());
        $this->assertTrue($this->service->isTokenExpired($expiredToken->accessToken));

        // Create non-expiring token
        $permanentToken = $this->service->createToken($user, 'Permanent Token', ['*'], null);
        $this->assertFalse($this->service->isTokenExpired($permanentToken->accessToken));
    }

    /**
     * Test: Get token expiration status
     *
     * @trace Requirement 37.2
     */
    public function test_get_token_expiration_status(): void
    {
        $user = User::factory()->create();

        // Test expiring token
        $expiringToken = $this->service->createToken($user, 'Expiring Token', ['*'], 30);
        $status = $this->service->getTokenExpirationStatus($expiringToken->accessToken);

        $this->assertTrue($status['expires']);
        $this->assertNotNull($status['expires_at']);
        $this->assertFalse($status['is_expired']);
        $this->assertGreaterThan(0, $status['days_remaining']);

        // Test non-expiring token
        $permanentToken = $this->service->createToken($user, 'Permanent Token', ['*'], null);
        $status = $this->service->getTokenExpirationStatus($permanentToken->accessToken);

        $this->assertFalse($status['expires']);
        $this->assertNull($status['expires_at']);
        $this->assertFalse($status['is_expired']);
        $this->assertNull($status['days_remaining']);
    }

    /**
     * Test: Get available abilities
     *
     * @trace Requirement 37.3
     */
    public function test_get_available_abilities(): void
    {
        $abilities = ApiTokenService::getAvailableAbilities();

        $this->assertContains('read:tickets', $abilities);
        $this->assertContains('write:tickets', $abilities);
        $this->assertContains('read:loans', $abilities);
        $this->assertContains('write:loans', $abilities);
        $this->assertContains('admin:all', $abilities);
    }

    /**
     * Test: Validate ability is valid
     *
     * @trace Requirement 37.3
     */
    public function test_validate_ability_is_valid(): void
    {
        $this->assertTrue(ApiTokenService::isValidAbility('read:tickets'));
        $this->assertTrue(ApiTokenService::isValidAbility('admin:all'));
        $this->assertTrue(ApiTokenService::isValidAbility('*'));
        $this->assertFalse(ApiTokenService::isValidAbility('invalid:ability'));
    }

    /**
     * Test: Get token usage statistics
     *
     * @trace Requirement 37.5
     */
    public function test_get_token_usage_statistics(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Test Token');

        // Create some usage logs
        $this->service->logTokenUsage($token->accessToken, 'api.tickets.index', '/api/v1/tickets', null, null, 200);
        $this->service->logTokenUsage($token->accessToken, 'api.tickets.store', '/api/v1/tickets', null, null, 201);
        $this->service->logTokenUsage($token->accessToken, 'api.loans.index', '/api/v1/loans', null, null, 200);
        $this->service->logTokenUsage($token->accessToken, 'api.tickets.index', '/api/v1/tickets', null, null, 401);

        $stats = $this->service->getTokenUsageStats($user, 30);

        // Note: Token creation also logs usage, so we have 5 total (1 creation + 4 manual)
        $this->assertGreaterThanOrEqual(4, $stats['total_requests']);
        $this->assertGreaterThanOrEqual(3, $stats['successful_requests']);
        $this->assertGreaterThanOrEqual(1, $stats['failed_requests']);
        $this->assertEquals(30, $stats['period_days']);
    }

    /**
     * Test: Token creation logs activity
     *
     * @trace Requirement 37.5
     */
    public function test_token_creation_logs_activity(): void
    {
        $user = User::factory()->create();

        $this->service->createToken($user, 'Audit Test Token', ['read:tickets']);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'api_token',
            'description' => 'API token created',
            'subject_type' => User::class,
            'subject_id' => $user->id,
        ]);
    }

    /**
     * Test: Token revocation logs activity
     *
     * @trace Requirement 37.5
     */
    public function test_token_revocation_logs_activity(): void
    {
        $user = User::factory()->create();
        $token = $this->service->createToken($user, 'Token to Revoke');

        $this->service->revokeToken($user, $token->accessToken->id);

        $this->assertDatabaseHas('activity_log', [
            'log_name' => 'api_token',
            'description' => 'API token revoked',
            'subject_type' => User::class,
            'subject_id' => $user->id,
        ]);
    }
}
