---
applyTo: "resources/lang/**,resources/views/**,app/**"
description: "Internationalization (i18n) patterns for Bahasa Melayu and English localization in ICTServe"
---

# Localization (i18n) — ICTServe Standards

## Purpose & Scope

Internationalization (i18n) and localization (l10n) best practices for ICTServe. Covers Laravel translation system, language switching, Bahasa Melayu (ms) and English (en) support, pluralization, and date/time formatting.

**Traceability**: D14 (UI/UX Design Guide), D15 (Bilingual Requirements)

---

## Language Structure

### Supported Languages

**Primary**: Bahasa Melayu (`ms`)
**Secondary**: English (`en`)

### Directory Structure

```
resources/lang/
├── en/
│   ├── auth.php
│   ├── validation.php
│   ├── assets.php
│   └── borrowings.php
└── ms/
    ├── auth.php
    ├── validation.php
    ├── assets.php
    └── borrowings.php
```

---

## Translation Syntax

### Using `__()` Helper

```php
// Controller
return view('assets.index', [
    'title' => __('assets.list_title'),
]);
```

```blade
-- Blade template --
<h1> __('assets.list_title') </h1>
<p> __('assets.description') </p>

-- With parameters --
<p> __('assets.total_count', ['count' => 150]) </p>
```

**Translation File** (`resources/lang/ms/assets.php`):
```php
return [
    'list_title' => 'Senarai Aset',
    'description' => 'Paparan semua aset di dalam sistem.',
    'total_count' => 'Jumlah: :count aset',
];
```

---

### Using `@lang` Directive

```blade
<h1>@lang('assets.list_title')</h1>
<p>@lang('assets.total_count', ['count' => 150])</p>
```

---

### Using `trans()` Helper

```php
// Same as __() but supports choice/pluralization
echo trans('assets.items_found', ['count' => 3]);
```

---

## Pluralization

### English Pluralization

**Translation File** (`resources/lang/en/assets.php`):
```php
return [
    'items_found' => '0 No assets found|1 One asset found|[2,*] :count assets found',
];
```

**Usage**:
```blade
 trans_choice('assets.items_found', 0)
-- Output: No assets found --

 trans_choice('assets.items_found', 1)
-- Output: One asset found --

 trans_choice('assets.items_found', 5)
-- Output: 5 assets found --
```

---

### Bahasa Melayu Pluralization

**Translation File** (`resources/lang/ms/assets.php`):
```php
return [
    // Bahasa Melayu doesn't have grammatical plurals
    'items_found' => '0 Tiada aset dijumpai|1 1 aset dijumpai|[2,*] :count aset dijumpai',
];
```

---

## JSON Translations (Inline)

### Using JSON Files

**File**: `resources/lang/ms.json`
```json

    "Login": "Log Masuk",
    "Logout": "Log Keluar",
    "Dashboard": "Papan Pemuka",
    "Assets": "Aset",
    "Save": "Simpan",
    "Cancel": "Batal"

```

**Usage**:
```blade
<button> __('Save') </button>
-- Output (ms): Simpan --
-- Output (en): Save --
```

**Advantages**:
- No need to create separate PHP files for simple translations
- Works across all files automatically

---

## Language Switching

### Middleware for Locale

**File**: `app/Http/Middleware/SetLocale.php`
```php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class SetLocale

    public function handle(Request $request, Closure $next)

        $locale = session('locale', config('app.locale'));

        if (in_array($locale, ['en', 'ms']))
            App::setLocale($locale);


        return $next($request);


```

**Register in `bootstrap/app.php`**:
```php
->withMiddleware(function (Middleware $middleware)
    $middleware->web(append: [
        \App\Http\Middleware\SetLocale::class,
  );
)
```

---

### Language Switcher (Livewire)

**Component**: `app/Livewire/LanguageSwitcher.php`
```php
namespace App\Livewire;

use Livewire\Component;

class LanguageSwitcher extends Component

    public function switchLanguage(string $locale): void

        if (!in_array($locale, ['en', 'ms']))
            return;


        session(['locale' => $locale]);
        $this->redirect(request()->header('Referer'));


    public function render()

        return view('livewire.language-switcher');


```

**View**: `resources/views/livewire/language-switcher.blade.php`
```blade
<div class="flex gap-2">
    <button
        wire:click="switchLanguage('ms')"
        class="px-3 py-1 rounded  app()->getLocale() === 'ms' ? 'bg-blue-600 text-white' : 'bg-gray-200' "
    >
        BM
    </button>
    <button
        wire:click="switchLanguage('en')"
        class="px-3 py-1 rounded  app()->getLocale() === 'en' ? 'bg-blue-600 text-white' : 'bg-gray-200' "
    >
        EN
    </button>
</div>
```

---

## Date & Time Localization

### Using Carbon

```php
use Carbon\Carbon;

// Set locale for Carbon
Carbon::setLocale(app()->getLocale());

$date = Carbon::parse('2025-01-15 14:30:00');

// Format
echo $date->translatedFormat('l, j F Y');
// ms: Rabu, 15 Januari 2025
// en: Wednesday, 15 January 2025

echo $date->diffForHumans();
// ms: 3 hari yang lalu
// en: 3 days ago
```

---

### Blade Helper

```blade
-- Format date --
 \Carbon\Carbon::parse($asset->created_at)->translatedFormat('j F Y')

-- Relative time --
 \Carbon\Carbon::parse($asset->created_at)->diffForHumans()
```

---

## Number Formatting

### Using `NumberFormatter`

```php
$formatter = new \NumberFormatter(app()->getLocale(), \NumberFormatter::DECIMAL);
echo $formatter->format(1234567.89);
// ms: 1,234,567.89
// en: 1,234,567.89

$currency = new \NumberFormatter(app()->getLocale(), \NumberFormatter::CURRENCY);
echo $currency->formatCurrency(1500, 'MYR');
// ms: RM1,500.00
// en: MYR 1,500.00
```

---

## Translation Conventions

### Translation Keys

**Use Descriptive Dot Notation**:
```php
// ✅ GOOD: Clear hierarchy
__('assets.form.labels.name')
__('borrowings.status.approved')
__('validation.required')

// ❌ BAD: Unclear structure
__('name')
__('approved')
```

---

### Parameter Naming

```php
// Translation file
return [
    'welcome_message' => 'Selamat datang, :name!',
    'asset_assigned' => 'Aset :asset_name telah diberikan kepada :user_name.',
];

// Usage
__('assets.welcome_message', ['name' => $user->name])
__('assets.asset_assigned', [
    'asset_name' => $asset->name,
    'user_name' => $user->name,
])
```

---

### HTML in Translations

```php
// Translation file
return [
    'terms' => 'Saya bersetuju dengan <a href=":url" class="underline">Terma dan Syarat</a>.',
];

// Usage (use !! !! to render HTML)
!! __('assets.terms', ['url' => route('terms')]) !!
```

---

## Validation Messages

### Custom Validation Messages

**File**: `resources/lang/ms/validation.php`
```php
return [
    'required' => 'Medan :attribute diperlukan.',
    'email' => 'Medan :attribute mestilah alamat e-mel yang sah.',
    'unique' => ':attribute telah wujud dalam sistem.',

    'attributes' => [
        'name' => 'nama',
        'email' => 'e-mel',
        'asset_tag' => 'tag aset',
  ,
];
```

**Usage in Form Request**:
```php
public function messages(): array

    return [
        'name.required' => __('validation.required', ['attribute' => __('validation.attributes.name')]),
  ;

```

---

## Filament Localization

### Publish Filament Translations

```bash
php artisan vendor:publish --tag=filament-translations
```

**Files Created**:
- `lang/vendor/filament/en/`
- `lang/vendor/filament/ms/`

---

### Set Filament Locale

**File**: `app/Providers/Filament/AdminPanelProvider.php`
```php
use Filament\Panel;

public function panel(Panel $panel): Panel

    return $panel
        ->id('admin')
        ->path('admin')
        ->login()
        ->locale(app()->getLocale());

```

---

## Best Practices

### 1. Always Use Translation Helpers

```blade
-- ✅ GOOD: Translatable --
<h1> __('assets.list_title') </h1>

-- ❌ BAD: Hardcoded --
<h1>Senarai Aset</h1>
```

---

### 2. Use JSON for Simple Strings

```json
// resources/lang/ms.json

    "Save": "Simpan",
    "Edit": "Sunting",
    "Delete": "Padam"

```

```blade
<button> __('Save') </button>
```

---

### 3. Organize by Feature

```
resources/lang/
├── en/
│   ├── assets.php      (Asset-related translations)
│   ├── borrowings.php  (Borrowing-related translations)
│   └── users.php       (User-related translations)
└── ms/
    ├── assets.php
    ├── borrowings.php
    └── users.php
```

---

### 4. Test Both Languages

```php
// Test helper
public function testTranslationExists()

    App::setLocale('ms');
    $this->assertEquals('Senarai Aset', __('assets.list_title'));

    App::setLocale('en');
    $this->assertEquals('Asset List', __('assets.list_title'));

```

---

## Localization Checklist

- [ ] All user-facing text uses translation helpers (`__()`, `@lang`)
- [ ] Translation files exist for both `ms` and `en`
- [ ] JSON translations used for simple strings
- [ ] Date/time formatted using Carbon with locale
- [ ] Number/currency formatted using `NumberFormatter`
- [ ] Language switcher component implemented
- [ ] Middleware sets locale from session
- [ ] Validation messages localized
- [ ] Filament translations published and configured
- [ ] Both languages tested manually

---

## References

- **Laravel Localization**: https://laravel.com/docs/12.x/localization
- **Carbon Localization**: https://carbon.nesbot.com/docs/#api-localization
- **Filament Translations**: https://filamentphp.com/docs/4.x/support/translations
- **ICTServe**: D14 (UI/UX Design), D15 (Bilingual Requirements)

---

**Status**: ✅ Production-ready
**Last Updated**: 2025-11-01
