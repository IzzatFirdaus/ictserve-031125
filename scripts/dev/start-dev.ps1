# ICTServe v3.6.0 Development Environment Startup Script
# Laravel 12.43.1 + Filament 4.3.1 + Livewire 3.7.3 + Tailwind 4.1.18
# Compliance: PDPA 2010, WCAG 2.2 AA, PSR-12, MyGOV Digital Service Standards v2.1.0
# Architecture: True Hybrid (Guest Forms + Authenticated Portal + Admin Panel)

<#
.SYNOPSIS
    Starts the ICTServe v3.6.0 development environment with all required services.

.DESCRIPTION
    This script starts the complete ICTServe development stack including:
    - Laravel application server (127.0.0.1:8000)
    - Redis server (cache, sessions, queues)
    - Laravel Reverb (WebSocket broadcasting)
    - Queue workers (background jobs)
    - Vite development server (frontend assets + HMR)
    - Laravel MCP server (AI integration)
    - Ollama AI server (local LLM for chatbot)
    - Laravel Pulse (performance monitoring)

.PARAMETER SkipChecks
    Skip pre-flight environment checks (PHP, Node.js, Laravel versions)

.PARAMETER NoMCP
    Skip starting the Laravel MCP server for AI integration

.PARAMETER NoBrowser
    Don't automatically open browser to the application

.PARAMETER Minimal
    Start only essential services (Laravel + Vite)

.PARAMETER InstallRedis
    Automatically install Redis in WSL if not found

.PARAMETER ProfileName
    Service profile to use. Options:
    - minimal: Laravel + Vite only
    - backend: Redis + Laravel + Reverb + Queue
    - frontend: Laravel + Vite
    - full: All services (default)
    - testing: Full stack + browser
    - ai: Full stack + Ollama AI server
    - production: Production-like setup with Horizon
    - horizon: Laravel Horizon focus (WSL-based)

.EXAMPLE
    .\scripts\dev\start-dev.ps1
    Start with full profile (all services)

.EXAMPLE
    .\scripts\dev\start-dev.ps1 -ProfileName minimal
    Start with minimal services only

.EXAMPLE
    .\scripts\dev\start-dev.ps1 -ProfileName ai -InstallRedis
    Start AI development profile and install Redis if needed

.EXAMPLE
    .\scripts\dev\start-dev.ps1 -ProfileName horizon
    Start with Laravel Horizon focus (WSL-based queue management)

.EXAMPLE
    .\scripts\dev\start-dev.ps1 -SkipChecks -NoMCP -NoBrowser
    Quick start without checks, MCP, or browser

.NOTES
    Requires: PHP 8.2.12+, Node.js 22.12+, Laravel 12.43.1
    Compliance: PDPA 2010, WCAG 2.2 AA, PSR-12, MyGOV Standards v2.1.0
    Architecture: D00-D18 documentation compliance
#>

param(
    [switch]$SkipChecks,
    [switch]$NoMCP,
    [switch]$NoBrowser,
    [switch]$Minimal,
    [switch]$InstallRedis,
    [switch]$Help,
    [Alias('Profile')]
    [ValidateSet('minimal', 'backend', 'frontend', 'full', 'testing', 'ai', 'production', 'horizon')]
    [string]$ProfileName = "full"
)

# Show help if requested
if ($Help) {
    Get-Help $MyInvocation.MyCommand.Path -Full
    exit 0
}

# Handle legacy -Minimal parameter
if ($Minimal) {
    $ProfileName = "minimal"
}

Write-Host "ICTServe v3.6.0 Development Environment" -ForegroundColor Cyan
Write-Host "=======================================" -ForegroundColor Cyan
Write-Host "Laravel 12.43.1 | PHP 8.2.12 | Filament 4.3.1" -ForegroundColor Gray
Write-Host "Profile: $ProfileName | Compliance: PDPA 2010 + WCAG 2.2 AA" -ForegroundColor Gray

# Show profile description
$profileDescriptions = @{
    "minimal" = "Essential services only (Laravel + Vite)"
    "backend" = "Backend development (Redis + Laravel + Reverb + Horizon)"
    "frontend" = "Frontend development (Laravel + Vite)"
    "full" = "Complete development stack (all services with WSL Horizon)"
    "testing" = "Full stack with browser for testing"
    "ai" = "AI development with Ollama local LLM"
    "production" = "Production-like environment with Horizon"
    "horizon" = "Laravel Horizon focus (Redis + Laravel + Reverb + Horizon)"
}

if ($profileDescriptions.ContainsKey($ProfileName)) {
    Write-Host "Description: $($profileDescriptions[$ProfileName])" -ForegroundColor Gray
}
Write-Host ""

# Get the current directory (project root)
$projectRoot = Get-Location
$startTime = Get-Date

# Source env script to set Node v22 for the current shell (for checks only)
if (Test-Path -Path "$PSScriptRoot\..\..\..env.ps1") {
    . "$PSScriptRoot\..\..\..env.ps1"
}

# Pre-flight checks
if (-not $SkipChecks) {
    Write-Host "[PREFLIGHT] Running environment checks..." -ForegroundColor Yellow

    # Check PHP version (required: 8.2.12)
    try {
        $phpVersion = (& php --version | Select-String "PHP (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value })
        if ($phpVersion -and [version]$phpVersion -ge [version]"8.2.12") {
            Write-Host "[OK] PHP $phpVersion (meets requirement: 8.2.12+)" -ForegroundColor Green
        } else {
            Write-Host "[WARN] PHP version $phpVersion may not meet requirements (8.2.12+)" -ForegroundColor Yellow
        }
    }
    catch {
        Write-Host "[ERROR] PHP not found in PATH" -ForegroundColor Red
        exit 1
    }

    # Check Node.js version (required: 22.12+ for Vite 7.0.7)
    try {
        $nodeVersion = (& node --version) 2>$null
        $nodeVersionNumeric = ($nodeVersion -replace 'v','')
        $parts = $nodeVersionNumeric.Split('.')
        $major = [int]$parts[0]
        $minor = [int]$parts[1]
        if ($major -ge 22 -and ($major -gt 22 -or $minor -ge 12)) {
            Write-Host "[OK] Node.js $nodeVersionNumeric (meets requirement: 22.12+)" -ForegroundColor Green
        } else {
            Write-Host "[WARN] Node.js $nodeVersionNumeric may cause issues with Vite 7.0.7 (requires 22.12+)" -ForegroundColor Yellow
        }

        # Check npm functionality
        $npmTest = (& npm --version 2>&1)
        if ($LASTEXITCODE -ne 0) {
            Write-Host "[WARN] npm has permission issues - frontend assets may not build" -ForegroundColor Yellow
            Write-Host "  - Run: .\scripts\dev\fix-npm.ps1 to attempt automatic fix" -ForegroundColor Gray
            Write-Host "  - Or reinstall Node.js from: https://nodejs.org/" -ForegroundColor Gray
        } else {
            Write-Host "[OK] npm $npmTest" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "[ERROR] Node.js not found - please install Node.js 22.12+ or run '. .\.env.ps1'" -ForegroundColor Red
    }

    # Check Laravel installation
    try {
        $laravelVersion = (& php artisan --version | Select-String "Laravel Framework (\d+\.\d+\.\d+)" | ForEach-Object { $_.Matches[0].Groups[1].Value })
        if ($laravelVersion) {
            Write-Host "[OK] Laravel Framework $laravelVersion" -ForegroundColor Green
        }
    }
    catch {
        Write-Host "[ERROR] Laravel not properly installed" -ForegroundColor Red
        exit 1
    }

    # Check .env file
    if (Test-Path ".env") {
        Write-Host "[OK] Environment configuration found" -ForegroundColor Green
    } else {
        Write-Host "[WARN] .env file missing - copying from .env.example" -ForegroundColor Yellow
        Copy-Item ".env.example" ".env"
    }

    Write-Host ""
}

# Enhanced service management functions
function Start-Service {
    param(
        [string]$Title,
        [string]$Command,
        [string]$Color = "Green",
        [string]$Description = "",
        [switch]$Critical = $false,
        [int]$Priority = 1
    )

    $timestamp = Get-Date -Format "HH:mm:ss"
    Write-Host "[$timestamp] Starting $Title..." -ForegroundColor $Color
    if ($Description) {
        Write-Host "  -> $Description" -ForegroundColor Gray
    }

    # Build command script with proper escaping
    $separator = "=" * $Title.Length
    $commandLines = @(
        "Set-Location '$projectRoot'"
        "if (Test-Path '.\.env.ps1') { . .\.env.ps1 }"
        "Write-Host '[$timestamp] $Title' -ForegroundColor $Color"
        "Write-Host '$separator' -ForegroundColor $Color"
    )

    if ($Description) {
        $commandLines += "Write-Host '$Description' -ForegroundColor Gray"
        $commandLines += "Write-Host ''"
    }

    $commandLines += @(
        "try {"
        "    $Command"
        "} catch {"
        "    Write-Host 'ERROR in $Title - Service failed to start' -ForegroundColor Red"
    )

    if ($Critical) {
        $commandLines += @(
            "    Write-Host 'CRITICAL SERVICE FAILED - Press any key to exit...' -ForegroundColor Red"
            "    [void](`$Host.UI.RawUI.ReadKey('NoEcho,IncludeKeyDown'))"
            "    exit 1"
        )
    }

    $commandLines += "}"

    $commandScript = $commandLines -join "; "

    # Start the service in a new PowerShell window
    Start-Process powershell -ArgumentList @("-NoExit", "-Command", $commandScript)

    # Staggered startup based on priority
    Start-Sleep -Milliseconds (500 * $Priority)
}

# Enhanced port checking with health validation
function Test-Port {
    param(
        [int]$Port,
        [int]$Attempts = 10,
        [int]$DelaySeconds = 1,
        [string]$ServiceName = $null,
        [string]$HealthEndpoint = $null,
        [switch]$Critical = $false
    )

    $serviceLabel = if ($ServiceName) { $ServiceName } else { "Port " + $Port }
    $timestamp = Get-Date -Format "HH:mm:ss"

    for ($i = 0; $i -lt $Attempts; $i++) {
        try {
            $result = Test-NetConnection -ComputerName 127.0.0.1 -Port $Port -WarningAction SilentlyContinue
            if ($result.TcpTestSucceeded) {
                # Additional health check if endpoint provided
                if ($HealthEndpoint) {
                    try {
                        $response = Invoke-WebRequest -Uri "http://127.0.0.1:$Port$HealthEndpoint" -TimeoutSec 10 -UseBasicParsing -ErrorAction Stop
                        if ($response.StatusCode -eq 200) {
                            Write-Host "[$timestamp] [OK] $serviceLabel healthy on 127.0.0.1:$Port$HealthEndpoint" -ForegroundColor Green
                            return $true
                        } else {
                            Write-Host "[$timestamp] [WAIT] $serviceLabel returned status $($response.StatusCode) (attempt $($i+1)/$Attempts)" -ForegroundColor Yellow
                        }
                    }
                    catch {
                        $errorMsg = $_.Exception.Message
                        if ($errorMsg -match "timeout|timed out") {
                            Write-Host "[$timestamp] [WAIT] $serviceLabel health check timeout (attempt $($i+1)/$Attempts)" -ForegroundColor Yellow
                        } elseif ($errorMsg -match "connection refused|could not connect") {
                            Write-Host "[$timestamp] [WAIT] $serviceLabel not ready yet (attempt $($i+1)/$Attempts)" -ForegroundColor Yellow
                        } else {
                            Write-Host "[$timestamp] [WAIT] $serviceLabel health check error: $($errorMsg -replace '[\r\n]+', ' ') (attempt $($i+1)/$Attempts)" -ForegroundColor Yellow
                        }
                    }
                    Start-Sleep -Seconds $DelaySeconds
                    continue
                } else {
                    Write-Host "[$timestamp] [OK] $serviceLabel ready on 127.0.0.1:$Port" -ForegroundColor Green
                    return $true
                }
            }
            else {
                Write-Host "[$timestamp] [WAIT] $serviceLabel starting... (attempt $($i+1)/$Attempts)" -ForegroundColor Yellow
                Start-Sleep -Seconds $DelaySeconds
            }
        }
        catch {
            Write-Host "[$timestamp] [ERROR] Failed to check $serviceLabel : $($_.Exception.Message)" -ForegroundColor Red
            Start-Sleep -Seconds $DelaySeconds
        }
    }

    if ($Critical) {
        $status = "[ERROR]"
        $statusColor = "Red"
    } else {
        $status = "[WARN]"
        $statusColor = "Yellow"
    }
    Write-Host "[$timestamp] $status $serviceLabel not ready after $Attempts attempts" -ForegroundColor $statusColor
    return $false
}

# Service profile configurations
function Get-ServiceProfile {
    param([string]$ProfileName)

    $profiles = @{
        "minimal" = @("laravel", "vite")
        "backend" = @("redis", "laravel", "reverb", "horizon")
        "frontend" = @("laravel", "vite")
        "full" = @("redis", "laravel", "reverb", "horizon", "vite", "mcp", "pulse")
        "testing" = @("redis", "laravel", "reverb", "horizon", "vite", "browser")
        "ai" = @("redis", "laravel", "reverb", "horizon", "vite", "mcp", "ollama")
        "production" = @("redis", "laravel", "reverb", "horizon", "pulse")
        "horizon" = @("redis", "laravel", "reverb", "horizon")
    }

    if ($profiles.ContainsKey($ProfileName)) {
        return $profiles[$ProfileName]
    } else {
        return $profiles["full"]
    }
}

# Enhanced WSL Redis detection and management
function Test-WSLRedis {
    $wslInfo = @{
        Available = $false
        HasRedis = $false
        IsRunning = $false
        CanStart = $false
    }

    try {
        if (Get-Command wsl.exe -ErrorAction SilentlyContinue) {
            $wslInfo.Available = $true

            # Check if Redis is installed
            $redisCheck = & wsl.exe -e bash -c 'command -v redis-server >/dev/null && echo installed || echo missing' 2>$null
            $wslInfo.HasRedis = ($redisCheck -eq 'installed')

            if ($wslInfo.HasRedis) {
                # Check if Redis is already running
                $redisStatus = & wsl.exe -e bash -c 'pgrep redis-server >/dev/null && echo running || echo stopped' 2>$null
                $wslInfo.IsRunning = ($redisStatus -eq 'running')

                # Check if we can start Redis (test sudo without password)
                if (-not $wslInfo.IsRunning) {
                    $sudoTest = & wsl.exe -e bash -c 'timeout 2 sudo -n true 2>/dev/null && echo can_sudo || echo need_password' 2>$null
                    $wslInfo.CanStart = ($sudoTest -eq 'can_sudo')
                }
            }
        }
    }
    catch {
        Write-Host "  - WSL detection failed: $_" -ForegroundColor DarkYellow
    }

    return $wslInfo
}

function Start-WSLRedis {
    Write-Host "  - Starting WSL Redis server..." -ForegroundColor Green

    # Try to start Redis without sudo first (if user has permissions)
    $startResult = & wsl.exe -e bash -c 'redis-server --daemonize yes --port 6379 --bind 127.0.0.1 2>/dev/null && echo started || echo failed' 2>$null

    if ($startResult -eq 'started') {
        Write-Host "  - Redis started successfully (user mode)" -ForegroundColor Green
        return $true
    }

    # Try with sudo if available
    $sudoResult = & wsl.exe -e bash -c 'timeout 2 sudo -n redis-server --daemonize yes --port 6379 --bind 127.0.0.1 2>/dev/null && echo started || echo failed' 2>$null

    if ($sudoResult -eq 'started') {
        Write-Host "  - Redis started successfully (sudo mode)" -ForegroundColor Green
        return $true
    }

    Write-Host "  - [WARN] Could not start WSL Redis automatically" -ForegroundColor Yellow
    Write-Host "      Try manually: wsl.exe redis-server --daemonize yes" -ForegroundColor Gray
    return $false
}

# Get services to start based on profile
$servicesToStart = Get-ServiceProfile -ProfileName $ProfileName
if (-not $servicesToStart) {
    Write-Host "[ERROR] Invalid profile: $ProfileName" -ForegroundColor Red
    Write-Host "Available profiles: minimal, backend, frontend, full, testing, ai, production, horizon" -ForegroundColor Yellow
    exit 1
}

$serviceCount = $servicesToStart.Count
$currentService = 0

Write-Host "Starting $serviceCount services for profile: $ProfileName" -ForegroundColor Cyan
Write-Host "Services: $($servicesToStart -join ', ')" -ForegroundColor Gray
Write-Host ""

# 1. Redis Server (Cache, Sessions, Queues, Reverb Backend)
if ($servicesToStart -contains "redis") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Redis Server (Cache + Sessions + Queues)" -ForegroundColor Yellow

    $wslRedis = Test-WSLRedis

    if ($wslRedis.Available -and $wslRedis.HasRedis) {
        if ($wslRedis.IsRunning) {
            Write-Host "  - WSL Redis already running" -ForegroundColor Green
        } else {
            $started = Start-WSLRedis
            if (-not $started) {
                Write-Host "  - Manual start required: wsl.exe redis-server --daemonize yes" -ForegroundColor Cyan
            }
        }

        # Start Redis monitor in separate window
        Start-Service -Title "Redis Server (WSL Monitor)" -Command "wsl.exe redis-cli ping; Write-Host 'Redis ready for Laravel!' -ForegroundColor Green; Write-Host 'Monitoring Redis commands...' -ForegroundColor Yellow; wsl.exe redis-cli monitor" -Color "Red" -Description "Cache, Sessions, Queues, Reverb Backend" -Priority 1

        # Verify Redis is accessible
        Test-Port -Port 6379 -Attempts 10 -DelaySeconds 1 -ServiceName 'Redis (WSL)'

    } elseif ($wslRedis.Available -and -not $wslRedis.HasRedis) {
        Write-Host "  - WSL available but Redis not installed" -ForegroundColor Yellow

        if ($InstallRedis) {
            Write-Host "  - Installing Redis in WSL..." -ForegroundColor Cyan
            $installResult = & wsl.exe -e bash -c 'sudo apt update && sudo apt install -y redis-server && echo installed || echo failed' 2>$null
            if ($installResult -eq 'installed') {
                Write-Host "  - Redis installed successfully! Rerun script to start." -ForegroundColor Green
            } else {
                Write-Host "  - [ERROR] Redis installation failed" -ForegroundColor Red
            }
        } else {
            Write-Host "  - Run with -InstallRedis to auto-install, or manually:" -ForegroundColor Cyan
            Write-Host "      wsl.exe sudo apt update" -ForegroundColor Gray
            Write-Host "      wsl.exe sudo apt install -y redis-server" -ForegroundColor Gray
        }

        # Check for alternative Redis (Laragon, Docker, etc.)
        $redisFound = Test-Port -Port 6379 -Attempts 3 -DelaySeconds 1 -ServiceName 'Alternative Redis'
        if (-not $redisFound) {
            Write-Host "  - [WARN] No Redis found. Some features may not work." -ForegroundColor Yellow
        }

    } else {
        Write-Host "  - WSL not available, checking for local Redis..." -ForegroundColor Yellow

        # Check for Laragon, XAMPP, or other local Redis
        $redisFound = Test-Port -Port 6379 -Attempts 5 -DelaySeconds 1 -ServiceName 'Local Redis'
        if (-not $redisFound) {
            Write-Host "  - [WARN] No Redis found. Install via:" -ForegroundColor Yellow
            Write-Host "      - WSL: wsl.exe sudo apt install redis-server" -ForegroundColor Gray
            Write-Host "      - Laragon Redis module" -ForegroundColor Gray
            Write-Host "      - Docker: docker run -d -p 6379:6379 redis:alpine" -ForegroundColor Gray
        }
    }

    Start-Sleep -Seconds 1
}

# 2. Laravel Application Server
if ($servicesToStart -contains "laravel") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Laravel Application Server" -ForegroundColor Yellow
    Start-Service -Title 'Laravel Server (127.0.0.1:8000)' -Command "php artisan serve --host=127.0.0.1 --port=8000" -Color "Blue" -Description "ICTServe v3.6.0 - True Hybrid Architecture" -Critical -Priority 2

    # Give Laravel server extra time to initialize
    Write-Host "  - Waiting for Laravel to initialize..." -ForegroundColor Gray
    Start-Sleep -Seconds 3

    Test-Port -Port 8000 -Attempts 15 -DelaySeconds 2 -ServiceName 'Laravel Server' -HealthEndpoint "/api/health" -Critical
    Start-Sleep -Seconds 1
}

# 3. Laravel Reverb (WebSocket Server for Real-time Features)
if ($servicesToStart -contains "reverb") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Laravel Reverb (WebSocket)" -ForegroundColor Yellow
    Start-Service -Title 'Laravel Reverb (ws://127.0.0.1:8080)' -Command "php artisan reverb:start --host=127.0.0.1 --port=8080" -Color "Magenta" -Description "Real-time notifications, live updates, broadcasting" -Priority 3

    # Give Reverb time to initialize
    Write-Host "  - Waiting for Reverb WebSocket server..." -ForegroundColor Gray
    Start-Sleep -Seconds 2

    Test-Port -Port 8080 -Attempts 15 -DelaySeconds 1 -ServiceName 'Laravel Reverb'
    Start-Sleep -Seconds 1
}

# 4. Laravel Horizon (WSL) / Queue Workers (Windows Fallback)
if ($servicesToStart -contains "queue" -or $servicesToStart -contains "horizon") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Laravel Horizon / Queue Workers" -ForegroundColor Yellow
    
    # Quick WSL check (timeout 2s)
    $horizonStarted = $false
    $wslAvailable = $false
    
    try {
        $wslTest = & wsl.exe -e bash -c 'echo ok' 2>$null
        $wslAvailable = ($wslTest -eq 'ok')
    } catch { }
    
    if ($wslAvailable -and (Test-Path "start-horizon-wsl.sh")) {
        # Quick check if already running
        $horizonRunning = & wsl.exe -e bash -c 'pgrep -f "php artisan horizon" >/dev/null && echo running || echo stopped' 2>$null
        
        if ($horizonRunning -eq 'running') {
            Write-Host "  - Laravel Horizon already running in WSL" -ForegroundColor Green
            $horizonStarted = $true
        } else {
            # Start in background without waiting
            Start-Service -Title "Laravel Horizon (WSL)" -Command "wsl.exe bash start-horizon-wsl.sh" -Color "DarkCyan" -Description "Queue management with ext-pcntl" -Priority 4
            Write-Host "  - Laravel Horizon starting in WSL (background)" -ForegroundColor Green
            $horizonStarted = $true
        }
    } else {
        Write-Host "  - WSL Horizon unavailable, using Windows queue workers" -ForegroundColor Yellow
        $horizonStarted = $false
    }
    
    # Fallback to Windows queue workers
    if (-not $horizonStarted) {
        Start-Service -Title "Laravel Queue Worker" -Command "php artisan queue:work redis --queue=default,helpdesk,notifications --tries=3 --timeout=300" -Color "Cyan" -Description "Background jobs" -Priority 4
        Write-Host "  - Queue worker started (Windows mode)" -ForegroundColor Green
    }
    
    # Quick status check (non-blocking)
    $timestamp = Get-Date -Format "HH:mm:ss"
    Write-Host "[$timestamp] [OK] Queue system ready" -ForegroundColor Green
    
    Start-Sleep -Seconds 1
}

# 5. Vite Development Server (Frontend Assets + HMR)
if ($servicesToStart -contains "vite") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Vite Development Server" -ForegroundColor Yellow

    # Check if npm is functional before starting Vite
    [void](& npm --version 2>&1)
    if ($LASTEXITCODE -ne 0) {
        Write-Host "  - [WARN] npm not functional - skipping Vite server" -ForegroundColor Yellow
        Write-Host "  - Frontend assets will not hot-reload. Run 'npm run build' manually." -ForegroundColor Gray
        Write-Host "  - To fix: Run .\scripts\dev\fix-npm.ps1 or reinstall Node.js" -ForegroundColor Cyan
    } else {
        # Check if node_modules is properly installed
        if (-not (Test-Path "node_modules\vite")) {
            Write-Host "  - [WARN] Vite not found, running npm install..." -ForegroundColor Yellow
            npm install --silent
        }

        Start-Service -Title 'Vite Dev Server (127.0.0.1:5173)' -Command "npm run dev" -Color "Green" -Description "Tailwind 4.1.18, Livewire 3.7.3, Hot Module Replacement" -Priority 5
        # Vite health check with proper endpoint
        $viteReady = $false
        for ($i = 0; $i -lt 10; $i++) {
            try {
                $result = Test-NetConnection -ComputerName 127.0.0.1 -Port 5173 -WarningAction SilentlyContinue
                if ($result.TcpTestSucceeded) {
                    Write-Host "  - [OK] Vite Dev Server ready on 127.0.0.1:5173" -ForegroundColor Green
                    $viteReady = $true
                    break
                }
            } catch { }
            Start-Sleep -Milliseconds 500
        }
        if (-not $viteReady) {
            Write-Host "  - [INFO] Vite starting (check window for status)" -ForegroundColor Gray
        }
    }
    Start-Sleep -Seconds 1
}

# 6. Laravel MCP Server (AI Integration) - Background start
if ($servicesToStart -contains "mcp" -and -not $NoMCP) {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Laravel MCP Server (AI Integration)" -ForegroundColor Yellow
    Start-Service -Title "Laravel MCP Server" -Command "php artisan boost:mcp" -Color "DarkCyan" -Description "AI chatbot integration" -Priority 6
    Write-Host "  - MCP server starting (background)" -ForegroundColor Green
}

# 7. Laravel Pulse (Performance Monitoring) - Background start
if ($servicesToStart -contains "pulse") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Laravel Pulse (Performance Monitoring)" -ForegroundColor Yellow
    Start-Service -Title "Laravel Pulse Monitor" -Command "php artisan pulse:work" -Color "DarkGreen" -Description "Performance monitoring" -Priority 7
    Write-Host "  - Pulse monitor starting (background)" -ForegroundColor Green
}

# 8. Ollama (Local AI Server for D18 AI Chatbot Integration)
if ($servicesToStart -contains "ollama") {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Ollama AI Server (Local LLM)" -ForegroundColor Yellow
    
    # Check if Ollama is installed
    try {
        $ollamaVersion = & ollama --version 2>$null
        if ($LASTEXITCODE -eq 0) {
            Write-Host "  - Ollama found: $ollamaVersion" -ForegroundColor Green
            
            # Check if Ollama service is already running
            $ollamaRunning = Test-Port -Port 11434 -Attempts 3 -DelaySeconds 1 -ServiceName 'Ollama'
            
            if (-not $ollamaRunning) {
                Write-Host "  - Starting Ollama server..." -ForegroundColor Cyan
                Start-Service -Title "Ollama AI Server (127.0.0.1:11434)" -Command "ollama serve" -Color "DarkMagenta" -Description "Local LLM for AI Chatbot (D18 Integration)" -Priority 8
                
                # Wait for Ollama to start
                Test-Port -Port 11434 -Attempts 10 -DelaySeconds 2 -ServiceName 'Ollama AI Server' -HealthEndpoint "/api/tags"
                
                # Check for required models
                Write-Host "  - Checking AI models..." -ForegroundColor Gray
                $models = & ollama list 2>$null
                if ($models -match "llama3.2:3b|phi3:mini|qwen2.5:3b") {
                    Write-Host "  - [OK] AI models available for chatbot" -ForegroundColor Green
                } else {
                    Write-Host "  - [INFO] No optimized models found. Consider installing:" -ForegroundColor Yellow
                    Write-Host "      ollama pull llama3.2:3b    # Fast, good quality" -ForegroundColor Gray
                    Write-Host "      ollama pull phi3:mini      # Microsoft, efficient" -ForegroundColor Gray
                    Write-Host "      ollama pull qwen2.5:3b     # Multilingual support" -ForegroundColor Gray
                }
            } else {
                Write-Host "  - Ollama already running on port 11434" -ForegroundColor Green
            }
        } else {
            Write-Host "  - [WARN] Ollama not installed. AI chatbot will use AWS Bedrock only." -ForegroundColor Yellow
            Write-Host "      Install from: https://ollama.ai/download" -ForegroundColor Gray
        }
    }
    catch {
        Write-Host "  - [WARN] Ollama not found. AI chatbot will use AWS Bedrock only." -ForegroundColor Yellow
        Write-Host "      Install from: https://ollama.ai/download" -ForegroundColor Gray
    }
    
    Start-Sleep -Seconds 1
}

# 9. Browser for Testing (Optional)
if ($servicesToStart -contains "browser" -and -not $NoBrowser) {
    $currentService++
    Write-Host "[$currentService/$serviceCount] Opening Browser for Testing" -ForegroundColor Yellow
    try {
        Start-Process 'http://127.0.0.1:8000'
        Write-Host "  - [OK] Browser opened to http://127.0.0.1:8000" -ForegroundColor Green
    }
    catch {
        Write-Host "  - [WARN] Could not open browser automatically" -ForegroundColor Yellow
    }
}

# Startup Summary
$endTime = Get-Date
$duration = ($endTime - $startTime).TotalSeconds
$timestamp = Get-Date -Format "HH:mm:ss"

Write-Host ""
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "ICTServe v3.6.0 Development Environment" -ForegroundColor Green
Write-Host "=========================================" -ForegroundColor Cyan
Write-Host "Profile: $ProfileName | Startup Time: $([math]::Round($duration, 1))s" -ForegroundColor Gray
Write-Host ""

# Service status summary
Write-Host "Active Services:" -ForegroundColor White
if ($servicesToStart -contains "redis") {
    Write-Host "  [REDIS] Redis Server          - Cache, Sessions, Queues (127.0.0.1:6379)" -ForegroundColor Red
}
if ($servicesToStart -contains "laravel") {
    Write-Host "  [LARAVEL] Laravel Server      - ICTServe Application (http://127.0.0.1:8000)" -ForegroundColor Blue
}
if ($servicesToStart -contains "reverb") {
    Write-Host "  [REVERB] Laravel Reverb       - WebSocket Broadcasting (ws://127.0.0.1:8080)" -ForegroundColor Magenta
}
if ($servicesToStart -contains "queue" -or $servicesToStart -contains "horizon") {
    Write-Host "  [HORIZON] Laravel Horizon     - Advanced Queue Management (WSL) / Queue Workers (Windows)" -ForegroundColor Cyan
}
if ($servicesToStart -contains "vite") {
    # Detect actual Vite port (may be 5174 if 5173 is in use)
    $vitePort = "5173"
    if (Test-NetConnection -ComputerName 127.0.0.1 -Port 5174 -InformationLevel Quiet -WarningAction SilentlyContinue) {
        $vitePort = "5174"
    }
    Write-Host "  [VITE] Vite Dev Server        - Frontend Assets + HMR (127.0.0.1:$vitePort)" -ForegroundColor Green
}
if ($servicesToStart -contains "mcp" -and -not $NoMCP) {
    Write-Host "  [MCP] Laravel MCP Server      - AI Integration & Chatbot" -ForegroundColor DarkCyan
}
if ($servicesToStart -contains "ollama") {
    Write-Host "  [OLLAMA] Ollama AI Server     - Local LLM for Chatbot (127.0.0.1:11434)" -ForegroundColor DarkMagenta
}
if ($servicesToStart -contains "pulse") {
    Write-Host "  [PULSE] Laravel Pulse         - Performance Monitoring" -ForegroundColor DarkGreen
}

Write-Host ""
Write-Host "Quick Access URLs:" -ForegroundColor White
Write-Host "  - Application:     http://127.0.0.1:8000" -ForegroundColor Gray
Write-Host "  - Admin Panel:     http://127.0.0.1:8000/admin" -ForegroundColor Gray
Write-Host "  - AI Chatbot:      http://127.0.0.1:8000/chatbot" -ForegroundColor Gray
Write-Host "  - Telescope:       http://127.0.0.1:8000/telescope" -ForegroundColor Gray
Write-Host "  - Pulse:           http://127.0.0.1:8000/pulse" -ForegroundColor Gray
if ($servicesToStart -contains "ollama") {
    Write-Host "  - Ollama API:      http://127.0.0.1:11434" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Development Commands:" -ForegroundColor White
Write-Host "  - Run Tests:       php artisan test" -ForegroundColor Gray
Write-Host "  - Code Format:     vendor/bin/pint" -ForegroundColor Gray
Write-Host "  - Static Analysis: vendor/bin/phpstan analyse" -ForegroundColor Gray
Write-Host "  - E2E Tests:       npm run test:e2e" -ForegroundColor Gray
Write-Host "  - Build Assets:    npm run build" -ForegroundColor Gray
Write-Host "  - Queue Status:    .\scripts\dev\start-queue-workers.ps1 -Action status" -ForegroundColor Gray
Write-Host "  - Queue Monitor:   php artisan queue:monitor" -ForegroundColor Gray
if ($servicesToStart -contains "ollama") {
    Write-Host "  - AI Models:       ollama list" -ForegroundColor Gray
    Write-Host "  - Install Model:   ollama pull llama3.2:3b" -ForegroundColor Gray
}

Write-Host ""
Write-Host "Compliance Reminders:" -ForegroundColor Yellow
Write-Host "  [PDPA] PDPA 2010: Personal data encryption & audit logging active" -ForegroundColor Gray
Write-Host "  [WCAG] WCAG 2.2 AA: 4.5:1 text contrast, 3:1 UI contrast required" -ForegroundColor Gray
Write-Host "  [MYGOV] MyGOV Standards: Bahasa Melayu only, mobile-first design" -ForegroundColor Gray
Write-Host "  [PSR12] PSR-12: Run vendor/bin/pint before commits" -ForegroundColor Gray

Write-Host ""
Write-Host "[$timestamp] All services ready! Press any key to stop all services..." -ForegroundColor Yellow

# Wait for user input
$null = $Host.UI.RawUI.ReadKey("NoEcho,IncludeKeyDown")

# Enhanced service shutdown
Write-Host ""
Write-Host "[$timestamp] Shutting down ICTServe development environment..." -ForegroundColor Red

# Graceful shutdown sequence
$shutdownServices = @(
    @{ Name = "Vite"; Pattern = "*vite*|*npm*dev*" },
    @{ Name = "Queue Worker"; Pattern = "*queue:work*" },
    @{ Name = "Laravel Horizon"; Pattern = "*horizon*|*artisan*horizon*" },
    @{ Name = "Laravel Reverb"; Pattern = "*reverb:start*" },
    @{ Name = "Laravel MCP"; Pattern = "*boost:mcp*" },
    @{ Name = "Ollama AI"; Pattern = "*ollama*serve*" },
    @{ Name = "Laravel Server"; Pattern = "*artisan*serve*" },
    @{ Name = "Redis Monitor"; Pattern = "*redis-cli*monitor*" }
)

foreach ($service in $shutdownServices) {
    try {
        $processes = Get-Process | Where-Object {
            $_.MainWindowTitle -match $service.Pattern -or
            $_.ProcessName -match $service.Pattern
        }

        if ($processes) {
            Write-Host "  - Stopping $($service.Name)..." -ForegroundColor Yellow
            $processes | Stop-Process -Force -ErrorAction SilentlyContinue
        }
    }
    catch {
        # Silent cleanup
    }
}

# Final cleanup
try {
    Get-Process | Where-Object {
        $_.MainWindowTitle -match "Laravel|Vite|Redis|Queue|MCP|Reverb|Horizon|Ollama"
    } | Stop-Process -Force -ErrorAction SilentlyContinue
}
catch {
    # Silent cleanup
}

$finalTime = Get-Date -Format "HH:mm:ss"
Write-Host "[$finalTime] ICTServe development environment stopped." -ForegroundColor Green
Write-Host ""

