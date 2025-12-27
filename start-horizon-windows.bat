@echo off
echo Starting Laravel Horizon for Windows...
echo Note: PCNTL/POSIX extensions are not available on Windows
echo Using alternative queue worker approach

REM Start Redis if not running
echo Checking Redis...
tasklist /FI "IMAGENAME eq redis-server.exe" 2>NUL | find /I /N "redis-server.exe">NUL
if "%ERRORLEVEL%"=="1" (
    echo Starting Redis...
    start "Redis" redis-server
    timeout /t 3 >nul
)

REM Run queue workers instead of Horizon
echo Starting Laravel Queue Workers...
start "Queue Worker 1" php artisan queue:work --queue=high,default --sleep=3 --tries=3 --max-time=3600
start "Queue Worker 2" php artisan queue:work --queue=default --sleep=3 --tries=3 --max-time=3600
start "Queue Worker 3" php artisan queue:work --queue=low --sleep=3 --tries=3 --max-time=3600

echo Laravel Queue Workers started successfully!
echo Press any key to stop all workers...
pause >nul

REM Stop all queue workers
echo Stopping queue workers...
taskkill /F /IM php.exe /FI "WINDOWTITLE eq Queue Worker*" 2>nul

echo Queue workers stopped.