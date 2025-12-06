# Complete Memory Import and Cleanup Execution Script
# Runs all steps in sequence with verification

param(
    [switch]$SkipConfirmation
)

$ErrorActionPreference = "Stop"

Write-Host "🚀 ICTServe Memory Import & Cleanup" -ForegroundColor Cyan
Write-Host "====================================`n" -ForegroundColor Cyan

# Step 1: Verify Prerequisites
Write-Host "📋 Step 1: Verifying Prerequisites..." -ForegroundColor Yellow

# Check Neo4j
Write-Host "  Checking Neo4j..." -ForegroundColor Gray
$neo4jRunning = docker ps --filter "name=neo4j" --format "{{.Names}}" 2>$null
if (-not $neo4jRunning) {
    Write-Host "  ❌ Neo4j not running. Starting..." -ForegroundColor Red
    docker-compose up -d neo4j_db
    Start-Sleep -Seconds 10
}
Write-Host "  ✅ Neo4j is running" -ForegroundColor Green

# Check memory.jsonl
Write-Host "  Checking memory.jsonl..." -ForegroundColor Gray
if (-not (Test-Path "storage\mcp\memory.jsonl")) {
    Write-Host "  ❌ memory.jsonl not found!" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ memory.jsonl exists" -ForegroundColor Green

# Check PHP
Write-Host "  Checking PHP..." -ForegroundColor Gray
$phpVersion = php -v 2>$null
if ($LASTEXITCODE -ne 0) {
    Write-Host "  ❌ PHP not found!" -ForegroundColor Red
    exit 1
}
Write-Host "  ✅ PHP available" -ForegroundColor Green

Write-Host "`n✅ All prerequisites met`n" -ForegroundColor Green

# Step 2: Import Memory to Neo4j
Write-Host "📥 Step 2: Importing memory.jsonl to Neo4j..." -ForegroundColor Yellow
php scripts/import-memory-to-neo4j.php

if ($LASTEXITCODE -ne 0) {
    Write-Host "`n❌ Import failed!" -ForegroundColor Red
    exit 1
}

Write-Host "`n✅ Import complete`n" -ForegroundColor Green

# Step 3: Verify Import
Write-Host "🔍 Step 3: Verifying Neo4j Import..." -ForegroundColor Yellow

$entityCount = docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "MATCH (n:MemoryEntity) RETURN count(n) as total" 2>$null | Select-String -Pattern "\d+" | ForEach-Object { $_.Matches[0].Value }

if ($entityCount -gt 0) {
    Write-Host "  ✅ Found $entityCount entities in Neo4j" -ForegroundColor Green
} else {
    Write-Host "  ❌ No entities found in Neo4j!" -ForegroundColor Red
    exit 1
}

$relationCount = docker exec neo4j_db cypher-shell -u neo4j -p MxXhTKH3qntipYLa1e0QOluJ "MATCH ()-[r]->() RETURN count(r) as total" 2>$null | Select-String -Pattern "\d+" | ForEach-Object { $_.Matches[0].Value }

Write-Host "  ✅ Found $relationCount relationships" -ForegroundColor Green

Write-Host "`n✅ Verification complete`n" -ForegroundColor Green

# Step 4: Cleanup Deprecated Files
Write-Host "🧹 Step 4: Cleaning up deprecated files..." -ForegroundColor Yellow

$filesToDelete = @(
    "DOCUMENTATION_REORGANIZATION_COMPLETE.md",
    "REPOSITORY_CLEANUP_PLAN.md"
)

$archivedDir = "archived"

Write-Host "`n📋 Files to delete:" -ForegroundColor Gray
foreach ($file in $filesToDelete) {
    if (Test-Path $file) {
        Write-Host "  - $file" -ForegroundColor Gray
    }
}

if (Test-Path $archivedDir) {
    $archivedFiles = Get-ChildItem -Path $archivedDir -Recurse -File
    Write-Host "  - $archivedDir\ ($($archivedFiles.Count) files)" -ForegroundColor Gray
}

if (-not $SkipConfirmation) {
    Write-Host "`n⚠️  This will permanently delete these files." -ForegroundColor Yellow
    $confirm = Read-Host "Continue? (yes/no)"

    if ($confirm -ne "yes") {
        Write-Host "❌ Cleanup cancelled" -ForegroundColor Red
        exit 0
    }
}

# Delete files
Write-Host "`n🗑️  Deleting files..." -ForegroundColor Cyan
$deletedCount = 0

foreach ($file in $filesToDelete) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "  ✅ Deleted: $file" -ForegroundColor Green
        $deletedCount++
    }
}

if (Test-Path $archivedDir) {
    $archivedFileCount = (Get-ChildItem -Path $archivedDir -Recurse -File).Count
    Remove-Item $archivedDir -Recurse -Force
    Write-Host "  ✅ Deleted: $archivedDir\ ($archivedFileCount files)" -ForegroundColor Green
    $deletedCount += $archivedFileCount
}

Write-Host "`n✅ Deleted $deletedCount files/directories`n" -ForegroundColor Green

# Step 5: Final Summary
Write-Host "📊 Final Summary" -ForegroundColor Cyan
Write-Host "================" -ForegroundColor Cyan
Write-Host "  Neo4j Entities: $entityCount" -ForegroundColor Gray
Write-Host "  Neo4j Relationships: $relationCount" -ForegroundColor Gray
Write-Host "  Files Deleted: $deletedCount" -ForegroundColor Gray
Write-Host "  Status: ✅ SUCCESS" -ForegroundColor Green

Write-Host "`n💡 Next Steps:" -ForegroundColor Yellow
Write-Host "  1. Browse Neo4j: http://localhost:7474" -ForegroundColor Gray
Write-Host "  2. Commit changes: git add -A && git commit -m 'feat: import memory to Neo4j and cleanup deprecated files'" -ForegroundColor Gray

Write-Host "`n✅ All done!" -ForegroundColor Green
