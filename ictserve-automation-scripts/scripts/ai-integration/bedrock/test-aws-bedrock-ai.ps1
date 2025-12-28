#Requires -Version 7.0
<#
.SYNOPSIS
    Tests AWS Bedrock cloud AI integration functionality.

.DESCRIPTION
    This script tests Claude model integration, model routing, response quality,
    DLP filtering, and data sovereignty compliance automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.NOTES
    Version: 1.0.0
    Requirements: 13.2, 13.4, 13.5, 13.6
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
    Name = "AWS Bedrock AI Test"
    Category = "AI Integration - Bedrock"
    Requirements = @("13.2", "13.4", "13.5", "13.6")
    ExpectedDuration = 120
}

function Test-BedrockConnectivity {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing AWS Bedrock connectivity" -Level INFO
    
    $results = @{
        TestName = "Bedrock Connectivity"
        Passed = $false
        Details = @{ AWSConfigured = $false; BedrockAvailable = $false; ResponseTime = 0 }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/bedrock/health" -Method GET -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response) {
            $results.Details.AWSConfigured = $response.aws_configured -eq $true
            $results.Details.BedrockAvailable = $response.status -eq 'connected'
        }
        
        $results.Passed = $results.Details.AWSConfigured -or $results.Details.BedrockAvailable
    }
    catch {
        Write-AutomationLog "Bedrock connectivity test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ClaudeModelIntegration {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing Claude model integration" -Level INFO
    
    $results = @{
        TestName = "Claude Model Integration"
        Passed = $false
        Details = @{ ModelAvailable = $false; ResponseGenerated = $false; ResponseTime = 0 }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/chat" -Method POST -Body @{
            message = "Hello, how can you help me?"
            provider = "bedrock"
            model = "claude-3-sonnet"
        } -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response) {
            $results.Details.ModelAvailable = $true
            $results.Details.ResponseGenerated = $null -ne $response.response
        }
        
        $results.Passed = $results.Details.ModelAvailable
    }
    catch {
        Write-AutomationLog "Claude model test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ModelRouting {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing model routing" -Level INFO
    
    $results = @{
        TestName = "Model Routing"
        Passed = $false
        Details = @{ RoutingConfigured = $false; FallbackWorks = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/routing/config" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.RoutingConfigured = $null -ne $response.primary -or $null -ne $response.fallback
        }
        
        $fallbackResponse = Invoke-ApiRequest -Url "$BaseUrl/api/ai/chat" -Method POST -Body @{
            message = "Test fallback routing"
            use_fallback = $true
        } -IgnoreErrors
        if ($fallbackResponse) {
            $results.Details.FallbackWorks = $true
        }
        
        $results.Passed = $results.Details.RoutingConfigured -or $results.Details.FallbackWorks
    }
    catch {
        Write-AutomationLog "Model routing test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-DLPFiltering {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing DLP filtering" -Level INFO
    
    $results = @{
        TestName = "DLP Filtering"
        Passed = $false
        Details = @{ DLPEnabled = $false; SensitiveDataFiltered = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/chat" -Method POST -Body @{
            message = "My IC number is 123456-78-9012"
        } -IgnoreErrors
        
        if ($response) {
            $results.Details.DLPEnabled = $response.dlp_applied -eq $true -or $response.filtered -eq $true
            $results.Details.SensitiveDataFiltered = $response.response -notmatch '\d{6}-\d{2}-\d{4}'
        }
        
        $results.Passed = $results.Details.DLPEnabled -or $results.Details.SensitiveDataFiltered
    }
    catch {
        Write-AutomationLog "DLP filtering test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-DataSovereignty {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing data sovereignty compliance" -Level INFO
    
    $results = @{
        TestName = "Data Sovereignty"
        Passed = $false
        Details = @{ RegionConfigured = $false; ComplianceEnabled = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/compliance/status" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.RegionConfigured = $null -ne $response.region
            $results.Details.ComplianceEnabled = $response.data_sovereignty -eq $true
        }
        
        $results.Passed = $results.Details.RegionConfigured -or $results.Details.ComplianceEnabled
    }
    catch {
        Write-AutomationLog "Data sovereignty test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AWSBedrockAITest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║           AWS Bedrock AI Test Suite                           ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Bedrock Connectivity"; Func = { Test-BedrockConnectivity } },
        @{ Name = "Claude Model Integration"; Func = { Test-ClaudeModelIntegration } },
        @{ Name = "Model Routing"; Func = { Test-ModelRouting } },
        @{ Name = "DLP Filtering"; Func = { Test-DLPFiltering } },
        @{ Name = "Data Sovereignty"; Func = { Test-DataSovereignty } }
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

$testResults = Start-AWSBedrockAITest
return $testResults
