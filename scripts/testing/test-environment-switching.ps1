#Requires -Version 5.1
<#
.SYNOPSIS
    Test ICTServe Environment Switching Scripts

.DESCRIPTION
    Tests the main environment switching functionality for XAMPP, Laragon, and Docker.
    Focuses on the core scripts needed for environment switching.

.EXAMPLE
    .\scripts\test-environment-switching.ps1

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
#>

[CmdletBinding()]
param()

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

$TestResults = @()

function Write-TestResult {
    param(
        [string]$TestName,
        [string]$Result,
        [string]$Message = ''
    )

    $colors = @{
        Pass = 'Green'
        Fail = 'Red'
        Warning = 'Yellow'
    }

    $testResult = [PSCustomObject]@{
        TestName = $TestName
        Result = $Result
        Message = $Message
        Timestamp = Get-Date
    }

    $script:TestResults += $testResult

    Write-Host "[$Result] $TestName" -ForegroundColor $colors[$Result]
    if ($Message) {
        Write-Host "    $Message" -ForegroundColor Gray
    }
}

try {
    Write-Host ""
    Write-Host "ICTServe Environment Switching Test" -ForegroundColor Cyan
    Write-Host "===================================" -ForegroundColor Cyan
    Write-Host ""

    # Test main switch environment script
    Write-Host "Testing Main Environment Switcher..." -ForegroundColor Yellow

    if (Test-Path 'scripts\switch-environment.ps1') {
        try {
            $errors = @()
            [System.Management.Automation.Language.Parser]::ParseFile('scripts\switch-environment.ps1', [ref]$null, [ref]$errors)
            if ($errors.Count -eq 0) {
                Write-TestResult "Main Switch Script Syntax" "Pass" "No syntax errors"
            } else {
                Write-TestResult "Main Switch Script Syntax" "Fail" "$($errors.Count) syntax errors"
            }
        } catch {
            Write-TestResult "Main Switch Script Syntax" "Fail" $_.Exception.Message
        }
    } else {
        Write-TestResult "Main Switch Script Exists" "Fail" "File not found"
    }

    # Test XAMPP scripts
    Write-Host "`nTesting XAMPP Scripts..." -ForegroundColor Yellow

    $xamppScripts = @(
        'scripts\xampp\setup-xampp.ps1',
        'scripts\xampp\start-xampp.ps1',
        'scripts\xampp\stop-xampp.ps1',
        'scripts\xampp\status-xampp.ps1'
    )

    foreach ($script in $xamppScripts) {
        $scriptName = Split-Path $script -Leaf
        if (Test-Path $script) {
            try {
                $errors = @()
                [System.Management.Automation.Language.Parser]::ParseFile($script, [ref]$null, [ref]$errors)
                if ($errors.Count -eq 0) {
                    Write-TestResult "XAMPP $scriptName Syntax" "Pass" "No syntax errors"
                } else {
                    Write-TestResult "XAMPP $scriptName Syntax" "Fail" "$($errors.Count) syntax errors"
                }
            } catch {
                Write-TestResult "XAMPP $scriptName Syntax" "Fail" $_.Exception.Message
            }
        } else {
            Write-TestResult "XAMPP $scriptName Exists" "Fail" "File not found"
        }
    }

    # Test Laragon scripts
    Write-Host "`nTesting Laragon Scripts..." -ForegroundColor Yellow

    $laragonScripts = @(
        'scripts\laragon\setup-laragon.ps1',
        'scripts\laragon\start-laragon.ps1',
        'scripts\laragon\stop-laragon.ps1',
        'scripts\laragon\status-laragon.ps1'
    )

    foreach ($script in $laragonScripts) {
        $scriptName = Split-Path $script -Leaf
        if (Test-Path $script) {
            try {
                $errors = @()
                [System.Management.Automation.Language.Parser]::ParseFile($script, [ref]$null, [ref]$errors)
                if ($errors.Count -eq 0) {
                    Write-TestResult "Laragon $scriptName Syntax" "Pass" "No syntax errors"
                } else {
                    Write-TestResult "Laragon $scriptName Syntax" "Fail" "$($errors.Count) syntax errors"
                }
            } catch {
                Write-TestResult "Laragon $scriptName Syntax" "Fail" $_.Exception.Message
            }
        } else {
            Write-TestResult "Laragon $scriptName Exists" "Fail" "File not found"
        }
    }

    # Test Docker scripts
    Write-Host "`nTesting Docker Scripts..." -ForegroundColor Yellow

    $dockerScripts = @(
        'scripts\docker\setup-docker.ps1',
        'scripts\docker\start-dev.ps1',
        'scripts\docker\stop-dev.ps1',
        'scripts\docker\status-dev.ps1'
    )

    foreach ($script in $dockerScripts) {
        $scriptName = Split-Path $script -Leaf
        if (Test-Path $script) {
            try {
                $errors = @()
                [System.Management.Automation.Language.Parser]::ParseFile($script, [ref]$null, [ref]$errors)
                if ($errors.Count -eq 0) {
                    Write-TestResult "Docker $scriptName Syntax" "Pass" "No syntax errors"
                } else {
                    Write-TestResult "Docker $scriptName Syntax" "Fail" "$($errors.Count) syntax errors"
                }
            } catch {
                Write-TestResult "Docker $scriptName Syntax" "Fail" $_.Exception.Message
            }
        } else {
            Write-TestResult "Docker $scriptName Exists" "Fail" "File not found"
        }
    }

    # Generate summary
    Write-Host "`nTest Summary" -ForegroundColor Cyan
    Write-Host "============" -ForegroundColor Cyan

    $totalTests = $TestResults.Count
    $passedTests = ($TestResults | Where-Object { $_.Result -eq 'Pass' }).Count
    $failedTests = ($TestResults | Where-Object { $_.Result -eq 'Fail' }).Count

    Write-Host "Total Tests: $totalTests" -ForegroundColor White
    Write-Host "Passed: $passedTests" -ForegroundColor Green
    Write-Host "Failed: $failedTests" -ForegroundColor Red

    if ($failedTests -gt 0) {
        Write-Host "`nFailed Tests:" -ForegroundColor Red
        $TestResults | Where-Object { $_.Result -eq 'Fail' } | ForEach-Object {
            Write-Host "  $($_.TestName): $($_.Message)" -ForegroundColor Red
        }
        exit 1
    } else {
        Write-Host "`nAll environment switching scripts are ready!" -ForegroundColor Green
        exit 0
    }
}
catch {
    Write-Host "`nTest execution failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
