#!/usr/bin/env pwsh
# Switch MCP Server Mode (Local vs Docker)
# Usage: .\scripts\switch-mcp-mode.ps1 -Mode [local|docker]

param(
    [Parameter(Mandatory=$true)]
    [ValidateSet('local', 'docker')]
    [string]$Mode
)

$configPath = "$env:USERPROFILE\.codex\config.toml"

if (-not (Test-Path $configPath)) {
    Write-Error "Config file not found: $configPath"
    exit 1
}

Write-Host "Switching MCP servers to $Mode mode..." -ForegroundColor Cyan

$content = Get-Content $configPath -Raw

if ($Mode -eq 'docker') {
    # Disable local servers
    $content = $content -replace '(?m)^\[mcp_servers\.memory\][^\[]*disabled = false', '[mcp_servers.memory]$1disabled = true'
    $content = $content -replace '(?m)^\[mcp_servers\.sequentialthinking\][^\[]*disabled = false', '[mcp_servers.sequentialthinking]$1disabled = true'
    $content = $content -replace '(?m)^\[mcp_servers\.chrome-devtools\][^\[]*disabled = false', '[mcp_servers.chrome-devtools]$1disabled = true'
    
    # Enable Docker servers
    $content = $content -replace '(?m)^\[mcp_servers\.memory-docker\][^\[]*disabled = true', '[mcp_servers.memory-docker]$1disabled = false'
    $content = $content -replace '(?m)^\[mcp_servers\.sequential-thinking-docker\][^\[]*disabled = true', '[mcp_servers.sequential-thinking-docker]$1disabled = false'
    $content = $content -replace '(?m)^\[mcp_servers\.chrome-devtools-docker\][^\[]*disabled = true', '[mcp_servers.chrome-devtools-docker]$1disabled = false'
    
    Write-Host "✓ Enabled Docker-based MCP servers" -ForegroundColor Green
    Write-Host "  Starting Docker containers..." -ForegroundColor Yellow
    
    docker compose up -d mcp-memory mcp-sequential-thinking mcp-chrome-devtools
    
    Write-Host "`nVerifying containers..." -ForegroundColor Yellow
    docker ps --filter "name=ictserve-mcp" --format "table {{.Names}}\t{{.Status}}"
    
} else {
    # Enable local servers
    $content = $content -replace '(?m)^\[mcp_servers\.memory\][^\[]*disabled = true', '[mcp_servers.memory]$1disabled = false'
    $content = $content -replace '(?m)^\[mcp_servers\.sequentialthinking\][^\[]*disabled = true', '[mcp_servers.sequentialthinking]$1disabled = false'
    $content = $content -replace '(?m)^\[mcp_servers\.chrome-devtools\][^\[]*disabled = true', '[mcp_servers.chrome-devtools]$1disabled = false'
    
    # Disable Docker servers
    $content = $content -replace '(?m)^\[mcp_servers\.memory-docker\][^\[]*disabled = false', '[mcp_servers.memory-docker]$1disabled = true'
    $content = $content -replace '(?m)^\[mcp_servers\.sequential-thinking-docker\][^\[]*disabled = false', '[mcp_servers.sequential-thinking-docker]$1disabled = true'
    $content = $content -replace '(?m)^\[mcp_servers\.chrome-devtools-docker\][^\[]*disabled = false', '[mcp_servers.chrome-devtools-docker]$1disabled = true'
    
    Write-Host "✓ Enabled local npm-based MCP servers" -ForegroundColor Green
}

Set-Content $configPath $content -NoNewline

Write-Host "`n✓ MCP mode switched to: $Mode" -ForegroundColor Green
Write-Host "`nNext steps:" -ForegroundColor Cyan
Write-Host "  1. Restart Codex extension in VS Code" -ForegroundColor White
Write-Host "  2. Check MCP server status in Codex output panel" -ForegroundColor White

if ($Mode -eq 'docker') {
    Write-Host "`nDocker commands:" -ForegroundColor Cyan
    Write-Host "  View logs: docker compose logs -f mcp-memory" -ForegroundColor White
    Write-Host "  Stop servers: docker compose stop mcp-memory mcp-sequential-thinking mcp-chrome-devtools" -ForegroundColor White
}
