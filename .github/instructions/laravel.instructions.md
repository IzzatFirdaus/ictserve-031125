---
applyTo: "app/**,routes/**,database/**,config/**,bootstrap/**"
description: "Laravel 12 standards: Application structure, Artisan workflows, Service Container patterns, and Eloquent conventions for ICTServe."
---

# Laravel 12 Development Instructions

**Purpose**
Defines mandatory framework standards for ICTServe. Ensures code consistency, maintainability, and alignment with Laravel 12's streamlined architecture and PHP 8.4's strict typing.

**Scope**
Applies to the Application layer (`app/`), Routing (`routes/`), Configuration (`config/`), and Bootstrap (`bootstrap/`).

## 1. Core Principles

1.  **Laravel Way**: Follow framework conventions over custom solutions.
2.  **Strict Typing**: All files must start with `declare(strict_types=1);`.
3.  **Thin Controllers**: Delegate business logic to **Services**.
4.  **Artisan Driven**: Use CLI generators to ensure boilerplate consistency.
5.  **Secure by Default**: Always validate inputs and escape outputs.

## 2. Laravel 12 Structure

**Key Changes from Legacy Versions**:
* **No Kernels**: Middleware and Exceptions are configured in `bootstrap/app.php`.
* **API Routes**: Must be explicitly installed via `php artisan install:api` (already present in ICTServe).
* **Providers**: Registered automatically or via `bootstrap/providers.php`.

**Middleware Configuration (`bootstrap/app.php`)**:
```php
return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
        ]);
        
        $middleware->api(prepend: [
            \Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();
````

## 3\. Artisan Command Patterns

Use these commands to generate standardized components:

```bash
# Model with Migration, Factory, and Seeder
php artisan make:model Asset -mfs

# Form Request
php artisan make:request StoreAssetRequest

# Service (Manual creation required, follow PSR-4)
# Create file: app/Services/AssetService.php

# Filament Resource
php artisan make:filament-resource Asset

# Volt Component
php artisan make:volt assets/create-form
```

## 4\. Eloquent Model Standards

See `model.instructions.md` for deep details.

  * **Casting**: Use `protected function casts(): array`.
  * **Fillable**: Always define `$fillable`.
  * **Traits**: Use `SoftDeletes` and `Auditable`.

## 5\. Controller & Routing

### Resource Controllers

Keep controllers clean. Use Resource routing where possible.

**`routes/web.php`**:

```php
use App\Http\Controllers\AssetController;

Route::middleware(['auth', 'verified'])->group(function () {
    Route::resource('assets', AssetController::class);
});
```

### Dependency Injection

Use **Constructor Property Promotion** to inject Services.

```php
<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\StoreAssetRequest;
use App\Services\AssetService;
use Illuminate\Http\RedirectResponse;

class AssetController extends Controller
{
    public function __construct(
        private readonly AssetService $service
    ) {}

    public function store(StoreAssetRequest $request): RedirectResponse
    {
        $this->service->create($request->validated());

        return redirect()->route('assets.index')
            ->with('success', __('assets.created_success'));
    }
}
```

## 6\. Service Container & Logic

**Rule**: Do not put complex business logic in Controllers.

**Service Pattern**:

```php
<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Asset;
use Illuminate\Support\Facades\DB;

class AssetService
{
    public function create(array $data): Asset
    {
        return DB::transaction(function () use ($data) {
            $asset = Asset::create($data);
            
            // Handle side effects (e.g., Audit logs, Notifications)
            
            return $asset;
        });
    }
}
```

## 7\. Configuration Best Practices

**Rule**: NEVER use `env()` outside of config files.

**❌ BAD**:

```php
// In Controller
$apiKey = env('API_KEY'); 
```

**✅ GOOD**:

```php
// config/services.php
'external_api' => [
    'key' => env('API_KEY'),
],

// In Controller
$apiKey = config('services.external_api.key');
```

## 8\. Pre-Commit Checklist

  - [ ] `declare(strict_types=1)` is present.
  - [ ] Logic is in Services, not Controllers.
  - [ ] Routes are named (`->name('assets.index')`).
  - [ ] `env()` calls are replaced with `config()`.
  - [ ] N+1 queries are prevented (use `with()`).
