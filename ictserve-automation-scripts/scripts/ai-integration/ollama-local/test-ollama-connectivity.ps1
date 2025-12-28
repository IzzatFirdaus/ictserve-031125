#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test Ollama Server Connectivity - AI Integration
.DESCRIPTION
    Tests connectivity to the local Ollama AI server.
    Validates health check, model loading, and basic response generation.
.PARAMETER Mode
    Execution mode: Headless, Visual, Demo, Interactive, Recording
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual'
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\..\utilities\api-helpers.ps1"

$TestConfig = @{
    Name = "Test Ollama Server Connectivity"
    Category = "AI Integration - Ollama Local"
    Requirements = @("13.1", "13.3")
    ExpectedDuration = 45
}

function Test-OllamaConnectivity {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.OllamaTests = @()
    
    try {
        $ollamaUrl = Get-ConfigValue -Key "OllamaUrl" -Default "http://localhost:11434"
        
        Write-TestStep "Testing Ollama server connectivity" -Mode $ExecutionMode
        
        # Test 1: Health check endpoint
        Write-TestStep "Test 1: Checking Ollama health endpoint" -Mode $ExecutionMode
        
        try {
            $healthResponse = Invoke-RestMethod -Uri "$ollamaUrl/api/tags" -Method GET -TimeoutSec 10
            Write-TestOutput "Ollama server is running" -Type "Success"
            $result.OllamaTests += @{ Test = "Health Check"; Status = "Passed" }
            
            # List available models
            if ($healthResponse.models) {
                Write-TestOutput "Available models: $($healthResponse.models.Count)" -Type "Info"
                foreach ($model in $healthResponse.models) {
                    Write-TestOutput "  - $($model.name) ($($model.size))" -Type "Info"
                }
            }
        } catch {
            Write-TestOutput "Ollama server not reachable: $($_.Exception.Message)" -Type "Error"
            $result.OllamaTests += @{ Test = "Health Check"; Status = "Failed"; Error = $_.Exception.Message }
        }
        
        # Test 2: Model availability
        Write-TestStep "Test 2: Checking model availability" -Mode $ExecutionMode
        
        $requiredModel = Get-ConfigValue -Key "OllamaModel" -Default "llama3.2"
        
        try {
            $modelInfo = Invoke-RestMethod -Uri "$ollamaUrl/api/show" -Method POST -Body (@{ name = $requiredModel } | ConvertTo-Json) -ContentType "application/json" -TimeoutSec 30
            Write-TestOutput "Model '$requiredModel' is available" -Type "Success"
            $result.OllamaTests += @{ Test = "Model Available"; Status = "Passed"; Model = $requiredModel }
        } catch {
            Write-TestOutput "Model '$requiredModel' not found or not loaded" -Type "Warning"
            $result.OllamaTests += @{ Test = "Model Available"; Status = "Failed"; Model = $requiredModel }
        }
        
        # Test 3: Basic generation test
        Write-TestStep "Test 3: Testing basic text generation" -Mode $ExecutionMode
        
        $testPrompt = "Say 'Hello ICTServe' in exactly 3 words."
        
        try {
            $generateBody = @{
                model = $requiredModel
                prompt = $testPrompt
                stream = $false
                options = @{
                    temperature = 0.1
                    num_predict = 50
                }
            } | ConvertTo-Json
            
            $startTime = Get-Date
            $generateResponse = Invoke-RestMethod -Uri "$ollamaUrl/api/generate" -Method POST -Body $generateBody -ContentType "application/json" -TimeoutSec 60
            $responseTime = (Get-Date) - $startTime
            
            if ($generateResponse.response) {
                Write-TestOutput "Generation successful in $($responseTime.TotalSeconds.ToString('F2'))s" -Type "Success"
                Write-TestOutput "Response: $($generateResponse.response.Substring(0, [Math]::Min(100, $generateResponse.response.Length)))..." -Type "Info"
                $result.OllamaTests += @{ 
                    Test = "Text Generation"
                    Status = "Passed"
                    ResponseTime = $responseTime.TotalSeconds
                }
            }
        } catch {
            Write-TestOutput "Generation failed: $($_.Exception.Message)" -Type "Error"
            $result.OllamaTests += @{ Test = "Text Generation"; Status = "Failed"; Error = $_.Exception.Message }
        }
        
        # Test 4: Embedding generation
        Write-TestStep "Test 4: Testing embedding generation" -Mode $ExecutionMode
        
        try {
            $embedBody = @{
                model = $requiredModel
                prompt = "Test embedding for ICTServe helpdesk system"
            } | ConvertTo-Json
            
            $embedResponse = Invoke-RestMethod -Uri "$ollamaUrl/api/embeddings" -Method POST -Body $embedBody -ContentType "application/json" -TimeoutSec 30
            
            if ($embedResponse.embedding) {
                Write-TestOutput "Embedding generated: $($embedResponse.embedding.Count) dimensions" -Type "Success"
                $result.OllamaTests += @{ 
                    Test = "Embedding Generation"
                    Status = "Passed"
                    Dimensions = $embedResponse.embedding.Count
                }
            }
        } catch {
            Write-TestOutput "Embedding generation not supported or failed" -Type "Warning"
            $result.OllamaTests += @{ Test = "Embedding Generation"; Status = "Skipped" }
        }
        
        # Test 5: Concurrent request handling
        Write-TestStep "Test 5: Testing concurrent request handling" -Mode $ExecutionMode
        
        $concurrentJobs = @()
        $concurrentCount = 3
        
        for ($i = 1; $i -le $concurrentCount; $i++) {
            $job = Start-Job -ScriptBlock {
                param($url, $model, $prompt)
                try {
                    $body = @{ model = $model; prompt = $prompt; stream = $false } | ConvertTo-Json
                    $response = Invoke-RestMethod -Uri "$url/api/generate" -Method POST -Body $body -ContentType "application/json" -TimeoutSec 60
                    return @{ Success = $true; Response = $response.response.Substring(0, 50) }
                } catch {
                    return @{ Success = $false; Error = $_.Exception.Message }
                }
            } -ArgumentList $ollamaUrl, $requiredModel, "Count to $i"
            $concurrentJobs += $job
        }
        
        $jobResults = $concurrentJobs | Wait-Job -Timeout 120 | Receive-Job
        $successCount = ($jobResults | Where-Object { $_.Success }).Count
        
        Write-TestOutput "Concurrent requests: $successCount/$concurrentCount successful" -Type $(if ($successCount -eq $concurrentCount) { "Success" } else { "Warning" })
        $result.OllamaTests += @{ 
            Test = "Concurrent Requests"
            Status = if ($successCount -eq $concurrentCount) { "Passed" } else { "Partial" }
            SuccessCount = $successCount
            TotalCount = $concurrentCount
        }
        
        $concurrentJobs | Remove-Job -Force
        
        # Calculate overall result
        $passedTests = ($result.OllamaTests | Where-Object { $_.Status -eq "Passed" }).Count
        $totalTests = ($result.OllamaTests | Where-Object { $_.Status -ne "Skipped" }).Count
        
        if ($passedTests -eq $totalTests -and $totalTests -gt 0) {
            $result.Status = "Passed"
            Write-TestOutput "All Ollama connectivity tests passed ($passedTests/$totalTests)" -Type "Success"
        } elseif ($passedTests -gt 0) {
            $result.Status = "Partial"
            Write-TestOutput "Ollama tests: $passedTests/$totalTests passed" -Type "Warning"
        } else {
            $result.Status = "Failed"
            Write-TestOutput "Ollama connectivity tests failed" -Type "Error"
        }
        
    } catch {
        $result.Status = "Failed"
        $result.ErrorMessage = $_.Exception.Message
        Write-TestOutput "Test failed: $($_.Exception.Message)" -Type "Error"
    } finally {
        $result.EndTime = Get-Date
        $result.Duration = ($result.EndTime - $result.StartTime).TotalSeconds
        Save-TestResult -Result $result
    }
    
    return $result
}

$testResult = Test-OllamaConnectivity -ExecutionMode $Mode
return $testResult
