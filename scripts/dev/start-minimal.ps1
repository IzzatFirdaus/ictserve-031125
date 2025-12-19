# ICTServe v3.6.0 - Minimal Development Startup
# This script starts only the essential services

Write-Host "ICTServe v3.6.0 - Starting Development Environment" -ForegroundColor Cyan
Write-Host "===================================================" -ForegroundColor Cyan
Write-Host ""

$projectRoot = Get-Location

# Check PHP
Write-Host "[CHECK] PHP..." -ForegroundColor Yellow
try {
    $phpVersion = php --version | Select-String "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
    Write-Host "  OK - PHP $phpVersion" -ForegroundColor Green
}
catch {
    Write-Host "  ERROR - PHP not found" -ForegroundColor Red
    exit 1
}

# Check Laravel
Write-Host "[CHECK] Laravel..." -ForegroundColor Yellow
try {
    $laravelVersion = php artisan --version | Select-String "Laravel Framework (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value }
    Write-Host "  OK - Laravel $laravelVersion" -ForegroundColor Green
}
catch {
    Write-Host "  ERROR - Laravel not found" -ForegroundColor Red
    exit 1
}

# Check .env
Write-Host "[CHECK] Environment..." -ForegroundColor Yellow
if (Test-Path ".env") {
    Write-Host "  OK - .env file exists" -ForegroundColor Green
} else {
    Write-Host "  WARN - Creating .env from .env.example" -ForegroundColor Yellow
    Copy-Item ".env.example" ".env"
    php artisan key:generate
}

Write-Host ""
Write-Host "Starting services..." -ForegroundColor Cyan
Write-Host ""

# Start Laravel Server
Write-Host "[1/1] Laravel Application Server" -ForegroundColor Yellow
$laravelCmd = "Set-Location '$projectRoot'; Write-Host 'Laravel Server Starting...' -ForegroundColor Blue; php artisan serve --host=127.0.0.1 --port=8000"
Start-Process powershell -ArgumentList "-NoExit", "-Command", $laravelCmd

Write-Host "  Waiting for Laravel server..." -ForegroundColor Gray
Start-Sleep -Seconds 3

# Check if Laravel is running
$attempts = 0
$maxAttempts = 15
$running = $false

while ($attempts -lt $maxAttempts -and -not $running) {
    try {
        $result = Test-NetConnection -ComputerName 127.0.0.1 -Port 8000 -WarningAction SilentlyContinue
        if ($result.TcpTestSucceeded) {
            Write-Host "  OK - Laravel server is running" -ForegroundColor Green
            $running = $true
        } else {
            $attempts++
            Write-Host "  WAIT - Attempt $attempts of $maxAttempts" -ForegroundColor Yellow
            Start-Sleep -Seconds 1
        }
    }
    catch {
        $attempts++
        Start-Sleep -Seconds 1
    }
}

if (-not $running) {
    Write-Host "  WARN - Laravel server may not be ready" -ForegroundColor Yellow
}

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "ICTServe Development Environment Ready" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""
Write-Host "Services:" -ForegroundColor White
Write-Host "  Laravel Server: http://127.0.0.1:8000" -ForegroundColor Blue
Write-Host ""
Write-Host "Quick Links:" -ForegroundColor White
Write-Host "  Application:  http://127.0.0.1:8000" -ForegroundColor Gray
Write-Host "  Admin Panel:  http://127.0.0.1:8000/admin" -ForegroundColor Gray
Write-Host "  Telescope:    http://127.0.0.1:8000/telescope" -ForegroundColor Gray
Write-Host ""
Write-Host "Commands:" -ForegroundColor White
Write-Host "  Run Tests:    php artisan test" -ForegroundColor Gray
Write-Host "  Format Code:  vendor/bin/pint" -ForegroundColor Gray
Write-Host "  Build Assets: npm run build" -ForegroundColor Gray
Write-Host ""
Write-Host "Press any key to stop services..." -ForegroundColor Yellow

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Shutdown
Write-Host ""
Write-Host "Stopping services..." -ForegroundColor Red

try {
    Get-Process | Where-Object { $_.MainWindowTitle -match "Laravel" } | Stop-Process -Force -ErrorAction SilentlyContinue
}
catch {
    # Silent cleanup
}

Write-Host "Services stopped." -ForegroundColor Green
Write-Host ""
