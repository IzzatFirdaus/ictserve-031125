#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Quick start script for ICTServe Docker development environment

.DESCRIPTION
    This script sets up and starts the complete ICTServe Docker development environment.
    It handles environment configuration, dependency installation, and service startup.

.PARAMETER Clean
    Clean rebuild - removes existing containers and volumes

.PARAMETER SkipBuild
    Skip Docker image building (use existing images)

.PARAMETER SkipMigrations
    Skip database migrations and seeding

.PARAMETER NoBrowser
    Don't open browser after startup

.EXAMPLE
    .\scripts\docker-start.ps1
    Standard Docker startup

.EXAMPLE
    .\scripts\docker-start.ps1 -Clean
    Clean rebuild with fresh containers
#>

param(
    [switch]$Clean,
    [switch]$SkipBuild,
    [switch]$SkipMigrations,
    [switch]$NoBrowser
)

# Set error action preference
$ErrorActionPreference = "Stop"

# Get script directory and project root
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = Split-Path -Parent $ScriptDir

# Change to project root
Set-Location $ProjectRoot

Write-Host "🐳 ICTServe Docker Development Environment" -ForegroundColor Cyan
Write-Host "=========================================" -ForegroundColor Cyan

# Check Docker availability
try {
    docker --version | Out-Null
    docker compose version | Out-Null
} catch {
    Write-Error "❌ Docker or Docker Compose not found. Please install Docker Desktop."
    exit 1
}

# Switch to Docker environment configuration
Write-Host ""
Write-Host "🔄 Configuring Docker environment..." -ForegroundColor Blue
try {
    & "$ScriptDir\switch-env.ps1" -env docker -Force
} catch {
    Write-Error "❌ Failed to switch to Docker configuration: $_"
    exit 1
}

# Clean rebuild if requested
if ($Clean) {
    Write-Host ""
    Write-Host "🧹 Cleaning existing containers and volumes..." -ForegroundColor Yellow

    # Stop and remove containers
    docker compose down -v --remove-orphans 2>$null

    # Remove images
    $Images = docker images "ictserve-*" -q 2>$null
    if ($Images) {
        docker rmi $Images -f 2>$null
        Write-Host "   ✅ Removed existing images" -ForegroundColor Green
    }

    # Clean node_modules to avoid binary conflicts
    if (Test-Path "node_modules") {
        Remove-Item -Recurse -Force "node_modules"
        Write-Host "   ✅ Removed node_modules" -ForegroundColor Green
    }

    Write-Host "   ✅ Clean completed" -ForegroundColor Green
}

# Build Docker images
if (-not $SkipBuild) {
    Write-Host ""
    Write-Host "🔨 Building Docker images..." -ForegroundColor Blue

    try {
        docker compose build --no-cache
        Write-Host "   ✅ Images built successfully" -ForegroundColor Green
    } catch {
        Write-Error "❌ Failed to build Docker images: $_"
        exit 1
    }
}

# Start services
Write-Host ""
Write-Host "🚀 Starting Docker services..." -ForegroundColor Blue

try {
    docker compose up -d
    Write-Host "   ✅ Services started" -ForegroundColor Green
} catch {
    Write-Error "❌ Failed to start Docker services: $_"
    exit 1
}

# Wait for database to be ready
Write-Host ""
Write-Host "⏳ Waiting for database to be ready..." -ForegroundColor Blue

$MaxAttempts = 30
$Attempt = 0

do {
    $Attempt++
    $DbReady = docker compose exec -T db mysqladmin ping -h localhost -psecret 2>$null

    if ($LASTEXITCODE -eq 0) {
        Write-Host "   ✅ Database is ready" -ForegroundColor Green
        break
    }

    if ($Attempt -ge $MaxAttempts) {
        Write-Error "❌ Database failed to start within timeout"
        exit 1
    }

    Write-Host "   ⏳ Attempt $Attempt/$MaxAttempts..." -ForegroundColor Gray
    Start-Sleep -Seconds 2
} while ($true)

# Install dependencies
Write-Host ""
Write-Host "📦 Installing dependencies..." -ForegroundColor Blue

try {
    # Install Composer dependencies
    docker compose exec -T app composer install --no-interaction --optimize-autoloader
    Write-Host "   ✅ Composer dependencies installed" -ForegroundColor Green

    # Install NPM dependencies
    docker compose exec -T app npm ci --no-audit --no-fund
    Write-Host "   ✅ NPM dependencies installed" -ForegroundColor Green

} catch {
    Write-Warning "⚠️  Some dependencies may have failed to install. Check logs if needed."
}

# Generate application key
Write-Host ""
Write-Host "🔑 Setting up Laravel..." -ForegroundColor Blue

try {
    # Generate app key
    docker compose exec -T app php artisan key:generate --force
    Write-Host "   ✅ Application key generated" -ForegroundColor Green

    # Clear caches
    docker compose exec -T app php artisan config:clear
    docker compose exec -T app php artisan cache:clear
    Write-Host "   ✅ Caches cleared" -ForegroundColor Green

} catch {
    Write-Warning "⚠️  Laravel setup may have issues. Check container logs."
}

# Run migrations and seeders
if (-not $SkipMigrations) {
    Write-Host ""
    Write-Host "🗄️  Setting up database..." -ForegroundColor Blue

    try {
        docker compose exec -T app php artisan migrate --force
        Write-Host "   ✅ Migrations completed" -ForegroundColor Green

        docker compose exec -T app php artisan db:seed --force
        Write-Host "   ✅ Database seeded" -ForegroundColor Green

    } catch {
        Write-Warning "⚠️  Database setup may have issues. Check container logs."
    }
}

# Build frontend assets
Write-Host ""
Write-Host "🎨 Building frontend assets..." -ForegroundColor Blue

try {
    docker compose exec -T app npm run build
    Write-Host "   ✅ Frontend assets built" -ForegroundColor Green
} catch {
    Write-Warning "⚠️  Frontend build may have issues. Check container logs."
}

# Fix permissions
Write-Host ""
Write-Host "🔐 Fixing permissions..." -ForegroundColor Blue

try {
    docker compose exec -T app chown -R www-data:www-data storage bootstrap/cache
    Write-Host "   ✅ Permissions fixed" -ForegroundColor Green
} catch {
    Write-Warning "⚠️  Permission fix may have failed. Check container logs."
}

# Display service status
Write-Host ""
Write-Host "📊 Service Status:" -ForegroundColor Cyan

$Services = @("app", "nginx", "db", "redis", "reverb")
foreach ($Service in $Services) {
    $Status = docker compose ps $Service --format "{{.State}}" 2>$null
    $Color = if ($Status -eq "running") { "Green" } else { "Red" }
    $Icon = if ($Status -eq "running") { "✅" } else { "❌" }

    Write-Host "   $Icon $Service`: $Status" -ForegroundColor $Color
}

# Display access information
Write-Host ""
Write-Host "🌐 Access Information:" -ForegroundColor Cyan
Write-Host "   • Application: http://localhost:8000" -ForegroundColor White
Write-Host "   • Admin Panel: http://localhost:8000/admin" -ForegroundColor White
Write-Host "   • Horizon: http://localhost:8000/horizon" -ForegroundColor White
Write-Host "   • Telescope: http://localhost:8000/telescope" -ForegroundColor White
Write-Host "   • Pulse: http://localhost:8000/pulse" -ForegroundColor White

Write-Host ""
Write-Host "👤 Default Credentials:" -ForegroundColor Cyan
Write-Host "   • Superuser: superuser@motac.gov.my / password" -ForegroundColor White
Write-Host "   • Admin: admin@motac.gov.my / password" -ForegroundColor White
Write-Host "   • Staff: staff@motac.gov.my / password" -ForegroundColor White

Write-Host ""
Write-Host "🔧 Useful Commands:" -ForegroundColor Cyan
Write-Host "   • View logs: docker compose logs -f app" -ForegroundColor Gray
Write-Host "   • Run artisan: docker compose exec app php artisan <command>" -ForegroundColor Gray
Write-Host "   • Access shell: docker compose exec app sh" -ForegroundColor Gray
Write-Host "   • Stop services: docker compose down" -ForegroundColor Gray

# Open browser
if (-not $NoBrowser) {
    Write-Host ""
    Write-Host "🌐 Opening browser..." -ForegroundColor Blue
    Start-Process "http://localhost:8000"
}

Write-Host ""
Write-Host "🎉 ICTServe Docker environment is ready!" -ForegroundColor Green
Write-Host "   Access your application at: http://localhost:8000" -ForegroundColor White
