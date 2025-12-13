# Laragon & Redis Troubleshooting Guide

**Quick Diagnostics & Solutions for Common Issues**

---

## Issue: PHP Server Won't Start

### Error: "Port 8000 already in use"

```powershell
# Find process using port 8000
Get-NetTCPConnection -LocalPort 8000 | Select-Object OwningProcess

# Kill the process (replace PID with actual process ID)
Stop-Process -Id <PID> -Force

# Or use a different port
php artisan serve --port=8001
```

### Error: "Laravel server returned 1"

```bash
# Check for PHP syntax errors
php -l app/Http/Kernel.php

# Clear all caches
php artisan optimize:clear

# Check .env file exists
Test-Path .env
```

---

## Issue: MySQL Connection Refused

### Error: "Connection refused on 127.0.0.1:3306"

```powershell
# Verify MySQL is running
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306

# If failed, open Laragon UI and click "Start All"
# Or restart from command line:
# (Laragon must be in PATH; if not, add C:\laragon\bin to PATH)

net start MySQL80      # or MySQL57, depending on version
```

### Error: "SQLSTATE[HY000] [2002] No connection could be made"

```bash
# Check .env database configuration
grep DB_ .env

# Verify credentials (default Laragon: root / empty password)
php artisan db:show

# If credentials wrong, update .env:
# DB_HOST=127.0.0.1
# DB_USERNAME=root
# DB_PASSWORD=
```

### Error: "Access denied for user 'root'@'localhost'"

```bash
# Reset MySQL root password in Laragon:
# 1. Open Laragon UI → Menu → MySQL → Password
# 2. Set new password
# 3. Update .env: DB_PASSWORD=your_new_password
# 4. Restart MySQL service
```

---

## Issue: Redis Connection Refused

### Error: "MISCONF Redis is configured to save RDB snapshots but is currently not able to persist"

```bash
# From WSL
sudo systemctl restart redis-server

# Verify running
redis-cli ping
# Expected: PONG

# Check from Windows
wsl.exe -e redis-cli ping
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
```

### Error: "Connection refused on 127.0.0.1:6379" from Laravel

**Cause 1**: Laragon's bundled Redis is still running and conflicting with WSL Redis.

**Solution**:

1. Open Laragon 6 UI
2. Find **Redis** in the services list
3. Click the **stop/toggle button** next to Redis to **disable it**
4. Verify status shows "Stopped" or "Offline"
5. Restart Laravel: `php artisan serve`

**Cause 2**: WSL Redis not running or not accessible.

**Solution**:

```bash
# Option 1: Verify WSL Redis is running
wsl.exe -e redis-cli ping
# Expected: PONG

# Option 2: Restart WSL
wsl.exe --shutdown
wsl.exe  # Re-open WSL terminal

# Option 3: Restart Redis manually
wsl.exe -e sudo systemctl restart redis-server

# Option 4: Verify port accessibility from Windows
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
# Expected: TcpTestSucceeded = True

# Option 5: Check if Redis is bound to the right interface
# Check if WSL provides `systemctl` and `redis-cli` before using WSL commands. If you see an error like `/bin/sh: systemctl: not found` or `/bin/sh: redis-cli: not found`, WSL may not have systemd enabled or redis-tools installed.
#
# Install and enable systemd + redis in WSL, or use Laragon's Redis instead (recommended for Windows Laragon setups).
# Automated install helper (repo included):
# To install Redis in WSL automatically, run this from Windows PowerShell:
#
# ```powershell
# npm run wsl-redis-setup
# ```

wsl.exe -e redis-cli INFO server | Select-String "bind"
# Should show: bind 0.0.0.0
```

### Error: "WRONGPASS invalid username-password pair"

```bash
# Redis password is set but .env doesn't match
# Check .env:
grep REDIS_PASSWORD .env

# If password not needed, set to null:
# REDIS_PASSWORD=null

# If password set in Redis, update .env to match:
# REDIS_PASSWORD=your_password
```

---

## Issue: "No application encryption key has been specified"

### Error: `Illuminate\Encryption\MissingAppKeyException`

```bash
# Clear stale service container cache
php artisan optimize:clear

# Generate or verify key
php artisan key:generate
php artisan config:show app.key

# Test app boots
php artisan tinker
# Type: exit  or Ctrl+D
```

---

## Issue: Vite Frontend Assets Not Loading

### Error: "Unable to locate file in Vite manifest: resources/js/app.js"

```bash
# Build frontend assets
npm run build

# Or use dev watch mode
npm run dev

# (Keep npm run dev running in a separate terminal during development)
```

### Error: "Cannot GET /js/app.js" (404)

```bash
# Vite dev server not running
# Solution: npm run dev in another terminal
npm run dev

# Or check if Vite is on the right port (default 5173)
Test-NetConnection -ComputerName 127.0.0.1 -Port 5173
```

n# Vite/Node version mismatch
If you see an error like `You are using Node.js 18.8.0. Vite requires Node.js version 20.19+ or 22.12+` or `TypeError: crypto.hash is not a function`, it means your active Node version is too old. Resolve by:

```powershell
# Option A (Windows): Use the dev helper that ensures Node v22
npm run dev:win

# Option B (manual activation)
cd C:\laragon\www\ictserve-031125
. .\.env.ps1
npm run dev
```

Note: `npm run dev` includes a Node version check (`scripts/dev/check-node-version.js`) and will fail early if Node is too old.

---

## Issue: "CORS error" from API

### Error: "Access to XMLHttpRequest blocked by CORS policy"

```bash
# Check CORS config
cat config/cors.php

# Ensure endpoints are whitelisted
# In bootstrap/app.php, verify CORS middleware is active:
# grep -A 5 "cors" bootstrap/app.php

# Test API endpoint directly
Invoke-WebRequest -Uri http://127.0.0.1:8000/api/status

# If still failing, check response headers
Invoke-WebRequest -Uri http://127.0.0.1:8000/api/status -Headers @{'Accept'='application/json'}
```

---

## Issue: Queue Jobs Not Processing

### Error: "No connection could be made because the target machine actively refused it"

```bash
# Ensure Redis is running
redis-cli ping
# Expected: PONG

# Start queue worker
php artisan queue:work

# Or test job dispatch
php artisan tinker
>>> dispatch(new App\Jobs\TestJob());
>>> exit
```

### Error: Jobs stuck in "failed" table

```bash
# View failed jobs
php artisan queue:failed

# Retry failed job
php artisan queue:retry <id>

# Retry all failed
php artisan queue:retry all

# Clear failed jobs
php artisan queue:flush
```

---

## Issue: Dependencies Won't Install

### Error: "Composer install failed" or "npm install failed"

```bash
# Clear caches first
composer clear-cache
npm cache clean --force

# Update composer
composer update --self

# Try install again
composer install --no-interaction --prefer-dist
npm ci
```

### Error: "Package not found" or "401 Unauthorized"

```bash
# GitHub API rate limit or access denied
# Add GitHub token to .env:
# PAT_GITHUB_ACCESS_TOKEN=github_pat_...

# Or configure locally:
composer config --global github-oauth.github.com <token>
```

---

## Issue: Session or Cache Not Working

### Error: "Session store does not exist"

```bash
# Check .env
grep SESSION_DRIVER .env
# Should be: SESSION_DRIVER=redis (or file/database)

# Verify Redis is accessible
redis-cli ping

# Fallback to file driver (temporary)
# In .env: SESSION_DRIVER=file
```

### Error: "Cache driver does not exist"

```bash
# Check .env
grep CACHE_STORE .env
# Should be: CACHE_STORE=redis

# Verify connection
php artisan tinker
>>> cache()->put('test', 'value', 60)
>>> cache()->get('test')
# Should return: "value"
```

---

## Issue: Database Migrations Fail

### Error: "SQLSTATE[42S01]: Table already exists"

```bash
# Migration was already run; skip it
php artisan migrate --ignore-seeder-errors

# Or check migration history
php artisan migrate:status
```

### Error: "SQLSTATE[42000]: Syntax error"

```bash
# Migration SQL syntax is invalid
# Review the migration file:
ls database/migrations | grep your_migration

# Check for dialect compatibility (MySQL 8.0 vs others)
php artisan db:show
```

### Error: "Illuminate\Database\QueryException"

```bash
# Generic database error; check logs
tail -f storage/logs/laravel.log

# Verify database exists and credentials are correct
php artisan db:show

# Test connection directly
mysql -h 127.0.0.1 -u root -p ictserve
```

---

## Issue: Tests Fail

### Error: "SQLSTATE[HY000] [2002] No connection could be made"

```bash
# Tests need a database; use .env.testing or configure test database
cat phpunit.xml | grep DB_DATABASE

# Ensure test database exists
php artisan migrate --env=testing

# Run tests
php artisan test
```

### Error: "Error: ENOENT: no such file or directory"

```bash
# Missing Node modules or compiled assets
npm ci
npm run build

# Then re-run tests
php artisan test
```

---

## Issue: WSL Terminal Keyboard Not Working

### Error: "Can't type password or commands in WSL"

```powershell
# Use Windows Terminal (better compatibility)
# Or enable Ctrl+Shift+C/V copy/paste:
# WSL Window → Properties → Options → Copy/Paste

# Or run with explicit root (no password prompt)
wsl.exe --user root
```

---

## System Diagnostics

### Check All Services Status

```powershell
# Apache/Nginx
Test-NetConnection -ComputerName 127.0.0.1 -Port 80

# MySQL
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306

# Redis (WSL)
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379

# Laravel Server (if running)
Test-NetConnection -ComputerName 127.0.0.1 -Port 8000

# Vite Dev Server (if running)
Test-NetConnection -ComputerName 127.0.0.1 -Port 5173
```

### Check Logs

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# MySQL logs (if available)
# C:\laragon\data\mysql\*.err

# WSL Redis logs
wsl.exe -e sudo journalctl -u redis-server -f
```

### Quick Health Check

```bash
php artisan tinker
>>> $this->db = DB::connection();
>>> $this->db->getVersion()
# Shows MySQL version if connected

>>> cache()->put('test', 'works', 60)
>>> cache()->get('test')
# Returns 'works' if Redis working

>>> exit
```

---

## Advanced Debugging

### Enable Query Logging

```php
// In routes/web.php or controller temporarily:
DB::enableQueryLog();
// ... your code ...
dd(DB::getQueryLog());
```

### Use Laravel Telescope

```bash
# Access debug toolbar
php artisan serve

# Visit: http://127.0.0.1:8000/telescope
```

### Check PHP Configuration

```bash
php -i                    # Full PHP info
php -r "phpinfo();"       # Same thing
php --version             # Version
php -m | Select-String redis  # Check redis extension loaded
```

---

## Getting Help

If issues persist:

1. **Check logs**: `storage/logs/laravel.log`
2. **Verify services**: All ports (80, 3306, 6379, 8000) accessible
3. **Check `.env` file**: Database, Redis, app credentials
4. **Search GitHub Issues**: <https://github.com/IzzatFirdaus/ictserve-031125/issues>
5. **Contact DevOps**: <devops@motac.gov.my>

---

**Last Updated**: December 7, 2025
