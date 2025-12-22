# Disable Redis for Local Development
# This script disables Redis and uses file/database alternatives

Write-Host "Disabling Redis for local development..." -ForegroundColor Cyan

# Check if .env exists
if (-not (Test-Path ".env")) {
    Write-Host "Error: .env file not found!" -ForegroundColor Red
    exit 1
}

# Backup current .env
Copy-Item ".env" ".env.backup.$(Get-Date -Format 'yyyyMMdd_HHmmss')" -Force
Write-Host "Created backup of .env" -ForegroundColor Green

# Update configuration to disable Redis
$envContent = Get-Content ".env" -Raw

# Use file-based alternatives
$envContent = $envContent -replace 'CACHE_STORE=redis', 'CACHE_STORE=file'
$envContent = $envContent -replace 'SESSION_DRIVER=redis', 'SESSION_DRIVER=file'
$envContent = $envContent -replace 'QUEUE_CONNECTION=redis', 'QUEUE_CONNECTION=database'

# Save updated .env
Set-Content ".env" $envContent -NoNewline

Write-Host "`nRedis disabled, using alternatives:" -ForegroundColor Green
Write-Host "  - CACHE_STORE: file" -ForegroundColor Yellow
Write-Host "  - SESSION_DRIVER: file" -ForegroundColor Yellow
Write-Host "  - QUEUE_CONNECTION: database" -ForegroundColor Yellow

# Clear Laravel caches
Write-Host "`nClearing Laravel caches..." -ForegroundColor Cyan
php artisan config:clear
php artisan cache:clear

# Run migrations for queue table if needed
Write-Host "`nSetting up database queue table..." -ForegroundColor Cyan
php artisan queue:table
php artisan migrate

Write-Host "`nDone! You can now run 'php artisan serve' without Redis." -ForegroundColor Green