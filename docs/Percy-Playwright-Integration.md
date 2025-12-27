# Percy + Playwright + BrowserStack Integration Guide

**ICTServe v3.6.1 Visual Testing Stack**

This guide covers the complete integration of Percy visual testing, Playwright E2E testing, and BrowserStack cross-browser testing with MCP (Model Context Protocol) support.

## Table of Contents

1. [Architecture Overview](#architecture-overview)
2. [Percy Visual Testing](#percy-visual-testing)
3. [Playwright E2E Testing](#playwright-e2e-testing)
4. [BrowserStack Integration](#browserstack-integration)
5. [MCP Server Configuration](#mcp-server-configuration)
6. [Testing Workflows](#testing-workflows)
7. [CI/CD Integration](#cicd-integration)
8. [Troubleshooting](#troubleshooting)

---

## Architecture Overview

ICTServe v3.6.1 uses a modern testing stack that replaces Laravel Dusk:

```
┌─────────────────────────────────────────────────────────────┐
│                    Testing Architecture                       │
├─────────────────────────────────────────────────────────────┤
│                                                               │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │   Playwright │  │    Percy     │  │ BrowserStack │      │
│  │   E2E Tests  │  │   Visual     │  │ Cross-Browser│      │
│  │   (1.57.0)   │  │   Testing    │  │   Testing    │      │
│  └──────┬───────┘  └──────┬───────┘  └──────┬───────┘      │
│         │                  │                  │              │
│         └──────────────────┼──────────────────┘              │
│                            │                                 │
│                    ┌───────▼────────┐                        │
│                    │  MCP Servers   │                        │
│                    │  Integration   │                        │
│                    └────────────────┘                        │
│                                                               │
│  Technology Stack:                                           │
│  • Laravel 12.43.1                                           │
│  • Livewire 3.7.3 (Server-Driven UI)                        │
│  • Filament 4.3.1 (Admin Panel)                             │
│  • Tailwind CSS 4.1.18 (Responsive Design)                  │
│  • PHPUnit 11.5.46 (Unit/Feature Tests)                     │
│                                                               │
└─────────────────────────────────────────────────────────────┘
```

### Technology Comparison

| Feature | Laravel Dusk | Percy + Playwright |
|---------|-------------|-------------------|
| **Status** | ⚠️ Deprecated | ✅ Recommended |
| **Visual Testing** | ❌ No | ✅ Percy |
| **Cross-Browser** | Limited | ✅ BrowserStack |
| **Performance** | Slower | Faster |
| **Maintenance** | Higher | Lower |
| **MCP Support** | ❌ No | ✅ Yes |
| **Livewire Support** | Basic | Excellent |
| **Accessibility** | Manual | Integrated |

---

## Percy Visual Testing

### What is Percy?

Percy is a visual testing platform that captures and compares screenshots across different:
- **Browsers** (Chrome, Firefox, Safari, Edge)
- **Viewports** (Mobile, Tablet, Desktop)
- **States** (Default, Hover, Focus, Error)

### Installation

Percy is already configured in ICTServe v3.6.1. Verify installation:

```bash
# Check package.json
npm list @percy/cli @percy/playwright

# Expected output:
# @percy/cli@1.31.6
# @percy/playwright@1.0.10
```

### Configuration Files

**percy.config.js** - Main configuration:

```javascript
export default {
  version: 2,
  projectName: "ictserve",
  
  snapshot: {
    // Responsive breakpoints for ICTServe
    widths: [375, 768, 1024, 1280, 1920],
    minHeight: 1024,
    
    // Hide dynamic content
    percyCSS: `
      [wire:loading] { display: none !important; }
      .dynamic-timestamp { display: none !important; }
    `,
    
    enableJavaScript: true,
    waitForTimeout: 1000,
  },
  
  clientInfo: "ICTServe v3.6.1 Percy Integration",
  environmentInfo: "Laravel 12.43.1, Livewire 3.7.3, Playwright 1.57.0",
};
```

### Environment Variables

Add to your `.env`:

```bash
# Percy Configuration
PERCY_TOKEN=your_percy_token_here
PERCY_PROJECT=ictserve
PERCY_ENABLED=true
PERCY_BRANCH=develop
PERCY_TARGET_BRANCH=develop
```

### Basic Percy Snapshot

```typescript
// tests/e2e/example-percy.spec.ts
import { test } from '@playwright/test';
import percySnapshot from '@percy/playwright';

test('Visual test - Guest landing page', async ({ page }) => {
  await page.goto('/');
  
  // Wait for Livewire to initialize
  await page.waitForLoadState('networkidle');
  
  // Capture Percy snapshot
  await percySnapshot(page, 'Guest Landing Page', {
    widths: [375, 768, 1280],
    minHeight: 1024,
  });
});
```

### Percy with Livewire

```typescript
test('Visual test - Helpdesk form with Livewire', async ({ page }) => {
  await page.goto('/helpdesk/create');
  
  // Wait for Livewire component
  await page.waitForSelector('[wire\\:id]');
  
  // Interact with Livewire form
  await page.fill('input[name="title"]', 'Test Issue');
  await page.selectOption('select[name="category"]', 'hardware');
  
  // Wait for Livewire update
  await page.waitForTimeout(500);
  
  // Capture snapshot of filled form
  await percySnapshot(page, 'Helpdesk Form - Filled', {
    widths: [375, 768, 1280],
  });
});
```

### Percy with Filament Admin

```typescript
test('Visual test - Filament admin dashboard', async ({ page }) => {
  // Login as admin
  await page.goto('/admin/login');
  await page.fill('input[name="email"]', 'admin@motac.gov.my');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  
  // Wait for Filament dashboard
  await page.waitForURL('/admin');
  await page.waitForSelector('.fi-sidebar');
  
  // Capture admin dashboard
  await percySnapshot(page, 'Filament Admin Dashboard', {
    widths: [1280, 1920],
    minHeight: 1024,
  });
});
```

---

## Playwright E2E Testing

### Installation

Playwright is already configured. Verify:

```bash
npx playwright --version
# Expected: Version 1.57.0
```

### Configuration Files

**playwright.config.ts** - Main config:

```typescript
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/e2e',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  
  use: {
    baseURL: 'http://localhost:8000',
    trace: 'on-first-retry',
    screenshot: 'only-on-failure',
  },
  
  projects: [
    {
      name: 'chromium',
      use: { ...devices['Desktop Chrome'] },
    },
    {
      name: 'firefox',
      use: { ...devices['Desktop Firefox'] },
    },
    {
      name: 'webkit',
      use: { ...devices['Desktop Safari'] },
    },
    {
      name: 'mobile-chrome',
      use: { ...devices['Pixel 5'] },
    },
    {
      name: 'mobile-safari',
      use: { ...devices['iPhone 12'] },
    },
  ],
});
```

**playwright.percy.config.ts** - Percy-specific config:

```typescript
import { defineConfig } from '@playwright/test';
import baseConfig from './playwright.config';

export default defineConfig({
  ...baseConfig,
  
  // Override for Percy snapshots
  projects: [
    {
      name: 'percy-chrome',
      use: {
        ...baseConfig.projects[0].use,
        viewport: { width: 1280, height: 1024 },
      },
    },
    {
      name: 'percy-mobile',
      use: {
        viewport: { width: 375, height: 812 },
      },
    },
  ],
});
```

### Basic Playwright Test

```typescript
// tests/e2e/helpdesk-flow.spec.ts
import { test, expect } from '@playwright/test';

test.describe('Helpdesk Module', () => {
  test('Guest can submit helpdesk ticket', async ({ page }) => {
    // Navigate to guest form
    await page.goto('/helpdesk/create');
    
    // Fill form
    await page.fill('input[name="title"]', 'Network Issue');
    await page.fill('textarea[name="description"]', 'Unable to connect to network');
    await page.selectOption('select[name="category"]', 'network');
    
    // Submit form
    await page.click('button[type="submit"]');
    
    // Verify success message
    await expect(page.locator('.success-message')).toBeVisible();
    await expect(page.locator('.ticket-number')).toContainText('TK-');
  });
});
```

### Playwright with Livewire

```typescript
test('Livewire real-time validation', async ({ page }) => {
  await page.goto('/helpdesk/create');
  
  // Trigger validation
  await page.fill('input[name="title"]', '');
  await page.blur('input[name="title"]');
  
  // Wait for Livewire validation
  await page.waitForSelector('.error-message');
  
  // Verify error message
  await expect(page.locator('.error-message')).toContainText('required');
});
```

### Accessibility Testing

```typescript
import { test, expect } from '@playwright/test';
import { injectAxe, checkA11y } from '@axe-core/playwright';

test('Accessibility - Helpdesk form', async ({ page }) => {
  await page.goto('/helpdesk/create');
  
  // Inject axe-core
  await injectAxe(page);
  
  // Run WCAG 2.2 AA checks
  await checkA11y(page, null, {
    detailedReport: true,
    detailedReportOptions: {
      html: true,
    },
  });
});
```

---

## BrowserStack Integration

### BrowserStack MCP Server

ICTServe v3.6.1 includes a BrowserStack MCP server for enhanced testing capabilities.

### Configuration

Add to your `.env`:

```bash
# BrowserStack Configuration
BROWSERSTACK_USERNAME=your_username
BROWSERSTACK_ACCESS_KEY=your_access_key
BROWSERSTACK_ENABLED=true

# Percy + BrowserStack Integration
PERCY_TOKEN=your_percy_token
PERCY_BROWSERSTACK_INTEGRATION=true
```

### Starting BrowserStack MCP Server

```bash
php artisan mcp:browserstack
```

This starts the MCP server with the following tools:

**Visual Testing Tools:**
- `browserstack_create_percy_session` - Create Percy visual testing session
- `browserstack_run_percy_visual_test` - Execute Percy visual tests
- `browserstack_mobile_visual_test` - Run mobile visual regression tests

**Accessibility Tools:**
- `browserstack_accessibility_test` - Run accessibility tests with Percy
- `browserstack_wcag_compliance_scan` - Run WCAG 2.2 AA compliance scan
- `browserstack_focus_state_validation` - Validate focus state accessibility
- `browserstack_color_contrast_validation` - Validate color contrast ratios
- `browserstack_keyboard_navigation_test` - Test keyboard navigation

**Live Session Tools:**
- `browserstack_create_live_session` - Create Live session for debugging
- `browserstack_capture_live_percy_snapshot` - Capture Percy snapshot during Live session
- `browserstack_create_visual_issue` - Create visual issue report from Live session

**Cross-Browser Tools:**
- `browserstack_run_cross_browser_test` - Run cross-browser visual tests
- `browserstack_get_browsers` - Get available browsers and devices

### MCP Configuration

The BrowserStack MCP server is configured in `.mcp.json`:

```json
{
  "mcpServers": {
    "browserstack": {
      "command": "php",
      "args": ["artisan", "mcp:browserstack"],
      "env": {
        "BROWSERSTACK_USERNAME": "",
        "BROWSERSTACK_ACCESS_KEY": "",
        "PERCY_TOKEN": "",
        "PERCY_ENABLED": "false"
      },
      "disabled": false,
      "autoApprove": [
        "browserstack_validate_config",
        "browserstack_create_percy_session",
        "browserstack_run_percy_visual_test",
        "browserstack_accessibility_test"
      ]
    }
  }
}
```

---

## MCP Server Configuration

### Playwright MCP

The Playwright MCP server is already configured in `.mcp.json`:

```json
{
  "playwright": {
    "command": "npx",
    "args": ["-y", "@playwright/mcp@latest"],
    "disabled": false,
    "autoApprove": [
      "browser_navigate",
      "browser_click",
      "browser_snapshot",
      "browser_fill",
      "browser_evaluate",
      "browser_take_screenshot"
    ]
  }
}
```

### Percy MCP (Recommended Addition)

To add Percy MCP support, update `.mcp.json`:

```json
{
  "percy": {
    "command": "npx",
    "args": ["-y", "@percy/cli"],
    "env": {
      "PERCY_TOKEN": "your_percy_token"
    },
    "disabled": false,
    "autoApprove": [
      "percy_snapshot",
      "percy_finalize",
      "percy_build_status"
    ]
  }
}
```

---

## Testing Workflows

### Local Development

```bash
# Run Playwright tests only
npm run test:e2e

# Run Playwright tests with Percy
npm run test:e2e:percy

# Run specific test file with Percy
npm run test:e2e:percy -- helpdesk.spec.ts

# Run Percy with BrowserStack
npm run test:percy:browserstack
```

### Continuous Integration

```bash
# Install Playwright browsers
npx playwright install

# Run full test suite with Percy
npm run ci:percy

# Run parallel Percy tests
npm run ci:percy:parallel
```

### Development Workflows

```bash
# Quick Percy validation
npm run dev:percy:quick

# Percy dashboard tests
npm run dev:percy:dashboard

# Percy form tests
npm run dev:percy:forms

# Percy accessibility tests
npm run dev:percy:accessibility

# Percy responsive tests
npm run dev:percy:responsive
```

---

## CI/CD Integration

### GitHub Actions

Create `.github/workflows/visual-testing.yml`:

```yaml
name: Visual Testing

on:
  push:
    branches: [main, develop]
  pull_request:
    branches: [main, develop]

jobs:
  percy-visual-tests:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '18'
      
      - name: Setup PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.2'
      
      - name: Install Dependencies
        run: |
          composer install --no-interaction
          npm ci
      
      - name: Install Playwright Browsers
        run: npx playwright install --with-deps
      
      - name: Start Laravel Server
        run: |
          php artisan serve &
          sleep 5
      
      - name: Run Percy Visual Tests
        env:
          PERCY_TOKEN: ${{ secrets.PERCY_TOKEN }}
          PERCY_BRANCH: ${{ github.head_ref }}
          PERCY_TARGET_BRANCH: develop
        run: npm run ci:percy
      
      - name: Upload Test Results
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: percy-test-results
          path: percy-reports/
```

### Environment Variables for CI

Set these secrets in your GitHub repository:

- `PERCY_TOKEN` - Your Percy project token
- `BROWSERSTACK_USERNAME` - BrowserStack username (optional)
- `BROWSERSTACK_ACCESS_KEY` - BrowserStack access key (optional)

---

## Troubleshooting

### Percy Issues

**Problem**: Percy snapshots not being captured

```bash
# Verify Percy token
npm run percy:token-validate

# Check Percy configuration
npm run percy:config-validate

# View Percy build status
npm run percy:status
```

**Problem**: Dynamic content visible in snapshots

Solution: Update `percy.config.js` to hide dynamic elements:

```javascript
percyCSS: `
  .your-dynamic-class { display: none !important; }
`
```

### Playwright Issues

**Problem**: Tests timing out

Solution: Increase timeout in `playwright.config.ts`:

```typescript
use: {
  actionTimeout: 10000, // 10 seconds
  navigationTimeout: 30000, // 30 seconds
}
```

**Problem**: Livewire state not updated

Solution: Add explicit wait:

```typescript
await page.waitForTimeout(500);
// or
await page.waitForSelector('[wire\\:id]');
```

### BrowserStack Issues

**Problem**: MCP server not starting

```bash
# Validate BrowserStack configuration
php artisan mcp:browserstack

# Check environment variables
echo $BROWSERSTACK_USERNAME
echo $BROWSERSTACK_ACCESS_KEY
```

**Problem**: Percy + BrowserStack integration not working

Solution: Ensure both tokens are set:

```bash
# .env
PERCY_TOKEN=your_percy_token
BROWSERSTACK_USERNAME=your_username
BROWSERSTACK_ACCESS_KEY=your_access_key
PERCY_BROWSERSTACK_INTEGRATION=true
```

---

## Best Practices

### 1. Snapshot Naming

Use descriptive names that reflect the component and state:

```typescript
// Good
await percySnapshot(page, 'Helpdesk Form - Empty State');
await percySnapshot(page, 'Helpdesk Form - Validation Errors');
await percySnapshot(page, 'Helpdesk Form - Success Message');

// Bad
await percySnapshot(page, 'Test 1');
await percySnapshot(page, 'Form');
```

### 2. Wait for Livewire

Always wait for Livewire components to initialize:

```typescript
// Wait for Livewire component
await page.waitForSelector('[wire\\:id]');

// Wait for network idle
await page.waitForLoadState('networkidle');
```

### 3. Hide Dynamic Content

Configure `percyCSS` to hide timestamps, user-specific data:

```javascript
percyCSS: `
  .dynamic-timestamp { display: none !important; }
  .user-avatar { visibility: hidden !important; }
  [wire:loading] { display: none !important; }
`
```

### 4. Test Multiple Viewports

Test responsive design across breakpoints:

```typescript
await percySnapshot(page, 'Component Name', {
  widths: [375, 768, 1280, 1920],
});
```

### 5. Accessibility First

Run accessibility checks before visual snapshots:

```typescript
// Check accessibility
await injectAxe(page);
await checkA11y(page);

// Then capture visual snapshot
await percySnapshot(page, 'Accessible Component');
```

---

## Migration from Laravel Dusk

If you have existing Laravel Dusk tests, follow this migration guide:

### 1. Identify Dusk Tests

```bash
find tests/Browser -name "*.php"
```

### 2. Convert to Playwright

**Dusk:**
```php
$browser->visit('/login')
    ->type('email', 'user@example.com')
    ->type('password', 'password')
    ->press('Login')
    ->assertPathIs('/dashboard');
```

**Playwright:**
```typescript
await page.goto('/login');
await page.fill('input[name="email"]', 'user@example.com');
await page.fill('input[name="password"]', 'password');
await page.click('button[type="submit"]');
await expect(page).toHaveURL('/dashboard');
```

### 3. Add Percy Snapshots

```typescript
// Before submission
await percySnapshot(page, 'Login Form - Empty');

// After submission
await percySnapshot(page, 'Dashboard - Authenticated User');
```

### 4. Remove Dusk Dependencies

```bash
# Remove Dusk
composer remove laravel/dusk

# Remove Dusk tests
rm -rf tests/Browser
```

---

## Resources

- **Percy Documentation**: https://docs.percy.io/
- **Playwright Documentation**: https://playwright.dev/
- **BrowserStack Documentation**: https://www.browserstack.com/docs
- **ICTServe Documentation**: `_reference/versions/v3.6.1_*.md`
- **Laravel Boost MCP**: `docs/Laravel-Boost.md`

---

## Support

For ICTServe v3.6.1 specific issues:

1. Check Laravel logs: `storage/logs/laravel.log`
2. Run configuration validation: `php artisan percy:validate-config`
3. Review test reports: `percy-reports/`
4. Check MCP server status: `php artisan mcp:browserstack`

---

**Last Updated**: December 2025  
**Version**: ICTServe v3.6.1  
**Technology Stack**: Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Playwright 1.57.0, Percy 1.31.6
