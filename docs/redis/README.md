# Redis Documentation for ICTServe

**Version**: 3.5.0  
**Redis**: 7.0.15 (WSL Ubuntu 24.04 LTS)  
**Laravel**: 12.40.2  
**Last Updated**: December 7, 2025

---

## 📖 Documentation Index

This directory contains comprehensive Redis setup and usage documentation for ICTServe development.

### Core Setup Guides

| Document | Purpose | Audience |
|----------|---------|----------|
| **[redis-setup.md](redis-setup.md)** | Main Redis setup guide covering WSL, Docker, and native installations | All developers |
| **[WSL_SETUP.md](WSL_SETUP.md)** | Detailed WSL Ubuntu Redis 7.0.15 installation and configuration | Windows developers (current setup) |
| **[PHPREDISADMIN_SETUP.md](PHPREDISADMIN_SETUP.md)** | Web-based Redis management interface setup | Developers wanting visual Redis management |
| **[REDIS_COMMANDS_REFERENCE.md](REDIS_COMMANDS_REFERENCE.md)** | Quick reference for Redis CLI commands and patterns | All developers |

---

## 🚀 Quick Start

### New Developer Setup (5 Minutes)

**Goal**: Get WSL Redis 7.0.15 running and connected to Laravel ICTServe app.

**Prerequisites**:

- Windows 10/11 with WSL 2 enabled
- Ubuntu 24.04 LTS installed in WSL
- XAMPP or Laragon with PHP 8.2+

**Steps**:

1. **Install Redis in WSL** (1 min):

   ```powershell
   # Option A — Automated: run the repo installer (Windows):
   npm run wsl-redis-setup

   # Option B — Manual one-liner:
   wsl.exe --user root -e bash -c "apt update && apt upgrade -y && apt install redis-server -y"
   ```

2. **Configure Redis for Windows access** (2 min):

   ```bash
   # In WSL
   sudo nano /etc/redis/redis.conf
   # Change: bind 127.0.0.1 ::1  →  bind 0.0.0.0 ::1
   # Save: Ctrl+O, Enter, Ctrl+X
   
   sudo systemctl restart redis-server
   sudo systemctl enable redis-server
   ```

3. **Test connection** (1 min):

   ```powershell
   # From Windows PowerShell
   wsl.exe -e redis-cli ping
   # Expected: PONG
   
   Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
   # Expected: TcpTestSucceeded = True
   ```

4. **Configure Laravel** (1 min):

   ```dotenv
   # Verify .env has these settings:
   REDIS_CLIENT=predis
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   CACHE_STORE=redis
   SESSION_DRIVER=redis
   QUEUE_CONNECTION=redis
   ```

   **Note**: Use `REDIS_CLIENT=predis` for cross-platform compatibility. Predis is a pure PHP library that works on Windows, Linux, and macOS without requiring PHP extensions.

5. **Test Laravel integration** (30 sec):

   ```powershell
   php artisan config:clear
   php artisan tinker
   >>> cache()->put('test', 'WSL Redis working!', 60);
   >>> cache()->get('test');
   => "WSL Redis working!"
   >>> exit
   ```

**Done!** Laravel is now using WSL Redis for cache, sessions, and queues.

**Optional**: Install [phpRedisAdmin](PHPREDISADMIN_SETUP.md) for visual management.

---

## 📚 Documentation Structure

### 1. [redis-setup.md](redis-setup.md) - Main Setup Guide

**Contents**:

- Overview of Redis usage in ICTServe
- WSL Native Redis setup (current recommended approach)
- Docker Compose alternative
- Laravel Reverb WebSocket server setup
- Configuration examples
- Troubleshooting common issues

**When to read**: First time setting up Redis, or when choosing between WSL/Docker/native options.

**Key sections**:

- ✅ WSL Native Redis Setup (recommended for XAMPP/Laragon)
- ✅ Laravel Reverb Setup (real-time broadcasting)
- ✅ Docker Compose Usage (alternative for containerized environments)
- ✅ Verification steps

---

### 2. [WSL_SETUP.md](WSL_SETUP.md) - WSL Redis Detailed Guide

**Contents**:

- Prerequisites (WSL 2, Ubuntu 24.04, systemd)
- Step-by-step installation
- Configuration (bind address, persistence, security)
- Auto-start setup
- Integration with Laravel
- Backup and restore
- Performance tuning
- Troubleshooting

**When to read**: When you need detailed WSL-specific Redis configuration or troubleshooting.

**Key sections**:

- ✅ Quick Install (automated one-liner)
- ✅ Manual Install (step-by-step)
- ✅ Configuration (bind address critical for Windows access)
- ✅ Auto-Start Setup (systemd service)
- ✅ Web Management Interface (phpRedisAdmin)

---

### 3. [PHPREDISADMIN_SETUP.md](PHPREDISADMIN_SETUP.md) - Web UI Guide

**Contents**:

- Prerequisites (PHP extensions, Composer)
- Installation via Git and Composer
- Configuration (connecting to WSL Redis)
- Usage guide (dashboard, key browser, operations)
- Troubleshooting (common dependency errors)
- Security best practices
- Alternative Redis GUI tools

**When to read**: When you want a visual interface to manage Redis keys, monitor server stats, or execute commands through a web UI.

**Key sections**:

- ✅ Installation (critical: must use Composer for dependencies)
- ✅ Configuration (config.inc.php for WSL Redis connection)
- ✅ Troubleshooting (PSR-7 dependency errors)
- ✅ Usage Guide (dashboard features, key operations)
- ✅ Security Notes (development vs production)

---

### 4. [REDIS_COMMANDS_REFERENCE.md](REDIS_COMMANDS_REFERENCE.md) - CLI Reference

**Contents**:

- Connection and server commands
- Key management operations
- Data type operations (String, List, Hash, Set, Sorted Set)
- Pub/Sub messaging
- Transactions and locking
- Laravel-specific key patterns
- Performance monitoring commands
- Backup and maintenance
- Common usage patterns
- Quick reference card

**When to read**: When working with Redis CLI directly, debugging cache issues, or implementing advanced Redis features.

**Key sections**:

- ✅ Laravel-Specific Keys (cache, session, queue formats)
- ✅ Common Laravel Commands (cache:clear, tinker usage)
- ✅ Performance & Monitoring (MONITOR, SLOWLOG, INFO)
- ✅ Useful Patterns (rate limiting, distributed locking, leaderboards)

---

## 🎯 Common Tasks

### Check if Redis is Running

```powershell
# From Windows PowerShell
wsl.exe -e redis-cli ping
# Expected: PONG

# Check service status
wsl.exe -e systemctl status redis-server
# Expected: active (running)
```

### View All Laravel Cache Keys

```powershell
wsl.exe -e redis-cli KEYS "laravel_database_*"
```

### Clear All Laravel Cache

```powershell
# Via Laravel Artisan
php artisan cache:clear

# Or directly via Redis CLI
wsl.exe -e redis-cli FLUSHDB
```

### Monitor Redis in Real-Time

```powershell
# Watch all incoming commands
wsl.exe -e redis-cli MONITOR

# Real-time stats
wsl.exe -e redis-cli --stat
```

### Backup Redis Data

```bash
# In WSL
redis-cli BGSAVE
cp /var/lib/redis/dump.rdb ~/redis-backup-$(date +%Y%m%d).rdb
```

### Access phpRedisAdmin Web UI

```
http://localhost/redis/phpRedisAdmin/
```

### Start Laravel with Redis Support

```powershell
# Terminal 1: Laravel server
php artisan serve

# Terminal 2: Vite (frontend)
# Option A (Windows, recommended): Use the PowerShell helper which ensures Node v22
npm run dev:win
# Option B (manual): ensure Node v22 is active for this session
cd C:\laragon\www\ictserve-031125
. .\.env.ps1
npm run dev

# Terminal 3: Reverb (WebSocket server)
php artisan reverb:start

# Or use convenience command (starts Laravel + Vite)
composer run dev
```

---

## 🔧 Troubleshooting Quick Links

### "Connection refused on 127.0.0.1:6379"

**Solution**: Check [WSL_SETUP.md - Troubleshooting](WSL_SETUP.md#issue-connection-refused-on-port-6379)

**Common causes**:

- Redis not running: `wsl.exe -e sudo systemctl start redis-server`
- Wrong binding in redis.conf: Should be `bind 0.0.0.0 ::1`
- Laragon's Redis conflicting: Disable in Laragon UI

### "Interface Psr\Http\Message\StreamInterface not found"

**Solution**: Check [PHPREDISADMIN_SETUP.md - Troubleshooting](PHPREDISADMIN_SETUP.md#issue-interface-psrhttpmessagestreaminterface-not-found)

**Fix**:

```powershell
cd C:\XAMPP\htdocs\redis\phpRedisAdmin
composer require predis/predis
```

### Laravel not using Redis cache

**Solution**: Check [redis-setup.md - Verifying You Are Using Redis](redis-setup.md#verifying-you-are-using-redis-in-laravel)

**Steps**:

1. Verify `.env` has `CACHE_STORE=redis`
2. Clear config: `php artisan config:clear`
3. Test: `php artisan tinker` → `cache()->get('test')`

### WSL keyboard not responding

**Solution**: Check [WSL_SETUP.md - Troubleshooting](WSL_SETUP.md#issue-wsl-keyboard-input-not-responding)

**Fixes**:

- Use Windows Terminal instead of PowerShell
- Run with explicit root: `wsl.exe --user root`
- Enable Ctrl+Shift+C/V copy/paste in WSL properties

---

## 📊 Architecture Overview

### ICTServe Redis Stack

```
┌─────────────────────────────────────────────┐
│           Laravel Application               │
│  (XAMPP/Laragon - Windows PHP 8.2.12)     │
│                                             │
│  ┌─────────────┐ ┌─────────────┐          │
│  │   Cache     │ │   Session   │          │
│  │ (redis)     │ │  (redis)    │          │
│  └──────┬──────┘ └──────┬──────┘          │
│         │                │                  │
│         └────────┬───────┘                 │
│                  │                          │
│         ┌────────▼─────────┐               │
│         │  Queue (redis)   │               │
│         └────────┬─────────┘               │
│                  │                          │
│         ┌────────▼─────────┐               │
│         │ Reverb WebSocket │               │
│         │   (port 8080)    │               │
│         └────────┬─────────┘               │
└──────────────────┼─────────────────────────┘
                   │
                   │ TCP 127.0.0.1:6379
                   │
           ┌───────▼───────┐
           │  WSL Ubuntu   │
           │ Redis 7.0.15  │
           │ (port 6379)   │
           └───────┬───────┘
                   │
         ┌─────────▼─────────┐
         │  phpRedisAdmin    │
         │  (Optional Web UI)│
         │ http://localhost/ │
         │ redis/            │
         │ phpRedisAdmin/    │
         └───────────────────┘
```

### Data Flow

1. **Laravel Cache Request**:
   - Laravel → phpredis extension → TCP 127.0.0.1:6379 → WSL Redis
   - Key format: `laravel_database_{cache_key}`

2. **Session Storage**:
   - User request → Laravel → Session driver → Redis
   - Key format: `laravel_database_session:{session_id}`
   - TTL: Based on session lifetime config

3. **Queue Jobs**:
   - Laravel → Queue driver → Redis List
   - Key format: `laravel_database_queues:{queue_name}`
   - Worker: `php artisan queue:work` processes jobs

4. **Real-Time Broadcasting** (Laravel Reverb):
   - Event triggered → Reverb server → WebSocket clients
   - Redis used as: Pub/Sub backend for multi-server sync

5. **phpRedisAdmin** (optional):
   - Browser → Apache → Predis library → TCP 127.0.0.1:6379 → WSL Redis
   - Provides: Visual key browser, server monitoring, command execution
   - **Note**: phpRedisAdmin tool uses phpredis extension, but Laravel uses Predis library

---

## 🔐 Security Considerations

### Development Environment (Current)

**Current Setup**:

- ✅ Redis bound to `0.0.0.0` (allows Windows loopback access)
- ✅ `protected-mode yes` (denies unauthenticated external connections)
- ✅ No password (convenient for local development)
- ✅ WSL network isolated from external internet

**Safe because**:

- WSL is not exposed to internet
- Only Windows host can connect via 127.0.0.1
- No sensitive production data in development

### Production Recommendations

**If deploying to production** (future):

1. **Enable Authentication**:

   ```ini
   # /etc/redis/redis.conf
   requirepass strong_random_password_here
   ```

2. **Bind to Specific Interface**:

   ```ini
   bind 127.0.0.1  # Localhost only (not 0.0.0.0)
   ```

3. **Use TLS/SSL** (for remote connections):

   ```ini
   tls-port 6380
   tls-cert-file /path/to/cert.pem
   tls-key-file /path/to/key.pem
   ```

4. **Configure Firewall**:

   ```bash
   sudo ufw allow from 192.168.1.0/24 to any port 6379  # Specific network
   sudo ufw deny 6379  # Block external
   ```

5. **Disable Dangerous Commands**:

   ```ini
   rename-command FLUSHDB ""
   rename-command FLUSHALL ""
   rename-command CONFIG ""
   ```

6. **Regular Backups**:

   ```bash
   redis-cli BGSAVE
   # Copy /var/lib/redis/dump.rdb to secure location
   ```

**Reference**: [WSL_SETUP.md - Security](WSL_SETUP.md#security)

---

## 🔗 External Resources

### Official Documentation

- **Redis Official Docs**: <https://redis.io/documentation>
- **Redis Commands**: <https://redis.io/commands>
- **Redis Best Practices**: <https://redis.io/topics/lru-cache>
- **Laravel Redis**: <https://laravel.com/docs/12.x/redis>
- **Laravel Cache**: <https://laravel.com/docs/12.x/cache>
- **Laravel Reverb**: <https://laravel.com/docs/12.x/reverb>
- **Laravel Broadcasting**: <https://laravel.com/docs/12.x/broadcasting>

### Tools & Libraries

- **Predis PHP Library**: <https://github.com/predis/predis> (recommended for Laravel)
- **phpredis Extension**: <https://github.com/phpredis/phpredis> (required for phpRedisAdmin tool)
- **phpRedisAdmin**: <https://github.com/erikdubbelboer/phpRedisAdmin>
- **RedisInsight**: <https://redis.com/redis-enterprise/redis-insight/>
- **Redis Commander**: <https://github.com/joeferner/redis-commander>

### WSL & Windows

- **WSL Documentation**: <https://docs.microsoft.com/en-us/windows/wsl>
- **Ubuntu Packages**: <https://packages.ubuntu.com/redis-server>
- **Systemd on WSL**: <https://devblogs.microsoft.com/commandline/systemd-support-is-now-available-in-wsl/>

---

## 📝 Changelog

### December 7, 2025 - v3.5.0

- ✅ **Added**: Complete phpRedisAdmin setup guide ([PHPREDISADMIN_SETUP.md](PHPREDISADMIN_SETUP.md))
- ✅ **Added**: Comprehensive Redis commands reference ([REDIS_COMMANDS_REFERENCE.md](REDIS_COMMANDS_REFERENCE.md))
- ✅ **Added**: This README.md for documentation navigation
- ✅ **Updated**: [redis-setup.md](redis-setup.md) with Predis emphasis and phpRedisAdmin integration
- ✅ **Updated**: [WSL_SETUP.md](WSL_SETUP.md) with Web Management Interface section
- ✅ **Updated**: All documentation to emphasize Predis over phpredis for Laravel applications
- ✅ **Clarified**: phpRedisAdmin tool uses phpredis extension, but Laravel should use Predis library
- ✅ **Verified**: All steps tested on Windows 11 with WSL Ubuntu 24.04 LTS + XAMPP
- ✅ **Fixed**: Documented Predis/PSR-7 dependency installation requirements

### Previous Updates

- **November 2025**: Initial WSL Redis 7.0.15 setup guide
- **November 2025**: Laravel Reverb WebSocket integration
- **November 2025**: Docker Compose alternative setup

---

## 🤝 Contributing

**Found an issue or improvement?**

1. Check existing documentation first
2. Test your solution in a clean WSL/XAMPP environment
3. Document exact steps to reproduce and fix
4. Update relevant markdown files
5. Include version numbers and error messages

**Documentation standards**:

- Use clear, step-by-step instructions
- Include expected outputs for verification steps
- Provide troubleshooting for common errors
- Test all commands before documenting

---

## 📧 Support

**Issues with Redis setup?**

1. **Check troubleshooting sections** in each guide
2. **Verify prerequisites** (WSL version, Ubuntu version, PHP extensions)
3. **Test basic connectivity** (`redis-cli ping`, port checks)
4. **Review logs**: `sudo journalctl -u redis-server -n 50`
5. **Check Laravel logs**: `storage/logs/laravel.log`

**Still stuck?**

- Review all four documentation files in order
- Ensure exact step-by-step compliance
- Check for typos in configuration files
- Verify no conflicting Redis instances (Laragon, Docker, etc.)

---

**Last Updated**: December 7, 2025  
**Maintained By**: ICTServe Development Team  
**Status**: ✅ Complete documentation set for Redis 7.0.15 + WSL + Laravel 12
