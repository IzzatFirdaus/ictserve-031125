# phpRedisAdmin Setup Guide

**Version**: Latest (December 2025)  
**Purpose**: Web-based Redis management interface for ICTServe development  
**Redis Version**: 7.0.15 (WSL Ubuntu 24.04 LTS)  
**Last Updated**: December 7, 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Verification](#verification)
6. [Usage Guide](#usage-guide)
7. [Troubleshooting](#troubleshooting)
8. [Security Notes](#security-notes)

---

## Overview

**phpRedisAdmin** is a web-based interface for managing Redis databases. It provides:

- ✅ **Visual Key Browser**: View all keys organized by database
- ✅ **Real-time Monitoring**: Server info, memory usage, connected clients
- ✅ **Key Management**: View, edit, delete keys with different data types
- ✅ **Server Commands**: Execute Redis commands through web UI
- ✅ **Multiple Redis Instances**: Connect to different Redis servers
- ✅ **Data Type Support**: Strings, Lists, Sets, Sorted Sets, Hashes

**Official Repository**: <https://github.com/erikdubbelboer/phpRedisAdmin>

---

## Prerequisites

- **WSL Redis 7.0.15** running and accessible on `127.0.0.1:6379`
- **XAMPP** or **Laragon** with Apache/PHP installed
- **PHP 8.2+** with the following extensions:
  - `php_redis` (phpredis) - **required for phpRedisAdmin tool itself**
  - `php_curl` - for HTTP requests
  - `php_json` - for JSON encoding/decoding
- **Composer** - PHP dependency manager

**Important Note**: phpRedisAdmin (the web management tool) requires the phpredis PHP extension to function. However, your Laravel application should use Predis (`REDIS_CLIENT=predis`) for better cross-platform compatibility. Both can coexist without conflict:

- **Laravel Application**: Uses Predis library (pure PHP, no extensions needed)
- **phpRedisAdmin Tool**: Uses phpredis extension (required for the web interface)

### Verify PHP Extensions

```powershell
# Check phpredis extension
php -m | Select-String redis
# Expected output: redis

# Check other required extensions
php -m | Select-String -Pattern "curl|json"
# Expected output: curl, json

# Verify PHP version
php -v
# Expected: PHP 8.2.x or higher
```

### Verify WSL Redis Running

```powershell
# Test Redis connection
wsl.exe -e redis-cli ping
# Expected output: PONG

# Check port accessibility from Windows
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
# Expected: TcpTestSucceeded = True
```

---

## Installation

### Step 1: Download phpRedisAdmin

**Option A: Clone with Git** (Recommended):

```powershell
# Navigate to XAMPP htdocs
cd C:\XAMPP\htdocs

# Create redis directory
mkdir redis -ErrorAction SilentlyContinue
cd redis

# Clone repository
git clone https://github.com/erikdubbelboer/phpRedisAdmin.git
cd phpRedisAdmin
```

**Option B: Download ZIP**:

1. Visit <https://github.com/erikdubbelboer/phpRedisAdmin>
2. Click "Code" → "Download ZIP"
3. Extract to `C:\XAMPP\htdocs\redis\phpRedisAdmin\`

### Step 2: Install PHP Dependencies via Composer

**CRITICAL**: You must use Composer to install dependencies. Direct git clone doesn't include required libraries.

```powershell
cd C:\XAMPP\htdocs\redis\phpRedisAdmin

# Remove any incomplete vendor directory
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue

# Install dependencies (including Predis and PSR-7 libraries)
composer require predis/predis

# Alternative: Install all dependencies if composer.json exists
composer install --no-dev
```

**Why Composer is Required**:

- Installs **Predis v3.3.0** (PHP Redis client for phpRedisAdmin's fallback communication)
- Installs **psr/http-message** (PSR-7 HTTP message interfaces)
- Installs **paragonie/random_compat** (PHP 7+ compatibility layer)
- Resolves all transitive dependencies automatically

**Note**: phpRedisAdmin primarily uses the phpredis PHP extension for Redis communication, but also includes Predis as a fallback option. The Composer dependencies are required for proper PSR-7 interface support.

**Common Error Without Composer**:

```
Fatal error: Interface 'Psr\Http\Message\StreamInterface' not found in
vendor/guzzle/streams/src/Stream.php
```

This error occurs when dependencies are missing. Solution: Use Composer as shown above.

---

## Configuration

### Step 1: Create Configuration File

```powershell
cd C:\XAMPP\htdocs\redis\phpRedisAdmin\includes

# Copy sample config
Copy-Item config.sample.inc.php config.inc.php
```

### Step 2: Edit Configuration

```powershell
# Open in editor
notepad config.inc.php
```

**Update the following settings**:

```php
<?php
// Configuration for WSL Redis connection

$config = array(
    'servers' => array(
        array(
            'name'   => 'WSL Redis (Local Development)',  // Display name
            'host'   => '127.0.0.1',                      // WSL Redis host
            'port'   => 6379,                             // Default Redis port
            'filter' => '*',                              // Show all keys
            'db'     => 0,                                // Default database
            
            // If Redis has password (from redis.conf requirepass)
            'auth'   => null,  // Set to 'your_password' if auth enabled
            
            // Optional: Connection timeout
            'timeout' => 2.5,
            
            // Optional: Read timeout
            'read_timeout' => 2.5,
        ),
        
        // Add more Redis instances here if needed
        // array(
        //     'name' => 'Production Redis',
        //     'host' => 'production.redis.server',
        //     'port' => 6379,
        //     'auth' => 'production_password',
        // ),
    ),
    
    // Optional: Enable login authentication for phpRedisAdmin itself
    'login' => array(
        // 'admin' => array(
        //     'password' => password_hash('admin_password', PASSWORD_DEFAULT),
        // ),
    ),
);
?>
```

**Key Configuration Options**:

| Option | Description | Example |
|--------|-------------|---------|
| `name` | Display name in UI | `'WSL Redis (Dev)'` |
| `host` | Redis server address | `'127.0.0.1'` for WSL |
| `port` | Redis port | `6379` (default) |
| `auth` | Redis password | `null` or `'your_password'` |
| `filter` | Key pattern filter | `'*'` (all), `'cache:*'` (prefix) |
| `db` | Default database index | `0` to `15` |
| `timeout` | Connection timeout | `2.5` seconds |

### Step 3: Configure Apache Virtual Host (Optional)

**For cleaner URL** (e.g., `http://redis.local` instead of `http://localhost/redis/phpRedisAdmin`):

Create `C:\XAMPP\apache\conf\extra\httpd-vhosts-redis.conf`:

```apache
<VirtualHost *:80>
    ServerName redis.local
    DocumentRoot "C:/XAMPP/htdocs/redis/phpRedisAdmin"
    
    <Directory "C:/XAMPP/htdocs/redis/phpRedisAdmin">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php
    </Directory>
    
    ErrorLog "C:/XAMPP/apache/logs/redis-error.log"
    CustomLog "C:/XAMPP/apache/logs/redis-access.log" common
</VirtualHost>
```

**Edit Windows hosts file** (`C:\Windows\System32\drivers\etc\hosts`):

```
127.0.0.1    redis.local
```

**Restart Apache**:

```powershell
# From XAMPP Control Panel, click "Stop" then "Start" for Apache
# Or via command line:
C:\XAMPP\apache\bin\httpd.exe -k restart
```

---

## Verification

### Step 1: Test Web Access

**Default URL**:

```
http://localhost/redis/phpRedisAdmin/
```

**Or with virtual host**:

```
http://redis.local/
```

### Step 2: Verify Dashboard Loads

Expected dashboard elements:

- ✅ **Server Info Panel**: Shows Redis version (7.0.15), uptime, connected clients
- ✅ **Database Tabs**: db0, db1, db2, etc. (up to db15)
- ✅ **Key Browser**: List of keys in selected database
- ✅ **Server Stats**: Used memory, peak memory, commands processed
- ✅ **Configuration**: Current Redis configuration values

### Step 3: Test Key Operations

**Create a test key**:

```powershell
# From PowerShell
wsl.exe -e redis-cli SET test:phpredisadmin "Hello from WSL Redis"
wsl.exe -e redis-cli EXPIRE test:phpredisadmin 3600  # 1 hour TTL
```

**Verify in phpRedisAdmin**:

1. Open <http://localhost/redis/phpRedisAdmin/>
2. Navigate to "db0" tab
3. Search for `test:phpredisadmin`
4. Click key to view value: should show "Hello from WSL Redis"
5. Check TTL: should show time remaining (~3600 seconds)

### Step 4: Verify Laravel Cache Keys

**After running Laravel app**:

```powershell
# Create Laravel cache entry
php artisan tinker
>>> cache()->put('laravel_test', 'ICTServe Cache Working', 600);
>>> exit
```

**Check in phpRedisAdmin**:

1. Refresh dashboard
2. Look for key: `laravel_database_laravel_test` (Laravel cache prefix)
3. Value should show: "ICTServe Cache Working"

---

## Usage Guide

### Dashboard Overview

**Top Navigation**:

- **Server Dropdown**: Switch between configured Redis instances
- **Database Tabs**: Click to view different Redis databases (db0-db15)
- **Search Bar**: Filter keys by pattern (e.g., `cache:*`, `session:*`)
- **Info Button**: View detailed server information

**Key Browser**:

- **Key List**: All keys in current database with type indicators
- **Type Icons**:
  - 📝 String
  - 📋 List
  - 🔗 Set
  - 🎯 Sorted Set
  - 🗂️ Hash
- **Actions**: View, Edit, Delete, Rename keys

### Common Operations

#### View Key Details

1. Click any key in the key browser
2. View:
   - **Type**: String, List, Hash, etc.
   - **Value**: Full content (formatted)
   - **TTL**: Time to live (if set)
   - **Encoding**: Internal Redis encoding
   - **Memory**: Memory used by key

#### Edit Key Value

1. Click key to open detail view
2. Click "Edit" button
3. Modify value (JSON-aware editor)
4. Click "Save"

#### Delete Keys

**Single key**:

1. Click key in browser
2. Click "Delete" button
3. Confirm deletion

**Bulk delete by pattern**:

1. Use search: `cache:old:*`
2. Select multiple keys (if feature available)
3. Click "Delete Selected"

#### Set TTL (Expiration)

1. Open key detail view
2. Find "TTL" section
3. Set expiration time (seconds)
4. Click "Update TTL"

### Server Information

**View detailed server stats**:

1. Click "Info" button in top navigation
2. Sections:
   - **Server**: Version, OS, process ID
   - **Clients**: Connected clients, blocked clients
   - **Memory**: Used memory, peak memory, fragmentation
   - **Stats**: Commands processed, keyspace hits/misses
   - **Replication**: Master/slave status
   - **CPU**: System/user CPU usage
   - **Keyspace**: Keys per database with TTL stats

### Execute Redis Commands

**CLI panel** (if enabled):

1. Navigate to "Console" or "CLI" tab
2. Enter Redis command: `INFO memory`
3. Click "Execute"
4. View results

**Common commands to try**:

```redis
INFO server
DBSIZE
KEYS cache:*
GET laravel_database_cache_key
TTL session:user:123
FLUSHDB  # ⚠️ Careful: Deletes all keys in current DB
```

---

## Troubleshooting

### Issue: "Interface Psr\Http\Message\StreamInterface not found"

**Cause**: Missing Composer dependencies (PSR-7 libraries).

**Solution**:

```powershell
cd C:\XAMPP\htdocs\redis\phpRedisAdmin
Remove-Item -Recurse -Force vendor -ErrorAction SilentlyContinue
composer require predis/predis
```

**Why it happens**: Cloning via `git clone` doesn't install dependencies. Always use Composer.

### Issue: "Cannot connect to Redis server"

**Symptoms**: Dashboard shows "Connection failed" or timeout error.

**Solutions**:

1. **Verify WSL Redis is running**:

   ```powershell
   wsl.exe -e redis-cli ping
   # Expected: PONG
   ```

2. **Check Redis binding** (must allow Windows connections):

   ```bash
   # In WSL
   sudo grep "^bind" /etc/redis/redis.conf
   # Expected: bind 0.0.0.0 ::1
   ```

3. **Restart Redis service**:

   ```powershell
   wsl.exe -e sudo systemctl restart redis-server
   ```

4. **Verify port accessibility**:

   ```powershell
   Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
   # Expected: TcpTestSucceeded = True
   ```

5. **Check phpredis extension** (required for phpRedisAdmin):

   ```powershell
   php -m | Select-String redis
   # Expected: redis
   ```

   **If phpredis is missing**:
   - For XAMPP: Download `php_redis.dll` from [PECL](https://pecl.php.net/package/redis)
   - Copy to `C:\xampp\php\ext\`
   - Add `extension=redis` to `php.ini`
   - Restart Apache

**Note**: Remember that phpRedisAdmin requires the phpredis extension, while your Laravel application can use Predis (`REDIS_CLIENT=predis` in `.env`).

### Issue: Blank page or PHP errors

**Cause**: PHP configuration issues or missing extensions.

**Solutions**:

1. **Check PHP error log**:

   ```powershell
   Get-Content C:\XAMPP\apache\logs\error.log -Tail 20
   ```

2. **Enable PHP error display** (development only):

   ```php
   # Add to top of index.php temporarily
   error_reporting(E_ALL);
   ini_set('display_errors', 1);
   ```

3. **Verify PHP extensions**:

   ```powershell
   php -m | Select-String -Pattern "redis|curl|json|session"
   ```

### Issue: "Authentication required" error

**Cause**: Redis configured with `requirepass` but phpRedisAdmin config has `auth => null`.

**Solution**:

1. **Check Redis password requirement**:

   ```bash
   # In WSL
   sudo grep "^requirepass" /etc/redis/redis.conf
   ```

2. **Update phpRedisAdmin config**:

   ```php
   // In includes/config.inc.php
   'auth' => 'your_redis_password',
   ```

### Issue: Keys not showing in dashboard

**Causes**: Wrong database selected, or key filter too restrictive.

**Solutions**:

1. **Check correct database**: Click through db0-db15 tabs
2. **Reset filter**: Change filter from specific pattern to `*`
3. **Verify keys exist**:

   ```powershell
   wsl.exe -e redis-cli DBSIZE
   wsl.exe -e redis-cli KEYS "*"
   ```

### Issue: Slow performance with many keys

**Cause**: Redis `KEYS *` command blocks server with large datasets.

**Solutions**:

1. **Use key patterns**: Filter by prefix (e.g., `cache:*` instead of `*`)
2. **Limit key display**: Modify config to show first N keys only
3. **Use Redis SCAN**: Some phpRedisAdmin versions support `SCAN` instead of `KEYS`

---

## Security Notes

### Development vs Production

**Development (Current Setup)**:

- ✅ Redis accessible only on localhost (127.0.0.1)
- ✅ phpRedisAdmin accessible only on local network
- ✅ No authentication required (convenient for local dev)

**Production (NOT RECOMMENDED)**:

- ❌ **Do NOT expose phpRedisAdmin to the internet**
- ❌ **Do NOT use in production without authentication**
- ❌ **Do NOT connect to production Redis from public phpRedisAdmin**

### Best Practices

1. **Use Authentication**:

   ```php
   // In includes/config.inc.php
   'login' => array(
       'admin' => array(
           'password' => password_hash('strong_password_here', PASSWORD_DEFAULT),
       ),
   ),
   ```

2. **Restrict Access** (Apache config):

   ```apache
   <Directory "C:/XAMPP/htdocs/redis/phpRedisAdmin">
       # Only allow local network
       Require ip 127.0.0.1 192.168.1.0/24
   </Directory>
   ```

3. **Use Redis Password**:

   ```bash
   # In WSL /etc/redis/redis.conf
   requirepass your_secure_redis_password
   ```

4. **Enable HTTPS** (for sensitive data):
   - Configure XAMPP SSL certificate
   - Access via `https://localhost/redis/phpRedisAdmin/`

5. **Regular Updates**:

   ```powershell
   cd C:\XAMPP\htdocs\redis\phpRedisAdmin
   git pull origin master
   composer update
   ```

### Data Privacy

**Be aware**:

- phpRedisAdmin displays **all Redis data** including:
  - Laravel sessions (may contain user IDs, CSRF tokens)
  - Cache entries (may contain sensitive data)
  - Queue jobs (may contain PII)

**For production Redis management**, consider:

- **Redis Commander** (Node.js alternative)
- **RedisInsight** (Official Redis GUI by Redis Ltd)
- **CLI only** (`redis-cli` with restricted access)

---

## Useful Links

- **Official Repository**: <https://github.com/erikdubbelboer/phpRedisAdmin>
- **Redis Documentation**: <https://redis.io/documentation>
- **Predis Documentation**: <https://github.com/predis/predis>
- **Laravel Redis Docs**: <https://laravel.com/docs/12.x/redis>
- **ICTServe Redis Setup**: [redis-setup.md](redis-setup.md)
- **ICTServe WSL Setup**: [WSL_SETUP.md](WSL_SETUP.md)

---

## Alternative Redis GUIs

If phpRedisAdmin doesn't meet your needs:

### RedisInsight (Official, Feature-Rich)

- **Download**: <https://redis.com/redis-enterprise/redis-insight/>
- **Platform**: Windows, macOS, Linux
- **Features**: Visual query builder, profiler, CLI, clustering support
- **License**: Free

### Redis Commander (Node.js)

- **Install**: `npm install -g redis-commander`
- **Run**: `redis-commander --redis-host 127.0.0.1`
- **Access**: <http://localhost:8081>
- **License**: MIT

### Medis (macOS only)

- **Download**: <https://getmedis.com/>
- **Platform**: macOS
- **Features**: Native app, fast, beautiful UI
- **License**: Free / Pro

---

**Last Updated**: December 7, 2025  
**Maintained By**: ICTServe Development Team  
**Status**: ✅ Tested and Working with WSL Redis 7.0.15
