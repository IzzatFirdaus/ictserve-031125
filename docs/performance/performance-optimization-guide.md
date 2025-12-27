# ICTServe Performance Optimization Guide

## Overview

This guide provides comprehensive instructions for optimizing performance across the ICTServe system to meet Core Web Vitals targets and ensure excellent user experience.

**Performance Targets:**

- LCP (Largest Contentful Paint): < 2.5 seconds
- FID (First Input Delay): < 100 milliseconds
- CLS (Cumulative Layout Shift): < 0.1
- TTFB (Time to First Byte): < 600 milliseconds
- Lighthouse Performance Score: 90+ (guest/authenticated), 85+ (admin)
- Lighthouse Accessibility Score: 100

**Document Traceability:**

- D07 System Integration Plan - Performance Testing
- D11 Technical Design - Performance Standards
- Requirements: 7.1, 7.2, 14.1, 15.4, 24.1

---

## 1. Performance Testing

### 1.1 Core Web Vitals Testing

Run automated Core Web Vitals tests:

```bash
# Run all Core Web Vitals tests
npm run test:e2e -- tests/e2e/performance/core-web-vitals.spec.ts

# Run specific page type tests
npm run test:e2e -- tests/e2e/performance/core-web-vitals.spec.ts --grep "Guest Pages"
npm run test:e2e -- tests/e2e/performance/core-web-vitals.spec.ts --grep "Authenticated Pages"
npm run test:e2e -- tests/e2e/performance/core-web-vitals.spec.ts --grep "Admin Pages"

# Generate comprehensive report
npm run test:e2e -- tests/e2e/performance/core-web-vitals.spec.ts --grep "Performance Report"
```

**Report Location:** `test-results/core-web-vitals-report.json`

### 1.2 Lighthouse Audit Testing

Run automated Lighthouse audits:

```bash
# Run all Lighthouse audits
npm run test:e2e -- tests/e2e/performance/lighthouse-audit.spec.ts

# Run specific page type audits
npm run test:e2e -- tests/e2e/performance/lighthouse-audit.spec.ts --grep "Guest Pages"
npm run test:e2e -- tests/e2e/performance/lighthouse-audit.spec.ts --grep "Authenticated Pages"
npm run test:e2e -- tests/e2e/performance/lighthouse-audit.spec.ts --grep "Admin Pages"

# Generate comprehensive report
npm run test:e2e -- tests/e2e/performance/lighthouse-audit.spec.ts --grep "Comprehensive Report"
```

**Report Location:** `test-results/lighthouse-audit-report.json`

### 1.3 Performance Monitoring

Monitor system performance in real-time:

```bash
# Show current performance metrics
php artisan performance:monitor

# Generate detailed performance report
php artisan performance:monitor --report

# Clear performance caches
php artisan performance:monitor --clear-cache

# Warm up critical caches
php artisan performance:monitor --warm-cache
```

**Report Location:** `storage/logs/performance-report-{timestamp}.txt`

---

## 2. Image Optimization

### 2.1 Image Lazy Loading

**Implementation:**

```blade
{{-- Priority image (above the fold) --}}
<img 
    src="{{ asset('images/hero.jpg') }}" 
    alt="ICTServe Hero Image"
    fetchpriority="high"
    width="1200"
    height="600"
>

{{-- Lazy loaded image (below the fold) --}}
<img 
    src="{{ asset('images/service-card.jpg') }}" 
    alt="Service Card"
    loading="lazy"
    decoding="async"
    width="400"
    height="300"
>
```

**Using PerformanceOptimizationService:**

```php
use App\Services\PerformanceOptimizationService;

$performanceService = app(PerformanceOptimizationService::class);

// Get image attributes with lazy loading
$attributes = $performanceService->getImageAttributes(
    src: asset('images/example.jpg'),
    alt: 'Example Image',
    priority: false // Set to true for above-the-fold images
);

// In Blade:
<img @foreach($attributes as $key => $value) {{ $key }}="{{ $value }}" @endforeach>
```

### 2.2 Responsive Images

**Implementation:**

```blade
<img 
    src="{{ asset('images/hero.jpg') }}" 
    srcset="
        {{ asset('images/hero-400.jpg') }} 400w,
        {{ asset('images/hero-800.jpg') }} 800w,
        {{ asset('images/hero-1200.jpg') }} 1200w
    "
    sizes="(max-width: 640px) 400px, (max-width: 1024px) 800px, 1200px"
    alt="Hero Image"
    loading="lazy"
>
```

### 2.3 Image Format Optimization

**Recommended Formats:**

- **WebP**: Primary format (90% smaller than JPEG)
- **JPEG**: Fallback for older browsers
- **SVG**: For icons and logos

**Implementation:**

```blade
<picture>
    <source srcset="{{ asset('images/hero.webp') }}" type="image/webp">
    <source srcset="{{ asset('images/hero.jpg') }}" type="image/jpeg">
    <img src="{{ asset('images/hero.jpg') }}" alt="Hero Image" loading="lazy">
</picture>
```

---

## 3. Livewire Component Optimization

### 3.1 OptimizedLivewireComponent Trait

**Usage:**

```php
<?php

namespace App\Livewire\Staff;

use App\Traits\OptimizedLivewireComponent;
use Livewire\Component;
use Livewire\Attributes\Computed;

class AuthenticatedDashboard extends Component
{
    use OptimizedLivewireComponent;

    #[Computed]
    public function statistics()
    {
        return $this->cacheData('dashboard_stats_' . auth()->id(), function () {
            return [
                'open_tickets' => $this->getOpenTicketsCount(),
                'pending_loans' => $this->getPendingLoansCount(),
                'overdue_items' => $this->getOverdueItemsCount(),
            ];
        }, minutes: 5);
    }

    #[Computed]
    public function recentTickets()
    {
        return $this->optimizeQuery(
            HelpdeskTicket::query()
                ->where('user_id', auth()->id())
                ->latest()
        )->limit(5)->get();
    }
}
```

### 3.2 Query Optimization

**Best Practices:**

```php
// ❌ BAD: N+1 Query Problem
$tickets = HelpdeskTicket::all();
foreach ($tickets as $ticket) {
    echo $ticket->user->name; // Triggers additional query
}

// ✅ GOOD: Eager Loading
$tickets = HelpdeskTicket::with('user')->get();
foreach ($tickets as $ticket) {
    echo $ticket->user->name; // No additional query
}

// ✅ BETTER: Optimized with Select
$tickets = HelpdeskTicket::query()
    ->select(['id', 'ticket_number', 'subject', 'status', 'user_id'])
    ->with('user:id,name,email')
    ->get();
```

### 3.3 Debounced Input Handling

**Implementation:**

```blade
{{-- Search input with 300ms debounce --}}
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="Search tickets..."
>

{{-- Large text field with lazy loading --}}
<textarea 
    wire:model.lazy="description"
    rows="5"
></textarea>
```

### 3.4 Lazy Loading Components

**Implementation:**

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class HeavyComponent extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.loading');
    }

    public function render()
    {
        // Heavy data loading here
        return view('livewire.heavy-component');
    }
}
```

---

## 4. Caching Strategies

### 4.1 Dashboard Statistics Caching

**Implementation:**

```php
use App\Services\PerformanceOptimizationService;

$performanceService = app(PerformanceOptimizationService::class);

// Cache dashboard statistics (5-minute TTL)
$stats = $performanceService->cacheDashboardStats(auth()->id(), function () {
    return [
        'open_tickets' => HelpdeskTicket::where('user_id', auth()->id())
            ->where('status', 'open')
            ->count(),
        'pending_loans' => LoanApplication::where('user_id', auth()->id())
            ->where('status', 'pending')
            ->count(),
    ];
});
```

### 4.2 Asset Availability Caching

**Implementation:**

```php
// Cache asset availability (5-minute TTL)
$availability = $performanceService->cacheAssetAvailability($assetId, function () use ($assetId) {
    return Asset::find($assetId)->getAvailabilityCalendar();
});
```

### 4.3 Cache Invalidation

**Implementation:**

```php
// Invalidate specific cache pattern
$performanceService->invalidateCache('dashboard_stats_*');

// Clear all performance caches
$performanceService->clearAllCaches();

// Warm up critical caches
$performanceService->warmUpCaches();
```

---

## 5. JavaScript Bundle Optimization

### 5.1 Code Splitting

**Vite Configuration:**

```javascript
// vite.config.js
import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/js/app.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                manualChunks: {
                    'vendor': ['alpinejs', 'axios'],
                    'livewire': ['@livewire/livewire'],
                },
            },
        },
        chunkSizeWarningLimit: 1000,
        minify: 'terser',
        terserOptions: {
            compress: {
                drop_console: true,
                drop_debugger: true,
            },
        },
    },
});
```

### 5.2 Asset Preloading

**Implementation:**

```blade
{{-- In layout head --}}
@php
$criticalAssets = [
    asset('build/assets/app.css') => 'css',
    asset('build/assets/app.js') => 'js',
    asset('fonts/inter.woff2') => 'font',
];
@endphp

{!! app(App\Services\PerformanceOptimizationService::class)->generatePreloadTags($criticalAssets) !!}
```

---

## 6. Database Optimization

### 6.1 Query Monitoring

**Enable Query Logging:**

```php
// In AppServiceProvider boot() method
if (app()->environment('local')) {
    DB::listen(function ($query) {
        if ($query->time > 1000) { // Log queries > 1 second
            Log::warning('Slow query detected', [
                'sql' => $query->sql,
                'bindings' => $query->bindings,
                'time' => $query->time . 'ms',
            ]);
        }
    });
}
```

### 6.2 Index Optimization

**Recommended Indexes:**

```php
// In migration
Schema::table('helpdesk_tickets', function (Blueprint $table) {
    $table->index('user_id');
    $table->index('status');
    $table->index(['user_id', 'status']); // Composite index
    $table->index('created_at');
});

Schema::table('loan_applications', function (Blueprint $table) {
    $table->index('user_id');
    $table->index('status');
    $table->index('approval_status');
    $table->index(['user_id', 'status']); // Composite index
});
```

### 6.3 Query Optimization Examples

```php
// ❌ BAD: Multiple queries
$openTickets = HelpdeskTicket::where('status', 'open')->count();
$closedTickets = HelpdeskTicket::where('status', 'closed')->count();
$pendingTickets = HelpdeskTicket::where('status', 'pending')->count();

// ✅ GOOD: Single query with grouping
$ticketCounts = HelpdeskTicket::query()
    ->select('status', DB::raw('count(*) as count'))
    ->groupBy('status')
    ->pluck('count', 'status');
```

---

## 7. Performance Checklist

### 7.1 Before Deployment

- [ ] Run Core Web Vitals tests (all pages pass)
- [ ] Run Lighthouse audits (meet score thresholds)
- [ ] Review slow query logs
- [ ] Verify cache configuration (Redis recommended)
- [ ] Test image lazy loading
- [ ] Verify responsive images
- [ ] Check JavaScript bundle size
- [ ] Test with throttled network (Slow 3G)
- [ ] Test on mobile devices
- [ ] Review database indexes

### 7.2 Post-Deployment Monitoring

- [ ] Monitor Core Web Vitals in production
- [ ] Track cache hit rates
- [ ] Monitor slow queries
- [ ] Review error logs
- [ ] Check memory usage
- [ ] Monitor API response times
- [ ] Track user-reported performance issues

---

## 8. Troubleshooting

### 8.1 High LCP (> 2.5s)

**Possible Causes:**

- Large images without optimization
- Render-blocking resources
- Slow server response time

**Solutions:**

- Implement image lazy loading
- Use WebP format with JPEG fallback
- Add explicit width/height to images
- Preload critical resources
- Optimize database queries

### 8.2 High FID (> 100ms)

**Possible Causes:**

- Heavy JavaScript execution
- Long tasks blocking main thread
- Unoptimized event handlers

**Solutions:**

- Implement code splitting
- Use debounced input handlers
- Defer non-critical JavaScript
- Optimize Livewire components

### 8.3 High CLS (> 0.1)

**Possible Causes:**

- Images without dimensions
- Dynamic content insertion
- Web fonts causing layout shift

**Solutions:**

- Add explicit width/height to all images
- Reserve space for dynamic content
- Use font-display: swap for web fonts
- Avoid inserting content above existing content

### 8.4 High TTFB (> 600ms)

**Possible Causes:**

- Slow database queries
- Inefficient caching
- Server resource constraints

**Solutions:**

- Implement Redis caching
- Optimize database queries
- Add database indexes
- Use CDN for static assets
- Increase server resources

---

## 9. Performance Metrics Dashboard

### 9.1 Real-Time Monitoring

**Tools:**

- Laravel Telescope (local development)
- New Relic / Datadog (production)
- Google Analytics (Core Web Vitals)
- Sentry (error tracking)

### 9.2 Key Metrics to Track

- **Core Web Vitals**: LCP, FID, CLS, TTFB
- **Lighthouse Scores**: Performance, Accessibility, Best Practices, SEO
- **Database**: Query count, slow queries, connection pool
- **Cache**: Hit rate, miss rate, eviction rate
- **Memory**: Usage, peak usage, limit
- **Response Times**: Average, P50, P95, P99

---

## 10. References

- **D07 System Integration Plan**: Performance testing requirements
- **D11 Technical Design**: Performance standards and infrastructure
- **D12 UI/UX Design Guide**: Accessibility and performance guidelines
- **Requirements**: 7.1, 7.2, 14.1, 15.4, 24.1

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-05  
**Author**: Performance Engineering Team  
**Status**: Ready for Implementation
