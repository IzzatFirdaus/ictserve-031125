# Index ICTServe codebase with Mimir
# Based on official Mimir file indexing documentation

param(
    [string]$Path = "C:\XAMPP\htdocs\ictserve-031125",
    [switch]$WithEmbeddings,
    [switch]$List,
    [switch]$Remove
)

$mimirPath = Join-Path $PSScriptRoot "..\Mimir"

if (-not (Test-Path $mimirPath)) {
    Write-Host "Error: Mimir directory not found" -ForegroundColor Red
    exit 1
}

Set-Location $mimirPath

if ($List) {
    Write-Host "=== Indexed Folders ===" -ForegroundColor Cyan
    npm run index:list
    exit 0
}

if ($Remove) {
    Write-Host "=== Removing Folder from Index ===" -ForegroundColor Yellow
    Write-Host "Path: $Path" -ForegroundColor Gray
    npm run index:remove $Path
    exit 0
}

Write-Host "=== Indexing ICTServe Codebase ===" -ForegroundColor Cyan
Write-Host "Path: $Path" -ForegroundColor Gray
Write-Host "Embeddings: $($WithEmbeddings ? 'Enabled' : 'Disabled')" -ForegroundColor Gray

if ($WithEmbeddings) {
    Write-Host "`nNote: Indexing with embeddings is slower but enables semantic search" -ForegroundColor Yellow
    npm run index:add $Path --embeddings
} else {
    npm run index:add $Path
}

Write-Host "`n=== Indexing Complete ===" -ForegroundColor Green
Write-Host "`nQuery indexed files:" -ForegroundColor Cyan
Write-Host "  npm run index:list" -ForegroundColor White
Write-Host "`nRemove from index:" -ForegroundColor Cyan
Write-Host "  npm run index:remove $Path" -ForegroundColor White
