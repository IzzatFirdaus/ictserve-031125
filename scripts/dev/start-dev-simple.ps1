# ICTServe Simple Development Environment Startup Script
# Focuses on core services: WSL Redis, Laravel, Vite, Queue, Reverb

param(
    [switch]$NoRedis,
    [switch]$InstallRedis
)

Write-Host "ICTServe v3.6.0 Development Environment (Simple)" -ForegroundColor Cyan
Write-Host "=================================================" -ForegroundColor Cyan
Write-Host ""

$projectRoot = Get-Location

# Function to start services in new windows
function Start-DevService {
    param(
        [string]$Title,
        [string]$Command,
        [string]$Color = "White"
    )
    
    Write-Host "Starting $Title..." -ForegroundColor $Color
    
    $script = @"
Set-Location '$projectRoot'
Write-Host '$Title' -ForegroundColor $Color
Write-Host '$('=' * $Title.Length)' -ForegroundColor $Color
Write-Host ''
$Command
"@
    
    Start-Process powershell -ArgumentList "-NoExit", "-Command", $script
    Start-Sleep -Seconds 1
}

# Function to check if port is available
function Test-ServicePort {
    param([int]$Port, [string]$ServiceName)
    
    try {
        $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue
        if ($result.TcpTestSucceeded) {
            Write-Host "[OK] $ServiceName is running on port $Port" -ForegroundColor Green
            return $true
        }
    }
    catch {
        # Silent fail
    }
    
    Write-Host "[WAIT] $ServiceName starting on port $Port..." -ForegroundColor Yellow
    return $false
}

# 1. WSL Redis Setup
if (-not $NoRedis) {
    Write-Host "[1/5] Setting up Redis..." -ForegroundColor Yellow
    
    if (Get-Command wsl.exe -ErrorAction SilentlyContinue) {
        # Check if Redis is installed
        $redisInstalled = & wsl.exe -e bash -c 'command -v redis-server >/dev/null && echo yes || echo no' 2>$null
        
        if ($redisInstalled -eq 'yes') {
            Write-Host "  └─ Redis found in WSL" -ForegroundColor Green
            
            # Check if Redis is running
            $redisRunning = & wsl.exe -e bash -c 'pgrep redis-server >/dev/null && echo yes || echo no' 2>$null
            
            if ($redisRunning -eq 'no') {
                Write-Host "  └─ Starting Redis..." -ForegroundColor Yellow
                & wsl.exe -e bash -c 'redis-server --daemonize yes --port 6379 --bind 127.0.0.1' 2>$null
                Start-Sleep -Seconds 2
            }
            
            # Test Redis connection
            $redisPing = & wsl.exe -e bash -c 'redis-cli ping 2>/dev/null || echo FAILED' 2>$null
            if ($redisPing -eq 'PONG') {
                Write-Host "  └─ Redis is ready!" -ForegroundColor Green
                
                # Start Redis monitor window
                Start-DevService -Title "Redis Monitor" -Command "wsl.exe redis-cli monitor" -Color "Red"
            } else {
                Write-Host "  └─ [WARN] Redis connection failed" -ForegroundColor Yellow
            }
            
        } elseif ($InstallRedis) {
            Write-Host "  └─ Installing Redis in WSL..." -ForegroundColor Cyan
            & wsl.exe -e bash -c 'sudo apt update && sudo apt install -y redis-server' 2>$null
            Write-Host "  └─ Redis installation completed. Please rerun the script." -ForegroundColor Green
            exit 0
            
        } else {
            Write-Host "  └─ Redis not found. Use -InstallRedis to install automatically" -ForegroundColor Yellow
            Write-Host "      Or install manually: wsl.exe sudo apt install redis-server" -ForegroundColor Gray
        }
    } else {
        Write-Host "  └─ WSL not available. Redis will be skipped." -ForegroundColor Yellow
    }
    
    Write-Host ""
}

# 2. Laravel Server
Write-Host "[2/5] Starting Laravel Server..." -ForegroundColor Yellow
Start-DevService -Title "Laravel Server" -Command "php artisan serve --host=127.0.0.1 --port=8000" -Color "Blue"

# Wait and check
Start-Sleep -Seconds 3
Test-ServicePort -Port 8000 -ServiceName "Laravel Server"
Write-Host ""

# 3. Vite Development Server
Write-Host "[3/5] Starting Vite Dev Server..." -ForegroundColor Yellow
Start-DevService -Title "Vite Dev Server" -Command "npm run dev" -Color "Green"

# Wait and check
Start-Sleep -Seconds 3
Test-ServicePort -Port 5173 -ServiceName "Vite Dev Server"
Write-Host ""

# 4. Laravel Queue Worker
Write-Host "[4/5] Starting Queue Worker..." -ForegroundColor Yellow
Start-DevService -Title "Queue Worker" -Command "php artisan queue:work --tries=3 --timeout=90" -Color "Cyan"
Write-Host ""

# 5. Laravel Reverb (WebSocket)
Write-Host "[5/5] Starting Laravel Reverb..." -ForegroundColor Yellow
Start-DevService -Title "Laravel Reverb" -Command "php artisan reverb:start --host=127.0.0.1 --port=6001" -Color "Magenta"

# Wait and check
Start-Sleep -Seconds 3
Test-ServicePort -Port 6001 -ServiceName "Laravel Reverb"
Write-Host ""

# Summary
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "ICTServe Development Environment Ready!" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host ""

Write-Host "Services:" -ForegroundColor White
if (-not $NoRedis) {
    Write-Host "  • Redis Server     - Cache & Sessions (127.0.0.1:6379)" -ForegroundColor Red
}
Write-Host "  • Laravel Server   - Application (http://127.0.0.1:8000)" -ForegroundColor Blue
Write-Host "  • Vite Dev Server  - Assets & HMR (127.0.0.1:5173)" -ForegroundColor Green
Write-Host "  • Queue Worker     - Background Jobs" -ForegroundColor Cyan
Write-Host "  • Laravel Reverb   - WebSocket (ws://127.0.0.1:6001)" -ForegroundColor Magenta

Write-Host ""
Write-Host "Quick Access:" -ForegroundColor White
Write-Host "  • Application:  http://127.0.0.1:8000" -ForegroundColor Gray
Write-Host "  • Admin Panel:  http://127.0.0.1:8000/admin" -ForegroundColor Gray
Write-Host "  • Helpdesk:     http://127.0.0.1:8000/helpdesk/create" -ForegroundColor Gray

Write-Host ""
Write-Host "Development Commands:" -ForegroundColor White
Write-Host "  • Run Tests:    php artisan test" -ForegroundColor Gray
Write-Host "  • Format Code:  vendor/bin/pint" -ForegroundColor Gray
Write-Host "  • Build Assets: npm run build" -ForegroundColor Gray

Write-Host ""
Write-Host "Press any key to open the application..." -ForegroundColor Yellow
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Open browser
try {
    Start-Process "http://127.0.0.1:8000"
    Write-Host "Browser opened to http://127.0.0.1:8000" -ForegroundColor Green
}
catch {
    Write-Host "Could not open browser automatically" -ForegroundColor Yellow
    Write-Host "Please visit: http://127.0.0.1:8000" -ForegroundColor Cyan
}

Write-Host ""
Write-Host "To stop all services, close the individual terminal windows." -ForegroundColor Gray
Write-Host "Happy coding! 🚀" -ForegroundColor Green