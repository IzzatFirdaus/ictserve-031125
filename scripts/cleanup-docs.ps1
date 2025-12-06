# Documentation Cleanup Script
# Removes deprecated documentation files from root directory

$ErrorActionPreference = "Stop"

Write-Host "=== ICTServe Documentation Cleanup ===" -ForegroundColor Cyan
Write-Host ""

# Define files to remove (deprecated/moved)
$filesToRemove = @(
    "CONSOLIDATION_FINAL_STATUS.md",
    "LARASTAN_PROGRESS_SESSION_1.md",
    "LARASTAN_RESOLUTION_GUIDE.md",
    "LIVEWIRE_MIGRATION_PROGRESS.md",
    "NEXT_SESSION_START_HERE.md",
    "PR_LIVEWIRE_3_UPDATES.md",
    "TASK_4_COMPLETION_SUMMARY.md",
    "VOLT_CONVERSION_STRATEGY.md"
)

# Files to keep (important)
$filesToKeep = @(
    "README.md",
    "AGENTS.md",
    "GEMINI.md",
    "CLAUDE.md",
    ".GEMINI.md",
    "CHANGELOG.md"
)

Write-Host "Files to remove:" -ForegroundColor Yellow
foreach ($file in $filesToRemove) {
    if (Test-Path $file) {
        Write-Host "  - $file" -ForegroundColor Red
    }
}

Write-Host ""
Write-Host "Files to keep:" -ForegroundColor Green
foreach ($file in $filesToKeep) {
    if (Test-Path $file) {
        Write-Host "  - $file" -ForegroundColor Green
    }
}

Write-Host ""
$confirm = Read-Host "Proceed with cleanup? (y/N)"

if ($confirm -ne "y") {
    Write-Host "Cleanup cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Removing deprecated files..." -ForegroundColor Cyan

$removed = 0
$notFound = 0

foreach ($file in $filesToRemove) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "  ✓ Removed: $file" -ForegroundColor Green
        $removed++
    } else {
        Write-Host "  - Not found: $file" -ForegroundColor Gray
        $notFound++
    }
}

Write-Host ""
Write-Host "=== Cleanup Complete ===" -ForegroundColor Cyan
Write-Host "  Removed: $removed files" -ForegroundColor Green
Write-Host "  Not found: $notFound files" -ForegroundColor Gray
Write-Host ""
Write-Host "Documentation now organized in docs/ directory:" -ForegroundColor Cyan
Write-Host "  - docs/docker/     - Docker setup and guides" -ForegroundColor White
Write-Host "  - docs/reference/  - Technical references" -ForegroundColor White
Write-Host ""
