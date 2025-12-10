# Laragon 6 Setup Guide for ICTServe

**Version**: 3.5.0  
**Framework**: Laravel 12 + Livewire 3 + Filament 4  
**Database**: MySQL 8.0+  
**Cache/Session**: Redis 7.0 (WSL Ubuntu)  
**Last Updated**: December 7, 2025

---

## Table of Contents

1. [System Requirements](#system-requirements)
2. [Installation](#installation)
3. [Initial Configuration](#initial-configuration)
4. [Common Tasks](#common-tasks)
5. [Troubleshooting](#troubleshooting)
6. [Package Management](#package-management)

---

## System Requirements

- **Windows**: 10 / 11 (64-bit)
- **Laragon 6**: Full Stack (PHP, MySQL, Apache/Nginx, Node.js)
- **WSL 2**: Ubuntu 24.04 LTS (for Redis; already installed on this system)
- **Docker Desktop**: Optional (not required if using WSL Redis)
- **Git**: For version control
- **PHP**: 8.2.12 (included in Laragon)
- **MySQL**: 8.0.40 / 8.4.2 (configurable; default 8.0)
- **Node.js**: Latest LTS (bundled in Laragon)
- **Redis**: 7.0.15 (installed in WSL Ubuntu)

---

## Installation

### 1. Download & Install Laragon

**Official**: <https://laragon.org/download/>  
**Archive URL** (v6.0+): <https://releases.laragon.org/laragon-wamp.exe>

1. Run the installer
2. Choose default installation path: `C:\laragon`
3. During setup, select:
   - ✅ Apache or Nginx (Apache recommended for compatibility)
   - ✅ MySQL 8.0 (or 8.4 if preferred)
   - ✅ PHP 8.2+
   - ✅ Node.js + npm
   - ✅ Visual Studio Code integration (optional)

4. Complete installation and **Start All** services from Laragon UI

### 2. Verify Laragon Services

Once Laragon is running, verify all services are online:

```powershell
# From Windows PowerShell
Test-NetConnection -ComputerName 127.0.0.1 -Port 80    # Apache
Test-NetConnection -ComputerName 127.0.0.1 -Port 3306  # MySQL
```

### 3. ⚠️ CRITICAL: Disable Laragon's Bundled Redis

**This step is essential to avoid port conflicts with WSL Redis.**

In the **Laragon 6 UI**:

1. Look at the services list (Apache, MySQL, Node, Redis, etc.)
2. Find **Redis** in the list
3. Click the **toggle/stop button** next to Redis to **DISABLE it**
4. Verify status changes to "Stopped" or "Offline"

**Why?**

- ICTServe uses **WSL Ubuntu Redis 7.0.15** instead of Laragon's bundled version
- Both try to use port 6379; disabling Laragon's prevents conflicts
- Avoids "Connection refused" errors and startup failures
- Reduces system resource consumption

**After this step**, Laravel automatically connects to WSL Redis via `.env` configuration.

### 3. ⚠️ CRITICAL: Disable Laragon's Bundled Redis

**This step is essential to avoid port conflicts with WSL Redis.**

In the **Laragon 6 UI**:

1. Look at the services list (Apache, MySQL, Node, Redis, etc.)
2. Find **Redis** in the list
3. Click the **toggle/stop button** next to Redis to **DISABLE it**
4. Verify status changes to "Stopped" or "Offline"

**Why?**

- ICTServe uses **WSL Ubuntu Redis 7.0.15** instead of Laragon's bundled version
- Both try to use port 6379; disabling Laragon's prevents conflicts
- Avoids "Connection refused" errors and startup failures
- Reduces system resource consumption

**After this step**, Laravel automatically connects to WSL Redis via `.env` configuration.

### 4. Clone/Download ICTServe

```bash
cd C:\laragon\www
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125
```

### 4. Install Dependencies

```bash
# PHP dependencies
composer install --no-interaction --prefer-dist

# JavaScript dependencies
npm ci  # (or npm install)

# Generate app key
php artisan key:generate
```

---

## Initial Configuration

### Environment Setup

The `.env` file is **already configured** for Laragon local development with **WSL Redis**:

```bash
# Key values in .env
APP_ENV=local
APP_DEBUG=true
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=          # (empty - default Laragon root)

# WSL Ubuntu Redis 7.0.15 (NOT Laragon's bundled Redis)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null   # WSL Redis uses no password by default

SESSION_DRIVER=redis  # ✅ Uses WSL Redis (Laragon's Redis must be DISABLED)
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

**⚠️ REMINDER**: Before running the app, **Laragon's bundled Redis MUST be disabled** in the Laragon UI (see [Disable Laragon's Bundled Redis](#-critical-disable-laragons-bundled-redis) section above).

### Database Setup

1. **Create database** (if not exists):

   ```bash
   php artisan migrate --seed
   ```

2. **Verify MySQL connection**:

   ```bash
   php artisan db:show
   ```

3. **Access via phpMyAdmin**:
   - URL: <http://127.0.0.1/phpmyadmin>
   - Username: `root`
   - Password: (empty)

---

## Common Tasks

### Start Development Stack

**Option 1: Full Dev Stack (Laravel + Vite + Queue + Reverb)**

```bash
composer run dev
```

**Option 2: Individual Services**

```bash
# Terminal 1: Laravel Server
php artisan serve --port=8000

# Terminal 2: Vite (Frontend build watcher)
# Option A (Windows PowerShell helper, recommended)
npm run dev:win
# Option B (manual activation):
cd C:\laragon\www\ictserve-031125
. .\.env.ps1
npm run dev

# Terminal 3: Queue Worker (background jobs)
php artisan queue:work

# Terminal 4: Reverb (WebSocket server)
php artisan reverb:start
```

### Database Migrations

```bash
php artisan migrate                 # Run pending migrations
php artisan migrate:rollback        # Undo last batch
php artisan migrate:reset           # Undo all
php artisan migrate:fresh --seed    # Fresh start with seeders
```

### Code Quality Checks

```bash
# Format code (PSR-12 compliance)
vendor/bin/pint

# Static analysis (Level 9)
vendor/bin/phpstan analyse

# All checks together
vendor/bin/phpstan analyse && vendor/bin/pint --dirty && php artisan test
```

### Clear Caches

```bash
php artisan optimize:clear    # Clear all bootstrap caches
php artisan cache:clear       # Clear application cache
php artisan config:cache      # Cache config (don't do in dev)
php artisan route:cache       # Cache routes (don't do in dev)
```

### Rebuild Frontend Assets

```bash
npm run build    # Production build
npm run dev      # Development watch
npm run lint:js  # Lint JavaScript
```

---

## Troubleshooting

### Issue: "No application encryption key has been specified"

**Cause**: Stale service container cache.

**Solution**:

```bash
php artisan optimize:clear
php artisan key:generate
php artisan serve
```

### Issue: Redis connection refused (port 6379)

**Cause**: WSL Redis is not running or not accessible.

**Solution**:

```bash
# Verify Redis in WSL
wsl.exe -e redis-cli ping
# Expected output: PONG

# If not running, restart WSL
wsl.exe --shutdown
wsl.exe  # Re-open WSL terminal

# Verify port accessibility from Windows
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
```

### Issue: MySQL connection refused (port 3306)

**Cause**: MySQL service not started in Laragon.

**Solution**:

1. Open Laragon UI
2. Click **Start All** button
3. Verify MySQL service shows "Running" (green indicator)
4. Retry: `php artisan db:show`

### Issue: Vite manifest error ("Unable to locate file in Vite manifest")

**Cause**: Frontend assets not built.

**Solution**:

```bash
npm run build    # Or npm run dev for watch mode
php artisan serve
```

### Issue: "CORS error" from API routes

**Cause**: CORS middleware configuration.

**Solution**: Check `config/cors.php` and `bootstrap/app.php` middleware configuration. Ensure `Accept: application/json` header is sent in API requests.

### Issue: Keyboard input not working in WSL terminal

**Cause**: Terminal compatibility issue.

**Solution**:

1. Use Windows Terminal instead of PowerShell
2. Right-click WSL window → Properties → Enable Ctrl+Shift+C/V for copy/paste
3. Or run with explicit root: `wsl.exe --user root`

---

## Package Management

### Update Components via Laragon Package Manager

Laragon UI provides a package manager for downloading/installing additional software.

**Location**: `C:\laragon\usr\packages.conf`

**Current Packages** (configured):

| Package | Version | Download |
|---------|---------|----------|
| **MySQL** | 8.4.2 | <https://dev.mysql.com/get/Downloads/MySQL-8.4/mysql-8.4.2-winx64.zip> |
| **MySQL** | 8.0.40 | <https://dev.mysql.com/get/Downloads/MySQL-8.0/mysql-8.0.40-winx64.zip> |
| **phpMyAdmin** | 5.2.1 | <https://files.phpmyadmin.net/phpMyAdmin/5.2.1/phpMyAdmin-5.2.1-english.zip> |
| **DBeaver** | Latest | <https://dbeaver.io/files/dbeaver-ce-latest-win32.win32.x86_64.zip> |
| **MongoDB** | 7.0.12 | <https://fastdl.mongodb.org/windows/mongodb-windows-x86_64-7.0.12.zip> |
| **PostgreSQL** | 16.4 | <https://get.enterprisedb.com/postgresql/postgresql-16.4-1-windows-x64-binaries.zip> |
| **Node.js** | Latest | (bundled in Laragon) |
| **VS Code** | Stable | <https://code.visualstudio.com/sha/download?build=stable&os=win32-x64-archive> |
| **Go** | 1.23.3 | <https://go.dev/dl/go1.23.3.windows-amd64.zip> |
| **PocketBase** | 0.22.8 | <https://github.com/pocketbase/pocketbase/releases/download/v0.22.8/pocketbase_0.22.8_windows_amd64.zip> |

### Manual Installation (MySQL Example)

```bash
# Download latest MySQL 8.0 binary from Laragon Package Manager
# Or via direct link: https://dev.mysql.com/get/Downloads/MySQL-8.0/mysql-8.0.40-winx64.zip

# Extract to: C:\laragon\data\mysql

# Restart Laragon services
# (Use Laragon UI: Stop All → Start All)
```

---

## Environment-Specific Configurations

### Local Development (.env)

```env
APP_ENV=local
APP_DEBUG=true
DB_HOST=127.0.0.1
SESSION_DRIVER=redis
CACHE_STORE=redis
QUEUE_CONNECTION=redis
```

### Docker Development (.env.docker)

```env
APP_ENV=docker
DB_HOST=db
SESSION_DRIVER=redis
REDIS_HOST=redis
```

To switch: `cp .env.docker .env` then `docker-compose up -d`

---

## Useful Commands Summary

```bash
# Development
php artisan serve                    # Start Laravel server (port 8000)
npm run dev                          # Watch mode for assets
php artisan queue:work               # Process queued jobs
php artisan reverb:start             # WebSocket server

# Database
php artisan migrate                  # Run migrations
php artisan migrate:fresh --seed     # Reset with seeders
php artisan db:show                  # Show DB connection info

# Code Quality
vendor/bin/pint                      # Format code
vendor/bin/phpstan analyse           # Static analysis
php artisan test                     # Run tests

# Cache Management
php artisan optimize:clear           # Clear all caches
php artisan config:cache             # Cache configuration
php artisan route:cache              # Cache routes

# Frontend
npm install                          # Install npm packages
npm run build                        # Production build
npm run lint:js                      # Lint JavaScript
```

---

## Resources

- **Laragon Official**: <https://laragon.org>
- **Laravel 12 Docs**: <https://laravel.com/docs/12.x>
- **Livewire 3 Docs**: <https://livewire.laravel.com>
- **Filament 4 Docs**: <https://filamentphp.com>
- **Redis Docs**: <https://redis.io/docs>

---

**Last Updated**: December 7, 2025  
**Maintained By**: ICTServe Development Team
