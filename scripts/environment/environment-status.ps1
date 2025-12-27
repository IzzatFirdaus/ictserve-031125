#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Check the status of ICTServe development environments (XAMPP and Docker)

.DESCRIPTION
    This script checks the current status of both XAMPP and Docker environments,
    shows which services are running, and provides information about the active
    environment configuration.

.PARAMETER ShowDetails
    Show detailed information about running services

.PARAMETER CheckConnectivity
    Test connectivity to databases and services

.EXAMPLE
    .\scripts\environment-status.ps1
    .\scripts\environment-status.ps1 -ShowDetails -CheckConnectivity

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: PowerShell 5.1+
#>

[CmdletBinding()]
param(
    [switch]$ShowDetails,
    [switch]$CheckConnectivity
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Continue'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }
function Write-Status { param([string]$Message) Write-Host "📊 $Message" -ForegroundColor Magenta }

$ProjectRoot = Split-Path -Parent $PSScriptRoot

# Check current environment configuration
function Get-CurrentEnvironment {
    Write-Status "Checking current environment configuration..."
    
    $envFile = "$ProjectRoot\.env"
    if (-not (Test-Path $envFile)) {
        Write-Warning "No .env file found. Environment not configured."
        return @{
            Type = "Unknown"
            Configured = $false
        }
    }
    
    $envContent = Get-Content $envFile -ErrorAction SilentlyContinue
    if (-not $envContent) {
        Write-Warning ".env file is empty or unreadable"
        return @{
            Type = "Unknown"
            Configured = $false
        }
    }
    
    # Determine environment type based on DB_HOST
    $dbHost = ($envContent | Where-Object { $_ -match '^DB_HOST=' }) -replace '^DB_HOST=', ''
    $appUrl = ($envContent | Where-Object { $_ -match '^APP_URL=' }) -replace '^APP_URL=', ''
    
    $envType = switch ($dbHost) {
        'db' { 'Docker' }
        '127.0.0.1' { 'XAMPP' }
        'localhost' { 'Local' }
        default { 'Unknown' }
    }
    
    return @{
        Type = $envType
        Configured = $true
        DBHost = $dbHost
        AppURL = $appUrl
        ConfigFile = $envFile
    }
}

# Check XAMPP services status
function Get-XamppStatus {
    Write-Status "Checking XAMPP services..."
    
    $xamppStatus = @{
        Apache = @{ Running = $false; Process = $null }
        MySQL = @{ Running = $false; Process = $null }
        Redis = @{ Running = $false; Process = $null }
    }
    
    # Check Apache (httpd)
    $apacheProcesses = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    if ($apacheProcesses) {
        $xamppStatus.Apache.Running = $true
        $xamppStatus.Apache.Process = $apacheProcesses | Select-Object -First 1
    }
    
    # Check MySQL (mysqld)
    $mysqlProcesses = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if ($mysqlProcesses) {
        $xamppStatus.MySQL.Running = $true
        $xamppStatus.MySQL.Process = $mysqlProcesses | Select-Object -First 1
    }
    
    # Check Redis
    $redisProcesses = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
    if ($redisProcesses) {
        $xamppStatus.Redis.Running = $true
        $xamppStatus.Redis.Process = $redisProcesses | Select-Object -First 1
    }
    
    return $xamppStatus
}

# Check Docker services status
function Get-DockerStatus {
    Write-Status "Checking Docker services..."
    
    $dockerStatus = @{
        DockerDesktop = @{ Running = $false; Version = $null }
        Containers = @()
        ComposeFile = $false
    }
    
    # Check if Docker is installed and running
    try {
        $dockerVersion = docker --version 2>$null
        if ($dockerVersion) {
            $dockerStatus.DockerDesktop.Version = $dockerVersion
            
            # Check if Docker Desktop is running
            $dockerInfo = docker info 2>$null
            if ($dockerInfo) {
                $dockerStatus.DockerDesktop.Running = $true
            }
        }
    }
    catch {
        # Docker not available
    }
    
    # Check for compose.yaml
    if (Test-Path "$ProjectRoot\compose.yaml") {
        $dockerStatus.ComposeFile = $true
        
        # Get container status if Docker is running
        if ($dockerStatus.DockerDesktop.Running) {
            try {
                Push-Location $ProjectRoot
                $containers = docker-compose ps --format json 2>$null | ConvertFrom-Json
                if ($containers) {
                    $dockerStatus.Containers = $containers
                }
            }
            catch {
                # Could not get container status
            }
            finally {
                Pop-Location
            }
        }
    }
    
    return $dockerStatus
}

# Check Laravel development services
function Get-LaravelStatus {
    Write-Status "Checking Laravel development services..."
    
    $laravelStatus = @{
        DevServer = @{ Running = $false; Process = $null; Port = $null }
        Reverb = @{ Running = $false; Process = $null; Port = $null }
        Horizon = @{ Running = $false; Process = $null }
        QueueWorker = @{ Running = $false; Process = $null }
    }
    
    # Check Laravel development server
    $devServerProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*artisan serve*" }
    
    if ($devServerProcesses) {
        $laravelStatus.DevServer.Running = $true
        $laravelStatus.DevServer.Process = $devServerProcesses | Select-Object -First 1
        
        # Try to extract port from command line
        $commandLine = $devServerProcesses[0].CommandLine
        if ($commandLine -match '--port=(\d+)') {
            $laravelStatus.DevServer.Port = $matches[1]
        } else {
            $laravelStatus.DevServer.Port = "8000" # Default port
        }
    }
    
    # Check Reverb WebSocket server
    $reverbProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*reverb:start*" }
    
    if ($reverbProcesses) {
        $laravelStatus.Reverb.Running = $true
        $laravelStatus.Reverb.Process = $reverbProcesses | Select-Object -First 1
        $laravelStatus.Reverb.Port = "8080" # Default Reverb port
    }
    
    # Check Laravel Horizon
    $horizonProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*horizon*" }
    
    if ($horizonProcesses) {
        $laravelStatus.Horizon.Running = $true
        $laravelStatus.Horizon.Process = $horizonProcesses | Select-Object -First 1
    }
    
    # Check Queue Worker
    $queueProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
        Where-Object { $_.CommandLine -like "*queue:work*" }
    
    if ($queueProcesses) {
        $laravelStatus.QueueWorker.Running = $true
        $laravelStatus.QueueWorker.Process = $queueProcesses | Select-Object -First 1
    }
    
    return $laravelStatus
}

# Test connectivity to services
function Test-ServiceConnectivity {
    if (-not $CheckConnectivity) { return @{} }
    
    Write-Status "Testing service connectivity..."
    
    $connectivity = @{
        HTTP = @{}
        Database = @{}
        Redis = @{}
        WebSocket = @{}
    }
    
    # Test HTTP endpoints
    $httpEndpoints = @{
        "Laravel App (8000)" = "http://127.0.0.1:8000"
        "Laravel App (Docker)" = "http://localhost:8000"
        "Apache (80)" = "http://127.0.0.1"
        "phpMyAdmin (Docker)" = "http://localhost:8080"
    }
    
    foreach ($name in $httpEndpoints.Keys) {
        $url = $httpEndpoints[$name]
        try {
            $response = Invoke-WebRequest -Uri $url -TimeoutSec 5 -ErrorAction Stop
            $connectivity.HTTP[$name] = @{ Status = "OK"; StatusCode = $response.StatusCode }
        }
        catch {
            $connectivity.HTTP[$name] = @{ Status = "Failed"; Error = $_.Exception.Message }
        }
    }
    
    # Test database connections
    $dbEndpoints = @{
        "MySQL (3306)" = @{ Host = "127.0.0.1"; Port = 3306 }
        "MySQL Docker (3306)" = @{ Host = "localhost"; Port = 3306 }
    }
    
    foreach ($name in $dbEndpoints.Keys) {
        $endpoint = $dbEndpoints[$name]
        try {
            $tcpClient = New-Object System.Net.Sockets.TcpClient
            $tcpClient.Connect($endpoint.Host, $endpoint.Port)
            $tcpClient.Close()
            $connectivity.Database[$name] = @{ Status = "OK" }
        }
        catch {
            $connectivity.Database[$name] = @{ Status = "Failed"; Error = $_.Exception.Message }
        }
    }
    
    # Test Redis connections
    $redisEndpoints = @{
        "Redis (6379)" = @{ Host = "127.0.0.1"; Port = 6379 }
        "Redis Docker (6379)" = @{ Host = "localhost"; Port = 6379 }
    }
    
    foreach ($name in $redisEndpoints.Keys) {
        $endpoint = $redisEndpoints[$name]
        try {
            $tcpClient = New-Object System.Net.Sockets.TcpClient
            $tcpClient.Connect($endpoint.Host, $endpoint.Port)
            $tcpClient.Close()
            $connectivity.Redis[$name] = @{ Status = "OK" }
        }
        catch {
            $connectivity.Redis[$name] = @{ Status = "Failed"; Error = $_.Exception.Message }
        }
    }
    
    return $connectivity
}

# Display environment status
function Show-EnvironmentStatus {
    param($Environment, $XamppStatus, $DockerStatus, $LaravelStatus, $Connectivity)
    
    Write-Host "`n" + "="*70 -ForegroundColor Blue
    Write-Host "  ICTServe Development Environment Status" -ForegroundColor Blue
    Write-Host "="*70 -ForegroundColor Blue
    
    # Current Environment
    Write-Host "`n📋 Current Environment Configuration:" -ForegroundColor Yellow
    if ($Environment.Configured) {
        Write-Success "Environment Type: $($Environment.Type)"
        Write-Info "Database Host: $($Environment.DBHost)"
        Write-Info "Application URL: $($Environment.AppURL)"
        Write-Info "Config File: $($Environment.ConfigFile)"
    } else {
        Write-Warning "Environment not properly configured"
    }
    
    # XAMPP Services
    Write-Host "`n🔧 XAMPP Services:" -ForegroundColor Yellow
    $xamppRunning = 0
    
    if ($XamppStatus.Apache.Running) {
        Write-Success "Apache Web Server: Running (PID: $($XamppStatus.Apache.Process.Id))"
        $xamppRunning++
    } else {
        Write-Error "Apache Web Server: Not Running"
    }
    
    if ($XamppStatus.MySQL.Running) {
        Write-Success "MySQL Database: Running (PID: $($XamppStatus.MySQL.Process.Id))"
        $xamppRunning++
    } else {
        Write-Error "MySQL Database: Not Running"
    }
    
    if ($XamppStatus.Redis.Running) {
        Write-Success "Redis Server: Running (PID: $($XamppStatus.Redis.Process.Id))"
        $xamppRunning++
    } else {
        Write-Info "Redis Server: Not Running (Optional)"
    }
    
    Write-Info "XAMPP Services Running: $xamppRunning/2 (Redis optional)"
    
    # Docker Services
    Write-Host "`n🐳 Docker Services:" -ForegroundColor Yellow
    
    if ($DockerStatus.DockerDesktop.Running) {
        Write-Success "Docker Desktop: Running"
        Write-Info "Version: $($DockerStatus.DockerDesktop.Version)"
        
        if ($DockerStatus.ComposeFile) {
            Write-Success "Docker Compose File: Found"
            
            if ($DockerStatus.Containers.Count -gt 0) {
                Write-Success "Containers: $($DockerStatus.Containers.Count) found"
                
                if ($ShowDetails) {
                    foreach ($container in $DockerStatus.Containers) {
                        $status = if ($container.State -eq "running") { "✅" } else { "❌" }
                        Write-Host "  $status $($container.Name): $($container.State)" -ForegroundColor White
                    }
                }
            } else {
                Write-Info "Containers: None running"
            }
        } else {
            Write-Warning "Docker Compose File: Not found"
        }
    } else {
        Write-Error "Docker Desktop: Not Running or Not Installed"
    }
    
    # Laravel Services
    Write-Host "`n🚀 Laravel Development Services:" -ForegroundColor Yellow
    
    if ($LaravelStatus.DevServer.Running) {
        Write-Success "Laravel Dev Server: Running on port $($LaravelStatus.DevServer.Port) (PID: $($LaravelStatus.DevServer.Process.Id))"
    } else {
        Write-Info "Laravel Dev Server: Not Running"
    }
    
    if ($LaravelStatus.Reverb.Running) {
        Write-Success "Reverb WebSocket: Running on port $($LaravelStatus.Reverb.Port) (PID: $($LaravelStatus.Reverb.Process.Id))"
    } else {
        Write-Info "Reverb WebSocket: Not Running"
    }
    
    if ($LaravelStatus.Horizon.Running) {
        Write-Success "Laravel Horizon: Running (PID: $($LaravelStatus.Horizon.Process.Id))"
    } else {
        Write-Info "Laravel Horizon: Not Running"
    }
    
    if ($LaravelStatus.QueueWorker.Running) {
        Write-Success "Queue Worker: Running (PID: $($LaravelStatus.QueueWorker.Process.Id))"
    } else {
        Write-Info "Queue Worker: Not Running"
    }
}

# Display connectivity test results
function Show-ConnectivityResults {
    param($Connectivity)
    
    if (-not $CheckConnectivity -or $Connectivity.Count -eq 0) { return }
    
    Write-Host "`n🌐 Connectivity Test Results:" -ForegroundColor Yellow
    
    # HTTP Connectivity
    if ($Connectivity.HTTP.Count -gt 0) {
        Write-Host "`n  HTTP Endpoints:" -ForegroundColor Cyan
        foreach ($endpoint in $Connectivity.HTTP.Keys) {
            $result = $Connectivity.HTTP[$endpoint]
            if ($result.Status -eq "OK") {
                Write-Success "  $endpoint - OK (Status: $($result.StatusCode))"
            } else {
                Write-Error "  $endpoint - Failed"
                if ($ShowDetails) {
                    Write-Host "    Error: $($result.Error)" -ForegroundColor Red
                }
            }
        }
    }
    
    # Database Connectivity
    if ($Connectivity.Database.Count -gt 0) {
        Write-Host "`n  Database Connections:" -ForegroundColor Cyan
        foreach ($db in $Connectivity.Database.Keys) {
            $result = $Connectivity.Database[$db]
            if ($result.Status -eq "OK") {
                Write-Success "  $db - OK"
            } else {
                Write-Error "  $db - Failed"
                if ($ShowDetails) {
                    Write-Host "    Error: $($result.Error)" -ForegroundColor Red
                }
            }
        }
    }
    
    # Redis Connectivity
    if ($Connectivity.Redis.Count -gt 0) {
        Write-Host "`n  Redis Connections:" -ForegroundColor Cyan
        foreach ($redis in $Connectivity.Redis.Keys) {
            $result = $Connectivity.Redis[$redis]
            if ($result.Status -eq "OK") {
                Write-Success "  $redis - OK"
            } else {
                Write-Info "  $redis - Not Available"
                if ($ShowDetails) {
                    Write-Host "    Error: $($result.Error)" -ForegroundColor Gray
                }
            }
        }
    }
}

# Show recommendations
function Show-Recommendations {
    param($Environment, $XamppStatus, $DockerStatus, $LaravelStatus)
    
    Write-Host "`n💡 Recommendations:" -ForegroundColor Yellow
    
    # Environment-specific recommendations
    switch ($Environment.Type) {
        'Docker' {
            if (-not $DockerStatus.DockerDesktop.Running) {
                Write-Warning "Docker environment configured but Docker Desktop is not running"
                Write-Info "  • Start Docker Desktop"
                Write-Info "  • Run: .\scripts\docker\start-docker-services.ps1"
            } elseif ($DockerStatus.Containers.Count -eq 0) {
                Write-Warning "Docker is running but no containers are active"
                Write-Info "  • Run: .\scripts\docker\start-docker-services.ps1"
            }
        }
        'XAMPP' {
            $xamppIssues = @()
            if (-not $XamppStatus.Apache.Running) { $xamppIssues += "Apache" }
            if (-not $XamppStatus.MySQL.Running) { $xamppIssues += "MySQL" }
            
            if ($xamppIssues.Count -gt 0) {
                Write-Warning "XAMPP environment configured but services not running: $($xamppIssues -join ', ')"
                Write-Info "  • Start XAMPP Control Panel"
                Write-Info "  • Run: .\scripts\xampp\start-xampp-services.ps1"
            }
        }
        'Unknown' {
            Write-Warning "Environment type could not be determined"
            Write-Info "  • Run: .\scripts\swap-environment.ps1 -Environment [xampp|docker]"
        }
    }
    
    # Laravel service recommendations
    if (-not $LaravelStatus.DevServer.Running) {
        Write-Info "Laravel development server is not running"
        Write-Info "  • Run: php artisan serve --host=127.0.0.1"
    }
    
    if (-not $LaravelStatus.Reverb.Running) {
        Write-Info "Reverb WebSocket server is not running (optional for real-time features)"
        Write-Info "  • Run: php artisan reverb:start"
    }
    
    # Quick start commands
    Write-Host "`n🚀 Quick Start Commands:" -ForegroundColor Yellow
    Write-Info "Switch to Docker:  .\scripts\swap-environment.ps1 -Environment docker"
    Write-Info "Switch to XAMPP:   .\scripts\swap-environment.ps1 -Environment xampp"
    Write-Info "Start XAMPP:       .\scripts\xampp\start-xampp-services.ps1"
    Write-Info "Start Docker:      .\scripts\docker\start-docker-services.ps1"
    Write-Info "Check status:      .\scripts\environment-status.ps1 -ShowDetails"
}

# Main execution
function Main {
    try {
        $environment = Get-CurrentEnvironment
        $xamppStatus = Get-XamppStatus
        $dockerStatus = Get-DockerStatus
        $laravelStatus = Get-LaravelStatus
        $connectivity = Test-ServiceConnectivity
        
        Show-EnvironmentStatus -Environment $environment -XamppStatus $xamppStatus -DockerStatus $dockerStatus -LaravelStatus $laravelStatus -Connectivity $connectivity
        Show-ConnectivityResults -Connectivity $connectivity
        Show-Recommendations -Environment $environment -XamppStatus $xamppStatus -DockerStatus $dockerStatus -LaravelStatus $laravelStatus
        
        Write-Host "`n" + "="*70 -ForegroundColor Green
        Write-Success "Environment status check completed!"
        Write-Host "="*70 -ForegroundColor Green
        
    }
    catch {
        Write-Error "Failed to check environment status: $($_.Exception.Message)"
        exit 1
    }
}

# Execute main function
Main
