# ICTServe v3.6.0 Development Setup Guide

## Prerequisites

### Required Software

1. **PHP 8.2.12+** (Currently using: 8.4.1)
   - Extensions: mbstring, openssl, pdo, tokenizer, xml, ctype, json, bcmath, fileinfo
   - Install via: Laragon, XAMPP, or standalone PHP

2. **Composer 2.4+** (Currently using: 2.4.1)
   - Download from: <https://getcomposer.org/>

3. **Node.js 22.12+** (Currently using: 22.14.0)
   - Download from: <https://nodejs.org/>
   - **Note**: If npm has permission issues, run `.\scripts\dev\fix-npm.ps1`

4. **MySQL 8.0+** or **MariaDB 10.3+**
   - Included with Laragon/XAMPP
   - Or install standalone

5. **Redis 7.0+** (Optional but recommended)
   - For Windows: Use WSL2 with Redis, or Laragon Redis module
   - Install in WSL: `wsl.exe sudo apt install redis-server`
   - Start in WSL: `wsl.exe redis-server --daemonize yes`

## Quick Start

### 1. Clone and Setup

```powershell
# Clone the repository
git clone <repository-url> ictserve
cd ictserve

# Run setup script (handles .env, composer, npm)
.\scripts\dev\setup-project.ps1

# If npm has issues, skip it and fix later
.\scripts\dev\setup-project.ps1 -SkipNpm
```

### 2. Configure Environment

Edit `.env` file:

```env
# Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# Application URL (use 127.0.0.1 on Windows)
APP_URL=http://127.0.0.1:8000

# Redis Configuration (if available)
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

### 3. Run Migrations

```powershell
# Create database tables
php artisan migrate

# Seed with sample data (optional)
php artisan db:seed
```

### 4. Start Development Server

```powershell
# Full development environment (all services)
.\scripts\dev\start-dev.ps1

# Minimal (Laravel + Vite only)
.\scripts\dev\start-dev.ps1 -Profile minimal

# Backend only (Laravel + Redis + Queue + Reverb)
.\scripts\dev\start-dev.ps1 -Profile backend

# Skip checks for faster startup
.\scripts\dev\start-dev.ps1 -SkipChecks
```

## Development Profiles

### Available Profiles

| Profile | Services | Use Case |
|---------|----------|----------|
| `minimal` | Laravel, Vite | Quick frontend development |
| `backend` | Redis, Laravel, Reverb, Queue | Backend API development |
| `frontend` | Laravel, Vite | Frontend-only development |
| `full` | All services | Complete development environment |
| `testing` | All + Browser | E2E testing |
| `ai` | All + MCP + Ollama | AI chatbot development |

### Profile Usage

```powershell
# Minimal profile (fastest startup)
.\scripts\dev\start-dev.ps1 -Profile minimal

# AI development profile
.\scripts\dev\start-dev.ps1 -Profile ai

# Testing profile with browser
.\scripts\dev\start-dev.ps1 -Profile testing
```

## Troubleshooting

### npm Permission Issues

**Problem**: `npm` commands fail with permission errors

**Solution**:

```powershell
# Run the fix script
.\scripts\dev\fix-npm.ps1

# Or manually reinstall Node.js from https://nodejs.org/
```

### Redis Not Available

**Problem**: Redis connection errors

**Solutions**:

1. **WSL2 Redis** (Recommended for Windows):

```powershell
# Install Redis in WSL
wsl.exe sudo apt update
wsl.exe sudo apt install -y redis-server

# Start Redis
wsl.exe redis-server --daemonize yes

# Verify
wsl.exe redis-cli ping
```

2. **Laragon Redis Module**:
   - Enable Redis module in Laragon
   - Start Redis from Laragon menu

3. **Docker Redis**:

```powershell
docker run -d -p 6379:6379 redis:alpine
```

4. **Disable Redis** (Not recommended):

```env
# In .env file
CACHE_STORE=file
SESSION_DRIVER=file
QUEUE_CONNECTION=sync
```

### Port Already in Use

**Problem**: Port 8000, 5173, or 8080 already in use

**Solution**:

```powershell
# Find process using port
netstat -ano | findstr :8000

# Kill process by PID
taskkill /PID <PID> /F

# Or use different ports in .env
APP_URL=http://127.0.0.1:8001
REVERB_PORT=8081
```

### Composer Install Fails

**Problem**: Composer dependencies fail to install

**Solution**:

```powershell
# Clear composer cache
composer clear-cache

# Install with platform requirements ignored
composer install --ignore-platform-reqs

# Update composer
composer self-update
```

### Database Connection Errors

**Problem**: Cannot connect to database

**Solutions**:

1. **Check MySQL is running**:
   - Laragon: Start MySQL from menu
   - XAMPP: Start MySQL from control panel

2. **Verify credentials in `.env`**:

```env
DB_HOST=127.0.0.1  # Use 127.0.0.1, not localhost
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=  # Empty for Laragon/XAMPP default
```

3. **Create database manually**:

```sql
CREATE DATABASE ictserve CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

## Development Commands

### Laravel Commands

```powershell
# Run tests
php artisan test

# Run specific test
php artisan test --filter=HelpdeskTicketTest

# Code formatting (PSR-12)
vendor/bin/pint

# Static analysis
vendor/bin/phpstan analyse

# Clear caches
php artisan optimize:clear

# Generate IDE helper files
php artisan ide-helper:generate
php artisan ide-helper:models
```

### Frontend Commands

```powershell
# Development server with HMR
npm run dev

# Build for production
npm run build

# Run E2E tests
npm run test:e2e

# Run accessibility tests
npm run test:accessibility
```

### Database Commands

```powershell
# Run migrations
php artisan migrate

# Rollback last migration
php artisan migrate:rollback

# Fresh migration with seeding
php artisan migrate:fresh --seed

# Create new migration
php artisan make:migration create_example_table
```

## Service URLs

When development server is running:

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Telescope**: <http://127.0.0.1:8000/telescope>
- **Pulse**: <http://127.0.0.1:8000/pulse>
- **Horizon**: <http://127.0.0.1:8000/horizon>
- **Vite Dev Server**: <http://127.0.0.1:5173>
- **Laravel Reverb**: ws://127.0.0.1:8080

## Quality Assurance

### Pre-Commit Checklist

Before committing code, ensure:

1. **Code Formatting**: `vendor/bin/pint`
2. **Static Analysis**: `vendor/bin/phpstan analyse`
3. **Tests Pass**: `php artisan test`
4. **Frontend Builds**: `npm run build`

### Compliance Standards

- **PDPA 2010**: Personal data encryption & audit logging
- **WCAG 2.2 AA**: 4.5:1 text contrast, 3:1 UI contrast
- **PSR-12**: PHP coding standards
- **MyGOV Standards**: Bahasa Melayu only, mobile-first design

## Additional Resources

- **Laravel Documentation**: <https://laravel.com/docs/12.x>
- **Filament Documentation**: <https://filamentphp.com/docs/4.x>
- **Livewire Documentation**: <https://livewire.laravel.com/docs/3.x>
- **Tailwind CSS**: <https://tailwindcss.com/docs>

## Getting Help

1. Check `docs/QUICK_START.md` for quick reference
2. Review `docs/D00_SYSTEM_OVERVIEW.md` for architecture
3. Check `.kiro/steering/*.md` for development guidelines
4. Run `php artisan` to see available commands
5. Check Laravel logs: `storage/logs/laravel.log`

## Known Issues

### npm Permission Errors

**Status**: Known issue with Node.js installation on this system  
**Workaround**: Run `.\scripts\dev\fix-npm.ps1` or reinstall Node.js  
**Impact**: Frontend assets won't hot-reload, but can be built manually with `npm run build`

### WSL Redis Setup

**Status**: Redis requires WSL2 on Windows for best performance  
**Workaround**: Use Laragon Redis module or Docker Redis  
**Impact**: Some features (caching, queues, real-time) may be slower without Redis
