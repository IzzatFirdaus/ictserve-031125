---
applyTo: "app/**,routes/**,database/**,config/**,bootstrap/**"
description: "Laravel 12 conventions, Artisan patterns, Model/Controller/Migration standards, and Service Container best practices for ICTServe"
---

# Laravel 12 — ICTServe Development Standards

## Purpose & Scope

Provides Laravel 12 specific conventions for ICTServe enterprise application. Covers Artisan workflows, Eloquent patterns, Service Container usage, middleware, routing, and testing standards aligned with D00–D15 documentation suite.

**Applies To**: Application layer (`app/`), routing (`routes/`), database (`database/`), configuration (`config/`), bootstrap (`bootstrap/`)

**Traceability**: D03 (Software Requirements), D04 (Software Design), D10 (Source Code Documentation)

---

## Core Principles

1. **Laravel Way First**: Use framework conventions before creating custom solutions
2. **Artisan-Driven Development**: Generate files via `php artisan make:*` commands
3. **Service Container**: Leverage dependency injection over facade static calls where possible
4. **Eloquent ORM**: Prefer relationships and query builder over raw SQL
5. **PSR-12 Compliance**: Follow PHP-FIG standards enforced by Laravel Pint

---

## Laravel 12 Streamlined Structure

**Critical Changes from Laravel 10**:
- ✅ **No `app/Http/Kernel.php`** — Use `bootstrap/app.php` for middleware/exception registration
- ✅ **No `app/Console/Kernel.php`** — Commands auto-register from `app/Console/Commands/`
- ✅ **No middleware directory** — Define middleware inline in `bootstrap/app.php`
- ✅ **Service providers** → Stored in `bootstrap/providers.php` (auto-discovery)

**Example: Middleware Registration**
```php
// bootstrap/app.php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
    )
    ->withMiddleware(function (Middleware $middleware)
        $middleware->web(append: [
            \App\Http\Middleware\EnsureAccessibility::class,
      );
)
    ->withExceptions(function (Exceptions $exceptions)
        $exceptions->reportable(function (Throwable $e)
            // Custom error handling
    );
)->create();
```

---

## Artisan Command Patterns

**Always use `--no-interaction`** for CI/CD automation:

```bash
# Model with factory, seeder, migration
php artisan make:model Asset --all --no-interaction

# Controller with resource methods
php artisan make:controller AssetController --resource --no-interaction

# Form Request for validation
php artisan make:request StoreAssetRequest --no-interaction

# Policy for authorization
php artisan make:policy AssetPolicy --model=Asset --no-interaction

# Migration with specific table action
php artisan make:migration add_status_to_assets_table --no-interaction

# Job with queue
php artisan make:job ProcessAssetBorrowing --queued --no-interaction
```

**List available commands**:
```bash
php artisan list              # All commands
php artisan make:model --help # Specific command options
```

---

## Eloquent Model Standards

**Required Traits & Conventions**:
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Auditable;
use OwenIt\Auditing\Contracts\Auditable as AuditableContract;

class Asset extends Model implements AuditableContract

    use HasFactory, SoftDeletes, Auditable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'asset_tag',
        'category_id',
        'status',
        'acquired_date',
  ;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array

        return [
            'acquired_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
      ;


    /**
     * Get the category that owns the asset.
     */
    public function category(): BelongsTo

        return $this->belongsTo(Category::class);


    /**
     * Get the borrowing records for the asset.
     */
    public function borrowings(): HasMany

        return $this->hasMany(Borrowing::class);


```

**Key Requirements**:
- ✅ `declare(strict_types=1)` at file start
- ✅ Use `protected function casts(): array` (NOT `protected $casts` property)
- ✅ Explicit return type hints for relationships
- ✅ PHPDoc blocks for array shapes and method descriptions
- ✅ `SoftDeletes` trait for auditable entities (per D09 Database Documentation)
- ✅ `Auditable` trait + interface for compliance tracking (PDPA 2010)

---

## Database Migrations

**Migration Rules**:
1. **Always include rollback logic** in `down()` method
2. **When modifying columns**, include ALL previous attributes (or they'll be lost)
3. **Use Laravel's column modifiers** (nullable, default, index, unique)
4. **Never use raw SQL** unless absolutely necessary

**Example: Creating Table**
```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration

    public function up(): void

        Schema::create('assets', function (Blueprint $table)
            $table->id();
            $table->string('name');
            $table->string('asset_tag')->unique();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['available', 'borrowed', 'maintenance', 'retired'])->default('available');
            $table->date('acquired_date');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['status', 'category_id']); // Composite index for queries
    );


    public function down(): void

        Schema::dropIfExists('assets');

;
```

**Example: Modifying Column**
```php
public function up(): void

    Schema::table('assets', function (Blueprint $table)
        // MUST include ALL attributes when modifying
        $table->string('asset_tag', 100)->unique()->nullable(false)->change();
);

```

---

## Routing Conventions

**Web Routes** (`routes/web.php`):
```php
use App\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;

// Named routes (REQUIRED for URL generation)
Route::middleware(['auth'])->group(function ()
    Route::resource('assets', AssetController::class)->names('assets');

    // Custom actions
    Route::post('assets/asset/borrow', [AssetController::class, 'borrow'])
        ->name('assets.borrow');
);
```

**API Routes** (`routes/api.php`):
```php
use App\Http\Controllers\Api\V1\AssetController;
use Illuminate\Support\Facades\Route;

// API versioning
Route::prefix('v1')->middleware('auth:sanctum')->group(function ()
    Route::apiResource('assets', AssetController::class);
);
```

**Route Model Binding**:
```php
// Automatic in routes/web.php
Route::get('assets/asset', [AssetController::class, 'show']); // asset auto-resolves to Asset model

// Controller receives typed model
public function show(Asset $asset): View

    return view('assets.show', compact('asset'));

```

---

## Service Container & Dependency Injection

**Prefer Constructor Injection**:
```php
<?php

namespace App\Services;

use App\Repositories\AssetRepository;
use Illuminate\Support\Facades\Log;

class AssetBorrowingService

    public function __construct(
        protected AssetRepository $assetRepository,
        protected AuditLogger $auditLogger
    )

    public function borrowAsset(Asset $asset, User $user): Borrowing

        $borrowing = $this->assetRepository->createBorrowing($asset, $user);
        $this->auditLogger->log('asset.borrowed', $borrowing);

        return $borrowing;


```

**Binding in Service Provider** (`app/Providers/AppServiceProvider.php`):
```php
public function register(): void

    $this->app->singleton(AssetBorrowingService::class);

    $this->app->bind(AssetRepositoryInterface::class, EloquentAssetRepository::class);

```

---

## Controller Validation

**Use Form Request Classes** (REQUIRED):
```php
// app/Http/Requests/StoreAssetRequest.php
<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAssetRequest extends FormRequest

    public function authorize(): bool

        return $this->user()->can('create', Asset::class);


    public function rules(): array

        return [
            'name' => ['required', 'string', 'max:255'],
            'asset_tag' => ['required', 'string', 'unique:assets,asset_tag'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:available,borrowed,maintenance,retired'],
            'acquired_date' => ['required', 'date', 'before_or_equal:today'],
      ;


    public function messages(): array

        return [
            'asset_tag.unique' => 'Kod aset ini telah wujud dalam sistem.',
            'acquired_date.before_or_equal' => 'Tarikh pemerolehan tidak boleh pada masa hadapan.',
      ;


```

**Controller Usage**:
```php
public function store(StoreAssetRequest $request): RedirectResponse

    // $request is already validated
    $asset = Asset::create($request->validated());

    return redirect()->route('assets.show', $asset)
        ->with('success', 'Aset berjaya ditambah.');

```

---

## Query Optimization

**Prevent N+1 Queries** (use eager loading):
```php
// ❌ BAD: N+1 problem
$assets = Asset::all();
foreach ($assets as $asset)
    echo $asset->category->name; // Fires 1 query per asset


// ✅ GOOD: Eager loading
$assets = Asset::with('category')->get();
foreach ($assets as $asset)
    echo $asset->category->name; // Only 2 queries total


// ✅ BETTER: Constrained eager loading (Laravel 11+)
$assets = Asset::with([
    'borrowings' => fn($query) => $query->latest()->limit(5)
])->get();
```

**Query Scopes**:
```php
// In Asset model
public function scopeAvailable(Builder $query): Builder

    return $query->where('status', 'available');


public function scopeByCategory(Builder $query, int $categoryId): Builder

    return $query->where('category_id', $categoryId);


// Usage
$assets = Asset::available()->byCategory(3)->get();
```

---

## Configuration Best Practices

**Never use `env()` outside `config/` files**:
```php
// ❌ BAD: env() in controller
$apiKey = env('MOTAC_API_KEY');

// ✅ GOOD: Use config()
$apiKey = config('services.motac.api_key');
```

**Configuration File** (`config/services.php`):
```php
return [
    'motac' => [
        'api_key' => env('MOTAC_API_KEY'),
        'api_url' => env('MOTAC_API_URL', 'https://api.motac.gov.my'),
        'timeout' => env('MOTAC_API_TIMEOUT', 30),
  ,
];
```

---

## Queue & Job Patterns

**Create Queued Job**:
```php
<?php

namespace App\Jobs;

use App\Models\Asset;
use App\Notifications\AssetBorrowedNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAssetBorrowedNotification implements ShouldQueue

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Asset $asset,
        public User $borrower
    )

    public function handle(): void

        $this->borrower->notify(new AssetBorrowedNotification($this->asset));


    public function failed(\Throwable $exception): void

        Log::error('Failed to send asset borrowed notification', [
            'asset_id' => $this->asset->id,
            'borrower_id' => $this->borrower->id,
            'error' => $exception->getMessage(),
      );


```

**Dispatch Job**:
```php
SendAssetBorrowedNotification::dispatch($asset, $user);

// With delay
SendAssetBorrowedNotification::dispatch($asset, $user)->delay(now()->addMinutes(5));

// On specific queue
SendAssetBorrowedNotification::dispatch($asset, $user)->onQueue('notifications');
```

---

## Testing Standards

**Feature Test Example**:
```php
<?php

namespace Tests\Feature;

use App\Models\Asset;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetBorrowingTest extends TestCase

    use RefreshDatabase;

    public function test_user_can_borrow_available_asset(): void

        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'available']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset));

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'asset_id' => $asset->id,
            'user_id' => $user->id,
      );


    public function test_user_cannot_borrow_unavailable_asset(): void

        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'borrowed']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset));

        $response->assertForbidden();


```

**Run Tests**:
```bash
php artisan test                                    # All tests
php artisan test --filter=AssetBorrowingTest       # Specific class
php artisan test tests/Feature/AssetBorrowingTest.php  # Specific file
php artisan test --parallel                         # Parallel execution
```

---

## URL Generation

**Use Named Routes** (REQUIRED):
```php
// ❌ BAD: Hard-coded URLs
<a href="/assets/ $asset->id ">View Asset</a>

// ✅ GOOD: Named route helper
<a href=" route('assets.show', $asset) ">View Asset</a>

// ✅ GOOD: In controllers
return redirect()->route('assets.index');
```

---

## Authentication & Authorization

**Gates** (for simple checks):
```php
// app/Providers/AuthServiceProvider.php (or bootstrap/app.php)
Gate::define('view-admin-panel', function (User $user)
    return $user->hasRole('admin');
);

// Usage
if (Gate::allows('view-admin-panel'))
    // User has access

```

**Policies** (for model-specific authorization):
```php
// app/Policies/AssetPolicy.php
public function update(User $user, Asset $asset): bool

    return $user->id === $asset->created_by || $user->hasRole('admin');


// Usage in controller
$this->authorize('update', $asset);
```

---

## Error Handling

**Custom Exception Handler** (`bootstrap/app.php`):
```php
->withExceptions(function (Exceptions $exceptions)
    $exceptions->reportable(function (AssetNotFoundException $e)
        Log::warning('Asset not found', ['asset_id' => $e->assetId]);
);

    $exceptions->renderable(function (AssetNotFoundException $e, Request $request)
        if ($request->expectsJson())
            return response()->json(['error' => 'Asset not found'], 404);

        return response()->view('errors.asset-not-found', [], 404);
);
)
```

---

## Common Pitfalls

### ❌ Avoid These Patterns

**1. Using DB facade instead of Eloquent**
```php
// ❌ BAD
$assets = DB::table('assets')->where('status', 'available')->get();

// ✅ GOOD
$assets = Asset::where('status', 'available')->get();
```

**2. N+1 Query Problems**
```php
// ❌ BAD
$assets = Asset::all();
foreach ($assets as $asset)
    echo $asset->category->name; // N+1


// ✅ GOOD
$assets = Asset::with('category')->get();
```

**3. Missing Authorization Checks**
```php
// ❌ BAD
public function update(Request $request, Asset $asset)

    $asset->update($request->all()); // No authorization!


// ✅ GOOD
public function update(UpdateAssetRequest $request, Asset $asset)

    $this->authorize('update', $asset);
    $asset->update($request->validated());

```

**4. Using env() Outside Config Files**
```php
// ❌ BAD
$key = env('API_KEY');

// ✅ GOOD
$key = config('services.external_api.key');
```

---

## References & Resources

- **Official Laravel 12 Docs**: https://laravel.com/docs/12.x
- **Laravel News**: https://laravel-news.com
- **Laracasts**: https://laracasts.com
- **ICTServe Traceability**: D03 (Requirements), D04 (Design), D10 (Source Code Documentation)
- **Standards**: PSR-12 (Code Style), ISO/IEC 12207 (Software Lifecycle)

---

**Status**: ✅ Production-ready for ICTServe
**Last Updated**: 2025-11-01
**Maintained By**: DevOps Team (devops@motac.gov.my)
