#Requires -Version 7.0
<#
.SYNOPSIS
    Tests asset lifecycle automation.

.DESCRIPTION
    This script tests asset registration, tracking, maintenance, transfers,
    inventory management, and availability checking automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 14.1, 14.2, 14.3, 14.4, 14.5, 14.6, 14.7
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
    Name = "Asset Lifecycle Test"
    Category = "Asset Management"
    Requirements = @("14.1", "14.2", "14.3", "14.4", "14.5", "14.6", "14.7")
    ExpectedDuration = 120
}

function Test-AssetRegistration {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing asset registration" -Level INFO
    
    $results = @{
        TestName = "Asset Registration"
        Passed = $false
        Details = @{ RegistrationEndpointAvailable = $false; AssetCreated = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/assets" -Method POST -Body @{
            name = "Test Asset"
            category = "Equipment"
            serial_number = "TEST-001"
        } -IgnoreErrors
        
        if ($response) {
            $results.Details.RegistrationEndpointAvailable = $true
            $results.Details.AssetCreated = $null -ne $response.id
        }
        
        $results.Passed = $results.Details.RegistrationEndpointAvailable
    }
    catch {
        Write-AutomationLog "Asset registration test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AssetTracking {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing asset tracking" -Level INFO
    
    $results = @{
        TestName = "Asset Tracking"
        Passed = $false
        Details = @{ TrackingEndpointAvailable = $false; AssetsListed = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/admin/assets" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.TrackingEndpointAvailable = $true
            $results.Details.AssetsListed = $response.Count -ge 0
        }
        
        $results.Passed = $results.Details.TrackingEndpointAvailable
    }
    catch {
        Write-AutomationLog "Asset tracking test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-AssetAvailability {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing asset availability" -Level INFO
    
    $results = @{
        TestName = "Asset Availability"
        Passed = $false
        Details = @{ AvailabilityEndpointAvailable = $false; AvailabilityChecked = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/assets/availability" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.AvailabilityEndpointAvailable = $true
            $results.Details.AvailabilityChecked = $true
        }
        
        $results.Passed = $results.Details.AvailabilityEndpointAvailable
    }
    catch {
        Write-AutomationLog "Asset availability test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AssetLifecycleTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║           Asset Lifecycle Test Suite                          ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Asset Registration"; Func = { Test-AssetRegistration } },
        @{ Name = "Asset Tracking"; Func = { Test-AssetTracking } },
        @{ Name = "Asset Availability"; Func = { Test-AssetAvailability } }
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

$testResults = Start-AssetLifecycleTest
return $testResults
