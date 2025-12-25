#Requires -Version 5.1
<#
.SYNOPSIS
    Stop Laragon services for ICTServe development

.DESCRIPTION
    Stops Laragon services and verifies they are stopped properly.
    Includes graceful shutdown and service verification.

.PARAMETER LaragonPath
    Path to Laragon installation (default: C:\laragon)

.PARAMETER Force
    Force stop services without graceful shutdown

.PARAMETER KeepLaragon
    Keep Laragon application running, only stop services

.EXAMPLE
    .\scripts\laragon\stop-laragon.ps1 -Force
    Force stop all Laragon services

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param(
    [string]$LaragonPath = 'C:\laragon',
    [switch]$Force,
    [switch]$KeepLaragon
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

function Stop-LaragonServices {
    Write-Status "Stopping Laragon services..." -Type Info

    # Use Laragon CLI if available
    $laragonCli = Join-Path $LaragonPath 'bin\laragon.exe'
    if (Test-Path $laragonCli) {
        try {
            & $laragonCli stop all
            Write-Status "Services stopped via Laragon CLI" -Type Success
            return
        }
        catch {
            Write-Status "Failed to stop services via CLI, trying manual approach" -Type Warning
        }
    }

    # Manual service stop approach
    $services = @{
        'nginx' = 'Nginx Web Server'
        'mysql' = 'MySQL Database'
        'redis' = 'Redis Cache'
        'Apache2.4' = 'Apache Web Server'
    }

    foreach ($serviceName in $services.Keys) {
        $displayName = $services[$serviceName]

        $service = Get-Service -Name $serviceName -ErrorAction SilentlyContinue
        if ($service -and $service.Status -eq 'Running') {
            try {
                Write-Status "Stopping $displayName..." -Type Info

                if ($Force) {
                    Stop-Service -Name $serviceName -Force
                }
                else {
                    Stop-Service -Name $serviceName
                }

                # Wait for service to stop
                $timeout = 30
                $waited = 0
                while ((Test-ServiceRunning $serviceName) -and $waited -lt $timeout) {
                    Start-Sleep -Seconds 1
                    $waited++
                }

                if (-not (Test-ServiceRunning $serviceName)) {
                    Write-Status "$displayName stopped successfully" -Type Success
                }
                else {
                    Write-Status "$displayName did not stop within timeout" -Type Warning
                }
            }
            catch {
                Write-Status "Failed to stop $displayName service: $($_.Exception.Message)" -Type Error
            }
        }
        elseif ($service) {
            Write-Status "$displayName was not running" -Type Info
        }
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

    # Stop Nginx processes
    $nginxProcesses = Get-Process -Name 'nginx' -ErrorAction SilentlyContinue
    if ($nginxProcesses) {
        Write-Status "Stopping Nginx processes..." -Type Info
        $nginxProcesses | Stop-Process -Force
    }

    # Stop MySQL processes (if not service)
    $mysqlProcesses = Get-Process -Name 'mysqld' -ErrorAction SilentlyContinue
    if ($mysqlProcesses) {
        Write-Status "Stopping MySQL processes..." -Type Info
        if ($Force) {
            $mysqlProcesses | Stop-Process -Force
        }
        else {
            # Try graceful shutdown first
            foreach ($process in $mysqlProcesses) {
                try {
                    $process.CloseMainWindow()
                    Start-Sleep -Seconds 5
                    if (-not $process.HasExited) {
                        $process.Kill()
                    }
                }
                catch {
                    $process | Stop-Process -Force
                }
            }
        }
    }

    # Stop Redis processes
    $redisProcesses = Get-Process -Name 'redis-server' -ErrorAction SilentlyContinue
    if ($redisProcesses) {
        Write-Status "Stopping Redis processes..." -Type Info
        $redisProcesses | Stop-Process -Force
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

function Stop-LaragonApplication {
    if ($KeepLaragon) {
        Write-Status "Keeping Laragon application running as requested" -Type Info
        return
    }

    $laragonProcess = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    if ($laragonProcess) {
        Write-Status "Stopping Laragon application..." -Type Info

        if ($Force) {
            $laragonProcess | Stop-Process -Force
        }
        else {
            # Try graceful shutdown
            try {
                $laragonProcess.CloseMainWindow()
                Start-Sleep -Seconds 5

                if (-not $laragonProcess.HasExited) {
                    $laragonProcess.Kill()
                }
            }
            catch {
                $laragonProcess | Stop-Process -Force
            }
        }

        Write-Status "Laragon application stopped" -Type Success
    }
    else {
        Write-Status "Laragon application was not running" -Type Info
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n🛑 Stopping Laragon Services for ICTServe" -ForegroundColor Cyan
    Write-Host "=" * 40 -ForegroundColor Cyan
    Write-Host ""

    # Verify Laragon installation
    if (-not (Test-Path $LaragonPath)) {
        Write-Status "Laragon not found at $LaragonPath, but continuing with service stop..." -Type Warning
    }

    # Stop related development processes first
    Stop-RelatedProcesses

    # Stop Laragon services
    Stop-LaragonServices

    # Stop Laragon application
    Stop-LaragonApplication

    # Verify services are stopped
    Write-Host "`n📊 Service Status:" -ForegroundColor Cyan
    Write-Host "-" * 20 -ForegroundColor Cyan

    $services = @('nginx', 'mysql', 'redis', 'Apache2.4')
    $allStopped = $true

    foreach ($serviceName in $services) {
        if (-not (Test-ServiceRunning $serviceName)) {
            Write-Status "$serviceName: Stopped" -Type Success
        }
        else {
            Write-Status "$serviceName: Still running" -Type Warning
            $allStopped = $false
        }
    }

    # Check Laragon application
    $laragonProcess = Get-Process -Name 'laragon' -ErrorAction SilentlyContinue
    if (-not $laragonProcess) {
        Write-Status "Laragon Application: Stopped" -Type Success
    }
    elseif ($KeepLaragon) {
        Write-Status "Laragon Application: Running (kept as requested)" -Type Info
    }
    else {
        Write-Status "Laragon Application: Still running" -Type Warning
        $allStopped = $false
    }

    # Check for any remaining processes
    $remainingProcesses = @()
    $processNames = @('nginx', 'mysqld', 'redis-server', 'php')

    foreach ($processName in $processNames) {
        $processes = Get-Process -Name $processName -ErrorAction SilentlyContinue
        if ($processes) {
            $remainingProcesses += $processes
        }
    }

    if ($remainingProcesses.Count -gt 0) {
        Write-Status "Found $($remainingProcesses.Count) remaining processes" -Type Warning

        if ($Force) {
            Write-Status "Force killing remaining processes..." -Type Info
            $remainingProcesses | Stop-Process -Force
            Write-Status "Remaining processes terminated" -Type Success
        }
        else {
            Write-Status "Use -Force parameter to terminate remaining processes" -Type Info
            Write-Host "Remaining processes:" -ForegroundColor Yellow
            $remainingProcesses | ForEach-Object {
                Write-Host "  - $($_.ProcessName) (PID: $($_.Id))" -ForegroundColor White
            }
        }
    }
    else {
        Write-Status "No remaining processes found" -Type Success
    }

    if ($allStopped -and $remainingProcesses.Count -eq 0) {
        Write-Host "`n✅ All Laragon services stopped successfully!" -ForegroundColor Green
    }
    else {
        Write-Host "`n⚠️  Some services may still be running" -ForegroundColor Yellow
    }

    Write-Host ""
    Write-Host "📝 Notes:" -ForegroundColor Cyan
    Write-Host "  - All Laragon services have been stopped" -ForegroundColor White
    Write-Host "  - You can restart services using start-laragon.ps1" -ForegroundColor White
    Write-Host "  - Or use Laragon UI to manage services" -ForegroundColor White
    Write-Host ""

    # Offer to open Laragon if it's still running
    if ((Get-Process -Name 'laragon' -ErrorAction SilentlyContinue) -and -not $KeepLaragon) {
        Write-Host "🔧 Laragon is still running. Open Laragon to verify status? (y/N): " -ForegroundColor Yellow -NoNewline
        $response = Read-Host
        if ($response -match '^[Yy]') {
            $laragonExe = Join-Path $LaragonPath 'laragon.exe'
            if (Test-Path $laragonExe) {
                Start-Process -FilePath $laragonExe
            }
        }
    }
}
catch {
    Write-Host "`n❌ Failed to stop Laragon services: $($_.Exception.Message)" -ForegroundColor Red

    Write-Host "`n🔧 Manual Steps:" -ForegroundColor Yellow
    Write-Host "  1. Right-click Laragon icon in system tray" -ForegroundColor White
    Write-Host "  2. Click 'Stop All' to stop services" -ForegroundColor White
    Write-Host "  3. Check Task Manager for remaining processes" -ForegroundColor White
    Write-Host "  4. Use -Force parameter for forceful shutdown" -ForegroundColor White

    exit 1
}

#endregion
