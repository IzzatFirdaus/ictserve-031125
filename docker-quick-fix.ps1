# Quick Fix for Docker Issues
# Fixes PHP version and node_modules conflicts

Write-Host "🔧 ICTServe Docker Quick Fix" -ForegroundColor Cyan
Write-Host "============================" -ForegroundColor Cyan
Write-Host ""

# Stop containers
Write-Host "⏹️  Stopping containers..." -ForegroundColor Yellow
docker compose down
Write-Host ""

# Clean node_modules
Write-Host "🧹 Removing node_modules (Windows/Linux conflict)..." -ForegroundColor Yellow
if (Test-Path "node_modules") {
    Remove-Item -Recurse -Force "node_modules" -ErrorAction SilentlyContinue
    Write-Host "✅ node_modules removed" -ForegroundColor Green
} else {
    Write-Host "✅ node_modules already clean" -ForegroundColor Green
}
Write-Host ""

# Rebuild with PHP 8.3
Write-Host "🔨 Rebuilding with PHP 8.3 + pcntl..." -ForegroundColor Yellow
docker compose build --no-cache app
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Build failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Build complete" -ForegroundColor Green
Write-Host ""

# Install Composer dependencies
Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Yellow
docker compose run --rm app composer install --no-interaction --ignore-platform-req=ext-pcntl
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Composer dependencies installed" -ForegroundColor Green
Write-Host ""

# Install NPM dependencies
Write-Host "📦 Installing NPM dependencies (fresh)..." -ForegroundColor Yellow
docker compose run --rm app npm install --no-save
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ NPM install failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ NPM dependencies installed" -ForegroundColor Green
Write-Host ""

Write-Host "✅ Quick fix complete!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Run: .\scripts\docker\setup-docker.ps1 -SkipBuild" -ForegroundColor White
Write-Host "  2. Or manually:" -ForegroundColor White
Write-Host "     docker compose up -d" -ForegroundColor White
Write-Host "     docker compose exec app php artisan key:generate --force" -ForegroundColor White
Write-Host "     docker compose exec app php artisan migrate --seed --force" -ForegroundColor White
Write-Host ""
