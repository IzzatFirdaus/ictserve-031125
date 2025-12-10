# ICTServe Development Environment Startup Script
# This script launches all required services in separate PowerShell windows

Write-Host "Starting ICTServe Development Environment..." -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Get the current directory (project root)
$projectRoot = Get-Location

# Function to start a service in a new PowerShell window
function Start-Service {
    param(
        [string]$Title,
        [string]$Command,
        [string]$Color = "Green"
    )

    Write-Host "Starting $Title..." -ForegroundColor $Color

    # Create a new PowerShell window with the command
    Start-Process powershell -ArgumentList @(
        "-NoExit",
        "-Command",
        "Set-Location '$projectRoot'; Write-Host '$Title' -ForegroundColor $Color; Write-Host '==================' -ForegroundColor $Color; $Command"
    )

    Start-Sleep -Milliseconds 500
}

# 1. Start Redis Server (WSL)
Write-Host "`n[1/5] Starting Redis Server (WSL)..." -ForegroundColor Yellow
Start-Service -Title "Redis Server (WSL)" -Command "wsl.exe --user root systemctl start redis-server; wsl.exe redis-cli ping; Write-Host 'Redis is running!' -ForegroundColor Green; wsl.exe redis-cli monitor" -Color "Red"

Start-Sleep -Seconds 2

# 2. Start Laravel Server
Write-Host "`n[2/5] Starting Laravel Server..." -ForegroundColor Yellow
Start-Service -Title "Laravel Server (Port 8000)" -Command "php artisan serve" -Color "Blue"

Start-Sleep -Seconds 2

# 3. Start Laravel Reverb (WebSocket Server)
Write-Host "`n[3/5] Starting Laravel Reverb..." -ForegroundColor Yellow
Start-Service -Title "Laravel Reverb (WebSocket)" -Command "php artisan reverb:start" -Color "Magenta"

Start-Sleep -Seconds 2

# 4. Start Queue Worker
Write-Host "`n[4/5] Starting Queue Worker..." -ForegroundColor Yellow
Start-Service -Title "Laravel Queue Worker" -Command "php artisan queue:work --tries=3 --timeout=90" -Color "Cyan"

Start-Sleep -Seconds 2

# 5. Start Vite Dev Server
Write-Host "`n[5/5] Starting Vite Dev Server..." -ForegroundColor Yellow
Start-Service -Title "Vite Dev Server (HMR)" -Command "npm run dev" -Color "Green"

Start-Sleep -Seconds 2

# Summary
Write-Host "`n=============================================" -ForegroundColor Cyan
Write-Host "All services started successfully!" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "`nRunning Services:" -ForegroundColor White
Write-Host "  1. Redis Server (WSL)       - Monitoring mode" -ForegroundColor Red
Write-Host "  2. Laravel Server           - http://127.0.0.1:8000" -ForegroundColor Blue
Write-Host "  3. Laravel Reverb           - ws://127.0.0.1:6001" -ForegroundColor Magenta
Write-Host "  4. Queue Worker             - Processing jobs" -ForegroundColor Cyan
Write-Host "  5. Vite Dev Server          - Hot Module Replacement" -ForegroundColor Green
Write-Host "`nPress any key to stop all services..." -ForegroundColor Yellow

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Stop all services
Write-Host "`nStopping all services..." -ForegroundColor Red
Get-Process | Where-Object { $_.MainWindowTitle -match "Laravel|Vite|Redis|Queue" } | Stop-Process -Force
Write-Host "All services stopped." -ForegroundColor Green
