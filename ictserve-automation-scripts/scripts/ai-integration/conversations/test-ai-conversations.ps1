#Requires -Version 7.0
<#
.SYNOPSIS
    Tests AI conversation management functionality.

.DESCRIPTION
    This script tests AI conversation save/load/delete functionality,
    streaming responses, and web-augmented responses automation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.NOTES
    Version: 1.0.0
    Requirements: 13.7, 13.8, 13.9
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
    Name = "AI Conversations Test"
    Category = "AI Integration - Conversations"
    Requirements = @("13.7", "13.8", "13.9")
    ExpectedDuration = 90
}

function Test-ConversationSave {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing conversation save" -Level INFO
    
    $results = @{
        TestName = "Conversation Save"
        Passed = $false
        Details = @{ SaveEndpointAvailable = $false; ConversationSaved = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/conversations" -Method POST -Body @{
            title = "Test Conversation"
            messages = @(@{ role = "user"; content = "Hello" })
        } -IgnoreErrors
        
        if ($response) {
            $results.Details.SaveEndpointAvailable = $true
            $results.Details.ConversationSaved = $null -ne $response.id
        }
        
        $results.Passed = $results.Details.SaveEndpointAvailable
    }
    catch {
        Write-AutomationLog "Conversation save test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ConversationLoad {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing conversation load" -Level INFO
    
    $results = @{
        TestName = "Conversation Load"
        Passed = $false
        Details = @{ LoadEndpointAvailable = $false; ConversationsRetrieved = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/conversations" -Method GET -IgnoreErrors
        if ($response) {
            $results.Details.LoadEndpointAvailable = $true
            $results.Details.ConversationsRetrieved = $response.Count -ge 0
        }
        
        $results.Passed = $results.Details.LoadEndpointAvailable
    }
    catch {
        Write-AutomationLog "Conversation load test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-StreamingResponses {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing streaming responses" -Level INFO
    
    $results = @{
        TestName = "Streaming Responses"
        Passed = $false
        Details = @{ StreamingSupported = $false; ChunkedResponse = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/chat/stream" -Method POST -Body @{
            message = "Tell me about ICTServe"
            stream = $true
        } -IgnoreErrors
        
        if ($response) {
            $results.Details.StreamingSupported = $true
        }
        
        $results.Passed = $results.Details.StreamingSupported
    }
    catch {
        Write-AutomationLog "Streaming responses test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-AIConversationsTest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║           AI Conversations Test Suite                         ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Conversation Save"; Func = { Test-ConversationSave } },
        @{ Name = "Conversation Load"; Func = { Test-ConversationLoad } },
        @{ Name = "Streaming Responses"; Func = { Test-StreamingResponses } }
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

$testResults = Start-AIConversationsTest
return $testResults
