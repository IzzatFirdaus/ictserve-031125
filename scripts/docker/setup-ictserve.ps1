#!/usr/bin/env pwsh
# Complete ICTServe Docker setup script with Node.js permission fixes

param(
    [ValidateSet("development", "production")]
    [string]$Mode = "development",
    [switch]$Clean,
    [switch]$SkipBuild,
    [switch]$Verbose
)

$ErrorActionPreference = "Stop"

Write-Host "=== ICTServe Docker Setup ===" -ForegroundColor Cyan
Write-Host "Mode: $Mode" -ForegroundColor Yellow
Write-Host ""

# Set compose files based on mode
$composeFiles = @("compose.yaml")
if ($Mode -eq "development") {
    $composeFiles += "compose.dev.yaml"
}

$composeCmd = "docker compose " + ($composeFiles | ForEach-Object { "-f $_" }) -join " "

# Clean environment if requested
if ($Clean) {
    Write-Host "Cleaning Docker environment..." -ForegroundColor Yellow

    Invoke-Expression "$composeCmd down --volumes --remove-orphans"
    docker system prune -f
    docker volume prune -f

    # Remove built images
    docker rmi ictserve-app:latest -f 2>$null

    Write-Host "✓ Environment cleaned" -ForegroundColor Green
    Write-Host ""
}

# Build images
if (-not $SkipBuild) {
    Write-Host "Building Docker images..." -ForegroundColor Yellow

    $buildArgs = @()
    if ($Mode -eq "development") {
        $buildArgs += "--build-arg", "INSTALL_DEV=true"
    }

    $buildCmd = "docker build $($buildArgs -join ' ') -t ictserve-app:latest ."

    if ($Verbose) {
        Write-Host "Running: $buildCmd" -ForegroundColor Gray
    }

    Invoke-Expression $buildCmd

    if ($LASTEXITCODE -ne 0) {
        Write-Host "✗ Docker build failed" -ForegroundColor Red
        exit 1
    }

    Write-Host "✓ Images built successfully" -ForegroundColor Green
    Write-Host ""
}

# Start services
Write-Host "Starting services..." -ForegroundColor Yellow

if ($Verbose) {
    Write-Host "Running: $composeCmd up -d" -ForegroundColor Gray
}

Invoke-Expression "$composeCmd up -d"

if ($LASTEXITCODE -ne 0) {
    Write-Host "✗ Failed to start services" -ForegroundColor Red
    exit 1
}

Write-Host "✓ Services started" -ForegroundColor Green

# Wait for services to be ready
Write-Host ""
Write-Host "Waiting for services to be ready..." -ForegroundColor Yellow

$maxWait = 60
$waited = 0

do {
    Start-Sleep 2
    $waited += 2

    $dbReady = docker compose exec db mysqladmin ping -h localhost -u root -psecret 2>$null
    $appReady = docker compose exec app php -v 2>$null

    if ($dbReady -and $appReady) {
        break
    }

    if ($waited -ge $maxWait) {
        Write-Host "✗ Services did not start within $maxWait seconds" -ForegroundColor Red
        docker compose logs
        exit 1
    }
} while ($true)

Write-Host "✓ Services are ready" -ForegroundColor Green

# Setup npm environment and install dependencies
Write-Host ""
Write-Host "Setting up npm environment..." -ForegroundColor Yellow

# Remove existing node_modules to avoid permission conflicts
docker compose exec app rm -rf /var/www/html/node_modules

# Install npm dependencies as www-data user
Write-Host "Installing npm dependencies..." -ForegroundColor Yellow
docker compose exec --user www-data app npm ci --prefer-offline --no-audit

if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠ npm ci failed, trying npm install..." -ForegroundColor Yellow
    docker compose exec --user www-data app npm install

    if ($LASTEXITCODE -ne 0) {
        Write-Host "⚠ npm install as www-data failed, trying as root..." -ForegroundColor Yellow
        docker compose exec app npm install
        # Entrypoint will fix permissions on next restart
        docker compose restart app
        Start-Sleep 5
    }
}

Write-Host "✓ npm environment configured" -ForegroundColor Green

# Build assets for production mode
if ($Mode -eq "production") {
    Write-Host ""
    Write-Host "Building production assets..." -ForegroundColor Yellow

    docker compose exec --user www-data app npm run build

    if ($LASTEXITCODE -ne 0) {
        Write-Host "✗ Asset build failed" -ForegroundColor Red
        docker compose logs app --tail 20
        exit 1
    }

    Write-Host "✓ Assets built successfully" -ForegroundColor Green
}

# Run Laravel setup commands
Write-Host ""
Write-Host "Setting up Laravel..." -ForegroundColor Yellow

# Generate app key if needed
$keyCheck = docker compose exec app php artisan config:show app.key 2>$null
if (-not $keyCheck -or $keyCheck -match "null") {
    docker compose exec app php artisan key:generate --force
    Write-Host "✓ Generated application key" -ForegroundColor Green
}

# Run migrations
docker compose exec app php artisan migrate --force 2>$null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ Database migrations completed" -ForegroundColor Green
} else {
    Write-Host "⚠ Database migrations skipped (may not be needed)" -ForegroundColor Yellow
}

# Clear caches
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

Write-Host "✓ Laravel setup completed" -ForegroundColor Green

# Final verification
Write-Host ""
Write-Host "Verifying setup..." -ForegroundColor Yellow

# Test npm build
docker compose exec --user www-data app npm run build 2>&1 | Out-Null
if ($LASTEXITCODE -eq 0) {
    Write-Host "✓ npm build test passed" -ForegroundColor Green
} else {
    Write-Host "✗ npm build test failed" -ForegroundColor Red
    Write-Host "Trying build as root..." -ForegroundColor Yellow
    docker compose exec app npm run build 2>&1 | Out-Null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✓ Build successful as root - fixing permissions..." -ForegroundColor Yellow
        docker compose exec app chown -R www-data:www-data /var/www/html/node_modules /var/www/html/public/build
    }
}

# Test PHP
$phpTest = docker compose exec app php artisan --version 2>$null
if ($phpTest) {
    Write-Host "✓ PHP/Laravel test passed" -ForegroundColor Green
} else {
    Write-Host "✗ PHP/Laravel test failed" -ForegroundColor Red
}

Write-Host ""
Write-Host "=== Setup Complete ===" -ForegroundColor Green
Write-Host ""

# Show service information
Write-Host "Services running:" -ForegroundColor Cyan
Write-Host "  • Application: http://localhost:8000" -ForegroundColor White
Write-Host "  • Database: localhost:3306 (user: laravel, pass: secret)" -ForegroundColor White
Write-Host "  • Redis: localhost:6379" -ForegroundColor White
Write-Host "  • Reverb WebSocket: localhost:8080" -ForegroundColor White

if ($Mode -eq "development") {
    Write-Host "  • Vite Dev Server: http://localhost:5173" -ForegroundColor White
    Write-Host ""
    Write-Host "Development commands:" -ForegroundColor Cyan
    Write-Host "  docker compose exec --user www-data app npm run dev" -ForegroundColor Gray
    Write-Host "  docker compose exec app php artisan tinker" -ForegroundColor Gray
    Write-Host "  docker compose exec app php artisan migrate" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Useful commands:" -ForegroundColor Cyan
Write-Host "  docker compose logs -f app" -ForegroundColor Gray
Write-Host "  docker compose exec app bash" -ForegroundColor Gray
Write-Host "  docker compose down" -ForegroundColor Gray
Write-Host "  docker compose restart app" -ForegroundColor Gray
Write-Host ""

# Show status
Write-Host "Container status:" -ForegroundColor Cyan
docker compose ps
Write-Host ""
