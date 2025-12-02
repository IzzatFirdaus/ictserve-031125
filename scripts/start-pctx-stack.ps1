#Requires -Version 5.1
<#
.SYNOPSIS
    Orchestrate PCTX + Mimir stack with health checks and logging

.DESCRIPTION
    Starts Neo4j → Mimir → PCTX with dependency ordering and health verification.
    Supports detached/background mode for CI/CD and interactive mode for development.

.PARAMETER ProjectRoot
    Project directory containing docker-compose.yml and pctx.json (default: current directory)

.PARAMETER MaxWaitSeconds
    Maximum seconds to wait for services (default: 120)

.PARAMETER Detached
    Run in background mode (exit immediately after starting services)

.PARAMETER Services
    Comma-separated list of services to start (default: "neo4j,mimir,pctx")

.PARAMETER Logs
    Show live logs while services start (only in foreground mode)

.EXAMPLE
    # Development mode (foreground, with logs)
    .\scripts\start-pctx-stack.ps1

    # CI/CD mode (background, quick exit)
    .\scripts\start-pctx-stack.ps1 -Detached

    # Start only Mimir (skip Neo4j, PCTX)
    .\scripts\start-pctx-stack.ps1 -Services "mimir"

    # Longer timeout for slow systems
    .\scripts\start-pctx-stack.ps1 -MaxWaitSeconds 300

.NOTES
    Requires: Docker, Docker Compose, PCTX installed
    Author: Claudette (Mimir Edition)
    Date: 2025-11-22
#>

param(
    [string]$ProjectRoot = (Get-Location),
    [int]$MaxWaitSeconds = 120,
    [switch]$Detached = $false,
    [string]$Services = "neo4j,mimir,pctx",
    [switch]$Logs = $false
)

$ErrorActionPreference = 'Stop'
$VerbosePreference = 'Continue'

# ============================================================================
# Logging Functions
# ============================================================================

$script:LogFile = "$ProjectRoot\logs\pctx-stack-$(Get-Date -Format 'yyyyMMdd-HHmmss').log"
$null = New-Item -ItemType Directory -Path (Split-Path $script:LogFile) -Force -ErrorAction SilentlyContinue

function Log {
    param([string]$Message, [ValidateSet('Info', 'Success', 'Warning', 'Error')][string]$Level = 'Info')

    $timestamp = Get-Date -Format 'HH:mm:ss'
    $prefix = switch ($Level) {
        'Success' { '✅' }
        'Warning' { '⚠️' }
        'Error' { '❌' }
        default { 'ℹ️' }
    }

    $msg = "[$timestamp] $prefix $Message"
    Write-Host $msg
    Add-Content -Path $script:LogFile -Value $msg
}

function LogSection {
    param([string]$Title)
    Log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
    Log $Title
    Log "━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━"
}

function DebugOutput {
    param([string]$Message)
    if ($VerbosePreference -eq 'Continue') {
        Log "DEBUG: $Message"
    }
}

# ============================================================================
# Verification Functions
# ============================================================================

function Test-DockerInstalled {
    try {
        $version = docker --version 2>&1
        Log "Docker: $version" -Level Info
        return $true
    } catch {
        Log "Docker not found or not accessible" -Level Error
        return $false
    }
}

function Test-DockerRunning {
    try {
        $null = docker ps --quiet 2>&1
        Log "Docker daemon is running" -Level Success
        return $true
    } catch {
        Log "Docker daemon is not running. Start Docker Desktop." -Level Error
        return $false
    }
}

function Test-PctxInstalled {
    try {
        $version = pctx --version 2>&1
        Log "PCTX: $version" -Level Info
        return $true
    } catch {
        Log "PCTX not found. Run: choco install pctx (or download from https://github.com/portofcontext/pctx)" -Level Error
        return $false
    }
}

function Test-FileExists {
    param([string]$Path, [string]$Description)

    if (Test-Path $Path) {
        Log "$Description found: $Path" -Level Success
        return $true
    } else {
        Log "$Description not found: $Path" -Level Error
        return $false
    }
}

function Test-HealthEndpoint {
    param([string]$Url, [string]$Service, [int]$TimeoutSeconds = 5)

    try {
        $response = Invoke-RestMethod -Uri $Url -Method Get -TimeoutSec $TimeoutSeconds -ErrorAction Stop
        if ($response -and $response.status -in @('ok', 'ready')) {
            Log "$Service health check PASSED" -Level Success
            return $true
        } else {
            DebugOutput "Health check response: $(ConvertTo-Json $response)"
            Log "$Service health check returned unexpected status" -Level Warning
            return $false
        }
    } catch {
        DebugOutput "Health check exception: $($_.Exception.Message)"
        return $false
    }
}

function Wait-ForHealthEndpoint {
    param(
        [string]$Url,
        [string]$Service,
        [int]$MaxSeconds = 60,
        [int]$IntervalSeconds = 2
    )

    Log "Waiting for $Service ($Url) to be ready..."
    $elapsed = 0

    while ($elapsed -lt $MaxSeconds) {
        if (Test-HealthEndpoint -Url $Url -Service $Service -TimeoutSeconds 3) {
            return $true
        }

        Start-Sleep -Seconds $IntervalSeconds
        $elapsed += $IntervalSeconds
        Write-Host -NoNewline "."
    }

    Log "$Service failed to become ready within ${MaxSeconds}s" -Level Error
    return $false
}

# ============================================================================
# Docker Operations
# ============================================================================

function Start-DockerServices {
    param(
        [string[]]$ServiceNames = @('neo4j_db', 'mimir_server', 'copilot_api_server')
    )

    LogSection "Starting Docker Services"

    Log "Detected services in docker-compose.yml: $($ServiceNames -join ', ')"

    foreach ($service in $ServiceNames) {
        Log "Starting $service..."
        try {
            docker-compose up -d $service 2>&1 | ForEach-Object { DebugOutput $_ }
            Log "$service started" -Level Success
        } catch {
            Log "Failed to start $service : $_" -Level Error
            return $false
        }
    }

    return $true
}

function Get-DockerServices {
    try {
        # Parse docker-compose.yml to extract service names
        $composeFile = Join-Path $ProjectRoot "docker-compose.yml"
        if (-not (Test-Path $composeFile)) {
            Log "docker-compose.yml not found" -Level Warning
            return @()
        }

        # Simple YAML parsing for service names
        $services = Select-String -Path $composeFile -Pattern '^\s+(\w+):' -AllMatches |
            ForEach-Object { $_.Matches.Groups[1].Value } |
            Where-Object { $_ -notin @('version', 'services') }

        return $services
    } catch {
        Log "Error parsing docker-compose.yml: $_" -Level Warning
        return @('neo4j_db', 'mimir_server', 'copilot_api_server')  # Fallback
    }
}

# ============================================================================
# PCTX Operations
# ============================================================================

function Start-PctxServer {
    param([bool]$Detached = $false)

    LogSection "Starting PCTX Server"

    # Verify pctx.json exists
    $configPath = Join-Path $ProjectRoot "pctx.json"
    if (-not (Test-FileExists -Path $configPath -Description "PCTX configuration")) {
        Log "Initialize PCTX config with: pctx init" -Level Warning
        return $false
    }

    cd $ProjectRoot

    if ($Detached) {
        Log "Starting PCTX in background (detached mode)..."
        $logDir = Join-Path $ProjectRoot "logs"
        $null = New-Item -ItemType Directory -Path $logDir -Force

        $outLog = Join-Path $logDir "pctx-stdout.log"
        $errLog = Join-Path $logDir "pctx-stderr.log"

        $process = Start-Process -FilePath "pctx" `
            -ArgumentList "dev --config $configPath" `
            -RedirectStandardOutput $outLog `
            -RedirectStandardError $errLog `
            -PassThru -WindowStyle Hidden

        Log "PCTX started in background (PID: $($process.Id))" -Level Success
        Log "Logs: $outLog / $errLog"

        # Save PID for later
        Set-Content -Path (Join-Path $ProjectRoot "pctx.pid") -Value $process.Id

        return $true
    } else {
        Log "Starting PCTX in foreground..."
        Log "Press Ctrl+C to stop all services"
        & pctx dev --config $configPath
    }
}

function Stop-PctxServer {
    $pidFile = Join-Path $ProjectRoot "pctx.pid"
    if (Test-Path $pidFile) {
        $pid = Get-Content $pidFile
        try {
            Stop-Process -Id $pid -ErrorAction SilentlyContinue
            Log "Stopped PCTX (PID: $pid)" -Level Success
            Remove-Item $pidFile -Force -ErrorAction SilentlyContinue
        } catch {
            Log "Failed to stop PCTX: $_" -Level Warning
        }
    }
}

# ============================================================================
# Service Orchestration
# ============================================================================

function Start-Stack {
    param([string[]]$ServiceNames)

    LogSection "Orchestrating ICTServe + PCTX Stack"

    Log "Project root: $ProjectRoot"
    Log "Max wait time: ${MaxWaitSeconds}s"
    Log "Mode: $(if ($Detached) { 'DETACHED' } else { 'FOREGROUND' })"
    Log "Services: $($ServiceNames -join ', ')"

    # ---- Prerequisites ----
    LogSection "Verifying Prerequisites"

    if (-not (Test-DockerInstalled)) { exit 1 }
    if (-not (Test-DockerRunning)) { exit 1 }
    if (-not (Test-PctxInstalled)) { exit 1 }
    if (-not (Test-FileExists -Path (Join-Path $ProjectRoot "docker-compose.yml") -Description "docker-compose.yml")) { exit 1 }

    # ---- Docker Services ----
    LogSection "Docker Services Phase"

    $dockerServices = Get-DockerServices
    $requestedServices = $ServiceNames | Where-Object { $_ -in $dockerServices }

    if ($requestedServices.Count -gt 0) {
        if (-not (Start-DockerServices -ServiceNames $requestedServices)) {
            Log "Failed to start Docker services" -Level Error
            exit 1
        }

        # Wait for key services
        Start-Sleep -Seconds 10  # Initial buffer for container startup

        if ($requestedServices -contains 'mimir_server') {
            if (-not (Wait-ForHealthEndpoint -Url "http://localhost:9042/health" -Service "Mimir" -MaxSeconds $MaxWaitSeconds)) {
                exit 1
            }
        }

        if ($requestedServices -contains 'neo4j_db') {
            Start-Sleep -Seconds 5
            Log "Neo4j started (further validation skipped for speed)" -Level Info
        }
    }

    # ---- PCTX Server ----
    if ($ServiceNames -contains 'pctx') {
        if (-not (Start-PctxServer -Detached $Detached)) {
            exit 1
        }

        if ($Detached) {
            Start-Sleep -Seconds 5
            if (-not (Wait-ForHealthEndpoint -Url "http://127.0.0.1:8080/health" -Service "PCTX" -MaxSeconds $MaxWaitSeconds)) {
                Log "PCTX startup failed, see logs: logs/pctx-*.log" -Level Error
                exit 1
            }
        }
    }

    # ---- Success ----
    LogSection "Stack Started Successfully"
    Log "Mimir MCP: http://localhost:9042/mcp" -Level Info
    Log "PCTX Proxy: http://127.0.0.1:8080/mcp" -Level Info
    Log "Neo4j: bolt://localhost:7687" -Level Info
    Log "Copilot API: http://localhost:4141" -Level Info
    Log "Logs: $script:LogFile" -Level Info

    if (-not $Detached) {
        Log "Services running in foreground. Press Ctrl+C to stop."
    }
}

# ============================================================================
# Main Entry Point
# ============================================================================

try {
    # Normalize services parameter
    [string[]]$requestedServices = $Services -split ',' | ForEach-Object { $_.Trim() } | Where-Object { $_ }

    # Validate project root
    if (-not (Test-Path $ProjectRoot)) {
        Log "Project root not found: $ProjectRoot" -Level Error
        exit 1
    }

    # Start stack
    Start-Stack -ServiceNames $requestedServices

    Log "✅ All systems ready!" -Level Success
} catch {
    Log "Fatal error: $($_.Exception.Message)" -Level Error
    Log "Stack trace: $($_.ScriptStackTrace)" -Level Error
    exit 1
} finally {
    Log "Script completed at $(Get-Date -Format 'HH:mm:ss')"
}
