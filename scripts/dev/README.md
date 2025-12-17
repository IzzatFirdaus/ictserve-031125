# ICTServe Development Scripts

## Overview

Enhanced development environment scripts for ICTServe v3.6.0 with Laravel 12.42.0, providing automated service management, WSL Redis integration, and comprehensive development tools.

## Available Scripts

### 1. Enhanced Development Script (`start-dev.ps1`)

**Full-featured development environment with service profiles and health monitoring.**

```bash
# Full development environment (recommended)
.\scripts\dev\start-dev.ps1

# Service profiles
.\scripts\dev\start-dev.ps1 -Profile minimal    # Laravel + Vite only
.\scripts\dev\start-dev.ps1 -Profile backend    # Backend services only
.\scripts\dev\start-dev.ps1 -Profile frontend   # Frontend development
.\scripts\dev\start-dev.ps1 -Profile ai         # AI development with MCP
.\scripts\dev\start-dev.ps1 -Profile testing    # Testing environment

# Options
.\scripts\dev\start-dev.ps1 -SkipChecks        # Skip environment checks
.\scripts\dev\start-dev.ps1 -NoMCP             # Disable MCP server
.\scripts\dev\start-dev.ps1 -NoBrowser         # Don't auto-open browser
.\scripts\dev\start-dev.ps1 -InstallRedis      # Auto-install WSL Redis
```

**Features:**

- ✅ Service profiles for different development needs
- ✅ Enhanced WSL Redis detection and management
- ✅ Health checks with retry logic
- ✅ Comprehensive error handling
- ✅ Performance monitoring and startup timing
- ✅ Graceful shutdown sequence

### 2. Simple Development Script (`start-dev-simple.ps1`)

**Streamlined version focusing on core services.**

```bash
# Simple development environment
.\scripts\dev\start-dev-simple.ps1

# Options
.\scripts\dev\start-dev-simple.ps1 -NoRedis      # Skip Redis setup
.\scripts\dev\start-dev-simple.ps1 -InstallRedis # Auto-install Redis
```

**Services Started:**

- 🔴 WSL Redis Server (Cache, Sessions, Queues)
- 🔵 Laravel Server (<http://127.0.0.1:8000>)
- 🟢 Vite Dev Server (127.0.0.1:5173)
- 🔷 Queue Worker (Background Jobs)
- 🟣 Laravel Reverb (WebSocket - ws://127.0.0.1:6001)

### 3. Development Helpers (`dev-helpers.ps1`)

**Quick commands for common development tasks.**

```bash
# All-in-one development helper
.\scripts\dev\dev-helpers.ps1 <command>

# Available commands
.\scripts\dev\dev-helpers.ps1 test              # Run PHPUnit tests
.\scripts\dev\dev-helpers.ps1 test -Coverage    # Run tests with coverage
.\scripts\dev\dev-helpers.ps1 format            # Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 analyse           # Static analysis
.\scripts\dev\dev-helpers.ps1 build             # Build production assets
.\scripts\dev\dev-helpers.ps1 clean             # Clear caches
.\scripts\dev\dev-helpers.ps1 setup             # Initial project setup
.\scripts\dev\dev-helpers.ps1 status            # Check service status
.\scripts\dev\dev-helpers.ps1 logs              # View application logs
.\scripts\dev\dev-helpers.ps1 help              # Show all commands
```

### 4. WSL Redis Setup (`setup-wsl-redis.ps1`)

**Automated WSL Redis installation and configuration.**

```bash
# Install and configure Redis in WSL
.\scripts\dev\setup-wsl-redis.ps1

# Options
.\scripts\dev\setup-wsl-redis.ps1 -Force        # Reinstall if exists
.\scripts\dev\setup-wsl-redis.ps1 -TestOnly     # Test without installing
```

### 5. Batch Alternative (`start-dev.bat`)

**Command Prompt compatible version for systems without PowerShell.**

```cmd
REM Basic development environment
scripts\dev\start-dev.bat
```

### 6. Testing Script (`test-script.ps1`)

**Validate script syntax and environment.**

```bash
# Test script syntax only
.\scripts\dev\test-script.ps1 -TestOnly

# Full environment test
.\scripts\dev\test-script.ps1
```

## NPM Script Integration

All scripts are integrated with package.json for easy access:

```bash
# Development environments
npm run dev:win              # Full development (PowerShell)
npm run dev:win:simple       # Simple development
npm run dev:win:minimal      # Minimal profile
npm run dev:win:backend      # Backend profile
npm run dev:win:ai           # AI development profile

# Utilities
npm run dev:helpers          # Development helper commands
npm run wsl-redis-setup      # WSL Redis setup
npm run test-dev-script      # Test development scripts
```

## Service Profiles

### Profile Comparison

| Profile | Services | Use Case | Startup Time |
|---------|----------|----------|--------------|
| **minimal** | Laravel + Vite | Quick testing, minimal resources | ~30s |
| **backend** | Redis + Laravel + Reverb + Queue | API development, backend testing | ~45s |
| **frontend** | Laravel + Vite | UI/UX development | ~30s |
| **full** | All services + MCP + Pulse | Complete development | ~60s |
| **testing** | Full + Browser | E2E testing, QA validation | ~60s |
| **ai** | Full + MCP + Ollama | AI chatbot development | ~75s |

### Service Details

#### Core Services

- **Laravel Server**: Main application (127.0.0.1:8000)
- **Vite Dev Server**: Frontend assets with HMR (127.0.0.1:5173)
- **Queue Worker**: Background job processing
- **Laravel Reverb**: WebSocket broadcasting (127.0.0.1:6001)

#### Optional Services

- **Redis Server**: Cache, sessions, queues (127.0.0.1:6379)
- **Laravel MCP Server**: AI integration and chatbot support
- **Laravel Pulse**: Performance monitoring and metrics

## WSL Redis Integration

### Automatic Detection

- ✅ WSL availability check
- ✅ Redis installation detection
- ✅ Running status verification
- ✅ Permission testing (sudo-less startup)

### Smart Installation

- 🔧 Auto-install option with `-InstallRedis`
- 🔧 Dedicated setup script for manual installation
- 🔧 Configuration optimization for Laravel
- 🔧 Fallback to alternative Redis sources

### Redis Management

```bash
# Manual Redis commands (WSL)
wsl.exe redis-server --daemonize yes    # Start Redis
wsl.exe redis-cli ping                  # Test connection
wsl.exe redis-cli shutdown              # Stop Redis
wsl.exe redis-cli monitor               # Monitor commands
```

## Troubleshooting

### Common Issues

#### Script Syntax Errors

```bash
# Test script syntax
.\scripts\dev\test-script.ps1 -TestOnly

# Check PowerShell version
$PSVersionTable.PSVersion
```

#### WSL Redis Issues

```bash
# Check WSL status
wsl.exe --status

# Test Redis installation
wsl.exe command -v redis-server

# Manual Redis start
wsl.exe redis-server --daemonize yes --port 6379 --bind 127.0.0.1
```

#### Port Conflicts

```bash
# Check port usage
netstat -ano | findstr :8000    # Laravel
netstat -ano | findstr :5173    # Vite
netstat -ano | findstr :6001    # Reverb
netstat -ano | findstr :6379    # Redis

# Kill process on port
taskkill /PID <PID> /F
```

#### Service Status

```bash
# Check all services
.\scripts\dev\dev-helpers.ps1 status

# Individual service checks
php artisan about               # Laravel status
npm run check-node             # Node.js version
wsl.exe redis-cli ping         # Redis connection
```

### Error Recovery

#### Clear All Caches

```bash
.\scripts\dev\dev-helpers.ps1 clean
```

#### Reset Environment

```bash
# Stop all services (close terminal windows)
# Clear caches
.\scripts\dev\dev-helpers.ps1 clean
# Restart development environment
.\scripts\dev\start-dev.ps1
```

#### Reinstall Dependencies

```bash
# PHP dependencies
composer install

# Node.js dependencies
rm -rf node_modules package-lock.json
npm install

# WSL Redis
.\scripts\dev\setup-wsl-redis.ps1 -Force
```

## Performance Optimization

### Startup Time Optimization

- **Staggered service startup** prevents resource conflicts
- **Health checks with retry logic** ensure services are ready
- **Priority-based startup sequence** optimizes dependency loading
- **Parallel service initialization** where possible

### Resource Management

- **Service profiles** allow resource-conscious development
- **Graceful shutdown** prevents resource leaks
- **Process monitoring** tracks service health
- **Memory usage optimization** through selective service loading

## Compliance & Standards

### Code Quality Integration

- **PSR-12 formatting** via Laravel Pint
- **PHPStan Level 9** static analysis
- **PHPUnit 12** testing with PHP 8 attributes
- **Accessibility testing** with Playwright

### ICTServe Compliance

- **PDPA 2010**: Malaysian privacy law compliance
- **WCAG 2.2 AA**: Accessibility standards (4.5:1 text, 3:1 UI contrast)
- **MyGOV Standards**: Malaysian government digital service requirements
- **PSR-12**: PHP coding standards enforcement

## Advanced Configuration

### Environment Variables

```env
# Laravel configuration
APP_URL=http://127.0.0.1:8000
APP_ENV=local

# Database
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306

# Redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379

# Broadcasting
BROADCAST_CONNECTION=reverb
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
```

### Custom Service Profiles
You can extend the service profiles by modifying the `Get-ServiceProfile` function in `start-dev.ps1`:

```powershell
$profiles = @{
    "custom" = @("redis", "laravel", "vite", "custom-service")
    # Add your custom profile here
}
```

## Support & Maintenance

### Regular Updates

- Scripts are version-controlled and tested
- Compatibility maintained with Laravel 12+ and PHP 8.2+
- WSL integration updated for latest Windows versions
- Service configurations optimized for performance

### Getting Help

1. **Check logs**: `.\scripts\dev\dev-helpers.ps1 logs`
2. **Test environment**: `.\scripts\dev\test-script.ps1`
3. **Check service status**: `.\scripts\dev\dev-helpers.ps1 status`
4. **Review documentation**: This README and QUICK_START.md

---

**ICTServe v3.6.0** | **Laravel 12.42.0** | **Enhanced Development Scripts** ✅  
**Last Updated**: December 16, 2025
