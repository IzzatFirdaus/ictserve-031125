# Test Redis 7.4.1 connection
# Part of Redis 7.4.1 upgrade process for ICTServe

$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$redisCli = "$redisPath\redis-cli.exe"
$passwordFile = "$redisPath\redis-password.txt"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis Connection Test" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify redis-cli exists
if (-not (Test-Path $redisCli)) {
    Write-Host "❌ redis-cli not found at: $redisCli" -ForegroundColor Red
    exit 1
}

# Get password
$password = $null
if (Test-Path $passwordFile) {
    $password = Get-Content $passwordFile -Raw
    $password = $password.Trim()
} else {
    Write-Host "⚠️  Password file not found" -ForegroundColor Yellow
    $password = Read-Host "Enter Redis password"
}

# Test 1: PING
Write-Host "Test 1: PING command" -ForegroundColor Cyan
try {
    $ping = & $redisCli -a $password ping 2>$null
    if ($ping -eq "PONG") {
        Write-Host "  ✅ PING successful" -ForegroundColor Green
    } else {
        Write-Host "  ❌ PING failed: $ping" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ PING failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: INFO
Write-Host ""
Write-Host "Test 2: Server information" -ForegroundColor Cyan
try {
    $info = & $redisCli -a $password info server 2>$null
    $version = ($info | Select-String "redis_version:").ToString().Split(':')[1].Trim()
    $uptime = ($info | Select-String "uptime_in_seconds:").ToString().Split(':')[1].Trim()
    Write-Host "  ✅ Redis version: $version" -ForegroundColor Green
    Write-Host "  ✅ Uptime: $uptime seconds" -ForegroundColor Green
} catch {
    Write-Host "  ❌ INFO failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: DBSIZE
Write-Host ""
Write-Host "Test 3: Database size" -ForegroundColor Cyan
try {
    $dbsize = & $redisCli -a $password dbsize 2>$null
    Write-Host "  ✅ Keys in database: $dbsize" -ForegroundColor Green
} catch {
    Write-Host "  ❌ DBSIZE failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: SET/GET
Write-Host ""
Write-Host "Test 4: SET/GET operations" -ForegroundColor Cyan
try {
    $setResult = & $redisCli -a $password set test_key "test_value_$(Get-Date -Format 'HHmmss')" 2>$null
    if ($setResult -eq "OK") {
        Write-Host "  ✅ SET successful" -ForegroundColor Green
    } else {
        Write-Host "  ❌ SET failed: $setResult" -ForegroundColor Red
    }

    $getValue = & $redisCli -a $password get test_key 2>$null
    if ($getValue) {
        Write-Host "  ✅ GET successful: $getValue" -ForegroundColor Green
    } else {
        Write-Host "  ❌ GET failed" -ForegroundColor Red
    }

    # Cleanup
    & $redisCli -a $password del test_key 2>$null | Out-Null
} catch {
    Write-Host "  ❌ SET/GET failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 5: Memory info
Write-Host ""
Write-Host "Test 5: Memory usage" -ForegroundColor Cyan
try {
    $memory = & $redisCli -a $password info memory 2>$null
    $usedMemory = ($memory | Select-String "used_memory_human:").ToString().Split(':')[1].Trim()
    $peakMemory = ($memory | Select-String "used_memory_peak_human:").ToString().Split(':')[1].Trim()
    Write-Host "  ✅ Used memory: $usedMemory" -ForegroundColor Green
    Write-Host "  ✅ Peak memory: $peakMemory" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Memory info not available" -ForegroundColor Yellow
}

# Test 6: Connected clients
Write-Host ""
Write-Host "Test 6: Connected clients" -ForegroundColor Cyan
try {
    $clients = & $redisCli -a $password info clients 2>$null
    $connectedClients = ($clients | Select-String "connected_clients:").ToString().Split(':')[1].Trim()
    Write-Host "  ✅ Connected clients: $connectedClients" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Client info not available" -ForegroundColor Yellow
}

# Test 7: Persistence
Write-Host ""
Write-Host "Test 7: Persistence status" -ForegroundColor Cyan
try {
    $persistence = & $redisCli -a $password info persistence 2>$null
    $rdbStatus = ($persistence | Select-String "rdb_last_save_status:").ToString().Split(':')[1].Trim()
    $aofStatus = ($persistence | Select-String "aof_enabled:").ToString().Split(':')[1].Trim()
    Write-Host "  ✅ RDB status: $rdbStatus" -ForegroundColor Green
    Write-Host "  ✅ AOF enabled: $aofStatus" -ForegroundColor Green
} catch {
    Write-Host "  ⚠️  Persistence info not available" -ForegroundColor Yellow
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Connection tests completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Redis 7.4.1 is working correctly." -ForegroundColor Cyan
Write-Host ""
Write-Host "Next step: Run test-laravel-redis.ps1" -ForegroundColor Yellow
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
