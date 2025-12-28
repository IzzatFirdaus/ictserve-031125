#!/usr/bin/env pwsh
<#
.SYNOPSIS
    System Monitoring & Health Testing Menu
.DESCRIPTION
    Provides an interactive menu for running system monitoring automation scripts
    including Laravel Pulse, Horizon, Telescope, and system health checks.
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"

function Show-MonitoringHealthMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    System Monitoring & Health Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "LARAVEL PULSE:" -ForegroundColor Green
    Write-Host "  1.  Test Pulse Dashboard Access" -ForegroundColor White
    Write-Host "  2.  Test Performance Metrics" -ForegroundColor White
    Write-Host "  3.  Test Slow Queries Detection" -ForegroundColor White
    Write-Host "  4.  Test Exception Tracking" -ForegroundColor White
    Write-Host "  5.  Test User Activity Monitoring" -ForegroundColor White
    Write-Host "  6.  Test Server Health Metrics" -ForegroundColor White
    Write-Host ""
    
    Write-Host "LARAVEL HORIZON:" -ForegroundColor Green
    Write-Host "  11. Test Horizon Dashboard Access" -ForegroundColor White
    Write-Host "  12. Test Queue Worker Status" -ForegroundColor White
    Write-Host "  13. Test Job Processing Metrics" -ForegroundColor White
    Write-Host "  14. Test Failed Jobs Management" -ForegroundColor White
    Write-Host "  15. Test Queue Throughput" -ForegroundColor White
    Write-Host "  16. Test Worker Scaling" -ForegroundColor White
    Write-Host "  17. Test Job Retry Functionality" -ForegroundColor White
    Write-Host ""
    
    Write-Host "LARAVEL TELESCOPE:" -ForegroundColor Green
    Write-Host "  21. Test Telescope Dashboard Access" -ForegroundColor White
    Write-Host "  22. Test Request Logging" -ForegroundColor White
    Write-Host "  23. Test Query Logging" -ForegroundColor White
    Write-Host "  24. Test Exception Logging" -ForegroundColor White
    Write-Host "  25. Test Mail Logging" -ForegroundColor White
    Write-Host "  26. Test Cache Operations Logging" -ForegroundColor White
    Write-Host ""
    
    Write-Host "SYSTEM HEALTH:" -ForegroundColor Green
    Write-Host "  31. Test System Status Endpoint" -ForegroundColor White
    Write-Host "  32. Test Database Connectivity" -ForegroundColor White
    Write-Host "  33. Test Redis Connectivity" -ForegroundColor White
    Write-Host "  34. Test Queue Connectivity" -ForegroundColor White
    Write-Host "  35. Test External Service Health" -ForegroundColor White
    Write-Host "  36. Test Disk Space Monitoring" -ForegroundColor White
    Write-Host "  37. Test Memory Usage" -ForegroundColor White
    Write-Host "  38. Test CPU Usage" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  40. Run All Pulse Tests" -ForegroundColor White
    Write-Host "  41. Run All Horizon Tests" -ForegroundColor White
    Write-Host "  42. Run All Telescope Tests" -ForegroundColor White
    Write-Host "  43. Run All System Health Tests" -ForegroundColor White
    Write-Host "  44. Run Complete Monitoring Suite (All 38 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "  M.  Change Execution Mode | H. Help | S. Search | 0. Back" -ForegroundColor Cyan
    Write-Host ""
}

function Start-MonitoringHealthMenu {
    param([string]$InitialMode = 'Visual')
    $currentMode = $InitialMode
    
    do {
        Show-MonitoringHealthMenu -CurrentMode $currentMode
        $selection = Read-Host "Select option"
        
        switch ($selection.ToUpper()) {
            '0' { return }
            'M' {
                $modeChoice = Read-Host "Select mode (1=Headless, 2=Visual, 3=Demo, 4=Interactive, 5=Recording)"
                $currentMode = switch ($modeChoice) { '1' { 'Headless' } '2' { 'Visual' } '3' { 'Demo' } '4' { 'Interactive' } '5' { 'Recording' } default { $currentMode } }
            }
            default { Write-Host "`nScript placeholder" -ForegroundColor Yellow; Start-Sleep -Seconds 1 }
        }
    } while ($true)
}

if ($MyInvocation.InvocationName -ne '.') { Start-MonitoringHealthMenu -InitialMode $Mode }
