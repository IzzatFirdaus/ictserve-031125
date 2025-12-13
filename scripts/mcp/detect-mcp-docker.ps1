#!/usr/bin/env pwsh
<#
Lightweight detector: finds running Docker containers that look like MCP / memory / copilot servers.
Usage: .\scripts\detect-mcp-docker.ps1
#>
try {
    $docker = Get-Command docker -ErrorAction SilentlyContinue
    if (-not $docker) {
        Write-Host "docker CLI not found in PATH. Skipping detection." -ForegroundColor Yellow
        exit 2
    }

    $out = docker ps --format "{{.ID}}`t{{.Names}}`t{{.Image}}" 2>$null
    if (-not $out) {
        Write-Host "No running containers found." -ForegroundColor Green
        exit 0
    }

    $matches = $out | Where-Object { $_ -match '(?i)mcp|modelcontext|copilot|copilot-api|mcp_docker' }
    if ($matches) {
        Write-Host "Potential Docker-managed MCP containers found:" -ForegroundColor Cyan
        $matches | ForEach-Object { Write-Host $_ }
        exit 0
    }

    Write-Host "No obvious MCP-related containers detected." -ForegroundColor Green
    exit 0
} catch {
    Write-Host "Error checking docker: $_" -ForegroundColor Red
    exit 3
}
