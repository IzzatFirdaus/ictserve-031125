# ICTServe Apache Alias Setup Script
# This script sets up Apache alias to access ICTServe at http://localhost/ictserve

Write-Host "ICTServe Apache Alias Setup" -ForegroundColor Cyan
Write-Host "================================" -ForegroundColor Cyan
Write-Host ""

# Define paths
$aliasDir = "C:\laragon\etc\apache2\alias"
$aliasFile = "$aliasDir\ictserve.conf"
$sourceFile = "apache-alias-ictserve.conf"

# Check if Laragon alias directory exists
if (-not (Test-Path $aliasDir)) {
    Write-Host "Creating alias directory: $aliasDir" -ForegroundColor Yellow
    New-Item -ItemType Directory -Path $aliasDir -Force | Out-Null
}

# Copy the alias configuration
Write-Host "Copying Apache alias configuration..." -ForegroundColor Green
Copy-Item -Path $sourceFile -Destination $aliasFile -Force

Write-Host "Apache alias configuration created successfully!" -ForegroundColor Green
Write-Host "File location: $aliasFile" -ForegroundColor Gray
Write-Host ""

# Instructions
Write-Host "Next Steps:" -ForegroundColor Cyan
Write-Host "1. Restart Apache in Laragon:" -ForegroundColor White
Write-Host "   - Right-click Laragon tray icon" -ForegroundColor Gray
Write-Host "   - Click Apache then Reload" -ForegroundColor Gray
Write-Host ""
Write-Host "2. Access your application at:" -ForegroundColor White
Write-Host "   http://localhost/ictserve" -ForegroundColor Yellow
Write-Host "   http://127.0.0.1/ictserve" -ForegroundColor Yellow
Write-Host ""
Write-Host "3. Test brand assets:" -ForegroundColor White
Write-Host "   http://localhost/ictserve/images/jata-negara.svg" -ForegroundColor Gray
Write-Host "   http://localhost/ictserve/images/motac-logo.png" -ForegroundColor Gray
Write-Host "   http://localhost/ictserve/favicon.ico" -ForegroundColor Gray
Write-Host ""
Write-Host "Setup complete!" -ForegroundColor Green
