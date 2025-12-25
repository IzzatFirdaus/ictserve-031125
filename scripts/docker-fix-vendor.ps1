# Fix vendor directory issue
Write-Host "=== Fixing Docker Vendor Issue ===" -ForegroundColor Cyan
Write-Host ""

# Stop containers
Write-Host "Stopping containers..." -ForegroundColor Yellow
docker compose down -v
Write-Host ""

# Install on HOST (not container)
Write-Host "Installing Composer dependencies on HOST..." -ForegroundColor Yellow
composer install --no-interaction
Write-Host ""

Write-Host "Installing NPM dependencies on HOST..." -ForegroundColor Yellow
npm install
Write-Host ""

# Restart
Write-Host "Starting containers..." -ForegroundColor Yellow
docker compose up -d
Start-Sleep -Seconds 10
Write-Host ""

# Setup Laravel
Write-Host "Setting up Laravel..." -ForegroundColor Yellow
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --seed --force
docker compose exec app php artisan optimize:clear
Write-Host ""

Write-Host "=== Fixed! ===" -ForegroundColor Green
Write-Host "Application: http://localhost:8000" -ForegroundColor Cyan
