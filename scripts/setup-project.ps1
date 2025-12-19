#!/usr/bin/env pwsh
# Laravel Project Setup Script
# Run this after cloning the repository

Write-Host "🚀 Setting up ICTServe Laravel Project..." -ForegroundColor Green

# Check if required tools are installed
Write-Host "📋 Checking prerequisites..." -ForegroundColor Yellow

# Check PHP
try {
    $phpVersion = php -v
    Write-Host "✅ PHP found: $($phpVersion.Split("`n")[0])" -ForegroundColor Green
} catch {
    Write-Host "❌ PHP not found. Please install PHP 8.2+" -ForegroundColor Red
    exit 1
}

# Check Composer
try {
    $composerVersion = composer --version
    Write-Host "✅ Composer found: $composerVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Composer not found. Please install Composer" -ForegroundColor Red
    exit 1
}

# Check Node.js
try {
    $nodeVersion = node --version
    Write-Host "✅ Node.js found: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Node.js not found. Please install Node.js 18+" -ForegroundColor Red
    exit 1
}

Write-Host "`n📦 Installing dependencies..." -ForegroundColor Yellow

# Install PHP dependencies (with platform requirement workarounds for Windows)
Write-Host "Installing Composer dependencies..."
$composerResult = composer install --ignore-platform-req=ext-intl --ignore-platform-req=ext-pcntl --ignore-platform-req=ext-zip --ignore-platform-req=ext-gd --ignore-platform-req=ext-posix --no-dev --optimize-autoloader
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Composer install failed. Please check PHP extensions." -ForegroundColor Red
    Write-Host "💡 Consider installing missing PHP extensions or run with --ignore-platform-reqs" -ForegroundColor Yellow
}

# Install Node.js dependencies (with force flag to handle Windows permission issues)
Write-Host "Installing NPM dependencies..."
$npmResult = npm install --force
if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️  NPM install had issues, trying alternative approach..." -ForegroundColor Yellow
    npm ci --force
}

Write-Host "`n🔧 Setting up environment..." -ForegroundColor Yellow

# Copy environment file
if (!(Test-Path ".env")) {
    Copy-Item ".env.example" ".env"
    Write-Host "✅ Created .env file from .env.example" -ForegroundColor Green
} else {
    Write-Host "⚠️  .env file already exists, skipping..." -ForegroundColor Yellow
}

# Generate application key
Write-Host "Generating application key..."
php artisan key:generate

Write-Host "`n🗄️  Setting up database..." -ForegroundColor Yellow

# Create storage directories
Write-Host "Creating storage directories..."
$storageDirs = @(
    "storage/app/public",
    "storage/framework/cache/data",
    "storage/framework/sessions",
    "storage/framework/testing",
    "storage/framework/views",
    "storage/logs"
)

foreach ($dir in $storageDirs) {
    if (!(Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
        Write-Host "✅ Created directory: $dir" -ForegroundColor Green
    }
}

# Set proper permissions (Windows)
Write-Host "Setting storage permissions..."
icacls storage /grant Everyone:F /T | Out-Null
icacls bootstrap/cache /grant Everyone:F /T | Out-Null

# Create storage symlink
Write-Host "Creating storage symlink..."
php artisan storage:link

Write-Host "`n🎨 Building frontend assets..." -ForegroundColor Yellow
npx vite build

Write-Host "`n🧹 Optimizing application..." -ForegroundColor Yellow

# Clear any existing caches first
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Cache configuration and routes (skip view cache due to Filament component issues)
php artisan config:cache
php artisan route:cache

# Try view cache, but don't fail if it has issues
Write-Host "Attempting view cache (may skip if Filament components have issues)..."
$viewCacheResult = php artisan view:cache 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "⚠️  View cache skipped due to component issues (this is OK for development)" -ForegroundColor Yellow
}

Write-Host "`n✅ Project setup complete!" -ForegroundColor Green
Write-Host "🌐 You can now run: php artisan serve" -ForegroundColor Cyan
Write-Host "📊 Admin panel: http://127.0.0.1:8000/admin" -ForegroundColor Cyan
