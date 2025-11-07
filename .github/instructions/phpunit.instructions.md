---
applyTo: "tests/**"
description: "PHPUnit 12 testing patterns, feature tests, Livewire/Volt testing, factory/seeder usage, and test assertions for ICTServe"
---

# PHPUnit 12 — ICTServe Testing Standards

## Purpose & Scope

PHPUnit 12 testing conventions for ICTServe. Covers feature tests, unit tests, Livewire/Volt component testing, database testing, factory patterns, and CI integration.

**Version Requirements**: PHP 8.3+, PHPUnit 12.x, Laravel 12.x

**Traceability**: D03 (Requirements Testing), D11 (Quality Assurance)

---

## Key Changes in PHPUnit 12

### ✅ Major Updates

- **Requires PHP 8.3+**: PHPUnit 12 no longer supports PHP 8.2
- **Attributes Only**: Doc-comment annotations (`@test`, `@dataProvider`) completely removed
- **Attributes Metadata**: All metadata now uses PHP 8 attributes (`#[...]`)
- **Simplified Test Doubles**: Removed mock generation for abstract classes/traits
- **Enhanced Attributes**: New `#[CoversNamespace]`, `#[UsesNamespace]`, `#[RequiresEnvironmentVariable]`
- **Cleaner API**: Removed 20+ deprecated methods from `MockBuilder`

### ❌ Removed Features

- `@test`, `@dataProvider`, `@covers`, `@uses` annotations (use attributes instead)
- `TestCase::iniSet()`, `TestCase::setLocale()`, `TestCase::createTestProxy()`
- `getMockForAbstractClass()`, `getMockForTrait()` (test concrete classes instead)
- `MockBuilder::addMethods()`, `MockBuilder::enableAutoload()`, `MockBuilder::enableProxyingToOriginalMethods()`
- `assertStringNotMatchesFormat()`, `assertContainsOnly()` (soft-deprecated in v11, removed in v12)
- Comma-separated `--group`, `--exclude-group` CLI options

---

## Test Creation

```bash
# Feature test (most common)
php artisan make:test AssetBorrowingTest --no-interaction

# Unit test
php artisan make:test AssetServiceTest --unit --no-interaction

# Livewire/Volt test (uses PHPUnit, not Pest)
php artisan make:test Livewire/CreateAssetTest --no-interaction
```

---

## Attributes Guide (PHPUnit 12)

### Core Test Attributes

```php
<?php declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Small;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\Before;
use PHPUnit\Framework\Attributes\After;
use PHPUnit\Framework\Attributes\RequiresPhp;
use PHPUnit\Framework\TestCase;
use App\Models\Asset;

// ✅ Use #[Test] instead of test prefix (or prefix with 'test')
#[Test]
#[TestDox('user can borrow available asset')]
#[CoversClass(AssetService::class)]
#[Small]
class AssetBorrowingTest extends TestCase
{
    #[Test]
    public function userCanBorrowAvailableAsset(): void
    {
        // Uses #[Test] attribute instead of test prefix
    }

    #[Test]
    #[RequiresPhp('^8.3')]
    public function skippedOnOlderPhpVersions(): void
    {
        // Test skipped if PHP < 8.3
    }

    // ✅ Use #[DataProvider] attribute instead of @dataProvider annotation
    #[DataProvider('borrowingScenarios')]
    #[Test]
    public function canBorrowWithMultipleScenarios(string $status, bool $expected): void
    {
        // Test logic
    }

    public static function borrowingScenarios(): array
    {
        return [
            'available asset' => ['available', true],
            'borrowed asset' => ['borrowed', false],
            'maintenance' => ['maintenance', false],
        ];
    }

    // ✅ Use #[TestWith] for inline data providers (NEW in PHPUnit 12)
    #[TestWith(['John', 25])]
    #[TestWith(['Jane', 30])]
    #[Test]
    public function testWithMultipleDataSets(string $name, int $age): void
    {
        $this->assertIsString($name);
        $this->assertIsInt($age);
    }

    // ✅ Setup/teardown using #[Before] and #[After] attributes (NEW in PHPUnit 12)
    #[Before]
    public function setUp(): void
    {
        // Runs before each test
    }

    #[After]
    public function tearDown(): void
    {
        // Runs after each test
    }

    // ✅ Test dependencies with #[Depends]
    #[Test]
    public function producerTest(): void
    {
        return 'result';
    }

    #[Depends('producerTest')]
    #[Test]
    public function consumerTest(string $result): void
    {
        $this->assertSame('result', $result);
    }

    // ✅ Group tests for organization
    #[Group('borrowing')]
    #[Test]
    public function groupedTest(): void
    {
        // Part of 'borrowing' group
    }
}
```

---

## Feature Test Pattern (PHPUnit 12)

```php
<?php declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use PHPUnit\Framework\Attributes\CoversClass;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;

#[CoversClass(AssetController::class)]
class AssetBorrowingTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[TestDox('user can borrow available asset')]
    public function userCanBorrowAvailableAsset(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'available']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset), [
                'return_by' => now()->addDays(7)->toDateString(),
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('borrowings', [
            'asset_id' => $asset->id,
            'user_id' => $user->id,
        ]);
    }

    #[Test]
    #[TestDox('user cannot borrow unavailable asset')]
    public function userCannotBorrowUnavailableAsset(): void
    {
        $user = User::factory()->create();
        $asset = Asset::factory()->create(['status' => 'borrowed']);

        $response = $this->actingAs($user)
            ->post(route('assets.borrow', $asset));

        $response->assertForbidden();
    }

    #[Test]
    #[TestDox('borrowing requires authentication')]
    public function borrowingRequiresAuthentication(): void
    {
        $asset = Asset::factory()->create();

        $response = $this->post(route('assets.borrow', $asset));

        $response->assertRedirect(route('login'));
    }
}
```

---

## Livewire/Volt Testing (PHPUnit 12)

```php
<?php declare(strict_types=1);

namespace Tests\Feature\Livewire;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;
use App\Models\Asset;
use App\Models\User;
use App\Livewire\CreateAsset;

class CreateAssetTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    #[TestDox('can create asset via Livewire component')]
    public function canCreateAsset(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateAsset::class)
            ->set('name', 'Laptop Dell')
            ->set('asset_tag', 'LT-001')
            ->set('status', 'available')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('assets', [
            'name' => 'Laptop Dell',
            'asset_tag' => 'LT-001',
        ]);
    }

    #[Test]
    #[TestDox('validates required fields in Livewire component')]
    public function validatesRequiredFields(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateAsset::class)
            ->set('name', '')
            ->call('save')
            ->assertHasErrors(['name' => 'required']);
    }

    #[Test]
    #[TestDox('shows validation error messages')]
    public function showsValidationMessages(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateAsset::class)
            ->set('asset_tag', 'INVALID-TAG-FORMAT')
            ->call('save')
            ->assertHasErrors('asset_tag');
    }

    #[Test]
    #[TestDox('emits success event after saving')]
    public function emitsSuccessEvent(): void
    {
        $user = User::factory()->create();

        Livewire::actingAs($user)
            ->test(CreateAsset::class)
            ->set('name', 'Laptop')
            ->set('asset_tag', 'LT-001')
            ->call('save')
            ->assertDispatched('asset-created');
    }
}
```

---

## Database Testing

```php
<?php declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Asset;

class AssetDatabaseTest extends TestCase
{
    use RefreshDatabase; // Reset database after each test

    #[Test]
    #[TestDox('creates asset with factory')]
    public function createsAssetWithFactory(): void
    {
        $asset = Asset::factory()->create([
            'name' => 'Laptop',
            'status' => 'available',
        ]);

        $this->assertDatabaseHas('assets', [
            'id' => $asset->id,
            'name' => 'Laptop',
        ]);
    }

    #[Test]
    #[TestDox('soft delete works correctly')]
    public function softDeleteWorks(): void
    {
        $asset = Asset::factory()->create();
        $id = $asset->id;

        $asset->delete();

        // Asset is soft-deleted (still in DB)
        $this->assertSoftDeleted('assets', ['id' => $id]);
        
        // Query builder doesn't find soft-deleted records by default
        $this->assertNull(Asset::find($id));
        
        // Can restore soft-deleted records
        $asset->restore();
        $this->assertNotNull(Asset::find($id));
    }

    #[Test]
    #[TestDox('factory creates multiple records')]
    public function factoryCreatesMultipleRecords(): void
    {
        $assets = Asset::factory(5)->create();

        $this->assertCount(5, $assets);
        $this->assertDatabaseCount('assets', 5);
    }

    #[Test]
    #[TestDox('factory with states')]
    public function factoryWithStates(): void
    {
        $availableAsset = Asset::factory()->available()->create();
        $borrowedAsset = Asset::factory()->borrowed()->create();

        $this->assertSame('available', $availableAsset->status);
        $this->assertSame('borrowed', $borrowedAsset->status);
    }
}
```

---

## Test Doubles (Mocks & Stubs) — PHPUnit 12

### Key Changes

- ❌ **Removed**: `getMockForAbstractClass()`, `getMockForTrait()` — test concrete classes instead
- ❌ **Removed**: `MockBuilder::addMethods()` — use regular methods on concrete classes
- ✅ **Use**: `createMock()` for testing interactions between objects
- ✅ **Use**: `createStub()` for isolating code under test from dependencies

```php
<?php declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;
use App\Services\PaymentService;
use App\Services\EmailService;

class PaymentServiceTest extends TestCase
{
    #[Test]
    public function testWithStub(): void
    {
        // ✅ Use createStub() to isolate code under test
        $emailService = $this->createStub(EmailService::class);
        $emailService->method('send')->willReturn(true);

        // Test code that uses $emailService
        $paymentService = new PaymentService($emailService);
        $result = $paymentService->processPayment(100);

        $this->assertTrue($result);
    }

    #[Test]
    public function testWithMock(): void
    {
        // ✅ Use createMock() to test interactions between objects
        $emailService = $this->createMock(EmailService::class);
        
        // Expect send() to be called once
        $emailService->expects($this->once())
            ->method('send')
            ->willReturn(true);

        $paymentService = new PaymentService($emailService);
        $paymentService->processPayment(100);
    }

    #[Test]
    public function testWithPartialMock(): void
    {
        // ✅ Use createPartialMock() to mock some methods while keeping others real
        $paymentService = $this->createPartialMock(
            PaymentService::class,
            ['validatePayment']
        );

        $paymentService->method('validatePayment')->willReturn(true);

        $result = $paymentService->processPayment(100);
        $this->assertTrue($result);
    }

    #[Test]
    public function testExceptionHandling(): void
    {
        // ✅ Mock to throw exception
        $emailService = $this->createMock(EmailService::class);
        $emailService->method('send')
            ->willThrowException(new \Exception('Send failed'));

        $paymentService = new PaymentService($emailService);

        $this->expectException(\Exception::class);
        $paymentService->processPayment(100);
    }
}
```

---

## Running Tests (PHPUnit 12)

```bash
# All tests
php artisan test

# All tests with verbose output
php artisan test --verbose

# Specific file
php artisan test tests/Feature/AssetBorrowingTest.php

# Specific test method by filter
php artisan test --filter=testMethod

# Tests with #[Small] attribute only
php artisan test --group=small

# Tests with #[Group('borrowing')] attribute
php artisan test --group=borrowing

# Exclude specific groups
php artisan test --exclude-group=large

# Parallel execution (faster)
php artisan test --parallel

# Parallel with specific processes
php artisan test --parallel --processes=4

# Code coverage report
php artisan test --coverage

# Coverage with minimum threshold
php artisan test --coverage --min=80

# TestDox output (human-readable)
php artisan test --testdox

# Stop on first failure
php artisan test --stop-on-failure

# Stop after N failures
php artisan test --stop-on-defect=5

# Only run tests that failed last time
php artisan test --only-fails
```

---

## Best Practices (PHPUnit 12)

### ✅ DO

1. **Use Attributes**: `#[Test]`, `#[DataProvider]`, `#[TestWith]`, `#[CoversClass]`
2. **Use Factories**: `Asset::factory()->create()` instead of manual setup
3. **Test All Paths**: Happy path, error path, edge cases, security checks
4. **Descriptive Names**: `#[TestDox('user can borrow available asset')]`
5. **Single Responsibility**: One behavior per test
6. **AAA Pattern**: Arrange → Act → Assert
7. **Use Stubs for Dependencies**: `createStub()` for isolated testing
8. **Use Mocks for Interactions**: `createMock()` for testing communication between objects
9. **RefreshDatabase**: Reset database between tests
10. **Test One Thing**: Each test verifies one specific behavior

### ❌ DON'T

1. **Don't Use Doc-Comment Annotations**: `@test`, `@dataProvider`, `@covers` → use attributes instead
2. **Don't Create Mocks for Abstract Classes**: Test concrete classes instead
3. **Don't Mix Stubs and Expectations**: Use `createStub()` XOR `createMock()`, not both
4. **Don't Test Implementation Details**: Test behavior, not internal methods
5. **Don't Use Test-Specific Methods**: Don't add test methods to production code
6. **Don't Ignore Deprecation Warnings**: Soft-deprecated methods will be removed in next version
7. **Don't Test Multiple Behaviors**: Keep tests focused on single responsibility
8. **Don't Use Global State**: Backup/restore global state when necessary
9. **Don't Hardcode Test Data**: Use factories for consistency
10. **Don't Create Mock Abstractions**: Mock only interfaces/concrete classes

---

## Data Providers — PHPUnit 12

```php
<?php declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\DataProviderExternal;
use PHPUnit\Framework\Attributes\TestWith;
use PHPUnit\Framework\Attributes\TestWithJson;
use PHPUnit\Framework\TestCase;

class DataProviderTest extends TestCase
{
    // ✅ Method-based data provider (most flexible)
    #[DataProvider('statusProvider')]
    public function testWithMethodProvider(string $status, bool $expected): void
    {
        $this->assertIsBool($expected);
    }

    public static function statusProvider(): array
    {
        return [
            'available' => ['available', true],
            'borrowed' => ['borrowed', false],
            'maintenance' => ['maintenance', false],
        ];
    }

    // ✅ Inline data provider with #[TestWith] (NEW)
    #[TestWith(['available', true])]
    #[TestWith(['borrowed', false])]
    #[TestWith(['maintenance', false])]
    public function testWithInlineProvider(string $status, bool $expected): void
    {
        $this->assertIsBool($expected);
    }

    // ✅ JSON-based data provider (NEW)
    #[TestWithJson('[0, 0, 0]')]
    #[TestWithJson('[1, 1, 2]')]
    public function testAdditionWithJson(int $a, int $b, int $expected): void
    {
        $this->assertSame($expected, $a + $b);
    }

    // ✅ External data provider (in different class)
    #[DataProviderExternal(ExternalDataProvider::class, 'statusProvider')]
    public function testWithExternalProvider(string $status, bool $expected): void
    {
        $this->assertIsBool($expected);
    }
}

// External data provider class
class ExternalDataProvider
{
    public static function statusProvider(): array
    {
        return [
            'available' => ['available', true],
            'borrowed' => ['borrowed', false],
        ];
    }
}
```

---

## Code Coverage (PHPUnit 12)

```php
<?php declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
use PHPUnit\Framework\Attributes\CoversFunction;
use PHPUnit\Framework\Attributes\CoversNamespace;
use PHPUnit\Framework\Attributes\UsesClass;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use App\Services\AssetService;

// ✅ Specify coverage for the entire class
#[CoversClass(AssetService::class)]
class AssetServiceTest extends TestCase
{
    // ✅ Override for specific test
    #[CoversMethod(AssetService::class, 'borrow')]
    public function testBorrowMethod(): void
    {
        // Tests specific method
    }

    // ✅ Specify allowed dependencies
    #[UsesClass(\Exception::class)]
    public function testWithException(): void
    {
        // This test uses \Exception, which is allowed
    }

    // ✅ Test that doesn't contribute to coverage
    #[CoversNothing]
    public function testWithoutCoverage(): void
    {
        // This test is not counted toward coverage
    }

    // ✅ Cover namespace (NEW in PHPUnit 12)
    #[CoversNamespace('App\\Services')]
    public function testNamespaceCoverage(): void
    {
        // All tests in App\Services namespace contribute to coverage
    }
}
```

---

## Migration from PHPUnit 11 → 12

### Step 1: Update composer.json
```bash
composer require --dev phpunit/phpunit:^12
```

### Step 2: Convert Annotations to Attributes

**Before (PHPUnit 11):**
```php
/**
 * @test
 * @dataProvider statusProvider
 * @covers AssetService
 */
public function testBorrow()
{
    // ...
}
```

**After (PHPUnit 12):**
```php
#[Test]
#[DataProvider('statusProvider')]
#[CoversClass(AssetService::class)]
public function testBorrow(): void
{
    // ...
}
```

### Step 3: Remove Old Mocking Patterns

**Before (PHPUnit 11):**
```php
$mock = $this->getMockForAbstractClass(SomeAbstractClass::class);
```

**After (PHPUnit 12):**
```php
// Test concrete class instead, or use interface:
$stub = $this->createStub(SomeInterface::class);
```

### Step 4: Update Lifecycle Methods

**Before (PHPUnit 11):**
```php
public function setUp(): void { }
public function tearDown(): void { }
```

**After (PHPUnit 12):**
```php
#[Before]
public function setUp(): void { }

#[After]
public function tearDown(): void { }
```

---

## Running Tests in CI/CD

```bash
# Run all tests in CI pipeline
php artisan test --parallel --coverage --min=80

# Run specific test suite
php artisan test tests/Feature --stop-on-failure

# Generate coverage report
php artisan test --coverage-html=coverage

# Export JUnit XML for CI systems
php artisan test --log-junit=junit.xml

# Run tests with TestDox for reporting
php artisan test --testdox > test-results.txt
```

---

## Common Assertions (PHPUnit 12)

```php
// Identity
$this->assertSame($expected, $actual);
$this->assertNotSame($unexpected, $actual);

// Equality
$this->assertEquals($expected, $actual);
$this->assertNotEquals($unexpected, $actual);

// Boolean
$this->assertTrue($condition);
$this->assertFalse($condition);

// Type
$this->assertIsString($value);
$this->assertIsInt($value);
$this->assertIsArray($value);
$this->assertInstanceOf(MyClass::class, $object);

// Null
$this->assertNull($value);
$this->assertNotNull($value);

// Collections
$this->assertEmpty($collection);
$this->assertNotEmpty($collection);
$this->assertCount(5, $collection);
$this->assertContains($needle, $haystack);

// Database (Laravel)
$this->assertDatabaseHas('users', ['email' => 'test@example.com']);
$this->assertDatabaseCount('users', 10);
$this->assertSoftDeleted('users', ['id' => $id]);

// HTTP responses (Laravel)
$response->assertStatus(200);
$response->assertRedirect();
$response->assertForbidden();
```

---

## References & Documentation

- **PHPUnit 12 Official**: https://phpunit.de/documentation.html
- **PHPUnit 12 Attributes**: https://docs.phpunit.de/en/12.0/attributes.html
- **PHPUnit 12 Release Notes**: https://phpunit.de/announcements/phpunit-12.html
- **Laravel Testing**: https://laravel.com/docs/12.x/testing
- **ICTServe**: D03 (Requirements Testing), D11 (Quality Assurance)

---

**Status**: ✅ Production-ready for PHPUnit 12  
**Last Updated**: 2025-11-07  
**Version**: PHPUnit 12.x, Laravel 12.x, PHP 8.3+
