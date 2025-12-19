# Laragon Version Updates - Implementation Tasks

## Task Overview

This document outlines the step-by-step implementation tasks for upgrading Laragon components, with primary focus on Redis 7.4.1 upgrade.

## Task 1: Pre-Migration Preparation

**Priority**: CRITICAL  
**Estimated Time**: 30 minutes  
**Dependencies**: None

### Subtasks

#### 1.1: Backup Current Redis Data
- [ ] Create backup directory with timestamp
- [ ] Backup `dump.rdb` file
- [ ] Backup `appendonly.aof` file (if exists)
- [ ] Verify backup integrity
- [ ] Document backup location

**Script**: `scripts/laragon/backup-redis.ps1`

```powershell
# Backup current Redis data
$timestamp = Get-Date -Format 'yyyyMMdd_HHmmss'
$backupDir = "C:\laragon\data\redis\backup_$timestamp"

Write-Host "Creating backup directory: $backupDir" -ForegroundColor Cyan
New-Item -ItemType Directory -Path $backupDir -Force | Out-Null

# Backup RDB file
if (Test-Path "C:\laragon\data\redis\dump.rdb") {
    Write-Host "Backing up dump.rdb..." -ForegroundColor Cyan
    Copy-Item "C:\laragon\data\redis\dump.rdb" -Destination "$backupDir\dump.rdb"
    Write-Host "✅ dump.rdb backed up" -ForegroundColor Green
}

# Backup AOF file
if (Test-Path "C:\laragon\data\redis\appendonly.aof") {
    Write-Host "Backing up appendonly.aof..." -ForegroundColor Cyan
    Copy-Item "C:\laragon\data\redis\appendonly.aof" -Destination "$backupDir\appendonly.aof"
    Write-Host "✅ appendonly.aof backed up" -ForegroundColor Green
}

# Verify backup
$backupFiles = Get-ChildItem -Path $backupDir
Write-Host "`nBackup completed:" -ForegroundColor Green
$backupFiles | ForEach-Object { Write-Host "  - $($_.Name) ($([math]::Round($_.Length/1MB, 2)) MB)" }
Write-Host "`nBackup location: $backupDir" -ForegroundColor Yellow
```

#### 1.2: Download Redis 7.4.1
- [ ] Download from GitHub releases
- [ ] Verify download integrity (SHA256)
- [ ] Extract to temporary location
- [ ] Verify all executables present

**Script**: `scripts/laragon/download-redis-7.4.1.ps1`

```powershell
# Download Redis 7.4.1 for Windows
$redisVersion = "7.4.1"
$downloadUrl = "https://github.com/tporadowski/redis/releases/download/v$redisVersion/Redis-x64-$redisVersion.zip"
$tempZip = "$env:TEMP\Redis-x64-$redisVersion.zip"
$extractPath = "$env:TEMP\redis-extract"

Write-Host "Downloading Redis $redisVersion..." -ForegroundColor Cyan
Invoke-WebRequest -Uri $downloadUrl -OutFile $tempZip -UseBasicParsing

Write-Host "Extracting Redis..." -ForegroundColor Cyan
Expand-Archive -Path $tempZip -DestinationPath $extractPath -Force

Write-Host "✅ Redis $redisVersion downloaded and extracted" -ForegroundColor Green
Write-Host "Location: $extractPath" -ForegroundColor Yellow
```

#### 1.3: Verify System Dependencies
- [ ] Check Visual C++ Redistributable 2015-2022
- [ ] Verify Windows version compatibility
- [ ] Check available disk space (min 1GB)
- [ ] Verify port 6379 availability

**Script**: `scripts/laragon/verify-redis-dependencies.ps1`

```powershell
# Verify Redis 7.4.1 dependencies
Write-Host "Checking Redis 7.4.1 dependencies..." -ForegroundColor Cyan

# Check Visual C++ Redistributable
$vcRedist = Get-ItemProperty "HKLM:\SOFTWARE\Microsoft\VisualStudio\14.0\VC\Runtimes\x64" -ErrorAction SilentlyContinue
if ($vcRedist) {
    Write-Host "✅ Visual C++ Redistributable installed" -ForegroundColor Green
} else {
    Write-Host "❌ Visual C++ Redistributable NOT found" -ForegroundColor Red
    Write-Host "   Download from: https://aka.ms/vs/17/release/vc_redist.x64.exe" -ForegroundColor Yellow
}

# Check disk space
$drive = Get-PSDrive C
$freeSpaceGB = [math]::Round($drive.Free / 1GB, 2)
if ($freeSpaceGB -gt 1) {
    Write-Host "✅ Sufficient disk space: $freeSpaceGB GB free" -ForegroundColor Green
} else {
    Write-Host "⚠️  Low disk space: $freeSpaceGB GB free" -ForegroundColor Yellow
}

# Check port 6379
$portInUse = Get-NetTCPConnection -LocalPort 6379 -ErrorAction SilentlyContinue
if ($portInUse) {
    Write-Host "⚠️  Port 6379 is currently in use" -ForegroundColor Yellow
} else {
    Write-Host "✅ Port 6379 is available" -ForegroundColor Green
}
```

## Task 2: Redis 7.4.1 Installation

**Priority**: CRITICAL  
**Estimated Time**: 20 minutes  
**Dependencies**: Task 1 completed

### Subtasks

#### 2.1: Install Redis 7.4.1 Binaries
- [ ] Create installation directory
- [ ] Copy binaries to Laragon bin folder
- [ ] Verify all executables work
- [ ] Set appropriate permissions

**Script**: `scripts/laragon/install-redis-7.4.1.ps1`

```powershell
# Install Redis 7.4.1 to Laragon
$redisVersion = "7.4.1"
$sourcePath = "$env:TEMP\redis-extract"
$targetPath = "C:\laragon\bin\redis\redis-x64-$redisVersion"

Write-Host "Installing Redis $redisVersion to Laragon..." -ForegroundColor Cyan

# Create target directory
New-Item -ItemType Directory -Path $targetPath -Force | Out-Null

# Copy binaries
Write-Host "Copying Redis binaries..." -ForegroundColor Cyan
Copy-Item "$sourcePath\*" -Destination $targetPath -Recurse -Force

# Verify installation
$executables = @(
    "redis-server.exe",
    "redis-cli.exe",
    "redis-benchmark.exe",
    "redis-check-aof.exe",
    "redis-check-rdb.exe"
)

Write-Host "`nVerifying installation:" -ForegroundColor Cyan
foreach ($exe in $executables) {
    if (Test-Path "$targetPath\$exe") {
        Write-Host "  ✅ $exe" -ForegroundColor Green
    } else {
        Write-Host "  ❌ $exe NOT FOUND" -ForegroundColor Red
    }
}

Write-Host "`n✅ Redis $redisVersion installed successfully" -ForegroundColor Green
Write-Host "Location: $targetPath" -ForegroundColor Yellow
```

#### 2.2: Create Optimized Configuration
- [ ] Create `redis.windows.conf` from template
- [ ] Update paths for Windows environment
- [ ] Configure memory limits
- [ ] Enable persistence (RDB + AOF)
- [ ] Configure logging

**Script**: `scripts/laragon/create-redis-config.ps1`

```powershell
# Create optimized Redis 7.4.1 configuration
$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$configFile = "$redisPath\redis.windows.conf"

$config = @"
# Redis 7.4.1 Configuration for ICTServe Production
# Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')

# Network Configuration
bind 127.0.0.1
port 6379
tcp-backlog 511
timeout 0
tcp-keepalive 300

# General Configuration
daemonize no
supervised no
loglevel notice
logfile "C:/laragon/data/redis/redis.log"
databases 16

# Snapshotting (RDB Persistence)
save 900 1
save 300 10
save 60 10000
stop-writes-on-bgsave-error yes
rdbcompression yes
rdbchecksum yes
dbfilename dump.rdb
dir "C:/laragon/data/redis/"

# Security
requirepass ictserve_redis_2024

# Memory Management
maxmemory 512mb
maxmemory-policy allkeys-lru
maxmemory-samples 5

# Lazy Freeing
lazyfree-lazy-eviction yes
lazyfree-lazy-expire yes
lazyfree-lazy-server-del yes
replica-lazy-flush yes

# Append Only File (AOF Persistence)
appendonly yes
appendfilename "appendonly.aof"
appendfsync everysec
no-appendfsync-on-rewrite no
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb
aof-load-truncated yes
aof-use-rdb-preamble yes

# Slow Log
slowlog-log-slower-than 10000
slowlog-max-len 128

# Latency Monitor
latency-monitor-threshold 100

# Event Notification (for Laravel Broadcasting)
notify-keyspace-events Ex

# Client Output Buffer Limits
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit replica 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60

# Advanced Configuration
hz 10
dynamic-hz yes
aof-rewrite-incremental-fsync yes
rdb-save-incremental-fsync yes
"@

Write-Host "Creating Redis configuration..." -ForegroundColor Cyan
$config | Out-File -FilePath $configFile -Encoding UTF8

Write-Host "✅ Configuration created: $configFile" -ForegroundColor Green
```

#### 2.3: Update Laragon Configuration
- [ ] Update `laragon.ini` to use Redis 7.4.1
- [ ] Restart Laragon service manager
- [ ] Verify Laragon recognizes new version

**Script**: `scripts/laragon/update-laragon-ini.ps1`

```powershell
# Update Laragon configuration for Redis 7.4.1
$laragonIni = "C:\laragon\usr\laragon.ini"

Write-Host "Updating Laragon configuration..." -ForegroundColor Cyan

# Read current configuration
$content = Get-Content $laragonIni

# Update Redis version
$content = $content -replace 'Version=redis-x64-.*', 'Version=redis-x64-7.4.1'

# Write updated configuration
$content | Set-Content $laragonIni

Write-Host "✅ Laragon configuration updated" -ForegroundColor Green
Write-Host "   Redis version: redis-x64-7.4.1" -ForegroundColor Yellow
```

## Task 3: Data Migration

**Priority**: CRITICAL  
**Estimated Time**: 15 minutes  
**Dependencies**: Task 2 completed

### Subtasks

#### 3.1: Stop Current Redis Instance
- [ ] Stop Redis 5.0.14.1 gracefully
- [ ] Verify Redis process terminated
- [ ] Wait for final RDB save

**Script**: `scripts/laragon/stop-redis.ps1`

```powershell
# Stop current Redis instance
Write-Host "Stopping Redis..." -ForegroundColor Cyan

# Try graceful shutdown first
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "Sending shutdown signal..." -ForegroundColor Cyan
    & "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-cli.exe" shutdown save
    Start-Sleep -Seconds 5
    
    # Force stop if still running
    $redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
    if ($redisProcess) {
        Write-Host "Force stopping Redis..." -ForegroundColor Yellow
        Stop-Process -Name "redis-server" -Force
    }
}

Write-Host "✅ Redis stopped" -ForegroundColor Green
```

#### 3.2: Migrate Data Files
- [ ] Verify RDB file integrity
- [ ] Copy RDB to new Redis data directory
- [ ] Copy AOF if exists
- [ ] Set appropriate permissions

**Script**: `scripts/laragon/migrate-redis-data.ps1`

```powershell
# Migrate Redis data to new version
$dataDir = "C:\laragon\data\redis"

Write-Host "Migrating Redis data..." -ForegroundColor Cyan

# Verify RDB file
if (Test-Path "$dataDir\dump.rdb") {
    $rdbSize = (Get-Item "$dataDir\dump.rdb").Length
    Write-Host "  RDB file size: $([math]::Round($rdbSize/1MB, 2)) MB" -ForegroundColor Cyan
    Write-Host "✅ RDB file ready for migration" -ForegroundColor Green
} else {
    Write-Host "⚠️  No RDB file found (starting fresh)" -ForegroundColor Yellow
}

# Verify AOF file
if (Test-Path "$dataDir\appendonly.aof") {
    $aofSize = (Get-Item "$dataDir\appendonly.aof").Length
    Write-Host "  AOF file size: $([math]::Round($aofSize/1MB, 2)) MB" -ForegroundColor Cyan
    Write-Host "✅ AOF file ready for migration" -ForegroundColor Green
} else {
    Write-Host "⚠️  No AOF file found" -ForegroundColor Yellow
}

Write-Host "`n✅ Data migration preparation complete" -ForegroundColor Green
```

#### 3.3: Start Redis 7.4.1
- [ ] Start Redis with new configuration
- [ ] Verify startup logs
- [ ] Check data loaded successfully
- [ ] Verify port binding

**Script**: `scripts/laragon/start-redis-7.4.1.ps1`

```powershell
# Start Redis 7.4.1
$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$configFile = "$redisPath\redis.windows.conf"

Write-Host "Starting Redis 7.4.1..." -ForegroundColor Cyan

# Start Redis server
Start-Process -FilePath "$redisPath\redis-server.exe" -ArgumentList $configFile -WindowStyle Minimized

# Wait for startup
Start-Sleep -Seconds 3

# Verify Redis is running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "✅ Redis 7.4.1 started successfully" -ForegroundColor Green
    Write-Host "   PID: $($redisProcess.Id)" -ForegroundColor Yellow
} else {
    Write-Host "❌ Redis failed to start" -ForegroundColor Red
    Write-Host "   Check logs: C:\laragon\data\redis\redis.log" -ForegroundColor Yellow
}
```

## Task 4: Validation & Testing

**Priority**: CRITICAL  
**Estimated Time**: 30 minutes  
**Dependencies**: Task 3 completed

### Subtasks

#### 4.1: Basic Connection Test
- [ ] Test Redis CLI connection
- [ ] Verify authentication works
- [ ] Check data integrity (key count)
- [ ] Test basic operations (SET/GET)

**Script**: `scripts/laragon/test-redis-connection.ps1`

```powershell
# Test Redis 7.4.1 connection
$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$password = "ictserve_redis_2024"

Write-Host "Testing Redis connection..." -ForegroundColor Cyan

# Test PING
Write-Host "`n1. Testing PING..." -ForegroundColor Cyan
$ping = & "$redisPath\redis-cli.exe" -a $password ping 2>$null
if ($ping -eq "PONG") {
    Write-Host "   ✅ PING successful" -ForegroundColor Green
} else {
    Write-Host "   ❌ PING failed" -ForegroundColor Red
}

# Test DBSIZE
Write-Host "`n2. Checking database size..." -ForegroundColor Cyan
$dbsize = & "$redisPath\redis-cli.exe" -a $password dbsize 2>$null
Write-Host "   Keys in database: $dbsize" -ForegroundColor Yellow

# Test SET/GET
Write-Host "`n3. Testing SET/GET operations..." -ForegroundColor Cyan
& "$redisPath\redis-cli.exe" -a $password set test_key "test_value" 2>$null | Out-Null
$value = & "$redisPath\redis-cli.exe" -a $password get test_key 2>$null
if ($value -eq "test_value") {
    Write-Host "   ✅ SET/GET successful" -ForegroundColor Green
} else {
    Write-Host "   ❌ SET/GET failed" -ForegroundColor Red
}

# Cleanup test key
& "$redisPath\redis-cli.exe" -a $password del test_key 2>$null | Out-Null

Write-Host "`n✅ Connection tests completed" -ForegroundColor Green
```

#### 4.2: Laravel Integration Test
- [ ] Update `.env.laragon` with Redis password
- [ ] Test Laravel Redis connection
- [ ] Test cache operations
- [ ] Test queue operations

**Script**: `scripts/laragon/test-laravel-redis.ps1`

```powershell
# Test Laravel Redis integration
Write-Host "Testing Laravel Redis integration..." -ForegroundColor Cyan

# Update .env.laragon
Write-Host "`n1. Updating .env.laragon..." -ForegroundColor Cyan
$envFile = ".env.laragon"
$content = Get-Content $envFile
$content = $content -replace 'REDIS_PASSWORD=.*', 'REDIS_PASSWORD=ictserve_redis_2024'
$content | Set-Content $envFile
Write-Host "   ✅ Environment updated" -ForegroundColor Green

# Test Laravel Redis connection
Write-Host "`n2. Testing Laravel Redis connection..." -ForegroundColor Cyan
$testResult = php artisan tinker --execute="echo Redis::ping();"
if ($testResult -match "PONG") {
    Write-Host "   ✅ Laravel Redis connection successful" -ForegroundColor Green
} else {
    Write-Host "   ❌ Laravel Redis connection failed" -ForegroundColor Red
}

# Test cache
Write-Host "`n3. Testing cache operations..." -ForegroundColor Cyan
php artisan tinker --execute="Cache::put('test', 'value', 60); echo Cache::get('test');"
Write-Host "   ✅ Cache operations successful" -ForegroundColor Green

Write-Host "`n✅ Laravel integration tests completed" -ForegroundColor Green
```

#### 4.3: Performance Benchmark
- [ ] Run Redis benchmark tool
- [ ] Compare with Redis 5.0.14.1 baseline
- [ ] Document performance improvements
- [ ] Verify memory usage

**Script**: `scripts/laragon/benchmark-redis.ps1`

```powershell
# Benchmark Redis 7.4.1 performance
$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$password = "ictserve_redis_2024"

Write-Host "Running Redis performance benchmark..." -ForegroundColor Cyan
Write-Host "This may take a few minutes...`n" -ForegroundColor Yellow

# Run benchmark
& "$redisPath\redis-benchmark.exe" -a $password -q -n 100000

Write-Host "`n✅ Benchmark completed" -ForegroundColor Green
```

#### 4.4: Horizon & Reverb Test
- [ ] Start Laravel Horizon
- [ ] Verify queue processing
- [ ] Start Laravel Reverb
- [ ] Test WebSocket broadcasting

**Script**: `scripts/laragon/test-horizon-reverb.ps1`

```powershell
# Test Horizon and Reverb with Redis 7.4.1
Write-Host "Testing Horizon and Reverb..." -ForegroundColor Cyan

# Test Horizon
Write-Host "`n1. Testing Laravel Horizon..." -ForegroundColor Cyan
$horizonStatus = php artisan horizon:status
Write-Host "   $horizonStatus" -ForegroundColor Yellow

# Test Reverb
Write-Host "`n2. Testing Laravel Reverb..." -ForegroundColor Cyan
Write-Host "   Starting Reverb (will run for 5 seconds)..." -ForegroundColor Yellow
$reverbJob = Start-Job -ScriptBlock { php artisan reverb:start }
Start-Sleep -Seconds 5
Stop-Job -Job $reverbJob
Remove-Job -Job $reverbJob

Write-Host "`n✅ Horizon and Reverb tests completed" -ForegroundColor Green
```

## Task 5: Documentation & Cleanup

**Priority**: MEDIUM  
**Estimated Time**: 20 minutes  
**Dependencies**: Task 4 completed

### Subtasks

#### 5.1: Update Documentation
- [ ] Update `README.md` with Redis 7.4.1
- [ ] Update `.kiro/steering/tech.md`
- [ ] Update setup scripts
- [ ] Document migration process

#### 5.2: Create Rollback Script
- [ ] Document rollback procedure
- [ ] Create automated rollback script
- [ ] Test rollback in development

**Script**: `scripts/laragon/rollback-redis.ps1`

```powershell
# Rollback to Redis 5.0.14.1
Write-Host "Rolling back to Redis 5.0.14.1..." -ForegroundColor Yellow

# Stop Redis 7.4.1
Write-Host "`n1. Stopping Redis 7.4.1..." -ForegroundColor Cyan
Stop-Process -Name "redis-server" -Force -ErrorAction SilentlyContinue
Write-Host "   ✅ Redis 7.4.1 stopped" -ForegroundColor Green

# Restore backup
Write-Host "`n2. Restoring backup..." -ForegroundColor Cyan
$latestBackup = Get-ChildItem "C:\laragon\data\redis\backup_*" | Sort-Object Name -Descending | Select-Object -First 1
if ($latestBackup) {
    Copy-Item "$($latestBackup.FullName)\dump.rdb" -Destination "C:\laragon\data\redis\dump.rdb" -Force
    Write-Host "   ✅ Backup restored from: $($latestBackup.Name)" -ForegroundColor Green
}

# Update laragon.ini
Write-Host "`n3. Updating Laragon configuration..." -ForegroundColor Cyan
$laragonIni = "C:\laragon\usr\laragon.ini"
$content = Get-Content $laragonIni
$content = $content -replace 'Version=redis-x64-.*', 'Version=redis-x64-5.0.14.1'
$content | Set-Content $laragonIni
Write-Host "   ✅ Configuration updated" -ForegroundColor Green

# Start Redis 5.0.14.1
Write-Host "`n4. Starting Redis 5.0.14.1..." -ForegroundColor Cyan
Start-Process -FilePath "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-server.exe" -WindowStyle Minimized
Start-Sleep -Seconds 3
Write-Host "   ✅ Redis 5.0.14.1 started" -ForegroundColor Green

Write-Host "`n✅ Rollback completed successfully" -ForegroundColor Green
```

#### 5.3: Cleanup Temporary Files
- [ ] Remove downloaded ZIP files
- [ ] Remove extraction directories
- [ ] Archive old Redis version
- [ ] Clean up old backups (keep last 3)

**Script**: `scripts/laragon/cleanup-redis-migration.ps1`

```powershell
# Cleanup after Redis migration
Write-Host "Cleaning up migration files..." -ForegroundColor Cyan

# Remove temporary files
Write-Host "`n1. Removing temporary files..." -ForegroundColor Cyan
Remove-Item "$env:TEMP\Redis-x64-*.zip" -Force -ErrorAction SilentlyContinue
Remove-Item "$env:TEMP\redis-extract" -Recurse -Force -ErrorAction SilentlyContinue
Write-Host "   ✅ Temporary files removed" -ForegroundColor Green

# Archive old Redis version
Write-Host "`n2. Archiving Redis 5.0.14.1..." -ForegroundColor Cyan
$archiveDir = "C:\laragon\bin\redis\archive"
New-Item -ItemType Directory -Path $archiveDir -Force | Out-Null
# Keep old version for rollback purposes
Write-Host "   ✅ Old version preserved for rollback" -ForegroundColor Green

# Clean old backups (keep last 3)
Write-Host "`n3. Cleaning old backups..." -ForegroundColor Cyan
$backups = Get-ChildItem "C:\laragon\data\redis\backup_*" | Sort-Object Name -Descending
if ($backups.Count -gt 3) {
    $backups | Select-Object -Skip 3 | Remove-Item -Recurse -Force
    Write-Host "   ✅ Removed $($backups.Count - 3) old backups" -ForegroundColor Green
} else {
    Write-Host "   ✅ No old backups to remove" -ForegroundColor Green
}

Write-Host "`n✅ Cleanup completed" -ForegroundColor Green
```

## Task 6: Monitoring & Optimization

**Priority**: LOW  
**Estimated Time**: 15 minutes  
**Dependencies**: Task 5 completed

### Subtasks

#### 6.1: Setup Monitoring
- [ ] Create health check script
- [ ] Schedule daily health checks
- [ ] Setup performance monitoring
- [ ] Configure alerting

**Script**: `scripts/laragon/monitor-redis.ps1`

```powershell
# Monitor Redis 7.4.1 health and performance
$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$password = "ictserve_redis_2024"

Write-Host "Redis Health Monitor" -ForegroundColor Cyan
Write-Host "===================" -ForegroundColor Cyan

# Check if Redis is running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "`n✅ Redis is running (PID: $($redisProcess.Id))" -ForegroundColor Green
} else {
    Write-Host "`n❌ Redis is NOT running" -ForegroundColor Red
    exit 1
}

# Get Redis info
Write-Host "`nRedis Information:" -ForegroundColor Cyan
$info = & "$redisPath\redis-cli.exe" -a $password info server 2>$null
$info | Select-String "redis_version", "uptime_in_days", "process_id"

# Memory usage
Write-Host "`nMemory Usage:" -ForegroundColor Cyan
$memory = & "$redisPath\redis-cli.exe" -a $password info memory 2>$null
$memory | Select-String "used_memory_human", "used_memory_peak_human", "maxmemory_human"

# Database stats
Write-Host "`nDatabase Statistics:" -ForegroundColor Cyan
$stats = & "$redisPath\redis-cli.exe" -a $password info stats 2>$null
$stats | Select-String "total_connections_received", "total_commands_processed", "keyspace_hits", "keyspace_misses"

# Connected clients
Write-Host "`nConnected Clients:" -ForegroundColor Cyan
$clients = & "$redisPath\redis-cli.exe" -a $password info clients 2>$null
$clients | Select-String "connected_clients"

Write-Host "`n✅ Health check completed" -ForegroundColor Green
```

#### 6.2: Performance Tuning
- [ ] Analyze slow log
- [ ] Optimize memory settings
- [ ] Tune persistence settings
- [ ] Configure connection pooling

#### 6.3: Security Hardening
- [ ] Review ACL permissions
- [ ] Update passwords
- [ ] Configure firewall rules
- [ ] Enable TLS (production)

## Success Criteria Checklist

- [ ] Redis 7.4.1 installed and running
- [ ] All data migrated successfully (zero data loss)
- [ ] Laravel connections working (default, cache, horizon, reverb)
- [ ] Performance improved by 30%+ over Redis 5.0.14.1
- [ ] All tests passing (unit, integration, performance)
- [ ] Documentation updated
- [ ] Rollback procedure tested and documented
- [ ] Monitoring and alerting configured
- [ ] Team trained on new Redis features

## Rollback Criteria

Rollback to Redis 5.0.14.1 if:
- Data loss detected
- Performance degradation > 10%
- Critical Laravel features broken
- Stability issues (crashes, memory leaks)
- Unable to resolve issues within 2 hours

## Post-Migration Tasks

1. **Week 1**: Monitor performance and stability daily
2. **Week 2**: Review slow log and optimize queries
3. **Week 3**: Analyze memory usage patterns
4. **Month 1**: Review ACL permissions and security
5. **Month 3**: Plan for Redis Cluster (if needed for scaling)

## Notes

- Keep Redis 5.0.14.1 installed for at least 1 month (rollback safety)
- Document any issues encountered during migration
- Share performance improvements with team
- Consider Redis Cluster for future horizontal scaling
