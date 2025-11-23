<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\User;
use PHPUnit\Framework\Attributes\Test;

class SimpleDbTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function test_db_connection(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
    }
}
