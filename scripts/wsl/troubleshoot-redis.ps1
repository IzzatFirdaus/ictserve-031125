# Redis Troubleshooting Script for ICTServe WSL Environment
# Diagnoses and attempts to fix common Redis connectivity issues

param(
    [switch]$AutoFix,
    [switch]$Verbose
)

function Write-Issue($title, $description, $solution = "") {
    Write-Host "🔍 $title" -ForegroundColor Yellow
    Write-Host "   $description" -ForegroundColor Gray
    if ($solution) {
        Write-Host "   💡 Solution: $solution" -ForegroundColor Cyan
    }
    Write-Host ""
}

function Write-Fix($message) {
    Write-Host "🔧 $message" -ForegroundColor Green
}

function Write-Check($message, $result) {
    $status = if ($result) { "✓" } else { "✗" }
    $color = if ($result) { "Green" } else { "Red" }
    Write-Host "$status $message" -ForegroundColor $color
}

function Test-WSLInstallation {
    try {
        $wslVersion = wsl --version 2>$null
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Test-UbuntuDistribution {
    try {
        $distros = wsl -l -v
        return $distros -match "Ubuntu"
    } catch {
        return $false
    }
}

function Test-RedisInstallation {
    try {
        $redisPath = wsl which redis-server 2>$null
        return $redisPath -ne $null -and $redisPath -ne ""
    } catch {
        return $false
    }
}

function Test-RedisConfiguration {
    try {
        $configExists = wsl test -f /etc/redis/redis.conf
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Test-RedisBinding {
    try {
        $bindConfig = wsl grep "^bind" /etc/redis/redis.conf 2>$null
        return $bindConfig -match "0\.0\.0\.0"
    } catch {
        return $false
    }
}

function Test-RedisProtectedMode {
    try {
        $protectedMode = wsl grep "^protected-mode" /etc/redis/redis.conf 2>$null
        return $protectedMode -match "no"
    } catch {
        return $false
    }
}

function Test-RedisService {
    try {
        $status = wsl sudo systemctl is-active redis-server 2>$null
        return $status -eq "active"
    } catch {
        return $false
    }
}

function Test-RedisPort {
    try {
        $portCheck = wsl netstat -tlnp 2>$null | wsl grep :6379
        return $portCheck -ne $null
    } catch {
        return $false
    }
}

function Test-WindowsFirewall {
    try {
        # Check if Windows Firewall might be blocking the connection
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $asyncResult = $tcpClient.BeginConnect("127.0.0.1", 6379, $null, $null)
        $success = $asyncResult.AsyncWaitHandle.WaitOne(2000, $false)
        
        if ($success) {
            $tcpClient.EndConnect($asyncResult)
            $tcpClient.Close()
            return $true
        } else {
            $tcpClient.Close()
            return $false
        }
    } catch {
        return $false
    }
}

function Fix-RedisConfiguration {
    Write-Fix "Updating Redis configuration for ICTServe..."
    
    # Backup current configuration
    wsl sudo cp /etc/redis/redis.conf /etc/redis/redis.conf.backup.$(Get-Date -Format 'yyyyMMddHHmmss')
    
    # Update bind setting
    wsl sudo sed -i 's/^bind 127\.0\.0\.1/bind 0.0.0.0/' /etc/redis/redis.conf
    
    # Update protected mode
    wsl sudo sed -i 's/^protected-mode yes/protected-mode no/' /etc/redis/redis.conf
    
    # Add bind setting if it doesn't exist
    $bindExists = wsl grep "^bind" /etc/redis/redis.conf
    if (-not $bindExists) {
        wsl sudo sh -c 'echo "bind 0.0.0.0" >> /etc/redis/redis.conf'
    }
    
    # Add protected mode setting if it doesn't exist
    $protectedExists = wsl grep "^protected-mode" /etc/redis/redis.conf
    if (-not $protectedExists) {
        wsl sudo sh -c 'echo "protected-mode no" >> /etc/redis/redis.conf'
    }
    
    Write-Fix "Redis configuration updated"
}

function Fix-RedisService {
    Write-Fix "Restarting Redis service..."
    
    # Stop Redis
    wsl sudo systemctl stop redis-server 2>$null
    wsl sudo service redis-server stop 2>$null
    
    # Kill any remaining processes
    wsl sudo pkill -f redis-server 2>$null
    
    # Start Redis
    wsl sudo systemctl start redis-server 2>$null
    if ($LASTEXITCODE -ne 0) {
        wsl sudo service redis-server start
    }
    
    Start-Sleep -Seconds 3
    Write-Fix "Redis service restarted"
}

function Fix-WSLNetworking {
    Write-Fix "Attempting to fix WSL networking..."
    
    # Restart WSL networking
    Write-Host "   Restarting WSL..." -ForegroundColor Gray
    wsl --shutdown
    Start-Sleep -Seconds 5
    
    # Test WSL is back up
    wsl echo "WSL restarted" > $null
    
    Write-Fix "WSL networking reset"
}

# Main troubleshooting execution
Write-Host "=== ICTServe Redis Troubleshooting ===" -ForegroundColor Green
Write-Host "Diagnosing Redis connectivity issues..." -ForegroundColor Yellow
Write-Host ""

$issues = @()
$fixes = @()

# Check WSL Installation
$wslInstalled = Test-WSLInstallation
Write-Check "WSL Installation" $wslInstalled
if (-not $wslInstalled) {
    $issues += "WSL is not installed or not working properly"
    Write-Issue "WSL Not Available" "Windows Subsystem for Linux is not installed or not functioning" "Install WSL: wsl --install"
}

# Check Ubuntu Distribution
if ($wslInstalled) {
    $ubuntuAvailable = Test-UbuntuDistribution
    Write-Check "Ubuntu Distribution" $ubuntuAvailable
    if (-not $ubuntuAvailable) {
        $issues += "Ubuntu distribution is not available in WSL"
        Write-Issue "Ubuntu Missing" "Ubuntu distribution is not installed in WSL" "Install Ubuntu: wsl --install -d Ubuntu"
    }
}

# Check Redis Installation
if ($wslInstalled -and $ubuntuAvailable) {
    $redisInstalled = Test-RedisInstallation
    Write-Check "Redis Installation" $redisInstalled
    if (-not $redisInstalled) {
        $issues += "Redis is not installed in WSL"
        Write-Issue "Redis Not Installed" "Redis server is not installed in the WSL Ubuntu distribution" "Run: .\scripts\wsl\install-redis.ps1"
    }
}

# Check Redis Configuration
if ($redisInstalled) {
    $configExists = Test-RedisConfiguration
    Write-Check "Redis Configuration File" $configExists
    
    if ($configExists) {
        $bindingCorrect = Test-RedisBinding
        Write-Check "Redis Binding Configuration" $bindingCorrect
        if (-not $bindingCorrect) {
            $issues += "Redis is not configured to bind to all interfaces"
            $fixes += "Fix-RedisConfiguration"
            Write-Issue "Incorrect Binding" "Redis is not configured to accept connections from Windows host" "Update bind setting to 0.0.0.0"
        }
        
        $protectedModeOff = Test-RedisProtectedMode
        Write-Check "Redis Protected Mode" $protectedModeOff
        if (-not $protectedModeOff) {
            $issues += "Redis protected mode is enabled"
            $fixes += "Fix-RedisConfiguration"
            Write-Issue "Protected Mode Enabled" "Redis protected mode prevents external connections" "Disable protected mode for development"
        }
    }
}

# Check Redis Service
if ($redisInstalled) {
    $serviceRunning = Test-RedisService
    Write-Check "Redis Service Status" $serviceRunning
    if (-not $serviceRunning) {
        $issues += "Redis service is not running"
        $fixes += "Fix-RedisService"
        Write-Issue "Service Not Running" "Redis service is not active" "Start Redis service"
    }
}

# Check Redis Port
if ($serviceRunning) {
    $portListening = Test-RedisPort
    Write-Check "Redis Port Listening" $portListening
    if (-not $portListening) {
        $issues += "Redis is not listening on port 6379"
        $fixes += "Fix-RedisService"
        Write-Issue "Port Not Listening" "Redis is not listening on the expected port 6379" "Restart Redis service"
    }
}

# Check Windows Connectivity
if ($portListening) {
    $windowsConnectivity = Test-WindowsFirewall
    Write-Check "Windows Host Connectivity" $windowsConnectivity
    if (-not $windowsConnectivity) {
        $issues += "Cannot connect from Windows host to WSL Redis"
        $fixes += "Fix-WSLNetworking"
        Write-Issue "Windows Connectivity Failed" "Windows cannot connect to Redis in WSL" "Check Windows Firewall and WSL networking"
    }
}

# Summary
Write-Host ""
if ($issues.Count -eq 0) {
    Write-Host "🎉 No issues detected! Redis should be working correctly." -ForegroundColor Green
} else {
    Write-Host "❌ Found $($issues.Count) issue(s):" -ForegroundColor Red
    foreach ($issue in $issues) {
        Write-Host "   • $issue" -ForegroundColor Yellow
    }
    
    if ($fixes.Count -gt 0 -and $AutoFix) {
        Write-Host ""
        Write-Host "🔧 Attempting automatic fixes..." -ForegroundColor Cyan
        
        foreach ($fix in ($fixes | Select-Object -Unique)) {
            switch ($fix) {
                "Fix-RedisConfiguration" { Fix-RedisConfiguration }
                "Fix-RedisService" { Fix-RedisService }
                "Fix-WSLNetworking" { Fix-WSLNetworking }
            }
        }
        
        Write-Host ""
        Write-Host "✅ Automatic fixes applied. Please run the connectivity test:" -ForegroundColor Green
        Write-Host "   .\scripts\wsl\test-redis-connectivity.ps1" -ForegroundColor Yellow
        
    } elseif ($fixes.Count -gt 0) {
        Write-Host ""
        Write-Host "💡 Run with -AutoFix to attempt automatic repairs:" -ForegroundColor Cyan
        Write-Host "   .\scripts\wsl\troubleshoot-redis.ps1 -AutoFix" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "For additional help:" -ForegroundColor White
Write-Host "• Test connectivity: .\scripts\wsl\test-redis-connectivity.ps1" -ForegroundColor Gray
Write-Host "• Manage Redis: .\scripts\wsl\manage-redis.ps1 status" -ForegroundColor Gray
Write-Host "• View logs: .\scripts\wsl\manage-redis.ps1 logs" -ForegroundColor Gray