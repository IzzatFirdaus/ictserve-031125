# Complete npm setup for ICTServe
# Handles both npm install (Node v18) and build (Node v22)

Write-Host "=== ICTServe Complete npm Setup ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "This script will:" -ForegroundColor Yellow
Write-Host "1. Configure npm with Node.js v18 (no permission issues)" -ForegroundColor Gray
Write-Host "2. Install dependencies with npm install" -ForegroundColor Gray
Write-Host "3. Build assets with Node.js v22 (Vite 7.2.0 requirement)" -ForegroundColor Gray
Write-Host ""

# Step 1: Configure npm with Node v18
Write-Host "Step 1: Configuring npm..." -ForegroundColor Cyan
& "$PSScriptRoot\fix-npm-complete.ps1"

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "ERROR: npm configuration failed" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Step 2: Installing dependencies..." -ForegroundColor Cyan
$env:Path = "C:\laragon\bin\nodejs\node-v18;$env:Path"
npm install

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "ERROR: npm install failed" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "Step 3: Building assets with Node.js v22..." -ForegroundColor Cyan
& "$PSScriptRoot\build-with-node22.ps1"

if ($LASTEXITCODE -ne 0) {
    Write-Host ""
    Write-Host "ERROR: Asset build failed" -ForegroundColor Red
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "SUCCESS! Complete npm setup finished" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Summary:" -ForegroundColor Cyan
Write-Host "  npm configured with Node.js v18" -ForegroundColor Gray
Write-Host "  Dependencies installed successfully" -ForegroundColor Gray
Write-Host "  Assets built with Node.js v22" -ForegroundColor Gray
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Start development: .\scripts\dev\start-dev.ps1" -ForegroundColor White
Write-Host "2. Or manually: php artisan serve" -ForegroundColor White
Write-Host ""
Write-Host "For future builds:" -ForegroundColor Cyan
Write-Host "  npm install: Use .\fix-npm.ps1 first" -ForegroundColor Gray
Write-Host "  npm run build: Use .\scripts\dev\build-with-node22.ps1" -ForegroundColor Gray
