#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Quick start script for ICTServe Laragon development environment

.DESCRIPTION
    This script sets up and starts the complete ICTServe Laragon development environment.
    It handles environment configuration, dependency installation, and service startup.

.PARAMETER SkipRedis
    Skip Redis setup and use file-based cache/sessions

.PARAMETER SkipMigrations
    Skip database migrations and seeding

.PARAMETER NoBrowser
    Don't open browser after startup

.PARAMETER InstallRedis
    Automatically install and configure WSL Redis

.EXAMPLE
    .\scripts\laragon-start.ps1
    Standard Laragon startup

.EXAMPLE
    .\scripts\laragon-start.ps1 -InstallRedis
    Setup with automatic Redis installation
#>

param(
    [switch]$SkipRedis,
    [switch]$SkipMigrations,
    [switch]$NoBrowser,
    [switch]$InstallRedis
)

# Set error action preference
$ErrorActionPreference = "Stop"

# Get script directory and project root
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = Split-Path -Parent $ScriptDir

# Change to project root
Set-Location $ProjectRoot

Write-Host "🚀 ICTServe Laragon Development Environment" -ForegroundColor Cyan
Write-Host "==========================================" -ForegroundColor Cyan

# Check Laragon prerequisites
Write-Host ""
Write-Host "🔍 Checking prerequisites..." -ForegroundColor Blue

# Check PHP
try {
    $PhpVersion = php --version 2>$null | Select-String "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
    if ($PhpVersion) {
        Write-Host "   ✅ PHP $PhpVersion found" -ForegroundColor Green
    } else {
        throw "PHP not found"
    }
} catch {
    Write-Error "❌ PHP not found. Please install PHP 8.2.12+ or start Laragon."
    exit 1
}

# Check Composer
try {
    composer --version | Out-Null
    Write-Host "   ✅ Composer found" -ForegroundColor Green
} catch {
    Write-Error "❌ Composer not found. Please install Composer."
    exit 1
}

# Check Node.js
try {
    $NodeVersion = node --version 2>$null
    if ($NodeVersion) {
        Write-Host "   ✅ Node.js $NodeVersion found" -ForegroundColor Green
    } else {
        throw "Node.js not found"
    }
} catch {
    Write-Error "❌ Node.js not found. Please install Node.js 22.12+."
    exit 1
}

# Check MySQL (Laragon)
try {
    mysql --version | Out-Null
    Write-Host "   ✅ MySQL found" -ForegroundColor Green
} catch {
    Write-Warning "⚠️  MySQL not found in PATH. Make sure Laragon MySQL is running."
}

# Switch to Laragon environment configuration
Write-Host ""
Write-Host "🔄 Configuring Laragon environment..." -ForegroundColor Blue
try {
    & "$ScriptDir\switch-env.ps1" -env laragon -Force
} catch {
    Write-Error "❌ Failed to switch to Laragon configuration: $_"
    exit 1
}

# Install dependencies
Write-Host ""
Write-Host "📦 Installing dependencies..." -ForegroundColor Blue

try {
    # Install Composer dependencies
    composer install --optimize-autoloader
    Write-Host "   ✅ Composer dependencies installed" -ForegroundColor Green

    # Install NPM dependencies
    npm ci --no-audit --no-fund
    Write-Host "   ✅ NPM dependencies installed" -ForegroundColor Green

} catch {
    Write-Warning "⚠️  Some dependencies may have failed to install. Check output above."
}

# Generate application key
Write-Host ""
Write-Host "🔑 Setting up Laravel..." -ForegroundColor Blue

try {
    # Generate app key if not exists
    $EnvContent = Get-Content ".env" -Raw
    if ($EnvContent -notmatch "APP_KEY=base64:") {
        php artisan key:generate --force
        Write-Host "   ✅ Application key generated" -ForegroundColor Green
    } else {
        Write-Host "   ✅ Application key already exists" -ForegroundColor Green
    }

    # Clear caches
    php artisan config:clear
    php artisan cache:clear
    Write-Host "   ✅ Caches cleared" -ForegroundColor Green

} catch {
    Write-Warning "⚠️  Laravel setup may have issues. Check output above."
}

# Setup Redis (WSL)
if (-not $SkipRedis) {
    Write-Host ""
    Write-Host "🔴 Setting up Redis..." -ForegroundColor Blue

    # Check if WSL is available
    try {
        wsl --version | Out-Null
        $WSLAvailable = $true
    } catch {
        $WSLAvailable = $false
        Write-Warning "⚠️  WSL not available. Redis setup skipped."
    }

    if ($WSLAvailable) {
        # Check if Redis is installed in WSL
        $RedisInstalled = wsl bash -c "which redis-server" 2>$null

        if (-not $RedisInstalled -and $InstallRedis) {
            Write-Host "   📥 Installing Redis in WSL..." -ForegroundColor Blue
            try {
                wsl bash -c "sudo apt update && sudo apt install -y redis-server"
                wsl bash -c "sudo systemctl enable redis-server"
                Write-Host "   ✅ Redis installed in WSL" -ForegroundColor Green
            } catch {
                Write-Warning "⚠️  Redis installation failed. You may need to install manually."
            }
        }

        # Start Redis service
        try {
            wsl bash -c "sudo systemctl start redis-server" 2>$null
            $RedisStatus = wsl bash -c "redis-cli ping" 2>$null

            if ($RedisStatus -eq "PONG") {
                Write-Host "   ✅ Redis is running" -ForegroundColor Green
            } else {
                Write-Warning "⚠️  Redis may not be running properly"
            }
        } catch {
            Write-Warning "⚠️  Could not start Redis. Check WSL Redis installation."
        }
    }

    if (-not $WSLAvailable -or -not $RedisInstalled) {
        Write-Host "   ℹ️  Configuring file-based cache/sessions..." -ForegroundColor Blue

        # Update .env to use file-based cache
        $EnvContent = Get-Content ".env" -Raw
        $EnvContent = $EnvContent -replace "CACHE_STORE=redis", "CACHE_STORE=file"
        $EnvContent = $EnvContent -replace "SESSION_DRIVER=redis", "SESSION_DRIVER=file"
        $EnvContent = $EnvContent -replace "QUEUE_CONNECTION=redis", "QUEUE_CONNECTION=database"
        Set-Content ".env" $EnvContent

        Write-Host "   ✅ Configured for file-based cache" -ForegroundColor Green
    }
}

# Setup database
Write-Host ""
Write-Host "🗄️  Setting up database..." -ForegroundColor Blue

# Check if database exists
try {
    $DbExists = mysql -u root -e "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'ictserve';" 2>$null

    if (-not $DbExists -or $DbExists -notmatch "ictserve") {
        Write-Host "   📥 Creating database..." -ForegroundColor Blue
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS ictserve;" 2>$null
        Write-Host "   ✅ Database created" -ForegroundColor Green
    } else {
        Write-Host "   ✅ Database already exists" -ForegroundColor Green
    }
} catch {
    Write-Warning "⚠️  Could not create database. Make sure Laragon MySQL is running."
    Write-Host "   💡 Start Laragon and ensure MySQL service is running" -ForegroundColor Yellow
}

# Run migrations and seeders
if (-not $SkipMigrations) {
    Write-Host ""
    Write-Host "🗄️  Running migrations..." -ForegroundColor Blue

    try {
        php artisan migrate --force
        Write-Host "   ✅ Migrations completed" -ForegroundColor Green

        php artisan db:seed --force
        Write-Host "   ✅ Database seeded" -ForegroundColor Green

    } catch {
        Write-Warning "⚠️  Database setup may have issues. Check database connection."
    }
}

# Build frontend assets
Write-Host ""
Write-Host "🎨 Building frontend assets..." -ForegroundColor Blue

try {
    npm run build
    Write-Host "   ✅ Frontend assets built" -ForegroundColor Green
} catch {
    Write-Warning "⚠️  Frontend build may have issues. Check Node.js setup."
}

# Display service information
Write-Host ""
Write-Host "📊 Laragon Service Information:" -ForegroundColor Cyan
Write-Host "   • MySQL: Laragon MySQL service" -ForegroundColor White
Write-Host "   • Redis: WSL Redis (if available)" -ForegroundColor White
Write-Host "   • PHP: Local PHP installation" -ForegroundColor White
Write-Host "   • Web Server: php artisan serve" -ForegroundColor White

# Display access information
Write-Host ""
Write-Host "🌐 Access Information:" -ForegroundColor Cyan
Write-Host "   • Application: http://127.0.0.1:8000" -ForegroundColor White
Write-Host "   • Admin Panel: http://127.0.0.1:8000/admin" -ForegroundColor White
Write-Host "   • Horizon: http://127.0.0.1:8000/horizon (if WSL Redis available)" -ForegroundColor White
Write-Host "   • Telescope: http://127.0.0.1:8000/telescope" -ForegroundColor White
Write-Host "   • Pulse: http://127.0.0.1:8000/pulse" -ForegroundColor White

Write-Host ""
Write-Host "👤 Default Credentials:" -ForegroundColor Cyan
Write-Host "   • Superuser: superuser@motac.gov.my / password" -ForegroundColor White
Write-Host "   • Admin: admin@motac.gov.my / password" -ForegroundColor White
Write-Host "   • Staff: staff@motac.gov.my / password" -ForegroundColor White

Write-Host ""
Write-Host "🚀 Next Steps:" -ForegroundColor Cyan
Write-Host "   1. Start development services:" -ForegroundColor White
Write-Host "      .\scripts\dev\start-dev.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "   2. Or start individual services:" -ForegroundColor White
Write-Host "      php artisan serve" -ForegroundColor Gray
Write-Host "      php artisan reverb:start" -ForegroundColor Gray
Write-Host "      npm run dev" -ForegroundColor Gray

Write-Host ""
Write-Host "🔧 Useful Commands:" -ForegroundColor Cyan
Write-Host "   • Run tests: php artisan test" -ForegroundColor Gray
Write-Host "   • Format code: vendor/bin/pint" -ForegroundColor Gray
Write-Host "   • Static analysis: vendor/bin/phpstan analyse" -ForegroundColor Gray
Write-Host "   • Helper script: .\scripts\dev\dev-helpers.ps1" -ForegroundColor Gray

# Start development services
Write-Host ""
$StartServices = Read-Host "Start development services now? (Y/n)"
if ($StartServices -notmatch "^[Nn]") {
    Write-Host ""
    Write-Host "🚀 Starting development services..." -ForegroundColor Blue

    try {
        & "$ScriptDir\dev\start-dev.ps1" -NoBrowser:$NoBrowser
    } catch {
        Write-Warning "⚠️  Could not start development services automatically."
        Write-Host "   💡 Run manually: .\scripts\dev\start-dev.ps1" -ForegroundColor Yellow
    }
} else {
    # Open browser if requested
    if (-not $NoBrowser) {
        Write-Host ""
        Write-Host "🌐 Opening browser..." -ForegroundColor Blue
        Start-Process "http://127.0.0.1:8000"
    }
}

Write-Host ""
Write-Host "🎉 ICTServe Laragon environment is ready!" -ForegroundColor Green
Write-Host "   Remember to start Laragon services (MySQL, etc.)" -ForegroundColor White
