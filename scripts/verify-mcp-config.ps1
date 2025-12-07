#!/usr/bin/env pwsh
# Verify MCP Server Configuration for Codex
# Checks config.toml and server availability

$ErrorActionPreference = "Continue"

Write-Host "`n=== Codex MCP Configuration Verification ===" -ForegroundColor Cyan
Write-Host ""

# Check config file
$configPath = "$env:USERPROFILE\.codex\config.toml"
Write-Host "1. Checking config file..." -ForegroundColor Yellow

if (Test-Path $configPath) {
    Write-Host "   ✓ Config found: $configPath" -ForegroundColor Green

    # Parse enabled servers
    $content = Get-Content $configPath -Raw
    $enabledServers = @()

    if ($content -match '\[mcp_servers\.memory\][^\[]*disabled = false') {
        $enabledServers += "memory (local)"
    }
    if ($content -match '\[mcp_servers\.sequentialthinking\][^\[]*disabled = false') {
        $enabledServers += "sequential-thinking (local)"
    }
    if ($content -match '\[mcp_servers\.chrome-devtools\][^\[]*disabled = false') {
        $enabledServers += "chrome-devtools (local)"
    }
    if ($content -match '\[mcp_servers\.memory-docker\][^\[]*disabled = false') {
        $enabledServers += "memory (docker)"
    }
    if ($content -match '\[mcp_servers\.sequential-thinking-docker\][^\[]*disabled = false') {
        $enabledServers += "sequential-thinking (docker)"
    }
    if ($content -match '\[mcp_servers\.chrome-devtools-docker\][^\[]*disabled = false') {
        $enabledServers += "chrome-devtools (docker)"
    }
    if ($content -match '\[mcp_servers\.laravel-boost\][^\[]*disabled = false') {
        $enabledServers += "laravel-boost"
    }

    Write-Host "`n   Enabled servers:" -ForegroundColor Cyan
    foreach ($server in $enabledServers) {
        Write-Host "   - $server" -ForegroundColor White
    }
} else {
    Write-Host "   ✗ Config not found!" -ForegroundColor Red
    Write-Host "   Expected: $configPath" -ForegroundColor Yellow
    exit 1
}

# Check Node.js
Write-Host "`n2. Checking Node.js..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version
    Write-Host "   ✓ Node.js installed: $nodeVersion" -ForegroundColor Green
} catch {
    Write-Host "   ✗ Node.js not found!" -ForegroundColor Red
    Write-Host "   Install from: https://nodejs.org/" -ForegroundColor Yellow
}

# Check npm packages (local mode)
if ($enabledServers -match "local") {
    Write-Host "`n3. Checking npm packages..." -ForegroundColor Yellow

    $packages = @(
        "@modelcontextprotocol/server-memory",
        "@modelcontextprotocol/server-sequential-thinking",
        "chrome-devtools-mcp"
    )

    foreach ($package in $packages) {
        $packagePath = "$env:APPDATA\npm\node_modules\$package"
        if (Test-Path $packagePath) {
            Write-Host "   ✓ $package installed" -ForegroundColor Green
        } else {
            Write-Host "   ✗ $package not found" -ForegroundColor Red
            Write-Host "     Install: npm install -g $package" -ForegroundColor Yellow
        }
    }
}

# Check Docker (docker mode)
if ($enabledServers -match "docker") {
    Write-Host "`n4. Checking Docker containers..." -ForegroundColor Yellow

    try {
        $containers = docker ps --filter "name=ictserve-mcp" --format "{{.Names}}" 2>$null

        if ($containers) {
            Write-Host "   ✓ Docker containers running:" -ForegroundColor Green
            foreach ($container in $containers) {
                $status = docker inspect --format='{{.State.Status}}' $container
                Write-Host "   - $container ($status)" -ForegroundColor White
            }
        } else {
            Write-Host "   ✗ No MCP containers running" -ForegroundColor Red
            Write-Host "   Start: docker compose up -d mcp-memory mcp-sequential-thinking mcp-chrome-devtools" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "   ✗ Docker not available" -ForegroundColor Red
        Write-Host "   Ensure Docker Desktop is running" -ForegroundColor Yellow
    }
}

# Check Laravel Boost
if ($enabledServers -contains "laravel-boost") {
    Write-Host "`n5. Checking Laravel Boost..." -ForegroundColor Yellow

    $artisanPath = "C:\XAMPP\htdocs\ictserve-031125\artisan"
    if (Test-Path $artisanPath) {
        Write-Host "   ✓ Laravel artisan found" -ForegroundColor Green

        try {
            $boostCheck = php $artisanPath list boost:mcp 2>&1
            if ($boostCheck -match 'boost:mcp') {
                Write-Host "   ✓ boost:mcp command available" -ForegroundColor Green
            } else {
                Write-Host "   ✗ boost:mcp command not found" -ForegroundColor Red
            }
        } catch {
            Write-Host "   ✗ Cannot execute artisan" -ForegroundColor Red
        }
    } else {
        Write-Host "   ✗ Laravel artisan not found" -ForegroundColor Red
    }
}

# Summary
Write-Host "`n=== Summary ===" -ForegroundColor Cyan
Write-Host "Configuration file: " -NoNewline
Write-Host "OK" -ForegroundColor Green

$mode = if ($enabledServers -match "docker") { "Docker" } else { "Local" }
Write-Host "Active mode: " -NoNewline
Write-Host $mode -ForegroundColor Cyan

Write-Host "`nNext steps:" -ForegroundColor Yellow
Write-Host "1. Restart VS Code to reload Codex extension" -ForegroundColor White
Write-Host "2. Check Codex output panel (View → Output → Codex)" -ForegroundColor White
Write-Host "3. Test MCP servers in Codex chat" -ForegroundColor White

Write-Host "`nUseful commands:" -ForegroundColor Yellow
Write-Host "  Switch mode: .\scripts\switch-mcp-mode.ps1 -Mode [local|docker]" -ForegroundColor White
Write-Host "  View logs: docker compose logs -f mcp-memory" -ForegroundColor White
Write-Host '  Test servers: .\scripts\test-mcp-servers.ps1' -ForegroundColor White

Write-Host ""
