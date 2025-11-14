---
applyTo: "tests/**,app/**"
description: "General testing best practices, TDD patterns, coverage targets, and quality assurance for ICTServe"
---

# Testing Best Practices — ICTServe Standards

## Purpose & Scope

General testing methodology, TDD patterns, coverage requirements, and quality assurance practices for ICTServe enterprise application.

**Traceability**: D03 (Testing Requirements), D11 (Quality Metrics)

---

## Test Pyramid

```
       /\
      /  \  E2E Tests (5-10%)
     /____\
    /      \  Integration Tests (20-30%)
   /________\
  /          \  Unit Tests (60-80%)
 /____________\
```

**Distribution**:
- **Unit Tests**: 60-80% — Fast, isolated, test single units
- **Integration Tests**: 20-30% — Test component interactions
- **E2E Tests**: 5-10% — Test complete user flows (Playwright/Dusk)

---

## TDD Workflow

**Red → Green → Refactor**:

1. **Red**: Write failing test first
```php
public function test_calculates_total_correctly(): void

    $service = new OrderService();
    $total = $service->calculateTotal(100, 3);
    $this->assertEquals(300, $total); // FAILS (method doesn't exist)

```

2. **Green**: Write minimal code to pass
```php
class OrderService

    public function calculateTotal(int $price, int $quantity): int

        return $price * $quantity; // PASSES


```

3. **Refactor**: Improve code quality while keeping tests green

---

## Coverage Targets

**ICTServe Requirements**:
- **Minimum Coverage**: 80% overall
- **Critical Paths**: 100% (authentication, authorization, payment, data integrity)
- **New Features**: 90%+ before merge

**Check Coverage**:
```bash
php artisan test --coverage --min=80
```

---

## Test Naming Conventions

**Descriptive Test Names**:
```php
// ✅ GOOD: Clear intent
public function test_user_can_borrow_available_asset(): void
public function test_user_cannot_borrow_unavailable_asset(): void
public function test_validates_unique_asset_tag(): void

// ❌ BAD: Vague
public function test_borrow(): void
public function test_validation(): void
```

---

## Arrange-Act-Assert (AAA)

```php
public function test_user_can_create_asset(): void

    // ARRANGE: Set up test data
    $user = User::factory()->create();
    $data = ['name' => 'Laptop', 'asset_tag' => 'LT-001'];

    // ACT: Execute action
    $response = $this->actingAs($user)
        ->post(route('assets.store'), $data);

    // ASSERT: Verify outcome
    $response->assertRedirect();
    $this->assertDatabaseHas('assets', ['asset_tag' => 'LT-001']);

```

---

## Test Isolation

**Each Test Should Be Independent**:
```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class AssetTest extends TestCase

    use RefreshDatabase; // Fresh database for each test

    public function test_creates_asset(): void

        Asset::factory()->create(['name' => 'Laptop']);
        $this->assertDatabaseCount('assets', 1);


    public function test_deletes_asset(): void

        // Previous test doesn't affect this one
        $asset = Asset::factory()->create();
        $asset->delete();
        $this->assertSoftDeleted('assets', ['id' => $asset->id]);


```

---

## Mocking & Stubbing

**When to Mock**:
- External APIs
- Slow operations (email, file uploads)
- Non-deterministic behavior (random, time)

**Example**:
```php
use Illuminate\Support\Facades\Http;

public function test_fetches_external_data(): void

    Http::fake([
        'api.example.com/*' => Http::response(['data' => 'success'], 200),
  );

    $service = new ExternalApiService();
    $result = $service->fetch();

    $this->assertEquals('success', $result['data']);

```

---

## Testing Exceptions

```php
use Illuminate\Validation\ValidationException;

public function test_throws_exception_for_invalid_data(): void

    $this->expectException(ValidationException::class);

    $service = new AssetService();
    $service->create(['name' => '']); // Missing required field

```

---

## Continuous Integration

**Run in CI** (`.github/workflows/ci.yml`):
```yaml
- name: Run Tests
  run: php artisan test --parallel --coverage --min=80
```

**Pre-Commit Hook** (`.git/hooks/pre-commit`):
```bash
#!/bin/bash
php artisan test --filter=changed
```

---

## Best Practices

1. **Test Behavior, Not Implementation** — Focus on what, not how
2. **Keep Tests Fast** — Use in-memory databases, mock external calls
3. **One Assertion Per Concept** — Test one thing at a time
4. **Use Factories** — Avoid manual object creation
5. **Test Edge Cases** — Null, empty, boundary values
6. **Clean Test Data** — Use `RefreshDatabase` trait

---

## References

- **Laravel Testing Docs**: https://laravel.com/docs/12.x/testing
- **PHPUnit**: https://phpunit.de
- **ICTServe**: D03 (Testing Requirements), D11 (Quality Metrics)

---

**Status**: ✅ Production-ready
**Last Updated**: 2025-11-01
