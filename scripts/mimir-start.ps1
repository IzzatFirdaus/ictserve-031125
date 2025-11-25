# Start Mimir Services
# Based on official Mimir documentation

param(
    [switch]$Wait = $true,
    [int]$TimeoutSeconds = 120
)

Write-Host "=== Starting Mimir Services ===" -ForegroundColor Cyan

# Navigate to Mimir directory
$mimirPath = Join-Path $PSScriptRoot "..\Mimir"
if (-not (Test-Path $mimirPath)) {
    Write-Host "Error: Mimir directory not found at $mimirPath" -ForegroundColor Red
    exit 1
}

Set-Location $mimirPath

# Start services
Write-Host "`nStarting Docker services..." -ForegroundColor Yellow
docker compose up -d

if ($LASTEXITCODE -ne 0) {
    Write-Host "Error: Failed to start services" -ForegroundColor Red
    exit 1
}

if ($Wait) {
    Write-Host "`nWaiting for services to be ready..." -ForegroundColor Yellow
    
    # Wait for Neo4j (45-60 seconds typical)
    Write-Host "  Waiting for Neo4j (http://localhost:7474)..." -ForegroundColor Gray
    $start = Get-Date
    while ((Get-Date) - $start -lt [TimeSpan]::FromSeconds($TimeoutSeconds)) {
        try {
            $resp = Invoke-WebRequest -Uri "http://localhost:7474" -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop
            if ($resp.StatusCode -eq 200) {
                Write-Host "  ✓ Neo4j ready" -ForegroundColor Green
                break
            }
        } catch {
            Start-Sleep -Seconds 2
        }
    }
    
    # Wait for Mimir health endpoint
    Write-Host "  Waiting for Mimir (http://localhost:9042/health)..." -ForegroundColor Gray
    $start = Get-Date
    while ((Get-Date) - $start -lt [TimeSpan]::FromSeconds($TimeoutSeconds)) {
        try {
            $resp = Invoke-WebRequest -Uri "http://localhost:9042/health" -UseBasicParsing -TimeoutSec 5 -ErrorAction Stop
            if ($resp.StatusCode -eq 200) {
                Write-Host "  ✓ Mimir ready" -ForegroundColor Green
                break
            }
        } catch {
            Start-Sleep -Seconds 2
        }
    }
}

Write-Host "`n=== Mimir Services Started ===" -ForegroundColor Green
Write-Host "`nAccess Points:" -ForegroundColor Cyan
Write-Host "  Portal:      http://localhost:9042/portal" -ForegroundColor White
Write-Host "  Health:      http://localhost:9042/health" -ForegroundColor White
Write-Host "  Neo4j:       http://localhost:7474" -ForegroundColor White
Write-Host "  Credentials: neo4j / MxXhTKH3qntipYLa1e0QOluJ" -ForegroundColor Gray

Write-Host "`nCommands:" -ForegroundColor Cyan
Write-Host "  Status:  docker compose ps" -ForegroundColor White
Write-Host "  Logs:    docker compose logs -f" -ForegroundColor White
Write-Host "  Stop:    docker compose down" -ForegroundColor White
