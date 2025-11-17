<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Services\ComponentInventoryService;
use App\Services\ComponentMetadataService;
use Illuminate\Console\Command;
use Illuminate\Support\Collection;

<<<<<<< HEAD
=======
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
 *
 * @phpstan-import-type ComponentInventoryItem from \App\Services\ComponentInventoryService
 */
>>>>>>> origin/main
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
<<<<<<< HEAD
            $this->info("\nDRY RUN MODE - No files will be modified\n");
            $this->previewMetadata($componentsArray, $metadata);
=======
            $this->info("\n🔍 DRY RUN MODE - No files will be modified\n");
            $this->previewMetadata($components, $metadata);
>>>>>>> origin/main

            return self::SUCCESS;
        }

        if (! $this->confirm('Add metadata to all components?', true)) {
            $this->info('Operation cancelled.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('Adding metadata...');

<<<<<<< HEAD
        $results = $metadata->batchAddMetadata($componentsArray);
=======
        // Add metadata to components
        $results = $metadata->batchAddMetadata($components->all());

        // Display results
>>>>>>> origin/main
        $this->displayResults($results);

        return self::SUCCESS;
    }

    /**
     * Preview metadata that would be added
     *
<<<<<<< HEAD
     * @param  array<int, array<string, mixed>>  $components
=======
     * @param  Collection<int, ComponentInventoryItem>  $components
>>>>>>> origin/main
     */
    protected function previewMetadata(Collection $components, ComponentMetadataService $metadata): void
    {
<<<<<<< HEAD
        $sample = array_slice($components, 0, 3);

        foreach ($sample as $component) {
            /** @var array{name?:string,type?:string,relative_path?:string} $component */
            $name = $component['name'] ?? 'Component';
            $type = $component['type'] ?? 'unknown';
            $relativePath = $component['relative_path'] ?? '';

=======
        $components->take(3)->each(function (array $component) use ($metadata): void {
>>>>>>> origin/main
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
        });

        $this->newLine();
        $this->info('Showing 3 of '.$components->count().' components. Run without --dry-run to apply changes.');
    }

    /**
     * Display batch operation results
     *
<<<<<<< HEAD
     * @param  array{success:int,skipped:int,failed:int,errors:array<int,string>}  $results
=======
     * @param  array{success: int, skipped: int, failed: int, errors: array<int, string>}  $results
>>>>>>> origin/main
     */
    protected function displayResults(array $results): void
    {
        $this->newLine();
        $this->info('Results:');
<<<<<<< HEAD
=======

        $success = (int) ($results['success'] ?? 0);
        $skipped = (int) ($results['skipped'] ?? 0);
        $failed = (int) ($results['failed'] ?? 0);
        $errors = $results['errors'] ?? [];
>>>>>>> origin/main

        $this->table(
            ['Status', 'Count'],
            [
<<<<<<< HEAD
                ['Success', $results['success']],
                ['Skipped (already has metadata)', $results['skipped']],
                ['Failed', $results['failed']],
=======
                ['Success', $success],
                ['Skipped (already has metadata)', $skipped],
                ['Failed', $failed],
>>>>>>> origin/main
            ]
        );

        if (! empty($errors)) {
            $this->newLine();
            $this->error('Errors:');
<<<<<<< HEAD
            foreach ($results['errors'] as $error) {
=======
            foreach ($errors as $error) {
>>>>>>> origin/main
                $this->line("  - {$error}");
            }
        }

        if ($success > 0) {
            $this->newLine();
<<<<<<< HEAD
            $this->info("Successfully added metadata to {$results['success']} components!");
=======
            $this->info("Successfully added metadata to {$success} components!");
>>>>>>> origin/main
        }
    }
}

