# Verify Redis 7.4.1 dependencies
# Part of Redis 7.4.1 upgrade process for ICTServe

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis 7.4.1 Dependency Check" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

$allChecksPassed = $true

# Check 1: Visual C++ Redistributable
Write-Host "1. Checking Visual C++ Redistributable..." -ForegroundColor Cyan
$vcRedist = Get-ItemProperty "HKLM:\SOFTWARE\Microsoft\VisualStudio\14.0\VC\Runtimes\x64" -ErrorAction SilentlyContinue
if ($vcRedist) {
    Write-Host "   ✅ Visual C++ Redistributable 2015-2022 installed" -ForegroundColor Green
    Write-Host "      Version: $($vcRedist.Version)" -ForegroundColor Gray
} else {
    Write-Host "   ❌ Visual C++ Redistributable NOT found" -ForegroundColor Red
    Write-Host "      Download from: https://aka.ms/vs/17/release/vc_redist.x64.exe" -ForegroundColor Yellow
    $allChecksPassed = $false
}

# Check 2: Windows version
Write-Host ""
Write-Host "2. Checking Windows version..." -ForegroundColor Cyan
$osInfo = Get-CimInstance Win32_OperatingSystem
$osVersion = [System.Environment]::OSVersion.Version
Write-Host "   ✅ Windows $($osInfo.Caption)" -ForegroundColor Green
Write-Host "      Version: $($osVersion.Major).$($osVersion.Minor).$($osVersion.Build)" -ForegroundColor Gray

# Check 3: Disk space
Write-Host ""
Write-Host "3. Checking disk space..." -ForegroundColor Cyan
$drive = Get-PSDrive C
$freeSpaceGB = [math]::Round($drive.Free / 1GB, 2)
if ($freeSpaceGB -gt 1) {
    Write-Host "   ✅ Sufficient disk space: $freeSpaceGB GB free" -ForegroundColor Green
} else {
    Write-Host "   ⚠️  Low disk space: $freeSpaceGB GB free" -ForegroundColor Yellow
    Write-Host "      Recommended: At least 1 GB free" -ForegroundColor Gray
}

# Check 4: Port 6379 availability
Write-Host ""
Write-Host "4. Checking port 6379..." -ForegroundColor Cyan
$portInUse = Get-NetTCPConnection -LocalPort 6379 -ErrorAction SilentlyContinue
if ($portInUse) {
    Write-Host "   ⚠️  Port 6379 is currently in use" -ForegroundColor Yellow
    Write-Host "      Process: $($portInUse.OwningProcess)" -ForegroundColor Gray
    Write-Host "      You'll need to stop Redis before upgrading" -ForegroundColor Gray
} else {
    Write-Host "   ✅ Port 6379 is available" -ForegroundColor Green
}

# Check 5: Laragon installation
Write-Host ""
Write-Host "5. Checking Laragon installation..." -ForegroundColor Cyan
if (Test-Path "C:\laragon") {
    Write-Host "   ✅ Laragon found at C:\laragon" -ForegroundColor Green

    # Check current Redis version
    if (Test-Path "C:\laragon\bin\redis\redis-x64-5.0.14.1") {
        Write-Host "      Current Redis: 5.0.14.1" -ForegroundColor Gray
    }
} else {
    Write-Host "   ❌ Laragon not found at C:\laragon" -ForegroundColor Red
    $allChecksPassed = $false
}

# Check 6: Redis data directory
Write-Host ""
Write-Host "6. Checking Redis data directory..." -ForegroundColor Cyan
if (Test-Path "C:\laragon\data\redis") {
    Write-Host "   ✅ Redis data directory exists" -ForegroundColor Green

    # Check for existing data
    if (Test-Path "C:\laragon\data\redis\dump.rdb") {
        $rdbSize = (Get-Item "C:\laragon\data\redis\dump.rdb").Length
        Write-Host "      dump.rdb: $([math]::Round($rdbSize/1MB, 2)) MB" -ForegroundColor Gray
    }
} else {
    Write-Host "   ⚠️  Redis data directory not found" -ForegroundColor Yellow
    Write-Host "      Will be created during installation" -ForegroundColor Gray
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
if ($allChecksPassed) {
    Write-Host "✅ All dependency checks passed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "You're ready to proceed with Redis 7.4.1 installation." -ForegroundColor Cyan
    Write-Host "Next step: Run install-redis-7.4.1.ps1" -ForegroundColor Yellow
} else {
    Write-Host "❌ Some dependency checks failed" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please resolve the issues above before proceeding." -ForegroundColor Yellow
}
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
