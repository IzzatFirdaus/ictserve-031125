<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\HorizonMonitoringService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Laravel\Horizon\Contracts\MasterSupervisorRepository;

/**
 * Horizon Health Check Command
 *
 * Production-ready health check for Laravel Horizon that can be used
 * by monitoring systems and load balancers.
 *
 * @see Requirements 23.4, 23.5
 */
class HorizonHealthCheck extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'horizon:health-check 
                            {--timeout=30 : Health check timeout in seconds}
                            {--exit-code : Return appropriate exit codes for monitoring}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Horizon health for production monitoring';

    /**
     * Execute the console command
     */
    public function handle(
        HorizonMonitoringService $monitoring,
        MasterSupervisorRepository $masterRepository
    ): int {
        $useExitCode = $this->option('exit-code');

        try {
            // Check if any master supervisors are running
            $masters = collect($masterRepository->all());

            if ($masters->isEmpty()) {
                $this->error('No Horizon masters found');
                Log::error('Horizon health check failed: No masters found');

                return $useExitCode ? 1 : 0;
            }

            $unhealthy = $masters->filter(fn ($master) => ! $master->isRunning());

            if ($unhealthy->isNotEmpty()) {
                $this->error("Unhealthy Horizon masters detected: {$unhealthy->count()}/{$masters->count()}");
                Log::warning('Horizon health check: Unhealthy masters detected', [
                    'unhealthy_count' => $unhealthy->count(),
                    'total_count' => $masters->count(),
                ]);

                return $useExitCode ? 1 : 0;
            }

            // Detailed health check
            /** @var array<string, array{healthy: bool}> $healthStatus */
            $healthStatus = $monitoring->checkHealthAndAlert();
            $allHealthy = collect($healthStatus)->every(fn (array $status) => $status['healthy']);

            if ($allHealthy) {
                $this->info('✅ All Horizon components are healthy');
                Log::info('Horizon health check passed', [
                    'supervisors' => $masters->count(),
                    'status' => 'healthy',
                ]);

                return 0;
            } else {
                $issues = collect($healthStatus)
                    ->filter(fn ($status) => ! $status['healthy'])
                    ->keys()
                    ->toArray();

                $this->error('❌ Horizon health issues detected: '.implode(', ', $issues));
                Log::warning('Horizon health check failed', [
                    'issues' => $issues,
                    'status' => 'unhealthy',
                ]);

                return $useExitCode ? 1 : 0;
            }
        } catch (\Exception $e) {
            $this->error('Health check failed: '.$e->getMessage());
            Log::error('Horizon health check exception', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return $useExitCode ? 2 : 0;
        }
    }
}
