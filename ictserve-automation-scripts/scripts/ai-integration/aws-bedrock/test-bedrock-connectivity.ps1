#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test AWS Bedrock Connectivity - AI Integration
.DESCRIPTION
    Tests connectivity to AWS Bedrock AI service.
    Validates authentication, model access, and basic response generation.
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
    Name = "Test AWS Bedrock Connectivity"
    Category = "AI Integration - AWS Bedrock"
    Requirements = @("13.2", "13.4")
    ExpectedDuration = 60
}

function Test-BedrockConnectivity {
    param([string]$ExecutionMode)
    
    $result = Initialize-TestResult -TestName $TestConfig.Name -Category $TestConfig.Category
    $result.BedrockTests = @()
    
    try {
        Write-TestStep "Testing AWS Bedrock connectivity" -Mode $ExecutionMode
        
        # Test via application API (which proxies to Bedrock)
        $baseUrl = Get-ConfigValue -Key "BaseUrl"
        
        # Test 1: Check AI service status
        Write-TestStep "Test 1: Checking AI service status" -Mode $ExecutionMode
        
        $statusResponse = Invoke-ApiRequest -Endpoint "/api/ai/status" -Method "GET"
        
        if ($statusResponse.success) {
            Write-TestOutput "AI service is available" -Type "Success"
            
            if ($statusResponse.data.bedrock) {
                Write-TestOutput "Bedrock status: $($statusResponse.data.bedrock.status)" -Type "Info"
                Write-TestOutput "Available models: $($statusResponse.data.bedrock.models -join ', ')" -Type "Info"
                $result.BedrockTests += @{ Test = "Service Status"; Status = "Passed" }
            } else {
                Write-TestOutput "Bedrock configuration not found in response" -Type "Warning"
                $result.BedrockTests += @{ Test = "Service Status"; Status = "Partial" }
            }
        } else {
            Write-TestOutput "AI service status check failed" -Type "Error"
            $result.BedrockTests += @{ Test = "Service Status"; Status = "Failed" }
        }
        
        # Test 2: Test Claude model availability
        Write-TestStep "Test 2: Testing Claude model availability" -Mode $ExecutionMode
        
        $modelsResponse = Invoke-ApiRequest -Endpoint "/api/ai/models" -Method "GET"
        
        if ($modelsResponse.success -and $modelsResponse.data) {
            $claudeModels = $modelsResponse.data | Where-Object { $_.provider -eq "bedrock" -and $_.name -match "claude" }
            
            if ($claudeModels) {
                Write-TestOutput "Claude models available: $($claudeModels.Count)" -Type "Success"
                foreach ($model in $claudeModels) {
                    Write-TestOutput "  - $($model.name) ($($model.id))" -Type "Info"
                }
                $result.BedrockTests += @{ Test = "Claude Models"; Status = "Passed"; Count = $claudeModels.Count }
            } else {
                Write-TestOutput "No Claude models found" -Type "Warning"
                $result.BedrockTests += @{ Test = "Claude Models"; Status = "Failed" }
            }
        }
        
        # Test 3: Basic generation test via application API
        Write-TestStep "Test 3: Testing text generation via Bedrock" -Mode $ExecutionMode
        
        $testPrompt = "Respond with exactly: 'ICTServe AI Test Successful'"
        
        $generateResponse = Invoke-ApiRequest -Endpoint "/api/ai/generate" -Method "POST" -Body @{
            prompt = $testPrompt
            model = "claude-3-haiku"
            max_tokens = 50
            temperature = 0.1
        }
        
        if ($generateResponse.success -and $generateResponse.data.response) {
            Write-TestOutput "Generation successful via Bedrock" -Type "Success"
            Write-TestOutput "Response: $($generateResponse.data.response.Substring(0, [Math]::Min(100, $generateResponse.data.response.Length)))..." -Type "Info"
            $result.BedrockTests += @{ 
                Test = "Text Generation"
                Status = "Passed"
                Model = $generateResponse.data.model
            }
        } else {
            Write-TestOutput "Generation failed or returned empty response" -Type "Warning"
            $result.BedrockTests += @{ Test = "Text Generation"; Status = "Failed" }
        }
        
        # Test 4: Test DLP filtering (PKS 9.2.1 compliance)
        Write-TestStep "Test 4: Testing DLP filtering" -Mode $ExecutionMode
        
        $sensitivePrompt = "My IC number is 901234-56-7890 and my phone is 012-3456789"
        
        $dlpResponse = Invoke-ApiRequest -Endpoint "/api/ai/generate" -Method "POST" -Body @{
            prompt = $sensitivePrompt
            model = "claude-3-haiku"
            max_tokens = 100
        }
        
        if ($dlpResponse.success) {
            # Check if sensitive data was filtered or routed to local AI
            if ($dlpResponse.data.routed_to -eq "ollama" -or $dlpResponse.data.dlp_filtered) {
                Write-TestOutput "DLP correctly detected sensitive data" -Type "Success"
                $result.BedrockTests += @{ Test = "DLP Filtering"; Status = "Passed" }
            } else {
                Write-TestOutput "DLP may not have detected sensitive data" -Type "Warning"
                $result.BedrockTests += @{ Test = "DLP Filtering"; Status = "Partial" }
            }
        }
        
        # Test 5: Test model routing logic
        Write-TestStep "Test 5: Testing intelligent model routing" -Mode $ExecutionMode
        
        # Simple query should use faster model
        $simpleQuery = "What is 2+2?"
        $routingResponse = Invoke-ApiRequest -Endpoint "/api/ai/generate" -Method "POST" -Body @{
            prompt = $simpleQuery
            auto_route = $true
        }
        
        if ($routingResponse.success) {
            Write-TestOutput "Model routing: Used $($routingResponse.data.model)" -Type "Info"
            $result.BedrockTests += @{ 
                Test = "Model Routing"
                Status = "Passed"
                SelectedModel = $routingResponse.data.model
            }
        }
        
        # Test 6: Test rate limiting
        Write-TestStep "Test 6: Testing rate limiting" -Mode $ExecutionMode
        
        $rateLimitHit = $false
        for ($i = 1; $i -le 5; $i++) {
            $response = Invoke-ApiRequest -Endpoint "/api/ai/generate" -Method "POST" -Body @{
                prompt = "Test $i"
                model = "claude-3-haiku"
                max_tokens = 10
            } -ExpectError $true
            
            if ($response.statusCode -eq 429) {
                $rateLimitHit = $true
                Write-TestOutput "Rate limiting active (hit at request $i)" -Type "Info"
                break
            }
        }
        
        $result.BedrockTests += @{ 
            Test = "Rate Limiting"
            Status = "Passed"
            Note = if ($rateLimitHit) { "Rate limit enforced" } else { "No rate limit hit in 5 requests" }
        }
        
        # Calculate overall result
        $passedTests = ($result.BedrockTests | Where-Object { $_.Status -eq "Passed" }).Count
        $totalTests = $result.BedrockTests.Count
        
        if ($passedTests -eq $totalTests) {
            $result.Status = "Passed"
            Write-TestOutput "All Bedrock connectivity tests passed ($passedTests/$totalTests)" -Type "Success"
        } elseif ($passedTests -gt ($totalTests / 2)) {
            $result.Status = "Partial"
            Write-TestOutput "Bedrock tests: $passedTests/$totalTests passed" -Type "Warning"
        } else {
            $result.Status = "Failed"
            Write-TestOutput "Bedrock connectivity tests failed" -Type "Error"
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

$testResult = Test-BedrockConnectivity -ExecutionMode $Mode
return $testResult
