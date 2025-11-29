# Laravel Folio — Page-Based Routing

## Overview

Laravel Folio is a powerful page-based router designed to simplify routing in Laravel applications. By placing Blade templates in your `resources/views/pages` directory, Folio automatically creates corresponding routes.

**Version**: Laravel 12.x compatible  
**Purpose**: Simplify routing with file-based conventions

## Installation

```bash
composer require laravel/folio
php artisan folio:install
```

This creates the `resources/views/pages` directory and registers Folio's service provider.

## Basic Usage

### Simple Routes

Create a Blade file in `resources/views/pages`:

```blade
{{-- resources/views/pages/index.blade.php --}}
<div>
    <h1>Welcome to ICTServe</h1>
</div>
```

Accessible at: `http://localhost/`

```blade
{{-- resources/views/pages/about.blade.php --}}
<div>
    <h1>About Us</h1>
</div>
```

Accessible at: `http://localhost/about`

### Nested Routes

```blade
{{-- resources/views/pages/helpdesk/index.blade.php --}}
<div>
    <h1>Helpdesk Dashboard</h1>
</div>
```

Accessible at: `http://localhost/helpdesk`

```blade
{{-- resources/views/pages/helpdesk/tickets.blade.php --}}
<div>
    <h1>All Tickets</h1>
</div>
```

Accessible at: `http://localhost/helpdesk/tickets`

## Route Parameters

### Single Parameter

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

Accessible at: `http://localhost/users/123`

### Multiple Parameters

```blade
{{-- resources/views/pages/posts/[category]/[slug].blade.php --}}
<div>
    <h1>Category: {{ $category }}</h1>
    <h2>Post: {{ $slug }}</h2>
</div>
```

Accessible at: `http://localhost/posts/technology/laravel-12-released`

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
    <p>Code: {{ $asset->asset_tag }}</p>
</div>
```

Folio automatically resolves the `Asset` model by ID.

## Middleware

### Apply Middleware to Single Page

```blade
<?php

use function Laravel\Folio\{middleware};

middleware(['auth', 'verified']);
?>

<div>
    <h1>Protected Page</h1>
</div>
```

### Apply Middleware to Directory

In `app/Providers/FolioServiceProvider.php`:

```php
use Laravel\Folio\Folio;

Folio::path(resource_path('views/pages/admin'))
    ->middleware(['auth', 'role:admin']);
```

## Named Routes

```blade
<?php

use function Laravel\Folio\name;

name('dashboard');
?>

<div>
    <h1>Dashboard</h1>
    <a href="{{ route('dashboard') }}">Refresh</a>
</div>
```

## Route Constraints

```blade
{{-- resources/views/pages/posts/[id].blade.php --}}
<?php

use function Laravel\Folio\{name, where};

name('posts.show');
where('id', '[0-9]+'); // Only numeric IDs
?>

<div>
    <h1>Post #{{ $id }}</h1>
</div>
```

## Soft-Deleted Models

```blade
<?php

use App\Models\Asset;
use function Laravel\Folio\{name, withTrashed};

name('assets.show');
withTrashed();
?>

<div>
    <h1>{{ $asset->name }}</h1>
    @if($asset->trashed())
        <span class="badge bg-danger">Deleted</span>
    @endif
</div>
```

## Render Hooks

Execute code before rendering:

```blade
<?php

use function Laravel\Folio\render;

render(function ($view, $asset) {
    $view->with('recentActivity', $asset->activities()->latest()->take(5)->get());
});
?>

<div>
    <h1>{{ $asset->name }}</h1>
    
    <h2>Recent Activity</h2>
    @foreach($recentActivity as $activity)
        <p>{{ $activity->description }}</p>
    @endforeach
</div>
```

## Organizing Pages

### Subdomain Routing

```php
// app/Providers/FolioServiceProvider.php
Folio::path(resource_path('views/pages/admin'))
    ->domain('admin.ictserve.test')
    ->middleware(['auth', 'role:admin']);
```

### URI Prefix

```php
Folio::path(resource_path('views/pages/api'))
    ->uri('/api/v1');
```

## Best Practices

1. **Use for Simple Pages**: Folio works best for pages with minimal logic
2. **Complex Logic**: Use controllers for complex business logic
3. **Middleware**: Apply authentication/authorization via middleware
4. **Named Routes**: Always name routes for easier refactoring
5. **Model Binding**: Leverage route model binding for cleaner code

## Integration with Livewire

```blade
{{-- resources/views/pages/helpdesk/create.blade.php --}}
<?php

use function Laravel\Folio\{name, middleware};

name('helpdesk.create');
middleware(['guest']);
?>

<div>
    <h1>Submit Helpdesk Ticket</h1>
    
    @livewire('helpdesk.create-ticket-form')
</div>
```

## Testing Folio Routes

```php
<?php

namespace Tests\Feature;

use Tests\TestCase;

class FolioRoutesTest extends TestCase
{
    public function test_homepage_loads(): void
    {
        $response = $this->get('/');
        
        $response->assertStatus(200);
        $response->assertSee('Welcome to ICTServe');
    }
    
    public function test_asset_page_requires_auth(): void
    {
        $response = $this->get('/assets/1');
        
        $response->assertRedirect('/login');
    }
}
```

## Common Patterns

### Dashboard Page

```blade
{{-- resources/views/pages/dashboard.blade.php --}}
<?php

use function Laravel\Folio\{name, middleware};

name('dashboard');
middleware(['auth', 'verified']);
?>

<x-app-layout>
    <x-slot name="header">
        <h2>Dashboard</h2>
    </x-slot>

    <div class="py-12">
        @livewire('authenticated-dashboard')
    </div>
</x-app-layout>
```

### Profile Page

```blade
{{-- resources/views/pages/profile.blade.php --}}
<?php

use function Laravel\Folio\{name, middleware};

name('profile');
middleware(['auth']);
?>

<x-app-layout>
    <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
        @livewire('profile.update-profile-information-form')
        
        @livewire('profile.update-password-form')
    </div>
</x-app-layout>
```

## Limitations

- Not suitable for complex routing logic
- Limited support for route groups
- Cannot define multiple HTTP methods per file
- Best for content-focused pages

## References

- Official Documentation: https://laravel.com/docs/12.x/folio
- GitHub Repository: https://github.com/laravel/folio
