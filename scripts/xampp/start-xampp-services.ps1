#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Start XAMPP services for ICTServe development

.DESCRIPTION
    This script starts the necessary XAMPP services (Apache, MySQL) and validates
    they are running correctly for ICTServe development.

.PARAMETER SkipValidation
    Skip service validation after starting

.PARAMETER StartRedis
    Also attempt to start Redis if available

.EXAMPLE
    .\scripts\xampp\start-xampp-services.ps1
    .\scripts\xampp\start-xampp-services.ps1 -StartRedis

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: XAMPP installation
#>

[CmdletBinding()]
param(
    [switch]$SkipValidation,
    [switch]$StartRedis
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }

# Find XAMPP installation
function Find-XamppInstallation {
    $xamppPaths = @(
        "C:\xampp",
        "C:\laragon",
        "C:\wamp64",
        "D:\xampp",
        "E:\xampp"
    )
    
    foreach ($path in $xamppPaths) {
        if (Test-Path $path) {
            Write-Success "Found local server installation at: $path"
            return $path
        }
    }
    
    throw "No XAMPP/Laragon/WAMP installation found. Please install one of these local development environments."
}

# Start Apache service
function Start-ApacheService {
    param([string]$XamppPath)
    
    Write-Info "Starting Apache web server..."
    
    $apachePaths = @(
        "$XamppPath\apache\bin\httpd.exe",
        "$XamppPath\bin\apache\httpd.exe",
        "$XamppPath\apache2\bin\httpd.exe"
    )
    
    $apacheExe = $null
    foreach ($path in $apachePaths) {
        if (Test-Path $path) {
            $apacheExe = $path
            break
        }
    }
    
    if (-not $apacheExe) {
        Write-Warning "Apache executable not found. Please start Apache manually through XAMPP Control Panel."
        return
    }
    
    # Check if Apache is already running
    $apacheProcess = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
    if ($apacheProcess) {
        Write-Success "Apache is already running"
        return
    }
    
    try {
        # Start Apache
        Start-Process -FilePath $apacheExe -WindowStyle Hidden
        Start-Sleep -Seconds 3
        
        # Verify Apache started
        $apacheProcess = Get-Process -Name "httpd" -ErrorAction SilentlyContinue
        if ($apacheProcess) {
            Write-Success "Apache web server started successfully"
        } else {
            Write-Warning "Apache may not have started correctly. Check XAMPP Control Panel."
        }
    }
    catch {
        Write-Warning "Could not start Apache automatically: $($_.Exception.Message)"
        Write-Info "Please start Apache manually through XAMPP Control Panel"
    }
}

# Start MySQL service
function Start-MySQLService {
    param([string]$XamppPath)
    
    Write-Info "Starting MySQL database server..."
    
    $mysqlPaths = @(
        "$XamppPath\mysql\bin\mysqld.exe",
        "$XamppPath\bin\mysql\bin\mysqld.exe",
        "$XamppPath\mysql\bin\mysqld_safe"
    )
    
    $mysqlExe = $null
    foreach ($path in $mysqlPaths) {
        if (Test-Path $path) {
            $mysqlExe = $path
            break
        }
    }
    
    if (-not $mysqlExe) {
        Write-Warning "MySQL executable not found. Please start MySQL manually through XAMPP Control Panel."
        return
    }
    
    # Check if MySQL is already running
    $mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
    if ($mysqlProcess) {
        Write-Success "MySQL is already running"
        return
    }
    
    try {
        # Start MySQL
        $mysqlArgs = @("--defaults-file=$XamppPath\mysql\bin\my.ini")
        Start-Process -FilePath $mysqlExe -ArgumentList $mysqlArgs -WindowStyle Hidden
        Start-Sleep -Seconds 5
        
        # Verify MySQL started
        $mysqlProcess = Get-Process -Name "mysqld" -ErrorAction SilentlyContinue
        if ($mysqlProcess) {
            Write-Success "MySQL database server started successfully"
        } else {
            Write-Warning "MySQL may not have started correctly. Check XAMPP Control Panel."
        }
    }
    catch {
        Write-Warning "Could not start MySQL automatically: $($_.Exception.Message)"
        Write-Info "Please start MySQL manually through XAMPP Control Panel"
    }
}

# Start Redis if available
function Start-RedisService {
    if (-not $StartRedis) { return }
    
    Write-Info "Checking for Redis installation..."
    
    $redisPaths = @(
        "C:\Program Files\Redis\redis-server.exe",
        "C:\Redis\redis-server.exe",
        "C:\tools\redis\redis-server.exe"
    )
    
    $redisExe = $null
    foreach ($path in $redisPaths) {
        if (Test-Path $path) {
            $redisExe = $path
            break
        }
    }
    
    if (-not $redisExe) {
        Write-Warning "Redis not found. Install Redis for Windows if you need caching features."
        return
    }
    
    # Check if Redis is already running
    $redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
    if ($redisProcess) {
        Write-Success "Redis is already running"
        return
    }
    
    try {
        # Start Redis
        Start-Process -FilePath $redisExe -WindowStyle Hidden
        Start-Sleep -Seconds 2
        
        # Verify Redis started
        $redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
        if ($redisProcess) {
            Write-Success "Redis server started successfully"
        } else {
            Write-Warning "Redis may not have started correctly"
        }
    }
    catch {
        Write-Warning "Could not start Redis: $($_.Exception.Message)"
    }
}

# Validate services are running
function Test-Services {
    if ($SkipValidation) {
        Write-Warning "Skipping service validation as requested"
        return
    }
    
    Write-Info "Validating services..."
    
    # Test Apache (port 80)
    try {
        $response = Invoke-WebRequest -Uri "http://127.0.0.1" -TimeoutSec 5 -ErrorAction Stop
        Write-Success "Apache is responding on port 80"
    }
    catch {
        Write-Warning "Apache is not responding on port 80. Check XAMPP Control Panel."
    }
    
    # Test MySQL (port 3306)
    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect("127.0.0.1", 3306)
        $tcpClient.Close()
        Write-Success "MySQL is accepting connections on port 3306"
    }
    catch {
        Write-Warning "MySQL is not accepting connections on port 3306. Check XAMPP Control Panel."
    }
    
    # Test Redis if requested (port 6379)
    if ($StartRedis) {
        try {
            $tcpClient = New-Object System.Net.Sockets.TcpClient
            $tcpClient.Connect("127.0.0.1", 6379)
            $tcpClient.Close()
            Write-Success "Redis is accepting connections on port 6379"
        }
        catch {
            Write-Warning "Redis is not accepting connections on port 6379"
        }
    }
}

# Show service information
function Show-ServiceInfo {
    Write-Host "`n" + "="*50 -ForegroundColor Green
    Write-Success "XAMPP Services Status"
    Write-Host "="*50 -ForegroundColor Green
    
    Write-Info "Service URLs:"
    Write-Info "  • Apache Web Server: http://127.0.0.1"
    Write-Info "  • phpMyAdmin: http://127.0.0.1/phpmyadmin"
    Write-Info "  • MySQL: 127.0.0.1:3306"
    
    if ($StartRedis) {
        Write-Info "  • Redis: 127.0.0.1:6379"
    }
    
    Write-Info "`nFor ICTServe development:"
    Write-Info "  • Run: php artisan serve --host=127.0.0.1"
    Write-Info "  • Run: php artisan reverb:start (separate terminal)"
    Write-Info "  • Access: http://127.0.0.1:8000"
    
    Write-Info "`nXAMP Control Panel:"
    Write-Info "  • Use XAMPP Control Panel to manage services"
    Write-Info "  • Monitor service status and logs"
}

# Main execution
function Main {
    try {
        Write-Host "`n" + "="*50 -ForegroundColor Blue
        Write-Host "  ICTServe XAMPP Service Starter" -ForegroundColor Blue
        Write-Host "="*50 -ForegroundColor Blue
        
        $xamppPath = Find-XamppInstallation
        
        Start-ApacheService -XamppPath $xamppPath
        Start-MySQLService -XamppPath $xamppPath
        Start-RedisService
        
        Test-Services
        Show-ServiceInfo
        
        Write-Success "`nXAMPP services startup completed!"
        
    }
    catch {
        Write-Error "Failed to start XAMPP services: $($_.Exception.Message)"
        Write-Info "Try starting services manually through XAMPP Control Panel"
        exit 1
    }
}

# Execute main function
Main
