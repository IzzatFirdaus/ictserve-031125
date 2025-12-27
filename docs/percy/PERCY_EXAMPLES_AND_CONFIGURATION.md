# Percy Visual Testing Examples and Configuration Guide

## Overview

This comprehensive guide provides examples and configuration patterns for Percy visual testing integration with ICTServe v3.6.1. The integration supports both Playwright 1.56.1 (primary) and Laravel Dusk (redundancy) test frameworks, designed to work seamlessly with the True Hybrid Architecture, Bahasa Melayu interface, and the Laravel 12.43.1 + Livewire 3.7.3 + Filament 4.3.1 technology stack.

> **Note**: Primary visual testing is performed through Playwright integration. Laravel Dusk serves as a redundancy layer for scenarios where Playwright may not be suitable.

## Table of Contents

1. [Quick Start Examples](#quick-start-examples)
2. [Configuration Examples](#configuration-examples)
3. [Test Pattern Examples](#test-pattern-examples)
4. [Laravel Dusk Percy Integration](#laravel-dusk-percy-integration)
5. [Environment-Specific Configuration](#environment-specific-configuration)
6. [CI/CD Integration Examples](#cicd-integration-examples)
7. [Troubleshooting Guide](#troubleshooting-guide)

---

## Quick Start Examples

### Basic Percy Snapshot

```typescript
import { test, expect } from '@playwright/test';
import { takePercySnapshot } from './utils/percy-utils';

test('Basic visual test', async ({ page }) => {
  await page.goto('/');
  await page.waitForLoadState('domcontentloaded');
  
  // Take a basic Percy snapshot
  await takePercySnapshot(page, {
    name: 'Homepage - Basic Test',
    userType: 'guest',
  });
  
  // Continue with regular assertions
  await expect(page).toHaveTitle(/ICTServe/);
});
```

### Responsive Visual Testing

```typescript
import { takeResponsiveSnapshots } from './utils/percy-utils';

test('Responsive visual test', async ({ page }) => {
  await page.goto('/dashboard');
  await page.waitForLoadState('domcontentloaded');
  
  // Take snapshots across multiple viewport sizes (375px, 768px, 1280px)
  await takeResponsiveSnapshots(page, 'Dashboard - Responsive', {
    userType: 'authenticated',
    validateBahasaMelayu: true,
  });
});
```

### Hybrid Architecture Testing (Guest vs Authenticated)

```typescript
test('Guest vs Authenticated comparison', async ({ page, authenticatedPage }) => {
  // Test guest workflow
  await page.goto('/helpdesk');
  await takePercySnapshot(page, {
    name: 'Helpdesk Form - Guest User',
    userType: 'guest',
  });
  
  // Test authenticated workflow
  await authenticatedPage.goto('/helpdesk');
  await takePercySnapshot(authenticatedPage, {
    name: 'Helpdesk Form - Authenticated User',
    userType: 'authenticated',
  });
});
```

---

## Configuration Examples

### Percy Configuration File (percy.config.js)

```javascript
/**
 * Percy Configuration for ICTServe v3.6.1 Visual Testing
 */
export default {
  version: 2,
  
  // Project configuration
  projectName: process.env.PERCY_PROJECT || 'ictserve',
  
  // Discovery settings
  discovery: {
    allowedHostnames: ['localhost', '127.0.0.1'],
    networkIdleTimeout: 100,
    disableCache: false,
  },
  
  // Default snapshot configuration
  snapshot: {
    // Responsive breakpoints for ICTServe v3.6.1
    widths: [375, 768, 1024, 1280, 1920],
    minHeight: 1024,
    
    // CSS to hide dynamic content
    percyCSS: `
      /* Hide dynamic timestamps and loading states */
      .dynamic-timestamp { display: none !important; }
      .loading-spinner { visibility: hidden !important; }
      
      /* Hide Livewire loading states */
      [wire\\:loading] { display: none !important; }
      
      /* Hide Filament admin dynamic content */
      .fi-loading { display: none !important; }
      .fi-notification { display: none !important; }
      
      /* Ensure consistent focus states */
      *:focus { outline: 2px solid #3b82f6 !important; }
    `,
    
    enableJavaScript: true,
    waitForTimeout: 1000,
  },
  
  // Upload configuration
  upload: {
    networkIdleTimeout: 750,
  },
  
  // Client information
  clientInfo: 'ICTServe v3.6.1 Percy Integration',
  environmentInfo: 'Laravel 12.43.1, Livewire 3.7.3, Filament 4.3.1, Playwright 1.56.1',
};
```

### Laravel Configuration (config/percy.php)

```php
<?php

declare(strict_types=1);

return [
    // Authentication
    'token' => env('PERCY_TOKEN'),
    'project' => env('PERCY_PROJECT', 'ictserve'),
    'enabled' => env('PERCY_ENABLED', true),
    
    // Build configuration
    'branch' => env('PERCY_BRANCH', 'develop'),
    'target_branch' => env('PERCY_TARGET_BRANCH', 'develop'),
    
    // Snapshot configuration
    'snapshot' => [
        'widths' => [375, 768, 1024, 1280, 1920],
        'min_height' => 1024,
        'percy_css' => [
            '.dynamic-timestamp { display: none !important; }',
            '.loading-spinner { visibility: hidden !important; }',
            '[wire\\:loading] { display: none !important; }',
        ],
    ],
    
    // ICTServe v3.6.1 specific configuration
    'ictserve' => [
        'hybrid_architecture' => [
            'guest_selectors' => ['.guest-form', '.guest-status'],
            'authenticated_selectors' => ['.dashboard', '.profile'],
            'admin_selectors' => ['.filament-admin', '.admin-panel'],
        ],
        'bahasa_melayu' => [
            'validate_language' => true,
            'exclude_language_switcher' => true,
        ],
    ],
    
    // Error handling
    'error_handling' => [
        'graceful_degradation' => true,
        'retry_attempts' => 3,
        'fail_on_error' => env('PERCY_FAIL_ON_ERROR', false),
    ],
];
```

---

## Test Pattern Examples

### 1. Dashboard Responsive Testing

```typescript
test.describe('Dashboard Responsive Visual Testing', () => {
  test('Mobile layout (375px)', async ({ page }) => {
    await page.setViewportSize({ width: 375, height: 667 });
    await page.goto('/staff/dashboard');
    await waitForStableContent(page);
    
    await takePercySnapshot(page, {
      name: 'Dashboard Mobile Layout',
      userType: 'authenticated',
      widths: [375],
      minHeight: 667,
      validateBahasaMelayu: true,
    });
  });
  
  test('Tablet layout (768px)', async ({ page }) => {
    await page.setViewportSize({ width: 768, height: 1024 });
    await page.goto('/staff/dashboard');
    await waitForStableContent(page);
    
    await takePercySnapshot(page, {
      name: 'Dashboard Tablet Layout',
      userType: 'authenticated',
      widths: [768],
      minHeight: 1024,
    });
  });
  
  test('Desktop layout (1280px)', async ({ page }) => {
    await page.setViewportSize({ width: 1280, height: 800 });
    await page.goto('/staff/dashboard');
    await waitForStableContent(page);
    
    await takePercySnapshot(page, {
      name: 'Dashboard Desktop Layout',
      userType: 'authenticated',
      widths: [1280, 1920],
    });
  });
});
```

### 2. Form State Testing

```typescript
test.describe('Helpdesk Form Visual States', () => {
  test('Empty form state', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForStableContent(page);
    
    await takePercySnapshot(page, {
      name: 'Helpdesk Form - Empty State',
      userType: 'guest',
      widths: [375, 768, 1280],
      percyCSS: `
        input, select, textarea { 
          border: 2px solid #e2e8f0 !important; 
        }
      `,
    });
  });
  
  test('Filled form state', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForStableContent(page);
    
    // Fill form fields
    await page.locator('input[name*="name"]').first().fill('Ahmad bin Abdullah');
    await page.locator('input[type="email"]').first().fill('ahmad@example.com');
    
    await takePercySnapshot(page, {
      name: 'Helpdesk Form - Filled State',
      userType: 'guest',
      widths: [375, 768, 1280],
      percyCSS: `
        input:not(:placeholder-shown) { 
          border: 2px solid #10b981 !important; 
        }
      `,
    });
  });
  
  test('Validation error state', async ({ page }) => {
    await page.goto('/helpdesk/create');
    await waitForStableContent(page);
    
    // Submit empty form to trigger validation
    await page.locator('button[type="submit"]').first().click();
    await page.waitForTimeout(500);
    
    await takePercySnapshot(page, {
      name: 'Helpdesk Form - Validation Error State',
      userType: 'guest',
      widths: [375, 768, 1280],
      percyCSS: `
        .error, [aria-invalid="true"] { 
          border: 2px solid #ef4444 !important; 
        }
      `,
    });
  });
});
```

### 3. Accessibility Visual Compliance

```typescript
test.describe('WCAG 2.2 AA Visual Compliance', () => {
  test('Homepage accessibility compliance', async ({ page }) => {
    await page.goto('/');
    await waitForStableContent(page);
    
    await takeAccessibilitySnapshot(page, 'Homepage - Accessibility Compliance', {
      userType: 'guest',
      widths: [375, 768, 1280],
      validateBahasaMelayu: true,
    });
  });
  
  test('Focus indicators validation', async ({ page }) => {
    await page.goto('/login');
    await waitForStableContent(page);
    
    // Focus on first interactive element
    await page.locator('input, button, a').first().focus();
    
    await takePercySnapshot(page, {
      name: 'Focus Indicators - Visual Validation',
      userType: 'guest',
      widths: [768, 1280],
      percyCSS: `
        *:focus, *:focus-visible { 
          outline: 3px solid #ff6b35 !important; 
          outline-offset: 2px !important; 
        }
      `,
    });
  });
});
```

### 4. Cross-Browser Visual Consistency

```typescript
test.describe('Cross-Browser Visual Consistency', () => {
  test('Homepage cross-browser consistency', async ({ page, browserName }) => {
    test.info().annotations.push({ type: 'browser', description: browserName });
    
    await page.goto('/');
    await waitForStableContent(page);
    
    await takePercySnapshot(page, {
      name: `Homepage - Cross-Browser (${browserName})`,
      userType: 'guest',
      widths: [375, 768, 1280],
    });
  });
});
```

### 5. Admin Panel (Filament) Testing

```typescript
test.describe('Filament Admin Panel Visual Testing', () => {
  test('Admin dashboard', async ({ adminPage }) => {
    await adminPage.goto('/admin');
    await waitForStableContent(adminPage);
    
    await takePercySnapshot(adminPage, {
      name: 'Filament Admin Dashboard',
      userType: 'admin',
      widths: [1024, 1280, 1920],
      percyCSS: `
        .fi-sidebar { visibility: visible !important; }
        .fi-loading { display: none !important; }
      `,
    });
  });
});
```

---

## Laravel Dusk Percy Integration

Laravel Dusk provides browser automation testing capabilities and serves as a **redundancy layer** for Percy visual testing after the primary Playwright integration.

### Prerequisites

```bash
# Install Laravel Dusk
composer require laravel/dusk --dev
php artisan dusk:install

# Install Percy CLI and dependencies
npm install -g @percy/cli
npm install @percy/selenium-webdriver
```

### Basic Dusk Percy Snapshot

```php
<?php

declare(strict_types=1);

namespace Tests\Browser;

use Tests\Browser\Traits\PercyDuskTrait;
use Tests\DuskTestCase;

class VisualTest extends DuskTestCase
{
    use PercyDuskTrait;

    public function testHomepageVisual(): void
    {
        $this->browse(function ($browser) {
            $browser->visit('/');
            
            // Wait for content to stabilize
            $this->waitForStableContent($browser);
            
            // Take Percy snapshot
            $this->takePercySnapshot($browser, 'Homepage - Dusk Test', [
                'widths' => [375, 768, 1280],
                'userType' => 'guest',
            ]);
            
            // Continue with regular assertions
            $browser->assertSee('ICTServe');
        });
    }
}
```

### Responsive Dusk Testing

```php
public function testResponsiveLayout(): void
{
    $this->browse(function ($browser) {
        // Mobile viewport
        $browser->resize(375, 667);
        $browser->visit('/');
        $this->waitForStableContent($browser);
        
        $this->takePercySnapshot($browser, 'Homepage - Mobile (375px)', [
            'widths' => [375],
            'minHeight' => 667,
        ]);
        
        // Tablet viewport
        $browser->resize(768, 1024);
        $this->takePercySnapshot($browser, 'Homepage - Tablet (768px)', [
            'widths' => [768],
        ]);
        
        // Desktop viewport
        $browser->resize(1280, 800);
        $this->takePercySnapshot($browser, 'Homepage - Desktop (1280px)', [
            'widths' => [1280, 1920],
        ]);
    });
}
```

### Hybrid Architecture Testing with Dusk

```php
public function testGuestWorkflow(): void
{
    $this->browse(function ($browser) {
        $browser->visit('/helpdesk');
        $this->waitForStableContent($browser);
        
        $this->takePercySnapshot($browser, 'Helpdesk - Guest User', [
            'userType' => 'guest',
        ]);
    });
}

public function testAuthenticatedWorkflow(): void
{
    $user = User::factory()->create();
    
    $this->browse(function ($browser) use ($user) {
        $browser->loginAs($user)
            ->visit('/staff/dashboard');
        
        $this->waitForStableContent($browser);
        
        $this->takePercySnapshot($browser, 'Dashboard - Authenticated User', [
            'userType' => 'authenticated',
        ]);
    });
}
```

### Using the PercyDuskTrait

The `PercyDuskTrait` provides reusable methods for Percy integration:

| Method | Description |
|--------|-------------|
| `takePercySnapshot($browser, $name, $options)` | Take a Percy visual snapshot |
| `takeResponsivePercySnapshots($browser, $name, $options)` | Take snapshots at multiple viewports |
| `takeAccessibilityPercySnapshot($browser, $name, $options)` | Take accessibility-focused snapshot |
| `waitForLivewireStable($browser, $timeout)` | Wait for Livewire to stabilize |
| `waitForStableContent($browser, $timeout)` | Wait for all content to stabilize |
| `setResponsiveViewport($browser, $size)` | Set viewport to predefined size |
| `takePercySnapshotHideDynamic($browser, $name, $options)` | Snapshot with dynamic content hidden |

### Running Dusk Tests with Percy

```bash
# Run all Dusk tests with Percy
npx percy exec -- php artisan dusk

# Run specific test file
npx percy exec -- php artisan dusk tests/Browser/Examples/PercyExampleTest.php

# Run specific test method
npx percy exec -- php artisan dusk --filter=basic_homepage_snapshot

# Run without Percy (functional tests only)
php artisan dusk
```

### Dusk Percy Configuration Options

```php
$this->takePercySnapshot($browser, 'My Snapshot', [
    // Viewport widths to capture
    'widths' => [375, 768, 1280],
    
    // Minimum height for snapshot
    'minHeight' => 1024,
    
    // Enable JavaScript during capture
    'enableJavaScript' => true,
    
    // Custom CSS to inject
    'percyCSS' => '.dynamic { display: none; }',
    
    // Scope snapshot to specific element
    'scope' => '#main-content',
    
    // User type for Hybrid Architecture
    'userType' => 'guest', // 'guest', 'authenticated', 'admin'
]);
```

### Dusk Example Files

| File | Description |
|------|-------------|
| `tests/Browser/Examples/PercyExampleTest.php` | Comprehensive Percy examples for Dusk |
| `tests/Browser/Traits/PercyDuskTrait.php` | Reusable Percy trait for Dusk tests |
| `tests/Browser/Examples/README.md` | Dusk examples documentation |

---

## Environment-Specific Configuration

### Local Development (config/percy.local.php)

```php
return [
    'enabled' => !empty(env('PERCY_TOKEN')),
    'debug' => true,
    'upload_timeout' => 60,
    
    'snapshot' => [
        'widths' => [768, 1280], // Fewer widths for faster local testing
        'min_height' => 800,
        'wait_for_timeout' => 500,
    ],
    
    'error_handling' => [
        'graceful_degradation' => true,
        'retry_attempts' => 1,
        'fail_on_error' => false,
    ],
    
    'performance' => [
        'async_upload' => false, // Synchronous for easier debugging
        'cache_enabled' => false,
    ],
];
```

### Testing Environment (config/percy.testing.php)

```php
return [
    'enabled' => !empty(env('PERCY_TOKEN')),
    'debug' => false,
    'upload_timeout' => 120,
    
    'snapshot' => [
        'widths' => [375, 768, 1280, 1920], // Full responsive testing
        'min_height' => 1024,
        'wait_for_timeout' => 1000,
    ],
    
    'error_handling' => [
        'graceful_degradation' => false, // Fail tests if Percy fails
        'retry_attempts' => 3,
        'fail_on_error' => true,
    ],
    
    'ci_cd' => [
        'parallel_execution' => true,
        'build_timeout' => 600,
    ],
];
```

### Staging Environment (config/percy.staging.php)

```php
return [
    'enabled' => !empty(env('PERCY_TOKEN')),
    'debug' => false,
    'upload_timeout' => 180,
    
    'snapshot' => [
        'widths' => [375, 768, 1280], // Reduced widths for faster staging
        'min_height' => 1024,
    ],
    
    'error_handling' => [
        'graceful_degradation' => true,
        'retry_attempts' => 3,
        'fail_on_error' => false,
    ],
    
    'validation' => [
        'pre_production_checks' => true,
        'accessibility_validation' => true,
    ],
];
```

### Production Environment (config/percy.production.php)

```php
return [
    'enabled' => env('PERCY_ENABLED', false), // Disabled by default
    'debug' => false,
    'upload_timeout' => 300,
    
    'snapshot' => [
        'widths' => [375, 768, 1024, 1280, 1920], // Full testing
        'min_height' => 1024,
        'wait_for_timeout' => 2000,
    ],
    
    'error_handling' => [
        'graceful_degradation' => false,
        'retry_attempts' => 5,
        'fail_on_error' => true,
    ],
    
    'security' => [
        'require_https' => true,
        'validate_ssl' => true,
        'audit_logging' => true,
    ],
];
```

---

## CI/CD Integration Examples

### GitHub Actions Workflow

```yaml
name: Percy Visual Testing

on:
  push:
    branches: [develop, main]
  pull_request:
    branches: [develop, main]

jobs:
  percy-visual-tests:
    runs-on: ubuntu-latest
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Setup Node.js
        uses: actions/setup-node@v4
        with:
          node-version: '20'
          cache: 'npm'
      
      - name: Install dependencies
        run: npm ci
      
      - name: Install Playwright browsers
        run: npx playwright install --with-deps
      
      - name: Run Percy visual tests
        env:
          PERCY_TOKEN: ${{ secrets.PERCY_TOKEN }}
          PERCY_BRANCH: ${{ github.head_ref || github.ref_name }}
          PERCY_PULL_REQUEST: ${{ github.event.pull_request.number }}
        run: npm run ci:percy
      
      - name: Upload test results
        if: always()
        uses: actions/upload-artifact@v4
        with:
          name: playwright-report
          path: playwright-report/
```

### Parallel CI Execution

```yaml
jobs:
  percy-parallel:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        shard: [1, 2, 3, 4]
    
    steps:
      - uses: actions/checkout@v4
      
      - name: Run Percy tests (shard ${{ matrix.shard }})
        env:
          PERCY_TOKEN: ${{ secrets.PERCY_TOKEN }}
          PERCY_PARALLEL_NONCE: ${{ github.run_id }}
          PERCY_PARALLEL_TOTAL: 4
        run: |
          npx playwright test --shard=${{ matrix.shard }}/4
```

### npm Scripts Reference

```bash
# Basic Percy execution
npm run test:e2e:percy                    # Run all tests with Percy
npm run test:e2e:percy:ui                 # Run with Playwright UI
npm run test:e2e:percy:debug              # Run in debug mode

# Specific test suites
npm run test:e2e:percy:helpdesk           # Helpdesk tests with Percy
npm run test:e2e:percy:loan               # Loan module tests with Percy
npm run test:e2e:percy:performance        # Performance validation tests

# Development shortcuts
npm run dev:percy:quick                   # Quick Percy validation
npm run dev:percy:dashboard               # Dashboard tests only
npm run dev:percy:forms                   # Form tests only
npm run dev:percy:accessibility           # Accessibility tests only
npm run dev:percy:responsive              # Responsive tests only

# CI/CD scripts
npm run ci:percy                          # CI Percy execution
npm run ci:percy:parallel                 # Parallel CI execution
npm run ci:percy:branch                   # Branch-specific execution

# Percy utilities
npm run percy:validate                    # Validate Percy configuration
npm run percy:status                      # Check Percy status
npm run percy:health-check                # Health check
npm run percy:cleanup                     # Cleanup Percy resources

# Graceful degradation
npm run test:e2e:no-percy                 # Run tests without Percy
npm run test:e2e:local-percy              # Local-only Percy mode
npm run test:e2e:fallback                 # Fallback mode
```

---

## Troubleshooting Guide

### Common Issues

#### 1. Percy Token Not Set

```bash
# Error: Percy token not found
# Solution: Set the PERCY_TOKEN environment variable

# Windows PowerShell
$env:PERCY_TOKEN = "your_percy_token_here"

# Windows CMD
set PERCY_TOKEN=your_percy_token_here

# Linux/macOS
export PERCY_TOKEN=your_percy_token_here
```

#### 2. Snapshots Not Captured

```typescript
// Check if Percy is enabled
import { isPercyEnabled } from './utils/percy-utils';

if (!isPercyEnabled()) {
  console.log('Percy is disabled - set PERCY_TOKEN to enable');
}
```

#### 3. Flaky Visual Tests

```typescript
// Increase wait times for content stabilization
await waitForStableContent(page);
await page.waitForTimeout(1000); // Additional wait if needed

// Use more specific selectors
await page.waitForSelector('[data-testid="content-loaded"]', {
  state: 'visible',
  timeout: 10000,
});
```

#### 4. Dynamic Content Causing Differences

```typescript
// Add custom Percy CSS to hide dynamic content
await takePercySnapshot(page, {
  name: 'Page with Dynamic Content',
  percyCSS: `
    .dynamic-timestamp { display: none !important; }
    .realtime-counter { visibility: hidden !important; }
    .user-avatar { opacity: 0 !important; }
  `,
});
```

#### 5. Livewire Components Not Loaded

```typescript
// Wait for Livewire to initialize
await page.waitForFunction(() => {
  return typeof (window as any).Livewire !== 'undefined';
}, { timeout: 10000 });

// Wait for Livewire loading states to complete
await page.waitForFunction(() => {
  return !document.querySelector('[wire\\:loading]');
}, { timeout: 5000 });
```

### Debug Mode

```bash
# Enable Percy debug logging
export PERCY_LOGLEVEL=debug

# Run tests with verbose output
npm run test:e2e:percy -- --reporter=list

# Run validation tests
npx playwright test tests/e2e/percy-integration-validation.spec.ts
```

### Health Check

```bash
# Run Percy health check
npm run percy:health-check

# Validate configuration
npm run percy:config-validate

# Check token validity
npm run percy:token-validate
```

---

## File Reference

### Playwright Test Files

| File | Description |
|------|-------------|
| `tests/e2e/percy-enhanced-examples.spec.ts` | 55 comprehensive Percy-enhanced test examples |
| `tests/e2e/percy-integration-demo.spec.ts` | Percy integration demonstration tests |
| `tests/e2e/percy-setup-validation.spec.ts` | Percy setup validation tests |
| `tests/e2e/percy-performance-validation.spec.ts` | Performance validation tests |
| `tests/e2e/percy-degradation-validation.spec.ts` | Graceful degradation tests |
| `tests/e2e/examples/percy-example-tests.spec.ts` | Playwright Percy example tests |
| `tests/e2e/examples/README.md` | Playwright examples documentation |

### Laravel Dusk Test Files (Redundancy Layer)

| File | Description |
|------|-------------|
| `tests/Browser/Examples/PercyExampleTest.php` | Comprehensive Percy examples for Dusk |
| `tests/Browser/Traits/PercyDuskTrait.php` | Reusable Percy trait for Dusk tests |
| `tests/Browser/Examples/README.md` | Dusk examples documentation |

### Utility Files

| File | Description |
|------|-------------|
| `tests/e2e/utils/percy-utils.ts` | Percy utility functions and configurations |
| `tests/e2e/fixtures/ictserve-fixtures.ts` | ICTServe-specific test fixtures |

### Configuration Files

| File | Description |
|------|-------------|
| `percy.config.js` | Main Percy configuration |
| `config/percy.php` | Laravel Percy configuration |
| `config/percy.local.php` | Local development overrides |
| `config/percy.testing.php` | Testing environment overrides |
| `config/percy.staging.php` | Staging environment overrides |
| `config/percy.production.php` | Production environment overrides |

### Documentation Files

| File | Description |
|------|-------------|
| `tests/e2e/PERCY_INTEGRATION_GUIDE.md` | Integration guide |
| `docs/percy/PERCY_EXAMPLES_AND_CONFIGURATION.md` | This file |
| `docs/percy/ERROR_HANDLING_IMPLEMENTATION.md` | Error handling documentation |
| `docs/percy/GRACEFUL_DEGRADATION.md` | Graceful degradation documentation |
| `docs/percy/PERCY_PERFORMANCE_OPTIMIZATION.md` | Performance optimization guide |

---

## Version Information

- **ICTServe Version**: 3.6.1
- **Laravel**: 12.43.1
- **Livewire**: 3.7.3
- **Filament**: 4.3.1
- **Playwright**: 1.56.1
- **Laravel Dusk**: (when installed)
- **Percy CLI**: 1.31.6
- **@percy/playwright**: 1.0.10

---

*Document created: December 26, 2025*
*Last updated: December 26, 2025*
*Author: Pasukan Pembangunan BPM MOTAC*
