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

        // Check if metadata already exists
        if ($this->hasMetadata($content)) {
            return false;
        }

        // Generate metadata header
        $header = $this->generateMetadataHeader($metadata);

        // Prepend metadata to file
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
        $category = $metadata['category'] ?? 'Component';
        $name = $metadata['name'] ?? 'Unknown';
        $description = $metadata['description'] ?? 'Reusable Blade component for consistent UI patterns';
        $requirements = $metadata['requirements'] ?? '6.1, 6.2, 14.1';
        $wcagLevel = $metadata['wcag_level'] ?? 'AA (SC 1.4.3, 2.1.1, 2.4.7)';
        $standards = $metadata['standards'] ?? 'D04 §6.1, D10 §7, D12 §9, D14 §8';
        $usage = $metadata['usage'] ?? sprintf('<x-%s.%s />', strtolower($category), Str::kebab($name));
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
     * @return array<string, mixed>
     */
    public function addMetadataToCategory(string $category, array $defaultMetadata = []): array
    {
        $categoryPath = resource_path("views/components/{$category}");

        if (! File::isDirectory($categoryPath)) {
            return [
                'success' => false,
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
     * @return array<string, mixed>
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
        if (isset($updates['version'])) {
            $content = preg_replace(
                '/@version\s+[\d.]+/',
                '@version '.$updates['version'],
                $content
            );
        }

        // Update requirements
        if (isset($updates['requirements'])) {
            $content = preg_replace(
                '/Requirements?:\s*[^\n]+/',
                'Requirements: '.$updates['requirements'],
                $content
            );
        }

        // Update WCAG level
        if (isset($updates['wcag_level'])) {
            $content = preg_replace(
                '/WCAG Level:\s*[^\n]+/',
                'WCAG Level: '.$updates['wcag_level'],
                $content
            );
        }

        // Update description
        if (isset($updates['description'])) {
            $content = preg_replace(
                '/@description\s+[^\n]+/',
                '@description '.$updates['description'],
                $content
            );
        }

        File::put($path, $content);

        return true;
    }
}
