# PowerShell environment script for ICTServe
# Run this to set Node v22.14.0 for the project
# Usage: . .\.env.ps1

 $nodeV22Path = "C:\Program Files\nodejs"
$nodeExe = Join-Path $nodeV22Path "node.exe"

if (Test-Path -Path $nodeExe) {
    # Rebuild PATH with v22 first (remove any existing node-v* entries)
    $pathArray = $env:PATH -split ';' | Where-Object { $_ -and $_ -notlike "*node-v*" }
    $env:PATH = $nodeV22Path + ';' + ($pathArray -join ';')

    # Verify version using full path
    $version = & $nodeExe --version
    Write-Host "Node version is now: $version" -ForegroundColor Green
}
else {
    $existingNode = Get-Command node -ErrorAction SilentlyContinue
    if ($existingNode) {
        Write-Host "Laragon Node v22 not found at $nodeExe. Using existing Node at $($existingNode.Source)." -ForegroundColor Yellow
        $version = & node --version
        Write-Host "Active Node version is: $version" -ForegroundColor Yellow
    }
    else {
        Write-Host "Laragon Node v22 not found and no Node executable on PATH. Install Node 22.12+ or update nodeV22Path in .env.ps1." -ForegroundColor Red
    }
}
