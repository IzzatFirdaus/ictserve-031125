# Percy Configuration Updates for ICTServe v3.6.1

## Task 14: Update Playwright configuration for Percy integration

This document summarizes the Playwright configuration updates made to support Percy visual testing integration for ICTServe v3.6.1.

## Files Updated

### 1. `playwright.config.ts` (Main Configuration)

**Key Updates:**

- Added Percy environment detection variables (`isPercyEnabled`, `isCI`, `skipPercy`)
- Enhanced `extraHTTPHeaders` with Percy-specific headers and ICTServe version information
- Added Percy-optimized viewport configuration and device settings
- Configured locale for Bahasa Melayu interface testing (`ms-MY`)
- Added timezone configuration for consistent timestamp handling (`Asia/Kuala_Lumpur`)
- Implemented reduced motion for consistent visual testing
- Added Percy-specific global setup and teardown files
- Enhanced browser projects with Percy-optimized configurations
- Added conditional Percy-specific responsive testing projects

**Percy Environment Detection:**

```typescript
const isPercyEnabled = process.env.PERCY_TOKEN && process.env.PERCY_TOKEN.length > 0;
const isCI = process.env.CI === "true";
const skipPercy = process.env.SKIP_PERCY === "true";
```

**Enhanced Headers:**

```typescript
extraHTTPHeaders: {
  "X-Percy-Test": isPercyEnabled ? "enabled" : "disabled",
  "X-Percy-Environment": isCI ? "ci" : "local",
  "X-ICTServe-Version": "3.6.1",
  "X-Laravel-Version": "12.43.1",
  "X-Livewire-Version": "3.7.3",
  "X-Filament-Version": "4.3.1",
}
```

### 2. `playwright.percy.config.ts` (Percy-Specific Configuration)

**New File Created:**

- Dedicated configuration for Percy-only test runs
- Optimized for visual testing with reduced parallelism and consistent settings
- Includes responsive testing projects (mobile, tablet, desktop-wide)
- Configured for stable Percy snapshots with network idle waiting

**Key Features:**

- Single worker for consistent visual testing
- Disabled parallelism for reliable snapshots
- Extended timeouts for Percy snapshot processing
- Optimized viewport configurations for different device types

### 3. `percy.config.js` (Percy Configuration)

**Updated to ES Modules:**

- Changed from `module.exports` to `export default` for ES module compatibility
- Maintained all existing Percy configuration settings
- Supports ICTServe v3.6.1 specific CSS rules and responsive breakpoints

### 4. `package.json` (Scripts)

**Added Percy-Specific Playwright Scripts:**

```json
"test:e2e:percy:config": "playwright test --config=playwright.percy.config.ts",
"test:e2e:percy:config:ui": "playwright test --config=playwright.percy.config.ts --ui",
"test:e2e:percy:config:debug": "playwright test --config=playwright.percy.config.ts --debug",
"test:e2e:percy:responsive": "playwright test --config=playwright.percy.config.ts --project=percy-mobile,percy-tablet,percy-desktop-wide",
"test:e2e:percy:chrome": "playwright test --config=playwright.percy.config.ts --project=percy-chrome"
```

## New Files Created

### 1. `tests/percy/percy-global-setup.ts`

**Purpose:** Global setup for Percy visual testing
**Features:**

- Percy environment validation
- ICTServe v3.6.1 specific setup logging
- Laravel server accessibility validation
- Percy token and configuration validation

### 2. `tests/percy/percy-global-teardown.ts`

**Purpose:** Global teardown for Percy visual testing
**Features:**

- Percy build finalization logging
- ICTServe v3.6.1 specific teardown summary
- Performance metrics reporting
- Percy dashboard link information

### 3. `tests/percy/percy-utils.ts`

**Purpose:** Utility functions for Percy visual testing
**Features:**

- ICTServe-specific Percy snapshot configuration
- Responsive snapshot helpers
- Hybrid Architecture testing support
- Bahasa Melayu interface validation
- WCAG compliance testing utilities
- Percy environment detection functions

**Key Functions:**

- `takeICTServeSnapshot()` - Enhanced Percy snapshot with ICTServe optimizations
- `takeResponsiveSnapshots()` - Multi-viewport responsive testing
- `takeHybridArchitectureSnapshot()` - Guest/authenticated/admin user testing
- `takeBahasaMelayuSnapshot()` - Bahasa Melayu interface validation
- `takeWCAGComplianceSnapshot()` - WCAG 2.2 AA compliance testing

### 4. `tests/percy/percy-config-simple-validation.spec.ts`

**Purpose:** Validation test for Percy configuration updates
**Features:**

- Percy environment detection validation
- Configuration file structure validation
- Playwright configuration constants validation
- Percy utilities functionality validation

## Configuration Features

### Percy Environment Detection

- Automatic detection of Percy token availability
- CI/CD environment detection
- Skip Percy option support
- Graceful degradation when Percy is disabled

### ICTServe v3.6.1 Specific Optimizations

- True Hybrid Architecture support (guest/authenticated/admin users)
- Bahasa Melayu interface testing configuration
- Laravel 12.43.1 + Livewire 3.7.3 + Filament 4.3.1 stack support
- WCAG 2.2 AA compliance visual validation
- Responsive design testing across multiple viewports

### Performance Optimizations

- Consistent viewport configurations for reliable snapshots
- Reduced motion settings for visual consistency
- Network idle waiting for Livewire components
- Optimized device scale factors
- Timezone and locale consistency

### Cross-Browser Support

- Percy-optimized configurations for Chrome, Firefox, Safari, and Edge
- Consistent viewport sizes across all browsers
- Device-specific configurations for mobile and tablet testing

## Usage Examples

### Basic Percy Test Run

```bash
npm run test:e2e:percy:config
```

### Responsive Percy Testing

```bash
npm run test:e2e:percy:responsive
```

### Percy Configuration Validation

```bash
npx playwright test tests/percy/percy-config-simple-validation.spec.ts --config=playwright.percy.config.ts
```

### Using Percy Utilities in Tests

```typescript
import { takeICTServeSnapshot, takeResponsiveSnapshots } from './percy-utils';

// Basic snapshot
await takeICTServeSnapshot(page, {
  name: 'Homepage - ICTServe v3.6.1',
  widths: [1280],
  userType: 'guest',
  validateBahasaMelayu: true,
});

// Responsive snapshots
await takeResponsiveSnapshots(page, 'Dashboard', {
  userType: 'authenticated',
  wcagLevel: 'AA',
});
```

## Validation Results

The configuration updates have been successfully validated:

✅ **Percy Environment Detection** - Working correctly
✅ **Configuration File Structure** - Valid ES module format
✅ **Playwright Configuration** - Properly loaded with Percy optimizations
✅ **Percy Utilities** - Functional and ready for use
✅ **Responsive Projects** - Available for multi-viewport testing
✅ **Global Setup/Teardown** - Properly configured and executing

## Requirements Satisfied

This implementation satisfies the following requirements from Task 14:

- ✅ **2.1** - Modify playwright.config.ts to support Percy snapshots
- ✅ **2.2** - Add Percy-specific test configuration options
- ✅ **2.3** - Configure viewport sizes and snapshot settings
- ✅ **2.4** - Add Percy environment detection and configuration
- ✅ **2.5** - Support for graceful Percy degradation

The Playwright configuration is now fully optimized for Percy visual testing integration with ICTServe v3.6.1, supporting the True Hybrid Architecture, Bahasa Melayu interface, and comprehensive responsive testing capabilities.
