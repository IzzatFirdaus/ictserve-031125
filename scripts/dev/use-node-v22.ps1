# Use Node.js v22 for Vite 7.2.0 compatibility
# Vite requires Node.js 20.19+ or 22.12+

Write-Host "=== Configuring Node.js v22 for Vite 7.2.0 ===" -ForegroundColor Cyan
Write-Host ""

$nodeV22Path = "C:\laragon\bin\nodejs\node-v22"

if (-not (Test-Path $nodeV22Path)) {
    Write-Host "ERROR: Node.js v22 not found at: $nodeV22Path" -ForegroundColor Red
    Write-Host ""
    Write-Host "Solutions:" -ForegroundColor Yellow
    Write-Host "1. Install Node.js v20 LTS from: https://nodejs.org/" -ForegroundColor White
    Write-Host "2. Or install Node.js v22 through Laragon" -ForegroundColor White
    exit 1
}

# Set Node.js v22 in PATH
$env:Path = "$nodeV22Path;$env:Path"

# Configure npm using environment variables (bypass .npmrc)
$projectRoot = Join-Path $PSScriptRoot "..\..\"
$npmGlobal = Join-Path $projectRoot ".npm-global"
$npmCache = Join-Path $projectRoot ".npm-cache"

# Create directories
@($npmGlobal, $npmCache) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
    }
}

# Set npm environment variables
$env:NPM_CONFIG_PREFIX = $npmGlobal
$env:NPM_CONFIG_CACHE = $npmCache
$env:NPM_CONFIG_USERCONFIG = "nul"  # Disable .npmrc

# Verify versions
Write-Host "Node.js version:" -ForegroundColor Yellow
node --version

Write-Host ""
Write-Host "npm version:" -ForegroundColor Yellow
npm --version

Write-Host ""
Write-Host "SUCCESS! Node.js v22 configured for Vite 7.2.0" -ForegroundColor Green
Write-Host ""
Write-Host "Environment variables set:" -ForegroundColor Cyan
Write-Host "  NPM_CONFIG_PREFIX: $npmGlobal" -ForegroundColor Gray
Write-Host "  NPM_CONFIG_CACHE: $npmCache" -ForegroundColor Gray
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Run: npm install" -ForegroundColor White
Write-Host "2. Run: npm run build" -ForegroundColor White
Write-Host ""
Write-Host "NOTE: Run this script in each new terminal session." -ForegroundColor Yellow
