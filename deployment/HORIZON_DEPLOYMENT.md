# ICTServe Laravel Horizon Deployment Guide

## Overview

This guide provides comprehensive instructions for deploying Laravel Horizon v5.41.0 in the ICTServe system for queue management and monitoring.

**Requirements Addressed**: 23.1, 23.4, 23.5, 23.6, 23.7, 23.8

## Prerequisites

### System Requirements

- **PHP**: 8.2.12+
- **Laravel**: 12.42.0
- **Redis**: 7.0+ (for queue backend)
- **Supervisor**: Latest (Linux/Unix) or Task Scheduler (Windows)
- **Memory**: Minimum 512MB available for queue workers
- **Disk Space**: 1GB for logs and temporary files

### ICTServe Queue Configuration

Horizon manages the following ICTServe queues:

| Queue | Purpose | Priority | Workers |
|-------|---------|----------|---------|
| `helpdesk` | Ticket notifications, SLA alerts | High | 2-8 |
| `notifications` | Email/WebSocket notifications | High | 1-4 |
| `asset-loan` | Loan approval workflows | Medium | 1-4 |
| `approvals` | Grade 41+ approval emails | Medium | 1-4 |
| `ai-chatbot` | AI processing, document analysis | Low | 1-2 |
| `reports` | Scheduled reports, exports | Low | 1-2 |
| `default` | General system jobs | Medium | 1-3 |

## Deployment Steps

### 1. Pre-Deployment Verification

```bash
# Check Laravel Horizon installation
php artisan horizon:install --verify

# Verify Redis connection
php artisan tinker
>>> Redis::ping()
=> "PONG"

# Check queue configuration
php artisan config:show horizon
```

### 2. Linux/Unix Deployment

#### Step 2.1: Install Supervisor Configuration

```bash
# Copy supervisor configuration
sudo cp deployment/supervisor/ictserve-horizon.conf /etc/supervisor/conf.d/

# Update supervisor
sudo supervisorctl reread
sudo supervisorctl update

# Verify configuration
sudo supervisorctl status ictserve-horizon
```

#### Step 2.2: Deploy Using Script

```bash
# Make script executable
chmod +x deployment/scripts/horizon-deploy.sh

# Deploy to production
sudo ./deployment/scripts/horizon-deploy.sh production deploy

# Verify deployment
./deployment/scripts/horizon-deploy.sh production health
```

### 3. Windows Deployment

#### Step 3.1: Deploy Using PowerShell Script

```powershell
# Run as Administrator
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Deploy Horizon
.\deployment\scripts\horizon-deploy.ps1 -Environment production -Action deploy

# Verify deployment
.\deployment\scripts\horizon-deploy.ps1 -Environment production -Action health
```

#### Step 3.2: Configure Task Scheduler (Optional)

For automatic startup on Windows:

1. Open Task Scheduler
2. Create Basic Task: "ICTServe Horizon"
3. Trigger: At startup
4. Action: Start program
   - Program: `php`
   - Arguments: `artisan horizon`
   - Start in: `C:\xampp\htdocs\ictserve-031125`

### 4. Post-Deployment Verification

#### Step 4.1: Health Checks

```bash
# Check Horizon status
php artisan horizon:status

# Monitor queue metrics
php artisan horizon:monitor-health

# View dashboard
curl http://your-domain.com/horizon
```

#### Step 4.2: Test Queue Processing

```bash
# Dispatch test job
php artisan tinker
>>> App\Jobs\SendNotificationJob::dispatch('test', ['message' => 'Test notification']);

# Monitor processing
php artisan queue:monitor redis:default,redis:helpdesk --max=100
```

## Configuration Files

### Horizon Configuration (`config/horizon.php`)

Key configuration sections:

```php
// Queue wait time thresholds (Requirement 23.5)
'waits' => [
    'redis:helpdesk' => 60,        // 1 minute
    'redis:notifications' => 30,   // 30 seconds
    'redis:asset-loan' => 120,     // 2 minutes
    'redis:ai-chatbot' => 600,     // 10 minutes
],

// Supervisor configuration (Requirement 23.2, 23.3)
'environments' => [
    'production' => [
        'supervisor-helpdesk' => [
            'connection' => 'redis',
            'queue' => ['helpdesk', 'notifications'],
            'balance' => 'auto',
            'minProcesses' => 2,
            'maxProcesses' => 8,
            'tries' => 3,
            'timeout' => 300,
            'backoff' => [10, 30, 60], // Exponential backoff
        ],
        // ... other supervisors
    ],
],
```

### Environment Variables

Add to `.env`:

```env
# Horizon Configuration
HORIZON_NAME=ICTServe
HORIZON_PATH=horizon
HORIZON_NOTIFICATION_EMAIL=admin@motac.gov.my

# Queue Configuration
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379
```

## Monitoring and Alerting

### Automated Health Monitoring

Horizon health is monitored automatically:

```bash
# Scheduled every 10 minutes via routes/console.php
Schedule::command('horizon:monitor-health --alert')
    ->everyTenMinutes()
    ->withoutOverlapping();
```

### Alert Thresholds (Requirement 23.5)

| Metric | Threshold | Action |
|--------|-----------|--------|
| Queue wait time | >60 seconds | Email alert |
| Failed jobs | >10 jobs | Email alert |
| Supervisor down | Any supervisor | Immediate alert |
| Worker processes | <80% active | Warning alert |

### Dashboard Access (Requirement 23.1)

- **URL**: `/horizon`
- **Access**: Admin and Superuser roles only
- **Features**: Real-time metrics, job monitoring, failed job management

## Job Management

### Job Tagging (Requirement 23.7)

All ICTServe jobs implement proper tagging:

```php
public function tags(): array
{
    return [
        'module-name',           // helpdesk, asset-loan, ai-chatbot
        'priority-level',        // high, medium, low
        'user:' . $this->userId, // User identification
    ];
}
```

### Retry Policies (Requirement 23.6)

- **Attempts**: 3 maximum retries
- **Backoff**: Exponential (10s, 30s, 60s)
- **Timeout**: Varies by job type (60s-600s)

### Failed Job Management

```bash
# View failed jobs
php artisan horizon:failed

# Retry specific failed job
php artisan horizon:retry 5

# Clear all failed jobs
php artisan horizon:forget --all

# Automated cleanup (weekly)
Schedule::command('horizon:forget --all')
    ->weeklyOn(1, '02:00');
```

## Integration with Laravel Pulse (Requirement 23.8)

Horizon metrics are integrated with Laravel Pulse:

```php
// Automatic metrics collection
Schedule::command('horizon:snapshot')
    ->everyFiveMinutes();

// Custom ICTServe metrics
$monitoring = app(HorizonMonitoringService::class);
$metrics = $monitoring->getMetricsForPulse();
```

## Troubleshooting

### Common Issues

#### 1. Horizon Not Starting

```bash
# Check Redis connection
php artisan tinker
>>> Redis::ping()

# Check supervisor logs
sudo tail -f /var/log/supervisor/supervisord.log

# Restart supervisor
sudo supervisorctl restart ictserve-horizon
```

#### 2. Jobs Not Processing

```bash
# Check queue status
php artisan queue:monitor redis:default --max=100

# Check worker processes
php artisan horizon:status

# Clear failed jobs
php artisan horizon:forget --all
```

#### 3. High Memory Usage

```bash
# Check memory limits in config/horizon.php
'memory' => 128, // MB per worker

# Monitor memory usage
php artisan horizon:monitor-health

# Restart workers
php artisan horizon:terminate
```

### Log Files

- **Horizon**: `storage/logs/horizon.log`
- **Deployment**: `storage/logs/horizon-deploy.log`
- **Health Monitoring**: `storage/logs/horizon-health.log`
- **Supervisor**: `/var/log/supervisor/ictserve-horizon.log` (Linux)

## Performance Optimization

### Production Settings

```php
// config/horizon.php
'fast_termination' => true,
'memory_limit' => 128,

// Supervisor configuration
'maxProcesses' => 8,        // Scale based on server capacity
'balanceMaxShift' => 1,     // Conservative scaling
'balanceCooldown' => 3,     // 3-minute cooldown
```

### Scaling Guidelines

| Server Specs | Max Workers | Memory Allocation |
|--------------|-------------|-------------------|
| 2 CPU, 4GB RAM | 4-6 workers | 64MB per worker |
| 4 CPU, 8GB RAM | 8-12 workers | 128MB per worker |
| 8 CPU, 16GB RAM | 16-24 workers | 256MB per worker |

## Security Considerations

### Dashboard Access

- Restricted to admin/superuser roles
- HTTPS required in production
- Rate limiting enabled

### Process Security

- Run as `www-data` user (Linux)
- Limited file system access
- No shell command execution in jobs

## Maintenance

### Regular Tasks

```bash
# Weekly failed job cleanup
php artisan horizon:forget --all

# Monthly log rotation
logrotate /etc/logrotate.d/ictserve-horizon

# Quarterly configuration review
php artisan config:show horizon
```

### Updates

1. Stop Horizon: `php artisan horizon:terminate`
2. Update Laravel/Horizon: `composer update`
3. Update configuration if needed
4. Restart Horizon: `supervisorctl restart ictserve-horizon`

## Support

For issues with Horizon deployment:

1. Check logs in `storage/logs/horizon*.log`
2. Verify configuration with `php artisan config:show horizon`
3. Test health endpoint: `/storage/horizon-health-check.php`
4. Contact ICTServe system administrators

---

**Document Version**: 1.0  
**Last Updated**: December 17, 2025  
**Compliance**: Requirements 23.1-23.8, D17 Queue Management Standards
