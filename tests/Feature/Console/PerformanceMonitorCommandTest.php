<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use Illuminate\Support\Facades\Cache;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PerformanceMonitorCommandTest extends TestCase
{
    #[Test]
    public function command_shows_metrics(): void
    {
        Cache::put('performance.cache.hits', 9);
        Cache::put('performance.cache.misses', 1);

        $this->artisan('performance:monitor')->assertExitCode(0);
    }

    #[Test]
    public function command_can_clear_performance_caches(): void
    {
        $this->artisan('performance:monitor', ['--clear-cache' => true])->assertExitCode(0);
    }

    #[Test]
    public function command_can_warm_up_performance_caches(): void
    {
        $this->artisan('performance:monitor', ['--warm-cache' => true])->assertExitCode(0);
    }
}
