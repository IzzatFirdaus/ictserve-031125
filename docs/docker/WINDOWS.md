# Windows Docker Guide

PowerShell scripts for Docker operations on Windows (Makefile alternative).

## Prerequisites

- Windows 10/11 with WSL2
- Docker Desktop for Windows
- PowerShell 5.1+ or PowerShell Core 7+

## Quick Reference

| Task | Command |
|------|---------|
| Build containers | `.\scripts\docker\build.ps1` |
| Start services | `docker compose up -d` |
| Stop services | `docker compose down` |
| View logs | `docker compose logs -f app` |
| Run artisan | `.\scripts\docker\artisan.ps1 migrate` |
| Run composer | `.\scripts\docker\composer.ps1 install` |
| Run npm | `.\scripts\docker\npm.ps1 run build` |
| Shell access | `docker compose exec app sh` |

## PowerShell Scripts

### Build Containers

```powershell
# Build all containers
.\scripts\docker\build.ps1

# Build specific service
.\scripts\docker\build.ps1 app
```

### Artisan Commands

```powershell
# Run migrations
.\scripts\docker\artisan.ps1 migrate

# Seed database
.\scripts\docker\artisan.ps1 db:seed

# Generate key
.\scripts\docker\artisan.ps1 key:generate

# Create Filament user
.\scripts\docker\artisan.ps1 make:filament-user
```

### Composer Commands

```powershell
# Install dependencies
.\scripts\docker\composer.ps1 install

# Update dependencies
.\scripts\docker\composer.ps1 update

# Install production dependencies
.\scripts\docker\composer.ps1 install --no-dev --optimize-autoloader
```

### npm Commands

```powershell
# Install dependencies
.\scripts\docker\npm.ps1 install

# Build assets
.\scripts\docker\npm.ps1 run build

# Development watch
.\scripts\docker\npm.ps1 run dev
```

## Common Tasks

### Initial Setup

```powershell
# Clone repository
git clone https://github.com/IzzatFirdaus/ictserve-031125.git
cd ictserve-031125

# Copy environment file
Copy-Item .env.example .env

# Build and start
.\scripts\docker\build.ps1
docker compose up -d

# Initialize application
.\scripts\docker\artisan.ps1 key:generate
.\scripts\docker\artisan.ps1 migrate --seed
.\scripts\docker\artisan.ps1 make:filament-user

# Access application
Start-Process http://localhost:8000
```

### Daily Development

```powershell
# Start services
docker compose up -d

# View logs
docker compose logs -f app

# Run migrations
.\scripts\docker\artisan.ps1 migrate

# Stop services
docker compose down
```

### Troubleshooting

```powershell
# Restart services
docker compose restart app nginx

# Rebuild containers
.\scripts\docker\build.ps1
docker compose up -d --force-recreate

# Clear caches
.\scripts\docker\artisan.ps1 cache:clear
.\scripts\docker\artisan.ps1 config:clear
.\scripts\docker\artisan.ps1 route:clear
```

## Frontend Assets

Node.js and npm are installed in the app container:

```powershell
# Build assets in container
.\scripts\docker\npm.ps1 run build

# Or on host machine (faster for development)
npm install
npm run dev
```

## Makefile Alternative

Windows doesn't have `make` by default. Use these PowerShell equivalents:

| Makefile | PowerShell |
|----------|------------|
| `make build` | `.\scripts\docker\build.ps1` |
| `make up` | `docker compose up -d` |
| `make down` | `docker compose down` |
| `make restart` | `docker compose restart` |
| `make logs` | `docker compose logs -f` |
| `make shell` | `docker compose exec app sh` |
| `make artisan cmd="migrate"` | `.\scripts\docker\artisan.ps1 migrate` |
| `make composer cmd="install"` | `.\scripts\docker\composer.ps1 install` |
| `make npm cmd="run build"` | `.\scripts\docker\npm.ps1 run build` |

## Installing Make on Windows (Optional)

If you prefer using Makefile:

### Option 1: Chocolatey

```powershell
# Install Chocolatey (if not installed)
Set-ExecutionPolicy Bypass -Scope Process -Force
[System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072
iex ((New-Object System.Net.WebClient).DownloadString('https://community.chocolatey.org/install.ps1'))

# Install make
choco install make
```

### Option 2: WSL2

```powershell
# Use make inside WSL2
wsl make build
wsl make up
```

## Execution Policy

If scripts won't run, set execution policy:

```powershell
# Check current policy
Get-ExecutionPolicy

# Set policy (run as Administrator)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Or bypass for single script
powershell -ExecutionPolicy Bypass -File .\scripts\docker\build.ps1
```

## Docker Desktop Settings

Recommended settings for Windows:

1. **Resources** → **Advanced**:
   - CPUs: 4+
   - Memory: 4GB+
   - Swap: 1GB

2. **General**:
   - ✅ Use WSL 2 based engine
   - ✅ Start Docker Desktop when you log in

3. **Docker Engine**:

   ```json
   {
     "builder": {
       "gc": {
         "enabled": true,
         "defaultKeepStorage": "20GB"
       }
     }
   }
   ```

## Troubleshooting Windows-Specific Issues

### WSL2 Integration

If containers can't access files:

```powershell
# Check WSL2 integration
docker context ls

# Restart Docker Desktop
Restart-Service -Name com.docker.service
```

### File Permissions

Windows file permissions may cause issues:

```powershell
# Fix inside container
docker compose exec app chmod -R 775 storage bootstrap/cache
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
```

### Line Endings

Git may convert LF to CRLF on Windows:

```powershell
# Configure Git to preserve LF
git config --global core.autocrlf input

# Re-clone repository
```

## Next Steps

- [Setup Guide](SETUP.md) - General Docker setup
- [Troubleshooting](TROUBLESHOOTING.md) - Common issues
- [Architecture](ARCHITECTURE.md) - Container design
