<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class BootstrapEnvTest extends TestCase
{
    public function test_bootstrap_envs_before_refreshdatabase(): void
    {
        // Output to confirm environment variables at test runtime
        echo '[FEATURE TEST] getenv(APP_ENV)='.(getenv('APP_ENV') ?: 'NULL')."\n";
        echo '[FEATURE TEST] getenv(DB_CONNECTION)='.(getenv('DB_CONNECTION') ?: 'NULL')."\n";

        $this->assertTrue(true);
    }
}
