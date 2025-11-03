<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Color Palette Upgrade Service
 *
 * Service for upgrading components from deprecated colors to WCAG 2.2 AA compliant palette.
 * Removes old warning yellow (#F1C40F) and danger red (#E74C3C) in favor of compliant colors.
 *
 * @component
 *
 * @name ColorPaletteUpgradeService
 *
 * @description Service for color palette compliance upgrades
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 6.1, 6.3, 14.2, 15.2
 * Standards: D04 §6.1, D14 §8
 * WCAG Level: AA (SC 1.4.3, 1.4.11)
 */
class ColorPaletteUpgradeService
{
    /**
     * Color mapping from deprecated to compliant colors
     */
    private const COLOR_MAPPINGS = [
        // Deprecated hex colors
        '#F1C40F' => '#ff8c00', // Old warning yellow → Compliant warning orange
        '#E74C3C' => '#b50c0c', // Old danger red → Compliant danger red

        // Deprecated Tailwind classes - Red (danger)
        'text-red-500' => 'text-danger',
        'text-red-600' => 'text-danger',
        'text-red-700' => 'text-danger-dark',
        'bg-red-500' => 'bg-danger',
        'bg-red-600' => 'bg-danger',
        'bg-red-700' => 'bg-danger-dark',
        'border-red-500' => 'border-danger',
        'border-red-600' => 'border-danger',
        'border-red-700' => 'border-danger-dark',
        'ring-red-500' => 'ring-danger',
        'ring-red-600' => 'ring-danger',
        'focus:ring-red-500' => 'focus:ring-danger',
        'focus:ring-red-600' => 'focus:ring-danger',
        'focus:border-red-500' => 'focus:border-danger',
        'focus:border-red-600' => 'focus:border-danger',
        'hover:bg-red-600' => 'hover:bg-danger',
        'hover:bg-red-700' => 'hover:bg-danger-dark',

        // Deprecated Tailwind classes - Yellow (warning)
        'text-yellow-400' => 'text-warning',
        'text-yellow-500' => 'text-warning',
        'text-yellow-600' => 'text-warning-dark',
        'bg-yellow-400' => 'bg-warning',
        'bg-yellow-500' => 'bg-warning',
        'bg-yellow-600' => 'bg-warning-dark',
        'border-yellow-400' => 'border-warning',
        'border-yellow-500' => 'border-warning',
        'border-yellow-600' => 'border-warning-dark',
        'ring-yellow-400' => 'ring-warning',
        'ring-yellow-500' => 'ring-warning',
        'focus:ring-yellow-400' => 'focus:ring-warning',
        'focus:ring-yellow-500' => 'focus:ring-warning',
        'focus:border-yellow-400' => 'focus:border-warning',
        'focus:border-yellow-500' => 'focus:border-warning',
        'hover:bg-yellow-500' => 'hover:bg-warning',
        'hover:bg-yellow-600' => 'hover:bg-warning-dark',
    ];

    /**
     * Upgrade colors in a component file
     *
     * @return array<string, mixed>
     */
    public function upgradeComponent(string $path): array
    {
        if (! File::exists($path)) {
            return [
                'success' => false,
                'message' => 'File not found',
                'replacements' => 0,
            ];
        }

        $content = File::get($path);
        $originalContent = $content;
        $replacements = 0;

        foreach (self::COLOR_MAPPINGS as $old => $new) {
            $count = 0;
            $content = str_replace($old, $new, $content, $count);
            $replacements += $count;
        }

        if ($replacements > 0) {
            File::put($path, $content);
        }

        return [
            'success' => true,
            'path' => $path,
            'replacements' => $replacements,
            'modified' => $replacements > 0,
        ];
    }

    /**
     * Upgrade all components in a category
     *
     * @return array<string, mixed>
     */
    public function upgradeCategory(string $category): array
    {
        $categoryPath = resource_path("views/components/{$category}");

        if (! File::isDirectory($categoryPath)) {
            return [
                'success' => false,
                'message' => "Category directory not found: {$category}",
                'processed' => 0,
                'modified' => 0,
                'total_replacements' => 0,
            ];
        }

        $files = File::files($categoryPath);
        $processed = 0;
        $modified = 0;
        $totalReplacements = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $result = $this->upgradeComponent($file->getPathname());
                $processed++;

                if ($result['modified']) {
                    $modified++;
                    $totalReplacements += $result['replacements'];
                }
            }
        }

        return [
            'success' => true,
            'category' => $category,
            'processed' => $processed,
            'modified' => $modified,
            'total_replacements' => $totalReplacements,
        ];
    }

    /**
     * Upgrade all components
     *
     * @return array<string, mixed>
     */
    public function upgradeAll(): array
    {
        $categories = [
            'accessibility',
            'data',
            'form',
            'layout',
            'navigation',
            'responsive',
            'ui',
        ];

        $results = [];
        $totalProcessed = 0;
        $totalModified = 0;
        $totalReplacements = 0;

        foreach ($categories as $category) {
            $result = $this->upgradeCategory($category);
            $results[$category] = $result;
            $totalProcessed += $result['processed'];
            $totalModified += $result['modified'];
            $totalReplacements += $result['total_replacements'];
        }

        // Process uncategorized components
        $rootPath = resource_path('views/components');
        $files = File::files($rootPath);
        $uncategorizedProcessed = 0;
        $uncategorizedModified = 0;
        $uncategorizedReplacements = 0;

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $result = $this->upgradeComponent($file->getPathname());
                $uncategorizedProcessed++;

                if ($result['modified']) {
                    $uncategorizedModified++;
                    $uncategorizedReplacements += $result['replacements'];
                }
            }
        }

        $results['uncategorized'] = [
            'success' => true,
            'category' => 'uncategorized',
            'processed' => $uncategorizedProcessed,
            'modified' => $uncategorizedModified,
            'total_replacements' => $uncategorizedReplacements,
        ];

        $totalProcessed += $uncategorizedProcessed;
        $totalModified += $uncategorizedModified;
        $totalReplacements += $uncategorizedReplacements;

        return [
            'success' => true,
            'total_processed' => $totalProcessed,
            'total_modified' => $totalModified,
            'total_replacements' => $totalReplacements,
            'by_category' => $results,
        ];
    }

    /**
     * Verify color compliance
     *
     * @return array<string, mixed>
     */
    public function verifyCompliance(string $path): array
    {
        if (! File::exists($path)) {
            return [
                'compliant' => false,
                'message' => 'File not found',
                'issues' => [],
            ];
        }

        $content = File::get($path);
        $issues = [];

        foreach (array_keys(self::COLOR_MAPPINGS) as $deprecated) {
            if (str_contains($content, $deprecated)) {
                $issues[] = $deprecated;
            }
        }

        return [
            'compliant' => empty($issues),
            'path' => $path,
            'issues' => $issues,
            'issue_count' => count($issues),
        ];
    }
}
