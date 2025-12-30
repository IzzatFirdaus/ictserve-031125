---
inclusion:
  fileMatchPattern:
    - 'resources/views/pages/**/*.blade.php'
    - 'app/Providers/FolioServiceProvider.php'
  applyWhen:
    - Page-based routing with Laravel Folio
    - File-based route conventions
    - Simple content pages
---

# Laravel Folio Page-Based Routing Guidelines

Laravel Folio simplifies routing by automatically creating routes from Blade templates in `resources/views/pages`.

## Basic Usage

```blade
{{-- resources/views/pages/about.blade.php --}}
<div>
    <h1>About Us</h1>
</div>
```

Accessible at: `http://localhost/about`

## Route Parameters

```blade
{{-- resources/views/pages/users/[id].blade.php --}}
<?php
use function Laravel\Folio\name;
name('users.show');
?>

<div>
    <h1>User #{{ $id }}</h1>
</div>
```

## Route Model Binding

```blade
{{-- resources/views/pages/assets/[Asset].blade.php --}}
<?php
use App\Models\Asset;
use function Laravel\Folio\name;
name('assets.show');
?>

<div>
    <h1>{{ $asset->name }}</h1>
</div>
```

## Middleware

```blade
<?php
use function Laravel\Folio\middleware;
middleware(['auth', 'verified']);
?>

<div>
    <h1>Protected Page</h1>
</div>
```

## ICTServe Usage

**CRITICAL**: ICTServe does NOT use Laravel Folio. All routing is defined in `routes/web.php` and `routes/api.php`.

Use traditional Laravel routing patterns:
- Controllers for business logic
- Livewire components for interactive UI
- Filament for admin panel

Do not create Folio pages unless explicitly requested.
