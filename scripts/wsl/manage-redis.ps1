# WSL Redis Management Script for ICTServe
# Provides PowerShell interface for managing Redis service in WSL

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet("start", "stop", "restart", "status", "test", "logs", "config", "info", "monitor")]
    [string]$Action,
    
    [int]$LogLines = 50,
    [switch]$Follow
)

function Write-Info($message) {
    Write-Host $message -ForegroundColor Cyan
}

function Write-Success($message) {
    Write-Host "✓ $message" -ForegroundColor Green
}

function Write-Warning($message) {
    Write-Host "⚠ $message" -ForegroundColor Yellow
}

function Write-Error($message) {
    Write-Host "✗ $message" -ForegroundColor Red
}

function Test-WSLAvailable {
    try {
        wsl --version > $null 2>&1
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Test-RedisInstalled {
    try {
        $version = wsl which redis-server 2>$null
        return $version -ne $null
    } catch {
        return $false
    }
}

function Start-WSLRedis {
    Write-Info "Starting Redis in WSL..."
    
    if (-not (Test-WSLAvailable)) {
        Write-Error "WSL is not available"
        return $false
    }
    
    if (-not (Test-RedisInstalled)) {
        Write-Error "Redis is not installed in WSL. Run install-redis.ps1 first."
        return $false
    }
    
    # Try systemctl first
    $systemctlResult = wsl sudo systemctl start redis-server 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Redis started via systemctl"
    } else {
        # Fallback to service command
        Write-Info "Trying service command..."
        wsl sudo service redis-server start
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Redis started via service command"
        } else {
            Write-Error "Failed to start Redis service"
            return $false
        }
    }
    
    # Wait for Redis to start
    Start-Sleep -Seconds 2
    
    # Validate Redis is running
    return Test-RedisConnectivity
}

function Stop-WSLRedis {
    Write-Info "Stopping Redis in WSL..."
    
    # Try systemctl first
    $systemctlResult = wsl sudo systemctl stop redis-server 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Success "Redis stopped via systemctl"
    } else {
        # Fallback to service command
        Write-Info "Trying service command..."
        wsl sudo service redis-server stop
        if ($LASTEXITCODE -eq 0) {
            Write-Success "Redis stopped via service command"
        } else {
            Write-Warning "Failed to stop Redis service gracefully"
            
            # Force kill Redis processes
            Write-Info "Force killing Redis processes..."
            wsl sudo pkill -f redis-server
            Write-Success "Redis processes terminated"
        }
    }
    
    return $true
}

function Restart-WSLRedis {
    Write-Info "Restarting Redis in WSL..."
    
    Stop-WSLRedis
    Start-Sleep -Seconds 1
    return Start-WSLRedis
}

function Get-RedisStatus {
    Write-Info "Checking Redis status..."
    
    if (-not (Test-WSLAvailable)) {
        Write-Error "WSL is not available"
        return
    }
    
    if (-not (Test-RedisInstalled)) {
        Write-Error "Redis is not installed in WSL"
        return
    }
    
    # Check systemctl status
    $systemctlStatus = wsl sudo systemctl is-active redis-server 2>$null
    Write-Host "Systemctl Status: $systemctlStatus" -ForegroundColor White
    
    # Check process status
    $processes = wsl ps aux | wsl grep redis-server | wsl grep -v grep
    if ($processes) {
        Write-Success "Redis processes running:"
        Write-Host $processes -ForegroundColor Gray
    } else {
        Write-Warning "No Redis processes found"
    }
    
    # Check port binding
    $portCheck = wsl netstat -tlnp | wsl grep :6379
    if ($portCheck) {
        Write-Success "Redis listening on port 6379:"
        Write-Host $portCheck -ForegroundColor Gray
    } else {
        Write-Warning "Redis not listening on port 6379"
    }
    
    # Test connectivity
    Test-RedisConnectivity
}

function Test-RedisConnectivity {
    Write-Info "Testing Redis connectivity..."
    
    # Test from WSL
    $wslTest = wsl redis-cli ping 2>$null
    if ($wslTest -eq "PONG") {
        Write-Success "Redis responds to ping from WSL"
    } else {
        Write-Error "Redis is not responding from WSL"
        return $false
    }
    
    # Test from Windows host
    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $tcpClient.Connect("127.0.0.1", 6379)
        $tcpClient.Close()
        Write-Success "Redis is accessible from Windows host (127.0.0.1:6379)"
        return $true
    } catch {
        Write-Error "Cannot connect to Redis from Windows host: $($_.Exception.Message)"
        Write-Warning "Check WSL networking and Windows Firewall settings"
        return $false
    }
}

function Show-RedisLogs {
    Write-Info "Showing Redis logs (last $LogLines lines)..."
    
    if ($Follow) {
        Write-Info "Following Redis logs (Ctrl+C to stop)..."
        wsl sudo tail -f -n $LogLines /var/log/redis/redis-server.log
    } else {
        wsl sudo tail -n $LogLines /var/log/redis/redis-server.log
    }
}

function Show-RedisConfig {
    Write-Info "Redis configuration:"
    wsl sudo cat /etc/redis/redis.conf | wsl grep -v "^#" | wsl grep -v "^$"
}

function Show-RedisInfo {
    Write-Info "Redis server information:"
    
    if (Test-RedisConnectivity) {
        wsl redis-cli info server
        Write-Host ""
        Write-Info "Memory usage:"
        wsl redis-cli info memory
        Write-Host ""
        Write-Info "Connected clients:"
        wsl redis-cli info clients
    } else {
        Write-Error "Cannot connect to Redis to get information"
    }
}

function Start-RedisMonitor {
    Write-Info "Starting Redis monitor (Ctrl+C to stop)..."
    Write-Warning "This will show all Redis commands in real-time"
    wsl redis-cli monitor
}

# Main script execution
Write-Host "=== ICTServe WSL Redis Management ===" -ForegroundColor Green

switch ($Action.ToLower()) {
    "start" {
        if (Start-WSLRedis) {
            Write-Success "Redis is now running"
        } else {
            Write-Error "Failed to start Redis"
            exit 1
        }
    }
    
    "stop" {
        if (Stop-WSLRedis) {
            Write-Success "Redis has been stopped"
        } else {
            Write-Error "Failed to stop Redis"
            exit 1
        }
    }
    
    "restart" {
        if (Restart-WSLRedis) {
            Write-Success "Redis has been restarted"
        } else {
            Write-Error "Failed to restart Redis"
            exit 1
        }
    }
    
    "status" {
        Get-RedisStatus
    }
    
    "test" {
        if (Test-RedisConnectivity) {
            Write-Success "Redis connectivity test passed"
        } else {
            Write-Error "Redis connectivity test failed"
            exit 1
        }
    }
    
    "logs" {
        Show-RedisLogs
    }
    
    "config" {
        Show-RedisConfig
    }
    
    "info" {
        Show-RedisInfo
    }
    
    "monitor" {
        Start-RedisMonitor
    }
    
    default {
        Write-Error "Unknown action: $Action"
        Write-Host "Available actions: start, stop, restart, status, test, logs, config, info, monitor"
        exit 1
    }
}

Write-Host ""