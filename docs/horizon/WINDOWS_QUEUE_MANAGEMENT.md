# Windows Queue Management for ICTServe

## Overview

Laravel Horizon requires the `pcntl` PHP extension for process control, which is **not available on Windows**. This document explains the Windows-compatible queue management solution for ICTServe development.

## The Problem

When trying to run `php artisan horizon` on Windows, you'll encounter this error:

```
Call to undefined function Laravel\Horizon\Console\pcntl_async_signals()
```

This is because:

- Horizon uses Unix process control functions (`pcntl_*`)
- These functions are not available in PHP on Windows
- There is no workaround to make Horizon work natively on Windows

## The Solution

ICTServe provides a Windows-compatible queue management script that uses multiple `queue:work` processes instead of Horizon.

### Quick Start

```powershell
# Start queue workers
.\scripts\dev\start-queue-workers.ps1 -Action start

# Check status
.\scripts\dev\start-queue-workers.ps1 -Action status

# Stop workers
.\scripts\dev\start-queue-workers.ps1 -Action stop

# Restart workers
.\scripts\dev\start-queue-workers.ps1 -Action restart
```

### What It Does

The script starts multiple PHP queue worker processes, each handling specific queues:

1. **Default Worker** (1 process)
   - Queue: `default`
   - Timeout: 60s
   - Memory: 128MB

2. **Helpdesk Workers** (2 processes)
   - Queues: `helpdesk`, `notifications`
   - Timeout: 300s (5 minutes)
   - Memory: 128MB

3. **Asset Loan Worker** (1 process)
   - Queues: `asset-loan`, `approvals`
   - Timeout: 180s (3 minutes)
   - Memory: 128MB

4. **AI Chatbot Worker** (1 process)
   - Queues: `ai-chatbot`, `document-processing`
   - Timeout: 600s (10 minutes)
   - Memory: 256MB

5. **Reports Worker** (1 process)
   - Queues: `reports`, `exports`
   - Timeout: 600s (10 minutes)
   - Memory: 256MB

### Monitoring Queues

```powershell
# Monitor specific queues
php artisan queue:monitor redis:default,redis:helpdesk,redis:asset-loan

# Check failed jobs
php artisan queue:failed

# Retry failed jobs
php artisan queue:retry all
```

### Development Workflow

The main development script (`scripts/dev/start-dev.ps1`) automatically uses the Windows-compatible queue management when you start the full development environment:

```powershell
.\scripts\dev\start-dev.ps1 -Profile full
```

## Differences from Horizon

### What You Lose

1. **Web Dashboard**: No Horizon web interface at `/horizon`
2. **Auto-scaling**: No automatic process scaling based on queue load
3. **Metrics**: No built-in metrics and monitoring dashboard
4. **Supervisor Management**: No centralized supervisor control

### What You Keep

1. **Queue Processing**: All queues work normally
2. **Job Retries**: Retry logic with exponential backoff
3. **Failed Jobs**: Failed job tracking and retry
4. **Multiple Queues**: Support for all ICTServe queues
5. **Priority**: Queue priority through multiple workers

## Production Deployment

**Important**: On production Linux servers, use Laravel Horizon as intended:

```bash
# Production (Linux)
php artisan horizon

# With Supervisor
[program:ictserve-horizon]
command=php /var/www/ictserve/artisan horizon
directory=/var/www/ictserve
autostart=true
autorestart=true
user=www-data
```

## Alternative: WSL2 with Horizon

If you need Horizon features during development, you can run it in WSL2:

```bash
# In WSL2 terminal
cd /mnt/c/path/to/ictserve
php artisan horizon
```

However, this requires:

- WSL2 installed and configured
- PHP and all extensions installed in WSL2
- Redis accessible from WSL2
- Proper file permissions

The Windows-compatible queue worker script is simpler and recommended for most development scenarios.

## Troubleshooting

### Workers Not Starting

```powershell
# Check if PHP is in PATH
php --version

# Check if Redis is running
Test-NetConnection -ComputerName 127.0.0.1 -Port 6379

# Check for existing workers
Get-Process | Where-Object { $_.ProcessName -eq "php" -and $_.CommandLine -like "*queue:work*" }
```

### Workers Not Processing Jobs

```powershell
# Check queue status
php artisan queue:monitor redis:default

# Check for failed jobs
php artisan queue:failed

# Restart workers
.\scripts\dev\start-queue-workers.ps1 -Action restart
```

### High Memory Usage

If workers consume too much memory, adjust the memory limits in `scripts/dev/start-queue-workers.ps1`:

```powershell
$WorkerProcesses = @{
    "helpdesk" = @{
        "memory" = 128  # Reduce if needed
    }
}
```

## References

- [Laravel Queues Documentation](https://laravel.com/docs/12.x/queues)
- [Laravel Horizon Documentation](https://laravel.com/docs/12.x/horizon)
- [ICTServe Queue Configuration](../config/horizon.php)
- [ICTServe Development Guide](./QUICK_START.md)
