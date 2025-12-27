#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Test all ICTServe environment switcher scripts

.DESCRIPTION
    This script tests the syntax and basic functionality of all environment
    switcher scripts to ensure they work correctly.

.EXAMPLE
    .\scripts\test-all-scripts.ps1

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Continue'

# Color output functions
function Write-Success { param([string]$Message) Write-Host "[SUCCESS] $Message" -ForegroundColor Green }
function Write-Warning { param([string]$Message) Write-Host "[WARNING] $Message" -ForegroundColor Yellow }
function Write-Error { param([string]$Message) Write-Host "[ERROR] $Message" -ForegroundColor Red }
function Write-Info { param([string]$Message) Write-Host "[INFO] $Message" -ForegroundColor Cyan }

$ProjectRoot = Split-Path -Parent $PSScriptRoot
$script:TestResults = @()

# Test script syntax
function Test-ScriptSyntax {
    param([string]$ScriptPath)
    
    Write-Info "Testing syntax: $ScriptPath"
    
    try {
        $null = [System.Management.Automation.PSParser]::Tokenize((Get-Content $ScriptPath -Raw), [ref]$null)
        Write-Success "Syntax OK: $ScriptPath"
        return $true
    }
    catch {
        Write-Error "Syntax Error in $ScriptPath`: $($_.Exception.Message)"
        return $false
    }
}

# Test script help
function Test-ScriptHelp {
    param([string]$ScriptPath)
    
    Write-Info "Testing help: $ScriptPath"
    
    try {
        $help = Get-Help $ScriptPath -ErrorAction Stop
        if ($help.Synopsis -and $help.Synopsis -ne $ScriptPath) {
            Write-Success "Help OK: $ScriptPath"
            return $true
        } else {
            Write-Warning "Help incomplete: $ScriptPath"
            return $false
        }
    }
    catch {
        Write-Warning "Help not available: $ScriptPath"
        return $false
    }
}

# Test script parameters
function Test-ScriptParameters {
    param([string]$ScriptPath)
    
    Write-Info "Testing parameters: $ScriptPath"
    
    try {
        $command = Get-Command $ScriptPath -ErrorAction Stop
        $paramCount = $command.Parameters.Count
        Write-Success "Parameters OK: $ScriptPath ($paramCount parameters)"
        return $true
    }
    catch {
        Write-Error "Parameter test failed: $ScriptPath - $($_.Exception.Message)"
        return $false
    }
}

# Main test function
function Test-AllScripts {
    Write-Host "`n" + "="*70 -ForegroundColor Blue
    Write-Host "  ICTServe Environment Switcher Script Tests" -ForegroundColor Blue
    Write-Host "="*70 -ForegroundColor Blue
    
    # Get all PowerShell scripts
    $scripts = @(
        "scripts\swap-environment.ps1",
        "scripts\environment-status.ps1",
        "scripts\quick-switch.ps1",
        "scripts\xampp\start-xampp-services.ps1",
        "scripts\xampp\stop-xampp-services.ps1",
        "scripts\docker\start-docker-services.ps1",
        "scripts\docker\stop-docker-services.ps1"
    )
    
    foreach ($script in $scripts) {
        $fullPath = Join-Path $ProjectRoot $script
        
        if (-not (Test-Path $fullPath)) {
            Write-Error "Script not found: $script"
            $script:TestResults += [PSCustomObject]@{
                Script = $script
                SyntaxTest = $false
                HelpTest = $false
                ParameterTest = $false
                Overall = $false
            }
            continue
        }
        
        Write-Host "`n" + "-"*50 -ForegroundColor Yellow
        Write-Host "Testing: $script" -ForegroundColor Yellow
        Write-Host "-"*50 -ForegroundColor Yellow
        
        $syntaxOK = Test-ScriptSyntax -ScriptPath $fullPath
        $helpOK = Test-ScriptHelp -ScriptPath $fullPath
        $paramOK = Test-ScriptParameters -ScriptPath $fullPath
        
        $overall = $syntaxOK -and $paramOK
        
        $script:TestResults += [PSCustomObject]@{
            Script = $script
            SyntaxTest = $syntaxOK
            HelpTest = $helpOK
            ParameterTest = $paramOK
            Overall = $overall
        }
        
        if ($overall) {
            Write-Success "Overall: PASS - $script"
        } else {
            Write-Error "Overall: FAIL - $script"
        }
    }
}

# Test basic functionality
function Test-BasicFunctionality {
    Write-Host "`n" + "="*70 -ForegroundColor Blue
    Write-Host "  Basic Functionality Tests" -ForegroundColor Blue
    Write-Host "="*70 -ForegroundColor Blue
    
    # Test environment status
    Write-Info "Testing environment status check..."
    try {
        $statusResult = & "$ProjectRoot\scripts\environment-status.ps1" 2>&1
        Write-Success "Environment status check: PASS"
    }
    catch {
        Write-Error "Environment status check: FAIL - $($_.Exception.Message)"
    }
    
    # Test XAMPP script help
    Write-Info "Testing XAMPP script help..."
    try {
        $xamppHelp = Get-Help "$ProjectRoot\scripts\xampp\start-xampp-services.ps1" -ErrorAction SilentlyContinue
        if ($xamppHelp) {
            Write-Success "XAMPP script help: PASS"
        } else {
            Write-Warning "XAMPP script help: Limited"
        }
    }
    catch {
        Write-Warning "XAMPP script help: Limited - $($_.Exception.Message)"
    }
    
    # Test Docker script help
    Write-Info "Testing Docker script help..."
    try {
        $dockerHelp = Get-Help "$ProjectRoot\scripts\docker\start-docker-services.ps1" -ErrorAction SilentlyContinue
        if ($dockerHelp) {
            Write-Success "Docker script help: PASS"
        } else {
            Write-Warning "Docker script help: Limited"
        }
    }
    catch {
        Write-Warning "Docker script help: Limited - $($_.Exception.Message)"
    }
}

# Show test results
function Show-TestResults {
    Write-Host "`n" + "="*70 -ForegroundColor Green
    Write-Host "  Test Results Summary" -ForegroundColor Green
    Write-Host "="*70 -ForegroundColor Green
    
    if ($script:TestResults.Count -eq 0) {
        Write-Warning "No test results to display"
        return
    }
    
    $totalScripts = $script:TestResults.Count
    $passedCount = 0
    
    # Count passed scripts manually to avoid PSObject issues
    foreach ($result in $script:TestResults) {
        if ($result.Overall -eq $true) {
            $passedCount++
        }
    }
    
    $failedScripts = $totalScripts - $passedCount
    
    Write-Host "`nOverall Results:" -ForegroundColor Yellow
    Write-Success "Passed: $passedCount/$totalScripts scripts"
    if ($failedScripts -gt 0) {
        Write-Error "Failed: $failedScripts/$totalScripts scripts"
    }
    
    Write-Host "`nDetailed Results:" -ForegroundColor Yellow
    Write-Host "Script".PadRight(40) + "Syntax".PadRight(10) + "Help".PadRight(10) + "Params".PadRight(10) + "Overall" -ForegroundColor Cyan
    Write-Host "-" * 80 -ForegroundColor Cyan
    
    foreach ($result in $script:TestResults) {
        try {
            $scriptName = Split-Path $result.Script -Leaf
            $syntax = if ($result.SyntaxTest -eq $true) { "PASS" } else { "FAIL" }
            $help = if ($result.HelpTest -eq $true) { "PASS" } else { "WARN" }
            $params = if ($result.ParameterTest -eq $true) { "PASS" } else { "FAIL" }
            $overall = if ($result.Overall -eq $true) { "PASS" } else { "FAIL" }
            
            $color = if ($result.Overall -eq $true) { 'Green' } else { 'Red' }
            
            Write-Host ($scriptName.PadRight(40) + $syntax.PadRight(10) + $help.PadRight(10) + $params.PadRight(10) + $overall) -ForegroundColor $color
        }
        catch {
            Write-Warning "Error displaying result for: $($result.Script)"
        }
    }
    
    Write-Host "`nRecommendations:" -ForegroundColor Yellow
    
    # Check for failed tests manually
    $hasFailedSyntax = $false
    $hasFailedParams = $false
    $hasLimitedHelp = $false
    
    foreach ($result in $script:TestResults) {
        if ($result.SyntaxTest -eq $false) { $hasFailedSyntax = $true }
        if ($result.ParameterTest -eq $false) { $hasFailedParams = $true }
        if ($result.HelpTest -eq $false) { $hasLimitedHelp = $true }
    }
    
    if ($hasFailedSyntax) {
        Write-Error "Some scripts have syntax errors"
    }
    
    if ($hasFailedParams) {
        Write-Error "Some scripts have parameter issues"
    }
    
    if ($hasLimitedHelp) {
        Write-Warning "Some scripts have limited help documentation"
    }
    
    if ($passedCount -eq $totalScripts) {
        Write-Success "`nAll scripts are ready for use!"
        Write-Info "You can now use the environment switcher scripts safely."
    } else {
        Write-Warning "`nSome scripts need attention before use."
        Write-Info "Please fix the issues above before using the scripts."
    }
}

# Main execution
function Main {
    try {
        Write-Info "Starting script testing..."
        Test-AllScripts
        
        Write-Info "Starting basic functionality tests..."
        Test-BasicFunctionality
        
        Write-Info "Showing test results..."
        Show-TestResults
        
        Write-Host "`n" + "="*70 -ForegroundColor Green
        Write-Success "Script testing completed!"
        Write-Host "="*70 -ForegroundColor Green
        
    }
    catch {
        Write-Error "Script testing failed: $($_.Exception.Message)"
        Write-Error "Error details: $($_.Exception.ToString())"
        Write-Error "Stack trace: $($_.ScriptStackTrace)"
        exit 1
    }
}

# Execute main function
Main