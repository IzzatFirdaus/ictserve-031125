#Requires -Version 7.0
<#
.SYNOPSIS
    Tests complete asset loan workflow end-to-end.

.DESCRIPTION
    This script tests the entire loan process from application to return,
    realistic user scenarios, and edge case automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 16.2, 16.4
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
    Name = "Asset Loan E2E Workflow Test"
    Category = "End-to-End Workflows"
    Requirements = @("16.2", "16.4")
    ExpectedDuration = 180
}

function Test-LoanApplication {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing loan application" -Level INFO
    
    $results = @{
        TestName = "Loan Application"
        Passed = $false
        Details = @{ ApplicationCreated = $false; LoanId = ""; ResponseTime = 0 }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/loans" -Method POST -Body @{
            asset_id = 1
            start_date = (Get-Date).AddDays(1).ToString("yyyy-MM-dd")
            end_date = (Get-Date).AddDays(7).ToString("yyyy-MM-dd")
            purpose = "E2E Test Loan"
        } -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response -and $response.id) {
            $results.Details.ApplicationCreated = $true
            $results.Details.LoanId = $response.id
            $script:TestLoanId = $response.id
        }
        
        $results.Passed = $results.Details.ApplicationCreated
    }
    catch {
        Write-AutomationLog "Loan application test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-LoanApproval {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing loan approval" -Level INFO
    
    $results = @{
        TestName = "Loan Approval"
        Passed = $false
        Details = @{ ApprovalWorks = $false; StatusUpdated = $false }
    }
    
    try {
        if ($script:TestLoanId) {
            $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/loans/$($script:TestLoanId)/approve" -Method POST -IgnoreErrors
            
            if ($response) {
                $results.Details.ApprovalWorks = $true
                $results.Details.StatusUpdated = $response.status -eq 'approved'
            }
        }
        
        $results.Passed = $results.Details.ApprovalWorks
    }
    catch {
        Write-AutomationLog "Loan approval test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AssetReturn {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing asset return" -Level INFO
    
    $results = @{
        TestName = "Asset Return"
        Passed = $false
        Details = @{ ReturnWorks = $false; LoanCompleted = $false }
    }
    
    try {
        if ($script:TestLoanId) {
            $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/loans/$($script:TestLoanId)/return" -Method POST -Body @{
                condition = "good"
                notes = "Returned in good condition"
            } -IgnoreErrors
            
            if ($response) {
                $results.Details.ReturnWorks = $true
                $results.Details.LoanCompleted = $response.status -eq 'completed'
            }
        }
        
        $results.Passed = $results.Details.ReturnWorks
    }
    catch {
        Write-AutomationLog "Asset return test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AssetLoanE2ETest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║        Asset Loan E2E Workflow Test Suite                     ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Loan Application"; Func = { Test-LoanApplication } },
        @{ Name = "Loan Approval"; Func = { Test-LoanApproval } },
        @{ Name = "Asset Return"; Func = { Test-AssetReturn } }
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

$testResults = Start-AssetLoanE2ETest
return $testResults
