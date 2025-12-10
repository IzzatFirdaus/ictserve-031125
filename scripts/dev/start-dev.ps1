# ICTServe Development Environment Startup Script
# This script launches all required services in separate PowerShell windows

Write-Host "Starting ICTServe Development Environment..." -ForegroundColor Cyan
Write-Host "=============================================" -ForegroundColor Cyan

# Get the current directory (project root)
# Load project root
$projectRoot = Get-Location

# Source env script to set Node v22 for the current shell (for checks only)
if (Test-Path -Path "$PSScriptRoot\..\..\.env.ps1") {
    . "$PSScriptRoot\..\..\.env.ps1"
}

# Verify active Node version
try {
    $nodeVersion = (& node --version) 2>$null
    $nodeVersionNumeric = ($nodeVersion -replace 'v','')
    $parts = $nodeVersionNumeric.Split('.')
    $major = [int]$parts[0]
    $minor = [int]$parts[1]
    if ($major -lt 22 -or ($major -eq 22 -and $minor -lt 12)) {
        Write-Host "Your active Node.js version is $nodeVersionNumeric. Vite requires Node 20.19+ or 22.12+. The script will still try to enforce Node v22 via .env.ps1 in new windows." -ForegroundColor Yellow
    }
}
catch {
    Write-Host "Unable to detect Node version in this shell - please run '. .\.env.ps1' or use Laragon Node v22." -ForegroundColor Yellow
}

# Function to start a service in a new PowerShell window
function Start-Service {
    param(
        [string]$Title,
        [string]$Command,
        [string]$Color = "Green"
    )

    Write-Host "Starting $Title..." -ForegroundColor $Color

    # Create a new PowerShell window with the command
    # Ensure .env.ps1 is sourced to set Node v22 PATH in new PowerShell windows when running npm/dev commands
    $psCommand = "Set-Location '$projectRoot'; . .\.env.ps1; Write-Host '$Title' -ForegroundColor $Color; Write-Host '==================' -ForegroundColor $Color; $Command"
    Start-Process powershell -ArgumentList @(
        "-NoExit",
        "-Command",
        $psCommand
    )

    Start-Sleep -Milliseconds 500
}

# Helper: Check a TCP port on 127.0.0.1 with retry
function Check-Port {
    param(
        [int]$Port,
        [int]$Attempts = 5,
        [int]$DelaySeconds = 1,
        [string]$ServiceName = $null
    )

    $serviceLabel = if ($ServiceName) { $ServiceName } else { "Port $Port" }
    for ($i = 0; $i -lt $Attempts; $i++) {
        try {
            $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue
            if ($result.TcpTestSucceeded) {
                Write-Host "[OK] $serviceLabel is reachable on 127.0.0.1:$Port" -ForegroundColor Green
                return $true
            }
            else {
                Write-Host "[WAIT] $serviceLabel not reachable (attempt $($i+1)/$Attempts). Retrying in $DelaySeconds sec..." -ForegroundColor Yellow
                Start-Sleep -Seconds $DelaySeconds
            }
        }
        catch {
            Write-Host "[ERROR] Failed to check $serviceLabel on port $Port: $_" -ForegroundColor DarkYellow
            Start-Sleep -Seconds $DelaySeconds
        }
    }

    Write-Host "[WARN] $serviceLabel not reachable after $Attempts attempts" -ForegroundColor DarkYellow
    return $false
}

# 1. Start Redis Server (WSL) if available
Write-Host "`n[1/5] Checking for WSL Redis..." -ForegroundColor Yellow
$wslAvailable = $false
$wslHasSystemctl = $false
$wslHasRedisCli = $false
try {
    if (Get-Command wsl.exe -ErrorAction SilentlyContinue) {
        $wslAvailable = $true
        $sysOut = & wsl.exe -e bash -lc "command -v systemctl >/dev/null && echo 1 || echo 0" 2>$null
        $wslHasSystemctl = ($sysOut -eq '1')
        $cliOut = & wsl.exe -e bash -lc "command -v redis-cli >/dev/null && echo 1 || echo 0" 2>$null
        $wslHasRedisCli = ($cliOut -eq '1')
    }
}
catch {
    Write-Host "WSL detection failed: $_" -ForegroundColor DarkYellow
}

if ($wslAvailable -and $wslHasSystemctl -and $wslHasRedisCli) {
    Write-Host "WSL Redis detected (systemctl + redis-cli). Starting Redis in WSL..." -ForegroundColor Green
    Start-Service -Title "Redis Server (WSL)" -Command "wsl.exe --user root systemctl start redis-server; wsl.exe redis-cli ping; Write-Host 'Redis is running!' -ForegroundColor Green; wsl.exe redis-cli monitor" -Color "Red"
    # Wait / verify Redis (6379)
    Check-Port -Port 6379 -Attempts 10 -DelaySeconds 1 -ServiceName 'Redis (WSL)'
}
else {
    Write-Host "WSL Redis not available or lacks redis-cli/systemctl; skipping WSL Redis start." -ForegroundColor DarkYellow
    Write-Host "Checking Laragon-managed Redis on 127.0.0.1:6379..." -ForegroundColor Yellow
    # Offer to install Redis in WSL if it's missing and we have WSL installed
    if ($wslAvailable -and -not $wslHasRedisCli) {
        $answer = Read-Host "WSL detected but redis-cli not found. Would you like to install Redis inside WSL now? (Y/N)"
        if ($answer -and $answer.ToUpper().StartsWith('Y')) {
            $installer = "$PSScriptRoot\install-wsl-redis.ps1"
            if (Test-Path $installer) {
                Write-Host "Launching WSL Redis installer (PowerShell wrapper)..." -ForegroundColor Cyan
                # Run installer synchronously
                & powershell -NoProfile -ExecutionPolicy Bypass -File $installer -Distro "" -Force
            }
            else {
                Write-Host "Installer script not found: $installer" -ForegroundColor Red
            }
        }
    }
    try {
        $test = Check-Port -Port 6379 -Attempts 5 -DelaySeconds 1 -ServiceName 'Laragon Redis'
        if (-not $test) {
            Write-Host "No Redis detected on 127.0.0.1:6379. If you want WSL Redis, install redis-server and redis-cli in WSL, or start Laragon Redis from Laragon UI." -ForegroundColor Yellow
        }
    }
    catch {
        Write-Host "Unable to check Laragon Redis status: $_" -ForegroundColor DarkYellow
    }
}

Start-Sleep -Seconds 2

# 2. Start Laravel Server
Write-Host "`n[2/5] Starting Laravel Server..." -ForegroundColor Yellow
Start-Service -Title "Laravel Server (Port 8000)" -Command "php artisan serve" -Color "Blue"
Check-Port -Port 8000 -Attempts 10 -DelaySeconds 1 -ServiceName 'Laravel Server'

Start-Sleep -Seconds 2

# 3. Start Laravel Reverb (WebSocket Server)
Write-Host "`n[3/5] Starting Laravel Reverb..." -ForegroundColor Yellow
Start-Service -Title "Laravel Reverb (WebSocket)" -Command "php artisan reverb:start" -Color "Magenta"
Check-Port -Port 6001 -Attempts 10 -DelaySeconds 1 -ServiceName 'Laravel Reverb'

Start-Sleep -Seconds 2

# 4. Start Queue Worker
Write-Host "`n[4/5] Starting Queue Worker..." -ForegroundColor Yellow
Start-Service -Title "Laravel Queue Worker" -Command "php artisan queue:work --tries=3 --timeout=90" -Color "Cyan"
# Verify Queue Worker process exists
function Check-QueueWorker {
    $attempts = 8
    $delay = 1
    for ($i=0; $i -lt $attempts; $i++) {
        try {
            # Look for process whose commandline includes 'queue:work'
            $wmi = Get-WmiObject Win32_Process | Where-Object { $_.CommandLine -and ($_.CommandLine -like '*queue:work*' -or $_.CommandLine -like '*artisan queue:work*') }
            if ($wmi) {
                Write-Host "[OK] Laravel Queue Worker appears to be running" -ForegroundColor Green
                return $true
            }
            else {
                Write-Host "[WAIT] Queue Worker not found (attempt $($i+1)/$attempts). Retrying in $delay sec..." -ForegroundColor Yellow
                Start-Sleep -Seconds $delay
            }
        }
        catch {
            Write-Host "[ERROR] Checking Queue Worker: $_" -ForegroundColor DarkYellow
            Start-Sleep -Seconds $delay
        }
    }
    Write-Host "[WARN] Queue Worker process not detected after $attempts attempts" -ForegroundColor DarkYellow
    return $false
}
Check-QueueWorker

Start-Sleep -Seconds 2

# 5. Start Vite Dev Server
Write-Host "`n[5/5] Starting Vite Dev Server..." -ForegroundColor Yellow
Start-Service -Title "Vite Dev Server (HMR)" -Command "npm run dev" -Color "Green"
Check-Port -Port 5173 -Attempts 10 -DelaySeconds 1 -ServiceName 'Vite Dev Server'

Start-Sleep -Seconds 2

# Summary
Write-Host "`n=============================================" -ForegroundColor Cyan
Write-Host "All services started successfully!" -ForegroundColor Green
Write-Host "=============================================" -ForegroundColor Cyan
Write-Host "`nRunning Services:" -ForegroundColor White
Write-Host "  1. Redis Server (WSL)       - Monitoring mode" -ForegroundColor Red
Write-Host "  2. Laravel Server           - http://127.0.0.1:8000" -ForegroundColor Blue
Write-Host "  3. Laravel Reverb           - ws://127.0.0.1:6001" -ForegroundColor Magenta
Write-Host "  4. Queue Worker             - Processing jobs" -ForegroundColor Cyan
Write-Host "  5. Vite Dev Server          - Hot Module Replacement" -ForegroundColor Green
Write-Host "`nPress any key to stop all services..." -ForegroundColor Yellow

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Stop all services
Write-Host "`nStopping all services..." -ForegroundColor Red
Get-Process | Where-Object { $_.MainWindowTitle -match "Laravel|Vite|Redis|Queue" } | Stop-Process -Force
Write-Host "All services stopped." -ForegroundColor Green
