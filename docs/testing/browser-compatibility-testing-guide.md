# Browser Compatibility Testing Guide

**ICTServe System**  
**Version**: 1.0.0  
**Date**: November 4, 2025  
**Task**: 15.1 - Browser Compatibility Validation  
**Requirements**: D03-FR-22.4, 22.5, Spec Glossary - Browser_Compatibility

---

## 1. Browser Support Matrix

### 1.1 Supported Browsers

ICTServe supports the following browsers as defined in the system specification:

| Browser | Minimum Version | Recommended Version | Support Level | Testing Priority |
|---------|----------------|---------------------|---------------|------------------|
| **Google Chrome** | 90+ | Latest stable | **FULL** | **HIGH** |
| **Mozilla Firefox** | 88+ | Latest stable | **FULL** | **HIGH** |
| **Apple Safari** | 14+ | Latest stable | **FULL** | **MEDIUM** |
| **Microsoft Edge** | 90+ (Chromium) | Latest stable | **FULL** | **HIGH** |

### 1.2 Support Level Definitions

- **FULL**: All features fully supported and tested
- **PARTIAL**: Core features supported, some advanced features may have limitations
- **UNSUPPORTED**: Not tested, may not work correctly

### 1.3 Unsupported Browsers

The following browsers are **NOT supported**:

- Internet Explorer (all versions) - End of life
- Edge Legacy (pre-Chromium versions < 90)
- Chrome < 90
- Firefox < 88
- Safari < 14
- Opera (not tested, may work due to Chromium base)
- Mobile browsers (separate testing required)

---

## 2. Testing Environments

### 2.1 Local Development Testing

**Setup Requirements**:

- Windows 10/11 or macOS 11+
- All supported browsers installed
- BrowserStack or similar cross-browser testing tool (optional)
- Playwright for automated testing

**Installation Commands**:

```powershell
# Install Playwright with browsers
npm install -D @playwright/test
npx playwright install chromium firefox webkit

# Verify installations
npx playwright --version
```

### 2.2 Automated Testing Environment

**Playwright Configuration** (`playwright.config.ts`):

```typescript
import { defineConfig, devices } from '@playwright/test';

export default defineConfig({
  testDir: './tests/Browser',
  fullyParallel: true,
  forbidOnly: !!process.env.CI,
  retries: process.env.CI ? 2 : 0,
  workers: process.env.CI ? 1 : undefined,
  reporter: 'html',
  use: {
    baseURL: 'http://localhost:8000',
    trace: 'on-first-retry',
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
      name: 'edge',
      use: { ...devices['Desktop Edge'], channel: 'msedge' },
    },
  ],

  webServer: {
    command: 'php artisan serve',
    url: 'http://localhost:8000',
    reuseExistingServer: !process.env.CI,
  },
});
```

### 2.3 CI/CD Integration

**GitHub Actions Workflow** (`.github/workflows/browser-tests.yml`):

```yaml
name: Browser Compatibility Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  test:
    timeout-minutes: 60
    runs-on: ubuntu-latest
    steps:
    - uses: actions/checkout@v3
    - uses: actions/setup-node@v3
      with:
        node-version: 18
    - name: Install dependencies
      run: npm ci
    - name: Install Playwright Browsers
      run: npx playwright install --with-deps
    - name: Run Playwright tests
      run: npx playwright test
    - uses: actions/upload-artifact@v3
      if: always()
      with:
        name: playwright-report
        path: playwright-report/
        retention-days: 30
```

---

## 3. Manual Testing Procedures

### 3.1 Pre-Testing Checklist

Before starting manual browser testing:

- [ ] Ensure local development server is running (`php artisan serve`)
- [ ] Ensure frontend assets are built (`npm run build`)
- [ ] Clear browser cache and cookies
- [ ] Disable browser extensions that may interfere
- [ ] Set browser zoom to 100%
- [ ] Prepare test data (test user accounts, sample submissions)

### 3.2 Core Functionality Test Cases

#### Test Case 1: Guest Helpdesk Form Submission

**Browsers**: Chrome, Firefox, Safari, Edge  
**Priority**: **CRITICAL**

| Step | Action | Expected Result | Chrome | Firefox | Safari | Edge |
|------|--------|----------------|--------|---------|--------|------|
| 1 | Navigate to `/helpdesk` | Form loads without errors | ⬜ | ⬜ | ⬜ | ⬜ |
| 2 | Fill all required fields | Real-time validation works | ⬜ | ⬜ | ⬜ | ⬜ |
| 3 | Upload attachment (< 5MB) | File upload succeeds | ⬜ | ⬜ | ⬜ | ⬜ |
| 4 | Submit form | Success message displayed | ⬜ | ⬜ | ⬜ | ⬜ |
| 5 | Check email | Confirmation email received | ⬜ | ⬜ | ⬜ | ⬜ |

#### Test Case 2: Guest Asset Loan Application

**Browsers**: Chrome, Firefox, Safari, Edge  
**Priority**: **CRITICAL**

| Step | Action | Expected Result | Chrome | Firefox | Safari | Edge |
|------|--------|----------------|--------|---------|--------|------|
| 1 | Navigate to `/loan` | Form loads without errors | ⬜ | ⬜ | ⬜ | ⬜ |
| 2 | Select asset from dropdown | Asset details displayed | ⬜ | ⬜ | ⬜ | ⬜ |
| 3 | Select date range | Calendar widget works | ⬜ | ⬜ | ⬜ | ⬜ |
| 4 | Fill applicant details | Validation works | ⬜ | ⬜ | ⬜ | ⬜ |
| 5 | Submit application | Success message displayed | ⬜ | ⬜ | ⬜ | ⬜ |

#### Test Case 3: Authenticated Staff Portal Login

**Browsers**: Chrome, Firefox, Safari, Edge  
**Priority**: **HIGH**

| Step | Action | Expected Result | Chrome | Firefox | Safari | Edge |
|------|--------|----------------|--------|---------|--------|------|
| 1 | Navigate to `/login` | Login form displayed | ⬜ | ⬜ | ⬜ | ⬜ |
| 2 | Enter credentials | Form validation works | ⬜ | ⬜ | ⬜ | ⬜ |
| 3 | Click "Login" | Redirect to dashboard | ⬜ | ⬜ | ⬜ | ⬜ |
| 4 | View submission history | Data loads correctly | ⬜ | ⬜ | ⬜ | ⬜ |
| 5 | Logout | Redirect to home page | ⬜ | ⬜ | ⬜ | ⬜ |

#### Test Case 4: Email-Based Approval Link

**Browsers**: Chrome, Firefox, Safari, Edge  
**Priority**: **HIGH**

| Step | Action | Expected Result | Chrome | Firefox | Safari | Edge |
|------|--------|----------------|--------|---------|--------|------|
| 1 | Click approval link in email | Approval page loads | ⬜ | ⬜ | ⬜ | ⬜ |
| 2 | View application details | Details displayed correctly | ⬜ | ⬜ | ⬜ | ⬜ |
| 3 | Add approval remarks | Text input works | ⬜ | ⬜ | ⬜ | ⬜ |
| 4 | Click "Approve" | Confirmation displayed | ⬜ | ⬜ | ⬜ | ⬜ |
| 5 | Check email | Confirmation email received | ⬜ | ⬜ | ⬜ | ⬜ |

#### Test Case 5: Filament Admin Panel

**Browsers**: Chrome, Firefox, Safari, Edge  
**Priority**: **HIGH**

| Step | Action | Expected Result | Chrome | Firefox | Safari | Edge |
|------|--------|----------------|--------|---------|--------|------|
| 1 | Navigate to `/admin` | Login prompt displayed | ⬜ | ⬜ | ⬜ | ⬜ |
| 2 | Login as admin | Dashboard loads | ⬜ | ⬜ | ⬜ | ⬜ |
| 3 | Navigate to Helpdesk Tickets | Table displays correctly | ⬜ | ⬜ | ⬜ | ⬜ |
| 4 | Filter and search | Filters work correctly | ⬜ | ⬜ | ⬜ | ⬜ |
| 5 | Edit a ticket | Form loads and saves | ⬜ | ⬜ | ⬜ | ⬜ |

### 3.3 Responsive Design Testing

Test at the following viewport sizes:

| Device Type | Viewport Size | Browsers to Test |
|-------------|---------------|------------------|
| Desktop | 1920×1080 | Chrome, Firefox, Safari, Edge |
| Laptop | 1366×768 | Chrome, Firefox, Safari, Edge |
| Tablet (Landscape) | 1024×768 | Chrome, Firefox, Safari, Edge |
| Tablet (Portrait) | 768×1024 | Chrome, Firefox, Safari, Edge |
| Mobile (Large) | 414×896 | Chrome, Firefox, Safari, Edge |
| Mobile (Medium) | 375×667 | Chrome, Firefox, Safari, Edge |
| Mobile (Small) | 320×568 | Chrome, Firefox, Safari, Edge |

**Testing Procedure**:

1. Open browser developer tools (F12)
2. Enable device emulation mode
3. Select viewport size
4. Navigate through key pages
5. Verify layout, touch targets, and functionality

### 3.4 Accessibility Testing

**Tools Required**:

- axe DevTools browser extension
- WAVE browser extension
- Lighthouse (built into Chrome DevTools)

**Testing Procedure**:

1. Install axe DevTools extension
2. Navigate to page under test
3. Run axe scan
4. Document any violations
5. Verify fixes in all browsers

---

## 4. Automated Testing Procedures

### 4.1 Playwright Test Suite

**Test File Structure**:

```
tests/Browser/
├── guest-forms/
│   ├── helpdesk-form.spec.ts
│   └── loan-application.spec.ts
├── authenticated-portal/
│   ├── login.spec.ts
│   ├── dashboard.spec.ts
│   └── submission-management.spec.ts
├── email-workflows/
│   └── approval-links.spec.ts
└── admin-panel/
    ├── helpdesk-management.spec.ts
    └── asset-management.spec.ts
```

**Example Test** (`tests/Browser/guest-forms/helpdesk-form.spec.ts`):

```typescript
import { test, expect } from '@playwright/test';

test.describe('Guest Helpdesk Form', () => {
  test.beforeEach(async ({ page }) => {
    await page.goto('/helpdesk');
  });

  test('should load form without errors', async ({ page }) => {
    await expect(page.locator('h1')).toContainText('Submit Helpdesk Ticket');
    await expect(page.locator('form')).toBeVisible();
  });

  test('should validate required fields', async ({ page }) => {
    await page.click('button[type="submit"]');
    await expect(page.locator('.error-message')).toBeVisible();
  });

  test('should submit form successfully', async ({ page }) => {
    await page.fill('input[name="name"]', 'Test User');
    await page.fill('input[name="email"]', 'test@motac.gov.my');
    await page.fill('input[name="phone"]', '0123456789');
    await page.selectOption('select[name="category"]', 'hardware');
    await page.fill('textarea[name="description"]', 'Test issue description');
    
    await page.click('button[type="submit"]');
    
    await expect(page.locator('.success-message')).toBeVisible();
    await expect(page.locator('.ticket-number')).toContainText(/HD\d{10}/);
  });

  test('should upload attachment', async ({ page }) => {
    const fileInput = page.locator('input[type="file"]');
    await fileInput.setInputFiles('tests/fixtures/test-image.jpg');
    
    await expect(page.locator('.file-preview')).toBeVisible();
  });
});
```

### 4.2 Running Automated Tests

**Run all tests**:

```powershell
npx playwright test
```

**Run specific browser**:

```powershell
npx playwright test --project=chromium
npx playwright test --project=firefox
npx playwright test --project=webkit
npx playwright test --project=edge
```

**Run specific test file**:

```powershell
npx playwright test tests/Browser/guest-forms/helpdesk-form.spec.ts
```

**Run with UI mode** (interactive debugging):

```powershell
npx playwright test --ui
```

**Generate HTML report**:

```powershell
npx playwright show-report
```

### 4.3 Visual Regression Testing

**Setup Percy** (optional):

```powershell
npm install --save-dev @percy/cli @percy/playwright
```

**Example Visual Test**:

```typescript
import { test } from '@playwright/test';
import percySnapshot from '@percy/playwright';

test('visual regression - helpdesk form', async ({ page }) => {
  await page.goto('/helpdesk');
  await percySnapshot(page, 'Helpdesk Form - Initial State');
  
  await page.fill('input[name="name"]', 'Test User');
  await percySnapshot(page, 'Helpdesk Form - Filled State');
});
```

---

## 5. Known Browser-Specific Issues

### 5.1 Safari-Specific Issues

| Issue | Description | Workaround | Status |
|-------|-------------|------------|--------|
| Date picker format | Safari uses native date picker | Use polyfill for consistency | ⏳ PENDING |
| Flexbox gaps | Older Safari versions have flexbox issues | Use margin fallback | ✅ RESOLVED |
| WebP support | Safari 14+ supports WebP | Provide JPEG fallback | ✅ RESOLVED |

### 5.2 Firefox-Specific Issues

| Issue | Description | Workaround | Status |
|-------|-------------|------------|--------|
| File upload styling | Firefox has limited file input styling | Use custom upload button | ✅ RESOLVED |
| Smooth scrolling | Firefox handles smooth scroll differently | Test scroll behavior | ⏳ PENDING |

### 5.3 Edge-Specific Issues

| Issue | Description | Workaround | Status |
|-------|-------------|------------|--------|
| Legacy Edge | Pre-Chromium Edge not supported | Detect and show upgrade message | ✅ RESOLVED |

---

## 6. Browser Feature Detection

### 6.1 Required Browser Features

ICTServe requires the following browser features:

- **ES2020 JavaScript** (async/await, optional chaining, nullish coalescing)
- **CSS Grid** (layout system)
- **CSS Flexbox** (layout system)
- **CSS Custom Properties** (theming)
- **Fetch API** (AJAX requests)
- **FormData API** (file uploads)
- **LocalStorage** (session persistence)
- **WebP Image Format** (with JPEG fallback)

### 6.2 Feature Detection Script

```javascript
// resources/js/browser-check.js
const requiredFeatures = {
  es2020: () => {
    try {
      eval('const x = { y: 1 }; const z = x?.y ?? 0;');
      return true;
    } catch {
      return false;
    }
  },
  cssGrid: () => CSS.supports('display', 'grid'),
  cssFlexbox: () => CSS.supports('display', 'flex'),
  cssCustomProperties: () => CSS.supports('--test', '0'),
  fetchAPI: () => 'fetch' in window,
  formData: () => 'FormData' in window,
  localStorage: () => {
    try {
      localStorage.setItem('test', 'test');
      localStorage.removeItem('test');
      return true;
    } catch {
      return false;
    }
  },
  webp: async () => {
    const webpData = 'data:image/webp;base64,UklGRiQAAABXRUJQVlA4IBgAAAAwAQCdASoBAAEAAwA0JaQAA3AA/vuUAAA=';
    const img = new Image();
    img.src = webpData;
    await img.decode();
    return img.width === 1;
  }
};

async function checkBrowserCompatibility() {
  const results = {};
  
  for (const [feature, check] of Object.entries(requiredFeatures)) {
    results[feature] = await check();
  }
  
  const unsupported = Object.entries(results)
    .filter(([_, supported]) => !supported)
    .map(([feature]) => feature);
  
  if (unsupported.length > 0) {
    console.warn('Unsupported features:', unsupported);
    showBrowserWarning(unsupported);
  }
  
  return unsupported.length === 0;
}

function showBrowserWarning(unsupportedFeatures) {
  const message = `
    Your browser does not support some required features: ${unsupportedFeatures.join(', ')}.
    Please upgrade to a modern browser:
    - Chrome 90+
    - Firefox 88+
    - Safari 14+
    - Edge 90+
  `;
  
  // Show warning banner
  const banner = document.createElement('div');
  banner.className = 'browser-warning';
  banner.textContent = message;
  document.body.prepend(banner);
}

// Run check on page load
document.addEventListener('DOMContentLoaded', checkBrowserCompatibility);
```

---

## 7. Performance Testing Across Browsers

### 7.1 Core Web Vitals Targets

Test Core Web Vitals in each browser:

| Metric | Target | Chrome | Firefox | Safari | Edge |
|--------|--------|--------|---------|--------|------|
| LCP | < 2.5s | ⬜ | ⬜ | ⬜ | ⬜ |
| FID | < 100ms | ⬜ | ⬜ | ⬜ | ⬜ |
| CLS | < 0.1 | ⬜ | ⬜ | ⬜ | ⬜ |
| TTFB | < 600ms | ⬜ | ⬜ | ⬜ | ⬜ |

### 7.2 Lighthouse Testing

**Run Lighthouse in Chrome**:

1. Open Chrome DevTools (F12)
2. Navigate to "Lighthouse" tab
3. Select "Desktop" or "Mobile"
4. Click "Analyze page load"
5. Review scores and recommendations

**Target Scores**:

- Performance: 90+
- Accessibility: 100
- Best Practices: 100
- SEO: 100

### 7.3 WebPageTest

**Cross-Browser Performance Testing**:

1. Visit <https://www.webpagetest.org/>
2. Enter ICTServe URL
3. Select browser and location
4. Run test
5. Compare results across browsers

---

## 8. Reporting and Documentation

### 8.1 Test Report Template

```markdown
# Browser Compatibility Test Report

**Date**: [Date]
**Tester**: [Name]
**Build Version**: [Version]
**Environment**: [Development/Staging/Production]

## Test Summary

| Browser | Version | Pass Rate | Critical Issues | Notes |
|---------|---------|-----------|-----------------|-------|
| Chrome | [Version] | [%] | [Count] | [Notes] |
| Firefox | [Version] | [%] | [Count] | [Notes] |
| Safari | [Version] | [%] | [Count] | [Notes] |
| Edge | [Version] | [%] | [Count] | [Notes] |

## Critical Issues

### Issue 1: [Title]
- **Browser**: [Browser and version]
- **Severity**: Critical/High/Medium/Low
- **Description**: [Description]
- **Steps to Reproduce**: [Steps]
- **Expected Result**: [Expected]
- **Actual Result**: [Actual]
- **Screenshot**: [Link]
- **Status**: Open/In Progress/Resolved

## Recommendations

1. [Recommendation 1]
2. [Recommendation 2]

## Sign-off

- **Tested By**: [Name]
- **Reviewed By**: [Name]
- **Approved By**: [Name]
```

### 8.2 Issue Tracking

**Create GitHub Issue for Browser Bugs**:

```markdown
**Title**: [Browser] - [Brief Description]

**Browser**: Chrome/Firefox/Safari/Edge
**Version**: [Version]
**OS**: Windows/macOS/Linux
**Severity**: Critical/High/Medium/Low

**Description**:
[Detailed description of the issue]

**Steps to Reproduce**:
1. [Step 1]
2. [Step 2]
3. [Step 3]

**Expected Behavior**:
[What should happen]

**Actual Behavior**:
[What actually happens]

**Screenshots/Videos**:
[Attach screenshots or videos]

**Additional Context**:
[Any additional information]

**Labels**: browser-compatibility, [browser-name], [severity]
```

---

## 9. Continuous Monitoring

### 9.1 Automated Browser Testing in CI/CD

**GitHub Actions Integration**:

```yaml
name: Cross-Browser Tests

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]
  schedule:
    - cron: '0 0 * * *'  # Daily at midnight

jobs:
  playwright-tests:
    runs-on: ubuntu-latest
    strategy:
      matrix:
        browser: [chromium, firefox, webkit]
    steps:
      - uses: actions/checkout@v3
      - uses: actions/setup-node@v3
      - run: npm ci
      - run: npx playwright install --with-deps ${{ matrix.browser }}
      - run: npx playwright test --project=${{ matrix.browser }}
      - uses: actions/upload-artifact@v3
        if: failure()
        with:
          name: test-results-${{ matrix.browser }}
          path: test-results/
```

### 9.2 Real User Monitoring (RUM)

**Implement Browser Analytics**:

```javascript
// Track browser usage
if (window.gtag) {
  gtag('event', 'browser_info', {
    browser: navigator.userAgent,
    viewport: `${window.innerWidth}x${window.innerHeight}`,
    platform: navigator.platform
  });
}

// Track Core Web Vitals
import {getCLS, getFID, getLCP} from 'web-vitals';

function sendToAnalytics(metric) {
  gtag('event', metric.name, {
    value: Math.round(metric.value),
    metric_id: metric.id,
    metric_value: metric.value,
    metric_delta: metric.delta,
  });
}

getCLS(sendToAnalytics);
getFID(sendToAnalytics);
getLCP(sendToAnalytics);
```

---

## 10. Maintenance and Updates

### 10.1 Browser Version Tracking

**Quarterly Review Schedule**:

- **Q1 (January)**: Review browser versions, update support matrix
- **Q2 (April)**: Review browser versions, update support matrix
- **Q3 (July)**: Review browser versions, update support matrix
- **Q4 (October)**: Review browser versions, update support matrix

### 10.2 Deprecation Policy

**When to Drop Browser Support**:

1. Browser vendor ends support (e.g., IE11)
2. Browser usage drops below 1% of ICTServe users
3. Browser version is 2+ years old
4. Security vulnerabilities in old browser versions

**Deprecation Process**:

1. Announce deprecation 6 months in advance
2. Show warning banner to affected users
3. Update documentation
4. Remove support after grace period

---

## 11. Conclusion

This guide provides comprehensive procedures for testing ICTServe across all supported browsers. Regular testing ensures consistent user experience and early detection of browser-specific issues.

**Key Takeaways**:

1. Test on all 4 supported browsers (Chrome, Firefox, Safari, Edge)
2. Use automated testing with Playwright for efficiency
3. Perform manual testing for critical user flows
4. Monitor Core Web Vitals across browsers
5. Document and track browser-specific issues
6. Maintain continuous monitoring in CI/CD

**Next Steps**:

1. Set up Playwright test suite
2. Run initial browser compatibility tests
3. Document any issues found
4. Implement fixes for critical issues
5. Establish regular testing schedule

---

**Document Version**: 1.0.0  
**Last Updated**: November 4, 2025  
**Next Review**: February 4, 2026  
**Document Owner**: ICTServe QA Team

---

**End of Browser Compatibility Testing Guide**
