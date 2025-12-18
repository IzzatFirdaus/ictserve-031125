<?php

declare(strict_types=1);

namespace Tests\Feature;

use Illuminate\Support\Env;
use Tests\TestCase;

class HorizonConfigTest extends TestCase
{
    public function test_horizon_config_values_have_expected_types(): void
    {
        $this->assertIsString(config('horizon.name'));
        $this->assertIsString(config('horizon.path'));
        $this->assertSame('default', config('horizon.use'));

        $prefix = config('horizon.prefix');
        $this->assertIsString($prefix);

        if (Env::get('HORIZON_PREFIX') === null) {
            $this->assertStringEndsWith('_horizon:', $prefix);
        }

        $this->assertIsArray(config('horizon.defaults'));
        $this->assertIsArray(config('horizon.environments'));
        $this->assertIsArray(config('horizon.waits'));

        $this->assertIsString(config('horizon.notifications.email'));
    }
}
