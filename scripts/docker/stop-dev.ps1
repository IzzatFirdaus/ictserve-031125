#!/usr/bin/env pwsh
<#
Stop the ICTServe development environment and optionally remove containers and volumes.

Usage:
  ./scripts/docker/stop-dev.ps1         # gracefully stops compose services
  ./scripts/docker/stop-dev.ps1 -RemoveVolumes  # removes volumes too (data)
#>

param(
    [switch]$RemoveVolumes
)

function Exec-Command {
    param($cmd)
    Write-Host "> $cmd" -ForegroundColor Cyan
    & cmd /c "$cmd"
}

try {
    Exec-Command 'docker version --format "Server: {{.Server.Version}}"'
} catch {
    Write-Host "Docker CLI not found or not running. Nothing to stop." -ForegroundColor Yellow
    exit 1
}

$composeCmd = 'docker compose'

if ($RemoveVolumes) {
    Write-Host "Stopping containers and removing volumes (this will erase DB/data volumes)" -ForegroundColor Yellow
    Exec-Command "$composeCmd down -v"
} else {
    Write-Host "Stopping compose services (containers will be removed by compose but volumes will be kept)" -ForegroundColor Cyan
    Exec-Command "$composeCmd down"
}

Write-Host "Stopped — run `$composeCmd ps` to confirm (or use scripts/docker/status.ps1)" -ForegroundColor Green
