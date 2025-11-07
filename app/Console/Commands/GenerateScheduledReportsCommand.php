<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\AutomatedReportService;
use Illuminate\Console\Command;

class GenerateScheduledReportsCommand extends Command
{
    protected $signature = 'reports:generate-scheduled
                           {--dry-run : Show what would be processed without actually generating reports}';

    protected $description = 'Generate and send scheduled reports';

    public function handle(AutomatedReportService $automatedReportService): int
    {
        $this->info('Starting scheduled report generation...');

        if ($this->option('dry-run')) {
            $this->warn('DRY RUN MODE - No reports will be generated');

            $dueSchedules = \App\Models\ReportSchedule::due()->get();

            if ($dueSchedules->isEmpty()) {
                $this->info('No scheduled reports are due for execution.');

                return self::SUCCESS;
            }

            $this->info("Found {$dueSchedules->count()} scheduled reports due for execution:");

            foreach ($dueSchedules as $schedule) {
                $this->line("- {$schedule->name} ({$schedule->module}) - {$schedule->frequency_description}");
            }

            return self::SUCCESS;
        }

        try {
            $results = $automatedReportService->processDueReports();

            $this->info('Report generation completed:');
            $this->line("- Processed: {$results['processed']} reports");
            $this->line("- Failed: {$results['failed']} reports");

            if ($results['failed'] > 0) {
                $this->error('Errors encountered:');
                foreach ($results['errors'] as $error) {
                    $this->error("- {$error['schedule_name']}: {$error['error']}");
                }

                return self::FAILURE;
            }

            if ($results['processed'] === 0) {
                $this->info('No scheduled reports were due for execution.');
            }

            return self::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Failed to process scheduled reports: {$e->getMessage()}");

            return self::FAILURE;
        }
    }
}
