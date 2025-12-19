# Redis Connectivity Test Script for ICTServe
# Comprehensive testing of Redis connectivity from Windows host to WSL

param(
    [switch]$Verbose,
    [int]$Timeout = 5
)

function Write-TestResult($test, $result, $message = "") {
    $status = if ($result) { "✓ PASS" } else { "✗ FAIL" }
    $color = if ($result) { "Green" } else { "Red" }
    
    Write-Host "$status $test" -ForegroundColor $color
    if ($message -and ($Verbose -or -not $result)) {
        Write-Host "    $message" -ForegroundColor Gray
    }
}

function Test-WSLAvailability {
    try {
        wsl --version > $null 2>&1
        return $LASTEXITCODE -eq 0
    } catch {
        return $false
    }
}

function Test-RedisInstallation {
    try {
        $version = wsl which redis-server 2>$null
        return $version -ne $null -and $version -ne ""
    } catch {
        return $false
    }
}

function Test-RedisService {
    try {
        $status = wsl sudo systemctl is-active redis-server 2>$null
        return $status -eq "active"
    } catch {
        # Fallback to process check
        try {
            $processes = wsl pgrep redis-server 2>$null
            return $processes -ne $null -and $processes -ne ""
        } catch {
            return $false
        }
    }
}

function Test-RedisPort {
    try {
        $portCheck = wsl netstat -tlnp 2>$null | wsl grep :6379
        return $portCheck -ne $null -and $portCheck -ne ""
    } catch {
        return $false
    }
}

function Test-WSLRedisConnection {
    try {
        $response = wsl redis-cli ping 2>$null
        return $response -eq "PONG"
    } catch {
        return $false
    }
}

function Test-WindowsHostConnection {
    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $asyncResult = $tcpClient.BeginConnect("127.0.0.1", 6379, $null, $null)
        $success = $asyncResult.AsyncWaitHandle.WaitOne($Timeout * 1000, $false)
        
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

function Test-RedisCommands {
    try {
        # Test basic Redis commands from Windows
        $testKey = "ictserve:test:$(Get-Date -Format 'yyyyMMddHHmmss')"
        $testValue = "ICTServe Redis Test"
        
        # Use redis-cli through WSL to test commands
        $setResult = wsl redis-cli set $testKey "$testValue" 2>$null
        if ($setResult -ne "OK") {
            return $false
        }
        
        $getValue = wsl redis-cli get $testKey 2>$null
        if ($getValue -ne $testValue) {
            return $false
        }
        
        $delResult = wsl redis-cli del $testKey 2>$null
        if ($delResult -ne "1") {
            return $false
        }
        
        return $true
    } catch {
        return $false
    }
}

function Test-RedisInfo {
    try {
        $info = wsl redis-cli info server 2>$null
        return $info -ne $null -and $info -ne "" -and $info.Contains("redis_version")
    } catch {
        return $false
    }
}

function Test-LaravelRedisConnection {
    # Test if Laravel can connect to Redis (if Laravel is available)
    try {
        if (Test-Path "artisan") {
            $laravelTest = php artisan tinker --execute="Redis::connection()->ping()" 2>$null
            return $laravelTest.Contains("PONG")
        } else {
            return $null # Laravel not available, skip test
        }
    } catch {
        return $false
    }
}

function Get-RedisConfiguration {
    try {
        $config = @{}
        $configLines = wsl redis-cli config get "*" 2>$null
        
        if ($configLines) {
            for ($i = 0; $i -lt $configLines.Count; $i += 2) {
                if ($i + 1 -lt $configLines.Count) {
                    $config[$configLines[$i]] = $configLines[$i + 1]
                }
            }
        }
        
        return $config
    } catch {
        return @{}
    }
}

# Main test execution
Write-Host "=== ICTServe Redis Connectivity Test ===" -ForegroundColor Green
Write-Host "Testing Redis connectivity from Windows host to WSL..." -ForegroundColor Yellow
Write-Host ""

$allTestsPassed = $true

# Test 1: WSL Availability
$wslAvailable = Test-WSLAvailability
Write-TestResult "WSL Availability" $wslAvailable "WSL must be installed and running"
$allTestsPassed = $allTestsPassed -and $wslAvailable

if (-not $wslAvailable) {
    Write-Host ""
    Write-Host "WSL is not available. Please install WSL first:" -ForegroundColor Red
    Write-Host "  wsl --install" -ForegroundColor Yellow
    exit 1
}

# Test 2: Redis Installation
$redisInstalled = Test-RedisInstallation
Write-TestResult "Redis Installation" $redisInstalled "Redis server must be installed in WSL"
$allTestsPassed = $allTestsPassed -and $redisInstalled

if (-not $redisInstalled) {
    Write-Host ""
    Write-Host "Redis is not installed. Run the installation script:" -ForegroundColor Red
    Write-Host "  .\scripts\wsl\install-redis.ps1" -ForegroundColor Yellow
    exit 1
}

# Test 3: Redis Service Status
$redisService = Test-RedisService
Write-TestResult "Redis Service Status" $redisService "Redis service must be running"
$allTestsPassed = $allTestsPassed -and $redisService

# Test 4: Redis Port Binding
$redisPort = Test-RedisPort
Write-TestResult "Redis Port Binding" $redisPort "Redis must be listening on port 6379"
$allTestsPassed = $allTestsPassed -and $redisPort

# Test 5: WSL Redis Connection
$wslConnection = Test-WSLRedisConnection
Write-TestResult "WSL Redis Connection" $wslConnection "Redis must respond to ping from WSL"
$allTestsPassed = $allTestsPassed -and $wslConnection

# Test 6: Windows Host Connection
$windowsConnection = Test-WindowsHostConnection
Write-TestResult "Windows Host Connection" $windowsConnection "Redis must be accessible from Windows host (127.0.0.1:6379)"
$allTestsPassed = $allTestsPassed -and $windowsConnection

# Test 7: Redis Commands
$redisCommands = Test-RedisCommands
Write-TestResult "Redis Commands" $redisCommands "Basic Redis commands (SET, GET, DEL) must work"
$allTestsPassed = $allTestsPassed -and $redisCommands

# Test 8: Redis Info
$redisInfo = Test-RedisInfo
Write-TestResult "Redis Info" $redisInfo "Redis server info must be accessible"
$allTestsPassed = $allTestsPassed -and $redisInfo

# Test 9: Laravel Redis Connection (if available)
$laravelConnection = Test-LaravelRedisConnection
if ($laravelConnection -ne $null) {
    Write-TestResult "Laravel Redis Connection" $laravelConnection "Laravel must be able to connect to Redis"
    $allTestsPassed = $allTestsPassed -and $laravelConnection
} else {
    Write-Host "⊝ SKIP Laravel Redis Connection" -ForegroundColor Yellow
    Write-Host "    Laravel not detected in current directory" -ForegroundColor Gray
}

# Display configuration information if verbose
if ($Verbose -and $wslConnection) {
    Write-Host ""
    Write-Host "=== Redis Configuration ===" -ForegroundColor Cyan
    
    $config = Get-RedisConfiguration
    if ($config.Count -gt 0) {
        Write-Host "Redis Version: $($config['redis_version'])" -ForegroundColor White
        Write-Host "Redis Mode: $($config['redis_mode'])" -ForegroundColor White
        Write-Host "Max Memory: $($config['maxmemory'])" -ForegroundColor White
        Write-Host "Max Memory Policy: $($config['maxmemory-policy'])" -ForegroundColor White
        Write-Host "Databases: $($config['databases'])" -ForegroundColor White
    }
    
    Write-Host ""
    Write-Host "Redis Server Info:" -ForegroundColor Cyan
    wsl redis-cli info server | Select-String "redis_version|redis_mode|process_id|uptime_in_seconds"
}

# Final result
Write-Host ""
if ($allTestsPassed) {
    Write-Host "🎉 All Redis connectivity tests passed!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Redis is properly configured and accessible from Windows host." -ForegroundColor Green
    Write-Host "Connection details:" -ForegroundColor White
    Write-Host "  Host: 127.0.0.1" -ForegroundColor Gray
    Write-Host "  Port: 6379" -ForegroundColor Gray
    Write-Host "  Password: (none - development mode)" -ForegroundColor Gray
    Write-Host ""
    Write-Host "You can now update your Laravel .env file:" -ForegroundColor Yellow
    Write-Host "  REDIS_HOST=127.0.0.1" -ForegroundColor Gray
    Write-Host "  REDIS_PORT=6379" -ForegroundColor Gray
    Write-Host "  REDIS_PASSWORD=null" -ForegroundColor Gray
    exit 0
} else {
    Write-Host "❌ Some Redis connectivity tests failed!" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please check the failed tests above and:" -ForegroundColor Yellow
    Write-Host "1. Ensure WSL is properly installed and running" -ForegroundColor White
    Write-Host "2. Run the Redis installation script: .\scripts\wsl\install-redis.ps1" -ForegroundColor White
    Write-Host "3. Check Windows Firewall settings" -ForegroundColor White
    Write-Host "4. Verify WSL networking configuration" -ForegroundColor White
    exit 1
}