# Index ICTServe Project in Mimir
param(
    [switch]$WithEmbeddings
)

$ErrorActionPreference = "Stop"

Write-Host "📚 Indexing ICTServe project in Mimir..." -ForegroundColor Cyan

# Check if Mimir is running
try {
    $health = Invoke-RestMethod -Uri "http://localhost:9042/health" -TimeoutSec 5
    Write-Host "✓ Mimir is running" -ForegroundColor Green
} catch {
    Write-Host "✗ Mimir is not running. Start it first with: .\scripts\mimir\start.ps1" -ForegroundColor Red
    exit 1
}

# Index workspace
$body = @{
    path = "/workspace"
    embeddings = $WithEmbeddings.IsPresent
} | ConvertTo-Json

Write-Host "`nIndexing /workspace (ICTServe root)..." -ForegroundColor Yellow
if ($WithEmbeddings) {
    Write-Host "⚠ Embeddings enabled - this may take several minutes" -ForegroundColor Yellow
}

try {
    $response = Invoke-RestMethod -Uri "http://localhost:9042/api/index/folder" `
        -Method Post `
        -ContentType "application/json" `
        -Body $body `
        -TimeoutSec 300

    Write-Host "`n✓ Indexing complete!" -ForegroundColor Green
    Write-Host "Files indexed: $($response.filesIndexed)" -ForegroundColor White
    Write-Host "Chunks created: $($response.chunksCreated)" -ForegroundColor White
    
    if ($WithEmbeddings) {
        Write-Host "Embeddings generated: $($response.embeddingsGenerated)" -ForegroundColor White
    }
} catch {
    Write-Host "`n✗ Indexing failed: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "Check logs: docker compose logs mimir-server" -ForegroundColor Yellow
    exit 1
}

Write-Host "`n🎯 Next steps:" -ForegroundColor Cyan
Write-Host "  1. Open Mimir Portal: http://localhost:9042/portal" -ForegroundColor White
Write-Host "  2. Try semantic search: 'Show me all Livewire components'" -ForegroundColor White
Write-Host "  3. Create TODOs and link files" -ForegroundColor White
