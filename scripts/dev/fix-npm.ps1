# Fix npm permission issues
# This script resolves common npm installation problems on Windows

Write-Host "Fixing npm configuration..." -ForegroundColor Yellow

# Create local npm directories
$npmGlobal = Join-Path $PSScriptRoot "..\..\..npm-global"
$npmCache = Join-Path $PSScriptRoot "..\..\.npm-cache"

if (-not (Test-Path $npmGlobal)) {
    New-Item -ItemType Directory -Path $npmGlobal -Force | Out-Null
    Write-Host "Created npm global directory: $npmGlobal" -ForegroundColor Green
}

if (-not (Test-Path $npmCache)) {
    New-Item -ItemType Directory -Path $npmCache -Force | Out-Null
    Write-Host "Created npm cache directory: $npmCache" -ForegroundColor Green
}

# Update .npmrc
$npmrcPath = Join-Path $PSScriptRoot "..\..\..npmrc"
$npmrcContent = @"
prefix=$($npmGlobal -replace '\\', '/')
cache=$($npmCache -replace '\\', '/')
"@

Set-Content -Path $npmrcPath -Value $npmrcContent -Force
Write-Host "Updated .npmrc configuration" -ForegroundColor Green

Write-Host ""
Write-Host "npm configuration fixed!" -ForegroundColor Green
Write-Host "You can now run: npm install" -ForegroundColor Cyan
