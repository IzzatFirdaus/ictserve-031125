# Test Laravel Redis integration
# Part of Redis 7.4.1 upgrade process for ICTServe

$passwordFile = "C:\laragon\bin\redis\redis-x64-7.4.1\redis-password.txt"

Write-Host "========================================" -ForegroundColor Cyan
Write-Host "  Laravel Redis Integration Test" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""

# Get Redis password
$password = $null
if (Test-Path $passwordFile) {
    $password = Get-Content $passwordFile -Raw
    $password = $password.Trim()
    Write-Host "✅ Redis password loaded" -ForegroundColor Green
} else {
    Write-Host "⚠️  Password file not found" -ForegroundColor Yellow
    $password = Read-Host "Enter Redis password"
}

# Update .env.laragon
Write-Host ""
Write-Host "Updating .env.laragon..." -ForegroundColor Cyan

$envFile = ".env.laragon"
if (-not (Test-Path $envFile)) {
    Write-Host "❌ .env.laragon not found" -ForegroundColor Red
    Write-Host "   Please ensure you're in the ICTServe project directory" -ForegroundColor Yellow
    exit 1
}

# Backup .env.laragon
$backupEnv = ".env.laragon.backup_$(Get-Date -Format 'yyyyMMdd_HHmmss')"
Copy-Item $envFile -Destination $backupEnv
Write-Host "  Backup created: $backupEnv" -ForegroundColor Gray

# Update Redis password
$content = Get-Content $envFile
$content = $content -replace 'REDIS_PASSWORD=.*', "REDIS_PASSWORD=$password"
$content | Set-Content $envFile

Write-Host "✅ .env.laragon updated with new Redis password" -ForegroundColor Green

# Test Laravel Redis connection
Write-Host ""
Write-Host "Testing Laravel Redis connection..." -ForegroundColor Cyan
Write-Host ""

# Test 1: Basic connection
Write-Host "Test 1: Redis PING via Laravel" -ForegroundColor Cyan
$pingTest = @"
try {
    `$result = Redis::ping();
    echo `$result ? 'PONG' : 'FAILED';
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$pingTest 2>&1
    if ($result -match "PONG") {
        Write-Host "  ✅ Laravel Redis connection successful" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Laravel Redis connection failed: $result" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 2: Cache operations
Write-Host ""
Write-Host "Test 2: Cache operations" -ForegroundColor Cyan
$cacheTest = @"
try {
    Cache::put('test_cache_key', 'test_value_' . time(), 60);
    `$value = Cache::get('test_cache_key');
    echo `$value ? 'SUCCESS: ' . `$value : 'FAILED';
    Cache::forget('test_cache_key');
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$cacheTest 2>&1
    if ($result -match "SUCCESS") {
        Write-Host "  ✅ Cache operations working" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Cache operations failed: $result" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 3: Redis key operations
Write-Host ""
Write-Host "Test 3: Redis key operations" -ForegroundColor Cyan
$keyTest = @"
try {
    Redis::set('laravel_test_key', 'laravel_test_value');
    `$value = Redis::get('laravel_test_key');
    echo `$value === 'laravel_test_value' ? 'SUCCESS' : 'FAILED';
    Redis::del('laravel_test_key');
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$keyTest 2>&1
    if ($result -match "SUCCESS") {
        Write-Host "  ✅ Redis key operations working" -ForegroundColor Green
    } else {
        Write-Host "  ❌ Redis key operations failed: $result" -ForegroundColor Red
    }
} catch {
    Write-Host "  ❌ Test failed: $($_.Exception.Message)" -ForegroundColor Red
}

# Test 4: Check Redis info
Write-Host ""
Write-Host "Test 4: Redis server info" -ForegroundColor Cyan
$infoTest = @"
try {
    `$info = Redis::info('server');
    echo 'Redis Version: ' . (`$info['redis_version'] ?? 'unknown');
} catch (Exception `$e) {
    echo 'ERROR: ' . `$e->getMessage();
}
"@

try {
    $result = php artisan tinker --execute=$infoTest 2>&1
    if ($result -match "Redis Version") {
        Write-Host "  ✅ $result" -ForegroundColor Green
    } else {
        Write-Host "  ⚠️  Could not retrieve Redis info" -ForegroundColor Yellow
    }
} catch {
    Write-Host "  ⚠️  Info test skipped" -ForegroundColor Yellow
}

# Summary
Write-Host ""
Write-Host "========================================" -ForegroundColor Cyan
Write-Host "✅ Laravel Redis integration tests completed!" -ForegroundColor Green
Write-Host ""
Write-Host "Configuration:" -ForegroundColor Cyan
Write-Host "  Redis Host: 127.0.0.1" -ForegroundColor White
Write-Host "  Redis Port: 6379" -ForegroundColor White
Write-Host "  Redis Password: $password" -ForegroundColor White
Write-Host ""
Write-Host "⚠️  IMPORTANT: Update other .env files if needed:" -ForegroundColor Yellow
Write-Host "  - .env" -ForegroundColor White
Write-Host "  - .env.docker" -ForegroundColor White
Write-Host "  - .env.xampp" -ForegroundColor White
Write-Host ""
Write-Host "Next step: Run benchmark-redis.ps1" -ForegroundColor Cyan
Write-Host "========================================" -ForegroundColor Cyan
Write-Host ""
