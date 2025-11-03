<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\File;

/**
 * Component Inventory Service
 *
 * Comprehensive service for auditing and managing the unified component library.
 * Provides inventory management, usage pattern analysis, and compliance checking.
 *
 * @component
 *
 * @name ComponentInventoryService
 *
 * @description Service for component library audit and management
 *
 * @author Pasukan BPM MOTAC
 *
 * @version 1.0.0
 *
 * @since 2025-11-03
 *
 * Requirements: 11.1, 17.1, 17.2
 * Standards: D04 §6.1, D10 §7, D12 §9
 * WCAG Level: N/A (Backend Service)
 */
class ComponentInventoryService
{
    /**
     * Component categories as defined in the unified architecture
     */
    private const CATEGORIES = [
        'accessibility',
        'data',
        'form',
        'layout',
        'navigation',
        'responsive',
        'ui',
    ];

    /**
     * Get complete component inventory
     *
     * @return array<string, mixed>
     */
    public function getInventory(): array
    {
        $inventory = [
            'total_components' => 0,
            'by_category' => [],
            'components' => [],
            'obsolete' => [],
            'duplicates' => [],
            'usage_patterns' => [],
        ];

        // Scan each category
        foreach (self::CATEGORIES as $category) {
            $categoryPath = resource_path("views/components/{$category}");
            $components = $this->scanCategory($category, $categoryPath);

            $inventory['by_category'][$category] = [
                'count' => count($components),
                'components' => $components,
            ];

            $inventory['total_components'] += count($components);
            $inventory['components'] = array_merge($inventory['components'], $components);
        }

        // Scan root components directory for uncategorized components
        $rootComponents = $this->scanRootComponents();
        if (! empty($rootComponents)) {
            $inventory['by_category']['uncategorized'] = [
                'count' => count($rootComponents),
                'components' => $rootComponents,
            ];
            $inventory['total_components'] += count($rootComponents);
            $inventory['components'] = array_merge($inventory['components'], $rootComponents);
        }

        // Identify obsolete components
        $inventory['obsolete'] = $this->identifyObsoleteComponents($inventory['components']);

        // Identify duplicate components
        $inventory['duplicates'] = $this->identifyDuplicates($inventory['components']);

        return $inventory;
    }

    /**
     * Scan components in a specific category
     *
     * @return array<int, array<string, mixed>>
     */
    private function scanCategory(string $category, string $path): array
    {
        if (! File::isDirectory($path)) {
            return [];
        }

        $components = [];
        $files = File::files($path);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $componentName = $file->getFilenameWithoutExtension();
                $components[] = $this->analyzeComponent($category, $componentName, $file->getPathname());
            }
        }

        return $components;
    }

    /**
     * Scan root components directory for uncategorized components
     *
     * @return array<int, array<string, mixed>>
     */
    private function scanRootComponents(): array
    {
        $rootPath = resource_path('views/components');
        $components = [];

        $files = File::files($rootPath);

        foreach ($files as $file) {
            if ($file->getExtension() === 'php') {
                $componentName = $file->getFilenameWithoutExtension();
                $components[] = $this->analyzeComponent('uncategorized', $componentName, $file->getPathname());
            }
        }

        return $components;
    }

    /**
     * Analyze a single component file
     *
     * @return array<string, mixed>
     */
    private function analyzeComponent(string $category, string $name, string $path): array
    {
        $content = File::get($path);
        $metadata = $this->extractMetadata($content);

        return [
            'name' => $name,
            'category' => $category,
            'path' => $path,
            'relative_path' => str_replace(resource_path(), '', $path),
            'content' => $content,
            'type' => $this->determineComponentType($path, $content),
            'size' => File::size($path),
            'lines' => substr_count($content, "\n") + 1,
            'has_metadata' => $metadata['has_metadata'],
            'metadata' => $metadata,
            'props' => $this->extractProps($content),
            'slots' => $this->extractSlots($content),
            'dependencies' => $this->extractDependencies($content),
            'wcag_compliant' => $this->checkWCAGCompliance($content),
            'uses_deprecated_colors' => $this->checkDeprecatedColors($content),
            'last_modified' => File::lastModified($path),
        ];
    }

    /**
     * Extract metadata from component content
     *
     * @return array<string, mixed>
     */
    private function extractMetadata(string $content): array
    {
        $metadata = [
            'has_metadata' => false,
            'component_name' => null,
            'description' => null,
            'author' => null,
            'version' => null,
            'created' => null,
            'updated' => null,
            'requirements' => [],
            'wcag_level' => null,
            'standards' => [],
        ];

        // Check for @component tag
        if (preg_match('/@component\s*\n\s*\*\s*@name\s+(.+)/', $content, $matches)) {
            $metadata['has_metadata'] = true;
            $metadata['component_name'] = trim($matches[1]);
        }

        // Extract description
        if (preg_match('/@description\s+(.+)/', $content, $matches)) {
            $metadata['description'] = trim($matches[1]);
        }

        // Extract author
        if (preg_match('/@author\s+(.+)/', $content, $matches)) {
            $metadata['author'] = trim($matches[1]);
        }

        // Extract version
        if (preg_match('/@version\s+([\d.]+)/', $content, $matches)) {
            $metadata['version'] = trim($matches[1]);
        }

        // Extract created date
        if (preg_match('/@(?:created|since)\s+([\d-]+)/', $content, $matches)) {
            $metadata['created'] = trim($matches[1]);
        }

        // Extract updated date
        if (preg_match('/@updated\s+([\d-]+)/', $content, $matches)) {
            $metadata['updated'] = trim($matches[1]);
        }

        // Extract requirements
        if (preg_match_all('/Requirements?:\s*([^\n]+)/', $content, $matches)) {
            $metadata['requirements'] = array_map('trim', explode(',', $matches[1][0] ?? ''));
        }

        // Extract WCAG level
        if (preg_match('/WCAG Level:\s*([^\n]+)/', $content, $matches)) {
            $metadata['wcag_level'] = trim($matches[1]);
        }

        // Extract standards
        if (preg_match_all('/Standards?:\s*([^\n]+)/', $content, $matches)) {
            $metadata['standards'] = array_map('trim', explode(',', $matches[1][0] ?? ''));
        }

        return $metadata;
    }

    /**
     * Extract component props
     *
     * @return array<int, string>
     */
    private function extractProps(string $content): array
    {
        $props = [];

        if (preg_match('/@props\(\[(.*?)\]\)/s', $content, $matches)) {
            $propsString = $matches[1];
            preg_match_all("/'([^']+)'\s*=>/", $propsString, $propMatches);
            $props = $propMatches[1];
        }

        return $props;
    }

    /**
     * Extract component slots
     *
     * @return array<int, string>
     */
    private function extractSlots(string $content): array
    {
        $slots = [];

        // Check for default slot
        if (str_contains($content, '{{ $slot }}')) {
            $slots[] = 'default';
        }

        // Check for named slots
        preg_match_all('/\{\{\s*\$([a-zA-Z_]+)\s*\}\}/', $content, $matches);
        foreach ($matches[1] as $slot) {
            if ($slot !== 'slot' && ! in_array($slot, $slots)) {
                $slots[] = $slot;
            }
        }

        return $slots;
    }

    /**
     * Extract component dependencies
     *
     * @return array<int, string>
     */
    private function extractDependencies(string $content): array
    {
        $dependencies = [];

        // Check for x-component usage
        preg_match_all('/<x-([a-z.-]+)/', $content, $matches);
        $dependencies = array_unique($matches[1]);

        return $dependencies;
    }

    /**
     * Check WCAG compliance indicators
     */
    private function checkWCAGCompliance(string $content): bool
    {
        $indicators = [
            'aria-label',
            'aria-describedby',
            'aria-required',
            'aria-invalid',
            'role=',
            'min-h-[44px]',
            'focus:ring',
        ];

        foreach ($indicators as $indicator) {
            if (str_contains($content, $indicator)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check for deprecated color usage
     */
    private function checkDeprecatedColors(string $content): bool
    {
        $deprecatedColors = [
            '#F1C40F', // Old warning yellow
            '#E74C3C', // Old danger red
            'yellow-400',
            'yellow-500',
            'red-500',
            'red-600',
        ];

        foreach ($deprecatedColors as $color) {
            if (str_contains($content, $color)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Identify obsolete components
     *
     * @param  array<int, array<string, mixed>>  $components
     * @return array<int, array<string, mixed>>
     */
    private function identifyObsoleteComponents(array $components): array
    {
        $obsolete = [];

        foreach ($components as $component) {
            // Components without metadata and not recently modified
            if (! $component['has_metadata'] && $component['last_modified'] < strtotime('-6 months')) {
                $obsolete[] = $component;
            }
        }

        return $obsolete;
    }

    /**
     * Identify duplicate components
     *
     * @param  array<int, array<string, mixed>>  $components
     * @return array<int, array<string, mixed>>
     */
    private function identifyDuplicates(array $components): array
    {
        $duplicates = [];
        $seen = [];

        foreach ($components as $component) {
            $name = $component['name'];

            if (isset($seen[$name])) {
                $duplicates[] = [
                    'name' => $name,
                    'instances' => [$seen[$name], $component],
                ];
            } else {
                $seen[$name] = $component;
            }
        }

        return $duplicates;
    }

    /**
     * Generate inventory report
     */
    public function generateReport(): string
    {
        $inventory = $this->getInventory();

        $report = "# Component Library Inventory Report\n\n";
        $report .= 'Generated: '.date('Y-m-d H:i:s')."\n\n";

        $report .= "## Summary\n\n";
        $report .= "- **Total Components**: {$inventory['total_components']}\n";
        $report .= '- **Obsolete Components**: '.count($inventory['obsolete'])."\n";
        $report .= '- **Duplicate Components**: '.count($inventory['duplicates'])."\n\n";

        $report .= "## Components by Category\n\n";
        foreach ($inventory['by_category'] as $category => $data) {
            $report .= "### {$category} ({$data['count']} components)\n\n";
            foreach ($data['components'] as $component) {
                $report .= "- **{$component['name']}**\n";
                $report .= "  - Path: `{$component['relative_path']}`\n";
                $report .= "  - Lines: {$component['lines']}\n";
                $report .= '  - Has Metadata: '.($component['has_metadata'] ? 'Yes' : 'No')."\n";
                $report .= '  - WCAG Compliant: '.($component['wcag_compliant'] ? 'Yes' : 'No')."\n";
                $report .= '  - Uses Deprecated Colors: '.($component['uses_deprecated_colors'] ? 'Yes' : 'No')."\n";
                if (! empty($component['props'])) {
                    $report .= '  - Props: '.implode(', ', $component['props'])."\n";
                }
                $report .= "\n";
            }
        }

        if (! empty($inventory['obsolete'])) {
            $report .= "## Obsolete Components\n\n";
            foreach ($inventory['obsolete'] as $component) {
                $report .= "- {$component['name']} ({$component['category']})\n";
            }
            $report .= "\n";
        }

        if (! empty($inventory['duplicates'])) {
            $report .= "## Duplicate Components\n\n";
            foreach ($inventory['duplicates'] as $duplicate) {
                $report .= "- **{$duplicate['name']}**\n";
                foreach ($duplicate['instances'] as $instance) {
                    $report .= "  - {$instance['relative_path']}\n";
                }
                $report .= "\n";
            }
        }

        return $report;
    }

    /**
     * Determine component type based on path and content
     */
    private function determineComponentType(string $path, string $content): string
    {
        // Check if it's a Livewire component
        if (str_contains($path, 'livewire') || str_contains($content, '@livewire') || str_contains($content, 'Livewire\Component')) {
            return 'livewire_component';
        }

        // Check if it's a Filament component
        if (str_contains($path, 'filament') || str_contains($content, 'Filament\\')) {
            return 'filament_component';
        }

        // Check if it's a form component
        if (str_contains($path, 'form')) {
            return 'form_component';
        }

        // Check if it's a layout component
        if (str_contains($path, 'layout')) {
            return 'layout_component';
        }

        // Check if it's a navigation component
        if (str_contains($path, 'navigation')) {
            return 'navigation_component';
        }

        // Check if it's a UI component
        if (str_contains($path, 'ui')) {
            return 'ui_component';
        }

        // Default to Blade component
        return 'blade_component';
    }
}
