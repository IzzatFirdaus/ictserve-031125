#!/usr/bin/env pwsh
<#
Start the ICTServe development environment using Docker Compose (PowerShell).

This script:
- verifies docker CLI availability
- copies .env.docker -> .env when .env does not already exist (asks first)
- runs `docker compose up -d --build` and reports status

Note: This is written for Windows PowerShell / PowerShell Core. Use the command
shell included with Docker Desktop or a PowerShell prompt that has Docker in PATH.
#>

function Exec-Command {
    param($cmd)
    Write-Host "> $cmd" -ForegroundColor Cyan
    & cmd /c "$cmd"
}

# Check docker availability
try {
    Exec-Command 'docker version --format "Server: {{.Server.Version}}"'
} catch {
    Write-Host "Docker CLI not found or not running. Please install/start Docker Desktop and try again." -ForegroundColor Red
    exit 1
}

# Ensure a .env file exists for Docker runs
if (-not (Test-Path -Path .env)) {
    if (Test-Path -Path .env.docker) {
        Write-Host ".env not found — copying .env.docker -> .env" -ForegroundColor Yellow
        Copy-Item -Path .env.docker -Destination .env -Force
    } else {
        Write-Host ".env.docker missing — please create or copy the correct environment file before proceeding." -ForegroundColor Red
        exit 1
    }
} else {
    Write-Host ".env already present — leaving it unchanged." -ForegroundColor Green
}

# Prefer modern `docker compose` if available
$composeCmd = 'docker compose'

Write-Host "Starting Docker Compose (building images) ..." -ForegroundColor Cyan
Exec-Command "$composeCmd up -d --build"

Write-Host "Waiting a few seconds for services to start..." -ForegroundColor Cyan
Start-Sleep -Seconds 6

Write-Host "Inspecting containers..." -ForegroundColor Cyan
Exec-Command "$composeCmd ps"

Write-Host "To run migrations & seeders (first-time setup) execute: docker compose exec app php artisan migrate --seed" -ForegroundColor Green
Write-Host "To check logs: docker compose logs -f --tail=200" -ForegroundColor Green

Write-Host "Done — your development stack should now be running at http://localhost:8000" -ForegroundColor Green
