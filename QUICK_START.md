# ICTServe Quick Start Guide

**Last Updated**: 2025-12-09  
**Version**: 3.6.0

---

## Table of Contents

1. [First Time Setup](#first-time-setup)
2. [Development Environment Options](#development-environment-options)
3. [Starting Development Services](#starting-development-services)
4. [Common Commands](#common-commands)
5. [Troubleshooting](#troubleshooting)
6. [Next Steps](#next-steps)

---

## First Time Setup

### 1. Install Dependencies

```bash
composer install
npm install
```

### 2. Configure Environment

```bash
# Copy environment file
cp .env.example .env

# Generate application key
php artisan key:generate
```

### 3. Setup Database

```bash
# Run migrations and seeders
php artisan migrate --seed
```

### 4. Setup Redis (Windows WSL)

```bash
# Start WSL
wsl.exe

# Install Redis
sudo apt update
sudo apt install redis-server

# Enable and start Redis
sudo systemctl enable redis-server
sudo systemctl start redis-server

# Test connection
redis-cli ping  # Should return PONG
```

---

## Development Environment Options

Choose the setup that best fits your workflow:

### Option 1: Automated Development Scripts (Recommended)

**Best for:** Quick setup, all services running, hot reload

**Start all services:**

```powershell
# PowerShell (Recommended)
.\scripts\dev\start-dev.ps1

# Command Prompt
scripts\dev\start-dev.bat

# Git Bash
./scripts/dev/start-dev.sh
```

This launches 5 terminal windows:

- ✅ Redis Server (WSL)
- ✅ Laravel Server (<http://127.0.0.1:8000>)
- ✅ Laravel Reverb (ws://127.0.0.1:6001)
- ✅ Queue Worker
- ✅ Vite Dev Server (HMR)

**Stop all services:**

```powershell
.\scripts\dev\stop-dev.ps1
```

**Setup Time:** 2 minutes  
**Configuration:** Zero  
**Team Consistency:** High

---

### Option 2: Laravel Artisan Server (Minimal)

**Best for:** Quick testing, solo development, minimal services

```bash
# Start Laravel server only
php artisan serve
```

**Access:** <http://127.0.0.1:8000>

**Additional services (optional):**

```bash
# WebSocket server (separate terminal)
php artisan reverb:start

# Queue worker (separate terminal)
php artisan queue:work

# Vite dev server (separate terminal)
npm run dev
```

**Or use combined command:**

```bash
composer run dev
```

**Setup Time:** 1 minute  
**Configuration:** Zero  
**Production-like:** No

---

### Option 3: Apache Virtual Host (Production-like)

**Best for:** Team development, custom domain, production parity

**Automated Setup:**

```powershell
# Run as Administrator
.\setup-vhost.ps1
```

**Access:** <http://ictserve.test>

**Manual Setup:**

See `VHOST_SETUP_GUIDE.md` for step-by-step instructions.

**Setup Time:** 5 minutes  
**Configuration:** Medium  
**Custom Domain:** Yes  
**Requires Admin:** Yes

---

### Option 4: Docker (Containerized)

**Best for:** Consistent environments, CI/CD, production parity

```bash
# Copy Docker environment
cp .env.docker .env

# Start containers
docker-compose up -d

# Run migrations
docker-compose exec app php artisan migrate --seed
```

**Access:** <http://localhost>

**Setup Time:** 10 minutes  
**Configuration:** High  
**Team Consistency:** Highest

---

## Starting Development Services

### Quick Start (All Services)

```powershell
# PowerShell
.\scripts\dev\start-dev.ps1
```

### Individual Services

```bash
# Laravel server
php artisan serve

# WebSocket server (real-time features)
php artisan reverb:start

# Queue worker (background jobs)
php artisan queue:work

# Vite dev server (hot reload)
npm run dev

# Redis server (WSL)
wsl.exe redis-server
```

### Combined Command

```bash
# Start Laravel + Reverb + Queue + Vite
composer run dev
```

---

## Common Commands

### Development

```bash
composer run dev          # Start all services
php artisan serve         # Laravel server only
npm run dev              # Vite dev server only
php artisan reverb:start # WebSocket server only
php artisan queue:work   # Queue worker only
```

### Database

```bash
php artisan migrate              # Run migrations
php artisan migrate:fresh --seed # Reset database
php artisan db:seed              # Run seeders only
php artisan migrate:rollback     # Rollback last migration
```

### Testing

```bash
php artisan test                           # Run all tests
php artisan test --filter=HelpdeskTest    # Run specific test
npx playwright test                        # E2E tests
npm run test                               # Frontend tests
```

### Code Quality

```bash
vendor/bin/pint              # Format PHP code (PSR-12)
vendor/bin/phpstan analyse   # Static analysis
npm run lint                 # Lint frontend code
npm run format               # Format frontend code
npm run quality              # Run all quality checks
```

### Cache Management

```bash
php artisan optimize:clear   # Clear all caches
php artisan config:cache     # Cache configuration
php artisan route:cache      # Cache routes
php artisan view:cache       # Cache views
```

### Build

```bash
npm run build                # Build production assets
npm run build:analyze        # Build with bundle analysis
```

---

## Access Points

### Key URLs

- **Homepage:** <http://127.0.0.1:8000/>
- **Helpdesk Form:** <http://127.0.0.1:8000/helpdesk/create>
- **Loan Application:** <http://127.0.0.1:8000/loan/create>
- **Admin Panel:** <http://127.0.0.1:8000/admin>
- **Dashboard:** <http://127.0.0.1:8000/dashboard> (requires login)
- **Status Checker:** <http://127.0.0.1:8000/status>

### Default Credentials

**Admin Account:**

- Email: `admin@motac.gov.my`
- Password: `password` (change in production)

**Test Staff Account:**

- Email: `staff@motac.gov.my`
- Password: `password`

---

## Troubleshooting

### Port Already in Use

```powershell
# Find process on port 8000
netstat -ano | findstr :8000

# Kill process
taskkill /PID <PID> /F
```

### Routes Return 404

```bash
php artisan route:clear
php artisan config:clear
php artisan optimize:clear
```

### Assets Not Loading

```bash
# Development
npm run dev

# Production
npm run build
```

### Vite Manifest Error

```bash
# Build assets first
npm run build

# OR start dev server
npm run dev
```

### Database Connection Failed

1. Start MySQL in Laragon/XAMPP
2. Verify credentials in `.env`:

   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=ictserve
   DB_USERNAME=root
   DB_PASSWORD=
   ```

3. Check database exists: `ictserve`

### Redis Connection Failed

```bash
# Test Redis connection
wsl.exe redis-cli ping  # Should return PONG

# Start Redis if not running
wsl.exe --user root systemctl start redis-server

# Check Redis status
wsl.exe --user root systemctl status redis-server
```

```text
http://localhost/redis/phpRedisAdmin
```

### Permission Errors

```bash
# Create storage link
php artisan storage:link

# Fix permissions (Windows)
icacls storage /grant:r "$env:USERNAME:(OI)(CI)F" /T
icacls bootstrap\cache /grant:r "$env:USERNAME:(OI)(CI)F" /T
```

### WebSocket Connection Failed

1. Ensure Reverb is running: `php artisan reverb:start`
2. Check `.env` settings:

   ```env
   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=your-app-id
   REVERB_APP_KEY=your-app-key
   REVERB_APP_SECRET=your-app-secret
   ```

3. Verify WebSocket URL: `ws://127.0.0.1:6001`

---

## Environment Comparison

| Feature | Automated Scripts | Artisan Serve | Virtual Host | Docker |
|---------|------------------|---------------|--------------|--------|
| Setup Time | 2 minutes | 1 minute | 5 minutes | 10 minutes |
| Configuration | Zero | Zero | Medium | High |
| All Services | Yes | No | Yes | Yes |
| Custom Domain | No | No | Yes | Yes |
| Hot Reload | Yes | Yes | Yes | Yes |
| Team Consistency | High | Medium | High | Highest |
| Production-like | Medium | No | Yes | Yes |
| Requires Admin | No | No | Yes | No |

---

## Next Steps

### 1. Read Documentation

- **System Overview:** `docs/D00_SYSTEM_OVERVIEW.md`
- **Development Plan:** `docs/D01_SYSTEM_DEVELOPMENT_PLAN.md`
- **Requirements:** `docs/D03_SOFTWARE_REQUIREMENTS_SPECIFICATION.md`
- **Design:** `docs/D04_SOFTWARE_DESIGN_DOCUMENT.md`
- **Technology Stack:** `.kiro/steering/tech.md`
- **Project Structure:** `.kiro/steering/structure.md`

### 2. Configure Services

**Email (SMTP):**

```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your-username
MAIL_PASSWORD=your-password
```

**Redis (Caching & Queues):**

```env
CACHE_STORE=redis
QUEUE_CONNECTION=redis
REDIS_HOST=127.0.0.1
REDIS_PORT=6379
```

**Reverb (WebSocket):**

```env
BROADCAST_CONNECTION=reverb
REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST=127.0.0.1
REVERB_PORT=6001
```

### 3. Development Workflow

```bash
# 1. Start development services
.\scripts\start-dev.ps1

# 2. Make changes to code

# 3. Run tests
php artisan test

# 4. Format code
vendor/bin/pint

# 5. Check for issues
vendor/bin/phpstan analyse

# 6. Commit changes
git add .
git commit -m "feat: your feature description"
```

### 4. Learn the Stack

- **Laravel 12:** <https://laravel.com/docs/12.x>
- **Livewire 3:** <https://livewire.laravel.com/docs/3.x>
- **Filament 4:** <https://filamentphp.com/docs/4.x>
- **Tailwind CSS 4:** <https://tailwindcss.com/docs>
- **Alpine.js 3:** <https://alpinejs.dev/start-here>

---

## Additional Resources

### Documentation

- **Complete Startup Guide:** `scripts/DEV-STARTUP-GUIDE.md`
- **Scripts Documentation:** `scripts/README.md`
- **Virtual Host Setup:** `VHOST_SETUP_GUIDE.md`
- **Laragon Setup:** `LARAGON_SETUP.md`
- **Redis Setup:** `docs/redis/redis-setup.md`
- **MCP Configuration:** `docs/mcp/MCP_CONFIGURATION.md`

### Development Guidelines

- **Behavior Guidelines:** `.kiro/steering/behavior.md`
- **Laravel Boost:** `.kiro/steering/laravel-boost.md`
- **Design System:** `.kiro/steering/design-system.md`
- **Product Overview:** `.kiro/steering/product.md`

### Troubleshooting

- **Apache Alias Test Results:** `APACHE_ALIAS_TEST_RESULTS.md`
- **Migration Guide:** `docs/reference/MIGRATION_v3.6.0.md`
- **Logs:** `storage/logs/laravel.log`
- **Apache Logs:** `storage/logs/apache-error.log`

---

## Support

For issues or questions:

1. **Check Documentation:** Start with relevant guide above
2. **Run Diagnostics:** `php artisan about`
3. **Check Logs:** `storage/logs/laravel.log`
4. **Review Guidelines:** `.kiro/steering/behavior.md`
5. **Contact Support:** <devops@motac.gov.my>

---

**Status:** ✅ Production Ready  
**Environment:** Local Development (XAMPP/Laragon)  
**Last Verified:** 2025-12-09
