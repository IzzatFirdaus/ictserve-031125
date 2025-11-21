@echo off
REM ICTServe UI/UX Fixes Verification Script (Windows)
REM Run this script to verify all 5 fixes are properly applied

echo =========================================
echo ICTServe UI/UX Fixes Verification
echo =========================================
echo.

REM Check 1: EmailQueueTrendsWidget exists
echo Fix 1 - EmailQueueTrendsWidget:
if exist "app\Filament\Widgets\EmailQueueTrendsWidget.php" (
    echo [32m✓ Created[0m
) else (
    echo [31m✗ Missing[0m
)

REM Check 2: filament-fixes.css exists
echo Fix 1 - filament-fixes.css:
if exist "resources\css\filament-fixes.css" (
    echo [32m✓ Created[0m
) else (
    echo [31m✗ Missing[0m
)

REM Check 3: AdminPanelProvider has enhanced styles
echo Fix 1 - AdminPanelProvider styles:
findstr /C:"fi-ta-icon svg" "app\Providers\Filament\AdminPanelProvider.php" >nul
if %errorlevel%==0 (
    echo [32m✓ Enhanced[0m
) else (
    echo [33m⚠ Needs verification[0m
)

REM Check 4: ReportBuilder collapsible fix
echo Fix 3 - ReportBuilder form:
findstr /C:"collapsible(false)" "app\Filament\Pages\ReportBuilder.php" >nul
if %errorlevel%==0 (
    echo [32m✓ Fixed[0m
) else (
    echo [31m✗ Not fixed[0m
)

REM Check 5: TwoFactorAuthentication QR code
echo Fix 4 - 2FA QR Code:
findstr /C:"api.qrserver.com" "app\Filament\Pages\TwoFactorAuthentication.php" >nul
if %errorlevel%==0 (
    echo [32m✓ Fixed[0m
) else (
    echo [31m✗ Not fixed[0m
)

REM Check 6: vite.config.js includes filament-fixes.css
echo Fix 5 - Vite config:
findstr /C:"filament-fixes.css" "vite.config.js" >nul
if %errorlevel%==0 (
    echo [32m✓ Configured[0m
) else (
    echo [31m✗ Not configured[0m
)

echo.
echo =========================================
echo Next Steps:
echo =========================================
echo 1. Run: npm run build
echo 2. Run: php artisan optimize:clear
echo 3. Visit: http://localhost:8000/admin
echo 4. Test each fix using UI_UX_FIXES_SUMMARY.md checklist
echo.
pause
