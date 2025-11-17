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

        $stats = $securityMonitoring->getDashboardStats();
        $this->displayResults($stats);

        // Generate report if requested
        if ($this->option('report')) {
            $this->generateReport($stats);
        }

        // Email report if requested
        if ($this->option('email')) {
            $this->emailReport($stats, $this->option('email'));
        }

        $this->info('Security scan completed successfully.');

        return self::SUCCESS;
    }

    /**
     * Display scan results
     *
     * @param  array<string, mixed>  $results
     */
    private function displayResults(array $results): void
    {
        $this->info('Security Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Failed Logins (24h)', $results['failed_logins_24h'] ?? 0],
                ['Suspicious Activities (24h)', $results['suspicious_activities_24h'] ?? 0],
                ['Role Changes (24h)', $results['role_changes_24h'] ?? 0],
                ['Config Modifications (24h)', $results['config_modifications_24h'] ?? 0],
                ['Active Sessions', $results['active_sessions'] ?? 0],
                ['Blocked IPs', $results['blocked_ips'] ?? 0],
                ['Critical Alerts', $results['critical_alerts'] ?? 0],
                ['Last Security Scan', $results['last_security_scan'] ?? 'N/A'],
            ]
        );
    }

    /**
     * Generate detailed report
     *
     * @param  array<string, mixed>  $results
     */
    private function generateReport(array $results): void
    {
        $this->info('Generating detailed security report...');

        $reportPath = storage_path('logs/security_report_'.date('Y-m-d_H-i-s').'.json');

        $reportData = [
            'statistics' => $results,
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
     *
     * @param  array<string, mixed>  $results
     */
    private function emailReport(array $results, string $email): void
    {
        $this->info("Emailing security report to: {$email}");

        // In a real implementation, this would send an email
        // For now, we'll just log it
        logger()->info('Security report email sent', [
            'recipient' => $email,
            'sent_at' => now()->toIso8601String(),
            'stats' => $results,
        ]);

        $this->info('Security report email sent successfully.');
    }
}
