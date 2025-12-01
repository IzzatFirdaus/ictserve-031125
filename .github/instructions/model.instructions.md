---
applyTo: "app/Models/**,database/migrations/**"
description: "Eloquent model standards: Strict typing, relationship patterns, casting methods, and attribute-based features for ICTServe."
---

# Eloquent Model Instructions

**Purpose**
Defines mandatory standards for Eloquent Models in ICTServe. Ensures data integrity, type safety, and consistency across the domain layer.

**Scope**
Applies to all files in `app/Models` and related database migrations.

## 1. Core Principles

1.  **Strict Typing**: All models must begin with `declare(strict_types=1);`.
2.  **Explicit Definition**: Do not rely on "magic" where explicit definitions (like table names or relationships) improve clarity.
3.  **Fat Models / Thin Controllers**: Encapsulate data logic (Scopes, Accessors) in the Model, but move complex business logic to **Services**.
4.  **Traceability**: Complex models must reference their D09 (Database) definition.

## 2. Model Structure

### Standard Template
```php
<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use OwenIt\Auditing\Contracts\Auditable;

class Asset extends Model implements Auditable
{
    use HasFactory;
    use SoftDeletes;
    use \OwenIt\Auditing\Auditable;

    // Explicit Table Name (Optional but recommended for legacy mapping)
    protected $table = 'assets';

    // Mass Assignment Protection
    protected $fillable = [
        'name',
        'asset_tag',
        'status',
        'category_id',
    ];

    // Eager Load by Default (Use sparingly)
    // protected $with = ['category'];
}
````

## 3\. Casting & Attributes

### Casts Method (Laravel 11+)

Use the `casts()` method instead of the `$casts` property for better type support and dynamic casting.

```php
protected function casts(): array
{
    return [
        'is_active' => 'boolean',
        'acquired_at' => 'datetime',
        'price' => 'decimal:2',
        'status' => AssetStatus::class, // Enum casting
        'metadata' => 'array',
        'password' => 'hashed',
    ];
}
```

### Accessors & Mutators

Use **Laravel Attribute** classes or **PHP 8.4 Property Hooks** (if supported by the serialization layer).

**Laravel Style**:

```php
use Illuminate\Database\Eloquent\Casts\Attribute;

protected function name(): Attribute
{
    return Attribute::make(
        get: fn (string $value) => ucfirst($value),
        set: fn (string $value) => strtolower($value),
    );
}
```

## 4\. Relationships

**Rules**:

1.  Always specify the return type (`BelongsTo`, `HasMany`, etc.).
2.  Always define the inverse relationship in the related model.

<!-- end list -->

```php
public function category(): BelongsTo
{
    return $this->belongsTo(Category::class);
}

public function loans(): HasMany
{
    return $this->hasMany(Loan::class);
}
```

## 5\. Scopes & Observers

### Attribute-Based Observers

Register observers directly on the model using PHP Attributes.

```php
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use App\Observers\AssetObserver;

#[ObservedBy(AssetObserver::class)]
class Asset extends Model
{
    // ...
}
```

### Local Scopes

Use Scopes for common filtering logic to keep controllers clean.

```php
public function scopeActive(Builder $query): void
{
    $query->where('status', 'active');
}

// Usage: Asset::active()->get();
```

## 6\. Migration Standards

### Foreign Keys

Always use constrained foreign keys to ensure referential integrity.

```php
$table->foreignId('category_id')
      ->constrained()
      ->cascadeOnDelete();
```

### Soft Deletes

Mandatory for all core entities (Assets, Tickets, Users) to preserve audit trails.

```php
$table->softDeletes();
```

## 7\. Pre-Commit Checklist

  - [ ] `declare(strict_types=1)` is present.
  - [ ] `$fillable` is defined (no `$guarded = []` allowed in production).
  - [ ] Relationships have return types.
  - [ ] Enums are used for status fields.
  - [ ] `casts()` method is used instead of `$casts` property.
