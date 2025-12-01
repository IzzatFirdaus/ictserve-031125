---
applyTo: "app/**,routes/**,database/**,resources/views/**,config/**"
description: "Performance optimization standards: N+1 prevention, caching strategies, queue usage, asset bundling, and monitoring for ICTServe."
---

# Performance Optimization Instructions

## Purpose
Defines mandatory performance standards and optimization techniques for ICTServe. The goal is to ensure the application meets the defined performance budget (TTFB < 600ms, LCP < 2.5s).

## Scope
Applies to database queries, PHP application logic, frontend assets (Vite/Livewire), and server configuration.

## 1. Database Optimization

### Prevent N+1 Queries (Mandatory)
**Rule**: Never query relationships inside a loop. Always use Eager Loading.

```php
// ❌ BAD: N+1 Query
$assets = Asset::all();
foreach ($assets as $asset) {
    echo $asset->category->name; // Executes 1 query per asset
}

// ✅ GOOD: Eager Loading
$assets = Asset::with('category')->get();

// ✅ BETTER: Constrained Eager Loading (Load only what you need)
$assets = Asset::with(['category:id,name'])->get();
````

### Indexing

**Rule**: All columns used in `WHERE`, `ORDER BY`, or `JOIN` clauses on tables with \>1000 rows must be indexed.

```php
Schema::create('assets', function (Blueprint $table) {
    // ...
    $table->string('status');
    $table->foreignId('category_id')->constrained();
    
    // Composite index for common filtering combo
    $table->index(['status', 'category_id']);
});
```

### Chunking & Lazy Loading

**Rule**: Use `chunk()` or `lazy()` for processing large datasets to conserve memory.

```php
// ❌ BAD: Loads 10k records into RAM
$users = User::all();

// ✅ GOOD: Process 100 at a time
User::chunk(100, function ($users) {
    foreach ($users as $user) {
        // Process...
    }
});
```

## 2\. Caching Strategies

### Data Caching

Cache expensive queries or computations.

```php
use Illuminate\Support\Facades\Cache;

// Cache settings for 24 hours
$settings = Cache::remember('app_settings', 86400, function () {
    return Setting::all()->pluck('value', 'key');
});
```

### Configuration Caching (Production)

These commands **MUST** run during the deployment pipeline:

```bash
php artisan config:cache
php artisan event:cache
php artisan route:cache
php artisan view:cache
```

## 3\. Frontend Optimization

### Vite & Assets

  - **Minification**: Ensure `npm run build` is used for production.
  - **Deferral**: Scripts should use `defer` (default in Vite).
  - **Images**: Use WebP format and lazy loading.

<!-- end list -->

```html
<img src="large-image.webp" loading="lazy" alt="Description" width="800" height="600">
```

### Livewire Performance

  - **Debounce**: Use `.debounce.300ms` or `.blur` for inputs to reduce server roundtrips.
  - **Isolation**: Use `wire:ignore` for third-party libraries (Charts, Maps) to prevent re-rendering.
  - **Computed**: Use `#[Computed(persist: true)]` to cache expensive derived state.

<!-- end list -->

```php
use Livewire\Attributes\Computed;

#[Computed(persist: true, seconds: 60)]
public function heavyData()
{
    return ExpensiveService::calculate();
}
```

## 4\. Asynchronous Processing

### Queues

**Rule**: Any task taking \>500ms (Emails, PDF generation, API calls) **MUST** be queued.

```php
// Dispatch to specific queue
ProcessReport::dispatch($data)->onQueue('reports');
```

### Queue Configuration

  - Use **Redis** driver in production.
  - Configure `retry_after` to be longer than the job's expected execution time.

## 5\. Server-Side Optimization (PHP)

### Opcache (Production `php.ini`)

Ensure these settings are enabled in the production environment:

```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

## 6\. Performance Budget & Monitoring

### Thresholds

| Metric | Target |
| :--- | :--- |
| **TTFB** (Time to First Byte) | \< 600ms |
| **LCP** (Largest Contentful Paint) | \< 2.5s |
| **CLS** (Cumulative Layout Shift) | \< 0.1 |
| **DB Queries** | \< 20 per request |

### Tooling

  - **Development**: Use **Laravel Debugbar** to monitor query counts and memory usage.
  - **Production**: Monitor logs for `QueryTime > 1s`.

## 7\. Pre-Commit Checklist

  - [ ] Checked for N+1 queries using Debugbar.
  - [ ] Added indexes for new foreign keys or filter columns.
  - [ ] Used `chunk()` for any bulk data operations.
  - [ ] Queued all email/notification logic.
  - [ ] Validated Lighthouse score is \>90.
