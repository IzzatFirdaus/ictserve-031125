<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

/**
 * Widget Layout Manager Service
 *
 * Manages user widget preferences, layouts, and customization options
 * following ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @trace Requirements: R5 (Widget Configuration), R20 (Widget Customization)
 *
 * @see D04 §3.2 Widget Management Architecture
 * @see D12 §3.4 User Experience Design
 *
 * @version 3.6.1
 */
class WidgetLayoutManager
{
    private const CACHE_TTL = 3600; // 1 hour

    private const DEFAULT_WIDGET_SIZE = 'medium';

    private const VALID_WIDGET_SIZES = ['small', 'medium', 'large'];

    public function __construct(
        private WidgetRegistry $widgetRegistry,
        private WidgetCategorizer $widgetCategorizer
    ) {}

    /**
     * Get user's widget layout preferences
     *
     * @param  User  $user  User instance
     * @return array User's widget layout configuration
     */
    public function getUserLayout(User $user): array
    {
        $cacheKey = "widget_layout.user.{$user->id}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($user) {
            $layout = $user->dashboard_layout ?? [];

            // Merge with default layout if empty or incomplete
            if (empty($layout)) {
                $layout = $this->getDefaultLayout($user);
                $this->saveUserLayout($user, $layout);
            }

            return $this->validateAndNormalizeLayout($layout);
        });
    }

    /**
     * Save user's widget layout preferences
     *
     * @param  User  $user  User instance
     * @param  array  $layout  Layout configuration
     * @return bool Success status
     */
    

/**
 * @param array<string, mixed> $layout
 */
public function saveUserLayout(User $user, array $layout): bool
    {
        try {
            $normalizedLayout = $this->validateAndNormalizeLayout($layout);

            $user->update([
                'dashboard_layout' => $normalizedLayout,
            ]);

            // Clear cache
            Cache::forget("widget_layout.user.{$user->id}");

            Log::info('User widget layout saved', [
                'user_id' => $user->id,
                'layout_summary' => $this->getLayoutSummary($normalizedLayout),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Failed to save user widget layout', [
                'user_id' => $user->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Get default widget layout based on user role
     *
     * @param  User  $user  User instance
     * @return array Default layout configuration
     */
    public function getDefaultLayout(User $user): array
    {
        $userRole = $this->getUserPrimaryRole($user);
        $availableWidgets = $this->widgetRegistry->getWidgetsByRole($userRole);

        $layout = [
            'version' => '1.0',
            'created_at' => now()->toISOString(),
            'widgets' => [],
            'hidden_widgets' => [],
            'widget_sizes' => [],
            'category_order' => ['header', 'content', 'charts'],
        ];

        // Organize widgets by category with default visibility and sizes
        foreach ($availableWidgets as $widget) {
            $widgetClass = $widget['widget_class'];
            $category = $widget['category'];

            $layout['widgets'][$category][] = [
                'class' => $widgetClass,
                'sort_order' => $widget['sort_order'],
                'visible' => true,
                'size' => self::DEFAULT_WIDGET_SIZE,
            ];

            $layout['widget_sizes'][$widgetClass] = self::DEFAULT_WIDGET_SIZE;
        }

        // Sort widgets within each category
        foreach ($layout['widgets'] as $category => $widgets) {
            usort($layout['widgets'][$category], function ($a, $b) {
                return $a['sort_order'] <=> $b['sort_order'];
            });
        }

        return $layout;
    }

    /**
     * Reset user layout to default
     *
     * @param  User  $user  User instance
     * @return bool Success status
     */
    public function resetToDefault(User $user): bool
    {
        $defaultLayout = $this->getDefaultLayout($user);

        return $this->saveUserLayout($user, $defaultLayout);
    }

    /**
     * Toggle widget visibility
     *
     * @param  User  $user  User instance
     * @param  string  $widgetClass  Widget class name
     * @param  bool  $visible  Visibility state
     * @return bool Success status
     */
    public function toggleWidgetVisibility(User $user, string $widgetClass, bool $visible): bool
    {
        $layout = $this->getUserLayout($user);

        // Update visibility in widgets array
        foreach ($layout['widgets'] as $category => &$widgets) {
            foreach ($widgets as &$widget) {
                if ($widget['class'] === $widgetClass) {
                    $widget['visible'] = $visible;
                    break 2;
                }
            }
        }

        // Update hidden_widgets array
        if ($visible) {
            $layout['hidden_widgets'] = array_filter(
                $layout['hidden_widgets'] ?? [],
                fn ($class) => $class !== $widgetClass
            );
        } else {
            $layout['hidden_widgets'][] = $widgetClass;
            $layout['hidden_widgets'] = array_unique($layout['hidden_widgets']);
        }

        return $this->saveUserLayout($user, $layout);
    }

    /**
     * Update widget size
     *
     * @param  User  $user  User instance
     * @param  string  $widgetClass  Widget class name
     * @param  string  $size  Widget size (small, medium, large)
     * @return bool Success status
     */
    public function updateWidgetSize(User $user, string $widgetClass, string $size): bool
    {
        if (! in_array($size, self::VALID_WIDGET_SIZES, true)) {
            Log::warning('Invalid widget size provided', [
                'widget_class' => $widgetClass,
                'size' => $size,
                'valid_sizes' => self::VALID_WIDGET_SIZES,
            ]);

            return false;
        }

        $layout = $this->getUserLayout($user);

        // Update size in widgets array
        foreach ($layout['widgets'] as $category => &$widgets) {
            foreach ($widgets as &$widget) {
                if ($widget['class'] === $widgetClass) {
                    $widget['size'] = $size;
                    break 2;
                }
            }
        }

        // Update widget_sizes array
        $layout['widget_sizes'][$widgetClass] = $size;

        return $this->saveUserLayout($user, $layout);
    }

    /**
     * Update widget order within category
     *
     * @param  User  $user  User instance
     * @param  string  $category  Widget category
     * @param  array  $widgetOrder  Array of widget class names in desired order
     * @return bool Success status
     */
    

/**
 * @param array<string, mixed> $widgetOrder
 */
public function updateWidgetOrder(User $user, string $category, array $widgetOrder): bool
    {
        $layout = $this->getUserLayout($user);

        if (! isset($layout['widgets'][$category])) {
            return false;
        }

        // Create a mapping of widget class to widget data
        $widgetMap = [];
        foreach ($layout['widgets'][$category] as $widget) {
            $widgetMap[$widget['class']] = $widget;
        }

        // Reorder widgets based on provided order
        $reorderedWidgets = [];
        foreach ($widgetOrder as $index => $widgetClass) {
            if (isset($widgetMap[$widgetClass])) {
                $widget = $widgetMap[$widgetClass];
                $widget['sort_order'] = $index + 1;
                $reorderedWidgets[] = $widget;
            }
        }

        $layout['widgets'][$category] = $reorderedWidgets;

        return $this->saveUserLayout($user, $layout);
    }

    /**
     * Export user layout configuration
     *
     * @param  User  $user  User instance
     * @return array Exportable layout configuration
     */
    public function exportLayout(User $user): array
    {
        $layout = $this->getUserLayout($user);

        return [
            'version' => $layout['version'] ?? '1.0',
            'exported_at' => now()->toISOString(),
            'user_role' => $this->getUserPrimaryRole($user),
            'layout' => $layout,
        ];
    }

    /**
     * Import layout configuration for user
     *
     * @param  User  $user  User instance
     * @param  array  $importData  Imported layout data
     * @return bool Success status
     */
    

/**
 * @param array<string, mixed> $importData
 */
public function importLayout(User $user, array $importData): bool
    {
        if (! isset($importData['layout']) || ! is_array($importData['layout'])) {
            Log::warning('Invalid import data provided', [
                'user_id' => $user->id,
                'import_data' => $importData,
            ]);

            return false;
        }

        $layout = $importData['layout'];

        // Validate that all widgets in the import are available to the user
        $userRole = $this->getUserPrimaryRole($user);
        $availableWidgets = collect($this->widgetRegistry->getWidgetsByRole($userRole))
            ->pluck('widget_class')
            ->toArray();

        // Filter out widgets that are not available to the user
        foreach ($layout['widgets'] ?? [] as $category => &$widgets) {
            $widgets = array_filter($widgets, function ($widget) use ($availableWidgets) {
                return in_array($widget['class'], $availableWidgets, true);
            });
        }

        return $this->saveUserLayout($user, $layout);
    }

    /**
     * Get layout statistics for user
     *
     * @param  User  $user  User instance
     * @return array Layout statistics
     */
    public function getLayoutStatistics(User $user): array
    {
        $layout = $this->getUserLayout($user);

        $stats = [
            'total_widgets' => 0,
            'visible_widgets' => 0,
            'hidden_widgets' => count($layout['hidden_widgets'] ?? []),
            'categories' => [],
            'sizes' => [
                'small' => 0,
                'medium' => 0,
                'large' => 0,
            ],
        ];

        foreach ($layout['widgets'] ?? [] as $category => $widgets) {
            $categoryStats = [
                'total' => count($widgets),
                'visible' => 0,
                'hidden' => 0,
            ];

            foreach ($widgets as $widget) {
                $stats['total_widgets']++;

                if ($widget['visible'] ?? true) {
                    $stats['visible_widgets']++;
                    $categoryStats['visible']++;
                } else {
                    $categoryStats['hidden']++;
                }

                $size = $widget['size'] ?? self::DEFAULT_WIDGET_SIZE;
                if (isset($stats['sizes'][$size])) {
                    $stats['sizes'][$size]++;
                }
            }

            $stats['categories'][$category] = $categoryStats;
        }

        return $stats;
    }

    /**
     * Validate and normalize layout configuration
     *
     * @param  array  $layout  Layout configuration
     * @return array Normalized layout
     */
    

/**
 * @param array<string, mixed> $layout
 */
private function validateAndNormalizeLayout(array $layout): array
    {
        $normalized = [
            'version' => $layout['version'] ?? '1.0',
            'created_at' => $layout['created_at'] ?? now()->toISOString(),
            'updated_at' => now()->toISOString(),
            'widgets' => $layout['widgets'] ?? [],
            'hidden_widgets' => array_unique($layout['hidden_widgets'] ?? []),
            'widget_sizes' => $layout['widget_sizes'] ?? [],
            'category_order' => $layout['category_order'] ?? ['header', 'content', 'charts'],
        ];

        // Validate widget sizes
        foreach ($normalized['widget_sizes'] as $widgetClass => $size) {
            if (! in_array($size, self::VALID_WIDGET_SIZES, true)) {
                $normalized['widget_sizes'][$widgetClass] = self::DEFAULT_WIDGET_SIZE;
            }
        }

        return $normalized;
    }

    /**
     * Get user's primary role
     *
     * @param  User  $user  User instance
     * @return string Primary role name
     */
    private function getUserPrimaryRole(User $user): string
    {
        // Use the role column if available, otherwise check permissions
        if (! empty($user->role)) {
            return $user->role;
        }

        // Fallback to checking permissions
        if ($user->hasRole('superuser')) {
            return 'superuser';
        }

        if ($user->hasRole('admin')) {
            return 'admin';
        }

        return 'staff';
    }

    /**
     * Get layout summary for logging
     *
     * @param  array  $layout  Layout configuration
     * @return array Layout summary
     */
    

/**
 * @param array<string, mixed> $layout
 */
private function getLayoutSummary(array $layout): array
    {
        $summary = [
            'version' => $layout['version'] ?? '1.0',
            'total_widgets' => 0,
            'hidden_count' => count($layout['hidden_widgets'] ?? []),
            'categories' => [],
        ];

        foreach ($layout['widgets'] ?? [] as $category => $widgets) {
            $summary['categories'][$category] = count($widgets);
            $summary['total_widgets'] += count($widgets);
        }

        return $summary;
    }
}
