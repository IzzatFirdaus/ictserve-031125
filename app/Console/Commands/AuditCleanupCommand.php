<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Audit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * Audit Cleanup Command
 *
 * Manages audit record retention policy for ICTServe compliance.
 * Archives records older than 7 years while maintaining compliance.
 *
 * @see D03-FR-010.2 Audit logging system
 * @see D09 Database Documentation - Audit retention
 */
class AuditCleanupCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'audit:cleanup
                            {--dry-run : Show what would be cleaned without actually doing it}
                            {--force : Force cleanup without confirmation}
                            {--archive : Archive records instead of deleting them}';

    /**
     * The console command description.
     */
    protected $description = 'Clean up audit records older than retention period (7 years)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $retentionYears = config('audit.retention.years', 7);
        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');
        $shouldArchive = $this->option('archive');

        $this->info("Audit Cleanup - Retention Period: {$retentionYears} years");
        $this->info("Cutoff Date: " . now()->subYears($retentionYears)->format('Y-m-d H:i:s'));

        // Get statistics
        $stats = Audit::getStatistics();
        $expiredCount = $stats['expired_records'];

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Records', number_format($stats['total_records'])],
                ['Records Last 30 Days', number_format($stats['records_last_30_days'])],
                ['Security Events Last 30 Days', number_format($stats['security_events_last_30_days'])],
                ['Oldest Record', $stats['oldest_record']?->format('Y-m-d H:i:s') ?? 'None'],
                ['Newest Record', $stats['newest_record']?->format('Y-m-d H:i:s') ?? 'None'],
                ['Expired Records', number_format($expiredCount)],
            ]
        );

        if ($expiredCount === 0) {
            $this->info('No expired audit records found.');
            return self::SUCCESS;
        }

        if ($isDryRun) {
            $this->warn("DRY RUN: Would process {$expiredCount} expired records");
            $this->showExpiredRecordsSample();
            return self::SUCCESS;
        }

        if (!$isForced && !$this->confirm("Process {$expiredCount} expired audit records?")) {
            $this->info('Cleanup cancelled.');
            return self::SUCCESS;
        }

        if ($shouldArchive) {
            return $this->archiveExpiredRecords($expiredCount);
        } else {
            $this->error('Direct deletion of audit records is not allowed for compliance.');
            $this->info('Use --archive option to archive records instead.');
            return self::FAILURE;
        }
    }

    /**
     * Archive expired audit records
     */
    private function archiveExpiredRecords(int $count): int
    {
        $this->info("Archiving {$count} expired audit records...");

        try {
            DB::beginTransaction();

            // Create archive table if it doesn't exist
            $this->createArchiveTable();

            // Move expired records to archive
            $expiredRecords = Audit::expired()->get();
            $archived = 0;

            foreach ($expiredRecords as $record) {
                // Insert into archive table
                DB::table('audits_archive')->insert([
                    'id' => $record->id,
                    'user_type' => $record->user_type,
                    'user_id' => $record->user_id,
                    'event' => $record->event,
                    'auditable_type' => $record->auditable_type,
                    'auditable_id' => $record->auditable_id,
                    'old_values' => json_encode($record->old_values),
                    'new_values' => json_encode($record->new_values),
                    'url' => $record->url,
                    'ip_address' => $record->ip_address,
                    'user_agent' => $record->user_agent,
                    'tags' => $record->tags,
                    'created_at' => $record->created_at,
                    'updated_at' => $record->updated_at,
                    'archived_at' => now(),
                ]);

                $archived++;

                if ($archived % 1000 === 0) {
                    $this->info("Archived {$archived} records...");
                }
            }

            // Remove from main table (this is allowed for archived records)
            DB::table('audits')->whereIn('id', $expiredRecords->pluck('id'))->delete();

            DB::commit();

            $this->info("Successfully archived {$archived} audit records.");
            $this->info("Records moved to 'audits_archive' table for long-term storage.");

            return self::SUCCESS;

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error("Failed to archive records: {$e->getMessage()}");
            return self::FAILURE;
        }
    }

    /**
     * Create archive table
     */
    private function createArchiveTable(): void
    {
        if (!DB::getSchemaBuilder()->hasTable('audits_archive')) {
            DB::statement('
                CREATE TABLE audits_archive (
                    id bigint unsigned NOT NULL,
                    user_type varchar(255) DEFAULT NULL,
                    user_id bigint unsigned DEFAULT NULL,
                    event varchar(255) NOT NULL,
                    auditable_type varchar(255) NOT NULL,
                    auditable_id bigint unsigned NOT NULL,
                    old_values json DEFAULT NULL,
                    new_values json DEFAULT NULL,
                    url text DEFAULT NULL,
                    ip_address varchar(45) DEFAULT NULL,
                    user_agent text DEFAULT NULL,
                    tags varchar(255) DEFAULT NULL,
                    created_at timestamp NULL DEFAULT NULL,
                    updated_at timestamp NULL DEFAULT NULL,
                    archived_at timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
                    PRIMARY KEY (id),
                    KEY audits_archive_auditable_type_auditable_id_index (auditable_type, auditable_id),
                    KEY audits_archive_user_id_user_type_index (user_id, user_type),
                    KEY audits_archive_created_at_index (created_at),
                    KEY audits_archive_archived_at_index (archived_at)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
            ');

            $this->info('Created audits_archive table.');
        }
    }

    /**
     * Show sample of expired records
     */
    private function showExpiredRecordsSample(): void
    {
        $sample = Audit::expired()->limit(5)->get();

        if ($sample->isNotEmpty()) {
            $this->info('Sample of expired records:');
            $this->table(
                ['ID', 'Event', 'Model', 'User', 'Created At'],
                $sample->map(function ($audit) {
                    return [
                        $audit->id,
                        $audit->event,
                        class_basename($audit->auditable_type),
                        $audit->user_info,
                        $audit->created_at->format('Y-m-d H:i:s'),
                    ];
                })->toArray()
            );
        }
    }
}
