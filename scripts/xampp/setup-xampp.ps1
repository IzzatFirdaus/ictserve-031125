#Requires -Version 5.1
<#
.SYNOPSIS
    Setup ICTServe for XAMPP Development Environment

.DESCRIPTION
    Configures the ICTServe Laravel application for development using XAMPP.
    Handles database setup, virtual hosts, environment configuration, and dependency installation.

.PARAMETER XamppPath
    Path to XAMPP installation (default: C:\xampp)

.PARAMETER SiteName
    Site name for virtual host configuration (default: ictserve)

.PARAMETER Force
    Force operation without confirmation prompts

.PARAMETER Clean
    Clean existing configuration before setup

.PARAMETER SkipDeps
    Skip dependency installation (composer, npm)

.PARAMETER CreateDatabase
    Create MySQL database and user

.PARAMETER RunMigrations
    Run Laravel migrations after setup

.EXAMPLE
    .\scripts\xampp\setup-xampp.ps1 -CreateDatabase -RunMigrations
    Full setup with database creation and migrations

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: XAMPP, PowerShell 5.1+
#>

[CmdletBinding()]
param(
    [string]$XamppPath = 'C:\xampp',
    [string]$SiteName = 'ictserve',
    [switch]$Force,
    [switch]$Clean,
    [switch]$SkipDeps,
    [switch]$CreateDatabase,
    [switch]$RunMigrations
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Configuration
$script:ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$script:PublicPath = Join-Path $script:ProjectRoot 'public'
$script:DatabaseName = 'ictserve'
$script:DatabaseUser = 'ictserve_user'
$script:DatabasePassword = 'ictserve_secret'

#region Utility Functions

function Write-Status {
    param(
        [string]$Message,
        [ValidateSet('Info', 'Success', 'Warning', 'Error')]
        [string]$Type = 'Info'
    )

    $colors = @{
        Info = 'Cyan'
        Success = 'Green'
        Warning = 'Yellow'
        Error = 'Red'
    }

    $icons = @{
        Info = 'ℹ️'
        Success = '✅'
        Warning = '⚠️'
        Error = '❌'
    }

    Write-Host "$($icons[$Type]) $Message" -ForegroundColor $colors[$Type]
}

function Test-XamppInstallation {
    if (-not (Test-Path $XamppPath)) {
        throw "XAMPP not found at $XamppPath. Please install XAMPP or specify correct path with -XamppPath"
    }

    $xamppControl = Join-Path $XamppPath 'xampp-control.exe'
    if (-not (Test-Path $xamppControl)) {
        throw "XAMPP control panel not found at $xamppControl"
    }

    Write-Status "XAMPP installation verified at $XamppPath" -Type Success
}

function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

function Start-XamppService {
    param([string]$ServiceName)

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if (-not $service) {
        Write-Status "Service $ServiceName not found. Starting via XAMPP..." -Type Warning

        # Try to start via XAMPP batch files
        $batchFile = Join-Path $XamppPath "$ServiceName`_start.bat"
        if (Test-Path $batchFile) {
            Start-Process -FilePath $batchFile -Wait -WindowStyle Hidden
        }
        return
    }

    if ($service.Status -ne 'Running') {
        Write-Status "Starting $ServiceName service..." -Type Info
        Start-Service -Name $ServiceName
        Write-Status "$ServiceName service started" -Type Success
    }
    else {
        Write-Status "$ServiceName service already running" -Type Success
    }
}

function Stop-XamppService {
    param([string]$ServiceName)

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if ($service -and $service.Status -eq 'Running') {
        Write-Status "Stopping $ServiceName service..." -Type Info
        Stop-Service -Name $ServiceName -Force
        Write-Status "$ServiceName service stopped" -Type Success
    }
}

#endregion

#region Environment Configuration

function New-EnvironmentFile {
    Write-Status "Creating XAMPP environment configuration..." -Type Info

    # Create .env.xampp if it doesn't exist
    $envXampp = Join-Path $script:ProjectRoot '.env.xampp'
    if (-not (Test-Path $envXampp) -or $Force) {
        $envContent = @"
APP_NAME=ICTServe
APP_ENV=local
APP_KEY=
APP_DEBUG=true
APP_URL=http://localhost
APP_TIMEZONE=Asia/Kuala_Lumpur

APP_LOCALE=ms
APP_FALLBACK_LOCALE=ms
APP_FAKER_LOCALE=en_US

# XAMPP Database Configuration
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=$script:DatabaseName
DB_USERNAME=$script:DatabaseUser
DB_PASSWORD=$script:DatabasePassword

# Redis Configuration (if Redis is installed)
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=null
REDIS_PORT=6379

# Cache and Session (using database for XAMPP)
CACHE_STORE=database
SESSION_DRIVER=database
QUEUE_CONNECTION=database

# Mail Configuration (using log driver for development)
MAIL_MAILER=log
MAIL_HOST=127.0.0.1
MAIL_PORT=1025
MAIL_USERNAME=null
MAIL_PASSWORD=null
MAIL_ENCRYPTION=null
MAIL_FROM_ADDRESS="noreply@ictserve.local"
MAIL_FROM_NAME="`${APP_NAME}"

# Laravel Reverb WebSocket Configuration
REVERB_APP_ID=ictserve-app
REVERB_APP_KEY=local-app-key-for-development
REVERB_APP_SECRET=local-app-secret-for-development
REVERB_HOST=127.0.0.1
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="`${REVERB_APP_KEY}"
VITE_REVERB_HOST="`${REVERB_HOST}"
VITE_REVERB_PORT="`${REVERB_PORT}"
VITE_REVERB_SCHEME="`${REVERB_SCHEME}"

# Telescope Configuration
TELESCOPE_ENABLED=true
TELESCOPE_PATH=telescope

# MCP Configuration
MEMORY_FILE_PATH=storage/mcp/memory.jsonl

# Development Tools
VITE_APP_NAME="`${APP_NAME}"
"@

        Set-Content -Path $envXampp -Value $envContent -Encoding UTF8
        Write-Status "Created .env.xampp configuration file" -Type Success
    }

    # Copy to .env
    if (Test-Path '.env') {
        $timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
        $backup = ".env.backup.$timestamp"
        Copy-Item '.env' $backup -Force
        Write-Status "Backed up existing .env to $backup" -Type Info
    }

    Copy-Item $envXampp '.env' -Force
    Write-Status "Applied XAMPP environment configuration" -Type Success
}

function Set-VirtualHost {
    Write-Status "Configuring Apache virtual host..." -Type Info

    $httpdConf = Join-Path $XamppPath 'apache\conf\httpd.conf'
    $vhostsConf = Join-Path $XamppPath 'apache\conf\extra\httpd-vhosts.conf'

    if (-not (Test-Path $httpdConf)) {
        throw "Apache configuration file not found: $httpdConf"
    }

    # Enable virtual hosts in httpd.conf
    $httpdContent = Get-Content $httpdConf -Raw
    if ($httpdContent -notmatch 'Include conf/extra/httpd-vhosts.conf') {
        $httpdContent = $httpdContent -replace '#Include conf/extra/httpd-vhosts.conf', 'Include conf/extra/httpd-vhosts.conf'
        Set-Content -Path $httpdConf -Value $httpdContent -Encoding UTF8
        Write-Status "Enabled virtual hosts in Apache configuration" -Type Success
    }

    # Create virtual host configuration
    $vhostConfig = @"

# ICTServe Virtual Host Configuration
<VirtualHost *:80>
    ServerName $SiteName.local
    ServerAlias www.$SiteName.local
    DocumentRoot "$($script:PublicPath -replace '\\', '/')"

    <Directory "$($script:PublicPath -replace '\\', '/')">
        Options Indexes FollowSymLinks
        AllowOverride All
        Require all granted
        DirectoryIndex index.php index.html
    </Directory>

    # PHP Configuration
    <FilesMatch "\.php$">
        SetHandler application/x-httpd-php
    </FilesMatch>

    # Security Headers
    Header always set X-Content-Type-Options nosniff
    Header always set X-Frame-Options DENY
    Header always set X-XSS-Protection "1; mode=block"

    # Laravel specific
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteRule ^(.*)$ index.php [QSA,L]

    ErrorLog "logs/$SiteName-error.log"
    CustomLog "logs/$SiteName-access.log" combined
</VirtualHost>
"@

    # Check if virtual host already exists
    if (Test-Path $vhostsConf) {
        $existingContent = Get-Content $vhostsConf -Raw
        if ($existingContent -notmatch "ServerName $SiteName\.local") {
            Add-Content -Path $vhostsConf -Value $vhostConfig -Encoding UTF8
            Write-Status "Added virtual host configuration for $SiteName.local" -Type Success
        }
        else {
            Write-Status "Virtual host for $SiteName.local already exists" -Type Info
        }
    }
    else {
        Set-Content -Path $vhostsConf -Value $vhostConfig -Encoding UTF8
        Write-Status "Created virtual host configuration file" -Type Success
    }
}

function Set-HostsFile {
    Write-Status "Updating Windows hosts file..." -Type Info

    if (-not (Test-Administrator)) {
        Write-Status "Administrator privileges required to update hosts file. Please run as administrator or add manually:" -Type Warning
        Write-Status "  127.0.0.1 $SiteName.local" -Type Warning
        Write-Status "  127.0.0.1 www.$SiteName.local" -Type Warning
        return
    }

    $hostsFile = 'C:\Windows\System32\drivers\etc\hosts'
    $hostsContent = Get-Content $hostsFile -Raw

    $entries = @(
        "127.0.0.1 $SiteName.local",
        "127.0.0.1 www.$SiteName.local"
    )

    $modified = $false
    foreach ($entry in $entries) {
        if ($hostsContent -notmatch [regex]::Escape($entry)) {
            Add-Content -Path $hostsFile -Value $entry -Encoding UTF8
            $modified = $true
        }
    }

    if ($modified) {
        Write-Status "Updated hosts file with local domain entries" -Type Success
    }
    else {
        Write-Status "Hosts file entries already exist" -Type Info
    }
}

#endregion

#region Database Setup

function New-Database {
    if (-not $CreateDatabase) {
        return
    }

    Write-Status "Setting up MySQL database..." -Type Info

    # Start MySQL service
    Start-XamppService 'mysql'

    # Wait for MySQL to be ready
    Start-Sleep -Seconds 5

    $mysqlPath = Join-Path $XamppPath 'mysql\bin\mysql.exe'
    if (-not (Test-Path $mysqlPath)) {
        throw "MySQL executable not found at $mysqlPath"
    }

    # Create database and user
    $sqlCommands = @"
CREATE DATABASE IF NOT EXISTS $script:DatabaseName CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS '$script:DatabaseUser'@'localhost' IDENTIFIED BY '$script:DatabasePassword';
GRANT ALL PRIVILEGES ON $script:DatabaseName.* TO '$script:DatabaseUser'@'localhost';
FLUSH PRIVILEGES;
"@

    try {
        $sqlCommands | & $mysqlPath -u root -p
        Write-Status "Database and user created successfully" -Type Success
    }
    catch {
        Write-Status "Failed to create database. You may need to create it manually." -Type Warning
        Write-Status "SQL commands to run:" -Type Info
        Write-Status $sqlCommands -Type Info
    }
}

#endregion

#region Dependencies and Laravel Setup

function Install-Dependencies {
    if ($SkipDeps) {
        Write-Status "Skipping dependency installation" -Type Info
        return
    }

    Write-Status "Installing project dependencies..." -Type Info

    # Ensure required directories exist
    $directories = @(
        'storage\framework\cache\data',
        'storage\framework\sessions',
        'storage\framework\views',
        'storage\logs',
        'storage\mcp',
        'bootstrap\cache'
    )

    foreach ($dir in $directories) {
        $fullPath = Join-Path $script:ProjectRoot $dir
        if (-not (Test-Path $fullPath)) {
            New-Item -ItemType Directory -Path $fullPath -Force | Out-Null
        }
    }

    # Install Composer dependencies
    if (Get-Command composer -ErrorAction SilentlyContinue) {
        Write-Status "Installing Composer dependencies..." -Type Info
        composer install --no-interaction --prefer-dist --optimize-autoloader

        if ($LASTEXITCODE -eq 0) {
            Write-Status "Composer dependencies installed" -Type Success
        }
        else {
            throw "Composer install failed"
        }
    }
    else {
        Write-Status "Composer not found. Please install Composer and run 'composer install'" -Type Warning
    }

    # Install NPM dependencies
    if (Get-Command npm -ErrorAction SilentlyContinue) {
        Write-Status "Installing NPM dependencies..." -Type Info
        npm ci

        if ($LASTEXITCODE -eq 0) {
            Write-Status "NPM dependencies installed" -Type Success
        }
        else {
            throw "NPM install failed"
        }
    }
    else {
        Write-Status "NPM not found. Please install Node.js and run 'npm ci'" -Type Warning
    }
}

function Initialize-Laravel {
    Write-Status "Initializing Laravel application..." -Type Info

    # Generate application key if missing
    $envContent = Get-Content '.env' -Raw
    if ($envContent -match 'APP_KEY=\s*$' -or $envContent -notmatch 'APP_KEY=') {
        if (Get-Command php -ErrorAction SilentlyContinue) {
            Write-Status "Generating application key..." -Type Info
            php artisan key:generate --force
            Write-Status "Application key generated" -Type Success
        }
        else {
            Write-Status "PHP not found. Please run 'php artisan key:generate' manually" -Type Warning
        }
    }

    # Run migrations if requested
    if ($RunMigrations -and (Get-Command php -ErrorAction SilentlyContinue)) {
        Write-Status "Running database migrations..." -Type Info
        php artisan migrate --seed --force

        if ($LASTEXITCODE -eq 0) {
            Write-Status "Database migrations completed" -Type Success
        }
        else {
            Write-Status "Migration failed. Please check database connection and run manually" -Type Warning
        }
    }

    # Build frontend assets
    if (Get-Command npm -ErrorAction SilentlyContinue) {
        Write-Status "Building frontend assets..." -Type Info
        npm run build

        if ($LASTEXITCODE -eq 0) {
            Write-Status "Frontend assets built" -Type Success
        }
        else {
            Write-Status "Asset build failed. You can run 'npm run dev' for development" -Type Warning
        }
    }

    # Clear caches
    if (Get-Command php -ErrorAction SilentlyContinue) {
        Write-Status "Clearing Laravel caches..." -Type Info
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear
        Write-Status "Caches cleared" -Type Success
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n🔧 ICTServe XAMPP Setup" -ForegroundColor Cyan
    Write-Host "=" * 30 -ForegroundColor Cyan
    Write-Host ""

    # Change to project root
    Push-Location $script:ProjectRoot

    # Verify XAMPP installation
    Test-XamppInstallation

    # Clean existing setup if requested
    if ($Clean) {
        Write-Status "Cleaning existing configuration..." -Type Info
        Stop-XamppService 'Apache2.4'
        Stop-XamppService 'mysql'
    }

    # Setup steps
    New-EnvironmentFile
    Set-VirtualHost
    Set-HostsFile
    New-Database
    Install-Dependencies
    Initialize-Laravel

    # Start services
    Write-Status "Starting XAMPP services..." -Type Info
    Start-XamppService 'Apache2.4'
    Start-XamppService 'mysql'

    Write-Host "`n✅ XAMPP setup completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "🌐 Access your application:" -ForegroundColor Cyan
    Write-Host "  - Local: http://localhost" -ForegroundColor White
    Write-Host "  - Virtual Host: http://$SiteName.local" -ForegroundColor White
    Write-Host "  - Admin Panel: http://$SiteName.local/admin" -ForegroundColor White
    Write-Host ""
    Write-Host "📝 Next steps:" -ForegroundColor Cyan
    Write-Host "  1. Open XAMPP Control Panel to manage services" -ForegroundColor White
    Write-Host "  2. Visit http://$SiteName.local to test the application" -ForegroundColor White
    Write-Host "  3. Run 'php artisan serve' for Laravel development server" -ForegroundColor White
    Write-Host ""

    if (-not $RunMigrations) {
        Write-Host "⚠️  Don't forget to run migrations:" -ForegroundColor Yellow
        Write-Host "   php artisan migrate --seed" -ForegroundColor White
        Write-Host ""
    }
}
catch {
    Write-Host "`n❌ Setup failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
finally {
    Pop-Location
}

#endregion
