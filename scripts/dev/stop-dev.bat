@echo off
REM ICTServe Development Environment Stop Script (Batch Version)
REM This script stops all running development services

echo Stopping ICTServe Development Services...
echo ==========================================
echo.

REM Stop Laravel Server (Port 8000)
echo Stopping Laravel Server (Port 8000)...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :8000') do (
    taskkill /F /PID %%a >nul 2>&1
)

REM Stop Laravel Reverb (Port 6001)
echo Stopping Laravel Reverb (Port 6001)...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :6001') do (
    taskkill /F /PID %%a >nul 2>&1
)

REM Stop Vite Dev Server (Port 5173)
echo Stopping Vite Dev Server (Port 5173)...
for /f "tokens=5" %%a in ('netstat -ano ^| findstr :5173') do (
    taskkill /F /PID %%a >nul 2>&1
)

REM Stop PHP processes (Queue Worker)
echo Stopping PHP Queue Worker...
taskkill /F /IM php.exe >nul 2>&1

REM Stop Node processes (Vite)
echo Stopping Node processes...
taskkill /F /IM node.exe >nul 2>&1

REM Stop Redis (WSL)
echo Stopping Redis Server (WSL)...
wsl.exe --user root systemctl stop redis-server

echo.
echo ==========================================
echo All development services stopped!
echo ==========================================
echo.
echo You can now safely close all terminal windows.
echo.
pause
