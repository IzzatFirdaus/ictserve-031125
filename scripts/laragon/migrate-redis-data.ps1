# Migrate Redis data to new version
# Part of Redis 7.4.1 upgrade process for ICTServe

$dataDir = "C:\laragon\data\redis"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis Data Migration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify Redis is stopped
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "❌ Redis is still running!" -ForegroundColor Red
    Write-Host "   Please run stop-redis.ps1 first" -ForegroundColor Yellow
    exit 1
}

Write-Host "✅ Redis is stopped" -ForegroundColor Green
Write-Host ""

# Check data directory
if (-not (Test-Path $dataDir)) {
    Write-Host "⚠️  Redis data directory not found: $dataDir" -ForegroundColor Yellow
    Write-Host "   Creating directory..." -ForegroundColor Cyan
    New-Item -ItemType Directory -Path $dataDir -Force | Out-Null
    Write-Host "✅ Directory created" -ForegroundColor Green
    Write-Host ""
    Write-Host "No data to migrate (fresh installation)" -ForegroundColor Cyan
    Write-Host ""
    exit 0
}

Write-Host "Checking for existing data..." -ForegroundColor Cyan
Write-Host ""

# Check RDB file
if (Test-Path "$dataDir\dump.rdb") {
    $rdbSize = (Get-Item "$dataDir\dump.rdb").Length
    $rdbModified = (Get-Item "$dataDir\dump.rdb").LastWriteTime
    Write-Host "✅ RDB file found" -ForegroundColor Green
    Write-Host "   Size: $([math]::Round($rdbSize/1MB, 2)) MB" -ForegroundColor Gray
    Write-Host "   Last modified: $rdbModified" -ForegroundColor Gray

    # Verify RDB integrity (if redis-check-rdb is available)
    $rdbCheck = "C:\laragon\bin\redis\redis-x64-7.4.1\redis-check-rdb.exe"
    if (Test-Path $rdbCheck) {
        Write-Host "   Checking RDB integrity..." -ForegroundColor Cyan
        try {
            $checkResult = & $rdbCheck "$dataDir\dump.rdb" 2>&1
            if ($checkResult -match "OK") {
                Write-Host "   ✅ RDB file is valid" -ForegroundColor Green
            } else {
                Write-Host "   ⚠️  RDB check returned warnings" -ForegroundColor Yellow
            }
        } catch {
            Write-Host "   ⚠️  Could not verify RDB integrity" -ForegroundColor Yellow
        }
    }
} else {
    Write-Host "⚠️  No RDB file found (starting fresh)" -ForegroundColor Yellow
}

Write-Host ""

# Check AOF file
if (Test-Path "$dataDir\appendonly.aof") {
    $aofSize = (Get-Item "$dataDir\appendonly.aof").Length
    $aofModified = (Get-Item "$dataDir\appendonly.aof").LastWriteTime
    Write-Host "✅ AOF file found" -ForegroundColor Green
    Write-Host "   Size: $([math]::Round($aofSize/1MB, 2)) MB" -ForegroundColor Gray
    Write-Host "   Last modified: $aofModified" -ForegroundColor Gray

    # Verify AOF integrity (if redis-check-aof is available)
    $aofCheck = "C:\laragon\bin\redis\redis-x64-7.4.1\redis-check-aof.exe"
    if (Test-Path $aofCheck) {
        Write-Host "   Checking AOF integrity..." -ForegroundColor Cyan
        try {
            $checkResult = & $aofCheck "$dataDir\appendonly.aof" 2>&1
            if ($checkResult -match "OK") {
                Write-Host "   ✅ AOF file is valid" -ForegroundColor Green
            } else {
                Write-Host "   ⚠️  AOF check returned warnings" -ForegroundColor Yellow
            }
        } catch {
            Write-Host "   ⚠️  Could not verify AOF integrity" -ForegroundColor Yellow
        }
    }
} else {
    Write-Host "⚠️  No AOF file found" -ForegroundColor Yellow
}

Write-Host ""

# Check for old log files
if (Test-Path "$dataDir\redis.log") {
    $logSize = (Get-Item "$dataDir\redis.log").Length
    Write-Host "Old log file found ($([math]::Round($logSize/1MB, 2)) MB)" -ForegroundColor Gray

    # Archive old log
    $archiveLog = "$dataDir\redis.log.old_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
    Write-Host "Archiving old log to: $archiveLog" -ForegroundColor Cyan
    Move-Item "$dataDir\redis.log" -Destination $archiveLog -Force
    Write-Host "✅ Old log archived" -ForegroundColor Green
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Data migration preparation complete" -ForegroundColor Green
Write-Host ""
Write-Host "Data location: $dataDir" -ForegroundColor Yellow
Write-Host ""
Write-Host "Redis 7.4.1 will automatically load existing data on startup." -ForegroundColor Cyan
Write-Host ""
Write-Host "Next step: Run start-redis-7.4.1.ps1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
