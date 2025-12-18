#!/usr/bin/env pwsh
# npm permission fixes have been consolidated into main Docker configuration files
# This script is now a simple wrapper that uses the main setup script

Write-Host "=== ICTServe npm & Vite Permission Fix ===" -ForegroundColor Cyan
Write-Host ""
Write-Host "npm permission fixes have been integrated into the main Docker configuration." -ForegroundColor Yellow
Write-Host "Running the main setup script instead..." -ForegroundColor Yellow
Write-Host ""

# Run the main setup script which includes all npm fixes
& "$PSScriptRoot/setup-ictserve.ps1" -Mode development -Verbose

Write-Host ""
Write-Host "=== Fix Complete ===" -ForegroundColor Green
Write-Host ""
Write-Host "All npm and Vite permission fixes are now handled by:" -ForegroundColor Cyan
Write-Host "  • Dockerfile entrypoint script (automatic permission fixes)" -ForegroundColor White
Write-Host "  • compose.yaml npm environment variables" -ForegroundColor White
Write-Host "  • setup-ictserve.ps1 comprehensive setup" -ForegroundColor White
Write-Host ""
Write-Host "You can now run:" -ForegroundColor Cyan
Write-Host "  docker compose exec --user www-data app npm run build" -ForegroundColor White
Write-Host "  docker compose exec --user www-data app npm run dev" -ForegroundColor White
Write-Host ""
