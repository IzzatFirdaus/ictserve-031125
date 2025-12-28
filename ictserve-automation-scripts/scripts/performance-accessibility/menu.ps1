#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Performance & Accessibility Testing Menu
.DESCRIPTION
    Provides an interactive menu for running performance and accessibility automation scripts
    including Core Web Vitals, WCAG compliance, and cross-browser testing.
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"

function Show-PerformanceAccessibilityMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    Performance & Accessibility Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "CORE WEB VITALS:" -ForegroundColor Green
    Write-Host "  1.  Test LCP (Largest Contentful Paint)" -ForegroundColor White
    Write-Host "  2.  Test FID (First Input Delay)" -ForegroundColor White
    Write-Host "  3.  Test CLS (Cumulative Layout Shift)" -ForegroundColor White
    Write-Host "  4.  Test TTFB (Time to First Byte)" -ForegroundColor White
    Write-Host "  5.  Test Page Load Performance" -ForegroundColor White
    Write-Host "  6.  Test Lighthouse Scores" -ForegroundColor White
    Write-Host "  7.  Test Resource Loading" -ForegroundColor White
    Write-Host "  8.  Test JavaScript Execution Time" -ForegroundColor White
    Write-Host ""
    
    Write-Host "WCAG 2.2 AA COMPLIANCE:" -ForegroundColor Green
    Write-Host "  11. Test Keyboard Navigation" -ForegroundColor White
    Write-Host "  12. Test Screen Reader Compatibility" -ForegroundColor White
    Write-Host "  13. Test Color Contrast" -ForegroundColor White
    Write-Host "  14. Test Focus Indicators" -ForegroundColor White
    Write-Host "  15. Test ARIA Labels" -ForegroundColor White
    Write-Host "  16. Test Form Accessibility" -ForegroundColor White
    Write-Host "  17. Test Image Alt Text" -ForegroundColor White
    Write-Host "  18. Test Link Text" -ForegroundColor White
    Write-Host "  19. Test Heading Structure" -ForegroundColor White
    Write-Host "  20. Test Skip Links" -ForegroundColor White
    Write-Host ""
    
    Write-Host "MOBILE & RESPONSIVE:" -ForegroundColor Green
    Write-Host "  21. Test Mobile Viewport" -ForegroundColor White
    Write-Host "  22. Test Touch Interactions" -ForegroundColor White
    Write-Host "  23. Test Responsive Breakpoints" -ForegroundColor White
    Write-Host "  24. Test Mobile Navigation" -ForegroundColor White
    Write-Host "  25. Test Mobile Forms" -ForegroundColor White
    Write-Host ""
    
    Write-Host "CROSS-BROWSER TESTING:" -ForegroundColor Green
    Write-Host "  31. Test Chrome Compatibility" -ForegroundColor White
    Write-Host "  32. Test Firefox Compatibility" -ForegroundColor White
    Write-Host "  33. Test Edge Compatibility" -ForegroundColor White
    Write-Host "  34. Test Safari Compatibility" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  40. Run All Core Web Vitals Tests" -ForegroundColor White
    Write-Host "  41. Run All WCAG Compliance Tests" -ForegroundColor White
    Write-Host "  42. Run All Mobile Tests" -ForegroundColor White
    Write-Host "  43. Run All Cross-Browser Tests" -ForegroundColor White
    Write-Host "  44. Run Complete Performance Suite (All 45 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "  M.  Change Execution Mode | H. Help | S. Search | 0. Back" -ForegroundColor Cyan
    Write-Host ""
}

function Start-PerformanceAccessibilityMenu {
    param([string]$InitialMode = 'Visual')
    $currentMode = $InitialMode
    
    do {
        Show-PerformanceAccessibilityMenu -CurrentMode $currentMode
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

if ($MyInvocation.InvocationName -ne '.') { Start-PerformanceAccessibilityMenu -InitialMode $Mode }
