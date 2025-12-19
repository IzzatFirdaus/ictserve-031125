# Create optimized Redis 7.4.1 configuration
# Part of Redis 7.4.1 upgrade process for ICTServe

$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$configFile = "$redisPath\redis.windows.conf"
$dataDir = "C:\laragon\data\redis"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis 7.4.1 Configuration Script" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify Redis installation
if (-not (Test-Path $redisPath)) {
    Write-Host "❌ Redis 7.4.1 not found at: $redisPath" -ForegroundColor Red
    Write-Host "   Please run install-redis-7.4.1.ps1 first" -ForegroundColor Yellow
    exit 1
}

# Ensure data directory exists
if (-not (Test-Path $dataDir)) {
    Write-Host "Creating Redis data directory..." -ForegroundColor Cyan
    New-Item -ItemType Directory -Path $dataDir -Force | Out-Null
    Write-Host "✅ Data directory created" -ForegroundColor Green
}

# Generate secure password
$password = "ictserve_redis_$(Get-Random -Minimum 1000 -Maximum 9999)"

Write-Host "Creating optimized Redis configuration..." -ForegroundColor Cyan

$config = @"
# Redis 7.4.1 Configuration for ICTServe Production
# Generated: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')
# Optimized for Laravel Reverb + Horizon + Cache

################################## NETWORK #####################################

# Accept connections on localhost only (security)
bind 127.0.0.1

# Accept connections on the specified port
port 6379

# TCP listen() backlog
tcp-backlog 511

# Close connection after client idle for N seconds (0 to disable)
timeout 0

# TCP keepalive
tcp-keepalive 300

################################# GENERAL #####################################

# Run as a foreground process (Laragon manages it)
daemonize no

# Supervised by Laragon
supervised no

# Log level (debug, verbose, notice, warning)
loglevel notice

# Log file location
logfile "$dataDir/redis.log"

# Number of databases
databases 16

################################ SNAPSHOTTING  ################################

# Save the DB to disk (RDB persistence)
save 900 1
save 300 10
save 60 10000

# Stop accepting writes if RDB snapshots fail
stop-writes-on-bgsave-error yes

# Compress RDB files
rdbcompression yes

# Checksum RDB files
rdbchecksum yes

# RDB filename
dbfilename dump.rdb

# Working directory for RDB and AOF files
dir "$dataDir/"

################################# REPLICATION #################################

# Replication options (for future scaling)
replica-serve-stale-data yes
replica-read-only yes
repl-diskless-sync no
repl-diskless-sync-delay 5

################################## SECURITY ###################################

# Require password for all connections
requirepass $password

################################### CLIENTS ####################################

# Max number of connected clients
# maxclients 10000

############################## MEMORY MANAGEMENT ##############################

# Maximum memory limit (512MB for ICTServe)
maxmemory 512mb

# Eviction policy when maxmemory is reached
maxmemory-policy allkeys-lru

# LRU and minimal TTL algorithms sample size
maxmemory-samples 5

############################# LAZY FREEING ####################################

# Lazy freeing for better performance
lazyfree-lazy-eviction yes
lazyfree-lazy-expire yes
lazyfree-lazy-server-del yes
replica-lazy-flush yes

############################## APPEND ONLY MODE ###############################

# Enable AOF persistence
appendonly yes

# AOF filename
appendfilename "appendonly.aof"

# fsync policy (everysec is good balance)
appendfsync everysec

# Don't fsync during rewrite
no-appendfsync-on-rewrite no

# Automatic AOF rewrite
auto-aof-rewrite-percentage 100
auto-aof-rewrite-min-size 64mb

# Load truncated AOF files
aof-load-truncated yes

# Use RDB preamble in AOF for faster restarts
aof-use-rdb-preamble yes

################################ LUA SCRIPTING  ###############################

# Max execution time for Lua scripts (milliseconds)
lua-time-limit 5000

################################ REDIS CLUSTER  ###############################

# Cluster mode disabled (single instance for now)
# cluster-enabled no

################################## SLOW LOG ###################################

# Log queries slower than this (microseconds)
slowlog-log-slower-than 10000

# Max slow log entries
slowlog-max-len 128

################################ LATENCY MONITOR ##############################

# Latency monitoring threshold (milliseconds)
latency-monitor-threshold 100

############################# EVENT NOTIFICATION ##############################

# Enable keyspace notifications for Laravel Broadcasting
notify-keyspace-events Ex

############################### ADVANCED CONFIG ###############################

# Hashes
hash-max-ziplist-entries 512
hash-max-ziplist-value 64

# Lists
list-max-ziplist-size -2
list-compress-depth 0

# Sets
set-max-intset-entries 512

# Sorted Sets
zset-max-ziplist-entries 128
zset-max-ziplist-value 64

# HyperLogLog
hll-sparse-max-bytes 3000

# Streams
stream-node-max-bytes 4096
stream-node-max-entries 100

# Active rehashing
activerehashing yes

# Client output buffer limits
client-output-buffer-limit normal 0 0 0
client-output-buffer-limit replica 256mb 64mb 60
client-output-buffer-limit pubsub 32mb 8mb 60

# Frequency of rehashing
hz 10

# Dynamic hz adjustment
dynamic-hz yes

# Incremental fsync for AOF rewrite
aof-rewrite-incremental-fsync yes

# Incremental fsync for RDB save
rdb-save-incremental-fsync yes

################################ WINDOWS SPECIFIC #############################

# Windows heap directory (optional)
# heapdir "$dataDir/"
"@

# Write configuration file
try {
    $config | Out-File -FilePath $configFile -Encoding UTF8
    Write-Host "✅ Configuration file created" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to create configuration: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Save password to secure file
$passwordFile = "$redisPath\redis-password.txt"
$password | Out-File -FilePath $passwordFile -Encoding UTF8
Write-Host "✅ Password saved to: $passwordFile" -ForegroundColor Green

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Redis configuration created successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration file: $configFile" -ForegroundColor Yellow
Write-Host "Redis password: $password" -ForegroundColor Yellow
Write-Host ""
Write-Host "⚠️  IMPORTANT: Save this password!" -ForegroundColor Red
Write-Host "   You'll need it for Laravel .env configuration" -ForegroundColor White
Write-Host ""
Write-Host "Next step: Run update-laragon-ini.ps1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
