@echo off
REM ICTServe Development Environment Startup (Batch Version)
REM This is a simplified version - use PowerShell script for full features

echo ICTServe v3.6.0 Development Environment
echo =======================================
echo.

REM Check if PowerShell is available (recommended)
where powershell >nul 2>&1
if %errorlevel% == 0 (
    echo [INFO] PowerShell detected - using enhanced script...
    powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0start-dev.ps1" %*
    goto :end
)

echo [WARN] PowerShell not found - using basic batch version
echo [INFO] For full features, install PowerShell and use start-dev.ps1
echo.

REM Basic service startup without advanced features
echo Starting Laravel development services...
echo.

REM Start Laravel server
echo [1/5] Starting Laravel Server...
start "Laravel Server" cmd /k "cd /d %~dp0..\.. && php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 3 /nobreak >nul

REM Start Vite dev server
echo [2/5] Starting Vite Dev Server...
start "Vite Dev Server" cmd /k "cd /d %~dp0..\.. && npm run dev"
timeout /t 3 /nobreak >nul

REM Start Reverb WebSocket Server
echo [3/5] Starting Laravel Reverb...
start "Laravel Reverb" cmd /k "cd /d %~dp0..\.. && php artisan reverb:start --host=127.0.0.1 --port=6001"
timeout /t 2 /nobreak >nul

REM Check for Horizon availability and start appropriate queue service
echo [4/5] Starting Queue Service...
php artisan horizon:status >nul 2>&1
if %errorlevel% == 0 (
    echo   ^> Using Laravel Horizon for queue management
    start "Laravel Horizon" cmd /k "cd /d %~dp0..\.. && php artisan horizon"
) else (
    echo   ^> Using basic Queue Worker ^(Horizon not available^)
    start "Queue Worker" cmd /k "cd /d %~dp0..\.. && php artisan queue:work --tries=3 --timeout=90"
)
timeout /t 2 /nobreak >nul

REM Optional: Start Redis monitoring if WSL is available
echo [5/5] Checking Redis availability...
where wsl.exe >nul 2>&1
if %errorlevel% == 0 (
    wsl.exe -e redis-cli ping >nul 2>&1
    if %errorlevel% == 0 (
        echo   ^> Redis detected in WSL - starting monitor
        start "Redis Monitor (WSL)" cmd /k "wsl.exe redis-cli monitor"
    ) else (
        echo   ^> Redis not available in WSL
    )
) else (
    echo   ^> WSL not available - skipping Redis monitor
)

echo.
echo ========================================
echo ICTServe Development Environment Ready!
echo ========================================
echo.
echo Services Started:
echo   - Laravel Server:    http://127.0.0.1:8000
echo   - Vite Dev Server:   http://127.0.0.1:5173
echo   - Laravel Reverb:    ws://127.0.0.1:6001
echo   - Queue Service:     Background processing
echo   - Redis Monitor:     WSL monitoring ^(if available^)
echo.
echo Quick Access:
echo   - Application:       http://127.0.0.1:8000
echo   - Admin Panel:       http://127.0.0.1:8000/admin
echo   - Horizon Dashboard: http://127.0.0.1:8000/horizon ^(if available^)
echo.
echo Note: For Redis, WSL setup, and advanced features,
echo       use the PowerShell version: start-dev.ps1
echo.
echo Press any key to open the application in browser...
pause >nul

REM Open browser
start http://127.0.0.1:8000

:end
echo.
echo To stop services, close the individual terminal windows.
pause