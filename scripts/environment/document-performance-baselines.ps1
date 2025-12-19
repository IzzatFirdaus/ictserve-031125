# ICTServe Performance Baseline Documentation Script
# Purpose: Document current performance baselines
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups/performance",
    [int]$TestDuration = 30,
    [switch]$Verbose
)

$ErrorActionPreference = "Continue"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

Write-Host "=== ICTServe Performance Baseline Documentation ===" -ForegroundColor Cyan
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host "Test Duration: $TestDuration seconds" -ForegroundColor Gray
Write-Host ""

$baselineFile = "$OutputDir/performance_baseline_$timestamp.json"
$reportFile = "$OutputDir/performance_report_$timestamp.md"

# Initialize baseline data structure
$baseline = @{
    timestamp = $timestamp
    test_duration_seconds = $TestDuration
    system_info = @{}
    database_metrics = @{}
    redis_metrics = @{}
    application_metrics = @{}
    web_server_metrics = @{}
    resource_usage = @{}
}

# Gather system information
Write-Host "Gathering system information..." -ForegroundColor Yellow
$baseline.system_info = @{
    os_version = [System.Environment]::OSVersion.VersionString
    processor_count = [System.Environment]::ProcessorCount
    total_memory_gb = [math]::Round((Get-CimInstance Win32_ComputerSystem).TotalPhysicalMemory / 1GB, 2)
    available_memory_gb = [math]::Round((Get-CimInstance Win32_OperatingSystem).FreePhysicalMemory / 1MB / 1024, 2)
    disk_free_space_gb = [math]::Round((Get-CimInstance Win32_LogicalDisk -Filter "DeviceID='C:'").FreeSpace / 1GB, 2)
}

# Test database performance
Write-Host "Testing database performance..." -ForegroundColor Yellow
try {
    # Simple database connection test
    $dbTestStart = Get-Date
    $dbTestResult = php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected';" 2>&1
    $dbTestEnd = Get-Date
    $dbConnectionTime = ($dbTestEnd - $dbTestStart).TotalMilliseconds
    
    # Get database size information
    $dbSizeQuery = @"
SELECT 
    table_schema as 'Database',
    ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) as 'Size_MB'
FROM information_schema.tables 
WHERE table_schema = DATABASE()
GROUP BY table_schema;
"@
    
    $baseline.database_metrics = @{
        connection_time_ms = [math]::Round($dbConnectionTime, 2)
        connection_status = if ($dbTestResult -match "Connected") { "Success" } else { "Failed" }
        test_timestamp = $dbTestStart.ToString("yyyy-MM-dd HH:mm:ss")
    }
    
    Write-Host "  ✓ Database connection: $([math]::Round($dbConnectionTime, 2))ms" -ForegroundColor Green
} catch {
    Write-Warning "Database performance test failed: $($_.Exception.Message)"
    $baseline.database_metrics = @{
        connection_status = "Failed"
        error = $_.Exception.Message
    }
}

# Test Redis performance
Write-Host "Testing Redis performance..." -ForegroundColor Yellow
try {
    $redisTestStart = Get-Date
    $redisPing = redis-cli ping 2>&1
    $redisTestEnd = Get-Date
    $redisResponseTime = ($redisTestEnd - $redisTestStart).TotalMilliseconds
    
    # Get Redis info
    $redisInfo = redis-cli info memory 2>&1
    $redisMemoryUsed = ($redisInfo | Select-String "used_memory_human:(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
    
    $baseline.redis_metrics = @{
        ping_response_time_ms = [math]::Round($redisResponseTime, 2)
        connection_status = if ($redisPing -eq "PONG") { "Success" } else { "Failed" }
        memory_used = $redisMemoryUsed
        test_timestamp = $redisTestStart.ToString("yyyy-MM-dd HH:mm:ss")
    }
    
    Write-Host "  ✓ Redis ping: $([math]::Round($redisResponseTime, 2))ms" -ForegroundColor Green
} catch {
    Write-Warning "Redis performance test failed: $($_.Exception.Message)"
    $baseline.redis_metrics = @{
        connection_status = "Failed"
        error = $_.Exception.Message
    }
}

# Test Laravel application performance
Write-Host "Testing Laravel application performance..." -ForegroundColor Yellow
try {
    # Test artisan command performance
    $artisanTestStart = Get-Date
    $artisanResult = php artisan route:list --json 2>&1
    $artisanTestEnd = Get-Date
    $artisanTime = ($artisanTestEnd - $artisanTestStart).TotalMilliseconds
    
    # Test basic HTTP response (if server is running)
    $httpResponseTime = $null
    try {
        $httpTestStart = Get-Date
        $httpResponse = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 5 -ErrorAction SilentlyContinue
        $httpTestEnd = Get-Date
        $httpResponseTime = ($httpTestEnd - $httpTestStart).TotalMilliseconds
    } catch {
        # Server might not be running
    }
    
    $baseline.application_metrics = @{
        artisan_route_list_time_ms = [math]::Round($artisanTime, 2)
        http_response_time_ms = if ($httpResponseTime) { [math]::Round($httpResponseTime, 2) } else { "N/A (server not running)" }
        laravel_version = (php artisan --version 2>&1)
        php_version = (php -v 2>&1 | Select-Object -First 1)
        test_timestamp = $artisanTestStart.ToString("yyyy-MM-dd HH:mm:ss")
    }
    
    Write-Host "  ✓ Artisan route:list: $([math]::Round($artisanTime, 2))ms" -ForegroundColor Green
    if ($httpResponseTime) {
        Write-Host "  ✓ HTTP response: $([math]::Round($httpResponseTime, 2))ms" -ForegroundColor Green
    } else {
        Write-Host "  ℹ HTTP test skipped (server not running)" -ForegroundColor Yellow
    }
} catch {
    Write-Warning "Laravel application test failed: $($_.Exception.Message)"
    $baseline.application_metrics = @{
        status = "Failed"
        error = $_.Exception.Message
    }
}

# Monitor resource usage over test duration
Write-Host "Monitoring resource usage for $TestDuration seconds..." -ForegroundColor Yellow
$resourceSamples = @()
$sampleInterval = 5 # seconds
$sampleCount = [math]::Floor($TestDuration / $sampleInterval)

for ($i = 0; $i -lt $sampleCount; $i++) {
    $sample = @{
        timestamp = (Get-Date).ToString("yyyy-MM-dd HH:mm:ss")
        cpu_usage_percent = (Get-CimInstance Win32_Processor | Measure-Object -Property LoadPercentage -Average).Average
        memory_usage_percent = [math]::Round(((Get-CimInstance Win32_OperatingSystem).TotalVisibleMemorySize - (Get-CimInstance Win32_OperatingSystem).FreePhysicalMemory) / (Get-CimInstance Win32_OperatingSystem).TotalVisibleMemorySize * 100, 2)
        disk_queue_length = (Get-Counter "\PhysicalDisk(_Total)\Current Disk Queue Length" -ErrorAction SilentlyContinue).CounterSamples.CookedValue
    }
    
    $resourceSamples += $sample
    
    if ($Verbose) {
        Write-Host "  Sample $($i + 1)/$sampleCount - CPU: $($sample.cpu_usage_percent)%, Memory: $($sample.memory_usage_percent)%" -ForegroundColor Gray
    }
    
    if ($i -lt ($sampleCount - 1)) {
        Start-Sleep -Seconds $sampleInterval
    }
}

# Calculate resource usage statistics
$baseline.resource_usage = @{
    cpu_usage_avg_percent = [math]::Round(($resourceSamples | Measure-Object -Property cpu_usage_percent -Average).Average, 2)
    cpu_usage_max_percent = ($resourceSamples | Measure-Object -Property cpu_usage_percent -Maximum).Maximum
    memory_usage_avg_percent = [math]::Round(($resourceSamples | Measure-Object -Property memory_usage_percent -Average).Average, 2)
    memory_usage_max_percent = ($resourceSamples | Measure-Object -Property memory_usage_percent -Maximum).Maximum
    sample_count = $resourceSamples.Count
    monitoring_duration_seconds = $TestDuration
}

Write-Host "  ✓ Resource monitoring completed" -ForegroundColor Green
Write-Host "    Average CPU: $($baseline.resource_usage.cpu_usage_avg_percent)%" -ForegroundColor Gray
Write-Host "    Average Memory: $($baseline.resource_usage.memory_usage_avg_percent)%" -ForegroundColor Gray

# Save baseline data
$baseline | ConvertTo-Json -Depth 4 | Out-File -FilePath $baselineFile -Encoding UTF8

# Generate performance report
$report = @"
# ICTServe Performance Baseline Report

**Generated**: $timestamp  
**Test Duration**: $TestDuration seconds  
**Purpose**: Pre-XAMPP migration performance documentation

---

## Executive Summary

This report documents the current ICTServe application performance before migrating to XAMPP environment. These baselines will be used to compare post-migration performance.

---

## System Information

- **Operating System**: $($baseline.system_info.os_version)
- **Processor Count**: $($baseline.system_info.processor_count)
- **Total Memory**: $($baseline.system_info.total_memory_gb) GB
- **Available Memory**: $($baseline.system_info.available_memory_gb) GB
- **Free Disk Space**: $($baseline.system_info.disk_free_space_gb) GB

---

## Database Performance

- **Connection Time**: $($baseline.database_metrics.connection_time_ms) ms
- **Connection Status**: $($baseline.database_metrics.connection_status)
- **Test Timestamp**: $($baseline.database_metrics.test_timestamp)

### Recommendations
- Target connection time: < 100ms for XAMPP MySQL
- Monitor for connection stability after migration

---

## Redis Performance

- **Ping Response Time**: $($baseline.redis_metrics.ping_response_time_ms) ms
- **Connection Status**: $($baseline.redis_metrics.connection_status)
- **Memory Used**: $($baseline.redis_metrics.memory_used)
- **Test Timestamp**: $($baseline.redis_metrics.test_timestamp)

### Recommendations
- Target ping response: < 5ms for WSL Redis
- Monitor memory usage patterns after migration

---

## Laravel Application Performance

- **Artisan Route List**: $($baseline.application_metrics.artisan_route_list_time_ms) ms
- **HTTP Response Time**: $($baseline.application_metrics.http_response_time_ms) ms
- **Laravel Version**: $($baseline.application_metrics.laravel_version)
- **PHP Version**: $($baseline.application_metrics.php_version)

### Recommendations
- Target artisan performance: < 2000ms
- Target HTTP response: < 500ms for basic pages

---

## Resource Usage (Average over $TestDuration seconds)

- **CPU Usage**: $($baseline.resource_usage.cpu_usage_avg_percent)% (Max: $($baseline.resource_usage.cpu_usage_max_percent)%)
- **Memory Usage**: $($baseline.resource_usage.memory_usage_avg_percent)% (Max: $($baseline.resource_usage.memory_usage_max_percent)%)
- **Sample Count**: $($baseline.resource_usage.sample_count)

### Recommendations
- Monitor for similar resource usage patterns after XAMPP migration
- Alert if CPU usage consistently exceeds current maximum
- Alert if memory usage increases significantly

---

## Performance Targets for XAMPP Environment

Based on current baselines, the following targets should be maintained or improved after migration:

| Metric | Current Baseline | XAMPP Target | Tolerance |
|--------|------------------|--------------|-----------|
| Database Connection | $($baseline.database_metrics.connection_time_ms)ms | < 100ms | ±20% |
| Redis Ping | $($baseline.redis_metrics.ping_response_time_ms)ms | < 5ms | ±50% |
| Artisan Commands | $($baseline.application_metrics.artisan_route_list_time_ms)ms | < 2000ms | ±30% |
| CPU Usage | $($baseline.resource_usage.cpu_usage_avg_percent)% | Similar | ±25% |
| Memory Usage | $($baseline.resource_usage.memory_usage_avg_percent)% | Similar | ±20% |

---

## Post-Migration Validation

After XAMPP migration, run the following validation:

1. **Performance Comparison**:
   ``````powershell
   .\scripts\environment\compare-performance.ps1 -BaselineFile "$baselineFile"
   ``````

2. **Load Testing**:
   - Test database operations under load
   - Verify Redis performance with WSL
   - Monitor resource usage patterns

3. **Application Testing**:
   - Run full test suite
   - Verify all Laravel services work correctly
   - Test real-world usage scenarios

---

## Files Generated

- **Baseline Data**: ``$(Split-Path $baselineFile -Leaf)``
- **Performance Report**: ``$(Split-Path $reportFile -Leaf)``

---

**Report Generated**: $(Get-Date -Format "yyyy-MM-dd HH:mm:ss")  
**Requirements**: 8.1, 8.2, 8.4
"@

$report | Out-File -FilePath $reportFile -Encoding UTF8

# Create performance comparison script for post-migration use
$comparisonScript = @"
# Performance Comparison Script
# Purpose: Compare current performance with pre-migration baseline

param(
    [string]`$BaselineFile = "$baselineFile",
    [int]`$TestDuration = 30
)

Write-Host "=== Performance Comparison ===" -ForegroundColor Cyan
Write-Host "Comparing with baseline: `$(Split-Path `$BaselineFile -Leaf)" -ForegroundColor Gray
Write-Host ""

if (-not (Test-Path `$BaselineFile)) {
    Write-Error "Baseline file not found: `$BaselineFile"
    exit 1
}

# Load baseline data
`$baseline = Get-Content `$BaselineFile | ConvertFrom-Json

Write-Host "Baseline from: `$(`$baseline.timestamp)" -ForegroundColor Yellow
Write-Host "Running current performance tests..." -ForegroundColor Yellow
Write-Host ""

# Run current tests (similar to baseline script)
# Database test
try {
    `$dbTestStart = Get-Date
    `$dbTestResult = php artisan tinker --execute="DB::connection()->getPdo(); echo 'Connected';" 2>&1
    `$dbTestEnd = Get-Date
    `$currentDbTime = (`$dbTestEnd - `$dbTestStart).TotalMilliseconds
    
    `$dbChange = `$currentDbTime - `$baseline.database_metrics.connection_time_ms
    `$dbChangePercent = [math]::Round((`$dbChange / `$baseline.database_metrics.connection_time_ms) * 100, 1)
    
    Write-Host "Database Connection:" -ForegroundColor Cyan
    Write-Host "  Baseline: `$(`$baseline.database_metrics.connection_time_ms)ms" -ForegroundColor Gray
    Write-Host "  Current:  `$([math]::Round(`$currentDbTime, 2))ms" -ForegroundColor Gray
    Write-Host "  Change:   `$([math]::Round(`$dbChange, 2))ms (`$dbChangePercent%)" -ForegroundColor $(if (`$dbChangePercent -le 20) { "Green" } elseif (`$dbChangePercent -le 50) { "Yellow" } else { "Red" })
} catch {
    Write-Host "Database Connection: Failed" -ForegroundColor Red
}

# Redis test
try {
    `$redisTestStart = Get-Date
    `$redisPing = redis-cli ping 2>&1
    `$redisTestEnd = Get-Date
    `$currentRedisTime = (`$redisTestEnd - `$redisTestStart).TotalMilliseconds
    
    `$redisChange = `$currentRedisTime - `$baseline.redis_metrics.ping_response_time_ms
    `$redisChangePercent = [math]::Round((`$redisChange / `$baseline.redis_metrics.ping_response_time_ms) * 100, 1)
    
    Write-Host "Redis Ping:" -ForegroundColor Cyan
    Write-Host "  Baseline: `$(`$baseline.redis_metrics.ping_response_time_ms)ms" -ForegroundColor Gray
    Write-Host "  Current:  `$([math]::Round(`$currentRedisTime, 2))ms" -ForegroundColor Gray
    Write-Host "  Change:   `$([math]::Round(`$redisChange, 2))ms (`$redisChangePercent%)" -ForegroundColor $(if (`$redisChangePercent -le 50) { "Green" } elseif (`$redisChangePercent -le 100) { "Yellow" } else { "Red" })
} catch {
    Write-Host "Redis Ping: Failed" -ForegroundColor Red
}

Write-Host ""
Write-Host "Performance comparison completed." -ForegroundColor Cyan
Write-Host ""
Write-Host "Interpretation:" -ForegroundColor Yellow
Write-Host "  Green: Performance within acceptable range" -ForegroundColor Green
Write-Host "  Yellow: Performance degraded but acceptable" -ForegroundColor Yellow
Write-Host "  Red: Significant performance degradation" -ForegroundColor Red
"@

$comparisonScript | Out-File -FilePath "$OutputDir/compare-performance.ps1" -Encoding UTF8

# Display summary
Write-Host ""
Write-Host "=== Performance Baseline Summary ===" -ForegroundColor Cyan
Write-Host "Test Duration: $TestDuration seconds" -ForegroundColor Gray
Write-Host "Samples Collected: $($resourceSamples.Count)" -ForegroundColor Gray
Write-Host ""
Write-Host "Key Metrics:" -ForegroundColor Gray
Write-Host "  Database Connection: $($baseline.database_metrics.connection_time_ms)ms" -ForegroundColor Gray
Write-Host "  Redis Ping: $($baseline.redis_metrics.ping_response_time_ms)ms" -ForegroundColor Gray
Write-Host "  Average CPU: $($baseline.resource_usage.cpu_usage_avg_percent)%" -ForegroundColor Gray
Write-Host "  Average Memory: $($baseline.resource_usage.memory_usage_avg_percent)%" -ForegroundColor Gray
Write-Host ""
Write-Host "Files Created:" -ForegroundColor Gray
Write-Host "  - Baseline data: $(Split-Path $baselineFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Performance report: $(Split-Path $reportFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Comparison script: compare-performance.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "✓ Performance baseline documentation completed!" -ForegroundColor Green
Write-Host ""
Write-Host "After XAMPP migration, run:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\compare-performance.ps1" -ForegroundColor Cyan