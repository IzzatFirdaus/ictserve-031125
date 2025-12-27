# ICTServe Redis Installation Helper Script
# Installs Redis via WSL for Laravel development

param(
    [switch]$Force,
    [switch]$StartAfterInstall
)

Write-Host "ICTServe Redis Installation Helper" -ForegroundColor Cyan
Write-Host "==================================" -ForegroundColor Cyan
Write-Host ""

# Check if WSL is available
if (-not (Get-Command wsl.exe -ErrorAction SilentlyContinue)) {
    Write-Host "[ERROR] WSL not found. Please install WSL first:" -ForegroundColor Red
    Write-Host "  1. Run: wsl --install" -ForegroundColor Gray
    Write-Host "  2. Restart your computer" -ForegroundColor Gray
    Write-Host "  3. Set up Ubuntu/Debian distribution" -ForegroundColor Gray
    exit 1
}

# Check if Redis is already installed
$redisCheck = & wsl.exe -e bash -c 'command -v redis-server >/dev/null && echo installed || echo missing' 2>$null
if ($redisCheck -eq 'installed' -and -not $Force) {
    Write-Host "[OK] Redis is already installed in WSL" -ForegroundColor Green

    # Check if running
    $redisStatus = & wsl.exe -e bash -c 'pgrep redis-server >/dev/null && echo running || echo stopped' 2>$null
    if ($redisStatus -eq 'running') {
        Write-Host "[OK] Redis is currently running" -ForegroundColor Green
    } else {
        Write-Host "[INFO] Redis is installed but not running" -ForegroundColor Yellow
        if ($StartAfterInstall) {
            Write-Host "Starting Redis..." -ForegroundColor Cyan
            $startResult = & wsl.exe -e bash -c 'redis-server --daemonize yes --port 6379 --bind 127.0.0.1 2>/dev/null && echo started || echo failed' 2>$null
            if ($startResult -eq 'started') {
                Write-Host "[OK] Redis started successfully" -ForegroundColor Green
            } else {
                Write-Host "[WARN] Could not start Redis automatically" -ForegroundColor Yellow
                Write-Host "  Try manually: wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
            }
        }
    }
    exit 0
}

Write-Host "[INFO] Installing Redis in WSL..." -ForegroundColor Cyan
Write-Host ""

# Update package list
Write-Host "Updating package list..." -ForegroundColor Yellow
$updateResult = & wsl.exe -e bash -c 'sudo apt update 2>&1'
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Failed to update package list" -ForegroundColor Red
    Write-Host $updateResult -ForegroundColor Gray
    exit 1
}
Write-Host "[OK] Package list updated" -ForegroundColor Green

# Install Redis
Write-Host "Installing Redis server..." -ForegroundColor Yellow
$installResult = & wsl.exe -e bash -c 'sudo apt install -y redis-server 2>&1'
if ($LASTEXITCODE -ne 0) {
    Write-Host "[ERROR] Failed to install Redis" -ForegroundColor Red
    Write-Host $installResult -ForegroundColor Gray
    exit 1
}
Write-Host "[OK] Redis server installed" -ForegroundColor Green

# Configure Redis for Laravel
Write-Host "Configuring Redis for Laravel..." -ForegroundColor Yellow
$configResult = & wsl.exe -e bash -c @'
sudo sed -i 's/^bind 127.0.0.1 ::1/bind 127.0.0.1/' /etc/redis/redis.conf 2>/dev/null || true
sudo sed -i 's/^# maxmemory <bytes>/maxmemory 256mb/' /etc/redis/redis.conf 2>/dev/null || true
sudo sed -i 's/^# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf 2>/dev/null || true
echo "Redis configured for Laravel"
'@

if ($LASTEXITCODE -eq 0) {
    Write-Host "[OK] Redis configured for Laravel development" -ForegroundColor Green
} else {
    Write-Host "[WARN] Redis configuration may need manual adjustment" -ForegroundColor Yellow
}

# Start Redis if requested
if ($StartAfterInstall) {
    Write-Host "Starting Redis server..." -ForegroundColor Yellow
    $startResult = & wsl.exe -e bash -c 'redis-server --daemonize yes --port 6379 --bind 127.0.0.1 2>/dev/null && echo started || echo failed' 2>$null

    if ($startResult -eq 'started') {
        Write-Host "[OK] Redis started successfully" -ForegroundColor Green

        # Test connection
        Start-Sleep -Seconds 2
        $pingResult = & wsl.exe -e bash -c 'redis-cli ping 2>/dev/null' 2>$null
        if ($pingResult -eq 'PONG') {
            Write-Host "[OK] Redis is responding to ping" -ForegroundColor Green
        } else {
            Write-Host "[WARN] Redis may not be responding properly" -ForegroundColor Yellow
        }
    } else {
        Write-Host "[WARN] Could not start Redis automatically" -ForegroundColor Yellow
        Write-Host "  Try manually: wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
    }
}

Write-Host ""
Write-Host "Redis Installation Complete!" -ForegroundColor Green
Write-Host "============================" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor White
Write-Host "  1. Run the development script: .\scripts\dev\start-dev.ps1" -ForegroundColor Gray
Write-Host "  2. Redis will be automatically detected and used" -ForegroundColor Gray
Write-Host ""
Write-Host "Manual Redis commands:" -ForegroundColor White
Write-Host "  • Start:   wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
Write-Host "  • Stop:    wsl.exe redis-cli shutdown" -ForegroundColor Gray
Write-Host "  • Status:  wsl.exe redis-cli ping" -ForegroundColor Gray
Write-Host "  • Monitor: wsl.exe redis-cli monitor" -ForegroundColor Gray
Write-Host ""
