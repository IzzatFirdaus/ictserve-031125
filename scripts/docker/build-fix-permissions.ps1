#!/usr/bin/env pwsh
# Fix Node.js permissions and rebuild Docker containers for ICTServe

param(
    [switch]$Development,
    [switch]$Production,
    [switch]$Clean
)

Write-Host "=== ICTServe Docker Build & Permission Fix ===" -ForegroundColor Cyan
Write-Host ""

# Determine build mode
$isDev = $Development -or (!$Production -and !$Clean)
$composeFiles = @("compose.yaml")

if ($isDev) {
    $composeFiles += "compose.dev.yaml"
    Write-Host "Building for DEVELOPMENT mode..." -ForegroundColor Yellow
} else {
    Write-Host "Building for PRODUCTION mode..." -ForegroundColor Yellow
}

# Clean build if requested
if ($Clean) {
    Write-Host ""
    Write-Host "Step 1: Cleaning existing containers and images..." -ForegroundColor Yellow

    docker compose -f ($composeFiles -join " -f ") down --volumes --remove-orphans
    docker system prune -f
    docker volume prune -f

    Write-Host "✓ Cleaned Docker environment" -ForegroundColor Green
}

Write-Host ""
Write-Host "Step 2: Building Docker images..." -ForegroundColor Yellow

$buildArgs = @()
if ($isDev) {
    $buildArgs += "--build-arg", "INSTALL_DEV=true"
}

# Build the main application image
docker build $buildArgs -t ictserve-app:latest .

if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Docker build failed" -ForegroundColor Red
    exit 1
}

Write-Host "✓ Built ictserve-app:latest" -ForegroundColor Green

Write-Host ""
Write-Host "Step 3: Starting services..." -ForegroundColor Yellow

# Start services
$composeCmd = "docker compose " + ($composeFiles | ForEach-Object { "-f $_" }) -join " "
Invoke-Expression "$composeCmd up -d"

if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Failed to start services" -ForegroundColor Red
    exit 1
}

Write-Host "✓ Services started successfully" -ForegroundColor Green

Write-Host ""
Write-Host "Step 4: Fixing permissions..." -ForegroundColor Yellow

# Fix permissions inside the container
docker compose exec app chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
docker compose exec app chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Create npm directories with correct permissions
docker compose exec app mkdir -p /var/www/.npm-cache /var/www/.npm-global
docker compose exec app chown -R www-data:www-data /var/www/.npm-cache /var/www/.npm-global

Write-Host "✓ Fixed file permissions" -ForegroundColor Green

Write-Host ""
Write-Host "Step 5: Testing npm build..." -ForegroundColor Yellow

# Test npm build as www-data user
$npmTest = docker compose exec --user www-data app npm run build

if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ npm run build successful!" -ForegroundColor Green
} else {
    Write-Host "✗ npm run build failed - checking logs..." -ForegroundColor Red
    docker compose logs app
    exit 1
}

Write-Host ""
Write-Host "=== Build Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "Services running:" -ForegroundColor Cyan
Write-Host "  • Application: http://localhost:8000" -ForegroundColor White
Write-Host "  • Database: localhost:3306" -ForegroundColor White
Write-Host "  • Redis: localhost:6379" -ForegroundColor White
Write-Host "  • Reverb WebSocket: localhost:8080" -ForegroundColor White

if ($isDev) {
    Write-Host "  • Vite Dev Server: http://localhost:5173" -ForegroundColor White
    Write-Host ""
    Write-Host "Development commands:" -ForegroundColor Cyan
    Write-Host "  docker compose exec app npm run dev" -ForegroundColor Gray
    Write-Host "  docker compose exec app php artisan tinker" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Useful commands:" -ForegroundColor Cyan
Write-Host "  docker compose logs -f app" -ForegroundColor Gray
Write-Host "  docker compose exec app bash" -ForegroundColor Gray
Write-Host "  docker compose down" -ForegroundColor Gray
Write-Host ""
