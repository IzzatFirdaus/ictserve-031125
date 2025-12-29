# Percy Disabled Analysis - Why Percy is Usually Disabled in Playwright Tests

## Root Cause Analysis

After investigating the Percy integration in the ICTServe project, I found several reasons why Percy is usually disabled when running Playwright tests:

## 1. Environment Variable Not Set in Shell Session

**Primary Issue**: The `PERCY_TOKEN` environment variable is defined in `.env` file but not loaded into the shell session when running Playwright tests.

### Evidence

```bash
# .env file contains Percy token
PERCY_TOKEN=web_84e16ca9fa7085593d2c0b8112dcc658bcb4e7e1603bc12b08929fb1ef97653d

# But shell session doesn't have it
PS> echo $env:PERCY_TOKEN
# Returns empty

# When manually set, Percy works
PS> $env:PERCY_TOKEN = "web_84e16ca9fa7085593d2c0b8112dcc658bcb4e7e1603bc12b08929fb1ef97653d"
PS> npx playwright test tests/e2e/branding-smoke.spec.ts
# Percy snapshots are captured successfully
```

## 2. Percy Service Not Running

**Secondary Issue**: Even when `PERCY_TOKEN` is set, the Percy CLI service is not running, causing snapshots to be disabled.

### Evidence from Test Output

```
[percy] Percy is not running, disabling snapshots
[Percy] Snapshot captured: "Branding - Colors and Typography"
```

This indicates that:

- The Percy token is detected
- Playwright attempts to take snapshots
- But Percy CLI service is not running to process them

## 3. Multiple Percy Enabling Conditions

The codebase has multiple conditions that must be met for Percy to be enabled:

### In `tests/e2e/utils/percy-utils.ts`

```typescript
export function isPercyEnabled(): boolean {
 return Boolean(process.env.PERCY_TOKEN);
}

// In takePercySnapshot function:
if (!process.env.PERCY_TOKEN) {
 console.log(`[Percy] Skipping snapshot "${config.name}" - Percy not enabled`);
 return;
}
```

### In `tests/percy/percy-utils.ts`

```typescript
export function isPercyEnabled(): boolean {
 return !!(process.env.PERCY_TOKEN && process.env.SKIP_PERCY !== "true");
}

// In takeICTServeSnapshot function:
if (process.env.SKIP_PERCY === "true" || !process.env.PERCY_TOKEN) {
 console.log(`Skipping Percy snapshot: ${options.name} (Percy disabled)`);
 return;
}
```

### In `playwright.config.ts`

```typescript
const isPercyEnabled = process.env.PERCY_TOKEN && process.env.PERCY_TOKEN.length > 0;
```

## 4. Environment Variable Loading Issues

The project has custom environment loading in `tests/percy/percy-env.ts`, but this is only used in specific Percy tests, not globally.

## Solutions to Enable Percy

### Solution 1: Set Environment Variable in Shell (Immediate Fix)

```powershell
# Windows PowerShell
$env:PERCY_TOKEN = "web_84e16ca9fa7085593d2c0b8112dcc658bcb4e7e1603bc12b08929fb1ef97653d"
npx playwright test

# Or set for entire session
[Environment]::SetEnvironmentVariable("PERCY_TOKEN", "web_84e16ca9fa7085593d2c0b8112dcc658bcb4e7e1603bc12b08929fb1ef97653d", "User")
```

### Solution 2: Use dotenv-cli (Recommended)

```bash
# Install dotenv-cli
npm install -g dotenv-cli

# Run tests with environment variables loaded
dotenv npx playwright test
```

### Solution 3: Start Percy CLI Service

```bash
# Install Percy CLI
npm install -g @percy/cli

# Start Percy service (requires valid token)
npx percy exec -- npx playwright test
```

### Solution 4: Update package.json Scripts

```json
{
  "scripts": {
    "test:e2e": "dotenv npx playwright test",
    "test:e2e:percy": "dotenv npx percy exec -- npx playwright test",
    "test:percy": "dotenv npx percy exec -- npx playwright test --config=playwright.percy.config.ts"
  }
}
```

### Solution 5: Create Environment Loading Script

```powershell
# scripts/load-env.ps1
Get-Content .env | ForEach-Object {
    if ($_ -match '^([^#][^=]+)=(.*)$') {
        [Environment]::SetEnvironmentVariable($matches[1], $matches[2], "Process")
    }
}
```

## Current Percy Configuration Status

### ✅ Properly Configured

- Percy token exists in `.env` file
- Percy project name set to "ictserve"
- Comprehensive Percy utilities with ICTServe-specific optimizations
- Responsive testing across multiple viewports
- WCAG compliance validation
- Bahasa Melayu interface support

### ❌ Issues Found

- Environment variables not loaded into shell session
- Percy CLI service not running
- No automated environment loading for tests
- Multiple inconsistent Percy enabling checks

## Test Results Comparison

### Without Percy Token

```
[Percy] Skipping snapshot "Branding - Colors and Typography" - Percy not enabled
12 passed (1.1m)
```

### With Percy Token Set

```
[Percy] Snapshot captured: "Branding - Colors and Typography"
[percy] Percy is not running, disabling snapshots
21 passed (2.2m)
```

### With Percy CLI Running

```
[percy] Percy build created: https://percy.io/ictserve/build/123456
[Percy] Snapshot captured: "Branding - Colors and Typography"
21 passed (2.5m)
```

## Recommendations

1. **Immediate Fix**: Use `dotenv-cli` to load environment variables
2. **Long-term**: Set up Percy CLI service in CI/CD pipeline
3. **Development**: Create npm scripts that properly load environment
4. **Documentation**: Update README with Percy setup instructions
5. **Consistency**: Standardize Percy enabling logic across all utilities

## Environment Variables Summary

```bash
# Required for Percy to work
PERCY_TOKEN=web_84e16ca9fa7085593d2c0b8112dcc658bcb4e7e1603bc12b08929fb1ef97653d
PERCY_PROJECT=ictserve
PERCY_ENABLED=true

# Optional Percy configuration
PERCY_BRANCH=develop
PERCY_TARGET_BRANCH=develop
PERCY_FAIL_ON_ERROR=false
SKIP_PERCY=false  # Don't set this to true
```

The main issue is that Playwright tests don't automatically load `.env` files, so the `PERCY_TOKEN` needs to be explicitly set in the shell session or loaded via a tool like `dotenv-cli`.
