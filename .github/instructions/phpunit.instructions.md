---
applyTo: "tests/**"
description: "PHPUnit 11 testing patterns, feature tests, Livewire/Volt testing, factory/seeder usage, and test assertions for ICTServe"
---

# PHPUnit 11 — ICTServe Testing Standards

## Purpose & Scope

PHPUnit 11 testing conventions for ICTServe. Covers feature tests, unit tests, Livewire/Volt component testing, database testing, factory patterns, and CI integration.

**Traceability**: D03 (Requirements Testing), D11 (Quality Assurance)

---

## Test Creation

```bash
# Feature test (most common)
php artisan make:test AssetBorrowingTest --no-interaction

# Unit test
php artisan make:test AssetServiceTest --unit --no-interaction

# Livewire/Volt test
php artisan make:test --pest Livewire/CreateAssetTest --no-interaction
```

---

## Feature Test Pattern

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\Asset, User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AssetBorrowingTest extends TestCase

    use RefreshDatabase;

    public function test_user_can_borrow_available_asset(): void
    
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'available']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset), [
                'return_by' => now()->addDays(7)->toDateString(),
          );

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

---

## Livewire/Volt Testing

```php
<?php

use App\Models\User, Asset;
use Livewire\Volt\Volt;
use function Pest\Laravel\actingAs, assertDatabaseHas;

test('can create asset', function () 
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('assets.create-asset')
        ->set('name', 'Laptop Dell')
        ->set('assetTag', 'LT-001')
        ->set('status', 'available')
        ->call('save')
        ->assertHasNoErrors();

    assertDatabaseHas('assets', ['asset_tag' => 'LT-001']);
);

test('validates required fields', function () 
    $user = User::factory()->create();

    Volt::actingAs($user)
        ->test('assets.create-asset')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
);
```

---

## Database Testing

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetTest extends TestCase

    use RefreshDatabase; // Reset database after each test

    public function test_creates_asset_with_factory(): void
    
        $asset = Asset::factory()->create([
            'name' => 'Laptop',
            'status' => 'available',
      );

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Laptop',
      );


    public function test_soft_delete_works(): void
    
        $asset = Asset::factory()->create();
        $asset->delete();

        $this->assertSoftDeleted('assets', ['id' => $asset->id]);


```

---

## Running Tests

```bash
# All tests
php artisan test

# Specific file
php artisan test tests/Feature/AssetBorrowingTest.php

# Specific test method
php artisan test --filter=test_user_can_borrow_available_asset

# Parallel execution
php artisan test --parallel

# With coverage
php artisan test --coverage --min=80
```

---

## Best Practices

1. **Use Factories**: `Asset::factory()->create()` instead of manual creation
2. **Test All Paths**: Happy path, error path, edge cases
3. **Descriptive Names**: `test_user_can_borrow_available_asset`
4. **Single Responsibility**: One behavior per test
5. **AAA Pattern**: Arrange → Act → Assert

---

## References

- **PHPUnit Docs**: https://phpunit.de/documentation.html
- **Laravel Testing**: https://laravel.com/docs/12.x/testing
- **ICTServe**: D03 (Requirements Testing), D11 (Quality Assurance)

---

**Status**: ✅ Production-ready  
**Last Updated**: 2025-11-01
