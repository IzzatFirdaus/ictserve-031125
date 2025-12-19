#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Swap between XAMPP and Docker environments for ICTServe development

.DESCRIPTION
    This script provides a comprehensive solution to switch between XAMPP (local) 
    and Docker development environments. It handles environment files, services, 
    database connections, and validates the switch.

.PARAMETER Environment
    Target environment: 'xampp' or 'docker'

.PARAMETER Force
    Force the switch without confirmation prompts

.PARAMETER SkipValidation
    Skip post-switch validation checks

.PARAMETER Backup
    Create backup of current .env before switching

.EXAMPLE
    .\scripts\swap-environment.ps1 -Environment docker
    .\scripts\swap-environment.ps1 -Environment xampp -Force -Backup

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: PowerShell 5.1+, Docker Desktop (for Docker mode), XAMPP (for XAMPP mode)
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('xampp', 'docker')]
    [string]$Environment,
    
    [switch]$Force,
    [switch]$SkipValidation,
    [switch]$Backup
)

# Set strict mode for better error handling
Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Script configuration
$ScriptName = "ICTServe Environment Swapper"
$ScriptVersion = "1.0.0"
$ProjectRoot = Split-Path -Parent $PSScriptRoot

# Color functions for output
function Write-ColorOutput {
    param(
        [string]$Message,
        [string]$Color = 'White'
    )
    
    $colors = @{
        'Red' = [ConsoleColor]::Red
        'Green' = [ConsoleColor]::Green
        'Yellow' = [ConsoleColor]::Yellow
        'Blue' = [ConsoleColor]::Blue
        'Cyan' = [ConsoleColor]::Cyan
        'Magenta' = [ConsoleColor]::Magenta
        'White' = [ConsoleColor]::White
    }
    
    if ($colors.ContainsKey($Color)) {
        Write-Host $Message -ForegroundColor $colors[$Color]
    } else {
        Write-Host $Message -ForegroundColor White
    }
}

function Write-Success { param([string]$Message) Write-ColorOutput "[SUCCESS] $Message" 'Green' }
function Write-Warning { param([string]$Message) Write-ColorOutput "[WARNING] $Message" 'Yellow' }
function Write-Error { param([string]$Message) Write-ColorOutput "[ERROR] $Message" 'Red' }
function Write-Info { param([string]$Message) Write-ColorOutput "[INFO] $Message" 'Cyan' }

# Header
function Show-Header {
    Write-ColorOutput "`n" + "="*60 'Blue'
    Write-ColorOutput "  $ScriptName v$ScriptVersion" 'Blue'
    Write-ColorOutput "  Switching to: $($Environment.ToUpper())" 'Blue'
    Write-ColorOutput "="*60 'Blue'
}

# Check prerequisites
function Test-Prerequisites {
    Write-Info "Checking prerequisites..."
    
    # Check if we're in the correct directory
    if (-not (Test-Path "$ProjectRoot\composer.json")) {
        throw "Not in ICTServe project root. Please run from project directory."
    }
    
    # Check Docker Desktop for Docker environment
    if ($Environment -eq 'docker') {
        try {
            $dockerVersion = docker --version 2>$null
            if (-not $dockerVersion) {
                throw "Docker is not installed or not in PATH"
            }
            Write-Success "Docker found: $dockerVersion"
            
            # Check if Docker Desktop is running
            $dockerInfo = docker info 2>$null
            if (-not $dockerInfo) {
                throw "Docker Desktop is not running. Please start Docker Desktop."
            }
        }
        catch {
            throw "Docker prerequisite check failed: $($_.Exception.Message)"
        }
    }
    
    # Check XAMPP for XAMPP environment
    if ($Environment -eq 'xampp') {
        $xamppPaths = @(
            "C:\xampp\mysql\bin\mysql.exe",
            "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysql.exe",
            "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysql.exe"
        )
        
        $xamppFound = $false
        foreach ($path in $xamppPaths) {
            if (Test-Path $path) {
                Write-Success "Local MySQL found at: $path"
                $xamppFound = $true
                break
            }
        }
        
        if (-not $xamppFound) {
            Write-Warning "No local MySQL installation detected. Ensure XAMPP/Laragon/WAMP is installed."
        }
    }
}

# Backup current environment
function Backup-Environment {
    if (-not $Backup) { return }
    
    Write-Info "Creating backup of current environment..."
    
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupDir = "$ProjectRoot\backups\env"
    
    if (-not (Test-Path $backupDir)) {
        New-Item -ItemType Directory -Path $backupDir -Force | Out-Null
    }
    
    if (Test-Path "$ProjectRoot\.env") {
        $backupFile = "$backupDir\.env.backup.$timestamp"
        Copy-Item "$ProjectRoot\.env" $backupFile
        Write-Success "Environment backed up to: $backupFile"
    }
}

# Stop current services
function Stop-CurrentServices {
    Write-Info "Stopping current services..."
    
    # Stop Docker services if running
    if (Test-Path "$ProjectRoot\compose.yaml") {
        try {
            Push-Location $ProjectRoot
            $dockerStatus = docker-compose ps -q 2>$null
            if ($dockerStatus) {
                Write-Info "Stopping Docker services..."
                docker-compose down --remove-orphans 2>$null
                Write-Success "Docker services stopped"
            }
        }
        catch {
            Write-Warning "Could not stop Docker services: $($_.Exception.Message)"
        }
        finally {
            Pop-Location
        }
    }
    
    # Stop Laravel development server if running
    $laravelProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*artisan serve*" }
    
    if ($laravelProcesses) {
        Write-Info "Stopping Laravel development server..."
        $laravelProcesses | Stop-Process -Force
        Write-Success "Laravel development server stopped"
    }
    
    # Stop Reverb server if running
    $reverbProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*reverb:start*" }
    
    if ($reverbProcesses) {
        Write-Info "Stopping Reverb server..."
        $reverbProcesses | Stop-Process -Force
        Write-Success "Reverb server stopped"
    }
}

# Switch environment files
function Switch-EnvironmentFiles {
    Write-Info "Switching environment configuration..."
    
    $envSource = switch ($Environment) {
        'docker' { "$ProjectRoot\.env.docker" }
        'xampp' { "$ProjectRoot\.env.example" }
    }
    
    if (-not (Test-Path $envSource)) {
        throw "Source environment file not found: $envSource"
    }
    
    # Copy environment file
    Copy-Item $envSource "$ProjectRoot\.env" -Force
    Write-Success "Environment file switched to $Environment configuration"
    
    # Update specific configurations based on environment
    Update-EnvironmentConfig
}

# Update environment-specific configurations
function Update-EnvironmentConfig {
    $envFile = "$ProjectRoot\.env"
    $envContent = Get-Content $envFile
    
    switch ($Environment) {
        'xampp' {
            # Ensure XAMPP-specific settings
            $envContent = $envContent -replace '^DB_HOST=.*', 'DB_HOST=127.0.0.1'
            $envContent = $envContent -replace '^DB_USERNAME=.*', 'DB_USERNAME=root'
            $envContent = $envContent -replace '^DB_PASSWORD=.*', 'DB_PASSWORD='
            $envContent = $envContent -replace '^REDIS_HOST=.*', 'REDIS_HOST=127.0.0.1'
            $envContent = $envContent -replace '^APP_URL=.*', 'APP_URL=http://127.0.0.1:8000'
            $envContent = $envContent -replace '^REVERB_HOST=.*', 'REVERB_HOST=127.0.0.1'
            $envContent = $envContent -replace '^VITE_REVERB_HOST=.*', 'VITE_REVERB_HOST=127.0.0.1'
        }
        'docker' {
            # Ensure Docker-specific settings
            $envContent = $envContent -replace '^DB_HOST=.*', 'DB_HOST=db'
            $envContent = $envContent -replace '^DB_USERNAME=.*', 'DB_USERNAME=laravel'
            $envContent = $envContent -replace '^DB_PASSWORD=.*', 'DB_PASSWORD=secret'
            $envContent = $envContent -replace '^REDIS_HOST=.*', 'REDIS_HOST=redis'
            $envContent = $envContent -replace '^APP_URL=.*', 'APP_URL=http://localhost:8000'
            $envContent = $envContent -replace '^REVERB_HOST=.*', 'REVERB_HOST=localhost'
            $envContent = $envContent -replace '^VITE_REVERB_HOST=.*', 'VITE_REVERB_HOST=localhost'
        }
    }
    
    $envContent | Set-Content $envFile
    Write-Success "Environment configuration updated for $Environment"
}

# Start services for the target environment
function Start-Services {
    Write-Info "Starting services for $Environment environment..."
    
    Push-Location $ProjectRoot
    
    try {
        switch ($Environment) {
            'docker' {
                # Start Docker services
                Write-Info "Starting Docker containers..."
                docker-compose up -d
                
                # Wait for database to be ready
                Write-Info "Waiting for database to be ready..."
                & "$ProjectRoot\scripts\docker\wait-for-db.sh"
                
                Write-Success "Docker services started successfully"
            }
            'xampp' {
                Write-Info "XAMPP environment configured. Please ensure XAMPP services are running:"
                Write-Info "  - Apache Web Server"
                Write-Info "  - MySQL Database"
                Write-Info "  - Redis (if using Redis cache)"
                Write-Warning "You may need to start these services manually through XAMPP Control Panel"
            }
        }
    }
    finally {
        Pop-Location
    }
}

# Run Laravel setup commands
function Initialize-Laravel {
    Write-Info "Initializing Laravel application..."
    
    Push-Location $ProjectRoot
    
    try {
        # Generate application key if not set
        if ($Environment -eq 'xampp') {
            php artisan key:generate --force
        }
        
        # Clear caches
        Write-Info "Clearing application caches..."
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear
        
        # Run migrations
        Write-Info "Running database migrations..."
        if ($Environment -eq 'docker') {
            docker-compose exec app php artisan migrate --force
        } else {
            php artisan migrate --force
        }
        
        # Install/update dependencies if needed
        if (-not (Test-Path "$ProjectRoot\vendor\autoload.php")) {
            Write-Info "Installing Composer dependencies..."
            if ($Environment -eq 'docker') {
                docker-compose exec app composer install --no-dev --optimize-autoloader
            } else {
                composer install
            }
        }
        
        # Build frontend assets if needed
        if (-not (Test-Path "$ProjectRoot\public\build")) {
            Write-Info "Building frontend assets..."
            npm run build
        }
        
        Write-Success "Laravel application initialized"
    }
    finally {
        Pop-Location
    }
}

# Validate the environment switch
function Test-Environment {
    if ($SkipValidation) {
        Write-Warning "Skipping validation as requested"
        return
    }
    
    Write-Info "Validating environment switch..."
    
    Push-Location $ProjectRoot
    
    try {
        # Test database connection
        Write-Info "Testing database connection..."
        try {
            if ($Environment -eq 'docker') {
                $dbTest = docker-compose exec -T app php -r "try { new PDO('mysql:host=db;dbname=ictserve', 'laravel', 'secret'); echo 'Database connected successfully'; } catch (Exception `$e) { echo 'Database connection failed'; }" 2>$null
            } else {
                $dbTest = php -r "try { new PDO('mysql:host=127.0.0.1;dbname=ictserve', 'root', ''); echo 'Database connected successfully'; } catch (Exception `$e) { echo 'Database connection failed'; }" 2>$null
            }
            
            if ($dbTest -like "*Database connected successfully*") {
                Write-Success "Database connection validated"
            } else {
                Write-Warning "Database connection test failed"
            }
        }
        catch {
            Write-Warning "Could not test database connection"
        }
        
        # Test Redis connection if configured
        Write-Info "Testing Redis connection..."
        try {
            $redisHost = if ($Environment -eq 'docker') { 'redis' } else { '127.0.0.1' }
            $tcpClient = New-Object System.Net.Sockets.TcpClient
            $tcpClient.Connect($redisHost, 6379)
            $tcpClient.Close()
            Write-Success "Redis connection validated"
        }
        catch {
            Write-Warning "Redis connection test failed (this may be expected if Redis is not configured)"
        }
        
        Write-Success "Environment validation completed"
    }
    catch {
        Write-Warning "Validation encountered errors: $($_.Exception.Message)"
    }
    finally {
        Pop-Location
    }
}

# Show post-switch information
function Show-PostSwitchInfo {
    Write-ColorOutput "`n" + "="*60 'Green'
    Write-Success "Environment successfully switched to: $($Environment.ToUpper())"
    Write-ColorOutput "="*60 'Green'
    
    switch ($Environment) {
        'docker' {
            Write-Info "Docker Environment Information:"
            Write-Info "  • Application URL: http://localhost:8000"
            Write-Info "  • Database: MySQL (container: db)"
            Write-Info "  • Redis: Available (container: redis)"
            Write-Info "  • Reverb WebSocket: ws://localhost:8080"
            Write-Info ""
            Write-Info "Useful Docker commands:"
            Write-Info "  • View logs: docker-compose logs -f"
            Write-Info "  • Execute commands: docker-compose exec app php artisan [command]"
            Write-Info "  • Stop services: docker-compose down"
        }
        'xampp' {
            Write-Info "XAMPP Environment Information:"
            Write-Info "  • Application URL: http://127.0.0.1:8000"
            Write-Info "  - Database: MySQL (127.0.0.1:3306)"
            Write-Info "  • Redis: 127.0.0.1:6379 (if installed)"
            Write-Info "  • Reverb WebSocket: ws://127.0.0.1:8080"
            Write-Info ""
            Write-Info "To start development:"
            Write-Info "  • Ensure XAMPP services are running"
            Write-Info "  • Run: php artisan serve --host=127.0.0.1"
            Write-Info "  • Run: php artisan reverb:start (in separate terminal)"
        }
    }
    
    Write-Info ""
    Write-Info "Next steps:"
    Write-Info "  • Verify services are running correctly"
    Write-Info "  • Run tests: php artisan test"
    Write-Info "  • Check application: visit the application URL"
}

# Main execution
function Main {
    try {
        Show-Header
        
        # Confirmation prompt
        if (-not $Force) {
            $confirmation = Read-Host "Are you sure you want to switch to $Environment environment? (y/N)"
            if ($confirmation -notmatch '^[Yy]') {
                Write-Warning "Operation cancelled by user"
                return
            }
        }
        
        Test-Prerequisites
        Backup-Environment
        Stop-CurrentServices
        Switch-EnvironmentFiles
        Start-Services
        Initialize-Laravel
        Test-Environment
        Show-PostSwitchInfo
        
        Write-Success "`nEnvironment switch completed successfully!"
        
    }
    catch {
        Write-Error "Environment switch failed: $($_.Exception.Message)"
        Write-Info "You may need to manually revert changes or check the error above."
        exit 1
    }
}

# Execute main function
Main
