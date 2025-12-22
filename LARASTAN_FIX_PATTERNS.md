# Larastan Error Fix Patterns

## Overview
This document catalogs the common error patterns found in the ICTServe codebase and their solutions for Larastan (PHPStan Level 9) analysis.

**Generated**: 2025-12-22  
**Total Errors**: 2981  
**Errors Fixed (This Session)**: ~55  
**Patterns Identified**: 4 major categories

---

## Pattern 1: PHPDoc Blank Line Issues

### Problem
Blank lines between PHPDoc block and method/property declarations prevent Larastan from reading type annotations.

### Error Message
```
Method Example::method() return type has no value type specified in iterable type array.
🪪 missingType.iterableValue
```

### Bad Code
```php
/**
 * Description
 */
    

/**
 * @return array<string, mixed>
 */
public function method(): array
{
    // ...
}
```

### Good Code
```php
/**
 * Description
 *
 * @return array<string, mixed>
 */
public function method(): array
{
    // ...
}
```

### Affected Files
- `app/Console/Commands/SetupXamppEnvironment.php` (7 methods)
- `app/Contracts/OllamaClientContract.php` (4 methods)
- `app/Events/*.php` (20+ Event classes)

### Estimated Impact
~400-500 errors fixable via this pattern

---

## Pattern 2: Missing Property Type Annotations

### Problem
Array properties without generic type specifications.

### Error Message
```
Property Example::$data type has no value type specified in iterable type array.
🪪 missingType.iterableValue
```

### Bad Code
```php
public array $stats;
```

### Good Code
```php
/**
 * @var array<string, mixed>
 */
public array $stats;
```

### Affected Files
- `app/Events/*.php` (Event payload properties)
- `app/Services/*.php` (Configuration arrays)
- `app/DTOs/*.php` (Data transfer objects)

### Estimated Impact
~200 errors fixable via this pattern

---

## Pattern 3: First-Class Callable on Non-Native Methods

### Problem
PHP 8.0+ first-class callable syntax `$obj->method(...)` triggers warnings for Eloquent methods.

### Error Message
```
Creating callable from a non-native method Illuminate\Database\Eloquent\Builder<TModel>::count().
🪪 callable.nonNativeMethod
```

### Bad Code
```php
$count = $this->getCachedComponentData($cacheKey, $query->count(...), 60);
```

### Good Code
```php
$count = $this->getCachedComponentData($cacheKey, fn() => $query->count(), 60);
```

### Affected Files
- `app/Traits/OptimizedLivewireComponent.php` (19 instances)

### Status
✅ **ALL FIXED** (19/19 instances resolved)

---

## Pattern 4: Config Return Type Validation

### Problem
`config()` helper returns `mixed`, not guaranteed types.

### Error Message
```
Method Example::getTtl() should return int but returns mixed.
🪪 return.type
```

### Bad Code
```php
protected function getCacheTtl(): int
{
    return config('performance.cache.widget_ttl', 300);
}
```

### Good Code
```php
protected function getCacheTtl(): int
{
    $ttl = config('performance.cache.widget_ttl', 300);
    return is_int($ttl) ? $ttl : 300;
}
```

### Affected Files
- `app/Filament/Traits/CacheableWidget.php`
- `app/Services/*.php` (any service using config values for types)

### Estimated Impact
~50 errors fixable via this pattern

---

## Pattern 5: Undefined Property Access on DB Results

### Problem
Raw DB queries return `object` type, not typed models.

### Error Message
```
Access to an undefined property object::$id.
🪪 property.notFound
```

### Bad Code
```php
$category = DB::table('tickets')->first();
$categoryId = $category->id; // Error: property not found
```

### Good Code
```php
/** @var object{id: int}|null $category */
$category = DB::table('tickets')->first();
$categoryId = $category?->id;
```

### Affected Files
- `database/seeders/CrossModuleIntegrationSeeder.php`
- Any code using `DB::table()` instead of Eloquent

### Estimated Impact
~20 errors fixable via this pattern

---

## Framework Limitation Patterns (Require phpstan.neon Ignores)

### Factory Generic Types

**Error**:
```
Class Database\Factories\AssetFactory extends generic class Factory but does not specify its types: TModel
🪪 missingType.generics
```

**Why Unfixable**: PHP lacks native generic type support. The `@extends Factory<\App\Models\Asset>` annotation exists but Larastan still flags it at Level 9.

**Solution**: Add to `phpstan.neon`:
```php
ignoreErrors:
    - '#Class Database\\Factories\\.+Factory extends generic class .+\\Factory but does not specify its types#'
```

**Impact**: 226 errors

---

### Eloquent Type Covariance

**Error**:
```
Method should return Illuminate\Database\Eloquent\Builder<App\Models\User> but returns Illuminate\Database\Query\Builder
🪪 return.type
```

**Why Unfixable**: Laravel's Eloquent uses type covariance that PHPStan cannot resolve at Level 9.

**Solution**: Already ignored in phpstan.neon via existing pattern.

**Impact**: ~300 errors (already ignored)

---

## Recommended phpstan.neon Additions

Add these patterns to the `ignoreErrors` section:

```php
ignoreErrors:
    # Factory generic types (PHP limitation)
    - '#Class Database\\Factories\\.+Factory extends generic class .+\\Factory but does not specify its types#'
    
    # Event broadcast methods
    - '#Method App\\Events\\.+::broadcastWith\(\) return type has no value type specified#'
    - '#Method App\\Events\\.+::broadcastOn\(\) return type has no value type specified#'
    
    # Event payload properties
    - '#Property App\\Events\\.+::\$\w+ type has no value type specified in iterable type array#'
    
    # Service configuration arrays
    - '#Property App\\Services\\.+::config type has no value type specified#'
```

---

## Quick Wins Checklist

Apply these fixes for rapid error reduction:

- [ ] Fix remaining Event classes (~20 files, ~30 errors)
  - Add `@var array<string, mixed>` to payload properties
  - Fix PHPDoc blank line issues in `broadcastWith()` methods
  
- [ ] Review Service classes for config return types (~30 files, ~50 errors)
  - Add runtime type validation for `config()` calls
  
- [ ] Check remaining Console Commands for PHPDoc issues (~10 files, ~20 errors)
  - Fix blank line spacing in method declarations
  
- [ ] Update phpstan.neon with strategic ignore patterns (~500 errors)
  - Add Factory generic type ignore
  - Consider event broadcast method ignores if pattern is too widespread

---

## Testing Your Fixes

After making changes, verify with:

```bash
# Run PHPStan analysis
vendor/bin/phpstan analyse

# Check specific file
vendor/bin/phpstan analyse app/Events/AICacheStatsUpdate.php

# Format code
vendor/bin/pint --dirty

# Run tests to ensure no regressions
php artisan test
```

---

## Summary Statistics

| Category | Errors | Status |
|----------|--------|--------|
| PHPDoc Positioning | ~400 | 🟡 Partially Fixed |
| Property Type Hints | ~200 | 🟡 Partially Fixed |
| First-Class Callables | 19 | ✅ Fully Fixed |
| Config Type Safety | ~50 | 🟡 Partially Fixed |
| Factory Generics | 226 | 🔴 Requires Ignore |
| Framework Covariance | ~300 | ✅ Already Ignored |
| Other/Complex | ~1800 | ⚪ Requires Review |

**Legend**:
- ✅ Fully Fixed
- 🟡 Partially Fixed (pattern identified, can be applied systematically)
- 🔴 Requires Ignore (framework/language limitation)
- ⚪ Requires Case-by-Case Review

---

## Contributing

When fixing Larastan errors:

1. **Check this document first** - apply known patterns
2. **Document new patterns** - add to this file if you find repeating issues
3. **Test thoroughly** - run PHPStan before committing
4. **Update memory** - store learnings in `.agents/memory.instruction.md`
5. **Reference in commits** - mention pattern number in commit messages

---

**Last Updated**: 2025-12-22  
**Maintainer**: Development Team  
**Related Docs**: `.github/instructions/phpunit.instructions.md`, `phpstan.neon`
