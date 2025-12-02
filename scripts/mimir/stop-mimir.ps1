#!/usr/bin/env pwsh
# Stop Mimir services

$ErrorActionPreference = "Stop"

Write-Host "🛑 Stopping Mimir Memory System..." -ForegroundColor Cyan

# Navigate to project root
$projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
Set-Location $projectRoot

# Stop Mimir services
Write-Host "🐳 Stopping Neo4j, Copilot API, and Mimir Server..." -ForegroundColor Yellow
docker compose stop neo4j copilot-api mimir-server

Write-Host "✅ Mimir services stopped" -ForegroundColor Green
