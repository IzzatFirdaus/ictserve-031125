<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     */
    #[Test]
    public function that_true_is_true(): void
    {
        // Basic sanity check that PHPUnit is configured correctly
        $value = true;
        $this->assertTrue($value);
    }
}
