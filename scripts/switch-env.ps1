#!/usr/bin/env pwsh
<#
.SYNOPSIS
    Switch between different environment configurations for ICTServe

.DESCRIPTION
    This script helps switch between different environment configurations:
    - laragon: Use Laragon/XAMPP with local MySQL and Redis (non-workspace)
    - docker: Use Docker Compose with containerized services (workspace)
    - workspace: Alias for docker configuration

.PARAMETER env
    Environment to switch to: laragon, docker, or workspace

.PARAMETER Force
    Force overwrite existing .env file without confirmation

.EXAMPLE
    .\scripts\switch-env.ps1 -env docker
    Switch to Docker configuration

.EXAMPLE
    .\scripts\switch-env.ps1 -env laragon -Force
    Switch to Laragon configuration without confirmation
#>

param(
    [Parameter(Mandatory = $true)]
    [ValidateSet("laragon", "docker", "workspace")]
    [string]$env,

    [switch]$Force
)

# Set error action preference
$ErrorActionPreference = "Stop"

# Get script directory and project root
$ScriptDir = Split-Path -Parent $MyInvocation.MyCommand.Path
$ProjectRoot = Split-Path -Parent $ScriptDir

# Change to project root
Set-Location $ProjectRoot

Write-Host "🔄 ICTServe Environment Switcher" -ForegroundColor Cyan
Write-Host "=================================" -ForegroundColor Cyan

# Normalize environment names
if ($env -eq "workspace") {
    $env = "docker"
}

# Define configuration files
$EnvFiles = @{
    "laragon" = @{
        "source" = ".env.laragon"
        "description" = "Laragon (Local MySQL, WSL Redis, Host Services)"
        "urls" = @("http://127.0.0.1:8000")
        "services" = @("MySQL (Laragon)", "Redis (WSL)", "PHP (Local)", "Ollama (Local)")
    }
    "docker" = @{
        "source" = ".env.workspace"
        "description" = "Docker Compose (Containerized Services)"
        "urls" = @("http://localhost:8000")
        "services" = @("MySQL (Container)", "Redis (Container)", "PHP (Container)", "Nginx (Container)")
    }
}

$MCPFiles = @{
    "laragon" = ".kiro/settings/mcp.laragon.json"
    "docker" = ".kiro/settings/mcp.workspace.json"
}

# Validate environment
if (-not $EnvFiles.ContainsKey($env)) {
    Write-Error "❌ Invalid environment: $env. Valid options: laragon, docker, workspace"
    exit 1
}

$Config = $EnvFiles[$env]
$SourceFile = $Config.source
$MCPSource = $MCPFiles[$env]

# Check if source files exist
if (-not (Test-Path $SourceFile)) {
    Write-Error "❌ Source file not found: $SourceFile"
    exit 1
}

if (-not (Test-Path $MCPSource)) {
    Write-Error "❌ MCP configuration file not found: $MCPSource"
    exit 1
}

# Display configuration info
Write-Host ""
Write-Host "📋 Configuration: $($Config.description)" -ForegroundColor Green
Write-Host "📁 Source File: $SourceFile" -ForegroundColor Gray
Write-Host "🔧 MCP Config: $MCPSource" -ForegroundColor Gray
Write-Host ""
Write-Host "🌐 Access URLs:" -ForegroundColor Yellow
foreach ($url in $Config.urls) {
    Write-Host "   • $url" -ForegroundColor White
}
Write-Host ""
Write-Host "⚙️  Services:" -ForegroundColor Yellow
foreach ($service in $Config.services) {
    Write-Host "   • $service" -ForegroundColor White
}
Write-Host ""

# Check if .env already exists
$EnvExists = Test-Path ".env"
$MCPExists = Test-Path ".kiro/settings/mcp.json"

if (($EnvExists -or $MCPExists) -and -not $Force) {
    Write-Host "⚠️  Existing configuration files found:" -ForegroundColor Yellow
    if ($EnvExists) { Write-Host "   • .env" -ForegroundColor Gray }
    if ($MCPExists) { Write-Host "   • .kiro/settings/mcp.json" -ForegroundColor Gray }
    Write-Host ""

    $Confirm = Read-Host "Do you want to overwrite? (y/N)"
    if ($Confirm -notmatch "^[Yy]") {
        Write-Host "❌ Operation cancelled" -ForegroundColor Red
        exit 0
    }
}

# Backup existing files
$BackupDir = "backups/env-$(Get-Date -Format 'yyyyMMdd-HHmmss')"
if ($EnvExists -or $MCPExists) {
    Write-Host "💾 Creating backup in $BackupDir..." -ForegroundColor Blue
    New-Item -ItemType Directory -Path $BackupDir -Force | Out-Null

    if ($EnvExists) {
        Copy-Item ".env" "$BackupDir/.env.backup" -Force
        Write-Host "   ✅ Backed up .env" -ForegroundColor Green
    }

    if ($MCPExists) {
        Copy-Item ".kiro/settings/mcp.json" "$BackupDir/mcp.json.backup" -Force
        Write-Host "   ✅ Backed up mcp.json" -ForegroundColor Green
    }
}

# Copy configuration files
Write-Host ""
Write-Host "🔄 Switching to $env configuration..." -ForegroundColor Blue

try {
    # Copy environment file
    Copy-Item $SourceFile ".env" -Force
    Write-Host "   ✅ Copied $SourceFile → .env" -ForegroundColor Green

    # Copy MCP configuration
    Copy-Item $MCPSource ".kiro/settings/mcp.json" -Force
    Write-Host "   ✅ Copied $MCPSource → .kiro/settings/mcp.json" -ForegroundColor Green

    # Generate app key if needed
    if (-not (Get-Content ".env" | Select-String "APP_KEY=base64:")) {
        Write-Host "   🔑 Generating application key..." -ForegroundColor Blue

        if ($env -eq "docker") {
            # For Docker, generate key inside container if running
            $ContainerRunning = docker compose ps app --format "{{.State}}" 2>$null
            if ($ContainerRunning -eq "running") {
                docker compose exec app php artisan key:generate --force | Out-Null
                Write-Host "   ✅ Generated key in Docker container" -ForegroundColor Green
            } else {
                Write-Host "   ⚠️  Docker container not running. Start with: docker compose up -d" -ForegroundColor Yellow
                Write-Host "   ⚠️  Then run: docker compose exec app php artisan key:generate" -ForegroundColor Yellow
            }
        } else {
            # For local environment
            php artisan key:generate --force | Out-Null
            Write-Host "   ✅ Generated application key" -ForegroundColor Green
        }
    }

    Write-Host ""
    Write-Host "✅ Successfully switched to $env configuration!" -ForegroundColor Green

    # Display next steps
    Write-Host ""
    Write-Host "📋 Next Steps:" -ForegroundColor Cyan

    if ($env -eq "docker") {
        Write-Host "   1. Start Docker services:" -ForegroundColor White
        Write-Host "      docker compose up -d" -ForegroundColor Gray
        Write-Host ""
        Write-Host "   2. Install dependencies (if needed):" -ForegroundColor White
        Write-Host "      docker compose exec app composer install" -ForegroundColor Gray
        Write-Host "      docker compose exec app npm ci" -ForegroundColor Gray
        Write-Host ""
        Write-Host "   3. Run migrations:" -ForegroundColor White
        Write-Host "      docker compose exec app php artisan migrate --seed" -ForegroundColor Gray
        Write-Host ""
        Write-Host "   4. Access application:" -ForegroundColor White
        Write-Host "      http://localhost:8000" -ForegroundColor Gray
    } else {
        Write-Host "   1. Start development services:" -ForegroundColor White
        Write-Host "      .\scripts\dev\start-dev.ps1" -ForegroundColor Gray
        Write-Host ""
        Write-Host "   2. Or start individual services:" -ForegroundColor White
        Write-Host "      php artisan serve" -ForegroundColor Gray
        Write-Host "      php artisan reverb:start" -ForegroundColor Gray
        Write-Host "      npm run dev" -ForegroundColor Gray
        Write-Host ""
        Write-Host "   3. Access application:" -ForegroundColor White
        Write-Host "      http://127.0.0.1:8000" -ForegroundColor Gray
    }

} catch {
    Write-Error "❌ Failed to switch configuration: $_"
    exit 1
}

Write-Host ""
Write-Host "🎉 Environment switch completed!" -ForegroundColor Green
