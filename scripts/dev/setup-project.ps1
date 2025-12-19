# ICTServe v3.6.0 Project Setup Script
# Prepares the development environment for first-time setup

param(
    [switch]$SkipNpm,
    [switch]$Force
)

Write-Host "ICTServe v3.6.0 Project Setup" -ForegroundColor Cyan
Write-Host "=============================" -ForegroundColor Cyan
Write-Host ""

$projectRoot = Get-Location
$hasErrors = $false

# 1. Check PHP
Write-Host "[1/7] Checking PHP installation..." -ForegroundColor Yellow
try {
    $phpVersion = php --version 2>&1 | Select-String "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
    if ($phpVersion -and [version]$phpVersion -ge [version]"8.2.12") {
        Write-Host "  [OK] PHP $phpVersion" -ForegroundColor Green
    } else {
        Write-Host "  [ERROR] PHP $phpVersion does not meet requirement (8.2.12+)" -ForegroundColor Red
        $hasErrors = $true
    }
} catch {
    Write-Host "  [ERROR] PHP not found in PATH" -ForegroundColor Red
    $hasErrors = $true
}

# 2. Check Composer
Write-Host "[2/7] Checking Composer..." -ForegroundColor Yellow
try {
    $composerVersion = composer --version 2>&1 | Select-String "Composer version (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
    if ($composerVersion) {
        Write-Host "  [OK] Composer $composerVersion" -ForegroundColor Green
    }
} catch {
    Write-Host "  [ERROR] Composer not found" -ForegroundColor Red
    $hasErrors = $true
}

# 3. Check Node.js
Write-Host "[3/7] Checking Node.js..." -ForegroundColor Yellow
try {
    $nodeVersion = node --version 2>&1
    if ($nodeVersion) {
        $nodeVersionNumeric = $nodeVersion -replace 'v',''
        Write-Host "  [OK] Node.js $nodeVersionNumeric" -ForegroundColor Green

        # Check npm
        $npmTest = npm --version 2>&1
        if ($LASTEXITCODE -ne 0) {
            Write-Host "  [WARN] npm has issues - will skip npm install" -ForegroundColor Yellow
            Write-Host "    Fix: Run .\scripts\dev\fix-npm.ps1 or reinstall Node.js" -ForegroundColor Gray
            $SkipNpm = $true
        } else {
            Write-Host "  [OK] npm $npmTest" -ForegroundColor Green
        }
    } else {
        Write-Host "  [ERROR] Node.js not found" -ForegroundColor Red
        $hasErrors = $true
    }
} catch {
    Write-Host "  [ERROR] Node.js not found" -ForegroundColor Red
    $hasErrors = $true
}

# 4. Setup .env file
Write-Host "[4/7] Setting up environment configuration..." -ForegroundColor Yellow
if (Test-Path ".env") {
    if ($Force) {
        Write-Host "  [WARN] Overwriting existing .env file" -ForegroundColor Yellow
        Copy-Item ".env.example" ".env" -Force
    } else {
        Write-Host "  [OK] .env file already exists" -ForegroundColor Green
    }
} else {
    Copy-Item ".env.example" ".env"
    Write-Host "  [OK] Created .env from .env.example" -ForegroundColor Green
}

# 5. Install Composer dependencies
Write-Host "[5/7] Installing PHP dependencies..." -ForegroundColor Yellow
if (-not $hasErrors) {
    try {
        Write-Host "  Running composer install..." -ForegroundColor Gray
        composer install --no-interaction --prefer-dist 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  [OK] Composer dependencies installed" -ForegroundColor Green
        } else {
            Write-Host "  [ERROR] Composer install failed" -ForegroundColor Red
            $hasErrors = $true
        }
    } catch {
        Write-Host "  [ERROR] Composer install failed" -ForegroundColor Red
        $hasErrors = $true
    }
} else {
    Write-Host "  [SKIP] Skipping due to previous errors" -ForegroundColor Yellow
}

# 6. Generate application key
Write-Host "[6/7] Generating application key..." -ForegroundColor Yellow
if (-not $hasErrors) {
    try {
        php artisan key:generate --force 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  [OK] Application key generated" -ForegroundColor Green
        } else {
            Write-Host "  [WARN] Key generation had issues" -ForegroundColor Yellow
        }
    } catch {
        Write-Host "  [ERROR] Key generation failed" -ForegroundColor Red
    }
}

# 7. Install npm dependencies
Write-Host "[7/7] Installing frontend dependencies..." -ForegroundColor Yellow
if (-not $SkipNpm -and -not $hasErrors) {
    try {
        Write-Host "  Running npm install (this may take a few minutes)..." -ForegroundColor Gray
        npm install --no-audit 2>&1 | Out-Null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  [OK] npm dependencies installed" -ForegroundColor Green
        } else {
            Write-Host "  [ERROR] npm install failed" -ForegroundColor Red
            Write-Host "    Run: .\scripts\dev\fix-npm.ps1 to attempt fix" -ForegroundColor Gray
        }
    } catch {
        Write-Host "  [ERROR] npm install failed" -ForegroundColor Red
        Write-Host "    Run: .\scripts\dev\fix-npm.ps1 to attempt fix" -ForegroundColor Gray
    }
} else {
    Write-Host "  [SKIP] npm install skipped" -ForegroundColor Yellow
    if ($SkipNpm) {
        Write-Host "    Run manually after fixing npm: npm install" -ForegroundColor Gray
    }
}

# Summary
Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Setup Summary" -ForegroundColor White
Write-Host "=========================================" -ForegroundColor Cyan

if ($hasErrors) {
    Write-Host "[ERROR] Setup completed with errors" -ForegroundColor Red
    Write-Host ""
    Write-Host "Please fix the errors above before running the development server." -ForegroundColor Yellow
    exit 1
} else {
    Write-Host "[OK] Setup completed successfully!" -ForegroundColor Green
    Write-Host ""
    Write-Host "Next Steps:" -ForegroundColor White
    Write-Host "  1. Configure database in .env file" -ForegroundColor Gray
    Write-Host "  2. Run migrations: php artisan migrate" -ForegroundColor Gray
    Write-Host "  3. Start development server: .\scripts\dev\start-dev.ps1" -ForegroundColor Gray
    Write-Host ""
    Write-Host "Or run all at once:" -ForegroundColor White
    Write-Host "  php artisan migrate && .\scripts\dev\start-dev.ps1" -ForegroundColor Cyan
}
