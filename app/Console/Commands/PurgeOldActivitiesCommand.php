<?php

declare(strict_types=1);

// name: PurgeOldActivitiesCommand
// description: Artisan command to purge portal activities older than 7 years (PDPA compliance)
// author: dev-team@motac.gov.my
// trace: D03 SRS-NFR-005, D09 §9, D11 §10 (Requirements 14.5)
// last-updated: 2025-11-06

namespace App\Console\Commands;

use App\Services\DataComplianceService;
use Illuminate\Console\Command;

class PurgeOldActivitiesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:purge-old-activities
                            {--dry-run : Show what would be deleted without actually deleting}
                            {--force : Force the operation without confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Purge portal activities older than 7 years (PDPA 2010 compliance)';

    /**
     * Execute the console command.
     */
    public function handle(DataComplianceService $complianceService): int
    {
        $this->info('PDPA Compliance: Portal Activity Purge');
        $this->info('=====================================');
        $this->newLine();

        // Generate retention report
        $report = $complianceService->generateRetentionReport();

        $this->info('Retention Policy: '.$report['retention_policy']);
        $this->info('Cutoff Date: '.$report['retention_cutoff_date']);
        $this->newLine();

        $this->info('Current Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Portal Activities', $report['statistics']['total_portal_activities']],
                ['Activities Eligible for Purge', $report['statistics']['activities_eligible_for_purge']],
                ['Total Users', $report['statistics']['total_users']],
                ['Anonymized Users', $report['statistics']['anonymized_users']],
            ]
        );

        $eligibleCount = $report['statistics']['activities_eligible_for_purge'];

        if ($eligibleCount === 0) {
            $this->info('No activities eligible for purge.');

            return Command::SUCCESS;
        }

        // Dry run mode
        if ($this->option('dry-run')) {
            $this->warn("DRY RUN: Would purge {$eligibleCount} activities.");

            return Command::SUCCESS;
        }

        // Confirmation
        if (! $this->option('force')) {
            if (! $this->confirm("Are you sure you want to purge {$eligibleCount} activities?")) {
                $this->info('Operation cancelled.');

                return Command::SUCCESS;
            }
        }

        // Perform purge
        $this->info('Purging old activities...');
        $purgedCount = $complianceService->purgeOldActivities();

        $this->info("Successfully purged {$purgedCount} activities.");
        $this->newLine();

        // Show updated statistics
        $updatedReport = $complianceService->generateRetentionReport();
        $this->info('Updated Statistics:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Portal Activities', $updatedReport['statistics']['total_portal_activities']],
                ['Activities Eligible for Purge', $updatedReport['statistics']['activities_eligible_for_purge']],
            ]
        );

        return Command::SUCCESS;
    }
}
