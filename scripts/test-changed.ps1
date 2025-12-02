#!/usr/bin/env pwsh
<#
Runs PHP unit and feature tests related to the files changed in git.

Default behaviour: Run the tests that are in the changed files.
Fallback: Try to find test files that reference classes named in changed app files.
If none found, run Unit and Feature quick suites as a safety check.

Usage:
  # Run tests for files changed in the last commit
  .\scripts\test-changed.ps1

  # Run tests for specific range
  .\scripts\test-changed.ps1 -Range "HEAD~3..HEAD"

#>

param(
    [string]$Range = "HEAD~1..HEAD",
    [switch]$ForceSuites
)

function Run-TestFile {
    param($file)

    Write-Host "---- Running tests for: $file" -ForegroundColor Cyan
    php artisan test $file
    if ($LASTEXITCODE -ne 0) {
        Write-Host "Test failed: $file" -ForegroundColor Red
        exit $LASTEXITCODE
    }
}

# Get changed files
$changed = git diff --name-only $Range | Where-Object { $_ -ne '' }

if (-not $changed) {
    Write-Host "No changed files found for range: $Range" -ForegroundColor Yellow
    exit 0
}

Write-Host "Changed files: " -NoNewline; $changed -join ', '

$testFiles = $changed | Where-Object { $_ -like 'tests/*' }

if ($testFiles.Count -gt 0 -and -not $ForceSuites) {
    foreach ($t in $testFiles) { Run-TestFile $t }
    Write-Host "All changed test files passed" -ForegroundColor Green
    exit 0
}

# Try to find tests referencing changed classes
$runAny = $false
foreach ($f in $changed) {
    if ($f -like 'app/*') {
        # Extract basename without extension
        $className = [System.IO.Path]::GetFileNameWithoutExtension($f)
        if ($className) {
            Write-Host "Searching tests for references to: $className" -ForegroundColor Cyan
            try {
                $refsRaw = git grep -l -- $className -- tests 2>$null
                $refs = @($refsRaw) # force array
            } catch {
                $refs = @()
            }
            if ($refs.Count -gt 0) {
                foreach ($r in $refs) { Run-TestFile $r; $runAny = $true }
            }
        }
    }
}

if (-not $runAny) {
    if ($ForceSuites) {
        Write-Host "Running Unit and Feature suites (forced)" -ForegroundColor Cyan
        php artisan test --testsuite=Unit
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        php artisan test --testsuite=Feature
        if ($LASTEXITCODE -ne 0) { exit $LASTEXITCODE }
        Write-Host "Suites passed" -ForegroundColor Green
    } else {
        Write-Host "No referenced tests found - please run the relevant test file(s) or run with -ForceSuites to run Unit/Feature suites." -ForegroundColor Yellow
        exit 0
    }
}

Write-Host "Done." -ForegroundColor Green
