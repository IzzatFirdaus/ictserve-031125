#!/usr/bin/env pwsh
# Development Environment Setup Script
# Run this for development-specific setup

Write-Host "🛠️  Setting up ICTServe Development Environment..." -ForegroundColor Green

Write-Host "`n📦 Installing development dependencies..." -ForegroundColor Yellow

# Install PHP dependencies with dev packages
Write-Host "Installing Composer dependencies (with dev packages)..."
composer install

# Install Node.js dependencies
Write-Host "Installing NPM dependencies..."
npm install

Write-Host "`n🔧 Development environment configuration..." -ForegroundColor Yellow

# Copy development environment file
if (!(Test-Path ".env")) {
    if (Test-Path ".env.local") {
        Copy-Item ".env.local" ".env"
        Write-Host "✅ Created .env from .env.local" -ForegroundColor Green
    } else {
        Copy-Item ".env.example" ".env"
        Write-Host "✅ Created .env from .env.example" -ForegroundColor Green
    }
} else {
    Write-Host "⚠️  .env file already exists" -ForegroundColor Yellow
}

# Generate application key if needed
$envContent = Get-Content ".env" -Raw
if ($envContent -notmatch "APP_KEY=base64:") {
    Write-Host "Generating application key..."
    php artisan key:generate
}

Write-Host "`n🗄️  Database setup..." -ForegroundColor Yellow

# Run migrations
Write-Host "Running database migrations..."
php artisan migrate

# Seed database if needed
$seedChoice = Read-Host "Do you want to seed the database with sample data? (y/N)"
if ($seedChoice -eq "y" -or $seedChoice -eq "Y") {
    Write-Host "Seeding database..."
    php artisan db:seed
}

Write-Host "`n🎨 Development assets..." -ForegroundColor Yellow

# Build assets in development mode
Write-Host "Building development assets..."
npm run dev

Write-Host "`n🧪 Development tools..." -ForegroundColor Yellow

# Install Laravel Pint (code formatting)
Write-Host "Setting up Laravel Pint..."
if (!(Test-Path "vendor/bin/pint")) {
    composer require laravel/pint --dev
}

# Install PHPStan (static analysis)
Write-Host "Setting up PHPStan..."
if (!(Test-Path "vendor/bin/phpstan")) {
    composer require larastan/larastan --dev
}

Write-Host "`n🔍 Running quality checks..." -ForegroundColor Yellow

# Format code
Write-Host "Formatting code with Pint..."
vendor/bin/pint

# Run static analysis
Write-Host "Running PHPStan analysis..."
vendor/bin/phpstan analyse --no-progress

# Run tests
Write-Host "Running tests..."
php artisan test

Write-Host "`n✅ Development environment setup complete!" -ForegroundColor Green
Write-Host "🌐 Start development server: php artisan serve" -ForegroundColor Cyan
Write-Host "🎨 Watch assets: npm run dev" -ForegroundColor Cyan
Write-Host "📊 Admin panel: http://127.0.0.1:8000/admin" -ForegroundColor Cyan
Write-Host "🧪 Run tests: php artisan test" -ForegroundColor Cyan
Write-Host "🎯 Format code: vendor/bin/pint" -ForegroundColor Cyan
