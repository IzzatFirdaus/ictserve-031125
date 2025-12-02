#!/usr/bin/env pwsh
# Start Mimir services integrated with ICTServe

$ErrorActionPreference = "Stop"

Write-Host "🚀 Starting Mimir Memory System..." -ForegroundColor Cyan

# Check if Docker is running
try {
    docker info | Out-Null
} catch {
    Write-Host "❌ Docker is not running. Please start Docker Desktop." -ForegroundColor Red
    exit 1
}

# Navigate to project root
$projectRoot = Split-Path -Parent (Split-Path -Parent $PSScriptRoot)
Set-Location $projectRoot

Write-Host "📁 Project root: $projectRoot" -ForegroundColor Gray

# Start Mimir services
Write-Host "🐳 Starting Neo4j, Copilot API, and Mimir Server..." -ForegroundColor Yellow
docker compose up -d neo4j copilot-api mimir-server

# Wait for services to be healthy
Write-Host "⏳ Waiting for services to be healthy..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Check service status
$services = @("ictserve-neo4j", "ictserve-copilot-api", "ictserve-mimir")
$allHealthy = $true

foreach ($service in $services) {
    $status = docker inspect --format='{{.State.Health.Status}}' $service 2>$null
    if ($status -eq "healthy" -or $status -eq "") {
        Write-Host "✅ $service is running" -ForegroundColor Green
    } else {
        Write-Host "⚠️  $service status: $status" -ForegroundColor Yellow
        $allHealthy = $false
    }
}

Write-Host ""
Write-Host "🎉 Mimir services started!" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Access URLs:" -ForegroundColor Cyan
Write-Host "   • Mimir Portal:    http://localhost:9042/portal" -ForegroundColor White
Write-Host "   • Mimir API:       http://localhost:9042/mcp" -ForegroundColor White
Write-Host "   • Neo4j Browser:   http://localhost:7474" -ForegroundColor White
Write-Host "   • Health Check:    http://localhost:9042/health" -ForegroundColor White
Write-Host ""
Write-Host "🔑 Neo4j Credentials:" -ForegroundColor Cyan
Write-Host "   • Username: neo4j" -ForegroundColor White
Write-Host "   • Password: MxXhTKH3qntipYLa1e0QOluJ" -ForegroundColor White
Write-Host ""

if (-not $allHealthy) {
    Write-Host "⚠️  Some services may still be starting. Check logs with:" -ForegroundColor Yellow
    Write-Host "   docker compose logs -f mimir-server" -ForegroundColor Gray
}
