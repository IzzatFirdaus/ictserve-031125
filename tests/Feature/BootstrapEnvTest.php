<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class BootstrapEnvTest extends TestCase
{
    #[Test]
    public function bootstrap_envs_before_refreshdatabase(): void
    {
        // Output to confirm environment variables at test runtime
        echo '[FEATURE TEST] getenv(APP_ENV)='.(getenv('APP_ENV') ?: 'NULL')."\n";
        echo '[FEATURE TEST] getenv(DB_CONNECTION)='.(getenv('DB_CONNECTION') ?: 'NULL')."\n";

        $this->assertTrue(true);
    }
}
