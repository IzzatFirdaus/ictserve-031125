# Advanced npm permission fix for Windows
# Resolves Node.js installation and permission issues

Write-Host "=== Advanced npm Fix ===" -ForegroundColor Cyan
Write-Host ""

# Step 1: Check Node.js installation
Write-Host "Step 1: Checking Node.js installation..." -ForegroundColor Yellow
$nodeVersion = node --version 2>&1
if ($nodeVersion -match "v\d+\.\d+\.\d+") {
    Write-Host "Node.js version: $nodeVersion" -ForegroundColor Green
} else {
    Write-Host "ERROR: Node.js not properly installed" -ForegroundColor Red
    exit 1
}

# Step 2: Clear npm cache and config
Write-Host ""
Write-Host "Step 2: Clearing npm cache..." -ForegroundColor Yellow
$env:npm_config_cache = "$PSScriptRoot\..\..\..npm-cache"
$env:npm_config_prefix = "$PSScriptRoot\..\..\..npm-global"

# Create directories
$npmGlobal = Join-Path $PSScriptRoot "..\..\..npm-global"
$npmCache = Join-Path $PSScriptRoot "..\..\.npm-cache"
$npmUser = Join-Path $PSScriptRoot "..\..\..npm-user"

@($npmGlobal, $npmCache, $npmUser) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
        Write-Host "Created: $_" -ForegroundColor Green
    }
}

# Step 3: Update .npmrc with proper configuration
Write-Host ""
Write-Host "Step 3: Updating .npmrc configuration..." -ForegroundColor Yellow
$npmrcPath = Join-Path $PSScriptRoot "..\..\..npmrc"
$npmrcContent = @"
prefix=$($npmGlobal -replace '\\', '/')
cache=$($npmCache -replace '\\', '/')
userconfig=$($npmUser -replace '\\', '/')/.npmrc
"@

Set-Content -Path $npmrcPath -Value $npmrcContent -Force
Write-Host "Updated .npmrc" -ForegroundColor Green

# Step 4: Set environment variables for current session
Write-Host ""
Write-Host "Step 4: Setting environment variables..." -ForegroundColor Yellow
$env:NPM_CONFIG_PREFIX = $npmGlobal
$env:NPM_CONFIG_CACHE = $npmCache
$env:NPM_CONFIG_USERCONFIG = "$npmUser\.npmrc"
Write-Host "Environment variables set" -ForegroundColor Green

# Step 5: Test npm with environment variables
Write-Host ""
Write-Host "Step 5: Testing npm..." -ForegroundColor Yellow
$npmTest = & npm --version 2>&1
if ($LASTEXITCODE -eq 0) {
    Write-Host "npm version: $npmTest" -ForegroundColor Green
    Write-Host ""
    Write-Host "SUCCESS! npm is now working." -ForegroundColor Green
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "1. Run: npm install" -ForegroundColor White
    Write-Host "2. Run: npm run build" -ForegroundColor White
} else {
    Write-Host ""
    Write-Host "WARNING: npm still has issues." -ForegroundColor Yellow
    Write-Host "Error: $npmTest" -ForegroundColor Red
    Write-Host ""
    Write-Host "Alternative solution:" -ForegroundColor Cyan
    Write-Host "1. Use Laragon's Node.js: C:\laragon\bin\nodejs\node-v18" -ForegroundColor White
    Write-Host "2. Or reinstall Node.js from: https://nodejs.org/" -ForegroundColor White
}
