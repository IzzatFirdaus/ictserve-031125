<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\ComponentMetadataService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

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

    public function handle(
        ComponentInventoryService $inventory,
        ComponentMetadataService $metadata
    ): int {
        $this->info('Scanning components...');

        /** @var Collection<int, array<string, mixed>> $components */
        $components = $inventory->scanComponents();

        if ($components->isEmpty()) {
            $this->warn('No components found.');

            return self::SUCCESS;
        }

        if ($type = $this->option('type')) {
            $components = $components->where('type', $type);
        }

        $this->info("Found {$components->count()} components.");

        /** @var array<int, array<string, mixed>> $componentsArray */
        $componentsArray = array_values(array_filter(
            $components->toArray(),
            static fn ($component): bool => is_array($component)
        ));

        if ($this->option('dry-run')) {
            $this->info("\nDRY RUN MODE - No files will be modified\n");
            $this->previewMetadata($componentsArray, $metadata);

            return self::SUCCESS;
        }

        if (! $this->confirm('Add metadata to all components?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('Adding metadata...');

        $results = $metadata->batchAddMetadata($componentsArray);
        $this->displayResults($results);

        return self::SUCCESS;
    }

    /**
     * Preview metadata that would be added
     *
     * @param  array<int, array<string, mixed>>  $components
     */
    protected function previewMetadata(array $components, ComponentMetadataService $metadata): void
    {
        $sample = array_slice($components, 0, 3);

        foreach ($sample as $component) {
            /** @var array{name?:string,type?:string,relative_path?:string} $component */
            $name = $component['name'] ?? 'Component';
            $type = $component['type'] ?? 'unknown';
            $relativePath = $component['relative_path'] ?? '';

            $this->newLine();
            $this->line("<fg=cyan>Component:</> {$name} ({$type})");
            $this->line("<fg=cyan>Path:</> {$relativePath}");

            $meta = $metadata->generateMetadata($component);
            /** @var array{name:string,description:string,author:string,version:string,trace:array<int,string>,wcag:string,browsers:string} $meta */

            $this->line("\n<fg=green>Metadata to be added:</>");
            $this->table(
                ['Field', 'Value'],
                [
                    ['Name', $meta['name'] ?? $name],
                    ['Description', $meta['description'] ?? ''],
                    ['Author', $meta['author'] ?? ''],
                    ['Version', $meta['version'] ?? ''],
                    ['Trace References', implode(', ', $meta['trace'] ?? [])],
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
     *
     * @param  array{success:int,skipped:int,failed:int,errors:array<int,string>}  $results
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('Results:');

        $this->table(
            ['Status', 'Count'],
            [
                ['Success', $results['success']],
                ['Skipped (already has metadata)', $results['skipped']],
                ['Failed', $results['failed']],
            ]
        );

        if (! empty($results['errors'])) {
            $this->newLine();
            $this->error('Errors:');
            foreach ($results['errors'] as $error) {
                $this->line("  - {$error}");
            }
        }

        if ($results['success'] > 0) {
            $this->newLine();
            $this->info("Successfully added metadata to {$results['success']} components!");
        }
    }
}

