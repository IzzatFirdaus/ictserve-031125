# Frontend Performance Optimization Guide

## Overview

This document describes the performance optimization strategies implemented in the ICTServe frontend to ensure fast page loads, smooth interactions, and excellent Core Web Vitals scores.

**Requirements:** 9.3, 10.1, 10.2, 10.3

## Table of Contents

1. [Asset Optimization](#asset-optimization)
2. [Code Splitting and Lazy Loading](#code-splitting-and-lazy-loading)
3. [Image Optimization](#image-optimization)
4. [Livewire Component Optimization](#livewire-component-optimization)
5. [Core Web Vitals Monitoring](#core-web-vitals-monitoring)
6. [Performance Budgets](#performance-budgets)
7. [Best Practices](#best-practices)

## Asset Optimization

### Vite Configuration

The application uses Vite for asset compilation with advanced optimization features:

```javascript
// vite.config.js
export default defineConfig({
    build: {
        // Code splitting for better caching
        rollupOptions: {
            output: {
                manualChunks: (id) => {
                    if (id.includes('node_modules')) {
                        if (id.includes('axios')) return 'vendor-axios';
                        if (id.includes('alpine')) return 'vendor-alpine';
                        if (id.includes('livewire')) return 'vendor-livewire';
                        return 'vendor';
                    }
                },
            },
        },
        // Minification and compression
        minify: 'terser',
        cssMinify: true,
    },
});
```

### Compression

- **Gzip compression** for all text assets
- **Brotli compression** for modern browsers
- Automatic compression during build process

### Build Commands

```bash
# Development build
npm run dev

# Production build with optimization
npm run build

# Analyze bundle size
ANALYZE=true npm run build
```

## Code Splitting and Lazy Loading

### Dynamic Imports

Use dynamic imports for heavy components:

```javascript
// Lazy load heavy components
const HeavyComponent = () => import('./components/HeavyComponent.vue');

// Use in component
<component :is="HeavyComponent" />
```

### Lazy Load Blade Component

Use the `<x-lazy-load>` component for content that should load on scroll:

```blade
<x-lazy-load threshold="0.1" root-margin="50px">
    {{-- Heavy content here --}}
    <x-data-table :data="$largeDataset" />
</x-lazy-load>
```

### Route-based Code Splitting

Vite automatically splits code by route. Ensure routes are properly organized:

```php
// routes/web.php
Route::get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');
    
Route::get('/reports', [ReportController::class, 'index'])
    ->name('reports.index');
```

## Image Optimization

### ImageOptimizationService

The `ImageOptimizationService` provides comprehensive image optimization:

```php
use App\Services\ImageOptimizationService;

$imageService = app(ImageOptimizationService::class);

// Generate responsive images
$images = $imageService->optimizeResponsiveImage(
    $sourcePath,
    $destinationPath,
    [320, 640, 768, 1024, 1280, 1536] // breakpoints
);

// Generate picture element
$html = $imageService->generatePictureElement(
    $images,
    'Alt text',
    'responsive-image',
    ['(max-width: 640px) 100vw', '(max-width: 1024px) 50vw', '33vw']
);
```

### Responsive Image Component

Use the `<x-responsive-image>` component for automatic responsive images:

```blade
<x-responsive-image
    src="/images/hero.jpg"
    alt="Hero image"
    class="w-full h-auto"
    sizes="(max-width: 640px) 100vw, (max-width: 1024px) 50vw, 33vw"
    loading="lazy"
    decoding="async"
/>
```

### Image Best Practices

1. **Use WebP format** with JPEG fallback
2. **Lazy load images** below the fold
3. **Provide width and height** to prevent layout shift
4. **Use blur placeholders** for better perceived performance
5. **Optimize image quality** based on viewport size

## Livewire Component Optimization

### LivewireOptimizationService

The `LivewireOptimizationService` provides optimization strategies:

```php
use App\Services\LivewireOptimizationService;

class MyLivewireComponent extends Component
{
    private LivewireOptimizationService $optimizer;
    
    public function mount()
    {
        $this->optimizer = app(LivewireOptimizationService::class);
    }
    
    public function getData()
    {
        // Cache component data
        return $this->optimizer->cacheComponentData(
            'my-component',
            'data-key',
            fn() => $this->fetchData(),
            300 // 5 minutes
        );
    }
    
    public function getTicketsProperty()
    {
        // Optimize query
        $query = Ticket::query();
        $query = $this->optimizer->optimizeQuery(
            $query,
            ['user', 'category'], // eager load
            ['id', 'title', 'status', 'created_at'] // select columns
        );
        
        return $query->paginate(15);
    }
}
```

### Component Best Practices

1. **Use wire:model.lazy** instead of wire:model.live for non-critical updates
2. **Implement debouncing** for search inputs (300ms)
3. **Cache frequently accessed data** (5-15 minutes)
4. **Eager load relationships** to prevent N+1 queries
5. **Select only needed columns** in queries
6. **Use pagination** for large datasets
7. **Implement lazy loading** for heavy components

### Example: Optimized Component

```php
class TicketList extends Component
{
    public $search = '';
    public $status = '';
    
    // Debounce search input
    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];
    
    public function getTicketsProperty()
    {
        $optimizer = app(LivewireOptimizationService::class);
        
        // Cache key based on filters
        $cacheKey = "tickets.{$this->search}.{$this->status}";
        
        return $optimizer->cacheComponentData(
            'ticket-list',
            $cacheKey,
            function () use ($optimizer) {
                $query = Ticket::query();
                
                // Optimize query
                $query = $optimizer->optimizeQuery(
                    $query,
                    ['user', 'assignedTo', 'category'],
                    ['id', 'ticket_number', 'subject', 'status', 'priority', 'created_at']
                );
                
                // Apply filters
                if ($this->search) {
                    $query->where('subject', 'like', "%{$this->search}%");
                }
                
                if ($this->status) {
                    $query->where('status', $this->status);
                }
                
                // Get pagination settings
                $settings = $optimizer->getOptimizedPaginationSettings(
                    $query->count()
                );
                
                return $query->paginate($settings['perPage']);
            },
            300 // 5 minutes
        );
    }
    
    public function render()
    {
        return view('livewire.ticket-list');
    }
}
```

## Core Web Vitals Monitoring

### Automatic Monitoring

Core Web Vitals are automatically monitored in production:

- **LCP (Largest Contentful Paint):** Target < 2.5s
- **FID (First Input Delay):** Target < 100ms
- **CLS (Cumulative Layout Shift):** Target < 0.1
- **TTFB (Time to First Byte):** Target < 800ms

### Performance Monitoring Service

```php
use App\Services\PerformanceMonitoringService;

$performanceService = app(PerformanceMonitoringService::class);

// Get performance report
$report = $performanceService->getPerformanceReport();

// Get budget status
$budget = $performanceService->getPerformanceBudgetStatus();

// Get alerts
$alerts = $performanceService->getPerformanceAlerts();
```

### Frontend Tracking

Performance metrics are automatically sent to the backend:

```javascript
import { initPerformanceMonitoring } from './performance-monitoring';

// Auto-initialized on page load
// Metrics are sent via Beacon API (non-blocking)
```

### Viewing Metrics

Access performance metrics via API:

```bash
# Get performance report
GET /api/performance/report

# Get budget status
GET /api/performance/budget-status

# Get alerts
GET /api/performance/alerts
```

## Performance Budgets

### Defined Budgets

| Metric | Budget | Warning | Critical |
|--------|--------|---------|----------|
| LCP | < 2.5s | 2.5-4.0s | > 4.0s |
| FID | < 100ms | 100-300ms | > 300ms |
| CLS | < 0.1 | 0.1-0.25 | > 0.25 |
| TTFB | < 800ms | 800-1800ms | > 1800ms |
| Bundle Size | < 500KB | 500-750KB | > 750KB |
| Page Load | < 2s | 2-3s | > 3s |

### Monitoring Budgets

```php
// Check if budgets are exceeded
$budget = $performanceService->getPerformanceBudgetStatus();

foreach ($budget as $metric => $status) {
    if ($status['status'] === 'exceeded') {
        // Alert or log
        Log::warning("Performance budget exceeded for {$metric}");
    }
}
```

## Best Practices

### General

1. **Minimize HTTP requests** - Combine files, use sprites
2. **Enable compression** - Gzip/Brotli for text assets
3. **Use CDN** - Serve static assets from CDN
4. **Implement caching** - Browser caching, server-side caching
5. **Optimize critical rendering path** - Inline critical CSS

### JavaScript

1. **Defer non-critical JavaScript** - Use defer/async attributes
2. **Remove unused code** - Tree shaking, dead code elimination
3. **Minimize main thread work** - Use Web Workers for heavy tasks
4. **Avoid long tasks** - Break up long-running operations

### CSS

1. **Remove unused CSS** - PurgeCSS in production
2. **Minimize CSS** - Minify and compress
3. **Avoid @import** - Use link tags instead
4. **Use CSS containment** - Isolate layout calculations

### Images

1. **Use modern formats** - WebP, AVIF with fallbacks
2. **Lazy load images** - Below the fold images
3. **Provide dimensions** - Prevent layout shift
4. **Optimize quality** - Balance quality vs file size

### Livewire

1. **Cache component data** - Reduce database queries
2. **Debounce updates** - Prevent excessive requests
3. **Lazy load components** - Load on demand
4. **Optimize queries** - Eager loading, select columns

## Troubleshooting

### Slow Page Loads

1. Check bundle size: `ANALYZE=true npm run build`
2. Review network waterfall in DevTools
3. Check database query performance
4. Review Livewire component render times

### Poor Core Web Vitals

1. **LCP issues:** Optimize images, reduce server response time
2. **FID issues:** Minimize JavaScript execution, use code splitting
3. **CLS issues:** Provide image dimensions, avoid dynamic content insertion
4. **TTFB issues:** Optimize server response, use caching

### High Memory Usage

1. Clear component caches: `$optimizer->invalidateComponentCache()`
2. Reduce cache duration for frequently changing data
3. Implement pagination for large datasets
4. Use lazy loading for heavy components

## Monitoring and Alerts

### Real-time Monitoring

Performance metrics are monitored in real-time and stored for analysis:

```php
// Get real-time metrics
$metrics = $performanceService->getMetricSummary('LCP');

// Clear metrics
$performanceService->clearMetrics('LCP');
```

### Alerts

Alerts are automatically generated when budgets are exceeded:

```php
$alerts = $performanceService->getPerformanceAlerts();

foreach ($alerts as $alert) {
    // Send notification
    Notification::send($admins, new PerformanceAlert($alert));
}
```

## Resources

- [Web Vitals](https://web.dev/vitals/)
- [Vite Performance](https://vitejs.dev/guide/performance.html)
- [Livewire Performance](https://livewire.laravel.com/docs/performance)
- [Laravel Performance](https://laravel.com/docs/performance)

## Conclusion

Following these optimization strategies ensures the ICTServe frontend delivers excellent performance with fast page loads, smooth interactions, and high Core Web Vitals scores.

For questions or issues, contact the development team.
