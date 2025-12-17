#!/usr/bin/env pwsh
# Clear all code analysis caches to fix vendor files showing in Problems tab

Write-Host "🧹 Clearing code analysis caches..." -ForegroundColor Cyan

# Clear PHPStan cache
if (Test-Path "bootstrap/cache/phpstan") {
    Remove-Item -Recurse -Force "bootstrap/cache/phpstan"
    Write-Host "✓ PHPStan cache cleared" -ForegroundColor Green
}

# Clear Intelephense cache (Windows)
$intelephenseCache = "$env:LOCALAPPDATA\intelephense"
if (Test-Path $intelephenseCache) {
    Remove-Item -Recurse -Force $intelephenseCache
    Write-Host "✓ Intelephense cache cleared" -ForegroundColor Green
}

# Clear Laravel IDE Helper
if (Test-Path "_ide_helper.php") {
    Remove-Item "_ide_helper.php"
    Write-Host "✓ IDE Helper cleared" -ForegroundColor Green
}

# Clear .phpunit.result.cache
if (Test-Path ".phpunit.result.cache") {
    Remove-Item ".phpunit.result.cache"
    Write-Host "✓ PHPUnit cache cleared" -ForegroundColor Green
}

Write-Host ""
Write-Host "✅ All caches cleared!" -ForegroundColor Green
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "   1. Reload VS Code window (Ctrl+Shift+P -> Reload Window)" -ForegroundColor White
Write-Host "   2. Wait for Intelephense to re-index (check status bar)" -ForegroundColor White
Write-Host "   3. Problems tab should now only show app/ issues" -ForegroundColor White
