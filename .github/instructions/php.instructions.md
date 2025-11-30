---
applyTo: "**/*.php"
description: "PHP 8.4 coding standards: Strict typing, Property Hooks, Enums, Constructor Promotion, and PSR-12/PER compliance for ICTServe."
---

# PHP 8.4 Coding Standards & Instructions

## Purpose
Defines mandatory syntax and architectural standards for PHP development within ICTServe. This document ensures codebase consistency, type safety, and leverages modern PHP 8.4 features for cleaner implementations.

## Scope
Applies to **all** PHP files (`app/`, `config/`, `database/`, `routes/`, `tests/`).

## 1. Core Principles

### Strict Typing (Mandatory)
Every single PHP file **MUST** begin with the strict types declaration. This prevents implicit type coercion and subtle bugs.

```php
<?php

declare(strict_types=1);

namespace App\Services;
````

### PSR-12 / PER Coding Style

  - **Indentation**: 4 spaces.
  - **Line Length**: Soft limit 120 characters.
  - **Visibility**: Must be declared on all properties and methods.
  - **Control Structures**: Spaces after keywords (`if (...)`, `foreach (...)`).

## 2\. PHP 8.4 Features (Preferred Patterns)

### Property Hooks

Use Property Hooks to encapsulate logic directly within properties, reducing the need for verbose getter/setter methods.

```php
// ✅ GOOD: PHP 8.4 Property Hooks
class UserProfile
{
    public string $firstName;
    public string $lastName;

    // Virtual property with get hook
    public string $fullName {
        get => "$this->firstName $this->lastName";
    }

    // Property with set validation hook
    public string $status {
        set {
            if (!in_array($value, ['active', 'inactive'])) {
                throw new \InvalidArgumentException("Invalid status");
            }
            $this->status = $value;
        }
    }
}
```

### Constructor Property Promotion

Use constructor promotion to reduce boilerplate in Data Transfer Objects (DTOs), Events, and Value Objects.

```php
// ✅ GOOD: Constructor Promotion
class CreateAssetDTO
{
    public function __construct(
        public readonly string $name,
        public readonly string $tag,
        public readonly ?int $categoryId = null,
    ) {}
}
```

### New Array Functions

Utilize PHP 8.4 array helper functions instead of verbose loops or `array_filter`.

```php
// ✅ GOOD: array_find
$adminUser = array_find($users, fn($user) => $user->isAdmin());

// ✅ GOOD: array_all (returns true if all elements satisfy callback)
$allActive = array_all($assets, fn($asset) => $asset->isActive());
```

## 3\. Type Safety & Null Handling

### Return Types

Every function and method **MUST** specify a return type. Use `void` if nothing is returned.

```php
// ✅ GOOD
public function calculateTotal(): float
{
    return 100.00;
}

// ❌ BAD
public function calculateTotal()
{
    return 100.00;
}
```

### Null-Safe Operator

Use `?->` to chain calls safely instead of nested `if` checks.

```php
// ✅ GOOD
$categoryName = $asset?->category?->name;

// ❌ BAD
$categoryName = null;
if ($asset && $asset->category) {
    $categoryName = $asset->category->name;
}
```

### Union & Intersection Types

Be explicit about accepted types.

```php
// Union Type
public function find(int|string $id): ?Asset {}

// Intersection Type
public function export(Auditable&Exportable $entity): void {}
```

## 4\. Enums & Match Expressions

### Enums

Use Backed Enums for status, categories, and fixed lists.

```php
enum AssetStatus: string
{
    case Available = 'available';
    case Borrowed = 'borrowed';
    case Maintenance = 'maintenance';

    public function label(): string
    {
        return match($this) {
            self::Available => 'Tersedia',
            self::Borrowed => 'Dipinjam',
            self::Maintenance => 'Penyelenggaraan',
        };
    }
}
```

### Match Expressions

Prefer `match` over `switch`. It is an expression (returns a value), strict, and exhaustive.

```php
$statusColor = match ($status) {
    AssetStatus::Available => 'success',
    AssetStatus::Borrowed => 'warning',
    AssetStatus::Maintenance => 'danger',
};
```

## 5\. Modern Array Destructuring

Use short array syntax `[]` and destructuring for cleaner data handling.

```php
// Array Destructuring
['name' => $name, 'email' => $email] = $request->validated();

// Symmetric Array Destructuring (PHP 7.1+)
[$first, $second] = $items;
```

## 6\. Attributes (Annotations)

Use native PHP Attributes instead of PHPDoc annotations for metadata.

```php
#[Override]
public function toArray(Request $request): array
{
    // ...
}

#[CurrentUser]
public function index(User $user)
{
    // ...
}
```

## 7\. Common Pitfalls to Avoid

1.  **Implicit Nulls**: Do not rely on implicit nulls.
      * *Bad*: `function foo($bar = null)` (where `$bar` isn't typed `?string`)
      * *Good*: `function foo(?string $bar = null)`
2.  **Weak Comparisons**: Always use `===` and `!==`.
3.  **Variable Variables**: Avoid `$foo = 'bar'; $$foo = 'baz';`. It makes code unsearchable.
4.  **Suppressing Errors**: Never use `@` (e.g., `@file_get_contents`). Handle the exception/error properly.

## 8\. Pre-Commit Checklist

  - [ ] File starts with `declare(strict_types=1);`.
  - [ ] No `switch` statements used (replaced with `match`).
  - [ ] No `array()` syntax used (replaced with `[]`).
  - [ ] All methods have return types.
  - [ ] Code formatted via `vendor/bin/pint`.
