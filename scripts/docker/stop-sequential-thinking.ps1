#!/usr/bin/env pwsh
# Stop Sequential Thinking MCP Server

param(
    [switch]$RemoveContainer
)

Write-Host "Stopping Sequential Thinking MCP Server..." -ForegroundColor Yellow

# Stop the service
docker compose -f docker/mcp-sequential-thinking.yml down

if ($RemoveContainer) {
    Write-Host "Removing container..." -ForegroundColor Yellow
    docker rmi mcp/sequentialthinking 2>$null
}

Write-Host "Sequential Thinking MCP Server stopped!" -ForegroundColor Green