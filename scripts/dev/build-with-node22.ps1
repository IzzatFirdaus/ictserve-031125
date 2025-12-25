# Build assets with Node.js v22 (required for Vite 7.2.0)
# This script temporarily disables .npmrc to work with Node v22

Write-Host "=== Building Assets with Node.js v22 ===" -ForegroundColor Cyan
Write-Host ""

$nodeV22Path = "C:\laragon\bin\nodejs\node-v22"
$projectRoot = Join-Path $PSScriptRoot "..\..\"
$npmrcPath = Join-Path $projectRoot ".npmrc"
$npmrcBackup = Join-Path $projectRoot ".npmrc.backup"

# Check Node.js v22
if (-not (Test-Path $nodeV22Path)) {
    Write-Host "ERROR: Node.js v22 not found" -ForegroundColor Red
    Write-Host "Install from: https://nodejs.org/" -ForegroundColor Yellow
    exit 1
}

# Backup .npmrc
if (Test-Path $npmrcPath) {
    Copy-Item $npmrcPath $npmrcBackup -Force
    Write-Host "Backed up .npmrc" -ForegroundColor Gray
}

try {
    # Remove .npmrc temporarily
    if (Test-Path $npmrcPath) {
        Remove-Item $npmrcPath -Force
    }

    # Set Node.js v22
    $env:Path = "$nodeV22Path;$env:Path"

    # Configure npm via environment
    $npmGlobal = Join-Path $projectRoot ".npm-global"
    $npmCache = Join-Path $projectRoot ".npm-cache"

    $env:NPM_CONFIG_PREFIX = $npmGlobal
    $env:NPM_CONFIG_CACHE = $npmCache

    Write-Host "Using Node.js:" -ForegroundColor Yellow
    node --version

    Write-Host ""
    Write-Host "Building assets..." -ForegroundColor Yellow
    npm run build

    if ($LASTEXITCODE -eq 0) {
        Write-Host ""
        Write-Host "SUCCESS! Assets built successfully" -ForegroundColor Green
    } else {
        Write-Host ""
        Write-Host "ERROR: Build failed" -ForegroundColor Red
        exit 1
    }

} finally {
    # Restore .npmrc
    if (Test-Path $npmrcBackup) {
        Move-Item $npmrcBackup $npmrcPath -Force
        Write-Host ""
        Write-Host "Restored .npmrc" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "Build complete!" -ForegroundColor Green
Write-Host "Assets are ready for production" -ForegroundColor Cyan
