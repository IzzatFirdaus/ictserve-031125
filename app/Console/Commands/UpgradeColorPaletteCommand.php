<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ColorPaletteUpgradeService;
use Illuminate\Console\Command;

/**
 * @phpstan-type CategoryResult array{success: bool, category: string, processed: int, modified: int, total_replacements: int}
 * @phpstan-type AllResult array{success: bool, total_processed: int, total_modified: int, total_replacements: int, by_category: array<string, array{processed: int, modified: int, total_replacements: int}>}
 */
class UpgradeColorPaletteCommand extends Command
{
    protected $signature = 'component:upgrade-colors
                            {--category= : Specific category to process}
                            {--dry-run : Preview changes without applying}';

    protected $description = 'Upgrade components to WCAG 2.2 AA compliant color palette';

    public function handle(ColorPaletteUpgradeService $service): int
    {
        $this->info('Upgrading color palette to WCAG 2.2 AA compliant colors...');
        $this->newLine();

        $isDryRun = (bool) $this->option('dry-run');

        if ($isDryRun) {
            $this->warn('DRY RUN MODE - No changes will be made');
            $this->newLine();
        }

        $categoryOption = $this->option('category');
        $category = is_string($categoryOption) && $categoryOption !== '' ? $categoryOption : null;

        $result = $category !== null
            ? $this->processCategory($service, $category, $isDryRun)
            : $this->processAll($service, $isDryRun);

        $this->displayResults($result, $isDryRun);

        return Command::SUCCESS;
    }

    /**
     * @return array<string, mixed>
     *
     * @phpstan-return CategoryResult
     */
    private function processCategory(ColorPaletteUpgradeService $service, string $category, bool $isDryRun): array
    {
        $this->info("Processing category: {$category}");

        if ($isDryRun) {
            return [
                'success' => true,
                'category' => $category,
                'processed' => 0,
                'modified' => 0,
                'total_replacements' => 0,
            ];
        }

        /** @var CategoryResult $result */
        $result = $service->upgradeCategory($category);

        return $result;
    }

    /**
     * @return array<string, mixed>
     *
     * @phpstan-return AllResult
     */
    private function processAll(ColorPaletteUpgradeService $service, bool $isDryRun): array
    {
        $this->info('Processing all categories...');

        if ($isDryRun) {
            return [
                'success' => true,
                'total_processed' => 0,
                'total_modified' => 0,
                'total_replacements' => 0,
                'by_category' => [],
            ];
        }

        /** @var AllResult $result */
        $result = $service->upgradeAll();

        return $result;
    }

    /**
     * @param  array<string, mixed>  $result
     *
     * @phpstan-param CategoryResult|AllResult $result
     */
    

/**
 * @param array<string, mixed> $result
 */
private function displayResults(array $result, bool $isDryRun): void
    {
        $this->newLine();

        if (isset($result['by_category']) && is_array($result['by_category'])) {
            $this->info('Results by Category');
            $this->newLine();

            $rows = [];
            /** @var array<string, array{processed: int, modified: int, total_replacements: int}> $categories */
            $categories = $result['by_category'];

            foreach ($categories as $category => $data) {
                $rows[] = [
                    ucfirst((string) $category),
                    (int) ($data['processed'] ?? 0),
                    (int) ($data['modified'] ?? 0),
                    (int) ($data['total_replacements'] ?? 0),
                ];
            }

            $this->table(
                ['Category', 'Processed', 'Modified', 'Replacements'],
                $rows
            );

            $this->newLine();

            $totalProcessed = (int) ($result['total_processed'] ?? 0);
            $totalModified = (int) ($result['total_modified'] ?? 0);
            $totalReplacements = (int) ($result['total_replacements'] ?? 0);

            $this->info("Total Processed: {$totalProcessed}");
            $this->info("Total Modified: {$totalModified}");
            $this->info("Total Replacements: {$totalReplacements}");

            return;
        }

        $processed = (int) ($result['processed'] ?? 0);
        $modified = (int) ($result['modified'] ?? 0);
        $replacements = (int) ($result['total_replacements'] ?? 0);

        $this->info("Processed: {$processed}");
        $this->info("Modified: {$modified}");
        $this->info("Replacements: {$replacements}");

        $this->newLine();

        if (! $isDryRun) {
            $this->info('Color palette upgrade complete!');
            $this->info('Run: php artisan component:inventory to verify');
        }
    }
}
