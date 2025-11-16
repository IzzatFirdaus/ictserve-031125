<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

/**
 * Component Metadata Service
 *
 * Service for adding and managing standardized metadata headers on Blade components.
 * Implements D10 §7 documentation standards with proper traceability.
 *
 * @component
 *
 * @name ComponentMetadataService
 *
 * @description Service for component metadata management
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 17.1, 17.2, 17.3, 17.4
 * Standards: D04 §6.1, D10 §7, D12 §9, D14 §8
 * WCAG Level: N/A (Backend Service)
 *
 * @phpstan-import-type ComponentInventoryItem from ComponentInventoryService
 *
 * @phpstan-type MetadataPayload array{
 *     category: string,
 *     name: string,
 *     description: string,
 *     requirements: string,
 *     wcag_level: string,
 *     standards: string,
 *     usage: string,
 *     trace: array<int, string>,
 *     browsers: string,
 *     author: string,
 *     version: string,
 *     wcag: string
 * }
 */
class ComponentMetadataService
{
    /**
     * Standard metadata template
     */
    private const METADATA_TEMPLATE = <<<'EOT'
{{--
/**
 * %s
 *
 * %s
 *
 * @component
 * @name %s
 * @description %s
 * @author Pasukan BPM MOTAC
 * @version 1.0.0
 * @since %s
 *
 * Requirements: %s
 * WCAG Level: %s
 * Standards: %s
 * Browsers: Chrome 90+, Firefox 88+, Safari 14+, Edge 90+
 *
 * Usage:
 * %s
 */
--}}
EOT;

    /**
     * Build metadata payload for a component.
     *
     * @param  array<string, mixed>  $component
     *
     * @phpstan-param ComponentInventoryItem|array<string, mixed>  $component
     *
     * @return array<string, mixed>
     *
     * @phpstan-return MetadataPayload
     */
    public function generateMetadata(array $component): array
    {
        $componentMetadata = $component['metadata'] ?? [];
        if (! is_array($componentMetadata)) {
            $componentMetadata = [];
        }

        /** @var array<string, mixed> $componentMetadata */
        $category = is_string($component['category'] ?? null) ? $component['category'] : 'component';
        $name = is_string($component['name'] ?? null) ? $component['name'] : 'Component';

        $metadataRequirements = $componentMetadata['requirements'] ?? [];
        if (! is_array($metadataRequirements)) {
            $metadataRequirements = is_string($metadataRequirements) ? [$metadataRequirements] : [];
        }
        $metadataRequirements = array_values(array_filter(
            $metadataRequirements,
            static fn ($value): bool => is_scalar($value)
        ));
        /** @var array<int, string> $metadataRequirements */
        $metadataRequirements = array_map(static fn ($value): string => (string) $value, $metadataRequirements);

        $metadataStandards = $componentMetadata['standards'] ?? [];
        if (! is_array($metadataStandards)) {
            $metadataStandards = is_string($metadataStandards) ? [$metadataStandards] : [];
        }
        $metadataStandards = array_values(array_filter(
            $metadataStandards,
            static fn ($value): bool => is_scalar($value)
        ));
        /** @var array<int, string> $metadataStandards */
        $metadataStandards = array_map(static fn ($value): string => (string) $value, $metadataStandards);

        $wcagLevel = is_string($componentMetadata['wcag_level'] ?? null) ? $componentMetadata['wcag_level'] : 'AA (SC 1.4.3, 2.1.1, 2.4.7)';
        $description = is_string($componentMetadata['description'] ?? null)
            ? $componentMetadata['description']
            : 'Reusable Blade component for consistent UI patterns';
        $rawUsage = $componentMetadata['usage'] ?? null;
        $usage = is_string($rawUsage) ? $rawUsage : sprintf('<x-%s.%s />', $category, Str::kebab($name));
        $trace = $metadataRequirements;
        $requirements = $this->stringifyList($metadataRequirements);
        $standards = $this->stringifyList($metadataStandards);

        return [
            'category' => $category,
            'name' => $name,
            'description' => $description,
            'requirements' => $requirements,
            'wcag_level' => $wcagLevel,
            'standards' => $standards,
            'usage' => $usage,
            'trace' => $trace,
            'browsers' => 'Chrome 90+, Firefox 88+, Safari 14+, Edge 90+',
            'author' => 'Pasukan BPM MOTAC',
            'version' => '1.0.0',
            'wcag' => $wcagLevel,
        ];
    }

    /**
     * Add metadata to a list of components.
     *
     * @param  array<int, ComponentInventoryItem|array<string, mixed>>  $components
     * @return array{success: int, skipped: int, failed: int, errors: array<int, string>}
     */
    public function batchAddMetadata(array $components): array
    {
        $results = [
            'success' => 0,
            'skipped' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        foreach ($components as $component) {
            if (! is_array($component) || ! isset($component['path'])) {
                $results['failed']++;
                $results['errors'][] = 'Component path missing';

                continue;
            }

            $path = is_string($component['path']) ? $component['path'] : null;
            if ($path === null) {
                $results['failed']++;
                $results['errors'][] = 'Component path is invalid';

                continue;
            }

            $metadata = $this->generateMetadata($component);
            $added = $this->addMetadata($path, $metadata);

            if ($added) {
                $results['success']++;
            } else {
                $results['skipped']++;
            }
        }

        return $results;
    }

    /**
     * Add metadata to a component file
     *
     * @param  array<string, mixed>  $metadata
     */
    public function addMetadata(string $path, array $metadata): bool
    {
        if (! File::exists($path)) {
            return false;
        }

        $content = File::get($path);

        if ($this->hasMetadata($content)) {
            return false;
        }

        $header = $this->generateMetadataHeader($metadata);
        $newContent = $header."\n\n".$content;

        File::put($path, $newContent);

        return true;
    }

    /**
     * Check if component has metadata
     */
    private function hasMetadata(string $content): bool
    {
        return str_contains($content, '@component') && str_contains($content, '@name');
    }

    /**
     * Generate metadata header
     *
     * @param  array<string, mixed>  $metadata
     */
    private function generateMetadataHeader(array $metadata): string
    {
        $category = is_string($metadata['category'] ?? null) ? $metadata['category'] : 'Component';
        $name = is_string($metadata['name'] ?? null) ? $metadata['name'] : 'Unknown';
        $description = is_string($metadata['description'] ?? null)
            ? $metadata['description']
            : 'Reusable Blade component for consistent UI patterns';

        $requirementsSource = $metadata['requirements'] ?? [];
        if (! is_array($requirementsSource)) {
            $requirementsSource = is_string($requirementsSource) ? [$requirementsSource] : [];
        }
        $requirementsSource = array_values(array_filter(
            $requirementsSource,
            static fn ($value): bool => is_scalar($value)
        ));
        /** @var array<int, string> $requirementsSource */
        $requirementsSource = array_map(static fn ($value): string => (string) $value, $requirementsSource);

        $standardsSource = $metadata['standards'] ?? [];
        if (! is_array($standardsSource)) {
            $standardsSource = is_string($standardsSource) ? [$standardsSource] : [];
        }
        $standardsSource = array_values(array_filter(
            $standardsSource,
            static fn ($value): bool => is_scalar($value)
        ));
        /** @var array<int, string> $standardsSource */
        $standardsSource = array_map(static fn ($value): string => (string) $value, $standardsSource);

        $requirements = $this->stringifyList($requirementsSource);
        $wcagLevel = is_string($metadata['wcag_level'] ?? null) ? $metadata['wcag_level'] : 'AA (SC 1.4.3, 2.1.1, 2.4.7)';
        $standards = $this->stringifyList($standardsSource);
        $usageValue = $metadata['usage'] ?? null;
        $usage = is_string($usageValue) ? $usageValue : sprintf('<x-%s.%s />', strtolower($category), Str::kebab($name));
        $date = date('Y-m-d');

        $title = ucfirst($category).' - '.Str::title(str_replace(['.blade', '-'], ['', ' '], $name)).' Blade Component';

        return sprintf(
            self::METADATA_TEMPLATE,
            $title,
            $description,
            Str::title(str_replace(['.blade', '-'], ['', ' '], $name)),
            $description,
            $date,
            $requirements,
            $wcagLevel,
            $standards,
            $usage
        );
    }

    /**
     * Add metadata to all components in a category
     *
     * @param  array<string, mixed>  $defaultMetadata
     * @return array{success: bool, category: string, processed: int, skipped: int, total: int, message?: string}
     */
    public function addMetadataToCategory(string $category, array $defaultMetadata = []): array
    {
        $categoryPath = resource_path("views/components/{$category}");

        if (! File::isDirectory($categoryPath)) {
            return [
                'success' => false,
                'category' => $category,
                'message' => "Category directory not found: {$category}",
                'processed' => 0,
                'skipped' => 0,
                'total' => 0,
            ];
        }

        $files = File::files($categoryPath);
        $processed = 0;
        $skipped = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $componentName = $file->getFilenameWithoutExtension();

                $metadata = array_merge([
                    'category' => $category,
                    'name' => $componentName,
                ], $defaultMetadata);

                if ($this->addMetadata($file->getPathname(), $metadata)) {
                    $processed++;
                } else {
                    $skipped++;
                }
            }
        }

        return [
            'success' => true,
            'category' => $category,
            'processed' => $processed,
            'skipped' => $skipped,
            'total' => $processed + $skipped,
        ];
    }

    /**
     * Add metadata to all components
     *
     * @return array{
     *     success: bool,
     *     total_processed: int,
     *     total_skipped: int,
     *     total_components: int,
     *     by_category: array<string, array{success: bool, category: string, processed: int, skipped: int, total: int}>
     * }
     */
    public function addMetadataToAll(): array
    {
        $categories = [
            'accessibility' => [
                'description' => 'Accessibility-focused components for WCAG 2.2 AA compliance',
                'requirements' => '6.1, 6.2, 6.3, 6.5, 14.4',
                'wcag_level' => 'AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5)',
            ],
            'data' => [
                'description' => 'Data display components for tables, lists, and structured content',
                'requirements' => '9.1, 11.1, 21.2',
                'wcag_level' => 'AA (SC 1.3.1, 1.4.3, 2.1.1)',
            ],
            'form' => [
                'description' => 'Form input components with validation and accessibility',
                'requirements' => '6.3, 6.5, 11.4, 21.3',
                'wcag_level' => 'AA (SC 1.4.3, 2.1.1, 2.4.7, 2.5.5, 3.3.1, 3.3.2)',
            ],
            'layout' => [
                'description' => 'Layout components for page structure and organization',
                'requirements' => '1.1, 6.1, 18.1, 25.3',
                'wcag_level' => 'AA (SC 1.3.1, 2.4.1)',
            ],
            'navigation' => [
                'description' => 'Navigation components for site and page navigation',
                'requirements' => '18.3, 25.2, 25.3',
                'wcag_level' => 'AA (SC 2.1.1, 2.4.1, 2.4.3)',
            ],
            'responsive' => [
                'description' => 'Responsive layout components for mobile-first design',
                'requirements' => '6.5, 14.5, 15.4',
                'wcag_level' => 'AA (SC 1.4.10)',
            ],
            'ui' => [
                'description' => 'User interface components for consistent design patterns',
                'requirements' => '6.1, 6.2, 14.1, 19.5',
                'wcag_level' => 'AA (SC 1.4.3, 2.1.1, 2.4.7)',
            ],
        ];

        $results = [];
        $totalProcessed = 0;
        $totalSkipped = 0;

        foreach ($categories as $category => $metadata) {
            $result = $this->addMetadataToCategory($category, $metadata);
            $results[$category] = $result;
            $totalProcessed += $result['processed'];
            $totalSkipped += $result['skipped'];
        }

        // Process uncategorized components
        $rootPath = resource_path('views/components');
        $files = File::files($rootPath);
        $uncategorizedProcessed = 0;
        $uncategorizedSkipped = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $componentName = $file->getFilenameWithoutExtension();

                $metadata = [
                    'category' => 'uncategorized',
                    'name' => $componentName,
                    'description' => 'Legacy component - consider categorization',
                    'requirements' => '6.1, 14.1',
                    'wcag_level' => 'AA (SC 1.4.3, 2.1.1)',
                ];

                if ($this->addMetadata($file->getPathname(), $metadata)) {
                    $uncategorizedProcessed++;
                } else {
                    $uncategorizedSkipped++;
                }
            }
        }

        $results['uncategorized'] = [
            'success' => true,
            'category' => 'uncategorized',
            'processed' => $uncategorizedProcessed,
            'skipped' => $uncategorizedSkipped,
            'total' => $uncategorizedProcessed + $uncategorizedSkipped,
        ];

        $totalProcessed += $uncategorizedProcessed;
        $totalSkipped += $uncategorizedSkipped;

        return [
            'success' => true,
            'total_processed' => $totalProcessed,
            'total_skipped' => $totalSkipped,
            'total_components' => $totalProcessed + $totalSkipped,
            'by_category' => $results,
        ];
    }

    /**
     * @param  array<int, string>|string  $items
     */
    private function stringifyList(array|string $items): string
    {
        $list = is_array($items) ? array_filter(array_map('trim', $items)) : [trim((string) $items)];

        return implode(', ', $list);
    }

    /**
     * Update metadata in existing component
     *
     * @param  array<string, mixed>  $updates
     */
    public function updateMetadata(string $path, array $updates): bool
    {
        if (! File::exists($path)) {
            return false;
        }

        $content = File::get($path);

        // Update version
        if (isset($updates['version']) && is_scalar($updates['version'])) {
            $content = preg_replace(
                '/@version\s+[\d.]+/',
                '@version '.(string) $updates['version'],
                $content
            ) ?? $content;
        }

        // Update requirements
        if (isset($updates['requirements']) && is_scalar($updates['requirements'])) {
            $content = preg_replace(
                '/Requirements?:\s*[^\n]+/',
                'Requirements: '.(string) $updates['requirements'],
                $content
            ) ?? $content;
        }

        // Update WCAG level
        if (isset($updates['wcag_level']) && is_scalar($updates['wcag_level'])) {
            $content = preg_replace(
                '/WCAG Level:\s*[^\n]+/',
                'WCAG Level: '.(string) $updates['wcag_level'],
                $content
            ) ?? $content;
        }

        // Update description
        if (isset($updates['description']) && is_scalar($updates['description'])) {
            $content = preg_replace(
                '/@description\s+[^\n]+/',
                '@description '.(string) $updates['description'],
                $content
            ) ?? $content;
        }

        File::put($path, $content);

        return true;
    }
}
