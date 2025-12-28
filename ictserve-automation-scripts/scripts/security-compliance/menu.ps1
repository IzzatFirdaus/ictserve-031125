#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Security & Compliance Testing Menu
.DESCRIPTION
    Provides an interactive menu for running security and compliance automation scripts
    including CSRF protection, PDPA compliance, and penetration testing.
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"

function Show-SecurityComplianceMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    Security & Compliance Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "SECURITY VALIDATION:" -ForegroundColor Green
    Write-Host "  1.  Test CSRF Protection" -ForegroundColor White
    Write-Host "  2.  Test Input Sanitization" -ForegroundColor White
    Write-Host "  3.  Test XSS Protection" -ForegroundColor White
    Write-Host "  4.  Test SQL Injection Protection" -ForegroundColor White
    Write-Host "  5.  Test Authentication Security" -ForegroundColor White
    Write-Host "  6.  Test Authorization Enforcement" -ForegroundColor White
    Write-Host "  7.  Test Session Security" -ForegroundColor White
    Write-Host "  8.  Test Password Hashing" -ForegroundColor White
    Write-Host "  9.  Test Rate Limiting" -ForegroundColor White
    Write-Host "  10. Test Security Headers" -ForegroundColor White
    Write-Host ""
    
    Write-Host "PDPA COMPLIANCE:" -ForegroundColor Green
    Write-Host "  11. Test Data Protection" -ForegroundColor White
    Write-Host "  12. Test Audit Logging" -ForegroundColor White
    Write-Host "  13. Test Data Retention" -ForegroundColor White
    Write-Host "  14. Test Consent Management" -ForegroundColor White
    Write-Host "  15. Test Data Export (Right to Access)" -ForegroundColor White
    Write-Host "  16. Test Data Deletion (Right to Erasure)" -ForegroundColor White
    Write-Host "  17. Test Privacy Settings" -ForegroundColor White
    Write-Host "  18. Test Data Encryption" -ForegroundColor White
    Write-Host ""
    
    Write-Host "FILE SECURITY:" -ForegroundColor Green
    Write-Host "  21. Test File Upload Security" -ForegroundColor White
    Write-Host "  22. Test Malware Protection (ClamAV)" -ForegroundColor White
    Write-Host "  23. Test File Type Validation" -ForegroundColor White
    Write-Host "  24. Test File Size Limits" -ForegroundColor White
    Write-Host "  25. Test Secure File Storage" -ForegroundColor White
    Write-Host ""
    
    Write-Host "PENETRATION TESTING:" -ForegroundColor Green
    Write-Host "  31. Test SQL Injection Vectors" -ForegroundColor White
    Write-Host "  32. Test XSS Vectors" -ForegroundColor White
    Write-Host "  33. Test CSRF Bypass Attempts" -ForegroundColor White
    Write-Host "  34. Test Authentication Bypass" -ForegroundColor White
    Write-Host "  35. Test Privilege Escalation" -ForegroundColor White
    Write-Host "  36. Test Information Disclosure" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  50. Run All Security Validation Tests" -ForegroundColor White
    Write-Host "  51. Run All PDPA Compliance Tests" -ForegroundColor White
    Write-Host "  52. Run All File Security Tests" -ForegroundColor White
    Write-Host "  53. Run All Penetration Tests" -ForegroundColor White
    Write-Host "  54. Run Complete Security Suite (All 52 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "  M.  Change Execution Mode | H. Help | S. Search | 0. Back" -ForegroundColor Cyan
    Write-Host ""
}

function Start-SecurityComplianceMenu {
    param([string]$InitialMode = 'Visual')
    $currentMode = $InitialMode
    
    do {
        Show-SecurityComplianceMenu -CurrentMode $currentMode
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

if ($MyInvocation.InvocationName -ne '.') { Start-SecurityComplianceMenu -InitialMode $Mode }
