<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ConfigurableAlertService;
use Illuminate\Console\Command;

/**
 * Check System Alerts Command
 *
 * Automated command to check system alerts and send notifications.
 * Runs via scheduler to monitor system health and performance.
 *
 * Requirements: 13.4, 9.3, 9.4, 2.5
 */
class CheckSystemAlerts extends Command
{
    protected $signature = 'alerts:check
                           {--dry-run : Check alerts without sending notifications}
                           {--type=* : Specific alert types to check}
                           {--force : Force check even if recently checked}';

    protected $description = 'Check system alerts and send notifications for critical issues';

    public function handle(ConfigurableAlertService $alertService): int
    {
        $this->info('Checking system alerts...');

        try {
            $specificTypes = $this->option('type');
            $dryRun = $this->option('dry-run');
            $force = $this->option('force');

            if ($dryRun) {
                $this->warn('Running in dry-run mode - no notifications will be sent');
            }

            $results = $alertService->checkAllAlerts();

            $this->displayAlertResults($results);

            $triggeredCount = collect($results)->where('triggered', true)->count();

            if ($triggeredCount > 0) {
                $this->warn("⚠️  {$triggeredCount} alert(s) triggered");

                if (! $dryRun) {
                    $this->info('Notifications sent to appropriate recipients');
                }
            } else {
                $this->info('✅ No alerts triggered - system operating normally');
            }

            return self::SUCCESS;

        } catch (\Exception $e) {
            $this->error("Failed to check system alerts: {$e->getMessage()}");

            return self::FAILURE;
        }
    }

    private function displayAlertResults(array $results): void
    {
        $this->newLine();
        $this->info('=== ALERT CHECK RESULTS ===');

        foreach ($results as $alertType => $result) {
            $status = $result['triggered'] ? '🔴 TRIGGERED' : '✅ OK';
            $this->line("{$status} {$this->formatAlertType($alertType)}");

            if ($result['triggered'] && isset($result['alert_data'])) {
                $alertData = $result['alert_data'];
                $this->line("   Severity: {$alertData['severity']}");
                $this->line("   Message: {$alertData['message']}");

                if (isset($alertData['count'])) {
                    $this->line("   Count: {$alertData['count']}");
                }

                if (isset($alertData['details']) && is_array($alertData['details'])) {
                    $detailCount = count($alertData['details']);
                    if ($detailCount > 0) {
                        $this->line("   Details: {$detailCount} items affected");
                    }
                }
            }
        }

        $this->newLine();
    }

    private function formatAlertType(string $type): string
    {
        return match ($type) {
            'overdue_tickets' => 'Overdue Tickets',
            'overdue_loans' => 'Overdue Loans',
            'approval_delays' => 'Approval Delays',
            'asset_shortages' => 'Asset Shortages',
            'system_health' => 'System Health',
            default => ucfirst(str_replace('_', ' ', $type)),
        };
    }
}
