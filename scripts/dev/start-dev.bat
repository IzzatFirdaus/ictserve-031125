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
echo [1/4] Starting Laravel Server...
start "Laravel Server" cmd /k "cd /d %~dp0..\.. && php artisan serve --host=127.0.0.1 --port=8000"
timeout /t 3 /nobreak >nul

REM Start Vite dev server
echo [2/4] Starting Vite Dev Server...
start "Vite Dev Server" cmd /k "cd /d %~dp0..\.. && npm run dev"
timeout /t 3 /nobreak >nul

REM Start Queue Worker
echo [3/4] Starting Queue Worker...
start "Queue Worker" cmd /k "cd /d %~dp0..\.. && php artisan queue:work --tries=3 --timeout=90"
timeout /t 2 /nobreak >nul

REM Start Reverb WebSocket Server
echo [4/4] Starting Laravel Reverb...
start "Laravel Reverb" cmd /k "cd /d %~dp0..\.. && php artisan reverb:start --host=127.0.0.1 --port=6001"
timeout /t 2 /nobreak >nul

echo.
echo ========================================
echo ICTServe Development Environment Ready!
echo ========================================
echo.
echo Services Started:
echo   - Laravel Server:    http://127.0.0.1:8000
echo   - Vite Dev Server:   http://127.0.0.1:5173
echo   - Queue Worker:      Background processing
echo   - Laravel Reverb:    ws://127.0.0.1:6001
echo.
echo Quick Access:
echo   - Application:       http://127.0.0.1:8000
echo   - Admin Panel:       http://127.0.0.1:8000/admin
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