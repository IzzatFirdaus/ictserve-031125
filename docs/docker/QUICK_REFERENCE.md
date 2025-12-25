# Docker Quick Reference - ICTServe

**Updated**: December 20, 2024  
**Status**: ✅ Composer issues fixed

## 🚀 Quick Setup

### Automated Setup (Recommended)

```powershell
# Development environment
.\scripts\docker\setup-docker.ps1

# Production environment
.\scripts\docker\setup-docker.ps1 -Environment production -Rebuild
```

```bash
# Linux/macOS/WSL
./scripts/docker/setup-docker.sh

# Production
./scripts/docker/setup-docker.sh --environment production --rebuild
```

### Manual Setup (Legacy)

```powershell
docker compose up -d
docker compose exec app php artisan key:generate
docker compose exec app php artisan migrate --seed
```

## 🐳 Container Management

### Basic Operations

```bash
# Start all services
docker compose up -d

# Stop all services
docker compose down

# Restart specific service
docker compose restart app

# View logs
docker compose logs -f app

# Shell access
docker compose exec app sh
```

### Development vs Production

```bash
# Development (with dev dependencies)
docker compose -f compose.yaml -f compose.dev.yaml up -d

# Production (optimized)
docker compose up -d
```

## 🔧 Application Commands

### Laravel Artisan

```bash
# Run any artisan command
docker compose exec app php artisan <command>

# Common commands
docker compose exec app php artisan migrate
docker compose exec app php artisan db:seed
docker compose exec app php artisan cache:clear
docker compose exec app php artisan config:cache
```

### Composer Operations

```bash
# Install dependencies (in container)
docker compose exec app composer install

# Update dependencies
docker compose exec app composer update

# Show installed packages
docker compose exec app composer show
```

### NPM Operations

```bash
# Install npm dependencies
docker compose exec app npm ci

# Build assets
docker compose exec app npm run build

# Development watch
docker compose exec app npm run dev
```

## 🌐 Service URLs

| Service | URL | Description |
|---------|-----|-------------|
| Application | <http://localhost:8000> | Main Laravel app |
| Admin Panel | <http://localhost:8000/admin> | Filament admin |
| Vite Dev Server | <http://localhost:5173> | Hot reload (dev only) |
| Reverb WebSocket | ws://localhost:8080 | Real-time features |

## 🔑 Default Credentials (Development)

| Role | Email | Password |
|------|-------|----------|
| Superuser | <admin@motac.gov.my> | password |
| Admin | <admin@motac.gov.my> | password |
| Staff | <staff@motac.gov.my> | password |
| Approver | <approver@motac.gov.my> | password |

## 🛠️ Troubleshooting

### Common Issues

| Issue | Solution |
|-------|----------|
| Composer errors | Use new setup script (fixes dependency conflicts) |
| 502 Bad Gateway | `docker compose restart nginx` |
| Permission denied | `docker compose exec app chmod -R 775 storage` |
| Port conflicts | Change ports in compose.yaml |
| Build failures | `docker compose build --no-cache` |

### Quick Fixes

```bash
# Fix styling issues
docker compose exec app npm run build
docker compose exec app php artisan view:clear
docker compose restart app

# Reset database
docker compose exec app php artisan migrate:fresh --seed

# Clean rebuild
docker compose down -v
docker compose build --no-cache
docker compose up -d
```

## 📊 Health Checks

### Service Status

```bash
# Check all services
docker compose ps

# Expected output: all services "running"
```

### Application Health

```bash
# Test application
curl http://localhost:8000

# Test database connection
docker compose exec app php artisan tinker --execute="echo DB::connection()->getDatabaseName();"

# Test Redis connection
docker compose exec app php artisan tinker --execute="echo cache()->get('test') ?? 'Redis working';"
```

## 🔄 Updates & Maintenance

### Update Application

```bash
# Pull latest code
git pull origin main

# Rebuild containers
docker compose build --no-cache

# Restart services
docker compose up -d

# Run migrations
docker compose exec app php artisan migrate
```

### Clean Up

```bash
# Remove stopped containers
docker compose down

# Remove unused images
docker system prune -f

# Remove all (nuclear option)
docker compose down -v
docker system prune -a -f
```

## 📁 Important Files

| File | Purpose |
|------|---------|
| `Dockerfile` | Container build instructions |
| `compose.yaml` | Production services |
| `compose.dev.yaml` | Development overrides |
| `.env.docker` | Container environment variables |
| `scripts/docker/setup-docker.*` | Automated setup scripts |

## 🆘 Getting Help

1. **Check logs**: `docker compose logs -f app`
2. **Review documentation**: [docs/docker/](.)
3. **Composer issues**: [COMPOSER_ISSUES_FIXED.md](COMPOSER_ISSUES_FIXED.md)
4. **General troubleshooting**: [troubleshooting.md](troubleshooting.md)

## ✅ What's Fixed

- ✅ **Composer install issues** - Dependencies now installed in container
- ✅ **Host-container conflicts** - Anonymous volumes prevent conflicts
- ✅ **Inconsistent environments** - Container manages its own dependencies
- ✅ **Build failures** - Proper dependency management
- ✅ **Performance issues** - Optimized volume strategy

---

**Need more details?** See [setup.md](setup.md) for comprehensive guide.
