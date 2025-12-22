#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Start ICTServe XAMPP services

.DESCRIPTION
    Starts all required services for ICTServe XAMPP development environment

.PARAMETER Profile
    Service profile to start: minimal, backend, frontend, full (default)

.EXAMPLE
    .\deployment\xampp\scripts\start-services.ps1
    Start all services

.EXAMPLE
    .\deployment\xampp\scripts\start-services.ps1 -Profile backend
    Start backend services only
#>

param(
    [ValidateSet('minimal', 'backend', 'frontend', 'full')]
    [string]$Profile = 'full'
)

function Write-Success { param($Message) Write-Host $Message -ForegroundColor Green }
function Write-Info { param($Message) Write-Host $Message -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host $Message -ForegroundColor Yellow }

Write-Info "=== ICTServe XAMPP Services ==="
Write-Info "Profile: $Profile"
Write-Info "Starting services..."

# Check if we're in the correct directory
if (-not (Test-Path "artisan")) {
    Write-Warning "Please run this script from the ICTServe root directory"
    exit 1
}

# Service profiles
$services = @{
    'minimal' = @('laravel')
    'backend' = @('laravel', 'reverb', 'queue')
    'frontend' = @('laravel', 'vite')
    'full' = @('laravel', 'reverb', 'queue', 'vite')
}

$selectedServices = $services[$Profile]

Write-Info "`nServices to start: $($selectedServices -join ', ')"
Write-Info "Press Ctrl+C to stop all services`n"

# Start services based on profile
$jobs = @()

if ($selectedServices -contains 'laravel') {
    Write-Info "Starting Laravel server (http://127.0.0.1:8000)..."
    $jobs += Start-Job -ScriptBlock {
        Set-Location $using:PWD
        php artisan serve --host=127.0.0.1 --port=8000
    }
    Start-Sleep -Seconds 2
}

if ($selectedServices -contains 'reverb') {
    Write-Info "Starting Laravel Reverb (ws://127.0.0.1:8080)..."
    $jobs += Start-Job -ScriptBlock {
        Set-Location $using:PWD
        php artisan reverb:start --host=0.0.0.0 --port=8080
    }
    Start-Sleep -Seconds 2
}

if ($selectedServices -contains 'queue') {
    Write-Info "Starting Queue Worker..."
    $jobs += Start-Job -ScriptBlock {
        Set-Location $using:PWD
        php artisan queue:work --tries=3 --timeout=90
    }
    Start-Sleep -Seconds 1
}

if ($selectedServices -contains 'vite') {
    Write-Info "Starting Vite Dev Server (http://127.0.0.1:5173)..."
    $jobs += Start-Job -ScriptBlock {
        Set-Location $using:PWD
        npm run dev
    }
    Start-Sleep -Seconds 2
}

Write-Success "`nAll services started!"
Write-Info "`nAccess URLs:"
Write-Info "- Application: http://127.0.0.1:8000"
Write-Info "- Admin Panel: http://127.0.0.1:8000/admin"
Write-Info "- Vite HMR: http://127.0.0.1:5173"
Write-Info "`nPress any key to stop all services..."

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Stop all jobs
Write-Info "`nStopping services..."
$jobs | Stop-Job
$jobs | Remove-Job

Write-Success "All services stopped"