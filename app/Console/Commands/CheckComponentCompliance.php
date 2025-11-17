<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\StandardsComplianceChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

<<<<<<< HEAD
=======
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
>>>>>>> origin/main
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

    public function handle(
        ComponentInventoryService $inventory,
        StandardsComplianceChecker $checker
    ): int {
        $this->info('Scanning frontend components...');

<<<<<<< HEAD
        /** @var array<string, mixed> $inventoryData */
        $inventoryData = $inventory->getInventory();
        $componentsSource = is_array($inventoryData['components'] ?? null)
            ? $inventoryData['components']
            : [];

        /** @var Collection<int, array<string, mixed>> $components */
        $components = collect($componentsSource);
=======
        // Scan all components
        $inventoryData = $inventory->getInventory();
        /** @phpstan-var ComponentInventory $inventoryData */
        /** @var Collection<int, array<string, mixed>> $components */
        $components = collect($inventoryData['components'] ?? [])
            ->map(static fn (array $component): array => $component);
>>>>>>> origin/main

        if ($components->isEmpty()) {
            $this->warn('No components found to audit.');

            return self::SUCCESS;
        }

        if ($type = $this->option('type')) {
            $components = $components->where('type', $type);
            $this->info("Filtered to {$components->count()} {$type} components.");
        }

        $this->newLine();
        $this->info('Running compliance checks...');

<<<<<<< HEAD
        /** @var array{statistics: array<string, mixed>, results: array<int, array<string, mixed>>, generated_at?: string} $report */
        $report = $checker->generateReport($components);

        /** @var array{
         *     total_components:int,
         *     average_compliance:float|null,
         *     by_type: array<string, array{count:int,average_compliance:float|null}>,
         *     critical_issues:int,
         *     high_issues:int,
         *     medium_issues:int,
         *     low_issues:int
         * } $statistics
         */
        $statistics = $report['statistics'];

        /** @var array<int, array<string, mixed>> $results */
        $results = $report['results'];

        $minScore = (int) ($this->option('min-score') ?: 80);

        $this->displayStatistics($statistics);
=======
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
>>>>>>> origin/main
        $this->displayResults($results, $minScore);

        if ($exportFormat = $this->option('export')) {
            $this->exportReport($report, (string) $exportFormat);
        }

<<<<<<< HEAD
=======
        // Determine exit code based on critical issues
>>>>>>> origin/main
        $criticalCount = (int) ($statistics['critical_issues'] ?? 0);

        if ($criticalCount > 0) {
            $this->error("{$criticalCount} critical compliance issues found.");

            return self::FAILURE;
        }

        $this->info('Compliance check completed successfully.');

        return self::SUCCESS;
    }

    /**
<<<<<<< HEAD
     * @param  array{
     *     total_components:int,
     *     average_compliance:float|null,
     *     by_type: array<string, array{count:int,average_compliance:float|null}>,
     *     critical_issues:int,
     *     high_issues:int,
     *     medium_issues:int,
     *     low_issues:int
=======
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
>>>>>>> origin/main
     * }  $statistics
     */
    protected function displayStatistics(array $statistics): void
    {
        $this->newLine();
<<<<<<< HEAD
        $this->info('Compliance Statistics:');
=======
        $this->info('📈 Compliance Statistics:');
        $average = $statistics['average_compliance'];
        $averageText = $average === null ? 'N/A' : $average.'%';
>>>>>>> origin/main
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Components', $statistics['total_components']],
<<<<<<< HEAD
                ['Average Compliance', ($statistics['average_compliance'] ?? 0).'%' ],
=======
                ['Average Compliance', $averageText],
>>>>>>> origin/main
                ['Critical Issues', $statistics['critical_issues']],
                ['High Issues', $statistics['high_issues']],
                ['Medium Issues', $statistics['medium_issues']],
                ['Low Issues', $statistics['low_issues']],
            ]
        );

        $this->newLine();
        $this->info('Compliance by Type:');

        $typeData = [];
        foreach ($statistics['by_type'] as $type => $data) {
<<<<<<< HEAD
            $average = $data['average_compliance'];
            $typeData[] = [
                (string) $type,
                (int) $data['count'],
                is_numeric($average) ? ((float) $average).'%' : 'N/A',
=======
            $typeAverage = $data['average_compliance'];
            $typeData[] = [
                (string) $type,
                $data['count'],
                $typeAverage === null ? 'N/A' : $typeAverage.'%',
>>>>>>> origin/main
            ];
        }

        $this->table(['Type', 'Count', 'Avg Compliance'], $typeData);
    }

    /**
<<<<<<< HEAD
     * @param  array<int, array<string, mixed>>  $results
=======
     * Display detailed compliance results
     *
     * @param  array<int, ComplianceResult>  $results
>>>>>>> origin/main
     */
    protected function displayResults(array $results, int $minScore): void
    {
        $this->newLine();
        $this->info('Detailed Results:');

<<<<<<< HEAD
        $failedComponents = collect($results)->filter(
            static function (array $result) use ($minScore): bool {
                $percentageValue = $result['compliance_percentage'] ?? null;
                $percentage = is_numeric($percentageValue) ? (float) $percentageValue : 0.0;
                $severityValue = $result['severity'] ?? '';
                $severity = is_string($severityValue) ? $severityValue : '';

                return $percentage < $minScore || $severity === 'critical';
            }
        );

        if ($failedComponents->isEmpty()) {
            $this->info('All components meet the minimum compliance score.');
=======
        $failedComponents = array_filter(
            $results,
            static fn (array $result): bool => (float) $result['compliance_percentage'] < $minScore
                || (string) $result['severity'] === 'critical'
        );

        /** @var array<int, ComplianceResult> $failedComponents */
        if ($failedComponents === []) {
            $this->info('✅ All components meet the minimum compliance score!');
>>>>>>> origin/main

            return;
        }

        foreach ($failedComponents as $result) {
            $this->displayComponentResult($result);
        }
    }

    /**
<<<<<<< HEAD
     * @param  array<string, mixed>  $result
=======
     * Display individual component result
     *
     * @param  ComplianceResult  $result
>>>>>>> origin/main
     */
    protected function displayComponentResult(array $result): void
    {
        $this->newLine();

<<<<<<< HEAD
        $severityValue = $result['severity'] ?? 'info';
        $severity = is_string($severityValue) ? $severityValue : 'info';
=======
        // Color-code severity
        $severity = (string) ($result['severity'] ?? '');
>>>>>>> origin/main
        $severityColor = match ($severity) {
            'critical' => 'red',
            'high' => 'yellow',
            'medium' => 'blue',
            default => 'gray',
        };

<<<<<<< HEAD
        $componentName = is_string($result['component'] ?? null) ? $result['component'] : 'Unknown';
        $type = is_string($result['type'] ?? null) ? $result['type'] : 'unknown';
        $path = is_string($result['path'] ?? null) ? $result['path'] : '';
        $complianceValue = $result['compliance_percentage'] ?? 0;
        $compliance = is_numeric($complianceValue) ? (float) $complianceValue : 0.0;
        $scoreValue = $result['score'] ?? 0;
        $score = is_numeric($scoreValue) ? (int) $scoreValue : 0;
        $maxScoreValue = $result['max_score'] ?? 0;
        $maxScore = is_numeric($maxScoreValue) ? (int) $maxScoreValue : 0;

        $this->line("<fg={$severityColor}>*</> {$componentName} ({$type})");
        $this->line("  Path: {$path}");
        $this->line("  Compliance: {$compliance}% ({$score}/{$maxScore})");
        $this->line('  Severity: '.strtoupper($severity));

        foreach ((array) ($result['checks'] ?? []) as $check) {
            if (! is_array($check)) {
                continue;
            }

            $passed = (bool) ($check['passed'] ?? false);
            $status = $passed ? 'OK' : 'FAIL';
            $color = $passed ? 'green' : 'red';
            $checkName = (string) ($check['name'] ?? 'Check');
            $percentage = (int) ($check['percentage'] ?? 0);

            $this->line("  <fg={$color}>{$status}</> {$checkName}: {$percentage}%");

            foreach ((array) ($check['issues'] ?? []) as $issue) {
                $this->line('    - '.(string) $issue);
=======
        $componentName = (string) ($result['component'] ?? '');
        $type = (string) ($result['type'] ?? '');
        $path = (string) ($result['path'] ?? '');
        $compliance = (string) ($result['compliance_percentage'] ?? '');
        $score = (string) ($result['score'] ?? '');
        $maxScore = (string) ($result['max_score'] ?? '');

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
>>>>>>> origin/main
            }
        }
    }

    /**
<<<<<<< HEAD
     * @param  array{
     *     statistics: array<string, mixed>,
     *     results: array<int, array<string, mixed>>,
     *     generated_at?: string
     * }  $report
=======
     * Export compliance report
     *
     * @param  array{statistics: array<string, mixed>, results: array<int, array<string, mixed>>, generated_at: string}  $report
>>>>>>> origin/main
     */
    protected function exportReport(array $report, string $format): void
    {
        /** @var array{statistics: array<string, mixed>, results: array<int, ComplianceResult>, generated_at: string} $report */
        $filename = storage_path('app/compliance-report-'.date('Y-m-d-His').".{$format}");

<<<<<<< HEAD
        if ($format === 'json') {
            $json = json_encode($report, JSON_PRETTY_PRINT);
            if ($json === false) {
                $this->error('Failed to encode report to JSON.');
=======
        match ($format) {
            'json' => File::put($filename, json_encode($report, JSON_PRETTY_PRINT) ?: ''),
            'html' => $this->exportHtml($report, $filename),
            'csv' => $this->exportCsv($report, $filename),
            default => $this->error("Unsupported export format: {$format}"),
        };
>>>>>>> origin/main

                return;
            }

            File::put($filename, $json);
        } elseif ($format === 'html') {
            $this->exportHtml($report, $filename);
        } elseif ($format === 'csv') {
            $this->exportCsv($report, $filename);
        } else {
            $this->error("Unsupported export format: {$format}");

            return;
        }

        $this->info("Report exported to: {$filename}");
    }

    /**
<<<<<<< HEAD
=======
     * Export report as HTML
     *
>>>>>>> origin/main
     * @param  array<string, mixed>  $report
     */
    protected function exportHtml(array $report, string $filename): void
    {
<<<<<<< HEAD
        /** @var view-string $view */
        $view = 'reports.compliance';

        $html = view($view, ['report' => $report])->render();
        File::put($filename, (string) $html);
    }

    /**
     * @param  array{results: array<int, array<string, mixed>>}  $report
=======
        $payload = json_encode($report, JSON_PRETTY_PRINT) ?: '';
        $html = '<!doctype html><html><body><pre>'.htmlspecialchars($payload, ENT_QUOTES).'</pre></body></html>';
        File::put($filename, $html);
    }

    /**
     * Export report as CSV
     *
     * @param  array{statistics: array<string, mixed>, results: array<int, ComplianceResult>, generated_at: string}  $report
>>>>>>> origin/main
     */
    protected function exportCsv(array $report, string $filename): void
    {
        $csv = fopen($filename, 'w');
        if ($csv === false) {
            $this->error('Unable to write CSV report.');

            return;
        }

        if ($csv === false) {
            $this->error('Unable to open CSV for writing.');

            return;
        }

        $toNumber = static function ($value): float {
            return is_numeric($value) ? (float) $value : 0.0;
        };
        $percentageFrom = static function (array $checks, string $key) use ($toNumber): float {
            $check = $checks[$key] ?? null;

            if (! is_array($check)) {
                return 0.0;
            }

            return $toNumber($check['percentage'] ?? null);
        };

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

<<<<<<< HEAD
        foreach ($report['results'] as $result) {
            $checks = is_array($result['checks'] ?? null) ? $result['checks'] : [];

            fputcsv($csv, [
                is_string($result['component'] ?? null) ? $result['component'] : '',
                is_string($result['type'] ?? null) ? $result['type'] : '',
                is_string($result['path'] ?? null) ? $result['path'] : '',
                $toNumber($result['compliance_percentage'] ?? null),
                $toNumber($result['score'] ?? null),
                $toNumber($result['max_score'] ?? null),
                is_string($result['severity'] ?? null) ? $result['severity'] : '',
                $percentageFrom($checks, 'metadata'),
                $percentageFrom($checks, 'accessibility'),
                $percentageFrom($checks, 'traceability'),
                $percentageFrom($checks, 'branding'),
                $percentageFrom($checks, 'bilingual'),
                $percentageFrom($checks, 'performance'),
=======
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
>>>>>>> origin/main
            ]);
        }

        fclose($csv);
    }
}

