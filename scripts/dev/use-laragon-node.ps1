# Use Laragon's Node.js installation
# This script configures the project to use Laragon's Node.js v18

Write-Host "=== Configuring Laragon Node.js ===" -ForegroundColor Cyan
Write-Host ""

# Set Laragon Node.js path (use v22 for better compatibility)
$laraonNodePath = "C:\laragon\bin\nodejs\node-v22"

if (-not (Test-Path $laraonNodePath)) {
    Write-Host "ERROR: Laragon Node.js not found at: $laraonNodePath" -ForegroundColor Red
    Write-Host "Please install Node.js through Laragon or adjust the path." -ForegroundColor Yellow
    exit 1
}

# Update PATH for current session
$env:Path = "$laraonNodePath;$env:Path"

# Verify Node.js and npm
Write-Host "Checking Node.js installation..." -ForegroundColor Yellow
$nodeVersion = node --version
$npmVersion = npm --version

Write-Host "Node.js version: $nodeVersion" -ForegroundColor Green
Write-Host "npm version: $npmVersion" -ForegroundColor Green
Write-Host ""

# Configure npm directories
$projectRoot = Join-Path $PSScriptRoot "..\..\"
$npmGlobal = Join-Path $projectRoot ".npm-global"
$npmCache = Join-Path $projectRoot ".npm-cache"

@($npmGlobal, $npmCache) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
    }
}

# Update .npmrc
$npmrcPath = Join-Path $projectRoot ".npmrc"
$npmrcContent = @"
prefix=$($npmGlobal -replace '\\', '/')
cache=$($npmCache -replace '\\', '/')
"@

Set-Content -Path $npmrcPath -Value $npmrcContent -Force
Write-Host "Updated .npmrc configuration" -ForegroundColor Green
Write-Host ""

# Test npm
Write-Host "Testing npm..." -ForegroundColor Yellow
$npmList = npm list --depth=0 2>&1
if ($LASTEXITCODE -eq 0 -or $npmList -match "ictserve") {
    Write-Host "npm is working correctly!" -ForegroundColor Green
} else {
    Write-Host "npm test completed (may show warnings if packages not installed)" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "SUCCESS! Laragon Node.js is now configured." -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "1. Run: npm install" -ForegroundColor White
Write-Host "2. Run: npm run build" -ForegroundColor White
Write-Host "3. Or run: composer run dev (starts all services)" -ForegroundColor White
Write-Host ""
Write-Host "NOTE: You may need to run this script each time you open a new terminal." -ForegroundColor Yellow
Write-Host "Or add C:\laragon\bin\nodejs\node-v18 to your system PATH permanently." -ForegroundColor Yellow
