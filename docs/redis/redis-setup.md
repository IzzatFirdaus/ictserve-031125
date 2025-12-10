# Redis Setup for ICTServe

**Version**: 3.5.0 (WSL Native + Reverb)  
**Framework**: Laravel 12.40.2 + Laravel Reverb 1.6.2  
**Database**: Redis 7.0.15 (WSL Ubuntu 24.04 LTS)  
**Last Updated**: December 7, 2025

This document explains how Redis is configured and can be run for ICTServe in development environments:

- **WSL Native Redis** (current setup - recommended for Windows Laragon)
- Docker Compose (alternative for consistent multi-container setup)
- Local Windows/Laragon Redis installation

---

## Overview

ICTServe uses Redis for the following features:

- **Laravel Cache Store**: `CACHE_STORE=redis`
- **Laravel Session Store**: `SESSION_DRIVER=redis` (optional)
- **Laravel Reverb**: Real-time WebSocket server for broadcasting (`BROADCAST_CONNECTION=reverb`)

By default the repo is configured to use:

- **Current Setup (Laragon)**: `REDIS_HOST=127.0.0.1` via `.env` (WSL Ubuntu Redis 7.0.15)
- **Docker Compose**: `REDIS_HOST=redis` inside `app` container via `.env.docker`
- **Docker Standalone**: `REDIS_HOST=127.0.0.1` via `.env` (Docker Redis container on host port 6379)

Redis is required when the app uses `QUEUE_CONNECTION=redis`, `SESSION_DRIVER=redis`, or `CACHE_STORE=redis`. **Note**: Traditional queue workers are replaced by **Laravel Reverb** for real-time functionality.

---

## WSL Native Redis Setup (Current - Recommended for Laragon/XAMPP)

**This is the current setup for ICTServe on Windows with Laragon or XAMPP.**

### Why WSL Redis?

- ✅ **Lightweight**: Native Ubuntu package, minimal overhead
- ✅ **No Docker**: Avoids container complexity for simple caching/session storage
- ✅ **Windows-Accessible**: Exposed to Windows on `127.0.0.1:6379`
- ✅ **Works with Multiple Servers**: Same Redis instance accessible from Laragon, XAMPP, Docker, or any Windows application
- ✅ **WSL Already Running**: Laragon/XAMPP developers likely have WSL for other tools
- ✅ **Persistent**: Data survives WSL restarts with AOF persistence
- ✅ **Port-Safe**: Doesn't conflict with Laragon's bundled Redis if disabled

### Prerequisites

- WSL 2 with Ubuntu 24.04 LTS installed
- WSL systemd enabled (required for service autostart)
- **If using Laragon**: Laragon's bundled Redis **DISABLED** in Laragon UI (critical to avoid port 6379 conflicts)
- **If using XAMPP**: Ensure no bundled Redis is running on port 6379
- **phpredis extension** installed in PHP (Laragon includes this; XAMPP may require manual installation)

See [docs/laragon/SETUP.md](../laragon/SETUP.md) for complete setup instructions.

### Installation

**Quick automated install** (runs as root, no password prompt):

```powershell
wsl.exe --user root -e bash -c "apt update && apt upgrade -y && apt install redis-server -y"
```

**Or manual step-by-step**:

```bash
# In WSL Ubuntu terminal
sudo apt update
sudo apt upgrade -y
sudo apt install -y redis-server

# Verify installation
redis-cli --version
# Output: redis-cli 7.0.15 (or similar)
```

### Configuration

**Bind Address** (allow Windows to connect):

```bash
sudo nano /etc/redis/redis.conf
```

Find and update:

```ini
bind 0.0.0.0 ::1
protected-mode yes
```

**Optional: Enable Persistence** (AOF):

```ini
appendonly yes
appendfsync everysec
```

**Save**: Ctrl+O, Enter, Ctrl+X

### Enable Auto-Start

```bash
sudo systemctl enable redis-server
sudo systemctl start redis-server
sudo systemctl status redis-server
# Should show: active (running)
```

```bash
To run a command as administrator (user "root"), use "sudo <command>".
See "man sudo_root" for details.

extremerazr@LENOVOLEGION:~$ wsl.exe -e redis-cli ping
PONG
extremerazr@LENOVOLEGION:~$ wsl.exe --user root systemctl enable redis-server
Synchronizing state of redis-server.service with SysV service script with /usr/lib/systemd/systemd-sysv-install.
Executing: /usr/lib/systemd/systemd-sysv-install enable redis-server
extremerazr@LENOVOLEGION:~$ wsl.exe --user root systemctl start redis-server
extremerazr@LENOVOLEGION:~$ wsl.exe --user root systemctl status redis-server
● redis-server.service - Advanced key-value store
     Loaded: loaded (/usr/lib/systemd/system/redis-server.service; enabled; preset: enabled)
     Active: active (running) since Tue 2025-12-09 14:53:02 +08; 4min 38s ago
       Docs: http://redis.io/documentation,
             man:redis-server(1)
   Main PID: 169 (redis-server)
     Status: "Ready to accept connections"
      Tasks: 5 (limit: 9442)
     Memory: 9.0M (peak: 9.7M)
        CPU: 464ms
     CGroup: /system.slice/redis-server.service
             └─169 "/usr/bin/redis-server 127.0.0.1:6379"

Dec 09 14:53:02 LENOVOLEGION systemd[1]: Starting redis-server.service - Advanced key-value store...
Dec 09 14:53:02 LENOVOLEGION systemd[1]: Started redis-server.service - Advanced key-value store.
```

### Verify Connection

**From WSL**:

```bash
redis-cli ping
# Output: PONG
```

**From Windows PowerShell**:

```powershell
wsl.exe -e redis-cli ping
# Output: PONG

Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
# Expected: TcpTestSucceeded = True
```

### Integration with Laravel

Ensure `.env` has:

```dotenv
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

**Verify phpredis extension is installed**:

```powershell
# For Laragon or XAMPP
php -m | Select-String redis
# Should output: redis

# Check detailed info
php -i | Select-String "redis"
```

**If phpredis is missing (XAMPP users)**:

1. Download `php_redis.dll` for your PHP version from [PECL](https://pecl.php.net/package/redis)
2. Copy to `C:\xampp\php\ext\` (or XAMPP's PHP extensions folder)
3. Edit `php.ini` and add: `extension=redis`
4. Restart Apache/XAMPP
5. Verify: `php -m | Select-String redis`

**Test connection**:

```powershell
php artisan cache:clear
php artisan config:show app.key
```

### phpRedisAdmin Web UI

**phpRedisAdmin** provides a visual interface for managing Redis databases.

**Access**: <http://localhost/redis/phpRedisAdmin/>

**Features**:

- Visual key browser with type indicators (String, List, Hash, Set, etc.)
- Real-time server monitoring (memory, connections, commands/sec)
- Key management (view, edit, delete, set TTL)
- Execute Redis commands through web UI
- Multiple database support (db0-db15)

**Installation Requirements**:

- PHP 8.2+ with phpredis extension
- Composer for dependency management
- XAMPP or Laragon with Apache

**Quick Setup**:

```powershell
# 1. Clone repository
cd C:\XAMPP\htdocs\redis
git clone https://github.com/erikdubbelboer/phpRedisAdmin.git
cd phpRedisAdmin

# 2. Install dependencies (CRITICAL - must use Composer)
composer require predis/predis

# 3. Configure
cd includes
Copy-Item config.sample.inc.php config.inc.php
notepad config.inc.php  # Set host to 127.0.0.1, port to 6379

# 4. Access
Start-Process "http://localhost/redis/phpRedisAdmin/"
```

**⚠️ Common Issue**: "Interface Psr\Http\Message\StreamInterface not found"

- **Cause**: Cloning without Composer doesn't install dependencies
- **Fix**: Run `composer require predis/predis` to install Predis v3.3+ with PSR-7 libraries

**📖 Complete Guide**: See [PHPREDISADMIN_SETUP.md](PHPREDISADMIN_SETUP.md) for:

- Detailed installation steps
- Configuration options
- Usage guide with screenshots
- Troubleshooting common issues
- Security best practices
- Alternative Redis GUI tools

**Official Repository**: <https://github.com/erikdubbelboer/phpRedisAdmin>

### Troubleshooting WSL Redis

**Issue**: "Connection refused on 127.0.0.1:6379"

**Solutions**:

1. Disable Laragon's bundled Redis in Laragon UI (stop Redis service)
2. Restart WSL: `wsl.exe --shutdown` then `wsl.exe`
3. Verify running: `wsl.exe -e redis-cli ping`
4. Check binding: `wsl.exe -e sudo systemctl restart redis-server`

**Issue**: Keyboard input not working in WSL

**Solutions**:

1. Use Windows Terminal instead of PowerShell
2. Run with explicit root: `wsl.exe --user root`
3. Enable Ctrl+Shift+C/V copy/paste in WSL window properties

**More**: See [docs/laragon/TROUBLESHOOTING.md](../laragon/TROUBLESHOOTING.md#error-connection-refused-on-1270016379-from-laravel)

---

## Laravel Reverb Setup (Real-Time Broadcasting)

**Laravel Reverb** replaces traditional queue workers for real-time features.

### Overview

Reverb is a **WebSocket server** that handles:

- Real-time notifications
- Broadcasting events to connected clients
- Live updates without page refresh
- Presence channels (who's online)

**Reference**: [Laravel Reverb Official Docs](https://laravel.com/docs/reverb)

### Configuration

**`.env` settings**:

```dotenv
BROADCAST_DRIVER=reverb

REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_APP_ID=12345
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
```

**Check current values**:

```powershell
php artisan config:show broadcasting
```

### Running Reverb

**Start the WebSocket server**:

```powershell
php artisan reverb:start
# Output: INFO  Starting server on 0.0.0.0:8080 (127.0.0.1).
```

**With additional options**:

```powershell
# Foreground with debug output
php artisan reverb:start --verbose

# Background via PM2 (production-like)
npm install -g pm2
pm2 start "php artisan reverb:start" --name "reverb"
```

### Development Stack (All Services)

**Terminal 1: Laravel Server**

```powershell
cd C:\laragon\www\ictserve-031125
php artisan serve
# Running on http://127.0.0.1:8000
```

**Terminal 2: Vite Frontend Bundler**

```powershell
npm run dev
# Watching for changes
```

**Terminal 3: Reverb WebSocket Server**

```powershell
php artisan reverb:start
# Running on ws://127.0.0.1:8080
```

**Terminal 4 (Optional): Queue Worker** (if using traditional jobs)

```powershell
php artisan queue:work
```

**Or use the convenience command**:

```powershell
composer run dev
# Starts Laravel + Vite together (doesn't start Reverb automatically)
```

### Verifying Reverb

**Check WebSocket port is open**:

```powershell
Test-NetConnection -ComputerName 127.0.0.1 -Port 8080
# Expected: TcpTestSucceeded = True
```

**Check logs**:

```powershell
tail -f storage/logs/laravel.log | Select-String -Pattern "Reverb|broadcast"
```

**Test broadcasting from Tinker**:

```powershell
php artisan tinker
>>> event(new \App\Events\YourEvent());
```

### Broadcasting Channel Security

**Require authentication** for private/presence channels:

Edit `routes/channels.php`:

```php
Broadcast::channel('user.{id}', function ($user, $id) {
    return (int) $user->id === (int) $id;
});
```

**Reference**: [Laravel Broadcasting Docs](https://laravel.com/docs/broadcasting)

---

## Files Modified / Created by the Team

- **`compose.yaml`**: Includes `redis` service and `app` service `depends_on` entry
- **`Dockerfile`**: Installs `phpredis` extension via `pecl`
- **`.env.docker`**: Sets `REDIS_HOST=redis`, `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis`
- **`config/broadcasting.php`**: Configured for Reverb with app credentials
- **`routes/channels.php`**: Channel authorization rules

---

## Docker Compose Usage (Alternative for Consistent Environments)

**Use Docker if you prefer containerized Redis or need multi-container consistency.**

### Build and Run

Build and run Docker Compose (rebuilds `app` container to include `phpredis`):

```powershell
# From repository root
docker compose -f compose.yaml up -d --build
```

### Health Checks

Check the Redis container is ready:

```powershell
# Check container status
docker compose -f compose.yaml ps

# Check redis health
docker compose -f compose.yaml logs redis --tail 100

# Ping the redis server
docker exec -it ictserve-redis redis-cli ping
# PONG indicates Redis is available
```

### Verify phpredis Extension

Verify PHP extension `redis` is available in the `app` container:

```powershell
docker exec -it ictserve-app php -m | Select-String redis
# or
docker exec -it ictserve-app php -i | Select-String "redis"
```

### Test Laravel Integration

Verify Laravel can use Redis (`.env.docker` is pre-configured):

```powershell
# Run inside container
docker exec -it ictserve-app php artisan cache:clear
# Should print: "Application cache cleared!"

# To test with Reverb
docker exec -it ictserve-app php artisan reverb:start

# To test with traditional queue worker
docker exec -it ictserve-app php artisan queue:work --once
```

### Docker Network Notes

- `compose.yaml` creates a Docker network where services communicate by service name (`redis`).
- If you rebuild the Docker image and the app container doesn't connect to Redis, verify `app` environment contains `REDIS_HOST=redis` and check `docker logs ictserve-app` for errors.
- To use a different port to avoid conflicts on your host machine, update `compose.yaml` (e.g. `"6380:6379"`) and set `REDIS_PORT` in `.env.docker`.

---

## Local Windows/Laragon Redis Installation (Alternative)

If you prefer native Windows Redis without WSL or Docker:

### Option A: Windows Binary or Laragon

1) Install Redis for Windows from [Redis Windows Releases](https://github.com/microsoftarchive/redis/releases) or via Laragon's package manager.

2) Configure to listen on `127.0.0.1:6379` in `redis.conf`.

3) Ensure `.env` has:

```dotenv
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

4) Test connection:

```powershell
redis-cli ping
# PONG -> ok
```

5) Verify Laravel can connect:

```powershell
php artisan cache:clear
```

### Option B: WSL (Recommended - Current Setup)

See [WSL Native Redis Setup](#wsl-native-redis-setup-current---recommended-for-laragon) section above.

### Option C: Docker Standalone (if not using compose.yaml)

```powershell
docker run -d --name ictserve-redis -p 6379:6379 redis:7
# Check
docker exec -it ictserve-redis redis-cli ping

# You can run a quick Redis CLI check from your host
redis-cli -h 127.0.0.1 -p 6379 ping
```

**Reference**: [Docker Official Redis Image](https://hub.docker.com/_/redis)

---

## Verifying You Are Using Redis in Laravel

Check your environment:

```powershell
php artisan env
```

Confirm settings:

```powershell
php artisan config:show cache.stores.redis
php artisan config:show broadcasting
```

Check Laravel config files:

```php
// config/database.php -> redis.connections.default
// config/cache.php -> 'redis' store
// config/broadcasting.php -> 'reverb' driver
```

Test cache and broadcasting:

```powershell
php artisan cache:clear
php artisan config:clear
php artisan config:cache
```

Check logs for errors:

```powershell
tail -f storage/logs/laravel.log
```

---

## Troubleshooting

### "Connection refused" or socket errors

- **Ensure Redis is started and listening** on the configured host/port.
- **For WSL**: `wsl.exe -e redis-cli ping` should return `PONG`
- **For Docker**: Use `docker compose ps`, `docker logs ictserve-redis`, and `docker exec -it ictserve-redis redis-cli ping`
- **For Windows native**: Use `redis-cli ping` locally

### Conflict on port 6379

- **WSL + Laragon Redis**: Disable Laragon's Redis in Laragon UI (stop the service)
- **Docker vs host**: If your host has Redis on 6379 and Docker tries 6379:6379, update `compose.yaml` to use `"6380:6379"` and set `REDIS_PORT=6380` in `.env.docker`

### phpredis extension missing

- Rebuilding required: `docker compose -f compose.yaml up -d --build`
- Verify installed: `php -m | Select-String redis` (for local) or `docker exec -it ictserve-app php -m | Select-String redis` (Docker)

### Session or cache still using `file` / `database`

- Check `.env` or `.env.docker` for `CACHE_STORE=redis` and `SESSION_DRIVER=redis`
- Run: `php artisan config:cache && php artisan config:clear`

### Incompatibilities with Predis vs phpredis

- This repo uses `REDIS_CLIENT=phpredis` (preferred).
- Some libraries allow both; `phpredis` is recommended and installed in the Dockerfile.

---

## Official References

- **Laravel Redis**: <https://laravel.com/docs/redis>
- **Laravel Cache**: <https://laravel.com/docs/cache>
- **Laravel Broadcasting**: <https://laravel.com/docs/broadcasting>
- **Laravel Reverb**: <https://laravel.com/docs/reverb>
- **phpredis**: <https://github.com/phpredis/phpredis>
- **Redis Official**: <https://redis.io/>
- **WSL Documentation**: <https://docs.microsoft.com/en-us/windows/wsl>

---

**Last Updated**: December 7, 2025  
**Maintained By**: ICTServe Development Team  
**Status**: ✅ Production-Ready
