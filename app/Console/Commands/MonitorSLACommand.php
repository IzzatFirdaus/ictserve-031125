<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SLATrackingService;
use Illuminate\Console\Command;

/**
 * Monitor SLA Command
 *
 * Checks for tickets approaching SLA breach and sends escalation notifications.
 *
 * @trace Requirements 8.4, 10.3, 13.3
 */
class MonitorSLACommand extends Command
{
    // Artisan command signature (not credentials - Laravel command name convention)
    protected $signature = 'helpdesk:monitor-sla';

    protected $description = 'Monitor SLA compliance and send escalation notifications';

    public function handle(SLATrackingService $slaService): int
    {
        $this->info('Monitoring SLA compliance...');

        $escalated = $slaService->escalateApproachingBreaches();

        $this->info("Escalated {$escalated} tickets approaching SLA breach.");

        // Get statistics
        /** @var array{total: int|float, compliant: int|float, breached: int|float, compliance_rate: int|float|string} $stats */
        $stats = $slaService->getComplianceStats();
        $complianceRate = is_scalar($stats['compliance_rate']) ? (string) $stats['compliance_rate'] : '0';
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Resolved', $stats['total']],
                ['Compliant', $stats['compliant']],
                ['Breached', $stats['breached']],
                ['Compliance Rate', $complianceRate.'%'],
            ]
        );

        return self::SUCCESS;
    }
}
