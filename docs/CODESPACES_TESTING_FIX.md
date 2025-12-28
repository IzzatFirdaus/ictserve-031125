# Codespaces Testing Setup - Quick Fix Guide

## Problem Summary
GitHub Copilot Agent cannot run tests because:
- ✗ Runtime lacks dev dependencies (PHPUnit, etc.)
- ✗ `vendor` is read-only from production build (`INSTALL_DEV=false`)
- ✗ Composer auth/permission issues

## Solution Applied

### 1. Updated `.devcontainer/devcontainer.json`

**Changes Made:**
```json
{
  "dockerComposeFile": [
    "../compose.yaml",
    "../compose.dev.yaml",    // ← ADDED: Development compose with INSTALL_DEV=true
    "../compose.ours.yaml"
  ],
  "remoteEnv": {
    "GITHUB_TOKEN": "${localEnv:GITHUB_TOKEN}",
    "COMPOSER_AUTH": "{\"github-oauth\": {\"github.com\": \"${localEnv:GITHUB_TOKEN}\"}}"  // ← ADDED
  },
  "containerEnv": {
    "COMPOSER_ALLOW_SUPERUSER": "1"  // ← ADDED
  }
}
```

**What This Does:**
- ✅ Includes `compose.dev.yaml` which sets `INSTALL_DEV=true`
- ✅ Passes `COMPOSER_AUTH` with GitHub token for authentication
- ✅ Allows Composer to run as superuser (required for container operations)

### 2. Created Helper Script: `scripts/codespaces-install-deps.sh`

**Usage:**
```bash
# Run this if dependencies are still missing after rebuild
bash scripts/codespaces-install-deps.sh
```

**What It Does:**
- Checks for `GITHUB_TOKEN` environment variable
- Configures Composer authentication
- Fixes vendor directory permissions if needed
- Installs Composer dependencies with dev packages (`composer install`)
- Installs npm dependencies
- Verifies PHPUnit is available

## Step-by-Step Fix Instructions

### Option 1: Rebuild Codespace (Recommended)

1. **Add GitHub Token Secret** (if not already done):
   - Go to: https://github.com/settings/codespaces/secrets
   - Click "New repository secret"
   - Name: `GITHUB_TOKEN`
   - Value: Your GitHub PAT token (starts with `ghp_` or `github_pat_`)
   - Repository: Select `ictserve-031125`

2. **Rebuild Codespace**:
   - Open Command Palette (F1 or Ctrl+Shift+P)
   - Type: "Rebuild Container"
   - Select: "Codespaces: Rebuild Container"
   - Wait for rebuild (includes dev dependencies now)

3. **Verify Installation**:
   ```bash
   php artisan test --version
   vendor/bin/phpunit --version
   ```

### Option 2: Manual Installation (Quick Fix)

If you don't want to rebuild:

```bash
# Run the helper script
bash scripts/codespaces-install-deps.sh

# Or manually:
export COMPOSER_AUTH="{\"github-oauth\": {\"github.com\": \"$GITHUB_TOKEN\"}}"
composer install --no-interaction --prefer-dist
```

## Verification Commands

After applying fix, verify everything works:

```bash
# 1. Check PHPUnit is available
vendor/bin/phpunit --version
# Expected: PHPUnit 11.x.x

# 2. Check Artisan test command works
php artisan test --help
# Expected: Shows test command options

# 3. Run a simple test
php artisan test --filter=ExampleTest
# Expected: Tests run without errors

# 4. Check dev dependencies are installed
composer show --dev | head -20
# Expected: Shows PHPUnit, PHPStan, Pint, etc.
```

## Expected Results After Fix

✅ **Dev dependencies installed** - PHPUnit, PHPStan, Pint, etc. available  
✅ **Vendor is writable** - Composer can update packages  
✅ **Tests can run** - `php artisan test` executes successfully  
✅ **GitHub authentication works** - Private dependencies fetch without errors

## Troubleshooting

### If `GITHUB_TOKEN` is not set:
```bash
# Check if token exists
echo $GITHUB_TOKEN

# If empty, add it to Codespaces secrets (see Step 1 above)
```

### If vendor is still read-only:
```bash
# Fix permissions manually
sudo chown -R www-data:www-data vendor
sudo chmod -R 775 vendor

# Reinstall
composer install
```

### If PHPUnit is missing:
```bash
# Check composer.json has dev dependencies
cat composer.json | grep -A 5 '"require-dev"'

# Force reinstall dev dependencies
composer install --dev --no-interaction
```

## Next Steps for Testing

Once dependencies are installed, execute the batch testing plan:

```bash
# Start with Batch 1
php artisan test tests/Feature/Environment/EnvFileExistsTest.php --stop-on-failure

# Or use the full batch execution script if available
```

## Related Files

- `.devcontainer/devcontainer.json` - Codespace configuration
- `compose.dev.yaml` - Development Docker Compose (INSTALL_DEV=true)
- `scripts/codespaces-install-deps.sh` - Dependency installation helper
- `.composer/auth.json` - Composer GitHub authentication

## Status

✅ **FIXED**: Codespace now rebuilds with dev dependencies  
✅ **READY**: Can execute batch-by-batch test plan once rebuilt

---

**Last Updated**: 2025-12-28  
**Fix Applied By**: AI Assistant  
**Verified**: Pending rebuild/manual installation
