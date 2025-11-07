---
applyTo:
  - '**/*.php'
  - 'routes/**'
  - 'database/**'
  - 'config/**'
  - 'bootstrap/**'
  - 'app/**'
description: |
  Laravel 12 development standards for ICTServe project.
  Enforces Artisan workflows, Eloquent patterns, Service Container usage,
  PSR-12 compliance, and Laravel 12 best practices for AI code generation.
tags:
  - laravel
  - php
  - eloquent
  - artisan
  - testing
version: '1.0.0'
lastUpdated: '2025-01-06'
---

# Laravel 12 Development Standards

## Overview

This rule defines Laravel 12 (February 2025) conventions and best practices for the ICTServe project. Follow these standards for all PHP backend development.

**Framework**: Laravel 12.x  
**PHP Version**: 8.2-8.4  
**Applies To**: Application layer, routing, database, configuration, bootstrap

## Core Principles

1. **Laravel Way First**: Use framework conventions before custom solutions
2. **Artisan-Driven**: Generate files via `php artisan make:*` commands
3. **Dependency Injection**: Use constructor injection over facades
4. **Eloquent ORM**: Prefer relationships over raw SQL
5. **PSR-12 Compliance**: Follow PHP-FIG standards (Laravel Pint)

## Laravel 12 Key Changes

- ✅ No `app/Http/Kernel.php` - Use `bootstrap/app.php`
- ✅ No `app/Console/Kernel.php` - Auto-register commands
- ✅ No middleware directory - Define inline in `bootstrap/app.php`
- ✅ Service providers in `bootstrap/providers.php`
- ✅ Attribute-based observers/scopes: `#[ObservedBy]`, `#[ScopedBy]`, `#[Scope]`
- ✅ UUID/ULID support via `HasUuids`/`HasUlids` traits

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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\HandleInertiaRequests::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->reportable(function (Throwable $e) {
            // Custom error handling
        });
    })->create();
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

# Observer for model events
php artisan make:observer AssetObserver --model=Asset --no-interaction

# Scope for query constraints
php artisan make:scope AncientScope --no-interaction
```

**List available commands**:

```bash
php artisan list              # All commands
php artisan make:model --help # Specific command options
```

---

## Eloquent Model Standards

**Laravel 12 Model with Modern Features**:

```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // or HasUlids
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Observers\AssetObserver;
use App\Models\Scopes\ActiveScope;

#[ObservedBy([AssetObserver::class])] // Laravel 12: Attribute-based observer registration
#[ScopedBy([ActiveScope::class])]     // Laravel 12: Attribute-based scope registration
class Asset extends Model
{
    use HasFactory, SoftDeletes, HasUuids;

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
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'acquired_date' => 'date',
            'created_at' => 'datetime',
            'updated_at' => 'datetime',
            'deleted_at' => 'datetime',
        ];
    }

    /**
     * The model's default values for attributes.
     *
     * @var array
     */
    protected $attributes = [
        'status' => 'available',
    ];

    /**
     * Get the category that owns the asset.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the borrowing records for the asset.
     */
    public function borrowings(): HasMany
    {
        return $this->hasMany(Borrowing::class);
    }
}
```

**Key Requirements**:

- ✅ `declare(strict_types=1)` at file start
- ✅ Use `protected function casts(): array` (NOT `protected $casts` property)
- ✅ Explicit return type hints for relationships
- ✅ PHPDoc blocks for array shapes and method descriptions
- ✅ `SoftDeletes` trait for logical deletion
- ✅ **Laravel 12**: Use `#[ObservedBy]` and `#[ScopedBy]` attributes instead of boot method registration
- ✅ **Laravel 12**: Use `HasUuids` or `HasUlids` traits for UUID/ULID primary keys
- ✅ Default attribute values via `$attributes` property

**Laravel 12 Model Features**:

**1. UUID/ULID Primary Keys**:

```php
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Article extends Model
{
    use HasUuids;

    // Override UUID generation if needed
    public function newUniqueId(): string
    {
        return (string) Uuid::uuid4();
    }

    // Specify which columns should receive UUIDs
    public function uniqueIds(): array
    {
        return ['id', 'discount_code'];
    }
}

// For ULIDs (26 character, lexicographically sortable)
use Illuminate\Database\Eloquent\Concerns\HasUlids;

class Article extends Model
{
    use HasUlids;
}
```

**2. Eloquent Strictness Configuration** (`AppServiceProvider`):

```php
use Illuminate\Database\Eloquent\Model;

public function boot(): void
{
    // Prevent lazy loading in non-production
    Model::preventLazyLoading(!$this->app->isProduction());
    
    // Throw exception when filling unfillable attributes
    Model::preventSilentlyDiscardingAttributes(!$this->app->isProduction());
}
```

**3. Model Inspection**:

```bash
php artisan model:show Flight  # Shows attributes, relationships, observers, scopes
```

**4. Composite Primary Keys**:

- Eloquent requires each model to have at least one uniquely identifying "ID"
- Composite primary keys are NOT supported by Eloquent
- Use multi-column unique indexes instead

**5. Pruning Models** (Laravel 12):

```php
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Prunable; // or MassPrunable

class Flight extends Model
{
    use Prunable;

    public function prunable(): Builder
    {
        return static::where('created_at', '<=', now()->subMonth());
    }

    protected function pruning(): void
    {
        // Delete associated files before pruning
    }
}

// Schedule in routes/console.php
use Illuminate\Support\Facades\Schedule;

Schedule::command('model:prune')->daily();
```

---

## Query Scopes (Laravel 12)

**Global Scopes with Attributes**:

```php
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use App\Models\Scopes\AncientScope;

#[ScopedBy([AncientScope::class])]
class User extends Model
{
    //
}

// Scope class
class AncientScope implements Scope
{
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where('created_at', '<', now()->subYears(2000));
    }
}
```

**Local Scopes with Attributes**:

```php
use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;

class User extends Model
{
    #[Scope]
    protected function popular(Builder $query): void
    {
        $query->where('votes', '>', 100);
    }

    #[Scope]
    protected function active(Builder $query): void
    {
        $query->where('active', 1);
    }

    // Dynamic scope with parameters
    #[Scope]
    protected function ofType(Builder $query, string $type): void
    {
        $query->where('type', $type);
    }
}

// Usage
$users = User::popular()->active()->get();
$users = User::ofType('admin')->get();
```

**Pending Attributes** (Laravel 12 - scopes with default values):

```php
#[Scope]
protected function draft(Builder $query): void
{
    $query->withAttributes([
        'hidden' => true,
    ]);
}

$draft = Post::draft()->create(['title' => 'In Progress']);
$draft->hidden; // true
```

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
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('asset_tag')->unique();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->enum('status', ['available', 'borrowed', 'maintenance', 'retired'])->default('available');
            $table->date('acquired_date');
            $table->timestamps();
            $table->softDeletes();
            
            $table->index(['status', 'category_id']); // Composite index for queries
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
```

**Example: Modifying Column**

```php
public function up(): void
{
    Schema::table('assets', function (Blueprint $table) {
        // MUST include ALL attributes when modifying
        $table->string('asset_tag', 100)->unique()->nullable(false)->change();
    });
}
```

---

## Routing Conventions

**Web Routes** (`routes/web.php`):

```php
use App\Http\Controllers\AssetController;
use Illuminate\Support\Facades\Route;

// Named routes (REQUIRED for URL generation)
Route::middleware(['auth'])->group(function () {
    Route::resource('assets', AssetController::class)->names('assets');
    
    // Custom actions
    Route::post('assets/{asset}/borrow', [AssetController::class, 'borrow'])
        ->name('assets.borrow');
});
```

**API Routes** (`routes/api.php`):

```php
use App\Http\Controllers\Api\V1\AssetController;
use Illuminate\Support\Facades\Route;

// API versioning
Route::prefix('v1')->middleware('auth:sanctum')->group(function () {
    Route::apiResource('assets', AssetController::class);
});
```

**Route Model Binding**:

```php
// Automatic in routes/web.php
Route::get('assets/{asset}', [AssetController::class, 'show']); // asset auto-resolves to Asset model

// Controller receives typed model
public function show(Asset $asset): View
{
    return view('assets.show', compact('asset'));
}
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
{
    public function __construct(
        protected AssetRepository $assetRepository,
        protected AuditLogger $auditLogger
    ) {}

    public function borrowAsset(Asset $asset, User $user): Borrowing
    {
        $borrowing = $this->assetRepository->createBorrowing($asset, $user);
        $this->auditLogger->log('asset.borrowed', $borrowing);
        
        return $borrowing;
    }
}
```

**Binding in Service Provider** (`app/Providers/AppServiceProvider.php`):

```php
public function register(): void
{
    $this->app->singleton(AssetBorrowingService::class);
    
    $this->app->bind(AssetRepositoryInterface::class, EloquentAssetRepository::class);
}
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
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Asset::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'asset_tag' => ['required', 'string', 'unique:assets,asset_tag'],
            'category_id' => ['required', 'exists:categories,id'],
            'status' => ['required', 'in:available,borrowed,maintenance,retired'],
            'acquired_date' => ['required', 'date', 'before_or_equal:today'],
        ];
    }

    public function messages(): array
    {
        return [
            'asset_tag.unique' => 'This asset code already exists in the system.',
            'acquired_date.before_or_equal' => 'Acquisition date cannot be in the future.',
        ];
    }
}
```

**Controller Usage**:

```php
public function store(StoreAssetRequest $request): RedirectResponse
{
    // $request is already validated
    $asset = Asset::create($request->validated());
    
    return redirect()->route('assets.show', $asset)
        ->with('success', 'Asset successfully added.');
}
```

---

## Query Optimization

**Prevent N+1 Queries** (use eager loading):

```php
// ❌ BAD: N+1 problem
$assets = Asset::all();
foreach ($assets as $asset) {
    echo $asset->category->name; // Fires 1 query per asset
}

// ✅ GOOD: Eager loading
$assets = Asset::with('category')->get();
foreach ($assets as $asset) {
    echo $asset->category->name; // Only 2 queries total
}

// ✅ BETTER: Constrained eager loading (Laravel 11+)
$assets = Asset::with([
    'borrowings' => fn($query) => $query->latest()->limit(5)
])->get();
```

**Query Scopes**:

```php
// In Asset model
use Illuminate\Database\Eloquent\Attributes\Scope;

#[Scope]
protected function available(Builder $query): void
{
    $query->where('status', 'available');
}

#[Scope]
protected function byCategory(Builder $query, int $categoryId): void
{
    $query->where('category_id', $categoryId);
}

// Usage
$assets = Asset::available()->byCategory(3)->get();
```

---

## Configuration Best Practices

**Never use `env()` outside `config/` files**:

```php
// ❌ BAD: env() in controller
$apiKey = env('EXTERNAL_API_KEY');

// ✅ GOOD: Use config()
$apiKey = config('services.external_api.key');
```

**Configuration File** (`config/services.php`):

```php
return [
    'external_api' => [
        'key' => env('EXTERNAL_API_KEY'),
        'url' => env('EXTERNAL_API_URL', 'https://api.example.com'),
        'timeout' => env('EXTERNAL_API_TIMEOUT', 30),
    ],
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
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(
        public Asset $asset,
        public User $borrower
    ) {}

    public function handle(): void
    {
        $this->borrower->notify(new AssetBorrowedNotification($this->asset));
    }

    public function failed(\Throwable $exception): void
    {
        Log::error('Failed to send asset borrowed notification', [
            'asset_id' => $this->asset->id,
            'borrower_id' => $this->borrower->id,
            'error' => $exception->getMessage(),
        ]);
    }
}
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
{
    use RefreshDatabase;

    public function test_user_can_borrow_available_asset(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'available']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset));

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'asset_id' => $asset->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_user_cannot_borrow_unavailable_asset(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'borrowed']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset));

        $response->assertForbidden();
    }
}
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
<a href="/assets/{{ $asset->id }}">View Asset</a>

// ✅ GOOD: Named route helper
<a href="{{ route('assets.show', $asset) }}">View Asset</a>

// ✅ GOOD: In controllers
return redirect()->route('assets.index');
```

---

## Authentication & Authorization

**Gates** (for simple checks):

```php
// bootstrap/app.php or app/Providers/AuthServiceProvider.php
Gate::define('view-admin-panel', function (User $user) {
    return $user->hasRole('admin');
});

// Usage
if (Gate::allows('view-admin-panel')) {
    // User has access
}
```

**Policies** (for model-specific authorization):

```php
// app/Policies/AssetPolicy.php
public function update(User $user, Asset $asset): bool
{
    return $user->id === $asset->created_by || $user->hasRole('admin');
}

// Usage in controller
$this->authorize('update', $asset);
```

---

## Error Handling

**Custom Exception Handler** (`bootstrap/app.php`):

```php
->withExceptions(function (Exceptions $exceptions) {
    $exceptions->reportable(function (AssetNotFoundException $e) {
        Log::warning('Asset not found', ['asset_id' => $e->assetId]);
    });

    $exceptions->renderable(function (AssetNotFoundException $e, Request $request) {
        if ($request->expectsJson()) {
            return response()->json(['error' => 'Asset not found'], 404);
        }
        return response()->view('errors.asset-not-found', [], 404);
    });
})
```

---

## Laravel 12 Starter Kits

**Available Starter Kits**:

- **React** (Inertia 2, TypeScript, shadcn/ui)
- **Vue** (Inertia 2, TypeScript, shadcn/ui)
- **Livewire** (Flux UI, Volt)

**WorkOS AuthKit Variant** (all kits):

- Social authentication
- Passkeys support
- SSO (Single Sign-On)
- Free up to 1 million monthly active users

**Installation**:

```bash
laravel new my-app
# Choose starter kit during installation
```

**Note**: Laravel Breeze and Jetstream will no longer receive additional updates. Use the new starter kits instead.

---

## Laravel Boost & AI Integration

**Laravel Boost** bridges AI coding agents and Laravel applications:

**Features**:

- 15+ specialized tools (database queries, Tinker, documentation search, browser logs)
- 17,000+ vectorized Laravel ecosystem documentation pieces (version-specific)
- Laravel-maintained AI guidelines
- Automatic package version detection

**Installation**:

```bash
composer require laravel/boost --dev
php artisan boost:install
```

**IDE Support**:

- **VS Code/Cursor**: Laravel VS Code Extension (syntax highlighting, snippets, Artisan integration)
- **PhpStorm**: Laravel Idea plugin (autocompletion, code generation, navigation)
- **Cloud IDE**: Firebase Studio (browser-based Laravel development)

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
foreach ($assets as $asset) {
    echo $asset->category->name; // N+1
}

// ✅ GOOD
$assets = Asset::with('category')->get();
```

**3. Missing Authorization Checks**

```php
// ❌ BAD
public function update(Request $request, Asset $asset)
{
    $asset->update($request->all()); // No authorization!
}

// ✅ GOOD
public function update(UpdateAssetRequest $request, Asset $asset)
{
    $this->authorize('update', $asset);
    $asset->update($request->validated());
}
```

**4. Using env() Outside Config Files**

```php
// ❌ BAD
$key = env('API_KEY');

// ✅ GOOD
$key = config('services.external_api.key');
```

**5. Not Using Named Routes**

```php
// ❌ BAD
return redirect('/assets/' . $asset->id);

// ✅ GOOD
return redirect()->route('assets.show', $asset);
```

---

## References & Resources

- **Official Laravel 12 Docs**: <https://laravel.com/docs/12.x>
- **Release Notes**: <https://laravel.com/docs/12.x/releases>
- **Starter Kits**: <https://laravel.com/starter-kits>
- **Laravel News**: <https://laravel-news.com>
- **Laracasts**: <https://laracasts.com>
- **Laravel Boost**: <https://github.com/laravel/boost>
- **Standards**: PSR-12 (Code Style), Semantic Versioning

---

## Compliance Checklist

When generating Laravel code, ensure:

- [ ] `declare(strict_types=1);` at file start
- [ ] Type hints on all parameters and return types
- [ ] PHPDoc blocks for arrays and complex types
- [ ] Use `protected function casts(): array` (not `$casts` property)
- [ ] Named routes for all URL generation
- [ ] Form Request classes for validation
- [ ] Eager loading to prevent N+1 queries
- [ ] Authorization checks via policies
- [ ] Queue jobs for async operations
- [ ] Comprehensive tests with RefreshDatabase

---

**Status**: ✅ Active for ICTServe Laravel 12 development  
**Version**: 1.0.0  
**Last Updated**: 2025-01-06
