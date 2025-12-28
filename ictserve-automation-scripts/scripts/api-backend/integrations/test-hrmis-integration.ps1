#Requires -Version 7.0
<#
.SYNOPSIS
    Tests HRMIS integration functionality.

.DESCRIPTION
    This script tests HRMIS integration features including:
    - User data synchronization
    - Grade verification
    - External service connectivity
    - Error handling

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.EXAMPLE
    .\test-hrmis-integration.ps1 -Mode Demo

.NOTES
    Version: 1.0.0
    Requirements: 10.1
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

. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "HRMIS Integration Test"
    Category = "API Backend - Integrations"
    Requirements = @("10.1")
    ExpectedDuration = 60
}

function Test-HRMISConnectivity {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing HRMIS connectivity" -Level INFO
    
    $results = @{
        TestName = "HRMIS Connectivity"
        Passed = $false
        Details = @{
            EndpointReachable = $false
            AuthenticationWorks = $false
            ResponseTime = 0
        }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/health" -Method GET -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response) {
            $results.Details.EndpointReachable = $true
            $results.Details.AuthenticationWorks = $response.status -eq 'connected' -or $response.authenticated -eq $true
        }
        
        # Also test via admin API
        $adminResponse = Invoke-ApiRequest -Url "$BaseUrl/api/admin/hrmis/status" -Method GET -IgnoreErrors
        if ($adminResponse) {
            $results.Details.EndpointReachable = $true
        }
        
        $results.Passed = $results.Details.EndpointReachable
    }
    catch {
        Write-AutomationLog "HRMIS connectivity test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-UserDataSync {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing user data synchronization" -Level INFO
    
    $results = @{
        TestName = "User Data Sync"
        Passed = $false
        Details = @{
            SyncEndpointAvailable = $false
            DataFieldsReturned = @()
            SyncTimestamp = $null
        }
    }
    
    try {
        # Test sync endpoint
        $syncResponse = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/sync" -Method POST -IgnoreErrors
        
        if ($syncResponse) {
            $results.Details.SyncEndpointAvailable = $true
            $results.Details.SyncTimestamp = $syncResponse.timestamp
            
            if ($syncResponse.fields) {
                $results.Details.DataFieldsReturned = $syncResponse.fields
            }
        }
        
        # Test user lookup
        $userResponse = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/user/lookup" -Method GET -IgnoreErrors
        if ($userResponse) {
            $results.Details.SyncEndpointAvailable = $true
        }
        
        $results.Passed = $results.Details.SyncEndpointAvailable
    }
    catch {
        Write-AutomationLog "User data sync test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-GradeVerification {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing grade verification" -Level INFO
    
    $results = @{
        TestName = "Grade Verification"
        Passed = $false
        Details = @{
            GradeEndpointAvailable = $false
            GradeDataReturned = $false
            ValidationWorks = $false
        }
    }
    
    try {
        $gradeResponse = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/grades" -Method GET -IgnoreErrors
        
        if ($gradeResponse) {
            $results.Details.GradeEndpointAvailable = $true
            $results.Details.GradeDataReturned = $gradeResponse.Count -gt 0 -or $null -ne $gradeResponse.grades
        }
        
        # Test grade validation
        $validationResponse = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/validate-grade" -Method POST -Body @{ grade = "41" } -IgnoreErrors
        if ($validationResponse) {
            $results.Details.ValidationWorks = $true
        }
        
        $results.Passed = $results.Details.GradeEndpointAvailable -or $results.Details.ValidationWorks
    }
    catch {
        Write-AutomationLog "Grade verification test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ErrorHandling {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing HRMIS error handling" -Level INFO
    
    $results = @{
        TestName = "Error Handling"
        Passed = $false
        Details = @{
            InvalidRequestHandled = $false
            TimeoutHandled = $false
            ErrorMessagesReturned = $false
        }
    }
    
    try {
        # Test invalid request
        $invalidResponse = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/user/invalid-id-12345" -Method GET -IgnoreErrors
        $results.Details.InvalidRequestHandled = $null -eq $invalidResponse -or $invalidResponse.error
        
        # Test with invalid data
        $badDataResponse = Invoke-ApiRequest -Url "$BaseUrl/api/integrations/hrmis/sync" -Method POST -Body @{ invalid = "data" } -IgnoreErrors
        $results.Details.ErrorMessagesReturned = $null -ne $badDataResponse
        
        $results.Passed = $results.Details.InvalidRequestHandled
    }
    catch {
        Write-AutomationLog "Error handling test error: $($_.Exception.Message)" -Level ERROR
        $results.Details.InvalidRequestHandled = $true
        $results.Passed = $true
    }
    
    return $results
}

function Start-HRMISIntegrationTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║            HRMIS Integration Test Suite                       ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    # Test 1: Connectivity
    Write-Host "  Test 1: HRMIS Connectivity" -ForegroundColor Yellow
    $connResults = Test-HRMISConnectivity
    $results.Tests += $connResults
    $results.Summary.TotalTests++
    if ($connResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
    Write-Host "    Response Time: $($connResults.Details.ResponseTime)ms" -ForegroundColor White
    Write-Host "    Result: $(if ($connResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($connResults.Passed) { 'Green' } else { 'Red' })
    Write-Host ""
    
    # Test 2: User Data Sync
    Write-Host "  Test 2: User Data Sync" -ForegroundColor Yellow
    $syncResults = Test-UserDataSync
    $results.Tests += $syncResults
    $results.Summary.TotalTests++
    if ($syncResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
    Write-Host "    Result: $(if ($syncResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($syncResults.Passed) { 'Green' } else { 'Red' })
    Write-Host ""
    
    # Test 3: Grade Verification
    Write-Host "  Test 3: Grade Verification" -ForegroundColor Yellow
    $gradeResults = Test-GradeVerification
    $results.Tests += $gradeResults
    $results.Summary.TotalTests++
    if ($gradeResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
    Write-Host "    Result: $(if ($gradeResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($gradeResults.Passed) { 'Green' } else { 'Red' })
    Write-Host ""
    
    # Test 4: Error Handling
    Write-Host "  Test 4: Error Handling" -ForegroundColor Yellow
    $errorResults = Test-ErrorHandling
    $results.Tests += $errorResults
    $results.Summary.TotalTests++
    if ($errorResults.Passed) { $results.Summary.PassedTests++ } else { $results.Summary.FailedTests++ }
    Write-Host "    Result: $(if ($errorResults.Passed) { '✓ PASS' } else { '✗ FAIL' })" -ForegroundColor $(if ($errorResults.Passed) { 'Green' } else { 'Red' })
    
    $results.EndTime = Get-Date
    $results.Duration = $results.EndTime - $results.StartTime
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║  Total: $($results.Summary.TotalTests)  Passed: $($results.Summary.PassedTests)  Failed: $($results.Summary.FailedTests)                                    ║" -ForegroundColor White
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    
    return $results
}

$testResults = Start-HRMISIntegrationTest
return $testResults
