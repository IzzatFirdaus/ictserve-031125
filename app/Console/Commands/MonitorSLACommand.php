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
    protected $signature = 'helpdesk:monitor-sla';

    protected $description = 'Monitor SLA compliance and send escalation notifications';

    public function handle(SLATrackingService $slaService): int
    {
        $this->info('Monitoring SLA compliance...');

        $escalated = $slaService->escalateApproachingBreaches();

        $this->info("Escalated {$escalated} tickets approaching SLA breach.");

        // Get statistics
        $stats = $slaService->getComplianceStats();
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Resolved', $stats['total']],
                ['Compliant', $stats['compliant']],
                ['Breached', $stats['breached']],
                ['Compliance Rate', $stats['compliance_rate'].'%'],
            ]
        );

        return self::SUCCESS;
    }
}
