#!/usr/bin/env pwsh
# Build Docker containers

param(
    [string]$Service = ""
)

if ($Service) {
    Write-Host "Building $Service container..." -ForegroundColor Cyan
    docker compose build $Service
} else {
    Write-Host "Building all containers..." -ForegroundColor Cyan
    docker compose build
}
