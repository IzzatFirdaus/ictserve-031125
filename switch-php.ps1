# ICTServe PHP Version Switcher for Laragon
# This script helps switch PHP versions in Laragon to unlock composer packages

param(
    [string]$TargetVersion = "8.4",
    [switch]$UpdatePath
)

Write-Host "ICTServe PHP Version Switcher" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Check if running in Laragon environment
$laragonPath = "C:\laragon"
if (-not (Test-Path $laragonPath)) {
    Write-Host "ERROR: Laragon not found at $laragonPath" -ForegroundColor Red
    Write-Host "Please ensure Laragon is installed and try again." -ForegroundColor Yellow
    exit 1
}

Write-Host "Current PHP Version:" -ForegroundColor Yellow
$currentPhp = php -v 2>$null | Select-Object -First 1
if ($currentPhp) {
    Write-Host $currentPhp -ForegroundColor White
} else {
    Write-Host "PHP not found in PATH" -ForegroundColor Red
}

Write-Host ""
Write-Host "Target PHP Version: $TargetVersion" -ForegroundColor Green

# Check current PATH
Write-Host ""
Write-Host "Current PHP PATH:" -ForegroundColor Yellow
$phpInPath = (Get-Command php -ErrorAction SilentlyContinue)
if ($phpInPath) {
    Write-Host $phpInPath.Source -ForegroundColor White
} else {
    Write-Host "PHP not found in PATH" -ForegroundColor Red
}

Write-Host ""
Write-Host "Available PHP Versions in Laragon:" -ForegroundColor Yellow
$phpPath = "$laragonPath\bin\php"
$availableVersions = @()
if (Test-Path $phpPath) {
    Get-ChildItem $phpPath -Directory | ForEach-Object {
        $version = $_.Name
        $phpExe = Join-Path $_.FullName "php.exe"
        if (Test-Path $phpExe) {
            Write-Host "  PHP $version" -ForegroundColor Green
            $availableVersions += $version
        }
    }
} else {
    Write-Host "  PHP directory not found at $phpPath" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "MANUAL STEPS TO SWITCH PHP:" -ForegroundColor Cyan
Write-Host "1. Open Laragon Control Panel" -ForegroundColor White
Write-Host "2. Right-click on Laragon tray icon" -ForegroundColor White
Write-Host "3. Go to PHP > Version" -ForegroundColor White
Write-Host "4. Select PHP $TargetVersion (if available)" -ForegroundColor White
Write-Host "5. Wait for Laragon to restart services" -ForegroundColor White


Write-Host ""
Write-Host "ALTERNATIVE: Update PATH for Current Session" -ForegroundColor Cyan
Write-Host "If Laragon switch doesn't work, try this:" -ForegroundColor Yellow
Write-Host ""
foreach ($ver in $availableVersions) {
    $phpDir = "$laragonPath\bin\php\$ver"
    Write-Host "For PHP $ver run:" -ForegroundColor White
    Write-Host "  `$env:Path = `"$phpDir;`" + `$env:Path" -ForegroundColor Gray
    Write-Host "  php -v" -ForegroundColor Gray
    Write-Host ""
}

Write-Host ""
Write-Host "Packages That Will Be Unlocked with PHP 8.3+:" -ForegroundColor Cyan
Write-Host "  - brianium/paratest (Parallel testing)" -ForegroundColor White
Write-Host "  - Latest Filament security updates" -ForegroundColor White
Write-Host "  - sebastian/environment v8.0+" -ForegroundColor White
Write-Host "  - Improved test execution performance" -ForegroundColor White

Write-Host ""
Write-Host "IMPORTANT NOTES:" -ForegroundColor Yellow
Write-Host "  - Close this terminal after switching PHP" -ForegroundColor Red
Write-Host "  - Open a NEW terminal to get updated PHP path" -ForegroundColor Red
Write-Host "  - Run 'php -v' to verify the switch worked" -ForegroundColor Red
Write-Host "  - Run 'composer install --ignore-platform-reqs' after switching" -ForegroundColor Red

Write-Host ""
Write-Host "After Switching PHP, Run These Commands:" -ForegroundColor Green
Write-Host "php -v" -ForegroundColor White
Write-Host "composer install --ignore-platform-reqs" -ForegroundColor White
Write-Host "composer require --dev brianium/paratest --ignore-platform-reqs" -ForegroundColor White
Write-Host "php artisan test --stop-on-failure" -ForegroundColor White

Write-Host ""
Write-Host "To Revert Back to PHP 8.2:" -ForegroundColor Cyan
Write-Host "  - Use same Laragon menu process" -ForegroundColor White
Write-Host "  - Select PHP 8.2.x version" -ForegroundColor White
Write-Host "  - Run 'composer install --ignore-platform-reqs' again" -ForegroundColor White

Write-Host ""
Write-Host "Ready to switch! Follow the manual steps above." -ForegroundColor Green
