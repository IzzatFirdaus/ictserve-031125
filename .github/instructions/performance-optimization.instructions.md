---
applyTo: "app/**,routes/**,database/**,resources/views/**"
description: "Performance optimization strategies, query optimization, N+1 prevention, caching, asset bundling, and monitoring for ICTServe"
---

# Performance Optimization — ICTServe Standards

## Purpose & Scope

Performance optimization best practices for ICTServe. Covers database query optimization, caching strategies, N+1 query prevention, asset optimization, and monitoring.

**Traceability**: D11 (Performance Requirements)

---

## Database Query Optimization

### Prevent N+1 Queries (CRITICAL)

```php
// ❌ BAD: N+1 problem (1 query + N queries for categories)
$assets = Asset::all();
foreach ($assets as $asset) 
    echo $asset->category->name; // Fires query for each asset


// ✅ GOOD: Eager loading (2 queries total)
$assets = Asset::with('category')->get();
foreach ($assets as $asset) 
    echo $asset->category->name;


// ✅ BETTER: Constrained eager loading (Laravel 11+)
$assets = Asset::with([
    'borrowings' => fn($q) => $q->latest()->limit(5)
])->get();
```

**Check for N+1 Problems** (use Laravel Debugbar):
```bash
composer require barryvdh/laravel-debugbar --dev
```

---

### Use Indexes

```php
// Migration
Schema::create('assets', function (Blueprint $table) 
    $table->id();
    $table->string('asset_tag')->unique(); // Unique index
    $table->foreignId('category_id')->constrained(); // Foreign key index
    $table->string('status');
    
    $table->index('status'); // Single column index
    $table->index(['status', 'category_id']); // Composite index
);
```

**When to Index**:
- Foreign keys
- Columns in `WHERE` clauses
- Columns in `ORDER BY`
- Columns in `JOIN` conditions

---

### Select Only Required Columns

```php
// ❌ BAD: Select all columns
$assets = Asset::all();

// ✅ GOOD: Select specific columns
$assets = Asset::select('id', 'name', 'asset_tag')->get();
```

---

### Use Chunking for Large Datasets

```php
// ❌ BAD: Load all records into memory
Asset::all()->each(function ($asset) 
    $this->process($asset);
);

// ✅ GOOD: Process in chunks
Asset::chunk(100, function ($assets) 
    foreach ($assets as $asset) 
        $this->process($asset);

);

// ✅ BETTER: Lazy collection (Laravel 11+)
Asset::lazy()->each(function ($asset) 
    $this->process($asset);
);
```

---

## Caching Strategies

### Cache Query Results

```php
use Illuminate\Support\Facades\Cache;

// Cache for 1 hour
$categories = Cache::remember('categories', 3600, function () 
    return Category::all();
);

// Cache forever (until manually cleared)
$settings = Cache::rememberForever('settings', function () 
    return Setting::pluck('value', 'key');
);

// Clear cache
Cache::forget('categories');
Cache::flush(); // Clear all cache
```

---

### Cache Database Queries (Model)

```php
class Asset extends Model

    public static function available()
    
        return Cache::remember('assets.available', 600, function () 
            return static::where('status', 'available')->get();
    );


```

---

### Cache Configuration

```php
// Cache config in production
php artisan config:cache
php artisan route:cache
php artisan view:cache

// Clear caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

---

## Asset Optimization

### Vite Build Optimization

```javascript
// vite.config.js
export default defineConfig(
    build: 
        rollupOptions: 
            output: 
                manualChunks: 
                    vendor: ['alpinejs', 'axios'],
            ,
        ,
    ,
,
);
```

**Build for Production**:
```bash
npm run build
```

---

### Image Optimization

```php
use Intervention\Image\Facades\Image;

$image = Image::make($uploadedFile)
    ->resize(800, null, function ($constraint) 
        $constraint->aspectRatio();
        $constraint->upsize();
)
    ->save(storage_path('app/public/assets/optimized.jpg'), 85);
```

---

### Lazy Loading Images

```blade
<img 
    src=" asset('placeholder.jpg') " 
    data-src=" asset($asset->image) " 
    loading="lazy"
    class="lazyload"
>
```

---

## Application Performance

### Use Queues for Slow Operations

```php
// app/Jobs/SendAssetBorrowedNotification.php
class SendAssetBorrowedNotification implements ShouldQueue

    use Dispatchable, InteractsWithQueue, Queueable;
    
    public function handle(): void
    
        Mail::to($this->user)->send(new AssetBorrowedMail($this->asset));



// Dispatch
SendAssetBorrowedNotification::dispatch($asset, $user);
```

**Run Queue Worker**:
```bash
php artisan queue:work --tries=3
```

---

### Optimize Autoloader

```bash
composer dump-autoload --optimize
```

---

### Enable OPcache (Production)

**`php.ini`**:
```ini
opcache.enable=1
opcache.memory_consumption=256
opcache.max_accelerated_files=20000
opcache.validate_timestamps=0
```

---

## Monitoring & Profiling

### Laravel Telescope (Development)

```bash
composer require laravel/telescope --dev
php artisan telescope:install
php artisan migrate
```

**Access**: `http://localhost/telescope`

---

### Query Logging (Debug)

```php
use Illuminate\Support\Facades\DB;

DB::enableQueryLog();

// Execute queries
Asset::where('status', 'available')->get();

// Get queries
dd(DB::getQueryLog());
```

---

### Performance Metrics

```php
// Measure execution time
$start = microtime(true);

// Code to measure
$assets = Asset::with('category')->get();

$duration = microtime(true) - $start;
Log::info("Query took $duration seconds");
```

---

## Best Practices Checklist

- [ ] Eager load relationships (prevent N+1)
- [ ] Add indexes to frequently queried columns
- [ ] Select only required columns
- [ ] Use chunking for large datasets
- [ ] Cache expensive queries
- [ ] Queue slow operations (emails, file processing)
- [ ] Optimize images before upload
- [ ] Build assets for production (`npm run build`)
- [ ] Enable OPcache in production
- [ ] Cache config/routes/views in production
- [ ] Monitor query performance (Telescope)

---

## Performance Budget

**ICTServe Targets**:
- **Page Load Time**: < 2 seconds (Lighthouse)
- **Time to First Byte (TTFB)**: < 600ms
- **Database Queries per Page**: < 20
- **N+1 Queries**: 0 (zero tolerance)
- **Lighthouse Score**: > 90

---

## References

- **Laravel Performance**: https://laravel.com/docs/12.x/optimization
- **Lighthouse**: https://developers.google.com/web/tools/lighthouse
- **ICTServe**: D11 (Performance Requirements)

---

**Status**: ✅ Production-ready  
**Last Updated**: 2025-11-01
