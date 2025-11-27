#!/usr/bin/env pwsh
# Check Mimir services status

$ErrorActionPreference = "Stop"

Write-Host "📊 Mimir Services Status" -ForegroundColor Cyan
Write-Host ""

# Check Docker containers
$services = @("ictserve-neo4j", "ictserve-copilot-api", "ictserve-mimir")

foreach ($service in $services) {
    $running = docker ps --filter "name=$service" --format "{{.Names}}" 2>$null
    if ($running) {
        $status = docker inspect --format='{{.State.Health.Status}}' $service 2>$null
        if ($status) {
            Write-Host "✅ $service - $status" -ForegroundColor Green
        } else {
            Write-Host "✅ $service - running (no healthcheck)" -ForegroundColor Green
        }
    } else {
        Write-Host "❌ $service - not running" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "🔗 Quick Links:" -ForegroundColor Cyan
Write-Host "   • Mimir Portal:  http://localhost:9042/portal" -ForegroundColor White
Write-Host "   • Neo4j Browser: http://localhost:7474" -ForegroundColor White
Write-Host "   • Health Check:  http://localhost:9042/health" -ForegroundColor White
Write-Host ""
Write-Host "📋 View logs:" -ForegroundColor Cyan
Write-Host "   docker compose logs -f mimir-server" -ForegroundColor Gray
