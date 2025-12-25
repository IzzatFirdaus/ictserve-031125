<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

/**
 * API Routes Tests
 *
 * Tests API route authentication and authorization:
 * - Token-based access to protected endpoints
 * - Token revocation functionality
 * - Ability-based access control
 *
 * @trace Requirements 1.1, 9.2, 9.5
 * @trace D03 SRS-API-001 (API Authentication Requirements)
 */
class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function api_requires_authentication(): void
    {
        $response = $this->getJson('/api/tickets');
        $response->assertUnauthorized();
    }

    #[Test]
    public function api_tickets_index_requires_read_ability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['write:tickets'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertForbidden();
    }

    #[Test]
    public function api_tickets_index_with_valid_token(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['read:tickets'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertOk();
    }

    #[Test]
    public function api_tickets_store_requires_write_ability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['read:tickets'])->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/tickets', []);
        $response->assertForbidden();
    }

    #[Test]
    public function api_loans_index_requires_read_ability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['read:loans'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/loans');
        $response->assertOk();
    }

    #[Test]
    public function api_admin_all_ability_grants_access(): void
    {
        $user = User::factory()->create(['role' => 'superuser']);
        $token = $user->createToken('test', ['admin:all'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertOk();
    }

    /**
     * Test: Token revocation removes token from database
     *
     * @trace Requirement 9.5
     */
    #[Test]
    public function token_revocation_removes_token_from_database(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $tokenResult = $user->createToken('test', ['read:tickets']);
        $tokenId = $tokenResult->accessToken->id;

        // Verify token exists
        $this->assertDatabaseHas('personal_access_tokens', [
            'id' => $tokenId,
        ]);

        // Revoke the token
        $tokenResult->accessToken->delete();

        // Verify token is removed
        $this->assertDatabaseMissing('personal_access_tokens', [
            'id' => $tokenId,
        ]);
    }

    /**
     * Test: Expired token is rejected
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function expired_token_is_rejected(): void
    {
        $user = User::factory()->create(['role' => 'admin']);

        // Create token that expires immediately
        $tokenResult = $user->createToken('test', ['read:tickets'], now()->subMinute());
        $token = $tokenResult->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertUnauthorized();
    }

    /**
     * Test: Wildcard ability grants all access
     *
     * @trace Requirement 9.2
     */
    #[Test]
    public function wildcard_ability_grants_all_access(): void
    {
        $user = User::factory()->create(['role' => 'superuser']);
        $token = $user->createToken('test', ['*'])->plainTextToken;

        // Should have access to all endpoints
        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertOk();

        $response = $this->withToken($token)->getJson('/api/loans');
        $response->assertOk();
    }
}
