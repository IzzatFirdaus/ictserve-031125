#!/usr/bin/env pwsh
# Comprehensive fix for npm and Vite permission issues in ICTServe Docker

Write-Host "=== ICTServe npm & Vite Permission Fix ===" -ForegroundColor Cyan
Write-Host ""

# Check if containers are running
$appRunning = docker compose ps --services --filter "status=running" | Select-String "app"

if (-not $appRunning) {
    Write-Host "Starting Docker containers..." -ForegroundColor Yellow
    docker compose up -d
    Start-Sleep 10
}

Write-Host "Step 1: Cleaning and resetting npm environment..." -ForegroundColor Yellow

# Stop containers to ensure clean state
docker compose down
Start-Sleep 2

# Remove local node_modules if it exists
if (Test-Path "node_modules") {
    Remove-Item -Recurse -Force node_modules
    Write-Host "✓ Removed local node_modules" -ForegroundColor Green
}

# Start containers
docker compose up -d
Start-Sleep 10

Write-Host "✓ Containers restarted" -ForegroundColor Green

Write-Host ""
Write-Host "Step 2: Setting up npm environment..." -ForegroundColor Yellow

# Create npm directories with correct permissions
docker compose exec app mkdir -p /var/www/.npm-cache /var/www/.npm-global
docker compose exec app chown -R www-data:www-data /var/www/.npm-cache /var/www/.npm-global

# Configure npm for www-data user
docker compose exec --user www-data app npm config set cache /var/www/.npm-cache
docker compose exec --user www-data app npm config set prefix /var/www/.npm-global

Write-Host "✓ npm environment configured" -ForegroundColor Green

Write-Host ""
Write-Host "Step 3: Installing dependencies..." -ForegroundColor Yellow

# Install dependencies as www-data user
docker compose exec --user www-data app npm ci --prefer-offline --no-audit

if ($LASTEXITCODE -ne 0) {
    Write-Host "npm ci failed, trying npm install..." -ForegroundColor Yellow
    docker compose exec --user www-data app npm install

    if ($LASTEXITCODE -ne 0) {
        Write-Host "npm install as www-data failed, trying as root..." -ForegroundColor Yellow
        docker compose exec app npm install

        # Fix permissions after root install
        docker compose exec app chown -R www-data:www-data /var/www/html/node_modules
        docker compose exec app chmod -R 775 /var/www/html/node_modules
    }
}

Write-Host "✓ Dependencies installed" -ForegroundColor Green

Write-Host ""
Write-Host "Step 4: Fixing Vite permissions..." -ForegroundColor Yellow

# Ensure node_modules is owned by www-data
docker compose exec app chown -R www-data:www-data /var/www/html/node_modules
docker compose exec app chmod -R 775 /var/www/html/node_modules

# Create Vite temp directory proactively
docker compose exec app mkdir -p /var/www/html/node_modules/.vite-temp
docker compose exec app chown -R www-data:www-data /var/www/html/node_modules/.vite-temp
docker compose exec app chmod -R 775 /var/www/html/node_modules/.vite-temp

Write-Host "✓ Vite permissions fixed" -ForegroundColor Green

Write-Host ""
Write-Host "Step 5: Testing Vite build..." -ForegroundColor Yellow

# Test the build as www-data
docker compose exec --user www-data app npm run build

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Vite build successful!" -ForegroundColor Green
} else {
    Write-Host "✗ Vite build failed as www-data, trying as root..." -ForegroundColor Yellow

    # Try as root and fix permissions after
    docker compose exec app npm run build

    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Build successful as root, fixing permissions..." -ForegroundColor Yellow
        docker compose exec app chown -R www-data:www-data /var/www/html/node_modules /var/www/html/public/build
        Write-Host "✓ Permissions fixed" -ForegroundColor Green
    } else {
        Write-Host "✗ Build failed completely" -ForegroundColor Red
        Write-Host ""
        Write-Host "Diagnostic information:" -ForegroundColor Yellow
        docker compose exec app ls -la /var/www/html/vite.config.js
        docker compose exec app ls -la /var/www/html/node_modules/ | Select-Object -First 5
        exit 1
    }
}

Write-Host ""
Write-Host "=== Fix Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "Your ICTServe Docker environment is ready!" -ForegroundColor Cyan
Write-Host "Application: http://localhost:8000" -ForegroundColor White
Write-Host ""
Write-Host "You can now run:" -ForegroundColor Cyan
Write-Host "  docker compose exec --user www-data app npm run build" -ForegroundColor White
Write-Host "  docker compose exec --user www-data app npm run dev" -ForegroundColor White
Write-Host ""
