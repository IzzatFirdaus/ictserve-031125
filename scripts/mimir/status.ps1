#!/usr/bin/env pwsh
# Check Mimir services status

Write-Host "📊 Mimir Services Status" -ForegroundColor Cyan
Write-Host "========================" -ForegroundColor Cyan
Write-Host ""

# Check container status
docker compose ps neo4j copilot-api mimir-server

Write-Host ""
Write-Host "🔍 Health Checks:" -ForegroundColor Yellow
Write-Host ""

# Check Mimir health
Write-Host "Mimir Server: " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "http://localhost:9042/health" -TimeoutSec 2 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Healthy" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Unhealthy (Status: $($response.StatusCode))" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Not responding" -ForegroundColor Red
}

# Check Neo4j
Write-Host "Neo4j:        " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "http://localhost:7474" -TimeoutSec 2 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Healthy" -ForegroundColor Green
    } else {
        Write-Host "⚠️  Unhealthy (Status: $($response.StatusCode))" -ForegroundColor Yellow
    }
} catch {
    Write-Host "❌ Not responding" -ForegroundColor Red
}

# Check Copilot API
Write-Host "Copilot API:  " -NoNewline
try {
    $response = Invoke-WebRequest -Uri "http://localhost:4141/" -TimeoutSec 2 -UseBasicParsing
    Write-Host "✅ Healthy" -ForegroundColor Green
} catch {
    Write-Host "❌ Not responding" -ForegroundColor Red
}

Write-Host ""
Write-Host "📍 Access URLs:" -ForegroundColor Yellow
Write-Host "   • Mimir Portal: http://localhost:9042/portal" -ForegroundColor White
Write-Host "   • Neo4j Browser: http://localhost:7474" -ForegroundColor White
Write-Host "   • Health Check: http://localhost:9042/health" -ForegroundColor White
