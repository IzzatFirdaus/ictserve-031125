<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SimpleDbTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function dbConnection(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
    }
}
