---
mode: agent
---

# Test Generation Workflow

You are an expert at writing comprehensive PHPUnit tests for Laravel 12 applications. Generate complete test coverage for existing code.

## Context

**ICTServe Testing Standards:**
- PHPUnit 11
- Test pyramid: Unit (60-80%), Integration (20-30%), E2E (5-10%)
- Coverage target: 80% minimum, 100% for critical paths
- AAA pattern (Arrange, Act, Assert)

## Test Generation Steps

### 1. Analyze Target Code

**Task:** Identify what needs testing

**Ask User:**
- What file/class/method should be tested?
- What are the critical business logic paths?
- What edge cases should be covered?
- Are there existing tests to reference?

**Read Target Code:**
```bash
# Use grep_search to analyze code structure
```

**Output:** Brief summary of code functionality and test scope

---

### 2. Identify Test Scenarios

**Task:** List all test cases needed

**Categories:**

**Happy Paths:**
- Normal successful execution
- Valid inputs produce expected outputs

**Error Paths:**
- Invalid inputs trigger validation errors
- Missing data returns appropriate errors
- Database constraints violated

**Edge Cases:**
- Boundary values (min/max)
- Empty collections
- Null values
- Race conditions (if applicable)

**Authorization:**
- Authenticated users can perform actions
- Unauthorized users blocked
- Role-based permissions enforced

**Output:** Bulleted list of test scenarios

---

### 3. Generate Feature Tests

**Task:** Create comprehensive feature tests

**Generate Test File:**
```bash
php artisan make:test [FeatureName]Test --no-interaction
```

**Template:**
```php
declare(strict_types=1);

namespace Tests\Feature;

use App\Models\User;
use App\Models\ModelName;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FeatureNameTest extends TestCase

    use RefreshDatabase;

    /** @test */
    public function user_can_create_resource_with_valid_data(): void

        // Arrange
        $user = User::factory()->create();
        $user->givePermissionTo('create-resources');

        $data = [
            'field_name' => 'Valid Value',
            'user_id' => $user->id,
      ;

        // Act
        $response = $this->actingAs($user)
            ->post(route('resources.store'), $data);

        // Assert
        $response->assertRedirect(route('resources.index'));
        $this->assertDatabaseHas('table_name', $data);


    /** @test */
    public function validation_fails_with_invalid_data(): void

        $user = User::factory()->create();
        $user->givePermissionTo('create-resources');

        $response = $this->actingAs($user)
            ->post(route('resources.store'), [
                'field_name' => '', // Invalid: required
          );

        $response->assertSessionHasErrors('field_name');


    /** @test */
    public function unauthorized_user_cannot_create_resource(): void

        $user = User::factory()->create();
        // No permission granted

        $response = $this->actingAs($user)
            ->post(route('resources.store'), [
                'field_name' => 'Value',
          );

        $response->assertForbidden();


```

**Checklist:**
- [ ] `use RefreshDatabase` trait
- [ ] Arrange-Act-Assert pattern
- [ ] Descriptive test method names
- [ ] Happy path tests
- [ ] Validation tests
- [ ] Authorization tests
- [ ] Edge case tests

---

### 4. Generate Unit Tests

**Task:** Test isolated business logic

**Generate Unit Test:**
```bash
php artisan make:test [ClassName]Test --unit --no-interaction
```

**Template:**
```php
declare(strict_types=1);

namespace Tests\Unit;

use App\Services\ServiceName;
use PHPUnit\Framework\TestCase;

class ServiceNameTest extends TestCase

    /** @test */
    public function it_calculates_total_correctly(): void

        $service = new ServiceName();

        $result = $service->calculateTotal(100, 10);

        $this->assertEquals(110, $result);


    /** @test */
    public function it_throws_exception_for_negative_values(): void

        $this->expectException(\InvalidArgumentException::class);

        $service = new ServiceName();
        $service->calculateTotal(-100, 10);


```

---

### 5. Generate Livewire/Volt Tests

**Task:** Test Livewire components

**Template:**
```php
use Livewire\Volt\Volt;

/** @test */
public function component_renders_correctly(): void

    Volt::test('component-name')
        ->assertSee('Expected Text');


/** @test */
public function component_action_updates_state(): void

    $user = User::factory()->create();

    Volt::test('component-name')
        ->actingAs($user)
        ->set('propertyName', 'New Value')
        ->call('actionMethod')
        ->assertSet('propertyName', 'Updated Value')
        ->assertDispatched('event-name');

```

---

### 6. Generate Filament Resource Tests

**Task:** Test Filament CRUD operations

**Template:**
```php
use App\Filament\Resources\ModelNameResource;
use App\Filament\Resources\ModelNameResource\Pages\ListModelNames;
use Livewire\Livewire;

/** @test */
public function can_list_resources(): void

    $user = User::factory()->create();
    $user->givePermissionTo('view-models');

    $models = ModelName::factory()->count(3)->create();

    Livewire::actingAs($user)
        ->test(ListModelNames::class)
        ->assertCanSeeTableRecords($models);


/** @test */
public function can_create_resource(): void

    $user = User::factory()->create();
    $user->givePermissionTo('create-models');

    $data = [
        'field_name' => 'Test Value',
  ;

    Livewire::actingAs($user)
        ->test(CreateModelName::class)
        ->fillForm($data)
        ->call('create')
        ->assertHasNoErrors();

    $this->assertDatabaseHas('table_name', $data);

```

---

### 7. Database Testing

**Task:** Test database interactions

**Template:**
```php
/** @test */
public function soft_delete_works_correctly(): void

    $model = ModelName::factory()->create();

    $model->delete();

    $this->assertSoftDeleted($model);
    $this->assertDatabaseHas('table_name', [
        'id' => $model->id,
  );


/** @test */
public function relationship_is_eager_loaded(): void

    $model = ModelName::factory()->create();

    $result = ModelName::with('user')->find($model->id);

    $this->assertTrue($result->relationLoaded('user'));

```

---

### 8. Run Tests

**Task:** Execute tests and verify passing

**Run All Tests:**
```bash
php artisan test
```

**Run Specific Test File:**
```bash
php artisan test tests/Feature/FeatureNameTest.php
```

**Run with Coverage:**
```bash
php artisan test --coverage --min=80
```

---

### 9. Test Coverage Report

**Task:** Generate and analyze coverage

**Check Coverage:**
```bash
php artisan test --coverage
```

**Identify Gaps:**
- Untested methods
- Uncovered branches
- Missing edge cases

**Add Missing Tests:** Repeat steps 3-6 for uncovered code

---

## Test Quality Checklist

**Code Quality:**
- [ ] All tests use `declare(strict_types=1);`
- [ ] AAA pattern (Arrange, Act, Assert) followed
- [ ] Descriptive test method names (reads like English)
- [ ] One assertion per test (or related group)
- [ ] No logic in tests (no if/else, loops)

**Coverage:**
- [ ] Happy paths tested
- [ ] Error paths tested
- [ ] Edge cases tested
- [ ] Authorization tested
- [ ] Validation tested

**Data:**
- [ ] `RefreshDatabase` trait used
- [ ] Factories used for test data
- [ ] Database assertions used (`assertDatabaseHas`)
- [ ] Soft deletes tested (`assertSoftDeleted`)

**Isolation:**
- [ ] Tests don't depend on each other
- [ ] Tests can run in any order
- [ ] Mock external services
- [ ] No shared state between tests

---

## Completion Report

**After Generating Tests:**

**Run Tests:**
```bash
php artisan test
```

**Check Coverage:**
```bash
php artisan test --coverage
```

**Report to User:**
```
Test Generation Complete:
✅ Feature tests: X files
✅ Unit tests: X files
✅ Livewire tests: X files
✅ Total assertions: X
✅ Coverage: X%

All tests passing: [YES/NO]
Coverage target met (80%): [YES/NO]

Next steps: [Identify gaps or suggest improvements]
```

---

## References

- `.github/instructions/phpunit.instructions.md`
- `.github/instructions/testing.instructions.md`
- D11 (Technical Design Documentation)
