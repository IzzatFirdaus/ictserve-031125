#Requires -Version 7.0
<#
.SYNOPSIS
    Tests complete helpdesk workflow end-to-end.

.DESCRIPTION
    This script tests the entire ticket lifecycle from submission to resolution,
    cross-module integration, and data consistency automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 16.1, 16.3
#>

[CmdletBinding()]
param(
    [Parameter()]
    [string]$BaseUrl = "http://localhost:8000",
    
    [Parameter()]
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Headless'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Helpdesk E2E Workflow Test"
    Category = "End-to-End Workflows"
    Requirements = @("16.1", "16.3")
    ExpectedDuration = 180
}

function Test-TicketSubmission {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing ticket submission" -Level INFO
    
    $results = @{
        TestName = "Ticket Submission"
        Passed = $false
        Details = @{ TicketCreated = $false; TicketNumber = ""; ResponseTime = 0 }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/tickets" -Method POST -Body @{
            subject = "E2E Test Ticket"
            description = "This is an end-to-end test ticket"
            category = "general"
            priority = "medium"
        } -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response -and $response.id) {
            $results.Details.TicketCreated = $true
            $results.Details.TicketNumber = $response.ticket_number
            $script:TestTicketId = $response.id
        }
        
        $results.Passed = $results.Details.TicketCreated
    }
    catch {
        Write-AutomationLog "Ticket submission test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-TicketAssignment {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing ticket assignment" -Level INFO
    
    $results = @{
        TestName = "Ticket Assignment"
        Passed = $false
        Details = @{ AssignmentWorks = $false; AssigneeSet = $false }
    }
    
    try {
        if ($script:TestTicketId) {
            $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/tickets/$($script:TestTicketId)/assign" -Method POST -Body @{
                assignee_id = 1
            } -IgnoreErrors
            
            if ($response) {
                $results.Details.AssignmentWorks = $true
                $results.Details.AssigneeSet = $null -ne $response.assignee_id
            }
        }
        
        $results.Passed = $results.Details.AssignmentWorks
    }
    catch {
        Write-AutomationLog "Ticket assignment test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-TicketResolution {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing ticket resolution" -Level INFO
    
    $results = @{
        TestName = "Ticket Resolution"
        Passed = $false
        Details = @{ ResolutionWorks = $false; StatusUpdated = $false }
    }
    
    try {
        if ($script:TestTicketId) {
            $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/tickets/$($script:TestTicketId)/resolve" -Method POST -Body @{
                resolution = "Issue resolved via E2E test"
            } -IgnoreErrors
            
            if ($response) {
                $results.Details.ResolutionWorks = $true
                $results.Details.StatusUpdated = $response.status -eq 'resolved'
            }
        }
        
        $results.Passed = $results.Details.ResolutionWorks
    }
    catch {
        Write-AutomationLog "Ticket resolution test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-HelpdeskE2ETest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║        Helpdesk E2E Workflow Test Suite                       ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Ticket Submission"; Func = { Test-TicketSubmission } },
        @{ Name = "Ticket Assignment"; Func = { Test-TicketAssignment } },
        @{ Name = "Ticket Resolution"; Func = { Test-TicketResolution } }
    )
    
    $testNum = 1
    foreach ($test in $tests) {
        Write-Host "  Test $testNum`: $($test.Name)" -ForegroundColor Yellow
        $testResult = & $test.Func
        $results.Tests += $testResult
        $results.Summary.TotalTests++
        if ($testResult.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
        Write-Host "    Result: $(if ($testResult.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($testResult.Passed) { 'Green' } else { 'Red' })
        Write-Host ""
        $testNum++
    }
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-HelpdeskE2ETest
return $testResults
