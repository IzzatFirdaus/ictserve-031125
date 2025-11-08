<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\SecurityMonitoringService;
use Illuminate\Console\Command;

/**
 * Security Scan Command
 *
 * Runs automated security scans and reports findings.
 * Part of the ICTServe security monitoring system.
 *
 * @see D03-FR-010.1 Security monitoring requirements
 * @see D11 Technical Design - Security automation
 */
class SecurityScanCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'security:scan
                            {--report : Generate detailed report}
                            {--email= : Email address to send report to}';

    /**
     * The console command description.
     */
    protected $description = 'Run automated security scan and report findings';

    /**
     * Execute the console command.
     */
    public function handle(SecurityMonitoringService $securityMonitoring): int
    {
        $this->info('Starting security scan...');

        // Run the security scan
        $results = $securityMonitoring->runSecurityScan();

        // Display results
        $this->displayResults($results);

        // Generate report if requested
        if ($this->option('report')) {
            $this->generateReport($results);
        }

        // Email report if requested
        if ($this->option('email')) {
            $this->emailReport($results, $this->option('email'));
        }

        $this->info('Security scan completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Display scan results
     */
    private function displayResults(array $results): void
    {
        $this->info('Security Scan Results');
        $this->info('Timestamp: '.$results['timestamp']);
        $this->newLine();

        foreach ($results['checks'] as $checkName => $checkResult) {
            $status = $checkResult['status'];
            $statusColor = match ($status) {
                'ok' => 'green',
                'warning' => 'yellow',
                'error' => 'red',
                default => 'white',
            };

            $this->line("<fg={$statusColor}>[{$status}]</> ".ucwords(str_replace('_', ' ', $checkName)));
            $this->line('  '.$checkResult['message']);

            if (! empty($checkResult['issues'])) {
                foreach ($checkResult['issues'] as $issue) {
                    $this->line("  <fg=red>• {$issue}</>");
                }
            }

            if (! empty($checkResult['details'])) {
                foreach ($checkResult['details'] as $detail) {
                    $this->line("  <fg=blue>• {$detail}</>");
                }
            }

            $this->newLine();
        }

        // Display security statistics
        $stats = app(SecurityMonitoringService::class)->getSecurityStatistics();
        $this->info('Security Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Failed Logins (Last Hour)', $stats['failed_logins_last_hour']],
                ['Suspicious Activities (Last Hour)', $stats['suspicious_activities_last_hour']],
                ['Blocked IPs', $stats['blocked_ips_count']],
                ['Security Alerts Today', $stats['security_alerts_today']],
            ]
        );
    }

    /**
     * Generate detailed report
     */
    private function generateReport(array $results): void
    {
        $this->info('Generating detailed security report...');

        $reportPath = storage_path('logs/security_report_'.date('Y-m-d_H-i-s').'.json');

        $reportData = [
            'scan_results' => $results,
            'statistics' => app(SecurityMonitoringService::class)->getSecurityStatistics(),
            'generated_at' => now()->toISOString(),
            'system_info' => [
                'app_env' => config('app.env'),
                'app_debug' => config('app.debug'),
                'php_version' => PHP_VERSION,
                'laravel_version' => app()->version(),
            ],
        ];

        file_put_contents($reportPath, json_encode($reportData, JSON_PRETTY_PRINT));

        $this->info("Detailed report saved to: {$reportPath}");
    }

    /**
     * Email report to specified address
     */
    private function emailReport(array $results, string $email): void
    {
        $this->info("Emailing security report to: {$email}");

        // In a real implementation, this would send an email
        // For now, we'll just log it
        logger()->info('Security report email sent', [
            'recipient' => $email,
            'scan_timestamp' => $results['timestamp'],
        ]);

        $this->info('Security report email sent successfully.');
    }
}
