# ICTServe v3.6.1 - XAMPP Environment Setup Guide

## Overview

This guide provides step-by-step instructions for migrating ICTServe v3.6.1 from Docker to a XAMPP environment using:

- **XAMPP**: MySQL 8.0+ and Apache (Windows native)
- **WSL Redis**: Redis 7.0+ in Windows Subsystem for Linux
- **Laravel Services**: All existing functionality maintained

## Prerequisites

### System Requirements

- **Windows 10/11** with Administrator privileges
- **WSL 2** (Windows Subsystem for Linux)
- **XAMPP** with MySQL 8.0+ and Apache
- **PHP 8.2.12** (included with XAMPP or separate installation)
- **Composer** for Laravel dependency management
- **Node.js 18+** and npm for frontend assets

### Installation Steps

#### 1. Install WSL 2

```powershell
# Run as Administrator
wsl --install
# Restart computer when prompted
```

#### 2. Install XAMPP

1. Download XAMPP from: <https://www.apachefriends.org/download.html>
2. Install to default location: `C:\xampp`
3. Ensure MySQL and Apache components are selected

#### 3. Install Redis in WSL

```bash
# In WSL terminal
sudo apt update
sudo apt install redis-server
```

## Quick Start

### 1. Validate Prerequisites

```bash
# Check current environment status
php artisan ict:setup-xampp --status

# Validate all prerequisites
php artisan ict:setup-xampp --validate
```

### 2. Setup XAMPP Services

```powershell
# Start XAMPP MySQL and Apache
.\scripts\xampp\manage-xampp.ps1 -Action start

# Start WSL Redis
.\scripts\wsl\manage-redis.ps1 -Action start
```

### 3. Switch Environment

```powershell
# Switch to XAMPP environment
.\scripts\environment\switch-environment.ps1 -Environment xampp

# Or use Laravel command
php artisan ict:setup-xampp --migrate
```

### 4. Verify Installation

```bash
# Run environment tests
php artisan test --group=xampp

# Check service status
php artisan ict:setup-xampp --status
```

## Detailed Setup Instructions

### Phase 1: XAMPP Configuration

#### Install and Configure XAMPP

```powershell
# 1. Download and install XAMPP
# 2. Run XAMPP optimization
.\scripts\xampp\manage-xampp.ps1 -Action optimize

# 3. Start XAMPP services
.\scripts\xampp\manage-xampp.ps1 -Action start

# 4. Verify XAMPP is running
.\scripts\xampp\manage-xampp.ps1 -Action status
```

#### XAMPP MySQL Configuration

The setup script automatically optimizes MySQL for ICTServe:

```ini
# Automatic optimizations applied:
max_connections = 200
innodb_buffer_pool_size = 256M
character-set-server = utf8mb4
collation-server = utf8mb4_unicode_ci
default-time-zone = '+08:00'
```

### Phase 2: WSL Redis Setup

#### Install Redis in WSL

```powershell
# Install Redis in WSL
.\scripts\wsl\manage-redis.ps1 -Action install

# Configure Redis for Windows host access
.\scripts\wsl\manage-redis.ps1 -Action configure

# Start Redis service
.\scripts\wsl\manage-redis.ps1 -Action start

# Test Redis connectivity
.\scripts\wsl\manage-redis.ps1 -Action test
```

#### Redis Configuration

The setup automatically configures Redis for ICTServe:

```conf
# Automatic configurations applied:
bind 0.0.0.0
protected-mode no
maxmemory 256mb
maxmemory-policy allkeys-lru
databases 16
```

### Phase 3: Laravel Configuration

#### Environment Configuration

The `.env.xampp` file is automatically created with optimal settings:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# Redis Configuration
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

#### Migration Process

```bash
# Execute full migration
php artisan ict:setup-xampp --migrate

# Or manual step-by-step:
# 1. Backup current environment
# 2. Switch configuration
# 3. Migrate database
# 4. Test all services
```

## Service Management

### Daily Operations

#### Start Development Environment

```powershell
# Start all services
.\scripts\xampp\manage-xampp.ps1 -Action start
.\scripts\wsl\manage-redis.ps1 -Action start

# Start Laravel development server
php artisan serve
```

#### Stop Development Environment

```powershell
# Stop all services
.\scripts\xampp\manage-xampp.ps1 -Action stop
.\scripts\wsl\manage-redis.ps1 -Action stop
```

#### Check Service Status

```powershell
# Check XAMPP status
.\scripts\xampp\manage-xampp.ps1 -Action status

# Check Redis status
.\scripts\wsl\manage-redis.ps1 -Action status

# Check Laravel services
php artisan ict:setup-xampp --status
```

### Environment Switching

```powershell
# Switch to XAMPP environment
.\scripts\environment\switch-environment.ps1 -Environment xampp

# Switch back to Docker (if needed)
.\scripts\environment\switch-environment.ps1 -Environment docker
```

## Laravel Services Integration

### Verified Services

All ICTServe Laravel services work with XAMPP environment:

- ✅ **Laravel Pulse 1.4.6** - Performance monitoring
- ✅ **Laravel Telescope 5.16.0** - Debugging and profiling
- ✅ **Laravel Horizon 5.x** - Queue management
- ✅ **Laravel Reverb 1.6.3** - WebSocket server
- ✅ **Filament 4.1.10** - Admin panel
- ✅ **Livewire 3.7.1** - Dynamic components

### Service Configuration

#### Laravel Horizon (Queue Management)

```bash
# Start Horizon for queue processing
php artisan horizon

# Monitor queue status
php artisan horizon:status
```

#### Laravel Reverb (WebSocket)

```bash
# Start Reverb WebSocket server
php artisan reverb:start --host=127.0.0.1 --port=8080
```

#### Laravel Pulse (Performance Monitoring)

Access at: `http://127.0.0.1:8000/pulse`

#### Laravel Telescope (Debugging)

Access at: `http://127.0.0.1:8000/telescope`

## Testing

### Environment Validation Tests

```bash
# Run XAMPP-specific tests
php artisan test --group=xampp

# Run all environment tests
php artisan test --group=environment

# Test specific functionality
php artisan test tests/Feature/Environment/XamppEnvironmentTest.php
```

### Performance Testing

```bash
# Test database performance
php artisan test --filter=it_validates_environment_performance

# Test concurrent operations
php artisan test --filter=it_can_handle_concurrent
```

## Troubleshooting

### Common Issues

#### 1. MySQL Connection Failed

```powershell
# Check if MySQL is running
.\scripts\xampp\manage-xampp.ps1 -Action status

# Restart MySQL
.\scripts\xampp\manage-xampp.ps1 -Action restart

# Check port conflicts
netstat -an | findstr :3306
```

#### 2. Redis Connection Failed

```powershell
# Check WSL Redis status
.\scripts\wsl\manage-redis.ps1 -Action status

# Restart Redis
.\scripts\wsl\manage-redis.ps1 -Action restart

# Test connectivity
.\scripts\wsl\manage-redis.ps1 -Action test
```

#### 3. Port Conflicts

```powershell
# Check port usage
netstat -an | findstr :3306  # MySQL
netstat -an | findstr :6379  # Redis
netstat -an | findstr :80    # Apache
netstat -an | findstr :8000  # Laravel
```

#### 4. WSL Issues

```powershell
# Restart WSL
wsl --shutdown
wsl

# Check WSL status
wsl --status
```

### Error Resolution

#### Database Migration Errors

```bash
# Clear Laravel caches
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Re-run migrations
php artisan migrate:fresh --seed
```

#### Redis Connection Errors

```bash
# Check Redis configuration
wsl cat /etc/redis/redis.conf | grep bind
wsl cat /etc/redis/redis.conf | grep protected-mode

# Reconfigure Redis
.\scripts\wsl\manage-redis.ps1 -Action configure
```

## Performance Optimization

### XAMPP MySQL Tuning

The setup automatically applies these optimizations:

```ini
# Performance settings
innodb_buffer_pool_size = 256M
query_cache_type = 1
query_cache_size = 32M
tmp_table_size = 64M
max_heap_table_size = 64M
```

### WSL Redis Tuning

```conf
# Memory management
maxmemory 256mb
maxmemory-policy allkeys-lru

# Connection optimization
tcp-keepalive 300
timeout 0
```

### Laravel Optimization

```bash
# Optimize Laravel for production-like performance
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Optimize Composer autoloader
composer dump-autoload --optimize
```

## Backup and Recovery

### Automatic Backups

The migration process automatically creates backups:

```
storage/backups/environment_migration/
├── database_backup_YYYYMMDD_HHMMSS.sql
├── redis_backup_YYYYMMDD_HHMMSS.json
├── config_backup_YYYYMMDD_HHMMSS/
└── migration_log_YYYYMMDD_HHMMSS.json
```

### Manual Backup

```bash
# Create manual backup
php artisan backup:run

# Backup specific components
mysqldump -h 127.0.0.1 -u root ictserve > backup.sql
```

### Recovery Process

```bash
# Restore from backup
mysql -h 127.0.0.1 -u root ictserve < backup.sql

# Switch back to previous environment
.\scripts\environment\switch-environment.ps1 -Environment docker
```

## Development Workflow

### Typical Development Day

```powershell
# 1. Start development environment
.\scripts\xampp\manage-xampp.ps1 -Action start
.\scripts\wsl\manage-redis.ps1 -Action start

# 2. Start Laravel services
php artisan serve                    # Web server
php artisan horizon                  # Queue processing
php artisan reverb:start            # WebSocket server

# 3. Start frontend development
npm run dev                          # Vite development server

# 4. End of day - stop services
.\scripts\xampp\manage-xampp.ps1 -Action stop
.\scripts\wsl\manage-redis.ps1 -Action stop
```

### Code Quality Checks

```bash
# Run code quality tools
vendor/bin/pint                      # PSR-12 formatting
vendor/bin/phpstan analyse           # Static analysis
php artisan test                     # PHPUnit tests
npm run build                        # Frontend build
```

## Support and Maintenance

### Health Monitoring

```bash
# Daily health check
php artisan ict:setup-xampp --status

# Performance monitoring
php artisan pulse:check

# Queue monitoring
php artisan horizon:status
```

### Log Monitoring

```bash
# Laravel logs
tail -f storage/logs/laravel.log

# XAMPP MySQL logs
tail -f C:\xampp\mysql\data\mysql_error.log

# WSL Redis logs
wsl tail -f /var/log/redis/redis-server.log
```

### Updates and Maintenance

```bash
# Update Laravel dependencies
composer update

# Update npm dependencies
npm update

# Clear and rebuild caches
php artisan optimize:clear
php artisan optimize
```

## Security Considerations

### Development Security

- XAMPP is configured for development use only
- Redis is configured without authentication for local development
- Use proper authentication in production environments

### Data Protection

- All personal data is encrypted according to PDPA 2010 requirements
- Audit logging is maintained for all data operations
- Backup procedures ensure data integrity

## Conclusion

The XAMPP environment provides a stable, high-performance development setup for ICTServe v3.6.1 while maintaining all existing functionality. The automated scripts and comprehensive testing ensure a smooth transition from Docker-based development.

For additional support or issues, refer to the troubleshooting section or check the Laravel logs for detailed error information.

---

**ICTServe v3.6.1 XAMPP Environment Setup**  
**Version**: 1.0  
**Last Updated**: January 19, 2025  
**Compatibility**: Windows 10/11, WSL 2, XAMPP, Laravel 12.42.0
