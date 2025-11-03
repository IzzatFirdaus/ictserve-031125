<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\StandardsComplianceChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Check Component Compliance Command
 *
 * Audits all frontend components against D00-D15 standards.
 *
 * @trace D03-FR-016.1, D03-FR-017.1, D03-FR-018.1
 * @trace D04 §8.1 (Component Compliance Checking)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class CheckComponentCompliance extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:compliance
                            {--type= : Filter by component type (blade_component, livewire_component, etc.)}
                            {--export= : Export report to file (json, html, csv)}
                            {--min-score= : Minimum compliance score to pass (default: 80)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit frontend components against D00-D15 standards';

    /**
     * Execute the console command.
     */
    public function handle(
        ComponentInventoryService $inventory,
        StandardsComplianceChecker $checker
    ): int {
        $this->info('🔍 Scanning frontend components...');

        // Scan all components
        $inventory = $inventory->getInventory();
        $components = collect($inventory['components'] ?? []);

        if ($components->isEmpty()) {
            $this->warn('No components found to audit.');

            return self::SUCCESS;
        }

        $this->info("Found {$components->count()} components.");

        // Filter by type if specified
        if ($type = $this->option('type')) {
            $components = $components->where('type', $type);
            $this->info("Filtered to {$components->count()} {$type} components.");
        }

        $this->newLine();
        $this->info('📊 Running compliance checks...');

        // Generate compliance report
        $report = $checker->generateReport($components);

        // Display statistics
        $this->displayStatistics($report['statistics']);

        // Display detailed results
        $this->displayResults($report['results'], (int) $this->option('min-score') ?: 80);

        // Export if requested
        if ($exportFormat = $this->option('export')) {
            $this->exportReport($report, $exportFormat);
        }

        // Determine exit code based on critical issues
        $criticalCount = $report['statistics']['critical_issues'] ?? 0;

        if ($criticalCount > 0) {
            $this->error("\n❌ {$criticalCount} critical compliance issues found!");

            return self::FAILURE;
        }

        $this->info("\n✅ Compliance check completed successfully!");

        return self::SUCCESS;
    }

    /**
     * Display compliance statistics
     */
    protected function displayStatistics(array $statistics): void
    {
        $this->newLine();
        $this->info('📈 Compliance Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Components', $statistics['total_components']],
                ['Average Compliance', $statistics['average_compliance'].'%'],
                ['Critical Issues', $statistics['critical_issues']],
                ['High Issues', $statistics['high_issues']],
                ['Medium Issues', $statistics['medium_issues']],
                ['Low Issues', $statistics['low_issues']],
            ]
        );

        $this->newLine();
        $this->info('📊 Compliance by Type:');
        $typeData = [];
        foreach ($statistics['by_type'] as $type => $data) {
            $typeData[] = [
                $type,
                $data['count'],
                $data['average_compliance'].'%',
            ];
        }
        $this->table(['Type', 'Count', 'Avg Compliance'], $typeData);
    }

    /**
     * Display detailed compliance results
     */
    protected function displayResults(array $results, int $minScore): void
    {
        $this->newLine();
        $this->info('🔍 Detailed Results:');

        $failedComponents = collect($results)->filter(function ($result) use ($minScore) {
            return $result['compliance_percentage'] < $minScore || $result['severity'] === 'critical';
        });

        if ($failedComponents->isEmpty()) {
            $this->info('✅ All components meet the minimum compliance score!');

            return;
        }

        foreach ($failedComponents as $result) {
            $this->displayComponentResult($result);
        }
    }

    /**
     * Display individual component result
     */
    protected function displayComponentResult(array $result): void
    {
        $this->newLine();

        // Color-code severity
        $severityColor = match ($result['severity']) {
            'critical' => 'red',
            'high' => 'yellow',
            'medium' => 'blue',
            default => 'gray',
        };

        $this->line("<fg={$severityColor}>■</> {$result['component']} ({$result['type']})");
        $this->line("  Path: {$result['path']}");
        $this->line("  Compliance: {$result['compliance_percentage']}% ({$result['score']}/{$result['max_score']})");
        $this->line('  Severity: '.strtoupper($result['severity']));

        // Display check results
        foreach ($result['checks'] as $checkName => $check) {
            $status = $check['passed'] ? '✓' : '✗';
            $color = $check['passed'] ? 'green' : 'red';

            $this->line("  <fg={$color}>{$status}</> {$check['name']}: {$check['percentage']}%");

            if (! empty($check['issues'])) {
                foreach ($check['issues'] as $issue) {
                    $this->line("    • {$issue}");
                }
            }
        }
    }

    /**
     * Export compliance report
     */
    protected function exportReport(array $report, string $format): void
    {
        $filename = storage_path('app/compliance-report-'.date('Y-m-d-His').".{$format}");

        match ($format) {
            'json' => File::put($filename, json_encode($report, JSON_PRETTY_PRINT)),
            'html' => $this->exportHtml($report, $filename),
            'csv' => $this->exportCsv($report, $filename),
            default => $this->error("Unsupported export format: {$format}"),
        };

        $this->info("\n📄 Report exported to: {$filename}");
    }

    /**
     * Export report as HTML
     */
    protected function exportHtml(array $report, string $filename): void
    {
        $html = view('reports.compliance', ['report' => $report])->render();
        File::put($filename, $html);
    }

    /**
     * Export report as CSV
     */
    protected function exportCsv(array $report, string $filename): void
    {
        $csv = fopen($filename, 'w');

        // Header
        fputcsv($csv, [
            'Component',
            'Type',
            'Path',
            'Compliance %',
            'Score',
            'Max Score',
            'Severity',
            'Metadata %',
            'Accessibility %',
            'Traceability %',
            'Branding %',
            'Bilingual %',
            'Performance %',
        ]);

        // Data
        foreach ($report['results'] as $result) {
            fputcsv($csv, [
                $result['component'],
                $result['type'],
                $result['path'],
                $result['compliance_percentage'],
                $result['score'],
                $result['max_score'],
                $result['severity'],
                $result['checks']['metadata']['percentage'],
                $result['checks']['accessibility']['percentage'],
                $result['checks']['traceability']['percentage'],
                $result['checks']['branding']['percentage'],
                $result['checks']['bilingual']['percentage'],
                $result['checks']['performance']['percentage'],
            ]);
        }

        fclose($csv);
    }
}
