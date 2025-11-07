<?php

declare(strict_types=1);

// name: CleanupOldPortalActivities
// description: Cleanup portal activities older than 7 years per retention policy
// author: dev-team@motac.gov.my
// trace: SRS-NFR-004; D03 §15.3; D11 §14.5; Requirement 14.5
// last-updated: 2025-11-06

namespace App\Console\Commands;

use App\Models\PortalActivity;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CleanupOldPortalActivities extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'portal:cleanup-activities
                          {--dry-run : Display what would be deleted without actually deleting}
                          {--years=7 : Number of years to retain activities (default: 7)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cleanup portal activity records older than the retention period (default: 7 years)';

    /**
     * Execute the console command.
     *
     * Per D03-NFR-004 and D11 §14.5: 7-year retention policy for audit logs
     *
     * @return int Command exit code (0 = success, 1 = failure)
     */
    public function handle(): int
    {
        $years = (int) $this->option('years');
        $dryRun = (bool) $this->option('dry-run');
        $cutoffDate = now()->subYears($years);

        $this->info('Portal Activity Cleanup');
        $this->line(str_repeat('-', 40));
        $this->line("Retention period: {$years} years");
        $this->line("Cutoff date: {$cutoffDate->toDateTimeString()}");
        $this->line('Mode: '.($dryRun ? 'DRY RUN (no deletion)' : 'LIVE (will delete)'));
        $this->line('');

        // Count activities older than cutoff date
        $oldActivitiesCount = PortalActivity::where('created_at', '<', $cutoffDate)->count();

        if ($oldActivitiesCount === 0) {
            $this->info('✓ No activities found older than retention period.');
            $this->info('✓ Audit log cleanup not required at this time.');

            return self::SUCCESS;
        }

        // Display statistics
        $this->line("Activities to delete: {$oldActivitiesCount}");
        $this->line('');

        // Show breakdown by activity type
        $breakdown = PortalActivity::where('created_at', '<', $cutoffDate)
            ->selectRaw('activity_type, COUNT(*) as count')
            ->groupBy('activity_type')
            ->orderByDesc('count')
            ->get();

        $this->line('Breakdown by activity type:');
        $this->table(['Activity Type', 'Count'], $breakdown->map(fn ($item) => [
            $item->activity_type,
            number_format($item->count),
        ])->toArray());

        $this->line('');

        // Confirmation prompt for live mode
        if (! $dryRun) {
            if (! $this->confirm("Delete {$oldActivitiesCount} activities older than {$years} years?", false)) {
                $this->warn('⚠ Cleanup cancelled by user.');

                return self::FAILURE;
            }

            // Perform deletion
            $deletedCount = PortalActivity::where('created_at', '<', $cutoffDate)->delete();

            // Log the cleanup action
            Log::info('Portal activity cleanup completed', [
                'deleted_count' => $deletedCount,
                'cutoff_date' => $cutoffDate->toDateTimeString(),
                'retention_years' => $years,
            ]);

            $this->info("✓ Successfully deleted {$deletedCount} portal activities.");
            $this->info("✓ Audit log cleanup completed per {$years}-year retention policy.");
        } else {
            $this->warn("⚠ DRY RUN: Would delete {$oldActivitiesCount} activities.");
            $this->info('✓ Run without --dry-run flag to perform actual deletion.');
        }

        return self::SUCCESS;
    }
}
