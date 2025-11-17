<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\StandardsComplianceChecker;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\File;

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

        /** @var array<string, mixed> $inventoryData */
        $inventoryData = $inventory->getInventory();
        $componentsSource = is_array($inventoryData['components'] ?? null)
            ? $inventoryData['components']
            : [];

        /** @var Collection<int, array<string, mixed>> $components */
        $components = collect($componentsSource);

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
        $this->displayResults($results, $minScore);

        if ($exportFormat = $this->option('export')) {
            $this->exportReport($report, (string) $exportFormat);
        }

        $criticalCount = (int) ($statistics['critical_issues'] ?? 0);

        if ($criticalCount > 0) {
            $this->error("{$criticalCount} critical compliance issues found.");

            return self::FAILURE;
        }

        $this->info('Compliance check completed successfully.');

        return self::SUCCESS;
    }

    /**
     * @param  array{
     *     total_components:int,
     *     average_compliance:float|null,
     *     by_type: array<string, array{count:int,average_compliance:float|null}>,
     *     critical_issues:int,
     *     high_issues:int,
     *     medium_issues:int,
     *     low_issues:int
     * }  $statistics
     */
    protected function displayStatistics(array $statistics): void
    {
        $this->newLine();
        $this->info('Compliance Statistics:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Total Components', $statistics['total_components']],
                ['Average Compliance', ($statistics['average_compliance'] ?? 0).'%' ],
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
            $average = $data['average_compliance'];
            $typeData[] = [
                (string) $type,
                (int) $data['count'],
                is_numeric($average) ? ((float) $average).'%' : 'N/A',
            ];
        }

        $this->table(['Type', 'Count', 'Avg Compliance'], $typeData);
    }

    /**
     * @param  array<int, array<string, mixed>>  $results
     */
    protected function displayResults(array $results, int $minScore): void
    {
        $this->newLine();
        $this->info('Detailed Results:');

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

            return;
        }

        foreach ($failedComponents as $result) {
            $this->displayComponentResult($result);
        }
    }

    /**
     * @param  array<string, mixed>  $result
     */
    protected function displayComponentResult(array $result): void
    {
        $this->newLine();

        $severityValue = $result['severity'] ?? 'info';
        $severity = is_string($severityValue) ? $severityValue : 'info';
        $severityColor = match ($severity) {
            'critical' => 'red',
            'high' => 'yellow',
            'medium' => 'blue',
            default => 'gray',
        };

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
            }
        }
    }

    /**
     * @param  array{
     *     statistics: array<string, mixed>,
     *     results: array<int, array<string, mixed>>,
     *     generated_at?: string
     * }  $report
     */
    protected function exportReport(array $report, string $format): void
    {
        $filename = storage_path('app/compliance-report-'.date('Y-m-d-His').".{$format}");

        if ($format === 'json') {
            $json = json_encode($report, JSON_PRETTY_PRINT);
            if ($json === false) {
                $this->error('Failed to encode report to JSON.');

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
     * @param  array<string, mixed>  $report
     */
    protected function exportHtml(array $report, string $filename): void
    {
        /** @var view-string $view */
        $view = 'reports.compliance';

        $html = view($view, ['report' => $report])->render();
        File::put($filename, (string) $html);
    }

    /**
     * @param  array{results: array<int, array<string, mixed>>}  $report
     */
    protected function exportCsv(array $report, string $filename): void
    {
        $csv = fopen($filename, 'w');

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
            ]);
        }

        fclose($csv);
    }
}

