# ICTServe Environment Configuration Guide

**Last Updated**: December 22, 2024  
**Version**: 3.6.0

## Overview

ICTServe now supports two distinct development environments:

1. **Docker (Workspace Default)** - Containerized development with consistent environments
2. **XAMPP (Non-Workspace)** - Traditional local development with XAMPP stack

## Quick Reference

| Aspect | Docker (Workspace) | XAMPP (Non-Workspace) |
|--------|-------------------|----------------------|
| **Configuration File** | `.env` (default) | `.env.xampp` |
| **Database Host** | `db` (container) | `127.0.0.1` (local) |
| **Redis Host** | `redis` (container) | `127.0.0.1` (local) |
| **App URL** | `http://localhost:8000` | `http://127.0.0.1:8000` |
| **Cache Store** | `redis` | `file` |
| **Queue Connection** | `redis` | `database` |
| **Session Driver** | `redis` | `file` |
| **Start Command** | `docker compose up -d` | `.\scripts\dev\start-dev.ps1` |

## Environment Files

### `.env` (Docker - Workspace Default)

The main `.env` file is now configured for Docker development by default. This is the workspace standard configuration.

**Key Settings:**

```env
DB_HOST=db
REDIS_HOST=redis
APP_URL=http://localhost:8000
CACHE_STORE=redis
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
```

### `.env.xampp` (XAMPP - Non-Workspace)

Complete XAMPP configuration for local development outside the workspace.

**Key Settings:**

```env
DB_HOST=127.0.0.1
REDIS_HOST=127.0.0.1
APP_URL=http://127.0.0.1:8000
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

### `.env.docker` (Legacy)

Maintained for backward compatibility. Contains Docker-specific overrides.

## Switching Environments

### Using PowerShell Script (Recommended)

```powershell
# Switch to Docker (workspace default)
.\scripts\switch-env.ps1 -env docker

# Switch to XAMPP (non-workspace)
.\scripts\switch-env.ps1 -env xampp

# Force switch without confirmation
.\scripts\switch-env.ps1 -env xampp -Force
```

### Using Bash Script (Linux/macOS/WSL)

```bash
# Switch to Docker
./scripts/switch-env.sh docker

# Switch to XAMPP
./scripts/switch-env.sh xampp

# Force switch without confirmation
./scripts/switch-env.sh xampp --force
```

### Manual Switching

```bash
# To Docker
copy .env.docker .env

# To XAMPP
copy .env.xampp .env
```

## Docker Development (Workspace Default)

### Quick Start

```bash
# Complete setup with automated script
.\scripts\docker-rebuild.ps1

# Or manual setup
docker compose up -d
```

### Services

- **app**: PHP 8.4-FPM with Laravel
- **nginx**: Web server (port 8000)
- **db**: MySQL 8.0 (port 3306)
- **redis**: Redis 7.0 (port 6379)
- **reverb**: Laravel Reverb WebSocket server
- **queue**: Laravel queue worker

### Access URLs

- Application: <http://localhost:8000>
- Admin Panel: <http://localhost:8000/admin>
- Horizon: <http://localhost:8000/horizon>
- Telescope: <http://localhost:8000/telescope>
- Pulse: <http://localhost:8000/pulse>

### Common Commands

```bash
# Start services
docker compose up -d

# View logs
docker compose logs -f app

# Execute commands in container
docker compose exec app php artisan migrate
docker compose exec app composer install
docker compose exec app npm run build

# Stop services
docker compose down

# Clean restart
docker compose down -v
docker compose up -d --build
```

## XAMPP Development (Non-Workspace)

### Prerequisites

- PHP 8.4.11+ with required extensions
- Node.js 22.12+
- Composer latest
- XAMPP with MySQL 8.0+
- Redis (optional but recommended)

### Quick Start

```bash
# 1. Switch to XAMPP configuration
.\scripts\switch-env.ps1 -env xampp

# 2. Install dependencies
composer install && npm install

# 3. Setup database
mysql -u root -p -e "CREATE DATABASE ictserve;"
php artisan migrate --seed

# 4. Start development services
.\scripts\dev\start-dev.ps1
```

### Services

- **Laravel Server**: <http://127.0.0.1:8000>
- **Vite Dev Server**: <http://127.0.0.1:5173>
- **Laravel Reverb**: ws://127.0.0.1:8080
- **Queue Workers**: Background job processing
- **Redis**: Cache/Queue/Session (optional)

### Common Commands

```bash
# Start all services
.\scripts\dev\start-dev.ps1

# Individual services (separate terminals)
php artisan serve              # Laravel server
php artisan reverb:start       # WebSocket server
php artisan queue:work         # Background jobs
npm run dev                    # Vite dev server

# Development helpers
.\scripts\dev\dev-helpers.ps1 test      # Run tests
.\scripts\dev\dev-helpers.ps1 format    # Format code
.\scripts\dev\dev-helpers.ps1 analyse   # Static analysis
```

## Migration Guide

### From XAMPP to Docker

```bash
# 1. Backup current .env
copy .env .env.backup

# 2. Switch to Docker configuration
.\scripts\switch-env.ps1 -env docker

# 3. Start Docker services
docker compose up -d

# 4. Run migrations (if needed)
docker compose exec app php artisan migrate
```

### From Docker to XAMPP

```bash
# 1. Backup current .env
copy .env .env.backup

# 2. Switch to XAMPP configuration
.\scripts\switch-env.ps1 -env xampp

# 3. Start XAMPP services (Apache, MySQL)

# 4. Start Laravel services
.\scripts\dev\start-dev.ps1
```

## Troubleshooting

### Docker Issues

**Port Already in Use:**

```bash
# Check what's using port 8000
netstat -ano | findstr :8000

# Stop conflicting services
docker compose down
```

**Database Connection Failed:**

```bash
# Verify .env settings
DB_HOST=db  # NOT 127.0.0.1

# Check database is running
docker compose ps db

# Test connection
docker compose exec app php artisan db:show
```

**Permission Issues:**

```bash
# Fix ownership (Linux/WSL)
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### XAMPP Issues

**Port Already in Use:**

```bash
# Check what's using port 8000
netstat -ano | findstr :8000

# Kill process (replace PID)
taskkill /PID <PID> /F

# Or use different port
php artisan serve --port=8001
```

**Redis Connection Failed:**

```bash
# Check Redis status (WSL)
wsl redis-cli ping

# Start Redis (WSL)
wsl sudo systemctl start redis-server

# Or disable Redis in .env
CACHE_STORE=file
QUEUE_CONNECTION=database
SESSION_DRIVER=file
```

**Database Connection Error:**

```bash
# Verify database exists
mysql -u root -p -e "SHOW DATABASES;"

# Create database if missing
mysql -u root -p -e "CREATE DATABASE ictserve;"

# Check .env settings
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=ictserve
DB_USERNAME=root
DB_PASSWORD=
```

## Best Practices

### Workspace Development (Docker)

1. **Always use Docker** for workspace development
2. **Never commit** `.env` file with secrets
3. **Use docker-rebuild.ps1** for clean setups
4. **Check logs** with `docker compose logs -f app`
5. **Clean up** with `docker compose down -v` when needed

### Non-Workspace Development (XAMPP)

1. **Use .env.xampp** as template
2. **Switch environments** with provided scripts
3. **Keep Redis optional** for flexibility
4. **Use dev-helpers.ps1** for common tasks
5. **Monitor services** with `dev-helpers.ps1 status`

## Additional Resources

- **Quick Start Guide**: [QUICK_START.md](QUICK_START.md)
- **Docker Documentation**: [Docker Compose Docs](https://docs.docker.com/compose/)
- **Laravel Documentation**: [Laravel 12 Docs](https://laravel.com/docs/12.x)
- **Redis WSL Setup**: [docs/redis/WSL_SETUP.md](docs/redis/WSL_SETUP.md)
- **Horizon WSL Setup**: [docs/horizon/HORIZON_WSL_SETUP.md](docs/horizon/HORIZON_WSL_SETUP.md)

## Support

For issues or questions:

1. Check [QUICK_START.md](QUICK_START.md) for setup instructions
2. Review [TROUBLESHOOTING.md](TROUBLESHOOTING.md) for common issues
3. Check [docs/INDEX.md](docs/INDEX.md) for comprehensive documentation
