#Requires -Version 5.1
<#
.SYNOPSIS
    ICTServe Environment Switcher - Switch between XAMPP, Laragon, and Docker environments

.DESCRIPTION
    This script provides a unified interface to switch between different development environments
    for the ICTServe Laravel application. It handles environment configuration, service management,
    and dependency installation for each environment type.

.PARAMETER Environment
    Target environment: xampp, laragon, or docker

.PARAMETER Action
    Action to perform: setup, start, stop, status, or switch

.PARAMETER Force
    Force operation without confirmation prompts

.PARAMETER Clean
    Clean existing configuration before setup

.PARAMETER SkipDeps
    Skip dependency installation during setup

.EXAMPLE
    .\scripts\switch-environment.ps1 -Environment xampp -Action setup
    Sets up XAMPP environment with full configuration

.EXAMPLE
    .\scripts\switch-environment.ps1 -Environment docker -Action switch -Force
    Switches to Docker environment without confirmation

.EXAMPLE
    .\scripts\switch-environment.ps1 -Environment laragon -Action status
    Shows status of Laragon environment

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: PowerShell 5.1+, Windows 10+
#>

[CmdletBinding()]
param(
    [Parameter(Mandatory = $true)]
    [ValidateSet('xampp', 'laragon', 'docker')]
    [string]$Environment,

    [Parameter(Mandatory = $true)]
    [ValidateSet('setup', 'start', 'stop', 'status', 'switch')]
    [string]$Action,

    [switch]$Force,
    [switch]$Clean,
    [switch]$SkipDeps
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Script configuration
$script:ScriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$script:ProjectRoot = Split-Path -Parent $script:ScriptRoot
$script:LogFile = Join-Path $script:ProjectRoot "storage\logs\environment-switch.log"

# Environment configurations
$script:Environments = @{
    xampp = @{
        Name = 'XAMPP'
        Description = 'XAMPP (Apache + MySQL + PHP)'
        EnvFile = '.env.xampp'
        SetupScript = 'scripts\xampp\setup-xampp.ps1'
        StartScript = 'scripts\xampp\start-xampp.ps1'
        StopScript = 'scripts\xampp\stop-xampp.ps1'
        StatusScript = 'scripts\xampp\status-xampp.ps1'
        DefaultPath = 'C:\xampp'
        Services = @('Apache', 'MySQL')
        Ports = @{
            Apache = 80
            MySQL = 3306
            Redis = 6379
        }
    }
    laragon = @{
        Name = 'Laragon'
        Description = 'Laragon (Nginx/Apache + MySQL + PHP)'
        EnvFile = '.env.laragon'
        SetupScript = 'scripts\laragon\setup-laragon.ps1'
        StartScript = 'scripts\laragon\start-laragon.ps1'
        StopScript = 'scripts\laragon\stop-laragon.ps1'
        StatusScript = 'scripts\laragon\status-laragon.ps1'
        DefaultPath = 'C:\laragon'
        Services = @('Nginx', 'Apache', 'MySQL', 'Redis')
        Ports = @{
            Nginx = 8080
            Apache = 80
            MySQL = 3306
            Redis = 6379
        }
    }
    docker = @{
        Name = 'Docker'
        Description = 'Docker Compose (Nginx + MySQL + Redis + PHP-FPM)'
        EnvFile = '.env.docker'
        SetupScript = 'scripts\docker\setup-docker.ps1'
        StartScript = 'scripts\docker\start-dev.ps1'
        StopScript = 'scripts\docker\stop-dev.ps1'
        StatusScript = 'scripts\docker\status-dev.ps1'
        DefaultPath = 'Docker Desktop'
        Services = @('app', 'db', 'redis', 'nginx')
        Ports = @{
            App = 8000
            MySQL = 3306
            Redis = 6379
            Nginx = 80
        }
    }
}

#region Utility Functions

function Write-Log {
    param(
        [string]$Message,
        [ValidateSet('Info', 'Warning', 'Error', 'Success')]
        [string]$Level = 'Info'
    )

    $timestamp = Get-Date -Format 'yyyy-MM-dd HH:mm:ss'
    $logEntry = "[$timestamp] [$Level] $Message"

    # Ensure log directory exists
    $logDir = Split-Path -Parent $script:LogFile
    if (-not (Test-Path $logDir)) {
        New-Item -ItemType Directory -Path $logDir -Force | Out-Null
    }

    # Write to log file
    Add-Content -Path $script:LogFile -Value $logEntry -Encoding UTF8

    # Write to console with colors
    switch ($Level) {
        'Info' { Write-Host "[INFO] $Message" -ForegroundColor Cyan }
        'Warning' { Write-Host "[WARN] $Message" -ForegroundColor Yellow }
        'Error' { Write-Host "[ERROR] $Message" -ForegroundColor Red }
        'Success' { Write-Host "[SUCCESS] $Message" -ForegroundColor Green }
    }
}

function Test-Administrator {
    $currentUser = [Security.Principal.WindowsIdentity]::GetCurrent()
    $principal = New-Object Security.Principal.WindowsPrincipal($currentUser)
    return $principal.IsInRole([Security.Principal.WindowsBuiltInRole]::Administrator)
}

function Test-PortAvailable {
    param([int]$Port)

    try {
        $listener = [System.Net.Sockets.TcpListener]::new([System.Net.IPAddress]::Any, $Port)
        $listener.Start()
        $listener.Stop()
        return $true
    }
    catch {
        return $false
    }
}

function Backup-Environment {
    if (Test-Path '.env') {
        $timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
        $backupFile = ".env.backup.$timestamp"
        Copy-Item '.env' $backupFile -Force
        Write-Log "Backed up current .env to $backupFile" -Level Success
        return $backupFile
    }
    return $null
}

function Test-Prerequisites {
    param([string]$Environment)

    Write-Log "Checking prerequisites for $Environment environment..." -Level Info

    $missing = @()
    $warnings = @()

    switch ($Environment) {
        'xampp' {
            $xamppPaths = @('C:\xampp', 'D:\xampp', 'E:\xampp')
            $xamppFound = $false

            foreach ($path in $xamppPaths) {
                if (Test-Path (Join-Path $path 'xampp-control.exe')) {
                    $xamppFound = $true
                    Write-Log "XAMPP found at $path" -Level Success
                    break
                }
            }

            if (-not $xamppFound) {
                $missing += 'XAMPP not found in common locations (C:\xampp, D:\xampp, E:\xampp)'
            }

            # Check if XAMPP services are installed
            $xamppServices = @('Apache2.4', 'mysql')
            foreach ($service in $xamppServices) {
                if (-not (Get-Service -Name $service -ErrorAction SilentlyContinue)) {
                    $warnings += "XAMPP service '$service' not found (may be managed by XAMPP directly)"
                }
            }
        }
        'laragon' {
            $laragonPaths = @('C:\laragon', 'D:\laragon', 'E:\laragon')
            $laragonFound = $false

            foreach ($path in $laragonPaths) {
                if (Test-Path (Join-Path $path 'laragon.exe')) {
                    $laragonFound = $true
                    Write-Log "Laragon found at $path" -Level Success
                    break
                }
            }

            if (-not $laragonFound) {
                $missing += 'Laragon not found in common locations (C:\laragon, D:\laragon, E:\laragon)'
            }
        }
        'docker' {
            try {
                $dockerVersion = docker --version 2>$null
                if ($dockerVersion) {
                    Write-Log "Docker found: $dockerVersion" -Level Success
                }
                else {
                    $missing += 'Docker not available or not responding'
                }
            }
            catch {
                $missing += 'Docker not available'
            }

            try {
                $composeVersion = docker compose version 2>$null
                if ($composeVersion) {
                    Write-Log "Docker Compose found: $composeVersion" -Level Success
                }
                else {
                    # Try legacy docker-compose
                    $legacyVersion = docker-compose --version 2>$null
                    if ($legacyVersion) {
                        Write-Log "Legacy Docker Compose found: $legacyVersion" -Level Success
                        $warnings += 'Using legacy docker-compose. Consider upgrading to Docker Compose V2'
                    }
                    else {
                        $missing += 'Docker Compose not available'
                    }
                }
            }
            catch {
                $missing += 'Docker Compose not available'
            }

            # Check if Docker daemon is running
            try {
                docker info | Out-Null
                Write-Log "Docker daemon is running" -Level Success
            }
            catch {
                $warnings += 'Docker daemon is not running. Please start Docker Desktop.'
            }
        }
    }

    # Check common prerequisites
    $commands = @{
        'php' = 'PHP interpreter'
        'composer' = 'Composer dependency manager'
        'npm' = 'Node.js package manager'
        'node' = 'Node.js runtime'
    }

    foreach ($cmd in $commands.Keys) {
        $command = Get-Command $cmd -ErrorAction SilentlyContinue
        if ($command) {
            try {
                $version = & $cmd --version 2>$null | Select-Object -First 1
                Write-Log "$($commands[$cmd]) found: $version" -Level Success
            }
            catch {
                Write-Log "$($commands[$cmd]) found at $($command.Source)" -Level Success
            }
        }
        else {
            if ($cmd -eq 'node' -and (Get-Command 'npm' -ErrorAction SilentlyContinue)) {
                # npm is available, so Node.js is likely installed
                $warnings += "$($commands[$cmd]) not found in PATH but npm is available"
            }
            else {
                $missing += "$($commands[$cmd]) not found in PATH"
            }
        }
    }

    # Check for Git (useful for development)
    if (Get-Command git -ErrorAction SilentlyContinue) {
        $gitVersion = git --version 2>$null
        Write-Log "Git found: $gitVersion" -Level Success
    }
    else {
        $warnings += 'Git not found in PATH (recommended for development)'
    }

    # Display warnings
    if ($warnings.Count -gt 0) {
        Write-Log "Warnings:" -Level Warning
        $warnings | ForEach-Object { Write-Log "  - $_" -Level Warning }
    }

    # Check for critical missing prerequisites
    if ($missing.Count -gt 0) {
        Write-Log "Missing prerequisites:" -Level Error
        $missing | ForEach-Object { Write-Log "  - $_" -Level Error }
        return $false
    }

    Write-Log "All critical prerequisites satisfied" -Level Success
    return $true
}

function Stop-AllEnvironments {
    Write-Log "Stopping all environments..." -Level Info

    # Stop Docker
    if (Get-Command docker -ErrorAction SilentlyContinue) {
        try {
            docker-compose down 2>$null
            Write-Log "Docker containers stopped" -Level Success
        }
        catch {
            Write-Log "No Docker containers to stop" -Level Info
        }
    }

    # Stop XAMPP services
    $xamppServices = @('Apache2.4', 'mysql')
    foreach ($service in $xamppServices) {
        $svc = Get-Service -Name $service -ErrorAction SilentlyContinue
        if ($svc -and $svc.Status -eq 'Running') {
            Stop-Service -Name $service -Force -ErrorAction SilentlyContinue
            Write-Log "Stopped $service service" -Level Success
        }
    }

    # Stop Laragon (if running)
    $laragonProcess = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    if ($laragonProcess) {
        Write-Log "Laragon is running. Please stop it manually from the system tray." -Level Warning
    }
}

#endregion

#region Main Functions

function Invoke-Setup {
    param([string]$Environment)

    $config = $script:Environments[$Environment]
    Write-Log "Setting up $($config.Name) environment..." -Level Info

    # Check prerequisites
    if (-not (Test-Prerequisites $Environment)) {
        throw "Prerequisites not met for $Environment environment"
    }

    # Stop other environments if requested
    if ($Clean) {
        Stop-AllEnvironments
    }

    # Run environment-specific setup
    $setupScript = Join-Path $script:ProjectRoot $config.SetupScript
    if (Test-Path $setupScript) {
        Write-Log "Running setup script: $setupScript" -Level Info

        $params = @{
            Force = $Force
            Clean = $Clean
            SkipDeps = $SkipDeps
        }

        & $setupScript @params

        if ($LASTEXITCODE -ne 0) {
            throw "Setup script failed with exit code $LASTEXITCODE"
        }
    }
    else {
        Write-Log "Setup script not found: $setupScript" -Level Warning
        Write-Log "Performing basic environment setup..." -Level Info

        # Basic setup: copy environment file
        if (Test-Path $config.EnvFile) {
            Backup-Environment
            Copy-Item $config.EnvFile '.env' -Force
            Write-Log "Copied $($config.EnvFile) to .env" -Level Success
        }
        else {
            Write-Log "Environment file $($config.EnvFile) not found" -Level Warning
        }
    }

    Write-Log "$($config.Name) environment setup completed" -Level Success
}

function Invoke-Start {
    param([string]$Environment)

    $config = $script:Environments[$Environment]
    Write-Log "Starting $($config.Name) environment..." -Level Info

    $startScript = Join-Path $script:ProjectRoot $config.StartScript
    if (Test-Path $startScript) {
        & $startScript

        if ($LASTEXITCODE -ne 0) {
            throw "Start script failed with exit code $LASTEXITCODE"
        }
    }
    else {
        Write-Log "Start script not found: $startScript" -Level Warning
        Write-Log "Please start $($config.Name) services manually" -Level Info
    }

    Write-Log "$($config.Name) environment started" -Level Success
}

function Invoke-Stop {
    param([string]$Environment)

    $config = $script:Environments[$Environment]
    Write-Log "Stopping $($config.Name) environment..." -Level Info

    $stopScript = Join-Path $script:ProjectRoot $config.StopScript
    if (Test-Path $stopScript) {
        & $stopScript

        if ($LASTEXITCODE -ne 0) {
            throw "Stop script failed with exit code $LASTEXITCODE"
        }
    }
    else {
        Write-Log "Stop script not found: $stopScript" -Level Warning

        # Basic stop logic
        switch ($Environment) {
            'docker' {
                if (Get-Command docker-compose -ErrorAction SilentlyContinue) {
                    docker-compose down
                }
            }
            'xampp' {
                $xamppServices = @('Apache2.4', 'mysql')
                foreach ($service in $xamppServices) {
                    $svc = Get-Service -Name $service -ErrorAction SilentlyContinue
                    if ($svc -and $svc.Status -eq 'Running') {
                        Stop-Service -Name $service -Force
                        Write-Log "Stopped $service service" -Level Success
                    }
                }
            }
        }
    }

    Write-Log "$($config.Name) environment stopped" -Level Success
}

function Get-Status {
    param([string]$Environment)

    $config = $script:Environments[$Environment]
    Write-Log "Checking $($config.Name) environment status..." -Level Info

    $statusScript = Join-Path $script:ProjectRoot $config.StatusScript
    if (Test-Path $statusScript) {
        & $statusScript
    }
    else {
        Write-Log "Status script not found: $statusScript" -Level Warning

        # Basic status check
        Write-Host ""
        Write-Host "$($config.Name) Environment Status" -ForegroundColor Cyan
        Write-Host "=" * 40 -ForegroundColor Cyan

        # Check ports
        foreach ($service in $config.Ports.Keys) {
            $port = $config.Ports[$service]
            $available = Test-PortAvailable $port
            $status = if ($available) { "Available" } else { "In Use" }
            $color = if ($available) { "Green" } else { "Red" }
            Write-Host "  $service (Port $port): $status" -ForegroundColor $color
        }

        # Check environment file
        $envExists = Test-Path '.env'
        $envStatus = if ($envExists) { "Present" } else { "Missing" }
        $envColor = if ($envExists) { "Green" } else { "Red" }
        Write-Host "  Environment File: $envStatus" -ForegroundColor $envColor

        # Check if current .env matches target environment
        if ($envExists -and (Test-Path $config.EnvFile)) {
            $currentEnv = Get-Content '.env' -Raw
            $targetEnv = Get-Content $config.EnvFile -Raw
            $matches = $currentEnv -eq $targetEnv
            $matchStatus = if ($matches) { "Matches $($config.EnvFile)" } else { "Different from $($config.EnvFile)" }
            $matchColor = if ($matches) { "Green" } else { "Yellow" }
            Write-Host "  Environment Config: $matchStatus" -ForegroundColor $matchColor
        }
    }
}

function Invoke-Switch {
    param([string]$Environment)

    $config = $script:Environments[$Environment]

    if (-not $Force) {
        Write-Host ""
        Write-Host "Switch to $($config.Name) Environment" -ForegroundColor Cyan
        Write-Host "=" * 40 -ForegroundColor Cyan
        Write-Host "Description: $($config.Description)" -ForegroundColor White
        Write-Host "Services: $($config.Services -join ', ')" -ForegroundColor White
        Write-Host ""

        $confirm = Read-Host "Continue with environment switch? (y/N)"
        if ($confirm -notmatch '^[Yy]') {
            Write-Log "Environment switch cancelled by user" -Level Info
            return
        }
    }

    Write-Log "Switching to $($config.Name) environment..." -Level Info

    # Stop current environment
    Write-Log "Stopping current environment..." -Level Info
    Stop-AllEnvironments

    # Switch environment file
    if (Test-Path $config.EnvFile) {
        Backup-Environment
        Copy-Item $config.EnvFile '.env' -Force
        Write-Log "Switched to $($config.EnvFile)" -Level Success
    }
    else {
        Write-Log "Environment file $($config.EnvFile) not found. Creating from .env.example..." -Level Warning
        if (Test-Path '.env.example') {
            Copy-Item '.env.example' '.env' -Force
            Write-Log "Created .env from .env.example" -Level Success
        }
        else {
            throw "No environment file available for $Environment"
        }
    }

    # Clear Laravel caches
    if (Get-Command php -ErrorAction SilentlyContinue) {
        Write-Log "Clearing Laravel caches..." -Level Info
        php artisan config:clear 2>$null
        php artisan cache:clear 2>$null
        php artisan route:clear 2>$null
        php artisan view:clear 2>$null
        Write-Log "Laravel caches cleared" -Level Success
    }

    Write-Log "Environment switched to $($config.Name)" -Level Success
    Write-Log "Next steps:" -Level Info
    Write-Log "  1. Run: .\scripts\switch-environment.ps1 -Environment $Environment -Action start" -Level Info
    Write-Log "  2. Verify services are running with: .\scripts\switch-environment.ps1 -Environment $Environment -Action status" -Level Info
}

#endregion

#region Main Execution

try {
    Write-Host ""
    Write-Host "ICTServe Environment Switcher" -ForegroundColor Cyan
    Write-Host "=" * 40 -ForegroundColor Cyan
    Write-Host "Environment: $($script:Environments[$Environment].Name)" -ForegroundColor White
    Write-Host "Action: $Action" -ForegroundColor White
    Write-Host ""

    # Change to project root
    Push-Location $script:ProjectRoot

    # Execute requested action
    switch ($Action) {
        'setup' { Invoke-Setup $Environment }
        'start' { Invoke-Start $Environment }
        'stop' { Invoke-Stop $Environment }
        'status' { Get-Status $Environment }
        'switch' { Invoke-Switch $Environment }
    }

    Write-Host ""
    Write-Host "Operation completed successfully!" -ForegroundColor Green
    Write-Log "Operation completed: $Action for $Environment" -Level Success
}
catch {
    $errorMessage = $_.Exception.Message
    Write-Host ""
    Write-Host "Operation failed: $errorMessage" -ForegroundColor Red
    Write-Log "Operation failed: $errorMessage" -Level Error
    exit 1
}
finally {
    Pop-Location
}

#endregion
