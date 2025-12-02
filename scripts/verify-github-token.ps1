<#
verify-github-token.ps1

Purpose: Verify a GitHub PAT using the GitHub REST API. The script prefers an
environment variable named GITHUB_API_KEY and falls back to Mimir/copilot-data/github_token.
This script will not print the token to the console.

Usage:
  .\scripts\verify-github-token.ps1
  .\scripts\verify-github-token.ps1 -Token 'ghp_...'
#>

param(
    [string]$Token
)

if (-not $Token) {
    # Prefer environment variable: prefer PAT_GITHUB_ACCESS_TOKEN (CI-friendly), fall back to legacy GITHUB_API_KEY
    $Token = $env:PAT_GITHUB_ACCESS_TOKEN
    if (-not $Token) { $Token = $env:GITHUB_API_KEY }
}

$root = Resolve-Path -Path "$(Split-Path -Parent $MyInvocation.MyCommand.Path)/.."
$mimirTokenFile = Join-Path $root 'Mimir\copilot-data\github_token'

if (-not $Token -and (Test-Path $mimirTokenFile)) {
    $Token = Get-Content -Path $mimirTokenFile -Raw
}

if (-not $Token) {
    Write-Error 'No token found. Set the PAT_GITHUB_ACCESS_TOKEN env var (preferred) or GITHUB_API_KEY locally, or place the token in Mimir/copilot-data/github_token.'
    exit 1
}

# Validate token by calling GitHub API /user endpoint
try {
    $headers = @{ Authorization = "Bearer $Token"; 'User-Agent' = 'ictserve-token-checker' }
    $result = Invoke-RestMethod -Uri 'https://api.github.com/user' -Headers $headers -Method Get -ErrorAction Stop
    Write-Host ('Token appears valid - authenticated as: {0} (id: {1}).' -f $result.login, $result.id)
} catch {
    Write-Error ("Failed to validate token. HTTP Error: " + $_.Exception.Message)
    Write-Error 'If you see 401/403 the token may be invalid or missing required scopes.'
    exit 2
}
