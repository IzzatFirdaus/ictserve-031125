#!/usr/bin/env pwsh
# Upgrade PHP to 8.3+ for ICTServe

Write-Host "=== PHP Upgrade Guide ===" -ForegroundColor Cyan
Write-Host ""

Write-Host "Current PHP Version:" -ForegroundColor Yellow
php -v

Write-Host "`n=== OPTION 1: Laragon (Recommended) ===" -ForegroundColor Green
Write-Host "1. Open Laragon"
Write-Host "2. Right-click Laragon tray icon > PHP > Version > Download more..."
Write-Host "3. Download PHP 8.3.x"
Write-Host "4. Right-click Laragon tray icon > PHP > Version > php-8.3.x"
Write-Host "5. Restart Laragon"

Write-Host "`n=== OPTION 2: Manual Download ===" -ForegroundColor Green
Write-Host "1. Download PHP 8.3+ from: https://windows.php.net/download/"
Write-Host "2. Extract to C:\laragon\bin\php\php-8.3.x"
Write-Host "3. Switch PHP version in Laragon"

Write-Host "`n=== OPTION 3: Use Docker Only ===" -ForegroundColor Green
Write-Host "Run dependencies inside Docker container (slower but works):"
Write-Host "docker compose exec app composer install"
Write-Host "docker compose exec app npm install"

Write-Host "`n=== After Upgrading ===" -ForegroundColor Cyan
Write-Host "Run: .\docker-fix-vendor.ps1"
Write-Host ""
