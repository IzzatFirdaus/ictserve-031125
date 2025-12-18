# ICTServe Docker Setup Script
# Automates Docker environment setup

param(
    [switch]$Clean,
    [switch]$SkipBuild
)

Write-Host "🐳 ICTServe Docker Setup" -ForegroundColor Cyan
Write-Host "=========================" -ForegroundColor Cyan
Write-Host ""

# Check if Docker is running
try {
    docker ps | Out-Null
} catch {
    Write-Host "❌ Docker is not running. Please start Docker Desktop." -ForegroundColor Red
    exit 1
}

# Clean up if requested
if ($Clean) {
    Write-Host "🧹 Cleaning up existing containers and volumes..." -ForegroundColor Yellow
    docker compose down -v
    Write-Host "✅ Cleanup complete" -ForegroundColor Green
    Write-Host ""
}

# Build images
if (-not $SkipBuild) {
    Write-Host "🔨 Building Docker images..." -ForegroundColor Yellow
    docker compose build --no-cache app
    if ($LASTEXITCODE -ne 0) {
        Write-Host "❌ Build failed" -ForegroundColor Red
        exit 1
    }
    Write-Host "✅ Build complete" -ForegroundColor Green
    Write-Host ""
}

# Install Composer dependencies
Write-Host "📦 Installing Composer dependencies..." -ForegroundColor Yellow
docker compose run --rm app composer install --no-interaction --prefer-dist
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Composer dependencies installed" -ForegroundColor Green
Write-Host ""

# Clean node_modules to avoid Windows/Linux binary conflicts
Write-Host "🧹 Cleaning node_modules (Windows/Linux compatibility)..." -ForegroundColor Yellow
if (Test-Path "node_modules") {
    Remove-Item -Recurse -Force "node_modules" -ErrorAction SilentlyContinue
}
Write-Host "✅ node_modules cleaned" -ForegroundColor Green
Write-Host ""

# Install NPM dependencies
Write-Host "📦 Installing NPM dependencies..." -ForegroundColor Yellow
docker compose run --rm app npm install --no-save
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ NPM install failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ NPM dependencies installed" -ForegroundColor Green
Write-Host ""

# Setup environment file
if (-not (Test-Path ".env.docker")) {
    Write-Host "⚙️  Creating .env.docker..." -ForegroundColor Yellow
    Copy-Item ".env.example" ".env.docker"
    
    # Update Docker-specific settings
    (Get-Content ".env.docker") `
        -replace 'DB_HOST=127.0.0.1', 'DB_HOST=db' `
        -replace 'REDIS_HOST=127.0.0.1', 'REDIS_HOST=redis' `
        -replace 'DB_PASSWORD=', 'DB_PASSWORD=secret' |
        Set-Content ".env.docker"
    
    Write-Host "✅ Environment file created" -ForegroundColor Green
    Write-Host ""
}

# Start services
Write-Host "🚀 Starting Docker services..." -ForegroundColor Yellow
docker compose up -d
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to start services" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Services started" -ForegroundColor Green
Write-Host ""

# Wait for database
Write-Host "⏳ Waiting for database..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Generate app key
Write-Host "🔑 Generating application key..." -ForegroundColor Yellow
docker compose exec app php artisan key:generate --force
Write-Host "✅ Application key generated" -ForegroundColor Green
Write-Host ""

# Run migrations
Write-Host "🗄️  Running database migrations..." -ForegroundColor Yellow
docker compose exec app php artisan migrate --seed --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Migration failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Database migrated and seeded" -ForegroundColor Green
Write-Host ""

# Build assets
Write-Host "🎨 Building frontend assets..." -ForegroundColor Yellow
docker compose exec app npm run build
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Asset build failed" -ForegroundColor Red
    exit 1
}
Write-Host "✅ Assets built" -ForegroundColor Green
Write-Host ""

# Fix permissions
Write-Host "🔒 Fixing permissions..." -ForegroundColor Yellow
docker compose exec app chown -R www-data:www-data storage bootstrap/cache
Write-Host "✅ Permissions fixed" -ForegroundColor Green
Write-Host ""

# Show status
Write-Host "📊 Service Status:" -ForegroundColor Cyan
docker compose ps
Write-Host ""

Write-Host "✅ Docker setup complete!" -ForegroundColor Green
Write-Host ""
Write-Host "🌐 Application URLs:" -ForegroundColor Cyan
Write-Host "   - Application: http://localhost:8000" -ForegroundColor White
Write-Host "   - Admin Panel: http://localhost:8000/admin" -ForegroundColor White
Write-Host ""
Write-Host "📝 Useful commands:" -ForegroundColor Cyan
Write-Host "   docker compose logs -f app    # View logs" -ForegroundColor White
Write-Host "   docker compose exec app sh    # Access container" -ForegroundColor White
Write-Host "   docker compose down           # Stop services" -ForegroundColor White
Write-Host ""
