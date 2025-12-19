#Requires -Version 5.1
<#
.SYNOPSIS
    Start Laragon services for ICTServe development

.DESCRIPTION
    Starts Laragon services and verifies they are running properly.
    Includes health checks and service verification.

.PARAMETER LaragonPath
    Path to Laragon installation (default: C:\laragon)

.PARAMETER WaitForServices
    Wait for services to be fully ready before completing

.PARAMETER OpenBrowser
    Open browser to application URL after starting services

.EXAMPLE
    .\scripts\laragon\start-laragon.ps1 -OpenBrowser
    Start services and open browser

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [string]$LaragonPath = 'C:\laragon',
    [switch]$WaitForServices,
    [switch]$OpenBrowser
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

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

function Test-PortOpen {
    param([int]$Port, [string]$Host = '127.0.0.1')

    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect($Host, $Port)
        $tcpClient.Close()
        return $true
    }
    catch {
        return $false
    }
}

function Start-LaragonService {
    $laragonExe = Join-Path $LaragonPath 'laragon.exe'

    if (-not (Test-Path $laragonExe)) {
        throw "Laragon executable not found at $laragonExe"
    }

    # Check if Laragon is already running
    $laragonProcess = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    if ($laragonProcess) {
        Write-Status "Laragon is already running" -Type Success
        return
    }

    Write-Status "Starting Laragon..." -Type Info
    Start-Process -FilePath $laragonExe -WindowStyle Minimized

    # Wait for Laragon to start
    $timeout = 30
    $waited = 0
    while (-not (Get-Process -Name 'laragon' -ErrorAction SilentlyContinue) -and $waited -lt $timeout) {
        Start-Sleep -Seconds 1
        $waited++
    }

    if (Get-Process -Name 'laragon' -ErrorAction SilentlyContinue) {
        Write-Status "Laragon started successfully" -Type Success
    }
    else {
        throw "Failed to start Laragon within timeout"
    }
}

function Start-LaragonServices {
    Write-Status "Starting Laragon services..." -Type Info

    # Use Laragon CLI if available
    $laragonCli = Join-Path $LaragonPath 'bin\laragon.exe'
    if (Test-Path $laragonCli) {
        try {
            & $laragonCli start all
            Write-Status "Services started via Laragon CLI" -Type Success
        }
        catch {
            Write-Status "Failed to start services via CLI, trying manual approach" -Type Warning
        }
    }

    # Manual service start approach
    $services = @{
        'nginx' = 'Nginx Web Server'
        'mysql' = 'MySQL Database'
        'redis' = 'Redis Cache'
    }

    foreach ($serviceName in $services.Keys) {
        $displayName = $services[$serviceName]

        # Try to start via Windows service
        $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
        if ($service) {
            if ($service.Status -ne 'Running') {
                try {
                    Start-Service -Name $serviceName
                    Write-Status "$displayName started" -Type Success
                }
                catch {
                    Write-Status "Failed to start $displayName service: $($_.Exception.Message)" -Type Warning
                }
            }
            else {
                Write-Status "$displayName already running" -Type Success
            }
        }
        else {
            Write-Status "$displayName service not found (may be managed by Laragon)" -Type Info
        }
    }
}

function Test-DatabaseConnection {
    $mysqlPath = Join-Path $LaragonPath 'bin\mysql\mysql-*\bin\mysql.exe'
    $mysqlExe = Get-ChildItem -Path $mysqlPath -ErrorAction SilentlyContinue | Select-Object -First 1

    if (-not $mysqlExe) {
        return $false
    }

    try {
        $result = & $mysqlExe.FullName -u root -e "SELECT 1;" 2>$null
        return $LASTEXITCODE -eq 0
    }
    catch {
        return $false
    }
}

function Test-WebServer {
    $urls = @('http://localhost', 'http://localhost:8080')

    foreach ($url in $urls) {
        try {
            $response = Invoke-WebRequest -Uri $url -TimeoutSec 5 -UseBasicParsing
            if ($response.StatusCode -eq 200) {
                return @{
                    Available = $true
                    Url = $url
                }
            }
        }
        catch {
            continue
        }
    }

    return @{
        Available = $false
        Url = $null
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n🚀 Starting Laragon Services for ICTServe" -ForegroundColor Cyan
    Write-Host "=" * 40 -ForegroundColor Cyan
    Write-Host ""

    # Verify Laragon installation
    if (-not (Test-Path $LaragonPath)) {
        throw "Laragon not found at $LaragonPath"
    }

    Write-Status "Laragon installation verified" -Type Success

    # Start Laragon application
    Start-LaragonService

    # Start services
    Start-LaragonServices

    # Wait for services if requested
    if ($WaitForServices) {
        Write-Status "Waiting for services to be ready..." -Type Info

        # Wait for web server
        $maxWait = 30
        $waited = 0
        $webReady = $false

        while (-not $webReady -and $waited -lt $maxWait) {
            if ((Test-PortOpen 80) -or (Test-PortOpen 8080)) {
                $webReady = $true
            }
            else {
                Start-Sleep -Seconds 1
                $waited++
            }
        }

        if ($webReady) {
            Write-Status "Web server is responding" -Type Success
        }
        else {
            Write-Status "Web server may not be responding" -Type Warning
        }

        # Wait for MySQL
        $waited = 0
        $dbReady = $false

        while (-not $dbReady -and $waited -lt $maxWait) {
            if (Test-PortOpen 3306) {
                $dbReady = $true
            }
            else {
                Start-Sleep -Seconds 1
                $waited++
            }
        }

        if ($dbReady) {
            Write-Status "MySQL is responding" -Type Success
        }
        else {
            Write-Status "MySQL may not be responding" -Type Warning
        }
    }

    # Service status check
    Write-Host "`n📊 Service Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    # Check Laragon process
    $laragonProcess = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    if ($laragonProcess) {
        Write-Status "Laragon: Running (PID: $($laragonProcess.Id))" -Type Success
    }
    else {
        Write-Status "Laragon: Not running" -Type Error
    }

    # Check web server
    $webStatus = Test-WebServer
    if ($webStatus.Available) {
        Write-Status "Web Server: Running at $($webStatus.Url)" -Type Success
    }
    else {
        Write-Status "Web Server: Not accessible" -Type Warning
    }

    # Check MySQL
    if (Test-PortOpen 3306) {
        Write-Status "MySQL: Port 3306 accessible" -Type Success

        if (Test-DatabaseConnection) {
            Write-Status "  Database: Connection successful" -Type Success
        }
        else {
            Write-Status "  Database: Connection failed" -Type Warning
        }
    }
    else {
        Write-Status "MySQL: Port 3306 not accessible" -Type Warning
    }

    # Check Redis
    if (Test-PortOpen 6379) {
        Write-Status "Redis: Port 6379 accessible" -Type Success
    }
    else {
        Write-Status "Redis: Port 6379 not accessible" -Type Warning
    }

    # Open browser if requested
    if ($OpenBrowser) {
        Write-Status "Opening browser..." -Type Info

        if ($webStatus.Available) {
            Start-Process $webStatus.Url
        }
        else {
            Start-Process "http://localhost"
        }

        # Also try the virtual host if it exists
        try {
            $hostsContent = Get-Content 'C:\Windows\System32\drivers\etc\hosts' -ErrorAction SilentlyContinue
            if ($hostsContent -match 'ictserve\.local') {
                Start-Sleep -Seconds 2
                Start-Process "http://ictserve.local"
            }
        }
        catch {
            # Ignore hosts file access errors
        }
    }

    Write-Host "`n✅ Laragon services started successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "🌐 Application URLs:" -ForegroundColor Cyan
    if ($webStatus.Available) {
        Write-Host "  - Main: $($webStatus.Url)" -ForegroundColor White
    }
    else {
        Write-Host "  - Main: http://localhost (may not be accessible yet)" -ForegroundColor Yellow
    }
    Write-Host "  - Virtual Host: http://ictserve.local (if configured)" -ForegroundColor White
    Write-Host "  - Admin Panel: http://localhost/admin" -ForegroundColor White
    Write-Host "  - phpMyAdmin: http://localhost/phpmyadmin" -ForegroundColor White
    Write-Host ""
    Write-Host "🔧 Management:" -ForegroundColor Cyan
    Write-Host "  - Laragon is running in system tray" -ForegroundColor White
    Write-Host "  - Right-click Laragon icon to manage services" -ForegroundColor White
    Write-Host ""
    Write-Host "📝 Development Commands:" -ForegroundColor Cyan
    Write-Host "  - php artisan serve (Laravel dev server)" -ForegroundColor White
    Write-Host "  - npm run dev (Frontend development)" -ForegroundColor White
    Write-Host "  - php artisan reverb:start (WebSocket server)" -ForegroundColor White
    Write-Host ""
}
catch {
    Write-Host "`n❌ Failed to start Laragon services: $($_.Exception.Message)" -ForegroundColor Red

    Write-Host "`n🔧 Troubleshooting:" -ForegroundColor Yellow
    Write-Host "  1. Check if ports 80, 3306, 6379 are available" -ForegroundColor White
    Write-Host "  2. Run Laragon as Administrator" -ForegroundColor White
    Write-Host "  3. Check Windows Firewall settings" -ForegroundColor White
    Write-Host "  4. Verify Laragon installation is complete" -ForegroundColor White
    Write-Host "  5. Try starting services manually from Laragon UI" -ForegroundColor White

    exit 1
}

#endregion
