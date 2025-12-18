# ICTServe Horizon Production Deployment Checklist

## Pre-Deployment Verification

### System Requirements ✅

- [ ] PHP 8.2.12+ installed and configured
- [ ] Laravel 12.42.0 application deployed
- [ ] Redis 7.0+ server running and accessible
- [ ] Supervisor installed (Linux/Unix) or Task Scheduler configured (Windows)
- [ ] Minimum 512MB RAM available for queue workers
- [ ] 1GB disk space for logs and temporary files

### Laravel Horizon Installation ✅

- [ ] Laravel Horizon v5.41.0 package installed via Composer
- [ ] Horizon configuration published (`php artisan horizon:install`)
- [ ] HorizonServiceProvider registered in `bootstrap/providers.php`
- [ ] Horizon routes accessible at `/horizon`

### Configuration Verification ✅

- [ ] `config/horizon.php` configured with ICTServe queue supervisors
- [ ] Environment variables set in `.env` file
- [ ] Redis connection configured and tested
- [ ] Queue wait time thresholds configured (Requirement 23.5)
- [ ] Job retry policies configured with exponential backoff (Requirement 23.6)

## Deployment Steps

### 1. Pre-Deployment Backup

- [ ] Create database backup
- [ ] Backup current application files
- [ ] Backup existing supervisor configurations
- [ ] Document current Horizon status

### 2. Application Deployment

- [ ] Deploy latest application code
- [ ] Run `composer install --no-dev --optimize-autoloader`
- [ ] Clear application caches (`php artisan config:clear`, `php artisan route:clear`)
- [ ] Run database migrations if needed
- [ ] Update file permissions

### 3. Horizon Configuration Deployment

#### Linux/Unix Systems

- [ ] Copy supervisor configuration: `sudo cp deployment/supervisor/ictserve-horizon.conf /etc/supervisor/conf.d/`
- [ ] Update supervisor: `sudo supervisorctl reread && sudo supervisorctl update`
- [ ] Verify supervisor configuration: `sudo supervisorctl status ictserve-horizon`

#### Windows Systems

- [ ] Configure Task Scheduler for Horizon startup (optional)
- [ ] Verify PowerShell execution policy allows script execution
- [ ] Test PowerShell deployment script: `.\deployment\scripts\horizon-deploy.ps1 -Action health`

### 4. Horizon Deployment Execution

#### Automated Deployment (Recommended)

```bash
# Linux/Unix
sudo ./deployment/scripts/horizon-deploy.sh production deploy

# Windows (PowerShell as Administrator)
.\deployment\scripts\horizon-deploy.ps1 -Environment production -Action deploy
```

#### Manual Deployment Steps

- [ ] Stop existing Horizon processes: `php artisan horizon:terminate`
- [ ] Wait for graceful shutdown (up to 60 seconds)
- [ ] Start Horizon: `php artisan horizon` (or via supervisor)
- [ ] Verify Horizon status: `php artisan horizon:status`

### 5. Health Check Configuration

- [ ] Deploy health check endpoint: `public/horizon-health.php`
- [ ] Test health check endpoint: `curl http://your-domain.com/horizon-health.php`
- [ ] Configure load balancer health checks (if applicable)
- [ ] Set up monitoring system integration

## Post-Deployment Verification

### Functional Testing ✅

- [ ] Horizon dashboard accessible at `/horizon`
- [ ] Dashboard shows all configured supervisors
- [ ] Queue workers are processing jobs
- [ ] Failed jobs can be retried from dashboard
- [ ] Real-time metrics updating correctly

### Performance Testing ✅

- [ ] Dispatch test jobs to all queues
- [ ] Verify job processing times within SLA
- [ ] Monitor memory usage of worker processes
- [ ] Check CPU utilization under load
- [ ] Validate auto-scaling behavior

### Integration Testing ✅

- [ ] Test helpdesk ticket notifications
- [ ] Test asset loan approval workflows
- [ ] Test AI chatbot job processing
- [ ] Test report generation jobs
- [ ] Verify email notifications are sent

### Monitoring Setup ✅

- [ ] Configure automated health monitoring
- [ ] Set up alerting for queue wait times >60 seconds
- [ ] Set up alerting for failed jobs >10
- [ ] Configure supervisor process monitoring
- [ ] Integrate with Laravel Pulse metrics

## Security Verification

### Access Control ✅

- [ ] Horizon dashboard restricted to admin/superuser roles
- [ ] Health check endpoint accessible to monitoring systems
- [ ] Supervisor processes running as appropriate user (`www-data`)
- [ ] Log files have correct permissions
- [ ] No sensitive data exposed in job payloads

### Network Security ✅

- [ ] Redis connection secured (password/firewall)
- [ ] Horizon dashboard served over HTTPS
- [ ] Health check endpoint rate limited (if needed)
- [ ] Supervisor control socket secured

## Rollback Procedures

### Automated Rollback

```bash
# Linux/Unix
sudo ./deployment/scripts/horizon-deploy.sh production rollback

# Windows
.\deployment\scripts\horizon-deploy.ps1 -Environment production -Action rollback
```

### Manual Rollback Steps

- [ ] Stop current Horizon processes
- [ ] Restore previous supervisor configuration
- [ ] Restart supervisor processes
- [ ] Verify rollback successful
- [ ] Document rollback reason and resolution

## Monitoring and Alerting

### Key Metrics to Monitor ✅

- [ ] Queue wait times (alert if >60 seconds)
- [ ] Failed job count (alert if >10 jobs)
- [ ] Worker process count
- [ ] Memory usage per worker
- [ ] Supervisor process status
- [ ] Redis connection health

### Alert Configuration ✅

- [ ] Email alerts to `admin@motac.gov.my`
- [ ] Slack notifications (if configured)
- [ ] Integration with existing monitoring systems
- [ ] Escalation procedures documented

### Health Check Endpoints ✅

- [ ] Primary: `http://your-domain.com/horizon-health.php`
- [ ] Backup: `http://your-domain.com/storage/horizon-health-check.php`
- [ ] Dashboard: `http://your-domain.com/horizon`

## Maintenance Procedures

### Daily Maintenance ✅

- [ ] Check Horizon dashboard for failed jobs
- [ ] Review queue wait times and processing rates
- [ ] Monitor worker memory usage
- [ ] Check log files for errors

### Weekly Maintenance ✅

- [ ] Clear old failed jobs: `php artisan horizon:forget --all`
- [ ] Review and rotate log files
- [ ] Check supervisor configuration for updates
- [ ] Verify backup procedures

### Monthly Maintenance ✅

- [ ] Review queue performance metrics
- [ ] Update Horizon configuration if needed
- [ ] Test rollback procedures
- [ ] Update documentation

## Troubleshooting Guide

### Common Issues and Solutions

#### Horizon Not Starting

1. Check Redis connection: `php artisan tinker` → `Redis::ping()`
2. Verify supervisor configuration: `sudo supervisorctl status`
3. Check application logs: `tail -f storage/logs/laravel.log`
4. Restart supervisor: `sudo supervisorctl restart ictserve-horizon`

#### Jobs Not Processing

1. Check queue status: `php artisan queue:monitor redis:default --max=100`
2. Verify worker processes: `php artisan horizon:status`
3. Clear failed jobs: `php artisan horizon:forget --all`
4. Restart Horizon: `php artisan horizon:terminate`

#### High Memory Usage

1. Check memory limits in `config/horizon.php`
2. Monitor per-worker memory: `php artisan horizon:monitor-health`
3. Reduce max processes if needed
4. Restart workers: `php artisan horizon:terminate`

#### Dashboard Access Issues

1. Verify user roles: Check admin/superuser permissions
2. Check middleware configuration
3. Clear route cache: `php artisan route:clear`
4. Verify HTTPS configuration

### Log File Locations

- **Application**: `storage/logs/laravel.log`
- **Horizon**: `storage/logs/horizon.log`
- **Deployment**: `storage/logs/horizon-deploy.log`
- **Health Monitoring**: `storage/logs/horizon-health.log`
- **Supervisor**: `/var/log/supervisor/ictserve-horizon.log` (Linux)

## Success Criteria

### Deployment Successful When: ✅

- [ ] All health checks pass (HTTP 200 from health endpoint)
- [ ] Horizon dashboard accessible and showing active supervisors
- [ ] All configured queues processing jobs within SLA
- [ ] No failed jobs accumulating
- [ ] Monitoring and alerting functional
- [ ] Performance metrics within acceptable ranges

### Performance Targets ✅

- [ ] Queue wait times <60 seconds (per Requirement 23.5)
- [ ] Job processing rate >100 jobs/minute
- [ ] Worker memory usage <128MB per process
- [ ] Failed job rate <1% of total jobs
- [ ] Dashboard response time <2 seconds

## Sign-off

### Technical Verification ✅

- [ ] **System Administrator**: Infrastructure and supervisor configuration verified
- [ ] **DevOps Engineer**: Deployment scripts and monitoring configured
- [ ] **Application Developer**: Horizon integration and job processing verified
- [ ] **QA Engineer**: Functional and performance testing completed

### Business Approval ✅

- [ ] **ICT Manager**: Production deployment approved
- [ ] **BPM MOTAC**: System ready for operational use
- [ ] **Stakeholders**: Acceptance criteria met

---

**Deployment Date**: _______________  
**Deployed By**: _______________  
**Approved By**: _______________  
**Next Review Date**: _______________

**Document Version**: 1.0  
**Requirements**: 23.1, 23.4, 23.5, 23.6, 23.7, 23.8  
**Compliance**: D17 Queue Management Standards
