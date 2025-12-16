# Fix Vendor Directory Permissions Issue

## Problem Summary

The `/var/www/html/vendor` directory is owned by `root`, preventing `www-data` user from running `composer install`. This causes:

1. ❌ `composer install` fails with "could not be created" error
2. ❌ Laravel fails to boot (Class "Illuminate\Foundation\Application" not found)
3. ❌ Agents show "non authenticated" errors (because Laravel can't boot)

## Root Cause

The `vendor` directory was created by a process running as `root`, creating a permission mismatch with the `www-data` user running inside the container.

## Solutions

### ✅ Solution 1: Rebuild Dev Container (RECOMMENDED for Codespaces)

**From VS Code Command Palette (Ctrl+Shift+P / Cmd+Shift+P):**

```
Dev Containers: Rebuild Container
```

This will recreate the container with proper permissions.

---

### ✅ Solution 2: Fix from Host Machine (If using local Docker)

**Run these commands from your HOST machine** (outside the container):

```bash
# Option A: Using Makefile
make composer

# Option B: Using docker compose directly
docker compose -f compose.yaml exec -u root app chown -R www-data:www-data /var/www/html/vendor
docker compose -f compose.yaml exec app composer install --no-interaction

# Option C: Nuclear option - recreate volume
docker compose -f compose.yaml down -v
docker compose -f compose.yaml up -d
docker compose -f compose.yaml exec app composer install --no-interaction
```

---

### ✅ Solution 3: Use Pre-installed Vendor (Temporary)

We successfully installed packages to `/tmp/vendor-new`. You can use this temporarily:

```bash
# Inside the container
export COMPOSER_VENDOR_DIR=/tmp/vendor-new

# Update composer.json to use this path temporarily
composer config vendor-dir /tmp/vendor-new

# Or set autoload path in bootstrap/app.php
```

**⚠️ Note**: This is a workaround. The proper solution is to fix the Docker volume ownership.

---

### ✅ Solution 4: Modify .devcontainer Configuration

If using Dev Containers, update `.devcontainer/devcontainer.json`:

```json
{
  "postCreateCommand": "composer install --no-interaction && npm ci",
  "remoteUser": "www-data",
  "containerUser": "www-data"
}
```

Then rebuild the container.

---

## Verification

After applying any solution, verify it works:

```bash
# Should show www-data as owner
ls -la /var/www/html/vendor

# Should complete successfully
composer install --no-interaction

# Should work without errors
php artisan --version

# Should show routes
php artisan route:list
```

---

## Authentication Issue Resolution

Once vendor dependencies are installed correctly:

1. **Laravel will boot properly** ✅
2. **Artisan commands will work** ✅
3. **Agents can authenticate** ✅

The "non authenticated" error is a symptom of Laravel not being able to boot due to missing vendor dependencies.

---

## Prevention

To prevent this issue in the future:

1. Always run composer commands as the correct user (`www-data` in this project)
2. Use the Makefile commands which handle this correctly
3. Never run `composer install` as root inside the container
4. Ensure `.devcontainer` configuration specifies the correct user

---

## Need Help?

If none of these solutions work, provide:
- Output of: `ls -la /var/www/html/vendor`
- Output of: `whoami`
- Output of: `docker compose ps` (from host)
- Your environment (Codespaces, local Docker, WSL, etc.)
