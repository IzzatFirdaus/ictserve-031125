# ICTServe WSL Redis Setup Guide

This guide provides comprehensive instructions for setting up Redis 7.0+ in WSL (Windows Subsystem for Linux) for the ICTServe Laravel application.

## Overview

The ICTServe system uses Redis for:

- Session storage
- Cache management
- Queue processing (Laravel Horizon)
- Real-time features (Laravel Reverb)
- Performance monitoring (Laravel Pulse)

## Prerequisites

- Windows 10/11 with WSL 2 enabled
- Ubuntu distribution installed in WSL
- PowerShell 5.1 or later

## Quick Setup

### 1. Install Redis in WSL

```powershell
# Run the automated installation script
.\scripts\wsl\install-redis.ps1
```

This script will:

- Verify WSL and Ubuntu installation
- Install Redis 7.0+ in WSL
- Configure Redis for Windows host connectivity
- Set up proper binding and security settings
- Test connectivity

### 2. Test Installation

```powershell
# Test Redis connectivity
.\scripts\wsl\test-redis-connectivity.ps1

# Test with verbose output
.\scripts\wsl\test-redis-connectivity.ps1 -Verbose
```

### 3. Update Laravel Configuration

Add these settings to your `.env` file:

```env
# Redis Configuration for WSL
REDIS_CLIENT=phpredis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
REDIS_DB=0

# Cache Configuration
CACHE_STORE=redis

# Session Configuration
SESSION_DRIVER=redis

# Queue Configuration
QUEUE_CONNECTION=redis
```

## Management Scripts

### Redis Service Management

```powershell
# Start Redis
.\scripts\wsl\manage-redis.ps1 start

# Stop Redis
.\scripts\wsl\manage-redis.ps1 stop

# Restart Redis
.\scripts\wsl\manage-redis.ps1 restart

# Check status
.\scripts\wsl\manage-redis.ps1 status

# Test connectivity
.\scripts\wsl\manage-redis.ps1 test

# View logs
.\scripts\wsl\manage-redis.ps1 logs

# Show configuration
.\scripts\wsl\manage-redis.ps1 config

# Show server info
.\scripts\wsl\manage-redis.ps1 info

# Monitor Redis commands
.\scripts\wsl\manage-redis.ps1 monitor
```

### WSL Bash Commands

```bash
# Inside WSL, you can use the bash script
./scripts/wsl/redis-control.sh start
./scripts/wsl/redis-control.sh status
./scripts/wsl/redis-control.sh logs 100
```

## Troubleshooting

### Automatic Troubleshooting

```powershell
# Diagnose issues
.\scripts\wsl\troubleshoot-redis.ps1

# Diagnose and auto-fix issues
.\scripts\wsl\troubleshoot-redis.ps1 -AutoFix

# Verbose troubleshooting
.\scripts\wsl\troubleshoot-redis.ps1 -Verbose
```

### Common Issues

#### 1. WSL Not Available
**Error**: WSL commands fail or not found
**Solution**:

```powershell
# Install WSL
wsl --install

# Install Ubuntu
wsl --install -d Ubuntu
```

#### 2. Redis Not Responding
**Error**: Connection refused or timeout
**Solutions**:

```powershell
# Check if Redis is running
.\scripts\wsl\manage-redis.ps1 status

# Restart Redis
.\scripts\wsl\manage-redis.ps1 restart

# Check configuration
.\scripts\wsl\manage-redis.ps1 config
```

#### 3. Windows Host Cannot Connect
**Error**: Cannot connect from Windows to WSL Redis
**Solutions**:

```powershell
# Check Redis binding
wsl grep "^bind" /etc/redis/redis.conf
# Should show: bind 0.0.0.0

# Check protected mode
wsl grep "^protected-mode" /etc/redis/redis.conf
# Should show: protected-mode no

# Restart WSL networking
wsl --shutdown
# Wait 5 seconds, then test again
```

#### 4. Laravel Cannot Connect
**Error**: Laravel Redis connection fails
**Solutions**:

1. Verify `.env` settings:

   ```env
   REDIS_HOST=127.0.0.1
   REDIS_PORT=6379
   REDIS_PASSWORD=null
   ```

2. Test Laravel connection:

   ```bash
   php artisan tinker
   Redis::connection()->ping()
   # Should return: "PONG"
   ```

3. Clear Laravel cache:

   ```bash
   php artisan config:clear
   php artisan cache:clear
   ```

## Configuration Details

### Redis Configuration File
Location: `/etc/redis/redis.conf`

Key settings for ICTServe:

```conf
# Network
bind 0.0.0.0
port 6379
protected-mode no

# Memory
maxmemory 256mb
maxmemory-policy allkeys-lru

# Persistence
save 900 1
save 300 10
save 60 10000

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log
```

### Laravel Redis Configuration
Location: `config/database.php`

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'phpredis'),
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
    ],
    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
    ],
    'cache' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_CACHE_DB', '1'),
    ],
],
```

## Performance Tuning

### Memory Optimization

```conf
# Adjust based on available system memory
maxmemory 512mb
maxmemory-policy allkeys-lru
maxmemory-samples 5
```

### Persistence Settings

```conf
# For development (frequent saves)
save 900 1
save 300 10
save 60 10000

# For production (less frequent saves)
save 3600 1
save 300 100
save 60 10000
```

### Network Optimization

```conf
# Increase backlog for high traffic
tcp-backlog 511

# Optimize keepalive
tcp-keepalive 300

# Timeout settings
timeout 300
```

## Security Considerations

### Development Environment

- Protected mode is disabled for easy connectivity
- No password required (development only)
- Binding to all interfaces (0.0.0.0)

### Production Environment
For production deployment, consider:

```conf
# Enable protected mode
protected-mode yes

# Set a strong password
requirepass your-strong-password-here

# Bind to specific interfaces only
bind 127.0.0.1 ::1

# Enable TLS
tls-port 6380
tls-cert-file /path/to/redis.crt
tls-key-file /path/to/redis.key
```

## Integration with ICTServe Services

### Laravel Horizon (Queue Management)

```env
HORIZON_REDIS_CONNECTION=default
```

### Laravel Pulse (Performance Monitoring)

```env
PULSE_REDIS_CONNECTION=default
```

### Laravel Reverb (WebSocket)

```env
REVERB_REDIS_CONNECTION=default
```

### Session Storage

```env
SESSION_DRIVER=redis
SESSION_LIFETIME=120
```

## Monitoring and Maintenance

### Log Files

- Redis logs: `/var/log/redis/redis-server.log`
- View logs: `.\scripts\wsl\manage-redis.ps1 logs`

### Performance Monitoring

```bash
# Monitor Redis commands
redis-cli monitor

# Get server info
redis-cli info

# Check memory usage
redis-cli info memory

# Check connected clients
redis-cli info clients
```

### Backup and Recovery

```bash
# Create backup
redis-cli BGSAVE

# Check backup status
redis-cli LASTSAVE

# Backup file location
/var/lib/redis/dump.rdb
```

## Support

For additional help:

- Check Redis logs: `.\scripts\wsl\manage-redis.ps1 logs`
- Run diagnostics: `.\scripts\wsl\troubleshoot-redis.ps1`
- Test connectivity: `.\scripts\wsl\test-redis-connectivity.ps1`
- Monitor Redis: `.\scripts\wsl\manage-redis.ps1 monitor`

## References

- [Redis Documentation](https://redis.io/documentation)
- [Laravel Redis Documentation](https://laravel.com/docs/redis)
- [WSL Documentation](https://docs.microsoft.com/en-us/windows/wsl/)
- [ICTServe System Requirements](../../.kiro/specs/xampp-environment-revert/requirements.md)
