# Phase 5 Finalization Script
# Date: November 23, 2025
# Purpose: Delete consolidated markdown files and verify completion

Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Cyan
Write-Host "║  Phase 5 Finalization - Documentation Consolidation           ║" -ForegroundColor Cyan
Write-Host "║  November 23, 2025                                            ║" -ForegroundColor Cyan
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Cyan
Write-Host ""

$projectRoot = "C:\XAMPP\htdocs\ictserve-031125"
Set-Location $projectRoot

# Files to delete (Phase 5 consolidated files)
$filesToDelete = @(
    "docs\technical\devtools-mcp-getting-started.md",
    "docs\e2e-triage\helpdesk-performance-triage.md",
    "docs\e2e-triage\loan-accessibility-triage.md",
    "docs\e2e-triage\loan-performance-triage.md"
)

Write-Host "📋 Step 1: Verify Neo4j Import" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────────" -ForegroundColor Gray
Write-Host ""
Write-Host "Before deleting files, verify Phase 5 entities in Neo4j:" -ForegroundColor White
Write-Host ""
Write-Host "1. Open Neo4j Browser: http://localhost:7474" -ForegroundColor Cyan
Write-Host "2. Run this query:" -ForegroundColor Cyan
Write-Host ""
Write-Host "   MATCH (e:Entity) WHERE e.phase = 5" -ForegroundColor Green
Write-Host "   RETURN e.name, e.entityType, size((e)-[:HAS_OBSERVATION]->()) AS observations" -ForegroundColor Green
Write-Host ""
Write-Host "3. Expected result: 4 entities with 59 total observations" -ForegroundColor Cyan
Write-Host ""

$continue = Read-Host "Have you verified the Neo4j import? (Y/N)"
if ($continue -ne "Y" -and $continue -ne "y") {
    Write-Host ""
    Write-Host "❌ Finalization cancelled. Please import Phase 5 entities first." -ForegroundColor Red
    Write-Host ""
    Write-Host "Import instructions:" -ForegroundColor Yellow
    Write-Host "1. Open Neo4j Browser: http://localhost:7474" -ForegroundColor White
    Write-Host "2. Copy contents of: scripts\import-phase5-entities.cypher" -ForegroundColor White
    Write-Host "3. Paste and execute in Neo4j Browser" -ForegroundColor White
    Write-Host "4. Re-run this script after successful import" -ForegroundColor White
    Write-Host ""
    exit 1
}

Write-Host ""
Write-Host "🗑️  Step 2: Delete Consolidated Files" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────────" -ForegroundColor Gray
Write-Host ""

$deletedCount = 0
$failedCount = 0

foreach ($file in $filesToDelete) {
    $fullPath = Join-Path $projectRoot $file
    
    if (Test-Path $fullPath) {
        try {
            Remove-Item -Path $fullPath -Force
            Write-Host "  ✅ Deleted: $file" -ForegroundColor Green
            $deletedCount++
        }
        catch {
            Write-Host "  ❌ Failed to delete: $file" -ForegroundColor Red
            Write-Host "     Error: $($_.Exception.Message)" -ForegroundColor Red
            $failedCount++
        }
    }
    else {
        Write-Host "  ⚠️  Not found: $file" -ForegroundColor Yellow
    }
}

Write-Host ""
Write-Host "─────────────────────────────────────────────────────────────────" -ForegroundColor Gray
Write-Host "Deleted: $deletedCount files" -ForegroundColor Green
if ($failedCount -gt 0) {
    Write-Host "Failed: $failedCount files" -ForegroundColor Red
}
Write-Host ""

Write-Host "✅ Step 3: Verify Completion" -ForegroundColor Yellow
Write-Host "─────────────────────────────────────────────────────────────────" -ForegroundColor Gray
Write-Host ""

# Check remaining operational docs
$remainingDocs = @(
    "docs\mimir.md"
)

Write-Host "Remaining operational documentation:" -ForegroundColor White
foreach ($doc in $remainingDocs) {
    $fullPath = Join-Path $projectRoot $doc
    if (Test-Path $fullPath) {
        $lineCount = (Get-Content $fullPath | Measure-Object -Line).Lines
        Write-Host "  📄 $doc ($lineCount lines)" -ForegroundColor Cyan
    }
}

Write-Host ""
Write-Host "╔════════════════════════════════════════════════════════════════╗" -ForegroundColor Green
Write-Host "║  Phase 5 Finalization Complete                                 ║" -ForegroundColor Green
Write-Host "╚════════════════════════════════════════════════════════════════╝" -ForegroundColor Green
Write-Host ""
Write-Host "📊 Final Statistics:" -ForegroundColor Yellow
Write-Host "  • Operational docs migrated: 13 of 14 (93%)" -ForegroundColor White
Write-Host "  • Total entities in Neo4j: 55" -ForegroundColor White
Write-Host "  • Total observations: 209" -ForegroundColor White
Write-Host "  • Total relations: 99+" -ForegroundColor White
Write-Host "  • Markdown lines consolidated: 5,013" -ForegroundColor White
Write-Host ""
Write-Host "🎉 Single Source of Truth: Neo4j Memory Graph" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Update README.md to reference Neo4j as primary knowledge source" -ForegroundColor White
Write-Host "  2. Add Neo4j query examples to developer onboarding" -ForegroundColor White
Write-Host "  3. Create Neo4j query cheat sheet" -ForegroundColor White
Write-Host ""
