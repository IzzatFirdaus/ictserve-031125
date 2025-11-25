#!/usr/bin/env pwsh
# Quick fix for Tailwind CSS styling issues

Write-Host "=== ICTServe Styling Fix ===" -ForegroundColor Cyan
Write-Host ""

Write-Host "Step 1: Clearing Laravel caches..." -ForegroundColor Yellow
docker compose exec app php artisan optimize:clear
Write-Host ""

Write-Host "Step 2: Restarting app container..." -ForegroundColor Yellow
docker compose restart app
Write-Host ""

Write-Host "Step 3: Waiting for app to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 3
Write-Host ""

Write-Host "=== Fix Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Hard refresh your browser (Ctrl+Shift+R or Ctrl+F5)"
Write-Host "2. If still not working, run: docker compose exec app npm run build"
Write-Host "3. Access: http://localhost:8000"
Write-Host ""
