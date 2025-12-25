<?php

declare(strict_types=1);

namespace Tests\Feature\Console;

use App\Services\HorizonMonitoringService;
use Illuminate\Support\Collection;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class MonitorHorizonProductionTest extends TestCase
{
    #[Test]
    public function command_exits_success_when_healthy(): void
    {
        $this->app->instance('redis', new class
        {
            public function ping(): string
            {
                return 'PONG';
            }
        });

        $master = new class('master-1', true, 2)
        {
            public string $name;

            public Collection $processes;

            private bool $running;

            public function __construct(string $name, bool $running, int $processCount)
            {
                $this->name = $name;
                $this->running = $running;
                $this->processes = collect(range(1, $processCount));
            }

            public function isRunning(): bool
            {
                return $this->running;
            }
        };

        $this->mock(MasterSupervisorRepository::class, function (MockInterface $mock) use ($master): void {
            $mock->shouldReceive('all')->once()->andReturn([$master]);
        });

        $this->mock(HorizonMonitoringService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getQueueStatistics')->once()->andReturn([
                'default' => ['wait_time' => 0, 'failed' => 0],
            ]);
        });

        $this->artisan('horizon:monitor-production')->assertExitCode(0);
    }

    #[Test]
    public function command_exits_failure_when_unhealthy(): void
    {
        $this->app->instance('redis', new class
        {
            public function ping(): string
            {
                return 'PONG';
            }
        });

        $this->mock(MasterSupervisorRepository::class, function (MockInterface $mock): void {
            $mock->shouldReceive('all')->once()->andReturn([]);
        });

        $this->mock(HorizonMonitoringService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('getQueueStatistics')->once()->andReturn([]);
        });

        $this->artisan('horizon:monitor-production')->assertExitCode(1);
    }
}
