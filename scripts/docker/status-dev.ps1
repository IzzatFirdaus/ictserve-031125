#!/usr/bin/env pwsh
<#
Show status and basic health checks for the ICTServe Docker Compose stack.

This script prints `docker compose ps` and `docker ps` filtered for app/db, plus a short tail of logs.
#>

function Exec-Command {
    param($cmd)
    Write-Host "\n> $cmd" -ForegroundColor Cyan
    & cmd /c "$cmd"
}

try {
    Exec-Command 'docker version --format "Server: {{.Server.Version}}"'
} catch {
    Write-Host "Docker CLI not found or not running." -ForegroundColor Red
    exit 1
}

$composeCmd = 'docker compose'

Exec-Command "$composeCmd ps"

Exec-Command 'docker ps --filter "name=ictserve-app" --filter "name=ictserve-db"'

Write-Host "\nTailing last logs from app (200 lines) and db (100 lines) - press CTRL+C to stop" -ForegroundColor Yellow
Exec-Command 'docker compose logs --tail=200 --no-color app'
Exec-Command 'docker compose logs --tail=100 --no-color db'
