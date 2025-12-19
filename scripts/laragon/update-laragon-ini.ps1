# Update Laragon configuration for Redis 7.4.1
# Part of Redis 7.4.1 upgrade process for ICTServe

$laragonIni = "C:\laragon\usr\laragon.ini"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Update Laragon Configuration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Verify Laragon configuration exists
if (-not (Test-Path $laragonIni)) {
    Write-Host "❌ Laragon configuration not found at: $laragonIni" -ForegroundColor Red
    exit 1
}

# Backup current configuration
$backupFile = "$laragonIni.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Write-Host "Creating backup of laragon.ini..." -ForegroundColor Cyan
Copy-Item $laragonIni -Destination $backupFile
Write-Host "✅ Backup created: $backupFile" -ForegroundColor Green

# Read current configuration
Write-Host ""
Write-Host "Reading current configuration..." -ForegroundColor Cyan
$content = Get-Content $laragonIni

# Find current Redis version
$currentVersion = ($content | Select-String -Pattern '^\[redis\]' -Context 0,5).Context.PostContext |
                  Select-String -Pattern '^Version=' |
                  ForEach-Object { $_.ToString().Replace('Version=', '') }

if ($currentVersion) {
    Write-Host "  Current Redis version: $currentVersion" -ForegroundColor Gray
} else {
    Write-Host "  ⚠️  No Redis version found in configuration" -ForegroundColor Yellow
}

# Update Redis version
Write-Host ""
Write-Host "Updating Redis version to 7.4.1..." -ForegroundColor Cyan
$content = $content -replace 'Version=redis-x64-.*', 'Version=redis-x64-7.4.1'

# Write updated configuration
try {
    $content | Set-Content $laragonIni -Encoding UTF8
    Write-Host "✅ Laragon configuration updated" -ForegroundColor Green
} catch {
    Write-Host "❌ Failed to update configuration: $($_.Exception.Message)" -ForegroundColor Red
    Write-Host "   Restoring backup..." -ForegroundColor Yellow
    Copy-Item $backupFile -Destination $laragonIni -Force
    exit 1
}

# Verify update
Write-Host ""
Write-Host "Verifying update..." -ForegroundColor Cyan
$updatedContent = Get-Content $laragonIni
$newVersion = ($updatedContent | Select-String -Pattern '^\[redis\]' -Context 0,5).Context.PostContext |
              Select-String -Pattern '^Version=' |
              ForEach-Object { $_.ToString().Replace('Version=', '') }

if ($newVersion -eq 'redis-x64-7.4.1') {
    Write-Host "✅ Configuration verified: $newVersion" -ForegroundColor Green
} else {
    Write-Host "❌ Verification failed: $newVersion" -ForegroundColor Red
    exit 1
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Laragon configuration updated successfully!" -ForegroundColor Green
Write-Host ""
Write-Host "Changes:" -ForegroundColor Cyan
Write-Host "  Old: $currentVersion" -ForegroundColor Gray
Write-Host "  New: redis-x64-7.4.1" -ForegroundColor Green
Write-Host ""
Write-Host "⚠️  IMPORTANT: Restart Laragon to apply changes" -ForegroundColor Yellow
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Cyan
Write-Host "  1. Stop current Redis: stop-redis.ps1" -ForegroundColor White
Write-Host "  2. Migrate data: migrate-redis-data.ps1" -ForegroundColor White
Write-Host "  3. Start Redis 7.4.1: start-redis-7.4.1.ps1" -ForegroundColor White
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
