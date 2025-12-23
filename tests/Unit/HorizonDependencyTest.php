<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class HorizonDependencyTest extends TestCase
{
    #[Test]
    public function it_loads_laravel_horizon_classes(): void
    {
        $this->assertTrue(class_exists(\Laravel\Horizon\Horizon::class));
    }
}
