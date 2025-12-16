# ICTServe Quick Start Guide

**Version**: 3.6.0 | **Laravel**: 12.42.0 | **PHP**: 8.2.12+ | **Node.js**: 22.12+

---

## 🚀 Essential Commands (Start Here!)

### First Time Setup

```bash
# 1. Install dependencies
composer install && npm install

# 2. Setup environment
cp .env.example .env && php artisan key:generate

# 3. Setup database
php artisan migrate --seed

# 4. Start development environment
.\scripts\dev\start-dev.ps1
```

### Daily Development

```bash
# Start all services (recommended)
.\scripts\dev\start-dev.ps1

# Or use npm script
npm run dev:win

# Quick helpers
.\scripts\dev\dev-helpers.ps1 test      # Run tests
.\scripts\dev\dev-helpers.ps1 format    # Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 status    # Check services
```

### Service Profiles

```bash
# Full development (default)
.\scripts\dev\start-dev.ps1

# Minimal (Laravel + Vite only)
.\scripts\dev\start-dev.ps1 -Profile minimal

# Backend development
.\scripts\dev\start-dev.ps1 -Profile backend

# AI development (includes MCP)
.\scripts\dev\start-dev.ps1 -Profile ai
```

### Quick Access URLs

- **Application**: <http://127.0.0.1:8000>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **Helpdesk**: <http://127.0.0.1:8000/helpdesk/create>
- **Asset Loan**: <http://127.0.0.1:8000/loan/create>

---

## Table of Contents

1. [Essential Commands](#-essential-commands-start-here)
2. [Development Environment Setup](#development-environment-setup)
3. [Service Management](#service-management)
4. [Development Workflow](#development-workflow)
5. [Troubleshooting](#troubleshooting)
6. [Advanced Configuration](#advanced-configuration)

---

## Development Environment Setup

### Prerequisites

- **PHP**: 8.2.12+ with extensions (mbstring, xml, curl, zip, gd, mysql)
- **Node.js**: 22.12+ (for Vite 7.0.7 compatibility)
- **Composer**: Latest version
- **MySQL**: 8.0+ or SQLite for development
- **Redis**: Optional but recommended (WSL, Laragon, or Docker)

### One-Command Setup

```bash
# Complete setup (first time only)
.\scripts\dev\dev-helpers.ps1 setup
```

### Manual Setup Steps

#### 1. Install Dependencies

```bash
composer install
npm install
```

#### 2. Environment Configuration

```bash
# Copy and configure environment
cp .env.example .env
php artisan key:generate

# Edit .env file with your database settings
# DB_CONNECTION=mysql
# DB_HOST=127.0.0.1
# DB_DATABASE=ictserve
```

#### 3. Database Setup

```bash
# Create database (if using MySQL)
mysql -u root -p -e "CREATE DATABASE ictserve;"

# Run migrations and seeders
php artisan migrate --seed
```

#### 4. Redis Setup (Optional but Recommended)

```bash
# WSL Redis (recommended)
wsl.exe
sudo apt update && sudo apt install redis-server
sudo systemctl enable redis-server && sudo systemctl start redis-server
redis-cli ping  # Should return PONG

# Or use Laragon/XAMPP Redis
# Or skip Redis (will use file-based cache/sessions)
```

---

## Service Management

### Start Development Environment

#### Recommended: Enhanced Development Script

```bash
# Start all services with health checks
.\scripts\dev\start-dev.ps1

# Available profiles:
.\scripts\dev\start-dev.ps1 -Profile minimal    # Laravel + Vite only
.\scripts\dev\start-dev.ps1 -Profile backend    # Backend services only
.\scripts\dev\start-dev.ps1 -Profile frontend   # Frontend development
.\scripts\dev\start-dev.ps1 -Profile ai         # AI development with MCP
.\scripts\dev\start-dev.ps1 -Profile testing    # Testing environment

# Options:
.\scripts\dev\start-dev.ps1 -SkipChecks        # Skip environment checks
.\scripts\dev\start-dev.ps1 -NoMCP             # Disable MCP server
.\scripts\dev\start-dev.ps1 -NoBrowser         # Don't open browser
```

**Services Started:**

- 🔴 Redis Server (Cache, Sessions, Queues)
- 🔵 Laravel Server (<http://127.0.0.1:8000>)
- 🟣 Laravel Reverb (WebSocket - ws://127.0.0.1:6001)
- 🔷 Queue Worker (Background Jobs)
- 🟢 Vite Dev Server (HMR - 127.0.0.1:5173)
- 🤖 Laravel MCP Server (AI Integration)
- 📊 Laravel Pulse (Performance Monitoring)

#### Alternative: Individual Services

```bash
# Laravel server only
php artisan serve

# All services with Composer
composer run dev

# Individual services (separate terminals)
php artisan reverb:start     # WebSocket server
php artisan queue:work       # Background jobs
npm run dev                  # Vite dev server
```

#### NPM Scripts (Package.json)

```bash
npm run dev:win              # Full development environment
npm run dev:win:minimal      # Minimal profile
npm run dev:win:backend      # Backend profile
npm run dev:win:ai           # AI development profile
npm run dev:helpers          # Development helper commands
```

### Service Status & Management

```bash
# Check service status
.\scripts\dev\dev-helpers.ps1 status

# View logs
.\scripts\dev\dev-helpers.ps1 logs

# Stop all services (press any key in main script window)
# Or manually kill processes if needed
```

---

## Development Workflow

### Daily Development Commands

#### Development Helper Script (Recommended)

```bash
# All-in-one development helper
.\scripts\dev\dev-helpers.ps1 <command>

# Available commands:
.\scripts\dev\dev-helpers.ps1 test              # Run PHPUnit tests
.\scripts\dev\dev-helpers.ps1 test -Coverage    # Run tests with coverage
.\scripts\dev\dev-helpers.ps1 test -Filter HelpdeskTest  # Run specific tests
.\scripts\dev\dev-helpers.ps1 format            # Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 analyse           # Static analysis (PHPStan Level 9)
.\scripts\dev\dev-helpers.ps1 build             # Build production assets
.\scripts\dev\dev-helpers.ps1 clean             # Clear caches and cleanup
.\scripts\dev\dev-helpers.ps1 setup             # Initial project setup
.\scripts\dev\dev-helpers.ps1 status            # Check service status
.\scripts\dev\dev-helpers.ps1 logs              # View application logs
.\scripts\dev\dev-helpers.ps1 help              # Show all commands
```

#### Core Laravel Commands

```bash
# Database
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Reset database with seeders
php artisan db:seed              # Run seeders only
php artisan migrate:rollback     # Rollback last migration

# Testing
php artisan test                           # Run all PHPUnit tests
php artisan test --filter=HelpdeskTest    # Run specific test class
php artisan test --coverage               # Run with coverage report
npx playwright test                        # E2E tests
npm run test:e2e:helpdesk                 # Test helpdesk module
npm run test:accessibility                # WCAG 2.2 AA compliance tests

# Code Quality (Mandatory before commits)
vendor/bin/pint                    # Format PHP code (PSR-12)
vendor/bin/phpstan analyse         # Static analysis (Level 9)
npm run build                      # Build production assets

# Cache Management
php artisan optimize:clear         # Clear all caches
php artisan config:cache          # Cache configuration
php artisan route:cache           # Cache routes
php artisan view:cache            # Cache views

# Laravel Boost (MCP Integration)
composer boost                    # Start MCP server
php artisan boost:install         # Install Boost assets
php artisan boost:update          # Update guidelines
```

#### NPM Scripts

```bash
# Development
npm run dev                       # Vite dev server
npm run build                     # Production build
npm run check-node               # Verify Node.js version

# Testing
npm run test:e2e                 # All E2E tests
npm run test:e2e:ui              # E2E tests with UI
npm run test:e2e:helpdesk        # Helpdesk module tests
npm run test:e2e:loan            # Asset loan module tests
npm run test:accessibility       # Accessibility tests
npm run playwright:install       # Install Playwright browsers

# Development Environment
npm run dev:win                  # Full development (Windows)
npm run dev:win:minimal          # Minimal development
npm run dev:win:backend          # Backend development
npm run dev:win:ai               # AI development
npm run dev:helpers              # Development helpers
```

### Recommended Development Flow

```bash
# 1. Start development environment
.\scripts\dev\start-dev.ps1

# 2. Make code changes

# 3. Run tests frequently
.\scripts\dev\dev-helpers.ps1 test

# 4. Format code before commits
.\scripts\dev\dev-helpers.ps1 format

# 5. Check for issues
.\scripts\dev\dev-helpers.ps1 analyse

# 6. Build assets for production
.\scripts\dev\dev-helpers.ps1 build

# 7. Commit changes
git add . && git commit -m "feat: your feature description"
```

---

### Quick Access & Testing

#### Application URLs

- **Homepage**: <http://127.0.0.1:8000>
- **Helpdesk Form**: <http://127.0.0.1:8000/helpdesk/create>
- **Asset Loan Form**: <http://127.0.0.1:8000/loan/create>
- **Admin Panel**: <http://127.0.0.1:8000/admin>
- **User Dashboard**: <http://127.0.0.1:8000/dashboard>
- **Laravel Telescope**: <http://127.0.0.1:8000/telescope>
- **Laravel Pulse**: <http://127.0.0.1:8000/pulse>

#### Default Test Credentials

```
Admin Account:
Email: admin@motac.gov.my
Password: password

Staff Account:
Email: staff@motac.gov.my
Password: password

Approver Account:
Email: approver@motac.gov.my
Password: password
```

#### Service Endpoints

- **Laravel Server**: <http://127.0.0.1:8000>
- **Vite Dev Server**: <http://127.0.0.1:5173>
- **WebSocket (Reverb)**: ws://127.0.0.1:6001
- **Redis**: 127.0.0.1:6379

---

## Troubleshooting

### Quick Fixes for Common Issues

#### Service Status Check

```bash
# Check all services at once
.\scripts\dev\dev-helpers.ps1 status

# Check individual ports
netstat -ano | findstr :8000    # Laravel
netstat -ano | findstr :5173    # Vite
netstat -ano | findstr :6001    # Reverb
netstat -ano | findstr :6379    # Redis
```

#### Port Already in Use

```bash
# Find and kill process on port 8000
netstat -ano | findstr :8000
taskkill /PID <PID> /F

# Or use development helper
.\scripts\dev\dev-helpers.ps1 clean
```

#### Laravel Issues

```bash
# Clear all caches (most common fix)
php artisan optimize:clear

# Specific cache clearing
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Regenerate autoload
composer dump-autoload
```

#### Asset/Frontend Issues

```bash
# Vite manifest error
npm run build                    # Build assets first
# OR
npm run dev                      # Start dev server

# Node.js version issues
npm run check-node               # Check Node version
# Ensure Node.js 22.12+ is installed

# Clear node_modules
rm -rf node_modules package-lock.json
npm install
```

#### Database Issues

```bash
# Connection failed
# 1. Start MySQL in Laragon/XAMPP
# 2. Verify .env settings:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=

# 3. Create database if missing
mysql -u root -p -e "CREATE DATABASE ictserve;"

# Migration issues
php artisan migrate:fresh --seed  # Reset database
php artisan migrate:rollback      # Rollback if needed
```

#### Redis Issues

```bash
# Test Redis connection
wsl.exe redis-cli ping           # Should return PONG

# Start Redis (WSL)
wsl.exe --user root systemctl start redis-server

# Check Redis status
wsl.exe --user root systemctl status redis-server

# Alternative: Use file-based cache (edit .env)
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

#### Permission Issues (Windows)

```bash
# Create storage link
php artisan storage:link

# Fix directory permissions
icacls storage /grant:r "$env:USERNAME:(OI)(CI)F" /T
icacls bootstrap\cache /grant:r "$env:USERNAME:(OI)(CI)F" /T

# Or use development helper
.\scripts\dev\dev-helpers.ps1 clean
```

#### WebSocket/Real-time Issues

```bash
# 1. Ensure Reverb is running
php artisan reverb:start

# 2. Check .env settings
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001

# 3. Test WebSocket connection
# Visit: http://127.0.0.1:8000 and check browser console
```

#### Environment Issues

```bash
# PHP version check (requires 8.2.12+)
php --version

# Node.js version check (requires 22.12+)
node --version

# Composer check
composer --version

# Laravel check
php artisan --version

# Complete environment check
.\scripts\dev\start-dev.ps1 -SkipChecks  # Skip if issues
```

---

## Advanced Configuration

### Environment Setup Options

| Method | Setup Time | Configuration | All Services | Team Consistency | Best For |
|--------|------------|---------------|--------------|------------------|----------|
| **Enhanced Scripts** | 2 min | Zero | ✅ | High | **Recommended** |
| Artisan Serve | 1 min | Zero | ❌ | Medium | Quick testing |
| Virtual Host | 5 min | Medium | ✅ | High | Production-like |
| Docker | 10 min | High | ✅ | Highest | CI/CD |

### Service Configuration

#### Email (SMTP)

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
MAIL_ENCRYPTION=tls
```

#### Redis (Caching & Queues)

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
REDIS_PASSWORD=null
```

#### WebSocket (Reverb)

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
REVERB_SCHEME=http
```

#### AI Integration (MCP)

```env
# Laravel MCP Server
MCP_ENABLED=true
MCP_SERVER_PORT=3000

# AI Chatbot (if using)
OLLAMA_HOST=http://127.0.0.1:11434
AWS_BEDROCK_REGION=us-east-1
```

### Development Profiles Explained

#### Minimal Profile

```bash
.\scripts\dev\start-dev.ps1 -Profile minimal
# Services: Laravel + Vite only
# Use for: Quick testing, minimal resource usage
```

#### Backend Profile

```bash
.\scripts\dev\start-dev.ps1 -Profile backend
# Services: Redis + Laravel + Reverb + Queue
# Use for: API development, backend testing
```

#### AI Profile

```bash
.\scripts\dev\start-dev.ps1 -Profile ai
# Services: Full + MCP + Ollama integration
# Use for: AI chatbot development, MCP testing
```

### Learning Resources

#### Core Technologies

- **Laravel 12**: <https://laravel.com/docs/12.x>
- **Livewire 3**: <https://livewire.laravel.com/docs/3.x>
- **Filament 4**: <https://filamentphp.com/docs/4.x>
- **Tailwind CSS 4**: <https://tailwindcss.com/docs>
- **Alpine.js 3**: <https://alpinejs.dev/start-here>

#### ICTServe Documentation

- **System Overview**: `docs/D00_SYSTEM_OVERVIEW.md`
- **Requirements**: `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md`
- **Design**: `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md`
- **Technology Stack**: `.kiro/steering/tech.md`
- **Project Structure**: `.kiro/steering/structure.md`
- **Development Guidelines**: `.kiro/steering/behavior.md`

---

## Additional Resources & Support

### Script Documentation

- **Enhanced Development Scripts**: `scripts/dev/README.md`
- **Development Helpers**: `scripts/dev/dev-helpers.ps1 help`
- **Service Profiles**: See script documentation for detailed profiles

### ICTServe Documentation

- **Complete System Guide**: `docs/D00_SYSTEM_OVERVIEW.md`
- **Development Guidelines**: `.kiro/steering/behavior.md`
- **Laravel Boost Integration**: `.kiro/steering/laravel-boost.md`
- **Technology Stack**: `.kiro/steering/tech.md`
- **Project Structure**: `.kiro/steering/structure.md`

### Compliance & Standards

- **PDPA 2010**: Malaysian privacy law compliance
- **WCAG 2.2 AA**: Accessibility standards (4.5:1 text, 3:1 UI contrast)
- **PSR-12**: PHP coding standards (enforced via Laravel Pint)
- **MyGOV Standards**: Malaysian government digital service requirements

### Getting Help

#### Self-Service Diagnostics

```bash
# System information
php artisan about

# Service status
.\scripts\dev\dev-helpers.ps1 status

# View logs
.\scripts\dev\dev-helpers.ps1 logs

# Environment check
.\scripts\dev\start-dev.ps1 -SkipChecks
```

#### Common Log Locations

- **Laravel Logs**: `storage/logs/laravel.log`
- **Web Server Logs**: Check Laragon/XAMPP logs
- **Browser Console**: F12 → Console tab
- **Queue Logs**: Laravel Horizon or queue worker terminal

#### Support Escalation

1. **Documentation**: Check relevant guides above
2. **Diagnostics**: Run `php artisan about` and `.\scripts\dev\dev-helpers.ps1 status`
3. **Logs**: Check `storage/logs/laravel.log` for errors
4. **Guidelines**: Review `.kiro/steering/behavior.md`
5. **Contact**: Development team or system administrator

---

## Quick Reference Card

### Essential Commands

```bash
# Setup (first time)
composer install && npm install
cp .env.example .env && php artisan key:generate
php artisan migrate --seed

# Daily development
.\scripts\dev\start-dev.ps1                    # Start all services
.\scripts\dev\dev-helpers.ps1 test             # Run tests
.\scripts\dev\dev-helpers.ps1 format           # Format code
.\scripts\dev\dev-helpers.ps1 status           # Check services

# Quality checks (before commits)
vendor/bin/pint                                # PSR-12 formatting
vendor/bin/phpstan analyse                     # Static analysis
npm run build                                  # Build assets
```

### Service URLs

- **App**: <http://127.0.0.1:8000>
- **Admin**: <http://127.0.0.1:8000/admin>
- **Telescope**: <http://127.0.0.1:8000/telescope>
- **Pulse**: <http://127.0.0.1:8000/pulse>

### Default Credentials

- **Admin**: <admin@motac.gov.my> / password
- **Staff**: <staff@motac.gov.my> / password

---

**ICTServe v3.6.0** | **Laravel 12.42.0** | **Production Ready** ✅  
**Last Updated**: December 16, 2025
