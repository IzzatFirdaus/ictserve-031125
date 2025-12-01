---
applyTo: "tests/**,phpunit.xml,pest.php"
description: "Testing standards: PHPUnit 12/Pest patterns, TDD workflow, E2E testing with Playwright, and coverage gates for ICTServe."
---

# Testing Instructions

**Purpose**
Defines mandatory testing standards for ICTServe. Ensures code reliability through a strict Test-Driven Development (TDD) workflow and comprehensive coverage gates.

**Scope**
Applies to `tests/Feature`, `tests/Unit`, `tests/Browser` (Playwright), and all CI pipelines.

## 1. Test Pyramid & Strategy

| Level | Tool | Target Coverage | Purpose |
| :--- | :--- | :--- | :--- |
| **Unit** | PHPUnit 12 / Pest | 100% (Critical Logic) | Isolate business logic (Services, DTOs, Helpers). No DB access. |
| **Feature** | PHPUnit 12 / Pest | 80% (Overall) | HTTP requests, Controllers, Integration, Database state. |
| **Component** | Volt::test | 100% (UI Logic) | Livewire/Volt interactions and state changes. |
| **E2E** | Playwright | Critical Flows | Browser-based user journeys (Login, Submission). |

## 2. PHPUnit 12 Standards

**Mandatory**: Use PHP Attributes instead of Annotations.

| Legacy (Forbidden) | Modern (Required) |
| :--- | :--- |
| `/** @test */` | `#[Test]` |
| `/** @group fast */` | `#[Group('fast')]` |
| `/** @dataProvider */` | `#[DataProvider('methodName')]` |

**Example (Feature Test)**:
```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class AssetBorrowingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function user_can_borrow_available_asset(): void
    {
        // Arrange
        $user = User::factory()->create();
        $asset = \App\Models\Asset::factory()->available()->create();

        // Act
        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset));

        // Assert
        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'asset_id' => $asset->id,
            'user_id' => $user->id,
        ]);
    }
}
````

## 3\. Livewire Volt Testing

Use the `Volt` testing facade for all UI components. Tests must verify state, actions, and events.

```php
use Livewire\Volt\Volt;
use App\Models\User;

#[Test]
public function it_validates_required_fields(): void
{
    $user = User::factory()->create();

    Volt::test('assets.create-form')
        ->actingAs($user)
        ->set('name', '') // Invalid
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
}
```

## 4\. E2E Testing (Playwright)

Use Playwright for critical user journeys that JavaScript interactions cannot fully mock.

**Command**: `npm run test:e2e`

**Spec Example**:

```typescript
import { test, expect } from '@playwright/test';

test('user can login and view dashboard', async ({ page }) => {
    await page.goto('/login');
    await page.fill('input[name="email"]', 'staff@motac.gov.my');
    await page.fill('input[name="password"]', 'password');
    await page.click('button[type="submit"]');

    await expect(page).toHaveURL('/dashboard');
    await expect(page.locator('h1')).toContainText('Dashboard');
});
```

## 5\. Mocking & Isolation

### External Services

**NEVER** make real HTTP calls in Unit/Feature tests. Use `Http::fake()`.

```php
use Illuminate\Support\Facades\Http;

#[Test]
public function it_syncs_with_external_api(): void
{
    Http::fake([
        '[api.external.com/](https://api.external.com/)*' => Http::response(['status' => 'ok'], 200),
    ]);

    // ... Run code that calls API
}
```

### Time Travel

Use `travelTo()` to test time-sensitive logic (SLAs, overdue notices).

```php
$this->travelTo(now()->addDays(15));
// Assert overdue email was sent
```

## 6\. Quality Gates

**Local Development**:
Run strictly before commit.

```bash
php artisan test --parallel
```

**CI Pipeline Requirements**:

1.  All tests must pass (Green).
2.  No skipped tests in `main` branch.
3.  Coverage must meet the 80% threshold.

## 7\. Pre-Commit Checklist

  - [ ] Used `RefreshDatabase` trait for DB tests.
  - [ ] Used `#[Test]` attribute on all test methods.
  - [ ] Used Factories (`User::factory()`) instead of manual DB insertion.
  - [ ] Mocked all external APIs and Queues (`Queue::fake()`).
  - [ ] Asserted unhappy paths (validation errors, 403 Forbidden).
