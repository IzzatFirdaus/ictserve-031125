#!/usr/bin/env pwsh
<#
.SYNOPSIS
    API Integration & Backend Testing Menu
.DESCRIPTION
    Provides an interactive menu for running API and backend system automation scripts
    including Sanctum API, HRMIS integration, email gateway, and database testing.
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"

function Show-APIBackendMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    API Integration & Backend System Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "LARAVEL SANCTUM API TESTING:" -ForegroundColor Green
    Write-Host "  1.  Test API Authentication" -ForegroundColor White
    Write-Host "  2.  Test API Token Management" -ForegroundColor White
    Write-Host "  3.  Test API Rate Limiting" -ForegroundColor White
    Write-Host "  4.  Test API Permission System" -ForegroundColor White
    Write-Host "  5.  Test API CORS Configuration" -ForegroundColor White
    Write-Host ""
    
    Write-Host "HRMIS INTEGRATION TESTING:" -ForegroundColor Green
    Write-Host "  11. Test HRMIS Connectivity" -ForegroundColor White
    Write-Host "  12. Test User Data Synchronization" -ForegroundColor White
    Write-Host "  13. Test Grade Verification" -ForegroundColor White
    Write-Host "  14. Test Department Mapping" -ForegroundColor White
    Write-Host "  15. Test HRMIS Error Handling" -ForegroundColor White
    Write-Host ""
    
    Write-Host "EMAIL GATEWAY INTEGRATION:" -ForegroundColor Green
    Write-Host "  21. Test SMTP Configuration" -ForegroundColor White
    Write-Host "  22. Test Email Template System" -ForegroundColor White
    Write-Host "  23. Test Email Queue Processing" -ForegroundColor White
    Write-Host "  24. Test Email Delivery Confirmation" -ForegroundColor White
    Write-Host "  25. Test Email Bounce Handling" -ForegroundColor White
    Write-Host ""
    
    Write-Host "DATABASE & CACHING:" -ForegroundColor Green
    Write-Host "  31. Test Database Connectivity" -ForegroundColor White
    Write-Host "  32. Test Transaction Integrity" -ForegroundColor White
    Write-Host "  33. Test Redis Connectivity" -ForegroundColor White
    Write-Host "  34. Test Session Storage" -ForegroundColor White
    Write-Host "  35. Test Cache Operations" -ForegroundColor White
    Write-Host ""
    
    Write-Host "WEBSOCKET & REAL-TIME:" -ForegroundColor Green
    Write-Host "  41. Test WebSocket Server (Laravel Reverb)" -ForegroundColor White
    Write-Host "  42. Test Real-time Notifications" -ForegroundColor White
    Write-Host "  43. Test Broadcasting Events" -ForegroundColor White
    Write-Host "  44. Test Presence Channels" -ForegroundColor White
    Write-Host ""
    
    Write-Host "QUEUE MONITORING:" -ForegroundColor Green
    Write-Host "  51. Test Horizon Dashboard" -ForegroundColor White
    Write-Host "  52. Test Job Processing" -ForegroundColor White
    Write-Host "  53. Test Failed Jobs Handling" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  80. Run All Sanctum API Tests" -ForegroundColor White
    Write-Host "  81. Run All HRMIS Tests" -ForegroundColor White
    Write-Host "  82. Run All Email Gateway Tests" -ForegroundColor White
    Write-Host "  83. Run All Database Tests" -ForegroundColor White
    Write-Host "  84. Run All WebSocket Tests" -ForegroundColor White
    Write-Host "  85. Run Complete API Backend Suite (All 89 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "  M.  Change Execution Mode | H. Help | S. Search | 0. Back" -ForegroundColor Cyan
    Write-Host ""
}

function Start-APIBackendMenu {
    param([string]$InitialMode = 'Visual')
    $currentMode = $InitialMode
    
    do {
        Show-APIBackendMenu -CurrentMode $currentMode
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

if ($MyInvocation.InvocationName -ne '.') { Start-APIBackendMenu -InitialMode $Mode }
