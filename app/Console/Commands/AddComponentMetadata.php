<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\ComponentMetadataService;
use Illuminate\Console\Command;

/**
 * Add Component Metadata Command
 *
 * Adds standardized metadata headers to frontend components per D10 §7.
 *
 * @trace D03-FR-017.1, D03-FR-017.2, D03-FR-017.3, D03-FR-017.4, D03-FR-017.5
 * @trace D04 §8.2 (Component Metadata)
 * @trace D10 §7 (Component Documentation Standards)
 *
 * @version 1.0.0
 *
 * @author Pasukan BPM MOTAC
 *
 * @created 2025-11-03
 */
class AddComponentMetadata extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'add:metadata
                            {--type= : Filter by component type}
                            {--force : Overwrite existing metadata}
                            {--dry-run : Preview changes without writing}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add standardized metadata headers to frontend components';

    /**
     * Execute the console command.
     */
    public function handle(
        ComponentInventoryService $inventory,
        ComponentMetadataService $metadata
    ): int {
        $this->info('🔍 Scanning components...');

        // Scan all components
        $components = $inventory->scanComponents();

        if ($components->isEmpty()) {
            $this->warn('No components found.');

            return self::SUCCESS;
        }

        // Filter by type if specified
        if ($type = $this->option('type')) {
            $components = $components->where('type', $type);
        }

        $this->info("Found {$components->count()} components.");

        // Dry run mode
        if ($this->option('dry-run')) {
            $this->info("\n🔍 DRY RUN MODE - No files will be modified\n");
            $this->previewMetadata($components->toArray(), $metadata);

            return self::SUCCESS;
        }

        // Confirm before proceeding
        if (! $this->confirm('Add metadata to all components?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('📝 Adding metadata...');

        // Add metadata to components
        $results = $metadata->batchAddMetadata($components->toArray());

        // Display results
        $this->displayResults($results);

        return self::SUCCESS;
    }

    /**
     * Preview metadata that would be added
     */
    protected function previewMetadata(array $components, ComponentMetadataService $metadata): void
    {
        $sample = array_slice($components, 0, 3);

        foreach ($sample as $component) {
            $this->newLine();
            $this->line("<fg=cyan>Component:</> {$component['name']} ({$component['type']})");
            $this->line("<fg=cyan>Path:</> {$component['relative_path']}");

            $meta = $metadata->generateMetadata($component);

            $this->line("\n<fg=green>Metadata to be added:</>");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $meta['name']],
                    ['Description', $meta['description']],
                    ['Author', $meta['author']],
                    ['Version', $meta['version']],
                    ['Trace References', implode(', ', $meta['trace'])],
                    ['WCAG', $meta['wcag'] ?? 'N/A'],
                    ['Browsers', $meta['browsers'] ?? 'N/A'],
                ]
            );
        }

        $this->newLine();
        $this->info('Showing 3 of '.count($components).' components. Run without --dry-run to apply changes.');
    }

    /**
     * Display batch operation results
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('📊 Results:');

        $this->table(
            ['Status', 'Count'],
            [
                ['✅ Success', $results['success']],
                ['⏭️  Skipped (already has metadata)', $results['skipped']],
                ['❌ Failed', $results['failed']],
            ]
        );

        if (! empty($results['errors'])) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($results['errors'] as $error) {
                $this->line("  • {$error}");
            }
        }

        if ($results['success'] > 0) {
            $this->newLine();
            $this->info("✅ Successfully added metadata to {$results['success']} components!");
        }
    }
}
