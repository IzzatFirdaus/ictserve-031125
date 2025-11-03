<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Component Inventory Command
 *
 * Artisan command for auditing the unified component library.
 * Generates comprehensive inventory reports and identifies issues.
 *
 * @command
 *
 * @name component:inventory
 *
 * @description Audit component library and generate inventory report
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 11.1, 17.1, 17.2
 * Standards: D04 §6.1, D10 §7
 */
class ComponentInventoryCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'component:inventory
                            {--output= : Output file path for the report}
                            {--format=text : Report format (text, json, html)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Audit component library and generate comprehensive inventory report';

    /**
     * Execute the console command.
     */
    public function handle(ComponentInventoryService $service): int
    {
        $this->info('🔍 Auditing component library...');
        $this->newLine();

        $inventory = $service->getInventory();

        // Display summary
        $this->displaySummary($inventory);

        // Display category breakdown
        $this->displayCategoryBreakdown($inventory);

        // Display issues
        $this->displayIssues($inventory);

        // Generate report if output specified
        if ($output = $this->option('output')) {
            $this->generateReport($service, $output);
        }

        $this->newLine();
        $this->info('✅ Component inventory audit complete!');

        return Command::SUCCESS;
    }

    /**
     * Display inventory summary
     *
     * @param  array<string, mixed>  $inventory
     */
    private function displaySummary(array $inventory): void
    {
        $this->info('📊 Summary');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Components', $inventory['total_components']],
                ['Categories', count($inventory['by_category'])],
                ['Obsolete Components', count($inventory['obsolete'])],
                ['Duplicate Components', count($inventory['duplicates'])],
            ]
        );
        $this->newLine();
    }

    /**
     * Display category breakdown
     *
     * @param  array<string, mixed>  $inventory
     */
    private function displayCategoryBreakdown(array $inventory): void
    {
        $this->info('📁 Components by Category');

        $rows = [];
        foreach ($inventory['by_category'] as $category => $data) {
            $withMetadata = count(array_filter($data['components'], fn ($c) => $c['has_metadata']));
            $wcagCompliant = count(array_filter($data['components'], fn ($c) => $c['wcag_compliant']));
            $deprecated = count(array_filter($data['components'], fn ($c) => $c['uses_deprecated_colors']));

            $rows[] = [
                ucfirst($category),
                $data['count'],
                $withMetadata,
                $wcagCompliant,
                $deprecated > 0 ? "<fg=red>{$deprecated}</>" : '0',
            ];
        }

        $this->table(
            ['Category', 'Total', 'With Metadata', 'WCAG Compliant', 'Deprecated Colors'],
            $rows
        );
        $this->newLine();
    }

    /**
     * Display identified issues
     *
     * @param  array<string, mixed>  $inventory
     */
    private function displayIssues(array $inventory): void
    {
        if (! empty($inventory['obsolete'])) {
            $this->warn('⚠️  Obsolete Components Found');
            foreach ($inventory['obsolete'] as $component) {
                $this->line("  - {$component['name']} ({$component['category']})");
            }
            $this->newLine();
        }

        if (! empty($inventory['duplicates'])) {
            $this->warn('⚠️  Duplicate Components Found');
            foreach ($inventory['duplicates'] as $duplicate) {
                $this->line("  - {$duplicate['name']}");
                foreach ($duplicate['instances'] as $instance) {
                    $this->line("    → {$instance['relative_path']}");
                }
            }
            $this->newLine();
        }

        // Check for components without metadata
        $withoutMetadata = array_filter($inventory['components'], fn ($c) => ! $c['has_metadata']);
        if (! empty($withoutMetadata)) {
            $this->warn('⚠️  Components Without Metadata: '.count($withoutMetadata));
            $this->line('  Run: php artisan component:add-metadata to fix');
            $this->newLine();
        }

        // Check for components with deprecated colors
        $withDeprecated = array_filter($inventory['components'], fn ($c) => $c['uses_deprecated_colors']);
        if (! empty($withDeprecated)) {
            $this->error('❌ Components Using Deprecated Colors: '.count($withDeprecated));
            foreach ($withDeprecated as $component) {
                $this->line("  - {$component['name']} ({$component['category']})");
            }
            $this->newLine();
        }
    }

    /**
     * Generate and save report
     */
    private function generateReport(ComponentInventoryService $service, string $output): void
    {
        $this->info("📝 Generating report: {$output}");

        $format = $this->option('format');

        $content = match ($format) {
            'json' => json_encode($service->getInventory(), JSON_PRETTY_PRINT),
            'html' => $this->generateHtmlReport($service->getInventory()),
            default => $service->generateReport(),
        };

        File::put($output, $content);

        $this->info("✅ Report saved to: {$output}");
    }

    /**
     * Generate HTML report
     *
     * @param  array<string, mixed>  $inventory
     */
    private function generateHtmlReport(array $inventory): string
    {
        // Simple HTML report generation
        $html = '<!DOCTYPE html><html><head><title>Component Inventory Report</title>';
        $html .= '<style>body{font-family:sans-serif;margin:20px;}table{border-collapse:collapse;width:100%;}';
        $html .= 'th,td{border:1px solid #ddd;padding:8px;text-align:left;}th{background:#0056b3;color:white;}</style>';
        $html .= '</head><body>';
        $html .= '<h1>Component Library Inventory Report</h1>';
        $html .= '<p>Generated: '.date('Y-m-d H:i:s').'</p>';
        $html .= '<h2>Summary</h2>';
        $html .= '<ul>';
        $html .= "<li>Total Components: {$inventory['total_components']}</li>";
        $html .= '<li>Obsolete Components: '.count($inventory['obsolete']).'</li>';
        $html .= '<li>Duplicate Components: '.count($inventory['duplicates']).'</li>';
        $html .= '</ul>';
        $html .= '</body></html>';

        return $html;
    }
}
