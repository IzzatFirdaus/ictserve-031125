# ICTServe Environment Switcher Scripts

This directory contains comprehensive PowerShell scripts to manage ICTServe development environments, allowing seamless switching between XAMPP (local) and Docker environments.

## 🚀 Quick Start

### Interactive Menu

```powershell
.\scripts\quick-switch.ps1
```

### Direct Commands

```powershell
# Check current status
.\scripts\environment-status.ps1 -ShowDetails -CheckConnectivity

# Switch to Docker environment
.\scripts\swap-environment.ps1 -Environment docker

# Switch to XAMPP environment  
.\scripts\swap-environment.ps1 -Environment xampp
```

## 📁 Script Overview

### Main Scripts

| Script | Purpose | Usage |
|--------|---------|-------|
| `quick-switch.ps1` | Interactive menu for all operations | `.\scripts\quick-switch.ps1` |
| `swap-environment.ps1` | Main environment switcher | `.\scripts\swap-environment.ps1 -Environment [xampp\|docker]` |
| `environment-status.ps1` | Check status of all services | `.\scripts\environment-status.ps1 -ShowDetails` |

### XAMPP Scripts (`xampp/`)

| Script | Purpose | Usage |
|--------|---------|-------|
| `start-xampp-services.ps1` | Start XAMPP services | `.\scripts\xampp\start-xampp-services.ps1` |
| `stop-xampp-services.ps1` | Stop XAMPP services | `.\scripts\xampp\stop-xampp-services.ps1 -Force` |

### Docker Scripts (`docker/`)

| Script | Purpose | Usage |
|--------|---------|-------|
| `start-docker-services.ps1` | Start Docker containers | `.\scripts\docker\start-docker-services.ps1` |
| `stop-docker-services.ps1` | Stop Docker containers | `.\scripts\docker\stop-docker-services.ps1` |

## 🔧 Environment Types

### XAMPP Environment

- **Database Host**: `127.0.0.1:3306`
- **Application URL**: `http://127.0.0.1:8000`
- **Requirements**: XAMPP/Laragon/WAMP installed
- **Services**: Apache, MySQL, Redis (optional)

### Docker Environment

- **Database Host**: `db` (container name)
- **Application URL**: `http://localhost:8000`
- **Requirements**: Docker Desktop running
- **Services**: All services containerized

## 📋 Detailed Usage

### 1. Environment Status Check

```powershell
# Basic status check
.\scripts\environment-status.ps1

# Detailed status with connectivity tests
.\scripts\environment-status.ps1 -ShowDetails -CheckConnectivity
```

**Output includes**:

- Current environment configuration
- XAMPP service status
- Docker service status
- Laravel development services
- Connectivity test results
- Recommendations

### 2. Environment Switching

```powershell
# Switch to Docker (with confirmation)
.\scripts\swap-environment.ps1 -Environment docker

# Switch to XAMPP (force, no confirmation)
.\scripts\swap-environment.ps1 -Environment xampp -Force

# Switch with backup of current .env
.\scripts\swap-environment.ps1 -Environment docker -Backup

# Switch and skip validation
.\scripts\swap-environment.ps1 -Environment xampp -SkipValidation
```

**The swap process**:

1. Checks prerequisites
2. Backs up current environment (if requested)
3. Stops current services
4. Switches environment files
5. Updates configuration
6. Starts target environment services
7. Initializes Laravel application
8. Validates the switch
9. Shows post-switch information

### 3. XAMPP Service Management

```powershell
# Start XAMPP services
.\scripts\xampp\start-xampp-services.ps1

# Start XAMPP with Redis
.\scripts\xampp\start-xampp-services.ps1 -StartRedis

# Stop XAMPP services
.\scripts\xampp\stop-xampp-services.ps1

# Force stop without confirmation
.\scripts\xampp\stop-xampp-services.ps1 -Force

# Stop including Redis
.\scripts\xampp\stop-xampp-services.ps1 -StopRedis
```

### 4. Docker Service Management

```powershell
# Start Docker services
.\scripts\docker\start-docker-services.ps1

# Start with image rebuild
.\scripts\docker\start-docker-services.ps1 -Build

# Stop Docker services
.\scripts\docker\stop-docker-services.ps1

# Stop and remove volumes (WARNING: deletes data)
.\scripts\docker\stop-docker-services.ps1 -RemoveVolumes

# Stop and remove images
.\scripts\docker\stop-docker-services.ps1 -RemoveImages
```

## 🌐 Service URLs

### XAMPP Environment

- **Application**: <http://127.0.0.1:8000>
- **Apache**: <http://127.0.0.1>
- **phpMyAdmin**: <http://127.0.0.1/phpmyadmin>
- **Reverb WebSocket**: ws://127.0.0.1:8080
- **Database**: 127.0.0.1:3306 (user: root, password: empty)
- **Redis**: 127.0.0.1:6379

### Docker Environment

- **Application**: <http://localhost:8000>
- **phpMyAdmin**: <http://localhost:8080>
- **Reverb WebSocket**: ws://localhost:8080
- **Database**: localhost:3306 (user: laravel, password: secret)
- **Redis**: localhost:6379

### Laravel Services (Both Environments)

- **Horizon Dashboard**: /horizon
- **Telescope Debug**: /telescope
- **Pulse Monitoring**: /pulse
- **Filament Admin**: /admin

## ⚠️ Important Notes

### Prerequisites

- **PowerShell 5.1+** required
- **Docker Desktop** for Docker environment
- **XAMPP/Laragon/WAMP** for XAMPP environment
- **Composer** and **Node.js** installed

### Best Practices

1. **Always check status** before switching environments
2. **Stop services** before switching to avoid conflicts
3. **Use 127.0.0.1** instead of localhost for XAMPP
4. **Allow time** for Docker containers to start
5. **Backup important data** before major changes

### Common Issues & Solutions

#### Docker Issues

```powershell
# Docker Desktop not running
# Solution: Start Docker Desktop and wait for it to be ready

# Containers won't start
# Solution: Check Docker logs
docker-compose logs

# Port conflicts
# Solution: Stop conflicting services or change ports
```

#### XAMPP Issues

```powershell
# Services won't start
# Solution: Check XAMPP Control Panel, ensure ports are free

# Database connection failed
# Solution: Verify MySQL is running and credentials are correct

# Permission issues
# Solution: Run PowerShell as Administrator
```

#### Laravel Issues

```powershell
# Application key not set
php artisan key:generate

# Database not migrated
php artisan migrate

# Assets not built
npm run build
```

## 🔍 Troubleshooting

### Debug Mode
All scripts support verbose output for troubleshooting:

```powershell
# Enable verbose output
$VerbosePreference = "Continue"
.\scripts\swap-environment.ps1 -Environment docker -Verbose
```

### Log Files
Check these locations for logs:

- **Docker**: `docker-compose logs`
- **XAMPP**: XAMPP Control Panel logs
- **Laravel**: `storage/logs/laravel.log`

### Manual Recovery
If scripts fail, you can manually:

1. **Reset environment file**:

   ```powershell
   Copy-Item .env.example .env
   ```

2. **Stop all services**:

   ```powershell
   docker-compose down
   # Stop XAMPP via Control Panel
   ```

3. **Clear Laravel caches**:

   ```powershell
   php artisan config:clear
   php artisan cache:clear
   ```

## 🚀 Development Workflow

### Typical Development Session

1. **Check current status**:

   ```powershell
   .\scripts\environment-status.ps1
   ```

2. **Switch to preferred environment**:

   ```powershell
   .\scripts\swap-environment.ps1 -Environment docker
   ```

3. **Start development services**:

   ```powershell
   # Laravel dev server
   php artisan serve --host=127.0.0.1
   
   # Reverb WebSocket (optional)
   php artisan reverb:start
   ```

4. **Develop and test**

5. **Stop services when done**:

   ```powershell
   .\scripts\docker\stop-docker-services.ps1
   # or
   .\scripts\xampp\stop-xampp-services.ps1
   ```

### Team Collaboration

- **Consistent environments**: All team members can use the same scripts
- **Easy onboarding**: New developers can quickly set up their environment
- **Flexible development**: Switch between environments as needed
- **Automated setup**: Scripts handle complex configuration automatically

## 📞 Support

If you encounter issues:

1. **Check the status** first: `.\scripts\environment-status.ps1 -ShowDetails`
2. **Review the logs** for specific error messages
3. **Try manual steps** if scripts fail
4. **Consult the troubleshooting section** above
5. **Ask the development team** for assistance

---

**Version**: 1.0.0  
**Author**: ICTServe Development Team  
**Last Updated**: December 2024
