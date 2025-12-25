<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class EnvDebugTest extends TestCase
{
    #[Test]
    public function env_values_are_expected(): void
    {
        // Output environment values to help debug PHPUnit bootstrap ordering
        echo 'getenv(APP_ENV)='.(getenv('APP_ENV') ?: 'NULL').PHP_EOL;
        echo '_ENV[APP_ENV]='.(isset($_ENV['APP_ENV']) ? $_ENV['APP_ENV'] : 'NULL').PHP_EOL;
        echo '_SERVER[APP_ENV]='.(isset($_SERVER['APP_ENV']) ? $_SERVER['APP_ENV'] : 'NULL').PHP_EOL;
        echo 'getenv(DB_CONNECTION)='.(getenv('DB_CONNECTION') ?: 'NULL').PHP_EOL;
        echo 'env(DB_CONNECTION)='.(env('DB_CONNECTION') ?: 'NULL').PHP_EOL;

        // Minimal assertion to keep PHPUnit happy
        $this->assertTrue(true);
    }
}
