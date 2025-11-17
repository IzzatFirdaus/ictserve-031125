<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

final class BroadcastingTest extends TestCase
{
    use RefreshDatabase;

    public function test_authorizes_private_user_channel_for_owner(): void
    {
        $user = User::factory()->create();

        // Ensure broadcast authentication uses pusher-style driver in test
        config(['broadcasting.default' => 'pusher']);

        $response = $this->actingAs($user)
            ->post('/broadcasting/auth', [
                'channel_name' => 'private-App.Models.User.'.$user->id,
                'socket_id' => '1234.1234',
            ]);

        $response->assertStatus(200);
        // A JSON payload with 'auth' key is expected from the default broadcaster
        $response->assertJsonStructure(['auth']);
    }

    public function test_unauthenticated_user_gets_401(): void
    {
        $user = User::factory()->create();

        $response = $this->post('/broadcasting/auth', [
            'channel_name' => 'private-App.Models.User.'.$user->id,
            'socket_id' => '1234.1234',
        ]);

        $response->assertStatus(401);
    }
}
