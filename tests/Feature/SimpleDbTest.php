<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

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
