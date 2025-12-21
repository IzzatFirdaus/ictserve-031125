<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

/**
 * Update Widget Metadata Command
 *
 * Batch updates all widgets to include WidgetMetadata trait
 * and proper documentation references following Filament v4 patterns.
 *
 * @trace Requirements: R2 (Widget Organization), R10 (Role-Based Access)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class UpdateWidgetMetadataCommand extends Command
{
    protected $signature = 'widgets:update-metadata 
                           {--dry-run : Show what would be updated without making changes}
                           {--force : Force update even if trait already exists}';

    protected $description = 'Update all widgets to include WidgetMetadata trait and documentation references';

    /**
     * Widget-specific role configurations
     */
    private const WIDGET_ROLES = [
        'SystemHealthWidget' => ['superuser'],
        'SystemMetricsWidget' => ['superuser'],
        'SensitiveAccessLogWidget' => ['superuser'],
        'UserActivityWidget' => ['superuser'],
        'UserActivityStatsWidget' => ['superuser'],
        'PerformanceMetricsWidget' => ['admin', 'superuser'],
        'HorizonHealthWidget' => ['admin', 'superuser'],
        'SlowQueriesTableWidget' => ['admin', 'superuser'],
        'HealthCheckTableWidget' => ['admin', 'superuser'],
    ];

    /**
     * Widget-specific documentation references
     */
    private const WIDGET_DOCS = [
        'UnifiedDashboardOverview' => 'D04 §3.2 Dashboard widgets, D12 §9 Performance optimization patterns',
        'HelpdeskStatsOverview' => 'D04 §3.2 Dashboard widgets, D03 SRS-ADM-003',
        'AssetLoanStatsOverview' => 'D04 §3.2 Dashboard widgets, D03 SRS-AST-001',
        'TicketsByStatusChart' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'TicketVolumeChart' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'ResolutionTimeChart' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'LoanAnalyticsWidget' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'CrossModuleIntegrationChart' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'UnifiedAnalyticsChart' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'EnhancedUnifiedAnalyticsChart' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'AssetUtilizationWidget' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'AssetAvailabilityCalendarWidget' => 'D04 §3.2 Dashboard widgets, D12 WCAG 2.2 AA compliance',
        'RecentActivityFeedWidget' => 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration',
        'RecentTicketsTable' => 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration',
        'CriticalAlertsWidget' => 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration',
        'QuickActionsWidget' => 'D04 §3.2 Dashboard widgets, D12 §3.4 User Experience',
        'ThemeToggleWidget' => 'D04 §3.2 Dashboard widgets, D12 §4 MyDS Design System',
        'SystemHealthWidget' => 'D03 §8.2 Performance monitoring requirements, D04 §3.2 Dashboard widgets',
        'SystemMetricsWidget' => 'D03 §8.2 Performance monitoring requirements, D04 §3.2 Dashboard widgets',
        'PerformanceMetricsWidget' => 'D03 §8.2 Performance monitoring requirements, D04 §3.2 Dashboard widgets',
        'HorizonHealthWidget' => 'D11 §9 Laravel Horizon integration, D04 §3.2 Dashboard widgets',
        'SlowQueriesTableWidget' => 'D11 §12.1 Performance standards, D04 §3.2 Dashboard widgets',
        'HealthCheckTableWidget' => 'D11 §12.1 Performance standards, D04 §3.2 Dashboard widgets',
        'EmailQueueStatsWidget' => 'D04 §3.2 Dashboard widgets, D11 §9 Queue management',
        'EmailQueueTrendsWidget' => 'D04 §3.2 Dashboard widgets, D11 §9 Queue management',
        'DataRetentionAlertWidget' => 'D04 §3.2 Dashboard widgets, D09 Database Documentation - Dual Audit System',
        'SensitiveAccessLogWidget' => 'D04 §3.2 Dashboard widgets, D09 Database Documentation - Dual Audit System',
        'UserActivityWidget' => 'D04 §3.2 Dashboard widgets, D09 Database Documentation - Dual Audit System',
        'UserActivityStatsWidget' => 'D04 §3.2 Dashboard widgets, D09 Database Documentation - Dual Audit System',
        'LoanApprovalQueueWidget' => 'D04 §3.2 Dashboard widgets, D03 SRS-AST-002',
        'CrossModuleIntegrationWidget' => 'D04 §3.2 Dashboard widgets, D03 Cross-module integration',
        'EnhancedRealTimeDashboardWidget' => 'D04 §3.2 Dashboard widgets, D16 Broadcasting Setup - Laravel Reverb integration',
    ];

    public function handle(): int
    {
        $isDryRun = $this->option('dry-run');
        $force = $this->option('force');

        $this->info('🔄 Updating widget metadata...');

        $widgetPath = app_path('Filament/Widgets');
        $widgetFiles = File::glob($widgetPath.'/*.php');

        $results = [
            'processed' => 0,
            'updated' => 0,
            'skipped' => 0,
            'errors' => 0,
        ];

        foreach ($widgetFiles as $file) {
            $results['processed']++;
            $className = $this->getClassNameFromFile($file);

            if (! $className) {
                $this->warn('Could not determine class name for: '.basename($file));
                $results['errors']++;

                continue;
            }

            try {
                $updated = $this->updateWidgetFile($file, $className, $isDryRun, $force);

                if ($updated) {
                    $results['updated']++;
                    $this->line("  ✅ Updated: {$className}");
                } else {
                    $results['skipped']++;
                    $this->line("  ⏭️  Skipped: {$className}");
                }
            } catch (\Exception $e) {
                $this->error("Error updating {$className}: ".$e->getMessage());
                $results['errors']++;
            }
        }

        $this->displaySummary($results, $isDryRun);

        return $results['errors'] > 0 ? self::FAILURE : self::SUCCESS;
    }

    private function updateWidgetFile(string $filePath, string $className, bool $isDryRun, bool $force): bool
    {
        $content = File::get($filePath);

        // Check if already has WidgetMetadata trait
        if (str_contains($content, 'use WidgetMetadata') && ! $force) {
            return false;
        }

        $originalContent = $content;

        // Add WidgetMetadata import if not present
        if (! str_contains($content, 'use App\Filament\Traits\WidgetMetadata;')) {
            $content = $this->addWidgetMetadataImport($content);
        }

        // Add WidgetMetadata trait usage if not present
        if (! str_contains($content, 'use WidgetMetadata')) {
            $content = $this->addWidgetMetadataTrait($content);
        }

        // Add role configuration if widget has specific roles
        if (isset(self::WIDGET_ROLES[$className])) {
            $content = $this->addRoleConfiguration($content, $className);
        }

        // Add documentation reference
        if (isset(self::WIDGET_DOCS[$className])) {
            $content = $this->addDocumentationReference($content, $className);
        }

        // Only write if content changed and not dry run
        if ($content !== $originalContent && ! $isDryRun) {
            File::put($filePath, $content);

            return true;
        }

        return $content !== $originalContent;
    }

    private function addWidgetMetadataImport(string $content): string
    {
        // Find the namespace line and add import after other use statements
        $lines = explode("\n", $content);
        $insertIndex = -1;

        for ($i = 0; $i < count($lines); $i++) {
            if (str_starts_with(trim($lines[$i]), 'use ') && ! str_contains($lines[$i], 'WidgetMetadata')) {
                $insertIndex = $i + 1;
            }
        }

        if ($insertIndex > -1) {
            array_splice($lines, $insertIndex, 0, 'use App\Filament\Traits\WidgetMetadata;');
        }

        return implode("\n", $lines);
    }

    private function addWidgetMetadataTrait(string $content): string
    {
        // Find class declaration and add trait usage
        $pattern = '/class\s+\w+\s+extends\s+\w+\s*\{([^}]*use\s+[^;]+;)?/';

        return preg_replace_callback($pattern, function ($matches) {
            $classDeclaration = $matches[0];

            if (str_contains($classDeclaration, 'use ')) {
                // Add after existing use statements
                return str_replace(';', ";\n    use WidgetMetadata;", $classDeclaration);
            } else {
                // Add as first use statement
                return str_replace('{', "{\n    use WidgetMetadata;\n", $classDeclaration);
            }
        }, $content);
    }

    private function addRoleConfiguration(string $content, string $className): string
    {
        $roles = self::WIDGET_ROLES[$className];
        $rolesString = "'".implode("', '", $roles)."'";

        $roleMethod = "
    /**
     * Widget roles - restricted access
     */
    public static function getWidgetRoles(): array
    {
        return [{$rolesString}];
    }";

        // Add after sort property if it exists
        if (preg_match('/protected static \?int \$sort = [^;]+;/', $content)) {
            $content = preg_replace(
                '/(protected static \?int \$sort = [^;]+;)/',
                "$1{$roleMethod}",
                $content
            );
        } else {
            // Add after class opening brace
            $content = preg_replace(
                '/(class\s+\w+[^{]*\{[^}]*use[^;]+;)/',
                "$1{$roleMethod}",
                $content
            );
        }

        return $content;
    }

    private function addDocumentationReference(string $content, string $className): string
    {
        $docRef = self::WIDGET_DOCS[$className];

        $docMethod = "
    /**
     * Documentation reference
     */
    public static function getDocumentationReference(): string
    {
        return '{$docRef}';
    }";

        // Add after role method if it exists, otherwise after sort property
        if (str_contains($content, 'getWidgetRoles()')) {
            $content = preg_replace(
                '/(public static function getWidgetRoles\(\): array\s*\{[^}]+\})/',
                "$1{$docMethod}",
                $content
            );
        } elseif (preg_match('/protected static \?int \$sort = [^;]+;/', $content)) {
            $content = preg_replace(
                '/(protected static \?int \$sort = [^;]+;)/',
                "$1{$docMethod}",
                $content
            );
        } else {
            // Add after class opening brace
            $content = preg_replace(
                '/(class\s+\w+[^{]*\{[^}]*use[^;]+;)/',
                "$1{$docMethod}",
                $content
            );
        }

        return $content;
    }

    private function getClassNameFromFile(string $filePath): ?string
    {
        $fileName = basename($filePath, '.php');

        return $fileName;
    }

    private function displaySummary(array $results, bool $isDryRun): void
    {
        $this->newLine();
        $this->info('📊 Update Summary:');
        $this->table(
            ['Metric', 'Count'],
            [
                ['Files Processed', $results['processed']],
                ['Widgets '.($isDryRun ? 'Would Be Updated' : 'Updated'), $results['updated']],
                ['Skipped', $results['skipped']],
                ['Errors', $results['errors']],
            ]
        );

        if ($isDryRun) {
            $this->info('🔍 This was a dry run. Remove --dry-run to apply changes.');
        }
    }
}
