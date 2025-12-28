#!/usr/bin/env pwsh
<#
.SYNOPSIS
    End-to-End Workflow Testing Menu
.DESCRIPTION
    Provides an interactive menu for running end-to-end workflow automation scripts
    including complete helpdesk workflows, loan workflows, and cross-module testing.
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"

function Show-EndToEndMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    End-to-End Workflow Testing" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "COMPLETE HELPDESK WORKFLOWS:" -ForegroundColor Green
    Write-Host "  1.  Guest Ticket: Submission to Resolution" -ForegroundColor White
    Write-Host "  2.  Authenticated Ticket: Submission to Resolution" -ForegroundColor White
    Write-Host "  3.  Ticket Escalation: Priority Change to Resolution" -ForegroundColor White
    Write-Host "  4.  Ticket Transfer: Assignment to Different Agent" -ForegroundColor White
    Write-Host "  5.  Ticket Collaboration: Multiple Agents Working" -ForegroundColor White
    Write-Host "  6.  Emergency Ticket: Urgent Priority Handling" -ForegroundColor White
    Write-Host "  7.  Ticket with Attachments: Full Lifecycle" -ForegroundColor White
    Write-Host "  8.  Ticket Feedback: Resolution to Rating" -ForegroundColor White
    Write-Host "  9.  Ticket Reopening: Closed to Active" -ForegroundColor White
    Write-Host "  10. Bulk Ticket Processing: Multiple Tickets" -ForegroundColor White
    Write-Host ""
    
    Write-Host "COMPLETE LOAN WORKFLOWS:" -ForegroundColor Green
    Write-Host "  11. Basic Loan: Application to Return" -ForegroundColor White
    Write-Host "  12. Multi-Asset Loan: Multiple Items" -ForegroundColor White
    Write-Host "  13. Loan Extension: Request and Approval" -ForegroundColor White
    Write-Host "  14. Loan Cancellation: Request to Cancellation" -ForegroundColor White
    Write-Host "  15. Loan Rejection: Application to Rejection" -ForegroundColor White
    Write-Host "  16. Overdue Loan: Late Return Handling" -ForegroundColor White
    Write-Host "  17. Damaged Asset: Return with Damage Report" -ForegroundColor White
    Write-Host "  18. Asset Transfer: Between Users" -ForegroundColor White
    Write-Host "  19. Emergency Loan: Urgent Request Processing" -ForegroundColor White
    Write-Host "  20. Loan with OTP: Pickup Verification" -ForegroundColor White
    Write-Host ""
    
    Write-Host "CROSS-MODULE INTEGRATION:" -ForegroundColor Green
    Write-Host "  21. Helpdesk to Loan: Issue Triggers Loan" -ForegroundColor White
    Write-Host "  22. User Journey: Registration to First Ticket" -ForegroundColor White
    Write-Host "  23. User Journey: Registration to First Loan" -ForegroundColor White
    Write-Host "  24. Admin Journey: Login to Report Generation" -ForegroundColor White
    Write-Host "  25. AI Integration: Query to Resolution" -ForegroundColor White
    Write-Host "  26. Notification Flow: Event to User Notification" -ForegroundColor White
    Write-Host "  27. Approval Chain: Multi-level Approval" -ForegroundColor White
    Write-Host "  28. Data Sync: HRMIS to User Profile" -ForegroundColor White
    Write-Host "  29. Complete User Journey: All Features" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  30. Run All Helpdesk E2E Tests" -ForegroundColor White
    Write-Host "  31. Run All Loan E2E Tests" -ForegroundColor White
    Write-Host "  32. Run All Cross-Module Tests" -ForegroundColor White
    Write-Host "  33. Run Critical Path Only" -ForegroundColor White
    Write-Host "  34. Run Complete E2E Suite (All 29 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "  M.  Change Execution Mode | H. Help | S. Search | 0. Back" -ForegroundColor Cyan
    Write-Host ""
}

function Start-EndToEndMenu {
    param([string]$InitialMode = 'Visual')
    $currentMode = $InitialMode
    
    do {
        Show-EndToEndMenu -CurrentMode $currentMode
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

if ($MyInvocation.InvocationName -ne '.') { Start-EndToEndMenu -InitialMode $Mode }
