# ICTServe Development Startup Scripts

This document describes the development startup scripts that launch all required services for ICTServe development, including Laravel Horizon support.

## Overview

ICTServe provides three startup scripts for different environments:

- **PowerShell** (`scripts/dev/start-dev.ps1`) - Full-featured with advanced detection and monitoring
- **Bash** (`scripts/dev/start-dev.sh`) - Git Bash/Linux compatible with Horizon support
- **Batch** (`scripts/dev/start-dev.bat`) - Basic Windows batch with Horizon detection

## Services Started

All scripts start the following services in separate terminal windows:

1. **Redis Server (WSL)** - Background data store and queue backend
2. **Laravel Server** - Main application server (<http://127.0.0.1:8000>)
3. **Laravel Reverb** - WebSocket server for real-time features (ws://127.0.0.1:6001)
4. **Queue Service** - Laravel Horizon or Queue Worker for background jobs
5. **Vite Dev Server** - Frontend asset compilation with Hot Module Replacement

## Horizon Integration

### Automatic Detection

All scripts now include intelligent Horizon detection:

```bash
# Check if Horizon is available and Redis is running
if [Redis accessible] && [Horizon command exists]; then
    # Use Laravel Horizon for advanced queue management
    start "Laravel Horizon"
else
    # Fallback to traditional queue worker
    start "Laravel Queue Worker"
fi
```

### Horizon Benefits

When Horizon is available and Redis is running:

- **Advanced Queue Management** - Multiple supervisors for different job types
- **Real-time Monitoring** - Dashboard at <http://127.0.0.1:8000/horizon>
- **Auto-scaling** - Dynamic process scaling based on queue load
- **Retry Policies** - Exponential backoff and intelligent retry handling
- **Job Tagging** - Better organization and filtering of background jobs

### Fallback Behavior

When Horizon is not available or Redis is not running:

- Falls back to traditional `php artisan queue:work`
- Still processes background jobs but without advanced features
- No dashboard monitoring available

## Script Features

### PowerShell Script (`start-dev.ps1`)

**Enhanced Features:**

- Service profiles (minimal, standard, full, horizon)
- Intelligent WSL Redis detection and startup
- Comprehensive service verification with retry logic
- Color-coded status messages and progress indicators
- Automatic Horizon status checking and management
- Service dependency validation
- Quick access URL display with Horizon dashboard link

**Usage:**

```powershell
# Standard startup with all services
.\scripts\dev\start-dev.ps1

# Minimal profile (Laravel + Vite only)
.\scripts\dev\start-dev.ps1 -Profile minimal

# Force Horizon usage
.\scripts\dev\start-dev.ps1 -Profile horizon

# Skip service verification
.\scripts\dev\start-dev.ps1 -SkipVerification
```

### Bash Script (`start-dev.sh`)

**Features:**

- Cross-platform compatibility (Git Bash, Linux, macOS)
- WSL Redis detection and automatic installation prompts
- Horizon availability checking and intelligent fallback
- Service verification with configurable retry attempts
- Color-coded terminal output for better visibility

**Usage:**

```bash
# Make executable (Linux/macOS)
chmod +x scripts/dev/start-dev.sh

# Run the script
./scripts/dev/start-dev.sh
```

### Batch Script (`start-dev.bat`)

**Features:**

- Basic Windows compatibility without PowerShell dependency
- Automatic PowerShell detection and delegation
- Horizon availability checking
- WSL Redis monitoring (if available)
- Simplified service startup for older Windows systems

**Usage:**

```batch
REM Run from project root
scripts\dev\start-dev.bat
```

## Configuration Requirements

### Redis Configuration

For Horizon to work properly, ensure Redis is configured correctly:

**`.env.local` (Critical Configuration):**

```env
# Use Predis client for cross-platform compatibility
REDIS_CLIENT=predis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Enable Redis queue connection
QUEUE_CONNECTION=redis
```

**Common Issue:** If `.env.local` has `REDIS_CLIENT=phpredis`, Horizon will fail on Windows. Always use `predis` for development.

### WSL Redis Setup

For optimal Horizon performance, install Redis in WSL:

```bash
# Install Redis in WSL (run as root)
sudo apt update
sudo apt install redis-server
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Test Redis connection
redis-cli ping  # Should return "PONG"
```

## Service Verification

### Port Checking

All scripts verify services are running by checking TCP ports:

- **Laravel Server**: 127.0.0.1:8000
- **Vite Dev Server**: 127.0.0.1:5173
- **Laravel Reverb**: 127.0.0.1:6001
- **Redis Server**: 127.0.0.1:6379

### Horizon Status Verification

Scripts check Horizon status using:

```bash
php artisan horizon:status
```

Expected responses:

- `"Horizon is running."` - Horizon active and managing queues
- `"Horizon is not running."` - Horizon not started
- Command not found - Horizon not installed

### Process Detection

For queue workers, scripts verify processes are running:

```bash
# Check for Horizon processes
pgrep -f "artisan horizon"

# Check for queue worker processes
pgrep -f "artisan queue:work"
```

## Troubleshooting

### Common Issues

1. **Horizon Not Starting**
   - Check Redis connection: `redis-cli ping`
   - Verify `.env.local` has `REDIS_CLIENT=predis`
   - Ensure WSL Redis is running: `wsl.exe systemctl status redis-server`

2. **Services Not Accessible**
   - Check Windows Firewall settings
   - Verify no other services are using the same ports
   - Run scripts as Administrator if needed

3. **WSL Issues**
   - Install WSL: `wsl --install`
   - Install Redis in WSL: Follow WSL Redis setup guide
   - Check WSL distribution: `wsl -l -v`

4. **Permission Errors**
   - Run PowerShell as Administrator
   - Set execution policy: `Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser`
   - Make bash script executable: `chmod +x scripts/dev/start-dev.sh`

### Debug Commands

```bash
# Check Horizon status
php artisan horizon:status

# Test Redis connection
redis-cli ping
wsl.exe redis-cli ping

# Check running processes
tasklist | findstr php
ps aux | grep artisan

# Verify service ports
netstat -an | findstr :8000
netstat -an | findstr :6379
```

## Quick Access URLs

When all services are running:

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Horizon Dashboard**: <http://127.0.0.1:8000/horizon> (if Horizon is running)
- **Vite Dev Server**: <http://127.0.0.1:5173>

## Stopping Services

To stop all services:

1. **Close Terminal Windows** - Each service runs in its own terminal
2. **Use Ctrl+C** - In each terminal window to gracefully stop services
3. **Horizon Specific**: `php artisan horizon:terminate` for graceful shutdown

## Related Documentation

- [Horizon WSL Setup Guide](../horizon/HORIZON_WSL_SETUP.md)
- [Redis Configuration Guide](../redis/WSL_SETUP.md)
- [Quick Start Guide](../../QUICK_START.md)
- [Laravel Horizon Documentation](../horizon/README.md)

## Script Maintenance

### Adding New Services

To add a new service to all scripts:

1. **Add service startup** in the appropriate section
2. **Add port verification** if the service uses a TCP port
3. **Update service summary** in the final output
4. **Add quick access URL** if applicable
5. **Update documentation** with the new service details

### Testing Changes

Before committing script changes:

1. Test all three script versions (PowerShell, Bash, Batch)
2. Verify services start correctly in different environments
3. Test both Horizon and queue worker fallback scenarios
4. Ensure service verification works properly
5. Check that all URLs are accessible after startup

This ensures consistent behavior across all development environments and maintains the reliability of the ICTServe development workflow.
