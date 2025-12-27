#!/usr/bin/env pwsh
# Rebuild Docker with PHP 8.4 and clean install

Write-Host "=== Rebuilding Docker with PHP 8.4 ===" -ForegroundColor Cyan

<<<<<<< HEAD
=======
# Ensure we're using Docker configuration
Write-Host "`nEnsuring Docker configuration..." -ForegroundColor Yellow
if (Test-Path ".env.docker") {
    Copy-Item ".env.docker" ".env" -Force
    Write-Host "Switched to Docker configuration (.env.docker → .env)" -ForegroundColor Green
} else {
    Write-Host "Using existing .env (should be Docker-configured)" -ForegroundColor Yellow
}

>>>>>>> af75c552fb7a4feda67d2d695f160bac8a26673c
# Stop and remove containers
Write-Host "`nStopping containers..." -ForegroundColor Yellow
docker compose down -v

# Remove Windows node_modules (binary conflicts)
Write-Host "`nCleaning Windows node_modules..." -ForegroundColor Yellow
if (Test-Path "node_modules") {
    try {
        Remove-Item -Recurse -Force node_modules -ErrorAction Stop
    } catch {
        Write-Host "Warning: Could not remove node_modules (files locked)" -ForegroundColor Yellow
        Write-Host "Continuing anyway - npm will overwrite files" -ForegroundColor Yellow
    }
}

# Rebuild image
Write-Host "`nRebuilding Docker image with PHP 8.4..." -ForegroundColor Yellow
docker compose build --no-cache app

# Start containers
Write-Host "`nStarting containers..." -ForegroundColor Yellow
docker compose up -d

# Wait for services
Write-Host "`nWaiting for services..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Install dependencies in container
Write-Host "`nInstalling Composer dependencies..." -ForegroundColor Yellow
docker compose exec app composer install --no-interaction --no-scripts

# Now run scripts after all packages are installed
Write-Host "Running post-install scripts..." -ForegroundColor Yellow
docker compose exec app composer dump-autoload --optimize

# Install NPM dependencies in container
Write-Host "`nInstalling NPM dependencies..." -ForegroundColor Yellow
docker compose exec app npm install

# Setup Laravel
Write-Host "`nSetting up Laravel..." -ForegroundColor Yellow

# Ensure .env exists
$envCheck = docker compose exec app test -f .env 2>&1
if ($LASTEXITCODE -ne 0) {
    Write-Host "Creating .env file..." -ForegroundColor Yellow
    docker compose exec app cp .env.example .env
}

# Regenerate autoloader to fix class not found errors
Write-Host "Regenerating autoloader..." -ForegroundColor Yellow
docker compose exec app composer dump-autoload --optimize

docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
docker compose exec app php artisan optimize:clear

Write-Host "`n=== Docker Setup Complete! ===" -ForegroundColor Green
Write-Host "Application: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Admin: http://localhost:8000/admin" -ForegroundColor Cyan
Write-Host "Login: superuser@motac.gov.my / password" -ForegroundColor Yellow
