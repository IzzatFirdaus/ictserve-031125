<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ColorPaletteUpgradeService;
use Illuminate\Console\Command;

/**
 * Upgrade Color Palette Command
 *
 * Artisan command for upgrading components to WCAG 2.2 AA compliant color palette.
 * Removes deprecated colors and replaces with compliant alternatives.
 *
 * @command
 *
 * @name component:upgrade-colors
 *
 * @description Upgrade components to WCAG 2.2 AA compliant colors
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 6.1, 6.3, 14.2, 15.2
 * Standards: D14 §8
 * WCAG Level: AA (SC 1.4.3, 1.4.11)
 */
class UpgradeColorPaletteCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'component:upgrade-colors
                            {--category= : Specific category to process}
                            {--dry-run : Preview changes without applying}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Upgrade components to WCAG 2.2 AA compliant color palette';

    /**
     * Execute the console command.
     */
    public function handle(ColorPaletteUpgradeService $service): int
    {
        $this->info('🎨 Upgrading color palette to WCAG 2.2 AA compliant colors...');
        $this->newLine();

        if ($this->option('dry-run')) {
            $this->warn('🔍 DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $category = $this->option('category');

        if ($category) {
            $result = $this->processCategory($service, $category);
        } else {
            $result = $this->processAll($service);
        }

        $this->displayResults($result);

        return Command::SUCCESS;
    }

    /**
     * Process a specific category
     *
     * @return array<string, mixed>
     */
    private function processCategory(ColorPaletteUpgradeService $service, string $category): array
    {
        $this->info("Processing category: {$category}");

        if ($this->option('dry-run')) {
            return [
                'success' => true,
                'category' => $category,
                'processed' => 0,
                'modified' => 0,
                'total_replacements' => 0,
            ];
        }

        return $service->upgradeCategory($category);
    }

    /**
     * Process all categories
     *
     * @return array<string, mixed>
     */
    private function processAll(ColorPaletteUpgradeService $service): array
    {
        $this->info('Processing all categories...');

        if ($this->option('dry-run')) {
            return [
                'success' => true,
                'total_processed' => 0,
                'total_modified' => 0,
                'total_replacements' => 0,
                'by_category' => [],
            ];
        }

        return $service->upgradeAll();
    }

    /**
     * Display results
     *
     * @param  array<string, mixed>  $result
     */
    private function displayResults(array $result): void
    {
        $this->newLine();

        if (isset($result['by_category'])) {
            // Multiple categories processed
            $this->info('📊 Results by Category');
            $this->newLine();

            $rows = [];
            foreach ($result['by_category'] as $category => $data) {
                $rows[] = [
                    ucfirst($category),
                    $data['processed'],
                    $data['modified'],
                    $data['total_replacements'],
                ];
            }

            $this->table(
                ['Category', 'Processed', 'Modified', 'Replacements'],
                $rows
            );

            $this->newLine();
            $this->info("✅ Total Processed: {$result['total_processed']}");
            $this->info("🔄 Total Modified: {$result['total_modified']}");
            $this->info("🎨 Total Replacements: {$result['total_replacements']}");
        } else {
            // Single category processed
            $this->info("✅ Processed: {$result['processed']}");
            $this->info("🔄 Modified: {$result['modified']}");
            $this->info("🎨 Replacements: {$result['total_replacements']}");
        }

        $this->newLine();

        if (! $this->option('dry-run')) {
            $this->info('✅ Color palette upgrade complete!');
            $this->info('💡 Run: php artisan component:inventory to verify');
        }
    }
}
