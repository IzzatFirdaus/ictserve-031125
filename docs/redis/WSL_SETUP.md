# Redis 7.0 Setup Guide (WSL Ubuntu)

**Version**: 7.0.15  
**Environment**: Windows Subsystem for Linux (WSL 2) Ubuntu 24.04 LTS  
**Purpose**: Cache, Session Storage, Queue Backend for ICTServe  
**Last Updated**: December 7, 2025

---

## Table of Contents

1. [Overview](#overview)
2. [Prerequisites](#prerequisites)
3. [Installation](#installation)
4. [Configuration](#configuration)
5. [Verification](#verification)
6. [Auto-Start Setup](#auto-start-setup)
7. [Security](#security)
8. [Troubleshooting](#troubleshooting)
9. [Performance Tips](#performance-tips)
10. [Web Management Interface](#web-management-interface-phpredisadmin)

---

## Overview

This setup uses **Redis 7.0.15** installed in **WSL Ubuntu 24.04 LTS** instead of Laragon's bundled Redis or Docker, providing:

- ✅ **Lightweight**: Minimal resource overhead (native Ubuntu pkg, not Docker or Laragon)
- ✅ **Replaces Laragon's Redis**: Better performance, fewer conflicts
- ✅ **Native Performance**: Direct Ubuntu package; no container overhead
- ✅ **Persistent**: Data survives WSL restarts (with appendonly mode)
- ✅ **Windows-Accessible**: Exposed to Windows on `127.0.0.1:6379`
- ✅ **Manageable**: Simple systemd service control

⚠️ **CRITICAL**: **Disable Laragon's bundled Redis service** in the Laragon UI to avoid port 6379 conflicts. See [Laragon Setup Guide](../laragon/SETUP.md) for instructions.

---

## Prerequisites

- **WSL 2** installed on Windows 10/11
- **Ubuntu 24.04 LTS** distro active in WSL
- **Windows Subsystem for Linux Update** (latest, available via Windows Update)
- **Systemd enabled** in WSL (required for service autostart)

### Check WSL Version

```powershell
wsl.exe --version
# Output: WSL version: 2.x.x
# Kernel version: 5.15.x or higher
```

### Check Ubuntu Version in WSL

```bash
lsb_release -a
# Output: Ubuntu 24.04 LTS
```

### Enable Systemd in WSL (if not already enabled)

```bash
# In WSL bash shell:
sudo nano /etc/wsl.conf
```

Add or uncomment:

```ini
[boot]
systemd=true
```

Save (Ctrl+O, Enter, Ctrl+X), then restart WSL:

```powershell
# From Windows PowerShell
wsl.exe --shutdown
wsl.exe  # Re-open WSL
```

---

## Installation

### Quick Install (Automated)

```bash
wsl.exe --user root -e bash -c "apt update && apt upgrade -y && apt install redis-server -y"
```

This runs as root and installs Redis without requiring a password prompt.

### Manual Install (Step-by-Step)

```bash
# Update package lists
sudo apt update

# Upgrade existing packages (optional but recommended)
sudo apt upgrade -y

# Install Redis Server and CLI tools
sudo apt install redis-server -y

# Verify installation
redis-cli --version
# Output: redis-cli 7.0.15 (or similar)
```

---

## Configuration

### Bind Address (Allow Windows Access)

Redis is configured to listen only on localhost by default. To allow Windows to connect, update the binding:

```bash
sudo nano /etc/redis/redis.conf
```

**Find**: `bind 127.0.0.1 ::1`  
**Change to**:

```ini
bind 0.0.0.0 ::1
protected-mode yes
```

- `0.0.0.0` allows all interfaces (including Windows loopback)
- `::1` keeps IPv6 localhost
- `protected-mode yes` denies unauthenticated clients from non-localhost (recommended)

**Save**: Ctrl+O, Enter, Ctrl+X

### Optional: Set Password (For Production-like Dev)

To require authentication:

```bash
sudo nano /etc/redis/redis.conf
```

**Find**: `# requirepass foobared`  
**Change to**:

```ini
requirepass your_secure_password_here
```

**Then update `.env`**:

```env
REDIS_PASSWORD=your_secure_password_here
```

### Optional: Enable Persistence

For development, optional but useful for data retention:

```bash
sudo nano /etc/redis/redis.conf
```

**Find**: `# appendonly no`  
**Change to**:

```ini
appendonly yes
appendfsync everysec
```

This enables AOF (Append-Only File) persistence with periodic fsync.

### Restart Redis After Configuration Changes

```bash
sudo systemctl restart redis-server
```

---

## Verification

### Test Connection from WSL

```bash
redis-cli ping
# Expected output: PONG
```

### Test Connection from Windows

```powershell
wsl.exe -e redis-cli ping
# Expected output: PONG
```

### Verify Port Accessibility

```powershell
# From Windows PowerShell
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379

# Expected output:
# ComputerName           : 127.0.0.1
# RemotePort             : 6379
# TcpTestSucceeded       : True
```

### Check Running Service

```bash
sudo systemctl status redis-server
# Should show: active (running)
```

### View Redis Info

```bash
redis-cli INFO server
# Shows version, uptime, memory usage, etc.
```

---

## Auto-Start Setup

### Enable Service Auto-Start

```bash
sudo systemctl enable redis-server
```

### Verify Autostart

```bash
sudo systemctl is-enabled redis-server
# Output: enabled
```

### Manual Service Control

```bash
# Start service
sudo systemctl start redis-server

# Stop service
sudo systemctl stop redis-server

# Restart service
sudo systemctl restart redis-server

# View logs
sudo journalctl -u redis-server -n 20  # Last 20 lines
sudo journalctl -u redis-server -f     # Follow logs
```

### Auto-Start WSL Itself (Windows Terminal)

To auto-start WSL and Redis when Windows boots, use Windows Task Scheduler or a startup script.

**Alternative**: Start WSL manually from Windows Terminal:

```powershell
wsl.exe  # Opens Ubuntu terminal
```

---

## Security

### Network Isolation

Currently, Redis is bound to `0.0.0.0` with `protected-mode yes`:

- ✅ Allows Windows (127.0.0.1) access
- ✅ Denies unauthenticated remote access
- ⚠️ **Not suitable for production** (use password + firewall)

### Development Best Practices

1. **Use a password** if you're on a shared network:

   ```bash
   sudo nano /etc/redis/redis.conf
   # Set: requirepass your_password
   ```

2. **Restrict firewall** (Windows Defender):
   - Block port 6379 from external IPs
   - Only allow 127.0.0.1 and WSL internal IP

3. **Don't expose to internet**:
   - Redis has no built-in encryption
   - Never port-forward 6379 externally

### Backup Redis Data

If using AOF persistence:

```bash
# Backup Redis database file
cp ~/.local/share/redis/appendonly.aof ~/redis-backup-$(date +%Y%m%d).aof
```

---

## Troubleshooting

### Issue: Redis service not running after installation

**Solution**:

```bash
sudo systemctl start redis-server
sudo systemctl status redis-server
```

### Issue: "Connection refused" on port 6379

**Cause**: Redis not listening on the configured interface.

**Solution**:

```bash
# Check binding
redis-cli INFO server | grep bind
# Should show: bind 0.0.0.0

# Restart service
sudo systemctl restart redis-server

# Verify accessibility
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379  # From Windows
```

### Issue: "MISCONF Redis is configured to save RDB snapshots" error

**Cause**: Insufficient permissions or disk space.

**Solution**:

```bash
# Increase max memory policy
redis-cli CONFIG SET maxmemory-policy allkeys-lru

# Or disable RDB (for development)
redis-cli CONFIG SET save ""
```

### Issue: WSL keyboard input not responding

**Cause**: Terminal compatibility issue.

**Solution**:

1. Use **Windows Terminal** instead of PowerShell
2. Or run with explicit root: `wsl.exe --user root`
3. Copy/paste using Ctrl+Shift+C/V (if enabled)

### Issue: Redis stops after WSL restart

**Cause**: Service not enabled for autostart.

**Solution**:

```bash
sudo systemctl enable redis-server
# Verify: sudo systemctl is-enabled redis-server
# Output: enabled
```

### Issue: Memory usage growing unbounded

**Cause**: No eviction policy configured.

**Solution**:

```bash
# Set maximum memory and eviction policy
redis-cli CONFIG SET maxmemory 512mb
redis-cli CONFIG SET maxmemory-policy allkeys-lru
redis-cli CONFIG REWRITE  # Save to redis.conf
```

---

## Performance Tips

### Monitor Memory Usage

```bash
redis-cli INFO memory
# Look for: used_memory_human
```

### Clear Cache (if needed)

```bash
redis-cli FLUSHALL          # Clear all databases
redis-cli FLUSHDB           # Clear current database only
redis-cli DBSIZE            # Check key count
redis-cli KEYS "*"          # List all keys
```

### Monitor Key Space

```bash
redis-cli MONITOR           # Watch all incoming commands (verbose)
redis-cli --stat            # Real-time stats
```

### Optimize for Development

```bash
# Reduce memory overhead
redis-cli CONFIG SET stop-writes-on-bgsave-error no
redis-cli CONFIG SET appendfsync no      # Async persistence (faster, less durable)
redis-cli CONFIG REWRITE                 # Save settings
```

For production, use `appendfsync everysec` (default) or `always`.

---

## Integration with Laravel

### Environment Variables (.env)

```env
# WSL Ubuntu Redis 7.0.15 connection
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null          # Set if password configured in redis.conf
REDIS_DATABASE=0

# Laravel drivers using Redis (uses WSL instance, NOT Laragon's Redis)
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### ⚠️ CRITICAL PRE-FLIGHT CHECKLIST

Before running your Laravel app, verify:

1. **Laragon's Redis is DISABLED**
   - Open Laragon UI
   - Find Redis in services list
   - Click stop/toggle to disable it
   - Status should show "Stopped" or "Offline"
   - ❌ **If you skip this, you'll get "Connection refused" errors!**

2. **WSL Redis is running**

   ```bash
   wsl.exe -e redis-cli ping
   # Expected output: PONG
   ```

3. **Port 6379 is accessible from Windows**

   ```powershell
   Test-NetConnection -ComputerName 127.0.0.1 -Port 6379
   # Expected: TcpTestSucceeded = True
   ```

If all three checks pass, Laravel will automatically use WSL Redis for cache, sessions, and queues.

### Test Redis Connection

```bash
php artisan tinker
>>> cache()->put('test', 'value', 60)
>>> cache()->get('test')
=> "value"
```

### Monitor from Laravel

```bash
php artisan cache:forget test         # Delete a key
php artisan queue:work                # Process queued jobs
php artisan horizon                   # Dashboard (if installed)
```

---

## Web Management Interface (phpRedisAdmin)

For visual Redis management, you can install **phpRedisAdmin** - a web-based interface that provides:

- Visual key browser with search and filtering
- Real-time server monitoring (memory, commands, connections)
- Key editing (view, modify, delete, set TTL)
- Execute Redis commands through web UI
- Support for all Redis data types (String, List, Hash, Set, Sorted Set)

### Quick Installation

```powershell
# From Windows PowerShell
cd C:\XAMPP\htdocs\redis
git clone https://github.com/erikdubbelboer/phpRedisAdmin.git
cd phpRedisAdmin

# Install dependencies via Composer (REQUIRED)
composer require predis/predis

# Configure
cd includes
Copy-Item config.sample.inc.php config.inc.php
# Edit config.inc.php: Set 'host' => '127.0.0.1', 'port' => 6379

# Access
Start-Process "http://localhost/redis/phpRedisAdmin/"
```

### Verify Connection to WSL Redis

Once phpRedisAdmin loads:

1. **Check server info**: Should show Redis 7.0.15, Ubuntu server
2. **Browse keys**: Navigate db0-db15 tabs to view Laravel cache/session keys
3. **Test operations**: Create, edit, delete test keys
4. **Monitor stats**: Watch memory usage, commands/sec, connected clients

### Troubleshooting phpRedisAdmin

**Error: "Interface Psr\Http\Message\StreamInterface not found"**

- **Cause**: Missing Composer dependencies
- **Fix**: Run `composer require predis/predis` to install Predis v3.3+ with PSR-7 libraries

**Error: "Cannot connect to Redis server"**

- **Verify WSL Redis running**: `wsl.exe -e redis-cli ping` should return PONG
- **Check binding**: Ensure redis.conf has `bind 0.0.0.0 ::1`
- **Check config.inc.php**: Host should be `127.0.0.1` (not `localhost` or `redis`)

**📖 Complete Guide**: See [PHPREDISADMIN_SETUP.md](PHPREDISADMIN_SETUP.md) for:

- Detailed installation and configuration
- Usage guide with all features
- Security best practices
- Alternative Redis GUI tools

---

## Backup & Restore

### Manual Backup

```bash
# From WSL
redis-cli BGSAVE                       # Background save (RDB)
cp /var/lib/redis/dump.rdb ~/backup-$(date +%Y%m%d).rdb
```

### Restore from Backup

```bash
sudo systemctl stop redis-server
sudo cp ~/backup-20251207.rdb /var/lib/redis/dump.rdb
sudo chown redis:redis /var/lib/redis/dump.rdb
sudo systemctl start redis-server
```

---

## Useful Commands Reference

```bash
# Service Control
sudo systemctl start redis-server
sudo systemctl stop redis-server
sudo systemctl restart redis-server
sudo systemctl status redis-server
sudo systemctl enable redis-server
sudo systemctl disable redis-server

# Redis CLI
redis-cli ping                         # Health check
redis-cli INFO                         # Full server info
redis-cli DBSIZE                       # Key count
redis-cli FLUSHDB                      # Clear current database
redis-cli CONFIG GET *                 # Show all config
redis-cli SLOWLOG GET 10               # Last 10 slow commands

# Logs
sudo journalctl -u redis-server -n 50  # Last 50 log lines
sudo journalctl -u redis-server -f     # Follow real-time logs

# Advanced
redis-cli MONITOR                      # Watch all commands
redis-cli --stat                       # Real-time statistics
redis-cli BGSAVE                       # Background save
redis-cli LASTSAVE                     # Last save timestamp
```

---

## Resources

- **Redis Official Docs**: <https://redis.io/documentation>
- **Ubuntu Packages**: <https://packages.ubuntu.com/redis-server>
- **Laravel Redis Docs**: <https://laravel.com/docs/12.x/redis>
- **WSL Docs**: <https://docs.microsoft.com/en-us/windows/wsl>

---

**Last Updated**: December 7, 2025  
**Maintained By**: ICTServe Development Team  
**Support**: See [../TROUBLESHOOTING.md](../TROUBLESHOOTING.md) for general issues
