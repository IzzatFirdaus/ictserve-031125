#!/usr/bin/env pwsh
# Stop Mimir services for ICTServe

param(
    [switch]$RemoveVolumes
)

Write-Host "🛑 Stopping Mimir services..." -ForegroundColor Cyan

docker compose stop mimir-server copilot-api neo4j

if ($RemoveVolumes) {
    Write-Host "🗑️  Removing volumes (data will be deleted)..." -ForegroundColor Red
    docker compose down -v --remove-orphans
    Write-Host "✅ Volumes removed" -ForegroundColor Green
} else {
    Write-Host "💾 Data preserved in volumes" -ForegroundColor Yellow
    Write-Host "   Use -RemoveVolumes to delete data" -ForegroundColor Gray
}

Write-Host "✨ Done!" -ForegroundColor Green
