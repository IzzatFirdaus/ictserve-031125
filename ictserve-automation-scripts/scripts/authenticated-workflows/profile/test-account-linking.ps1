#Requires -Version 7.0
<#
.SYNOPSIS
    Tests account linking functionality for guest-to-authenticated user transitions.

.DESCRIPTION
    This script tests account linking features including:
    - Guest submission linking to authenticated accounts
    - Data consistency validation
    - Audit trail verification

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-account-linking.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 6.1, 6.2
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000",
    
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\..\utilities\visual-demo-helpers.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Account Linking Test"
    Category = "Authenticated Workflows - Profile"
    Requirements = @("6.1", "6.2")
    ExpectedDuration = 90
}

$TestCredentials = @{
    Email = "test.user@motac.gov.my"
    Password = "TestPassword123!"
}

function Invoke-Login {
    param($Driver, $ExecutionMode)
    
    Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/login" -Mode $ExecutionMode
    Wait-ForElement -Driver $Driver -Selector "form" -Timeout 10
    Fill-FormField -Driver $Driver -Selector "#email, input[name='email']" -Value $TestCredentials.Email -Mode $ExecutionMode
    Fill-FormField -Driver $Driver -Selector "#password, input[name='password']" -Value $TestCredentials.Password -Mode $ExecutionMode
    $submitButton = Find-Element -Driver $Driver -Selector "button[type='submit']"
    Click-Element -Driver $Driver -Element $submitButton -Mode $ExecutionMode
    Start-Sleep -Seconds 3
}

function Test-GuestSubmissionLinking {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing guest submission linking" -Level INFO
    
    $results = @{
        TestName = "Guest Submission Linking"
        Passed = $false
        Details = @{
            LinkingOptionFound = $false
            GuestSubmissionsVisible = $false
            LinkButtonPresent = $false
        }
    }
    
    try {
        $linkingUrls = @(
            "$BaseUrl/profile/link-submissions",
            "$BaseUrl/account/link",
            "$BaseUrl/settings/link-guest"
        )
        
        foreach ($url in $linkingUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $linkingSection = Find-Element -Driver $Driver -Selector ".link-submissions, [data-link-guest], .guest-linking" -Required $false
            if ($linkingSection) {
                $results.Details.LinkingOptionFound = $true
                break
            }
        }
        
        # Also check profile page for linking option
        if (-not $results.Details.LinkingOptionFound) {
            Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/profile" -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $linkOption = Find-Element -Driver $Driver -Selector "a:contains('Link'), button:contains('Link Guest')" -Required $false
            $results.Details.LinkingOptionFound = $null -ne $linkOption
        }
        
        if ($results.Details.LinkingOptionFound) {
            $guestList = Find-Element -Driver $Driver -Selector ".guest-submissions, [data-guest-submissions]" -Required $false
            $results.Details.GuestSubmissionsVisible = $null -ne $guestList
            
            $linkButton = Find-Element -Driver $Driver -Selector "button:contains('Link'), [data-action='link']" -Required $false
            $results.Details.LinkButtonPresent = $null -ne $linkButton
        }
        
        Take-Screenshot -Driver $Driver -Name "guest-linking" -Mode $ExecutionMode
        $results.Passed = $results.Details.LinkingOptionFound
    }
    catch {
        Write-AutomationLog "Guest submission linking test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-DataConsistency {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing data consistency" -Level INFO
    
    $results = @{
        TestName = "Data Consistency"
        Passed = $false
        Details = @{
            ProfileDataLoaded = $false
            TicketHistoryConsistent = $false
            LoanHistoryConsistent = $false
        }
    }
    
    try {
        # Check profile data
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/profile" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $profileData = Find-Element -Driver $Driver -Selector ".profile-data, [data-profile]" -Required $false
        $results.Details.ProfileDataLoaded = $null -ne $profileData
        
        # Check ticket history
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/helpdesk/my-tickets" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $ticketList = Find-Element -Driver $Driver -Selector ".ticket-list, table" -Required $false
        $results.Details.TicketHistoryConsistent = $null -ne $ticketList
        
        # Check loan history
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/loans/my-loans" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $loanList = Find-Element -Driver $Driver -Selector ".loan-list, table" -Required $false
        $results.Details.LoanHistoryConsistent = $null -ne $loanList
        
        Take-Screenshot -Driver $Driver -Name "data-consistency" -Mode $ExecutionMode
        $results.Passed = $results.Details.ProfileDataLoaded
    }
    catch {
        Write-AutomationLog "Data consistency test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AuditTrail {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing audit trail" -Level INFO
    
    $results = @{
        TestName = "Audit Trail"
        Passed = $false
        Details = @{
            ActivityLogFound = $false
            TimestampsPresent = $false
            ActionTypesLogged = $false
        }
    }
    
    try {
        $auditUrls = @(
            "$BaseUrl/profile/activity",
            "$BaseUrl/account/activity-log",
            "$BaseUrl/settings/audit"
        )
        
        foreach ($url in $auditUrls) {
            Navigate-ToUrl -Driver $Driver -Url $url -Mode $ExecutionMode
            Start-Sleep -Seconds 2
            
            $activityLog = Find-Element -Driver $Driver -Selector ".activity-log, [data-activity], .audit-trail" -Required $false
            if ($activityLog) {
                $results.Details.ActivityLogFound = $true
                break
            }
        }
        
        if ($results.Details.ActivityLogFound) {
            $timestamps = Find-Element -Driver $Driver -Selector "time, .timestamp, [data-timestamp]" -Required $false
            $results.Details.TimestampsPresent = $null -ne $timestamps
            
            $actionTypes = Find-Element -Driver $Driver -Selector ".action-type, [data-action-type]" -Required $false
            $results.Details.ActionTypesLogged = $null -ne $actionTypes
        }
        
        Take-Screenshot -Driver $Driver -Name "audit-trail" -Mode $ExecutionMode
        $results.Passed = $results.Details.ActivityLogFound -or $results.Details.TimestampsPresent
    }
    catch {
        Write-AutomationLog "Audit trail test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AccountLinkingTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║             Account Linking Test Suite                        ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        $driver = Initialize-WebDriver -Mode $Mode
        
        Write-Host "  Logging in..." -ForegroundColor Gray
        Invoke-Login -Driver $driver -ExecutionMode $Mode
        
        # Test 1: Guest Submission Linking
        Write-Host "  Test 1: Guest Submission Linking" -ForegroundColor Yellow
        $linkResults = Test-GuestSubmissionLinking -Driver $driver -ExecutionMode $Mode
        $results.Tests += $linkResults
        $results.Summary.TotalTests++
        if ($linkResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($linkResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($linkResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Data Consistency
        Write-Host "  Test 2: Data Consistency" -ForegroundColor Yellow
        $consistencyResults = Test-DataConsistency -Driver $driver -ExecutionMode $Mode
        $results.Tests += $consistencyResults
        $results.Summary.TotalTests++
        if ($consistencyResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($consistencyResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($consistencyResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Audit Trail
        Write-Host "  Test 3: Audit Trail" -ForegroundColor Yellow
        $auditResults = Test-AuditTrail -Driver $driver -ExecutionMode $Mode
        $results.Tests += $auditResults
        $results.Summary.TotalTests++
        if ($auditResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($auditResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($auditResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Account linking test failed: $($_.Exception.Message)" -Level ERROR
        throw
    }
    finally {
        if ($driver) { Close-WebDriver -Driver $driver }
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-AccountLinkingTest
return $testResults
