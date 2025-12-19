#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Stop XAMPP services for ICTServe development

.DESCRIPTION
    This script stops XAMPP services (Apache, MySQL, Redis) gracefully and
    validates they have stopped correctly.

.PARAMETER Force
    Force stop services without confirmation

.PARAMETER StopRedis
    Also stop Redis if running

.EXAMPLE
    .\scripts\xampp\stop-xampp-services.ps1
    .\scripts\xampp\stop-xampp-services.ps1 -Force -StopRedis

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: XAMPP installation
#>

[CmdletBinding()]
param(
    [switch]$Force,
    [switch]$StopRedis
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }

# Stop Apache service
function Stop-ApacheService {
    Write-Info "Stopping Apache web server..."
    
    $apacheProcesses = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    if (-not $apacheProcesses) {
        Write-Success "Apache is not running"
        return
    }
    
    try {
        # Try graceful shutdown first
        $apacheProcesses | ForEach-Object {
            $_.CloseMainWindow() | Out-Null
        }
        
        Start-Sleep -Seconds 3
        
        # Check if processes are still running
        $remainingProcesses = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
        if ($remainingProcesses) {
            Write-Warning "Graceful shutdown failed, forcing Apache to stop..."
            $remainingProcesses | Stop-Process -Force
        }
        
        Write-Success "Apache web server stopped successfully"
    }
    catch {
        Write-Warning "Error stopping Apache: $($_.Exception.Message)"
    }
}

# Stop MySQL service
function Stop-MySQLService {
    Write-Info "Stopping MySQL database server..."
    
    $mysqlProcesses = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if (-not $mysqlProcesses) {
        Write-Success "MySQL is not running"
        return
    }
    
    try {
        # Try graceful shutdown using mysqladmin
        $xamppPaths = @(
            "C:\xampp\mysql\bin\mysqladmin.exe",
            "C:\laragon\bin\mysql\mysql-8.0.30-winx64\bin\mysqladmin.exe",
            "C:\wamp64\bin\mysql\mysql8.0.31\bin\mysqladmin.exe"
        )
        
        $mysqladminExe = $null
        foreach ($path in $xamppPaths) {
            if (Test-Path $path) {
                $mysqladminExe = $path
                break
            }
        }
        
        if ($mysqladminExe) {
            Write-Info "Attempting graceful MySQL shutdown..."
            & $mysqladminExe -u root shutdown 2>$null
            Start-Sleep -Seconds 3
        }
        
        # Check if processes are still running
        $remainingProcesses = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
        if ($remainingProcesses) {
            Write-Warning "Graceful shutdown failed, forcing MySQL to stop..."
            $remainingProcesses | Stop-Process -Force
        }
        
        Write-Success "MySQL database server stopped successfully"
    }
    catch {
        Write-Warning "Error stopping MySQL: $($_.Exception.Message)"
    }
}

# Stop Redis service
function Stop-RedisService {
    if (-not $StopRedis) { return }
    
    Write-Info "Stopping Redis server..."
    
    $redisProcesses = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
    if (-not $redisProcesses) {
        Write-Success "Redis is not running"
        return
    }
    
    try {
        # Try graceful shutdown
        $redisProcesses | ForEach-Object {
            $_.CloseMainWindow() | Out-Null
        }
        
        Start-Sleep -Seconds 2
        
        # Check if processes are still running
        $remainingProcesses = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
        if ($remainingProcesses) {
            Write-Warning "Graceful shutdown failed, forcing Redis to stop..."
            $remainingProcesses | Stop-Process -Force
        }
        
        Write-Success "Redis server stopped successfully"
    }
    catch {
        Write-Warning "Error stopping Redis: $($_.Exception.Message)"
    }
}

# Stop Laravel development services
function Stop-LaravelServices {
    Write-Info "Stopping Laravel development services..."
    
    # Stop Laravel development server
    $laravelProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*artisan serve*" }
    
    if ($laravelProcesses) {
        Write-Info "Stopping Laravel development server..."
        $laravelProcesses | Stop-Process -Force
        Write-Success "Laravel development server stopped"
    }
    
    # Stop Reverb server
    $reverbProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*reverb:start*" }
    
    if ($reverbProcesses) {
        Write-Info "Stopping Reverb WebSocket server..."
        $reverbProcesses | Stop-Process -Force
        Write-Success "Reverb WebSocket server stopped"
    }
    
    # Stop Horizon queue worker
    $horizonProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*horizon*" }
    
    if ($horizonProcesses) {
        Write-Info "Stopping Laravel Horizon..."
        $horizonProcesses | Stop-Process -Force
        Write-Success "Laravel Horizon stopped"
    }
}

# Validate services have stopped
function Test-ServicesStopped {
    Write-Info "Validating services have stopped..."
    
    $runningServices = @()
    
    # Check Apache
    if (Get-Process -Name "httpd" -ErrorAction SilentlyContinue) {
        $runningServices += "Apache (httpd)"
    }
    
    # Check MySQL
    if (Get-Process -Name "mysqld" -ErrorAction SilentlyContinue) {
        $runningServices += "MySQL (mysqld)"
    }
    
    # Check Redis
    if ($StopRedis -and (Get-Process -Name "redis-server" -ErrorAction SilentlyContinue)) {
        $runningServices += "Redis (redis-server)"
    }
    
    if ($runningServices.Count -eq 0) {
        Write-Success "All services stopped successfully"
    } else {
        Write-Warning "The following services are still running: $($runningServices -join ', ')"
        Write-Info "You may need to stop them manually through XAMPP Control Panel"
    }
}

# Show post-stop information
function Show-PostStopInfo {
    Write-Host "`n" + "="*50 -ForegroundColor Green
    Write-Success "XAMPP Services Stopped"
    Write-Host "="*50 -ForegroundColor Green
    
    Write-Info "Services that were stopped:"
    Write-Info "  • Apache Web Server"
    Write-Info "  • MySQL Database Server"
    
    if ($StopRedis) {
        Write-Info "  • Redis Server"
    }
    
    Write-Info "  • Laravel Development Services"
    
    Write-Info "`nTo restart services:"
    Write-Info "  • Use XAMPP Control Panel, or"
    Write-Info "  • Run: .\scripts\xampp\start-xampp-services.ps1"
    
    Write-Info "`nTo switch to Docker environment:"
    Write-Info "  • Run: .\scripts\swap-environment.ps1 -Environment docker"
}

# Main execution
function Main {
    try {
        Write-Host "`n" + "="*50 -ForegroundColor Blue
        Write-Host "  ICTServe XAMPP Service Stopper" -ForegroundColor Blue
        Write-Host "="*50 -ForegroundColor Blue
        
        # Confirmation prompt
        if (-not $Force) {
            $confirmation = Read-Host "Are you sure you want to stop XAMPP services? (y/N)"
            if ($confirmation -notmatch '^[Yy]') {
                Write-Warning "Operation cancelled by user"
                return
            }
        }
        
        Stop-LaravelServices
        Stop-ApacheService
        Stop-MySQLService
        Stop-RedisService
        
        Test-ServicesStopped
        Show-PostStopInfo
        
        Write-Success "`nXAMPP services stopped successfully!"
        
    }
    catch {
        Write-Error "Failed to stop XAMPP services: $($_.Exception.Message)"
        Write-Info "Try stopping services manually through XAMPP Control Panel"
        exit 1
    }
}

# Execute main function
Main
