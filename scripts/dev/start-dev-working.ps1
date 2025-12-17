# ICTServe v3.6.0 Development Environment - Working Version
# Laravel 12.42.0 + Filament 4.1.10 + Livewire 3.7.1 + Tailwind 4.1.17

param(
    [switch]$SkipChecks,
    [switch]$NoBrowser,
    [switch]$Minimal
)

Write-Host "ICTServe v3.6.0 Development Environment" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "Laravel 12.42.0 | PHP 8.2.12 | Filament 4.1.10" -ForegroundColor Gray
Write-Host ""

$projectRoot = Get-Location
$startTime = Get-Date

function Start-DevService {
    param(
        [string]$Title,
        [string]$Command,
        [string]$Color = "Green"
    )

    $timestamp = Get-Date -Format "HH:mm:ss"
    Write-Host "[$timestamp] Starting $Title..." -ForegroundColor $Color

    $commandScript = @"
Set-Location '$projectRoot'
Write-Host '[$timestamp] $Title' -ForegroundColor $Color
Write-Host '$("=" * $Title.Length)' -ForegroundColor $Color
try {
    $Command
} catch {
    Write-Host 'ERROR: Service failed to start' -ForegroundColor Red
    Read-Host 'Press Enter to continue'
}
"@

    Start-Process powershell -ArgumentList @("-NoExit", "-Command", $commandScript)
    Start-Sleep -Seconds 2
}

function Test-ServicePort {
    param([int]$Port, [string]$ServiceName)

    $timestamp = Get-Date -Format "HH:mm:ss"
    for ($i = 0; $i -lt 10; $i++) {
        try {
            $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue
            if ($result.TcpTestSucceeded) {
                Write-Host "[$timestamp] [OK] $ServiceName ready on 127.0.0.1:$Port" -ForegroundColor Green
                return $true
            }
            Write-Host "[$timestamp] [WAIT] $ServiceName starting... (attempt $($i+1)/10)" -ForegroundColor Yellow
            Start-Sleep -Seconds 2
        }
        catch {
            Start-Sleep -Seconds 2
        }
    }
    Write-Host "[$timestamp] [WARN] $ServiceName not ready after 10 attempts" -ForegroundColor Yellow
    return $false
}

Write-Host "Starting development services..." -ForegroundColor Cyan
Write-Host ""

# Start Laravel Server
Write-Host "[1/2] Laravel Application Server" -ForegroundColor Yellow
Start-DevService -Title "Laravel Server" -Command "php artisan serve --host=127.0.0.1 --port=8000" -Color "Blue"
Test-ServicePort -Port 8000 -ServiceName "Laravel Server"

# Start Vite Dev Server
Write-Host "[2/2] Vite Development Server" -ForegroundColor Yellow
Start-DevService -Title "Vite Dev Server" -Command "npm run dev" -Color "Green"
Test-ServicePort -Port 5173 -ServiceName "Vite Dev Server"

# Summary
$endTime = Get-Date
$duration = ($endTime - $startTime).TotalSeconds
$timestamp = Get-Date -Format "HH:mm:ss"

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "ICTServe v3.6.0 Development Environment" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Startup Time: $([math]::Round($duration, 1))s" -ForegroundColor Gray
Write-Host ""

Write-Host "Active Services:" -ForegroundColor White
Write-Host "  [LARAVEL] Laravel Server - http://127.0.0.1:8000" -ForegroundColor Blue
Write-Host "  [VITE] Vite Dev Server - http://127.0.0.1:5173" -ForegroundColor Green

Write-Host ""
Write-Host "Quick Access URLs:" -ForegroundColor White
Write-Host "  Application:  http://127.0.0.1:8000" -ForegroundColor Gray
Write-Host "  Admin Panel:  http://127.0.0.1:8000/admin" -ForegroundColor Gray

if (-not $NoBrowser) {
    Write-Host ""
    Write-Host "Opening browser..." -ForegroundColor Yellow
    try {
        Start-Process "http://127.0.0.1:8000"
        Write-Host "Browser opened successfully" -ForegroundColor Green
    }
    catch {
        Write-Host "Could not open browser automatically" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "[$timestamp] All services ready! Press any key to stop..." -ForegroundColor Yellow

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Shutdown
Write-Host ""
Write-Host "[$timestamp] Shutting down services..." -ForegroundColor Red

try {
    Get-Process | Where-Object {
        $_.MainWindowTitle -like "*Laravel*" -or
        $_.MainWindowTitle -like "*Vite*" -or
        $_.ProcessName -like "*php*" -or
        $_.ProcessName -like "*node*"
    } | Stop-Process -Force -ErrorAction SilentlyContinue
}
catch {
    # Silent cleanup
}

Write-Host "Development environment stopped." -ForegroundColor Green
