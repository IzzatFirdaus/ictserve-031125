# Percy Documentation Summary - Setup and Usage Guide

Based on the latest Percy documentation and examples, here's a comprehensive guide for setting up and using Percy with Playwright.

## Percy Overview

Percy is an AI-powered visual testing platform acquired by BrowserStack in 2020. It provides smart visual regression testing by comparing screenshots with baseline images using computer vision techniques to reduce false positives.

### Key Features

- **AI-powered comparison**: Reduces false positives by ignoring minor font rendering differences and 1px shifts
- **Multi-browser support**: Chrome, Firefox, Safari, Edge
- **Responsive testing**: Multiple viewport widths
- **Fast processing**: Builds typically complete in minutes
- **Free tier**: 5,000 screenshots monthly

## Installation and Setup

### 1. Install Required Packages

```bash
npm install --save-dev @percy/cli @percy/playwright @playwright/test
```

### 2. Get Percy Token

1. Sign in to [Percy.io](https://percy.io/)
2. Create a project of type "Web"
3. Copy the generated token (starts with `web_` for web projects)

### 3. Set Environment Variable

The `PERCY_TOKEN` environment variable is **required** for Percy to work:

```bash
# Linux/macOS
export PERCY_TOKEN=your_percy_token_here

# Windows Command Prompt
set PERCY_TOKEN=your_percy_token_here

# Windows PowerShell
$env:PERCY_TOKEN = "your_percy_token_here"

# Or add to .env file (requires dotenv loading)
PERCY_TOKEN=your_percy_token_here
```

## Percy Configuration

### percy.yml Configuration File

Create a `percy.yml` file in your project root:

```yaml
version: 2
snapshot:
  widths:
    - 375    # Mobile
    - 768    # Tablet
    - 1280   # Desktop
    - 1920   # Wide Desktop
  min-height: 1024
  percy-css: |
    /* Hide dynamic content that changes between snapshots */
    .dynamic-timestamp { display: none !important; }
    .loading-spinner { visibility: hidden !important; }
    .notification-badge { display: none !important; }
    
    /* Hide animations */
    *, *::before, *::after {
      animation-duration: 0s !important;
      animation-delay: 0s !important;
      transition-duration: 0s !important;
      transition-delay: 0s !important;
    }
discovery:
  disable-cache: true
```

## Using Percy with Playwright

### Basic Test Example

```typescript
import { test, expect } from '@playwright/test';
import percySnapshot from '@percy/playwright';

test('Visual regression test', async ({ page }) => {
  await page.goto('https://example.com');
  
  // Wait for page to be fully loaded
  await page.waitForLoadState('networkidle');
  
  // Take Percy snapshot
  await percySnapshot(page, 'Homepage');
});
```

### Advanced Percy Snapshot Options

```typescript
await percySnapshot(page, 'Homepage with options', {
  // Custom viewport widths
  widths: [375, 1280],
  
  // Minimum height
  minHeight: 1024,
  
  // Custom CSS to hide dynamic elements
  percyCSS: `
    .ads-banner { display: none !important; }
    .user-avatar { visibility: hidden !important; }
  `,
  
  // Capture full page
  fullPage: true,
  
  // Ignore specific regions
  ignoreRegionSelectors: ['.dynamic-content'],
  
  // Custom ignore regions by coordinates
  customIgnoreRegions: [
    { top: 10, right: 10, bottom: 120, left: 10 }
  ]
});
```

### Handling Lazy Loading

For websites with lazy-loaded images:

```typescript
import scrollToBottom from 'scroll-to-bottomjs';

test('Page with lazy loading', async ({ page }) => {
  await page.goto('https://example.com');
  
  // Scroll to bottom to trigger lazy loading
  await page.evaluate(scrollToBottom);
  
  // Wait for images to load
  await page.waitForTimeout(2000);
  
  await percySnapshot(page, 'Full page with lazy loaded content');
});
```

## Running Percy Tests

### Method 1: Using percy exec (Recommended)

```bash
# Run tests with Percy
npx percy exec -- npx playwright test

# With custom config
npx percy exec --config ./percy.yml -- npx playwright test

# Run specific test file
npx percy exec -- npx playwright test tests/visual.spec.ts
```

### Method 2: Package.json Scripts

```json
{
  "scripts": {
    "test:visual": "percy exec -- playwright test",
    "test:visual:config": "percy exec --config ./percy.yml -- playwright test",
    "test:percy": "dotenv percy exec -- playwright test"
  }
}
```

### Method 3: Using dotenv-cli (For .env files)

```bash
# Install dotenv-cli globally
npm install -g dotenv-cli

# Run with environment variables loaded
dotenv npx percy exec -- npx playwright test
```

## Common Issues and Solutions

### Issue 1: "Percy is not running, disabling snapshots"

**Cause**: Percy CLI service is not running or `PERCY_TOKEN` is not set.

**Solutions**:

1. Ensure `PERCY_TOKEN` is set in environment
2. Use `percy exec` command to start Percy service
3. Check token is valid and not expired

```bash
# Check if token is set
echo $PERCY_TOKEN  # Linux/macOS
echo $env:PERCY_TOKEN  # Windows PowerShell

# Run with percy exec
npx percy exec -- npx playwright test
```

### Issue 2: "Percy token not provided"

**Cause**: Environment variable not loaded into test process.

**Solutions**:

1. Set environment variable in shell session
2. Use dotenv-cli to load .env file
3. Set in CI/CD environment variables

### Issue 3: Too Many False Positives

**Solutions**:

1. Use `percy-css` to hide dynamic content
2. Wait for animations to complete
3. Use `waitForLoadState('networkidle')`
4. Hide user-specific content (avatars, timestamps)

## Best Practices

### 1. Environment Setup

- Always use `percy exec` command
- Set `PERCY_TOKEN` environment variable
- Use configuration files for consistent settings

### 2. Test Writing

- Wait for page stability before snapshots
- Handle lazy-loaded content
- Hide dynamic elements with CSS
- Use descriptive snapshot names

### 3. Configuration

- Define appropriate viewport widths
- Set minimum height for consistency
- Use `percy-css` to hide dynamic content
- Enable cache disabling for development

### 4. CI/CD Integration

- Store `PERCY_TOKEN` as secret environment variable
- Use `percy exec` in build scripts
- Set up proper branch comparison

## Example Package.json Configuration

```json
{
  "devDependencies": {
    "@percy/cli": "^1.28.8",
    "@percy/playwright": "^1.0.4",
    "@playwright/test": "^1.40.0",
    "dotenv-cli": "^7.3.0"
  },
  "scripts": {
    "test:e2e": "playwright test",
    "test:visual": "dotenv percy exec -- playwright test",
    "test:visual:local": "percy exec --config ./percy.yml -- playwright test",
    "percy:debug": "percy exec --verbose -- playwright test"
  }
}
```

## Environment Variables Reference

```bash
# Required
PERCY_TOKEN=web_your_token_here

# Optional
PERCY_PROJECT=your-project-name
PERCY_BRANCH=main
PERCY_TARGET_BRANCH=main
PERCY_PARALLEL_NONCE=build-123
PERCY_PARALLEL_TOTAL=4
PERCY_FAIL_ON_ERROR=false

# Debug
PERCY_DEBUG=true
PERCY_VERBOSE=true
```

## Percy Dashboard Features

- **Build comparison**: Compare current build with baseline
- **Visual diff highlighting**: AI-powered change detection
- **Multi-browser results**: See differences across browsers
- **Approval workflow**: Approve or reject visual changes
- **Integration**: GitHub, GitLab, Jenkins, etc.

## Troubleshooting Commands

```bash
# Check Percy CLI version
npx percy --version

# Validate Percy configuration
npx percy config:validate

# Debug Percy execution
npx percy exec --verbose -- playwright test

# Check Percy build status
npx percy build:info

# List Percy projects
npx percy project:list
```

This documentation provides a complete guide for setting up and using Percy with Playwright, addressing the common issues found in the ICTServe project where Percy was being disabled due to environment variable and CLI service setup problems.
