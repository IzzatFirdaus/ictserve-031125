<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\StandardsComplianceChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
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
 *
 * @phpstan-import-type ComponentInventory from \App\Services\ComponentInventoryService
 * @phpstan-import-type ComponentInventoryItem from \App\Services\ComponentInventoryService
 *
 * @phpstan-type ComplianceResult array{
 *     component: string,
 *     type: string,
 *     path: string,
 *     compliance_percentage: float|int|string,
 *     score: float|int|string,
 *     max_score: float|int|string,
 *     severity: string,
 *     checks: array<string, array{
 *         passed: bool,
 *         name?: string,
 *         percentage: float|int|string,
 *         issues?: array<int, string>
 *     }>
 * }
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
        $inventoryData = $inventory->getInventory();
        /** @phpstan-var ComponentInventory $inventoryData */
        /** @var Collection<int, array<string, mixed>> $components */
        $components = collect($inventoryData['components'] ?? [])
            ->map(static fn (array $component): array => $component);

        if ($components->isEmpty()) {
            $this->warn('No components found to audit.');

            return self::SUCCESS;
        }

        $this->info("Found {$components->count()} components.");

        // Filter by type if specified
        $typeOption = $this->option('type');
        if ($typeOption && is_string($typeOption)) {
            $components = $components->where('type', $typeOption);
            $this->info("Filtered to {$components->count()} {$typeOption} components.");
        }

        $this->newLine();
        $this->info('📊 Running compliance checks...');

        // Generate compliance report
        /** @var array{statistics: array<string, mixed>, results: array<int, array<string, mixed>>, generated_at: string} $report */
        $report = $checker->generateReport($components);

        // Display statistics
        /** @var array{
         *     total_components: int,
         *     average_compliance: float|int|null,
         *     critical_issues: int,
         *     high_issues: int,
         *     medium_issues: int,
         *     low_issues: int,
         *     by_type: array<string, array{count: int, average_compliance: float|int|null}>
         * } $statistics */
        $statistics = $report['statistics'];
        $this->displayStatistics($statistics);

        // Display detailed results
        $minScore = (int) $this->option('min-score') ?: 80;
        /** @var array<int, ComplianceResult> $results */
        $results = $report['results'];
        $this->displayResults($results, $minScore);

        // Export if requested
        $exportFormat = $this->option('export');
        if ($exportFormat && is_string($exportFormat)) {
            $this->exportReport($report, $exportFormat);
        }

        // Determine exit code based on critical issues
        $criticalCount = (int) ($statistics['critical_issues'] ?? 0);

        if ($criticalCount > 0) {
            $this->error("\n❌ {$criticalCount} critical compliance issues found!");

            return self::FAILURE;
        }

        $this->info("\n✅ Compliance check completed successfully!");

        return self::SUCCESS;
    }

    /**
     * Display compliance statistics
     *
     * @param  array{
     *     total_components: int,
     *     average_compliance: float|int|null,
     *     critical_issues: int,
     *     high_issues: int,
     *     medium_issues: int,
     *     low_issues: int,
     *     by_type: array<string, array{count: int, average_compliance: float|int|null}>
     * }  $statistics
     */

    /**
     * @param  array<string, mixed>  $statistics
     */
    protected function displayStatistics(array $statistics): void
    {
        $this->newLine();
        $this->info('📈 Compliance Statistics:');

        $totalComponentsValue = $statistics['total_components'] ?? 0;
        $totalComponents = is_numeric($totalComponentsValue) ? (int) $totalComponentsValue : 0;

        $criticalIssuesValue = $statistics['critical_issues'] ?? 0;
        $criticalIssues = is_numeric($criticalIssuesValue) ? (int) $criticalIssuesValue : 0;

        $highIssuesValue = $statistics['high_issues'] ?? 0;
        $highIssues = is_numeric($highIssuesValue) ? (int) $highIssuesValue : 0;

        $mediumIssuesValue = $statistics['medium_issues'] ?? 0;
        $mediumIssues = is_numeric($mediumIssuesValue) ? (int) $mediumIssuesValue : 0;

        $lowIssuesValue = $statistics['low_issues'] ?? 0;
        $lowIssues = is_numeric($lowIssuesValue) ? (int) $lowIssuesValue : 0;

        $average = $statistics['average_compliance'] ?? null;
        $averageText = $average === null ? 'N/A' : (is_numeric($average) ? number_format((float) $average, 2) : 'N/A').'%';

        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Components', $totalComponents],
                ['Average Compliance', $averageText],
                ['Critical Issues', $criticalIssues],
                ['High Issues', $highIssues],
                ['Medium Issues', $mediumIssues],
                ['Low Issues', $lowIssues],
            ]
        );

        $this->newLine();
        $this->info('📊 Compliance by Type:');
        $typeData = [];
        $byType = is_array($statistics['by_type'] ?? null) ? $statistics['by_type'] : [];

        foreach ($byType as $type => $data) {
            if (! is_array($data)) {
                continue;
            }
            $typeAverage = $data['average_compliance'] ?? null;
            $count = is_numeric($data['count'] ?? 0) ? (int) $data['count'] : 0;
            $typeData[] = [
                (string) $type,
                $count,
                $typeAverage === null ? 'N/A' : (is_numeric($typeAverage) ? number_format((float) $typeAverage, 2) : 'N/A').'%',
            ];
        }
        $this->table(['Type', 'Count', 'Avg Compliance'], $typeData);
    }

    /**
     * Display detailed compliance results
     *
     * @param  array<int, array<string, mixed>>  $results
     */

    /**
     * @param  array<int, array<string, mixed>>  $results
     */
    protected function displayResults(array $results, int $minScore): void
    {
        $this->newLine();
        $this->info('🔍 Detailed Results:');

        $failedComponents = array_filter(
            $results,
            static function (array $result) use ($minScore): bool {
                $compliancePercentage = $result['compliance_percentage'] ?? 0;
                $severity = $result['severity'] ?? '';

                return (is_numeric($compliancePercentage) && (float) $compliancePercentage < $minScore)
                    || (is_string($severity) && $severity === 'critical');
            }
        );

        /** @var array<int, ComplianceResult> $failedComponents */
        if ($failedComponents === []) {
            $this->info('✅ All components meet the minimum compliance score!');

            return;
        }

        foreach ($failedComponents as $result) {
            $this->displayComponentResult($result);
        }
    }

    /**
     * Display individual component result
     *
     * @param  ComplianceResult  $result
     */

    /**
     * @param  array<string, mixed>  $result
     */
    protected function displayComponentResult(array $result): void
    {
        $this->newLine();

        // Color-code severity
        $severityValue = $result['severity'] ?? '';
        $severity = is_string($severityValue) ? $severityValue : '';
        $severityColor = match ($severity) {
            'critical' => 'red',
            'high' => 'yellow',
            'medium' => 'blue',
            default => 'gray',
        };

        $componentNameValue = $result['component'] ?? '';
        $componentName = is_string($componentNameValue) ? $componentNameValue : '';

        $typeValue = $result['type'] ?? '';
        $type = is_string($typeValue) ? $typeValue : '';

        $pathValue = $result['path'] ?? '';
        $path = is_string($pathValue) ? $pathValue : '';

        $complianceValue = $result['compliance_percentage'] ?? 0;
        $compliance = is_numeric($complianceValue) ? number_format((float) $complianceValue, 2) : '0';

        $scoreValue = $result['score'] ?? 0;
        $score = is_numeric($scoreValue) ? (string) $scoreValue : '0';

        $maxScoreValue = $result['max_score'] ?? 0;
        $maxScore = is_numeric($maxScoreValue) ? (string) $maxScoreValue : '0';

        $this->line("<fg={$severityColor}>■</> {$componentName} ({$type})");
        $this->line("  Path: {$path}");
        $this->line("  Compliance: {$compliance}% ({$score}/{$maxScore})");
        $this->line('  Severity: '.strtoupper($severity));

        // Display check results
        $checks = $result['checks'] ?? [];
        if (! is_array($checks)) {
            $checks = [];
        }
        /** @var array<string, array{passed: bool, name?: string, percentage: float|int|string, issues?: array<int, string>}> $checks */
        foreach ($checks as $checkName => $check) {
            $passed = (bool) ($check['passed'] ?? false);
            $status = $passed ? '✓' : '✗';
            $color = $passed ? 'green' : 'red';
            $checkLabel = (string) $checkName;
            $percentage = (string) ($check['percentage'] ?? '');

            $this->line("  <fg={$color}>{$status}</> {$checkLabel}: {$percentage}%");

            $issues = $check['issues'] ?? [];
            if (is_array($issues) && ! empty($issues)) {
                foreach ($issues as $issue) {
                    $this->line('    • '.(string) $issue);
                }
            }
        }
    }

    /**
     * Export compliance report
     *
     * @param  array{statistics: array<string, mixed>, results: array<int, array<string, mixed>>, generated_at: string}  $report
     */

    /**
     * @param  array<string, mixed>  $report
     */
    protected function exportReport(array $report, string $format): void
    {
        /** @var array{statistics: array<string, mixed>, results: array<int, ComplianceResult>, generated_at: string} $report */
        $filename = storage_path('app/compliance-report-'.date('Y-m-d-His').".{$format}");

        match ($format) {
            'json' => File::put($filename, json_encode($report, JSON_PRETTY_PRINT) ?: ''),
            'html' => $this->exportHtml($report, $filename),
            'csv' => $this->exportCsv($report, $filename),
            default => $this->error("Unsupported export format: {$format}"),
        };

        $this->info("\n📄 Report exported to: {$filename}");
    }

    /**
     * Export report as HTML
     *
     * @param  array<string, mixed>  $report
     */

    /**
     * @param  array<string, mixed>  $report
     */
    protected function exportHtml(array $report, string $filename): void
    {
        $payload = json_encode($report, JSON_PRETTY_PRINT) ?: '';
        $html = '<!doctype html><html><body><pre>'.htmlspecialchars($payload, ENT_QUOTES).'</pre></body></html>';
        File::put($filename, $html);
    }

    /**
     * Export report as CSV
     *
     * @param  array{statistics: array<string, mixed>, results: array<int, ComplianceResult>, generated_at: string}  $report
     */

    /**
     * @param  array<string, mixed>  $report
     */
    protected function exportCsv(array $report, string $filename): void
    {
        $csv = fopen($filename, 'w');
        if ($csv === false) {
            $this->error('Unable to write CSV report.');

            return;
        }

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

        $reportResults = $report['results'] ?? [];
        if (! is_array($reportResults)) {
            fclose($csv);

            return;
        }

        /** @var array<int, ComplianceResult> $reportResults */

        // Data
        foreach ($reportResults as $result) {
            /** @var ComplianceResult $result */
            $checks = $result['checks'];
            fputcsv($csv, [
                (string) ($result['component'] ?? ''),
                (string) ($result['type'] ?? ''),
                (string) ($result['path'] ?? ''),
                (string) ($result['compliance_percentage'] ?? ''),
                (string) ($result['score'] ?? ''),
                (string) ($result['max_score'] ?? ''),
                (string) ($result['severity'] ?? ''),
                (string) ($checks['metadata']['percentage'] ?? ''),
                (string) ($checks['accessibility']['percentage'] ?? ''),
                (string) ($checks['traceability']['percentage'] ?? ''),
                (string) ($checks['branding']['percentage'] ?? ''),
                (string) ($checks['bilingual']['percentage'] ?? ''),
                (string) ($checks['performance']['percentage'] ?? ''),
            ]);
        }

        fclose($csv);
    }
}
