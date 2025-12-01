---
applyTo: "resources/lang/**,resources/views/**,app/**"
description: "Internationalization (i18n) standards: Bahasa Melayu/English support, translation keys, date formatting, and locale middleware for ICTServe."
---

# Localization (i18n) Instructions

**Purpose**
Defines mandatory standards for Internationalization (i18n) and Localization (l10n) in ICTServe. Ensures the application is fully bilingual with **Bahasa Melayu (ms)** as the primary language and **English (en)** as secondary.

**Scope**
Applies to `resources/lang`, `resources/views`, Controllers, Livewire Components, and Filament Resources.

## 1. Language Architecture

### Supported Locales
* **Primary**: `ms` (Bahasa Melayu) - Default for guests.
* **Secondary**: `en` (English) - Toggleable by user.

### Directory Structure
Store translation files in `resources/lang` (or `lang/` depending on Laravel version configuration).

```text
resources/lang/
├── ms/ (Primary)
│   ├── auth.php
│   ├── validation.php
│   ├── tickets.php
│   └── loans.php
├── en/ (Secondary)
│   ├── auth.php
│   ├── validation.php
│   ├── tickets.php
│   └── loans.php
````

## 2\. Implementation Patterns

### Usage in PHP (Controllers/Services)

Always use the helper function `__()`.

```php
// ✅ GOOD: Translatable key
session()->flash('success', __('tickets.created_success'));

// ❌ BAD: Hardcoded text
session()->flash('success', 'Tiket berjaya dicipta.');
```

### Usage in Blade / Livewire

Use the `__` helper or `@lang` directive.

```blade
<h1>{{ __('dashboard.welcome', ['name' => $user->name]) }}</h1>

@lang('dashboard.welcome', ['name' => $user->name])
```

### JSON Translations

For simple UI labels (buttons, generic headers), use JSON files (`ms.json`, `en.json`).

```json
// resources/lang/ms.json
{
    "Save": "Simpan",
    "Cancel": "Batal",
    "Dashboard": "Papan Pemuka"
}
```

```blade
<button>{{ __('Save') }}</button>
```

## 3\. Formatting Standards

### Dates & Times

Use **Carbon** with the application locale.

```php
// Configured in AppServiceProvider or Middleware
Carbon::setLocale(app()->getLocale());

// Display
echo $ticket->created_at->translatedFormat('d F Y'); 
// Output (ms): 15 Januari 2025
// Output (en): 15 January 2025

echo $ticket->created_at->diffForHumans();
// Output (ms): 2 jam lepas
```

### Numbers & Currency

Use `NumberFormatter` for locale-aware formatting.

```php
$fmt = new NumberFormatter(app()->getLocale(), NumberFormatter::CURRENCY);
echo $fmt->formatCurrency($amount, 'MYR');
```

## 4\. Filament Localization

Filament detects the locale automatically. Ensure the panel provider is configured to respect the user's session locale.

**AdminPanelProvider.php**:

```php
public function panel(Panel $panel): Panel
{
    return $panel
        // ...
        ->middleware([
            \App\Http\Middleware\SetLocale::class, // Ensure this runs
        ]);
}
```

To publish/customize Filament translations:

```bash
php artisan vendor:publish --tag=filament-translations
```

## 5\. Middleware Implementation

Ensure a middleware handles locale switching based on Session or Browser preference.

**`app/Http/Middleware/SetLocale.php`**:

```php
<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = session('locale', config('app.locale'));

        if (in_array($locale, ['ms', 'en'])) {
            App::setLocale($locale);
        }

        return $next($request);
    }
}
```

## 6\. Pre-Commit Checklist

  - [ ] All new user-facing text is wrapped in `__()`.
  - [ ] Keys are added to **both** `ms/*.php` and `en/*.php` files.
  - [ ] No hardcoded dates (used Carbon `translatedFormat`).
  - [ ] Filament resources use `->label(__('keys...'))`.
  - [ ] Validation messages in Form Requests use translated strings.
