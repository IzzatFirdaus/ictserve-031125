# Apache Alias Configuration Test Results

**Test Date:** 2025-12-04  
**Test Method:** Playwright MCP Server  
**Configuration:** Apache Alias via Laragon

## Configuration Overview

### Apache Alias Setup

The project includes an Apache alias configuration file: `apache-alias-ictserve.conf`

```apache
Alias /ictserve "C:/laragon/www/ictserve-031125/public"

<Directory "C:/laragon/www/ictserve-031125/public">
    Options Indexes FollowSymLinks MultiViews
    AllowOverride All
    Require all granted
</Directory>
```

This configuration allows accessing the application at: `http://localhost/ictserve/`

## Test Results

### ✅ Homepage Accessible
- **URL:** http://localhost/ictserve/
- **Status:** PASS
- **Title:** ICTServe
- **Content:** Homepage loads correctly with all service cards

### ❌ Routes Return 404
- **URL:** http://localhost/ictserve/helpdesk/create
- **Status:** FAIL - 404 Not Found
- **Issue:** Laravel routing not working with subdirectory path

## Root Cause Analysis

The issue occurs because:

1. **Apache Alias is configured** to serve from `/ictserve/`
2. **APP_URL is set** to `http://127.0.0.1:8000` (for `php artisan serve`)
3. **Mismatch causes** Laravel to generate incorrect URLs and fail route matching

When accessing via Apache alias at `http://localhost/ictserve/`:
- Static assets load from the correct path
- Laravel routes fail because the application doesn't know it's in a subdirectory
- The `.htaccess` rewrite rules don't account for the subdirectory prefix

## Solutions

### Option 1: Use php artisan serve (RECOMMENDED)

This is the simplest and most reliable approach:

```bash
php artisan serve
# Access at: http://127.0.0.1:8000
```

**Advantages:**
- No web server configuration needed
- Works immediately
- No subdirectory path issues
- Consistent across all environments

### Option 2: Configure Apache Virtual Host

Create a proper virtual host instead of using an alias:

1. **Create virtual host file** in Laragon:
   - Location: `C:\laragon\etc\apache2\sites-enabled\ictserve.test.conf`

```apache
<VirtualHost *:80>
    ServerName ictserve.test
    DocumentRoot "C:/laragon/www/ictserve-031125/public"
    
    <Directory "C:/laragon/www/ictserve-031125/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Require all granted
    </Directory>
</VirtualHost>
```

2. **Add to hosts file**:
   - Location: `C:\Windows\System32\drivers\etc\hosts`
   - Add: `127.0.0.1 ictserve.test`

3. **Update .env**:
```env
APP_URL=http://ictserve.test
```

4. **Restart Apache** in Laragon

5. **Access at:** `http://ictserve.test`

### Option 3: Fix Apache Alias Configuration (NOT RECOMMENDED)

To make the alias work, you would need to:

1. **Update .env**:
```env
APP_URL=http://localhost/ictserve
```

2. **Update bootstrap/app.php** to handle subdirectory:
```php
// This is complex and error-prone
```

3. **Clear caches**:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

**Why NOT recommended:**
- Complex configuration
- Potential routing issues
- Asset path problems
- Harder to debug
- Not portable across environments

## Current Configuration Status

### .env Settings
```env
APP_URL=http://127.0.0.1:8000
DB_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
REVERB_HOST=127.0.0.1
```

These settings are optimized for `php artisan serve`, not Apache alias.

## Recommendations

### For Development (Local Machine)

**Use `php artisan serve`:**

```bash
# Start all services
composer run dev

# Or individually:
php artisan serve              # http://127.0.0.1:8000
php artisan reverb:start       # WebSocket server
php artisan queue:work         # Queue worker
npm run dev                    # Vite dev server
```

**Advantages:**
- Zero configuration
- Works immediately
- No subdirectory issues
- Consistent with documentation

### For Production/Staging

**Use proper virtual host:**
- Configure Apache/Nginx virtual host
- Point DocumentRoot to `public/` directory
- Set APP_URL to match domain
- No subdirectory paths

### For Team Collaboration

**Document the approach:**
- Update README.md with clear setup instructions
- Provide both `php artisan serve` and virtual host options
- Include troubleshooting section
- Add to onboarding documentation

## Setup Script

The project includes a setup script: `setup-apache-alias.ps1`

This script should be updated to:
1. Create a virtual host instead of an alias
2. Update the hosts file
3. Configure .env correctly
4. Restart Apache

## Conclusion

**Current Status:**
- ✅ Apache alias is configured
- ✅ Homepage loads
- ❌ Routes return 404 due to APP_URL mismatch

**Recommended Action:**
Use `php artisan serve` for development. It's simpler, more reliable, and matches the current `.env` configuration.

**Alternative:**
Set up a proper Apache virtual host (e.g., `ictserve.test`) if you prefer using Apache/Laragon's web server.

## Related Files

- `apache-alias-ictserve.conf` - Apache alias configuration
- `setup-apache-alias.ps1` - Setup script (needs updating)
- `LARAGON_SETUP.md` - Comprehensive setup guide
- `.env` - Environment configuration
- `public/.htaccess` - Laravel rewrite rules
