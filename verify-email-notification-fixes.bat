@echo off
echo ========================================
echo Email Queue & Notification UI/UX Fixes
echo Verification Script
echo ========================================
echo.

echo [1/4] Checking EmailQueueTrendsWidget...
findstr /C:"rgb(0, 86, 179)" app\Filament\Widgets\EmailQueueTrendsWidget.php >nul
if %errorlevel%==0 (
    echo [OK] Chart colors updated to system colors
) else (
    echo [FAIL] Chart colors not found
)

echo.
echo [2/4] Checking email-queue-monitoring.blade.php...
findstr /C:"x-filament::badge" resources\views\filament\pages\email-queue-monitoring.blade.php >nul
if %errorlevel%==0 (
    echo [OK] Filament badge component used
) else (
    echo [FAIL] Filament badge not found
)

findstr /C:"x-filament::alert" resources\views\filament\pages\email-queue-monitoring.blade.php >nul
if %errorlevel%==0 (
    echo [OK] Filament alert component used
) else (
    echo [FAIL] Filament alert not found
)

echo.
echo [3/4] Checking notification-center.blade.php...
findstr /C:"w-8 h-8" resources\views\filament\pages\notification-center.blade.php >nul
if %errorlevel%==0 (
    echo [OK] Icon sizing standardized (w-8 h-8)
) else (
    echo [FAIL] Icon sizing not standardized
)

echo.
echo [4/4] Checking notification-preferences.blade.php...
findstr /C:"w-5 h-5" resources\views\filament\pages\notification-preferences.blade.php >nul
if %errorlevel%==0 (
    echo [OK] Icon sizing standardized (w-5 h-5)
) else (
    echo [FAIL] Icon sizing not standardized
)

echo.
echo ========================================
echo Verification Complete
echo ========================================
echo.
echo Next Steps:
echo 1. Run: php artisan optimize:clear
echo 2. Run: npm run build
echo 3. Test at: http://localhost:8000/admin
echo.
pause
