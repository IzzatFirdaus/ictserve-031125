# Monitor Redis 7.4.1 health and performance
# Continuous monitoring script for Redis

$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$redisCli = "$redisPath\redis-cli.exe"
$passwordFile = "$redisPath\redis-password.txt"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis Health Monitor" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get password
$password = $null
if (Test-Path $passwordFile) {
    $password = Get-Content $passwordFile -Raw
    $password = $password.Trim()
} else {
    Write-Host "⚠️  Password file not found" -ForegroundColor Yellow
    $password = Read-Host "Enter Redis password"
}

# Check if Redis is running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "✅ Redis is running (PID: $($redisProcess.Id))" -ForegroundColor Green
} else {
    Write-Host "❌ Redis is NOT running" -ForegroundColor Red
    Write-Host ""
    exit 1
}

Write-Host ""

# Get Redis info
Write-Host "Redis Server Information:" -ForegroundColor Cyan
Write-Host "=========================" -ForegroundColor Gray
try {
    $info = & $redisCli -a $password info server 2>$null
    $version = ($info | Select-String "redis_version:").ToString().Split(':')[1].Trim()
    $uptime = ($info | Select-String "uptime_in_days:").ToString().Split(':')[1].Trim()
    $pid = ($info | Select-String "process_id:").ToString().Split(':')[1].Trim()

    Write-Host "  Version: $version" -ForegroundColor White
    Write-Host "  Uptime: $uptime days" -ForegroundColor White
    Write-Host "  Process ID: $pid" -ForegroundColor White
} catch {
    Write-Host "  ⚠️  Could not retrieve server info" -ForegroundColor Yellow
}

# Memory usage
Write-Host ""
Write-Host "Memory Usage:" -ForegroundColor Cyan
Write-Host "=============" -ForegroundColor Gray
try {
    $memory = & $redisCli -a $password info memory 2>$null
    $usedMemory = ($memory | Select-String "used_memory_human:").ToString().Split(':')[1].Trim()
    $peakMemory = ($memory | Select-String "used_memory_peak_human:").ToString().Split(':')[1].Trim()
    $maxMemory = ($memory | Select-String "maxmemory_human:").ToString().Split(':')[1].Trim()
    $fragRatio = ($memory | Select-String "mem_fragmentation_ratio:").ToString().Split(':')[1].Trim()

    Write-Host "  Used: $usedMemory" -ForegroundColor White
    Write-Host "  Peak: $peakMemory" -ForegroundColor White
    Write-Host "  Max: $maxMemory" -ForegroundColor White
    Write-Host "  Fragmentation: $fragRatio" -ForegroundColor White
} catch {
    Write-Host "  ⚠️  Could not retrieve memory info" -ForegroundColor Yellow
}

# Database statistics
Write-Host ""
Write-Host "Database Statistics:" -ForegroundColor Cyan
Write-Host "====================" -ForegroundColor Gray
try {
    $stats = & $redisCli -a $password info stats 2>$null
    $totalConnections = ($stats | Select-String "total_connections_received:").ToString().Split(':')[1].Trim()
    $totalCommands = ($stats | Select-String "total_commands_processed:").ToString().Split(':')[1].Trim()
    $opsPerSec = ($stats | Select-String "instantaneous_ops_per_sec:").ToString().Split(':')[1].Trim()
    $keyspaceHits = ($stats | Select-String "keyspace_hits:").ToString().Split(':')[1].Trim()
    $keyspaceMisses = ($stats | Select-String "keyspace_misses:").ToString().Split(':')[1].Trim()

    Write-Host "  Total Connections: $totalConnections" -ForegroundColor White
    Write-Host "  Total Commands: $totalCommands" -ForegroundColor White
    Write-Host "  Ops/sec: $opsPerSec" -ForegroundColor White
    Write-Host "  Keyspace Hits: $keyspaceHits" -ForegroundColor White
    Write-Host "  Keyspace Misses: $keyspaceMisses" -ForegroundColor White

    # Calculate hit rate
    if ($keyspaceHits -and $keyspaceMisses) {
        $hits = [int]$keyspaceHits
        $misses = [int]$keyspaceMisses
        $total = $hits + $misses
        if ($total -gt 0) {
            $hitRate = [math]::Round(($hits / $total) * 100, 2)
            Write-Host "  Hit Rate: $hitRate%" -ForegroundColor White
        }
    }
} catch {
    Write-Host "  ⚠️  Could not retrieve statistics" -ForegroundColor Yellow
}

# Connected clients
Write-Host ""
Write-Host "Connected Clients:" -ForegroundColor Cyan
Write-Host "==================" -ForegroundColor Gray
try {
    $clients = & $redisCli -a $password info clients 2>$null
    $connectedClients = ($clients | Select-String "connected_clients:").ToString().Split(':')[1].Trim()
    $blockedClients = ($clients | Select-String "blocked_clients:").ToString().Split(':')[1].Trim()

    Write-Host "  Connected: $connectedClients" -ForegroundColor White
    Write-Host "  Blocked: $blockedClients" -ForegroundColor White
} catch {
    Write-Host "  ⚠️  Could not retrieve client info" -ForegroundColor Yellow
}

# Persistence status
Write-Host ""
Write-Host "Persistence Status:" -ForegroundColor Cyan
Write-Host "===================" -ForegroundColor Gray
try {
    $persistence = & $redisCli -a $password info persistence 2>$null
    $rdbStatus = ($persistence | Select-String "rdb_last_save_status:").ToString().Split(':')[1].Trim()
    $rdbLastSave = ($persistence | Select-String "rdb_last_save_time:").ToString().Split(':')[1].Trim()
    $aofEnabled = ($persistence | Select-String "aof_enabled:").ToString().Split(':')[1].Trim()
    $aofStatus = ($persistence | Select-String "aof_last_write_status:").ToString().Split(':')[1].Trim()

    Write-Host "  RDB Status: $rdbStatus" -ForegroundColor White
    Write-Host "  RDB Last Save: $(([DateTimeOffset]::FromUnixTimeSeconds($rdbLastSave)).LocalDateTime)" -ForegroundColor White
    Write-Host "  AOF Enabled: $aofEnabled" -ForegroundColor White
    if ($aofEnabled -eq "1") {
        Write-Host "  AOF Status: $aofStatus" -ForegroundColor White
    }
} catch {
    Write-Host "  ⚠️  Could not retrieve persistence info" -ForegroundColor Yellow
}

# Replication status
Write-Host ""
Write-Host "Replication Status:" -ForegroundColor Cyan
Write-Host "===================" -ForegroundColor Gray
try {
    $replication = & $redisCli -a $password info replication 2>$null
    $role = ($replication | Select-String "role:").ToString().Split(':')[1].Trim()
    $connectedSlaves = ($replication | Select-String "connected_slaves:").ToString().Split(':')[1].Trim()

    Write-Host "  Role: $role" -ForegroundColor White
    Write-Host "  Connected Slaves: $connectedSlaves" -ForegroundColor White
} catch {
    Write-Host "  ⚠️  Could not retrieve replication info" -ForegroundColor Yellow
}

# Database keys
Write-Host ""
Write-Host "Database Keys:" -ForegroundColor Cyan
Write-Host "==============" -ForegroundColor Gray
try {
    $keyspace = & $redisCli -a $password info keyspace 2>$null
    $databases = $keyspace | Select-String "^db\d+:"

    if ($databases) {
        foreach ($db in $databases) {
            Write-Host "  $db" -ForegroundColor White
        }
    } else {
        Write-Host "  No keys in any database" -ForegroundColor Gray
    }
} catch {
    Write-Host "  ⚠️  Could not retrieve keyspace info" -ForegroundColor Yellow
}

# Slow log
Write-Host ""
Write-Host "Recent Slow Queries:" -ForegroundColor Cyan
Write-Host "====================" -ForegroundColor Gray
try {
    $slowLog = & $redisCli -a $password slowlog get 5 2>$null
    if ($slowLog) {
        Write-Host "  $slowLog" -ForegroundColor White
    } else {
        Write-Host "  No slow queries" -ForegroundColor Green
    }
} catch {
    Write-Host "  ⚠️  Could not retrieve slow log" -ForegroundColor Yellow
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Health check completed" -ForegroundColor Green
Write-Host ""
Write-Host "For continuous monitoring, run:" -ForegroundColor Cyan
Write-Host "  redis-cli -a [password] --stat" -ForegroundColor White
Write-Host ""
Write-Host "For latency monitoring, run:" -ForegroundColor Cyan
Write-Host "  redis-cli -a [password] --latency" -ForegroundColor White
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
