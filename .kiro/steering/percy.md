---
inclusion:
  fileMatchPattern:
    - 'tests/percy/**/*.ts'
    - 'tests/e2e/**/*.spec.ts'
    - 'percy.config.js'
    - 'playwright*.config.ts'
    - '**/utils/percy-utils.ts'
applyWhen:
  - Visual regression testing
  - Percy snapshot creation
  - E2E test development with visual validation
  - Playwright test configuration
---

# Percy Visual Testing Steering Guide for ICTServe v3.6.1

This steering file provides comprehensive guidance for Percy visual testing integration with the ICTServe Laravel application using Playwright.

## Project Context

ICTServe v3.6.1 uses:

- **Laravel 12.43.1** + **Livewire 3.7.3** + **Filament 4.3.1**
- **Playwright 1.56.1** for E2E testing
- **Percy** for AI-powered visual regression testing
- **True Hybrid Architecture** (guest + authenticated users)
- **Bahasa Melayu** interface (v3.6.0+)
- **WCAG 2.2 AA** compliance requirements

## Percy Configuration

### Environment Variables (Required)

```bash
# Primary token (already configured in .env)
PERCY_TOKEN=web_5d6dc49aa1266a5a9ff36a0edecd719aba085b4a690f001f11415e3db780ae79
PERCY_PROJECT=ictserve
PERCY_ENABLED=true
PERCY_BRANCH=develop

# Optional configuration
PERCY_FAIL_ON_ERROR=false
SKIP_PERCY=false  # Never set to true unless debugging
```

### Critical Setup Requirements

1. **Always use `percy exec` command**:

   ```bash
   # ✅ Correct
   npx percy exec -- npx playwright test
   
   # ❌ Wrong (Percy will be disabled)
   npx playwright test
   ```

2. **Load environment variables**:

   ```bash
   # Use dotenv-cli for .env loading
   dotenv npx percy exec -- npx playwright test
   ```

3. **Package.json scripts** (update these):

   ```json
   {
     "scripts": {
       "test:e2e": "dotenv npx playwright test",
       "test:e2e:percy": "dotenv npx percy exec -- npx playwright test",
       "test:percy": "dotenv npx percy exec --config ./percy.config.js -- npx playwright test --config=playwright.percy.config.ts"
     }
   }
   ```

## Percy Utilities Usage

### Use ICTServe-Specific Utilities
Always import from the project's Percy utilities:

```typescript
// ✅ Correct - Use project utilities
import { 
  takePercySnapshot, 
  takeResponsiveSnapshots,
  takeAccessibilitySnapshot,
  takeHybridArchitectureSnapshots,
  waitForStableContent 
} from "./utils/percy-utils";

// ❌ Avoid direct imports unless necessary
import percySnapshot from '@percy/playwright';
```

### Standard Snapshot Pattern

```typescript
test('Visual test with ICTServe optimizations', async ({ page }) => {
  await page.goto('/dashboard');
  
  // Wait for Livewire components to stabilize
  await waitForStableContent(page);
  
  // Take Percy snapshot with ICTServe configurations
  await takePercySnapshot(page, {
    name: 'Dashboard - Main View',
    userType: 'authenticated',
    validateBahasaMelayu: true,
    wcagLevel: 'AA'
  });
});
```

## ICTServe-Specific Configurations

### User Type Testing (Hybrid Architecture)

```typescript
// Test different user types
await takePercySnapshot(page, {
  name: 'Landing Page',
  userType: 'guest'  // 'guest' | 'authenticated' | 'admin'
});
```

### Responsive Testing

```typescript
// Test across multiple viewports
await takeResponsiveSnapshots(page, 'Dashboard', {
  userType: 'authenticated',
  validateBahasaMelayu: true
});
```

### Accessibility Compliance

```typescript
// WCAG 2.2 AA compliance snapshots
await takeAccessibilitySnapshot(page, 'Form Validation', {
  wcagLevel: 'AA'
});
```

### Bahasa Melayu Interface Validation

```typescript
await takePercySnapshot(page, {
  name: 'Interface - Bahasa Melayu',
  validateBahasaMelayu: true,
  percyCSS: `
    .lang-en { display: none !important; }
    .english-only { display: none !important; }
  `
});
```

## Dynamic Content Handling

### Hide ICTServe-Specific Dynamic Elements
The project's Percy configuration automatically hides:

- Dynamic timestamps (`.dynamic-timestamp`)
- Loading spinners (`.loading-spinner`, `.skeleton-loader`)
- User avatars (`.user-avatar`)
- Real-time notifications (`.notification-badge`)
- Livewire loading states (`[wire:loading]`, `.wire-loading`)
- Filament admin notifications (`.fi-loading`, `.fi-notification`)

### Custom Dynamic Content Hiding

```typescript
await takePercySnapshot(page, {
  name: 'Custom Hidden Elements',
  percyCSS: `
    /* Hide project-specific dynamic content */
    .last-login-time { display: none !important; }
    .realtime-counter { display: none !important; }
    .validation-message { display: none !important; }
    
    /* Hide language switcher (v3.6.0+ Bahasa Melayu only) */
    .language-switcher { display: none !important; }
  `
});
```

## Livewire Integration

### Wait for Livewire Components

```typescript
test('Livewire component visual test', async ({ page }) => {
  await page.goto('/helpdesk/create');
  
  // Wait for Livewire to initialize
  await waitForStableContent(page);
  
  // Fill form to trigger Livewire updates
  await page.fill('input[name="title"]', 'Test Ticket');
  
  // Wait for Livewire to process
  await page.waitForFunction(() => {
    return !document.querySelector('[wire\\:loading]');
  });
  
  await takePercySnapshot(page, {
    name: 'Helpdesk Form - Filled State',
    userType: 'authenticated'
  });
});
```

## Form State Documentation

### Capture Multiple Form States

```typescript
test('Form states documentation', async ({ page }) => {
  await page.goto('/loan/apply');
  await waitForStableContent(page);
  
  // Empty state
  await takeFormStateSnapshots(page, 'Loan Application Form');
  
  // Filled state
  await page.fill('input[name="amount"]', '50000');
  await page.fill('textarea[name="purpose"]', 'Business expansion');
  await takePercySnapshot(page, {
    name: 'Loan Application Form - Filled State'
  });
  
  // Validation error state
  await page.fill('input[name="amount"]', '');
  await page.click('button[type="submit"]');
  await page.waitForSelector('.validation-message');
  await takePercySnapshot(page, {
    name: 'Loan Application Form - Validation Errors',
    percyCSS: `
      .validation-message { display: block !important; }
    `
  });
});
```

## Performance Optimization

### Efficient Snapshot Timing

```typescript
test('Optimized snapshot timing', async ({ page }) => {
  await page.goto('/dashboard');
  
  // Wait for network idle (Livewire components loaded)
  await page.waitForLoadState('networkidle', { timeout: 5000 });
  
  // Additional wait for animations to complete
  await page.waitForTimeout(500);
  
  // Take snapshot
  await takePercySnapshot(page, {
    name: 'Dashboard - Optimized Timing'
  });
});
```

### Lazy Loading Handling

```typescript
import scrollToBottom from 'scroll-to-bottomjs';

test('Page with lazy loading', async ({ page }) => {
  await page.goto('/gallery');
  
  // Scroll to trigger lazy loading
  await page.evaluate(scrollToBottom);
  
  // Wait for images to load
  await page.waitForTimeout(2000);
  
  await takePercySnapshot(page, {
    name: 'Gallery - All Images Loaded'
  });
});
```

## Debugging Percy Issues

### Check Percy Status

```typescript
import { isPercyEnabled, getPercyBuildInfo } from "./utils/percy-utils";

test.beforeEach(async () => {
  if (!isPercyEnabled()) {
    console.warn('⚠️ Percy is disabled. Check PERCY_TOKEN environment variable.');
    console.log('Percy info:', getPercyBuildInfo());
  }
});
```

### Environment Validation

```typescript
test('Percy environment validation', async () => {
  const percyInfo = getPercyBuildInfo();
  
  expect(percyInfo.token).toBeDefined();
  expect(percyInfo.project).toBe('ictserve');
  expect(percyInfo.branch).toBe('develop');
  
  console.log('✅ Percy environment validated:', percyInfo);
});
```

## Common Patterns

### Admin Interface Testing

```typescript
test('Filament admin interface', async ({ page }) => {
  // Login as admin user
  await page.goto('/admin/login');
  await page.fill('input[name="email"]', 'admin@motac.gov.my');
  await page.fill('input[name="password"]', 'password');
  await page.click('button[type="submit"]');
  
  await page.waitForURL('/admin/dashboard');
  await waitForStableContent(page);
  
  await takePercySnapshot(page, {
    name: 'Admin Dashboard',
    userType: 'admin',
    widths: [1024, 1280, 1920] // Admin typically uses larger screens
  });
});
```

### Cross-Browser Testing

```typescript
// Use different projects in playwright.config.ts
test('Cross-browser visual consistency', async ({ page, browserName }) => {
  await page.goto('/');
  await waitForStableContent(page);
  
  await takePercySnapshot(page, {
    name: `Homepage - ${browserName}`,
    userType: 'guest'
  });
});
```

## Error Handling

### Graceful Percy Failures

```typescript
test('Test with Percy error handling', async ({ page }) => {
  await page.goto('/dashboard');
  
  try {
    await takePercySnapshot(page, {
      name: 'Dashboard - With Error Handling'
    });
  } catch (error) {
    console.warn('Percy snapshot failed, continuing test:', error.message);
    // Test continues even if Percy fails
  }
  
  // Regular test assertions
  await expect(page.locator('h1')).toContainText('Dashboard');
});
```

## CI/CD Integration

### GitHub Actions Example

```yaml
- name: Run Percy Tests
  env:
    PERCY_TOKEN: ${{ secrets.PERCY_TOKEN }}
    PERCY_BRANCH: ${{ github.head_ref }}
    PERCY_TARGET_BRANCH: develop
  run: |
    npm install
    npx playwright install
    dotenv npx percy exec -- npx playwright test
```

### Local Development

```bash
# Set environment variable for session
export PERCY_TOKEN=web_5d6dc49aa1266a5a9ff36a0edecd719aba085b4a690f001f11415e3db780ae79

# Run Percy tests
npm run test:e2e:percy

# Debug Percy issues
PERCY_DEBUG=true npm run test:e2e:percy
```

## File References

- **Percy Config**: `percy.config.js`
- **Playwright Config**: `playwright.config.ts`, `playwright.percy.config.ts`
- **Percy Utilities**: `tests/e2e/utils/percy-utils.ts`
- **Percy Tests**: `tests/percy/` directory
- **Environment**: `.env` (PERCY_TOKEN configuration)

## Best Practices Summary

1. **Always use `percy exec`** command wrapper
2. **Load environment variables** with dotenv-cli
3. **Use project-specific utilities** for consistency
4. **Wait for Livewire components** to stabilize
5. **Hide dynamic content** with percy-css
6. **Test multiple user types** (guest, authenticated, admin)
7. **Validate Bahasa Melayu interface** elements
8. **Ensure WCAG 2.2 AA compliance** in snapshots
9. **Handle lazy loading** and animations
10. **Use descriptive snapshot names** with context

## Troubleshooting

### Percy Disabled Messages
If you see `[Percy] Skipping snapshot - Percy not enabled`:

1. Check `PERCY_TOKEN` environment variable is set
2. Use `percy exec` command wrapper
3. Verify token is valid and not expired
4. Ensure `SKIP_PERCY` is not set to `true`

### Percy Service Not Running
If you see `[percy] Percy is not running, disabling snapshots`:

1. Use `npx percy exec --` command prefix
2. Install Percy CLI: `npm install -g @percy/cli`
3. Check network connectivity to Percy API

This steering guide ensures consistent Percy visual testing practices across the ICTServe project while leveraging the existing sophisticated Percy integration and utilities.
