# Complete Docker Setup - Final Steps
# Run after docker-quick-fix.ps1

Write-Host "=== ICTServe Docker - Final Setup ===" -ForegroundColor Cyan
Write-Host ""

# Start services
Write-Host "Starting services..." -ForegroundColor Yellow
docker compose up -d
Start-Sleep -Seconds 10
Write-Host "Services started" -ForegroundColor Green
Write-Host ""

# Generate app key
Write-Host "Generating application key..." -ForegroundColor Yellow
docker compose exec app php artisan key:generate --force
Write-Host "Key generated" -ForegroundColor Green
Write-Host ""

# Run migrations
Write-Host "Running migrations..." -ForegroundColor Yellow
docker compose exec app php artisan migrate --seed --force
Write-Host "Database ready" -ForegroundColor Green
Write-Host ""

# Build assets
Write-Host "Building assets..." -ForegroundColor Yellow
docker compose exec app npm run build
Write-Host "Assets built" -ForegroundColor Green
Write-Host ""

# Fix permissions
Write-Host "Fixing permissions..." -ForegroundColor Yellow
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
Write-Host "Permissions fixed" -ForegroundColor Green
Write-Host ""

Write-Host "=== Setup Complete! ===" -ForegroundColor Green
Write-Host ""
Write-Host "Application: http://localhost:8000" -ForegroundColor Cyan
Write-Host "Admin Panel: http://localhost:8000/admin" -ForegroundColor Cyan
Write-Host ""
Write-Host "Default credentials:" -ForegroundColor Yellow
Write-Host "  Email: superuser@motac.gov.my" -ForegroundColor White
Write-Host "  Password: password" -ForegroundColor White
Write-Host ""
