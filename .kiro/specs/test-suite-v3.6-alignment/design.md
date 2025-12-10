# Test Suite v3.6.0 Alignment - Design Document

## Overview

This design document outlines the approach for updating all pre-existing PHPUnit tests in the ICTServe project to align with the v3.6.0 system iteration. The update ensures tests accurately reflect the True Hybrid Architecture, Dual Audit System, Bahasa Melayu-only UI, and all features documented in D00-D17.

**Reference Documents:**

- D00_SYSTEM_OVERVIEW.md - System vision (v3.6.0)
- D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Software requirements
- D09_DATABASE_DOCUMENTATION.md - Database schema and dual audit
- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization standards
- D15_LANGUAGE_MS_EN.md - Language localization (Bahasa Melayu sahaja)
- PHPUnit 11.x Documentation - Attribute-based testing

## Architecture

The test suite alignment follows a systematic transformation approach across multiple dimensions:

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                    TEST SUITE v3.6.0 ALIGNMENT ARCHITECTURE                  │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐          │
│  │  LANGUAGE       │    │  ARCHITECTURE   │    │  SYNTAX         │          │
│  │  ALIGNMENT      │    │  ALIGNMENT      │    │  MODERNIZATION  │          │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤          │
│  │ • BM-only UI    │    │ • Hybrid flows  │    │ • PHP 8 attrs   │          │
│  │ • Translation   │    │ • Dual audit    │    │ • #[Test]       │          │
│  │   keys          │    │ • RBAC roles    │    │ • #[DataProvider]│         │
│  │ • Email content │    │ • Notifications │    │ • Imports       │          │
│  └─────────────────┘    └─────────────────┘    └─────────────────┘          │
│           │                     │                     │                      │
│           └─────────────────────┼─────────────────────┘                      │
│                                 ▼                                            │
│                    ┌─────────────────────────┐                               │
│                    │   UPDATED TEST SUITE    │                               │
│                    │   (v3.6.0 Compliant)    │                               │
│                    └─────────────────────────┘                               │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Test Categories Requiring Updates

1. **Feature Tests** (`tests/Feature/`)
   - HTTP endpoint tests
   - Livewire component tests
   - Filament resource tests
   - Integration tests
   - Service tests

2. **Unit Tests** (`tests/Unit/`)
   - Service class tests
   - Model tests
   - Middleware tests
   - Helper function tests

3. **Browser Tests** (`tests/Browser/`)
   - Laravel Dusk accessibility tests
   - E2E workflow tests

### Transformation Patterns

#### Pattern 1: Bahasa Melayu Content Assertions

**Before (English):**

```php
$response->assertSee('Submit Ticket');
$response->assertSee('Your ticket has been submitted successfully');
```

**After (Bahasa Melayu):**

```php
$response->assertSee(__('helpdesk.submit_ticket')); // 'Hantar Tiket'
$response->assertSee(__('helpdesk.ticket_submitted_success'));
```

#### Pattern 2: Hybrid Data Association Testing

**Before (Single path):**

```php
public function test_user_can_submit_ticket(): void
{
    $user = User::factory()->create();
    $this->actingAs($user)->post('/helpdesk/submit', $data);
}
```

**After (Dual path - Authenticated + Guest):**

```php
#[Test]
public function authenticatedUserCanSubmitTicketWithUserIdLinked(): void
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/helpdesk/submit', $data);
    
    $ticket = HelpdeskTicket::latest()->first();
    $this->assertEquals($user->id, $ticket->user_id);
}

#[Test]
public function guestCanSubmitTicketWithNullUserId(): void
{
    $response = $this->post('/helpdesk/submit', $guestData);
    
    $ticket = HelpdeskTicket::latest()->first();
    $this->assertNull($ticket->user_id);
    $this->assertEquals($guestData['submitter_email'], $ticket->submitter_email);
}
```

#### Pattern 3: Dual Audit System Testing

**Before (Single audit):**

```php
public function test_ticket_creation_is_audited(): void
{
    // Only checking one audit system
}
```

**After (Dual audit):**

```php
#[Test]
public function ticketCreationIsAuditedInBothSystems(): void
{
    $ticket = HelpdeskTicket::factory()->create();
    
    // Owen-it compliance audit (field-level)
    $this->assertDatabaseHas('audits', [
        'auditable_type' => HelpdeskTicket::class,
        'auditable_id' => $ticket->id,
    ]);
    
    // Spatie activity log (operational)
    $this->assertDatabaseHas('activity_log', [
        'subject_type' => HelpdeskTicket::class,
        'subject_id' => $ticket->id,
    ]);
}
```

#### Pattern 4: Role-Based Access Control Testing

**Before (Basic role check):**

```php
public function test_admin_can_access_panel(): void
{
    $admin = User::factory()->admin()->create();
    $this->actingAs($admin)->get('/admin')->assertOk();
}
```

**After (Comprehensive RBAC):**

```php
#[Test]
#[DataProvider('roleAccessProvider')]
public function roleHasCorrectAccessPermissions(string $role, string $route, int $expectedStatus): void
{
    $user = User::factory()->withRole($role)->create();
    $response = $this->actingAs($user)->get($route);
    $response->assertStatus($expectedStatus);
}

public static function roleAccessProvider(): array
{
    return [
        'staff can access dashboard' => ['staff', '/dashboard', 200],
        'staff cannot access admin' => ['staff', '/admin', 403],
        'admin can access admin panel' => ['admin', '/admin', 200],
        'admin cannot access telescope' => ['admin', '/telescope', 403],
        'superuser can access telescope' => ['superuser', '/telescope', 200],
        'superuser can access pulse' => ['superuser', '/pulse', 200],
    ];
}
```

#### Pattern 5: PHP 8 Attribute Conversion

**Before (PHPDoc):**

```php
/**
 * @test
 * @dataProvider validEmailProvider
 */
public function test_valid_email_is_accepted($email): void
```

**After (PHP 8 Attributes):**

```php
#[Test]
#[DataProvider('validEmailProvider')]
public function validEmailIsAccepted(string $email): void
```

### Required Imports

Each test file must include appropriate imports:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
```

## Data Models

No database schema changes required. Tests must align with existing v3.6.0 schema:

- `users` - staff, admin, superuser roles
- `helpdesk_tickets` - nullable `user_id` FK for hybrid association
- `loan_applications` - nullable `user_id` FK for hybrid association
- `audits` - owen-it compliance audit trail
- `activity_log` - spatie operational logging
- `personal_access_tokens` - Sanctum API tokens
- `notification_preferences` - multi-channel notification settings

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: Bahasa Melayu Content Validation

*For any* UI test that asserts text content (form labels, buttons, messages, error messages), the asserted text SHALL be in Bahasa Melayu, either as literal strings or translation key references from `lang/ms/`.
**Validates: Requirements 1.1, 1.2, 1.4, 1.5**

### Property 2: Hybrid Submission Path Validation

*For any* submission test (helpdesk tickets, loan applications), the test suite SHALL include both authenticated (user_id linked) and guest (user_id=NULL with submitter_* fields) test cases.
**Validates: Requirements 2.1, 2.2, 2.4, 2.5**

### Property 3: Authenticated Form Auto-Fill Validation

*For any* authenticated user accessing a submission form, the test SHALL verify that profile data (name, email, phone, department, grade) is pre-filled from the user's profile.
**Validates: Requirements 2.3**

### Property 4: Email Domain Restriction Validation

*For any* self-registration attempt, the test SHALL verify that only @motac.gov.my email addresses are accepted, and all other domains are rejected with appropriate error messages.
**Validates: Requirements 3.1, 3.5**

### Property 5: Flexible Login Validation

*For any* login attempt, the test SHALL verify that both full email format (<user@motac.gov.my>) and short username format (user) successfully authenticate the same user.
**Validates: Requirements 3.3**

### Property 6: Dual Audit System Validation

*For any* auditable model change, the test SHALL verify that both owen-it audit records (with old/new values) and spatie activity log entries are created.
**Validates: Requirements 4.1, 4.2, 4.3**

### Property 7: Role-Based Access Validation

*For any* protected route or resource, the test SHALL verify that staff, admin, and superuser roles have correct access permissions as defined in D00 §5.1.
**Validates: Requirements 5.1, 5.2, 5.3**

### Property 8: Notification System Validation

*For any* notification-triggering action, the test SHALL verify that notifications are created according to user preferences (immediate/daily/weekly digest) and stored in the database for in-app display.
**Validates: Requirements 6.1, 6.2, 6.3, 6.5**

### Property 9: API Token Authentication Validation

*For any* API endpoint protected by Sanctum, the test SHALL verify that valid tokens with appropriate abilities grant access, invalid/expired tokens are rejected, and rate limits are enforced.
**Validates: Requirements 7.1, 7.2, 7.3, 7.4**

### Property 10: Filament Component Validation

*For any* Filament resource test, the test SHALL verify that actions execute correctly, tables support filtering/sorting/pagination, forms validate and submit properly, and dashboard widgets render.
**Validates: Requirements 9.2, 9.3, 9.4, 9.5**

### Property 11: Cross-Module Data Linking Validation

*For any* cross-module operation (e.g., damaged asset return), the test SHALL verify that related records are properly linked (e.g., helpdesk ticket linked to loan_transaction).
**Validates: Requirements 10.2, 10.5**

### Property 12: Test Count Preservation

*For any* test file before and after update, the number of test methods SHALL remain identical (unless new tests are explicitly added for new v3.6.0 features).
**Validates: Requirements 8.5, 11.2**

## Error Handling

1. **Syntax Errors**: If updates introduce syntax errors, the file should be flagged for manual review
2. **Missing Imports**: Automated detection of missing PHPUnit attribute imports
3. **Test Failures**: Any test failures after updates indicate logic changes that need investigation
4. **Translation Key Errors**: Missing translation keys should be reported for addition to lang/ms/
5. **Model Relationship Errors**: Outdated relationships should be updated to current schema

## Testing Strategy

### Dual Testing Approach

The test suite uses both unit tests and property-based testing:

**Unit Tests:**

- Verify specific examples and edge cases
- Test integration points between components
- Validate error conditions and boundary values

**Property-Based Tests:**

- Verify universal properties across all inputs
- Use PHPUnit with data providers for parameterized testing
- Ensure correctness properties hold for generated test data

### Testing Framework

- **Framework**: PHPUnit 11.5.44
- **Attributes**: PHP 8 native attributes (#[Test], #[DataProvider], etc.)
- **Livewire Testing**: Livewire::test() for component testing
- **Filament Testing**: livewire() helper for Filament resources
- **Database**: RefreshDatabase trait for isolation

### Test Categories to Update

Based on codebase analysis, tests requiring updates fall into these categories:

1. **Language-Related Tests**: Tests asserting English UI text
2. **Authentication Tests**: Tests not covering hybrid flows
3. **Audit Tests**: Tests only checking single audit system
4. **RBAC Tests**: Tests not covering all four roles
5. **Notification Tests**: Tests not covering multi-channel preferences
6. **API Tests**: Tests not using Sanctum token authentication
7. **Syntax Tests**: Tests using PHPDoc annotations instead of PHP 8 attributes

### Validation Approach

1. **Pre-update**: Run `php artisan test` to establish baseline
2. **Per-file validation**: After each file update, verify no syntax errors
3. **Post-update**: Run `php artisan test` to verify all tests pass
4. **Manual review**: Spot-check updated files for proper formatting and alignment

### Property-Based Test Implementation

Each correctness property will be implemented as a PHPUnit test with data providers:

```php
/**
 * **Feature: test-suite-v3.6-alignment, Property 4: Email Domain Restriction Validation**
 */
#[Test]
#[DataProvider('emailDomainProvider')]
public function emailDomainRestrictionIsEnforced(string $email, bool $shouldPass): void
{
    $response = $this->post('/register', ['email' => $email, ...]);
    
    if ($shouldPass) {
        $response->assertSessionHasNoErrors('email');
    } else {
        $response->assertSessionHasErrors('email');
    }
}

public static function emailDomainProvider(): array
{
    return [
        'valid motac email' => ['user@motac.gov.my', true],
        'invalid gmail' => ['user@gmail.com', false],
        'invalid yahoo' => ['user@yahoo.com', false],
        'invalid subdomain' => ['user@sub.motac.gov.my', false],
    ];
}
```
