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
     * @return array<string, mixed>
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
     * @return array<string, mixed>
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
                    $data['skipped'],
                    $data['total'],
                ];
            }

            $this->table(
                ['Category', 'Processed', 'Skipped', 'Total'],
                $rows
            );

            $this->newLine();
            $this->info("✅ Total Processed: {$result['total_processed']}");
            $this->info("⏭️  Total Skipped: {$result['total_skipped']}");
            $this->info("📦 Total Components: {$result['total_components']}");
        } else {
            // Single category processed
            $this->info("✅ Processed: {$result['processed']}");
            $this->info("⏭️  Skipped: {$result['skipped']}");
            $this->info("📦 Total: {$result['total']}");
        }

        $this->newLine();

        if (! $this->option('dry-run')) {
            $this->info('✅ Metadata addition complete!');
            $this->info('💡 Run: php artisan component:inventory to verify');
        }
    }
}
