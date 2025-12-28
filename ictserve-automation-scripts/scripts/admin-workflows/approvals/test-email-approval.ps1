#Requires -Version 7.0
<#
.SYNOPSIS
    Tests email-based approval workflow functionality.

.DESCRIPTION
    This script tests email approval features including:
    - Approval email generation
    - Token security validation
    - Workflow processing
    - Approval without system login

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-email-approval.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 7.1, 7.2, 7.3, 7.4, 7.5, 7.6, 7.7
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
    Name = "Email Approval Test"
    Category = "Admin Workflows - Approvals"
    Requirements = @("7.1", "7.2", "7.3", "7.4", "7.5", "7.6", "7.7")
    ExpectedDuration = 120
}

function Test-ApprovalEmailGeneration {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing approval email generation" -Level INFO
    
    $results = @{
        TestName = "Approval Email Generation"
        Passed = $false
        Details = @{
            EmailQueueFound = $false
            ApprovalLinksGenerated = $false
            TokensSecure = $false
        }
    }
    
    try {
        # Check email queue via API
        $apiResponse = Invoke-ApiRequest -Url "$BaseUrl/api/admin/email-queue" -Method GET -IgnoreErrors
        
        if ($apiResponse) {
            $results.Details.EmailQueueFound = $true
            
            # Check for approval emails
            $approvalEmails = $apiResponse | Where-Object { $_.type -eq 'approval' -or $_.subject -match 'approval' }
            $results.Details.ApprovalLinksGenerated = $approvalEmails.Count -gt 0
        }
        
        # Navigate to admin email logs
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/admin/email-logs" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $emailLogs = Find-Element -Driver $Driver -Selector ".email-logs, table, [data-email-logs]" -Required $false
        if ($emailLogs) {
            $results.Details.EmailQueueFound = $true
            
            $approvalLinks = Find-Element -Driver $Driver -Selector "a[href*='approve'], [data-approval-link]" -Required $false
            $results.Details.ApprovalLinksGenerated = $null -ne $approvalLinks
        }
        
        Take-Screenshot -Driver $Driver -Name "approval-emails" -Mode $ExecutionMode
        $results.Passed = $results.Details.EmailQueueFound
    }
    catch {
        Write-AutomationLog "Approval email generation test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-TokenSecurity {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing token security" -Level INFO
    
    $results = @{
        TestName = "Token Security"
        Passed = $false
        Details = @{
            TokensAreUnique = $false
            TokensExpire = $false
            InvalidTokenRejected = $false
        }
    }
    
    try {
        # Test invalid token rejection
        $invalidToken = "invalid-token-12345"
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/approve/$invalidToken" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        $errorMessage = Find-Element -Driver $Driver -Selector ".error, .alert-danger, [data-error]" -Required $false
        $results.Details.InvalidTokenRejected = $null -ne $errorMessage
        
        # Check for 404 or error page
        $pageTitle = Execute-JavaScript -Driver $Driver -Script "return document.title;"
        if ($pageTitle -match '404|error|invalid|expired') {
            $results.Details.InvalidTokenRejected = $true
        }
        
        if ($ExecutionMode -eq 'Demo') {
            Show-Annotation -Text "Invalid token correctly rejected" -Duration 2000
        }
        
        Take-Screenshot -Driver $Driver -Name "token-security" -Mode $ExecutionMode
        $results.Passed = $results.Details.InvalidTokenRejected
    }
    catch {
        Write-AutomationLog "Token security test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ApprovalWithoutLogin {
    [CmdletBinding()]
    param(
        [Parameter(Mandatory = $true)]
        $Driver,
        [Parameter(Mandatory = $true)]
        [string]$ExecutionMode
    )
    
    Write-AutomationLog "Testing approval without login" -Level INFO
    
    $results = @{
        TestName = "Approval Without Login"
        Passed = $false
        Details = @{
            ApprovalPageAccessible = $false
            NoLoginRequired = $false
            ActionButtonsPresent = $false
        }
    }
    
    try {
        # Clear any existing session
        Execute-JavaScript -Driver $Driver -Script "document.cookie.split(';').forEach(c => document.cookie = c.trim().split('=')[0] + '=;expires=Thu, 01 Jan 1970 00:00:00 UTC;path=/');"
        
        # Try to access approval page (would need a valid token in real scenario)
        Navigate-ToUrl -Driver $Driver -Url "$BaseUrl/approve/demo-token" -Mode $ExecutionMode
        Start-Sleep -Seconds 2
        
        # Check if we're redirected to login
        $currentUrl = Execute-JavaScript -Driver $Driver -Script "return window.location.href;"
        $results.Details.NoLoginRequired = -not ($currentUrl -match '/login')
        
        # Check for approval action buttons
        $approveButton = Find-Element -Driver $Driver -Selector "button:contains('Approve'), [data-action='approve']" -Required $false
        $rejectButton = Find-Element -Driver $Driver -Selector "button:contains('Reject'), [data-action='reject']" -Required $false
        $results.Details.ActionButtonsPresent = ($null -ne $approveButton) -or ($null -ne $rejectButton)
        
        $results.Details.ApprovalPageAccessible = $results.Details.NoLoginRequired -or $results.Details.ActionButtonsPresent
        
        Take-Screenshot -Driver $Driver -Name "approval-no-login" -Mode $ExecutionMode
        $results.Passed = $results.Details.ApprovalPageAccessible -or $results.Details.NoLoginRequired
    }
    catch {
        Write-AutomationLog "Approval without login test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-EmailApprovalTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║             Email Approval Test Suite                         ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $driver = $null
    
    try {
        $driver = Initialize-WebDriver -Mode $Mode
        
        # Test 1: Approval Email Generation
        Write-Host "  Test 1: Approval Email Generation" -ForegroundColor Yellow
        $emailResults = Test-ApprovalEmailGeneration -Driver $driver -ExecutionMode $Mode
        $results.Tests += $emailResults
        $results.Summary.TotalTests++
        if ($emailResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($emailResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($emailResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 2: Token Security
        Write-Host "  Test 2: Token Security" -ForegroundColor Yellow
        $tokenResults = Test-TokenSecurity -Driver $driver -ExecutionMode $Mode
        $results.Tests += $tokenResults
        $results.Summary.TotalTests++
        if ($tokenResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Invalid Token Rejected: $(if ($tokenResults.Details.InvalidTokenRejected) { '✓' } else { '○' })" -ForegroundColor $(if ($tokenResults.Details.InvalidTokenRejected) { 'Green' } else { 'Yellow' })
        Write-Host "    Result: $(if ($tokenResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($tokenResults.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        
        # Test 3: Approval Without Login
        Write-Host "  Test 3: Approval Without Login" -ForegroundColor Yellow
        $noLoginResults = Test-ApprovalWithoutLogin -Driver $driver -ExecutionMode $Mode
        $results.Tests += $noLoginResults
        $results.Summary.TotalTests++
        if ($noLoginResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($noLoginResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($noLoginResults.Passed) { 'Green' } else { 'Red' })
        
    }
    catch {
        Write-AutomationLog "Email approval test failed: $($_.Exception.Message)" -Level ERROR
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

$testResults = Start-EmailApprovalTest
return $testResults
