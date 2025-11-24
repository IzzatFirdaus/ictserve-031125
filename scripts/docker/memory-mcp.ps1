#!/usr/bin/env pwsh
# MCP Servers Helper Script
# Usage: ./scripts/docker/mcp-servers.ps1 [start|stop|status|logs] [memory|sequential-thinking|all]

param(
    [Parameter(Position=0)]
    [ValidateSet('start', 'stop', 'status', 'shell', 'logs')]
    [string]$Action = 'status'
)

$ErrorActionPreference = 'Stop'

function Start-MemoryServer {
    Write-Host "Starting MCP Memory Server..." -ForegroundColor Cyan
    docker compose up -d memory
    Write-Host "✓ MCP Memory Server started" -ForegroundColor Green
    Write-Host "Memory data stored in: memory-data volume" -ForegroundColor Yellow
}

function Stop-MemoryServer {
    Write-Host "Stopping MCP Memory Server..." -ForegroundColor Cyan
    docker compose stop memory
    Write-Host "✓ MCP Memory Server stopped" -ForegroundColor Green
}

function Show-Status {
    Write-Host "MCP Memory Server Status:" -ForegroundColor Cyan
    docker compose ps memory
    Write-Host "`nMemory Volume:" -ForegroundColor Cyan
    docker volume inspect ictserve-031125_memory-data --format '{{.Mountpoint}}'
}

function Open-Shell {
    Write-Host "Opening shell in MCP Memory container..." -ForegroundColor Cyan
    docker compose exec memory sh
}

function Show-Logs {
    Write-Host "MCP Memory Server Logs:" -ForegroundColor Cyan
    docker compose logs -f memory
}

switch ($Action) {
    'start'  { Start-MemoryServer }
    'stop'   { Stop-MemoryServer }
    'status' { Show-Status }
    'shell'  { Open-Shell }
    'logs'   { Show-Logs }
}
