<?php

declare(strict_types=1);

namespace Tests\Feature\Performance;

use App\Services\PerformanceAlertService;
use Illuminate\Support\Facades\Artisan;
use Mockery;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CheckPerformanceMetricsCommandTest extends TestCase
{
    #[Test]
    public function it_exits_successfully_when_metrics_check_succeeds(): void
    {
        $alertService = Mockery::mock(PerformanceAlertService::class);
        $alertService->shouldReceive('checkPerformanceMetrics')->once();
        $this->app->instance(PerformanceAlertService::class, $alertService);

        $exitCode = Artisan::call('ict:check-performance', ['--dry-run' => true]);

        $this->assertSame(0, $exitCode);
    }

    #[Test]
    public function it_exits_with_failure_when_metrics_check_throws(): void
    {
        $alertService = Mockery::mock(PerformanceAlertService::class);
        $alertService->shouldReceive('checkPerformanceMetrics')->once()->andThrow(new \RuntimeException('failure'));
        $this->app->instance(PerformanceAlertService::class, $alertService);

        $exitCode = Artisan::call('ict:check-performance', ['--dry-run' => true]);

        $this->assertSame(1, $exitCode);
    }
}
