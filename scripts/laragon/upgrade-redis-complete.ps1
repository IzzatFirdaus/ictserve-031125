# Complete Redis 7.4.1 Upgrade Script
# Automated upgrade process for ICTServe

Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Redis 7.4.1 Complete Upgrade" -ForegroundColor Cyan
Write-Host "  ICTServe v3.6.0" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "This script will upgrade Redis from 5.0.14.1 to 7.4.1" -ForegroundColor Yellow
Write-Host ""

$response = Read-Host "Continue with upgrade? (yes/no)"
if ($response -ne "yes") {
    Write-Host "Upgrade cancelled." -ForegroundColor Yellow
    exit 0
}

Write-Host ""
Write-Host "Starting upgrade process..." -ForegroundColor Cyan
Write-Host ""

# Step 1: Backup
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 1: Backup Current Data" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\backup-redis.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Backup failed. Aborting upgrade." -ForegroundColor Red
    exit 1
}
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 2: Download
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 2: Download Redis 7.4.1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\download-redis-7.4.1.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Download failed. Aborting upgrade." -ForegroundColor Red
    exit 1
}
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 3: Verify Dependencies
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 3: Verify Dependencies" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\verify-redis-dependencies.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Dependency check failed. Please resolve issues before continuing." -ForegroundColor Red
    $response = Read-Host "Continue anyway? (yes/no)"
    if ($response -ne "yes") {
        exit 1
    }
}
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 4: Install
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 4: Install Redis 7.4.1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\install-redis-7.4.1.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Installation failed. Aborting upgrade." -ForegroundColor Red
    exit 1
}
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 5: Create Configuration
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 5: Create Configuration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\create-redis-config.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Configuration creation failed. Aborting upgrade." -ForegroundColor Red
    exit 1
}
Write-Host ""
Write-Host "⚠️  IMPORTANT: Save the Redis password shown above!" -ForegroundColor Red
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 6: Update Laragon
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 6: Update Laragon Configuration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\update-laragon-ini.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Laragon update failed. Aborting upgrade." -ForegroundColor Red
    exit 1
}
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 7: Stop Current Redis
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 7: Stop Current Redis" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\stop-redis.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to stop Redis. Aborting upgrade." -ForegroundColor Red
    exit 1
}
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 8: Migrate Data
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 8: Migrate Data" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\migrate-redis-data.ps1"
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 9: Start Redis 7.4.1
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 9: Start Redis 7.4.1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\start-redis-7.4.1.ps1"
if ($LASTEXITCODE -ne 0) {
    Write-Host "❌ Failed to start Redis 7.4.1" -ForegroundColor Red
    Write-Host ""
    Write-Host "You can rollback using: .\scripts\laragon\rollback-redis.ps1" -ForegroundColor Yellow
    exit 1
}
Write-Host ""
Read-Host "Press Enter to continue to testing..."

# Step 10: Test Connection
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 10: Test Redis Connection" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\test-redis-connection.ps1"
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 11: Test Laravel Integration
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 11: Test Laravel Integration" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
& ".\scripts\laragon\test-laravel-redis.ps1"
Write-Host ""
Read-Host "Press Enter to continue..."

# Step 12: Benchmark (Optional)
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "STEP 12: Performance Benchmark (Optional)" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
$response = Read-Host "Run performance benchmark? (yes/no)"
if ($response -eq "yes") {
    & ".\scripts\laragon\benchmark-redis.ps1"
}

# Final Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host "  ✅ UPGRADE COMPLETED SUCCESSFULLY!" -ForegroundColor Green
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
Write-Host "Redis 7.4.1 is now running!" -ForegroundColor Cyan
Write-Host ""
Write-Host "Next steps:" -ForegroundColor Yellow
Write-Host "  1. Monitor Redis: .\scripts\laragon\monitor-redis.ps1" -ForegroundColor White
Write-Host "  2. Test Horizon/Reverb: .\scripts\laragon\test-horizon-reverb.ps1" -ForegroundColor White
Write-Host "  3. Update other .env files with new Redis password" -ForegroundColor White
Write-Host ""
Write-Host "If you encounter issues:" -ForegroundColor Yellow
Write-Host "  - Rollback: .\scripts\laragon\rollback-redis.ps1" -ForegroundColor White
Write-Host "  - Check logs: C:\laragon\data\redis\redis.log" -ForegroundColor White
Write-Host ""
Write-Host "Redis 5.0.14.1 is still available for rollback at:" -ForegroundColor Gray
Write-Host "  C:\laragon\bin\redis\redis-x64-5.0.14.1" -ForegroundColor Gray
Write-Host ""
Write-Host "========================================" -ForegroundColor Green
Write-Host ""
