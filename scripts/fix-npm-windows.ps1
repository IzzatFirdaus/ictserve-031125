#!/usr/bin/env pwsh
# Fix npm build error on Windows host

Write-Host "=== Fix npm Build Error on Windows ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "This will remove node_modules and package-lock.json, then reinstall." -ForegroundColor Yellow
Write-Host ""

$confirm = Read-Host "Continue? (y/n)"
if ($confirm -ne 'y') {
    Write-Host "Cancelled." -ForegroundColor Red
    exit
}

Write-Host ""
Write-Host "Step 1: Removing node_modules..." -ForegroundColor Yellow
if (Test-Path "node_modules") {
    Remove-Item -Recurse -Force node_modules
    Write-Host "✓ Removed node_modules" -ForegroundColor Green
} else {
    Write-Host "✓ node_modules not found (skipped)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Step 2: Removing package-lock.json..." -ForegroundColor Yellow
if (Test-Path "package-lock.json") {
    Remove-Item -Force package-lock.json
    Write-Host "✓ Removed package-lock.json" -ForegroundColor Green
} else {
    Write-Host "✓ package-lock.json not found (skipped)" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Step 3: Reinstalling npm dependencies..." -ForegroundColor Yellow
npm install

Write-Host ""
Write-Host "Step 4: Building assets..." -ForegroundColor Yellow
npm run build

Write-Host ""
Write-Host "=== Fix Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "If you still have issues, use Docker container instead:" -ForegroundColor Cyan
Write-Host "  docker compose exec app npm run build"
Write-Host ""
