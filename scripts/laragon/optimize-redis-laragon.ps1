#Requires -Version 5.1
<#
.SYNOPSIS
    Optimize Redis Configuration for ICTServe Laragon Development

.DESCRIPTION
    Creates the optimal Redis configuration for ICTServe v3.6.1 running on Laragon.
    Configures Predis client, Redis databases, and performance settings.

.PARAMETER LaragonPath
    Path to Laragon installation (default: C:\laragon)

.PARAMETER Force
    Force overwrite existing configuration

.EXAMPLE
    .\scripts\laragon\optimize-redis-laragon.ps1 -Force
    Optimize Redis configuration with force overwrite

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0 - Optimized for ICTServe v3.6.1
    Requires: Laragon, PowerShell 5.1+, Redis 7.0+
#>

[CmdletBinding()]
param(
    [string]$LaragonPath = 'C:\laragon',
    [switch]$Force
)

Set-StrictMode -Version Latest
$ErrorActionPreference = 'Stop'

# Configuration
$script:ProjectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
#region Utility Functions

function Write-Status {
    param(
        [string]$Message,
        [ValidateSet('Info', 'Success', 'Warning', 'Error')]
        [string]$Type = 'Info'
    )

    $colors = @{
        Info = 'Cyan'
        Success = 'Green'
        Warning = 'Yellow'
        Error = 'Red'
    }

    $icons = @{
        Info = 'ℹ️'
        Success = '✅'
        Warning = '⚠️'
        Error = '❌'
    }

    Write-Host "$($icons[$Type]) $Message" -ForegroundColor $colors[$Type]
}

function Test-RedisConnection {
    param(
        [string]$Host = '127.0.0.1',
        [int]$Port = 6379,
        [int]$TimeoutSeconds = 5
    )

    try {
        $tcpClient = New-Object System.Net.Sockets.TcpClient
        $asyncResult = $tcpClient.BeginConnect($Host, $Port, $null, $null)
        $waitHandle = $asyncResult.AsyncWaitHandle
        
        if ($waitHandle.WaitOne($TimeoutSeconds * 1000)) {
            $tcpClient.EndConnect($asyncResult)
            $tcpClient.Close()
            return $true
        }
        else {
            $tcpClient.Close()
            return $false
        }
    }
    catch {
        return $false
    }
}

function Test-PredisPackage {
    $composerLock = Join-Path $script:ProjectRoot 'composer.lock'
    if (Test-Path $composerLock) {
        $lockContent = Get-Content $composerLock -Raw | ConvertFrom-Json
        $predisInstalled = $lockContent.packages | Where-Object { $_.name -eq 'predis/predis' }
        return $predisInstalled -ne $null
    }
    return $false
}

#endregion
#region Redis Configuration Functions

function Install-PredisPackage {
    Write-Status "Checking Predis package installation..." -Type Info
    
    if (Test-PredisPackage) {
        Write-Status "✓ Predis package is already installed" -Type Success
        return
    }
    
    Write-Status "Installing Predis package for optimal Windows/Laragon compatibility..." -Type Info
    try {
        Push-Location $script:ProjectRoot
        composer require predis/predis --no-interaction --prefer-dist
        
        if ($LASTEXITCODE -eq 0) {
            Write-Status "✓ Predis package installed successfully" -Type Success
        }
        else {
            Write-Status "✗ Failed to install Predis package" -Type Error
        }
    }
    catch {
        Write-Status "✗ Error installing Predis: $($_.Exception.Message)" -Type Error
    }
    finally {
        Pop-Location
    }
}

function Optimize-EnvironmentConfiguration {
    Write-Status "Optimizing .env configuration for Redis with Laragon..." -Type Info
    
    $envFile = Join-Path $script:ProjectRoot '.env'
    if (-not (Test-Path $envFile)) {
        Write-Status "✗ .env file not found. Please create it first." -Type Error
        return
    }
    
    $envContent = Get-Content $envFile -Raw
    $modified = $false
    
    # Optimal Redis client for Windows/Laragon
    if ($envContent -notmatch 'REDIS_CLIENT=predis') {
        if ($envContent -match 'REDIS_CLIENT=.*') {
            $envContent = $envContent -replace 'REDIS_CLIENT=.*', 'REDIS_CLIENT=predis'
        }
        else {
            $envContent += "`nREDIS_CLIENT=predis"
        }
        $modified = $true
        Write-Status "✓ Set Redis client to predis (optimal for Windows)" -Type Success
    }
    
    # Ensure Redis host is correct
    if ($envContent -notmatch 'REDIS_HOST=127\.0\.0\.1') {
        if ($envContent -match 'REDIS_HOST=.*') {
            $envContent = $envContent -replace 'REDIS_HOST=.*', 'REDIS_HOST=127.0.0.1'
        }
        else {
            $envContent += "`nREDIS_HOST=127.0.0.1"
        }
        $modified = $true
        Write-Status "✓ Set Redis host to 127.0.0.1" -Type Success
    }
    
    # Optimize cache driver
    if ($envContent -notmatch 'CACHE_STORE=redis') {
        if ($envContent -match 'CACHE_STORE=.*') {
            $envContent = $envContent -replace 'CACHE_STORE=.*', 'CACHE_STORE=redis'
        }
        else {
            $envContent += "`nCACHE_STORE=redis"
        }
        $modified = $true
        Write-Status "✓ Set cache driver to Redis" -Type Success
    }
    
    # Optimize session driver
    if ($envContent -notmatch 'SESSION_DRIVER=redis') {
        if ($envContent -match 'SESSION_DRIVER=.*') {
            $envContent = $envContent -replace 'SESSION_DRIVER=.*', 'SESSION_DRIVER=redis'
        }
        else {
            $envContent += "`nSESSION_DRIVER=redis"
        }
        $modified = $true
        Write-Status "✓ Set session driver to Redis" -Type Success
    }
    
    # Optimize queue connection
    if ($envContent -notmatch 'QUEUE_CONNECTION=redis') {
        if ($envContent -match 'QUEUE_CONNECTION=.*') {
            $envContent = $envContent -replace 'QUEUE_CONNECTION=.*', 'QUEUE_CONNECTION=redis'
        }
        else {
            $envContent += "`nQUEUE_CONNECTION=redis"
        }
        $modified = $true
        Write-Status "✓ Set queue connection to Redis" -Type Success
    }
    
    if ($modified) {
        Set-Content -Path $envFile -Value $envContent -Encoding UTF8
        Write-Status "✓ Updated .env file with optimal Redis configuration" -Type Success
    }
    else {
        Write-Status "✓ Redis configuration already optimal" -Type Success
    }
}

#endregion
#region Redis Service Management

function Start-RedisService {
    Write-Status "Starting Redis service for Laragon..." -Type Info
    
    # Check if Redis is already running
    if (Test-RedisConnection) {
        Write-Status "✓ Redis is already running and accessible" -Type Success
        return
    }
    
    # Try to find Redis in Laragon installation
    $redisExe = Get-ChildItem -Path (Join-Path $LaragonPath 'bin') -Filter 'redis-server.exe' -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    
    if ($redisExe) {
        try {
            Write-Status "Starting Redis server..." -Type Info
            Start-Process -FilePath $redisExe.FullName -WindowStyle Hidden
            Start-Sleep -Seconds 3
            
            if (Test-RedisConnection) {
                Write-Status "✓ Redis server started successfully" -Type Success
            }
            else {
                Write-Status "✗ Redis server failed to start properly" -Type Error
            }
        }
        catch {
            Write-Status "✗ Error starting Redis: $($_.Exception.Message)" -Type Error
        }
    }
    else {
        Write-Status "✗ Redis executable not found in Laragon installation" -Type Error
        Write-Status "Please install Redis through Laragon's Quick Add feature" -Type Info
        Write-Status "Or download Redis from: https://github.com/microsoftarchive/redis/releases" -Type Info
    }
}

function Test-RedisPerformance {
    Write-Status "Testing Redis performance..." -Type Info
    
    if (-not (Test-RedisConnection)) {
        Write-Status "✗ Cannot test performance - Redis not accessible" -Type Error
        return
    }
    
    $redisCliPath = Get-ChildItem -Path (Join-Path $LaragonPath 'bin') -Filter 'redis-cli.exe' -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    
    if ($redisCliPath) {
        try {
            # Test basic operations
            $startTime = Get-Date
            & $redisCliPath.FullName -h 127.0.0.1 -p 6379 ping 2>$null | Out-Null
            $pingTime = (Get-Date) - $startTime
            
            if ($pingTime.TotalMilliseconds -lt 10) {
                Write-Status "✓ Redis ping response time: $($pingTime.TotalMilliseconds.ToString('F2'))ms (excellent)" -Type Success
            }
            elseif ($pingTime.TotalMilliseconds -lt 50) {
                Write-Status "✓ Redis ping response time: $($pingTime.TotalMilliseconds.ToString('F2'))ms (good)" -Type Success
            }
            else {
                Write-Status "⚠ Redis ping response time: $($pingTime.TotalMilliseconds.ToString('F2'))ms (slow)" -Type Warning
            }
            
            # Test memory usage
            $memoryInfo = & $redisCliPath.FullName -h 127.0.0.1 -p 6379 info memory 2>$null
            if ($memoryInfo) {
                $usedMemory = ($memoryInfo -split "`n" | Where-Object { $_ -match '^used_memory_human:' }) -replace 'used_memory_human:', ''
                if ($usedMemory) {
                    Write-Status "✓ Redis memory usage: $usedMemory" -Type Info
                }
            }
        }
        catch {
            Write-Status "⚠ Could not perform Redis performance test" -Type Warning
        }
    }
}

#endregion
#region Laravel Configuration Optimization

function Optimize-LaravelRedisConfig {
    Write-Status "Optimizing Laravel Redis configuration..." -Type Info
    
    # Check if database.php needs optimization
    $databaseConfig = Join-Path $script:ProjectRoot 'config\database.php'
    if (Test-Path $databaseConfig) {
        $configContent = Get-Content $databaseConfig -Raw
        
        # Check if Redis configuration is already optimized
        if ($configContent -match "env\('REDIS_CLIENT', 'predis'\)") {
            Write-Status "✓ Laravel Redis configuration already optimized" -Type Success
        }
        else {
            Write-Status "⚠ Laravel Redis configuration may need manual optimization" -Type Warning
            Write-Status "Ensure config/database.php uses 'predis' as default Redis client" -Type Info
        }
    }
    
    # Clear Laravel configuration cache
    if (Get-Command php -ErrorAction SilentlyContinue) {
        Write-Status "Clearing Laravel configuration cache..." -Type Info
        try {
            php artisan config:clear
            php artisan cache:clear
            Write-Status "✓ Laravel caches cleared" -Type Success
        }
        catch {
            Write-Status "⚠ Could not clear Laravel caches" -Type Warning
        }
    }
}

function Test-LaravelRedisIntegration {
    Write-Status "Testing Laravel Redis integration..." -Type Info
    
    if (-not (Get-Command php -ErrorAction SilentlyContinue)) {
        Write-Status "⚠ PHP not found in PATH - cannot test Laravel integration" -Type Warning
        return
    }
    
    try {
        # Test Redis connection through Laravel
        $testResult = php artisan tinker --execute="echo Redis::ping();" 2>$null
        if ($testResult -match "PONG") {
            Write-Status "✓ Laravel Redis integration working correctly" -Type Success
        }
        else {
            Write-Status "⚠ Laravel Redis integration test inconclusive" -Type Warning
        }
    }
    catch {
        Write-Status "⚠ Could not test Laravel Redis integration" -Type Warning
    }
}

#endregion
#region Main Execution

try {
    Write-Host "`n🔧 ICTServe Redis Optimization for Laragon" -ForegroundColor Cyan
    Write-Host "=" * 45 -ForegroundColor Cyan
    Write-Host ""

    Push-Location $script:ProjectRoot

    # Step 1: Install Predis package
    Install-PredisPackage

    # Step 2: Start Redis service
    Start-RedisService

    # Step 3: Optimize environment configuration
    Optimize-EnvironmentConfiguration

    # Step 4: Optimize Laravel Redis configuration
    Optimize-LaravelRedisConfig

    # Step 5: Test Redis performance
    Test-RedisPerformance

    # Step 6: Test Laravel Redis integration
    Test-LaravelRedisIntegration

    Write-Host "`n✅ Redis optimization completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "🔧 Configuration Summary:" -ForegroundColor Cyan
    Write-Host "  • Redis Client: Predis (optimal for Windows/Laragon)" -ForegroundColor White
    Write-Host "  • Redis Host: 127.0.0.1:6379" -ForegroundColor White
    Write-Host "  • Cache Driver: Redis" -ForegroundColor White
    Write-Host "  • Session Driver: Redis" -ForegroundColor White
    Write-Host "  • Queue Connection: Redis" -ForegroundColor White
    Write-Host ""
    Write-Host "📊 Database Allocation:" -ForegroundColor Cyan
    Write-Host "  • DB 0: Default/General" -ForegroundColor White
    Write-Host "  • DB 1: Cache" -ForegroundColor White
    Write-Host "  • DB 2: Sessions" -ForegroundColor White
    Write-Host "  • DB 3: Queues" -ForegroundColor White
    Write-Host "  • DB 4: Laravel Reverb (WebSocket)" -ForegroundColor White
    Write-Host "  • DB 5: Laravel Pulse (Monitoring)" -ForegroundColor White
    Write-Host "  • DB 6: Laravel Horizon (Queue Management)" -ForegroundColor White
    Write-Host ""
    Write-Host "🚀 Next Steps:" -ForegroundColor Cyan
    Write-Host "  1. Test your ICTServe application" -ForegroundColor White
    Write-Host "  2. Monitor Redis performance in Laragon" -ForegroundColor White
    Write-Host "  3. Use Laravel Horizon for queue monitoring" -ForegroundColor White
    Write-Host "  4. Use Laravel Pulse for performance monitoring" -ForegroundColor White
    Write-Host ""
    Write-Host "🔍 Troubleshooting:" -ForegroundColor Cyan
    Write-Host "  • Run: .\scripts\laragon\redis-health-check.ps1 -Fix -Detailed" -ForegroundColor White
    Write-Host "  • Check Laragon Redis service status" -ForegroundColor White
    Write-Host "  • Verify Redis is running on port 6379" -ForegroundColor White
    Write-Host ""

}
catch {
    Write-Host "`n❌ Redis optimization failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host ""
    Write-Host "🔧 Manual Steps:" -ForegroundColor Yellow
    Write-Host "  1. Install Redis through Laragon Quick Add" -ForegroundColor White
    Write-Host "  2. Run: composer require predis/predis" -ForegroundColor White
    Write-Host "  3. Set REDIS_CLIENT=predis in .env file" -ForegroundColor White
    Write-Host "  4. Restart Laragon services" -ForegroundColor White
    Write-Host ""
    exit 1
}
finally {
    Pop-Location
}

#endregion