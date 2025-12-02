<#[
Exports a sanitized `.env.example` from a local `.env` file by redacting sensitive keys
and removing duplicate keys.

Usage:
  # Default usage: uses repository root .env and writes .env.example
  ./scripts/laragon/export-example.ps1

  # Custom input and output
  ./scripts/laragon/export-example.ps1 -EnvPath ".env" -OutPath ".env.example" -Force

Notes:
  - Does not commit or push any changes.
  - By default, it will ask before overwriting an existing .env.example.
  - Replace the $sensitiveKeys array to add/remove keys to sanitize.
#>

param(
    [string]$EnvPath = '.env',
    [string]$OutPath = '.env.example',
    [string]$RedactionPlaceholder = 'REDACTED',
    [switch]$Force
)

Set-StrictMode -Version Latest

function Write-Info($msg) { Write-Host "[INFO] $msg" -ForegroundColor Cyan }
function Write-Ok($msg) { Write-Host "[OK] $msg" -ForegroundColor Green }
function Write-Warn($msg) { Write-Host "[WARN] $msg" -ForegroundColor Yellow }
function Write-Err($msg) { Write-Host "[ERROR] $msg" -ForegroundColor Red }

if (-not (Test-Path $EnvPath)) {
    Write-Err "Env file '$EnvPath' not found. Run from repo root or pass -EnvPath path to your .env file."
    exit 1
}

Write-Info "Reading: $EnvPath"
$raw = Get-Content -Raw $EnvPath -ErrorAction Stop

# Keys that may contain sensitive values — replace these with placeholders or blank values
$sensitiveKeys = @(
    'APP_KEY', 'DB_PASSWORD', 'AWS_SECRET_ACCESS_KEY', 'AWS_ACCESS_KEY_ID',
    'PAT_GITHUB_ACCESS_TOKEN', 'GITHUB_TOKEN', 'REVERB_APP_KEY', 'REVERB_APP_SECRET',
    'NEO4J_PASSWORD', 'DEEPL_API_KEY', 'CONTEXT7_API_KEY', 'FIRECRAWL_API_KEY',
    'REDIS_PASSWORD', 'MAIL_PASSWORD', 'MAIL_USERNAME', 'FILESYSTEM_S3_KEY', 'FILESYSTEM_S3_SECRET'
)

# If OutPath exists, ask for confirmation unless -Force specified
if ((Test-Path $OutPath) -and (-not $Force)) {
    $ans = Read-Host "The file '$OutPath' already exists. Overwrite? (y/N)"
    if ($ans -notin @('y','Y','yes','Yes')) {
        Write-Info "Aborting: did not overwrite $OutPath"
        exit 0
    }
}

# Process: keep comments and blank lines; for keys, track first occurrence and sanitize if sensitive
$outLines = @()
$seenKeys = @{}

foreach ($line in $raw -split "\r?\n") {
    if ($line -match '^\s*$' -or $line.TrimStart().StartsWith('#')) {
        # Blank or comment — preserve
        $outLines += $line
        continue
    }

    if ($line -match '^([^=]+)=(.*)$') {
        $k = $matches[1].Trim()
        $v = $matches[2]
        # If we've already processed this key, skip duplicates
        if ($seenKeys.ContainsKey($k)) { continue }

        $seenKeys[$k] = $true

        # If key is sensitive, replace value with a placeholder (no secrets in .env.example)
        if ($sensitiveKeys -contains $k) {
            # if DB_PASSWORD/APP_KEY prefer blank; else use generic placeholder
            switch ($k) {
                'DB_PASSWORD' { $newVal = '' }
                'APP_KEY' { $newVal = '' }
                default { $newVal = $RedactionPlaceholder }
            }
            $outLines += "$k=$newVal"
            continue
        }

        # For non-sensitive keys keep original value; preserve quoting
        $outLines += $line
        continue
    }

    # If we reach here, it's an unparseable line — preserve as-is
    $outLines += $line
}

Write-Info "Writing sanitized file to: $OutPath"
$outLines -join "`n" | Set-Content $OutPath -Encoding UTF8 -Force
Write-Ok "Saved $OutPath (sensitive values redacted)."
Write-Info "Tip: Review and commit the new '$OutPath' file if you want to update the repo's example env."

exit 0
