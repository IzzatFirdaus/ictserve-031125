<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\Cache;

/**
 * Widget Lazy Loader Service
 *
 * Implements deferred widget loading for optimal performance
 * following ICTServe v3.6.1 patterns and Filament v4.3.1 compliance.
 *
 * @trace Requirements: R4 (Widget Performance), R17 (Performance Standards)
 *
 * @see D04 §3.2 Widget Management Architecture
 *
 * @version 3.6.1
 */
class WidgetLazyLoader
{
    /**
     * Loading priorities by category
     */
    private const LOADING_PRIORITIES = [
        'header' => 1,    // Load first - critical stats
        'charts' => 2,    // Load second - data visualizations
        'content' => 3,   // Load last - detailed content
    ];

    /**
     * Default lazy loading thresholds
     */
    private const LAZY_LOAD_THRESHOLDS = [
        'viewport_distance' => 200,  // Pixels from viewport
        'max_concurrent' => 3,       // Maximum concurrent loads
        'retry_attempts' => 3,       // Retry failed loads
        'timeout' => 10000,          // Timeout in milliseconds
    ];

    /**
     * Check if widget should be lazy loaded
     *
     * @param  string  $widgetClass  Widget class name
     * @param  array  $config  Widget configuration
     * @return bool True if should be lazy loaded
     */
    public function shouldLazyLoad(string $widgetClass, array $config = []): bool
    {
        // Never lazy load critical header widgets
        $category = $config['category'] ?? $this->detectCategory($widgetClass);

        if ($category === 'header') {
            return false;
        }

        // Check explicit configuration
        if (isset($config['lazy_load'])) {
            return (bool) $config['lazy_load'];
        }

        // Check if widget is marked as non-critical
        return $this->isNonCriticalWidget($widgetClass);
    }

    /**
     * Get loading priority for widget
     *
     * @param  string  $widgetClass  Widget class name
     * @param  array  $config  Widget configuration
     * @return int Priority (1 = highest, 3 = lowest)
     */
    public function getLazyLoadPriority(string $widgetClass, array $config = []): int
    {
        $category = $config['category'] ?? $this->detectCategory($widgetClass);

        return self::LOADING_PRIORITIES[$category] ?? 3;
    }

    /**
     * Create lazy load placeholder component
     *
     * @param  string  $widgetClass  Widget class name
     * @param  array  $config  Widget configuration
     * @return string HTML placeholder
     */
    public function createLazyLoadComponent(string $widgetClass, array $config = []): string
    {
        $priority = $this->getLazyLoadPriority($widgetClass, $config);
        $placeholder = $this->getPlaceholderContent($widgetClass, $config);
        $loadingId = 'lazy-widget-'.md5($widgetClass);

        return view('components.lazy-widget', [
            'widgetClass' => $widgetClass,
            'priority' => $priority,
            'placeholder' => $placeholder,
            'loadingId' => $loadingId,
            'config' => $config,
        ])->render();
    }

    /**
     * Generate placeholder content for widget
     *
     * @param  string  $widgetClass  Widget class name
     * @param  array  $config  Widget configuration
     * @return array Placeholder configuration
     */
    public function getPlaceholderContent(string $widgetClass, array $config = []): array
    {
        $category = $config['category'] ?? $this->detectCategory($widgetClass);
        $displayName = $this->getWidgetDisplayName($widgetClass);

        return match ($category) {
            'header' => [
                'type' => 'stats',
                'title' => $displayName,
                'skeleton' => 'stats-skeleton',
                'height' => 'h-24',
                'animation' => 'pulse',
            ],
            'charts' => [
                'type' => 'chart',
                'title' => $displayName,
                'skeleton' => 'chart-skeleton',
                'height' => 'h-64',
                'animation' => 'pulse',
            ],
            'content' => [
                'type' => 'content',
                'title' => $displayName,
                'skeleton' => 'content-skeleton',
                'height' => 'h-48',
                'animation' => 'pulse',
            ],
            default => [
                'type' => 'generic',
                'title' => $displayName,
                'skeleton' => 'generic-skeleton',
                'height' => 'h-32',
                'animation' => 'pulse',
            ],
        };
    }

    /**
     * Get JavaScript for intersection observer
     *
     * @return string JavaScript code
     */
    public function getIntersectionObserverScript(): string
    {
        return <<<'JS'
// Widget Lazy Loading with Intersection Observer
document.addEventListener('DOMContentLoaded', function() {
    const lazyWidgets = document.querySelectorAll('[data-lazy-widget]');
    const loadingQueue = [];
    let concurrentLoads = 0;
    const maxConcurrent = 3;

    // Intersection Observer for viewport detection
    const observer = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                const widget = entry.target;
                const priority = parseInt(widget.dataset.priority) || 3;
                
                // Add to loading queue
                loadingQueue.push({
                    element: widget,
                    priority: priority,
                    widgetClass: widget.dataset.widgetClass
                });
                
                // Stop observing this widget
                observer.unobserve(widget);
            }
        });
        
        // Process queue
        processLoadingQueue();
    }, {
        rootMargin: '200px', // Load 200px before entering viewport
        threshold: 0.1
    });

    // Observe all lazy widgets
    lazyWidgets.forEach(widget => {
        observer.observe(widget);
    });

    // Process loading queue with priority and concurrency control
    function processLoadingQueue() {
        // Sort by priority (lower number = higher priority)
        loadingQueue.sort((a, b) => a.priority - b.priority);
        
        while (loadingQueue.length > 0 && concurrentLoads < maxConcurrent) {
            const item = loadingQueue.shift();
            loadWidget(item.element, item.widgetClass);
        }
    }

    // Load individual widget
    function loadWidget(element, widgetClass) {
        concurrentLoads++;
        
        // Show loading state
        element.classList.add('loading');
        
        // Fetch widget content
        fetch(`/admin/widgets/load/${encodeURIComponent(widgetClass)}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}`);
            }
            return response.text();
        })
        .then(html => {
            // Replace placeholder with actual widget
            element.innerHTML = html;
            element.classList.remove('loading');
            element.classList.add('loaded');
            
            // Dispatch loaded event
            element.dispatchEvent(new CustomEvent('widget:loaded', {
                detail: { widgetClass: widgetClass }
            }));
        })
        .catch(error => {
            console.error('Failed to load widget:', widgetClass, error);
            
            // Show error state
            element.classList.remove('loading');
            element.classList.add('error');
            element.innerHTML = `
                <div class="p-4 text-center text-gray-500">
                    <p>Widget gagal dimuat</p>
                    <button onclick="retryLoadWidget(this, '${widgetClass}')" 
                            class="mt-2 text-sm text-blue-600 hover:text-blue-800">
                        Cuba Semula
                    </button>
                </div>
            `;
        })
        .finally(() => {
            concurrentLoads--;
            // Process next items in queue
            setTimeout(processLoadingQueue, 100);
        });
    }

    // Retry failed widget load
    window.retryLoadWidget = function(button, widgetClass) {
        const element = button.closest('[data-lazy-widget]');
        element.classList.remove('error');
        loadWidget(element, widgetClass);
    };

    // Load high priority widgets immediately
    const highPriorityWidgets = document.querySelectorAll('[data-lazy-widget][data-priority="1"]');
    highPriorityWidgets.forEach(widget => {
        loadingQueue.push({
            element: widget,
            priority: 1,
            widgetClass: widget.dataset.widgetClass
        });
        observer.unobserve(widget);
    });
    
    processLoadingQueue();
});
JS;
    }

    /**
     * Detect widget category from class name
     *
     * @param  string  $widgetClass  Widget class name
     * @return string Category
     */
    private function detectCategory(string $widgetClass): string
    {
        if (! class_exists($widgetClass)) {
            return 'content';
        }

        $reflection = new \ReflectionClass($widgetClass);

        if ($reflection->isSubclassOf('Filament\Widgets\StatsOverviewWidget')) {
            return 'header';
        }

        if ($reflection->isSubclassOf('Filament\Widgets\ChartWidget')) {
            return 'charts';
        }

        return 'content';
    }

    /**
     * Check if widget is non-critical
     *
     * @param  string  $widgetClass  Widget class name
     * @return bool True if non-critical
     */
    private function isNonCriticalWidget(string $widgetClass): bool
    {
        $className = class_basename($widgetClass);

        // Critical widgets that should never be lazy loaded
        $criticalPatterns = [
            'Stats',
            'Overview',
            'Summary',
            'Alert',
            'Critical',
            'Emergency',
        ];

        foreach ($criticalPatterns as $pattern) {
            if (str_contains($className, $pattern)) {
                return false;
            }
        }

        return true;
    }

    /**
     * Get display name for widget
     *
     * @param  string  $widgetClass  Widget class name
     * @return string Display name
     */
    private function getWidgetDisplayName(string $widgetClass): string
    {
        $className = class_basename($widgetClass);

        // Convert PascalCase to readable format
        return preg_replace('/(?<!^)[A-Z]/', ' $0', $className) ?? $className;
    }

    /**
     * Get lazy loading statistics
     *
     * @return array Statistics
     */
    public function getStatistics(): array
    {
        return Cache::remember('lazy_loader_stats', 300, function () {
            // This would be populated by actual usage metrics
            return [
                'total_widgets' => 0,
                'lazy_loaded' => 0,
                'immediate_loaded' => 0,
                'average_load_time' => 0,
                'cache_hit_rate' => 0,
            ];
        });
    }
}
