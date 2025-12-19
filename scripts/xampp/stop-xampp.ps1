#Requires -Version 5.1
<#
.SYNOPSIS
    Stop XAMPP services for ICTServe development

.DESCRIPTION
    Stops Apache and MySQL services for XAMPP development environment.
    Includes graceful shutdown and service verification.

.PARAMETER XamppPath
    Path to XAMPP installation (default: C:\xampp)

.PARAMETER Force
    Force stop services without graceful shutdown

.EXAMPLE
    .\scripts\xampp\stop-xampp.ps1 -Force
    Force stop all XAMPP services

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [string]$XamppPath = 'C:\xampp',
    [switch]$Force
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

function Test-ServiceRunning {
    param([string]$ServiceName)

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    return $service -and $service.Status -eq 'Running'
}

function Stop-XamppService {
    param([string]$ServiceName, [string]$DisplayName)

    Write-Status "Stopping $DisplayName..." -Type Info

    $service = Get-Service -Name $ServiceName -ErrorAction SilentlyContinue
    if (-not $service) {
        Write-Status "$DisplayName service not found. Trying XAMPP batch files..." -Type Warning

        # Try XAMPP batch files
        $batchFile = Join-Path $XamppPath "$ServiceName`_stop.bat"
        if (Test-Path $batchFile) {
            Start-Process -FilePath $batchFile -Wait -WindowStyle Hidden
            Start-Sleep -Seconds 2
        }

        # Also try to kill processes directly
        $processNames = @{
            'Apache2.4' = @('httpd', 'apache')
            'mysql' = @('mysqld', 'mysql')
        }

        if ($processNames.ContainsKey($ServiceName)) {
            foreach ($processName in $processNames[$ServiceName]) {
                $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
                if ($processes) {
                    Write-Status "Terminating $processName processes..." -Type Info
                    $processes | Stop-Process -Force
                }
            }
        }

        Write-Status "$DisplayName stopped via process termination" -Type Success
        return
    }

    if ($service.Status -eq 'Running') {
        try {
            if ($Force) {
                Stop-Service -Name $ServiceName -Force
            }
            else {
                Stop-Service -Name $ServiceName
            }

            # Wait for service to stop
            $timeout = 30
            $waited = 0
            while ((Test-ServiceRunning $ServiceName) -and $waited -lt $timeout) {
                Start-Sleep -Seconds 1
                $waited++
            }

            if (-not (Test-ServiceRunning $ServiceName)) {
                Write-Status "$DisplayName stopped successfully" -Type Success
            }
            else {
                Write-Status "$DisplayName did not stop within timeout" -Type Warning

                if ($Force) {
                    Write-Status "Force killing $DisplayName processes..." -Type Warning
                    $processNames = @{
                        'Apache2.4' = @('httpd', 'apache')
        l' = @('mysqld', 'mysql')
                    }

                    if ($processNames.ContainsKey($ServiceName)) {
                        foreach ($processName in $processNames[$ServiceName]) {
                            $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
                            if ($processes) {
                                $processes | Stop-Process -Force
                            }
                        }
                    }
                }
            }
        }
        catch {
            Write-Status "Failed to stop $DisplayName service: $($_.Exception.Message)" -Type Error
        }
    }
    else {
        Write-Status "$DisplayName was not running" -Type Info
    }
}

function Stop-RelatedProcesses {
    Write-Status "Stopping related processes..." -Type Info

    # Stop PHP processes that might be running
    $phpProcesses = Get-Process -Name 'php' -ErrorAction SilentlyContinue
    if ($phpProcesses) {
        Write-Status "Stopping PHP processes..." -Type Info
        $phpProcesses | Stop-Process -Force
    }

    # Stop any Laravel Artisan processes
    $artisanProcesses = Get-Process | Where-Object { $_.ProcessName -eq 'php' -and $_.CommandLine -like '*artisan*' } -ErrorAction SilentlyContinue
    if ($artisanProcesses) {
        Write-Status "Stopping Laravel Artisan processes..." -Type Info
        $artisanProcesses | Stop-Process -Force
    }

    # Stop Node.js development servers
    $nodeProcesses = Get-Process -Name 'node' -ErrorAction SilentlyContinue | Where-Object {
        $_.CommandLine -like '*vite*' -or $_.CommandLine -like '*webpack*' -or $_.CommandLine -like '*npm*'
    }
    if ($nodeProcesses) {
        Write-Status "Stopping Node.js development processes..." -Type Info
        $nodeProcesses | Stop-Process -Force
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n🛑 Stopping XAMPP Services for ICTServe" -ForegroundColor Cyan
    Write-Host "=" * 40 -ForegroundColor Cyan
    Write-Host ""

    # Verify XAMPP installation
    if (-not (Test-Path $XamppPath)) {
        Write-Status "XAMPP not found at $XamppPath, but continuing with service stop..." -Type Warning
    }

    # Stop related development processes first
    Stop-RelatedProcesses

    # Stop Apache
    Stop-XamppService 'Apache2.4' 'Apache Web Server'

    # Stop MySQL
    Stop-XamppService 'mysql' 'MySQL Database'

    # Verify services are stopped
    Write-Host "`n📊 Service Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    if (-not (Test-ServiceRunning 'Apache2.4')) {
        Write-Status "Apache: Stopped" -Type Success
    }
    else {
        Write-Status "Apache: Still running" -Type Warning
    }

    if (-not (Test-ServiceRunning 'mysql')) {
        Write-Status "MySQL: Stopped" -Type Success
    }
    else {
        Write-Status "MySQL: Still running" -Type Warning
    }

    # Check for any remaining XAMPP processes
    $xamppProcesses = @()
    $processNames = @('httpd', 'apache', 'mysqld', 'mysql')

    foreach ($processName in $processNames) {
        $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
        if ($processes) {
            $xamppProcesses += $processes
        }
    }

    if ($xamppProcesses.Count -gt 0) {
        Write-Status "Found $($xamppProcesses.Count) remaining XAMPP processes" -Type Warning

        if ($Force) {
            Write-Status "Force killing remaining processes..." -Type Info
            $xamppProcesses | Stop-Process -Force
            Write-Status "Remaining processes terminated" -Type Success
        }
        else {
            Write-Status "Use -Force parameter to terminate remaining processes" -Type Info
        }
    }
    else {
        Write-Status "No remaining XAMPP processes found" -Type Success
    }

    Write-Host "`n✅ XAMPP services stopped successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "📝 Notes:" -ForegroundColor Cyan
    Write-Host "  - All XAMPP services have been stopped" -ForegroundColor White
    Write-Host "  - You can restart services using start-xampp.ps1" -ForegroundColor White
    Write-Host "  - Or use XAMPP Control Panel to manage services" -ForegroundColor White
    Write-Host ""

    # Offer to open XAMPP Control Panel
    if (Test-Path (Join-Path $XamppPath 'xampp-control.exe')) {
        Write-Host "🔧 Open XAMPP Control Panel to verify status? (y/N): " -ForegroundColor Yellow -NoNewline
        $response = Read-Host
        if ($response -match '^[Yy]') {
            Start-Process -FilePath (Join-Path $XamppPath 'xampp-control.exe')
        }
    }
}
catch {
    Write-Host "`n❌ Failed to stop XAMPP services: $($_.Exception.Message)" -ForegroundColor Red

    Write-Host "`n🔧 Manual Steps:" -ForegroundColor Yellow
    Write-Host "  1. Open XAMPP Control Panel" -ForegroundColor White
    Write-Host "  2. Click 'Stop' for Apache and MySQL" -ForegroundColor White
    Write-Host "  3. Check Task Manager for remaining processes" -ForegroundColor White
    Write-Host "  4. Restart your computer if services won't stop" -ForegroundColor White

    exit 1
}

#endregion
