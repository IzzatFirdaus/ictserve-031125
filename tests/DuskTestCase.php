<?php

declare(strict_types=1);

namespace Tests;

use Closure;

/**
 * Base class for browser tests when Laravel Dusk is not installed.
 */
abstract class DuskTestCase extends TestCase
{
    protected function setUp(): void
    {
        if (! class_exists(\Laravel\Dusk\Browser::class)) {
            $this->markTestSkipped('Laravel Dusk is not installed.');
        }

        parent::setUp();
    }

    protected function browse(Closure $callback): void
    {
        $this->markTestSkipped('Laravel Dusk is not installed.');
    }
}
