#!/usr/bin/env pwsh

<#
.SYNOPSIS
    Sequential Test Runner with Fail-Fast behavior

.DESCRIPTION
    Runs PHPUnit tests one file at a time and stops on first failure.
    Provides clear progress reporting and summary.

.PARAMETER TestDir
    Directory containing test files (default: tests)

.EXAMPLE
    .\scripts\testing\run-tests-sequential.ps1
    .\scripts\testing\run-tests-sequential.ps1 -TestDir tests/Feature
    .\scripts\testing\run-tests-sequential.ps1 -TestDir tests/Unit
#>

param(
    [string]$TestDir = "tests"
)

# Set error action preference
$ErrorActionPreference = "Continue"

# Change to project root (2 levels up from scripts/testing)
$ProjectRoot = Split-Path (Split-Path $PSScriptRoot -Parent) -Parent
Set-Location $ProjectRoot

Write-Host "========================================" -ForegroundColor Blue
Write-Host "Sequential Test Runner (Fail-Fast)" -ForegroundColor Blue
Write-Host "========================================" -ForegroundColor Blue
Write-Host "Test Directory: $TestDir" -ForegroundColor Yellow
Write-Host ""

# Validate test directory exists
if (-not (Test-Path $TestDir)) {
    Write-Host "Error: Test directory '$TestDir' does not exist" -ForegroundColor Red
    exit 1
}

# Find all test files
$TestFiles = Get-ChildItem -Path $TestDir -Filter "*Test.php" -Recurse -File | Sort-Object FullName
$TotalFiles = $TestFiles.Count

if ($TotalFiles -eq 0) {
    Write-Host "No test files found in $TestDir" -ForegroundColor Yellow
    exit 0
}

Write-Host "Found $TotalFiles test files" -ForegroundColor Green
Write-Host ""

# Initialize counters
$Passed = 0
$Failed = 0
$Current = 0

# Arrays to store results
$PassedFiles = @()
$FailedFiles = @()

# Run each test file
foreach ($TestFile in $TestFiles) {
    $Current++
    $RelativePath = $TestFile.FullName.Replace("$ProjectRoot\", "").Replace("$ProjectRoot/", "")
    
    Write-Host "----------------------------------------" -ForegroundColor Blue
    Write-Host "[$Current/$TotalFiles] Running: $RelativePath" -ForegroundColor Yellow
    Write-Host "----------------------------------------" -ForegroundColor Blue
    
    # Run the test
    $TestCommand = "php artisan test `"$RelativePath`""
    $Process = Start-Process -FilePath "php" -ArgumentList "artisan", "test", "`"$RelativePath`"" -NoNewWindow -Wait -PassThru
    
    if ($Process.ExitCode -eq 0) {
        $Passed++
        $PassedFiles += $RelativePath
        Write-Host "✓ PASSED" -ForegroundColor Green
    } else {
        $Failed++
        $FailedFiles += $RelativePath
        Write-Host "✗ FAILED" -ForegroundColor Red
        Write-Host ""
        Write-Host "========================================" -ForegroundColor Red
        Write-Host "Test execution stopped due to failure" -ForegroundColor Red
        Write-Host "========================================" -ForegroundColor Red
        break
    }
    
    Write-Host ""
}

# Print summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Blue
Write-Host "TEST SUMMARY" -ForegroundColor Blue
Write-Host "========================================" -ForegroundColor Blue
Write-Host "Total Files:   $TotalFiles" -ForegroundColor Yellow
Write-Host "Tests Run:     $Current" -ForegroundColor Yellow
Write-Host "Passed:        $Passed" -ForegroundColor Green
Write-Host "Failed:        $Failed" -ForegroundColor Red
Write-Host ""

# Print passed files
if ($Passed -gt 0) {
    Write-Host "Passed Files:" -ForegroundColor Green
    foreach ($File in $PassedFiles) {
        Write-Host "  ✓ $File" -ForegroundColor Green
    }
    Write-Host ""
}

# Print failed files
if ($Failed -gt 0) {
    Write-Host "Failed Files:" -ForegroundColor Red
    foreach ($File in $FailedFiles) {
        Write-Host "  ✗ $File" -ForegroundColor Red
    }
    Write-Host ""
    exit 1
}

Write-Host "All tests passed!" -ForegroundColor Green
exit 0
