# Rollback to Redis 5.0.14.1
# Emergency rollback script for Redis upgrade

Write-Host "========================================" -ForegroundColor Red
Write-Host "  Redis Rollback to 5.0.14.1" -ForegroundColor Red
Write-Host "========================================" -ForegroundColor Red
Write-Host ""
Write-Host "⚠️  WARNING: This will rollback to Redis 5.0.14.1" -ForegroundColor Yellow
Write-Host ""

$response = Read-Host "Are you sure you want to rollback? (yes/no)"
if ($response -ne "yes") {
    Write-Host "Rollback cancelled." -ForegroundColor Cyan
    exit 0
}

Write-Host ""

# Step 1: Stop Redis 7.4.1
Write-Host "Step 1: Stopping Redis 7.4.1..." -ForegroundColor Cyan
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Stop-Process -Name "redis-server" -Force -ErrorAction SilentlyContinue
    Start-Sleep -Seconds 2
    Write-Host "  ✅ Redis 7.4.1 stopped" -ForegroundColor Green
} else {
    Write-Host "  ✅ Redis was not running" -ForegroundColor Green
}

# Step 2: Restore backup
Write-Host ""
Write-Host "Step 2: Restoring data backup..." -ForegroundColor Cyan
$backupDirs = Get-ChildItem "C:\laragon\data\redis\backup_*" -Directory -ErrorAction SilentlyContinue |
              Sort-Object Name -Descending

if ($backupDirs) {
    $latestBackup = $backupDirs[0]
    Write-Host "  Using backup: $($latestBackup.Name)" -ForegroundColor Gray

    if (Test-Path "$($latestBackup.FullName)\dump.rdb") {
        Copy-Item "$($latestBackup.FullName)\dump.rdb" -Destination "C:\laragon\data\redis\dump.rdb" -Force
        Write-Host "  ✅ dump.rdb restored" -ForegroundColor Green
    }

    if (Test-Path "$($latestBackup.FullName)\appendonly.aof") {
        Copy-Item "$($latestBackup.FullName)\appendonly.aof" -Destination "C:\laragon\data\redis\appendonly.aof" -Force
        Write-Host "  ✅ appendonly.aof restored" -ForegroundColor Green
    }
} else {
    Write-Host "  ⚠️  No backup found" -ForegroundColor Yellow
}

# Step 3: Update laragon.ini
Write-Host ""
Write-Host "Step 3: Updating Laragon configuration..." -ForegroundColor Cyan
$laragonIni = "C:\laragon\usr\laragon.ini"

if (Test-Path $laragonIni) {
    $content = Get-Content $laragonIni
    $content = $content -replace 'Version=redis-x64-.*', 'Version=redis-x64-5.0.14.1'
    $content | Set-Content $laragonIni
    Write-Host "  ✅ Configuration updated to Redis 5.0.14.1" -ForegroundColor Green
} else {
    Write-Host "  ❌ laragon.ini not found" -ForegroundColor Red
}

# Step 4: Restore .env.laragon
Write-Host ""
Write-Host "Step 4: Restoring .env.laragon..." -ForegroundColor Cyan
$envBackups = Get-ChildItem ".env.laragon.backup_*" -File -ErrorAction SilentlyContinue |
              Sort-Object Name -Descending

if ($envBackups) {
    $latestEnvBackup = $envBackups[0]
    Write-Host "  Using backup: $($latestEnvBackup.Name)" -ForegroundColor Gray
    Copy-Item $latestEnvBackup.FullName -Destination ".env.laragon" -Force
    Write-Host "  ✅ .env.laragon restored" -ForegroundColor Green
} else {
    Write-Host "  ⚠️  No .env.laragon backup found" -ForegroundColor Yellow
    Write-Host "     You may need to update REDIS_PASSWORD manually" -ForegroundColor Gray
}

# Step 5: Start Redis 5.0.14.1
Write-Host ""
Write-Host "Step 5: Starting Redis 5.0.14.1..." -ForegroundColor Cyan
$redisServer = "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-server.exe"

if (Test-Path $redisServer) {
    Start-Process -FilePath $redisServer -WindowStyle Minimized
    Start-Sleep -Seconds 3

    $redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
    if ($redisProcess) {
        Write-Host "  ✅ Redis 5.0.14.1 started (PID: $($redisProcess.Id))" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Redis failed to start" -ForegroundColor Red
    }
} else {
    Write-Host "  ❌ Redis 5.0.14.1 not found at: $redisServer" -ForegroundColor Red
}

# Step 6: Verify rollback
Write-Host ""
Write-Host "Step 6: Verifying rollback..." -ForegroundColor Cyan
$redisCli = "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-cli.exe"

if (Test-Path $redisCli) {
    try {
        $ping = & $redisCli ping 2>$null
        if ($ping -eq "PONG") {
            Write-Host "  ✅ Redis 5.0.14.1 is responding" -ForegroundColor Green
        } else {
            Write-Host "  ⚠️  Redis may not be fully initialized" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "  ⚠️  Could not verify Redis status" -ForegroundColor Yellow
    }
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Rollback completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Current Redis version: 5.0.14.1" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Test Laravel connection: php artisan tinker" -ForegroundColor White
Write-Host "     >>> Redis::ping()" -ForegroundColor White
Write-Host ""
Write-Host "  2. Verify data integrity" -ForegroundColor White
Write-Host ""
Write-Host "  3. Review rollback reason and plan next upgrade attempt" -ForegroundColor White
Write-Host ""
Write-Host "Redis 7.4.1 files are still available at:" -ForegroundColor Gray
Write-Host "  C:\laragon\bin\redis\redis-x64-7.4.1" -ForegroundColor Gray
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
