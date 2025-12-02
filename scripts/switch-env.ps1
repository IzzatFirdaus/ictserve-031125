<#
Switch environment files for ICTServe (PowerShell)

Usage:
  # Switch to Docker env (copies .env.docker -> .env)
  .\scripts\switch-env.ps1 -env docker

  # Switch to example env (.env.example -> .env)
  .\scripts\switch-env.ps1 -env example

  # Restore most recent .env backup
  .\scripts\switch-env.ps1 -env restore

This script will create a timestamped backup of the current .env file before replacing it.
It is safe to run on Windows PowerShell (desktop / WSL) from the repository root.
#>
param(
    [Parameter(Mandatory=$true)]
    [ValidateSet('docker','example','restore')]
    [string]$env
)

Set-StrictMode -Version Latest

function Backup-Env {
    if (Test-Path .env) {
        $ts = (Get-Date).ToString('yyyyMMdd_HHmmss')
        $backup = ".env.backup.$ts"
        Copy-Item -Path .env -Destination $backup -Force
        Write-Host "Backed up current .env to $backup"
        return $backup
    }
    else {
        Write-Host "No existing .env to back up"
        return $null
    }
}

switch ($env) {
    'docker' {
        if (-not (Test-Path .env.docker)) { Write-Error '.env.docker not found in project root.'; exit 1 }
        Backup-Env | Out-Null
        Copy-Item -Path .env.docker -Destination .env -Force
        Write-Host 'Copied .env.docker -> .env (Docker configuration applied)'
        break
    }
    'example' {
        if (-not (Test-Path .env.example)) { Write-Error '.env.example not found in project root.'; exit 1 }
        Backup-Env | Out-Null
        Copy-Item -Path .env.example -Destination .env -Force
        Write-Host 'Copied .env.example -> .env (example configuration applied)'
        break
    }
    'restore' {
        $backups = Get-ChildItem -Path . -Filter '.env.backup.*' | Sort-Object LastWriteTime -Descending
        if ($backups -and $backups.Count -gt 0) {
            $latest = $backups[0].FullName
            Copy-Item -Path $latest -Destination .env -Force
            Write-Host "Restored $latest -> .env"
        } else {
            Write-Error 'No .env.backup.* files found to restore.'; exit 1
        }
        break
    }
}

Write-Host "Next: run 'php artisan config:clear && php artisan cache:clear' to reload settings if needed."
