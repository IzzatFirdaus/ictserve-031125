---
title: "Production Setup Troubleshooting Guide"
description: "Comprehensive record of HTTP 500 errors and infrastructure issues encountered during production deployment with Docker, Nginx, PHP-FPM, and Laravel 12"
date: "2025-12-02"
status: "Resolved"
trace: "D03-FR-001, D04-SEC-01, D11-SYS-001"
---

# Production Setup Troubleshooting Guide

**Date**: December 2, 2025  
**System**: ICTServe v3.5.0 - Laravel 12, Docker Compose, Nginx, PHP-FPM  
**Issue**: HTTP 500 errors preventing homepage from loading after infrastructure restart  
**Resolution**: Complete - All 6 blocking issues identified and fixed

---

## Executive Summary

After restarting Docker containers with production configuration, the homepage returned HTTP 500 errors despite all containers being healthy and running. Through systematic debugging, six distinct issues were identified and resolved:

1. **Broadcast Channel Errors** - Undefined Redis connection
2. **Vendor Dependency Cache** - Laravel Sail service provider references
3. **Missing Encryption Key** - Empty APP_KEY environment variable
4. **Missing Database Migrations** - Sessions table not created
5. **Nginx Host Header Bug** - Asset URLs missing port number
6. **Vite Dev Server File** - Production assets not served from manifest

Each issue has been documented with root cause, symptoms, solution, and verification.

---

## Issue #1: Broadcast Channel Initialization Errors

### Symptoms

- **Error Message**: `InvalidArgumentException: Broadcast connection [redis] is not defined`
- **HTTP Status**: 500 Internal Server Error
- **Frequency**: Every request
- **Error Location**: `routes/channels.php` line 11 during app bootstrap

### Root Cause Analysis

The file `routes/channels.php` contained four `Broadcast::channel()` definitions:

```php
Broadcast::channel('user.{id}', function ($user, $id) { ... });
Broadcast::channel('submission.{type}.{id}', function ($user, $type, $id) { ... });
Broadcast::channel('asset.{id}', function ($user, $id) { ... });
Broadcast::channel('App.Models.User.{id}', function ($user, $id) { ... });
```

Each call during app bootstrap triggered Laravel's BroadcastManager to initialize the broadcast connection. The `.env.docker` was configured with `BROADCAST_CONNECTION=reverb`, but the channel registration code tried to use Redis driver via the facade, which wasn't defined in the container configuration.

### Technical Details

- **Broadcast Manager Location**: `vendor/laravel/framework/src/Illuminate/Broadcasting/BroadcastManager.php`
- **Driver Resolution**: Attempts to resolve `redis` connection from `config/database.php`
- **In Production Docker**: Redis not configured (database driver used for sessions instead)
- **Timing**: Error occurs during service container bootstrap, before request logging initialized

### Solution Implemented

**File**: `routes/channels.php`

Commented out all four broadcast channel definitions to prevent bootstrap-time initialization:

```php
// Broadcast::channel('user.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

// Broadcast::channel('submission.{type}.{id}', function ($user, $type, $id) {
//     return match ($type) {
//         'ticket' => $user->can('view', \App\Models\HelpdeskTicket::find($id)),
//         'loan' => $user->can('view', \App\Models\LoanApplication::find($id)),
//         default => false,
//     };
// });

// Broadcast::channel('asset.{id}', function ($user, $id) {
//     return $user->can('view', Asset::find($id));
// });

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });
```

### Rationale

- **Short-term**: Prevents 500 errors on every page load
- **Long-term**: Real-time features (broadcasting) require proper Redis/Reverb setup in production
- **Current State**: Single-tenant staff portal doesn't require real-time notifications
- **Future**: Can be re-enabled when real-time infrastructure is available

### Verification

After fix:

```bash
docker compose logs app --tail=20
# No more broadcast channel errors in logs
```

---

## Issue #2: Vendor Cache - Sail Service Provider Not Found

### Symptoms

- **Error Message**: `Class "Laravel\Sail\SailServiceProvider" not found`
- **Context**: Occurred after running `php artisan optimize:clear`
- **Command**: `php artisan optimize:clear` initially cleared bootstrap caches
- **Subsequent Error**: When requesting homepage, Laravel tried to load deferred providers from cache

### Root Cause Analysis

The bootstrap cache stored references to deferred service providers from `composer.lock`. The file listed `laravel/sail` as a transitive dependency, but Sail is a dev-only package not installed in the production vendor directory.

**Discovery Process**:

1. Ran `php artisan optimize:clear` to reset caches after fixing broadcast channels
2. App attempted to rebuild service provider cache
3. Package discovery found `Laravel\Sail\SailServiceProvider` reference in lock file
4. Container rebuild failed because Sail package wasn't in vendor
5. Deferred provider loading failed silently until next request

### Technical Details

- **Affected File**: `composer.lock` (transitive dependency reference)
- **Package**: `laravel/sail` (^1.43.1) - development only
- **Location**: In vendor autoload manifest from previous installation
- **Impact**: Only occurs when bootstrap caches are cleared and rebuilt

### Solution Implemented

**Command**:

```bash
docker compose exec app composer install --no-dev --no-interaction --prefer-dist --ignore-platform-req=ext-gd
```

**Actions Taken**:

1. Removed all 68 development packages from vendor
2. Ran package discovery with production packages only
3. Rebuilt autoloader without Sail references
4. Cleared configuration cache
5. Cleared route cache
6. Cleared compiled views
7. Ran Filament upgrade

**Output**:

```
Package discovery completed
Configuration cache cleared
Route cache cleared
Compiled views cleared
Filament upgrade completed
```

### Verification

After fix:

```bash
docker compose exec app php artisan route:list
# Successfully lists all routes without provider errors

docker compose exec app composer dump-autoload
# No Sail provider references in autoload
```

---

## Issue #3: Missing Application Encryption Key

### Symptoms

- **Error Message**: `No application encryption key has been specified`
- **Location**: `vendor/laravel/framework/src/Illuminate/Encryption/EncryptionServiceProvider.php:83`
- **HTTP Status**: 500 Internal Server Error
- **Timing**: During app bootstrap (before request logging)

### Root Cause Analysis

The file `.env.docker` had `APP_KEY=` (blank value). Docker Compose loads environment variables from `.env.docker` at **container creation time** via the `env_file:` directive in `compose.yaml`.

**Key Discovery**:

- Container restart (`docker restart`) does NOT reload `.env.docker`
- Container recreation (`docker rm` + `docker compose up`) DOES reload environment
- Simply updating `.env.docker` on host and restarting container = stale environment

### Technical Details

- **Config File**: `.env.docker` (loaded by compose.yaml's `env_file:` directive)
- **Service Provider**: `EncryptionServiceProvider` in `bootstrap/providers.php`
- **Check Location**: `EncryptionServiceProvider::register()` at line 83
- **Condition**: Throws exception if `APP_KEY` is empty or not set

### Solution Implemented

**Step 1**: Updated `.env.docker` with encryption key value:

```dotenv
APP_ENV=production
APP_KEY=base64:/2lLuUKZI9QSDTBWO6/8pCyCvinRoBWIZRK4w50szFk=
APP_DEBUG=false
```

**Step 2**: Forced container recreation to reload environment:

```bash
docker container rm ictserve-app -f
docker compose up -d app
```

**Step 3**: Verified environment variable in new container:

```bash
docker compose exec app sh -c "echo $APP_KEY"
# Output: base64:/2lLuUKZI9QSDTBWO6/8pCyCvinRoBWIZRK4w50szFk=
```

### Key Insight

```
Environment Variables are Loaded at Container Creation Time
┌─────────────────────────────────────────────────────────┐
│ Edit .env.docker on host                                │
│ ✗ docker restart app → OLD environment                  │
│ ✓ docker rm + docker compose up → NEW environment       │
└─────────────────────────────────────────────────────────┘
```

### Verification

After fix:

```bash
docker compose exec app php artisan tinker --execute "dd(env('APP_KEY'))"
# Output: "base64:/2lLuUKZI9QSDTBWO6/8pCyCvinRoBWIZRK4w50szFk="
```

---

## Issue #4: Missing Database Migrations - Sessions Table

### Symptoms

- **Error Message**: `SQLSTATE[42S02]: Base table or view not found: 1146 Table 'ictserve.sessions' doesn't exist`
- **SQL Query**: `select * from sessions where id = '9T4W6kVWkwFoVGhz7FRKSPHYApnWEl0Yh3v0fkyL'`
- **HTTP Status**: 500 Internal Server Error
- **Root Cause**: Application attempts to retrieve session on first request, table missing

### Root Cause Analysis

The database was never migrated after container restart. Laravel stores sessions in the database (configured via `config/session.php`), but the sessions table was never created.

**Why Not Caught Earlier**:

1. First request fails with broadcast channel error (before reaching session retrieval)
2. After fixing broadcast channels, app progresses further in bootstrap
3. Only when trying to read existing session from database does the error appear

### Technical Details

- **Session Driver**: Database (from `config/session.php`)
- **Migration**: Typically created with `php artisan session:table` command
- **Migration File**: Auto-generated in `database/migrations/`
- **Table Name**: `sessions` in `ictserve` database

### Solution Implemented

**Step 1**: Run all pending migrations:

```bash
docker compose exec app php artisan migrate --force --no-interaction
```

**Output** (40+ migrations):

```
Creating migration table
Running migrations:
  0001_01_01_000001_create_cache_table ........................... DONE
  0001_01_01_000002_create_jobs_table ............................ DONE
  2025_11_03_043832_create_divisions_table ....................... DONE
  2025_11_03_043839_create_grades_table .......................... DONE
  2025_11_03_043847_create_positions_table ....................... DONE
  2025_11_03_043900_create_users_table ........................... DONE
  [... 33 more migrations ...]
  2025_12_01_000010_create_activity_log_table .................... DONE
```

**Note**: The sessions table migration is NOT included in the standard migration set, requiring separate generation.

### Verification

After migrations:

```bash
docker compose exec app mysql -h db -u root -p"$DB_PASSWORD" -e "SHOW TABLES FROM ictserve" | grep sessions
# Output: sessions
```

---

## Issue #5: Nginx Host Header Bug - Asset URLs Missing Port

### Symptoms

- **Observable**: Network requests show CSS/JS loading from `http://localhost/build/...` instead of `http://localhost:8000/build/...`
- **Result**: Asset requests fail with connection refused (no service on port 80 from container perspective)
- **HTTP Status**: Assets fail to load, page renders unstyled
- **Debugging**: Appeared correct in config, but Network tab showed wrong URLs

### Root Cause Analysis

Nginx configuration had:

```nginx
fastcgi_param HTTP_HOST $host;
```

In Nginx variables:

- `$host` = hostname only (e.g., `localhost`)
- `$http_host` = hostname + port (e.g., `localhost:8000`)

Laravel's `url()` and `asset()` helpers generate URLs based on the Host header passed by Nginx through `HTTP_HOST` FastCGI parameter.

**Impact Chain**:

1. Browser requests `http://localhost:8000/` (with port)
2. Nginx receives request and sets `fastcgi_param HTTP_HOST localhost` (NO port)
3. PHP receives `$_SERVER['HTTP_HOST'] = 'localhost'`
4. Laravel generates URLs as `http://localhost/build/css/app.css` (no port)
5. Browser tries to connect to `http://localhost:80/build/css/...` → fails

### Technical Details

- **Nginx Variable Reference**: <http://nginx.org/en/docs/http/ngx_http_core_module.html>
- **PHP Receives As**: `$_SERVER['HTTP_HOST']` (set by fastcgi_param)
- **Laravel Uses**: `Request::getHttpHost()` which reads HTTP_HOST
- **Config File**: `nginx.conf` in project root

### Solution Implemented

Updated both FastCGI parameter locations in `nginx.conf`:

**Location 1** (Health check endpoint):

```diff
- fastcgi_param HTTP_HOST $host;
+ fastcgi_param HTTP_HOST $http_host;
```

**Location 2** (PHP file handling):

```diff
- fastcgi_param HTTP_HOST $host;
+ fastcgi_param HTTP_HOST $http_host;
```

**Restart Nginx**:

```bash
docker compose restart nginx
```

### Verification

After fix:

```bash
# Network tab shows correct URLs
GET http://localhost:8000/build/css/app-D2av8Pxw.css [200 OK]
GET http://localhost:8000/build/js/app-CwmGDm4N.js [200 OK]
```

---

## Issue #6: Vite Dev Server File - Production Assets Not Served

### Symptoms

- **File**: `public/hot` (empty file, created by Vite dev server)
- **Behavior**: Presence of this file causes Laravel to treat app as running in dev mode
- **Result**: Vite helper tries to load from dev server at `http://127.0.0.1:5173` instead of manifest
- **Expected**: Should load from `public/build/manifest.json` with versioned asset paths

### Root Cause Analysis

Laravel's Vite helper detects dev vs. production mode by checking for `public/hot` file:

```php
// In Vite helper
if (file_exists($this->publicPath('hot'))) {
    // DEV MODE: use dev server
    return "http://127.0.0.1:5173";
} else {
    // PRODUCTION MODE: use manifest
    return manifest.json assets
}
```

The `public/hot` file was present from development setup and never cleaned up before production deployment.

### Technical Details

- **File Location**: `public/hot`
- **Created By**: Laravel/Vite when dev server runs (npm run dev)
- **Content**: Port number of dev server (typically 5173)
- **Detection**: Checked on every page load by Vite helper
- **Persistence**: File persists across container restarts unless explicitly deleted

### Solution Implemented

**Step 1**: Remove the hot file from container:

```bash
docker compose exec app rm -f public/hot
```

**Step 2**: Verify removal:

```bash
docker compose exec app ls -la public/hot
# Output: ls: public/hot: No such file or directory ✓
```

**Step 3**: Test asset loading:

```bash
# Make fresh request
docker compose exec app curl http://localhost:8000/ > /dev/null

# Check Network tab - should show:
# GET http://localhost:8000/build/css/app-D2av8Pxw.css [200]
# NOT: http://127.0.0.1:5173/resources/css/app.css
```

### Why APP_DEBUG Matters Here

- `APP_DEBUG=true` → Vite helper returns errors more verbosely
- `APP_DEBUG=false` → Silent fallback (CSS/JS fail to load but app continues)

The fix was discovered by temporarily setting `APP_DEBUG=true` to see the actual error showing Vite dev server attempts.

---

## Debugging Timeline & Key Insights

### Phase 1: Initial Restart (Containers Healthy but Pages 500)

```
✓ All 11 containers running
✓ PHP-FPM healthy with workers
✓ Nginx configured and listening
✗ Every request returns HTTP 500
✗ Logs show error but no details visible
```

**Insight**: When Laravel fails to bootstrap, logging doesn't initialize → log file remains empty

### Phase 2: Discovery of Broadcast Channel Error

```
✗ Checked error logs: completely empty
✗ Tried artisan commands: worked fine
✗ Tried HTTP requests: 500 errors
→ Enabled APP_DEBUG=true to see detailed errors
```

**Key Learning**: Browser with DevTools shows full error details when APP_DEBUG=true

### Phase 3: Cascade of Dependent Issues

```
Fix #1: Broadcast channels commented
↓
Fix #2: Composer dependency cache cleaned
↓
Fix #3: APP_KEY added to env
↓
Fix #4: Container recreated to load env
↓
Fix #5: Database migrations run
↓
→ Now page loads! But CSS missing...
↓
Fix #6: Nginx host header corrected
Fix #7: Vite hot file removed
↓
→ Page fully styled!
```

**Key Learning**: Multiple issues compound. Each fix uncovers the next issue when removed.

### Phase 4: CSS Not Loading - Wrong URLs

```
✓ Production CSS built: app-D2av8Pxw.css exists
✓ Manifest configured correctly
✗ Network tab shows: http://localhost/build/css/app.css (NO PORT)
→ Nginx $host vs $http_host variable confusion
```

**Key Learning**: Network debugging essential - URL details reveal infrastructure issues

---

## Prevention Checklist for Future Deployments

### Pre-Deployment

- [ ] Verify all environment variables in `.env.docker` are correctly set
- [ ] Verify `APP_KEY` is non-empty and valid base64
- [ ] Remove `public/hot` file before containerizing
- [ ] Confirm `APP_DEBUG=false` and `APP_ENV=production`
- [ ] Review broadcast configuration matches actual infrastructure (Redis, Reverb, etc.)

### Container Build

- [ ] Use `--no-dev` flag in composer install to avoid dev dependencies
- [ ] Verify no service provider references development packages
- [ ] Run database migrations before starting services
- [ ] Create sessions table if using database driver

### Nginx Configuration

- [ ] Use `$http_host` instead of `$host` for all FastCGI parameters
- [ ] Ensure all PHP location blocks pass proper Host header
- [ ] Test asset URLs in Network tab to verify port is included

### Bootstrap Verification

- [ ] Check container logs for any provider errors
- [ ] Verify artisan commands work (indicates app bootstraps correctly)
- [ ] Test homepage with `curl` from container and host
- [ ] Confirm CSS/JS load from manifest paths with port number

### Testing Script

```bash
#!/bin/bash
# Production readiness check
docker compose exec app php artisan route:list > /dev/null && echo "✓ Routes OK"
docker compose exec app php artisan config:cache && echo "✓ Config cached"
docker compose exec app ls public/hot > /dev/null 2>&1 && echo "✗ hot file exists!" || echo "✓ No hot file"
docker compose exec app php artisan tinker --execute "dd(config('app.key'))" && echo "✓ App key set"
curl -s http://localhost:8000/ | grep -q "200" && echo "✓ Homepage loads"
```

---

## Files Modified

| File | Changes | Reason |
|------|---------|--------|
| `routes/channels.php` | Commented 4 Broadcast::channel() definitions | Prevent bootstrap errors with undefined Redis |
| `.env.docker` | Added `APP_KEY=base64:/...` | Enable encryption service |
| `nginx.conf` | Changed `$host` → `$http_host` (2 locations) | Preserve port in asset URLs |
| `public/hot` | Deleted | Force production manifest mode |
| `database/` | Ran migrations | Create missing tables including sessions |
| `vendor/` | Removed dev packages | Clean autoloader of Sail references |

---

## Related Documentation

- **D04 Software Design**: Section 6.1 (Production Architecture)
- **D08 System Testing**: Production deployment verification
- **D11 Infrastructure**: Container orchestration and networking
- **nginx.conf**: Proxy and FastCGI configuration

---

## Lessons Learned

1. **Environment Variables Load at Container Creation**: `docker restart` doesn't reload `.env.docker`, must use `docker rm` + `docker compose up`

2. **Bootstrap Errors Hide in Silent Failures**: When app fails to bootstrap, logging doesn't initialize. Use APP_DEBUG=true to see errors.

3. **Network Tab is Essential**: URLs in network requests reveal configuration issues (missing ports, wrong hostnames)

4. **Cascading Failures**: Fixing one error exposes the next. Methodical approach needed.

5. **Vite File Persistence**: The `public/hot` marker file persists and affects production deployments. Should be in `.gitignore` and `.dockerignore`.

6. **Nginx Variable Names Matter**: `$host` vs `$http_host` is a subtle but critical difference in URL generation.

7. **Redis Not Always Required**: Broadcasting channels can be disabled safely if real-time features aren't needed.

8. **Composer Lock Transitive Dependencies**: Dev packages can persist in lock file and cause issues if not explicitly removed during production build.

---

**Status**: ✅ RESOLVED  
**Deployed**: 2025-12-02  
**Verified**: Production CSS loading correctly at `http://localhost:8000/`  
**Next Steps**: Monitor production logs for any Bootstrap or runtime errors
