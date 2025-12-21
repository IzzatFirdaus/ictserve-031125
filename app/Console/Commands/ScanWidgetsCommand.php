<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Contracts\WidgetRegistryInterface;
use App\Services\WidgetCategorizer;
use App\Services\WidgetDeduplicator;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Symfony\Component\Finder\Finder;

/**
 * Scan Widgets Command
 *
 * Artisan command to discover and register widgets from the
 * app/Filament/Widgets/ directory with automatic categorization
 * and duplicate detection.
 *
 * @trace Requirements: R1 (Widget Deduplication), R3 (Missing Widget Detection)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class ScanWidgetsCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'widgets:scan 
                           {--dry-run : Show what would be registered without making changes}
                           {--force : Force re-registration of existing widgets}
                           {--category= : Only scan widgets for specific category}
                           {--path= : Custom path to scan (default: app/Filament/Widgets)}';

    /**
     * The console command description.
     */
    protected $description = 'Scan and register widgets from the Filament widgets directory';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     */
    public function handle(
        WidgetRegistryInterface $widgetRegistry,
        WidgetCategorizer $categorizer,
        WidgetDeduplicator $deduplicator
    ): int {
        $this->info('🔍 Scanning for Filament widgets...');

        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');
        $categoryFilter = $this->option('category');
        $customPath = $this->option('path');

        // Validate category filter
        if ($categoryFilter && ! \in_array($categoryFilter, ['header', 'content', 'charts'])) {
            $this->error("Invalid category: {$categoryFilter}. Valid categories: header, content, charts");

            return self::FAILURE;
        }

        // Determine scan path
        $scanPath = $customPath ? base_path($customPath) : app_path('Filament/Widgets');

        if (! File::isDirectory($scanPath)) {
            $this->error("Widget directory not found: {$scanPath}");

            return self::FAILURE;
        }

        // Discover widget files
        $widgetFiles = $this->discoverWidgetFiles($scanPath);

        if (empty($widgetFiles)) {
            $this->warn('No widget files found in the specified directory.');

            return self::SUCCESS;
        }

        $this->info('Found '.\count($widgetFiles).' widget files');

        // Process each widget file
        $results = [
            'scanned' => 0,
            'registered' => 0,
            'skipped' => 0,
            'errors' => 0,
            'duplicates' => 0,
        ];

        $widgets = [];

        foreach ($widgetFiles as $file) {
            $results['scanned']++;

            try {
                $widgetClass = $this->getWidgetClassFromFile($file);

                if (! $widgetClass) {
                    $this->warn("Could not determine class name for: {$file}");
                    $results['errors']++;

                    continue;
                }

                // Validate widget class
                if (! class_exists($widgetClass)) {
                    $this->warn("Widget class does not exist: {$widgetClass}");
                    $results['errors']++;

                    continue;
                }

                // Detect category
                $category = $categorizer->detectCategory($widgetClass);

                // Apply category filter
                if ($categoryFilter && $category !== $categoryFilter) {
                    $results['skipped']++;

                    continue;
                }

                // Check if already registered
                if ($widgetRegistry->isRegistered($widgetClass) && ! $force) {
                    $this->line('  ⏭️  Skipping already registered: '.class_basename($widgetClass));
                    $results['skipped']++;

                    continue;
                }

                // Prepare widget configuration
                $config = [
                    'category' => $category,
                    'sort_order' => $categorizer->getNextSortOrder($widgets, $category),
                    'is_active' => true,
                    'roles' => $this->detectWidgetRoles($widgetClass),
                    'refresh_rate' => $this->getDefaultRefreshRate($category),
                    'cache_ttl' => $this->getDefaultCacheTtl($category),
                ];

                $widgets[$widgetClass] = $config;

                $this->line('  ✅ Found: '.class_basename($widgetClass)." (category: {$category})");
            } catch (\Exception $e) {
                $this->error("Error processing {$file}: ".$e->getMessage());
                $results['errors']++;
            }
        }

        // Check for duplicates
        $duplicates = $deduplicator->detectDuplicates($widgets);

        if (! empty($duplicates)) {
            $results['duplicates'] = \count($duplicates);
            $this->warn('⚠️  Found '.\count($duplicates).' duplicate widgets:');

            foreach ($duplicates as $duplicate) {
                $this->line('    • '.class_basename($duplicate['duplicate']).
                    ' (duplicate of '.class_basename($duplicate['original']).')');
            }

            if (! $isDryRun) {
                $widgets = $deduplicator->removeDuplicates($widgets);
                $this->info('Duplicates removed automatically');
            }
        }

        // Register widgets
        if (! $isDryRun) {
            foreach ($widgets as $widgetClass => $config) {
                try {
                    $widgetRegistry->register($widgetClass, $config);
                    $results['registered']++;
                } catch (\Exception $e) {
                    $this->error("Failed to register {$widgetClass}: ".$e->getMessage());
                    $results['errors']++;
                }
            }
        }

        // Display summary
        $this->displaySummary($results, $isDryRun);

        // Display validation results
        if (! empty($widgets)) {
            $validation = $categorizer->validatePlacement($widgets);
            $this->displayValidationResults($validation);
        }

        return $results['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    /**
     * Discover widget files in the specified directory
     *
     * @return array<string>
     */
    private function discoverWidgetFiles(string $path): array
    {
        $finder = new Finder;
        $files = [];

        try {
            $finder->files()
                ->in($path)
                ->name('*.php')
                ->notName('*Test.php')
                ->notName('*Trait.php');

            foreach ($finder as $file) {
                $files[] = $file->getRealPath();
            }
        } catch (\Exception $e) {
            $this->error('Error scanning directory: '.$e->getMessage());
        }

        return $files;
    }

    /**
     * Extract widget class name from file path
     */
    private function getWidgetClassFromFile(string $filePath): ?string
    {
        $relativePath = str_replace(app_path(), '', $filePath);
        $relativePath = ltrim($relativePath, '/\\');

        // Convert file path to namespace
        $namespace = 'App\\'.str_replace(['/', '\\', '.php'], ['\\', '\\', ''], $relativePath);

        return class_exists($namespace) ? $namespace : null;
    }

    /**
     * Detect appropriate roles for widget
     *
     * @return array<string>
     */
    private function detectWidgetRoles(string $widgetClass): array
    {
        $className = class_basename($widgetClass);

        // Sensitive widgets - superuser only
        if (
            str_contains($className, 'Audit') ||
            str_contains($className, 'Security') ||
            str_contains($className, 'Sensitive') ||
            str_contains($className, 'System')
        ) {
            return ['superuser'];
        }

        // Admin widgets
        if (
            str_contains($className, 'Admin') ||
            str_contains($className, 'Performance') ||
            str_contains($className, 'Health')
        ) {
            return ['admin', 'superuser'];
        }

        // Default - all roles
        return ['staff', 'admin', 'superuser'];
    }

    /**
     * Get default refresh rate based on category
     */
    private function getDefaultRefreshRate(string $category): int
    {
        return match ($category) {
            'header' => 60,    // 1 minute for stats
            'charts' => 300,   // 5 minutes for charts
            'content' => 120,  // 2 minutes for content
            default => 300,
        };
    }

    /**
     * Get default cache TTL based on category
     */
    private function getDefaultCacheTtl(string $category): int
    {
        return match ($category) {
            'header' => 300,   // 5 minutes for stats
            'charts' => 900,   // 15 minutes for charts
            'content' => 600,  // 10 minutes for content
            default => 600,
        };
    }

    /**
     * Display command summary
     *
     * @param  array<string, int>  $results
     */
    private function displaySummary(array $results, bool $isDryRun): void
    {
        $this->newLine();
        $this->info('📊 Scan Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Files Scanned', $results['scanned']],
                ['Widgets '.($isDryRun ? 'Found' : 'Registered'), $isDryRun ? $results['scanned'] - $results['errors'] : $results['registered']],
                ['Skipped', $results['skipped']],
                ['Duplicates Found', $results['duplicates']],
                ['Errors', $results['errors']],
            ]
        );

        if ($isDryRun) {
            $this->info('🔍 This was a dry run. Use --force to actually register widgets.');
        }
    }

    /**
     * Display validation results
     *
     * @param  array<string, mixed>  $validation
     */
    private function displayValidationResults(array $validation): void
    {
        $this->newLine();
        $this->info('🔍 Widget Placement Validation:');

        if ($validation['is_valid']) {
            $this->info('✅ All widgets pass placement validation');
        } else {
            $this->warn('⚠️  Found placement violations:');

            foreach ($validation['violations'] as $violation) {
                $severity = $violation['severity'] === 'error' ? '❌' : '⚠️';
                $this->line("  {$severity} {$violation['category']}: {$violation['rule']}");
            }
        }

        // Display summary
        $summary = $validation['summary'];
        $this->table(
            ['Category', 'Widget Count'],
            [
                ['Header', $summary['header_count']],
                ['Content', $summary['content_count']],
                ['Charts', $summary['charts_count']],
                ['Total', $summary['total_count']],
            ]
        );
    }
}
