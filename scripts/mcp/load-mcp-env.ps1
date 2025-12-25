param([switch]$StartVSCode)

$envFile = ".\.env"
if (-not (Test-Path $envFile)) {
    Write-Host "ERROR: .env file not found" -ForegroundColor Red
    exit 1
}

Write-Host "Loading MCP environment variables..." -ForegroundColor Cyan

$mcpKeys = @("FIRECRAWL_API_KEY", "CONTEXT7_API_KEY", "DEEPL_API_KEY", "PAT_GITHUB_ACCESS_TOKEN")
$loaded = 0

foreach ($line in (Get-Content $envFile)) {
    $line = $line.Trim()
    if ($line -and -not $line.StartsWith('#') -and $line -match '^([^=]+)=(.*)$') {
        $key = $Matches[1].Trim()
        $value = $Matches[2].Trim() -replace '^"(.+)"$', '$1'

        if ($mcpKeys -contains $key) {
            [Environment]::SetEnvironmentVariable($key, $value, "Process")
            $loaded++
            Write-Host "   $key loaded" -ForegroundColor Green
        }
    }
}

Write-Host "`nLoaded $loaded / $($mcpKeys.Count) variables" -ForegroundColor $(if ($loaded -eq $mcpKeys.Count) {"Green"} else {"Yellow"})

if ($StartVSCode) {
    Write-Host "`nStarting VS Code..." -ForegroundColor Cyan
    & code .
}
