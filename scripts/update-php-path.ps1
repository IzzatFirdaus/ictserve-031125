# ICTServe PHP PATH Updater
# This script directly updates the PATH to use a specific PHP version

param(
    [string]$Version = "8.4"
)

$laragonPath = "C:\laragon"
$phpBasePath = "$laragonPath\bin\php"

Write-Host "ICTServe PHP PATH Updater" -ForegroundColor Cyan
Write-Host "=========================" -ForegroundColor Cyan

# Find available PHP versions
Write-Host "Scanning for PHP versions..." -ForegroundColor Yellow
$availableVersions = @()
if (Test-Path $phpBasePath) {
    Get-ChildItem $phpBasePath -Directory | ForEach-Object {
        $versionDir = $_.Name
        $phpExe = Join-Path $_.FullName "php.exe"
        if (Test-Path $phpExe) {
            $availableVersions += @{
                Name = $versionDir
                Path = $_.FullName
                Exe = $phpExe
            }
            Write-Host "  Found: $versionDir" -ForegroundColor Green
        }
    }
}

if ($availableVersions.Count -eq 0) {
    Write-Host "ERROR: No PHP versions found in $phpBasePath" -ForegroundColor Red
    exit 1
}

# Find the best match for the requested version
$targetPhp = $null
foreach ($php in $availableVersions) {
    if ($php.Name -like "*$Version*") {
        $targetPhp = $php
        break
    }
}

if (-not $targetPhp) {
    Write-Host "ERROR: PHP $Version not found" -ForegroundColor Red
    Write-Host ""
    Write-Host "Available PHP versions:" -ForegroundColor Yellow
    foreach ($php in $availableVersions) {
        Write-Host "  $($php.Name)" -ForegroundColor Green
    }
    exit 1
}

Write-Host ""
Write-Host "Target PHP: $($targetPhp.Name)" -ForegroundColor Green
Write-Host "Path: $($targetPhp.Path)" -ForegroundColor Gray

Write-Host "Current PHP:" -ForegroundColor Yellow
$currentPhp = php -v 2>$null | Select-Object -First 1
if ($currentPhp) {
    Write-Host $currentPhp -ForegroundColor White
} else {
    Write-Host "PHP not found in PATH" -ForegroundColor Red
}

Write-Host ""
Write-Host "Current PHP:" -ForegroundColor Yellow
$currentPhp = php -v 2>$null | Select-Object -First 1
if ($currentPhp) {
    Write-Host $currentPhp -ForegroundColor White
} else {
    Write-Host "PHP not found in PATH" -ForegroundColor Red
}

Write-Host ""
Write-Host "Updating PATH to use $($targetPhp.Name)..." -ForegroundColor Green

# Remove existing PHP paths from PATH (including XAMPP, other Laragon versions)
$currentPath = $env:Path
$pathParts = $currentPath -split ';'
$filteredParts = $pathParts | Where-Object {
    $_ -notlike "*\bin\php\*" -and
    $_ -notlike "*xampp*php*" -and
    $_ -notlike "*php*" -or
    $_ -eq $targetPhp.Path
}

# Add new PHP path at the beginning for highest priority
$newPath = "$($targetPhp.Path);" + ($filteredParts -join ';')
$env:Path = $newPath

Write-Host "PATH updated successfully!" -ForegroundColor Green

Write-Host ""
Write-Host "Verifying new PHP version..." -ForegroundColor Yellow
$newPhp = php -v 2>$null | Select-Object -First 1
if ($newPhp) {
    Write-Host $newPhp -ForegroundColor White

    # Check if it's the expected version
    if ($newPhp -like "*$Version*") {
        Write-Host ""
        Write-Host "SUCCESS! PHP $Version is now active in this terminal session." -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "WARNING: PHP version may not be exactly $Version" -ForegroundColor Yellow
        Write-Host "But PATH has been updated to: $($targetPhp.Path)" -ForegroundColor Gray
    }
} else {
    Write-Host "ERROR: PHP still not found in PATH" -ForegroundColor Red
    Write-Host "Attempted to use: $($targetPhp.Path)" -ForegroundColor Gray
    exit 1
}
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. composer install --ignore-platform-reqs" -ForegroundColor White
Write-Host "2. composer require --dev brianium/paratest --ignore-platform-reqs" -ForegroundColor White
Write-Host "3. php artisan test --stop-on-failure" -ForegroundColor White
Write-Host ""
Write-Host "NOTE: This PATH change only affects this terminal session." -ForegroundColor Yellow
Write-Host "For permanent changes, use Laragon's GUI or modify system PATH." -ForegroundColor Yellow
