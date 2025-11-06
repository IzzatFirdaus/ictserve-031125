# Livewire Component Optimization Guide

## Overview

This document provides comprehensive guidelines for optimizing Livewire components in the ICTServe application to ensure fast, responsive user interfaces with minimal server load.

**Requirements:** 2.1, 2.2, 9.3, 10.2

## Table of Contents

1. [OptimizedLivewireComponent Trait](#optimizedlivewirecomponent-trait)
2. [Caching Strategies](#caching-strategies)
3. [Query Optimization](#query-optimization)
4. [State Management](#state-management)
5. [Debouncing and Throttling](#debouncing-and-throttling)
6. [Lazy Loading](#lazy-loading)
7. [Performance Monitoring](#performance-monitoring)
8. [Best Practices](#best-practices)

## OptimizedLivewireComponent Trait

The `OptimizedLivewireComponent` trait provides performance optimization methods for Livewire components.

### Usage

```php
use App\Traits\OptimizedLivewireComponent;
use Livewire\Component;

class MyComponent extends Component
{
    use OptimizedLivewireComponent;
    
    public function mount()
    {
        // Initialize optimizer
        $this->bootOptimizedLivewireComponent();
    }
}
```

### Available Methods

- `cacheData($key, $callback, $duration)` - Cache component data
- `invalidateCache($key)` - Invalidate component cache
- `optimizeQuery($query, $eagerLoad, $select)` - Optimize database queries
- `getOptimizedPagination($totalRecords)` - Get optimized pagination settings
- `trackRenderTime($startTime)` - Track component render time
- `getPerformanceMetrics()` - Get component performance metrics
- `lazyLoadData($callback, $chunkSize)` - Lazy load data in chunks
- `cacheQuery($key, $query, $duration)` - Cache query results
- `memoize($key, $callback)` - Memoize expensive computations

## Caching Strategies

### Component Data Caching

Cache frequently accessed data to reduce database queries:

```php
public function getTicketsProperty()
{
    return $this->cacheData(
        'tickets.' . $this->status,
        fn() => Ticket::where('status', $this->status)->get(),
        300 // 5 minutes
    );
}
```

### Query Result Caching

Cache query results for better performance:

```php
public function getStatisticsProperty()
{
    return $this->cacheQuery(
        'statistics',
        Ticket::selectRaw('status, COUNT(*) as count')->groupBy('status'),
        600 // 10 minutes
    );
}
```

### Cache Invalidation

Invalidate cache when data changes:

```php
public function updateTicket()
{
    // Update ticket
    $this->ticket->update($this->form);
    
    // Invalidate cache
    $this->invalidateCache('tickets.' . $this->status);
}
```

## Query Optimization

### Eager Loading

Prevent N+1 queries by eager loading relationships:

```php
public function getTicketsProperty()
{
    $query = Ticket::query();
    
    // Optimize with eager loading
    $query = $this->optimizeQuery(
        $query,
        ['user', 'assignedTo', 'category'], // Relationships to eager load
        ['id', 'ticket_number', 'subject', 'status'] // Columns to select
    );
    
    return $query->get();
}
```

### Column Selection

Select only needed columns to reduce data transfer:

```php
// Bad - Selects all columns
$tickets = Ticket::all();

// Good - Selects only needed columns
$tickets = Ticket::select(['id', 'ticket_number', 'subject', 'status'])->get();
```

### Pagination

Use optimized pagination settings:

```php
public function getTicketsProperty()
{
    $query = Ticket::query();
    $totalRecords = $query->count();
    
    // Get optimized pagination settings
    $settings = $this->getOptimizedPagination($totalRecords);
    
    return $query->paginate($settings['perPage']);
}
```

## State Management

### Optimize Component State

Remove null values and optimize serialization:

```php
public function mount()
{
    // Initialize state
    $this->form = [
        'title' => '',
        'description' => '',
        'status' => 'open',
    ];
    
    // Optimize state
    $this->optimizeState();
}
```

### Batch Updates

Update multiple properties in a single render:

```php
public function updateMultiple()
{
    $this->batchUpdate([
        'status' => 'closed',
        'resolved_at' => now(),
        'resolved_by' => auth()->id(),
    ]);
}
```

### Memoization

Memoize expensive computations:

```php
public function getStatisticsProperty()
{
    return $this->memoize('statistics', function() {
        // Expensive computation
        return [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];
    });
}
```

## Debouncing and Throttling

### Debounce Search Input

Prevent excessive server requests:

```blade
{{-- Debounce search input (300ms) --}}
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="Search..."
>
```

### Lazy Model Binding

Use lazy binding for non-critical updates:

```blade
{{-- Lazy binding - updates on blur --}}
<input 
    type="text" 
    wire:model.lazy="description"
>

{{-- Live binding with debounce --}}
<input 
    type="text" 
    wire:model.live.debounce.500ms="search"
>
```

### Throttle Button Clicks

Prevent multiple rapid clicks:

```blade
<button 
    wire:click="submit"
    wire:loading.attr="disabled"
    wire:loading.class="opacity-50 cursor-not-allowed"
>
    <span wire:loading.remove>Submit</span>
    <span wire:loading>Submitting...</span>
</button>
```

## Lazy Loading

### Lazy Load Components

Load heavy components on demand:

```blade
<x-lazy-load>
    @livewire('heavy-component')
</x-lazy-load>
```

### Defer Loading

Defer loading of heavy data:

```php
public function mount()
{
    // Defer loading of heavy data
    $this->deferLoading('statistics', function() {
        return $this->calculateStatistics();
    });
}
```

### Chunked Loading

Load data in chunks for better performance:

```php
public function loadTickets()
{
    $generator = $this->lazyLoadData(
        fn($offset, $limit) => Ticket::skip($offset)->take($limit)->get(),
        50 // Chunk size
    );
    
    foreach ($generator as $chunk) {
        // Process chunk
        $this->tickets = array_merge($this->tickets, $chunk->toArray());
    }
}
```

## Performance Monitoring

### Track Render Time

Monitor component render performance:

```php
public function render()
{
    $startTime = microtime(true);
    
    // Component logic
    $data = $this->getData();
    
    // Track render time
    $this->trackRenderTime($startTime);
    
    return view('livewire.my-component', compact('data'));
}
```

### Get Performance Metrics

View component performance metrics:

```php
public function getMetrics()
{
    $metrics = $this->getPerformanceMetrics();
    
    // Returns:
    // [
    //     'render_count' => 10,
    //     'average_render_time' => 150.5,
    //     'cache_hit_rate' => 0.75,
    //     'last_rendered' => Carbon instance,
    // ]
}
```

## Best Practices

### 1. Use wire:model.lazy for Non-Critical Updates

```blade
{{-- Good for non-critical fields --}}
<input type="text" wire:model.lazy="description">

{{-- Good for search with debounce --}}
<input type="text" wire:model.live.debounce.300ms="search">
```

### 2. Implement Caching for Frequently Accessed Data

```php
public function getTicketsProperty()
{
    return $this->cacheData(
        'tickets',
        fn() => Ticket::with('user')->get(),
        300 // 5 minutes
    );
}
```

### 3. Eager Load Relationships

```php
// Bad - N+1 queries
$tickets = Ticket::all();
foreach ($tickets as $ticket) {
    echo $ticket->user->name; // Separate query for each ticket
}

// Good - Single query with eager loading
$tickets = Ticket::with('user')->get();
foreach ($tickets as $ticket) {
    echo $ticket->user->name; // No additional queries
}
```

### 4. Select Only Needed Columns

```php
// Bad - Selects all columns
$tickets = Ticket::all();

// Good - Selects only needed columns
$tickets = Ticket::select(['id', 'ticket_number', 'subject'])->get();
```

### 5. Use Pagination for Large Datasets

```php
// Bad - Loads all records
$tickets = Ticket::all();

// Good - Paginated results
$tickets = Ticket::paginate(15);
```

### 6. Implement Loading States

```blade
<div wire:loading class="loading-overlay">
    <div class="spinner"></div>
    <span>Loading...</span>
</div>

<div wire:loading.remove>
    {{-- Content --}}
</div>
```

### 7. Use wire:key in Loops

```blade
@foreach($tickets as $ticket)
    <div wire:key="ticket-{{ $ticket->id }}">
        {{-- Ticket content --}}
    </div>
@endforeach
```

### 8. Avoid Unnecessary Re-renders

```php
// Use computed properties for derived data
public function getFilteredTicketsProperty()
{
    return $this->tickets->filter(fn($ticket) => $ticket->status === $this->status);
}

// Access in template
{{ $this->filteredTickets->count() }}
```

### 9. Implement Debouncing for Search

```blade
<input 
    type="text" 
    wire:model.live.debounce.300ms="search"
    placeholder="Search tickets..."
>
```

### 10. Cache Query Results

```php
public function getStatisticsProperty()
{
    return Cache::remember('ticket-statistics', 600, function() {
        return [
            'total' => Ticket::count(),
            'open' => Ticket::where('status', 'open')->count(),
            'closed' => Ticket::where('status', 'closed')->count(),
        ];
    });
}
```

## Example: Fully Optimized Component

See `app/Livewire/OptimizedTicketList.php` for a complete example of an optimized Livewire component implementing all best practices.

## Troubleshooting

### Slow Component Renders

1. Check render time: `$this->trackRenderTime($startTime)`
2. Review database queries: Enable query logging
3. Implement caching for frequently accessed data
4. Optimize queries with eager loading

### High Memory Usage

1. Reduce cache duration for frequently changing data
2. Implement pagination for large datasets
3. Select only needed columns in queries
4. Clear component cache: `$this->invalidateCache()`

### Excessive Server Requests

1. Implement debouncing for search inputs (300ms)
2. Use wire:model.lazy instead of wire:model.live
3. Batch updates when possible
4. Implement client-side filtering for small datasets

## Resources

- [Livewire Performance Documentation](https://livewire.laravel.com/docs/performance)
- [Laravel Query Optimization](https://laravel.com/docs/queries)
- [Laravel Caching](https://laravel.com/docs/cache)

## Conclusion

Following these optimization strategies ensures Livewire components deliver excellent performance with fast response times and minimal server load.

For questions or issues, contact the development team.
