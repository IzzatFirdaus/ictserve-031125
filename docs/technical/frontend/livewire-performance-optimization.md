# Livewire Component Performance Optimization Guide

## Overview

This document provides comprehensive guidelines for optimizing Livewire component performance in the ICTServe application. Following these best practices ensures efficient database queries, proper caching strategies, optimized state management, and improved user experience.

**Requirements:** 6.1, 6.2, 7.1

## Table of Contents

1. [OptimizedLivewireComponent Trait](#optimizedlivewirecomponent-trait)
2. [Lazy Loading](#lazy-loading)
3. [Database Query Optimization](#database-query-optimization)
4. [Component Caching](#component-caching)
5. [State Management](#state-management)
6. [Performance Monitoring](#performance-monitoring)
7. [Best Practices](#best-practices)
8. [Examples](#examples)

## OptimizedLivewireComponent Trait

The `OptimizedLivewireComponent` trait provides performance optimization methods for Livewire components.

### Usage

```php
<?php

namespace App\Livewire\Helpdesk;

use App\Traits\OptimizedLivewireComponent;
use Livewire\Component;
use Livewire\WithPagination;

class TicketList extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    public function mount(): void
    {
        $this->bootOptimizedLivewireComponent();
    }

    public function render()
    {
        $startTime = microtime(true);

        // Your component logic here

        $this->trackRenderTime($startTime);

        return view('livewire.helpdesk.ticket-list');
    }
}
```

### Available Methods

#### `cacheData(string $key, callable $callback, int $duration = 300): mixed`

Cache component data with automatic invalidation.

```php
$tickets = $this->cacheData('tickets.page.' . $this->getPage(), function () {
    return HelpdeskTicket::query()
        ->with(['user', 'assignedTo'])
        ->paginate(15);
}, 300); // Cache for 5 minutes
```

#### `invalidateCache(?string $key = null): bool`

Invalidate component cache when data changes.

```php
public function updatedSearch(): void
{
    $this->resetPage();
    $this->invalidateCache(); // Clear all component caches
}
```

#### `optimizeQuery($query, array $eagerLoad = [], array $select = [])`

Optimize database queries with eager loading and column selection.

```php
$query = $this->optimizeQuery(
    HelpdeskTicket::query(),
    ['user', 'assignedTo', 'category'], // Eager load relationships
    ['id', 'ticket_number', 'subject', 'status', 'created_at'] // Select only needed columns
);
```

#### `getOptimizedPagination(int $totalRecords): array`

Get optimized pagination settings based on total records.

```php
$totalRecords = $query->count();
$paginationSettings = $this->getOptimizedPagination($totalRecords);
$tickets = $query->paginate($paginationSettings['perPage']);
```

#### `trackRenderTime(float $startTime): void`

Track component render time for performance monitoring.

```php
public function render()
{
    $startTime = microtime(true);

    // Component logic

    $this->trackRenderTime($startTime);

    return view('livewire.component');
}
```

## Lazy Loading

Lazy loading defers the loading of heavy components until they are needed, improving initial page load times.

### Using Livewire's Lazy Attribute

```php
use Livewire\Attributes\Lazy;

#[Lazy]
class HeavyDashboard extends Component
{
    public function placeholder()
    {
        return view('livewire.placeholders.loading');
    }

    public function render()
    {
        // Heavy data loading
        return view('livewire.heavy-dashboard');
    }
}
```

### Blade Template

```blade
<livewire:heavy-dashboard lazy />
```

### Lazy Loading Data in Chunks

```php
public function loadMoreData(): void
{
    $this->lazyLoadData(function ($offset, $chunkSize) {
        return HelpdeskTicket::query()
            ->offset($offset)
            ->limit($chunkSize)
            ->get();
    }, 50);
}
```

## Database Query Optimization

### Eager Loading Relationships

Always eager load relationships to prevent N+1 query problems.

**Bad:**
```php
$tickets = HelpdeskTicket::all();
foreach ($tickets as $ticket) {
    echo $ticket->user->name; // N+1 query problem
}
```

**Good:**
```php
$tickets = HelpdeskTicket::with('user')->get();
foreach ($tickets as $ticket) {
    echo $ticket->user->name; // Single query
}
```

### Select Only Needed Columns

Reduce memory usage and query time by selecting only required columns.

**Bad:**
```php
$tickets = HelpdeskTicket::all(); // Selects all columns
```

**Good:**
```php
$tickets = HelpdeskTicket::select([
    'id',
    'ticket_number',
    'subject',
    'status',
    'created_at'
])->get();
```

### Using the Optimization Trait

```php
$query = $this->optimizeQuery(
    HelpdeskTicket::query(),
    ['user', 'assignedTo', 'category'], // Eager load
    ['id', 'ticket_number', 'subject', 'status', 'created_at'] // Select columns
);
```

### Chunking Large Datasets

For processing large datasets, use chunking to reduce memory usage.

```php
HelpdeskTicket::chunk(100, function ($tickets) {
    foreach ($tickets as $ticket) {
        // Process ticket
    }
});
```

## Component Caching

### Cache Query Results

Cache expensive database queries to reduce load times.

```php
public function render()
{
    $cacheKey = implode('.', [
        $this->search,
        $this->status,
        $this->getPage(),
    ]);

    $tickets = $this->cacheData($cacheKey, function () {
        return HelpdeskTicket::query()
            ->with(['user', 'assignedTo'])
            ->where('status', $this->status)
            ->paginate(15);
    }, 300); // Cache for 5 minutes

    return view('livewire.ticket-list', compact('tickets'));
}
```

### Invalidate Cache on Updates

Always invalidate cache when data changes.

```php
public function updatedSearch(): void
{
    $this->resetPage();
    $this->invalidateCache(); // Clear cache
}

public function createTicket(): void
{
    // Create ticket logic
    $this->invalidateCache(); // Clear cache after creating
}
```

### Cache Dashboard Statistics

```php
public function getDashboardStats(): array
{
    return $this->cacheData('dashboard_stats', function () {
        return [
            'total_tickets' => HelpdeskTicket::count(),
            'open_tickets' => HelpdeskTicket::where('status', 'open')->count(),
            'resolved_tickets' => HelpdeskTicket::where('status', 'resolved')->count(),
        ];
    }, 600); // Cache for 10 minutes
}
```

## State Management

### Use Computed Properties

Computed properties are cached automatically by Livewire.

```php
use Livewire\Attributes\Computed;

#[Computed]
public function tickets()
{
    return HelpdeskTicket::query()
        ->with(['user', 'assignedTo'])
        ->paginate(15);
}
```

### Debounce User Input

Debounce search inputs to reduce server requests.

```blade
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="Search tickets..."
>
```

### Use wire:model.lazy for Non-Critical Updates

For non-critical updates, use `wire:model.lazy` instead of `wire:model.live`.

```blade
<select wire:model.lazy="status">
    <option value="">All Statuses</option>
    <option value="open">Open</option>
    <option value="closed">Closed</option>
</select>
```

### Batch Property Updates

Update multiple properties at once to reduce renders.

```php
public function applyFilters(array $filters): void
{
    $this->batchUpdate([
        'status' => $filters['status'] ?? '',
        'priority' => $filters['priority'] ?? '',
        'search' => $filters['search'] ?? '',
    ]);
}
```

## Performance Monitoring

### Track Render Times

Monitor component render times to identify performance bottlenecks.

```php
public function render()
{
    $startTime = microtime(true);

    // Component logic

    $this->trackRenderTime($startTime);

    return view('livewire.component');
}
```

### Get Performance Metrics

Retrieve performance metrics for analysis.

```php
public function getMetrics(): array
{
    return $this->getPerformanceMetrics();
}
```

### Performance Alerts

Set up alerts for slow components.

```php
public function render()
{
    $startTime = microtime(true);

    // Component logic

    $renderTime = (microtime(true) - $startTime) * 1000;

    if ($renderTime > 500) {
        Log::warning('Slow component render', [
            'component' => class_basename($this),
            'render_time' => $renderTime,
        ]);
    }

    $this->trackRenderTime($startTime);

    return view('livewire.component');
}
```

## Best Practices

### 1. Always Use Eager Loading

```php
// Bad
$tickets = HelpdeskTicket::all();

// Good
$tickets = HelpdeskTicket::with(['user', 'assignedTo', 'category'])->get();
```

### 2. Select Only Needed Columns

```php
// Bad
$tickets = HelpdeskTicket::all();

// Good
$tickets = HelpdeskTicket::select(['id', 'ticket_number', 'subject', 'status'])->get();
```

### 3. Cache Expensive Queries

```php
$tickets = $this->cacheData('tickets', function () {
    return HelpdeskTicket::with('user')->get();
}, 300);
```

### 4. Invalidate Cache on Updates

```php
public function updateTicket(): void
{
    // Update logic
    $this->invalidateCache();
}
```

### 5. Use Lazy Loading for Heavy Components

```php
#[Lazy]
class HeavyComponent extends Component
{
    // Component logic
}
```

### 6. Debounce User Input

```blade
<input wire:model.live.debounce.300ms="search">
```

### 7. Use Computed Properties

```php
#[Computed]
public function tickets()
{
    return HelpdeskTicket::query()->paginate(15);
}
```

### 8. Track Performance Metrics

```php
public function render()
{
    $startTime = microtime(true);
    // Logic
    $this->trackRenderTime($startTime);
    return view('livewire.component');
}
```

### 9. Optimize Pagination

```php
$totalRecords = $query->count();
$paginationSettings = $this->getOptimizedPagination($totalRecords);
$tickets = $query->paginate($paginationSettings['perPage']);
```

### 10. Use Query String for Filters

```php
protected $queryString = [
    'search' => ['except' => ''],
    'status' => ['except' => 'all'],
];
```

## Examples

### Optimized Ticket List Component

```php
<?php

namespace App\Livewire\Helpdesk;

use App\Models\HelpdeskTicket;
use App\Traits\OptimizedLivewireComponent;
use Livewire\Component;
use Livewire\WithPagination;

class TicketList extends Component
{
    use OptimizedLivewireComponent;
    use WithPagination;

    public string $search = '';
    public string $status = '';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->bootOptimizedLivewireComponent();
    }

    public function render()
    {
        $startTime = microtime(true);

        $cacheKey = implode('.', [
            $this->search,
            $this->status,
            $this->sortField,
            $this->sortDirection,
            $this->getPage(),
        ]);

        $tickets = $this->cacheData($cacheKey, function () {
            $query = HelpdeskTicket::query();

            $query = $this->optimizeQuery(
                $query,
                ['user', 'assignedTo', 'category'],
                ['id', 'ticket_number', 'subject', 'status', 'priority', 'created_at']
            );

            if ($this->search) {
                $query->where('ticket_number', 'like', "%{$this->search}%")
                    ->orWhere('subject', 'like', "%{$this->search}%");
            }

            if ($this->status) {
                $query->where('status', $this->status);
            }

            $query->orderBy($this->sortField, $this->sortDirection);

            $totalRecords = $query->count();
            $paginationSettings = $this->getOptimizedPagination($totalRecords);

            return $query->paginate($paginationSettings['perPage']);
        }, 300);

        $this->trackRenderTime($startTime);

        return view('livewire.helpdesk.ticket-list', compact('tickets'));
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
        $this->invalidateCache();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
        $this->invalidateCache();
    }
}
```

### Optimized Dashboard Component

```php
<?php

namespace App\Livewire\Helpdesk;

use App\Services\Helpdesk\HelpdeskAnalyticsService;
use App\Traits\OptimizedLivewireComponent;
use Carbon\Carbon;
use Livewire\Component;

class HelpdeskDashboard extends Component
{
    use OptimizedLivewireComponent;

    public ?string $startDate = null;
    public ?string $endDate = null;
    public string $period = '30';

    public function mount(): void
    {
        $this->bootOptimizedLivewireComponent();
        $this->setDefaultDateRange();
    }

    public function render()
    {
        $startTime = microtime(true);

        $cacheKey = implode('.', [
            $this->startDate ?? 'null',
            $this->endDate ?? 'null',
            $this->period,
        ]);

        $dashboardData = $this->cacheData($cacheKey, function () {
            $analyticsService = app(HelpdeskAnalyticsService::class);

            $startDate = $this->startDate ? Carbon::parse($this->startDate) : null;
            $endDate = $this->endDate ? Carbon::parse($this->endDate) : null;

            return $analyticsService->getDashboardData($startDate, $endDate);
        }, 600); // Cache for 10 minutes

        $this->trackRenderTime($startTime);

        return view('livewire.helpdesk.helpdesk-dashboard', compact('dashboardData'));
    }

    public function updatedPeriod(): void
    {
        $this->setDefaultDateRange();
        $this->invalidateCache();
    }

    private function setDefaultDateRange(): void
    {
        $days = (int) $this->period;
        $this->endDate = now()->format('Y-m-d');
        $this->startDate = now()->subDays($days)->format('Y-m-d');
    }
}
```

## Performance Checklist

- [ ] Use `OptimizedLivewireComponent` trait
- [ ] Implement lazy loading for heavy components
- [ ] Eager load all relationships
- [ ] Select only needed columns
- [ ] Cache expensive queries
- [ ] Invalidate cache on updates
- [ ] Debounce user input
- [ ] Use computed properties
- [ ] Track render times
- [ ] Optimize pagination settings
- [ ] Use query string for filters
- [ ] Implement performance monitoring

## Troubleshooting

### Slow Component Renders

1. Check for N+1 query problems
2. Verify eager loading is implemented
3. Review cache strategy
4. Check render time metrics
5. Optimize database queries

### Cache Issues

1. Verify cache keys are unique
2. Check cache invalidation logic
3. Review cache duration
4. Monitor cache hit rates

### Memory Issues

1. Use chunking for large datasets
2. Select only needed columns
3. Implement pagination
4. Clear unused data

## Additional Resources

- [Livewire Documentation](https://livewire.laravel.com)
- [Laravel Query Optimization](https://laravel.com/docs/queries)
- [Laravel Caching](https://laravel.com/docs/cache)
- [Performance Monitoring Guide](./performance-optimization.md)

## Conclusion

Following these optimization guidelines ensures that Livewire components in the ICTServe application perform efficiently, provide excellent user experience, and scale well with increasing data volumes. Regular performance monitoring and adherence to best practices are essential for maintaining optimal performance.

