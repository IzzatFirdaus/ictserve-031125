#Requires -Version 5.1

<#
.SYNOPSIS
    ICTServe Environment Switching Script for XAMPP Configuration

.DESCRIPTION
    This script switches between different environment configurations for ICTServe v3.6.1.
    Supports switching to XAMPP environment with MySQL and WSL Redis configuration.
    
    Requirements 3.5, 4.3, 7.4 - Environment switching and configuration management

.PARAMETER Environment
    The target environment to switch to. Valid values: xampp, docker, local

.PARAMETER Backup
    Create a backup of the current .env file before switching

.PARAMETER Validate
    Validate the environment configuration after switching

.PARAMETER Force
    Force the switch without confirmation prompts

.EXAMPLE
    .\switch-environment.ps1 -Environment xampp
    Switches to XAMPP environment with confirmation

.EXAMPLE
    .\switch-environment.ps1 -Environment xampp -Backup -Validate -Force
    Switches to XAMPP environment with backup, validation, and no prompts

.NOTES
    Author: ICTServe Development Team
    Version: 3.6.1
    Requirements: PowerShell 5.1+, ICTServe Laravel Application
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('xampp', 'docker', 'local')]
    [string]$Environment,
    
    [switch]$Backup,
    
    [switch]$Validate,
    
    [switch]$Force
)

# Script configuration
$ErrorActionPreference = 'Stop'
$ProgressPreference = 'SilentlyContinue'

# Paths
$RootPath = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
$EnvPath = Join-Path $RootPath '.env'
$BackupPath = Join-Path $RootPath 'storage/backups/env'

# Environment file mappings
$EnvironmentFiles = @{
    'xampp'  = '.env.xampp'
    'docker' = '.env.docker'
    'local'  = '.env.local'
}

# Color functions
function Write-Success {
    param([string]$Message)
    Write-Host "✅ $Message" -ForegroundColor Green
}

function Write-Info {
    param([string]$Message)
    Write-Host "ℹ️  $Message" -ForegroundColor Cyan
}

function Write-Warning {
    param([string]$Message)
    Write-Host "⚠️  $Message" -ForegroundColor Yellow
}

function Write-Error {
    param([string]$Message)
    Write-Host "❌ $Message" -ForegroundColor Red
}

function Write-Header {
    param([string]$Title)
    Write-Host ""
    Write-Host "=" * 60 -ForegroundColor Blue
    Write-Host " $Title" -ForegroundColor Blue
    Write-Host "=" * 60 -ForegroundColor Blue
    Write-Host ""
}

# Backup current environment
function Backup-Environment {
    Write-Info "Creating backup of current environment..."
    
    if (-not (Test-Path $BackupPath)) {
        New-Item -ItemType Directory -Path $BackupPath -Force | Out-Null
    }
    
    if (Test-Path $EnvPath) {
        $timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"
        $backupFile = Join-Path $BackupPath ".env.backup.$timestamp"
        Copy-Item $EnvPath $backupFile
        Write-Success "Environment backed up to: $backupFile"
        return $backupFile
    } else {
        Write-Warning "No existing .env file found to backup"
        return $null
    }
}

# Validate environment configuration
function Test-EnvironmentConfiguration {
    param([string]$EnvironmentType)
    
    Write-Info "Validating $EnvironmentType environment configuration..."
    
    $validationResults = @{
        'EnvFileExists' = $false
        'DatabaseConfig' = $false
        'RedisConfig' = $false
        'ServicesConfig' = $false
        'RequiredVars' = $false
    }
    
    # Check if .env file exists
    if (Test-Path $EnvPath) {
        $validationResults.EnvFileExists = $true
        $envContent = Get-Content $EnvPath -Raw
        
        # Validate based on environment type
        switch ($EnvironmentType) {
            'xampp' {
                # Check XAMPP MySQL configuration
                if ($envContent -match 'DB_HOST=127\.0\.0\.1' -and 
                    $envContent -match 'DB_PORT=3306' -and 
                    $envContent -match 'DB_USERNAME=root' -and 
                    $envContent -match 'DB_PASSWORD=$') {
                    $validationResults.DatabaseConfig = $true
                }
                
                # Check WSL Redis configuration
                if ($envContent -match 'REDIS_HOST=127\.0\.0\.1' -and 
                    $envContent -match 'REDIS_PORT=6379' -and 
                    $envContent -match 'CACHE_STORE=redis') {
                    $validationResults.RedisConfig = $true
                }
                
                # Check Laravel services configuration
                if ($envContent -match 'PULSE_ENABLED=true' -and 
                    $envContent -match 'TELESCOPE_ENABLED=true' -and 
                    $envContent -match 'REVERB_HOST=127\.0\.0\.1') {
                    $validationResults.ServicesConfig = $true
                }
            }
            'docker' {
                # Docker-specific validation
                if ($envContent -match 'DB_HOST=db' -or $envContent -match 'DB_HOST=mysql') {
                    $validationResults.DatabaseConfig = $true
                }
                
                if ($envContent -match 'REDIS_HOST=redis') {
                    $validationResults.RedisConfig = $true
                }
                
                $validationResults.ServicesConfig = $true
            }
            'local' {
                # Local development validation
                $validationResults.DatabaseConfig = $true
                $validationResults.RedisConfig = $true
                $validationResults.ServicesConfig = $true
            }
        }
        
        # Check required variables
        $requiredVars = @('APP_NAME', 'APP_ENV', 'APP_URL', 'DB_CONNECTION')
        $missingVars = @()
        
        foreach ($var in $requiredVars) {
            if ($envContent -notmatch "$var=") {
                $missingVars += $var
            }
        }
        
        if ($missingVars.Count -eq 0) {
            $validationResults.RequiredVars = $true
        } else {
            Write-Warning "Missing required variables: $($missingVars -join ', ')"
        }
    }
    
    # Display validation results
    Write-Host ""
    Write-Host "Validation Results:" -ForegroundColor Yellow
    Write-Host "==================" -ForegroundColor Yellow
    
    foreach ($check in $validationResults.GetEnumerator()) {
        $status = if ($check.Value) { "✅ PASS" } else { "❌ FAIL" }
        $color = if ($check.Value) { "Green" } else { "Red" }
        Write-Host "  $($check.Key): " -NoNewline
        Write-Host $status -ForegroundColor $color
    }
    
    $allPassed = ($validationResults.Values | Where-Object { $_ -eq $false }).Count -eq 0
    
    if ($allPassed) {
        Write-Success "All validation checks passed!"
    } else {
        Write-Error "Some validation checks failed. Please review the configuration."
    }
    
    return $allPassed
}

# Test service connectivity
function Test-ServiceConnectivity {
    param([string]$EnvironmentType)
    
    Write-Info "Testing service connectivity for $EnvironmentType environment..."
    
    $results = @{
        'MySQL' = $false
        'Redis' = $false
        'WebServer' = $false
    }
    
    switch ($EnvironmentType) {
        'xampp' {
            # Test XAMPP MySQL
            try {
                $tcpClient = New-Object System.Net.Sockets.TcpClient
                $tcpClient.Connect('127.0.0.1', 3306)
                $tcpClient.Close()
                $results.MySQL = $true
                Write-Success "XAMPP MySQL is accessible on 127.0.0.1:3306"
            } catch {
                Write-Warning "XAMPP MySQL is not accessible on 127.0.0.1:3306"
            }
            
            # Test WSL Redis
            try {
                $tcpClient = New-Object System.Net.Sockets.TcpClient
                $tcpClient.Connect('127.0.0.1', 6379)
                $tcpClient.Close()
                $results.Redis = $true
                Write-Success "WSL Redis is accessible on 127.0.0.1:6379"
            } catch {
                Write-Warning "WSL Redis is not accessible on 127.0.0.1:6379"
            }
            
            # Test XAMPP Apache
            try {
                $response = Invoke-WebRequest -Uri 'http://127.0.0.1' -TimeoutSec 5 -ErrorAction SilentlyContinue
                if ($response.StatusCode -eq 200) {
                    $results.WebServer = $true
                    Write-Success "XAMPP Apache is responding on http://127.0.0.1"
                }
            } catch {
                Write-Warning "XAMPP Apache is not responding on http://127.0.0.1"
            }
        }
        'docker' {
            Write-Info "Docker connectivity testing requires containers to be running"
            Write-Info "Run 'docker-compose up -d' to start services"
        }
        'local' {
            Write-Info "Local environment connectivity depends on your local setup"
        }
    }
    
    return $results
}

# Main switching function
function Switch-Environment {
    param([string]$TargetEnvironment)
    
    Write-Header "ICTServe Environment Switcher v3.6.1"
    
    # Check if source environment file exists
    $sourceFile = $EnvironmentFiles[$TargetEnvironment]
    $sourcePath = Join-Path $RootPath $sourceFile
    
    if (-not (Test-Path $sourcePath)) {
        Write-Error "Source environment file not found: $sourceFile"
        Write-Info "Available environment files:"
        foreach ($env in $EnvironmentFiles.GetEnumerator()) {
            $path = Join-Path $RootPath $env.Value
            $exists = if (Test-Path $path) { "✅" } else { "❌" }
            Write-Host "  $($env.Key): $($env.Value) $exists"
        }
        exit 1
    }
    
    # Show current environment info
    if (Test-Path $EnvPath) {
        $currentContent = Get-Content $EnvPath -Raw
        if ($currentContent -match 'APP_ENV=(\w+)') {
            $currentEnv = $matches[1]
            Write-Info "Current environment: $currentEnv"
        }
    } else {
        Write-Info "No current .env file found"
    }
    
    Write-Info "Switching to: $TargetEnvironment"
    Write-Info "Source file: $sourceFile"
    
    # Confirmation prompt
    if (-not $Force) {
        $confirmation = Read-Host "Do you want to continue? (y/N)"
        if ($confirmation -notmatch '^[Yy]') {
            Write-Info "Operation cancelled by user"
            exit 0
        }
    }
    
    # Create backup if requested
    $backupFile = $null
    if ($Backup) {
        $backupFile = Backup-Environment
    }
    
    try {
        # Copy environment file
        Write-Info "Copying $sourceFile to .env..."
        Copy-Item $sourcePath $EnvPath -Force
        Write-Success "Environment switched to $TargetEnvironment"
        
        # Validate configuration if requested
        if ($Validate) {
            Write-Host ""
            $validationPassed = Test-EnvironmentConfiguration -EnvironmentType $TargetEnvironment
            
            if ($validationPassed) {
                # Test service connectivity
                Write-Host ""
                Test-ServiceConnectivity -EnvironmentType $TargetEnvironment
            }
        }
        
        # Show next steps
        Write-Host ""
        Write-Host "Next Steps:" -ForegroundColor Yellow
        Write-Host "===========" -ForegroundColor Yellow
        
        switch ($TargetEnvironment) {
            'xampp' {
                Write-Host "1. Ensure XAMPP MySQL and Apache are running"
                Write-Host "2. Ensure Redis is running in WSL: wsl sudo service redis-server start"
                Write-Host "3. Run Laravel migrations: php artisan migrate"
                Write-Host "4. Clear Laravel caches: php artisan config:clear && php artisan cache:clear"
                Write-Host "5. Start Laravel development server: php artisan serve --host=127.0.0.1"
            }
            'docker' {
                Write-Host "1. Start Docker containers: docker-compose up -d"
                Write-Host "2. Run Laravel migrations: docker-compose exec app php artisan migrate"
                Write-Host "3. Access application at configured Docker URL"
            }
            'local' {
                Write-Host "1. Ensure local services are running (MySQL, Redis, etc.)"
                Write-Host "2. Run Laravel migrations: php artisan migrate"
                Write-Host "3. Start Laravel development server: php artisan serve"
            }
        }
        
        Write-Host ""
        Write-Success "Environment switch completed successfully!"
        
    } catch {
        Write-Error "Failed to switch environment: $($_.Exception.Message)"
        
        # Restore backup if available
        if ($backupFile -and (Test-Path $backupFile)) {
            Write-Info "Restoring backup..."
            Copy-Item $backupFile $EnvPath -Force
            Write-Success "Backup restored"
        }
        
        exit 1
    }
}

# Restore environment from backup
function Restore-Environment {
    param([string]$BackupFile)
    
    if (-not $BackupFile) {
        # Find latest backup
        if (Test-Path $BackupPath) {
            $latestBackup = Get-ChildItem $BackupPath -Filter "*.env.backup.*" | 
                           Sort-Object LastWriteTime -Descending | 
                           Select-Object -First 1
            
            if ($latestBackup) {
                $BackupFile = $latestBackup.FullName
                Write-Info "Using latest backup: $($latestBackup.Name)"
            } else {
                Write-Error "No backup files found in $BackupPath"
                exit 1
            }
        } else {
            Write-Error "Backup directory not found: $BackupPath"
            exit 1
        }
    }
    
    if (-not (Test-Path $BackupFile)) {
        Write-Error "Backup file not found: $BackupFile"
        exit 1
    }
    
    Write-Info "Restoring environment from backup: $BackupFile"
    Copy-Item $BackupFile $EnvPath -Force
    Write-Success "Environment restored from backup"
}

# Main execution
try {
    # Change to script directory
    Set-Location $RootPath
    
    # Execute main function
    Switch-Environment -TargetEnvironment $Environment
    
} catch {
    Write-Error "Script execution failed: $($_.Exception.Message)"
    Write-Host "Stack trace:" -ForegroundColor Red
    Write-Host $_.ScriptStackTrace -ForegroundColor Red
    exit 1
}