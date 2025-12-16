<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\PerformanceAlertService;
use Illuminate\Console\Command as LaravelCommand;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

/**
 * Check Performance Metrics Command
 *
 * Scheduled command to check performance metrics and trigger alerts.
 *
 * @trace Requirements 16.3 - Automated alerting for performance threshold breaches
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 3.6.0
 */
class CheckPerformanceMetrics extends LaravelCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ict:check-performance
                            {--dry-run : Run without sending alerts}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Semak metrik prestasi dan hantar amaran jika ambang dilanggar';

    /**
     * Execute the console command.
     */
    public function handle(PerformanceAlertService $alertService): int
    {
        $this->info('Menyemak metrik prestasi...');

        if ($this->option('dry-run')) {
            $this->warn('Mod dry-run: Amaran tidak akan dihantar.');
        }

        try {
            $alertService->checkPerformanceMetrics();
            $this->info('Semakan metrik prestasi selesai.');

            return SymfonyCommand::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Ralat semasa menyemak metrik: {$e->getMessage()}");

            return SymfonyCommand::FAILURE;
        }
    }
}
