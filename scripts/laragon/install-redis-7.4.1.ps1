# Install Redis 7.4.1 to Laragon
# Part of Redis 7.4.1 upgrade process for ICTServe

$redisVersion = "7.4.1"
$sourcePath = "$env:TEMP\redis-extract"
$targetPath = "C:\laragon\bin\redis\redis-x64-$redisVersion"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis 7.4.1 Installation Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify source files exist
if (-not (Test-Path $sourcePath)) {
    Write-Host "❌ Redis 7.4.1 files not found at: $sourcePath" -ForegroundColor Red
    Write-Host "   Please run download-redis-7.4.1.ps1 first" -ForegroundColor Yellow
    exit 1
}

# Check if target already exists
if (Test-Path $targetPath) {
    Write-Host "⚠️  Redis 7.4.1 already installed at: $targetPath" -ForegroundColor Yellow
    $response = Read-Host "Reinstall? (y/n)"
    if ($response -ne 'y') {
        Write-Host "Installation cancelled." -ForegroundColor Yellow
        exit 0
    }
    Write-Host "Removing existing installation..." -ForegroundColor Cyan
    Remove-Item $targetPath -Recurse -Force
}

# Create target directory
Write-Host "Creating installation directory..." -ForegroundColor Cyan
New-Item -ItemType Directory -Path $targetPath -Force | Out-Null

# Copy binaries
Write-Host "Copying Redis binaries..." -ForegroundColor Cyan
try {
    Copy-Item "$sourcePath\*" -Destination $targetPath -Recurse -Force
    Write-Host "✅ Binaries copied successfully" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to copy binaries: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Verify installation
Write-Host ""
Write-Host "Verifying installation..." -ForegroundColor Cyan
$executables = @(
    "redis-server.exe",
    "redis-cli.exe",
    "redis-benchmark.exe",
    "redis-check-aof.exe",
    "redis-check-rdb.exe"
)

$allFilesPresent = $true
foreach ($exe in $executables) {
    if (Test-Path "$targetPath\$exe") {
        $fileSize = (Get-Item "$targetPath\$exe").Length
        Write-Host "  ✅ $exe ($([math]::Round($fileSize/1MB, 2)) MB)" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $exe NOT FOUND" -ForegroundColor Red
        $allFilesPresent = $false
    }
}

# Test Redis version
Write-Host ""
Write-Host "Testing Redis version..." -ForegroundColor Cyan
try {
    $versionOutput = & "$targetPath\redis-server.exe" --version
    Write-Host "  $versionOutput" -ForegroundColor Gray
} catch {
    Write-Host "  ⚠️  Could not verify version" -ForegroundColor Yellow
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
if ($allFilesPresent) {
    Write-Host "✅ Redis 7.4.1 installed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Installation location: $targetPath" -ForegroundColor Yellow
    Write-Host ""
    Write-Host "Next steps:" -ForegroundColor Cyan
    Write-Host "  1. Run create-redis-config.ps1 to create configuration" -ForegroundColor White
    Write-Host "  2. Run update-laragon-ini.ps1 to update Laragon" -ForegroundColor White
} else {
    Write-Host "❌ Installation incomplete - some files are missing" -ForegroundColor Red
}
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
