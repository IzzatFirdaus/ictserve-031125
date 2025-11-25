#!/usr/bin/env pwsh
# Test MCP Server Connectivity
# Quick test to verify MCP servers respond correctly

$ErrorActionPreference = "Continue"

Write-Host "`n=== MCP Server Connectivity Test ===" -ForegroundColor Cyan
Write-Host ""

# Parse config to find enabled servers
$configPath = "$env:USERPROFILE\.codex\config.toml"
if (-not (Test-Path $configPath)) {
    Write-Host "✗ Config file not found: $configPath" -ForegroundColor Red
    exit 1
}

$content = Get-Content $configPath -Raw
$enabledServers = @()

# Detect enabled servers
if ($content -match '\[mcp_servers\.memory\][^\[]*disabled = false') {
    $enabledServers += @{Name="memory"; Type="local"; Container=""}
}
if ($content -match '\[mcp_servers\.memory-docker\][^\[]*disabled = false') {
    $enabledServers += @{Name="memory"; Type="docker"; Container="ictserve-mcp-memory"}
}
if ($content -match '\[mcp_servers\.sequentialthinking\][^\[]*disabled = false') {
    $enabledServers += @{Name="sequential-thinking"; Type="local"; Container=""}
}
if ($content -match '\[mcp_servers\.sequential-thinking-docker\][^\[]*disabled = false') {
    $enabledServers += @{Name="sequential-thinking"; Type="docker"; Container="ictserve-mcp-sequential-thinking"}
}

Write-Host "Testing $($enabledServers.Count) enabled servers..." -ForegroundColor Yellow
Write-Host ""

foreach ($server in $enabledServers) {
    $name = $server.Name
    $type = $server.Type
    
    Write-Host "Testing $name ($type)..." -NoNewline
    
    if ($type -eq "docker") {
        $container = $server.Container
        try {
            $status = docker inspect --format='{{.State.Status}}' $container 2>$null
            if ($status -eq "running") {
                Write-Host " ✓" -ForegroundColor Green
            } else {
                Write-Host " ✗ (container not running)" -ForegroundColor Red
            }
        } catch {
            Write-Host " ✗ (container not found)" -ForegroundColor Red
        }
    } else {
        # Local servers - check if npm package exists
        $packageMap = @{
            "memory" = "@modelcontextprotocol/server-memory"
            "sequential-thinking" = "@modelcontextprotocol/server-sequential-thinking"
            "chrome-devtools" = "chrome-devtools-mcp"
        }
        
        $package = $packageMap[$name]
        $packagePath = "$env:APPDATA\npm\node_modules\$package"
        
        if (Test-Path $packagePath) {
            Write-Host " ✓" -ForegroundColor Green
        } else {
            Write-Host " ✗ (package not installed)" -ForegroundColor Red
        }
    }
}

# Test Mimir if enabled
if ($content -match '\[mcp_servers\.mimir\][^\[]*disabled = false') {
    Write-Host "`nTesting Mimir server..." -NoNewline
    try {
        $response = Invoke-WebRequest -Uri "http://localhost:9042/health" -TimeoutSec 2 -ErrorAction Stop
        Write-Host " ✓" -ForegroundColor Green
    } catch {
        Write-Host " ✗ (not responding)" -ForegroundColor Red
    }
}

# Test Laravel Boost if enabled
if ($content -match '\[mcp_servers\.laravel-boost\][^\[]*disabled = false') {
    Write-Host "Testing Laravel Boost..." -NoNewline
    $artisanPath = "C:\XAMPP\htdocs\ictserve-031125\artisan"
    if (Test-Path $artisanPath) {
        Write-Host " ✓" -ForegroundColor Green
    } else {
        Write-Host " ✗ (artisan not found)" -ForegroundColor Red
    }
}

Write-Host "`n=== Test Complete ===" -ForegroundColor Cyan
Write-Host ""
