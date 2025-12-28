#Requires -Version 7.0
<#
.SYNOPSIS
    Tests MCP server integration functionality.

.DESCRIPTION
    This script tests Model Context Protocol server functionality
    and AI assistant tool integration automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 13.10
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
    Name = "MCP Server Test"
    Category = "AI Integration - MCP"
    Requirements = @("13.10")
    ExpectedDuration = 60
}

function Test-MCPServerHealth {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing MCP server health" -Level INFO
    
    $results = @{
        TestName = "MCP Server Health"
        Passed = $false
        Details = @{ ServerRunning = $false; EndpointAvailable = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/mcp" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.ServerRunning = $true
            $results.Details.EndpointAvailable = $true
        }
        
        $results.Passed = $results.Details.ServerRunning -or $results.Details.EndpointAvailable
    }
    catch {
        Write-AutomationLog "MCP server health test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-MCPToolIntegration {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing MCP tool integration" -Level INFO
    
    $results = @{
        TestName = "MCP Tool Integration"
        Passed = $false
        Details = @{ ToolsAvailable = $false; ToolExecutionWorks = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/mcp/tools" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.ToolsAvailable = $response.Count -gt 0
        }
        
        $results.Passed = $results.Details.ToolsAvailable
    }
    catch {
        Write-AutomationLog "MCP tool integration test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-MCPServerTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║              MCP Server Test Suite                            ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "MCP Server Health"; Func = { Test-MCPServerHealth } },
        @{ Name = "MCP Tool Integration"; Func = { Test-MCPToolIntegration } }
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

$testResults = Start-MCPServerTest
return $testResults
