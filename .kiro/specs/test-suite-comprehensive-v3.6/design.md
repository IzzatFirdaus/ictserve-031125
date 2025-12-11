# Test Suite Comprehensive v3.6.0 - Design Document

## Overview

This design document outlines the unified approach for modernizing and aligning all PHPUnit tests in the ICTServe project with the v3.6.0 system iteration. The comprehensive update combines PHP 8 attribute modernization with system feature alignment, ensuring tests accurately reflect the True Hybrid Architecture, Dual Audit System, Bahasa Melayu-only UI, and all features documented in D00-D17.

**Reference Documents:**

- D00_SYSTEM_OVERVIEW.md - System vision (v3.6.0)
- D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md - Software requirements
- D09_DATABASE_DOCUMENTATION.md - Database schema and dual audit
- D10_SOURCE_CODE_DOCUMENTATION.md - Code organization standards
- D15_LANGUAGE_MS_EN.md - Language localization (Bahasa Melayu sahaja)
- PHPUnit 11.x Documentation - Attribute-based testing
- PSR-12 - Extended Coding Style Guide

## Architecture

The comprehensive test suite transformation follows a multi-dimensional approach combining syntax modernization with system feature alignment:

```text
┌─────────────────────────────────────────────────────────────────────────────┐
│                 COMPREHENSIVE TEST SUITE v3.6.0 TRANSFORMATION               │
├─────────────────────────────────────────────────────────────────────────────┤
│                                                                              │
│  ┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐          │
│  │  SYNTAX         │    │  LANGUAGE       │    │  ARCHITECTURE   │          │
│  │  MODERNIZATION  │    │  ALIGNMENT      │    │  ALIGNMENT      │          │
│  ├─────────────────┤    ├─────────────────┤    ├─────────────────┤          │
│  │ • PHP 8 attrs   │    │ • BM-only UI    │    │ • Hybrid flows  │          │
│  │ • #[Test]       │    │ • Translation   │    │ • Dual audit    │          │
│  │ • #[DataProvider]│   │   keys          │    │ • RBAC roles    │          │
│  │ • Imports       │    │ • Email content │    │ • Notifications │          │
│  │ • PSR-12        │    │ • Error msgs    │    │ • API tokens    │          │
│  └─────────────────┘    └─────────────────┘    └─────────────────┘          │
│           │                     │                     │                      │
│           └─────────────────────┼─────────────────────┘                      │
│                                 ▼                                            │
│                    ┌─────────────────────────┐                               │
│                    │   COMPREHENSIVE         │                               │
│                    │   TEST SUITE v3.6.0     │                               │
│                    │   (Fully Aligned)       │                               │
│                    └─────────────────────────┘                               │
│                                                                              │
└─────────────────────────────────────────────────────────────────────────────┘
```

## Components and Interfaces

### Test Categories Requiring Comprehensive Updates

1. **Feature Tests** (`tests/Feature/`)
   - HTTP endpoint tests with BM content validation
   - Livewire component tests with hybrid flows
   - Filament resource tests with RBAC validation
   - Integration tests with dual audit verification
   - Service tests with API token authentication

2. **Unit Tests** (`tests/Unit/`)
   - Service class tests with modern attributes
   - Model tests with hybrid data association
   - Middleware tests with BM localization
   - Helper function tests with strict typing

3. **Browser Tests** (`tests/Browser/`)
   - Laravel Dusk accessibility tests with BM content
   - E2E workflow tests with hybrid authentication

### Comprehensive Transformation Patterns

#### Pattern 1: Complete PHP 8 Attribute Conversion

**Before (Legacy PHPDoc):**

```php
/**
 * Test user registration with email verification
 *
 * @test
 * @dataProvider emailProvider
 * @group authentication
 * @trace Requirements 3.1
 */
public function test_user_registration_with_email_verification($email): void
```

**After (Modern PHP 8 Attributes):**

```php
/**
 * Test user registration with email verification
 *
 * @trace Requirements 3.1
 */
#[Test]
#[DataProvider('emailProvider')]
#[Group('authentication')]
public function userRegistrationWithEmailVerification(string $email): void
```

#### Pattern 2: Bahasa Melayu Content Validation

**Before (English assertions):**

```php
$response->assertSee('Submit Ticket');
$response->assertSee('Your ticket has been submitted successfully');
$response->assertSee('Please fill in all required fields');
```

**After (Bahasa Melayu assertions):**

```php
$response->assertSee(__('helpdesk.submit_ticket')); // 'Hantar Tiket'
$response->assertSee(__('helpdesk.ticket_submitted_success')); // 'Tiket berjaya dihantar'
$response->assertSee(__('validation.required_fields')); // 'Sila isi semua medan yang diperlukan'
```

#### Pattern 3: Hybrid Architecture Testing

**Before (Single authentication path):**

```php
#[Test]
public function userCanSubmitTicket(): void
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)->post('/helpdesk/submit', $data);
    $response->assertRedirect('/dashboard');
}
```

**After (Dual path - Authenticated + Guest):**

```php
#[Test]
public function authenticatedUserCanSubmitTicketWithUserIdLinked(): void
{
    $user = User::factory()->create();
    $data = HelpdeskTicket::factory()->make()->toArray();
    
    $response = $this->actingAs($user)->post('/helpdesk/submit', $data);
    
    $ticket = HelpdeskTicket::latest()->first();
    $this->assertEquals($user->id, $ticket->user_id);
    $this->assertNull($ticket->submitter_email); // Profile data used instead
    $response->assertRedirect('/dashboard');
}

#[Test]
public function guestCanSubmitTicketWithNullUserId(): void
{
    $data = HelpdeskTicket::factory()->make([
        'submitter_name' => 'Ahmad Bin Ali',
        'submitter_email' => 'ahmad@motac.gov.my',
        'submitter_phone' => '03-12345678',
    ])->toArray();
    
    $response = $this->post('/helpdesk/submit', $data);
    
    $ticket = HelpdeskTicket::latest()->first();
    $this->assertNull($ticket->user_id);
    $this->assertEquals($data['submitter_email'], $ticket->submitter_email);
    $response->assertSee(__('helpdesk.guest_submission_success'));
}
```

#### Pattern 4: Dual Audit System Validation

**Before (Single audit system):**

```php
#[Test]
public function ticketCreationIsAudited(): void
{
    $ticket = HelpdeskTicket::factory()->create();
    
    $this->assertDatabaseHas('audits', [
        'auditable_type' => HelpdeskTicket::class,
        'auditable_id' => $ticket->id,
    ]);
}
```

**After (Comprehensive dual audit):**

```php
#[Test]
public function ticketCreationIsAuditedInBothSystems(): void
{
    $user = User::factory()->create();
    $this->actingAs($user);
    
    $ticket = HelpdeskTicket::factory()->create();
    
    // Owen-it compliance audit (field-level tracking)
    $this->assertDatabaseHas('audits', [
        'auditable_type' => HelpdeskTicket::class,
        'auditable_id' => $ticket->id,
        'event' => 'created',
    ]);
    
    // Spatie activity log (operational logging)
    $this->assertDatabaseHas('activity_log', [
        'subject_type' => HelpdeskTicket::class,
        'subject_id' => $ticket->id,
        'causer_type' => User::class,
        'causer_id' => $user->id,
        'description' => 'created',
    ]);
}
```

#### Pattern 5: Comprehensive RBAC Testing

**Before (Basic role check):**

```php
#[Test]
public function adminCanAccessPanel(): void
{
    $admin = User::factory()->admin()->create();
    $response = $this->actingAs($admin)->get('/admin');
    $response->assertOk();
}
```

**After (Comprehensive four-role RBAC):**

```php
#[Test]
#[DataProvider('roleAccessProvider')]
public function roleHasCorrectAccessPermissions(string $role, string $route, int $expectedStatus, ?string $expectedContent = null): void
{
    $user = User::factory()->withRole($role)->create();
    $response = $this->actingAs($user)->get($route);
    
    $response->assertStatus($expectedStatus);
    
    if ($expectedContent) {
        $response->assertSee(__($expectedContent));
    }
}

public static function roleAccessProvider(): array
{
    return [
        // Staff role permissions
        'staff can access dashboard' => ['staff', '/dashboard', 200, 'dashboard.welcome'],
        'staff cannot access admin' => ['staff', '/admin', 403, null],
        'staff cannot access telescope' => ['staff', '/telescope', 403, null],
        'staff cannot access pulse' => ['staff', '/pulse', 403, null],
        
        // Admin role permissions
        'admin can access admin panel' => ['admin', '/admin', 200, null],
        'admin can access pulse' => ['admin', '/pulse', 200, null],
        'admin cannot access telescope' => ['admin', '/telescope', 403, null],
        
        // Superuser role permissions
        'superuser can access telescope' => ['superuser', '/telescope', 200, null],
        'superuser can access pulse' => ['superuser', '/pulse', 200, null],
        'superuser can access admin' => ['superuser', '/admin', 200, null],
        'superuser can access dashboard' => ['superuser', '/dashboard', 200, 'dashboard.welcome'],
    ];
}
```

#### Pattern 6: Multi-Channel Notification Testing

**Before (Basic notification):**

```php
#[Test]
public function ticketCreationSendsNotification(): void
{
    $ticket = HelpdeskTicket::factory()->create();
    
    Notification::assertSentTo($ticket->assignee, TicketAssignedNotification::class);
}
```

**After (Multi-channel with preferences):**

```php
#[Test]
#[DataProvider('notificationPreferenceProvider')]
public function ticketCreationRespectsNotificationPreferences(string $emailFrequency, bool $inAppEnabled, int $expectedEmailCount, int $expectedDatabaseCount): void
{
    $assignee = User::factory()->create();
    $assignee->notificationPreferences()->create([
        'email_frequency' => $emailFrequency,
        'in_app_notifications' => $inAppEnabled,
    ]);
    
    $ticket = HelpdeskTicket::factory()->create(['assigned_to' => $assignee->id]);
    
    // Verify email notifications based on preference
    if ($emailFrequency === 'immediate') {
        Notification::assertSentTo($assignee, TicketAssignedNotification::class);
    } else {
        Notification::assertNotSentTo($assignee, TicketAssignedNotification::class);
    }
    
    // Verify database notifications
    $this->assertDatabaseCount('notifications', $expectedDatabaseCount);
    
    if ($inAppEnabled) {
        $this->assertDatabaseHas('notifications', [
            'notifiable_type' => User::class,
            'notifiable_id' => $assignee->id,
            'type' => TicketAssignedNotification::class,
        ]);
    }
}

public static function notificationPreferenceProvider(): array
{
    return [
        'immediate email + in-app' => ['immediate', true, 1, 1],
        'daily digest + in-app' => ['daily', true, 0, 1],
        'weekly digest + in-app' => ['weekly', true, 0, 1],
        'immediate email only' => ['immediate', false, 1, 0],
        'daily digest only' => ['daily', false, 0, 0],
    ];
}
```

### Required Imports and File Structure

Each test file must include comprehensive imports and proper structure:

```php
<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Depends;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use App\Models\User;
use App\Models\HelpdeskTicket;
use App\Notifications\TicketAssignedNotification;

class ComprehensiveHelpdeskTest extends TestCase
{
    use RefreshDatabase;
    
    // Test methods with #[Test] attributes
}
```

## Data Models

Tests must align with existing v3.6.0 schema without requiring changes:

**Core Tables:**

- `users` - staff, admin, superuser roles with nullable profile fields
- `helpdesk_tickets` - nullable `user_id` FK for hybrid association
- `loan_applications` - nullable `user_id` FK for hybrid association
- `audits` - owen-it compliance audit trail with old/new values
- `activity_log` - spatie operational logging with causer tracking
- `personal_access_tokens` - Sanctum API tokens with abilities
- `notification_preferences` - multi-channel notification settings
- `notifications` - database notifications for in-app display

**Hybrid Data Association Pattern:**

```sql
-- Authenticated submission
user_id: 123, submitter_email: NULL, submitter_name: NULL, submitter_phone: NULL

-- Guest submission  
user_id: NULL, submitter_email: 'guest@motac.gov.my', submitter_name: 'Guest User', submitter_phone: '03-12345678'
```

## Correctness Properties

*A property is a characteristic or behavior that should hold true across all valid executions of a system-essentially, a formal statement about what the system should do. Properties serve as the bridge between human-readable specifications and machine-verifiable correctness guarantees.*

### Property 1: PHP 8 Attribute Compliance

*For any* test file after conversion, all test methods SHALL use `#[Test]` attributes and the file SHALL contain proper PHPUnit attribute imports.
**Validates: Requirements 1.1, 1.4, 2.1-2.4**

### Property 2: Bahasa Melayu Content Validation

*For any* UI test that asserts text content (form labels, buttons, messages, error messages), the asserted text SHALL be in Bahasa Melayu, either as literal strings or translation key references from `lang/ms/`.
**Validates: Requirements 3.1-3.5**

### Property 3: Hybrid Submission Path Validation

*For any* submission test (helpdesk tickets, loan applications), the test suite SHALL include both authenticated (user_id linked) and guest (user_id=NULL with submitter_* fields) test cases.
**Validates: Requirements 4.1, 4.2, 4.4, 4.5**

### Property 4: Authenticated Form Auto-Fill Validation

*For any* authenticated user accessing a submission form, the test SHALL verify that profile data (name, email, phone, department, grade) is pre-filled from the user's profile.
**Validates: Requirements 4.3**

### Property 5: Email Domain Restriction Validation

*For any* self-registration attempt, the test SHALL verify that only @motac.gov.my email addresses are accepted, and all other domains are rejected with appropriate error messages.
**Validates: Requirements 5.1, 5.5**

### Property 6: Flexible Login Validation

*For any* login attempt, the test SHALL verify that both full email format (<user@motac.gov.my>) and short username format (user) successfully authenticate the same user.
**Validates: Requirements 5.3**

### Property 7: Dual Audit System Validation

*For any* auditable model change, the test SHALL verify that both owen-it audit records (with old/new values) and spatie activity log entries are created.
**Validates: Requirements 6.1, 6.2, 6.3**

### Property 8: Role-Based Access Validation

*For any* protected route or resource, the test SHALL verify that staff, admin, and superuser roles have correct access permissions as defined in D00 §5.1.
**Validates: Requirements 7.1, 7.2, 7.3**

### Property 9: Notification System Validation

*For any* notification-triggering action, the test SHALL verify that notifications are created according to user preferences (immediate/daily/weekly digest) and stored in the database for in-app display.
**Validates: Requirements 8.1, 8.2, 8.3, 8.5**

### Property 10: API Token Authentication Validation

*For any* API endpoint protected by Sanctum, the test SHALL verify that valid tokens with appropriate abilities grant access, invalid/expired tokens are rejected, and rate limits are enforced.
**Validates: Requirements 9.1, 9.2, 9.3, 9.4**

### Property 11: Filament Component Validation

*For any* Filament resource test, the test SHALL verify that actions execute correctly, tables support filtering/sorting/pagination, forms validate and submit properly, and dashboard widgets render.
**Validates: Requirements 10.2, 10.3, 10.4, 10.5**

### Property 12: Cross-Module Data Linking Validation

*For any* cross-module operation (e.g., damaged asset return), the test SHALL verify that related records are properly linked (e.g., helpdesk ticket linked to loan_transaction).
**Validates: Requirements 11.2, 11.5**

### Property 13: Documentation Preservation

*For any* test method with `@trace` or `@traceability` tags, these tags SHALL be preserved after conversion while PHPDoc annotations are converted to PHP 8 attributes.
**Validates: Requirements 12.1, 12.2, 12.3**

### Property 14: Test Count Preservation

*For any* test file before and after update, the number of test methods SHALL remain identical (unless new tests are explicitly added for new v3.6.0 features).
**Validates: Requirements 13.2**

## Error Handling

1. **Syntax Errors**: If updates introduce syntax errors, the file should be flagged for manual review
2. **Missing Imports**: Automated detection of missing PHPUnit attribute imports
3. **Test Failures**: Any test failures after updates indicate logic changes that need investigation
4. **Translation Key Errors**: Missing translation keys should be reported for addition to lang/ms/
5. **Model Relationship Errors**: Outdated relationships should be updated to current schema
6. **Audit System Errors**: Missing audit configurations should be reported for system setup
7. **RBAC Configuration Errors**: Missing role assignments should be flagged for user management

## Testing Strategy

### Comprehensive Testing Approach

The test suite uses both unit tests and property-based testing with comprehensive coverage:

**Unit Tests:**

- Verify specific examples and edge cases
- Test integration points between components
- Validate error conditions and boundary values
- Cover all four RBAC roles individually

**Property-Based Tests:**

- Verify universal properties across all inputs
- Use PHPUnit with data providers for parameterized testing
- Ensure correctness properties hold for generated test data
- Test hybrid flows with various user states

**Integration Tests:**

- Verify crule functionality
- Test dual audit system integration
- Validate multi-channel notification delivery
- Ensure API token authentication across endpoints

### Testing Framework Configuration

- **Framework**: PHPUnit 11.5.44
- **Attributes**: PHP 8 native attributes (#[Test], #[DataProvider], etc.)
- **Livewire Testing**: Livewire::test() for component testing
- **Filament Testing**: livewire() helper for Filament resources
- **Database**: RefreshDatabase trait for isolation
- **Notifications**: Notification::fake() for testing
- **Queue**: Queue::fake() for job testing
- **Storage**: Storage::fake() for file testing

### Validation Approach

1. **Pre-update**: Run `php artisan test` to establish baseline
2. **Per-file validation**: After each file update, verify no syntax errors using `getDiagnostics`
3. **Incremental testing**: Run specific test files after updates
4. **Post-update**: Run complete test suite to verify all tests pass
5. **Manual review**: Spot-check updated files for proper formatting and alignment
6. **Property verification**: Ensure all correctness properties are implemented

### Property-Based Test Implementation

Each correctness property will be implemented as a PHPUnit test with comprehensive data providers:

```php
/**
 * **Feature: test-suite-comprehensive-v3.6, Property 5: Email Domain Restriction Validation**
 */
#[Test]
#[DataProvider('emailDomainProvider')]
public function emailDomainRestrictionIsEnforced(string $email, bool $shouldPass, string $expectedError = null): void
{
    $userData = [
        'name' => 'Test User',
        'email' => $email,
        'password' => 'password123',
        'password_confirmation' => 'password123',
    ];
    
    $response = $this->post('/register', $userData);
    
    if ($shouldPass) {
        $response->assertSessionHasNoErrors('email');
        $response->assertRedirect('/email/verify');
        $this->assertDatabaseHas('users', ['email' => $email]);
    } else {
        $response->assertSessionHasErrors('email');
        if ($expectedError) {
            $response->assertSessionHasErrors(['email' => __($expectedError)]);
        }
        $this->assertDatabaseMissing('users', ['email' => $email]);
    }
}

public static function emailDomainProvider(): array
{
    return [
        'valid motac email' => ['user@motac.gov.my', true],
        'valid motac email with numbers' => ['user123@motac.gov.my', true],
        'invalid gmail' => ['user@gmail.com', false, 'validation.email_domain'],
        'invalid yahoo' => ['user@yahoo.com', false, 'validation.email_domain'],
        'invalid hotmail' => ['user@hotmail.com', false, 'validation.email_domain'],
        'invalid subdomain' => ['user@sub.motac.gov.my', false, 'validation.email_domain'],
        'invalid similar domain' => ['user@motac.gov.my.fake.com', false, 'validation.email_domain'],
        'case insensitive valid' => ['USER@MOTAC.GOV.MY', true],
    ];
}
```
