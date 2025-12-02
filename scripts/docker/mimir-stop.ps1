#!/usr/bin/env pwsh
# Wrapper script for stopping Mimir services
# Calls the main Mimir stop script

param(
    [switch]$RemoveVolumes
)

if ($RemoveVolumes) {
    & "$PSScriptRoot\..\mimir\stop.ps1" -RemoveVolumes
} else {
    & "$PSScriptRoot\..\mimir\stop.ps1"
}
