# ICTServe Redis Data Export Script
# Purpose: Export current Redis data and configuration
# Requirements: 8.1, 8.2, 8.4

param(
    [string]$OutputDir = "storage/backups/redis",
    [string]$RedisHost = "127.0.0.1",
    [int]$RedisPort = 6379,
    [string]$RedisPassword = "",
    [switch]$Verbose
)

$ErrorActionPreference = "Continue"
$timestamp = Get-Date -Format "yyyy-MM-dd_HH-mm-ss"

# Create output directory
if (-not (Test-Path $OutputDir)) {
    New-Item -ItemType Directory -Path $OutputDir -Force | Out-Null
}

Write-Host "=== ICTServe Redis Data Export ===" -ForegroundColor Cyan
Write-Host "Timestamp: $timestamp" -ForegroundColor Gray
Write-Host ""

# Read Redis configuration from .env if not provided
if (Test-Path ".env") {
    $envContent = Get-Content ".env" -Raw
    
    if (-not $PSBoundParameters.ContainsKey('RedisHost')) {
        $envRedisHost = ($envContent | Select-String "REDIS_HOST=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
        if ($envRedisHost) { $RedisHost = $envRedisHost }
    }
    
    if (-not $PSBoundParameters.ContainsKey('RedisPort')) {
        $envRedisPort = ($envContent | Select-String "REDIS_PORT=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
        if ($envRedisPort) { $RedisPort = [int]$envRedisPort }
    }
    
    if (-not $PSBoundParameters.ContainsKey('RedisPassword')) {
        $envRedisPassword = ($envContent | Select-String "REDIS_PASSWORD=(.+)" | ForEach-Object { $_.Matches.Groups[1].Value })
        if ($envRedisPassword -and $envRedisPassword -ne "null") { $RedisPassword = $envRedisPassword }
    }
}

Write-Host "Redis Configuration:" -ForegroundColor Cyan
Write-Host "  Host: $RedisHost" -ForegroundColor Gray
Write-Host "  Port: $RedisPort" -ForegroundColor Gray
Write-Host "  Password: $(if ($RedisPassword) { '[SET]' } else { '[NONE]' })" -ForegroundColor Gray
Write-Host ""

# Define output files
$dataExportFile = "$OutputDir/redis_data_$timestamp.txt"
$configExportFile = "$OutputDir/redis_config_$timestamp.txt"
$infoExportFile = "$OutputDir/redis_info_$timestamp.txt"
$summaryFile = "$OutputDir/redis_export_summary_$timestamp.json"

# Function to execute Redis command
function Invoke-RedisCommand {
    param(
        [string]$Command,
        [string]$Description = ""
    )
    
    try {
        $redisCliArgs = @("-h", $RedisHost, "-p", $RedisPort)
        if ($RedisPassword) {
            $redisCliArgs += @("-a", $RedisPassword)
        }
        $redisCliArgs += @("--raw")
        
        if ($Description) {
            Write-Host "  $Description..." -ForegroundColor Yellow
        }
        
        $result = & redis-cli @redisCliArgs $Command.Split(' ') 2>&1
        
        if ($LASTEXITCODE -ne 0) {
            throw "Redis command failed: $result"
        }
        
        return $result
    } catch {
        Write-Warning "Redis command '$Command' failed: $($_.Exception.Message)"
        return $null
    }
}

# Test Redis connection
Write-Host "Testing Redis connection..." -ForegroundColor Yellow
$pingResult = Invoke-RedisCommand "PING" "Testing connection"

if ($pingResult -ne "PONG") {
    Write-Warning "Redis connection test failed. Checking alternative methods..."
    
    # Try WSL Redis if Windows Redis fails
    Write-Host "Attempting WSL Redis connection..." -ForegroundColor Yellow
    try {
        $wslPing = wsl redis-cli -h 127.0.0.1 -p $RedisPort ping 2>&1
        if ($wslPing -eq "PONG") {
            Write-Host "  ✓ WSL Redis connection successful" -ForegroundColor Green
            $useWSL = $true
        } else {
            throw "WSL Redis also failed"
        }
    } catch {
        Write-Error "Cannot connect to Redis server. Please ensure Redis is running."
        Write-Host ""
        Write-Host "Troubleshooting steps:" -ForegroundColor Yellow
        Write-Host "1. Check if Redis is running: redis-cli ping" -ForegroundColor Gray
        Write-Host "2. Check WSL Redis: wsl redis-cli ping" -ForegroundColor Gray
        Write-Host "3. Verify Redis configuration in .env file" -ForegroundColor Gray
        Write-Host "4. Check Redis service status" -ForegroundColor Gray
        exit 1
    }
} else {
    Write-Host "  ✓ Redis connection successful" -ForegroundColor Green
    $useWSL = $false
}

# Function to execute Redis command with WSL support
function Invoke-RedisCommandWithWSL {
    param(
        [string]$Command,
        [string]$Description = ""
    )
    
    if ($Description) {
        Write-Host "  $Description..." -ForegroundColor Yellow
    }
    
    try {
        if ($useWSL) {
            $result = wsl redis-cli -h $RedisHost -p $RedisPort --raw $Command.Split(' ') 2>&1
        } else {
            $result = Invoke-RedisCommand $Command
        }
        
        return $result
    } catch {
        Write-Warning "Redis command '$Command' failed: $($_.Exception.Message)"
        return $null
    }
}

# Get Redis info
Write-Host "Gathering Redis information..." -ForegroundColor Cyan
$redisInfo = Invoke-RedisCommandWithWSL "INFO" "Getting Redis server info"

if ($redisInfo) {
    $redisInfo | Out-File -FilePath $infoExportFile -Encoding UTF8
    Write-Host "  ✓ Redis info exported" -ForegroundColor Green
}

# Get Redis configuration
Write-Host "Exporting Redis configuration..." -ForegroundColor Cyan
$redisConfig = Invoke-RedisCommandWithWSL "CONFIG GET *" "Getting Redis configuration"

if ($redisConfig) {
    # Format configuration output
    $configFormatted = @"
# Redis Configuration Export
# Generated: $timestamp
# Host: $RedisHost:$RedisPort

$redisConfig
"@
    $configFormatted | Out-File -FilePath $configExportFile -Encoding UTF8
    Write-Host "  ✓ Redis configuration exported" -ForegroundColor Green
}

# Get all Redis keys and their data
Write-Host "Exporting Redis data..." -ForegroundColor Cyan
$allKeys = Invoke-RedisCommandWithWSL "KEYS *" "Getting all Redis keys"

$exportedData = @"
# Redis Data Export
# Generated: $timestamp
# Host: $RedisHost:$RedisPort

"@

$keyCount = 0
$totalSize = 0

if ($allKeys -and $allKeys.Count -gt 0) {
    Write-Host "  Found $($allKeys.Count) keys to export..." -ForegroundColor Yellow
    
    foreach ($key in $allKeys) {
        if ([string]::IsNullOrWhiteSpace($key)) { continue }
        
        $keyCount++
        
        # Get key type
        $keyType = Invoke-RedisCommandWithWSL "TYPE $key"
        
        # Get TTL
        $ttl = Invoke-RedisCommandWithWSL "TTL $key"
        
        $exportedData += "`n# Key: $key (Type: $keyType, TTL: $ttl)`n"
        
        # Export based on key type
        switch ($keyType) {
            "string" {
                $value = Invoke-RedisCommandWithWSL "GET `"$key`""
                $exportedData += "SET `"$key`" `"$value`"`n"
                if ($ttl -gt 0) {
                    $exportedData += "EXPIRE `"$key`" $ttl`n"
                }
            }
            "hash" {
                $hashData = Invoke-RedisCommandWithWSL "HGETALL `"$key`""
                if ($hashData) {
                    for ($i = 0; $i -lt $hashData.Count; $i += 2) {
                        $field = $hashData[$i]
                        $value = $hashData[$i + 1]
                        $exportedData += "HSET `"$key`" `"$field`" `"$value`"`n"
                    }
                    if ($ttl -gt 0) {
                        $exportedData += "EXPIRE `"$key`" $ttl`n"
                    }
                }
            }
            "list" {
                $listData = Invoke-RedisCommandWithWSL "LRANGE `"$key`" 0 -1"
                if ($listData) {
                    foreach ($item in $listData) {
                        $exportedData += "RPUSH `"$key`" `"$item`"`n"
                    }
                    if ($ttl -gt 0) {
                        $exportedData += "EXPIRE `"$key`" $ttl`n"
                    }
                }
            }
            "set" {
                $setData = Invoke-RedisCommandWithWSL "SMEMBERS `"$key`""
                if ($setData) {
                    foreach ($member in $setData) {
                        $exportedData += "SADD `"$key`" `"$member`"`n"
                    }
                    if ($ttl -gt 0) {
                        $exportedData += "EXPIRE `"$key`" $ttl`n"
                    }
                }
            }
            "zset" {
                $zsetData = Invoke-RedisCommandWithWSL "ZRANGE `"$key`" 0 -1 WITHSCORES"
                if ($zsetData) {
                    for ($i = 0; $i -lt $zsetData.Count; $i += 2) {
                        $member = $zsetData[$i]
                        $score = $zsetData[$i + 1]
                        $exportedData += "ZADD `"$key`" $score `"$member`"`n"
                    }
                    if ($ttl -gt 0) {
                        $exportedData += "EXPIRE `"$key`" $ttl`n"
                    }
                }
            }
            default {
                $exportedData += "# Unsupported key type: $keyType`n"
            }
        }
        
        if ($Verbose -and ($keyCount % 100 -eq 0)) {
            Write-Host "    Processed $keyCount keys..." -ForegroundColor Gray
        }
    }
    
    Write-Host "  ✓ Exported $keyCount keys" -ForegroundColor Green
} else {
    Write-Host "  ℹ No keys found in Redis database" -ForegroundColor Yellow
    $exportedData += "`n# No keys found in Redis database`n"
}

# Save exported data
$exportedData | Out-File -FilePath $dataExportFile -Encoding UTF8

# Create summary
$summary = @{
    timestamp = $timestamp
    redis_host = $RedisHost
    redis_port = $RedisPort
    connection_method = if ($useWSL) { "WSL" } else { "Windows" }
    total_keys = $keyCount
    files = @{
        data_export = Split-Path $dataExportFile -Leaf
        config_export = Split-Path $configExportFile -Leaf
        info_export = Split-Path $infoExportFile -Leaf
    }
    export_size_mb = [math]::Round((Get-Item $dataExportFile -ErrorAction SilentlyContinue).Length / 1MB, 2)
} | ConvertTo-Json -Depth 3

$summary | Out-File -FilePath $summaryFile -Encoding UTF8

# Create Redis restore script
$restoreScript = @"
# Redis Data Restore Script
# Generated: $timestamp

param(
    [string]`$RedisHost = "$RedisHost",
    [int]`$RedisPort = $RedisPort,
    [string]`$RedisPassword = "$RedisPassword",
    [string]`$DataFile = "$dataExportFile"
)

Write-Host "=== Redis Data Restore ===" -ForegroundColor Cyan
Write-Host "Restoring from: `$DataFile" -ForegroundColor Gray
Write-Host "Target Redis: `$RedisHost:`$RedisPort" -ForegroundColor Gray
Write-Host ""

if (-not (Test-Path `$DataFile)) {
    Write-Error "Data file not found: `$DataFile"
    exit 1
}

# Test connection
try {
    `$pingResult = redis-cli -h `$RedisHost -p `$RedisPort $(if ($RedisPassword) { "-a `$RedisPassword" }) ping
    if (`$pingResult -ne "PONG") {
        throw "Connection test failed"
    }
    Write-Host "✓ Redis connection successful" -ForegroundColor Green
} catch {
    Write-Error "Cannot connect to Redis: `$(`$_.Exception.Message)"
    exit 1
}

# Restore data
Write-Host "Restoring Redis data..." -ForegroundColor Yellow
try {
    `$commands = Get-Content `$DataFile | Where-Object { `$_ -notmatch "^#" -and `$_.Trim() -ne "" }
    `$commandCount = 0
    
    foreach (`$command in `$commands) {
        redis-cli -h `$RedisHost -p `$RedisPort $(if ($RedisPassword) { "-a `$RedisPassword" }) `$command.Split(' ') | Out-Null
        `$commandCount++
        
        if (`$commandCount % 100 -eq 0) {
            Write-Host "  Processed `$commandCount commands..." -ForegroundColor Gray
        }
    }
    
    Write-Host "✓ Restored `$commandCount Redis commands" -ForegroundColor Green
} catch {
    Write-Error "Restore failed: `$(`$_.Exception.Message)"
    exit 1
}

Write-Host ""
Write-Host "Redis data restore completed successfully!" -ForegroundColor Green
"@

$restoreScript | Out-File -FilePath "$OutputDir/restore_redis_$timestamp.ps1" -Encoding UTF8

# Display summary
Write-Host ""
Write-Host "=== Redis Export Summary ===" -ForegroundColor Cyan
Write-Host "Redis Server: $RedisHost:$RedisPort" -ForegroundColor Gray
Write-Host "Connection Method: $(if ($useWSL) { 'WSL' } else { 'Windows' })" -ForegroundColor Gray
Write-Host "Keys Exported: $keyCount" -ForegroundColor Gray
Write-Host "Export Directory: $OutputDir" -ForegroundColor Gray
Write-Host ""
Write-Host "Files Created:" -ForegroundColor Gray
Write-Host "  - Data export: $(Split-Path $dataExportFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Configuration: $(Split-Path $configExportFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Server info: $(Split-Path $infoExportFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Summary: $(Split-Path $summaryFile -Leaf)" -ForegroundColor Gray
Write-Host "  - Restore script: restore_redis_$timestamp.ps1" -ForegroundColor Gray
Write-Host ""
Write-Host "✓ Redis export completed successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "To restore Redis data, run:" -ForegroundColor Yellow
Write-Host "  .\$OutputDir\restore_redis_$timestamp.ps1" -ForegroundColor Cyan