---
title: "Quick Reference: Laravel 12 Bootstrap Troubleshooting"
description: "Troubleshooting guide for common Laravel 12 bootstrap and environment loading issues"
---

# Laravel 12 Bootstrap Troubleshooting Quick Reference

## Common Issues & Fixes

### Issue: `MissingAppKeyException` on startup

**Symptoms**:

```
Illuminate\Encryption\MissingAppKeyException
No application encryption key has been specified.
```

**Quick Fix**:

1. Check `.env` file exists: `ls -la .env` or `Get-Item .env`
2. Verify APP_KEY is set: `grep APP_KEY .env` or `Select-String APP_KEY .env`
3. Check `public/index.php` has Dotenv loading (lines 17-23):

   ```php
   $envPath = __DIR__.'/../.env';
   if (file_exists($envPath)) {
       $dotenv = \Dotenv\Dotenv::createImmutable(__DIR__.'/..');
       $dotenv->load();
   }
   ```

4. If not present, add the code above before `require_once bootstrap/app.php`

**See**: `docs/LARAVEL_12_ENV_LOADING_FIX.md` for detailed explanation

---

### Issue: `.env` file exists but isn't being read

**Check**:

```bash
# Verify .env is readable
file .env
Get-Item .env  # PowerShell

# Verify Dotenv library is installed
composer show vlucas/phpdotenv

# Test Dotenv manually
php -r "
\$dotenv = \Dotenv\Dotenv::createImmutable('.');
\$dotenv->load();
echo 'APP_KEY: ' . getenv('APP_KEY');
"
```

**Common Causes**:

- `.env` file in wrong directory (must be project root, not `public/`)
- File permissions preventing read access
- Dotenv library not installed: `composer require vlucas/phpdotenv`
- App entry point not loading `.env` before bootstrap

---

### Issue: APP_KEY is empty or invalid

**Check**:

```bash
grep APP_KEY .env
# Should output: APP_KEY=base64:xxxxxxxxxxxxx (not empty)
```

**Fix**:

```bash
php artisan key:generate
# Generates new valid APP_KEY
```

---

### Issue: Application works in CLI but not in HTTP

**Cause**: Different entry points have different environment loading

**Check**:

- CLI uses `artisan` file which handles environment loading
- HTTP uses `public/index.php` which must explicitly load `.env`
- Verify `public/index.php` contains Dotenv loading code

**Common Fix**: Add Dotenv loading to `public/index.php`

---

## Bootstrap Execution Order (Laravel 12)

```
1. HTTP Request → public/index.php
2. Load Composer Autoloader
3. [CRITICAL] Load .env file via Dotenv
4. Require bootstrap/app.php
   ├─ Application::configure()
   ├─ Load service providers
   ├─ Load config files
   └─ Initialize encryption service (requires APP_KEY)
5. Request handling → Route dispatch
```

⚠️ **Step 3 is REQUIRED in Laravel 12** — do not skip

---

## Verification Checklist

```
☐ .env file exists in project root
☐ APP_KEY is set and non-empty
☐ APP_KEY is valid base64 format
☐ public/index.php contains Dotenv loading code
☐ Dotenv library is installed (vendor/vlucas/phpdotenv)
☐ public/index.php loads .env BEFORE bootstrap/app.php
☐ .env file has correct permissions (readable by web server)
☐ Application boots without errors: php artisan tinker
☐ HTTP requests work: php artisan serve
```

---

## Debugging Commands

```bash
# Test APP_KEY is accessible
php artisan tinker --execute="echo config('app.key');"

# Verify .env is being loaded
php artisan tinker --execute="echo getenv('APP_KEY');"

# Check environment variables
php artisan tinker --execute="var_dump(\$_ENV['APP_KEY'] ?? 'NOT SET');"

# Test bootstrap directly
php public/index.php 2>&1 | head -50

# Check .env file location and permissions
php -r "echo 'Project root: ' . getcwd();" 
php -r "echo '.env exists: ' . (file_exists('.env') ? 'YES' : 'NO');"
php -r "echo '.env readable: ' . (is_readable('.env') ? 'YES' : 'NO');"
```

---

## References

- **Full Documentation**: `docs/LARAVEL_12_ENV_LOADING_FIX.md`
- **Modified File**: `public/index.php` (lines 17-23)
- **Trace ID**: D00-Laravel12-EnvLoading
- **Status**: ✅ Resolved in ICTServe

---

**Last Updated**: 2025-12-07
