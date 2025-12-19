# WSL Redis Installation Script for ICTServe
# This script installs and configures Redis 7.0+ in WSL for Windows host connectivity

param(
    [switch]$Force,
    [string]$RedisVersion = "7.0"
)

Write-Host "=== ICTServe WSL Redis Installation ===" -ForegroundColor Green
Write-Host "Installing Redis $RedisVersion in WSL environment..." -ForegroundColor Yellow

# Check if WSL is available
try {
    $wslVersion = wsl --version 2>$null
    if ($LASTEXITCODE -ne 0) {
        throw "WSL not found"
    }
    Write-Host "✓ WSL is available" -ForegroundColor Green
} catch {
    Write-Error "WSL is not installed or not available. Please install WSL first."
    Write-Host "Install WSL: wsl --install" -ForegroundColor Yellow
    exit 1
}

# Check WSL distribution
Write-Host "Checking WSL distributions..." -ForegroundColor Yellow
$wslDistros = wsl -l -v
Write-Host $wslDistros

# Verify Ubuntu distribution exists
$hasUbuntu = $wslDistros | Select-String -Pattern "Ubuntu"
if (-not $hasUbuntu) {
    Write-Warning "Ubuntu distribution not found. Installing Ubuntu..."
    wsl --install -d Ubuntu
    Write-Host "Please restart your computer and run this script again after Ubuntu installation completes." -ForegroundColor Yellow
    exit 0
}

Write-Host "✓ Ubuntu distribution found" -ForegroundColor Green

# Update package lists
Write-Host "Updating package lists..." -ForegroundColor Yellow
wsl sudo apt update

# Install Redis
Write-Host "Installing Redis server..." -ForegroundColor Yellow
wsl sudo apt install -y redis-server

# Verify Redis installation
Write-Host "Verifying Redis installation..." -ForegroundColor Yellow
$redisVersion = wsl redis-server --version
Write-Host "Installed: $redisVersion" -ForegroundColor Green

# Configure Redis for Windows host connectivity
Write-Host "Configuring Redis for Windows host connectivity..." -ForegroundColor Yellow

# Create Redis configuration backup
wsl sudo cp /etc/redis/redis.conf /etc/redis/redis.conf.backup

# Configure Redis settings for ICTServe development
$redisConfig = @"
# ICTServe Redis Configuration for WSL
# Bind to all interfaces for Windows host connectivity
bind 0.0.0.0

# Port configuration
port 6379

# Disable protected mode for development
protected-mode no

# TCP settings
tcp-backlog 511
timeout 0
tcp-keepalive 300

# Memory management for development
maxmemory 256mb
maxmemory-policy allkeys-lru

# Persistence settings for development
save 900 1
save 300 10
save 60 10000

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log

# Database settings
databases 16

# Security settings for development
# requirepass ictserve-redis-dev

# Performance settings
# Disable some checks for development performance
stop-writes-on-bgsave-error no
rdbcompression yes
rdbchecksum yes
"@

# Write configuration to temporary file and move to Redis config
$tempConfig = "/tmp/redis-ictserve.conf"
$redisConfig | wsl tee $tempConfig > $null
wsl sudo cp $tempConfig /etc/redis/redis.conf
wsl rm $tempConfig

Write-Host "✓ Redis configuration updated for ICTServe" -ForegroundColor Green

# Create Redis log directory
wsl sudo mkdir -p /var/log/redis
wsl sudo chown redis:redis /var/log/redis

# Enable and start Redis service
Write-Host "Enabling and starting Redis service..." -ForegroundColor Yellow
wsl sudo systemctl enable redis-server
wsl sudo systemctl start redis-server

# Wait for Redis to start
Start-Sleep -Seconds 3

# Test Redis service status
$redisStatus = wsl sudo systemctl is-active redis-server
if ($redisStatus -eq "active") {
    Write-Host "✓ Redis service is running" -ForegroundColor Green
} else {
    Write-Warning "Redis service status: $redisStatus"
    Write-Host "Attempting to start Redis manually..." -ForegroundColor Yellow
    wsl sudo service redis-server start
    Start-Sleep -Seconds 2
}

# Test Redis connectivity from WSL
Write-Host "Testing Redis connectivity from WSL..." -ForegroundColor Yellow
$wslTest = wsl redis-cli ping
if ($wslTest -eq "PONG") {
    Write-Host "✓ Redis responds to ping from WSL" -ForegroundColor Green
} else {
    Write-Error "Redis is not responding from WSL"
    exit 1
}

# Test Redis connectivity from Windows host
Write-Host "Testing Redis connectivity from Windows host..." -ForegroundColor Yellow
try {
    $tcpClient = New-Object System.Net.Sockets.TcpClient
    $tcpClient.Connect("127.0.0.1", 6379)
    $tcpClient.Close()
    Write-Host "✓ Redis is accessible from Windows host (127.0.0.1:6379)" -ForegroundColor Green
} catch {
    Write-Error "Cannot connect to Redis from Windows host: $($_.Exception.Message)"
    Write-Host "This might be due to Windows Firewall or WSL networking issues." -ForegroundColor Yellow
    exit 1
}

# Display Redis information
Write-Host "`n=== Redis Installation Summary ===" -ForegroundColor Green
Write-Host "Redis Version: $(wsl redis-server --version)" -ForegroundColor White
Write-Host "Configuration: /etc/redis/redis.conf" -ForegroundColor White
Write-Host "Log File: /var/log/redis/redis-server.log" -ForegroundColor White
Write-Host "Connection: 127.0.0.1:6379" -ForegroundColor White
Write-Host "Status: $(wsl sudo systemctl is-active redis-server)" -ForegroundColor White

Write-Host "`n=== Next Steps ===" -ForegroundColor Yellow
Write-Host "1. Update your Laravel .env file:" -ForegroundColor White
Write-Host "   REDIS_HOST=127.0.0.1" -ForegroundColor Gray
Write-Host "   REDIS_PORT=6379" -ForegroundColor Gray
Write-Host "   REDIS_PASSWORD=null" -ForegroundColor Gray
Write-Host "2. Test Laravel Redis connection" -ForegroundColor White
Write-Host "3. Use manage-redis.ps1 to control Redis service" -ForegroundColor White

Write-Host "`n✓ WSL Redis installation completed successfully!" -ForegroundColor Green