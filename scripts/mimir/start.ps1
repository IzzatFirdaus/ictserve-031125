#!/usr/bin/env pwsh
# Start Mimir services only

Write-Host "🚀 Starting Mimir services..." -ForegroundColor Cyan

# Start Mimir services
docker compose up -d neo4j copilot-api mimir-server

Write-Host ""
Write-Host "✅ Mimir services started!" -ForegroundColor Green
Write-Host ""
Write-Host "📍 Access points:" -ForegroundColor Yellow
Write-Host "   • Mimir Portal: http://localhost:9042/portal" -ForegroundColor White
Write-Host "   • Neo4j Browser: http://localhost:7474 (user: neo4j, pass: MxXhTKH3qntipYLa1e0QOluJ)" -ForegroundColor White
Write-Host "   • Health Check: http://localhost:9042/health" -ForegroundColor White
Write-Host ""
Write-Host "💡 Tip: Run './scripts/mimir/status.ps1' to check service status" -ForegroundColor Gray
