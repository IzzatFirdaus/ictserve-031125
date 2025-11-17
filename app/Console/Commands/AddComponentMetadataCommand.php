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
            /** @var array{success:bool,category:string,processed:int,skipped:int,total:int} $result */
            $result = $this->processCategory($service, $category);
        } else {
            /** @var array{
             *     success: bool,
             *     total_processed: int,
             *     total_skipped: int,
             *     total_components: int,
             *     by_category: array<string, array{processed:int,skipped:int,total:int}>
             * } $result
             */
            $result = $this->processAll($service);
        }

        $this->displayResults($result);

        return Command::SUCCESS;
    }

    /**
     * Process a specific category
     *
     * @return array{success:bool,category:string,processed:int,skipped:int,total:int}
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

        $result = $service->addMetadataToCategory($category);

        $processed = $result['processed'] ?? 0;
        if (! is_int($processed)) {
            $processed = 0;
        }

        $skipped = $result['skipped'] ?? 0;
        if (! is_int($skipped)) {
            $skipped = 0;
        }

        $total = $result['total'] ?? 0;
        if (! is_int($total)) {
            $total = 0;
        }

        return [
            'success' => (bool) ($result['success'] ?? false),
            'category' => $category,
            'processed' => $processed,
            'skipped' => $skipped,
            'total' => $total,
        ];
    }

    /**
     * Process all categories
     *
     * @return array{
     *     success: bool,
     *     total_processed: int,
     *     total_skipped: int,
     *     total_components: int,
     *     by_category: array<string, array{processed:int,skipped:int,total:int}>
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

        $result = $service->addMetadataToAll();

        $processed = $result['total_processed'] ?? 0;
        if (! is_int($processed)) {
            $processed = 0;
        }

        $skipped = $result['total_skipped'] ?? 0;
        if (! is_int($skipped)) {
            $skipped = 0;
        }

        $total = $result['total_components'] ?? 0;
        if (! is_int($total)) {
            $total = 0;
        }

        $byCategory = [];
        if (is_array($result['by_category'] ?? null)) {
            foreach ($result['by_category'] as $category => $data) {
                if (! is_array($data)) {
                    continue;
                }

                $categoryProcessed = is_int($data['processed'] ?? null) ? $data['processed'] : 0;
                $categorySkipped = is_int($data['skipped'] ?? null) ? $data['skipped'] : 0;
                $categoryTotal = is_int($data['total'] ?? null) ? $data['total'] : 0;

                $byCategory[(string) $category] = [
                    'processed' => $categoryProcessed,
                    'skipped' => $categorySkipped,
                    'total' => $categoryTotal,
                ];
            }
        }

        return [
            'success' => (bool) ($result['success'] ?? false),
            'total_processed' => $processed,
            'total_skipped' => $skipped,
            'total_components' => $total,
            'by_category' => $byCategory,
        ];
    }

    /**
     * Display results
     *
     * @param  array{
     *     by_category?: array<string, array{processed:int,skipped:int,total:int}>,
     *     total_processed?: int,
     *     total_skipped?: int,
     *     total_components?: int,
     *     processed?: int,
     *     skipped?: int,
     *     total?: int,
     *     category?: string,
     *     success?: bool
     * }  $result
     */
    private function displayResults(array $result): void
    {
        $this->newLine();

        if (isset($result['by_category']) && is_array($result['by_category'])) {
            $this->info('Results by Category');
            $this->newLine();

            $rows = [];
            foreach ($result['by_category'] as $category => $data) {
                $processed = (int) ($data['processed'] ?? 0);
                $skipped = (int) ($data['skipped'] ?? 0);
                $total = (int) ($data['total'] ?? 0);

                $rows[] = [
                    ucfirst((string) $category),
                    $processed,
                    $skipped,
                    $total,
                ];
            }

            $this->table(
                ['Category', 'Processed', 'Skipped', 'Total'],
                $rows
            );

            $this->newLine();
            $this->info('Total Processed: '.(int) ($result['total_processed'] ?? 0));
            $this->info('Total Skipped: '.(int) ($result['total_skipped'] ?? 0));
            $this->info('Total Components: '.(int) ($result['total_components'] ?? 0));
        } else {
            $this->info('Processed: '.(int) ($result['processed'] ?? 0));
            $this->info('Skipped: '.(int) ($result['skipped'] ?? 0));
            $this->info('Total: '.(int) ($result['total'] ?? 0));
        }

        $this->newLine();

        if (! $this->option('dry-run')) {
            $this->info('Metadata addition complete!');
            $this->info('Run: php artisan component:inventory to verify');
        }
    }
}
