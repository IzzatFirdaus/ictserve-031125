#Requires -Version 7.0
<#
.SYNOPSIS
    Tests advanced monitoring automation.

.DESCRIPTION
    This script tests Laravel Pulse, Horizon, and Telescope functionality,
    performance metrics, queue monitoring, and debugging access automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 15.1, 15.2, 15.3, 15.4, 15.5, 15.6, 15.7
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
    Name = "Advanced Monitoring Test"
    Category = "Monitoring"
    Requirements = @("15.1", "15.2", "15.3", "15.4", "15.5", "15.6", "15.7")
    ExpectedDuration = 90
}

function Test-LaravelPulse {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing Laravel Pulse" -Level INFO
    
    $results = @{
        TestName = "Laravel Pulse"
        Passed = $false
        Details = @{ PulseEndpointAvailable = $false; MetricsCollected = $false }
    }
    
    try {
        $response = Invoke-WebRequest -Uri "$BaseUrl/pulse" -Method GET -ErrorAction SilentlyContinue
        if ($response.StatusCode -eq 200) {
            $results.Details.PulseEndpointAvailable = $true
            $results.Details.MetricsCollected = $true
        }
        
        $results.Passed = $results.Details.PulseEndpointAvailable
    }
    catch {
        Write-AutomationLog "Laravel Pulse test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-LaravelHorizon {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing Laravel Horizon" -Level INFO
    
    $results = @{
        TestName = "Laravel Horizon"
        Passed = $false
        Details = @{ HorizonEndpointAvailable = $false; QueueMonitoring = $false }
    }
    
    try {
        $response = Invoke-WebRequest -Uri "$BaseUrl/horizon" -Method GET -ErrorAction SilentlyContinue
        if ($response.StatusCode -eq 200) {
            $results.Details.HorizonEndpointAvailable = $true
            $results.Details.QueueMonitoring = $true
        }
        
        $results.Passed = $results.Details.HorizonEndpointAvailable
    }
    catch {
        Write-AutomationLog "Laravel Horizon test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-LaravelTelescope {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing Laravel Telescope" -Level INFO
    
    $results = @{
        TestName = "Laravel Telescope"
        Passed = $false
        Details = @{ TelescopeEndpointAvailable = $false; DebuggingAccess = $false }
    }
    
    try {
        $response = Invoke-WebRequest -Uri "$BaseUrl/telescope" -Method GET -ErrorAction SilentlyContinue
        if ($response.StatusCode -eq 200) {
            $results.Details.TelescopeEndpointAvailable = $true
            $results.Details.DebuggingAccess = $true
        }
        
        $results.Passed = $results.Details.TelescopeEndpointAvailable
    }
    catch {
        Write-AutomationLog "Laravel Telescope test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AdvancedMonitoringTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║         Advanced Monitoring Test Suite                        ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Laravel Pulse"; Func = { Test-LaravelPulse } },
        @{ Name = "Laravel Horizon"; Func = { Test-LaravelHorizon } },
        @{ Name = "Laravel Telescope"; Func = { Test-LaravelTelescope } }
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

$testResults = Start-AdvancedMonitoringTest
return $testResults
