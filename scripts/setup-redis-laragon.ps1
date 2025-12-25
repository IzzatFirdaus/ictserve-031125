#Requires -Version 5.1
<#
.SYNOPSIS
    Quick Redis Setup for ICTServe on Laragon

.DESCRIPTION
    One-command setup for optimal Redis configuration on Laragon.
    Installs Predis, configures environment, and starts Redis service.

.PARAMETER Force
    Force overwrite existing configuration

.EXAMPLE
    .\scripts\setup-redis-laragon.ps1 -Force
    Quick Redis setup with force overwrite

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: Laragon, PowerShell 5.1+
#>

[CmdletBinding()]
param(
    [switch]$Force
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

try {
    Write-Host "`n🚀 ICTServe Redis Quick Setup for Laragon" -ForegroundColor Cyan
    Write-Host "=" * 45 -ForegroundColor Cyan
    Write-Host ""

    # Step 1: Run Redis optimization
    Write-Host "🔧 Running Redis optimization..." -ForegroundColor Yellow
    if ($Force) {
        & ".\scripts\laragon\optimize-redis-laragon.ps1" -Force
    }
    else {
        & ".\scripts\laragon\optimize-redis-laragon.ps1"
    }

    # Step 2: Run health check
    Write-Host "`n🔍 Running health check..." -ForegroundColor Yellow
    & ".\scripts\laragon\redis-health-check.ps1" -Fix -Detailed

    Write-Host "`n✅ Redis setup completed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "🎯 Quick Test Commands:" -ForegroundColor Cyan
    Write-Host "  php artisan tinker --execute=\"echo Redis::ping();\"" -ForegroundColor White
    Write-Host "  redis-cli ping" -ForegroundColor White
    Write-Host ""
    Write-Host "📖 For detailed documentation:" -ForegroundColor Cyan
    Write-Host "  docs\redis\LARAGON_REDIS_SETUP.md" -ForegroundColor White
    Write-Host ""

}
catch {
    Write-Host "`n❌ Setup failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "🔧 Try manual steps:" -ForegroundColor Yellow
    Write-Host "  1. Install Redis in Laragon (Quick Add > Redis)" -ForegroundColor White
    Write-Host "  2. Run: composer require predis/predis" -ForegroundColor White
    Write-Host "  3. Set REDIS_CLIENT=predis in .env" -ForegroundColor White
    Write-Host "  4. Restart Laragon" -ForegroundColor White
    Write-Host ""
    exit 1
}