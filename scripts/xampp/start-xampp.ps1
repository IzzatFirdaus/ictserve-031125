#Requires -Version 5.1
<#
.SYNOPSIS
    Start XAMPP services for ICTServe development

.DESCRIPTION
    Starts Apache and MySQL services for XAMPP development environment.
    Includes health checks and service verification.

.PARAMETER XamppPath
    Path to XAMPP installation (default: C:\xampp)

.PARAMETER WaitForServices
    Wait for services to be fully ready before completing

.PARAMETER OpenBrowser
    Open browser to application URL after starting services

.EXAMPLE
    .\scripts\xampp\start-xampp.ps1 -OpenBrowser
    Start services and open browser

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [string]$XamppPath = 'C:\xampp',
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
        Info = '[INFO]'
        Success = '[OK]'
        Warning = '[WARN]'
        Error = '[ERROR]'
    }

    Write-Host "$($icons[$Type]) $Message" -ForegroundColor $colors[$Type]
}

function Test-ServiceRunning {
    param([string]$ServiceName)

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    return $service -and $service.Status -eq 'Running'
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

function Start-XamppService {
    param([string]$ServiceName, [string]$DisplayName)

    Write-Status "Starting $DisplayName..." -Type Info

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if (-not $service) {
        Write-Status "$DisplayName service not found. Trying XAMPP batch files..." -Type Warning

        # Try XAMPP batch files
        $batchFile = Join-Path $XamppPath "$ServiceName`_start.bat"
        if (Test-Path $batchFile) {
            Start-Process -FilePath $batchFile -Wait -WindowStyle Hidden
            Start-Sleep -Seconds 3
        }
        else {
            throw "$DisplayName service not available and batch file not found"
        }
    }
    else {
        if ($service.Status -ne 'Running') {
            Start-Service -Name $ServiceName
            Start-Sleep -Seconds 2
        }
    }

    # Verify service is running
    if (Test-ServiceRunning $ServiceName) {
        Write-Status "$DisplayName started successfully" -Type Success
    }
    else {
        Write-Status "$DisplayName may not be running properly" -Type Warning
    }
}

function Test-DatabaseConnection {
    $mysqlPath = Join-Path $XamppPath 'mysql\bin\mysql.exe'
    if (-not (Test-Path $mysqlPath)) {
        return $false
    }

    try {
        $result = & $mysqlPath -u root -e "SELECT 1;" 2>$null
        return $LASTEXITCODE -eq 0
    }
    catch {
        return $false
    }
}

function Test-WebServer {
    try {
        $response = Invoke-WebRequest -Uri 'http://localhost' -TimeoutSec 5 -UseBasicParsing
        return $response.StatusCode -eq 200
    }
    catch {
        return $false
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n[START] Starting XAMPP Services for ICTServe" -ForegroundColor Cyan
    Write-Host "=" * 40 -ForegroundColor Cyan
    Write-Host ""

    # Verify XAMPP installation
    if (-not (Test-Path $XamppPath)) {
        throw "XAMPP not found at $XamppPath"
    }

    $xamppControl = Join-Path $XamppPath 'xampp-control.exe'
    if (-not (Test-Path $xamppControl)) {
        throw "XAMPP control panel not found"
    }

    Write-Status "XAMPP installation verified" -Type Success

    # Start Apache
    Start-XamppService 'Apache2.4' 'Apache Web Server'

    # Start MySQL
    Start-XamppService 'mysql' 'MySQL Database'

    # Wait for services if requested
    if ($WaitForServices) {
        Write-Status "Waiting for services to be ready..." -Type Info

        # Wait for Apache (port 80)
        $maxWait = 30
        $waited = 0
        while (-not (Test-PortOpen 80) -and $waited -lt $maxWait) {
            Start-Sleep -Seconds 1
            $waited++
        }

        if (Test-PortOpen 80) {
            Write-Status "Apache is responding on port 80" -Type Success
        }
        else {
            Write-Status "Apache may not be responding on port 80" -Type Warning
        }

        # Wait for MySQL (port 3306)
        $waited = 0
        while (-not (Test-PortOpen 3306) -and $waited -lt $maxWait) {
            Start-Sleep -Seconds 1
            $waited++
        }

        if (Test-PortOpen 3306) {
            Write-Status "MySQL is responding on port 3306" -Type Success
        }
        else {
            Write-Status "MySQL may not be responding on port 3306" -Type Warning
        }
    }

    # Service status check
    Write-Host "`n[STATUS] Service Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    # Apache status
    if (Test-ServiceRunning 'Apache2.4') {
        Write-Status "Apache: Running" -Type Success
        if (Test-PortOpen 80) {
            Write-Status "  Port 80: Accessible" -Type Success
        }
        else {
            Write-Status "  Port 80: Not accessible" -Type Warning
        }
    }
    else {
        Write-Status "Apache: Not running" -Type Error
    }

    # MySQL status
    if (Test-ServiceRunning 'mysql') {
        Write-Status "MySQL: Running" -Type Success
        if (Test-PortOpen 3306) {
            Write-Status "  Port 3306: Accessible" -Type Success
        }
        else {
            Write-Status "  Port 3306: Not accessible" -Type Warning
        }

        if (Test-DatabaseConnection) {
            Write-Status "  Database: Connected" -Type Success
        }
        else {
            Write-Status "  Database: Connection failed" -Type Warning
        }
    }
    else {
        Write-Status "MySQL: Not running" -Type Error
    }

    # Test web server response
    Write-Host "`n[WEB] Web Server Test:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    if (Test-WebServer) {
        Write-Status "Web server responding at http://localhost" -Type Success
    }
    else {
        Write-Status "Web server not responding at http://localhost" -Type Warning
    }

    # Open XAMPP Control Panel
    Write-Status "Opening XAMPP Control Panel..." -Type Info
    Start-Process -FilePath $xamppControl

    # Open browser if requested
    if ($OpenBrowser) {
        Write-Status "Opening browser..." -Type Info
        Start-Process "http://localhost"

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

    Write-Host "`n[SUCCESS] XAMPP services started successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "[URLS] Application URLs:" -ForegroundColor Cyan
    Write-Host "  - Main: http://localhost" -ForegroundColor White
    Write-Host "  - Virtual Host: http://ictserve.local (if configured)" -ForegroundColor White
    Write-Host "  - Admin Panel: http://localhost/admin" -ForegroundColor White
    Write-Host "  - phpMyAdmin: http://localhost/phpmyadmin" -ForegroundColor White
    Write-Host ""
    Write-Host "[MGMT] Management:" -ForegroundColor Cyan
    Write-Host "  - XAMPP Control Panel is now open" -ForegroundColor White
    Write-Host "  - Use the control panel to stop/restart services" -ForegroundColor White
    Write-Host ""
    Write-Host "[DEV] Development Commands:" -ForegroundColor Cyan
    Write-Host "  - php artisan serve (Laravel dev server)" -ForegroundColor White
    Write-Host "  - npm run dev (Frontend development)" -ForegroundColor White
    Write-Host "  - php artisan reverb:start (WebSocket server)" -ForegroundColor White
    Write-Host ""
}
catch {
    Write-Host "`n[ERROR] Failed to start XAMPP services: $($_.Exception.Message)" -ForegroundColor Red

    Write-Host "`n[HELP] Troubleshooting:" -ForegroundColor Yellow
    Write-Host "  1. Check if ports 80 and 3306 are available" -ForegroundColor White
    Write-Host "  2. Run XAMPP Control Panel as Administrator" -ForegroundColor White
    Write-Host "  3. Check Windows Firewall settings" -ForegroundColor White
    Write-Host "  4. Verify XAMPP installation is complete" -ForegroundColor White

    exit 1
}

#endregion
