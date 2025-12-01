# General Testing Instructions

**Purpose**
Defines the testing strategy and quality assurance gates for ICTServe.

**Scope**
Applies to `tests/`, `app/`, and CI pipelines.

## 1. Test Pyramid & Strategy

| Type | Tool | Target Coverage | Purpose |
| :--- | :--- | :--- | :--- |
| **Unit** | PHPUnit 12 | 100% (Critical logic) | Isolated business logic (Services/DTOs). |
| **Feature** | PHPUnit 12 | 80% (Overall) | HTTP requests, Controllers, Integration. |
| **Component** | Volt::test | 100% (UI logic) | Livewire/Volt interactions. |
| **E2E** | Playwright | Critical Flows | Browser-based user journeys (Login, Submit). |

## 2. TDD Workflow (Red-Green-Refactor)

1.  **Red**: Write a failing test using PHPUnit Attributes.
    ```php
    #[Test]
    public function it_calculates_total(): void {
        $this->assertEquals(100, Service::total(50, 2));
    }
    ```
2.  **Green**: Write minimal code to pass.
3.  **Refactor**: Optimize while keeping tests green.

## 3. Best Practices

### Isolation
* Use `RefreshDatabase` for all DB tests.
* Use `Http::fake()` for external APIs.
* Use `Notification::fake()` / `Queue::fake()` for side effects.

### Factories over Fixtures
Always use Model Factories.
```php
$user = User::factory()->admin()->create();
````

### Naming

  * **Method**: `it_does_something_specific`.
  * **Attribute**: `#[TestDox("It sends an email when ticket is created")]`.

## 4\. Quality Assurance Gates

**Local Dev**:

```bash
php artisan test --parallel
```

**CI Pipeline**:

  * Must pass **100%** of tests.
  * Must meet **80%** code coverage.
  * Must pass **Larastan** (Level 9).

## 5\. Traceability

Link tests to requirements in the DocBlock (if complex).

```php
/**
 * @see SRS-FR-012 Ticket Assignment
 */
#[Test]
public function it_assigns_ticket_to_staff(): void {}
