# WSL Redis Management Script for ICTServe v3.6.1
# Purpose: Manage Redis 7.0+ in Windows Subsystem for Linux
# Requirements: WSL with Ubuntu/Debian distribution

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("start", "stop", "restart", "status", "install", "configure", "test")]
    [string]$Action,
    
    [Parameter(Mandatory=$false)]
    [string]$WSLDistribution = "Ubuntu"
)

# Color output functions
function Write-Success { param($Message) Write-Host "✅ $Message" -ForegroundColor Green }
function Write-Error { param($Message) Write-Host "❌ $Message" -ForegroundColor Red }
function Write-Info { param($Message) Write-Host "ℹ️  $Message" -ForegroundColor Cyan }
function Write-Warning { param($Message) Write-Host "⚠️  $Message" -ForegroundColor Yellow }

# Check WSL availability
function Test-WSLAvailability {
    try {
        $wslVersion = wsl --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Success "WSL is available"
            return $true
        }
    }
    catch {
        Write-Error "WSL is not installed or not available"
        Write-Info "Install WSL: https://docs.microsoft.com/en-us/windows/wsl/install"
        return $false
    }
    return $false
}

# Check if WSL distribution exists
function Test-WSLDistribution {
    try {
        $distributions = wsl --list --quiet
        if ($distributions -contains $WSLDistribution) {
            Write-Success "WSL distribution '$WSLDistribution' is available"
            return $true
        } else {
            Write-Error "WSL distribution '$WSLDistribution' not found"
            Write-Info "Available distributions: $($distributions -join ', ')"
            return $false
        }
    }
    catch {
        Write-Error "Failed to check WSL distributions"
        return $false
    }
}

# Test Redis connectivity from Windows
function Test-RedisConnectivity {
    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect("127.0.0.1", 6379)
        $tcpClient.Close()
        Write-Success "Redis connection successful (127.0.0.1:6379)"
        return $true
    }
    catch {
        Write-Error "Redis connection failed: $($_.Exception.Message)"
        Write-Info "Ensure Redis is running and configured to accept connections from Windows host"
        return $false
    }
}

# Test Redis functionality
function Test-RedisFunctionality {
    Write-Info "Testing Redis functionality..."
    
    try {
        # Test basic Redis operations using redis-cli in WSL
        $testKey = "ictserve_test_$(Get-Date -Format 'yyyyMMddHHmmss')"
        $testValue = "test_value_$(Get-Random)"
        
        # Set a test key
        $setResult = wsl -d $WSLDistribution redis-cli set $testKey $testValue
        if ($setResult -eq "OK") {
            Write-Success "Redis SET operation successful"
        } else {
            Write-Error "Redis SET operation failed"
            return $false
        }
        
        # Get the test key
        $getValue = wsl -d $WSLDistribution redis-cli get $testKey
        if ($getValue -eq $testValue) {
            Write-Success "Redis GET operation successful"
        } else {
            Write-Error "Redis GET operation failed"
            return $false
        }
        
        # Delete the test key
        $delResult = wsl -d $WSLDistribution redis-cli del $testKey
        if ($delResult -eq "1") {
            Write-Success "Redis DEL operation successful"
        } else {
            Write-Error "Redis DEL operation failed"
            return $false
        }
        
        Write-Success "Redis functionality test completed successfully"
        return $true
    }
    catch {
        Write-Error "Redis functionality test failed: $($_.Exception.Message)"
        return $false
    }
}

# Install Redis in WSL
function Install-WSLRedis {
    Write-Info "Installing Redis in WSL distribution: $WSLDistribution"
    
    if (-not (Test-WSLDistribution)) {
        return $false
    }
    
    try {
        Write-Info "Updating package lists..."
        wsl -d $WSLDistribution sudo apt update
        
        Write-Info "Installing Redis server..."
        wsl -d $WSLDistribution sudo apt install -y redis-server
        
        Write-Info "Verifying Redis installation..."
        $redisVersion = wsl -d $WSLDistribution redis-server --version
        Write-Success "Redis installed: $redisVersion"
        
        # Configure Redis for Windows host access
        Configure-WSLRedis
        
        return $true
    }
    catch {
        Write-Error "Failed to install Redis: $($_.Exception.Message)"
        return $false
    }
}

# Configure Redis for ICTServe and Windows host access
function Configure-WSLRedis {
    Write-Info "Configuring Redis for ICTServe and Windows host access..."
    
    try {
        # Backup original configuration
        wsl -d $WSLDistribution sudo cp /etc/redis/redis.conf /etc/redis/redis.conf.backup.$(date +%Y%m%d_%H%M%S)
        
        # Configure Redis for Windows host connectivity
        Write-Info "Configuring Redis to accept connections from Windows host..."
        
        # Allow connections from any IP (for Windows host)
        wsl -d $WSLDistribution sudo sed -i 's/bind 127.0.0.1/bind 0.0.0.0/' /etc/redis/redis.conf
        
        # Disable protected mode for development
        wsl -d $WSLDistribution sudo sed -i 's/protected-mode yes/protected-mode no/' /etc/redis/redis.conf
        
        # Set appropriate memory policy
        wsl -d $WSLDistribution sudo sed -i 's/# maxmemory-policy noeviction/maxmemory-policy allkeys-lru/' /etc/redis/redis.conf
        
        # Add ICTServe-specific configuration
        $redisConfig = @"

# ICTServe v3.6.1 Configuration
maxmemory 256mb
maxmemory-policy allkeys-lru
tcp-keepalive 300
timeout 0

# Logging
loglevel notice
logfile /var/log/redis/redis-server.log

# Persistence (development settings)
save 900 1
save 300 10
save 60 10000

# Database settings
databases 16
"@
        
        # Add configuration to Redis config file
        Write-Info "Adding ICTServe-specific Redis configuration..."
        $configContent = $redisConfig | wsl -d $WSLDistribution sudo tee -a /etc/redis/redis.conf
        
        Write-Success "Redis configuration completed"
        
        # Create log directory if it doesn't exist
        wsl -d $WSLDistribution sudo mkdir -p /var/log/redis
        wsl -d $WSLDistribution sudo chown redis:redis /var/log/redis
        
        Write-Info "Redis is configured for ICTServe. Restart Redis to apply changes."
        return $true
    }
    catch {
        Write-Error "Failed to configure Redis: $($_.Exception.Message)"
        return $false
    }
}

# Start Redis service in WSL
function Start-WSLRedis {
    Write-Info "Starting Redis in WSL..."
    
    try {
        # Check if Redis is already running
        $redisStatus = wsl -d $WSLDistribution sudo service redis-server status
        if ($redisStatus -match "Active: active") {
            Write-Info "Redis is already running"
        } else {
            Write-Info "Starting Redis service..."
            wsl -d $WSLDistribution sudo service redis-server start
            Start-Sleep -Seconds 2
        }
        
        # Validate Redis is running and accessible
        if (Test-RedisConnectivity) {
            Write-Success "Redis started successfully and is accessible from Windows"
            return $true
        } else {
            Write-Error "Redis started but is not accessible from Windows"
            return $false
        }
    }
    catch {
        Write-Error "Failed to start Redis: $($_.Exception.Message)"
        return $false
    }
}

# Stop Redis service in WSL
function Stop-WSLRedis {
    Write-Info "Stopping Redis in WSL..."
    
    try {
        wsl -d $WSLDistribution sudo service redis-server stop
        Write-Success "Redis stopped"
        return $true
    }
    catch {
        Write-Error "Failed to stop Redis: $($_.Exception.Message)"
        return $false
    }
}

# Get Redis status
function Get-WSLRedisStatus {
    Write-Info "Checking Redis status in WSL..."
    
    try {
        # Check service status
        $serviceStatus = wsl -d $WSLDistribution sudo service redis-server status
        Write-Info "Service Status:"
        Write-Host $serviceStatus
        
        # Check if Redis is responding
        Write-Info "Testing connectivity..."
        Test-RedisConnectivity | Out-Null
        
        # Check Redis info
        Write-Info "Redis Information:"
        $redisInfo = wsl -d $WSLDistribution redis-cli info server
        Write-Host $redisInfo
        
        # Check memory usage
        Write-Info "Memory Usage:"
        $memoryInfo = wsl -d $WSLDistribution redis-cli info memory | Select-String "used_memory_human"
        Write-Host $memoryInfo
        
        return $true
    }
    catch {
        Write-Error "Failed to get Redis status: $($_.Exception.Message)"
        return $false
    }
}

# Enable Redis auto-start on WSL boot
function Enable-RedisAutoStart {
    Write-Info "Enabling Redis auto-start on WSL boot..."
    
    try {
        # Create systemd service override (if systemd is available)
        $systemdCheck = wsl -d $WSLDistribution systemctl --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            wsl -d $WSLDistribution sudo systemctl enable redis-server
            Write-Success "Redis auto-start enabled via systemd"
        } else {
            # Fallback: Add to .bashrc or create init script
            Write-Info "Systemd not available, using alternative method..."
            $initScript = "sudo service redis-server start > /dev/null 2>&1"
            wsl -d $WSLDistribution "echo '$initScript' >> ~/.bashrc"
            Write-Success "Redis auto-start added to .bashrc"
        }
        
        return $true
    }
    catch {
        Write-Error "Failed to enable Redis auto-start: $($_.Exception.Message)"
        return $false
    }
}

# Main execution
Write-Info "WSL Redis Management for ICTServe v3.6.1"

if (-not (Test-WSLAvailability)) {
    exit 1
}

switch ($Action) {
    "install" {
        if (Install-WSLRedis) {
            Write-Success "Redis installation completed"
            Write-Info "Run: .\manage-redis.ps1 -Action start"
        }
    }
    "configure" {
        if (Configure-WSLRedis) {
            Write-Success "Redis configuration completed"
            Write-Info "Run: .\manage-redis.ps1 -Action restart"
        }
    }
    "start" {
        if (Start-WSLRedis) {
            Enable-RedisAutoStart | Out-Null
        }
    }
    "stop" {
        Stop-WSLRedis
    }
    "restart" {
        Stop-WSLRedis
        Start-Sleep -Seconds 2
        Start-WSLRedis
    }
    "status" {
        Get-WSLRedisStatus
    }
    "test" {
        if (Test-RedisConnectivity) {
            Test-RedisFunctionality
        }
    }
}

Write-Info "WSL Redis management completed. Use 'Get-Help .\manage-redis.ps1' for usage information."