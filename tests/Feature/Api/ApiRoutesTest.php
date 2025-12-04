<?php

declare(strict_types=1);

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ApiRoutesTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_requires_authentication(): void
    {
        $response = $this->getJson('/api/tickets');
        $response->assertUnauthorized();
    }

    public function test_api_tickets_index_requires_read_ability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['write:tickets'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertForbidden();
    }

    public function test_api_tickets_index_with_valid_token(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['read:tickets'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertOk();
    }

    public function test_api_tickets_store_requires_write_ability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['read:tickets'])->plainTextToken;

        $response = $this->withToken($token)->postJson('/api/tickets', []);
        $response->assertForbidden();
    }

    public function test_api_loans_index_requires_read_ability(): void
    {
        $user = User::factory()->create(['role' => 'admin']);
        $token = $user->createToken('test', ['read:loans'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/loans');
        $response->assertOk();
    }

    public function test_api_admin_all_ability_grants_access(): void
    {
        $user = User::factory()->create(['role' => 'superuser']);
        $token = $user->createToken('test', ['admin:all'])->plainTextToken;

        $response = $this->withToken($token)->getJson('/api/tickets');
        $response->assertOk();
    }
}
