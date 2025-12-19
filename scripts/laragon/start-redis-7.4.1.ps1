# Start Redis 7.4.1
# Part of Redis 7.4.1 upgrade process for ICTServe

$redisPath = "C:\laragon\bin\redis\redis-x64-7.4.1"
$configFile = "$redisPath\redis.windows.conf"
$redisServer = "$redisPath\redis-server.exe"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Start Redis 7.4.1 Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify Redis installation
if (-not (Test-Path $redisServer)) {
    Write-Host "❌ Redis 7.4.1 not found at: $redisServer" -ForegroundColor Red
    Write-Host "   Please run install-redis-7.4.1.ps1 first" -ForegroundColor Yellow
    exit 1
}

# Verify configuration file
if (-not (Test-Path $configFile)) {
    Write-Host "❌ Configuration file not found: $configFile" -ForegroundColor Red
    Write-Host "   Please run create-redis-config.ps1 first" -ForegroundColor Yellow
    exit 1
}

# Check if Redis is already running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "⚠️  Redis is already running (PID: $($redisProcess.Id))" -ForegroundColor Yellow
    $response = Read-Host "Restart Redis? (y/n)"
    if ($response -eq 'y') {
        Write-Host "Stopping current Redis instance..." -ForegroundColor Cyan
        Stop-Process -Name "redis-server" -Force
        Start-Sleep -Seconds 2
    } else {
        Write-Host "Keeping current Redis instance running" -ForegroundColor Cyan
        exit 0
    }
}

# Start Redis server
Write-Host "Starting Redis 7.4.1..." -ForegroundColor Cyan
Write-Host "  Server: $redisServer" -ForegroundColor Gray
Write-Host "  Config: $configFile" -ForegroundColor Gray
Write-Host ""

try {
    Start-Process -FilePath $redisServer -ArgumentList $configFile -WindowStyle Minimized
    Write-Host "✅ Redis server started" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to start Redis: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}

# Wait for startup
Write-Host ""
Write-Host "Waiting for Redis to initialize..." -ForegroundColor Cyan
Start-Sleep -Seconds 3

# Verify Redis is running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host "✅ Redis is running (PID: $($redisProcess.Id))" -ForegroundColor Green

    # Get process details
    $cpuUsage = $redisProcess.CPU
    $memoryMB = [math]::Round($redisProcess.WorkingSet64 / 1MB, 2)
    Write-Host "   CPU: $([math]::Round($cpuUsage, 2))s" -ForegroundColor Gray
    Write-Host "   Memory: $memoryMB MB" -ForegroundColor Gray
} else {
    Write-Host "❌ Redis failed to start" -ForegroundColor Red
    Write-Host ""
    Write-Host "Check the log file for errors:" -ForegroundColor Yellow
    Write-Host "  C:\laragon\data\redis\redis.log" -ForegroundColor White
    exit 1
}

# Check if port is listening
Write-Host ""
Write-Host "Checking port 6379..." -ForegroundColor Cyan
Start-Sleep -Seconds 2
$port = Get-NetTCPConnection -LocalPort 6379 -ErrorAction SilentlyContinue
if ($port) {
    Write-Host "✅ Redis is listening on port 6379" -ForegroundColor Green
} else {
    Write-Host "⚠️  Port 6379 not detected (may still be initializing)" -ForegroundColor Yellow
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Redis 7.4.1 started successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Server Information:" -ForegroundColor Cyan
Write-Host "  Version: Redis 7.4.1" -ForegroundColor White
Write-Host "  Host: 127.0.0.1" -ForegroundColor White
Write-Host "  Port: 6379" -ForegroundColor White
Write-Host "  PID: $($redisProcess.Id)" -ForegroundColor White
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Test connection: test-redis-connection.ps1" -ForegroundColor White
Write-Host "  2. Test Laravel: test-laravel-redis.ps1" -ForegroundColor White
Write-Host "  3. Benchmark: benchmark-redis.ps1" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
