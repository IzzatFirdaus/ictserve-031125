# Redis Setup Guide for ICTServe on Laragon

## Overview

This guide provides the optimal Redis configuration for ICTServe v3.6.1 running on Laragon. The configuration uses **Predis** as the Redis client for maximum compatibility with Windows and Laragon environments.

## Quick Setup

### 1. Run the Optimization Script

```powershell
# Navigate to your ICTServe project
cd c:\laragon\www\ictserve-031125

# Run the Redis optimization script
.\scripts\laragon\optimize-redis-laragon.ps1 -Force

# Run health check to verify setup
.\scripts\laragon\redis-health-check.ps1 -Fix -Detailed
```

### 2. Verify Installation

```powershell
# Check if Redis is running
.\scripts\laragon\redis-health-check.ps1

# Test Laravel Redis integration
php artisan tinker --execute="echo Redis::ping();"
```

## Manual Configuration

### 1. Install Predis Package

```bash
composer require predis/predis
```

### 2. Configure Environment Variables

Add these settings to your `.env` file:

```env
# Redis Client Configuration - CRITICAL for Laragon
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Redis Database Allocation for ICTServe v3.6.1
REDIS_DB=0
REDIS_CACHE_DB=1
REDIS_SESSION_DB=2
REDIS_QUEUE_DB=3
REDIS_REVERB_DB=4
REDIS_PULSE_DB=5
REDIS_HORIZON_DB=6

# Redis Connection Optimization
REDIS_MAX_RETRIES=3
REDIS_BACKOFF_ALGORITHM=decorrelated_jitter
REDIS_BACKOFF_BASE=100
REDIS_BACKOFF_CAP=1000
REDIS_PERSISTENT=false
REDIS_PREFIX=ictserve-database-

# Cache Configuration
CACHE_STORE=redis
CACHE_PREFIX=ictserve_cache

# Session Configuration
SESSION_DRIVER=redis
SESSION_LIFETIME=7200

# Queue Configuration
QUEUE_CONNECTION=redis
```

### 3. Install Redis in Laragon

#### Option A: Laragon Quick Add (Recommended)
1. Open Laragon
2. Right-click on Laragon tray icon
3. Go to **Quick Add** > **Redis**
4. Select and install Redis

#### Option B: Manual Installation
1. Download Redis from: https://github.com/microsoftarchive/redis/releases
2. Extract to `C:\laragon\bin\redis\`
3. Start Redis manually or through Laragon

## Database Allocation

ICTServe v3.6.1 uses multiple Redis databases for optimal performance:

| Database | Purpose | Configuration |
|----------|---------|---------------|
| DB 0 | Default/General | `REDIS_DB=0` |
| DB 1 | Cache | `REDIS_CACHE_DB=1` |
| DB 2 | Sessions | `REDIS_SESSION_DB=2` |
| DB 3 | Queues | `REDIS_QUEUE_DB=3` |
| DB 4 | Laravel Reverb (WebSocket) | `REDIS_REVERB_DB=4` |
| DB 5 | Laravel Pulse (Monitoring) | `REDIS_PULSE_DB=5` |
| DB 6 | Laravel Horizon (Queue Management) | `REDIS_HORIZON_DB=6` |

## Why Predis for Laragon?

### Advantages of Predis on Windows/Laragon:

1. **Pure PHP Implementation**: No C extensions required
2. **Windows Compatibility**: Better compatibility with Windows environments
3. **Laragon Integration**: Works seamlessly with Laragon's PHP versions
4. **Easy Installation**: No compilation or extension configuration needed
5. **Debugging**: Easier to debug connection issues

### Performance Considerations:

- **Development**: Predis is perfect for development environments
- **Production**: Consider phpredis extension for production if available
- **Memory Usage**: Slightly higher memory usage than phpredis
- **Speed**: Adequate performance for most applications

## Troubleshooting

### Common Issues and Solutions

#### 1. Redis Connection Failed

```powershell
# Check if Redis is running
.\scripts\laragon\redis-health-check.ps1

# Start Redis manually
.\scripts\laragon\start-laragon.ps1
```

#### 2. Predis Package Missing

```bash
# Install Predis
composer require predis/predis

# Verify installation
composer show predis/predis
```

#### 3. Laravel Cache Issues

```bash
# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

#### 4. Session Issues

```bash
# Clear sessions
php artisan session:clear

# Verify session configuration
php artisan config:show session
```

### Health Check Commands

```powershell
# Comprehensive health check
.\scripts\laragon\redis-health-check.ps1 -Fix -Detailed

# Test Redis connection
redis-cli ping

# Check Redis info
redis-cli info

# Monitor Redis commands
redis-cli monitor
```

## Performance Optimization

### Redis Configuration Tuning

Create `storage/redis/redis.conf`:

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

# Slow Log
slowlog-log-slower-than 10000
slowlog-max-len 128
```

### Laravel Configuration

Ensure `config/database.php` has optimal settings:

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'),
    
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel')).'-database-'),
        'persistent' => env('REDIS_PERSISTENT', false),
        'serialization' => 'php',
        'compression' => 'none',
    ],
    
    // ... database configurations
],
```

## Integration with ICTServe Features

### Laravel Horizon (Queue Management)

```bash
# Start Horizon
php artisan horizon

# Monitor queues
php artisan horizon:status
```

### Laravel Pulse (Performance Monitoring)

```bash
# View performance metrics
# Visit: http://ictserve.local/pulse
```

### Laravel Reverb (WebSocket)

```bash
# Start Reverb server
php artisan reverb:start

# Test WebSocket connection
# Visit: http://ictserve.local (real-time features)
```

## Security Considerations

### Development Environment

- Redis password not required for local development
- Bind to 127.0.0.1 only (localhost)
- Use default port 6379

### Production Environment

```env
# Production Redis security
REDIS_PASSWORD=your-secure-password
REDIS_HOST=your-redis-server
REDIS_PORT=6379
```

## Monitoring and Maintenance

### Regular Maintenance

```bash
# Check Redis memory usage
redis-cli info memory

# Check connected clients
redis-cli info clients

# Monitor slow queries
redis-cli slowlog get 10
```

### Performance Monitoring

```bash
# Real-time monitoring
redis-cli --latency

# Continuous stats
redis-cli --stat
```

## Support and Resources

### Documentation Links

- [Laravel Redis Documentation](https://laravel.com/docs/redis)
- [Predis Documentation](https://github.com/predis/predis)
- [Redis Documentation](https://redis.io/documentation)
- [Laragon Documentation](https://laragon.org/docs/)

### ICTServe Specific Scripts

- `scripts/laragon/optimize-redis-laragon.ps1` - Redis optimization
- `scripts/laragon/redis-health-check.ps1` - Health checking
- `scripts/laragon/setup-laragon.ps1` - Complete Laragon setup

### Getting Help

1. Run health check script for diagnostics
2. Check Laragon Redis service status
3. Verify environment configuration
4. Test Redis connection manually
5. Review Laravel logs for Redis errors

---

**Last Updated**: December 19, 2025  
**Version**: ICTServe v3.6.1  
**Environment**: Laragon Development