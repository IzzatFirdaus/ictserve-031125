# ICTServe Development Environment Stop Script
# This script stops all running development services

Write-Host "Stopping ICTServe Development Services..." -ForegroundColor Red
Write-Host "==========================================" -ForegroundColor Red

# Function to stop processes by port
function Stop-ProcessByPort {
    param([int]$Port)

    $connections = netstat -ano | Select-String ":$Port"
    if ($connections) {
        foreach ($connection in $connections) {
            $parts = $connection -split '\s+'
            $pid = $parts[-1]
            if ($pid -match '^\d+$') {
                try {
                    Stop-Process -Id $pid -Force -ErrorAction SilentlyContinue
                    Write-Host "Stopped process on port $Port (PID: $pid)" -ForegroundColor Yellow
                } catch {
                    Write-Host "Could not stop process on port $Port (PID: $pid)" -ForegroundColor Red
                }
            }
        }
    }
}

# Function to stop processes by name pattern
function Stop-ProcessByName {
    param([string]$Pattern)

    $processes = Get-Process | Where-Object { $_.ProcessName -match $Pattern }
    foreach ($process in $processes) {
        try {
            Stop-Process -Id $process.Id -Force -ErrorAction SilentlyContinue
            Write-Host "Stopped $($process.ProcessName) (PID: $($process.Id))" -ForegroundColor Yellow
        } catch {
            Write-Host "Could not stop $($process.ProcessName)" -ForegroundColor Red
        }
    }
}

# Stop services by port
Write-Host "`nStopping services by port..." -ForegroundColor Cyan
Stop-ProcessByPort -Port 8000  # Laravel Server
Stop-ProcessByPort -Port 6001  # Laravel Reverb
Stop-ProcessByPort -Port 5173  # Vite Dev Server

# Stop PHP processes (Laravel Queue Worker)
Write-Host "`nStopping PHP processes..." -ForegroundColor Cyan
Get-Process | Where-Object { $_.ProcessName -eq "php" -and $_.CommandLine -match "queue:work" } | ForEach-Object {
    Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue
    Write-Host "Stopped Queue Worker (PID: $($_.Id))" -ForegroundColor Yellow
}

# Stop Node processes (Vite)
Write-Host "`nStopping Node processes..." -ForegroundColor Cyan
Get-Process | Where-Object { $_.ProcessName -eq "node" -and $_.CommandLine -match "vite" } | ForEach-Object {
    Stop-Process -Id $_.Id -Force -ErrorAction SilentlyContinue
    Write-Host "Stopped Vite Dev Server (PID: $($_.Id))" -ForegroundColor Yellow
}

# Stop Redis (WSL)
Write-Host "`nStopping Redis Server (WSL)..." -ForegroundColor Cyan
try {
    wsl.exe --user root systemctl stop redis-server
    Write-Host "Redis Server stopped" -ForegroundColor Yellow
} catch {
    Write-Host "Could not stop Redis Server" -ForegroundColor Red
}

# Summary
Write-Host "`n==========================================" -ForegroundColor Red
Write-Host "All development services stopped!" -ForegroundColor Green
Write-Host "==========================================" -ForegroundColor Red
Write-Host "`nYou can now safely close all terminal windows." -ForegroundColor White
Write-Host ""

pause
