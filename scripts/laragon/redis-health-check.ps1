#Requires -Version 5.1
<#
.SYNOPSIS
    Redis Health Check and Troubleshooting for ICTServe Laragon

.DESCRIPTION
    Comprehensive Redis health check, troubleshooting, and optimization script
    specifically designed for ICTServe running on Laragon.

.PARAMETER LaragonPath
    Path to Laragon installation (default: C:\laragon)

.PARAMETER Fix
    Attempt to fix common Redis issues automatically

.PARAMETER Detailed
    Show detailed Redis configuration and performance metrics

.EXAMPLE
    .\scripts\laragon\redis-health-check.ps1 -Fix -Detailed
    Run comprehensive health check with automatic fixes

.NOTES
    Author: ICTServe Development Team
    Version: 1.0.0
    Requires: Laragon, PowerShell 5.1+
#>

[CmdletBinding()]
param(
    [string]$LaragonPath = 'C:\laragon',
    [switch]$Fix,
    [switch]$Detailed
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

function Get-RedisInfo {
    param(
        [string]$Host = '127.0.0.1',
        [int]$Port = 6379
    )

    try {
        # Try to get Redis info using redis-cli if available
        $redisCliPath = Get-ChildItem -Path (Join-Path $LaragonPath 'bin') -Filter 'redis-cli.exe' -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
        
        if ($redisCliPath) {
            $info = & $redisCliPath.FullName -h $Host -p $Port info 2>$null
            if ($LASTEXITCODE -eq 0) {
                return $info
            }
        }
        
        return $null
    }
    catch {
        return $null
    }
}

function Test-PHPRedisExtension {
    try {
        $phpPath = Get-ChildItem -Path (Join-Path $LaragonPath 'bin\php') -Filter 'php.exe' -Recurse | Select-Object -First 1
        if ($phpPath) {
            $extensions = & $phpPath.FullName -m 2>$null
            return ($extensions -contains 'redis')
        }
        return $false
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

#region Health Check Functions

function Test-RedisService {
    Write-Status "Checking Redis service status..." -Type Info
    
    $issues = @()
    $recommendations = @()
    
    # Test basic connection
    if (Test-RedisConnection) {
        Write-Status "✓ Redis is accessible on 127.0.0.1:6379" -Type Success
    }
    else {
        Write-Status "✗ Redis is not accessible on 127.0.0.1:6379" -Type Error
        $issues += "Redis connection failed"
        $recommendations += "Start Redis service or check if Redis is installed in Laragon"
    }
    
    # Check Redis process
    $redisProcess = Get-Process -Name 'redis-server' -ErrorAction SilentlyContinue
    if ($redisProcess) {
        Write-Status "✓ Redis server process is running (PID: $($redisProcess.Id))" -Type Success
    }
    else {
        Write-Status "✗ Redis server process not found" -Type Warning
        $issues += "Redis process not running"
        $recommendations += "Start Redis server manually or through Laragon"
    }
    
    # Get Redis info if available
    if ($Detailed) {
        $redisInfo = Get-RedisInfo
        if ($redisInfo) {
            Write-Status "Redis Server Information:" -Type Info
            $redisInfo -split "`n" | Where-Object { $_ -match '^(redis_version|used_memory_human|connected_clients|total_commands_processed):' } | ForEach-Object {
                Write-Host "  $_" -ForegroundColor White
            }
        }
    }
    
    return @{
        Issues = $issues
        Recommendations = $recommendations
    }
}

function Test-PHPRedisConfiguration {
    Write-Status "Checking PHP Redis configuration..." -Type Info
    
    $issues = @()
    $recommendations = @()
    
    # Check PHP Redis extension
    if (Test-PHPRedisExtension) {
        Write-Status "✓ PHP Redis extension is installed" -Type Success
    }
    else {
        Write-Status "✗ PHP Redis extension not found" -Type Warning
        $issues += "PHP Redis extension missing"
        $recommendations += "Install PHP Redis extension or use Predis as fallback"
    }
    
    # Check Predis package
    if (Test-PredisPackage) {
        Write-Status "✓ Predis package is installed" -Type Success
    }
    else {
        Write-Status "✗ Predis package not found" -Type Warning
        $issues += "Predis package missing"
        $recommendations += "Install Predis package: composer require predis/predis"
    }
    
    return @{
        Issues = $issues
        Recommendations = $recommendations
    }
}

function Test-LaravelRedisConfiguration {
    Write-Status "Checking Laravel Redis configuration..." -Type Info
    
    $issues = @()
    $recommendations = @()
    
    # Check .env file
    $envFile = Join-Path $script:ProjectRoot '.env'
    if (Test-Path $envFile) {
        $envContent = Get-Content $envFile -Raw
        
        # Check Redis client setting
        if ($envContent -match 'REDIS_CLIENT=predis') {
            Write-Status "✓ Redis client set to predis (recommended for Windows)" -Type Success
        }
        elseif ($envContent -match 'REDIS_CLIENT=phpredis') {
            if (Test-PHPRedisExtension) {
                Write-Status "✓ Redis client set to phpredis (extension available)" -Type Success
            }
            else {
                Write-Status "✗ Redis client set to phpredis but extension not available" -Type Error
                $issues += "Redis client mismatch"
                $recommendations += "Change REDIS_CLIENT to 'predis' in .env file"
            }
        }
        else {
            Write-Status "⚠ Redis client not explicitly set (using default)" -Type Warning
            $recommendations += "Set REDIS_CLIENT=predis in .env file for better compatibility"
        }
        
        # Check Redis host
        if ($envContent -match 'REDIS_HOST=127\.0\.0\.1') {
            Write-Status "✓ Redis host correctly set to 127.0.0.1" -Type Success
        }
        else {
            Write-Status "⚠ Redis host not set to 127.0.0.1" -Type Warning
            $recommendations += "Set REDIS_HOST=127.0.0.1 in .env file"
        }
        
        # Check cache driver
        if ($envContent -match 'CACHE_STORE=redis') {
            Write-Status "✓ Cache driver set to Redis" -Type Success
        }
        else {
            Write-Status "⚠ Cache driver not set to Redis" -Type Warning
            $recommendations += "Set CACHE_STORE=redis in .env file for better performance"
        }
        
        # Check session driver
        if ($envContent -match 'SESSION_DRIVER=redis') {
            Write-Status "✓ Session driver set to Redis" -Type Success
        }
        else {
            Write-Status "⚠ Session driver not set to Redis" -Type Warning
            $recommendations += "Set SESSION_DRIVER=redis in .env file for better performance"
        }
        
        # Check queue connection
        if ($envContent -match 'QUEUE_CONNECTION=redis') {
            Write-Status "✓ Queue connection set to Redis" -Type Success
        }
        else {
            Write-Status "⚠ Queue connection not set to Redis" -Type Warning
            $recommendations += "Set QUEUE_CONNECTION=redis in .env file for background jobs"
        }
    }
    else {
        Write-Status "✗ .env file not found" -Type Error
        $issues += ".env file missing"
        $recommendations += "Create .env file from .env.example"
    }
    
    return @{
        Issues = $issues
        Recommendations = $recommendations
    }
}

function Test-RedisPerformance {
    Write-Status "Testing Redis performance..." -Type Info
    
    $issues = @()
    $recommendations = @()
    
    if (Test-RedisConnection) {
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
                    $issues += "Slow Redis response time"
                    $recommendations += "Check Redis configuration and system resources"
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
    
    return @{
        Issues = $issues
        Recommendations = $recommendations
    }
}

#endregion

#region Fix Functions

function Fix-RedisConfiguration {
    if (-not $Fix) {
        return
    }
    
    Write-Status "Attempting to fix Redis configuration issues..." -Type Info
    
    # Update .env file with optimal Redis settings
    $envFile = Join-Path $script:ProjectRoot '.env'
    if (Test-Path $envFile) {
        $envContent = Get-Content $envFile -Raw
        $modified = $false
        
        # Set Redis client to predis for better Windows compatibility
        if ($envContent -notmatch 'REDIS_CLIENT=predis') {
            if ($envContent -match 'REDIS_CLIENT=.*') {
                $envContent = $envContent -replace 'REDIS_CLIENT=.*', 'REDIS_CLIENT=predis'
            }
            else {
                $envContent += "`nREDIS_CLIENT=predis"
            }
            $modified = $true
            Write-Status "✓ Set Redis client to predis" -Type Success
        }
        
        # Ensure Redis host is set correctly
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
        
        # Set optimal cache driver
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
        
        # Set optimal session driver
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
        
        # Set optimal queue connection
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
    }
    
    # Install Predis if not available
    if (-not (Test-PredisPackage)) {
        Write-Status "Installing Predis package for optimal Laragon compatibility..." -Type Info
        try {
            Push-Location $script:ProjectRoot
            composer require predis/predis --no-interaction --prefer-dist
            if ($LASTEXITCODE -eq 0) {
                Write-Status "✓ Predis package installed successfully" -Type Success
                Write-Status "✓ Predis provides better Windows/Laragon compatibility than phpredis" -Type Info
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
}

function Start-RedisService {
    if (-not $Fix) {
        return
    }
    
    Write-Status "Attempting to start Redis service..." -Type Info
    
    # Try to find and start Redis
    $redisExe = Get-ChildItem -Path (Join-Path $LaragonPath 'bin') -Filter 'redis-server.exe' -Recurse -ErrorAction SilentlyContinue | Select-Object -First 1
    
    if ($redisExe) {
        try {
            # Check if Redis is already running
            if (-not (Test-RedisConnection)) {
                Write-Status "Starting Redis server..." -Type Info
                Start-Process -FilePath $redisExe.FullName -WindowStyle Hidden
                Start-Sleep -Seconds 3
                
                if (Test-RedisConnection) {
                    Write-Status "✓ Redis server started successfully" -Type Success
                }
                else {
                    Write-Status "✗ Redis server failed to start" -Type Error
                }
            }
            else {
                Write-Status "✓ Redis server is already running" -Type Success
            }
        }
        catch {
            Write-Status "✗ Error starting Redis: $($_.Exception.Message)" -Type Error
        }
    }
    else {
        Write-Status "✗ Redis executable not found in Laragon installation" -Type Error
        Write-Status "Please install Redis through Laragon's Quick Add feature" -Type Info
    }
}

#endregion

#region Main Execution

try {
    Write-Host "`n🔍 ICTServe Redis Health Check" -ForegroundColor Cyan
    Write-Host "=" * 35 -ForegroundColor Cyan
    Write-Host ""

    Push-Location $script:ProjectRoot

    # Run health checks
    $allIssues = @()
    $allRecommendations = @()

    $serviceCheck = Test-RedisService
    $allIssues += $serviceCheck.Issues
    $allRecommendations += $serviceCheck.Recommendations

    $phpCheck = Test-PHPRedisConfiguration
    $allIssues += $phpCheck.Issues
    $allRecommendations += $phpCheck.Recommendations

    $laravelCheck = Test-LaravelRedisConfiguration
    $allIssues += $laravelCheck.Issues
    $allRecommendations += $laravelCheck.Recommendations

    $performanceCheck = Test-RedisPerformance
    $allIssues += $performanceCheck.Issues
    $allRecommendations += $performanceCheck.Recommendations

    # Apply fixes if requested
    if ($Fix) {
        Write-Host "`n🔧 Applying Fixes..." -ForegroundColor Yellow
        Write-Host "=" * 20 -ForegroundColor Yellow
        
        Start-RedisService
        Fix-RedisConfiguration
    }

    # Summary
    Write-Host "`n📊 Health Check Summary" -ForegroundColor Cyan
    Write-Host "=" * 25 -ForegroundColor Cyan
    
    if ($allIssues.Count -eq 0) {
        Write-Status "✅ All Redis health checks passed!" -Type Success
        Write-Status "Your Redis configuration is optimal for ICTServe" -Type Success
    }
    else {
        Write-Status "⚠️ Found $($allIssues.Count) issue(s) that need attention:" -Type Warning
        foreach ($issue in $allIssues) {
            Write-Host "  • $issue" -ForegroundColor Yellow
        }
    }

    if ($allRecommendations.Count -gt 0) {
        Write-Host "`n💡 Recommendations:" -ForegroundColor Cyan
        foreach ($recommendation in ($allRecommendations | Sort-Object -Unique)) {
            Write-Host "  • $recommendation" -ForegroundColor White
        }
    }

    Write-Host "`n🚀 Next Steps:" -ForegroundColor Cyan
    Write-Host "  1. Run this script with -Fix to automatically resolve issues" -ForegroundColor White
    Write-Host "  2. Run .\scripts\laragon\optimize-redis-laragon.ps1 for complete optimization" -ForegroundColor White
    Write-Host "  3. Restart Laragon services after making changes" -ForegroundColor White
    Write-Host "  4. Test your ICTServe application to verify Redis functionality" -ForegroundColor White
    Write-Host "  5. Use -Detailed flag for more comprehensive diagnostics" -ForegroundColor White
    Write-Host ""
    Write-Host "📚 Documentation:" -ForegroundColor Cyan
    Write-Host "  • Redis Setup Guide: docs\redis\LARAGON_REDIS_SETUP.md" -ForegroundColor White
    Write-Host "  • ICTServe Specs: .kiro\specs\ictserve-comprehensive-v3.6" -ForegroundColor White
    Write-Host ""

}
catch {
    Write-Host "`n❌ Health check failed: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
finally {
    Pop-Location
}

#endregion