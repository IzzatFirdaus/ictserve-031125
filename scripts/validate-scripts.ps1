#Requires -Version 5.1
<#
.SYNOPSIS
    Validate PowerShell scripts for syntax errors

.DESCRIPTION
    Tests all PowerShell scripts in the scripts directory for syntax errors
    and basic functionality without executing them fully.

.EXAMPLE
    .\scripts\validate-scripts.ps1
    Validate all scripts

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$script:ScriptRoot = Split-Path -Parent $MyInvocation.MyCommand.Path
$script:ProjectRoot = $script:ScriptRoot
$script:ValidationResults = @()

function Write-ValidationResult {
    param(
        [string]$ScriptPath,
        [ValidateSet('Pass', 'Fail', 'Warning')]
        [string]$Result,
        [string]$Message = ''
    )

    $colors = @{
        Pass = 'Green'
        Fail = 'Red'
        Warning = 'Yellow'
    }

    $icons = @{
        Pass = '✅'
        Fail = '❌'
        Warning = '⚠️'
    }

    $validationResult = [PSCustomObject]@{
        ScriptPath = $ScriptPath
        Result = $Result
        Message = $Message
        Timestamp = Get-Date
    }

    $script:ValidationResults += $validationResult

    $relativePath = $ScriptPath -replace [regex]::Escape($script:ProjectRoot), '.'
    Write-Host "$($icons[$Result]) $relativePath`: " -NoNewline -ForegroundColor $colors[$Result]
    Write-Host $Result -ForegroundColor $colors[$Result]

    if ($Message) {
        Write-Host "    $Message" -ForegroundColor Gray
    }
}

function Test-PowerShellSyntax {
    param([string]$ScriptPath)

    try {
        # Parse the script to check for syntax errors
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

function Get-PowerShellScripts {
    param([string]$Path)

    return Get-ChildItem -Path $Path -Filter "*.ps1" -Recurse | Where-Object {
        $_.Name -notmatch '^\..*' -and
        $_.Directory.Name -ne 'node_modules' -and
        $_.Directory.Name -ne 'vendor'
    }
}

try {
    Write-Host "`n🔍 PowerShell Script Validation" -ForegroundColor Cyan
    Write-Host "=" * 35 -ForegroundColor Cyan
    Write-Host ""

    # Get all PowerShell scripts
    Write-Host "Scanning for PowerShell scripts..." -ForegroundColor Yellow
    $scriptFiles = @(Get-PowerShellScripts -Path $script:ProjectRoot)

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
    Write-Host "`n📊 Validation Summary" -ForegroundColor Cyan
    Write-Host "=" * 20 -ForegroundColor Cyan

    $totalScripts = $script:ValidationResults.Count
    $passedScripts = ($script:ValidationResults | Where-Object { $_.Result -eq 'Pass' }).Count
    $failedScripts = ($script:ValidationResults | Where-Object { $_.Result -eq 'Fail' }).Count
    $warningScripts = ($script:ValidationResults | Where-Object { $_.Result -eq 'Warning' }).Count

    Write-Host "Total Scripts: $totalScripts" -ForegroundColor White
    Write-Host "Passed: $passedScripts" -ForegroundColor Green
    Write-Host "Failed: $failedScripts" -ForegroundColor Red
    Write-Host "Warnings: $warningScripts" -ForegroundColor Yellow

    # Show failed scripts
    if ($failedScripts -gt 0) {
        Write-Host "`n❌ Failed Scripts:" -ForegroundColor Red
        $script:ValidationResults | Where-Object { $_.Result -eq 'Fail' } | ForEach-Object {
            $relativePath = $_.ScriptPath -replace [regex]::Escape($script:ProjectRoot), '.'
            Write-Host "  $relativePath`: $($_.Message)" -ForegroundColor Red
        }
    }

    # Exit with appropriate code
    if ($failedScripts -gt 0) {
        Write-Host "`n❌ Some scripts have syntax errors. Please fix them." -ForegroundColor Red
        exit 1
    }
    else {
        Write-Host "`n✅ All scripts passed validation!" -ForegroundColor Green
        exit 0
    }
}
catch {
    Write-Host "`n❌ Validation failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
