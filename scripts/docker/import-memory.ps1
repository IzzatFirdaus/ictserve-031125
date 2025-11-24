#!/usr/bin/env pwsh
# Import memory.jsonl into MCP Memory Server
# Usage: ./scripts/docker/import-memory.ps1

$ErrorActionPreference = 'Stop'

Write-Host "Importing memory data into MCP Memory Server..." -ForegroundColor Cyan

# Check if memory container is running
$containerStatus = docker compose ps memory --format json | ConvertFrom-Json
if (-not $containerStatus -or $containerStatus.State -ne "running") {
    Write-Host "Starting MCP Memory Server..." -ForegroundColor Yellow
    docker compose up -d memory
    Start-Sleep -Seconds 5
}

# Copy memory.jsonl to container
Write-Host "Copying memory.jsonl to container..." -ForegroundColor Yellow
docker compose cp storage/mcp/memory.jsonl memory:/app/dist/memory.jsonl

Write-Host "✓ Memory data imported successfully" -ForegroundColor Green
Write-Host "Memory file location: /app/dist/memory.jsonl" -ForegroundColor Yellow

# Verify import
Write-Host "`nVerifying import..." -ForegroundColor Cyan
docker compose exec memory sh -c "ls -lh /app/dist/memory.jsonl"

Write-Host "`n✓ Import complete!" -ForegroundColor Green
Write-Host "Use MCP tools to query the knowledge graph" -ForegroundColor Yellow
