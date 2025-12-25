#!/usr/bin/env pwsh
<#
.SYNOPSIS
    ICTServe Docker Setup Script - Addresses composer install issues
.DESCRIPTION
    This script sets up the ICTServe Docker environment with proper composer dependency management.
    It addresses the previous issues where composer install occurred in the container.
.PARAMETER Environment
    Environment to set up: 'production' or 'development' (default: development)
.PARAMETER Rebuild
    Force rebuild of Docker images
.EXAMPLE
    .\scripts\docker\setup-docker.ps1
    .\scripts\docker\setup-docker.ps1 -Environment production -Rebuild
#>

param(
    [ValidateSet('development', 'production')]
    [string]$Environment = 'development',
    [switch]$Rebuild
)

# Set error handling
$ErrorActionPreference = 'Stop'

Write-Host "🐳 ICTServe Docker Setup" -ForegroundColor Cyan
Write-Host "Environment: $Environment" -ForegroundColor Yellow

# Check prerequisites
Write-Host "`n📋 Checking prerequisites..." -ForegroundColor Green

# Check Docker
try {
    $dockerVersion = docker --version
    Write-Host "✅ Docker: $dockerVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker not found. Please install Docker Desktop." -ForegroundColor Red
    exit 1
}

# Check Docker Compose
try {
    $composeVersion = docker compose version
    Write-Host "✅ Docker Compose: $composeVersion" -ForegroundColor Green
} catch {
    Write-Host "❌ Docker Compose not found. Please update Docker Desktop." -ForegroundColor Red
    exit 1
}

# Verify required files
$requiredFiles = @(
    'Dockerfile',
    'compose.yaml',
    'composer.json',
    'composer.lock',
    'package.json',
    'package-lock.json',
    '.env.docker'
)

foreach ($file in $requiredFiles) {
    if (!(Test-Path $file)) {
        Write-Host "❌ Required file missing: $file" -ForegroundColor Red
        exit 1
    }
}
Write-Host "✅ All required files present" -ForegroundColor Green

# Stop existing containers
Write-Host "`n🛑 Stopping existing containers..." -ForegroundColor Yellow
docker compose down -v 2>$null

# Clean up if rebuild requested
if ($Rebuild) {
    Write-Host "🧹 Cleaning up existing images..." -ForegroundColor Yellow
    docker compose down --rmi all -v 2>$null
    docker system prune -f 2>$null
}

# Build and start services based on environment
Write-Host "`n🔨 Building and starting services..." -ForegroundColor Green

if ($Environment -eq 'development') {
    Write-Host "Starting development environment with dev dependencies..." -ForegroundColor Yellow
    
    # Build with development dependencies
    docker compose -f compose.yaml -f compose.dev.yaml build --no-cache
    
    # Start services
    docker compose -f compose.yaml -f compose.dev.yaml up -d
    
    $composeFiles = "-f compose.yaml -f compose.dev.yaml"
} else {
    Write-Host "Starting production environment..." -ForegroundColor Yellow
    
    # Build production image
    docker compose build --no-cache
    
    # Start services
    docker compose up -d
    
    $composeFiles = ""
}

# Wait for services to be ready
Write-Host "`n⏳ Waiting for services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Check service status
Write-Host "`n📊 Checking service status..." -ForegroundColor Green
$services = docker compose ps --format json | ConvertFrom-Json

foreach ($service in $services) {
    $status = $service.State
    $name = $service.Service
    
    if ($status -eq 'running') {
        Write-Host "✅ $name is running" -ForegroundColor Green
    } else {
        Write-Host "❌ $name is $status" -ForegroundColor Red
    }
}

# Initialize Laravel application
Write-Host "`n🚀 Initializing Laravel application..." -ForegroundColor Green

# Generate application key if needed
Write-Host "Generating application key..." -ForegroundColor Yellow
docker compose exec app php artisan key:generate --force

# Run database migrations
Write-Host "Running database migrations..." -ForegroundColor Yellow
docker compose exec app php artisan migrate --force

# Seed database if development
if ($Environment -eq 'development') {
    Write-Host "Seeding database..." -ForegroundColor Yellow
    docker compose exec app php artisan db:seed --force
}

# Clear and cache configuration
Write-Host "Optimizing application..." -ForegroundColor Yellow
docker compose exec app php artisan config:clear
docker compose exec app php artisan route:clear
docker compose exec app php artisan view:clear

if ($Environment -eq 'production') {
    docker compose exec app php artisan config:cache
    docker compose exec app php artisan route:cache
    docker compose exec app php artisan view:cache
}

# Build frontend assets
Write-Host "`n🎨 Building frontend assets..." -ForegroundColor Green

if ($Environment -eq 'development') {
    Write-Host "Installing npm dependencies..." -ForegroundColor Yellow
    docker compose exec app npm ci
    
    Write-Host "Building development assets..." -ForegroundColor Yellow
    docker compose exec app npm run build
} else {
    Write-Host "Building production assets..." -ForegroundColor Yellow
    docker compose exec app npm ci --only=production
    docker compose exec app npm run build
}

# Create admin user for development
if ($Environment -eq 'development') {
    Write-Host "`n👤 Creating admin user..." -ForegroundColor Green
    Write-Host "Creating default admin user (admin@motac.gov.my / password)..." -ForegroundColor Yellow
    
    $createUserScript = @"
use App\Models\User;
use Illuminate\Support\Facades\Hash;

`$user = User::firstOrCreate(
    ['email' => 'admin@motac.gov.my'],
    [
        'name' => 'System Administrator',
        'password' => Hash::make('password'),
        'email_verified_at' => now(),
    ]
);

`$user->assignRole('superuser');
echo "Admin user created: admin@motac.gov.my / password\n";
"@

    $createUserScript | docker compose exec -T app php artisan tinker
}

# Display service information
Write-Host "`n🌐 Service Information:" -ForegroundColor Cyan
Write-Host "Application: http://localhost:8000" -ForegroundColor White
Write-Host "Admin Panel: http://localhost:8000/admin" -ForegroundColor White

if ($Environment -eq 'development') {
    Write-Host "Vite Dev Server: http://localhost:5173" -ForegroundColor White
    Write-Host "Reverb WebSocket: ws://localhost:8080" -ForegroundColor White
}

# Display credentials
if ($Environment -eq 'development') {
    Write-Host "`n🔑 Development Credentials:" -ForegroundColor Cyan
    Write-Host "Admin: admin@motac.gov.my / password" -ForegroundColor White
    Write-Host "Staff: staff@motac.gov.my / password" -ForegroundColor White
    Write-Host "Approver: approver@motac.gov.my / password" -ForegroundColor White
}

# Display useful commands
Write-Host "`n🛠️  Useful Commands:" -ForegroundColor Cyan
Write-Host "View logs: docker compose logs -f app" -ForegroundColor White
Write-Host "Shell access: docker compose exec app sh" -ForegroundColor White
Write-Host "Run artisan: docker compose exec app php artisan <command>" -ForegroundColor White
Write-Host "Stop services: docker compose down" -ForegroundColor White

if ($Environment -eq 'development') {
    Write-Host "Start Vite: docker compose exec vite npm run dev" -ForegroundColor White
}

Write-Host "`n✅ Docker setup complete!" -ForegroundColor Green
Write-Host "The application is now running with proper composer dependency management." -ForegroundColor Yellow