#Requires -Version 5.1
<#
.SYNOPSIS
    Simple PowerShell script validation

.DESCRIPTION
    Tests PowerShell scripts for syntax errors

.EXAMPLE
    .\scripts\validate-scripts-simple.ps1

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$ScriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = $ScriptRoot
$ValidationResults = @()

function Write-ValidationResult {
    param(
        [string]$ScriptPath,
        [string]$Result,
        [string]$Message = ''
    )

    $colors = @{
        Pass = 'Green'
        Fail = 'Red'
        Warning = 'Yellow'
    }

    $icons = @{
        Pass = 'PASS'
        Fail = 'FAIL'
        Warning = 'WARN'
    }

    $validationResult = [PSCustomObject]@{
        ScriptPath = $ScriptPath
        Result = $Result
        Message = $Message
        Timestamp = Get-Date
    }

    $script:ValidationResults += $validationResult

    $relativePath = $ScriptPath -replace [regex]::Escape($ProjectRoot), '.'
    Write-Host "[$($icons[$Result])] $relativePath : $Result" -ForegroundColor $colors[$Result]

    if ($Message) {
        Write-Host "    $Message" -ForegroundColor Gray
    }
}

function Test-PowerShellSyntax {
    param([string]$ScriptPath)

    try {
        $tokens = $null
        $errors = $null
        $ast = [System.Management.Automation.Language.Parser]::ParseFile($ScriptPath, [ref]$tokens, [ref]$errors)

        if ($errors.Count -gt 0) {
            $errorMessages = $errors | ForEach-Object { "$($_.Message) (Line $($_.Extent.StartLineNumber))" }
            return @{
                Valid = $false
                Errors = $errorMessages
            }
        }

        return @{
            Valid = $true
            Errors = @()
        }
    }
    catch {
        return @{
            Valid = $false
            Errors = @($_.Exception.Message)
        }
    }
}

try {
    Write-Host ""
    Write-Host "PowerShell Script Validation" -ForegroundColor Cyan
    Write-Host "============================" -ForegroundColor Cyan
    Write-Host ""

    # Get all PowerShell scripts
    Write-Host "Scanning for PowerShell scripts..." -ForegroundColor Yellow

    $scriptFiles = Get-ChildItem -Path $ProjectRoot -Filter "*.ps1" -Recurse | Where-Object {
        $_.Name -notmatch '^\..*' -and
        $_.Directory.Name -ne 'node_modules' -and
        $_.Directory.Name -ne 'vendor'
    }

    if ($scriptFiles.Count -eq 0) {
        Write-Host "No PowerShell scripts found to validate" -ForegroundColor Yellow
        exit 0
    }

    Write-Host "Found $($scriptFiles.Count) PowerShell scripts to validate" -ForegroundColor Yellow
    Write-Host ""

    # Validate each script
    foreach ($scriptFile in $scriptFiles) {
        try {
            $validation = Test-PowerShellSyntax -ScriptPath $scriptFile.FullName

            if ($validation.Valid) {
                Write-ValidationResult -ScriptPath $scriptFile.FullName -Result 'Pass' -Message 'Syntax is valid'
            }
            else {
                $errorMessage = $validation.Errors -join '; '
                Write-ValidationResult -ScriptPath $scriptFile.FullName -Result 'Fail' -Message $errorMessage
            }
        }
        catch {
            Write-ValidationResult -ScriptPath $scriptFile.FullName -Result 'Fail' -Message "Validation error: $($_.Exception.Message)"
        }
    }

    # Generate summary
    Write-Host ""
    Write-Host "Validation Summary" -ForegroundColor Cyan
    Write-Host "==================" -ForegroundColor Cyan

    $totalScripts = $ValidationResults.Count
    $passedScripts = ($ValidationResults | Where-Object { $_.Result -eq 'Pass' }).Count
    $failedScripts = ($ValidationResults | Where-Object { $_.Result -eq 'Fail' }).Count

    Write-Host "Total Scripts: $totalScripts" -ForegroundColor White
    Write-Host "Passed: $passedScripts" -ForegroundColor Green
    Write-Host "Failed: $failedScripts" -ForegroundColor Red

    # Show failed scripts
    if ($failedScripts -gt 0) {
        Write-Host ""
        Write-Host "Failed Scripts:" -ForegroundColor Red
        $ValidationResults | Where-Object { $_.Result -eq 'Fail' } | ForEach-Object {
            $relativePath = $_.ScriptPath -replace [regex]::Escape($ProjectRoot), '.'
            Write-Host "  $relativePath : $($_.Message)" -ForegroundColor Red
        }
    }

    # Exit with appropriate code
    if ($failedScripts -gt 0) {
        Write-Host ""
        Write-Host "Some scripts have syntax errors. Please fix them." -ForegroundColor Red
        exit 1
    }
    else {
        Write-Host ""
        Write-Host "All scripts passed validation!" -ForegroundColor Green
        exit 0
    }
}
catch {
    Write-Host ""
    Write-Host "Validation failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
