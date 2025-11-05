# ICTServe Testing Strategy

**Version**: 1.0  
**Date**: 2025-11-02  
**Status**: Active  
**Requirements**: D03 (All requirements), D04 (Design), D14 (Accessibility)

## Overview

This document defines the comprehensive testing strategy for the ICTServe system, covering unit tests, feature tests, integration tests, end-to-end tests, accessibility tests, and performance tests.

## Testing Pyramid

```
                    ┌─────────────────┐
                    │   E2E Tests     │  ← 10% (Playwright, Dusk)
                    │   (Slow)        │
                    └─────────────────┘
                  ┌───────────────────────┐
                  │  Integration Tests    │  ← 20% (Feature tests)
                  │  (Medium)             │
                  └───────────────────────┘
              ┌─────────────────────────────────┐
              │      Unit Tests                 │  ← 70% (PHPUnit)
              │      (Fast)                     │
              └─────────────────────────────────┘
```

## Test Categories

### 1. Unit Tests (70% of test suite)

**Purpose**: Test individual components in isolation

**Coverage**:
- ✅ Models (User, HelpdeskTicket, LoanApplication, etc.)
- ✅ Enums (TicketPriority, TicketStatus)
- ✅ Services (DualApprovalService, NotificationService, SLAManager)
- ✅ Middleware (SetLocaleMiddleware, EnsureUserHasRole)
- ✅ Policies (UserPolicy, HelpdeskTicketPolicy, LoanApplicationPolicy)
- ✅ Helpers and utilities

**Tools**: PHPUnit 11

**Location**: `tests/Unit/`

**Execution**: `php artisan test --testsuite=Unit`

### 2. Feature Tests (20% of test suite)

**Purpose**: Test feature workflows and integrations

**Coverage**:
- ✅ Helpdesk workflows (guest submission, ticket management)
- ✅ Asset loan workflows (guest application, approval, tracking)
- ✅ Authentication and authorization
- ✅ Livewire component interactions
- ✅ Form validation and submission
- ✅ Email notifications
- ✅ Queue processing
- ✅ Audit trail logging

**Tools**: PHPUnit 11, Livewire Testing

**Location**: `tests/Feature/`

**Execution**: `php artisan test --testsuite=Feature`

### 3. Integration Tests (5% of test suite)

**Purpose**: Test external system integrations

**Coverage**:
- ⚠️ SMTP email gateway integration
- ⚠️ HRMIS API integration (optional)
- ⚠️ Redis cache and session management
- ⚠️ File storage and signed URLs
- ⚠️ Database transactions and rollbacks

**Tools**: PHPUnit 11, HTTP mocking

**Location**: `tests/Feature/Integration/`

**Execution**: `php artisan test --testsuite=Feature --filter=Integration`

### 4. End-to-End Tests (5% of test suite)

**Purpose**: Test complete user journeys across the application

**Coverage**:
- ✅ Guest ticket submission → email confirmation → status tracking
- ✅ Guest loan application → email approval → notification
- ✅ Authenticated portal workflows
- ✅ Admin panel workflows (Filament)
- ✅ Cross-browser compatibility

**Tools**: Laravel Dusk, Playwright

**Location**: `tests/Browser/`, `tests/Playwright/`

**Execution**: 
- Dusk: `php artisan dusk`
- Playwright: `npm run test:e2e`

### 5. Accessibility Tests (Continuous)

**Purpose**: Ensure WCAG 2.2 Level AA compliance

**Coverage**:
- ✅ Automated accessibility scanning (axe-core)
- ✅ Keyboard navigation testing
- ✅ Screen reader compatibility
- ✅ Color contrast validation (4.5:1 text, 3:1 UI)
- ✅ Touch target size (44×44px minimum)
- ✅ Focus indicators (3px outline, 2px offset, 3:1 contrast)

**Tools**: Playwright + axe-core, Lighthouse, manual testing

**Location**: `tests/Playwright/accessibility.spec.ts`, `tests/Feature/Accessibility/`

**Execution**: `npm run test:accessibility`

### 6. Performance Tests (Continuous)

**Purpose**: Ensure Core Web Vitals targets are met

**Coverage**:
- ✅ LCP (Largest Contentful Paint) < 2.5s
- ✅ FID (First Input Delay) < 100ms
- ✅ CLS (Cumulative Layout Shift) < 0.1
- ✅ TTFB (Time to First Byte) < 600ms
- ⚠️ Load testing for concurrent users
- ⚠️ Database query optimization verification

**Tools**: Playwright + Web Vitals API, Lighthouse

**Location**: `tests/Playwright/core-web-vitals-authenticated.spec.ts`

**Execution**: `npm run test:performance`

## Test Execution Strategy

### Local Development

```bash
# Run all tests
php artisan test

# Run specific test suite
php artisan test --testsuite=Unit
php artisan test --testsuite=Feature

# Run specific test file
php artisan test tests/Unit/Models/UserTest.php

# Run specific test method
php artisan test --filter=test_is_staff_returns_true_for_staff_role

# Run with coverage
php artisan test --coverage --min=80

# Run Dusk tests
php artisan dusk

# Run Playwright tests
npm run test:e2e
```

### Continuous Integration (CI/CD)

```yaml
# GitHub Actions workflow
name: Tests

on: [push, pull_request]

jobs:
  tests:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v3
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: 8.2
          extensions: mbstring, pdo, pdo_mysql, redis
          
      - name: Install Dependencies
        run: composer install --no-interaction --prefer-dist
        
      - name: Run Unit Tests
        run: php artisan test --testsuite=Unit --stop-on-failure
        
      - name: Run Feature Tests
        run: php artisan test --testsuite=Feature --stop-on-failure
        
      - name: Run Code Coverage
        run: php artisan test --coverage --min=80
        
      - name: Run Playwright Tests
        run: npm run test:e2e
        
      - name: Run Accessibility Tests
        run: npm run test:accessibility
        
      - name: Run Performance Tests
        run: npm run test:performance
```

## Quality Gates

### Code Coverage Targets

- **Overall Coverage**: Minimum 80%
- **Critical Paths**: Minimum 95%
  - Guest ticket submission workflow
  - Guest loan application workflow
  - Email-based approval workflow
  - Portal-based approval workflow
  - Audit trail logging
  - WCAG 2.2 AA compliance

### Test Execution Requirements

- ✅ All unit tests must pass before commit
- ✅ All feature tests must pass before merge
- ✅ All E2E tests must pass before deployment
- ✅ Code coverage must meet minimum thresholds
- ✅ Accessibility tests must pass (100 Lighthouse score)
- ✅ Performance tests must meet Core Web Vitals targets

### Static Analysis

```bash
# PSR-12 compliance
vendor/bin/pint

# Static analysis (PHPStan/Larastan level 5)
vendor/bin/phpstan analyse

# Frontend linting
npm run lint:js
npm run lint:css
```

## Test Data Management

### Factories

All models have factories for generating test data:

```php
// User factory with role states
User::factory()->staff()->create();
User::factory()->approver()->create();
User::factory()->admin()->create();
User::factory()->superuser()->create();

// HelpdeskTicket factory with guest/authenticated variants
HelpdeskTicket::factory()->guest()->create();
HelpdeskTicket::factory()->authenticated()->create();

// LoanApplication factory with guest/authenticated variants
LoanApplication::factory()->guest()->create();
LoanApplication::factory()->authenticated()->create();
```

### Seeders

Test database seeders for consistent test data:

```bash
# Seed test database
php artisan db:seed --class=TestDatabaseSeeder

# Seed specific seeder
php artisan db:seed --class=AdminUserSeeder
```

### Database Refresh

```php
use Illuminate\Foundation\Testing\RefreshDatabase;

class ExampleTest extends TestCase

    use RefreshDatabase;
    
    // Database is automatically migrated and refreshed for each test

```

## Test Naming Conventions

### Unit Tests

```php
// Pattern: test_method_name_expected_behavior
public function test_is_staff_returns_true_for_staff_role(): void
public function test_generate_ticket_number_creates_unique_number(): void
public function test_approval_token_expires_after_seven_days(): void
```

### Feature Tests

```php
// Pattern: test_feature_scenario
public function test_guest_can_submit_helpdesk_ticket(): void
public function test_authenticated_user_can_view_submission_history(): void
public function test_approver_can_approve_loan_application_via_email(): void
```

### E2E Tests

```typescript
// Pattern: describe feature, test scenario
describe('Guest Ticket Submission', () => 
  test('should submit ticket and receive confirmation email', async () => 
    // Test implementation
  );
);
```

## Test Documentation

### Test Comments

```php
/**
 * Test user has staff role method.
 *
 * Verifies that isStaff() returns true for users with 'staff' role
 * and false for all other roles.
 *
 * Requirements: D03-FR-003 (Four-role RBAC)
 */
public function test_is_staff_returns_true_for_staff_role(): void

    // Test implementation

```

### Test Reports

- **Unit Test Report**: `tests/Unit/phpunit-unit-results.xml`
- **Feature Test Report**: `tests/Feature/phpunit-feature-results.xml`
- **Coverage Report**: `storage/coverage/index.html`
- **Playwright Report**: `tests/Playwright/playwright-report/index.html`

## Accessibility Testing Checklist

### Automated Testing (axe-core)

- ✅ No critical or serious violations
- ✅ Valid ARIA attributes
- ✅ Proper role assignments
- ✅ Sufficient color contrast
- ✅ Form labels and descriptions
- ✅ Keyboard navigation support

### Manual Testing

- ✅ Keyboard navigation (Tab, Enter, Escape, Arrow keys)
- ✅ Screen reader testing (NVDA, JAWS, VoiceOver)
- ✅ Focus indicators visible (3px outline, 2px offset, 3:1 contrast)
- ✅ Touch targets minimum 44×44px
- ✅ Responsive design (320px-1920px)
- ✅ Color independence (no information by color alone)

## Performance Testing Checklist

### Core Web Vitals

- ✅ LCP < 2.5s (Largest Contentful Paint)
- ✅ FID < 100ms (First Input Delay)
- ✅ CLS < 0.1 (Cumulative Layout Shift)
- ✅ TTFB < 600ms (Time to First Byte)

### Lighthouse Scores

- ✅ Performance: 90+
- ✅ Accessibility: 100
- ✅ Best Practices: 100
- ✅ SEO: 100

### Load Testing

- ⚠️ Concurrent users: 100+ (to be implemented)
- ⚠️ Response time: < 200ms for 95th percentile (to be implemented)
- ⚠️ Error rate: < 0.1% (to be implemented)

## Security Testing Checklist

### OWASP Top 10

- ⚠️ SQL Injection prevention (to be tested)
- ⚠️ XSS prevention (to be tested)
- ⚠️ CSRF protection (to be tested)
- ⚠️ Authentication and session management (to be tested)
- ⚠️ Access control (partially tested)
- ⚠️ Security misconfiguration (to be tested)
- ⚠️ Sensitive data exposure (to be tested)
- ⚠️ File upload security (to be tested)
- ⚠️ Rate limiting (to be tested)
- ⚠️ Logging and monitoring (partially tested)

## Test Maintenance

### Regular Reviews

- **Weekly**: Review failing tests and fix immediately
- **Monthly**: Review test coverage and identify gaps
- **Quarterly**: Review test strategy and update as needed
- **Annually**: Comprehensive test suite audit

### Test Refactoring

- Remove duplicate tests
- Consolidate similar tests
- Update tests for changed requirements
- Improve test readability and maintainability

### Test Performance

- Keep unit tests fast (< 1s per test)
- Optimize feature tests (< 5s per test)
- Minimize E2E tests (< 30s per test)
- Use database transactions for speed

## Troubleshooting

### Common Issues

1. **Failing Tests Due to Schema Changes**
   - Solution: Update factories and migrations
   - Run: `php artisan migrate:fresh --seed`

2. **Slow Test Execution**
   - Solution: Use in-memory SQLite for tests
   - Solution: Optimize database queries
   - Solution: Use database transactions

3. **Flaky E2E Tests**
   - Solution: Add explicit waits
   - Solution: Use Playwright's auto-waiting
   - Solution: Increase timeout values

4. **Coverage Not Meeting Targets**
   - Solution: Identify untested code paths
   - Solution: Add missing tests
   - Solution: Remove dead code

## Resources

### Documentation

- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Documentation](https://laravel.com/docs/12.x/testing)
- [Livewire Testing Documentation](https://livewire.laravel.com/docs/testing)
- [Playwright Documentation](https://playwright.dev/)
- [Laravel Dusk Documentation](https://laravel.com/docs/12.x/dusk)

### Tools

- **PHPUnit**: Unit and feature testing
- **Livewire Testing**: Component testing
- **Laravel Dusk**: Browser testing
- **Playwright**: E2E testing
- **axe-core**: Accessibility testing
- **Lighthouse**: Performance and accessibility auditing

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-02  
**Next Review**: 2025-12-02  
**Traceability**: D03 (All requirements), D04 (Design), D14 (Accessibility)
