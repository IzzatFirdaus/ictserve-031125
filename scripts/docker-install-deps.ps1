#!/usr/bin/env pwsh
# Install dependencies inside Docker container (workaround for PHP version mismatch)

Write-Host "=== Installing Dependencies in Docker Container ===" -ForegroundColor Cyan

# Start containers
Write-Host "`nStarting containers..." -ForegroundColor Yellow
docker compose up -d

# Wait for containers
Write-Host "Waiting for containers to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Install Composer dependencies in container
Write-Host "`nInstalling Composer dependencies in container..." -ForegroundColor Yellow
docker compose exec app composer install --no-interaction --prefer-dist

# Install NPM dependencies in container
Write-Host "`nInstalling NPM dependencies in container..." -ForegroundColor Yellow
docker compose exec app npm install

# Generate app key
Write-Host "`nGenerating application key..." -ForegroundColor Yellow
docker compose exec app php artisan key:generate

# Run migrations
Write-Host "`nRunning migrations..." -ForegroundColor Yellow
docker compose exec app php artisan migrate --force

# Seed database
Write-Host "`nSeeding database..." -ForegroundColor Yellow
docker compose exec app php artisan db:seed --force

# Clear caches
Write-Host "`nClearing caches..." -ForegroundColor Yellow
docker compose exec app php artisan optimize:clear

Write-Host "`n=== Setup Complete! ===" -ForegroundColor Green
Write-Host "Application: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Admin Panel: http://localhost:8000/admin" -ForegroundColor Cyan
Write-Host "Default Login: superuser@motac.gov.my / password" -ForegroundColor Yellow
