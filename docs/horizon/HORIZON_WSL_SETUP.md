# Laravel Horizon WSL Setup Guide

**Version**: ICTServe v3.6.1  
**Laravel Horizon**: v5.41.0  
**Environment**: Windows Subsystem for Linux (WSL 2) + Windows XAMPP  
**Last Updated**: December 20, 2025  
**Status**: Production Ready

---

## Overview

This guide documents the complete setup process for Laravel Horizon with WSL, including all issues encountered, solutions implemented, and configuration requirements. Laravel Horizon requires the `pcntl` and `posix` PHP extensions which are not available on Windows, making WSL the optimal solution for Windows development environments.

## Table of Contents

1. [Prerequisites](#prerequisites)
2. [Architecture Overview](#architecture-overview)
3. [Common Issues & Solutions](#common-issues--solutions)
4. [Step-by-Step Setup](#step-by-step-setup)
5. [Configuration Files](#configuration-files)
6. [Testing & Verification](#testing--verification)
7. [Production Deployment](#production-deployment)
8. [Troubleshooting](#troubleshooting)

---

## Prerequisites

### Required Components

- **WSL 2** with Ubuntu 24.04 LTS
- **PHP 8.2.12+** installed in WSL with required extensions
- **Redis** running (WSL or Windows accessible on 127.0.0.1:6379)
- **Laravel 12.43.1** with Horizon v5.41.0 installed
- **Predis** package for Redis connectivity

### PHP Extensions Required (WSL Only)

```bash
# Check required extensions in WSL
php -m | grep -E "(pcntl|posix|redis)"
```

Required extensions:

- `pcntl` - Process control (Unix/Linux only)
- `posix` - POSIX functions (Unix/Linux only)
- `redis` or Predis package for Redis connectivity

---

## Architecture Overview

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Windows Host  │    │   WSL Ubuntu    │    │   Redis Server  │
│                 │    │                 │    │                 │
│ Laravel App     │◄──►│ Horizon Daemon  │◄──►│ 127.0.0.1:6379 │
│ (XAMPP/Laragon) │    │ (PHP 8.2+pcntl) │    │ (WSL or Windows)│
│                 │    │                 │    │                 │
│ Queue Dispatch  │    │ Job Processing  │    │ Queue Storage   │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

**Key Points**:

- Laravel app runs on Windows (XAMPP/Laragon)
- Horizon daemon runs in WSL (requires pcntl/posix)
- Redis accessible from both environments
- Jobs dispatched from Windows, processed in WSL

---

## Common Issues & Solutions

### Issue 1: Missing pcntl/posix Extensions on Windows

**Error**:

```
Call to undefined function pcntl_signal()
```

**Root Cause**: Windows PHP doesn't include Unix-specific extensions.

**Solution**: Run Horizon in WSL where these extensions are available.

### Issue 2: Redis Client Configuration Conflicts

**Error**:

```
Class "Redis" not found
```

**Root Cause**: Multiple `.env` files with conflicting `REDIS_CLIENT` settings.

**Investigation Process**:

1. `.env` file had `REDIS_CLIENT=predis`
2. `.env.local` file had `REDIS_CLIENT=phpredis` (overriding)
3. Laravel loads `.env.local` after `.env`
4. `phpredis` extension not available on Windows

**Solution**:

```bash
# Check all .env files for REDIS_CLIENT
grep -r "REDIS_CLIENT" .env*

# Ensure consistent configuration
# .env and .env.local should both use:
REDIS_CLIENT=predis
```

### Issue 3: Queue Configuration Mismatch

**Error**: Jobs dispatched but not processed.

**Root Cause**: Horizon supervisors not configured for all queue names.

**Solution**: Update `config/horizon.php` to include all queues used by jobs.

### Issue 4: File Permission Issues in WSL

**Error**:

```
The stream or file "storage/logs/laravel.log" could not be opened: Permission denied
```

**Root Cause**: Cross-filesystem permissions between Windows and WSL.

**Workaround**: Acceptable for development; use proper Linux filesystem in production.

---

## Step-by-Step Setup

### Step 1: Install PHP 8.2 in WSL

```bash
# Update WSL Ubuntu
sudo apt update && sudo apt upgrade -y

# Install PHP 8.2 with required extensions
sudo apt install software-properties-common -y
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Install PHP 8.2 and extensions
sudo apt install php8.2 php8.2-cli php8.2-fpm php8.2-mysql php8.2-xml php8.2-curl \
                 php8.2-zip php8.2-mbstring php8.2-gd php8.2-redis php8.2-pcntl \
                 php8.2-posix composer -y

# Verify installation
php8.2 --version
php8.2 -m | grep -E "(pcntl|posix|redis)"
```

### Step 2: Configure Redis Client

**Critical**: Ensure consistent Redis client configuration across all environment files.

```bash
# Check all .env files
grep -r "REDIS_CLIENT" .env*

# Update all files to use predis
# .env
REDIS_CLIENT=predis

# .env.local (if exists)
REDIS_CLIENT=predis
```

**Why Predis?**

- Pure PHP implementation (no extension required)
- Works on both Windows and Linux
- Included with Laravel by default
- Better compatibility for cross-platform development

### Step 3: Update Queue Configuration

```bash
# Ensure queue uses Redis
# .env and .env.local
QUEUE_CONNECTION=redis
```

### Step 4: Configure Horizon Supervisors

Update `config/horizon.php` to include all queue names used by your jobs:

```php
'defaults' => [
    'supervisor-reports' => [
        'connection' => 'redis',
        'queue' => ['reports', 'exports', 'digests'], // Added 'digests'
        // ... other config
    ],
    // ... other supervisors
],
```

### Step 5: Clear Configuration Cache

```bash
# Clear all Laravel caches
php artisan optimize:clear
```

### Step 6: Start Horizon in WSL

```bash
# Start Horizon daemon
wsl.exe -e php8.2 /mnt/c/XAMPP/htdocs/ictserve-031125/artisan horizon

# Check status (in new terminal)
wsl.exe -e php8.2 /mnt/c/XAMPP/htdocs/ictserve-031125/artisan horizon:status
```

---

## Configuration Files

### Redis Configuration (`config/database.php`)

```php
'redis' => [
    'client' => env('REDIS_CLIENT', 'predis'), // Use predis for compatibility
    
    'options' => [
        'cluster' => env('REDIS_CLUSTER', 'redis'),
        'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel')) . '-database-'),
        'persistent' => env('REDIS_PERSISTENT', false),
    ],

    'default' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        // ... retry configuration
    ],

    'horizon' => [
        'url' => env('REDIS_URL'),
        'host' => env('REDIS_HOST', '127.0.0.1'),
        'username' => env('REDIS_USERNAME'),
        'password' => env('REDIS_PASSWORD'),
        'port' => env('REDIS_PORT', '6379'),
        'database' => env('REDIS_DB', '0'),
        'options' => [
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'))) . '_horizon:',
        ],
    ],
],
```

### Queue Configuration (`config/queue.php`)

```php
$redisSupported = static fn(): bool => 
    extension_loaded('redis') || class_exists(\Predis\Client::class);

$defaultQueueConnection = env('QUEUE_CONNECTION', 'database');

// Fallback to database if Redis not available
if ($defaultQueueConnection === 'redis' && !$redisSupported()) {
    $defaultQueueConnection = 'database';
}

return [
    'default' => $defaultQueueConnection,
    
    'connections' => [
        'redis' => [
            'driver' => 'redis',
            'connection' => env('REDIS_QUEUE_CONNECTION', 'default'),
            'queue' => env('REDIS_QUEUE', 'default'),
            'retry_after' => (int) env('REDIS_QUEUE_RETRY_AFTER', 90),
            'block_for' => null,
            'after_commit' => false,
        ],
        // ... other connections
    ],
];
```

### Horizon Configuration (`config/horizon.php`)

```php
return [
    'name' => env('HORIZON_NAME', 'ICTServe'),
    'domain' => env('HORIZON_DOMAIN'),
    'path' => env('HORIZON_PATH', 'horizon'),
    'use' => 'default',
    'prefix' => env('HORIZON_PREFIX', Str::slug(env('APP_NAME', 'ictserve'), '_') . '_horizon:'),
    'middleware' => ['web'],

    'waits' => [
        'redis:default' => 60,
        'redis:helpdesk' => 60,
        'redis:notifications' => 30,
        'redis:asset-loan' => 120,
        'redis:approvals' => 300,
        'redis:ai-chatbot' => 600,
        'redis:reports' => 300,
    ],

    'environments' => [
        'local' => [
            'supervisor-default' => [
                'connection' => 'redis',
                'queue' => ['default'],
                'balance' => 'simple',
                'maxProcesses' => 3,
                'tries' => 3,
                'timeout' => 60,
            ],
            'supervisor-helpdesk' => [
                'connection' => 'redis',
                'queue' => ['helpdesk', 'notifications'],
                'balance' => 'auto',
                'minProcesses' => 1,
                'maxProcesses' => 3,
                'tries' => 3,
                'timeout' => 300,
            ],
            'supervisor-reports' => [
                'connection' => 'redis',
                'queue' => ['reports', 'exports', 'digests'],
                'balance' => 'simple',
                'minProcesses' => 1,
                'maxProcesses' => 1,
                'tries' => 3,
                'timeout' => 600,
            ],
        ],
    ],
];
```

### Environment Variables

**Required in both `.env` and `.env.local`**:

```env
# Queue Configuration
QUEUE_CONNECTION=redis

# Redis Configuration (CRITICAL)
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null

# Horizon Configuration
HORIZON_NAME=ICTServe
HORIZON_PATH=horizon
```

---

## Testing & Verification

### Test Redis Connection

```php
// In tinker: php artisan tinker
use Illuminate\Support\Facades\Redis;

// Test Redis connection
Redis::ping(); // Should return {}

// Test queue dispatch
dispatch(new \App\Jobs\ProcessNotificationDigest(['frequency' => 'daily']));

// Check queue size
Redis::connection('default')->llen('queues:digests');
```

### Test Horizon Status

```bash
# Check if Horizon is running
wsl.exe -e php8.2 /path/to/project/artisan horizon:status

# Expected output: "Horizon is running."
```

### Test Job Processing

```bash
# Process jobs manually (alternative to Horizon)
php artisan queue:work redis --queue=digests --once

# Expected output: Job processed successfully with timing
```

### Access Horizon Dashboard

- URL: <http://127.0.0.1:8000/horizon>
- Requires authentication (admin/superuser roles)
- Shows real-time job processing, failed jobs, and metrics

---

## Production Deployment

### Supervisor Configuration

Create `/etc/supervisor/conf.d/ictserve-horizon.conf`:

```ini
[program:ictserve-horizon]
process_name=%(program_name)s
command=php /var/www/ictserve/artisan horizon
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
redirect_stderr=true
stdout_logfile=/var/www/ictserve/storage/logs/horizon.log
stopwaitsecs=3600
```

### Deployment Commands

```bash
# During deployment
php artisan horizon:terminate
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start ictserve-horizon

# Verify status
sudo supervisorctl status ictserve-horizon
php artisan horizon:status
```

### Monitoring & Alerts

```bash
# Set up monitoring
php artisan horizon:snapshot

# Configure alerts in config/horizon.php
'waits' => [
    'redis:default' => 60,        // Alert if jobs wait > 60 seconds
    'redis:helpdesk' => 60,       // Critical queue
    'redis:notifications' => 30,   // Fast processing required
],
```

---

## Troubleshooting

### Horizon Won't Start

**Check PHP Extensions**:

```bash
wsl.exe -e php8.2 -m | grep -E "(pcntl|posix)"
```

**Check Redis Connection**:

```bash
wsl.exe -e redis-cli ping
```

**Check Configuration**:

```bash
php artisan config:show horizon
php artisan config:show queue
```

### Jobs Not Processing

**Check Queue Names**:

```php
// Verify job queue assignment
class YourJob implements ShouldQueue
{
    public function __construct()
    {
        $this->onQueue('your-queue-name'); // Must match Horizon config
    }
}
```

**Check Supervisor Configuration**:

```php
// In config/horizon.php, ensure queue is listed
'supervisor-name' => [
    'queue' => ['your-queue-name'], // Must include your queue
],
```

### Redis Connection Issues

**Check Client Configuration**:

```bash
grep -r "REDIS_CLIENT" .env*
# All files should show: REDIS_CLIENT=predis
```

**Test Direct Connection**:

```php
$predis = new \Predis\Client(['host' => '127.0.0.1', 'port' => 6379]);
$predis->ping(); // Should work
```

### Permission Issues

**WSL File Permissions**:

```bash
# In WSL, fix permissions if needed
sudo chown -R www-data:www-data /var/www/ictserve/storage
sudo chmod -R 775 /var/www/ictserve/storage
```

**Cross-Platform Development**:

- Use WSL filesystem for production-like setup
- Windows filesystem acceptable for development
- Consider Docker for team consistency

---

## Performance Considerations

### Memory Usage

```php
// Monitor memory usage
'memory_limit' => 128, // MB per supervisor process
```

### Process Scaling

```php
// Auto-scaling configuration
'balance' => 'auto',
'autoScalingStrategy' => 'time', // or 'size'
'minProcesses' => 1,
'maxProcesses' => 4,
'balanceMaxShift' => 1,
'balanceCooldown' => 3,
```

### Queue Prioritization

```php
// Separate supervisors for different priorities
'supervisor-critical' => [
    'queue' => ['notifications'],
    'maxProcesses' => 4,
    'timeout' => 30,
],
'supervisor-normal' => [
    'queue' => ['reports', 'exports'],
    'maxProcesses' => 2,
    'timeout' => 600,
],
```

---

## Security Considerations

### Dashboard Access

```php
// In app/Providers/HorizonServiceProvider.php
protected function gate(): void
{
    Gate::define('viewHorizon', function (?User $user) {
        return $user?->hasRole(['superuser', 'admin']) ?? false;
    });
}
```

### Redis Security

```env
# Use password in production
REDIS_PASSWORD=your-secure-password

# Restrict network access
REDIS_HOST=127.0.0.1  # Localhost only
```

### Job Data Security

```php
// Avoid storing sensitive data in job payloads
class SecureJob implements ShouldQueue
{
    public function __construct(
        private int $userId,     // Store ID, not sensitive data
        private string $action   // Store action, not credentials
    ) {}
}
```

---

## Resources

- **Laravel Horizon Documentation**: <https://laravel.com/docs/12.x/horizon>
- **Laravel Queue Documentation**: <https://laravel.com/docs/12.x/queues>
- **Redis Documentation**: <https://redis.io/documentation>
- **WSL Documentation**: <https://docs.microsoft.com/en-us/windows/wsl/>
- **ICTServe Queue Management**: [D17_QUEUE_MANAGEMENT_HORIZON.md](../D17_QUEUE_MANAGEMENT_HORIZON.md)

---

**Last Updated**: December 20, 2025  
**Maintained By**: ICTServe Development Team  
**Support**: See [TROUBLESHOOTING.md](../TROUBLESHOOTING.md) for additional help
