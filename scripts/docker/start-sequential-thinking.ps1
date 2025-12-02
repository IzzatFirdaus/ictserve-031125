#!/usr/bin/env pwsh
# Start Sequential Thinking MCP Server

Write-Host "Starting Sequential Thinking MCP Server..." -ForegroundColor Green

# Ensure network exists
docker network create ictserve_network 2>$null

# Start the service
docker compose -f docker/mcp-sequential-thinking.yml up -d

# Check status
$status = docker ps --filter "name=ictserve_sequential_thinking" --format "table {{.Names}}\t{{.Status}}"
Write-Host $status

Write-Host "Sequential Thinking MCP Server started successfully!" -ForegroundColor Green
Write-Host "Use 'docker logs ictserve_sequential_thinking' to view logs" -ForegroundColor Yellow