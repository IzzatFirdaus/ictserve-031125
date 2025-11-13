# E2E Tests - ICTServe

End-to-end (E2E) tests for ICTServe using Playwright Test Framework.

## Prerequisites

### Required

1. **Node.js and npm** (v18 or higher)
   ```bash
   node --version  # Should be v18+
   npm --version
   ```

2. **Laravel Application Dependencies**
   ```bash
   composer install --no-interaction
   ```

3. **Laravel Application Environment**
   ```bash
   # Copy .env.example to .env if needed
   cp .env.example .env
   
   # Generate application key
   php artisan key:generate
   
   # Run database migrations
   php artisan migrate --seed
   ```

4. **Playwright Installation**
   ```bash
   # Install Playwright dependencies
   npm ci
   
   # Install Playwright browsers
   npx playwright install --with-deps chromium
   ```

## Running Tests

### Start Laravel Server

The Playwright configuration automatically starts the Laravel server, but you can also start it manually:

```bash
php artisan serve
```

The server will run on `http://localhost:8000` (configured in `playwright.config.ts`).

### Run All Tests

```bash
# Run all E2E tests
npx playwright test

# Run with UI mode (interactive)
npx playwright test --ui

# Run specific test file
npx playwright test accessibility-compliance.spec.ts

# Run tests in headed mode (see browser)
npx playwright test --headed
```

### Run Specific Test Suites

```bash
# Accessibility tests only
npx playwright test accessibility

# Performance tests only
npx playwright test performance

# Helpdesk module tests
npx playwright test helpdesk
```

### Debug Mode

```bash
# Debug mode - opens inspector
npx playwright test --debug

# Debug specific test
npx playwright test accessibility-compliance.spec.ts --debug
```

## Test Reports

### View HTML Report

After running tests:

```bash
npx playwright show-report
```

### CI/CD Integration

Tests automatically generate JSON and HTML reports:
- HTML Report: `playwright-report/index.html`
- JSON Report: `test-results/results.json`

## Test Configuration

Configuration is in `playwright.config.ts`:

- **Base URL**: `http://localhost:8000` (auto-configured)
- **Browser**: Chromium (Desktop Chrome)
- **Parallel**: Tests run in parallel for speed
- **Retries**: 2 retries on CI, 0 locally
- **Timeout**: 5 minutes per test, 30s for actions
- **Screenshots**: Only on failure
- **Videos**: Retained on failure
- **Traces**: On first retry (for debugging)

## Test Structure

```
tests/e2e/
├── accessibility-*.spec.ts      # Accessibility/WCAG tests
├── dashboard-*.spec.ts           # Dashboard tests
├── helpdesk-*.spec.ts            # Helpdesk module tests
├── loan-*.spec.ts                # Loan module tests
├── staff-*.spec.ts               # Staff workflow tests
├── guest-*.spec.ts               # Guest user flows
├── performance/                  # Performance tests
│   ├── core-web-vitals.spec.ts
│   └── lighthouse-audit.spec.ts
├── fixtures/                     # Test fixtures
│   └── ictserve-fixtures.ts
└── pages/                        # Page Object Models
    ├── staff-dashboard.page.ts
    └── staff-login.page.ts
```

## Common Issues

### Server Not Running

**Error**: `Could not connect to server`

**Solution**: Make sure Laravel is running:
```bash
php artisan serve
```

Or let Playwright auto-start it (default behavior).

### Port Already in Use

**Error**: `Address already in use`

**Solution**: Kill the process using port 8000:
```bash
# Linux/Mac
lsof -ti:8000 | xargs kill -9

# Or use different port
php artisan serve --port=8001
# Update playwright.config.ts baseURL accordingly
```

### Database Issues

**Error**: Database connection or missing tables

**Solution**:
```bash
# Reset and seed database
php artisan migrate:fresh --seed

# Or use testing database
cp .env.example .env.testing
# Update DB_DATABASE=ictserve_test
php artisan migrate --seed --env=testing
```

### Missing Playwright Browsers

**Error**: `browserType.launch: Executable doesn't exist`

**Solution**:
```bash
npx playwright install --with-deps chromium
```

## Test Credentials

Tests use seeded test users:

- **Staff User**: `userstaff@motac.gov.my` / `password`
- **Admin User**: `admin@motac.gov.my` / `password`
- **Guest User**: `guest@motac.gov.my` / `password`

Make sure database seeding is complete before running authenticated tests.

## Writing New Tests

### Basic Test Structure

```typescript
import { test, expect } from '@playwright/test';

test.describe('Feature Name', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/');
  });

  test('should do something', async ({ page }) => {
    // Arrange
    await page.goto('/some-page');
    
    // Act
    await page.getByRole('button', { name: 'Submit' }).click();
    
    // Assert
    await expect(page.locator('.success-message')).toBeVisible();
  });
});
```

### Using Custom Fixtures

```typescript
import { test, expect } from './fixtures/ictserve-fixtures';

test('authenticated test', async ({ authenticatedPage }) => {
  // authenticatedPage is already logged in
  await expect(authenticatedPage.locator('[data-testid="dashboard"]')).toBeVisible();
});
```

### Accessibility Testing

```typescript
import { test, expect } from '@playwright/test';
import AxeBuilder from '@axe-core/playwright';

test('accessibility compliance', async ({ page }) => {
  await page.goto('/');
  
  const results = await new AxeBuilder({ page })
    .withTags(['wcag2a', 'wcag2aa', 'wcag22aa'])
    .analyze();
  
  expect(results.violations).toEqual([]);
});
```

## CI/CD Configuration

### GitHub Actions Example

```yaml
name: E2E Tests
on: [push, pull_request]

jobs:
  test:
    runs-on: ubuntu-latest
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.3'
          
      - name: Install Dependencies
        run: |
          composer install --no-interaction
          npm ci
          
      - name: Prepare Laravel
        run: |
          cp .env.example .env
          php artisan key:generate
          php artisan migrate --seed
          
      - name: Install Playwright Browsers
        run: npx playwright install --with-deps chromium
        
      - name: Run E2E Tests
        run: npx playwright test
        
      - name: Upload Test Results
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: playwright-report
          path: playwright-report/
```

## Documentation References

- **WCAG 2.2 AA**: All pages should pass accessibility tests
- **D12 UI/UX Design Guide**: Component patterns
- **D14 UI/UX Style Guide**: Branding and styling
- **D15 Language Guide**: i18n/l10n standards

## Support

For issues or questions:
- Check test logs: `npx playwright test --reporter=list`
- View traces: `npx playwright show-trace trace.zip`
- Review test results documentation in `tests/e2e/ACCESSIBILITY_TEST_RESULTS.md`

## Traceability

- **SRS Requirements**: See individual test files for `@trace` comments
- **D03 Requirements**: Cross-referenced in test descriptions
- **D04 Design**: Component testing aligned with design spec
- **D11 Technical**: Performance benchmarks defined

---

**Last Updated**: 2025-11-13
**Version**: 1.0.0
