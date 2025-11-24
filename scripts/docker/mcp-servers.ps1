#!/usr/bin/env pwsh
# MCP Servers Helper Script
# Usage: ./scripts/docker/mcp-servers.ps1 [start|stop|status|logs] [memory|sequential-thinking|all]

param(
    [Parameter(Position=0)]
    [ValidateSet('start', 'stop', 'status', 'logs', 'restart')]
    [string]$Action = 'status',
    
    [Parameter(Position=1)]
    [ValidateSet('memory', 'sequential-thinking', 'all')]
    [string]$Server = 'all'
)

$ErrorActionPreference = 'Stop'

$services = if ($Server -eq 'all') { 
    @('memory', 'sequential-thinking') 
} else { 
    @($Server) 
}

function Start-Servers {
    Write-Host "Starting MCP Servers: $($services -join ', ')..." -ForegroundColor Cyan
    docker compose up -d @services
    Write-Host "✓ Servers started" -ForegroundColor Green
}

function Stop-Servers {
    Write-Host "Stopping MCP Servers: $($services -join ', ')..." -ForegroundColor Cyan
    docker compose stop @services
    Write-Host "✓ Servers stopped" -ForegroundColor Green
}

function Restart-Servers {
    Write-Host "Restarting MCP Servers: $($services -join ', ')..." -ForegroundColor Cyan
    docker compose restart @services
    Write-Host "✓ Servers restarted" -ForegroundColor Green
}

function Show-Status {
    Write-Host "MCP Servers Status:" -ForegroundColor Cyan
    docker compose ps @services
}

function Show-Logs {
    Write-Host "MCP Servers Logs:" -ForegroundColor Cyan
    docker compose logs -f @services
}

switch ($Action) {
    'start'   { Start-Servers }
    'stop'    { Stop-Servers }
    'restart' { Restart-Servers }
    'status'  { Show-Status }
    'logs'    { Show-Logs }
}
