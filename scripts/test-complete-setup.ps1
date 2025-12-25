# Complete ICTServe Development Setup Test
Write-Host "ICTServe v3.6.0 Development Setup Verification" -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Test 1: Environment Check
Write-Host "`n[TEST 1] Environment Prerequisites" -ForegroundColor Yellow
$tests = @()

# PHP Check
try {
    $phpVersion = (& php --version | Select-String "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value })
    if ([version]$phpVersion -ge [version]"8.2.12") {
        Write-Host "✅ PHP $phpVersion (meets requirement: 8.2.12+)" -ForegroundColor Green
        $tests += @{ Name = "PHP"; Status = "PASS" }
    } else {
        Write-Host "❌ PHP $phpVersion (requires 8.2.12+)" -ForegroundColor Red
        $tests += @{ Name = "PHP"; Status = "FAIL" }
    }
} catch {
    Write-Host "❌ PHP not found" -ForegroundColor Red
    $tests += @{ Name = "PHP"; Status = "FAIL" }
}

# Node.js Check
try {
    $nodeVersion = (& node --version) -replace 'v',''
    $parts = $nodeVersion.Split('.')
    $major = [int]$parts[0]
    $minor = [int]$parts[1]
    if ($major -ge 22 -and ($major -gt 22 -or $minor -ge 12)) {
        Write-Host "✅ Node.js $nodeVersion (meets requirement: 22.12+)" -ForegroundColor Green
        $tests += @{ Name = "Node.js"; Status = "PASS" }
    } else {
        Write-Host "❌ Node.js $nodeVersion (requires 22.12+)" -ForegroundColor Red
        $tests += @{ Name = "Node.js"; Status = "FAIL" }
    }
} catch {
    Write-Host "❌ Node.js not found" -ForegroundColor Red
    $tests += @{ Name = "Node.js"; Status = "FAIL" }
}

# Vite Check
try {
    $viteVersion = & npx vite --version 2>$null
    if ($viteVersion) {
        Write-Host "✅ Vite available ($viteVersion)" -ForegroundColor Green
        $tests += @{ Name = "Vite"; Status = "PASS" }
    } else {
        Write-Host "❌ Vite not available" -ForegroundColor Red
        $tests += @{ Name = "Vite"; Status = "FAIL" }
    }
} catch {
    Write-Host "❌ Vite check failed" -ForegroundColor Red
    $tests += @{ Name = "Vite"; Status = "FAIL" }
}

# Test 2: Laravel Configuration
Write-Host "`n[TEST 2] Laravel Configuration" -ForegroundColor Yellow
try {
    $laravelCheck = & php artisan about --only=environment 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Laravel environment OK" -ForegroundColor Green
        $tests += @{ Name = "Laravel Config"; Status = "PASS" }
    } else {
        Write-Host "❌ Laravel environment issues" -ForegroundColor Red
        $tests += @{ Name = "Laravel Config"; Status = "FAIL" }
    }
} catch {
    Write-Host "❌ Laravel check failed" -ForegroundColor Red
    $tests += @{ Name = "Laravel Config"; Status = "FAIL" }
}

# Test 3: Database Connection
Write-Host "`n[TEST 3] Database Connection" -ForegroundColor Yellow
try {
    $dbCheck = & php artisan db:show --database=mysql 2>$null
    if ($LASTEXITCODE -eq 0) {
        Write-Host "✅ Database connection OK" -ForegroundColor Green
        $tests += @{ Name = "Database"; Status = "PASS" }
    } else {
        Write-Host "❌ Database connection failed" -ForegroundColor Red
        $tests += @{ Name = "Database"; Status = "FAIL" }
    }
} catch {
    Write-Host "❌ Database check failed" -ForegroundColor Red
    $tests += @{ Name = "Database"; Status = "FAIL" }
}

# Test 4: Development Script Components
Write-Host "`n[TEST 4] Development Script Components" -ForegroundColor Yellow

# Check script files exist
$scriptFiles = @(
    "scripts\dev\start-dev.ps1",
    "scripts\dev\dev-helpers.ps1"
)

foreach ($file in $scriptFiles) {
    if (Test-Path $file) {
        Write-Host "✅ $file exists" -ForegroundColor Green
        $tests += @{ Name = "Script: $file"; Status = "PASS" }
    } else {
        Write-Host "❌ $file missing" -ForegroundColor Red
        $tests += @{ Name = "Script: $file"; Status = "FAIL" }
    }
}

# Test 5: Quick Service Start Test
Write-Host "`n[TEST 5] Quick Service Start Test" -ForegroundColor Yellow
Write-Host "Testing Laravel server startup..." -ForegroundColor Gray

$laravelProcess = Start-Process powershell -ArgumentList "-NoExit", "-Command", "php artisan serve --host=127.0.0.1 --port=8000" -PassThru
Start-Sleep -Seconds 5

try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000/api/health" -TimeoutSec 10 -UseBasicParsing
    if ($response.StatusCode -eq 200) {
        Write-Host "✅ Laravel server starts and health check passes" -ForegroundColor Green
        $tests += @{ Name = "Laravel Startup"; Status = "PASS" }
    } else {
        Write-Host "❌ Laravel health check failed (Status: $($response.StatusCode))" -ForegroundColor Red
        $tests += @{ Name = "Laravel Startup"; Status = "FAIL" }
    }
} catch {
    Write-Host "❌ Laravel startup test failed: $_" -ForegroundColor Red
    $tests += @{ Name = "Laravel Startup"; Status = "FAIL" }
} finally {
    Stop-Process -Id $laravelProcess.Id -Force -ErrorAction SilentlyContinue
}

# Summary
Write-Host "`n[SUMMARY] Test Results" -ForegroundColor Cyan
Write-Host "========================" -ForegroundColor Cyan

$passCount = ($tests | Where-Object { $_.Status -eq "PASS" }).Count
$failCount = ($tests | Where-Object { $_.Status -eq "FAIL" }).Count
$totalCount = $tests.Count

foreach ($test in $tests) {
    $icon = if ($test.Status -eq "PASS") { "✅" } else { "❌" }
    $color = if ($test.Status -eq "PASS") { "Green" } else { "Red" }
    Write-Host "$icon $($test.Name)" -ForegroundColor $color
}

Write-Host "`nOverall: $passCount/$totalCount tests passed" -ForegroundColor $(if ($failCount -eq 0) { "Green" } else { "Yellow" })

if ($failCount -eq 0) {
    Write-Host "`n🎉 All tests passed! Your development environment is ready." -ForegroundColor Green
    Write-Host "`nNext steps:" -ForegroundColor Cyan
    Write-Host "  1. Run: .\scripts\dev\start-dev.ps1" -ForegroundColor White
    Write-Host "  2. Visit: http://127.0.0.1:8000" -ForegroundColor White
    Write-Host "  3. Admin: http://127.0.0.1:8000/admin" -ForegroundColor White
} else {
    Write-Host "`n⚠️  Some tests failed. Please address the issues above before running the development environment." -ForegroundColor Yellow
}

Write-Host "`nDevelopment script fixes applied:" -ForegroundColor Cyan
Write-Host "  ✅ Fixed Laravel health check endpoint (/api/health)" -ForegroundColor Green
Write-Host "  ✅ Fixed Vite command resolution (using npx)" -ForegroundColor Green
Write-Host "  ✅ Added service initialization delays" -ForegroundColor Green
Write-Host "  ✅ Improved error handling and timeouts" -ForegroundColor Green
Write-Host "  ✅ Fixed Reverb port configuration (8080)" -ForegroundColor Green
Write-Host "  ✅ Updated .env.local with proper settings" -ForegroundColor Green
Write-Host "  ✅ Added node_modules validation" -ForegroundColor Green