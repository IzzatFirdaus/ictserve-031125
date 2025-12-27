# Playwright E2E Tests - Troubleshooting Guide

## Quick Fix Checklist

### Before Running Tests

- [ ] npm dependencies installed: `npm ci`
- [ ] Playwright browsers installed: `npx playwright install --with-deps`
- [ ] Composer dependencies installed: `composer install --ignore-platform-reqs`
- [ ] `.env` file exists and configured
- [ ] `APP_KEY` generated: `php artisan key:generate`
- [ ] Database migrated: `php artisan migrate`
- [ ] Test users seeded: `php artisan db:seed --class=StaffUserSeeder`
- [ ] Laravel server running: `php artisan serve --host=127.0.0.1 --port=8000`

## Common Errors & Solutions

### 1. "Cannot find module percy-utils"

**Error**:
```
Error: Cannot find module '/path/to/tests/e2e/utils/percy-utils'
```

**Status**: ✅ FIXED

**Solution**: This issue has been resolved by creating the `tests/e2e/utils/percy-utils.ts` wrapper module.

### 2. "Could not authenticate against github.com"

**Error**:
```
Failed to download package from dist: Could not authenticate against github.com
```

**Cause**: GitHub API rate limiting during `composer install`

**Solutions**:

#### Option A: Use GitHub Personal Access Token
```bash
# Create token at https://github.com/settings/tokens
# Set environment variable
export COMPOSER_AUTH='{"github-oauth": {"github.com": "your_token_here"}}'

# Then run
composer install --ignore-platform-reqs
```

#### Option B: Use Composer Config
```bash
composer config -g github-oauth.github.com your_token_here
composer install --ignore-platform-reqs
```

#### Option C: Wait and Retry
```bash
# Wait 1 hour for rate limit reset
# Check rate limit status
curl -H "Authorization: token YOUR_TOKEN" https://api.github.com/rate_limit

# Retry install
composer install --ignore-platform-reqs
```

### 3. "symfony/cache conflicts with ext-redis"

**Error**:
```
symfony/cache v7.4.1 conflicts with ext-redis <6.1
ext-redis is present at version 5.3.7
```

**Solutions**:

#### Option A: Ignore Platform Requirements (Recommended for Testing)
```bash
composer install --ignore-platform-reqs
```

#### Option B: Upgrade Redis Extension (System-Level)
```bash
# Ubuntu/Debian
sudo pecl install redis

# Or upgrade to Redis 6.1+
sudo apt-get install php8.3-redis
```

#### Option C: Use Different Cache Driver
```env
# In .env file
CACHE_DRIVER=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

### 4. "Process from config.webServer was not able to start"

**Error**:
```
Error: Process from config.webServer was not able to start. Exit code: 255
PHP Fatal error: Failed opening required 'vendor/autoload.php'
```

**Cause**: Composer dependencies not installed

**Solution**:
```bash
# Install dependencies
composer install --ignore-platform-reqs

# Verify vendor/autoload.php exists
ls -la vendor/autoload.php
```

### 5. "No tests found"

**Error**:
```
Listing tests:
Total: 0 tests in 0 files
```

**Cause**: Usually syntax errors or missing dependencies in test files

**Solution**:
```bash
# Check for syntax errors
npx playwright test --list 2>&1 | grep -i "error\|syntax"

# If percy-utils errors appear, verify file exists
ls -la tests/e2e/utils/percy-utils.ts

# Re-run test discovery
SKIP_PERCY=true npx playwright test --list
```

### 6. "waitForURL timeout"

**Error**:
```
TimeoutError: page.waitForURL: Timeout 90000ms exceeded
```

**Causes**:
1. Laravel server not running
2. Laravel server startup too slow
3. Database not seeded
4. Authentication issues

**Solutions**:

#### Check Laravel Server
```bash
# Verify server is running
curl http://127.0.0.1:8000

# Check server logs
php artisan serve --host=127.0.0.1 --port=8000
```

#### Verify Database
```bash
# Check if test users exist
php artisan tinker
>>> \App\Models\User::where('email', 'userstaff@motac.gov.my')->first()
```

#### Increase Timeouts (Last Resort)
Edit `tests/e2e/fixtures/ictserve-fixtures.ts`:
```typescript
// Increase timeout from 90000 to 120000
await page.waitForURL('/dashboard', { timeout: 120000 });
```

### 7. "Percy snapshot failed"

**Error**:
```
❌ Percy snapshot failed: [name]
```

**Causes**:
1. `PERCY_TOKEN` not set
2. Percy disabled via `SKIP_PERCY=true`
3. Network issues

**Solutions**:

#### Verify Percy Configuration
```bash
# Check if Percy is enabled
echo $PERCY_TOKEN
echo $SKIP_PERCY

# Test Percy connection
npx percy --version
```

#### Run Without Percy
```bash
# Skip Percy snapshots
SKIP_PERCY=true npm run test:e2e
```

#### Enable Percy
```bash
# Set Percy token
export PERCY_TOKEN=your_percy_token_here
export PERCY_PROJECT=ictserve

# Run with Percy
npm run test:e2e:percy
```

### 8. "CSRF token mismatch"

**Error**:
```
419 | Page Expired
```

**Causes**:
1. Session expired during test
2. CSRF middleware blocking requests
3. Cookie issues

**Solutions**:

#### Verify .env Configuration
```env
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
SESSION_SECURE_COOKIE=false
SESSION_SAME_SITE=lax
```

#### Clear Laravel Cache
```bash
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## Test Execution Workflow

### Step 1: Environment Setup
```bash
# Clone repository
cd /path/to/ictserve-031125

# Install dependencies
npm ci
composer install --ignore-platform-reqs

# Setup environment
cp .env.example .env
php artisan key:generate
```

### Step 2: Database Setup
```bash
# Create database (if using MySQL)
mysql -u root -p
CREATE DATABASE ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
exit

# Run migrations
php artisan migrate

# Seed test users
php artisan db:seed --class=StaffUserSeeder
```

### Step 3: Install Playwright
```bash
npx playwright install --with-deps
```

### Step 4: Start Laravel Server
```bash
# Terminal 1: Laravel server
php artisan serve --host=127.0.0.1 --port=8000

# Keep this running
```

### Step 5: Run Tests
```bash
# Terminal 2: Run tests
SKIP_PERCY=true npm run test:e2e

# Or run specific tests
SKIP_PERCY=true npx playwright test branding-smoke.spec.ts --project=chromium
```

## Performance Tips

### Speed Up Test Execution

1. **Use SQLite for Testing**
```env
DB_CONNECTION=sqlite
DB_DATABASE=:memory:
```

2. **Disable Unnecessary Services**
```env
CACHE_DRIVER=array
QUEUE_CONNECTION=sync
SESSION_DRIVER=array
```

3. **Run Specific Tests**
```bash
# Run only smoke tests
npx playwright test --grep="smoke"

# Run only one browser
npx playwright test --project=chromium

# Skip performance tests
SKIP_PERFORMANCE=true npx playwright test
```

4. **Parallel Execution**
```bash
# Use more workers (careful with database)
npx playwright test --workers=4
```

## Debugging Tips

### Visual Debugging
```bash
# UI mode (recommended)
npx playwright test --ui

# Headed browser (see what's happening)
npx playwright test --headed

# Debug mode (step through)
npx playwright test --debug
```

### Capture Artifacts
```bash
# Take screenshots on failure
npx playwright test --screenshot=only-on-failure

# Record video
npx playwright test --video=retain-on-failure

# Generate trace
npx playwright test --trace=on
```

### Check Test Logs
```bash
# Show browser console logs
npx playwright test --workers=1 --reporter=list

# Generate HTML report
npx playwright show-report
```

## Environment Variables Reference

```env
# Laravel
APP_ENV=testing
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

# Database (SQLite recommended for tests)
DB_CONNECTION=sqlite
DB_DATABASE=:memory:

# Percy (optional)
PERCY_TOKEN=your_token
PERCY_PROJECT=ictserve
PERCY_ENABLED=true
SKIP_PERCY=false

# Performance Tests
SKIP_PERFORMANCE=false

# Playwright
CI=false
```

## Getting Help

### Check Test Status
```bash
# List all tests
npx playwright test --list

# Check for errors
npx playwright test --list 2>&1 | grep -i error
```

### Verify Installation
```bash
# Check npm packages
npm ls @playwright/test @percy/playwright

# Check Playwright browsers
npx playwright --version

# Check Laravel
php artisan --version
```

### Review Documentation
1. `PLAYWRIGHT_TEST_STATUS.md` - Current test status
2. `playwright.config.ts` - Playwright configuration
3. `tests/e2e/fixtures/ictserve-fixtures.ts` - Test fixtures
4. `tests/e2e/utils/percy-utils.ts` - Percy utilities

---

**Last Updated**: 2025-12-27  
**Status**: Tests Ready for Execution (Pending Dependency Resolution)
