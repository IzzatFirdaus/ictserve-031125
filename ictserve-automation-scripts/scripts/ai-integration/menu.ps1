#!/usr/bin/env pwsh
<#
.SYNOPSIS
    AI Integration Testing Menu - Interactive menu for AI automation scripts
.DESCRIPTION
    Provides an interactive menu for running AI integration automation scripts
    including Ollama local AI, AWS Bedrock cloud AI, and MCP server testing.
.NOTES
    Part of ICTServe Comprehensive Automation Suite
#>

param(
    [ValidateSet('Headless', 'Visual', 'Demo', 'Interactive', 'Recording')]
    [string]$Mode = 'Visual',
    [switch]$ReturnToMain
)

$ErrorActionPreference = 'Stop'
$ScriptRoot = $PSScriptRoot

. "$ScriptRoot\..\..\utilities\common-functions.ps1"
. "$ScriptRoot\..\..\utilities\browser-automation.ps1"
. "$ScriptRoot\..\..\utilities\visual-demo-helpers.ps1"

function Show-AIIntegrationMenu {
    param([string]$CurrentMode = 'Visual')
    
    Clear-Host
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host "    AI Integration Testing - Cloud Hybrid Architecture" -ForegroundColor White
    Write-Host "    Mode: [$CurrentMode]" -ForegroundColor Yellow
    Write-Host "===============================================" -ForegroundColor Cyan
    Write-Host ""
    
    Write-Host "OLLAMA LOCAL AI TESTING:" -ForegroundColor Green
    Write-Host "  1.  Test Ollama Server Connectivity" -ForegroundColor White
    Write-Host "  2.  Test Local Model Management" -ForegroundColor White
    Write-Host "  3.  Test FAQ Bot Responses (RAG)" -ForegroundColor White
    Write-Host "  4.  Test Sensitive Data Processing (PKS 4.2)" -ForegroundColor White
    Write-Host "  5.  Test Embedding Generation" -ForegroundColor White
    Write-Host "  6.  Test Conversation Context" -ForegroundColor White
    Write-Host "  7.  Test Local AI Performance" -ForegroundColor White
    Write-Host "  8.  Test Model Switching" -ForegroundColor White
    Write-Host "  9.  Test Offline AI Functionality" -ForegroundColor White
    Write-Host "  10. Test Local AI Security" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AWS BEDROCK CLOUD AI TESTING:" -ForegroundColor Green
    Write-Host "  11. Test AWS Bedrock Connectivity" -ForegroundColor White
    Write-Host "  12. Test Claude Model Integration (Opus/Sonnet/Haiku)" -ForegroundColor White
    Write-Host "  13. Test Model Routing Logic" -ForegroundColor White
    Write-Host "  14. Test DLP Filtering (PKS 9.2.1)" -ForegroundColor White
    Write-Host "  15. Test Public Data Processing" -ForegroundColor White
    Write-Host "  16. Test API Rate Limiting" -ForegroundColor White
    Write-Host "  17. Test Cost Optimization" -ForegroundColor White
    Write-Host "  18. Test Multi-region Failover" -ForegroundColor White
    Write-Host "  19. Test Cloud AI Security" -ForegroundColor White
    Write-Host "  20. Test Bedrock Model Performance" -ForegroundColor White
    Write-Host ""
    
    Write-Host "INTELLIGENT MODEL ROUTING:" -ForegroundColor Green
    Write-Host "  21. Test Data Sensitivity Detection" -ForegroundColor White
    Write-Host "  22. Test Query Complexity Analysis" -ForegroundColor White
    Write-Host "  23. Test Fallback Mechanisms" -ForegroundColor White
    Write-Host "  24. Test Load Balancing" -ForegroundColor White
    Write-Host "  25. Test Cost-Performance Optimization" -ForegroundColor White
    Write-Host ""
    
    Write-Host "CONVERSATION MANAGEMENT:" -ForegroundColor Green
    Write-Host "  31. Test Conversation Creation" -ForegroundColor White
    Write-Host "  32. Test Conversation Persistence" -ForegroundColor White
    Write-Host "  33. Test Conversation History" -ForegroundColor White
    Write-Host "  34. Test Conversation Sharing" -ForegroundColor White
    Write-Host "  35. Test Conversation Deletion" -ForegroundColor White
    Write-Host ""
    
    Write-Host "STREAMING & REAL-TIME FEATURES:" -ForegroundColor Green
    Write-Host "  41. Test Server-Sent Events (SSE)" -ForegroundColor White
    Write-Host "  42. Test Streaming Response Handling" -ForegroundColor White
    Write-Host "  43. Test Stream Interruption" -ForegroundColor White
    Write-Host "  44. Test Stream Error Handling" -ForegroundColor White
    Write-Host "  45. Test Stream Performance" -ForegroundColor White
    Write-Host ""
    
    Write-Host "WEB-AUGMENTED RESPONSES:" -ForegroundColor Green
    Write-Host "  51. Test DuckDuckGo Integration" -ForegroundColor White
    Write-Host "  52. Test Web Search Filtering" -ForegroundColor White
    Write-Host "  53. Test Search Result Synthesis" -ForegroundColor White
    Write-Host "  54. Test Search Rate Limiting" -ForegroundColor White
    Write-Host "  55. Test Search Result Caching" -ForegroundColor White
    Write-Host ""
    
    Write-Host "MCP SERVER INTEGRATION:" -ForegroundColor Green
    Write-Host "  61. Test MCP Server Connectivity" -ForegroundColor White
    Write-Host "  62. Test AI Assistant Tools" -ForegroundColor White
    Write-Host "  63. Test Tool Authentication" -ForegroundColor White
    Write-Host "  64. Test Tool Performance" -ForegroundColor White
    Write-Host "  65. Test Tool Error Handling" -ForegroundColor White
    Write-Host ""
    
    Write-Host "AUTOMATED OPERATIONS:" -ForegroundColor Yellow
    Write-Host "  80. Run All Ollama Local Tests" -ForegroundColor White
    Write-Host "  81. Run All AWS Bedrock Tests" -ForegroundColor White
    Write-Host "  82. Run All Model Routing Tests" -ForegroundColor White
    Write-Host "  83. Run All Conversation Tests" -ForegroundColor White
    Write-Host "  84. Run All Streaming Tests" -ForegroundColor White
    Write-Host "  85. Run All Web-Augmented Tests" -ForegroundColor White
    Write-Host "  86. Run All MCP Server Tests" -ForegroundColor White
    Write-Host "  87. Run Complete AI Integration Suite (All 89 Scripts)" -ForegroundColor White
    Write-Host ""
    
    Write-Host "UTILITIES:" -ForegroundColor Cyan
    Write-Host "  M.  Change Execution Mode" -ForegroundColor White
    Write-Host "  H.  Help for this category" -ForegroundColor White
    Write-Host "  S.  Search specific test" -ForegroundColor White
    Write-Host "  0.  Back to Main Menu" -ForegroundColor White
    Write-Host ""
}

function Start-AIIntegrationMenu {
    param([string]$InitialMode = 'Visual')
    
    $currentMode = $InitialMode
    
    do {
        Show-AIIntegrationMenu -CurrentMode $currentMode
        $selection = Read-Host "Select option"
        
        switch ($selection.ToUpper()) {
            '0' { return }
            'M' {
                Write-Host "`nSelect Mode:" -ForegroundColor Yellow
                Write-Host "  1. Headless (Fast)" -ForegroundColor White
                Write-Host "  2. Visual (Live Browser)" -ForegroundColor White
                Write-Host "  3. Demo (Annotated)" -ForegroundColor White
                Write-Host "  4. Interactive (Pauses)" -ForegroundColor White
                Write-Host "  5. Recording (Video)" -ForegroundColor White
                $modeChoice = Read-Host "Select mode (1-5)"
                $currentMode = switch ($modeChoice) {
                    '1' { 'Headless' }
                    '2' { 'Visual' }
                    '3' { 'Demo' }
                    '4' { 'Interactive' }
                    '5' { 'Recording' }
                    default { $currentMode }
                }
            }
            'H' {
                Write-Host "`nAI Integration Help:" -ForegroundColor Yellow
                Write-Host "  - Tests Cloud Hybrid AI architecture" -ForegroundColor Gray
                Write-Host "  - Ollama: Local AI for sensitive data (PKS 4.2)" -ForegroundColor Gray
                Write-Host "  - AWS Bedrock: Cloud AI for public data" -ForegroundColor Gray
                Write-Host "  - Intelligent routing based on data sensitivity" -ForegroundColor Gray
                Write-Host "`nPress any key to continue..." -ForegroundColor Gray
                $null = $Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown')
            }
            default {
                Write-Host "`nScript execution placeholder - implement script mapping" -ForegroundColor Yellow
                Start-Sleep -Seconds 1
            }
        }
    } while ($true)
}

if ($MyInvocation.InvocationName -ne '.') {
    Start-AIIntegrationMenu -InitialMode $Mode
}
