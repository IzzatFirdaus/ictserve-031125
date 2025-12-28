#Requires -Version 7.0
<#
.SYNOPSIS
    Tests Ollama local AI integration functionality.

.DESCRIPTION
    This script tests local LLM server connectivity, model loading, FAQ responses,
    RAG functionality, and embedding generation.

.PARAMETER BaseUrl
    The base URL of the ICTServe application.

.PARAMETER Mode
    The execution mode (Headless, Visual, Demo, Interactive, Recording).

.NOTES
    Version: 1.0.0
    Requirements: 13.1, 13.3
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
    Name = "Ollama Local AI Test"
    Category = "AI Integration - Ollama"
    Requirements = @("13.1", "13.3")
    ExpectedDuration = 120
}

function Test-OllamaConnectivity {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing Ollama connectivity" -Level INFO
    
    $results = @{
        TestName = "Ollama Connectivity"
        Passed = $false
        Details = @{ ServerReachable = $false; ModelsAvailable = $false; ResponseTime = 0 }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/ollama/health" -Method GET -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response) {
            $results.Details.ServerReachable = $response.status -eq 'connected' -or $response.available -eq $true
        }
        
        $modelsResponse = Invoke-ApiRequest -Url "$BaseUrl/api/ai/ollama/models" -Method GET -IgnoreErrors
        if ($modelsResponse -and $modelsResponse.Count -gt 0) {
            $results.Details.ModelsAvailable = $true
        }
        
        $results.Passed = $results.Details.ServerReachable -or $results.Details.ModelsAvailable
    }
    catch {
        Write-AutomationLog "Ollama connectivity test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-ModelLoading {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing model loading" -Level INFO
    
    $results = @{
        TestName = "Model Loading"
        Passed = $false
        Details = @{ ModelLoaded = $false; LoadTime = 0; ModelName = "" }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/ollama/load" -Method POST -Body @{
            model = "llama3.2"
        } -IgnoreErrors
        $results.Details.LoadTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response) {
            $results.Details.ModelLoaded = $response.loaded -eq $true -or $response.status -eq 'ready'
            $results.Details.ModelName = $response.model
        }
        
        $results.Passed = $results.Details.ModelLoaded
    }
    catch {
        Write-AutomationLog "Model loading test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-FAQResponses {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing FAQ responses" -Level INFO
    
    $results = @{
        TestName = "FAQ Responses"
        Passed = $false
        Details = @{ ResponseGenerated = $false; ResponseTime = 0; ResponseLength = 0 }
    }
    
    try {
        $startTime = Get-Date
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/chat" -Method POST -Body @{
            message = "How do I submit a helpdesk ticket?"
            provider = "ollama"
        } -IgnoreErrors
        $results.Details.ResponseTime = ((Get-Date) - $startTime).TotalMilliseconds
        
        if ($response -and $response.response) {
            $results.Details.ResponseGenerated = $true
            $results.Details.ResponseLength = $response.response.Length
        }
        
        $results.Passed = $results.Details.ResponseGenerated
    }
    catch {
        Write-AutomationLog "FAQ responses test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-RAGFunctionality {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing RAG functionality" -Level INFO
    
    $results = @{
        TestName = "RAG Functionality"
        Passed = $false
        Details = @{ RAGEnabled = $false; ContextRetrieved = $false; SourcesProvided = $false }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/rag/query" -Method POST -Body @{
            query = "What are the loan policies?"
        } -IgnoreErrors
        
        if ($response) {
            $results.Details.RAGEnabled = $true
            $results.Details.ContextRetrieved = $null -ne $response.context
            $results.Details.SourcesProvided = $null -ne $response.sources
        }
        
        $results.Passed = $results.Details.RAGEnabled
    }
    catch {
        Write-AutomationLog "RAG functionality test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Test-EmbeddingGeneration {
    [CmdletBinding()]
    param()
    
    Write-AutomationLog "Testing embedding generation" -Level INFO
    
    $results = @{
        TestName = "Embedding Generation"
        Passed = $false
        Details = @{ EmbeddingsGenerated = $false; VectorDimensions = 0 }
    }
    
    try {
        $response = Invoke-ApiRequest -Url "$BaseUrl/api/ai/embeddings" -Method POST -Body @{
            text = "Test document for embedding"
        } -IgnoreErrors
        
        if ($response -and $response.embedding) {
            $results.Details.EmbeddingsGenerated = $true
            $results.Details.VectorDimensions = $response.embedding.Count
        }
        
        $results.Passed = $results.Details.EmbeddingsGenerated
    }
    catch {
        Write-AutomationLog "Embedding generation test error: $($_.Exception.Message)" -Level ERROR
    }
    
    return $results
}

function Start-OllamaLocalAITest {
    $results = @{
        TestName = $TestConfig.Name
        StartTime = Get-Date
        Tests = @()
        Summary = @{ TotalTests = 0; PassedTests = 0; FailedTests = 0 }
    }
    
    Write-Host ""
    Write-Host "╔══════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
    Write-Host "║            Ollama Local AI Test Suite                         ║" -ForegroundColor Cyan
    Write-Host "╚══════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
    Write-Host ""
    
    $tests = @(
        @{ Name = "Ollama Connectivity"; Func = { Test-OllamaConnectivity } },
        @{ Name = "Model Loading"; Func = { Test-ModelLoading } },
        @{ Name = "FAQ Responses"; Func = { Test-FAQResponses } },
        @{ Name = "RAG Functionality"; Func = { Test-RAGFunctionality } },
        @{ Name = "Embedding Generation"; Func = { Test-EmbeddingGeneration } }
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

$testResults = Start-OllamaLocalAITest
return $testResults
