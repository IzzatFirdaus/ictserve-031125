# ICTServe v3.6.0 Development Environment Startup Script - Minimal Version
# Laravel 12.43.1 + Filament 4.3.1 + Livewire 3.7.3 + Tailwind 4.1.18

param(
    [switch]$SkipChecks,
    [switch]$NoMCP,
    [switch]$NoBrowser,
    [switch]$Minimal,
    [string]$Profile = "minimal"
)

Write-Host "ICTServe v3.6.0 Development Environment" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "Laravel 12.43.1 | PHP 8.2.12 | Filament 4.3.1" -ForegroundColor Gray
Write-Host "Profile: $Profile" -ForegroundColor Gray
Write-Host ""

# Get the current directory (project root)
$projectRoot = Get-Location
$startTime = Get-Date

# Enhanced service management functions
function Start-Service {
    param(
        [string]$Title,
        [string]$Command,
        [string]$Color = "Green",
        [string]$Description = "",
        [switch]$Critical = $false,
        [int]$Priority = 1
    )

    $timestamp = Get-Date -Format "HH:mm:ss"
    Write-Host "[$timestamp] Starting $Title..." -ForegroundColor $Color
    if ($Description) {
        Write-Host "  -> $Description" -ForegroundColor Gray
    }

    # Build command script with proper escaping
    $separator = "=" * $Title.Length

    $commandLines = @(
        "Set-Location '$projectRoot'"
        "Write-Host '[$timestamp] $Title' -ForegroundColor $Color"
        "Write-Host '$separator' -ForegroundColor $Color"
    )

    if ($Description) {
        $commandLines += "Write-Host '$Description' -ForegroundColor Gray"
        $commandLines += "Write-Host ''"
    }

    $commandLines += @(
        "try {"
        "    $Command"
        "} catch {"
        "    Write-Host 'ERROR in $Title - Service failed to start' -ForegroundColor Red"
    )

    if ($Critical) {
        $commandLines += @(
            "    Write-Host 'CRITICAL SERVICE FAILED - Press any key to exit...' -ForegroundColor Red"
            "    Read-Host"
            "    exit 1"
        )
    }

    $commandLines += "}"

    $commandScript = $commandLines -join "; "

    # Start the service in a new PowerShell window
    Start-Process powershell -ArgumentList @("-NoExit", "-Command", $commandScript)

    # Staggered startup based on priority
    Start-Sleep -Milliseconds (500 * $Priority)
}

# Enhanced port checking
function Check-Port {
    param(
        [int]$Port,
        [int]$Attempts = 10,
        [int]$DelaySeconds = 1,
        [string]$ServiceName = $null,
        [switch]$Critical = $false
    )

    $serviceLabel = if ($ServiceName) { $ServiceName } else { "Port " + $Port }
    $timestamp = Get-Date -Format "HH:mm:ss"

    for ($i = 0; $i -lt $Attempts; $i++) {
        try {
            $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue
            if ($result.TcpTestSucceeded) {
                Write-Host "[$timestamp] [OK] $serviceLabel ready on 127.0.0.1:$Port" -ForegroundColor Green
                return $true
            }
            else {
                Write-Host "[$timestamp] [WAIT] $serviceLabel starting... (attempt $($i+1)/$Attempts)" -ForegroundColor Yellow
                Start-Sleep -Seconds $DelaySeconds
            }
        }
        catch {
            Write-Host "[$timestamp] [ERROR] Failed to check $serviceLabel : $($_.Exception.Message)" -ForegroundColor Red
            Start-Sleep -Seconds $DelaySeconds
        }
    }

    if ($Critical) {
        $status = "[ERROR]"
        $statusColor = "Red"
    } else {
        $status = "[WARN]"
        $statusColor = "Yellow"
    }
    Write-Host "[$timestamp] $status $serviceLabel not ready after $Attempts attempts" -ForegroundColor $statusColor
    return $false
}

Write-Host "Starting minimal development environment..." -ForegroundColor Cyan
Write-Host ""

# 1. Laravel Application Server
Write-Host "[1/2] Laravel Application Server" -ForegroundColor Yellow
Start-Service -Title 'Laravel Server (127.0.0.1:8000)' -Command "php artisan serve --host=127.0.0.1 --port=8000" -Color "Blue" -Description "ICTServe v3.6.0 - True Hybrid Architecture" -Critical -Priority 1
Check-Port -Port 8000 -Attempts 15 -DelaySeconds 1 -ServiceName 'Laravel Server' -Critical
Start-Sleep -Seconds 1

# 2. Vite Development Server
Write-Host "[2/2] Vite Development Server" -ForegroundColor Yellow
Start-Service -Title 'Vite Dev Server (127.0.0.1:5173)' -Command "npm run dev" -Color "Green" -Description "Tailwind 4.1.18, Livewire 3.7.3, Hot Module Replacement" -Priority 2
Check-Port -Port 5173 -Attempts 15 -DelaySeconds 1 -ServiceName 'Vite Dev Server'
Start-Sleep -Seconds 1

# Startup Summary
$endTime = Get-Date
$duration = ($endTime - $startTime).TotalSeconds
$timestamp = Get-Date -Format "HH:mm:ss"

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "ICTServe v3.6.0 Development Environment" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Profile: $Profile | Startup Time: $([math]::Round($duration, 1))s" -ForegroundColor Gray
Write-Host ""

# Service status summary
Write-Host "Active Services:" -ForegroundColor White
Write-Host '  [LARAVEL] Laravel Server      - ICTServe Application (http://127.0.0.1:8000)' -ForegroundColor Blue
Write-Host '  [VITE] Vite Dev Server        - Frontend Assets + HMR (127.0.0.1:5173)' -ForegroundColor Green

Write-Host ""
Write-Host "Quick Access URLs:" -ForegroundColor White
Write-Host '  • Application:     http://127.0.0.1:8000' -ForegroundColor Gray
Write-Host '  • Admin Panel:     http://127.0.0.1:8000/admin' -ForegroundColor Gray

Write-Host ""
Write-Host "[$timestamp] All services ready! Press any key to stop all services..." -ForegroundColor Yellow

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Enhanced service shutdown
Write-Host ""
Write-Host "[$timestamp] Shutting down ICTServe development environment..." -ForegroundColor Red

# Graceful shutdown sequence
$shutdownServices = @(
    @{ Name = "Vite"; Pattern = "*vite*|*npm*dev*" },
    @{ Name = "Laravel Server"; Pattern = "*artisan*serve*" }
)

foreach ($service in $shutdownServices) {
    try {
        $processes = Get-Process | Where-Object {
            $_.MainWindowTitle -match $service.Pattern -or
            $_.ProcessName -match $service.Pattern
        }

        if ($processes) {
            Write-Host "  └─ Stopping $($service.Name)..." -ForegroundColor Yellow
            $processes | Stop-Process -Force -ErrorAction SilentlyContinue
        }
    }
    catch {
        # Silent cleanup
    }
}

$finalTime = Get-Date -Format "HH:mm:ss"
Write-Host "[$finalTime] ICTServe development environment stopped." -ForegroundColor Green
Write-Host ""
