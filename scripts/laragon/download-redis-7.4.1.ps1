# Download Redis 7.4.1 for Windows
# Part of Redis 7.4.1 upgrade process for ICTServe

$redisVersion = "7.4.1"
$downloadUrl = "https://github.com/tporadowski/redis/releases/download/v$redisVersion/Redis-x64-$redisVersion.zip"
$tempZip = "$env:TEMP\Redis-x64-$redisVersion.zip"
$extractPath = "$env:TEMP\redis-extract"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis 7.4.1 Download Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if already downloaded
if (Test-Path $tempZip) {
    Write-Host "⚠️  Redis 7.4.1 already downloaded" -ForegroundColor Yellow
    $response = Read-Host "Re-download? (y/n)"
    if ($response -ne 'y') {
        Write-Host "Using existing download..." -ForegroundColor Cyan
    } else {
        Remove-Item $tempZip -Force
    }
}

# Download Redis
if (-not (Test-Path $tempZip)) {
    Write-Host "Downloading Redis $redisVersion from GitHub..." -ForegroundColor Cyan
    Write-Host "URL: $downloadUrl" -ForegroundColor Gray
    Write-Host ""

    try {
        Invoke-WebRequest -Uri $downloadUrl -OutFile $tempZip -UseBasicParsing
        $fileSize = (Get-Item $tempZip).Length
        Write-Host "✅ Download completed ($([math]::Round($fileSize/1MB, 2)) MB)" -ForegroundColor Green
    } catch {
        Write-Host "❌ Download failed: $($_.Exception.Message)" -ForegroundColor Red
        exit 1
    }
}

# Extract Redis
Write-Host ""
Write-Host "Extracting Redis..." -ForegroundColor Cyan

if (Test-Path $extractPath) {
    Remove-Item $extractPath -Recurse -Force
}

try {
    Expand-Archive -Path $tempZip -DestinationPath $extractPath -Force
    Write-Host "✅ Extraction completed" -ForegroundColor Green
} catch {
    Write-Host "❌ Extraction failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Verify extracted files
Write-Host ""
Write-Host "Verifying extracted files..." -ForegroundColor Cyan
$requiredFiles = @(
    "redis-server.exe",
    "redis-cli.exe",
    "redis-benchmark.exe",
    "redis-check-aof.exe",
    "redis-check-rdb.exe"
)

$allFilesPresent = $true
foreach ($file in $requiredFiles) {
    if (Test-Path "$extractPath\$file") {
        Write-Host "  ✅ $file" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $file NOT FOUND" -ForegroundColor Red
        $allFilesPresent = $false
    }
}

Write-Host ""
if ($allFilesPresent) {
    Write-Host "✅ Redis 7.4.1 downloaded and extracted successfully" -ForegroundColor Green
    Write-Host ""
    Write-Host "Location: $extractPath" -ForegroundColor Yellow
    Write-Host "Next step: Run verify-redis-dependencies.ps1" -ForegroundColor Cyan
} else {
    Write-Host "❌ Some files are missing. Please re-download." -ForegroundColor Red
    exit 1
}

Write-Host ""
