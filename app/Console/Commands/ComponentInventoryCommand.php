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
 *
 * @phpstan-import-type ComponentInventory from \App\Services\ComponentInventoryService
 * @phpstan-import-type ComponentInventoryItem from \App\Services\ComponentInventoryService
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
        /** @phpstan-var ComponentInventory $inventory */

        // Display summary
        $this->displaySummary($inventory);

        // Display category breakdown
        $this->displayCategoryBreakdown($inventory);

        // Display issues
        $this->displayIssues($inventory);

        // Generate report if output specified
        $outputOption = $this->option('output');
        if ($outputOption && is_string($outputOption)) {
            $this->generateReport($service, $outputOption);
        }

        $this->newLine();
        $this->info('✅ Component inventory audit complete!');

        return Command::SUCCESS;
    }

    /**
     * Display inventory summary
     *
     * @param  ComponentInventory  $inventory
     */

    /**
     * @param  array<string, mixed>  $inventory
     */
    private function displaySummary(array $inventory): void
    {
        $this->info('📊 Summary');

        $totalComponentsValue = $inventory['total_components'] ?? 0;
        $totalComponents = is_numeric($totalComponentsValue) ? (int) $totalComponentsValue : 0;
        $byCategory = is_array($inventory['by_category'] ?? null) ? $inventory['by_category'] : [];
        $obsolete = is_array($inventory['obsolete'] ?? null) ? $inventory['obsolete'] : [];
        $duplicates = is_array($inventory['duplicates'] ?? null) ? $inventory['duplicates'] : [];

        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Components', $totalComponents],
                ['Categories', count($byCategory)],
                ['Obsolete Components', count($obsolete)],
                ['Duplicate Components', count($duplicates)],
            ]
        );
        $this->newLine();
    }

    /**
     * Display category breakdown
     *
     * @param  ComponentInventory  $inventory
     */

    /**
     * @param  array<string, mixed>  $inventory
     */
    private function displayCategoryBreakdown(array $inventory): void
    {
        $this->info('📁 Components by Category');

        $rows = [];
        $categories = is_array($inventory['by_category'] ?? null) ? $inventory['by_category'] : [];

        foreach ($categories as $category => $data) {
            if (! is_array($data)) {
                continue;
            }

            $components = is_array($data['components'] ?? null) ? $data['components'] : [];
            $count = is_numeric($data['count'] ?? 0) ? (int) $data['count'] : 0;

            $withMetadata = count(array_filter($components, static function (mixed $component): bool {
                return is_array($component) && (bool) ($component['has_metadata'] ?? false);
            }));
            $wcagCompliant = count(array_filter($components, static function (mixed $component): bool {
                return is_array($component) && (bool) ($component['wcag_compliant'] ?? false);
            }));
            $deprecated = count(array_filter($components, static function (mixed $component): bool {
                return is_array($component) && (bool) ($component['uses_deprecated_colors'] ?? false);
            }));

            $rows[] = [
                ucfirst((string) $category),
                $count,
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
     * @param  ComponentInventory  $inventory
     */

    /**
     * @param  array<string, mixed>  $inventory
     */
    private function displayIssues(array $inventory): void
    {
        $obsolete = is_array($inventory['obsolete'] ?? null) ? $inventory['obsolete'] : [];
        if (! empty($obsolete)) {
            $this->warn('⚠️  Obsolete Components Found');
            foreach ($obsolete as $component) {
                if (! is_array($component)) {
                    continue;
                }
                $name = $component['name'] ?? 'Unknown';
                $category = $component['category'] ?? 'Unknown';
                $this->line("  - {$name} ({$category})");
            }
            $this->newLine();
        }

        $duplicates = is_array($inventory['duplicates'] ?? null) ? $inventory['duplicates'] : [];
        if (! empty($duplicates)) {
            $this->warn('⚠️  Duplicate Components Found');
            foreach ($duplicates as $duplicate) {
                if (! is_array($duplicate)) {
                    continue;
                }
                $name = $duplicate['name'] ?? 'Unknown';
                $this->line("  - {$name}");
                $instances = is_array($duplicate['instances'] ?? null) ? $duplicate['instances'] : [];
                foreach ($instances as $instance) {
                    if (! is_array($instance)) {
                        continue;
                    }
                    $relativePath = $instance['relative_path'] ?? 'Unknown';
                    $this->line("    → {$relativePath}");
                }
            }
            $this->newLine();
        }

        // Check for components without metadata
        $components = is_array($inventory['components'] ?? null) ? $inventory['components'] : [];
        $withoutMetadata = array_filter($components, static function (mixed $c): bool {
            return is_array($c) && ! ($c['has_metadata'] ?? false);
        });
        if (! empty($withoutMetadata)) {
            $this->warn('⚠️  Components Without Metadata: '.count($withoutMetadata));
            $this->line('  Run: php artisan component:add-metadata to fix');
            $this->newLine();
        }

        // Check for components with deprecated colors
        $withDeprecated = array_filter($components, static function (mixed $c): bool {
            return is_array($c) && ($c['uses_deprecated_colors'] ?? false);
        });
        if (! empty($withDeprecated)) {
            $this->error('❌ Components Using Deprecated Colors: '.count($withDeprecated));
            foreach ($withDeprecated as $component) {
                if (! is_array($component)) {
                    continue;
                }
                $name = $component['name'] ?? 'Unknown';
                $category = $component['category'] ?? 'Unknown';
                $this->line("  - {$name} ({$category})");
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
            'json' => json_encode($service->getInventory(), JSON_PRETTY_PRINT) ?: '',
            'html' => $this->generateHtmlReport($service->getInventory()),
            default => $service->generateReport(),
        };

        File::put($output, $content);

        $this->info("✅ Report saved to: {$output}");
    }

    /**
     * Generate HTML report
     *
     * @param  ComponentInventory  $inventory
     */

    /**
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

        $totalComponentsValue = $inventory['total_components'] ?? 0;
        $totalComponents = is_numeric($totalComponentsValue) ? (int) $totalComponentsValue : 0;
        $obsolete = is_array($inventory['obsolete'] ?? null) ? $inventory['obsolete'] : [];
        $duplicates = is_array($inventory['duplicates'] ?? null) ? $inventory['duplicates'] : [];

        $html .= "<li>Total Components: {$totalComponents}</li>";
        $html .= '<li>Obsolete Components: '.count($obsolete).'</li>';
        $html .= '<li>Duplicate Components: '.count($duplicates).'</li>';
        $html .= '</ul>';
        $html .= '</body></html>';

        return $html;
    }
}
