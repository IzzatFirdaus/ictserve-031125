# Automated Testing Pipeline

**Version**: 1.0  
**Date**: 2025-11-02  
**Status**: Active  
**Requirements**: D03 (All requirements), D14 (Accessibility), Task 13.2

## Overview

The ICTServe system uses a comprehensive automated testing pipeline with continuous integration (CI/CD) to ensure code quality, accessibility compliance, and performance standards are maintained throughout the development lifecycle.

## Pipeline Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Code Push / Pull Request                  │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│                   GitHub Actions Workflow                    │
├─────────────────────────────────────────────────────────────┤
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ Code Quality │  │  Unit Tests  │  │Feature Tests │      │
│  │  - Pint      │  │  - PHPUnit   │  │  - PHPUnit   │      │
│  │  - PHPStan   │  │  - 51 tests  │  │  - 30+ tests │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │  Dusk Tests  │  │ Playwright   │  │Accessibility │      │
│  │  - Browser   │  │  - E2E Tests │  │  - axe-core  │      │
│  │  - 6 tests   │  │  - 15 tests  │  │  - WCAG 2.2  │      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│                                                               │
│  ┌──────────────┐  ┌──────────────┐                         │
│  │ Performance  │  │   Coverage   │                         │
│  │  - Core Web  │  │  - 80% min   │                         │
│  │  - Vitals    │  │  - 95% crit  │                         │
│  └──────────────┘  └──────────────┘                         │
└──────────────────────┬──────────────────────────────────────┘
                       │
                       ▼
┌─────────────────────────────────────────────────────────────┐
│              Quality Gates & Deployment Decision             │
│  ✅ All tests pass                                           │
│  ✅ Coverage meets thresholds                                │
│  ✅ Accessibility score 100                                  │
│  ✅ Performance meets Core Web Vitals                        │
└─────────────────────────────────────────────────────────────┘
```

## CI/CD Configuration

### GitHub Actions Workflow

**File**: `.github/workflows/tests.yml`

The workflow consists of 5 parallel jobs:

1. **tests** - Unit and feature tests with code coverage
2. **dusk** - Laravel Dusk browser tests
3. **playwright** - Playwright E2E tests
4. **accessibility** - WCAG 2.2 AA compliance tests
5. **performance** - Core Web Vitals performance tests

### Job: tests

**Purpose**: Run unit tests, feature tests, and code quality checks

**Steps**:
1. Checkout code
2. Setup PHP 8.2/8.3 with extensions
3. Setup Node.js 20
4. Install Composer dependencies
5. Install NPM dependencies
6. Build frontend assets
7. Setup environment and database
8. Run PSR-12 code style check (Pint)
9. Run PHPStan static analysis
10. Run unit tests
11. Run feature tests
12. Generate code coverage report
13. Upload coverage to Codecov

**Matrix Strategy**:
- PHP versions: 8.2, 8.3
- Laravel version: 12.x

**Quality Gates**:
- ✅ PSR-12 compliance (Pint)
- ✅ PHPStan level 5 analysis
- ✅ All unit tests pass
- ✅ All feature tests pass
- ✅ Minimum 80% code coverage

### Job: dusk

**Purpose**: Run Laravel Dusk browser tests

**Steps**:
1. Checkout code
2. Setup PHP 8.2
3. Setup Node.js 20
4. Install dependencies
5. Build frontend assets
6. Setup environment and database
7. Upgrade Chrome Driver
8. Start Chrome Driver
9. Start Laravel server
10. Run Dusk tests
11. Upload screenshots on failure

**Quality Gates**:
- ✅ All Dusk tests pass
- ✅ No browser console errors

### Job: playwright

**Purpose**: Run Playwright E2E tests

**Steps**:
1. Checkout code
2. Setup PHP 8.2
3. Setup Node.js 20
4. Install dependencies
5. Install Playwright browsers
6. Build frontend assets
7. Setup environment and database
8. Start Laravel server
9. Run Playwright tests
10. Upload test report

**Quality Gates**:
- ✅ All E2E tests pass
- ✅ No critical failures

### Job: accessibility

**Purpose**: Run WCAG 2.2 AA accessibility tests

**Steps**:
1. Checkout code
2. Setup PHP 8.2
3. Setup Node.js 20
4. Install dependencies
5. Install Playwright browsers
6. Build frontend assets
7. Setup environment and database
8. Start Laravel server
9. Run accessibility tests
10. Upload accessibility report

**Quality Gates**:
- ✅ Lighthouse accessibility score: 100
- ✅ No critical or serious axe-core violations
- ✅ WCAG 2.2 Level AA compliance

### Job: performance

**Purpose**: Run Core Web Vitals performance tests

**Steps**:
1. Checkout code
2. Setup PHP 8.2
3. Setup Node.js 20
4. Install dependencies
5. Install Playwright browsers
6. Build frontend assets
7. Setup environment and database
8. Start Laravel server
9. Run performance tests
10. Upload performance report

**Quality Gates**:
- ✅ LCP < 2.5s (Largest Contentful Paint)
- ✅ FID < 100ms (First Input Delay)
- ✅ CLS < 0.1 (Cumulative Layout Shift)
- ✅ TTFB < 600ms (Time to First Byte)
- ✅ Lighthouse performance score: 90+

## Local Testing Commands

### PHP Tests

```bash
# Run all tests
php artisan test

# Run unit tests only
php artisan test --testsuite=Unit

# Run feature tests only
php artisan test --testsuite=Feature

# Run specific test file
php artisan test tests/Unit/Models/UserTest.php

# Run specific test method
php artisan test --filter=test_is_staff_returns_true_for_staff_role

# Run with coverage
php artisan test --coverage --min=80

# Run with coverage HTML report
php artisan test --coverage-html storage/coverage
```

### NPM Test Scripts

```bash
# Run all PHP tests
npm run test

# Run unit tests
npm run test:unit

# Run feature tests
npm run test:feature

# Run with coverage
npm run test:coverage

# Run E2E tests
npm run test:e2e

# Run E2E tests with UI
npm run test:e2e:ui

# Run E2E tests in debug mode
npm run test:e2e:debug

# Run accessibility tests
npm run test:accessibility

# Run performance tests
npm run test:performance

# Run authenticated performance tests
npm run test:performance:authenticated

# Run all tests (unit + feature + E2E)
npm run test:all
```

### Code Quality Commands

```bash
# Run all quality checks
npm run quality:check

# Format code (PSR-12)
vendor/bin/pint

# Check code style without fixing
vendor/bin/pint --test

# Run static analysis
vendor/bin/phpstan analyse

# Run static analysis with memory limit
vendor/bin/phpstan analyse --memory-limit=2G

# Lint JavaScript
npm run lint:js

# Lint CSS
npm run lint:css

# Format frontend code
npm run format
```

### Browser Tests

```bash
# Run Laravel Dusk tests
php artisan dusk

# Run specific Dusk test
php artisan dusk tests/Browser/HelpdeskWorkflowTest.php

# Run Dusk with specific browser
php artisan dusk --env=dusk.chrome

# Update Chrome Driver
php artisan dusk:chrome-driver --detect
```

## Code Coverage Configuration

### PHPUnit Coverage

**Configuration**: `phpunit.xml`

```xml
<source>
    <include>
        <directory>app</directory>
    </include>
</source>
```

**Coverage Targets**:
- Overall: 80% minimum
- Critical paths: 95% minimum

**Critical Paths**:
- Guest ticket submission workflow
- Guest loan application workflow
- Email-based approval workflow
- Portal-based approval workflow
- Audit trail logging
- WCAG 2.2 AA compliance

### Coverage Reports

**HTML Report**:
```bash
php artisan test --coverage-html storage/coverage
# Open: storage/coverage/index.html
```

**Clover XML Report**:
```bash
php artisan test --coverage-clover coverage.xml
```

**Text Report**:
```bash
php artisan test --coverage
```

### Codecov Integration

**Upload Coverage**:
```bash
# Automatic in CI/CD
codecov -f coverage.xml

# Manual upload
bash <(curl -s https://codecov.io/bash)
```

## Quality Gates

### Pre-Commit Checks

```bash
# Run before committing
vendor/bin/pint
vendor/bin/phpstan analyse
php artisan test --testsuite=Unit
```

### Pre-Push Checks

```bash
# Run before pushing
vendor/bin/pint --test
vendor/bin/phpstan analyse
php artisan test
npm run test:e2e
```

### Pre-Merge Checks

```bash
# Run before merging PR
npm run quality:check
php artisan test --coverage --min=80
npm run test:all
npm run test:accessibility
npm run test:performance
```

## Continuous Integration Triggers

### On Push

- Branches: `main`, `develop`
- Runs: All jobs (tests, dusk, playwright, accessibility, performance)

### On Pull Request

- Target branches: `main`, `develop`
- Runs: All jobs (tests, dusk, playwright, accessibility, performance)

### Manual Trigger

```bash
# Trigger workflow manually via GitHub Actions UI
# Or using GitHub CLI
gh workflow run tests.yml
```

## Test Environment Configuration

### Environment Variables

**File**: `.env.testing`

```env
APP_ENV=testing
APP_DEBUG=true
APP_KEY=base64:...

DB_CONNECTION=sqlite
DB_DATABASE=:memory:

CACHE_STORE=array
SESSION_DRIVER=array
QUEUE_CONNECTION=sync
MAIL_MAILER=array

BCRYPT_ROUNDS=4
```

### Database Configuration

**SQLite In-Memory**:
- Fast test execution
- Isolated test environment
- Automatic cleanup

**Configuration**: `phpunit.xml`

```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```

## Artifacts and Reports

### Test Artifacts

**Dusk Screenshots** (on failure):
- Path: `tests/Browser/screenshots`
- Retention: 30 days

**Dusk Console Logs** (on failure):
- Path: `tests/Browser/console`
- Retention: 30 days

**Playwright Report**:
- Path: `tests/Playwright/playwright-report`
- Retention: 30 days

**Accessibility Report**:
- Path: `tests/Playwright/playwright-report`
- Retention: 30 days

**Performance Report**:
- Path: `tests/Playwright/playwright-report`
- Retention: 30 days

### Coverage Reports

**Codecov**:
- URL: https://codecov.io/gh/[org]/[repo]
- Badge: ![codecov](https://codecov.io/gh/[org]/[repo]/branch/main/graph/badge.svg)

**Local HTML Report**:
- Path: `storage/coverage/index.html`
- Generated: `php artisan test --coverage-html storage/coverage`

## Monitoring and Alerts

### GitHub Actions Status

**Status Badge**:
```markdown
![Tests](https://github.com/[org]/[repo]/workflows/Tests/badge.svg)
```

**Notifications**:
- Email on failure
- Slack integration (optional)
- GitHub notifications

### Coverage Monitoring

**Codecov Checks**:
- Coverage decrease threshold: 1%
- Patch coverage minimum: 80%
- Project coverage minimum: 80%

## Troubleshooting

### Common CI/CD Issues

**1. Tests Failing Locally But Passing in CI**

**Cause**: Environment differences

**Solution**:
```bash
# Use same environment as CI
cp .env.example .env
php artisan key:generate
php artisan migrate:fresh
php artisan test
```

**2. Dusk Tests Timing Out**

**Cause**: Slow server startup or network issues

**Solution**:
```yaml
# Increase wait time in workflow
- name: Wait for server
  run: sleep 10
```

**3. Playwright Tests Flaky**

**Cause**: Race conditions or timing issues

**Solution**:
```typescript
// Use Playwright's auto-waiting
await page.waitForLoadState('networkidle');
await page.waitForSelector('[data-testid="element"]');
```

**4. Coverage Not Meeting Threshold**

**Cause**: Untested code paths

**Solution**:
```bash
# Generate coverage report
php artisan test --coverage-html storage/coverage

# Identify untested code
# Open: storage/coverage/index.html
```

**5. Memory Limit Exceeded (PHPStan)**

**Cause**: Large codebase analysis

**Solution**:
```bash
# Increase memory limit
vendor/bin/phpstan analyse --memory-limit=2G
```

## Best Practices

### Test Writing

1. **Keep tests fast** (< 1s per unit test)
2. **Use database transactions** for speed
3. **Mock external services** in tests
4. **Use factories** for test data
5. **Test one thing** per test method
6. **Use descriptive test names**
7. **Follow AAA pattern** (Arrange, Act, Assert)

### CI/CD Optimization

1. **Cache dependencies** (Composer, NPM)
2. **Run tests in parallel** (matrix strategy)
3. **Use SQLite in-memory** for speed
4. **Optimize asset builds** (production mode)
5. **Upload artifacts** only on failure
6. **Set appropriate timeouts**

### Quality Assurance

1. **Run tests before commit**
2. **Review coverage reports**
3. **Fix failing tests immediately**
4. **Monitor CI/CD performance**
5. **Update dependencies regularly**
6. **Document test failures**

## Maintenance

### Weekly Tasks

- ✅ Review failing tests
- ✅ Fix flaky tests
- ✅ Update test documentation

### Monthly Tasks

- ✅ Review code coverage
- ✅ Identify untested code
- ✅ Update test strategy
- ✅ Optimize CI/CD performance

### Quarterly Tasks

- ✅ Comprehensive test audit
- ✅ Update testing tools
- ✅ Review quality gates
- ✅ Update CI/CD configuration

## Resources

### Documentation

- [GitHub Actions Documentation](https://docs.github.com/en/actions)
- [PHPUnit Documentation](https://phpunit.de/documentation.html)
- [Laravel Testing Documentation](https://laravel.com/docs/12.x/testing)
- [Playwright Documentation](https://playwright.dev/)
- [Codecov Documentation](https://docs.codecov.com/)

### Tools

- **GitHub Actions**: CI/CD platform
- **PHPUnit**: PHP testing framework
- **Playwright**: E2E testing framework
- **Laravel Dusk**: Browser testing
- **Codecov**: Code coverage reporting
- **Pint**: PSR-12 code formatter
- **PHPStan**: Static analysis tool

---

**Document Version**: 1.0  
**Last Updated**: 2025-11-02  
**Next Review**: 2025-12-02  
**Traceability**: D03 (All requirements), D14 (Accessibility), Task 13.2
