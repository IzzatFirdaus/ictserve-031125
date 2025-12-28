#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Guest User Workflows Menu - Interactive menu for guest user automation scripts
.DESCRIPTION
    Provides an interactive menu for running guest user workflow automation scripts
    including helpdesk ticket submission, asset loan requests, and integration tests.
.NOTES
    Part of ICTServe Comprehensive Automation Suite
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual',
    [switch]$ReturnToMain
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

# Import common utilities
. "$ScriptRoot\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\utilities\visual-demo-helpers.ps1"

function Show-GuestWorkflowsMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    Guest User Workflows - Frontend & Backend Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "DEMONSTRATION OPTIONS:" -ForegroundColor Magenta
    Write-Host "  D1. Quick Demo (5 min)      - Essential guest workflows" -ForegroundColor Gray
    Write-Host "  D2. Complete Demo (15 min)  - All guest features" -ForegroundColor Gray
    Write-Host "  D3. Training Session        - Interactive with pauses" -ForegroundColor Gray
    Write-Host "  D4. Video Recording         - Capture for training" -ForegroundColor Gray
    Write-Host ""
    
    Write-Host "HELPDESK TICKET WORKFLOWS:" -ForegroundColor Green
    Write-Host "  1.  Submit Basic Helpdesk Ticket" -ForegroundColor White
    Write-Host "  2.  Submit Ticket with File Attachments" -ForegroundColor White
    Write-Host "  3.  Submit Ticket with Multiple Categories" -ForegroundColor White
    Write-Host "  4.  Test Form Validation Errors" -ForegroundColor White
    Write-Host "  5.  Test CSRF Protection" -ForegroundColor White
    Write-Host "  6.  Track Ticket Status by Number" -ForegroundColor White
    Write-Host "  7.  Track Ticket Status by Email" -ForegroundColor White
    Write-Host "  8.  Test Email Notifications" -ForegroundColor White
    Write-Host "  9.  Test Ticket Auto-Assignment" -ForegroundColor White
    Write-Host "  10. Test Emergency Priority Handling" -ForegroundColor White
    Write-Host ""
    
    Write-Host "ASSET LOAN WORKFLOWS:" -ForegroundColor Green
    Write-Host "  11. Submit Basic Asset Loan Request" -ForegroundColor White
    Write-Host "  12. Check Asset Availability Calendar" -ForegroundColor White
    Write-Host "  13. Submit Loan with Date Conflicts" -ForegroundColor White
    Write-Host "  14. Test Asset Category Selection" -ForegroundColor White
    Write-Host "  15. Test Loan Duration Validation" -ForegroundColor White
    Write-Host "  16. Test Department Asset Restrictions" -ForegroundColor White
    Write-Host "  17. Track Loan Application Status" -ForegroundColor White
    Write-Host "  18. Test Loan Approval Workflow Trigger" -ForegroundColor White
    Write-Host "  19. Test Asset Conflict Detection" -ForegroundColor White
    Write-Host "  20. Test Loan Extension Requests" -ForegroundColor White
    Write-Host ""
    
    Write-Host "INTEGRATION & SYSTEM TESTING:" -ForegroundColor Green
    Write-Host "  21. Test ClamAV File Scanning" -ForegroundColor White
    Write-Host "  22. Test Email Gateway Integration" -ForegroundColor White
    Write-Host "  23. Test Database Transaction Integrity" -ForegroundColor White
    Write-Host "  24. Test Laravel Queue Processing" -ForegroundColor White
    Write-Host "  25. Test Redis Session Management" -ForegroundColor White
    Write-Host ""
    
    Write-Host "PERFORMANCE & ACCESSIBILITY:" -ForegroundColor Green
    Write-Host "  26. Test Page Load Performance (Core Web Vitals)" -ForegroundColor White
    Write-Host "  27. Test Mobile Responsiveness" -ForegroundColor White
    Write-Host "  28. Test Keyboard Navigation" -ForegroundColor White
    Write-Host "  29. Test Screen Reader Compatibility" -ForegroundColor White
    Write-Host ""
    
    Write-Host "ERROR HANDLING & EDGE CASES:" -ForegroundColor Green
    Write-Host "  30. Test Network Timeout Scenarios" -ForegroundColor White
    Write-Host "  31. Test File Upload Size Limits" -ForegroundColor White
    Write-Host "  32. Test Invalid File Type Uploads" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  40. Run All Helpdesk Workflows" -ForegroundColor White
    Write-Host "  41. Run All Asset Loan Workflows" -ForegroundColor White
    Write-Host "  42. Run All Integration Tests" -ForegroundColor White
    Write-Host "  43. Run Critical Path Only" -ForegroundColor White
    Write-Host "  44. Run Complete Guest User Suite (All 50 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "UTILITIES:" -ForegroundColor Cyan
    Write-Host "  M.  Change Execution Mode" -ForegroundColor White
    Write-Host "  H.  Help for this category" -ForegroundColor White
    Write-Host "  S.  Search specific test" -ForegroundColor White
    Write-Host "  0.  Back to Main Menu" -ForegroundColor White
    Write-Host ""
}

function Invoke-GuestWorkflowScript {
    param(
        [int]$ScriptNumber,
        [string]$Mode
    )
    
    $scriptMap = @{
        1  = "helpdesk\submit-basic-ticket.ps1"
        2  = "helpdesk\submit-ticket-with-attachments.ps1"
        3  = "helpdesk\submit-ticket-multiple-categories.ps1"
        4  = "helpdesk\test-form-validation.ps1"
        5  = "helpdesk\test-csrf-protection.ps1"
        6  = "helpdesk\track-ticket-by-number.ps1"
        7  = "helpdesk\track-ticket-by-email.ps1"
        8  = "helpdesk\test-email-notifications.ps1"
        9  = "helpdesk\test-ticket-auto-assignment.ps1"
        10 = "helpdesk\test-emergency-priority.ps1"
        11 = "asset-loans\submit-basic-loan-request.ps1"
        12 = "asset-loans\check-asset-availability.ps1"
        13 = "asset-loans\test-date-conflicts.ps1"
        14 = "asset-loans\test-asset-category-selection.ps1"
        15 = "asset-loans\test-loan-duration-validation.ps1"
        16 = "asset-loans\test-department-restrictions.ps1"
        17 = "asset-loans\track-loan-status.ps1"
        18 = "asset-loans\test-approval-workflow-trigger.ps1"
        19 = "asset-loans\test-asset-conflict-detection.ps1"
        20 = "asset-loans\test-loan-extension-requests.ps1"
        21 = "integration-tests\test-clamav-scanning.ps1"
        22 = "integration-tests\test-email-gateway.ps1"
        23 = "integration-tests\test-database-transactions.ps1"
        24 = "integration-tests\test-queue-processing.ps1"
        25 = "integration-tests\test-redis-sessions.ps1"
        26 = "performance\test-core-web-vitals.ps1"
        27 = "performance\test-mobile-responsiveness.ps1"
        28 = "performance\test-keyboard-navigation.ps1"
        29 = "performance\test-screen-reader.ps1"
        30 = "error-handling\test-network-timeout.ps1"
        31 = "error-handling\test-file-upload-limits.ps1"
        32 = "error-handling\test-invalid-file-types.ps1"
    }
    
    if ($scriptMap.ContainsKey($ScriptNumber)) {
        $scriptPath = Join-Path $ScriptRoot $scriptMap[$ScriptNumber]
        if (Test-Path $scriptPath) {
            Write-Host "`nExecuting: $($scriptMap[$ScriptNumber])" -ForegroundColor Cyan
            & $scriptPath -Mode $Mode
        } else {
            Write-Host "`nScript not found: $scriptPath" -ForegroundColor Red
            Write-Host "Press any key to continue..." -ForegroundColor Gray
            $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
        }
    }
}

function Start-GuestWorkflowsMenu {
    param([string]$InitialMode = 'Visual')
    
    $currentMode = $InitialMode
    
    do {
        Show-GuestWorkflowsMenu -CurrentMode $currentMode
        $selection = Read-Host "Select option"
        
        switch ($selection.ToUpper()) {
            '0' { return }
            'M' {
                Write-Host "`nSelect Mode:" -ForegroundColor Yellow
                Write-Host "  1. Headless (Fast)" -ForegroundColor White
                Write-Host "  2. Visual (Live Browser)" -ForegroundColor White
                Write-Host "  3. Demo (Annotated)" -ForegroundColor White
                Write-Host "  4. Interactive (Pauses)" -ForegroundColor White
                Write-Host "  5. Recording (Video)" -ForegroundColor White
                $modeChoice = Read-Host "Select mode (1-5)"
                $currentMode = switch ($modeChoice) {
                    '1' { 'Headless' }
                    '2' { 'Visual' }
                    '3' { 'Demo' }
                    '4' { 'Interactive' }
                    '5' { 'Recording' }
                    default { $currentMode }
                }
            }
            'H' {
                Write-Host "`nGuest Workflows Help:" -ForegroundColor Yellow
                Write-Host "  - Scripts test guest user functionality without authentication" -ForegroundColor Gray
                Write-Host "  - Helpdesk scripts test ticket submission and tracking" -ForegroundColor Gray
                Write-Host "  - Asset loan scripts test loan requests and availability" -ForegroundColor Gray
                Write-Host "  - Integration tests verify backend system connectivity" -ForegroundColor Gray
                Write-Host "`nPress any key to continue..." -ForegroundColor Gray
                $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
            }
            'S' {
                $searchTerm = Read-Host "`nEnter search term"
                Write-Host "Searching for '$searchTerm'..." -ForegroundColor Yellow
                # Search implementation
                Write-Host "Press any key to continue..." -ForegroundColor Gray
                $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
            }
            'D1' {
                Write-Host "`nStarting Quick Demo (5 min)..." -ForegroundColor Cyan
                & "$ScriptRoot\demos\quick-demo.ps1" -Mode 'Demo'
            }
            'D2' {
                Write-Host "`nStarting Complete Demo (15 min)..." -ForegroundColor Cyan
                & "$ScriptRoot\demos\complete-demo.ps1" -Mode 'Demo'
            }
            'D3' {
                Write-Host "`nStarting Training Session..." -ForegroundColor Cyan
                & "$ScriptRoot\demos\training-session.ps1" -Mode 'Interactive'
            }
            'D4' {
                Write-Host "`nStarting Video Recording..." -ForegroundColor Cyan
                & "$ScriptRoot\demos\record-demo.ps1" -Mode 'Recording'
            }
            { $_ -match '^\d+$' -and [int]$_ -ge 1 -and [int]$_ -le 32 } {
                Invoke-GuestWorkflowScript -ScriptNumber ([int]$selection) -Mode $currentMode
            }
            '40' {
                Write-Host "`nRunning All Helpdesk Workflows..." -ForegroundColor Cyan
                1..10 | ForEach-Object { Invoke-GuestWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '41' {
                Write-Host "`nRunning All Asset Loan Workflows..." -ForegroundColor Cyan
                11..20 | ForEach-Object { Invoke-GuestWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '42' {
                Write-Host "`nRunning All Integration Tests..." -ForegroundColor Cyan
                21..25 | ForEach-Object { Invoke-GuestWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '43' {
                Write-Host "`nRunning Critical Path Tests..." -ForegroundColor Cyan
                @(1, 6, 11, 17, 21) | ForEach-Object { Invoke-GuestWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '44' {
                Write-Host "`nRunning Complete Guest User Suite..." -ForegroundColor Cyan
                1..32 | ForEach-Object { Invoke-GuestWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            default {
                Write-Host "`nInvalid selection. Please try again." -ForegroundColor Red
                Start-Sleep -Seconds 1
            }
        }
    } while ($true)
}

# Run menu if script is executed directly
if ($MyInvocation.InvocationName -ne '.') {
    Start-GuestWorkflowsMenu -InitialMode $Mode
}
