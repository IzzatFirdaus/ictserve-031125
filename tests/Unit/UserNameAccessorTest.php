<?php

declare(strict_types=1);

namespace Tests\Unit;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class UserNameAccessorTest extends TestCase
{
    #[Test]
    public function name_accessor_handles_array(): void
    {
        $user = new User;
        $user->setRawAttributes(['name' => ['en' => 'Alice', 'ms' => 'Alicia']], true);

        $this->assertIsString($user->name);
        $this->assertSame('Alice', $user->name);
    }

    #[Test]
    public function name_accessor_handles_string(): void
    {
        $user = new User;
        $user->setRawAttributes(['name' => 'Bob'], true);

        $this->assertIsString($user->name);
        $this->assertSame('Bob', $user->name);
    }
}
