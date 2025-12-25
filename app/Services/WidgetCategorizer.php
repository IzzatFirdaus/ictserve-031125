<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Widget Categorizer Service
 *
 * Organizes widgets by type and importance following MyDS 12-8-4 grid system
 * and ICTServe v3.6.1 patterns with Filament v4.3.1 compliance.
 *
 * @trace Requirements: R2 (Widget Organization), R11 (MyDS Compliance)
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see D12 §4 MyDS Design System
 *
 * @version 3.6.1
 */
class WidgetCategorizer
{
    /**
     * Valid widget categories
     */
    private const VALID_CATEGORIES = ['header', 'content', 'charts'];

    /**
     * MyDS 12-8-4 grid breakpoints
     */
    private const GRID_BREAKPOINTS = [
        'desktop' => 12,  // 12 columns on desktop
        'tablet' => 8,    // 8 columns on tablet
        'mobile' => 4,    // 4 columns on mobile
    ];

    /**
     * Maximum widgets per category for optimal UX
     */
    private const MAX_WIDGETS_PER_CATEGORY = [
        'header' => 6,    // Maximum 6 header stats
        'content' => 12,  // Maximum 12 content widgets
        'charts' => 8,    // Maximum 8 chart widgets
    ];

    /**
     * Detect widget category based on class hierarchy
     *
     * @param  string  $widgetClass  Fully qualified widget class name
     * @return string Category (header, content, charts)
     */
    public function detectCategory(string $widgetClass): string
    {
        if (! class_exists($widgetClass)) {
            Log::warning('Widget class does not exist for categorization', [
                'widget_class' => $widgetClass,
            ]);

            return 'content';
        }

        try {
            $reflection = new \ReflectionClass($widgetClass);

            // Check parent class to determine category
            if ($reflection->isSubclassOf('Filament\Widgets\StatsOverviewWidget')) {
                return 'header';
            }

            if ($reflection->isSubclassOf('Filament\Widgets\ChartWidget')) {
                return 'charts';
            }

            // Check class name patterns for additional hints
            $className = class_basename($widgetClass);

            if (preg_match('/^(Stats|Overview|Summary|Count|Total)/', $className)) {
                return 'header';
            }

            if (preg_match('/(Chart|Graph|Analytics|Trend|Distribution)/', $className)) {
                return 'charts';
            }

            return 'content';
        } catch (\ReflectionException $e) {
            Log::error('Error detecting widget category', [
                'widget_class' => $widgetClass,
                'error' => $e->getMessage(),
            ]);

            return 'content';
        }
    }

    /**
     * Get next sort order for category
     *
     * @param  array  $widgets  Existing widget configurations
     * @param  string  $category  Target category
     * @return int Next sort order
     */
    

/**
 * @param array<string, mixed> $widgets
 */
public function getNextSortOrder(array $widgets, string $category): int
    {
        $maxOrder = 0;

        foreach ($widgets as $config) {
            if (($config['category'] ?? 'content') === $category) {
                $maxOrder = max($maxOrder, $config['sort_order'] ?? 1);
            }
        }

        return $maxOrder + 1;
    }

    /**
     * Organize widgets into sections
     *
     * @param  array  $widgets  Widget configurations
     * @return array Organized widget sections
     */
    

/**
 * @param array<string, mixed> $widgets
 */
public function organizeWidgets(array $widgets): array
    {
        $organized = [
            'header' => [],
            'content' => [],
            'charts' => [],
        ];

        foreach ($widgets as $widgetClass => $config) {
            $category = $config['category'] ?? $this->detectCategory($widgetClass);
            $sortOrder = $config['sort_order'] ?? 1;

            $organized[$category][] = [
                'class' => $widgetClass,
                'config' => $config,
                'sort_order' => $sortOrder,
            ];
        }

        // Sort each category by sort order
        foreach ($organized as $category => $categoryWidgets) {
            usort($organized[$category], function ($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
        }

        return $organized;
    }

    /**
     * Validate widget placement rules
     *
     * @param  array  $widgets  Widget configurations
     * @return array Validation results
     */
    

/**
 * @param array<string, mixed> $widgets
 */
public function validatePlacement(array $widgets): array
    {
        $organized = $this->organizeWidgets($widgets);
        $violations = [];

        foreach ($organized as $category => $categoryWidgets) {
            $count = count($categoryWidgets);
            $maxAllowed = self::MAX_WIDGETS_PER_CATEGORY[$category];

            // Check widget count limits
            if ($count > $maxAllowed) {
                $violations[] = [
                    'category' => $category,
                    'rule' => "Too many widgets ({$count}/{$maxAllowed})",
                    'severity' => 'warning',
                    'suggestion' => 'Consider moving some widgets to other categories or making them optional',
                ];
            }

            // Check sort order gaps
            $sortOrders = array_column($categoryWidgets, 'sort_order');
            $expectedOrder = range(1, count($sortOrders));

            if ($sortOrders !== $expectedOrder) {
                $violations[] = [
                    'category' => $category,
                    'rule' => 'Sort order gaps detected',
                    'severity' => 'info',
                    'suggestion' => 'Consider normalizing sort orders to sequential values',
                ];
            }
        }

        return [
            'is_valid' => empty($violations),
            'violations' => $violations,
            'summary' => [
                'header_count' => count($organized['header']),
                'content_count' => count($organized['content']),
                'charts_count' => count($organized['charts']),
                'total_count' => array_sum(array_map('count', $organized)),
            ],
        ];
    }

    /**
     * Get MyDS grid configuration for category
     *
     * @param  string  $category  Widget category
     * @return array Grid configuration
     */
    public function getGridConfiguration(string $category): array
    {
        return match ($category) {
            'header' => [
                'desktop' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-4 xl:grid-cols-6',
                'spacing' => 'gap-4 lg:gap-6',
                'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
            ],
            'charts' => [
                'desktop' => 'grid-cols-1 lg:grid-cols-2 xl:grid-cols-3',
                'spacing' => 'gap-6 lg:gap-8',
                'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
            ],
            'content' => [
                'desktop' => 'grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4',
                'spacing' => 'gap-4 md:gap-6',
                'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
            ],
            default => [
                'desktop' => 'grid-cols-1',
                'spacing' => 'gap-4',
                'container' => 'max-w-7xl mx-auto px-4 sm:px-6 lg:px-8',
            ],
        };
    }

    /**
     * Validate category name
     *
     * @param  string  $category  Category to validate
     * @return bool True if valid
     */
    public function isValidCategory(string $category): bool
    {
        return in_array($category, self::VALID_CATEGORIES, true);
    }

    /**
     * Get category statistics
     *
     * @param  array  $widgets  Widget configurations
     * @return array Category statistics
     */
    

/**
 * @param array<string, mixed> $widgets
 */
public function getStatistics(array $widgets): array
    {
        $organized = $this->organizeWidgets($widgets);

        return [
            'total_widgets' => count($widgets),
            'categories' => [
                'header' => [
                    'count' => count($organized['header']),
                    'percentage' => count($widgets) > 0
                        ? round((count($organized['header']) / count($widgets)) * 100, 2)
                        : 0,
                ],
                'content' => [
                    'count' => count($organized['content']),
                    'percentage' => count($widgets) > 0
                        ? round((count($organized['content']) / count($widgets)) * 100, 2)
                        : 0,
                ],
                'charts' => [
                    'count' => count($organized['charts']),
                    'percentage' => count($widgets) > 0
                        ? round((count($organized['charts']) / count($widgets)) * 100, 2)
                        : 0,
                ],
            ],
            'validation' => $this->validatePlacement($widgets),
        ];
    }

    /**
     * Suggest optimal category for widget
     *
     * @param  string  $widgetClass  Widget class name
     * @param  array  $existingWidgets  Existing widget configurations
     * @return array Suggestion with reasoning
     */
    

/**
 * @param array<string, mixed> $existingWidgets
 */
public function suggestCategory(string $widgetClass, array $existingWidgets = []): array
    {
        $detectedCategory = $this->detectCategory($widgetClass);
        $organized = $this->organizeWidgets($existingWidgets);
        $currentCount = count($organized[$detectedCategory]);
        $maxAllowed = self::MAX_WIDGETS_PER_CATEGORY[$detectedCategory];

        $suggestion = [
            'category' => $detectedCategory,
            'confidence' => 'high',
            'reasoning' => 'Based on class hierarchy analysis',
        ];

        // Check if category is at capacity
        if ($currentCount >= $maxAllowed) {
            $alternativeCategories = array_filter(self::VALID_CATEGORIES, function ($cat) use ($organized) {
                return count($organized[$cat]) < self::MAX_WIDGETS_PER_CATEGORY[$cat];
            });

            if (! empty($alternativeCategories)) {
                $suggestion = [
                    'category' => $alternativeCategories[0],
                    'confidence' => 'medium',
                    'reasoning' => "Primary category '{$detectedCategory}' is at capacity ({$currentCount}/{$maxAllowed})",
                ];
            }
        }

        return $suggestion;
    }
}
