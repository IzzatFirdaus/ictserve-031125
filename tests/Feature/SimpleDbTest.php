<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class SimpleDbTest extends TestCase
{
    #[Test]
    public function db_connection(): void
    {
        $user = User::factory()->create();
        $this->assertDatabaseCount('users', 1);
    }
}
