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

# Install PHP dependencies
Write-Host "Installing Composer dependencies..."
composer install --no-dev --optimize-autoloader

# Install Node.js dependencies
Write-Host "Installing NPM dependencies..."
npm ci

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
npm run build

Write-Host "`n🧹 Optimizing application..." -ForegroundColor Yellow

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

Write-Host "`n✅ Project setup complete!" -ForegroundColor Green
Write-Host "🌐 You can now run: php artisan serve" -ForegroundColor Cyan
Write-Host "📊 Admin panel: http://127.0.0.1:8000/admin" -ForegroundColor Cyan