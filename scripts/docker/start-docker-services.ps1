#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Start Docker services for ICTServe development

.DESCRIPTION
    This script starts Docker containers for ICTServe development environment,
    validates they are running correctly, and initializes the application.

.PARAMETER Build
    Force rebuild of Docker images before starting

.PARAMETER SkipValidation
    Skip service validation after starting

.PARAMETER DetachedMode
    Run containers in detached mode (default: true)

.EXAMPLE
    .\scripts\docker\start-docker-services.ps1
    .\scripts\docker\start-docker-services.ps1 -Build

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: Docker Desktop
#>

[CmdletBinding()]
param(
    [switch]$Build,
    [switch]$SkipValidation,
    [switch]$DetachedMode = $true
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }

$ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)

# Check Docker prerequisites
function Test-DockerPrerequisites {
    Write-Info "Checking Docker prerequisites..."
    
    # Check if Docker is installed
    try {
        $dockerVersion = docker --version 2>$null
        if (-not $dockerVersion) {
            throw "Docker is not installed or not in PATH"
        }
        Write-Success "Docker found: $dockerVersion"
    }
    catch {
        throw "Docker is not installed. Please install Docker Desktop."
    }
    
    # Check if Docker Desktop is running
    try {
        $dockerInfo = docker info 2>$null
        if (-not $dockerInfo) {
            throw "Docker Desktop is not running"
        }
        Write-Success "Docker Desktop is running"
    }
    catch {
        throw "Docker Desktop is not running. Please start Docker Desktop and try again."
    }
    
    # Check if compose.yaml exists
    if (-not (Test-Path "$ProjectRoot\compose.yaml")) {
        throw "compose.yaml not found in project root"
    }
    Write-Success "Docker Compose configuration found"
}

# Stop any existing containers
function Stop-ExistingContainers {
    Write-Info "Stopping any existing containers..."
    
    Push-Location $ProjectRoot
    try {
        $existingContainers = docker-compose ps -q 2>$null
        if ($existingContainers) {
            Write-Info "Found existing containers, stopping them..."
            docker-compose down --remove-orphans 2>$null
            Write-Success "Existing containers stopped"
        } else {
            Write-Success "No existing containers to stop"
        }
    }
    catch {
        Write-Warning "Could not stop existing containers: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Build Docker images if requested
function Build-DockerImages {
    if (-not $Build) { return }
    
    Write-Info "Building Docker images..."
    
    Push-Location $ProjectRoot
    try {
        docker-compose build --no-cache
        Write-Success "Docker images built successfully"
    }
    catch {
        throw "Failed to build Docker images: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Start Docker containers
function Start-DockerContainers {
    Write-Info "Starting Docker containers..."
    
    Push-Location $ProjectRoot
    try {
        $composeArgs = @("up")
        if ($DetachedMode) {
            $composeArgs += "-d"
        }
        
        docker-compose @composeArgs
        Write-Success "Docker containers started successfully"
    }
    catch {
        throw "Failed to start Docker containers: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Wait for services to be ready
function Wait-ForServices {
    Write-Info "Waiting for services to be ready..."
    
    Push-Location $ProjectRoot
    try {
        # Wait for database
        Write-Info "Waiting for database to be ready..."
        $maxAttempts = 30
        $attempt = 0
        
        do {
            $attempt++
            try {
                $dbTest = docker-compose exec -T db mysql -u laravel -psecret -e "SELECT 1;" 2>$null
                if ($dbTest) {
                    Write-Success "Database is ready"
                    break
                }
            }
            catch {
                # Continue waiting
            }
            
            if ($attempt -ge $maxAttempts) {
                throw "Database did not become ready within expected time"
            }
            
            Write-Info "Waiting for database... (attempt $attempt/$maxAttempts)"
            Start-Sleep -Seconds 2
        } while ($true)
        
        # Wait for Redis
        Write-Info "Waiting for Redis to be ready..."
        $attempt = 0
        
        do {
            $attempt++
            try {
                $redisTest = docker-compose exec -T redis redis-cli ping 2>$null
                if ($redisTest -like "*PONG*") {
                    Write-Success "Redis is ready"
                    break
                }
            }
            catch {
                # Continue waiting
            }
            
            if ($attempt -ge 15) {
                Write-Warning "Redis did not respond within expected time, but continuing..."
                break
            }
            
            Write-Info "Waiting for Redis... (attempt $attempt/15)"
            Start-Sleep -Seconds 2
        } while ($true)
        
    }
    finally {
        Pop-Location
    }
}

# Initialize Laravel application
function Initialize-LaravelApp {
    Write-Info "Initializing Laravel application..."
    
    Push-Location $ProjectRoot
    try {
        # Install/update Composer dependencies
        Write-Info "Installing Composer dependencies..."
        docker-compose exec -T app composer install --no-dev --optimize-autoloader
        
        # Clear caches
        Write-Info "Clearing application caches..."
        docker-compose exec -T app php artisan config:clear
        docker-compose exec -T app php artisan cache:clear
        docker-compose exec -T app php artisan route:clear
        docker-compose exec -T app php artisan view:clear
        
        # Run migrations
        Write-Info "Running database migrations..."
        docker-compose exec -T app php artisan migrate --force
        
        # Seed database if needed
        Write-Info "Seeding database..."
        docker-compose exec -T app php artisan db:seed --force
        
        # Build frontend assets
        Write-Info "Building frontend assets..."
        docker-compose exec -T app npm ci
        docker-compose exec -T app npm run build
        
        # Set proper permissions
        Write-Info "Setting proper permissions..."
        docker-compose exec -T app chown -R www-data:www-data /var/www/storage
        docker-compose exec -T app chown -R www-data:www-data /var/www/bootstrap/cache
        
        Write-Success "Laravel application initialized successfully"
    }
    catch {
        Write-Warning "Laravel initialization encountered errors: $($_.Exception.Message)"
        Write-Info "You may need to run initialization commands manually"
    }
    finally {
        Pop-Location
    }
}

# Validate services are running
function Test-DockerServices {
    if ($SkipValidation) {
        Write-Warning "Skipping service validation as requested"
        return
    }
    
    Write-Info "Validating Docker services..."
    
    Push-Location $ProjectRoot
    try {
        # Check container status
        $containers = docker-compose ps --format "table {{.Name}}\t{{.State}}"
        Write-Info "Container status:"
        Write-Host $containers
        
        # Test application response
        Write-Info "Testing application response..."
        $maxAttempts = 10
        $attempt = 0
        
        do {
            $attempt++
            try {
                $response = Invoke-WebRequest -Uri "http://localhost:8000" -TimeoutSec 10 -ErrorAction Stop
                Write-Success "Application is responding on http://localhost:8000"
                break
            }
            catch {
                if ($attempt -ge $maxAttempts) {
                    Write-Warning "Application is not responding on http://localhost:8000"
                    Write-Info "Check container logs: docker-compose logs app"
                    break
                }
                Write-Info "Waiting for application... (attempt $attempt/$maxAttempts)"
                Start-Sleep -Seconds 3
            }
        } while ($true)
        
        # Test database connection
        Write-Info "Testing database connection..."
        try {
            $dbTest = docker-compose exec -T app php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database connected successfully';" 2>$null
            if ($dbTest -like "*Database connected successfully*") {
                Write-Success "Database connection validated"
            } else {
                Write-Warning "Database connection test failed"
            }
        }
        catch {
            Write-Warning "Could not test database connection: $($_.Exception.Message)"
        }
        
        Write-Success "Service validation completed"
    }
    finally {
        Pop-Location
    }
}

# Show service information
function Show-ServiceInfo {
    Write-Host "`n" + "="*60 -ForegroundColor Green
    Write-Success "Docker Services Started Successfully"
    Write-Host "="*60 -ForegroundColor Green
    
    Write-Info "Service URLs:"
    Write-Info "  • Application: http://localhost:8000"
    Write-Info "  • phpMyAdmin: http://localhost:8080"
    Write-Info "  • Reverb WebSocket: ws://localhost:8080"
    
    Write-Info "`nContainer Information:"
    Push-Location $ProjectRoot
    try {
        docker-compose ps
    }
    finally {
        Pop-Location
    }
    
    Write-Info "`nUseful Docker commands:"
    Write-Info "  • View logs: docker-compose logs -f [service]"
    Write-Info "  • Execute commands: docker-compose exec app php artisan [command]"
    Write-Info "  • Stop services: docker-compose down"
    Write-Info "  • Restart services: docker-compose restart"
    
    Write-Info "`nDevelopment commands:"
    Write-Info "  • Laravel shell: docker-compose exec app bash"
    Write-Info "  • Run tests: docker-compose exec app php artisan test"
    Write-Info "  • Watch assets: docker-compose exec app npm run dev"
}

# Main execution
function Main {
    try {
        Write-Host "`n" + "="*60 -ForegroundColor Blue
        Write-Host "  ICTServe Docker Service Starter" -ForegroundColor Blue
        Write-Host "="*60 -ForegroundColor Blue
        
        Test-DockerPrerequisites
        Stop-ExistingContainers
        Build-DockerImages
        Start-DockerContainers
        Wait-ForServices
        Initialize-LaravelApp
        Test-DockerServices
        Show-ServiceInfo
        
        Write-Success "`nDocker services started successfully!"
        Write-Info "Access your application at: http://localhost:8000"
        
    }
    catch {
        Write-Error "Failed to start Docker services: $($_.Exception.Message)"
        Write-Info "Check Docker Desktop and try again, or run: docker-compose logs"
        exit 1
    }
}

# Execute main function
Main
