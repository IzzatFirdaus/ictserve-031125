<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeaderViewTest extends TestCase
{
    use RefreshDatabase;

    public function test_header_renders_with_name_array(): void
    {
        $user = User::factory()->create();

        // simulate a scenario where name was erroneously assigned an array at runtime
        $user->setRawAttributes(array_merge($user->getAttributes(), ['name' => ['en' => 'ArrayName', 'ms' => 'Nama']]), true);
        $this->actingAs($user);

        $response = $this->get('/');

        $response->assertStatus(200);
        $response->assertSee('A'); // The initial of ArrayName should be shown
        $response->assertSee('ArrayName');
    }
}
