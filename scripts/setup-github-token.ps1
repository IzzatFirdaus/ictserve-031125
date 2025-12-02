<#
Setup-GitHub-Token.ps1

Purpose: Help developers safely configure a GitHub Personal Access Token for
local development or Mimir usage. This script will NOT commit any secrets and
encourages using `.env` or the Mimir copilot-data file (which is gitignored).

USAGE (PowerShell):
  .\scripts\setup-github-token.ps1              # interactive
  .\scripts\setup-github-token.ps1 -Token 'ghp_...'

#>

param(
    [string]$Token
)

function PromptForToken {
    Write-Host "Enter GitHub Personal Access Token (will not be printed):"
    $secure = Read-Host -AsSecureString
    return [Runtime.InteropServices.Marshal]::PtrToStringAuto([Runtime.InteropServices.Marshal]::SecureStringToBSTR($secure))
}

if (-not $Token) {
    $Token = PromptForToken
}

if (-not $Token) {
    Write-Error "No token provided - aborting."
    exit 1
}

$root = Resolve-Path -Path "$(Split-Path -Parent $MyInvocation.MyCommand.Path)/.."

# 1) offer to write to .env (project root .env)
$envFile = Join-Path $root '.env'
if (Test-Path $envFile) {
    Write-Host "Detected existing .env file at: $envFile"
    $confirm = Read-Host "Would you like to add/update PAT_GITHUB_ACCESS_TOKEN in .env? (y/n)"
    if ($confirm -match '^[Yy]') {
        $content = Get-Content $envFile -Raw
        if ($content -match '^PAT_GITHUB_ACCESS_TOKEN=') {
            Write-Host "Updating existing PAT_GITHUB_ACCESS_TOKEN in .env"
            (Get-Content $envFile) | ForEach-Object { $_ -replace '^PAT_GITHUB_ACCESS_TOKEN=.*', "PAT_GITHUB_ACCESS_TOKEN=$Token" } | Set-Content $envFile
        } else {
            Write-Host "Appending PAT_GITHUB_ACCESS_TOKEN to .env"
            Add-Content -Path $envFile -Value "`n# GitHub PAT for local tools (safe to add, DO NOT commit)" -Encoding utf8
            Add-Content -Path $envFile -Value "PAT_GITHUB_ACCESS_TOKEN=$Token" -Encoding utf8
        }
        Write-Host "Added/updated PAT_GITHUB_ACCESS_TOKEN in $envFile (do not commit this file)."
    }
} else {
    Write-Host "No .env file present at project root. You can create one using cp .env.example .env and then re-run this script."
}

# 2) offer to write into Mimir/copilot-data/github_token (used by local Mimir proxies)
$mimirTokenFile = Join-Path $root 'Mimir\copilot-data\github_token'
$choice = Read-Host "Would you like to (also) write token to Mimir/copilot-data/github_token? (y/n)"
if ($choice -match '^[Yy]') {
    $dir = Split-Path $mimirTokenFile -Parent
    if (-not (Test-Path $dir)) {
        New-Item -ItemType Directory -Path $dir -Force | Out-Null
    }

    if (Test-Path $mimirTokenFile) {
        $bak = "$mimirTokenFile.bak.$((Get-Date).ToString('yyyyMMddHHmmss'))"
        Copy-Item -Path $mimirTokenFile -Destination $bak -Force
        Write-Host "Backed up existing token file to $bak"
    }

    # Write token - file is gitignored in this repo's default .gitignore configuration
    Set-Content -Path $mimirTokenFile -Value $Token -NoNewline -Encoding UTF8
    Write-Host "Wrote token into $mimirTokenFile (file is gitignored; rotate token if ever leaked)."
}

Write-Host "Setup complete. To verify the token you can run: .\scripts\verify-github-token.ps1"
