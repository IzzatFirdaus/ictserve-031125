# Redis Setup Guide for ICTServe on Laragon

## Overview

This guide provides comprehensive instructions for setting up and optimizing Redis for the ICTServe system running on Laragon. Redis is essential for caching, sessions, queues, and real-time features.

## Prerequisites

- Laragon installed and running
- ICTServe project properly set up
- PowerShell 5.1 or higher

## Quick Setup

### 1. Run the Automated Setup

```powershell
# Navigate to project root
cd C:\laragon\www\ictserve-031125

# Run the optimized Laragon setup
.\scripts\laragon\setup-laragon.ps1 -CreateDatabase -RunMigrations -Force

# Run Redis health check
.\scripts\laragon\redis-health-check.ps1 -Fix -Detailed
```

### 2. Verify Redis Installation

```powershell
# Test Redis connection
.\scripts\laragon\redis-health-check.ps1
```

## Manual Redis Installation (if needed)

### Option 1: Laragon Quick Add

1. Open Laragon
2. Right-click on Laragon tray icon
3. Go to **Quick Add** > **Redis**
4. Select the latest Redis version
5. Wait for installation to complete
6. Start Redis service

### Option 2: Manual Installation

1. Download Redis for Windows from: https://github.com/microsoftarchive/redis/releases
2. Extract to `C:\laragon\bin\redis\redis-x.x.x\`
3. Create Redis configuration file
4. Start Redis server manually

## Configuration Details

### Environment Variables (.env.laragon)

```env
# Optimized Redis Configuration for Laragon
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Database Separation for Different Services
REDIS_DB=0              # Default/General
REDIS_CACHE_DB=1        # Laravel Cache
REDIS_SESSION_DB=2      # User Sessions
REDIS_QUEUE_DB=3        # Background Jobs
REDIS_REVERB_DB=4       # WebSocket/Real-time
REDIS_PULSE_DB=5        # Performance Monitoring
REDIS_HORIZON_DB=6      # Queue Dashboard

# Connection Optimization
REDIS_MAX_RETRIES=3
REDIS_BACKOFF_ALGORITHM=decorrelated_jitter
REDIS_BACKOFF_BASE=100
REDIS_BACKOFF_CAP=1000
REDIS_PERSISTENT=false
REDIS_PREFIX=ictserve-laragon-

# Service Configuration
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

### Laravel Database Configuration

The system automatically configures multiple Redis connections in `config/database.php`:

- **default**: General Redis operations
- **cache**: Laravel cache system
- **session**: User session storage
- **queue**: Background job processing
- **reverb**: WebSocket/real-time features
- **pulse**: Performance monitoring
- **horizon**: Queue dashboard

## Redis Client Options

### Predis (Recommended for Laragon)

**Advantages:**
- Pure PHP implementation
- Better Windows compatibility
- No PHP extension required
- Easier debugging

**Configuration:**
```env
REDIS_CLIENT=predis
```

### PhpRedis (Alternative)

**Advantages:**
- Better performance
- Lower memory usage
- Native C extension

**Requirements:**
- PHP Redis extension installed
- May require additional configuration on Windows

**Configuration:**
```env
REDIS_CLIENT=phpredis
```

## Performance Optimization

### Redis Configuration File

The setup script creates an optimized Redis configuration at `storage/redis/redis.conf`:

```conf
# Memory Management
maxmemory 256mb
maxmemory-policy allkeys-lru

# Persistence (optimized for development)
save 900 1
save 300 10
save 60 10000

# Performance
tcp-backlog 511
maxclients 10000
timeout 0
tcp-keepalive 300
```

### Laravel Cache Configuration

```php
// config/cache.php
'redis' => [
    'driver' => 'redis',
    'connection' => env('REDIS_CACHE_CONNECTION', 'cache'),
    'lock_connection' => env('REDIS_CACHE_LOCK_CONNECTION', 'default'),
],
```

## Troubleshooting

### Common Issues and Solutions

#### 1. Redis Connection Failed

**Symptoms:**
- "Connection refused" errors
- Cache/session not working
- Queue jobs not processing

**Solutions:**
```powershell
# Check if Redis is running
.\scripts\laragon\redis-health-check.ps1

# Start Redis manually
.\scripts\laragon\redis-health-check.ps1 -Fix

# Check Laragon services
# Open Laragon > Services > Start Redis
```

#### 2. PHP Redis Extension Issues

**Symptoms:**
- "Class 'Redis' not found" errors
- Performance issues with phpredis client

**Solutions:**
```powershell
# Switch to Predis client
# Update .env file:
REDIS_CLIENT=predis

# Install Predis package
composer require predis/predis
```

#### 3. Memory Issues

**Symptoms:**
- Redis running out of memory
- Slow performance
- Connection timeouts

**Solutions:**
```conf
# Update Redis configuration
maxmemory 512mb
maxmemory-policy allkeys-lru

# Clear Redis data
redis-cli FLUSHALL
```

#### 4. Port Conflicts

**Symptoms:**
- Redis fails to start
- Port 6379 already in use

**Solutions:**
```powershell
# Check what's using port 6379
netstat -an | findstr :6379

# Kill conflicting processes
taskkill /F /IM redis-server.exe

# Use alternative port
REDIS_PORT=6380
```

### Health Check Commands

```powershell
# Comprehensive health check
.\scripts\laragon\redis-health-check.ps1 -Detailed

# Fix issues automatically
.\scripts\laragon\redis-health-check.ps1 -Fix

# Test specific Redis operations
redis-cli ping
redis-cli info memory
redis-cli info clients
```

## Testing Redis Functionality

### Basic Connection Test

```php
// Test in Laravel Tinker
php artisan tinker

// Test Redis connection
use Illuminate\Support\Facades\Redis;
Redis::ping(); // Should return "PONG"

// Test cache
Cache::put('test', 'value', 60);
Cache::get('test'); // Should return "value"

// Test session
session(['test' => 'session_value']);
session('test'); // Should return "session_value"
```

### Performance Testing

```powershell
# Redis benchmark (if redis-cli available)
redis-cli --latency -h 127.0.0.1 -p 6379

# Laravel cache performance
php artisan tinker
Benchmark::dd([
    'Redis Cache' => fn() => Cache::put('bench', str_repeat('x', 1000)),
    'File Cache' => fn() => Cache::store('file')->put('bench', str_repeat('x', 1000)),
]);
```

## Integration with ICTServe Features

### Real-time Features (Laravel Reverb)

Redis is used for WebSocket scaling and message broadcasting:

```env
REVERB_SCALING_ENABLED=true
REVERB_REDIS_CONNECTION=reverb
```

### Background Jobs (Laravel Horizon)

Redis powers the queue system for background processing:

```env
QUEUE_CONNECTION=redis
HORIZON_REDIS_CONNECTION=horizon
```

### Performance Monitoring (Laravel Pulse)

Redis stores performance metrics:

```env
PULSE_REDIS_CONNECTION=pulse
```

### Session Management

Redis provides fast session storage:

```env
SESSION_DRIVER=redis
SESSION_CONNECTION=session
```

## Monitoring and Maintenance

### Daily Maintenance

```powershell
# Check Redis health
.\scripts\laragon\redis-health-check.ps1

# Monitor memory usage
redis-cli info memory

# Check connected clients
redis-cli info clients
```

### Weekly Maintenance

```powershell
# Clear expired keys
redis-cli --scan --pattern "*" | xargs redis-cli del

# Restart Redis for memory cleanup
# Through Laragon: Services > Restart Redis
```

### Performance Monitoring

```powershell
# Monitor slow queries
redis-cli config set slowlog-log-slower-than 1000
redis-cli slowlog get 10

# Monitor memory usage trends
redis-cli info memory | findstr used_memory_human
```

## Security Considerations

### Development Environment

```env
# No password required for local development
REDIS_PASSWORD=null

# Bind to localhost only
REDIS_HOST=127.0.0.1
```

### Production Environment

```env
# Use strong password
REDIS_PASSWORD=your_secure_password_here

# Consider SSL/TLS for remote connections
REDIS_SCHEME=tls
```

## Backup and Recovery

### Backup Redis Data

```powershell
# Create backup
redis-cli BGSAVE

# Copy RDB file
copy "C:\laragon\bin\redis\dump.rdb" "backup\redis-backup-$(Get-Date -Format 'yyyyMMdd').rdb"
```

### Restore Redis Data

```powershell
# Stop Redis
# Replace dump.rdb with backup
# Start Redis
```

## Advanced Configuration

### Custom Redis Configuration

Create `storage/redis/custom.conf`:

```conf
# ICTServe Custom Redis Configuration
include storage/redis/redis.conf

# Custom settings for ICTServe
maxmemory 512mb
maxmemory-policy volatile-lru

# Logging
loglevel notice
logfile storage/logs/redis.log

# Persistence
save 300 10
save 60 1000
```

### Multiple Redis Instances

For high-traffic scenarios, consider running multiple Redis instances:

```env
# Primary Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Cache-specific Redis
REDIS_CACHE_HOST=127.0.0.1
REDIS_CACHE_PORT=6380

# Session-specific Redis
REDIS_SESSION_HOST=127.0.0.1
REDIS_SESSION_PORT=6381
```

## Support and Resources

### Getting Help

1. Run the health check script: `.\scripts\laragon\redis-health-check.ps1 -Detailed`
2. Check Laravel logs: `storage/logs/laravel.log`
3. Check Redis logs: `storage/logs/redis.log`
4. Review Laragon documentation
5. Consult Redis documentation: https://redis.io/documentation

### Useful Commands

```powershell
# Redis CLI commands
redis-cli ping                    # Test connection
redis-cli info                    # Server information
redis-cli monitor                 # Monitor commands
redis-cli --scan                  # List all keys
redis-cli flushdb                 # Clear current database
redis-cli flushall                # Clear all databases

# Laravel commands
php artisan cache:clear           # Clear application cache
php artisan config:clear          # Clear configuration cache
php artisan queue:work            # Process queue jobs
php artisan horizon:status        # Check Horizon status
```

This guide ensures optimal Redis performance for your ICTServe system on Laragon. Follow the automated setup scripts for the best experience, and use the troubleshooting section when issues arise.