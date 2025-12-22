#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Automated XAMPP deployment setup for ICTServe

.DESCRIPTION
    This script sets up ICTServe for non-workspace XAMPP development environment.
    It configures the environment, installs dependencies, and prepares the application.

.PARAMETER SkipDependencies
    Skip composer and npm dependency installation

.PARAMETER SkipDatabase
    Skip database creation and migration

.PARAMETER Force
    Force overwrite existing files without confirmation

.PARAMETER RedisSetup
    Setup Redis for enhanced performance (optional)

.EXAMPLE
    .\deployment\xampp\setup-xampp.ps1
    Standard XAMPP setup

.EXAMPLE
    .\deployment\xampp\setup-xampp.ps1 -RedisSetup -Force
    Setup with Redis and force overwrite

.NOTES
    - Requires XAMPP to be installed and running
    - Requires Composer and Node.js to be installed
    - Run from ICTServe root directory
#>

param(
    [switch]$SkipDependencies,
    [switch]$SkipDatabase,
    [switch]$Force,
    [switch]$RedisSetup
)

# Color functions
function Write-Success { param($Message) Write-Host $Message -ForegroundColor Green }
function Write-Warning { param($Message) Write-Host $Message -ForegroundColor Yellow }
function Write-Error { param($Message) Write-Host $Message -ForegroundColor Red }
function Write-Info { param($Message) Write-Host $Message -ForegroundColor Cyan }
function Write-Step { param($Message) Write-Host "`n=== $Message ===" -ForegroundColor Magenta }

Write-Info "ICTServe XAMPP Deployment Setup"
Write-Info "Version: 3.6.0"
Write-Info "Target: Non-Workspace XAMPP Environment"
Write-Info "Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')"

# Check if we're in the correct directory
if (-not (Test-Path "composer.json")) {
    Write-Error "Error: Please run this script from the ICTServe root directory"
    Write-Info "Expected: .\deployment\xampp\setup-xampp.ps1"
    exit 1
}

# Check prerequisites
Write-Step "Checking Prerequisites"

# Check XAMPP
$xamppPath = @(
    "C:\xampp",
    "C:\Program Files\XAMPP",
    "D:\xampp"
) | Where-Object { Test-Path $_ } | Select-Object -First 1

if (-not $xamppPath) {
    Write-Warning "XAMPP not found in common locations"
    Write-Info "Please ensure XAMPP is installed and accessible"
} else {
    Write-Success "XAMPP found at: $xamppPath"
}

# Check Composer
try {
    $composerVersion = composer --version 2>$null
    Write-Success "Composer: $($composerVersion -split ' ' | Select-Object -First 2 -Join ' ')"
} catch {
    Write-Error "Composer not found. Please install Composer first."
    Write-Info "Download from: https://getcomposer.org/"
    exit 1
}

# Check Node.js
try {
    $nodeVersion = node --version 2>$null
    $npmVersion = npm --version 2>$null
    Write-Success "Node.js: $nodeVersion, npm: $npmVersion"
    
    # Check Node.js version (require 22.12+)
    $nodeVersionNumber = [version]($nodeVersion -replace 'v', '')
    $requiredVersion = [version]"22.12.0"
    if ($nodeVersionNumber -lt $requiredVersion) {
        Write-Warning "Node.js version $nodeVersion is below required 22.12+"
        Write-Info "Please update Node.js from: https://nodejs.org/"
    }
} catch {
    Write-Error "Node.js not found. Please install Node.js 22.12+ first."
    Write-Info "Download from: https://nodejs.org/"
    exit 1
}

# Check PHP
try {
    $phpVersion = php --version 2>$null | Select-Object -First 1
    Write-Success "PHP: $($phpVersion -split ' ' | Select-Object -First 2 -Join ' ')"
    
    # Check PHP version (require 8.4+)
    $phpVersionNumber = [version](($phpVersion -split ' ')[1] -split '-')[0]
    $requiredPhpVersion = [version]"8.4.0"
    if ($phpVersionNumber -lt $requiredPhpVersion) {
        Write-Warning "PHP version $phpVersionNumber is below required 8.4+"
        Write-Info "Please update PHP or use XAMPP with PHP 8.4+"
    }
} catch {
    Write-Error "PHP not found in PATH. Please ensure XAMPP PHP is accessible."
    exit 1
}

# Backup existing .env if it exists
Write-Step "Environment Configuration"

if (Test-Path ".env") {
    if (-not $Force) {
        Write-Warning "Existing .env file found"
        $confirm = Read-Host "Backup and replace with XAMPP configuration? (y/N)"
        if ($confirm -ne 'y' -and $confirm -ne 'Y') {
            Write-Info "Setup cancelled"
            exit 0
        }
    }
    
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupFile = ".env.backup.xampp.$timestamp"
    Copy-Item ".env" $backupFile
    Write-Success "Backed up existing .env to: $backupFile"
}

# Copy XAMPP environment configuration
Copy-Item "deployment\xampp\.env.xampp" ".env" -Force
Write-Success "Deployed XAMPP environment configuration"

# Generate application key if needed
$envContent = Get-Content ".env" -Raw
if ($envContent -match "APP_KEY=\s*$" -or $envContent -notmatch "APP_KEY=") {
    Write-Info "Generating application key..."
    php artisan key:generate --force
    Write-Success "Application key generated"
}

# Install dependencies
if (-not $SkipDependencies) {
    Write-Step "Installing Dependencies"
    
    Write-Info "Installing Composer dependencies..."
    try {
        composer install --no-interaction --prefer-dist --optimize-autoloader
        Write-Success "Composer dependencies installed"
    } catch {
        Write-Error "Failed to install Composer dependencies"
        Write-Info "Try running: composer install --no-interaction"
        exit 1
    }
    
    Write-Info "Installing NPM dependencies..."
    try {
        npm ci --prefer-offline --no-audit
        Write-Success "NPM dependencies installed"
    } catch {
        Write-Warning "npm ci failed, trying npm install..."
        try {
            npm install --prefer-offline --no-audit
            Write-Success "NPM dependencies installed"
        } catch {
            Write-Error "Failed to install NPM dependencies"
            Write-Info "Try running: npm install"
            exit 1
        }
    }
} else {
    Write-Info "Skipping dependency installation (--SkipDependencies)"
}

# Database setup
if (-not $SkipDatabase) {
    Write-Step "Database Setup"
    
    Write-Info "Checking MySQL connection..."
    try {
        $mysqlTest = mysql -u root -e "SELECT 1;" 2>$null
        Write-Success "MySQL connection successful"
        
        Write-Info "Creating database if not exists..."
        mysql -u root -e "CREATE DATABASE IF NOT EXISTS ictserve;" 2>$null
        Write-Success "Database 'ictserve' ready"
        
        Write-Info "Running migrations..."
        php artisan migrate --force
        Write-Success "Database migrations completed"
        
        Write-Info "Seeding database..."
        php artisan db:seed --force
        Write-Success "Database seeding completed"
        
    } catch {
        Write-Warning "MySQL connection failed"
        Write-Info "Please ensure XAMPP MySQL is running and try:"
        Write-Info "1. Start XAMPP Control Panel"
        Write-Info "2. Start MySQL service"
        Write-Info "3. Run: mysql -u root -e 'CREATE DATABASE ictserve;'"
        Write-Info "4. Run: php artisan migrate --seed"
    }
} else {
    Write-Info "Skipping database setup (--SkipDatabase)"
}

# Redis setup (optional)
if ($RedisSetup) {
    Write-Step "Redis Setup (Optional)"
    
    Write-Info "Checking for Redis..."
    try {
        $redisTest = redis-cli ping 2>$null
        if ($redisTest -eq "PONG") {
            Write-Success "Redis is running"
            
            # Update .env to use Redis
            $envContent = Get-Content ".env" -Raw
            $envContent = $envContent -replace "CACHE_STORE=file", "CACHE_STORE=redis"
            $envContent = $envContent -replace "SESSION_DRIVER=file", "SESSION_DRIVER=redis"
            $envContent = $envContent -replace "QUEUE_CONNECTION=database", "QUEUE_CONNECTION=redis"
            Set-Content ".env" $envContent
            
            Write-Success "Updated configuration to use Redis"
        }
    } catch {
        Write-Warning "Redis not found or not running"
        Write-Info "To install Redis:"
        Write-Info "1. WSL: wsl --install, then: sudo apt install redis-server"
        Write-Info "2. Windows: Download from GitHub releases"
        Write-Info "3. Or continue with file-based cache (current setup)"
    }
}

# Clear caches and optimize
Write-Step "Optimization"

Write-Info "Clearing caches..."
php artisan optimize:clear 2>$null
Write-Success "Caches cleared"

Write-Info "Optimizing autoloader..."
composer dump-autoload --optimize 2>$null
Write-Success "Autoloader optimized"

# Build frontend assets
Write-Info "Building frontend assets..."
try {
    npm run build
    Write-Success "Frontend assets built"
} catch {
    Write-Warning "Frontend build failed"
    Write-Info "You can build assets later with: npm run build"
}

# Create storage directories
Write-Info "Setting up storage directories..."
$storageDirs = @(
    "storage/app/public",
    "storage/framework/cache",
    "storage/framework/sessions",
    "storage/framework/views",
    "storage/logs",
    "storage/mcp"
)

foreach ($dir in $storageDirs) {
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }
}
Write-Success "Storage directories ready"

# Create symbolic link for storage
Write-Info "Creating storage link..."
try {
    php artisan storage:link --force 2>$null
    Write-Success "Storage link created"
} catch {
    Write-Warning "Storage link creation failed (may require admin privileges)"
}

# Final setup
Write-Step "Final Configuration"

Write-Info "Verifying installation..."
try {
    $aboutOutput = php artisan about --only=environment 2>$null
    Write-Success "Laravel application ready"
} catch {
    Write-Warning "Laravel verification failed"
}

# Display completion message
Write-Step "Setup Complete!"

Write-Success "ICTServe XAMPP deployment completed successfully!"
Write-Info ""
Write-Info "Next Steps:"
Write-Info "1. Start XAMPP services (Apache, MySQL)"
Write-Info "2. Start Laravel development server:"
Write-Info "   php artisan serve"
Write-Info ""
Write-Info "3. Start Vite development server (new terminal):"
Write-Info "   npm run dev"
Write-Info ""
Write-Info "4. Optional - Start WebSocket server (new terminal):"
Write-Info "   php artisan reverb:start"
Write-Info ""
Write-Info "Access URLs:"
Write-Info "- Application: http://127.0.0.1:8000"
Write-Info "- Admin Panel: http://127.0.0.1:8000/admin"
Write-Info "- Telescope: http://127.0.0.1:8000/telescope"
Write-Info "- Pulse: http://127.0.0.1:8000/pulse"
Write-Info ""
Write-Info "Default Credentials:"
Write-Info "- Superuser: superuser@motac.gov.my / password"
Write-Info "- Admin: admin@motac.gov.my / password"
Write-Info "- Staff: staff@motac.gov.my / password"
Write-Info ""

if ($RedisSetup -and (Test-Path ".env")) {
    $envContent = Get-Content ".env" -Raw
    if ($envContent -match "CACHE_STORE=redis") {
        Write-Info "Redis Configuration: Enabled"
        Write-Info "- Horizon Dashboard: http://127.0.0.1:8000/horizon"
    }
}

Write-Info "For service management, use:"
Write-Info ".\deployment\xampp\scripts\start-services.ps1"
Write-Info ".\deployment\xampp\scripts\health-check.ps1"

Write-Success "`nDeployment completed! 🎉"