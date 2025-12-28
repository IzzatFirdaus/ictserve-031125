#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Authenticated User Workflows Menu - Interactive menu for authenticated user automation scripts
.DESCRIPTION
    Provides an interactive menu for running authenticated user workflow automation scripts
    including authentication, dashboard, enhanced helpdesk, and profile management.
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

. "$ScriptRoot\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\utilities\visual-demo-helpers.ps1"

function Show-AuthenticatedWorkflowsMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    Authenticated User Workflows - Frontend & Backend Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "AUTHENTICATION & SESSION MANAGEMENT:" -ForegroundColor Green
    Write-Host "  1.  Test Email/Username Login" -ForegroundColor White
    Write-Host "  2.  Test Password Validation" -ForegroundColor White
    Write-Host "  3.  Test Remember Me Functionality" -ForegroundColor White
    Write-Host "  4.  Test Password Reset Flow" -ForegroundColor White
    Write-Host "  5.  Test Account Lockout Protection" -ForegroundColor White
    Write-Host "  6.  Test Google Workspace SSO" -ForegroundColor White
    Write-Host "  7.  Test Session Timeout Handling" -ForegroundColor White
    Write-Host "  8.  Test Concurrent Session Management" -ForegroundColor White
    Write-Host "  9.  Test Profile Data Sync with HRMIS" -ForegroundColor White
    Write-Host "  10. Test Two-Factor Authentication" -ForegroundColor White
    Write-Host ""
    
    Write-Host "DASHBOARD & REAL-TIME FEATURES:" -ForegroundColor Green
    Write-Host "  11. Test Dashboard Widget Loading" -ForegroundColor White
    Write-Host "  12. Test Real-time Statistics Updates" -ForegroundColor White
    Write-Host "  13. Test Notification Center (Laravel Reverb)" -ForegroundColor White
    Write-Host "  14. Test Quick Action Buttons" -ForegroundColor White
    Write-Host "  15. Test Keyboard Shortcuts" -ForegroundColor White
    Write-Host "  16. Test Dashboard Customization" -ForegroundColor White
    Write-Host "  17. Test Mobile Dashboard View" -ForegroundColor White
    Write-Host "  18. Test Dashboard Performance" -ForegroundColor White
    Write-Host "  19. Test WebSocket Connection Handling" -ForegroundColor White
    Write-Host "  20. Test Push Notification Integration" -ForegroundColor White
    Write-Host ""
    
    Write-Host "ENHANCED HELPDESK FEATURES:" -ForegroundColor Green
    Write-Host "  21. Test Auto-filled Personal Information" -ForegroundColor White
    Write-Host "  22. Test Ticket History View" -ForegroundColor White
    Write-Host "  23. Test Ticket Comments System" -ForegroundColor White
    Write-Host "  24. Test File Attachment to Existing Tickets" -ForegroundColor White
    Write-Host "  25. Test Ticket Priority Escalation" -ForegroundColor White
    Write-Host "  26. Test Ticket Assignment Requests" -ForegroundColor White
    Write-Host "  27. Test Ticket Status Change Notifications" -ForegroundColor White
    Write-Host "  28. Test Ticket Claiming from Guest Submissions" -ForegroundColor White
    Write-Host "  29. Test Ticket Collaboration Features" -ForegroundColor White
    Write-Host "  30. Test Ticket Resolution Feedback" -ForegroundColor White
    Write-Host ""
    
    Write-Host "ENHANCED ASSET LOAN FEATURES:" -ForegroundColor Green
    Write-Host "  31. Test Enhanced Loan Application" -ForegroundColor White
    Write-Host "  32. Test Real-time Asset Availability" -ForegroundColor White
    Write-Host "  33. Test Loan History Management" -ForegroundColor White
    Write-Host "  34. Test Loan Extension Requests" -ForegroundColor White
    Write-Host "  35. Test Asset Pickup OTP System" -ForegroundColor White
    Write-Host "  36. Test Loan Return Process" -ForegroundColor White
    Write-Host "  37. Test Asset Maintenance Scheduling" -ForegroundColor White
    Write-Host "  38. Test Loan Approval Tracking" -ForegroundColor White
    Write-Host "  39. Test Asset Transfer Between Users" -ForegroundColor White
    Write-Host "  40. Test Loan Cancellation Process" -ForegroundColor White
    Write-Host ""
    
    Write-Host "PROFILE & ACCOUNT MANAGEMENT:" -ForegroundColor Green
    Write-Host "  41. Test Profile Viewing" -ForegroundColor White
    Write-Host "  42. Test Editable Field Updates" -ForegroundColor White
    Write-Host "  43. Test Read-only Field Correction Requests" -ForegroundColor White
    Write-Host "  44. Test Notification Preferences" -ForegroundColor White
    Write-Host "  45. Test Account Linking" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  60. Run All Authentication Tests" -ForegroundColor White
    Write-Host "  61. Run All Dashboard Tests" -ForegroundColor White
    Write-Host "  62. Run All Enhanced Helpdesk Tests" -ForegroundColor White
    Write-Host "  63. Run All Enhanced Asset Loan Tests" -ForegroundColor White
    Write-Host "  64. Run All Profile Management Tests" -ForegroundColor White
    Write-Host "  65. Run Complete Authenticated User Suite (All 67 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "UTILITIES:" -ForegroundColor Cyan
    Write-Host "  M.  Change Execution Mode" -ForegroundColor White
    Write-Host "  H.  Help for this category" -ForegroundColor White
    Write-Host "  S.  Search specific test" -ForegroundColor White
    Write-Host "  0.  Back to Main Menu" -ForegroundColor White
    Write-Host ""
}

function Invoke-AuthenticatedWorkflowScript {
    param(
        [int]$ScriptNumber,
        [string]$Mode
    )
    
    $scriptMap = @{
        1  = "authentication\test-email-login.ps1"
        2  = "authentication\test-password-validation.ps1"
        3  = "authentication\test-remember-me.ps1"
        4  = "authentication\test-password-reset.ps1"
        5  = "authentication\test-account-lockout.ps1"
        6  = "authentication\test-google-sso.ps1"
        7  = "authentication\test-session-timeout.ps1"
        8  = "authentication\test-concurrent-sessions.ps1"
        9  = "authentication\test-hrmis-sync.ps1"
        10 = "authentication\test-two-factor-auth.ps1"
        11 = "dashboard\test-widget-loading.ps1"
        12 = "dashboard\test-realtime-updates.ps1"
        13 = "dashboard\test-notification-center.ps1"
        14 = "dashboard\test-quick-actions.ps1"
        15 = "dashboard\test-keyboard-shortcuts.ps1"
        16 = "dashboard\test-dashboard-customization.ps1"
        17 = "dashboard\test-mobile-dashboard.ps1"
        18 = "dashboard\test-dashboard-performance.ps1"
        19 = "dashboard\test-websocket-connection.ps1"
        20 = "dashboard\test-push-notifications.ps1"
        21 = "enhanced-helpdesk\test-auto-filled-forms.ps1"
        22 = "enhanced-helpdesk\test-ticket-history.ps1"
        23 = "enhanced-helpdesk\test-ticket-comments.ps1"
        24 = "enhanced-helpdesk\test-file-attachment.ps1"
        25 = "enhanced-helpdesk\test-priority-escalation.ps1"
        26 = "enhanced-helpdesk\test-assignment-requests.ps1"
        27 = "enhanced-helpdesk\test-status-notifications.ps1"
        28 = "enhanced-helpdesk\test-ticket-claiming.ps1"
        29 = "enhanced-helpdesk\test-collaboration.ps1"
        30 = "enhanced-helpdesk\test-resolution-feedback.ps1"
        31 = "enhanced-loans\test-enhanced-application.ps1"
        32 = "enhanced-loans\test-realtime-availability.ps1"
        33 = "enhanced-loans\test-loan-history.ps1"
        34 = "enhanced-loans\test-extension-requests.ps1"
        35 = "enhanced-loans\test-pickup-otp.ps1"
        36 = "enhanced-loans\test-loan-return.ps1"
        37 = "enhanced-loans\test-maintenance-scheduling.ps1"
        38 = "enhanced-loans\test-approval-tracking.ps1"
        39 = "enhanced-loans\test-asset-transfer.ps1"
        40 = "enhanced-loans\test-loan-cancellation.ps1"
        41 = "profile-management\test-profile-viewing.ps1"
        42 = "profile-management\test-field-updates.ps1"
        43 = "profile-management\test-correction-requests.ps1"
        44 = "profile-management\test-notification-preferences.ps1"
        45 = "profile-management\test-account-linking.ps1"
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

function Start-AuthenticatedWorkflowsMenu {
    param([string]$InitialMode = 'Visual')
    
    $currentMode = $InitialMode
    
    do {
        Show-AuthenticatedWorkflowsMenu -CurrentMode $currentMode
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
                Write-Host "`nAuthenticated Workflows Help:" -ForegroundColor Yellow
                Write-Host "  - Scripts test authenticated user functionality" -ForegroundColor Gray
                Write-Host "  - Authentication scripts test login and session management" -ForegroundColor Gray
                Write-Host "  - Dashboard scripts test real-time features and widgets" -ForegroundColor Gray
                Write-Host "  - Enhanced features test advanced helpdesk and loan capabilities" -ForegroundColor Gray
                Write-Host "`nPress any key to continue..." -ForegroundColor Gray
                $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
            }
            'S' {
                $searchTerm = Read-Host "`nEnter search term"
                Write-Host "Searching for '$searchTerm'..." -ForegroundColor Yellow
                Write-Host "Press any key to continue..." -ForegroundColor Gray
                $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
            }
            { $_ -match '^\d+$' -and [int]$_ -ge 1 -and [int]$_ -le 45 } {
                Invoke-AuthenticatedWorkflowScript -ScriptNumber ([int]$selection) -Mode $currentMode
            }
            '60' {
                Write-Host "`nRunning All Authentication Tests..." -ForegroundColor Cyan
                1..10 | ForEach-Object { Invoke-AuthenticatedWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '61' {
                Write-Host "`nRunning All Dashboard Tests..." -ForegroundColor Cyan
                11..20 | ForEach-Object { Invoke-AuthenticatedWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '62' {
                Write-Host "`nRunning All Enhanced Helpdesk Tests..." -ForegroundColor Cyan
                21..30 | ForEach-Object { Invoke-AuthenticatedWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '63' {
                Write-Host "`nRunning All Enhanced Asset Loan Tests..." -ForegroundColor Cyan
                31..40 | ForEach-Object { Invoke-AuthenticatedWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '64' {
                Write-Host "`nRunning All Profile Management Tests..." -ForegroundColor Cyan
                41..45 | ForEach-Object { Invoke-AuthenticatedWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            '65' {
                Write-Host "`nRunning Complete Authenticated User Suite..." -ForegroundColor Cyan
                1..45 | ForEach-Object { Invoke-AuthenticatedWorkflowScript -ScriptNumber $_ -Mode $currentMode }
            }
            default {
                Write-Host "`nInvalid selection. Please try again." -ForegroundColor Red
                Start-Sleep -Seconds 1
            }
        }
    } while ($true)
}

if ($MyInvocation.InvocationName -ne '.') {
    Start-AuthenticatedWorkflowsMenu -InitialMode $Mode
}
