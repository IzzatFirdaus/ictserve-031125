<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SlaMonitoringService;
use Illuminate\Console\Command;

/**
 * Send SLA Alerts Command
 *
 * Sends email alerts to approvers for loan applications at risk of SLA breach.
 * Should be scheduled to run every hour during business hours.
 *
 * @see D03-FR-023.7 (SLA monitoring and alerts)
 */
class SendSlaAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sla:send-alerts
                            {--dry-run : Show what would be sent without actually sending}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send SLA alert emails to approvers for applications at risk of breach';

    /**
     * Execute the console command.
     */
    public function handle(SlaMonitoringService $slaService): int
    {
        $this->info('Checking SLA status for pending loan applications...');

        // Get summary first
        $summary = $slaService->getSlaSummary();

        $this->table(
            ['Status', 'Count'],
            [
                ['Total Pending', $summary['total_pending']],
                ['On Track', $summary['ok']],
                ['Warning', $summary['warning']],
                ['Critical', $summary['critical']],
                ['Breached', $summary['breached']],
                ['Compliance Rate', $summary['compliance_rate'].'%'],
            ]
        );

        if ($summary['warning'] + $summary['critical'] + $summary['breached'] === 0) {
            $this->info('No applications at risk. No alerts to send.');

            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn('Dry run mode - no emails will be sent.');

            $applicationsAtRisk = $slaService->getApplicationsAtRisk();

            $this->info("Would send alerts for {$applicationsAtRisk->count()} application(s):");

            foreach ($applicationsAtRisk as $application) {
                $sla = $slaService->getSlaStatus($application);
                $this->line("  - {$application->application_number}: {$sla['status']} ({$sla['hours_elapsed']}h elapsed)");
            }

            return self::SUCCESS;
        }

        $this->info('Sending SLA alerts...');

        $results = $slaService->sendSlaAlerts();

        $this->info("Alerts sent: {$results['sent']}");

        if ($results['failed'] > 0) {
            $this->warn("Failed to send: {$results['failed']}");

            return self::FAILURE;
        }

        return self::SUCCESS;
    }
}
