<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentMetadataService;
use Illuminate\Console\Command;

/**
 * Add Component Metadata Command
 *
 * Artisan command for adding standardized metadata headers to Blade components.
 * Implements D10 §7 documentation standards.
 *
 * @command
 *
 * @name component:add-metadata
 *
 * @description Add standardized metadata headers to components
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 17.1, 17.2, 17.3, 17.4
 * Standards: D10 §7
 */
class AddComponentMetadataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'component:add-metadata
                            {--category= : Specific category to process}
                            {--dry-run : Preview changes without applying}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add standardized metadata headers to Blade components';

    /**
     * Execute the console command.
     */
    public function handle(ComponentMetadataService $service): int
    {
        $this->info('📝 Adding metadata to components...');
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
     * @return array{success: bool, category: string, processed: int, skipped: int, total: int, message?: string}
     */
    private function processCategory(ComponentMetadataService $service, string $category): array
    {
        $this->info("Processing category: {$category}");

        if ($this->option('dry-run')) {
            return [
                'success' => true,
                'category' => $category,
                'processed' => 0,
                'skipped' => 0,
                'total' => 0,
            ];
        }

        return $service->addMetadataToCategory($category);
    }

    /**
     * Process all categories
     *
     * @return array{
     *     success: bool,
     *     total_processed: int,
     *     total_skipped: int,
     *     total_components: int,
     *     by_category: array<string, array{success: bool, category: string, processed: int, skipped: int, total: int}>
     * }
     */
    private function processAll(ComponentMetadataService $service): array
    {
        $this->info('Processing all categories...');

        if ($this->option('dry-run')) {
            return [
                'success' => true,
                'total_processed' => 0,
                'total_skipped' => 0,
                'total_components' => 0,
                'by_category' => [],
            ];
        }

        return $service->addMetadataToAll();
    }

    /**
     * Display results
     *
     * @param  array{
     *     success: bool,
     *     category?: string,
     *     processed?: int,
     *     skipped?: int,
     *     total?: int,
     *     total_processed?: int,
     *     total_skipped?: int,
     *     total_components?: int,
     *     by_category?: array<string, array{success: bool, category: string, processed: int, skipped: int, total: int}>
     * }  $result
     */
    private function displayResults(array $result): void
    {
        $this->newLine();

        if (isset($result['by_category']) && is_array($result['by_category'])) {
            $this->info('???? Results by Category');
            $this->newLine();

            /** @var array<string, array{success: bool, category: string, processed: int, skipped: int, total: int}> $byCategory */
            $byCategory = $result['by_category'];

            $rows = [];
            foreach ($byCategory as $category => $data) {
                $rows[] = [
                    ucfirst((string) $category),
                    (int) ($data['processed'] ?? 0),
                    (int) ($data['skipped'] ?? 0),
                    (int) ($data['total'] ?? 0),
                ];
            }

            $this->table(
                ['Category', 'Processed', 'Skipped', 'Total'],
                $rows
            );

            $this->newLine();
            $this->info('??? Total Processed: '.((int) ($result['total_processed'] ?? 0)));
            $this->info('??????  Total Skipped: '.((int) ($result['total_skipped'] ?? 0)));
            $this->info('???? Total Components: '.((int) ($result['total_components'] ?? 0)));
        } else {
            $processed = (int) ($result['processed'] ?? 0);
            $skipped = (int) ($result['skipped'] ?? 0);
            $total = (int) ($result['total'] ?? 0);

            $this->info("??? Processed: {$processed}");
            $this->info("??????  Skipped: {$skipped}");
            $this->info("???? Total: {$total}");
        }

        if (! $this->option('dry-run')) {
            $this->info('??? Metadata addition complete!');
            $this->info('???? Run: php artisan component:inventory to verify');
        }
    }
}
