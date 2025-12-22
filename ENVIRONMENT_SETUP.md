# ICTServe Environment Configuration Guide

**Last Updated**: December 22, 2024  
**Version**: 3.6.0

## 🚨 SECURITY NOTICE

**Environment files containing secrets are now properly protected from accidental commits.**

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

## Environment Files Status

### ✅ Safe (Can be committed)
- `.env.example` - Main template
- `.env.*.example` - All example templates

### 🚫 Protected (Never committed)
- `.env` - Your main environment file
- `.env.bak` - Your backup with API keys
- `.env.laragon` - Laragon configuration
- `.env.workspace` - Docker configuration
- `.env.docker` - Docker configuration
- `.env.testing` - Testing configuration
- `.env.staging` - Staging configuration
- `.env.xampp` - XAMPP configuration

## Quick Setup

### For Docker Development (Workspace Default)
```bash
# The .env file is already configured for Docker
# Start the containers
docker compose up -d

# Run development server
composer run dev
```

### For XAMPP Development (Non-Workspace)
```bash
# Copy XAMPP template
cp .env.example .env.xampp
# Configure for XAMPP (MySQL on 127.0.0.1, local Redis)
# Add your API keys to .env.xampp

# Or use the switcher script
.\scripts\switch-env.ps1 -env xampp
```

### For Laragon Development
```bash
# Copy Laragon template
cp .env.example .env
# Configure for Laragon (MySQL on 127.0.0.1, WSL Redis)
# Add your API keys to .env

# Or use the switcher script
.\scripts\switch-env.ps1 -env laragon
```

## Environment Files

### `.env` (Docker - Workspace Default)

The main `.env` file is now configured for Docker development by default. This is the workspace standard configuration.

**Key Settings:**
- `DB_HOST=db` (Docker container)
- `REDIS_HOST=redis` (Docker container)
- `APP_URL=http://localhost:8000`
- `CACHE_STORE=redis`
- `QUEUE_CONNECTION=redis`
- `SESSION_DRIVER=redis`

### `.env.xampp` (XAMPP - Non-Workspace)

For traditional XAMPP development with local services.

**Key Settings:**
- `DB_HOST=127.0.0.1` (Local MySQL)
- `REDIS_HOST=127.0.0.1` (Local Redis)
- `APP_URL=http://127.0.0.1:8000`
- `CACHE_STORE=file`
- `QUEUE_CONNECTION=database`
- `SESSION_DRIVER=file`

## Security Changes

**Previous Issue**: Environment configuration files were accidentally committed to Git, creating a security risk.

**Resolution**: 
- Removed all environment-specific files from Git tracking
- Added comprehensive `.env.*` patterns to `.gitignore`
- Created security documentation and guidelines

**Impact**: No actual secrets were exposed, but future incidents are now prevented.

## Development Workflows

### Docker Workflow (Recommended)

1. **Initial Setup**:
   ```bash
   # Clone repository
   git clone <repository-url>
   cd ictserve-031125
   
   # Start containers
   docker compose up -d
   
   # Install dependencies
   composer install
   npm install
   
   # Run migrations
   php artisan migrate --seed
   ```

2. **Daily Development**:
   ```bash
   # Start services
   docker compose up -d
   
   # Run development server
   composer run dev
   
   # In separate terminal for frontend
   npm run dev
   ```

3. **Stopping Services**:
   ```bash
   docker compose down
   ```

### XAMPP Workflow

1. **Initial Setup**:
   ```bash
   # Clone repository
   git clone <repository-url>
   cd ictserve-031125
   
   # Copy environment file
   cp .env.example .env.xampp
   
   # Switch to XAMPP environment
   .\scripts\switch-env.ps1 -env xampp
   
   # Install dependencies
   composer install
   npm install
   
   # Start XAMPP services
   # (Start Apache and MySQL in XAMPP Control Panel)
   
   # Run migrations
   php artisan migrate --seed
   ```

2. **Daily Development**:
   ```bash
   # Start XAMPP services (Apache, MySQL)
   # Start Redis if using caching
   
   # Run development server
   .\scripts\dev\start-dev.ps1
   
   # Or manually:
   php artisan serve --host=127.0.0.1 --port=8000
   npm run dev
   ```

## Environment Switching

Use the provided scripts to switch between environments:

```bash
# Switch to Docker environment
.\scripts\switch-env.ps1 -env docker

# Switch to XAMPP environment  
.\scripts\switch-env.ps1 -env xampp

# Switch to Laragon environment
.\scripts\switch-env.ps1 -env laragon
```

## Troubleshooting

### Common Issues

1. **Port Conflicts**:
   - Docker: Ensure ports 8000, 3306, 6379, 8080 are available
   - XAMPP: Ensure ports 80, 443, 3306 are available

2. **Permission Issues**:
   - Windows: Run PowerShell as Administrator
   - Ensure Docker Desktop is running (for Docker workflow)

3. **Database Connection**:
   - Docker: Use `db` as hostname
   - XAMPP: Use `127.0.0.1` as hostname

4. **Redis Connection**:
   - Docker: Use `redis` as hostname
   - XAMPP: Install and start Redis locally

### Getting Help

1. Check the specific deployment documentation:
   - [Docker Setup](docs/docker/SETUP.md)
   - [XAMPP Deployment](deployment/xampp/README.md)
2. Review [TROUBLESHOOTING.md](docs/docker/TROUBLESHOOTING.md) for common issues
3. Check [docs/INDEX.md](docs/INDEX.md) for comprehensive documentation

## Need Help?

- Read `docs/ENVIRONMENT_SECURITY.md` for detailed security guidelines
- Use the environment switcher scripts in `scripts/`
- Contact the development team for configuration questions

---
**Remember**: Keep your API keys local and never commit environment files with secrets!
