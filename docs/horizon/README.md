# Laravel Horizon Documentation

**Laravel Horizon v5.41.0** - Queue Management Dashboard for ICTServe v3.6.1

---

## Overview

Laravel Horizon provides a beautiful dashboard and code-driven configuration for Redis queues in ICTServe. This directory contains comprehensive documentation for setting up and managing Horizon in various environments.

## Documentation Files

### Setup Guides

- **[HORIZON_WSL_SETUP.md](HORIZON_WSL_SETUP.md)** - Complete WSL setup guide
  - PHP 8.2 installation with required extensions (pcntl, posix)
  - Redis client configuration (Predis vs phpredis)
  - Queue supervisor configuration
  - Production deployment with Supervisor
  - Comprehensive troubleshooting

### Key Features

- **Real-time Queue Monitoring**: Live dashboard showing job processing
- **Failed Job Management**: Retry and inspect failed jobs
- **Performance Metrics**: Job throughput, wait times, and processing statistics
- **Auto-scaling**: Dynamic process scaling based on queue load
- **Multi-environment Support**: Local, staging, and production configurations

## Quick Start

### Prerequisites

- **WSL 2** with Ubuntu 24.04 LTS (Windows requirement)
- **PHP 8.2+** with `pcntl` and `posix` extensions (WSL only)
- **Redis** server running and accessible
- **Predis** package for Redis connectivity

### Installation Status

✅ **Already Installed**: Horizon is pre-installed in ICTServe v3.6.1

### Configuration

```env
# Required environment variables
QUEUE_CONNECTION=redis
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### Start Horizon

```bash
# In WSL (required for pcntl/posix extensions)
wsl.exe -e php8.2 /path/to/ictserve/artisan horizon

# Check status
wsl.exe -e php8.2 /path/to/ictserve/artisan horizon:status
```

### Access Dashboard

- **URL**: <http://127.0.0.1:8000/horizon>
- **Authentication**: Requires admin or superuser role
- **Features**: Real-time job monitoring, failed job management, metrics

## Architecture

### ICTServe Queue Structure

```
┌─────────────────┐    ┌─────────────────┐    ┌─────────────────┐
│   Windows Host  │    │   WSL Ubuntu    │    │   Redis Server  │
│                 │    │                 │    │                 │
│ Laravel App     │◄──►│ Horizon Daemon  │◄──►│ 127.0.0.1:6379 │
│ (Dispatch Jobs) │    │ (Process Jobs)  │    │ (Queue Storage) │
└─────────────────┘    └─────────────────┘    └─────────────────┘
```

### Queue Supervisors

- **supervisor-helpdesk**: Ticket notifications, SLA alerts
- **supervisor-asset-loan**: Approval workflows, reminders
- **supervisor-ai**: AI processing, document analysis
- **supervisor-reports**: Scheduled reports, data exports

## Common Issues & Solutions

### Issue: "Call to undefined function pcntl_signal()"

**Solution**: Use WSL - Windows PHP doesn't include Unix extensions.

### Issue: "Class 'Redis' not found"

**Solution**: Use `REDIS_CLIENT=predis` in all `.env` files.

### Issue: Jobs dispatched but not processed

**Solution**: Check Horizon supervisor configuration includes your queue names.

## Production Deployment

### Supervisor Configuration

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
```

## Resources

### Internal Documentation

- **Queue Management**: [../D17_QUEUE_MANAGEMENT_HORIZON.md](../D17_QUEUE_MANAGEMENT_HORIZON.md)
- **Redis Setup**: [../redis/WSL_SETUP.md](../redis/WSL_SETUP.md)
- **System Architecture**: [../D00_SYSTEM_OVERVIEW.md](../D00_SYSTEM_OVERVIEW.md)

### External Links

- **Laravel Horizon Docs**: <https://laravel.com/docs/12.x/horizon>
- **Laravel Queue Docs**: <https://laravel.com/docs/12.x/queues>
- **Redis Documentation**: <https://redis.io/documentation>
- **WSL Documentation**: <https://docs.microsoft.com/en-us/windows/wsl/>

---

**Last Updated**: December 20, 2025  
**Maintained By**: ICTServe Development Team  
**Support**: See [HORIZON_WSL_SETUP.md](HORIZON_WSL_SETUP.md) for detailed troubleshooting
