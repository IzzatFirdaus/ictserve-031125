# Backup current Redis data
# Part of Redis 7.4.1 upgrade process for ICTServe

$timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$backupDir = "C:\laragon\data\redis\backup_$timestamp"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis Data Backup Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Creating backup directory: $backupDir" -ForegroundColor Cyan
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

# Backup RDB file
if (Test-Path "C:\laragon\data\redis\dump.rdb") {
    Write-Host "Backing up dump.rdb..." -ForegroundColor Cyan
    Copy-Item "C:\laragon\data\redis\dump.rdb" -Destination "$backupDir\dump.rdb"
    $rdbSize = (Get-Item "$backupDir\dump.rdb").Length
    Write-Host "✅ dump.rdb backed up ($([math]::Round($rdbSize/1MB, 2)) MB)" -ForegroundColor Green
} else {
    Write-Host "⚠️  No dump.rdb file found" -ForegroundColor Yellow
}

# Backup AOF file
if (Test-Path "C:\laragon\data\redis\appendonly.aof") {
    Write-Host "Backing up appendonly.aof..." -ForegroundColor Cyan
    Copy-Item "C:\laragon\data\redis\appendonly.aof" -Destination "$backupDir\appendonly.aof"
    $aofSize = (Get-Item "$backupDir\appendonly.aof").Length
    Write-Host "✅ appendonly.aof backed up ($([math]::Round($aofSize/1MB, 2)) MB)" -ForegroundColor Green
} else {
    Write-Host "⚠️  No appendonly.aof file found" -ForegroundColor Yellow
}

# Backup redis.conf if exists
if (Test-Path "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis.windows.conf") {
    Write-Host "Backing up redis.windows.conf..." -ForegroundColor Cyan
    Copy-Item "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis.windows.conf" -Destination "$backupDir\redis.windows.conf"
    Write-Host "✅ redis.windows.conf backed up" -ForegroundColor Green
}

# Verify backup
Write-Host ""
Write-Host "Backup completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Backup contents:" -ForegroundColor Cyan
$backupFiles = Get-ChildItem -Path $backupDir
$backupFiles | ForEach-Object {
    Write-Host "  - $($_.Name) ($([math]::Round($_.Length/1MB, 2)) MB)" -ForegroundColor White
}

Write-Host ""
Write-Host "Backup location: $backupDir" -ForegroundColor Yellow
Write-Host ""
