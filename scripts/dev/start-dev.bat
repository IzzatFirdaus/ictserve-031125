@echo off
REM ICTServe Development Environment Startup Script (Batch Version)
REM This script launches all required services in separate Command Prompt windows

echo Starting ICTServe Development Environment...
echo =============================================
echo.

REM Get the current directory
set PROJECT_ROOT=%cd%

REM 1. Start Redis Server (WSL)
echo [1/5] Starting Redis Server (WSL)...
start "Redis Server (WSL)" cmd /k "wsl.exe --user root systemctl start redis-server && wsl.exe redis-cli ping && echo Redis is running! && wsl.exe redis-cli monitor"
timeout /t 2 /nobreak >nul

REM 2. Start Laravel Server
echo [2/5] Starting Laravel Server...
start "Laravel Server (Port 8000)" cmd /k "cd /d %PROJECT_ROOT% && php artisan serve"
timeout /t 2 /nobreak >nul

REM 3. Start Laravel Reverb (WebSocket Server)
echo [3/5] Starting Laravel Reverb...
start "Laravel Reverb (WebSocket)" cmd /k "cd /d %PROJECT_ROOT% && php artisan reverb:start"
timeout /t 2 /nobreak >nul

REM 4. Start Queue Worker
echo [4/5] Starting Queue Worker...
start "Laravel Queue Worker" cmd /k "cd /d %PROJECT_ROOT% && php artisan queue:work --tries=3 --timeout=90"
timeout /t 2 /nobreak >nul

REM 5. Start Vite Dev Server
echo [5/5] Starting Vite Dev Server...
start "Vite Dev Server (HMR)" cmd /k "cd /d %PROJECT_ROOT% && npm run dev"
timeout /t 2 /nobreak >nul

echo.
echo =============================================
echo All services started successfully!
echo =============================================
echo.
echo Running Services:
echo   1. Redis Server (WSL)       - Monitoring mode
echo   2. Laravel Server           - http://127.0.0.1:8000
echo   3. Laravel Reverb           - ws://127.0.0.1:6001
echo   4. Queue Worker             - Processing jobs
echo   5. Vite Dev Server          - Hot Module Replacement
echo.
echo Close this window to keep services running.
echo To stop all services, close each window individually.
echo.
pause
