---
applyTo: "tests/**"
description: "PHPUnit 12 testing standards: Attributes, Feature/Unit test structure, Livewire Volt testing, and CI gates for ICTServe."
---

# PHPUnit 12 Testing Instructions

**Purpose**
Defines mandatory testing standards for ICTServe. Enforces **PHPUnit 12** conventions (Attributes over Annotations), coverage requirements, and specific patterns for testing Laravel features and Volt components.

**Scope**
Applies to `tests/Feature`, `tests/Unit`, and all test-related traits.

## 1. PHPUnit 12 Migration (Attributes)

**Mandatory Change**: You **MUST** use PHP 8 Attributes instead of PHPDoc annotations.

| Legacy (Forbidden) | Modern (Required) |
| :--- | :--- |
| `/** @test */` | `#[Test]` |
| `/** @group fast */` | `#[Group('fast')]` |
| `/** @dataProvider provider */` | `#[DataProvider('provider')]` |
| `/** @depends testBase */` | `#[Depends('testBase')]` |

**Example**:
```php
<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

#[Group('core')]
class CalculationTest extends TestCase
{
    #[Test]
    public function it_calculates_total_correctly(): void
    {
        // ...
    }
}
````

## 2\. Feature Tests (End-to-End)

Use Feature tests for HTTP endpoints, Controllers, and full request cycles.

**Structure**:

1.  **Arrange**: Setup data (Factories).
2.  **Act**: Make request (`get`, `post`, `actingAs`).
3.  **Assert**: Check Status, JSON structure, Database state.

<!-- end list -->

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\Ticket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class CreateTicketTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function authenticated_user_can_create_ticket(): void
    {
        // Arrange
        $user = User::factory()->create();
        $data = ['subject' => 'Server Down', 'priority' => 'high'];

        // Act
        $response = $this->actingAs($user)
                         ->postJson(route('api.v1.tickets.store'), $data);

        // Assert
        $response->assertCreated()
                 ->assertJsonFragment(['subject' => 'Server Down']);

        $this->assertDatabaseHas('tickets', [
            'subject' => 'Server Down',
            'user_id' => $user->id
        ]);
    }
}
```

## 3\. Unit Tests (Isolated Logic)

Use Unit tests for **Services**, **DTOs**, and complex **Model** logic.

  * **Do NOT** touch the database in Unit tests (unless strictly necessary).
  * **Mock** dependencies.

<!-- end list -->

```php
#[Test]
public function it_calculates_sla_deadline(): void
{
    $service = new SlaService();
    $deadline = $service->calculate('high', now());
    
    $this->assertEquals(now()->addHours(4), $deadline);
}
```

## 4\. Livewire Volt Testing

Use the `Volt` testing facade for all UI components.

**Pattern**:

```php
use Livewire\Volt\Volt;
use App\Models\User;

#[Test]
public function user_can_save_profile(): void
{
    $user = User::factory()->create();

    Volt::test('profile.edit-form')
        ->actingAs($user)
        ->set('name', 'New Name')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('profile-updated');

    $this->assertEquals('New Name', $user->refresh()->name);
}
```

## 5\. Database & Factories

### RefreshDatabase

Always use the `RefreshDatabase` trait in Feature tests to ensure a clean state.

### Factories

Use Model Factories for data setup. Do not manually insert DB records.

```php
// ✅ GOOD
$asset = Asset::factory()->active()->create();

// ❌ BAD
$asset = new Asset();
$asset->name = 'Test';
$asset->save();
```

## 6\. Coverage & Quality Gates

  * **Minimum Coverage**: 80% Line Coverage for Business Logic.
  * **Command**: `php artisan test --coverage --min=80`
  * **CI/CD**: Tests failing in CI will block the merge.

## 7\. Pre-Commit Checklist

  - [ ] Replaced all `@test` annotations with `#[Test]`.
  - [ ] Used `RefreshDatabase` for DB tests.
  - [ ] Asserted both Happy Path (Success) and Unhappy Path (Validation Error/403).
  - [ ] Used `Volt::test()` for UI components.
