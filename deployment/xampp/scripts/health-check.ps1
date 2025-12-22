#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Health check for ICTServe XAMPP services

.DESCRIPTION
    Checks the status of all ICTServe services and dependencies
#>

function Write-Success { param($Message) Write-Host "✓ $Message" -ForegroundColor Green }
function Write-Failure { param($Message) Write-Host "✗ $Message" -ForegroundColor Red }
function Write-Info { param($Message) Write-Host "ℹ $Message" -ForegroundColor Cyan }
function Write-Section { param($Message) Write-Host "`n=== $Message ===" -ForegroundColor Magenta }

Write-Info "ICTServe XAMPP Health Check"
Write-Info "Date: $(Get-Date -Format 'yyyy-MM-dd HH:mm:ss')`n"

$allHealthy = $true

# Check PHP
Write-Section "PHP Environment"
try {
    $phpVersion = php --version 2>$null | Select-Object -First 1
    Write-Success "PHP: $($phpVersion -split ' ' | Select-Object -First 2 -Join ' ')"
} catch {
    Write-Failure "PHP not found or not accessible"
    $allHealthy = $false
}

# Check Composer
Write-Section "Composer"
try {
    $composerVersion = composer --version 2>$null
    Write-Success "Composer: $($composerVersion -split ' ' | Select-Object -First 2 -Join ' ')"
} catch {
    Write-Failure "Composer not found"
    $allHealthy = $false
}

# Check Node.js
Write-Section "Node.js & NPM"
try {
    $nodeVersion = node --version 2>$null
    $npmVersion = npm --version 2>$null
    Write-Success "Node.js: $nodeVersion"
    Write-Success "npm: $npmVersion"
} catch {
    Write-Failure "Node.js/npm not found"
    $allHealthy = $false
}

# Check MySQL
Write-Section "MySQL Database"
try {
    $mysqlTest = mysql -u root -e "SELECT VERSION();" 2>$null
    if ($mysqlTest) {
        Write-Success "MySQL: Connected"
        
        # Check database exists
        $dbCheck = mysql -u root -e "SHOW DATABASES LIKE 'ictserve';" 2>$null
        if ($dbCheck) {
            Write-Success "Database 'ictserve': Exists"
        } else {
            Write-Failure "Database 'ictserve': Not found"
            $allHealthy = $false
        }
    }
} catch {
    Write-Failure "MySQL: Not accessible"
    Write-Info "  Ensure XAMPP MySQL service is running"
    $allHealthy = $false
}

# Check Redis (optional)
Write-Section "Redis (Optional)"
try {
    $redisTest = redis-cli ping 2>$null
    if ($redisTest -eq "PONG") {
        Write-Success "Redis: Running"
    } else {
        Write-Info "Redis: Not running (optional)"
    }
} catch {
    Write-Info "Redis: Not installed (optional)"
}

# Check Laravel
Write-Section "Laravel Application"
if (Test-Path ".env") {
    Write-Success ".env file: Exists"
    
    # Check APP_KEY
    $envContent = Get-Content ".env" -Raw
    if ($envContent -match "APP_KEY=base64:") {
        Write-Success "APP_KEY: Configured"
    } else {
        Write-Failure "APP_KEY: Not configured"
        Write-Info "  Run: php artisan key:generate"
        $allHealthy = $false
    }
} else {
    Write-Failure ".env file: Not found"
    Write-Info "  Copy deployment/xampp/.env.xampp to .env"
    $allHealthy = $false
}

# Check vendor directory
if (Test-Path "vendor") {
    Write-Success "Composer dependencies: Installed"
} else {
    Write-Failure "Composer dependencies: Not installed"
    Write-Info "  Run: composer install"
    $allHealthy = $false
}

# Check node_modules
if (Test-Path "node_modules") {
    Write-Success "NPM dependencies: Installed"
} else {
    Write-Failure "NPM dependencies: Not installed"
    Write-Info "  Run: npm install"
    $allHealthy = $false
}

# Check storage permissions
Write-Section "Storage Permissions"
$storageWritable = $true
$storageDirs = @("storage/logs", "storage/framework/cache", "bootstrap/cache")

foreach ($dir in $storageDirs) {
    if (Test-Path $dir) {
        try {
            $testFile = Join-Path $dir ".write-test"
            "test" | Out-File $testFile -ErrorAction Stop
            Remove-Item $testFile -ErrorAction SilentlyContinue
            Write-Success "$dir: Writable"
        } catch {
            Write-Failure "$dir: Not writable"
            $storageWritable = $false
            $allHealthy = $false
        }
    } else {
        Write-Failure "$dir: Not found"
        $storageWritable = $false
        $allHealthy = $false
    }
}

# Check running services
Write-Section "Running Services"

# Check Laravel server
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:8000" -TimeoutSec 2 -ErrorAction SilentlyContinue
    Write-Success "Laravel Server: Running (http://127.0.0.1:8000)"
} catch {
    Write-Info "Laravel Server: Not running"
    Write-Info "  Start with: php artisan serve"
}

# Check Vite
try {
    $response = Invoke-WebRequest -Uri "http://127.0.0.1:5173" -TimeoutSec 2 -ErrorAction SilentlyContinue
    Write-Success "Vite Dev Server: Running (http://127.0.0.1:5173)"
} catch {
    Write-Info "Vite Dev Server: Not running"
    Write-Info "  Start with: npm run dev"
}

# Summary
Write-Section "Summary"
if ($allHealthy) {
    Write-Success "All critical checks passed!"
    Write-Info "`nYou can start services with:"
    Write-Info ".\deployment\xampp\scripts\start-services.ps1"
} else {
    Write-Failure "Some checks failed. Please review the issues above."
    Write-Info "`nFor setup help, see:"
    Write-Info ".\deployment\xampp\README.md"
}

Write-Info ""