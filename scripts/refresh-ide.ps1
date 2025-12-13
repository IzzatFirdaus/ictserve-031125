#!/usr/bin/env pwsh
# IDE Refresh Script
# Run this when IDE shows incorrect branch info or has indexing issues

Write-Host "🔄 Refreshing IDE and Git integration..." -ForegroundColor Green

Write-Host "`n🧹 Cleaning Laravel caches..." -ForegroundColor Yellow
php artisan optimize:clear

Write-Host "`n🔧 Regenerating autoloader..." -ForegroundColor Yellow
composer dump-autoload

Write-Host "`n📦 Rebuilding frontend assets..." -ForegroundColor Yellow
npm run build

Write-Host "`n🗂️  Fixing file permissions..." -ForegroundColor Yellow
icacls storage /grant Everyone:F /T | Out-Null
icacls bootstrap/cache /grant Everyone:F /T | Out-Null

Write-Host "`n🔍 Cleaning IDE helper files..." -ForegroundColor Yellow
Remove-Item vendor/_laravel_ide -Recurse -Force -ErrorAction SilentlyContinue
Remove-Item .phpunit.result.cache -Force -ErrorAction SilentlyContinue

Write-Host "`n📋 Current Git Status:" -ForegroundColor Yellow
git status --porcelain

Write-Host "`n🌿 Current Branch Info:" -ForegroundColor Yellow
git branch -vv

Write-Host "`n✅ IDE refresh complete!" -ForegroundColor Green
Write-Host "💡 If issues persist:" -ForegroundColor Cyan
Write-Host "   1. Restart your IDE completely" -ForegroundColor Cyan
Write-Host "   2. Reload the workspace/project" -ForegroundColor Cyan
Write-Host "   3. Clear IDE cache/indexes if available" -ForegroundColor Cyan