# Fix Redis Configuration for Local Development
# This script configures Redis to work with local XAMPP/host development

Write-Host "Fixing Redis configuration for local development..." -ForegroundColor Cyan

# Check if .env exists
if (-not (Test-Path ".env")) {
    Write-Host "Error: .env file not found!" -ForegroundColor Red
    exit 1
}

# Backup current .env
Copy-Item ".env" ".env.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')" -Force
Write-Host "Created backup of .env" -ForegroundColor Green

# Update Redis configuration for local development
$envContent = Get-Content ".env" -Raw

# Option 1: Use predis (no PHP extension required)
$envContent = $envContent -replace 'REDIS_CLIENT=phpredis', 'REDIS_CLIENT=predis'
$envContent = $envContent -replace 'REDIS_HOST=redis', 'REDIS_HOST=127.0.0.1'

# Option 2: Disable Redis for cache/session (use file/database instead)
# Uncomment these lines if you want to disable Redis completely:
# $envContent = $envContent -replace 'CACHE_STORE=redis', 'CACHE_STORE=file'
# $envContent = $envContent -replace 'SESSION_DRIVER=redis', 'SESSION_DRIVER=file'
# $envContent = $envContent -replace 'QUEUE_CONNECTION=redis', 'QUEUE_CONNECTION=database'

# Save updated .env
Set-Content ".env" $envContent -NoNewline

Write-Host "`nRedis configuration updated:" -ForegroundColor Green
Write-Host "  - REDIS_CLIENT: predis (no PHP extension required)" -ForegroundColor Yellow
Write-Host "  - REDIS_HOST: 127.0.0.1 (localhost)" -ForegroundColor Yellow

# Clear Laravel caches
Write-Host "`nClearing Laravel caches..." -ForegroundColor Cyan
php artisan config:clear
php artisan cache:clear

Write-Host "`nDone! You can now run 'php artisan serve' for local development." -ForegroundColor Green
Write-Host "`nNote: Make sure Redis is running on localhost:6379" -ForegroundColor Yellow
Write-Host "  - For WSL: Start Redis in WSL" -ForegroundColor Gray
Write-Host "  - For Windows: Install Redis for Windows or use Docker" -ForegroundColor Gray
