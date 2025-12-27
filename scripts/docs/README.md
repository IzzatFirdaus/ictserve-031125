# ICTServe Development Scripts

This directory contains PowerShell scripts to help manage the ICTServe development environment.

## Environment Management Scripts

### `switch-env.ps1`

Switches between different environment configurations for ICTServe.

**Usage:**
```powershell
# Switch to Docker configuration (workspace)
.\scripts\switch-env.ps1 -env docker

# Switch to Laragon configuration (non-workspace)
.\scripts\switch-env.ps1 -env laragon

# Force overwrite without confirmation
.\scripts\switch-env.ps1 -env docker -Force
```

**Supported Environments:**
- `laragon` - Laragon with local MySQL and WSL Redis
- `docker` - Docker Compose with containerized services
- `workspace` - Alias for docker configuration

**What it does:**
- Copies appropriate `.env` file (`.env.example` or `.env.workspace`)
- Updates MCP configuration (`.kiro/settings/mcp.json`)
- Creates backup of existing configuration
- Generates application key if needed
- Displays next steps for the selected environment

### `laragon-start.ps1`

Quick start script for ICTServe Laragon development environment.

**Usage:**
```powershell
# Standard Laragon startup
.\scripts\laragon-start.ps1

# Setup with automatic Redis installation
.\scripts\laragon-start.ps1 -InstallRedis

# Skip Redis setup (use file-based cache)
.\scripts\laragon-start.ps1 -SkipRedis

# Skip database migrations
.\scripts\laragon-start.ps1 -SkipMigrations

# Don't open browser after startup
.\scripts\laragon-start.ps1 -NoBrowser
```

**What it does:**
- Checks Laragon prerequisites (PHP, Composer, Node.js, MySQL)
- Switches to Laragon environment configuration
- Installs Composer and NPM dependencies
- Generates application key
- Sets up WSL Redis (optional) or configures file-based cache
- Creates database and runs migrations/seeders
- Builds frontend assets
- Displays service information and next steps
- Optionally starts development services

### `docker-start.ps1`

Quick start script for ICTServe Docker development environment.

**Usage:**
```powershell
# Standard Docker startup
.\scripts\docker-start.ps1

# Clean rebuild with fresh containers
.\scripts\docker-start.ps1 -Clean

# Skip Docker image building
.\scripts\docker-start.ps1 -SkipBuild

# Skip database migrations
.\scripts\docker-start.ps1 -SkipMigrations

# Don't open browser after startup
.\scripts\docker-start.ps1 -NoBrowser
```

**What it does:**
- Switches to Docker environment configuration
- Builds Docker images (if needed)
- Starts all Docker services
- Waits for database to be ready
- Installs Composer and NPM dependencies
- Generates application key
- Runs database migrations and seeders
- Builds frontend assets
- Fixes file permissions
- Displays service status and access information

## Development Scripts (dev/ directory)

### `start-dev.ps1`

Enhanced development script for Laragon/XAMPP environment with multiple profiles.

**Usage:**
```powershell
# Start all services (default)
.\scripts\dev\start-dev.ps1

# Use specific profile
.\scripts\dev\start-dev.ps1 -ProfileName ai

# Skip environment checks
.\scripts\dev\start-dev.ps1 -SkipChecks

# Show help
.\scripts\dev\start-dev.ps1 -Help
```

**Available Profiles:**
- `minimal` - Laravel + Vite only
- `backend` - Backend services only
- `frontend` - Frontend development
- `full` - All services (default)
- `ai` - AI development with Ollama + MCP
- `testing` - Testing environment + browser
- `production` - Production-like setup

### `dev-helpers.ps1`

All-in-one development helper with common tasks.

**Usage:**
```powershell
# Run tests
.\scripts\dev\dev-helpers.ps1 test

# Format code (PSR-12)
.\scripts\dev\dev-helpers.ps1 format

# Static analysis (PHPStan Level 9)
.\scripts\dev\dev-helpers.ps1 analyse

# Build production assets
.\scripts\dev\dev-helpers.ps1 build

# Check service status
.\scripts\dev\dev-helpers.ps1 status

# Show all commands
.\scripts\dev\dev-helpers.ps1 help
```

## Configuration Files

### Environment Files

- `.env.example` - General template (deprecated, use specific files)
- `.env.laragon` - Template for Laragon configuration
- `.env.workspace` - Template for Docker configuration
- `.env.docker` - Production Docker configuration

### MCP Configuration Files

- `.kiro/settings/mcp.json` - Default MCP configuration (auto-managed)
- `.kiro/settings/mcp.laragon.json` - Laragon MCP configuration
- `.kiro/settings/mcp.workspace.json` - Docker MCP configuration

## Environment Switching Workflow

### From Laragon to Docker

```powershell
# 1. Switch environment
.\scripts\switch-env.ps1 -env docker

# 2. Start Docker services
.\scripts\docker-start.ps1

# 3. Access application
# http://localhost:8000
```

### From Docker to Laragon

```powershell
# 1. Stop Docker services
docker compose down

# 2. Switch environment
.\scripts\switch-env.ps1 -env laragon

# 3. Start Laragon services
.\scripts\laragon-start.ps1
# or
.\scripts\dev\start-dev.ps1

# 4. Access application
# http://127.0.0.1:8000
```

## Troubleshooting

### Common Issues

**Environment switch fails:**
- Check if source files exist (`.env.example`, `.env.workspace`)
- Ensure you have write permissions
- Use `-Force` flag to overwrite existing files

**Docker startup fails:**
- Ensure Docker Desktop is running
- Check if ports 8000, 3306, 6379 are available
- Try clean rebuild: `.\scripts\docker-start.ps1 -Clean`

**Laragon startup fails:**
- Check if PHP, MySQL, Redis are installed
- Verify WSL Redis is running: `wsl redis-cli ping`
- Check port availability: `netstat -ano | findstr :8000`

### Getting Help

Each script includes built-in help:

```powershell
# Get help for any script
Get-Help .\scripts\switch-env.ps1 -Full
Get-Help .\scripts\docker-start.ps1 -Full
Get-Help .\scripts\dev\start-dev.ps1 -Full
```

## Best Practices

1. **Always use the environment switcher** instead of manually copying files
2. **Create backups** before switching environments (automatic with scripts)
3. **Check service status** after starting any environment
4. **Use appropriate URLs** for each environment (127.0.0.1 vs localhost)
5. **Stop services properly** before switching environments

## Contributing

When adding new scripts:

1. Follow PowerShell best practices
2. Include comprehensive help documentation
3. Add error handling and validation
4. Update this README with new script information
5. Test on both environments (Laragon and Docker)
