# WSL Redis Setup Helper Script
# Installs and configures Redis in WSL for ICTServe development

param(
    [switch]$Force,
    [switch]$TestOnly
)

Write-Host "ICTServe WSL Redis Setup" -ForegroundColor Cyan
Write-Host "========================" -ForegroundColor Cyan
Write-Host ""

# Check WSL availability
if (-not (Get-Command wsl.exe -ErrorAction SilentlyContinue)) {
    Write-Host "[ERROR] WSL not available. Please install WSL first." -ForegroundColor Red
    Write-Host "Install WSL: https://docs.microsoft.com/en-us/windows/wsl/install" -ForegroundColor Gray
    exit 1
}

Write-Host "[OK] WSL is available" -ForegroundColor Green

# Check if Redis is already installed
$redisCheck = & wsl.exe -e bash -c "command -v redis-server >/dev/null && echo 'installed' || echo 'missing'" 2>$null

if ($redisCheck -eq 'installed' -and -not $Force) {
    Write-Host "[OK] Redis is already installed in WSL" -ForegroundColor Green
    
    # Check if Redis is running
    $redisStatus = & wsl.exe -e bash -c "pgrep redis-server >/dev/null && echo 'running' || echo 'stopped'" 2>$null
    
    if ($redisStatus -eq 'running') {
        Write-Host "[OK] Redis is currently running" -ForegroundColor Green
    } else {
        Write-Host "[INFO] Redis is installed but not running" -ForegroundColor Yellow
        Write-Host "Start Redis with: wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
    }
    
    # Test Redis connection
    $pingResult = & wsl.exe -e bash -c "redis-cli ping 2>/dev/null || echo 'FAILED'" 2>$null
    if ($pingResult -eq 'PONG') {
        Write-Host "[OK] Redis connection test successful" -ForegroundColor Green
    } else {
        Write-Host "[WARN] Redis connection test failed" -ForegroundColor Yellow
    }
    
    if ($TestOnly) {
        exit 0
    }
    
    Write-Host ""
    Write-Host "Redis is ready! You can now run the development script." -ForegroundColor Green
    exit 0
}

if ($TestOnly) {
    Write-Host "[INFO] Redis not installed. Would install if not in test mode." -ForegroundColor Yellow
    exit 0
}

# Install Redis
Write-Host "[INFO] Installing Redis in WSL..." -ForegroundColor Yellow
Write-Host "This may take a few minutes..." -ForegroundColor Gray
Write-Host ""

# Update package list
Write-Host "Updating package list..." -ForegroundColor Yellow
$updateResult = & wsl.exe -e bash -c "sudo apt update >/dev/null 2>&1 && echo 'success' || echo 'failed'" 2>$null

if ($updateResult -ne 'success') {
    Write-Host "[ERROR] Failed to update package list" -ForegroundColor Red
    Write-Host "Try manually: wsl.exe sudo apt update" -ForegroundColor Gray
    exit 1
}

Write-Host "[OK] Package list updated" -ForegroundColor Green

# Install Redis
Write-Host "Installing Redis server..." -ForegroundColor Yellow
$installResult = & wsl.exe -e bash -c "sudo apt install -y redis-server >/dev/null 2>&1 && echo 'success' || echo 'failed'" 2>$null

if ($installResult -ne 'success') {
    Write-Host "[ERROR] Failed to install Redis" -ForegroundColor Red
    Write-Host "Try manually: wsl.exe sudo apt install -y redis-server" -ForegroundColor Gray
    exit 1
}

Write-Host "[OK] Redis installed successfully" -ForegroundColor Green

# Configure Redis
Write-Host "Configuring Redis..." -ForegroundColor Yellow
$configResult = & wsl.exe -e bash -c "sudo sed -i 's/^bind 127.0.0.1 ::1/bind 127.0.0.1/' /etc/redis/redis.conf 2>/dev/null && echo 'success' || echo 'failed'" 2>$null

if ($configResult -eq 'success') {
    Write-Host "[OK] Redis configured for local access" -ForegroundColor Green
} else {
    Write-Host "[WARN] Could not configure Redis automatically" -ForegroundColor Yellow
}

# Start Redis
Write-Host "Starting Redis server..." -ForegroundColor Yellow
$startResult = & wsl.exe -e bash -c "redis-server --daemonize yes --port 6379 --bind 127.0.0.1 2>/dev/null && echo 'success' || echo 'failed'" 2>$null

if ($startResult -eq 'success') {
    Write-Host "[OK] Redis started successfully" -ForegroundColor Green
} else {
    Write-Host "[WARN] Could not start Redis automatically" -ForegroundColor Yellow
    Write-Host "Try manually: wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
}

# Test Redis connection
Start-Sleep -Seconds 2
$pingResult = & wsl.exe -e bash -c "redis-cli ping 2>/dev/null || echo 'FAILED'" 2>$null

if ($pingResult -eq 'PONG') {
    Write-Host "[OK] Redis connection test successful" -ForegroundColor Green
} else {
    Write-Host "[WARN] Redis connection test failed" -ForegroundColor Yellow
    Write-Host "Redis may need a moment to start up" -ForegroundColor Gray
}

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "WSL Redis Setup Complete!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Redis Commands:" -ForegroundColor White
Write-Host "  Start:    wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
Write-Host "  Stop:     wsl.exe redis-cli shutdown" -ForegroundColor Gray
Write-Host "  Test:     wsl.exe redis-cli ping" -ForegroundColor Gray
Write-Host "  Monitor:  wsl.exe redis-cli monitor" -ForegroundColor Gray
Write-Host ""
Write-Host "You can now run the ICTServe development script!" -ForegroundColor Green
Write-Host "  .\scripts\dev\start-dev.ps1" -ForegroundColor Cyan