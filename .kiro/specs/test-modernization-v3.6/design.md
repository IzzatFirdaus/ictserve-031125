# Test Modernization v3.6.0 - Design Document

## Overview

This design document outlines the approach for modernizing all PHPUnit tests in the ICTServe project to use PHP 8 attributes instead of PHPDoc annotations. The modernization ensures compatibility with PHPUnit 11.x and follows Laravel 12 best practices.

**Reference Documents:**

- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization standards
- PHPUnit 11.x Documentation - Attribute-based testing
- PSR-12 - Extended Coding Style Guide

## Architecture

The test modernization follows a file-by-file transformation approach:

```text
┌─────────────────────────────────────────────────────────────────────────┐
│                      TEST FILE TRANSFORMATION                            │
├─────────────────────────────────────────────────────────────────────────┤
│  Input: Test file with PHPDoc annotations                                │
│  ├── @test annotations                                                   │
│  ├── @dataProvider annotations                                           │
│  ├── @depends annotations                                                │
│  ├── @group annotations                                                  │
│  └── @covers annotations                                                 │
├─────────────────────────────────────────────────────────────────────────┤
│  Transformation Rules:                                                   │
│  1. Add PHP 8 attribute imports                                          │
│  2. Convert @test → #[Test]                                              │
│  3. Convert @dataProvider → #[DataProvider]                              │
│  4. Convert @depends → #[Depends]                                        │
│  5. Convert @group → #[Group]                                            │
│  6. Convert @covers → #[CoversClass] or #[CoversMethod]                  │
│  7. Preserve @trace and @traceability comments                           │
│  8. Maintain test logic unchanged                                        │
├─────────────────────────────────────────────────────────────────────────┤
│  Output: Test file with PHP 8 attributes                                 │
│  ├── use PHPUnit\Framework\Attributes\Test;                              │
│  ├── #[Test] attributes on test methods                                  │
│  └── Preserved documentation and test logic                              │
└─────────────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Test File Categories

1. **Feature Tests** (`tests/Feature/`)
   - Integration tests for HTTP endpoints
   - Livewire component tests
   - Database interaction tests

2. **Unit Tests** (`tests/Unit/`)
   - Service class tests
   - Model tests
   - Helper function tests

3. **Browser Tests** (`tests/Browser/`)
   - Laravel Dusk accessibility tests

### Transformation Patterns

#### Pattern 1: @test Annotation to #[Test] Attribute

**Before:**

```php
/**
 * Test description
 *
 * @test
 *
 * @trace Requirements 1.1
 */
public function method_name(): void
```

**After:**

```php
/**
 * Test description
 *
 * @trace Requirements 1.1
 */
#[Test]
public function method_name(): void
```

#### Pattern 2: test_ Prefix to #[Test] Attribute

**Before:**

```php
public function test_method_name(): void
```

**After:**

```php
#[Test]
public function method_name(): void
```

#### Pattern 3: Standalone @test to #[Test] Attribute

**Before:**

```php
/** @test */
public function method_name(): void
```

**After:**

```php
#[Test]
public function method_name(): void
```

### Required Imports

Each test file must include the appropriate PHPUnit attribute imports:

```php
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\CoversMethod;
```

## Data Models

No database changes required. This is a code-only transformation.

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Test Count Preservation

*For any* test file before and after conversion, the number of test methods SHALL remain identical.
**Validates: Requirements 5.2**

### Property 2: Test Assertion Preservation

*For any* test method before and after conversion, the assertions and test logic SHALL remain unchanged.
**Validates: Requirements 5.2**

### Property 3: Import Statement Presence

*For any* test file using `#[Test]` attribute, the file SHALL contain `use PHPUnit\Framework\Attributes\Test;` import.
**Validates: Requirements 1.4**

### Property 4: Documentation Preservation

*For any* test method with `@trace` or `@traceability` tags, these tags SHALL be preserved after conversion.
**Validates: Requirements 2.1, 2.2**

### Property 5: Strict Types Declaration

*For any* test file, the file SHALL begin with `declare(strict_types=1);` statement.
**Validates: Requirements 3.1**

## Error Handling

1. **Syntax Errors**: If conversion introduces syntax errors, the file should be flagged for manual review
2. **Missing Imports**: Automated detection of missing attribute imports
3. **Test Failures**: Any test failures after conversion indicate logic changes that need investigation

## Testing Strategy

### Validation Approach

1. **Pre-conversion**: Run `php artisan test` to establish baseline
2. **Per-file validation**: After each file conversion, verify no syntax errors
3. **Post-conversion**: Run `php artisan test` to verify all tests pass
4. **Manual review**: Spot-check converted files for proper formatting

### Test Categories to Update

Based on codebase analysis:

1. **Files with @test annotations**: `tests/Feature/ThemeToggleTest.php`
2. **Files with test_ prefix**: Multiple files in `tests/Unit/Services/`, `tests/Unit/Models/`
3. **Files already using #[Test]**: Verify consistency and add missing imports if needed
