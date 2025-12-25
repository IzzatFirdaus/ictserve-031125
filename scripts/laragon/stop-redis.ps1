# Stop current Redis instance
# Part of Redis 7.4.1 upgrade process for ICTServe

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Stop Redis Server" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Check if Redis is running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue

if (-not $redisProcess) {
    Write-Host "✅ Redis is not running" -ForegroundColor Green
    Write-Host ""
    exit 0
}

Write-Host "Redis is currently running (PID: $($redisProcess.Id))" -ForegroundColor Cyan
Write-Host ""

# Try graceful shutdown first
Write-Host "Attempting graceful shutdown..." -ForegroundColor Cyan

# Try to find redis-cli
$redisCli = $null
$possiblePaths = @(
    "C:\laragon\bin\redis\redis-x64-5.0.14.1\redis-cli.exe",
    "C:\laragon\bin\redis\redis-x64-7.4.1\redis-cli.exe"
)

foreach ($path in $possiblePaths) {
    if (Test-Path $path) {
        $redisCli = $path
        break
    }
}

if ($redisCli) {
    Write-Host "Using redis-cli: $redisCli" -ForegroundColor Gray
    try {
        & $redisCli shutdown save 2>$null
        Write-Host "✅ Shutdown command sent" -ForegroundColor Green
        Start-Sleep -Seconds 5
    } catch {
        Write-Host "⚠️  Graceful shutdown failed" -ForegroundColor Yellow
    }
} else {
    Write-Host "⚠️  redis-cli not found, will force stop" -ForegroundColor Yellow
}

# Check if still running
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue

if ($redisProcess) {
    Write-Host ""
    Write-Host "Redis still running, forcing shutdown..." -ForegroundColor Yellow
    try {
        Stop-Process -Name "redis-server" -Force
        Write-Host "✅ Redis force stopped" -ForegroundColor Green
    } catch {
        Write-Host "❌ Failed to stop Redis: $($_.Exception.Message)" -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host "✅ Redis stopped gracefully" -ForegroundColor Green
}

# Wait a moment for cleanup
Start-Sleep -Seconds 2

# Verify Redis is stopped
$redisProcess = Get-Process -Name "redis-server" -ErrorAction SilentlyContinue
if ($redisProcess) {
    Write-Host ""
    Write-Host "❌ Redis is still running!" -ForegroundColor Red
    Write-Host "   Please stop Redis manually before proceeding" -ForegroundColor Yellow
    exit 1
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Redis stopped successfully" -ForegroundColor Green
Write-Host ""
Write-Host "Next step: Run migrate-redis-data.ps1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
