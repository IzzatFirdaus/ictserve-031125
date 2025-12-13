#!/usr/bin/env pwsh
<#
Simple helper wrapper for running tests during development.
Usage:
  # PHPUnit (default)
  .\scripts\run-test.ps1 -File tests/Unit/Services/SLAManagementServiceTest.php
  .\scripts\run-test.ps1 -File tests/Feature/ApprovalWorkflowTest.php -Filter test_can_approve -Kind phpunit

  # Playwright E2E
  .\scripts\run-test.ps1 -File tests/e2e/loan.spec.ts -Filter "submit" -Kind e2e

Options:
  -File   : Path to test file (optional - if omitted, runs full php tests)
  -Filter : Filter to pass to php artisan test or playwright -g
  -Kind   : phpunit|e2e (default: phpunit)
#>

param(
    [string]$File,
    [string]$Filter,
    [ValidateSet('phpunit','e2e')][string]$Kind = 'phpunit'
)

if ($Kind -eq 'phpunit') {
    if ($null -ne $File -and $File -ne '') {
        $cmd = "php artisan test $File"
        if ($Filter) { $cmd += " --filter=`"$Filter`"" }
    } else {
        $cmd = "php artisan test"
    }
    Write-Host "Running: $cmd" -ForegroundColor Cyan
    iex $cmd
    exit $LASTEXITCODE
}

if ($Kind -eq 'e2e') {
    if (-not (Test-Path package.json)) {
        Write-Host "No package.json found - run in project root" -ForegroundColor Red; exit 1
    }

    if ($null -ne $File -and $File -ne '') {
        $cmd = "npx playwright test $File"
        if ($Filter) { $cmd += " -g `"$Filter`"" }
    } else {
        $cmd = "npm run test:e2e"
    }
    Write-Host "Running: $cmd" -ForegroundColor Cyan
    iex $cmd
    exit $LASTEXITCODE
}
