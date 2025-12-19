# Environment Switching Script for ICTServe v3.6.1
# Purpose: Switch between Docker and XAMPP environments
# Usage: .\switch-environment.ps1 -Environment xampp|docker

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("xampp", "docker")]
    [string]$Environment,
    
    [Parameter(Mandatory=$false)]
    [switch]$Force,
    
    [Parameter(Mandatory=$false)]
    [switch]$Backup = $true,
    
    [Parameter(Mandatory=$false)]
    [switch]$Validate = $true
)

# Color output functions
function Write-Success { param($Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Error { param($Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param($Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }

# Configuration
$ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$EnvFile = Join-Path $ProjectRoot ".env"
$EnvXamppFile = Join-Path $ProjectRoot ".env.xampp"
$EnvDockerFile = Join-Path $ProjectRoot ".env.docker"
$BackupDir = Join-Path $ProjectRoot "storage\backups\environment"

# Ensure backup directory exists
function Initialize-BackupDirectory {
    if (-not (Test-Path $BackupDir)) {
        New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null
        Write-Info "Created backup directory: $BackupDir"
    }
}

# Backup current environment
function Backup-CurrentEnvironment {
    if (-not $Backup) {
        Write-Info "Skipping backup (disabled by parameter)"
        return
    }
    
    Write-Info "Backing up current environment..."
    
    $timestamp = Get-Date -Format "yyyyMMdd_HHmmss"
    $backupFile = Join-Path $BackupDir ".env.backup.$timestamp"
    
    if (Test-Path $EnvFile) {
        Copy-Item $EnvFile $backupFile
        Write-Success "Environment backed up to: $backupFile"
    } else {
        Write-Warning "No .env file found to backup"
    }
}

# Validate environment prerequisites
function Test-EnvironmentPrerequisites {
    param([string]$TargetEnvironment)
    
    Write-Info "Validating prerequisites for $TargetEnvironment environment..."
    
    switch ($TargetEnvironment) {
        "xampp" {
            return Test-XamppPrerequisites
        }
        "docker" {
            return Test-DockerPrerequisites
        }
    }
}

# Test XAMPP prerequisites
function Test-XamppPrerequisites {
    $xamppPath = "C:\xampp"
    $wslAvailable = $false
    
    # Check XAMPP installation
    if (-not (Test-Path $xamppPath)) {
        Write-Error "XAMPP not found at $xamppPath"
        Write-Info "Install XAMPP from: https://www.apachefriends.org/download.html"
        return $false
    }
    
    # Check XAMPP MySQL
    $mysqlPath = Join-Path $xamppPath "mysql\bin\mysqld.exe"
    if (-not (Test-Path $mysqlPath)) {
        Write-Error "XAMPP MySQL not found at $mysqlPath"
        return $false
    }
    
    # Check XAMPP Apache
    $apachePath = Join-Path $xamppPath "apache\bin\httpd.exe"
    if (-not (Test-Path $apachePath)) {
        Write-Error "XAMPP Apache not found at $apachePath"
        return $false
    }
    
    # Check WSL availability
    try {
        $wslVersion = wsl --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            $wslAvailable = $true
            Write-Success "WSL is available"
        }
    }
    catch {
        Write-Error "WSL is not available"
        Write-Info "Install WSL: https://docs.microsoft.com/en-us/windows/wsl/install"
        return $false
    }
    
    # Check Redis in WSL
    try {
        $redisCheck = wsl redis-cli --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Redis is available in WSL"
        } else {
            Write-Warning "Redis not found in WSL - will need to install"
        }
    }
    catch {
        Write-Warning "Could not check Redis in WSL"
    }
    
    Write-Success "XAMPP prerequisites validated"
    return $true
}

# Test Docker prerequisites
function Test-DockerPrerequisites {
    # Check Docker installation
    try {
        $dockerVersion = docker --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Docker is available: $dockerVersion"
        } else {
            Write-Error "Docker is not available"
            return $false
        }
    }
    catch {
        Write-Error "Docker is not installed"
        Write-Info "Install Docker Desktop: https://www.docker.com/products/docker-desktop"
        return $false
    }
    
    # Check Docker Compose
    try {
        $composeVersion = docker-compose --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Docker Compose is available: $composeVersion"
        } else {
            Write-Warning "Docker Compose not found as separate command (may be integrated)"
        }
    }
    catch {
        Write-Warning "Could not verify Docker Compose"
    }
    
    Write-Success "Docker prerequisites validated"
    return $true
}

# Switch to XAMPP environment
function Switch-ToXamppEnvironment {
    Write-Info "Switching to XAMPP environment..."
    
    # Check if .env.xampp exists
    if (-not (Test-Path $EnvXamppFile)) {
        Write-Error ".env.xampp file not found"
        Write-Info "Create .env.xampp file first or run: php artisan ict:create-xampp-config"
        return $false
    }
    
    # Copy XAMPP environment file
    Copy-Item $EnvXamppFile $EnvFile -Force
    Write-Success "Switched to XAMPP environment configuration"
    
    # Start XAMPP services
    Write-Info "Starting XAMPP services..."
    $xamppScript = Join-Path $PSScriptRoot "..\xampp\manage-xampp.ps1"
    if (Test-Path $xamppScript) {
        & $xamppScript -Action start
    } else {
        Write-Warning "XAMPP management script not found, start services manually"
    }
    
    # Start WSL Redis
    Write-Info "Starting WSL Redis..."
    $redisScript = Join-Path $PSScriptRoot "..\wsl\manage-redis.ps1"
    if (Test-Path $redisScript) {
        & $redisScript -Action start
    } else {
        Write-Warning "WSL Redis management script not found, start Redis manually"
    }
    
    # Clear Laravel caches
    Write-Info "Clearing Laravel caches..."
    try {
        Set-Location $ProjectRoot
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear
        Write-Success "Laravel caches cleared"
    }
    catch {
        Write-Warning "Could not clear Laravel caches: $($_.Exception.Message)"
    }
    
    return $true
}

# Switch to Docker environment
function Switch-ToDockerEnvironment {
    Write-Info "Switching to Docker environment..."
    
    # Check if .env.docker exists, create from .env.example if not
    if (-not (Test-Path $EnvDockerFile)) {
        $envExample = Join-Path $ProjectRoot ".env.example"
        if (Test-Path $envExample) {
            Copy-Item $envExample $EnvDockerFile
            Write-Info "Created .env.docker from .env.example"
        } else {
            Write-Error "Neither .env.docker nor .env.example found"
            return $false
        }
    }
    
    # Copy Docker environment file
    Copy-Item $EnvDockerFile $EnvFile -Force
    Write-Success "Switched to Docker environment configuration"
    
    # Stop XAMPP services if running
    Write-Info "Stopping XAMPP services..."
    $xamppScript = Join-Path $PSScriptRoot "..\xampp\manage-xampp.ps1"
    if (Test-Path $xamppScript) {
        & $xamppScript -Action stop
    }
    
    # Stop WSL Redis if running
    Write-Info "Stopping WSL Redis..."
    $redisScript = Join-Path $PSScriptRoot "..\wsl\manage-redis.ps1"
    if (Test-Path $redisScript) {
        & $redisScript -Action stop
    }
    
    # Start Docker services
    Write-Info "Starting Docker services..."
    try {
        Set-Location $ProjectRoot
        docker-compose up -d
        Write-Success "Docker services started"
    }
    catch {
        Write-Warning "Could not start Docker services: $($_.Exception.Message)"
    }
    
    # Clear Laravel caches
    Write-Info "Clearing Laravel caches..."
    try {
        php artisan config:clear
        php artisan cache:clear
        php artisan route:clear
        php artisan view:clear
        Write-Success "Laravel caches cleared"
    }
    catch {
        Write-Warning "Could not clear Laravel caches: $($_.Exception.Message)"
    }
    
    return $true
}

# Validate environment after switch
function Test-EnvironmentAfterSwitch {
    param([string]$TargetEnvironment)
    
    if (-not $Validate) {
        Write-Info "Skipping validation (disabled by parameter)"
        return $true
    }
    
    Write-Info "Validating $TargetEnvironment environment after switch..."
    
    try {
        Set-Location $ProjectRoot
        
        # Test database connection
        Write-Info "Testing database connection..."
        $dbTest = php artisan tinker --execute="DB::connection()->getPdo(); echo 'Database OK';" 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Database connection successful"
        } else {
            Write-Error "Database connection failed"
            return $false
        }
        
        # Test Redis connection
        Write-Info "Testing Redis connection..."
        $redisTest = php artisan tinker --execute="Redis::connection()->ping(); echo 'Redis OK';" 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Redis connection successful"
        } else {
            Write-Error "Redis connection failed"
            return $false
        }
        
        # Test cache
        Write-Info "Testing cache functionality..."
        $cacheTest = php artisan tinker --execute="Cache::put('test', 'value', 60); echo Cache::get('test');" 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Cache functionality working"
        } else {
            Write-Warning "Cache test failed"
        }
        
        Write-Success "Environment validation completed successfully"
        return $true
        
    }
    catch {
        Write-Error "Environment validation failed: $($_.Exception.Message)"
        return $false
    }
}

# Display environment status
function Show-EnvironmentStatus {
    Write-Info "Current Environment Status:"
    
    if (Test-Path $EnvFile) {
        $envContent = Get-Content $EnvFile
        $dbHost = ($envContent | Select-String "^DB_HOST=").ToString().Split("=")[1]
        $redisHost = ($envContent | Select-String "^REDIS_HOST=").ToString().Split("=")[1]
        $appEnv = ($envContent | Select-String "^APP_ENV=").ToString().Split("=")[1]
        
        Write-Host "  Database Host: $dbHost" -ForegroundColor Yellow
        Write-Host "  Redis Host: $redisHost" -ForegroundColor Yellow
        Write-Host "  App Environment: $appEnv" -ForegroundColor Yellow
        
        # Detect environment type
        if ($dbHost -eq "127.0.0.1" -and $redisHost -eq "127.0.0.1") {
            Write-Host "  Environment Type: XAMPP" -ForegroundColor Green
        } else {
            Write-Host "  Environment Type: Docker/Other" -ForegroundColor Cyan
        }
    } else {
        Write-Warning "No .env file found"
    }
}

# Main execution
Write-Info "ICTServe v3.6.1 Environment Switcher"
Write-Info "Target Environment: $Environment"

# Initialize
Initialize-BackupDirectory

# Show current status
Show-EnvironmentStatus

# Validate prerequisites
if (-not (Test-EnvironmentPrerequisites -TargetEnvironment $Environment)) {
    Write-Error "Prerequisites validation failed for $Environment environment"
    exit 1
}

# Confirm switch if not forced
if (-not $Force) {
    $confirmation = Read-Host "Switch to $Environment environment? (y/N)"
    if ($confirmation -ne "y" -and $confirmation -ne "Y") {
        Write-Info "Environment switch cancelled"
        exit 0
    }
}

# Backup current environment
Backup-CurrentEnvironment

# Perform the switch
$switchSuccess = $false
switch ($Environment) {
    "xampp" {
        $switchSuccess = Switch-ToXamppEnvironment
    }
    "docker" {
        $switchSuccess = Switch-ToDockerEnvironment
    }
}

if (-not $switchSuccess) {
    Write-Error "Environment switch failed"
    exit 1
}

# Validate after switch
if (-not (Test-EnvironmentAfterSwitch -TargetEnvironment $Environment)) {
    Write-Warning "Environment switch completed but validation failed"
    Write-Info "Check the configuration and services manually"
    exit 1
}

# Show final status
Write-Success "Environment switch to $Environment completed successfully"
Show-EnvironmentStatus

Write-Info "Next steps:"
switch ($Environment) {
    "xampp" {
        Write-Host "  1. Run migrations: php artisan migrate" -ForegroundColor Yellow
        Write-Host "  2. Start Laravel: php artisan serve" -ForegroundColor Yellow
        Write-Host "  3. Monitor services: .\scripts\xampp\manage-xampp.ps1 -Action status" -ForegroundColor Yellow
    }
    "docker" {
        Write-Host "  1. Check containers: docker-compose ps" -ForegroundColor Yellow
        Write-Host "  2. Run migrations: docker-compose exec app php artisan migrate" -ForegroundColor Yellow
        Write-Host "  3. Access application: http://localhost" -ForegroundColor Yellow
    }
}