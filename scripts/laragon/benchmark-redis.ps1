# Benchmark Redis 7.4.1 performance
# Part of Redis 7.4.1 upgrade process for ICTServe

$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$redisBenchmark = "$redisPath\redis-benchmark.exe"
$passwordFile = "$redisPath\redis-password.txt"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis Performance Benchmark" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify redis-benchmark exists
if (-not (Test-Path $redisBenchmark)) {
    Write-Host "❌ redis-benchmark not found at: $redisBenchmark" -ForegroundColor Red
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

Write-Host "Running Redis performance benchmark..." -ForegroundColor Cyan
Write-Host "This will take a few minutes..." -ForegroundColor Yellow
Write-Host ""

# Quick benchmark (100,000 requests)
Write-Host "Quick Benchmark (100,000 requests):" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Gray
& $redisBenchmark -a $password -q -n 100000

Write-Host ""
Write-Host "========================================" -ForegroundColor Gray
Write-Host ""

# Detailed benchmark for specific operations
Write-Host "Detailed Benchmark (10,000 requests per operation):" -ForegroundColor Cyan
Write-Host ""

# SET operations
Write-Host "1. SET operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t set -n 10000 -q

# GET operations
Write-Host ""
Write-Host "2. GET operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t get -n 10000 -q

# INCR operations
Write-Host ""
Write-Host "3. INCR operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t incr -n 10000 -q

# LPUSH operations
Write-Host ""
Write-Host "4. LPUSH operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t lpush -n 10000 -q

# RPUSH operations
Write-Host ""
Write-Host "5. RPUSH operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t rpush -n 10000 -q

# LPOP operations
Write-Host ""
Write-Host "6. LPOP operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t lpop -n 10000 -q

# SADD operations
Write-Host ""
Write-Host "7. SADD operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t sadd -n 10000 -q

# HSET operations
Write-Host ""
Write-Host "8. HSET operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t hset -n 10000 -q

# ZADD operations
Write-Host ""
Write-Host "9. ZADD operations:" -ForegroundColor Cyan
& $redisBenchmark -a $password -t zadd -n 10000 -q

# Pipeline benchmark
Write-Host ""
Write-Host "10. Pipeline benchmark (16 commands):" -ForegroundColor Cyan
& $redisBenchmark -a $password -n 10000 -P 16 -q

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Benchmark completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Performance Notes:" -ForegroundColor Cyan
Write-Host "  - Higher requests/sec = better performance" -ForegroundColor White
Write-Host "  - Redis 7.4.1 should show 30-50% improvement over 5.0.14.1" -ForegroundColor White
Write-Host "  - Pipeline operations show significant performance gains" -ForegroundColor White
Write-Host ""
Write-Host "For production monitoring, consider:" -ForegroundColor Yellow
Write-Host "  - Running benchmarks during off-peak hours" -ForegroundColor White
Write-Host "  - Comparing results with baseline from Redis 5.0.14.1" -ForegroundColor White
Write-Host "  - Monitoring memory usage and latency" -ForegroundColor White
Write-Host ""
Write-Host "Next step: Run test-horizon-reverb.ps1 (optional)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
