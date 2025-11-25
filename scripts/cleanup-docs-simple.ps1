# Simple Documentation Cleanup Script

$files = @(
    "CONSOLIDATION_FINAL_STATUS.md",
    "LARASTAN_PROGRESS_SESSION_1.md",
    "LARASTAN_RESOLUTION_GUIDE.md",
    "LIVEWIRE_MIGRATION_PROGRESS.md",
    "NEXT_SESSION_START_HERE.md",
    "PR_LIVEWIRE_3_UPDATES.md",
    "TASK_4_COMPLETION_SUMMARY.md",
    "VOLT_CONVERSION_STRATEGY.md",
    "mimir.md"
)

Write-Host "Removing deprecated files..." -ForegroundColor Cyan

foreach ($file in $files) {
    if (Test-Path $file) {
        Remove-Item $file -Force
        Write-Host "Removed: $file" -ForegroundColor Green
    }
}

Write-Host "Cleanup complete!" -ForegroundColor Green
