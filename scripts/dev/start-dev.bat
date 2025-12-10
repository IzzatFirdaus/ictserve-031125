@echo off
REM ICTServe Development Environment Startup Script (Batch Version)
REM This script launches all required services in separate Command Prompt windows

echo Starting ICTServe Development Environment...
echo =============================================
echo.

REM Get the current directory
set PROJECT_ROOT=%cd%

REM 1. Start Redis Server (WSL) if available
echo [1/5] Starting Redis Server (WSL)...
where wsl.exe >nul 2>nul
if %ERRORLEVEL%==0 (
	REM Check for systemctl and redis-cli presence inside WSL
	for /f "delims=" %%i in ('wsl.exe -e bash -lc "command -v systemctl >/dev/null && echo 1 || echo 0" 2^>nul') do set SYSCTL_PRESENT=%%i
	for /f "delims=" %%i in ('wsl.exe -e bash -lc "command -v redis-cli >/dev/null && echo 1 || echo 0" 2^>nul') do set REDISCLI_PRESENT=%%i
	if "%SYSCTL_PRESENT%"=="1" if "%REDISCLI_PRESENT%"=="1" (
		start "Redis Server (WSL)" cmd /k "cd /d %PROJECT_ROOT% && wsl.exe --user root systemctl start redis-server && wsl.exe redis-cli ping && echo Redis is running! && wsl.exe redis-cli monitor"
	) else (
		echo Skipping WSL Redis start: systemctl or redis-cli not found in WSL.
		echo Checking local 127.0.0.1:6379...
		REM Check with PowerShell Test-NetConnection
		powershell -Command "for ($i=0; $i -lt 5; $i++) { if ((Test-NetConnection -ComputerName 127.0.0.1 -Port 6379).TcpTestSucceeded) { Write-Host 'Laragon Redis is running on 127.0.0.1:6379' -ForegroundColor Green; exit 0 } Start-Sleep -Seconds 1 } Write-Host 'No Redis on 127.0.0.1:6379' -ForegroundColor Yellow; exit 1"
	)
) else (
	echo WSL not detected; skipping WSL Redis start.
	echo Checking local 127.0.0.1:6379...
	powershell -Command "for ($i=0; $i -lt 5; $i++) { if ((Test-NetConnection -ComputerName 127.0.0.1 -Port 6379).TcpTestSucceeded) { Write-Host 'Laragon Redis is running on 127.0.0.1:6379' -ForegroundColor Green; exit 0 } Start-Sleep -Seconds 1 } Write-Host 'No Redis on 127.0.0.1:6379' -ForegroundColor Yellow; exit 1"
)
timeout /t 2 /nobreak >nul

REM If WSL exists but redis isn't installed, offer to run the installer (PowerShell at scripts\dev\install-wsl-redis.ps1)
where wsl.exe >nul 2>nul
if %ERRORLEVEL%==0 (
	echo.
	set /p INSTALLWSL="WSL detected but Redis not present. Install Redis in WSL now? (Y/N): "
	if /I "%INSTALLWSL%"=="Y" (
		echo Launching installer (PowerShell wrapper)...
		powershell -NoProfile -ExecutionPolicy Bypass -File "%~dp0install-wsl-redis.ps1"
	) else (
		echo Skipping automated WSL Redis install.
	)
)
powershell -Command "for ($i=0; $i -lt 10; $i++) { if ((Test-NetConnection -ComputerName 127.0.0.1 -Port 8000).TcpTestSucceeded) { Write-Host '[OK] Laravel Server is reachable on 127.0.0.1:8000' -ForegroundColor Green; exit 0 } Start-Sleep -Seconds 1 } Write-Host '[WARN] Laravel Server did not respond on 127.0.0.1:8000' -ForegroundColor Yellow; exit 1"

REM 2. Start Laravel Server
echo [2/5] Starting Laravel Server...
start "Laravel Server (Port 8000)" cmd /k "cd /d %PROJECT_ROOT% && php artisan serve"
timeout /t 2 /nobreak >nul
powershell -Command "for ($i=0; $i -lt 10; $i++) { if ((Test-NetConnection -ComputerName 127.0.0.1 -Port 6001).TcpTestSucceeded) { Write-Host '[OK] Laravel Reverb is reachable on 127.0.0.1:6001' -ForegroundColor Green; exit 0 } Start-Sleep -Seconds 1 } Write-Host '[WARN] Laravel Reverb did not respond on 127.0.0.1:6001' -ForegroundColor Yellow; exit 1"

REM 3. Start Laravel Reverb (WebSocket Server)
echo [3/5] Starting Laravel Reverb...
start "Laravel Reverb (WebSocket)" cmd /k "cd /d %PROJECT_ROOT% && php artisan reverb:start"
timeout /t 2 /nobreak >nul
powershell -Command "for ($i=0; $i -lt 8; $i++) { $proc=(Get-WmiObject Win32_Process | Where-Object { $_.CommandLine -and ($_.CommandLine -like '*queue:work*' -or $_.CommandLine -like '*artisan queue:work*') }); if ($proc) { Write-Host '[OK] Laravel Queue Worker detected' -ForegroundColor Green; exit 0 } Start-Sleep -Seconds 1 } Write-Host '[WARN] Laravel Queue Worker not detected' -ForegroundColor Yellow; exit 1"

REM 4. Start Queue Worker
echo [4/5] Starting Queue Worker...
start "Laravel Queue Worker" cmd /k "cd /d %PROJECT_ROOT% && php artisan queue:work --tries=3 --timeout=90"
timeout /t 2 /nobreak >nul
powershell -Command "for ($i=0; $i -lt 10; $i++) { if ((Test-NetConnection -ComputerName 127.0.0.1 -Port 5173).TcpTestSucceeded) { Write-Host '[OK] Vite Dev Server is reachable on 127.0.0.1:5173' -ForegroundColor Green; exit 0 } Start-Sleep -Seconds 1 } Write-Host '[WARN] Vite Dev Server did not respond on 127.0.0.1:5173' -ForegroundColor Yellow; exit 1"

REM 5. Start Vite Dev Server
echo [5/5] Starting Vite Dev Server...
start "Vite Dev Server (HMR)" cmd /k "cd /d %PROJECT_ROOT% && set PATH=C:\\laragon\\bin\\nodejs\\node-v22;%PATH% && npm run dev"
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
