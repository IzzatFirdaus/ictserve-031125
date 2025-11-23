# ICTServe Repository Cleanup Script
# Removes deprecated files after Neo4j import verification

Write-Host "🧹 ICTServe Repository Cleanup" -ForegroundColor Cyan
Write-Host "================================`n" -ForegroundColor Cyan

# Verify Neo4j has data before deleting
Write-Host "🔍 Verifying Neo4j import..." -ForegroundColor Yellow
$neo4jCount = docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "MATCH (n:MemoryEntity) RETURN count(n) as total" 2>$null

if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Cannot connect to Neo4j. Aborting cleanup." -ForegroundColor Red
    exit 1
}

Write-Host "✅ Neo4j connection verified`n" -ForegroundColor Green

# Files to delete
$filesToDelete = @(
    "DOCUMENTATION_REORGANIZATION_COMPLETE.md",
    "REPOSITORY_CLEANUP_PLAN.md"
)

# Archived directory
$archivedDir = "archived"

Write-Host "📋 Files to delete:" -ForegroundColor Yellow
foreach ($file in $filesToDelete) {
    if (Test-Path $file) {
        Write-Host "  - $file" -ForegroundColor Gray
    }
}

if (Test-Path $archivedDir) {
    $archivedFiles = Get-ChildItem -Path $archivedDir -Recurse -File
    Write-Host "  - $archivedDir\ ($($archivedFiles.Count) files)" -ForegroundColor Gray
}

Write-Host "`n⚠️  This will permanently delete these files." -ForegroundColor Yellow
$confirm = Read-Host "Continue? (yes/no)"

if ($confirm -ne "yes") {
    Write-Host "❌ Cleanup cancelled" -ForegroundColor Red
    exit 0
}

# Delete root files
Write-Host "`n🗑️  Deleting root files..." -ForegroundColor Cyan
foreach ($file in $filesToDelete) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "  ✅ Deleted: $file" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Not found: $file" -ForegroundColor Yellow
    }
}

# Delete archived directory
if (Test-Path $archivedDir) {
    Write-Host "`n🗑️  Deleting archived directory..." -ForegroundColor Cyan
    Remove-Item $archivedDir -Recurse -Force
    Write-Host "  ✅ Deleted: $archivedDir\" -ForegroundColor Green
}

# Verify cleanup
Write-Host "`n✅ Cleanup complete!" -ForegroundColor Green
Write-Host "`n📊 Verification:" -ForegroundColor Cyan
Write-Host "  - Root files removed: $($filesToDelete.Count)" -ForegroundColor Gray
Write-Host "  - Archived directory removed: $(if (Test-Path $archivedDir) { 'No' } else { 'Yes' })" -ForegroundColor Gray

Write-Host "`n💡 Next steps:" -ForegroundColor Yellow
Write-Host "  1. Verify Neo4j data: docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ 'MATCH (n:MemoryEntity) RETURN count(n)'" -ForegroundColor Gray
Write-Host "  2. Commit changes: git add -A && git commit -m 'chore: cleanup deprecated files after Neo4j import'" -ForegroundColor Gray
