#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Quick environment switcher for ICTServe development

.DESCRIPTION
    This script provides a simple menu-driven interface to quickly switch
    between XAMPP and Docker environments, start/stop services, and check status.

.EXAMPLE
    .\scripts\quick-switch.ps1

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: PowerShell 5.1+
#>

[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }
function Write-Menu { param([string]$Message) Write-Host $Message -ForegroundColor White }

$ProjectRoot = Split-Path -Parent $PSScriptRoot

# Show main menu
function Show-MainMenu {
    Clear-Host
    Write-Host "`n" + "="*60 -ForegroundColor Blue
    Write-Host "  ICTServe Development Environment Quick Switcher" -ForegroundColor Blue
    Write-Host "="*60 -ForegroundColor Blue
    
    Write-Menu "`n📋 Environment Management:"
    Write-Menu "  1. Check Environment Status"
    Write-Menu "  2. Switch to Docker Environment"
    Write-Menu "  3. Switch to XAMPP Environment"
    
    Write-Menu "`n🐳 Docker Services:"
    Write-Menu "  4. Start Docker Services"
    Write-Menu "  5. Stop Docker Services"
    
    Write-Menu "`n🔧 XAMPP Services:"
    Write-Menu "  6. Start XAMPP Services"
    Write-Menu "  7. Stop XAMPP Services"
    
    Write-Menu "`n🚀 Laravel Services:"
    Write-Menu "  8. Start Laravel Dev Server"
    Write-Menu "  9. Start Reverb WebSocket Server"
    Write-Menu "  10. Stop Laravel Services"
    
    Write-Menu "`n❓ Help & Information:"
    Write-Menu "  11. Show Help"
    Write-Menu "  12. Show Service URLs"
    
    Write-Menu "`n  0. Exit"
    
    Write-Host "`n" + "="*60 -ForegroundColor Blue
}

# Get current environment info
function Get-QuickEnvironmentInfo {
    $envFile = "$ProjectRoot\.env"
    if (-not (Test-Path $envFile)) {
        return "Not Configured"
    }
    
    $envContent = Get-Content $envFile -ErrorAction SilentlyContinue
    $dbHost = ($envContent | Where-Object { $_ -match '^DB_HOST=' }) -replace '^DB_HOST=', ''
    
    switch ($dbHost) {
        'db' { return "Docker" }
        '127.0.0.1' { return "XAMPP" }
        default { return "Unknown" }
    }
}

# Execute menu choice
function Invoke-MenuChoice {
    param([string]$Choice)
    
    switch ($Choice) {
        '1' {
            Write-Info "Checking environment status..."
            & "$ProjectRoot\scripts\environment-status.ps1" -ShowDetails -CheckConnectivity
        }
        '2' {
            Write-Info "Switching to Docker environment..."
            & "$ProjectRoot\scripts\swap-environment.ps1" -Environment docker
        }
        '3' {
            Write-Info "Switching to XAMPP environment..."
            & "$ProjectRoot\scripts\swap-environment.ps1" -Environment xampp
        }
        '4' {
            Write-Info "Starting Docker services..."
            & "$ProjectRoot\scripts\docker\start-docker-services.ps1"
        }
        '5' {
            Write-Info "Stopping Docker services..."
            & "$ProjectRoot\scripts\docker\stop-docker-services.ps1"
        }
        '6' {
            Write-Info "Starting XAMPP services..."
            & "$ProjectRoot\scripts\xampp\start-xampp-services.ps1"
        }
        '7' {
            Write-Info "Stopping XAMPP services..."
            & "$ProjectRoot\scripts\xampp\stop-xampp-services.ps1"
        }
        '8' {
            Write-Info "Starting Laravel development server..."
            $currentEnv = Get-QuickEnvironmentInfo
            if ($currentEnv -eq "XAMPP") {
                Write-Info "Starting on 127.0.0.1:8000..."
                Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$ProjectRoot'; php artisan serve --host=127.0.0.1"
            } else {
                Write-Info "Starting on localhost:8000..."
                Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$ProjectRoot'; php artisan serve"
            }
            Write-Success "Laravel development server started in new window"
        }
        '9' {
            Write-Info "Starting Reverb WebSocket server..."
            Start-Process powershell -ArgumentList "-NoExit", "-Command", "cd '$ProjectRoot'; php artisan reverb:start"
            Write-Success "Reverb WebSocket server started in new window"
        }
        '10' {
            Write-Info "Stopping Laravel services..."
            $laravelProcesses = Get-Process -Name "php" -ErrorAction SilentlyContinue | 
                Where-Object { $_.CommandLine -like "*artisan*" }
            
            if ($laravelProcesses) {
                $laravelProcesses | Stop-Process -Force
                Write-Success "Laravel services stopped"
            } else {
                Write-Info "No Laravel services running"
            }
        }
        '11' {
            Show-Help
        }
        '12' {
            Show-ServiceUrls
        }
        '0' {
            Write-Success "Goodbye!"
            return $false
        }
        default {
            Write-Warning "Invalid choice. Please try again."
        }
    }
    
    return $true
}

# Show help information
function Show-Help {
    Clear-Host
    Write-Host "`n" + "="*60 -ForegroundColor Green
    Write-Host "  ICTServe Environment Switcher Help" -ForegroundColor Green
    Write-Host "="*60 -ForegroundColor Green
    
    Write-Info "`n🎯 Purpose:"
    Write-Menu "This tool helps you manage ICTServe development environments:"
    Write-Menu "  • Switch between XAMPP (local) and Docker environments"
    Write-Menu "  • Start and stop services for each environment"
    Write-Menu "  • Check the status of all services"
    
    Write-Info "`n🐳 Docker Environment:"
    Write-Menu "  • Uses containerized services (MySQL, Redis, Nginx)"
    Write-Menu "  • Application runs at: http://localhost:8000"
    Write-Menu "  • Database host: db (container name)"
    Write-Menu "  • Requires Docker Desktop to be running"
    
    Write-Info "`n🔧 XAMPP Environment:"
    Write-Menu "  • Uses local XAMPP/Laragon/WAMP installation"
    Write-Menu "  • Application runs at: http://127.0.0.1:8000"
    Write-Menu "  • Database host: 127.0.0.1"
    Write-Menu "  • Requires XAMPP services to be running"
    
    Write-Info "`n🚀 Laravel Services:"
    Write-Menu "  • Development Server: Serves the application"
    Write-Menu "  • Reverb WebSocket: Real-time features (optional)"
    Write-Menu "  • Horizon: Queue management (optional)"
    
    Write-Info "`n📁 Script Locations:"
    Write-Menu "  • Main switcher: .\scripts\swap-environment.ps1"
    Write-Menu "  • Status checker: .\scripts\environment-status.ps1"
    Write-Menu "  • Docker scripts: .\scripts\docker\"
    Write-Menu "  • XAMPP scripts: .\scripts\xampp\"
    
    Write-Info "`n💡 Tips:"
    Write-Menu "  • Always check status before switching environments"
    Write-Menu "  • Stop services before switching to avoid conflicts"
    Write-Menu "  • Use 127.0.0.1 instead of localhost for XAMPP"
    Write-Menu "  • Docker containers may take time to start"
    
    Write-Host "`nPress any key to return to main menu..."
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
}

# Show service URLs
function Show-ServiceUrls {
    Clear-Host
    Write-Host "`n" + "="*60 -ForegroundColor Green
    Write-Host "  ICTServe Service URLs" -ForegroundColor Green
    Write-Host "="*60 -ForegroundColor Green
    
    $currentEnv = Get-QuickEnvironmentInfo
    Write-Info "Current Environment: $currentEnv"
    
    Write-Info "`n🐳 Docker Environment URLs:"
    Write-Menu "  • Application: http://localhost:8000"
    Write-Menu "  • phpMyAdmin: http://localhost:8080"
    Write-Menu "  • Reverb WebSocket: ws://localhost:8080"
    Write-Menu "  • Database: localhost:3306 (user: laravel, pass: secret)"
    Write-Menu "  • Redis: localhost:6379"
    
    Write-Info "`n🔧 XAMPP Environment URLs:"
    Write-Menu "  • Application: http://127.0.0.1:8000"
    Write-Menu "  • Apache: http://127.0.0.1"
    Write-Menu "  • phpMyAdmin: http://127.0.0.1/phpmyadmin"
    Write-Menu "  • Reverb WebSocket: ws://127.0.0.1:8080"
    Write-Menu "  • Database: 127.0.0.1:3306 (user: root, pass: empty)"
    Write-Menu "  • Redis: 127.0.0.1:6379"
    
    Write-Info "`n🚀 Laravel Development URLs:"
    Write-Menu "  • Horizon Dashboard: /horizon"
    Write-Menu "  • Telescope Debug: /telescope"
    Write-Menu "  • Pulse Monitoring: /pulse"
    Write-Menu "  • Filament Admin: /admin"
    
    Write-Info "`n📊 API Endpoints:"
    Write-Menu "  • Health Check: /api/health"
    Write-Menu "  • API Documentation: /api/documentation"
    
    Write-Host "`nPress any key to return to main menu..."
    $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
}

# Main execution loop
function Main {
    try {
        do {
            Show-MainMenu
            
            $currentEnv = Get-QuickEnvironmentInfo
            Write-Info "Current Environment: $currentEnv"
            
            $choice = Read-Host "`nEnter your choice (0-12)"
            $continue = Invoke-MenuChoice -Choice $choice
            
            if ($continue) {
                Write-Host "`nPress any key to continue..."
                $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
            }
            
        } while ($continue)
        
    }
    catch {
        Write-Error "An error occurred: $($_.Exception.Message)"
        Write-Host "Press any key to exit..."
        $null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")
        exit 1
    }
}

# Execute main function
Main
