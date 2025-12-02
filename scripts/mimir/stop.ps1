#!/usr/bin/env pwsh
# Stop Mimir services only

param(
    [switch]$RemoveVolumes
)

Write-Host "🛑 Stopping Mimir services..." -ForegroundColor Cyan

if ($RemoveVolumes) {
    Write-Host "⚠️  Removing volumes (data will be deleted)..." -ForegroundColor Yellow
    docker compose stop neo4j copilot-api mimir-server
    docker compose rm -f neo4j copilot-api mimir-server
    docker volume rm ictserve-031125_neo4j-data ictserve-031125_neo4j-logs 2>$null
} else {
    docker compose stop neo4j copilot-api mimir-server
}

Write-Host ""
Write-Host "✅ Mimir services stopped!" -ForegroundColor Green
Write-Host ""
if (-not $RemoveVolumes) {
    Write-Host "💾 Data preserved in volumes" -ForegroundColor Gray
    Write-Host "💡 To remove data: ./scripts/mimir/stop.ps1 -RemoveVolumes" -ForegroundColor Gray
}
