# Complete npm fix for ICTServe
# Handles Node.js version selection and npm configuration

Write-Host "=== ICTServe npm Configuration Fix ===" -ForegroundColor Cyan
Write-Host ""

# Function to test Node.js/npm
function Test-NodeNpm {
    param($nodePath)

    $oldPath = $env:Path
    $env:Path = "$nodePath;$env:Path"

    try {
        $nodeVer = & node --version 2>&1
        $npmVer = & npm --version 2>&1

        if ($LASTEXITCODE -eq 0) {
            $env:Path = $oldPath
            return @{
                Success = $true
                NodeVersion = $nodeVer
                NpmVersion = $npmVer
                Path = $nodePath
            }
        }
    } catch {}

    $env:Path = $oldPath
    return @{ Success = $false }
}

# Try different Node.js installations
Write-Host "Searching for working Node.js installation..." -ForegroundColor Yellow
Write-Host ""

$nodePaths = @(
    "C:\laragon\bin\nodejs\node-v18",
    "C:\laragon\bin\nodejs\node-v20",
    "C:\Program Files\nodejs"
)

$workingNode = $null
foreach ($path in $nodePaths) {
    if (Test-Path $path) {
        Write-Host "Testing: $path" -ForegroundColor Gray
        $result = Test-NodeNpm -nodePath $path

        if ($result.Success) {
            $workingNode = $result
            Write-Host "Working: Node $($result.NodeVersion), npm $($result.NpmVersion)" -ForegroundColor Green
            break
        } else {
            Write-Host "Failed" -ForegroundColor Red
        }
    }
}

if (-not $workingNode) {
    Write-Host ""
    Write-Host "ERROR: No working Node.js installation found!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Solutions:" -ForegroundColor Yellow
    Write-Host "1. Install Node.js 20 LTS from: https://nodejs.org/" -ForegroundColor White
    Write-Host "2. Or use Laragon to install Node.js" -ForegroundColor White
    exit 1
}

Write-Host ""
Write-Host "Using Node.js from: $($workingNode.Path)" -ForegroundColor Green
Write-Host ""

# Configure npm directories
$projectRoot = Join-Path $PSScriptRoot "..\..\"
$npmGlobal = Join-Path $projectRoot ".npm-global"
$npmCache = Join-Path $projectRoot ".npm-cache"

Write-Host "Configuring npm directories..." -ForegroundColor Yellow
@($npmGlobal, $npmCache) | ForEach-Object {
    if (-not (Test-Path $_)) {
        New-Item -ItemType Directory -Path $_ -Force | Out-Null
        Write-Host "Created: $_" -ForegroundColor Green
    }
}

# Update .npmrc
$npmrcPath = Join-Path $projectRoot ".npmrc"
$npmrcContent = "prefix=$($npmGlobal -replace '\\', '/')`ncache=$($npmCache -replace '\\', '/')"

Set-Content -Path $npmrcPath -Value $npmrcContent -Force
Write-Host "Updated .npmrc" -ForegroundColor Green
Write-Host ""

# Create helper script for future use
$helperContent = "# Quick npm fix - run this if npm stops working`n"
$helperContent += "`$env:Path = `"$($workingNode.Path);`$env:Path`"`n"
$helperContent += "Write-Host `"Node.js configured: $($workingNode.NodeVersion)`" -ForegroundColor Green"

$helperPath = Join-Path $projectRoot "fix-npm.ps1"
Set-Content -Path $helperPath -Value $helperContent -Force

Write-Host "SUCCESS! npm is now configured." -ForegroundColor Green
Write-Host ""
Write-Host "Configuration saved to: fix-npm.ps1" -ForegroundColor Cyan
Write-Host "Run .\fix-npm.ps1 in new terminals to configure Node.js" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "1. Run: npm install" -ForegroundColor White
Write-Host "2. Run: npm run build" -ForegroundColor White
Write-Host "3. Or run: composer run dev" -ForegroundColor White
Write-Host ""

# Test npm
Write-Host "Testing npm..." -ForegroundColor Yellow
$env:Path = "$($workingNode.Path);$env:Path"
$npmTest = & npm list --depth=0 2>&1
if ($LASTEXITCODE -eq 0 -or $npmTest -match "ictserve") {
    Write-Host "npm is working!" -ForegroundColor Green
} else {
    Write-Host "npm may need packages installed (run: npm install)" -ForegroundColor Yellow
}
