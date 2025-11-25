#!/usr/bin/env pwsh
# Check if assets are properly built

Write-Host "=== ICTServe Asset Build Checker ===" -ForegroundColor Cyan
Write-Host ""

# Check if public/build exists
Write-Host "1. Checking public/build directory..." -ForegroundColor Yellow
docker compose exec app sh -c "ls -la public/build 2>/dev/null || echo 'Directory not found'"
Write-Host ""

# Check manifest.json
Write-Host "2. Checking manifest.json..." -ForegroundColor Yellow
docker compose exec app sh -c "cat public/build/manifest.json 2>/dev/null || echo 'Manifest not found'"
Write-Host ""

# Check if Vite is running
Write-Host "3. Checking if Vite dev server is running..." -ForegroundColor Yellow
docker compose exec app sh -c "ps aux | grep 'vite' | grep -v grep || echo 'Vite not running'"
Write-Host ""

# Check Laravel cache
Write-Host "4. Checking Laravel view cache..." -ForegroundColor Yellow
docker compose exec app sh -c "ls -la storage/framework/views/ | head -n 10"
Write-Host ""

Write-Host "=== Recommendations ===" -ForegroundColor Green
Write-Host "If public/build is missing:"
Write-Host "  docker compose exec app npm run build"
Write-Host ""
Write-Host "If styles still not loading:"
Write-Host "  docker compose exec app php artisan optimize:clear"
Write-Host "  docker compose restart app"
Write-Host ""
Write-Host "For development with hot reload:"
Write-Host "  docker compose exec app npm run dev"
Write-Host "  (Keep this running in separate terminal)"
