#!/usr/bin/env pwsh
# Start Mimir services for ICTServe

Write-Host "🚀 Starting Mimir services..." -ForegroundColor Cyan

# Start Neo4j and Copilot API first
Write-Host "📊 Starting Neo4j database..." -ForegroundColor Yellow
docker compose up -d neo4j

Write-Host "🤖 Starting Copilot API..." -ForegroundColor Yellow
docker compose up -d copilot-api

# Wait for services to be healthy
Write-Host "⏳ Waiting for services to be ready..." -ForegroundColor Yellow
Start-Sleep -Seconds 10

# Start Mimir server
Write-Host "🧠 Starting Mimir server..." -ForegroundColor Yellow
docker compose up -d mimir-server

# Wait for Mimir to be ready
Write-Host "⏳ Waiting for Mimir to initialize..." -ForegroundColor Yellow
Start-Sleep -Seconds 5

# Check health
Write-Host "`n✅ Checking service health..." -ForegroundColor Green

$neo4jHealth = docker compose ps neo4j --format json | ConvertFrom-Json | Select-Object -ExpandProperty Health
$copilotHealth = docker compose ps copilot-api --format json | ConvertFrom-Json | Select-Object -ExpandProperty Health
$mimirHealth = docker compose ps mimir-server --format json | ConvertFrom-Json | Select-Object -ExpandProperty Health

Write-Host "Neo4j: $neo4jHealth" -ForegroundColor $(if ($neo4jHealth -eq "healthy") { "Green" } else { "Red" })
Write-Host "Copilot API: $copilotHealth" -ForegroundColor $(if ($copilotHealth -eq "healthy") { "Green" } else { "Red" })
Write-Host "Mimir: $mimirHealth" -ForegroundColor $(if ($mimirHealth -eq "healthy") { "Green" } else { "Red" })

Write-Host "`n🎯 Mimir URLs:" -ForegroundColor Cyan
Write-Host "  Portal:  http://localhost:9042/portal" -ForegroundColor White
Write-Host "  Health:  http://localhost:9042/health" -ForegroundColor White
Write-Host "  Neo4j:   http://localhost:7474" -ForegroundColor White
Write-Host "           (user: neo4j, pass: MxXhTKH3qntipYLa1e0QOluJ)" -ForegroundColor Gray

Write-Host "`n✨ Done!" -ForegroundColor Green
