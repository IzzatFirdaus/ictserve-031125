# PowerShell environment script for ICTServe
# Run this to set Node v22.14.0 for the project
# Usage: . .\.env.ps1

$nodeV22Path = "C:\laragon\bin\nodejs\node-v22"

# Reconstruct PATH with v22 first (remove any existing node-v18 or node-v22 entries)
$pathArray = $env:PATH -split ';' | Where-Object { $_ -notlike "*node-v*" }
$env:PATH = $nodeV22Path + ';' + ($pathArray -join ';')

# Verify version using full path
$version = & "$nodeV22Path\node.exe" --version
Write-Host "Node version is now: $version" -ForegroundColor Green
