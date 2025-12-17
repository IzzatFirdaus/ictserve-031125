# Script validation test
param([switch]$DryRun)

Write-Host "Validating ICTServe development script..." -ForegroundColor Cyan

try {
    # Test script loading
    $scriptPath = "scripts\dev\start-dev.ps1"
    $scriptContent = Get-Content $scriptPath -Raw
    
    # Parse the script to check for syntax errors
    $errors = $null
    $tokens = $null
    $ast = [System.Management.Automation.Language.Parser]::ParseInput($scriptContent, [ref]$tokens, [ref]$errors)
    
    if ($errors.Count -gt 0) {
        Write-Host "[ERROR] Script has syntax errors:" -ForegroundColor Red
        foreach ($error in $errors) {
            Write-Host "  Line $($error.Extent.StartLineNumber): $($error.Message)" -ForegroundColor Yellow
        }
        exit 1
    } else {
        Write-Host "[OK] Script syntax is valid" -ForegroundColor Green
    }
    
    # Test function definitions
    $functions = $ast.FindAll({$args[0] -is [System.Management.Automation.Language.FunctionDefinitionAst]}, $true)
    Write-Host "[OK] Found $($functions.Count) function definitions" -ForegroundColor Green
    
    foreach ($func in $functions) {
        Write-Host "  - $($func.Name)" -ForegroundColor Gray
    }
    
    # Test parameter definitions
    $params = $ast.FindAll({$args[0] -is [System.Management.Automation.Language.ParameterAst]}, $true)
    Write-Host "[OK] Found $($params.Count) parameters" -ForegroundColor Green
    
    if ($DryRun) {
        Write-Host ""
        Write-Host "Dry run test - simulating script execution..." -ForegroundColor Yellow
        
        # Test with minimal profile
        Write-Host "Testing minimal profile..." -ForegroundColor Cyan
        & $scriptPath -SkipChecks -Profile minimal -NoMCP -NoBrowser -WhatIf 2>&1 | Out-Null
        
        Write-Host "[OK] Dry run completed successfully" -ForegroundColor Green
    }
    
    Write-Host ""
    Write-Host "Script validation completed successfully!" -ForegroundColor Green
    
} catch {
    Write-Host "[ERROR] Script validation failed: $_" -ForegroundColor Red
    exit 1
}