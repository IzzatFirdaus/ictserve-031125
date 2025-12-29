# Playwright Tests with Percy Integration Summary

## Overview
This document provides a comprehensive list of all Playwright tests in the ICTServe v3.6.1 project and their Percy visual testing configuration.

## Percy Configuration Files

### Main Configuration Files

1. **`playwright.config.ts`** - Main Playwright configuration with Percy integration
2. **`playwright.percy.config.ts`** - Percy-specific Playwright configuration
3. **`playwright.screenshots.config.ts`** - Screenshot-focused configuration
4. **`percy.config.js`** - Percy visual testing configuration

### Percy Environment Detection

- Percy is enabled when `PERCY_TOKEN` environment variable is set
- Configuration supports both CI and local development environments
- Includes responsive testing across multiple viewport sizes (375, 768, 1024, 1280, 1920px)

## All Playwright Test Files

### E2E Tests Directory (`tests/e2e/`)

#### Core Application Tests (Percy Integrated)

1. **`accessibility.comprehensive.spec.ts`** ✅ Percy
   - WCAG 2.2 Level AA compliance testing
   - Uses `takePercySnapshot`, `takeAccessibilitySnapshot`

2. **`accessibility.interactions.spec.ts`** ✅ Percy
   - Interactive accessibility testing
   - Uses Percy utilities from `percy-utils.ts`

3. **`admin-dashboard.spec.ts`** ✅ Percy
   - Admin dashboard functionality
   - Percy snapshots for admin interface

4. **`branding-smoke.spec.ts`** ✅ Percy
   - Branding and visual consistency checks
   - Uses `takePercySnapshot`

5. **`comprehensive-form-screenshots.spec.ts`** ✅ Percy
   - Form screenshot generation
   - Uses `takeResponsiveSnapshots`, `waitForStableContent`

6. **`cross-browser.spec.ts`** ✅ Percy
   - Cross-browser compatibility testing
   - Uses `takePercySnapshot`, `takeResponsiveSnapshots`

7. **`dashboard.spec.ts`** ✅ Percy
   - Main dashboard testing with responsive design
   - Uses `takePercySnapshot`, `takeResponsiveSnapshots`

8. **`dashboard-percy-enhanced.spec.ts`** ✅ Percy
   - Enhanced Percy-specific dashboard tests
   - Uses `takeResponsiveSnapshots`, `takeAccessibilitySnapshot`

9. **`devtools.integration.spec.ts`** ✅ Percy
   - Chrome DevTools integration testing
   - Uses `takePercySnapshot`

10. **`filament.components.debug.spec.ts`** ✅ Percy
    - Filament admin components testing
    - Uses `takePercySnapshot`

11. **`guest-flow-screenshots.spec.ts`** ✅ Percy
    - Guest user flow documentation
    - Uses `takeFormStateSnapshots`, `waitForStableContent`

12. **`guest-landing-accessibility.spec.ts`** ✅ Percy
    - Guest landing page accessibility
    - Uses `takePercySnapshot`, `takeAccessibilitySnapshot`

13. **`helpdesk.spec.ts`** ✅ Percy
    - Helpdesk module testing
    - Uses `takeFormStateSnapshots`, `takeHybridArchitectureSnapshots`

14. **`helpdesk-performance.spec.ts`** ✅ Percy
    - Helpdesk performance testing
    - Uses `takePercySnapshot`

15. **`loan.spec.ts`** ✅ Percy
    - Loan module core functionality
    - Uses `takeResponsiveSnapshots`, `takeFormStateSnapshots`

16. **`loan-module.spec.ts`** ✅ Percy
    - Loan module E2E testing
    - Uses `takePercySnapshot`, `takeFormStateSnapshots`

17. **`loan-module-performance.spec.ts`** ✅ Percy
    - Loan module performance testing
    - Uses `takePercySnapshot`

18. **`ollama-accessibility.spec.ts`** ✅ Percy
    - Ollama integration accessibility
    - Uses `takePercySnapshot`, `takeAccessibilitySnapshot`

19. **`staff-flow.spec.ts`** ✅ Percy
    - Staff user flow testing
    - Uses `takePercySnapshot`, `takeResponsiveSnapshots`

#### Percy-Specific Tests
1. **`percy-degradation-validation.spec.ts`** ✅ Percy
    - Percy service degradation testing
    - Direct `percySnapshot` usage with error handling

2. **`percy-enhanced-examples.spec.ts`** ✅ Percy
    - Percy integration examples and best practices
    - Uses comprehensive Percy utilities

3. **`percy-integration-demo.spec.ts`** ✅ Percy
    - Percy integration demonstration
    - Uses `isPercyEnabled`, `getPercyBuildInfo`

4. **`percy-integration-validation.spec.ts`** ✅ Percy
    - Percy integration validation tests
    - Uses Percy utilities and configuration validation

5. **`percy-performance-validation.spec.ts`** ✅ Percy
    - Percy performance optimization testing
    - Direct `percySnapshot` usage

6. **`percy-setup-validation.spec.ts`** ✅ Percy
    - Percy setup and configuration validation
    - Direct `percySnapshot` usage

#### BrowserStack Integration Tests (Percy Enabled)
1. **`browserstack-accessibility-percy.spec.ts`** ✅ Percy
    - BrowserStack accessibility testing with Percy
    - Uses `percySnapshot` from `@percy/playwright`

2. **`browserstack-automate-percy.spec.ts`** ✅ Percy
    - BrowserStack automation with Percy integration
    - Uses `percySnapshot` and `BrowserStackPercyHelper`

3. **`browserstack-live-session-percy.spec.ts`** ✅ Percy
    - BrowserStack live sessions with Percy
    - Uses `percySnapshot`

4. **`browserstack-test-management-percy.spec.ts`** ✅ Percy
    - BrowserStack test management with Percy
    - Uses `percySnapshot` for test case validation

#### Admin Tests (Limited Percy)
1. **`admin-auth-bypass.spec.ts`** ❌ No Percy
2. **`admin-debug-fixture.spec.ts`** ❌ No Percy
3. **`admin-debug.spec.ts`** ⚠️ Partial Percy (uses `waitForStableContent`)
4. **`admin-login-debug.spec.ts`** ❌ No Percy
5. **`admin-simple.spec.ts`** ❌ No Percy
6. **`admin-user-check.spec.ts`** ❌ No Percy

### Examples Directory (`tests/e2e/examples/`)
1. **`browserstack-percy-integration.spec.ts`** ✅ Percy
    - BrowserStack Percy integration examples
    - Uses `percySnapshot` and `BrowserStackPercyHelper`

2. **`percy-example-tests.spec.ts`** ✅ Percy
    - Percy usage examples and patterns
    - Uses comprehensive Percy utilities

### Performance Tests (`tests/e2e/performance/`)
1. **`core-web-vitals.spec.ts`** ❌ No Percy
2. **`lighthouse-audit.spec.ts`** ❌ No Percy

### Screenshots Tests (`tests/e2e/screenshots/`)
1. **`admin-screenshot-automation.spec.ts`** ❌ No Percy
2. **`screenshot-automation.spec.ts`** ❌ No Percy

### Percy-Specific Tests Directory (`tests/percy/`)
1. **`error-handling-validation.spec.ts`** ⚠️ Percy CLI Testing
2. **`percy-config-simple-validation.spec.ts`** ⚠️ Percy Config Testing
3. **`percy-config-validation.spec.ts`** ⚠️ Percy Config Testing
4. **`percy-degradation-validation.spec.ts`** ✅ Percy
5. **`percy-snapshot-validation.spec.ts`** ✅ Percy
6. **`simple-error-validation.spec.ts`** ⚠️ Percy CLI Testing

## Percy Integration Summary

### Tests with Full Percy Integration (29 tests)

- All core application functionality tests
- All BrowserStack integration tests
- All Percy-specific demonstration tests
- Responsive design validation
- Accessibility compliance testing
- Form state documentation

### Tests without Percy Integration (8 tests)

- Admin debugging and authentication tests
- Performance measurement tests (Core Web Vitals, Lighthouse)
- Screenshot automation utilities

### Percy Configuration Features

1. **Responsive Testing**: 375px, 768px, 1024px, 1280px, 1920px viewports
2. **User Type Support**: Guest, Authenticated, Admin user flows
3. **Bahasa Melayu Interface**: Language-specific visual validation
4. **WCAG 2.2 AA Compliance**: Accessibility-focused snapshots
5. **Dynamic Content Handling**: Hides loading states, timestamps, notifications
6. **Livewire Integration**: Waits for Livewire components to stabilize
7. **Filament Admin Support**: Admin interface consistency validation

### Percy Utilities (`tests/e2e/utils/percy-utils.ts`)

- `takePercySnapshot()` - Enhanced snapshot with ICTServe configurations
- `takeResponsiveSnapshots()` - Multi-viewport testing
- `takeHybridArchitectureSnapshots()` - Guest vs authenticated comparison
- `takeAccessibilitySnapshot()` - WCAG compliance validation
- `takeFormStateSnapshots()` - Form documentation
- `waitForStableContent()` - Dynamic content stabilization

## Running Percy Tests

### Commands

```bash
# Run all tests with Percy
npm run test:e2e:percy

# Run specific Percy test
npm run test:e2e:percy -- tests/e2e/dashboard.spec.ts

# Run Percy-only tests
npx playwright test --config=playwright.percy.config.ts

# Run with Percy environment
PERCY_TOKEN=your_token npx playwright test
```

### Environment Variables

- `PERCY_TOKEN` - Required for Percy snapshots
- `PERCY_PROJECT` - Project name (default: "ictserve")
- `PERCY_BRANCH` - Branch name for comparison
- `SKIP_PERCY` - Disable Percy even with token set
